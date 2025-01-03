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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;



class PaymentController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
    }

    public function processPayment(Request $request): RedirectResponse
    {
        $borDetails = User::find($request->user()->id)->board;
        $chapterId = $borDetails['chapter_id'];

        $chapterDetails = Chapters::find($chapterId);
        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;
        $chapterName = $chapterDetails['name'];
        $chConf = $chapterDetails['conference'];
        $chPcid = $chapterDetails['primary_coordinator_id'];

        $presDetails = DB::table('boards as bd')
            ->select('bd.chapter_id as chapter_id', 'bd.board_position_id as board_position_id', 'bd.first_name as first_name', 'bd.last_name as last_name',
                'bd.street_address as street_address', 'bd.city as city', 'bd.state as state', 'bd.zip as zip')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', 1)
            ->first();

        $company = $chapterName.', '.$chapterState;
        $next_renewal_year = $chapterDetails['next_renewal_year'];

        $chapterEmailList = DB::table('boards as bd')
            ->select('bd.email as bor_email')
            ->where('bd.chapter_id', '=', $chapterId)
            ->get();
        $emailListBoard = '';
        foreach ($chapterEmailList as $val) {
            $email = $val->bor_email;
            $escaped_email = str_replace("'", "\\'", $email);
            if ($emailListBoard == '') {
                $emailListBoard = $escaped_email;
            } else {
                $emailListBoard .= ','.$escaped_email;
            }
        }

        $corDetails = DB::table('coordinators')
            ->select('email')
            ->where('id', $chapterDetails->primary_coordinator_id)
            ->first();
        $cor_pcemail = $corDetails->email;

        $coordinatorEmailList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $chPcid)
            ->get();

        foreach ($coordinatorEmailList as $key => $value) {
            $coordinatorList[$key] = (array) $value;
        }
        $filterCoordinatorList = array_filter($coordinatorList[0]);
        unset($filterCoordinatorList['id']);
        unset($filterCoordinatorList['layer0']);
        $filterCoordinatorList = array_reverse($filterCoordinatorList);
        $str = '';
        $array_rows = count($filterCoordinatorList);
        $i = 0;

        $emailListCoor = '';
        foreach ($filterCoordinatorList as $key => $val) {
            if ($val > 1) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    if ($emailListCoor == '') {
                        $emailListCoor = $corList[0]->cord_email;
                    } else {
                        $emailListCoor .= ','.$corList[0]->cord_email;
                    }
                }
            }
        }

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
        $today = Carbon::today()->format('m-d-Y');

        // Call the load_coordinators function
        // $coordinatorData = $this->load_coordinators($chapterId, $chConf, $chPcid);
        // $ConfCoorEmail = $coordinatorData['ConfCoorEmail'];
        // $coordinator_array = $coordinatorData['coordinator_array'];

        // Load Conference Coordinators for Sending Email
        $ccEmailData = $this->userController->loadConferenceCoord($chPcid);
        $emailCC = $ccEmailData['cc_email'];

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

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        $merchantDefinedField1 = new AnetAPI\UserFieldType;
        $merchantDefinedField1->setName('MemberCount');
        $merchantDefinedField1->setValue($members);

        $merchantDefinedField2 = new AnetAPI\UserFieldType;
        $merchantDefinedField2->setName('SustainingDonation');
        $merchantDefinedField2->setValue($sustaining);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType;
        //$transactionRequestType->setTransactionType('authOnlyTransaction');
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
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response and parse it to display the results of authorizing the card
                /** @var \net\authorize\api\contract\v1\CreateTransactionResponse $response */
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $mailData = [
                        'chapterName' => $chapterName,
                        'chapterState' => $chapterState,
                        'pres_fname' => $presDetails->first_name,
                        'pres_lname' => $presDetails->last_name,
                        'pres_street' => $presDetails->street_address,
                        'pres_city' => $presDetails->city,
                        'pres_state' => $presDetails->state,
                        'pres_zip' => $presDetails->zip,
                        'members' => $members,
                        'late' => $late,
                        'sustaining' => $donation,
                        'reregTotal' => $rereg,
                        'processing' => $fee,
                        'totalPaid' => $total,
                        'fname' => $first,
                        'lname' => $last,
                        'email' => $email,
                        'chapterId' => $chapterId,
                        'invoice' => $randomInvoiceNumber,
                        'datePaid' => $today,
                        'chapterMembers' => $members,
                        'chapterDate' => Carbon::today()->format('m-d-Y'),
                        'chapterTotal' => $sustaining,
                    ];

                    $to_email = $email;
                    $to_email2 = explode(',', $emailListBoard);
                    $to_email3 = $cor_pcemail;
                    $to_email4 = explode(',', $emailListCoor);
                    $to_email5 = $emailCC;
                    $to_email6 = 'dragonmom@msn.com';

                    $existingRecord = Chapters::where('id', $chapterId)->first();
                    $existingRecord->members_paid_for = $members;
                    $existingRecord->next_renewal_year = $next_renewal_year + 1;
                    $existingRecord->dues_last_paid = Carbon::today();

                    Mail::to([$to_email])
                        ->cc($to_email3)
                        ->queue(new PaymentsReRegChapterThankYou($mailData));

                    Mail::to([$to_email5, $to_email6])
                        ->queue(new PaymentsReRegOnline($mailData));

                    if ($sustaining > 0.00) {
                        $existingRecord->sustaining_donation = $sustaining;
                        $existingRecord->sustaining_date = Carbon::today();

                        Mail::to([$to_email])
                            ->cc($to_email3)
                            ->queue(new PaymentsSustainingChapterThankYou($mailData));
                    }
                    $existingRecord->save();

                    // Success notification
                    return redirect()->to('/home')->with('success', 'Payment was successfully processed and profile has been updated!');
                    // return redirect()->route('home')->with('success', 'Payment successful! Transaction ID: ' . $tresponse->getTransId());
                } else {
                    // Transaction failed
                    $error_message = 'Transaction Failed';
                    if ($tresponse->getErrors() != null) {
                        $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                        $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();
                    }

                    return redirect()->to('/board/reregpayment')->with('fail', $error_message);
                }

                // Or, print errors if the API request wasn't successful
            } else {
                // Transaction Failed
                /** @var \net\authorize\api\contract\v1\CreateTransactionResponse $response */
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $error_message = 'Transaction Failed';
                    $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                    $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();

                    return redirect()->back()->with('fail', $error_message);
                } else {
                    $error_message = 'Transaction Failed';
                    $error_message .= "\n Error Code: ".$response->getMessages()->getMessage()[0]->getCode();
                    $error_message .= "\n Error Message: ".$response->getMessages()->getMessage()[0]->getText();

                    return redirect()->to('/board/reregpayment')->with('fail', $error_message);
                }
            }
        } else {
            // No response returned
            return redirect()->to('/board/reregpayment')->with('fail', 'No response returned');
        }
    }

    public function processDonation(Request $request): RedirectResponse
    {
        $borDetails = User::find($request->user()->id)->board;
        $chapterId = $borDetails['chapter_id'];

        $chapterDetails = Chapters::find($chapterId);
        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;
        $chapterName = $chapterDetails['name'];
        $chConf = $chapterDetails['conference'];
        $chPcid = $chapterDetails['primary_coordinator_id'];

        $presDetails = DB::table('boards as bd')
            ->select('bd.chapter_id as chapter_id', 'bd.board_position_id as board_position_id', 'bd.first_name as first_name', 'bd.last_name as last_name',
                'bd.street_address as street_address', 'bd.city as city', 'bd.state as state', 'bd.zip as zip')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', 1)
            ->first();

        $company = $chapterName.', '.$chapterState;
        $next_renewal_year = $chapterDetails['next_renewal_year'];

        $chapterEmailList = DB::table('boards as bd')
            ->select('bd.email as bor_email')
            ->where('bd.chapter_id', '=', $chapterId)
            ->get();
        $emailListBoard = '';
        foreach ($chapterEmailList as $val) {
            $email = $val->bor_email;
            $escaped_email = str_replace("'", "\\'", $email);
            if ($emailListBoard == '') {
                $emailListBoard = $escaped_email;
            } else {
                $emailListBoard .= ','.$escaped_email;
            }
        }

        $corDetails = DB::table('coordinators')
            ->select('email')
            ->where('id', $chapterDetails->primary_coordinator_id)
            ->first();
        $cor_pcemail = $corDetails->email;

        $coordinatorEmailList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $chPcid)
            ->get();

        foreach ($coordinatorEmailList as $key => $value) {
            $coordinatorList[$key] = (array) $value;
        }
        $filterCoordinatorList = array_filter($coordinatorList[0]);
        unset($filterCoordinatorList['id']);
        unset($filterCoordinatorList['layer0']);
        $filterCoordinatorList = array_reverse($filterCoordinatorList);
        $str = '';
        $array_rows = count($filterCoordinatorList);
        $i = 0;

        $emailListCoor = '';
        foreach ($filterCoordinatorList as $key => $val) {
            if ($val > 1) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    if ($emailListCoor == '') {
                        $emailListCoor = $corList[0]->cord_email;
                    } else {
                        $emailListCoor .= ','.$corList[0]->cord_email;
                    }
                }
            }
        }

        $m2mdonation = $request->input('donation');
        $donation = (float) preg_replace('/[^\d.]/', '', $m2mdonation);
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

        // Call the load_coordinators function
        // $coordinatorData = $this->load_coordinators($chapterId, $chConf, $chPcid);
        // $ConfCoorEmail = $coordinatorData['ConfCoorEmail'];
        // $coordinator_array = $coordinatorData['coordinator_array'];

         // Load Conference Coordinators for Sending Email
         $ccEmailData = $this->userController->loadConferenceCoord($chPcid);
         $emailCC = $ccEmailData['cc_email'];

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
        $order->setDescription('Mother-to-Mother Donation');

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

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        // $merchantDefinedField1 = new AnetAPI\UserFieldType();
        // $merchantDefinedField1->setName('MemberCount');
        // $merchantDefinedField1->setValue($members);

        $merchantDefinedField1 = new AnetAPI\UserFieldType;
        $merchantDefinedField1->setName('Donation');
        $merchantDefinedField1->setValue($donation);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType;
        //$transactionRequestType->setTransactionType('authOnlyTransaction');
        $transactionRequestType->setTransactionType('authCaptureTransaction');
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest;
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response and parse it to display the results of authorizing the card
                /** @var \net\authorize\api\contract\v1\CreateTransactionResponse $response */
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $mailData = [
                        'chapterName' => $chapterName,
                        'chapterState' => $chapterState,
                        'pres_fname' => $presDetails->first_name,
                        'pres_lname' => $presDetails->last_name,
                        'pres_street' => $presDetails->street_address,
                        'pres_city' => $presDetails->city,
                        'pres_state' => $presDetails->state,
                        'pres_zip' => $presDetails->zip,
                        'donation' => $donation,
                        'processing' => $fee,
                        'total' => $total,
                        'fname' => $first,
                        'lname' => $last,
                        'email' => $email,
                        'chapterId' => $chapterId,
                        'invoice' => $randomInvoiceNumber,
                        'datePaid' => $today,
                        'chapterAmount' => $donation,
                    ];

                    $to_email = $email;
                    $to_email2 = explode(',', $emailListBoard);
                    $to_email3 = $cor_pcemail;
                    $to_email4 = explode(',', $emailListCoor);
                    $to_email5 = $emailCC;
                    $to_email6 = 'dragonmom@msn.com';

                    $existingRecord = Chapters::where('id', $chapterId)->first();

                    Mail::to([$to_email5, $to_email6])
                        ->queue(new PaymentsM2MOnline($mailData));

                    if ($donation > 0.00) {
                        $existingRecord->m2m_payment = $donation;
                        $existingRecord->m2m_date = Carbon::today();

                        Mail::to([$to_email])
                            ->cc($to_email3)
                            ->queue(new PaymentsM2MChapterThankYou($mailData));
                    }
                    $existingRecord->save();

                    // Success notification
                    return redirect()->to('/home')->with('success', 'Payment was successfully processed, thank you for your donation!');
                    // return redirect()->route('home')->with('success', 'Payment successful! Transaction ID: ' . $tresponse->getTransId());
                } else {
                    // Transaction failed
                    $error_message = 'Transaction Failed';
                    if ($tresponse->getErrors() != null) {
                        $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                        $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();
                    }

                    return redirect()->to('/board/m2mdonation')->with('fail', $error_message);
                }

                // Or, print errors if the API request wasn't successful
            } else {
                // Transaction Failed
                /** @var \net\authorize\api\contract\v1\CreateTransactionResponse $response */
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $error_message = 'Transaction Failed';
                    $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                    $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();

                    return redirect()->back()->with('fail', $error_message);
                } else {
                    $error_message = 'Transaction Failed';
                    $error_message .= "\n Error Code: ".$response->getMessages()->getMessage()[0]->getCode();
                    $error_message .= "\n Error Message: ".$response->getMessages()->getMessage()[0]->getText();

                    return redirect()->to('/board/m2mdonation')->with('fail', $error_message);
                }
            }
        } else {
            // No response returned
            return redirect()->to('/board/m2mdonation')->with('fail', 'No response returned');
        }
    }

    // public function load_coordinators($chapterId, $chConf, $chPcid)
    // {
    //     $chapterDetails = Chapters::find($chapterId)
    //         ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state',
    //             'chapters.conference_id as conf', 'chapters.primary_coordinator_id as pcid')
    //         ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
    //         ->where('chapters.is_active', '=', '1')
    //         ->first();

    //     $reportingList = DB::table('coordinator_reporting_tree')
    //         ->select('*')
    //         ->where('id', '=', $chPcid)
    //         ->get();

    //     foreach ($reportingList as $key => $value) {
    //         $reportingList[$key] = (array) $value;
    //     }
    //     $filterReportingList = array_filter($reportingList[0]);
    //     unset($filterReportingList['id']);
    //     unset($filterReportingList['layer0']);
    //     $filterReportingList = array_reverse($filterReportingList);
    //     $str = '';
    //     $array_rows = count($filterReportingList);
    //     $i = 0;
    //     $coordinator_array = [];
    //     foreach ($filterReportingList as $key => $val) {
    //         $corList = DB::table('coordinators as cd')
    //             ->select('cd.id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cd.email as email', 'cp.short_title as pos')
    //             ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //             ->where('cd.id', '=', $val)
    //             ->get();
    //         $coordinator_array[$i] = ['id' => $corList[0]->cid,
    //             'first_name' => $corList[0]->fname,
    //             'last_name' => $corList[0]->lname,
    //             'email' => $corList[0]->email,
    //             'position' => $corList[0]->pos];

    //         $i++;
    //     }
    //     $coordinator_count = count($coordinator_array);

    //     for ($i = 0; $i < $coordinator_count; $i++) {
    //         if ($coordinator_array[$i]['position'] == 'RC') {
    //             $rc_email = $coordinator_array[$i]['email'];
    //             $rc_id = $coordinator_array[$i]['id'];
    //         } elseif ($coordinator_array[$i]['position'] == 'CC') {
    //             $cc_email = $coordinator_array[$i]['email'];
    //             $cc_id = $coordinator_array[$i]['id'];
    //         }
    //     }

    //     switch ($chConf) {
    //         case 1:
    //             $to_email = $cc_email;
    //             break;
    //         case 2:
    //             $to_email = $cc_email;
    //             break;
    //         case 3:
    //             $to_email = $cc_email;
    //             break;
    //         case 4:
    //             $to_email = $cc_email;
    //             break;
    //         case 5:
    //             $to_email = $cc_email;
    //             break;
    //         default:
    //             $to_email = 'admin@momsclub.org';
    //     }

    //     return [
    //         'ConfCoorEmail' => $to_email,
    //         'coordinator_array' => $coordinator_array,
    //     ];
    // }
}
