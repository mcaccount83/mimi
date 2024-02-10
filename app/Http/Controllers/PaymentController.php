<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Mail\PaymentsReRegReceipt;
use App\Mail\PaymentsReRegOnline;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapter;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $borDetails = User::find($request->user()->id)->BoardDetails;
        $chapterId = $borDetails['chapter_id'];

        $chapterDetails = Chapter::find($chapterId);
        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;
        $chapterName = $chapterDetails['name'];

        $company = $chapterName . ', ' . $chapterState;
        $next_renewal_year = $chapterDetails['next_renewal_year'];

        $corDetails = DB::table('coordinator_details')
            ->select('email')
            ->where('coordinator_id', $chapterDetails->primary_coordinator_id)
            ->first();
        $cor_email = $corDetails->email;

        $members = $request->input('members');
        $late = $request->input('late');
        $rereg = $request->input('rereg');
        $donation = $request->input('sustaining');
        $sustaining = (float) preg_replace('/[^\d.]/', '', $donation);
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

        /* Create a merchantAuthenticationType object with authentication details
            retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('AUTHORIZENET_API_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('AUTHORIZENET_TRANSACTION_KEY'));

        // Set the transaction's refId
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expirationDate);
        $creditCard->setCardCode($cvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($chapterId);
        $order->setDescription("Re-Registration Payment");

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($first);
        $customerAddress->setLastName($last);
        $customerAddress->setCompany($company);
        $customerAddress->setAddress($address);
        $customerAddress->setCity($city);
        $customerAddress->setState($state);
        $customerAddress->setZip($zip);
        $customerAddress->setCountry("USA");

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($chapterId);
        $customerData->setEmail($email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        $merchantDefinedField1 = new AnetAPI\UserFieldType();
        $merchantDefinedField1->setName("MemberCount");
        $merchantDefinedField1->setValue($members);

        $merchantDefinedField2 = new AnetAPI\UserFieldType();
        $merchantDefinedField2->setName("SustainingDonation");
        $merchantDefinedField2->setValue($sustaining);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);
        $transactionRequestType->addToUserFields($merchantDefinedField2);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        //$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $mailData = [
                        'chapterName' => $chapterName,
                        'chapterState' => $chapterState,
                        'members' => $members,
                        'late' => $late,
                        'sustaining' => $sustaining,
                        'reregTotal' =>$rereg,
                        'processing' => $fee,
                        'totalPaid' => $total,
                        'datePaid' => Carbon::today()->format('m-d-Y'),
                        'chapterTotal' => $sustaining,
                    ];

                    $to_email = $email;
                    $to_email2 = $cor_email;
                    $to_email3 = "dragonmom@msn.com";

                    $existingRecord = Chapter::where('id', $chapterId)->first();
                        $existingRecord->members_paid_for = $members;
                        $existingRecord->next_renewal_year = $next_renewal_year + 1;
                        $existingRecord->dues_last_paid = Carbon::today();

                        Mail::to($to_email)
                            ->send(new PaymentsReRegReceipt($mailData));

                        Mail::to($to_email2)
                            ->send(new PaymentsReRegOnline($mailData));

                        Mail::to($to_email3)
                            ->send(new PaymentsReRegOnline($mailData));

                        if ($sustaining > 0.00) {
                            $existingRecord->sustaining_donation = $sustaining;
                            $existingRecord->sustaining_date = Carbon::today();

                            Mail::to($to_email)
                                ->cc($to_email2)
                                ->send(new PaymentsSustainingChapterThankYou($mailData));
                        }
                    $existingRecord->save();

                    // Success notification
                    return redirect()->to('/home')->with('success', 'Payment was successfully processed and profile has been updated!');
                    // return redirect()->route('home')->with('success', 'Payment successful! Transaction ID: ' . $tresponse->getTransId());
                } else {
                    // Transaction failed
                    $error_message = "Transaction Failed";
                    if ($tresponse->getErrors() != null) {
                        $error_message .= "\n Error Code: " . $tresponse->getErrors()[0]->getErrorCode();
                        $error_message .= "\n Error Message: " . $tresponse->getErrors()[0]->getErrorText();
                    }
                    return redirect()->to('/board/showreregpayment')->with('fail', $error_message);
                }


                // Or, print errors if the API request wasn't successful
            } else {
                // Transaction Failed
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $error_message = "Transaction Failed";
                    $error_message .= "\n Error Code: " . $tresponse->getErrors()[0]->getErrorCode();
                    $error_message .= "\n Error Message: " . $tresponse->getErrors()[0]->getErrorText();
                    return redirect()->back()->with('fail', $error_message);
                } else {
                    $error_message = "Transaction Failed";
                    $error_message .= "\n Error Code: " . $response->getMessages()->getMessage()[0]->getCode();
                    $error_message .= "\n Error Message: " . $response->getMessages()->getMessage()[0]->getText();
                    return redirect()->to('/board/showreregpayment')->with('fail', $error_message);
                }
            }
        } else {
            // No response returned
            return redirect()->to('/board/showreregpayment')->with('fail', 'No response returned');
        }
    }
}
