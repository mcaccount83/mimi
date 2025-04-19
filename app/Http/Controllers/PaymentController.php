<?php

namespace App\Http\Controllers;

use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsM2MOnline;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegOnline;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapters;
use App\Models\Payments;
use App\Models\PaymentLog;
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

    protected $baseMailDataController;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController, BaseMailDataController $baseMailDataController)
    {
        $this->userController = $userController;
        $this->baseBoardController = $baseBoardController;
        $this->baseMailDataController = $baseMailDataController;
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

        $baseQuery = $this->baseBoardController->getChapterDetails($request->user()->board->chapter_id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $chDetails->id;
        $stateShortName = $baseQuery['stateShortName'];
        $PresDetails = $baseQuery['PresDetails'];

        $emailListChap = $baseQuery['emailListChap'];
        $emailCC = $baseQuery['emailCC'];
        $pcEmail = $baseQuery['pcEmail'];
        $AdminEmail = 'dragonmom@msn.com';

        $input = $request->all();

        $payment = $request->input('rereg');
        $rereg = (float) preg_replace('/[^\d.]/', '', $request->input('rereg'));
        $paymentDate = Carbon::today();
        $invoice = $paymentResponse['data']['invoiceNumber'];
        $donation = $request->input('sustaining');
        $sustaining = (float) preg_replace('/[^\d.]/', '', $request->input('sustaining'));

        $chapter = Chapters::find($chId);
        $payments = Payments::find($chId);

        DB::beginTransaction();
        try {
            $chapter->next_renewal_year = $chapter->next_renewal_year + 1;
            $chapter->save();

            $payments->rereg_members = $request->input('members');
            $payments->rereg_payment = $rereg;
            $payments->rereg_date = $paymentDate;
            $payments->rereg_invoice = $invoice;
            $payments->save();

            if ($donation && $sustaining > 0) {
                $payments->sustaining_donation = $sustaining;
                $payments->sustaining_date = $paymentDate;
                $payments->save();
            }

            $baseQueryUpd = $this->baseBoardController->getChapterDetails($request->user()->board->chapter_id);
            $chPayments = $baseQueryUpd['chPayments'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getPaymentData($chPayments, $input),
            );

            Mail::to($emailListChap)
                ->cc($pcEmail)
                ->queue(new PaymentsReRegChapterThankYou($mailData));

            if ($donation && $sustaining > 0) {
                Mail::to($emailListChap)
                    ->cc($pcEmail)
                    ->queue(new PaymentsSustainingChapterThankYou($mailData));
            }

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

        if (! $paymentResponse['success']) {
            return redirect()->to('/board/reregpayment')->with('fail', $paymentResponse['error']);
        }

        $baseQuery = $this->baseBoardController->getChapterDetails($request->user()->board->chapter_id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $chDetails->id;
        $stateShortName = $baseQuery['stateShortName'];
        $PresDetails = $baseQuery['PresDetails'];

        $emailListChap = $baseQuery['emailListChap'];
        $emailCC = $baseQuery['emailCC'];
        $pcEmail = $baseQuery['pcEmail'];
        $AdminEmail = 'dragonmom@msn.com';

        $input = $request->all();

        $m2mDonation = $request->input('m2mdonation');
        $m2m = (float) preg_replace('/[^\d.]/', '', $request->input('m2mdonation'));
        $sustainingDonation = $request->input('sustaining');
        $sustaining = (float) preg_replace('/[^\d.]/', '', $request->input('sustaining'));
        $paymentDate = Carbon::today();
        $invoice = $paymentResponse['data']['invoiceNumber'];

        $chapter = Chapters::find($chId);
        $payments = Payments::find($chId);

        DB::beginTransaction();
        try {
            if ($m2mDonation && $m2m > 0) {
                $payments->m2m_donation = $m2m;
                $payments->m2m_date = $paymentDate;
                $payments->save();
            }

            if ($sustainingDonation && $sustaining > 0) {
                $payments->sustaining_donation = $sustaining;
                $payments->sustaining_date = $paymentDate;
                $payments->save();
            }

            $baseQueryUpd = $this->baseBoardController->getChapterDetails($request->user()->board->chapter_id);
            $chPayments = $baseQueryUpd['chPayments'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getPaymentData($chPayments, $input),
            );

            if ($m2mDonation && $m2m > 0) {
                Mail::to($emailListChap)
                    ->cc($pcEmail)
                    ->queue(new PaymentsM2MChapterThankYou($mailData));
            }

            if ($sustainingDonation && $sustaining > 0) {
                Mail::to($emailListChap)
                    ->cc($pcEmail)
                    ->queue(new PaymentsSustainingChapterThankYou($mailData));
            }

            Mail::to([$emailCC, $AdminEmail])
                ->queue(new PaymentsM2MOnline($mailData));

            DB::commit();

            return redirect()->to('/home')->with('success', 'Payment was successfully processed and profile has been updated!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return redirect()->to('/board/donation')->with('fail', $paymentResponse['error']);
        }
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
        $m2mdonation = $request->input('m2m');
        $m2m = (float) preg_replace('/[^\d.]/', '', $m2mdonation);
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

        $merchantDefinedField3 = new AnetAPI\UserFieldType;
        $merchantDefinedField3->setName('m2mDonation');
        $merchantDefinedField3->setValue($m2m);

        // Create payment log data
        $logData = [
            'customer_id' => $userId,
            'amount' => $amount,
            'status' => 'pending',
            'request_data' => [
                'members' => $members,
                'late' => $late,
                'rereg' => $rereg,
                'sustaining_donation' => $sustaining,
                'm2m_donation' => $m2m,
                'fee' => $fee,
                'invoice' => $randomInvoiceNumber,
                'company' => $company,
                'email' => $email,
                'total' => $amount
            ]
        ];

        // Create initial log entry before processing
        $paymentLog = PaymentLog::create($logData);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType;
        if (app()->environment('local')) {
            $transactionRequestType->setTransactionType('authOnlyTransaction');  // Auth Only for testing Purposes
        } else {
            $transactionRequestType->setTransactionType('authCaptureTransaction');  // Caputre Payment for Live Traansactions
        }
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);
        $transactionRequestType->addToUserFields($merchantDefinedField2);
        $transactionRequestType->addToUserFields($merchantDefinedField3);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest;
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        // After processing, update the log with the response
        if ($response != null) {
            if ($response->getMessages()->getResultCode() == 'Ok') {
            /** @var \net\authorize\api\contract\v1\CreateTransactionResponse $response */
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    // Update log with success response
                    $paymentLog->update([
                        'transaction_id' => $tresponse->getTransId(),
                        'status' => 'success',
                        'response_code' => $tresponse->getResponseCode(),
                        'response_message' => $tresponse->getMessages()[0]->getDescription(),
                        'response_data' => [
                            'auth_code' => $tresponse->getAuthCode(),
                            'avs_result_code' => $tresponse->getAvsResultCode(),
                            'cvv_result_code' => $tresponse->getCvvResultCode(),
                            'account_number' => $tresponse->getAccountNumber(),
                            'transaction_hash' => $tresponse->getTransHashSha2()
                        ]
                    ]);

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

                // Update log with error response
                $paymentLog->update([
                    'status' => 'failed',
                    'response_code' => $tresponse->getErrors()[0]->getErrorCode(),
                    'response_message' => $tresponse->getErrors()[0]->getErrorText(),
                    'response_data' => [
                        'error_details' => $error_message
                    ]
                ]);
            } else {
                $error_message = 'Transaction Failed';
                $error_message .= "\n Error Code: ".$response->getMessages()->getMessage()[0]->getCode();
                $error_message .= "\n Error Message: ".$response->getMessages()->getMessage()[0]->getText();

                // Update log with error response
                $paymentLog->update([
                    'status' => 'failed',
                    'response_code' => $response->getMessages()->getMessage()[0]->getCode(),
                    'response_message' => $response->getMessages()->getMessage()[0]->getText(),
                    'response_data' => [
                        'error_details' => $error_message
                    ]
                ]);
            }
        } else {
            $error_message = 'No response returned';

            // Update log with no response
            $paymentLog->update([
                'status' => 'failed',
                'response_message' => 'No response returned',
                'response_data' => [
                    'error_details' => 'No response was received from the payment gateway'
                ]
            ]);
        }

        return [
            'success' => false,
            'error' => $error_message,
        ];
    }

    public function index(Request $request)
    {
        $query = PaymentLog::with('board');

        // Add filters if needed
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $paymentLogs = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('payment-logs.index', compact('paymentLogs'));
    }

    public function show($id)
    {
        $log = PaymentLog::findOrFail($id);
        return view('payment-logs.show', compact('log'));
    }
}
