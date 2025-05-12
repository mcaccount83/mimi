<?php

namespace App\Http\Controllers;

use App\Mail\NewChapterThankYou;
use App\Mail\PaymentsNewChapOnline;
use App\Mail\PaymentsSustainingPublicThankYou;
use App\Mail\PaymentsM2MPublicThankYou;
use App\Mail\PaymentsPublicDonationOnline;
use App\Models\BoardsPending;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\PaymentLog;
use App\Models\ResourceCategory;
use App\Models\Resources;
use App\Models\State;
use App\Models\User;
use App\Services\PositionConditionsService;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PublicController extends Controller
{
    protected $userController;

    protected $positionConditionsService;

    protected $baseBoardController;

    protected $baseChapterController;

    protected $baseMailDataController;

    public function __construct(UserController $userController, PositionConditionsService $positionConditionsService, BaseBoardController $baseBoardController,
        BaseMailDataController $baseMailDataController, BaseChapterController $baseChapterController)
    {
        $this->userController = $userController;
        $this->positionConditionsService = $positionConditionsService;
        $this->baseBoardController = $baseBoardController;
        $this->baseChapterController = $baseChapterController;
        $this->baseMailDataController = $baseMailDataController;
    }

    private function token()
    {
        $client_id = config('services.google.client_id');
        $client_secret = config('services.google.client_secret');
        $refresh_token = config('services.google.refresh_token');
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
            'scope' => 'https://www.googleapis.com/auth/drive', // Add the necessary scope for Shared Drive access
        ]);

        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        return $accessToken;
    }

    public function chapterLinks(): View
    {
        $international = DB::table('chapters')
            ->select('chapters.*', 'state.state_short_name', 'state.state_long_name')
            ->join('state', 'chapters.state_id', '=', 'state.id')
            ->where('state_id', '=', '52')
            ->where('active_status', '1')
            ->where('name', 'not like', '%test%')
            ->orderBy('name')
            ->get();

        $chapters = DB::table('chapters')
            ->select('chapters.*', 'state.state_short_name', 'state.state_long_name')
            ->join('state', 'chapters.state_id', '=', 'state.id')
            ->where('chapters.state_id', '<>', 52)
            ->where('active_status', '1')
            ->where('name', 'not like', '%test%')
            ->orderBy('state_id')
            ->orderBy('name')
            ->get();

        // Preprocess website URLs
        foreach ($chapters as $chapter) {
            if (! Str::startsWith($chapter->website_url, ['http://', 'https://'])) {
                $chapter->website_url = 'https://'.$chapter->website_url;
            }
        }

        return view('public.chapterlinks', ['chapters' => $chapters, 'international' => $international]);
    }

    /**
     * Show the Chapter Resources Page
     */
    public function chapterResources(): View
    {

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['resources' => $resources, 'resourceCategories' => $resourceCategories];

        return view('public.resources')->with($data);

    }

    /**
     * Show the PDF viewer page
     */
    public function showPdf(Request $request)
    {
        $fileId = $request->query('id');

        if (empty($fileId)) {
            return abort(404, 'No file ID provided.');
        }

        return view('public.pdf-viewer', ['fileId' => $fileId]);
    }

    /**
     * Proxy for Google Drive files to avoid CORS issues
     */
    public function proxyGoogleDriveFile(Request $request)
    {
        $fileId = $request->query('id');

        if (empty($fileId)) {
            return abort(404, 'File ID is required');
        }

        try {
            // Use your existing token method that's already working for uploads
            $accessToken = $this->token();

            $client = new Client;

            // Use the Google Drive API directly with your auth token
            $response = $client->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&supportsAllDrives=true", [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                ],
                'stream' => true,
                'timeout' => 30,
            ]);

            // Get content type from response headers
            $contentType = $response->getHeaderLine('Content-Type');

            // Stream the response back to the client
            return response()->stream(
                function () use ($response) {
                    $body = $response->getBody();
                    while (! $body->eof()) {
                        echo $body->read(1024);
                    }
                },
                200,
                [
                    'Content-Type' => $contentType ?: 'application/pdf',
                    'Content-Disposition' => 'inline; filename="document.pdf"',
                    'Cache-Control' => 'no-cache',
                ]
            );

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Google Drive API error', [
                'message' => $e->getMessage(),
                'file_id' => $fileId,
            ]);

            return response()->json([
                'error' => 'Failed to fetch file from Google Drive',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show New Chapter Registration
     */
    public function editNewChapter(Request $request): View
    {
        $allStates = State::all();  // Full List for Dropdown Menu

        $data = ['allStates' => $allStates,
        ];

        return view('public.newchapter')->with($data);
    }

    /**
     * Show New Chapter Registration Success Message
     */
    public function viewNewChapter(Request $request): View
    {
        $allStates = State::all();  // Full List for Dropdown Menu

        $data = ['allStates' => $allStates,
        ];

        return view('public.newchaptersuccess')->with($data);
    }

    /**
     * Update New Chapter Registration
     */
    public function updateNewChapter(Request $request): RedirectResponse
    {
        $input = $request->all();
        $description = 'New Chapter Application';
        $transactionType = 'authOnlyTransaction';
        $name = $input['ch_name'];
        $founder = $input['ch_pre_fname'].' '.$input['ch_pre_lname'];

        $paymentResponse = $this->processPublicPayment($request, $name, $description, $transactionType);

        if (! $paymentResponse['success']) {
            return redirect()->to('/newchapter')->with('fail', $paymentResponse['error']);
        }

        $lastupdatedDate = date('Y-m-d H:i:s');
        $country = 'USA';
        $regId = '0';
        $statusId = '1';
        $activeStatus = '2';
        $currentMonth = date('m');
        $currentYear = date('Y');

        $input = $request->all();
        $invoice = $paymentResponse['data']['invoiceNumber'];
        $stateId = $input['ch_state'];

        $state = State::find($stateId);
        $stateShortName = $state->state_short_name;

        $confId = null;

        if (in_array($stateShortName, ['AK', 'HI', 'ID', 'MN', 'MT', 'ND', 'OR', 'SD', 'WA', 'WI', 'WY', '**', 'AA', 'AE', 'AP'])) {
            $confId = '1';
        } elseif (in_array($stateShortName, ['AZ', 'CA', 'CO', 'NM', 'NV', 'OK', 'TX', 'UT'])) {
            $confId = '2';
        } elseif (in_array($stateShortName, ['AL', 'AR', 'DC', 'FL', 'GA', 'KY', 'LA', 'MD', 'MS', 'NC', 'SC', 'TN', 'VA', 'WV'])) {
            $confId = '3';
        } elseif (in_array($stateShortName, ['CT', 'DE', 'MA', 'ME', 'NH', 'NJ', 'NY', 'PA', 'RI', 'VT'])) {
            $confId = '4';
        } elseif (in_array($stateShortName, ['IA', 'IL', 'IN', 'KS', 'MI', 'MO', 'NE', 'OH'])) {
            $confId = '5';
        }

        $ccDetails = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('conference_id', $confId)
            ->where('position_id', 7)
            ->where('is_active', 1)
            ->where('on_leave', '!=', '1')
            ->first();

        $pcId = $ccDetails ? $ccDetails->id : null; // Add a check in case no coordinator is found

        $sanitizedName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', '.', ' '], '-', $input['ch_name']);

        DB::beginTransaction();
        try {
            $chapterId = Chapters::create([
                'name' => $input['ch_name'],
                'sanitized_name' => $sanitizedName,
                'state_id' => $stateId,
                'country_short_name' => $country,
                'conference_id' => $confId,
                'region_id' => $regId,
                'status_id' => $statusId,
                'territory' => $input['ch_boundariesterry'],
                'inquiries_contact' => $input['ch_inqemailcontact'],
                'start_month_id' => $currentMonth,
                'start_year' => $currentYear,
                'next_renewal_year' => $currentYear + 1,
                'primary_coordinator_id' => $pcId,
                'founders_name' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                'last_updated_by' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                'last_updated_date' => $lastupdatedDate,
                'created_at' => $lastupdatedDate,
                'active_status' => $activeStatus,
            ])->id;

            // Foundrer Info
            if (isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make($input['password']),
                    'user_type' => 'pending',
                    'is_active' => 1,
                ])->id;
                BoardsPending::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country' => $country,
                    'phone' => $input['ch_pre_phone'],
                    'last_updated_by' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                    'last_updated_date' => $lastupdatedDate,
                ])->id;

            }

            $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $emailCC = $baseQuery['emailCC'];  // CC Email
            // $AdminEmail = 'dragonmom@msn.com';
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $paymentsAdmin = $adminEmail['payments_admin'];

            $founerEmail = $input['ch_pre_email'];

            $mailData = array_merge(
                $this->baseMailDataController->getNewChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPublicPaymentData($input, $invoice),
            );

            Mail::to($founerEmail)
                ->cc($emailCC)
                ->queue(new NewChapterThankYou($mailData));

            Mail::to([$emailCC, $paymentsAdmin])
                ->queue(new PaymentsNewChapOnline($mailData));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/newchapter')->with('fail', 'Something went wrong, Please try again...');
        }

        return redirect()->to('/newchaptersuccess')->with('success', 'Chapter created successfully');
    }

     /**
     * Show New Chapter Registration
     */
    public function editDonation(Request $request): View
    {
        $allStates = State::all();  // Full List for Dropdown Menu

        $data = ['allStates' => $allStates,
        ];

        return view('public.donation')->with($data);
    }

     /**
     * Show New Chapter Registration Success Message
     */
    public function viewDonation(Request $request): View
    {
        $allStates = State::all();  // Full List for Dropdown Menu

        $data = ['allStates' => $allStates,
        ];

        return view('public.donationsuccess')->with($data);
    }

    /**
     * Update New Chapter Registration
     */
    public function updateDonation(Request $request): RedirectResponse
    {
        $description = 'M2M_Sustaining Donation';
        $transactionType = 'authCaptureTransaction';
        $name = 'N/A';

        $paymentResponse = $this->processPublicPayment($request, $name, $description, $transactionType);

        if (! $paymentResponse['success']) {
            return redirect()->to('/donation')->with('fail', $paymentResponse['error']);
        }

        $input = $request->all();
        $donarEmail = $request->input('email');
        $invoice = $paymentResponse['data']['invoiceNumber'];

        $adminEmail = $this->positionConditionsService->getAdminEmail();
        $paymentsAdmin = $adminEmail['payments_admin'];

        // $AdminEmail = 'dragonmom@msn.com';

        DB::beginTransaction();
        try {
             $mailData = array_merge(
                $this->baseMailDataController->getPublicPaymentData($input, $invoice),
            );

            Mail::to($donarEmail)
                ->queue(new PaymentsSustainingPublicThankYou($mailData));

            Mail::to($donarEmail)
                ->queue(new PaymentsM2MPublicThankYou($mailData));

            Mail::to($paymentsAdmin)
                ->queue(new PaymentsPublicDonationOnline($mailData));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/donation')->with('fail', 'Something went wrong, Please try again...');
        }

        return redirect()->to('/donationsuccess')->with('success', 'Chapter created successfully');
    }

    public function processPublicPayment(Request $request, $name, $description, $transactionType)
    {
        $newchap = $request->input('newchap');
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
        $order->setDescription($description);

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType;
        $customerAddress->setFirstName($first);
        $customerAddress->setLastName($last);
        $customerAddress->setCompany($name);
        $customerAddress->setAddress($address);
        $customerAddress->setCity($city);
        $customerAddress->setState($state);
        $customerAddress->setZip($zip);
        $customerAddress->setCountry('USA');

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType;
        $customerData->setType('individual');
        $customerData->setEmail($email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType;
        $duplicateWindowSetting->setSettingName('duplicateWindow');
        $duplicateWindowSetting->setSettingValue('60');

        // Add some merchant defined fields. These fields won't be stored with the transaction, but will be echoed back in the response.
        $merchantDefinedField1 = new AnetAPI\UserFieldType;
        $merchantDefinedField1->setName('SustainingDonation');
        $merchantDefinedField1->setValue($sustaining);

        $merchantDefinedField2 = new AnetAPI\UserFieldType;
        $merchantDefinedField2->setName('m2mDonation');
        $merchantDefinedField2->setValue($m2m);

        // Create payment log data
        $logData = [
            'amount' => $amount,
            'status' => 'pending',
            'request_data' => [
                'newchap' => $newchap,
                'sustaining_donation' => $sustaining,
                'm2m_donation' => $m2m,
                'fee' => $fee,
                'invoice' => $randomInvoiceNumber,
                'company' => $name,
                // 'founder' => $founder,
                'email' => $email,
                'total' => $amount,
                'transaction' => $transactionType,
            ],
        ];

        // Create initial log entry before processing
        $paymentLog = PaymentLog::create($logData);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType;
        // $transactionRequestType->setTransactionType($transactionType);
        if (app()->environment('local')) {
            $transactionRequestType->setTransactionType('authOnlyTransaction');  // Auth Only for testing Purposes
        } else {
            $transactionRequestType->setTransactionType($transactionType);  // Live Traansactions based on type of transaction
        }
        // $transactionRequestType->setTransactionType('authOnlyTransaction');  // Auth Only for New Chapters
        // if (app()->environment('local')) {
        //     $transactionRequestType->setTransactionType('authOnlyTransaction');  // Auth Only for testing Purposes
        // } else {
        //     $transactionRequestType->setTransactionType('authCaptureTransaction');  // Caputre Payment for Live Traansactions
        // }
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
                            'transaction_hash' => $tresponse->getTransHashSha2(),
                        ],
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
                        'error_details' => $error_message,
                    ],
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
                        'error_details' => $error_message,
                    ],
                ]);
            }
        } else {
            $error_message = 'No response returned';

            // Update log with no response
            $paymentLog->update([
                'status' => 'failed',
                'response_message' => 'No response returned',
                'response_data' => [
                    'error_details' => 'No response was received from the payment gateway',
                ],
            ]);
        }

        return [
            'success' => false,
            'error' => $error_message,
        ];
    }
}
