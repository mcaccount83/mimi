<?php

namespace App\Http\Controllers;

use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsM2MOnline;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegOnline;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapters;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PaymentController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseBoardController;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController)
    {

        $this->userController = $userController;
        $this->baseBoardController = $baseBoardController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
        ];
    }

    /**
     * Re-Registration & Sustaining Donation Payment
     */
    public function reRegistrationPayment(Request $request): RedirectResponse
    {
        $paymentResponse = $this->processPayment($request);

        if (! $paymentResponse['success']) {
            return redirect()->to('/board/reregpayment')->with('fail', $paymentResponse['error']);
        }

        try {
            $baseQuery = $this->baseBoardController->getChapterDetails($request->user()->board->chapter_id);
            $chapterDetails = $baseQuery['chDetails'];
            $chapterState = $baseQuery['stateShortName'];
            $presDetails = $baseQuery['PresDetails'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailCC = $baseQuery['emailCC'];
            $pcEmail = $baseQuery['pcEmail'];
            $AdminEmail = 'dragonmom@msn.com';

            $rereg = $request->input('rereg');
            $donation = $request->input('sustaining');

            // Update chapter record
            $existingRecord = Chapters::where('id', $chapterDetails->id)->first();
            $existingRecord->members_paid_for = $request->input('members');
            $existingRecord->next_renewal_year = $chapterDetails->next_renewal_year + 1;
            $existingRecord->dues_last_paid = Carbon::today();
            $existingRecord->save();

            // Send Chepter email
            if ($rereg) {
                $mailData = [
                    'chapterName' => $chapterDetails->name,
                    'chapterState' => $chapterState,
                    'chapterMembers' => $request->input('members'),
                    'chapterDate' => Carbon::today()->format('m-d-Y'),
                ];

                Mail::to($emailListChap)
                    ->cc($pcEmail)
                    ->queue(new PaymentsReRegChapterThankYou($mailData));
            }

            // Update Record and Send Chepter email

            $sustaining = (float) preg_replace('/[^\d.]/', '', $request->input('sustaining'));
            if ($donation && $sustaining > 0) {
                $existingRecord->sustaining_donation = $sustaining;
                $existingRecord->sustaining_date = Carbon::today();
                $existingRecord->save();

                $mailData = [
                    'chapterName' => $chapterDetails->name,
                    'chapterState' => $chapterState,
                    'chapterTotal' => $sustaining,
                ];

            // if ($donation) {
            //     $sustaining = (float) preg_replace('/[^\d.]/', '', $request->input('sustaining'));
            //     $existingRecord->sustaining_donation = $sustaining;
            //     $existingRecord->sustaining_date = Carbon::today();
            //     $existingRecord->save();

            //     $mailData = [
            //         'chapterName' => $chapterDetails->name,
            //         'chapterState' => $chapterState,
            //         'chapterTotal' => $sustaining,
            //     ];

                Mail::to($emailListChap)
                    ->cc($pcEmail)
                    ->queue(new PaymentsSustainingChapterThankYou($mailData));
            }

            // Send Admin email
            $mailData = [
                'chapterName' => $chapterDetails->name,
                'chapterState' => $chapterState,
                'pres_fname' => $presDetails->first_name,
                'pres_lname' => $presDetails->last_name,
                'pres_street' => $presDetails->street_address,
                'pres_city' => $presDetails->city,
                'pres_state' => $presDetails->state,
                'pres_zip' => $presDetails->zip,
                'members' => $request->input('members'),
                'late' => $request->input('late'),
                'sustaining' => $request->input('sustaining'),
                'reregTotal' => $request->input('rereg'),
                'processing' => $request->input('fee'),
                'totalPaid' => $request->input('total'),
                'fname' => $request->input('first_name'),
                'lname' => $request->input('last_name'),
                'email' => $request->input('email'),
                'chapterId' => $chapterDetails->id,
                'invoice' => $paymentResponse['data']['invoiceNumber'],
                'datePaid' => Carbon::today()->format('m-d-Y'),
            ];

            Mail::to([$emailCC, $AdminEmail])
                ->queue(new PaymentsReRegOnline($mailData));

            DB::commit();

            return redirect()->to('/home')->with('success', 'Payment was successfully processed and profile has been updated!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return redirect()->to('/board/reregpayment')->with('fail', $paymentResponse['error']);
        }
    }

    /**
     * M2M Fund & Sustaining Donation Payment
     */
    public function m2mPayment(Request $request): RedirectResponse
    {
        $paymentResponse = $this->processPayment($request);

        if ($paymentResponse['success']) {
            $baseQuery = $this->baseBoardController->getChapterDetails($request->user()->board->chapter_id);
            $chapterDetails = $baseQuery['chDetails'];
            $chapterState = $baseQuery['stateShortName'];
            $presDetails = $baseQuery['PresDetails'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailCC = $baseQuery['emailCC'];
            $pcEmail = $baseQuery['pcEmail'];
            $AdminEmail = 'dragonmom@msn.com';

            $sustaining = $request->input('donation');
            $m2m = $request->input('m2mdonation');

            // Get chapter record
            $existingRecord = Chapters::where('id', $chapterDetails->id)->first();

            // Save Chapter and Send Chepter email
            if ($sustaining) {
                $sustaining = (float) preg_replace('/[^\d.]/', '', $request->input('sustaining'));
                $existingRecord->sustaining_donation = $sustaining;
                $existingRecord->sustaining_date = Carbon::today();
                $existingRecord->save();

                $mailData = [
                    'chapterName' => $chapterDetails->name,
                    'chapterState' => $chapterState,
                    'chapterTotal' => $sustaining,
                ];

                Mail::to($emailListChap)
                    ->cc($pcEmail)
                    ->queue(new PaymentsSustainingChapterThankYou($mailData));
            }

            if ($m2m) {
                $donation = (float) preg_replace('/[^\d.]/', '', $request->input('donation'));
                $existingRecord->m2m_payment = $donation;
                $existingRecord->m2m_date = Carbon::today();
                $existingRecord->save();

                $mailData = [
                    'chapterName' => $chapterDetails->name,
                    'chapterState' => $chapterState,
                    'chapterAmount' => $donation,
                ];

                Mail::to($emailListChap)
                    ->cc($pcEmail)
                    ->queue(new PaymentsM2MChapterThankYou($mailData));
            }

            // Send Admin email
            $mailData = [
                'chapterName' => $chapterDetails->name,
                'chapterState' => $chapterState,
                'pres_fname' => $presDetails->first_name,
                'pres_lname' => $presDetails->last_name,
                'pres_street' => $presDetails->street_address,
                'pres_city' => $presDetails->city,
                'pres_state' => $presDetails->state,
                'pres_zip' => $presDetails->zip,
                'sustaining' => $request->input('sustaining'),
                'm2m' => $request->input('m2m'),
                'processing' => $request->input('fee'),
                'totalPaid' => $request->input('total'),
                'fname' => $request->input('first_name'),
                'lname' => $request->input('last_name'),
                'email' => $request->input('email'),
                'chapterId' => $chapterDetails->id,
                'invoice' => $paymentResponse['data']['invoiceNumber'],
                'datePaid' => Carbon::today()->format('m-d-Y'),
            ];

            Mail::to([$emailCC, $AdminEmail])
                ->queue(new PaymentsM2MOnline($mailData));

            return redirect()->to('/home')->with('success', 'Donation was successfully processed and profile has been updated!');
        }

        return redirect()->to('/board/donation')->with('fail', $paymentResponse['error']);
    }

    /**
     * Process payments with Authorize.net
     */
    public function processPayment(Request $request): array
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $chapterId = $bdDetails->chapter_id;

        $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
        $chapterDetails = $baseQuery['chDetails'];
        $chapterState = $baseQuery['stateShortName'];
        $chapterName = $chapterDetails->name;

        $company = $chapterName.', '.$chapterState;

        $members = $request->input('members');
        $late = $request->input('late');
        $rereg = $request->input('rereg');
        $donation = $request->input('sustaining');
        $sustaining = (float) preg_replace('/[^\d.]/', '', $donation);
        // $m2mdonation = $request->input('m2m');
        // $m2m = (float) preg_replace('/[^\d.]/', '', $m2mdonation);
        $fee = $request->input('fee');
        $cardNumber = $request->input('card_number');
        $expirationDate = $request->input('expiration_date');
        $cvv = $request->input('cvv');
        $first = $request->input('first_name');
        $last = $request->input('last_name');
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $email = $request->input('email');
        $total = $request->input('total');
        $amount = (float) preg_replace('/[^\d.]/', '', $total);
        $today = Carbon::today()->format('m-d-Y');

        /* Create a merchantAuthenticationType object with authentication details
            retrieved from the constants file */
        /** @var \net\authorize\api\contract\v1\MerchantAuthenticationType $merchantAuthentication */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType;
        $merchantAuthentication->setName(config('settings.authorizenet_api_login_id'));
        $merchantAuthentication->setTransactionKey(config('settings.authorizenet_transaction_key'));

        // Set the transaction's refId
        $refId = 'ref'.time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType;
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expirationDate);
        $creditCard->setCardCode($cvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType;
        $paymentOne->setCreditCard($creditCard);

        // Generate a random invoice number
        $randomInvoiceNumber = mt_rand(100000, 999999);
        // Create order information
        $order = new AnetAPI\OrderType;
        $order->setInvoiceNumber($randomInvoiceNumber);
        $order->setDescription('Re-Registration Payment');

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType;
        $customerAddress->setFirstName($first);
        $customerAddress->setLastName($last);
        $customerAddress->setCompany($company);
        $customerAddress->setAddress($address);
        $customerAddress->setCity($city);
        $customerAddress->setState($state);
        $customerAddress->setZip($zip);
        $customerAddress->setCountry('USA');

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType;
        $customerData->setType('individual');
        $customerData->setId($chapterId);
        $customerData->setEmail($email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType;
        $duplicateWindowSetting->setSettingName('duplicateWindow');
        $duplicateWindowSetting->setSettingValue('60');

        // Add some merchant defined fields. These fields won't be stored with the transaction, but will be echoed back in the response.
        $merchantDefinedField1 = new AnetAPI\UserFieldType;
        $merchantDefinedField1->setName('MemberCount');
        $merchantDefinedField1->setValue($members);

        $merchantDefinedField2 = new AnetAPI\UserFieldType;
        $merchantDefinedField2->setName('SustainingDonation');
        $merchantDefinedField2->setValue($sustaining);

        // $merchantDefinedField3 = new AnetAPI\UserFieldType;
        // $merchantDefinedField3->setName('m2mDonation');
        // $merchantDefinedField3->setValue($m2m);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType;
        // $transactionRequestType->setTransactionType('authOnlyTransaction');
        $transactionRequestType->setTransactionType('authCaptureTransaction');
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);
        $transactionRequestType->addToUserFields($merchantDefinedField2);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest;
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == 'Ok') {
                /** @var \net\authorize\api\contract\v1\CreateTransactionResponse $response */
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    return [
                        'success' => true,
                        'data' => [
                            'transactionId' => $tresponse->getTransId(),
                            'invoiceNumber' => $randomInvoiceNumber,
                        ],
                    ];
                }
            }

            // Handle errors
            /** @var \net\authorize\api\contract\v1\CreateTransactionResponse $response */
            $tresponse = $response->getTransactionResponse();
            if ($tresponse != null && $tresponse->getErrors() != null) {
                $error_message = 'Transaction Failed';
                $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();
            } else {
                $error_message = 'Transaction Failed';
                $error_message .= "\n Error Code: ".$response->getMessages()->getMessage()[0]->getCode();
                $error_message .= "\n Error Message: ".$response->getMessages()->getMessage()[0]->getText();
            }
        } else {
            $error_message = 'No response returned';
        }

        return [
            'success' => false,
            'error' => $error_message,
        ];
    }
}
