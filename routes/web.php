<?php

use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardControllerNew;
use App\Http\Controllers\BoardPaymentController;
use App\Http\Controllers\BoardPendingController;
use App\Http\Controllers\BoardPendingControllerNew;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChapterReportController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\CoordinatorReportController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EOYReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\FinancialReportControllerNew;
use App\Http\Controllers\ForumSubscriptionController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiriesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\TechReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserReportController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MySentEmailsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Error Pages Test Routes...Public, No login required
Route::get('/test-500', function () {
    abort(500);
});
Route::get('/test-404', function () {
    abort(404);
});
Route::get('/test-403', function () {
    abort(403);
});
Route::get('/test-401', function () {
    abort(401);
});
Route::get('/test-419', function () {
    abort(419);
});
Route::get('/test-429', function () {
    abort(429);
});

// Login and Logout Routes...Public, No login required...Used for Board & Coordinator Layouts
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [LoginController::class, 'showLoginForm']);
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('logout', [LoginController::class, 'logout']);

// Password Reset Routes...Public, No login required...Used for Board & Coordinator Layouts
Route::get('password/request', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// UserControllert Routes...Public, No login required...Used for Board & Coordinator Layouts
Route::get('/checkemail/{email}', [UserController::class, 'checkEmail'])->name('checkemail');
Route::post('/checkpassword', [UserController::class, 'checkCurrentPassword'])->name('checkpassword');
Route::post('/updatepassword', [UserController::class, 'updatePassword'])->name('updatepassword');
Route::get('/load-email-details/{chId}', [UserController::class, 'loadEmailDetails'])->name('load.email.details');
Route::get('/load-coordinator-list/{id}', [UserController::class, 'loadCoordinatorList'])->name('load.coordinator.list');

// Public Page Routes...Public, No login required
Route::get('/chapter-links', [PublicController::class, 'chapterLinks'])->name('chapter.links');
Route::get('/chapter-resources', [PublicController::class, 'chapterResources'])->name('public.resources');
Route::get('/pdf-viewer', [PublicController::class, 'showPdf'])->name('pdf-viewer');
Route::get('/pdf-proxy', [PublicController::class, 'proxyGoogleDriveFile'])->name('pdf-proxy');
Route::get('/newchapter', [PublicController::class, 'editNewChapter'])->name('public.newchapter');
Route::post('/updatenewchapter', [PublicController::class, 'updateNewChapter'])->name('public.updatenewchapter');
Route::get('/newchaptersuccess', [PublicController::class, 'viewNewChapter'])->name('public.newchaptersuccess');
Route::get('/donation', [PublicController::class, 'editDonation'])->name('public.donation');
Route::post('/updatedonation', [PublicController::class, 'updateDonation'])->name('public.updatedonation');
Route::get('/donationsuccess', [PublicController::class, 'viewDonation'])->name('public.donationsuccess');
Route::post('/public-payment', [PublicController::class, 'processPublicPayment'])->name('public.payment');
Route::get('/newcoordinator', [PublicController::class, 'editNewCoordinator'])->name('public.newcoordinator');
Route::post('/updatenewcoordinator', [PublicController::class, 'updateNewCoordinator'])->name('public.updatenewcoordinator');
Route::get('/newcoordinatorsuccess', [PublicController::class, 'viewNewCoordinator'])->name('public.newcoordinatorsuccess');
Route::get('/newinquiry', [PublicController::class, 'editNewInquiry'])->name('public.newinquiry');
Route::post('/updatenewinquiry', [PublicController::class, 'updateNewInquiry'])->name('public.updatenewinquiry');
Route::get('/newinquirysuccess', [PublicController::class, 'viewNewInquiry'])->name('public.newinquirysuccess');
Route::get('/grantlist', [PublicController::class, 'viewGrantList'])->name('public.grantlist');

Route::get('/grantlist-pdf', [PublicController::class, 'generateGratList'])->name('pdf.grantlist');
Route::post('/grant-list-pdf', [PublicController::class, 'saveGratList'])->name('pdf.generategrantlist');


// In your routes/web.php or wherever you have routes
Route::get(config('sentemails.routepath'), [MySentEmailsController::class, 'index'])
    ->middleware(config('sentemails.middleware'))
    ->name('sentemails');

    Route::get('adminreports/sentemails/attachment-{id}', [MySentEmailsController::class, 'downloadAttachment'])
    ->middleware(['web', 'auth'])
    ->name('sentemails.downloadAttachment');

// Keep the other package routes
Route::get(config('sentemails.routepath').'/{id}', [\Dcblogdev\LaravelSentEmails\Controllers\SentEmailsController::class, 'show'])
    ->middleware(config('sentemails.middleware'))
    ->name('sentemails.show');

// Allow error log to be viewed without login
Route::get('techreports/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');
// Tech Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/techreports/adminemail', [TechReportController::class, 'AdminEmailList'])->name('techreports.adminemail');
    Route::post('/techreports/addadminemail', [TechReportController::class, 'addAdminEmail'])->name('techreports.addadminemail');
    Route::post('/techreports/updateadminemail/{id}', [TechReportController::class, 'updateAdminEmail'])->name('techreports.updateadminemail');
    Route::post('/techreports/deleteadminemail', [TechReportController::class, 'deleteAdminEmail'])->name('techreports.deleteadminemail');
    Route::get('/techreports/googledrive', [TechReportController::class, 'googleDriveList'])->name('techreports.googledrive');
    Route::post('/techreports/addgoogledrive', [TechReportController::class, 'addGoogleDrive'])->name('techreports.addgoogledrive');
    Route::post('/techreports/updategoogledrive/{id}', [TechReportController::class, 'updateGoogleDrive'])->name('techreports.updategoogledrive');
    Route::post('/techreports/deletegoogledrive', [TechReportController::class, 'deleteGoogleDrive'])->name('techreports.deletegoogledrive');
    Route::get('/techreports/chapterlist', [TechReportController::class, 'listActiveChapters'])->name('techreports.chapterlist');
        Route::get('/techreports/viewasactivechapter', [TechReportController::class, 'viewAsActiveChapter'])->name('techreports.viewaschapter.active');
                Route::get('/techreports/viewasdisbandchapter', [TechReportController::class, 'viewAsDisbandedChapter'])->name('techreports.viewaschapter.disbanded');
        Route::get('/techreports/viewaspendingchapter', [TechReportController::class, 'viewAsAsPendingChapter'])->name('techreports.viewaschapter.pending');
        Route::get('/techreports/viewasoutgoingchapter', [TechReportController::class, 'viewAsOutgoingChapter'])->name('techreports.viewaschapter.outgoing');

    Route::get('/techreports/chapterlistzapped', [TechReportController::class, 'listZappedChapters'])->name('techreports.chapterlistzapped');
    Route::get('/techreports/chapterlistpending', [TechReportController::class, 'listPendingChapters'])->name('techreports.chapterlistpending');
    Route::post('/techreports/updatechapterdelete', [TechReportController::class, 'updateChapterDelete'])->name('techreports.updatechapterdelete');
    Route::post('/techreports/updatecoordinatordelete', [TechReportController::class, 'updateCoordinatorDelete'])->name('techreports.updatecoordinatordelete');
    Route::post('/techreports/resetProbationSubmission', [TechReportController::class, 'resetProbationSubmission'])->name('techreports.resetProbationSubmission');
    Route::get('/techreports/eoy', [TechReportController::class, 'showEOY'])->name('techreports.eoy');
    Route::post('/techreports/eoy/resetyear', [TechReportController::class, 'resetYear'])->name('techreports.resetyear');
    Route::post('/techreports/eoy/resetyeareoy', [TechReportController::class, 'resetYearEOY'])->name('techreports.resetyeareoy');
    Route::post('/techreports/eoy/resetdisbandedusers', [TechReportController::class, 'resetDisbandedUsers'])->name('techreports.resetdisbandedusers');
    Route::post('/techreports/eoy/resetoutgoingusers', [TechReportController::class, 'resetOutgoingUsers'])->name('techreports.resetoutgoingusers');
    Route::post('/techreports/eoy/updateeoydatabase', [TechReportController::class, 'updateEOYDatabase'])->name('techreports.updateeoydatabase');
    Route::post('/techreports/eoy/updateeoydatabaseafter', [TechReportController::class, 'updateEOYDatabaseAFTERTesting'])->name('techreports.updateeoydatabaseafter');
    Route::post('/techreports/eoy/updatedatadatabase', [TechReportController::class, 'updateDataDatabase'])->name('techreports.updatedatadatabase');
    Route::post('/techreports/eoy/updateeoytesting', [TechReportController::class, 'updateEOYTesting'])->name('techreports.updateeoytesting');
    Route::post('/techreports/eoy/updateeoylive', [TechReportController::class, 'updateEOYLive'])->name('techreports.updateeoylive');
    Route::post('/techreports/eoy/updateunsubscribelists', [TechReportController::class, 'updateUnsubscribeLists'])->name('techreports.updateunsubscribelists');
    Route::post('/techreports/eoy/updatesubscribelists', [TechReportController::class, 'updateSubscribeLists'])->name('techreports.updatesubscribelists');
    Route::get('/techreports/conferencelist', [TechReportController::class, 'conferenceList'])->name('techreports.conferencelist');
    Route::get('/techreports/regionlist', [TechReportController::class, 'regionList'])->name('techreports.regionlist');
    Route::post('/techreports/updateregion/{id}', [TechReportController::class, 'updateRegion'])->name('techreports.updateregion');
    Route::get('/techreports/statelist', [TechReportController::class, 'stateList'])->name('techreports.statelist');
    Route::post('/techreports/updatestate/{id}', [TechReportController::class, 'updateState'])->name('techreports.updatestate');
});

// Admin Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    // Route::get('/adminreports/maillog', [AdminReportController::class, 'showMailLog'])->name('adminreports.maillog');
    // Route::get('/adminreports/maildetails/{id}', [AdminReportController::class, 'showMailDetails'])->name('adminreports.maildetails');
    // Route::get(config('sentemails.routepath').'/body/{id}', [\Dcblogdev\LaravelSentEmails\Controllers\SentEmailsController::class, 'body'])
    // ->middleware(config('sentemails.middleware'))->name('sentemails.body');
    Route::get('/adminreports/paymentlog', [AdminReportController::class, 'showPaymentLog'])->name('adminreports.paymentlog');
    Route::get('/adminreports/paymentdetails/{id}', [AdminReportController::class, 'showPaymentDetails'])->name('adminreports.paymentdetails');
    Route::get('/adminreports/donationlog', [AdminReportController::class, 'showDonationLog'])->name('adminreports.donationlog');
    Route::get('/adminreports/rereg', [AdminReportController::class, 'showReReg'])->name('adminreports.rereg');
    Route::get('/adminreports/reregedit/{id}', [AdminReportController::class, 'editReReg'])->name('adminreports.editrereg');
    Route::post('/adminreports/regdateupdate/{id}', [AdminReportController::class, 'updateReReg'])->name('adminreports.updaterereg');
    Route::get('/adminreports/inquiriesnotify', [AdminReportController::class, 'inquiriesNotify'])->name('adminreports.inquiriesnotify');
    Route::post('/adminreports/updateinquiries/{id}', [AdminReportController::class, 'updateInquiriesEmail'])->name('adminreports.updateinquiries');
    Route::get('/adminreports/downloads', [AdminReportController::class, 'showDownloads'])->name('adminreports.downloads');
});

// User Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::post('/userreports/updateuserdelete', [UserController::class, 'updateUserDelete'])->name('userreports.updateuserdelete');
    Route::get('/userreports/useradmin', [UserReportController::class, 'showUserAdmin'])->name('userreports.useradmin');
    Route::get('/userreports/duplicateuser', [UserReportController::class, 'showDuplicate'])->name('userreports.duplicateuser');
    Route::get('/userreports/duplicateboardid', [UserReportController::class, 'showDuplicateId'])->name('userreports.duplicateboardid');
    Route::get('/userreports/nopresident', [UserReportController::class, 'showNoPresident'])->name('userreports.nopresident');
    // Route::get('/userreports/nopresidentinactive', [UserReportController::class, 'showNoPresidentInactive'])->name('userreports.nopresidentinactive');
    Route::get('/userreports/addnewboard/{id}', [UserReportController::class, 'addBoardNew'])->name('userreports.addnewboard');
    Route::post('/userreports/updatenewboard/{id}', [UserReportController::class, 'updateBoardNew'])->name('userreports.updatenewboard');
    // Route::get('/userreports/noactivechapter', [UserReportController::class, 'showNoActiveChapter'])->name('userreports.noactivechapter');
    Route::get('/userreports/usernoactiveboard', [UserReportController::class, 'showUserNoActiveBoard'])->name('userreports.usernoactiveboard');
    Route::get('/userreports/usernoactivecoord', [UserReportController::class, 'showUserNoActiveCoord'])->name('userreports.usernoactivecoord');
    Route::get('/userreports/edituser/{id}', [UserReportController::class, 'editUserInformation'])->name('userreports.edituser');
    Route::post('/userreports/updateuser/{id}', [UserReportController::class, 'updateUserInformation'])->name('userreports.updateuser');
    Route::get('/userreports/noactiveboard', [UserReportController::class, 'showNoActiveBoard'])->name('userreports.noactiveboard');
    Route::get('/userreports/edituserboard/{id}', [UserReportController::class, 'editUserBoardInformation'])->name('userreports.edituserboard');
    Route::post('/userreports/updateuserboard/{id}', [UserReportController::class, 'updateUserBoardInformation'])->name('userreports.updateuserboard');
    Route::get('/userreports/editusercoord/{id}', [UserReportController::class, 'editUserCoordInformation'])->name('userreports.editusercoord');
    Route::post('/userreports/updateusercoord/{id}', [UserReportController::class, 'updateUserCoordInformation'])->name('userreports.updateusercoord');
    Route::get('/userreports/outgoingboard', [UserReportController::class, 'showOutgoingBoard'])->name('userreports.outgoingboard');
    Route::get('/userreports/disbandedboard', [UserReportController::class, 'showDisbandedBoard'])->name('userreports.disbandedboard');
});

// Resources Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/resources/bugs', [ResourcesController::class, 'showBugs'])->name('resources.bugs');
    Route::post('/resources/addbugs', [ResourcesController::class, 'addBugs'])->name('resources.addbugs');
    Route::post('/resources/updatebugs/{id}', [ResourcesController::class, 'updateBugs'])->name('resources.updatebugs');
    Route::get('/resources/resources', [ResourcesController::class, 'showResources'])->name('resources.resources');
    Route::post('/resources/addresources', [ResourcesController::class, 'addResources'])->name('resources.addresources');
    Route::post('/resources/updateresources/{id}', [ResourcesController::class, 'updateResources'])->name('resources.updateresources');
    Route::get('/resources/toolkit', [ResourcesController::class, 'showToolkit'])->name('resources.toolkit');
    Route::post('/resources/addtoolkit', [ResourcesController::class, 'addToolkit'])->name('resources.addtoolkit');
    Route::post('/resources/updatetoolkit/{id}', [ResourcesController::class, 'updateToolkit'])->name('resources.updatetoolkit');
    Route::get('/resources/elearning', [ResourcesController::class, 'showELearning'])->name('resources.elearning');
});

// Payment Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/payment/reregistration', [PaymentController::class, 'showChapterReRegistration'])->name('payment.chapreregistration');
    Route::get('/payment/donations', [PaymentController::class, 'showRptDonations'])->name('payment.chapdonations');
    Route::get('/payment/chapterpaymentedit/{id}', [PaymentController::class, 'editChapterPayment'])->name('payment.editpayment');
    Route::get('/payment/chapterpaymenthistory/{id}', [PaymentController::class, 'viewPaymentHistory'])->name('payment.paymenthistory');
    Route::post('/payment/chapterpaymentupdate/{id}', [PaymentController::class, 'updateChapterPayment'])->name('payment.updatepayment');
    Route::get('/payment/reregistrationreminder', [PaymentController::class, 'createChapterReRegistrationReminder'])->name('payment.chapreregreminder');
    Route::get('/payment/reregistrationlatereminder', [PaymentController::class, 'createChapterReRegistrationLateReminder'])->name('payment.chaprereglatereminder');
    Route::get('/payment/grantlist', [PaymentController::class, 'showGrantList'])->name('payment.grantlist');
    Route::get('/payment/grantdetailsedit/{id}', [PaymentController::class, 'editGrantDetails'])->name('payment.editgrantdetails');
    Route::post('/payment/grantdetailsupdate/{id}', [PaymentController::class, 'UpdateGrantDetails'])->name('payment.updategrantdetails');
    Route::get('/payment/unsubmitgrant/{id}', [PaymentController::class, 'updateUnsubmitGrantRequest']);
    Route::get('/payment/cleargrantreview/{id}', [PaymentController::class, 'updateClearGrantReview']);
});

// Inquiries Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/inquiries/inquiries', [ChapterController::class, 'showChapterInquiries'])->name('chapters.chapinquiries');
    Route::get('/inquiries/inquirieszapped', [ChapterController::class, 'showZappedChapterInquiries'])->name('chapters.chapinquirieszapped');

    Route::get('/inquiries/inquiryapplication', [InquiriesController::class, 'showInquiryApplication'])->name('inquiries.inquiryapplication');
    Route::get('/inquiries/inquiryapplicationedit/{id}', [InquiriesController::class, 'editInquiryApplication'])->name('inquiries.editinquiryapplication');
    Route::post('/inquiries/inquiryapplicationupdate/{id}', [InquiriesController::class, 'updateInquiryApplication'])->name('inquiries.updateinquiryapplication');
    Route::post('/inquiries/updateinquiryresponse/{id}', [InquiriesController::class, 'updateInquiryResponse'])->name('inquiries.updateinquiryresponse');
    Route::post('/inquiries/clearinquiryresponse/{id}', [InquiriesController::class, 'clearInquiryResponse'])->name('inquiries.clearinquiryresponse');
});

// Online Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/online/website', [ChapterController::class, 'showChapterWebsite'])->name('chapters.chapwebsite');
    Route::get('/online/socialmedia', [ChapterController::class, 'showRptSocialMedia'])->name('chapters.chapsocialmedia');
    Route::get('/online/websiteedit/{id}', [ChapterController::class, 'editChapterWebsite'])->name('chapters.editwebsite');
    Route::post('/online/websiteupdate/{id}', [ChapterController::class, 'updateChapterWebsite'])->name('chapters.updatewebsite');
});

// Chapter Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/chapter/chapterlist', [ChapterController::class, 'showChapters'])->name('chapters.chaplist');
    Route::get('/chapter/zapped', [ChapterController::class, 'showZappedChapter'])->name('chapters.chapzapped');
    Route::get('/chapter/details/{id}', [ChapterController::class, 'viewChapterDetails'])->name('chapters.view');
    Route::get('/chapters/checkein', [ChapterController::class, 'checkEIN'])->name('chapters.checkein');
    Route::post('/chapter/details/updateein', [ChapterController::class, 'updateEIN'])->name('chapters.updateein');
     Route::post('/chapter/details/updatename', [ChapterController::class, 'updateName'])->name('chapters.updatename');
    Route::post('/chapter/updatedisband', [ChapterController::class, 'updateChapterDisband'])->name('chapters.updatechapdisband');
    Route::post('/chapter/unzap', [ChapterController::class, 'updateChapterUnZap'])->name('chapters.updatechapterunzap');
    Route::get('/chapter/detailsedit/{id}', [ChapterController::class, 'editChapterDetails'])->name('chapters.edit');
    Route::post('/chapter/detailsupdate/{id}', [ChapterController::class, 'updateChapterDetails'])->name('chapters.update');
    Route::get('/chapter/boardedit/{id}', [ChapterController::class, 'editChapterBoard'])->name('chapters.editboard');
    Route::post('/chapter/boardupdate/{id}', [ChapterController::class, 'updateChapterBoard'])->name('chapters.updateboard');
    // Route::get('/online/website', [ChapterController::class, 'showChapterWebsite'])->name('chapters.chapwebsite');
    // Route::get('/online/socialmedia', [ChapterController::class, 'showRptSocialMedia'])->name('chapters.chapsocialmedia');
    // Route::get('/online/websiteedit/{id}', [ChapterController::class, 'editChapterWebsite'])->name('chapters.editwebsite');
    // Route::post('/online/websiteupdate/{id}', [ChapterController::class, 'updateChapterWebsite'])->name('chapters.updatewebsite');
});

// ChapterReport Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/chapterreports/chapterstatus', [ChapterReportController::class, 'showRptChapterStatus'])->name('chapreports.chaprptchapterstatus');
    Route::get('/chapterreports/einstatus', [ChapterReportController::class, 'showRptEINstatus'])->name('chapreports.chaprpteinstatus');
    Route::get('/chapterreports/irsedit/{id}', [ChapterReportController::class, 'editChapterIRS'])->name('chapreports.editirs');
    Route::post('/chapterreports/irsupdate/{id}', [ChapterReportController::class, 'updateChapterIRS'])->name('chapreports.updateirs');
    Route::get('/chapterreports/newchapters', [ChapterReportController::class, 'showRptNewChapters'])->name('chapreports.chaprptnewchapters');
    Route::get('/chapterreports/largechapters', [ChapterReportController::class, 'showRptLargeChapters'])->name('chapreports.chaprptlargechapters');
    Route::get('/chapterreports/probation', [ChapterReportController::class, 'showRptProbation'])->name('chapreports.chaprptprobation');
    Route::get('/chapterreports/coordinators', [ChapterReportController::class, 'showRptChapterCoordinators'])->name('chapreports.chaprptcoordinators');
});

// Email Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::post('/chapter/sendstartup', [EmailController::class, 'sendChapterStartup'])->name('chapters.sendstartup');
    Route::post('/chapter/sendnewchapter', [EmailController::class, 'sendNewChapterEmail'])->name('chapters.sendnewchapter');
    Route::post('/chapter/sendchapter', [EmailController::class, 'sendChapterEmail'])->name('chapters.sendchapter');
    Route::post('/chapter/sendcoord', [EmailController::class, 'sendCoordEmail'])->name('coordinators.sendcoord');
    Route::post('/chapter/sendcoordup', [EmailController::class, 'sendCoordUplineEmail'])->name('coordinators.sendcoordup');
    Route::post('/chapter/sendchapterrereg', [EmailController::class, 'sendChapterReReg'])->name('chapters.sendchapterrereg');
    Route::post('/chapter/sendchapterrereglate', [EmailController::class, 'sendChapterReRegLate'])->name('chapters.sendchapterrereglate');
    Route::get('/eoy/boardreport/reminder', [EmailController::class, 'sendEOYBoardReportReminder'])->name('eoyreports.eoyboardreportreminder');
    Route::get('/eoy/financialreport/reminder', [EmailController::class, 'sendEOYFinancialReportReminder'])->name('eoyreports.eoyfinancialreportreminder');
    Route::get('/eoy/status/reminder', [EmailController::class, 'sendEOYStatusReminder'])->name('eoyreports.eoystatusreminder');
    Route::post('/inquiries/sendnochapter', [EmailController::class, 'sendNoChapterInquiries'])->name('inquiries.sendnochapter');
    Route::post('/inquiries/sendyeschapter', [EmailController::class, 'sendYesChapterInquiries'])->name('inquiries.sendyeschapter');
    Route::post('/inquiries/sendchapter', [EmailController::class, 'sendChapterInquiryEmailModal'])->name('inquiries.sendchapter');
    Route::post('/inquiries/sendmember', [EmailController::class, 'sendMemberInquiryEmailModal'])->name('inquiries.sendmember');
});

// New Chapter/Coordinator Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/application/new', [ChapterController::class, 'addChapterNew'])->name('chapters.addnew');
    Route::post('/application/newupdate', [ChapterController::class, 'updateChapterNew'])->name('chapters.updatenew');
    Route::get('/application/newint', [ChapterController::class, 'addChapterNewInt'])->name('chapters.addnewint');
    Route::post('/application/newupdateint', [ChapterController::class, 'updateChapterNewInt'])->name('chapters.updatenewint');
    Route::get('/application/chapterpending', [ChapterController::class, 'showPendingChapters'])->name('chapters.chaplistpending');
    Route::get('/application/chapterpendingedit/{id}', [ChapterController::class, 'editPendingChapterDetails'])->name('chapters.editpending');
    Route::post('/application/chapterpendingupdate/{id}', [ChapterController::class, 'updatePendingChapterDetails'])->name('chapters.updatepending');
    Route::post('/application/chapterupdateapprove', [ChapterController::class, 'updateApproveChapter'])->name('chapters.updateapprove');
    Route::post('/application/chapterupdatedecline', [ChapterController::class, 'updateDeclineChapter'])->name('chapters.updatedecline');
    Route::get('/application/chapterdeclined', [ChapterController::class, 'showNotApprovedChapters'])->name('chapters.chaplistdeclined');
    Route::get('/application/coordapplication/{id}', [CoordinatorController::class, 'viewCoordApplication'])->name('coordinators.viewapplication');
    Route::get('/application/coordpending', [CoordinatorController::class, 'showPendingCoordinator'])->name('coordinators.coordpending');
    Route::post('/application/coordpendingdetailsupdate/{id}', [CoordinatorController::class, 'updatePendingCoordinatorDetails'])->name('coordinators.updatepending');
    Route::post('/application/coordupdateapprove', [CoordinatorController::class, 'updateApproveApplication'])->name('coordinators.updateapprove');
    Route::post('/application/coordupdatedecline', [CoordinatorController::class, 'updateRejectApplication'])->name('coordinators.updatereject');
    Route::get('/application/coordrejected', [CoordinatorController::class, 'showRejectedCoordinator'])->name('coordinators.coordrejected');
});

// Coordinator Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/coordinator/coordlist', [CoordinatorController::class, 'showCoordinators'])->name('coordinators.coordlist');
    Route::get('/coordinator/retired', [CoordinatorController::class, 'showRetiredCoordinator'])->name('coordinators.coordretired');
    Route::get('/coordinator/new', [CoordinatorController::class, 'addCoordNew'])->name('coordinators.editnew');
    Route::post('/coordinator/newupdate', [CoordinatorController::class, 'updateCoordNew'])->name('coordinators.updatenew');
    Route::get('/coordinator/details/{id}', [CoordinatorController::class, 'viewCoordDetails'])->name('coordinators.view');
    Route::post('/coordinator/details/sendbigsister', [CoordinatorController::class, 'sendBigSisterEmail'])->name('coordinators.sendbigsister');
    Route::post('/coordinator/details/updatecardsent', [CoordinatorController::class, 'updateCardSent'])->name('coordinators.updatecardsent');
    Route::post('/coordinator/details/updateonleave', [CoordinatorController::class, 'updateOnLeave'])->name('coordinators.updateonleave');
    Route::post('/coordinator/details/updateremoveleave', [CoordinatorController::class, 'updateRemoveLeave'])->name('coordinators.updateremoveleave');
    Route::post('/coordinator/details/updateretire', [CoordinatorController::class, 'updateRetire'])->name('coordinators.updateretire');
    Route::post('/coordinator/details/updateunretire', [CoordinatorController::class, 'updateUnRetire'])->name('coordinators.updateunretire');
    Route::get('/coordinator/details/editrole/{id}', [CoordinatorController::class, 'editCoordRole'])->name('coordinators.editrole');
    Route::post('/coordinator/details/updaterole/{id}', [CoordinatorController::class, 'updateCoordRole'])->name('coordinators.updaterole');
    Route::get('/coordinator/details/edit/{id}', [CoordinatorController::class, 'editCoordDetails'])->name('coordinators.editdetails');
    Route::post('/coordinator/details/update/{id}', [CoordinatorController::class, 'updateCoordDetails'])->name('coordinators.updatedetails');
    Route::get('/coordinator/details/editrecognition/{id}', [CoordinatorController::class, 'editCoordRecognition'])->name('coordinators.editrecognition');
    Route::post('/coordinator/details/updaterecognition/{id}', [CoordinatorController::class, 'updateCoordRecognition'])->name('coordinators.updaterecognition');
    Route::get('viewprofile', [CoordinatorController::class, 'viewCoordProfile'])->name('coordinators.viewprofile');
    Route::get('/profile/profile', [CoordinatorController::class, 'editCoordProfile'])->name('coordinators.profile');
    Route::post('/profile/profileupdate', [CoordinatorController::class, 'updateCoordProfile'])->name('coordinators.profileupdate');
});

// CoordinatorReport Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/coordreports/volunteerutilization', [CoordinatorReportController::class, 'showRptVolUtilization'])->name('coordreports.coordrptvolutilization');
    Route::get('/coordreports/appreciation', [CoordinatorReportController::class, 'showRptAppreciation'])->name('coordreports.coordrptappreciation');
    Route::get('/coordreports/birthdays', [CoordinatorReportController::class, 'showRptBirthdays'])->name('coordreports.coordrptbirthdays');
    Route::get('/coordreports/reportingtree', [CoordinatorReportController::class, 'showRptReportingTree'])->name('coordreports.coordrptreportingtree');
});

// Export Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/export/chapter', [ExportController::class, 'indexChapter'])->name('export.chapter');
    Route::get('/export/zapchapter', [ExportController::class, 'indexZappedChapter'])->name('export.zapchapter');
    Route::get('/export/coordinator', [ExportController::class, 'indexCoordinator'])->name('export.coordinator');
    Route::get('/export/retiredcoordinator', [ExportController::class, 'indexRetiredCoordinator'])->name('export.retiredcoordinator');
    Route::get('/export/appreciation', [ExportController::class, 'indexAppreciation'])->name('export.appreciation');
    Route::get('/export/chaptercoordinator', [ExportController::class, 'indexChapterCoordinator'])->name('export.chaptercoordinator');
    Route::get('/export/rereg', [ExportController::class, 'indexReReg'])->name('export.rereg');
    Route::get('/export/einstatus', [ExportController::class, 'indexEINStatus'])->name('export.einstatus');
    Route::get('/export/eoystatus', [ExportController::class, 'indexEOYStatus'])->name('export.eoystatus');
    Route::get('/export/intchapter', [ExportController::class, 'indexInternationalChapter'])->name('export.intchapter');
    Route::get('/export/intzapchapter', [ExportController::class, 'indexInternationalZapChapter'])->name('export.intzapchapter');
    Route::get('/export/intcoordinator', [ExportController::class, 'indexIntCoordinator'])->name('export.intcoordinator');
    Route::get('/export/intretcoordinator', [ExportController::class, 'indexIntRetCoordinator'])->name('export.intretcoordinator');
    Route::get('/export/intrereg', [ExportController::class, 'indexIntReReg'])->name('export.intrereg');
    Route::get('/export/inteinstatus', [ExportController::class, 'indexIntEINStatus'])->name('export.inteinstatus');
    Route::get('/export/intirsfiling', [ExportController::class, 'indexInternationalIRSFiling'])->name('export.intirsfiling');
    Route::get('/export/inteoystatus', [ExportController::class, 'indexIntEOYStatus'])->name('export.inteoystatus');
    Route::get('/export/constantcontact', [ExportController::class, 'indexConstantContact'])->name('export.constantcontact');
});

// EOYReports Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/eoyreports/status', [EOYReportController::class, 'showEOYStatus'])->name('eoyreports.eoystatus');
    Route::get('/eoyreports/editstatus/{id}', [EOYReportController::class, 'editEOYDetails'])->name('eoyreports.view');
    Route::post('/eoyreports/updatestatus/{id}', [EOYReportController::class, 'updateEOYDetails'])->name('eoyreports.update');
    Route::get('/eoyreports/boardreport', [EOYReportController::class, 'showEOYBoardReport'])->name('eoyreports.eoyboardreport');
    Route::get('/eoyreports/editboardreport/{id}', [EOYReportController::class, 'editBoardReport'])->name('eoyreports.editboardreport');
    Route::post('/eoyreports/editboardreport/{id}', [EOYReportController::class, 'editBoardReport'])->name('eoyreports.activateboardreport');
    Route::post('eoyreports/updateboardreport/{id}', [EOYReportController::class, 'updateEOYBoardReport'])->name('eoyreports.updateboardreport');
    Route::get('/eoyreports/financialreport', [EOYReportController::class, 'showEOYFinancialReport'])->name('eoyreports.eoyfinancialreport');
    Route::get('/eoyreports/editfinancialreview/{id}', [EOYReportController::class, 'editFinancialReview'])->name('eoyreports.editfinancialreview');
    Route::post('/eoyreports/updatefinancialreport/{id}', [EOYReportController::class, 'updateEOYFinancialReport'])->name('eoyreports.updatefinancialreport');
    Route::get('/eoyreports/unsubmit/{id}', [EOYReportController::class, 'updateUnsubmit']);
    Route::get('/eoyreports/unsubmitfinal/{id}', [EOYReportController::class, 'updateUnsubmitFinal']);
    Route::get('/eoyreports/clearreview/{id}', [EOYReportController::class, 'updateClearReview']);
    Route::get('/eoyreports/attachments', [EOYReportController::class, 'showEOYAttachments'])->name('eoyreports.eoyattachments');
    Route::get('/eoyreports/editattachments/{id}', [EOYReportController::class, 'editEOYAttachments'])->name('eoyreports.editattachments');
    Route::post('/eoyreports/updateattachments/{id}', [EOYReportController::class, 'updateEOYAttachments'])->name('eoyreports.updateattachments');
    Route::get('/eoyreports/boundaries', [EOYReportController::class, 'showEOYBoundaries'])->name('eoyreports.eoyboundaries');
    Route::get('/eoyreports/editboundaries/{id}', [EOYReportController::class, 'editEOYBoundaries'])->name('eoyreports.editboundaries');
    Route::post('/eoyreports/updateboundaries/{id}', [EOYReportController::class, 'updateEOYBoundaries'])->name('eoyreports.updateboundaries');
    Route::get('/eoyreports/awards', [EOYReportController::class, 'showEOYAwards'])->name('eoyreports.eoyawards');
    Route::get('/eoyreports/editawards/{id}', [EOYReportController::class, 'editEOYAwards'])->name('eoyreports.editawards');
    Route::post('/eoyreports/updateawards/{id}', [EOYReportController::class, 'updateEOYAwards'])->name('eoyreports.updateawards');
    Route::get('/eoyreports/awardhistory/{id}', [EOYReportController::class, 'viewEOYAwardsHistory'])->name('eoyreports.awardhistory');
    Route::get('/eoyreports/irssubmission', [EOYReportController::class, 'showIRSSubmission'])->name('eoyreports.eoyirssubmission');
    Route::get('/eoyreports/editirssubmission/{id}', [EOYReportController::class, 'editIRSSubmission'])->name('eoyreports.editirssubmission');
    Route::post('/eoyreports/updateirssubmission/{id}', [EOYReportController::class, 'updateIRSSubmission'])->name('eoyreports.updateirssubmission');
});

Route::middleware('auth')->group(function () {
    Route::get('/board/newchapterstatus/{id}', [BoardPendingController::class, 'showNewChapterStatus'])->name('board.newchapterstatus');
    Route::get('/board/chapterprofile/{id}', [BoardController::class, 'chapterProfile'])->name('board.chapterprofile');
    Route::get('/board/profile/{id}', [BoardController::class, 'editProfile'])->name('board.editprofile');
    Route::post('/board/profileupdate/{id}', [BoardController::class, 'updateProfile'])->name('board.updateprofile');
    Route::get('/board/boardreport/{id}', [BoardController::class, 'editBoardReport'])->name('board.editboardreport');
    Route::post('/board/boardreportupatea/{id}', [BoardController::class, 'updateBoardReport'])->name('board.updateboardreport');
    Route::get('/board/probation/{id}', [BoardController::class, 'editProbationSubmission'])->name('board.editprobation');
    Route::post('/board/probationupdate/{id}', [BoardController::class, 'updateProbationSubmission'])->name('board.updateprobation');
    Route::get('/board/resources/{id}', [BoardController::class, 'viewResources'])->name('board.viewresources');
    Route::get('/board/resources/manual/{id}', [BoardController::class, 'editManualOrderForm'])->name('board.editmanual');
    Route::get('/board/elearning/{id}', [BoardController::class, 'viewELearning'])->name('board.viewelearning');
});

// Board Payment Controller Routes...Board Login Required
Route::middleware('auth')->group(function () {
    Route::post('/process-payment', [BoardPaymentController::class, 'reRegistrationPayment'])->name('process.payment');
    Route::post('/process-donation', [BoardPaymentController::class, 'm2mPayment'])->name('process.donation');
    Route::post('/process-manual', [BoardPaymentController::class, 'manualPayment'])->name('process.manual');
    Route::get('/board/reregpayment/{id}', [BoardPaymentController::class, 'editReregistrationPaymentForm'])->name('board.editreregpayment');
    Route::get('/board/donation/{id}', [BoardPaymentController::class, 'editDonationForm'])->name('board.editdonate');
    Route::get('/board/grantrequestlist/{id}', [BoardController::class, 'viewGrantRequestList'])->name('board.viewgrantrequestlist');
    Route::get('/board/newgrantrequest/{id}', [BoardController::class, 'showNewGrantRequest'])->name('board.newgrantrequest');
    Route::post('/board/newgrantrequestupdate/{id}', [BoardController::class, 'updateNewGrantRequest'])->name('board.updatenewgrantrequest');
    Route::get('/board/grantdetails/{id}', [BoardController::class, 'viewGrantDetails'])->name('board.viewgrantdetails');
    Route::post('/board/updategrantrequest/{id}', [BoardController::class, 'updateGrantRequest'])->name('board.updategrantrequest');
});

// Financial Report Controller Routes...Board Login Required
Route::middleware('auth')->group(function () {
    Route::get('/board/financialreport/{id}', [FinancialReportController::class, 'editFinancialReport'])->name('board.editfinancialreport');
    Route::post('/board/financialreportupdate/{id}', [FinancialReportController::class, 'updateFinancialReport'])->name('board.updatefinancialreport');
    Route::get('/board/disbandchecklist/{id}', [FinancialReportController::class, 'editDisbandChecklist'])->name('board.editdisbandchecklist');
    Route::post('/board/disbandchecklistupdate/{id}', [FinancialReportController::class, 'updateDisbandChecklist'])->name('board.updatedisbandchecklist');
    Route::get('/board/financialreportfinal/{id}', [FinancialReportController::class, 'editFinancialReportFinal'])->name('board.editfinancialreportfinal');
    Route::post('/board/financialreportfinalupdate/{id}', [FinancialReportController::class, 'updateFinancialReportFinal'])->name('board.updatefinancialreportfinal');
    Route::post('/board/disbandreportupdate/{id}', [FinancialReportController::class, 'updateDisbandReport'])->name('board.updatedisbandreport');
});


Route::middleware('auth')->group(function () {
    Route::get('/board-new/newchapterstatus/{id}', [BoardPendingControllerNew::class, 'showNewChapterStatus'])->name('board-new.newchapterstatus');
    Route::get('/board-new/chapterprofile/{id}', [BoardControllerNew::class, 'chapterProfile'])->name('board-new.chapterprofile');
    Route::get('/board-new/board/{id}', [BoardControllerNew::class, 'editBoard'])->name('board-new.editboard');
    Route::post('/board-new/boardupdate/{id}', [BoardControllerNew::class, 'updateBoard'])->name('board-new.updateboard');
    Route::get('/board-new/documents/{id}', [BoardControllerNew::class, 'viewDocuments'])->name('board-new.viewdocuments');
     Route::get('/board-new/rereghistory/{id}', [BoardControllerNew::class, 'viewReRegHistory'])->name('board-new.viewrereghistory');
    Route::get('/board-new/donationhistory/{id}', [BoardControllerNew::class, 'viewDonationHistory'])->name('board-new.viewdonationhistory');
    Route::get('/board-new/endofyear/{id}', [BoardControllerNew::class, 'viewEndOfYear'])->name('board-new.viewendofyear');
    Route::get('/board-new/endofyear/boardreport/{id}', [BoardControllerNew::class, 'editBoardReport'])->name('board-new.editboardreport');
    Route::post('/board-new/endofyear/boardreportupatea/{id}', [BoardControllerNew::class, 'updateBoardReport'])->name('board-new.updateboardreport');
    Route::get('/board-new/probation/{id}', [BoardControllerNew::class, 'editProbationSubmission'])->name('board-new.editprobation');
    Route::post('/board-new/probationupdate/{id}', [BoardControllerNew::class, 'updateProbationSubmission'])->name('board-new.updateprobation');
    Route::get('/board-new/online/{id}', [BoardControllerNew::class, 'editOnlineInfo'])->name('board-new.editonline');
    Route::post('/board-new/onlineupdate/{id}', [BoardControllerNew::class, 'updateOnlineInfo'])->name('board-new.updateonline');
    Route::get('/board-new/resources/{id}', [BoardControllerNew::class, 'viewResources'])->name('board-new.viewresources');
    Route::get('/board-new/resources/manual/{id}', [BoardControllerNew::class, 'editManualOrderForm'])->name('board-new.editmanual');
    Route::get('/board-new/elearning/{id}', [BoardControllerNew::class, 'viewELearning'])->name('board-new.viewelearning');
    Route::get('/board-new/profile/{id}', [BoardControllerNew::class, 'editBoardProfile'])->name('board-new.profile');
    Route::post('/board-new/profileupdate/{id}', [BoardControllerNew::class, 'updateBoardProfile'])->name('board-new.updateprofile');
});

// Board Payment Controller Routes...Board Login Required
Route::middleware('auth')->group(function () {
    Route::get('/board-new/reregpayment/{id}', [BoardPaymentController::class, 'editReregistrationPaymentFormNEW'])->name('board-new.editreregpayment');
    Route::get('/board-new/donation/{id}', [BoardPaymentController::class, 'editDonationFormNEW'])->name('board-new.editdonate');
    Route::get('/board-new/grantrequestlist/{id}', [BoardControllerNew::class, 'viewGrantRequestList'])->name('board-new.viewgrantrequestlist');
    Route::get('/board-new/newgrantrequest/{id}', [BoardControllerNew::class, 'showNewGrantRequest'])->name('board-new.newgrantrequest');
    Route::post('/board-new/newgrantrequestupdate/{id}', [BoardControllerNew::class, 'updateNewGrantRequest'])->name('board-new.updatenewgrantrequest');
    Route::get('/board-new/grantdetails/{id}', [BoardControllerNew::class, 'viewGrantDetails'])->name('board-new.viewgrantdetails');
    Route::post('/board-new/updategrantrequest/{id}', [BoardControllerNew::class, 'updateGrantRequest'])->name('board-new.updategrantrequest');
});

// Financial Report Controller Routes...Board Login Required
Route::middleware('auth')->group(function () {
    Route::get('/board-new/endofyear/financialreport/{id}', [FinancialReportControllerNew::class, 'editFinancialReport'])->name('board-new.editfinancialreport');
    Route::post('/board-new/endofyear/financialreportupdate/{id}', [FinancialReportControllerNew::class, 'updateFinancialReport'])->name('board-new.updatefinancialreport');
    Route::get('/board-new/disbandchecklist/{id}', [FinancialReportControllerNew::class, 'editDisbandChecklist'])->name('board-new.editdisbandchecklist');
    Route::post('/board-new/disbandchecklistupdate/{id}', [FinancialReportControllerNew::class, 'updateDisbandChecklist'])->name('board-new.updatedisbandchecklist');
    Route::get('/board-new/financialreportfinal/{id}', [FinancialReportControllerNew::class, 'editFinancialReportFinal'])->name('board-new.editfinancialreportfinal');
    Route::post('/board-new/financialreportfinalupdate/{id}', [FinancialReportControllerNew::class, 'updateFinancialReportFinal'])->name('board-new.updatefinancialreportfinal');
    Route::post('/board-new/disbandreportupdate/{id}', [FinancialReportControllerNew::class, 'updateDisbandReport'])->name('board-new.updatedisbandreport');
});

// PDF Controller Routes...Used for Board & Coordinator Layouts
Route::middleware('auth')->group(function () {
    Route::get('/board/chapteringoodstanding/pdf/{id}', [PDFController::class, 'generateGoodStanding'])->name('pdf.chapteringoodstanding');
    Route::get('/board/financialreport/pdf/{id}', [PDFController::class, 'generateFinancialReport'])->name('pdf.financialreport');
    Route::post('/financial-report-pdf', [PDFController::class, 'saveFinancialReport'])->name('pdf.generatefinancialreport');
    Route::post('/generate-probation-letter', [PDFController::class, 'saveProbationLetter'])->name('pdf.generateProbationLetter');
    Route::get('/board/newchapfaxcover/pdf/{id}', [PDFController::class, 'generateNewChapterFaxCover'])->name('pdf.newchapfaxcover');
    Route::get('/board/eodeptfaxcover/pdf', [PDFController::class, 'generateEODeptFaxCover'])->name('pdf.eodeptfaxcover');
    Route::get('/board/combinedsubordinatefiling/pdf', [PDFController::class, 'generateCombinedIRSSubordinateFiling'])->name('pdf.combinedsubordinatefiling');
    Route::get('/board/combinedirsupdates/pdf', [PDFController::class, 'generateCombinedIRSUpdates'])->name('pdf.combinedirsupdates');
    Route::get('/board/combinednamechangeletter/pdf/{id}', [PDFController::class, 'generateCombinedNameChangeLetter'])->name('pdf.combinednamechangeletter');
    Route::get('/board/combinedirsfilingcorrections/pdf', [PDFController::class, 'generateCombinedIRSFilingCorrections'])->name('pdf.combinedirsfilingcorrections');
    Route::get('/board/grantrequest/pdf/{id}', [PDFController::class, 'generateFGrantRequest'])->name('pdf.grantrequest');
    Route::post('/grant-request-pdf', [PDFController::class, 'saveGrantRequest'])->name('pdf.generategrantrequest');
});

// Google Controller -- Uploading Files Routes...Used for Board & Coordinator Layouts
Route::middleware('auth')->group(function () {
    Route::post('/files/storeEIN/{id}', [GoogleController::class, 'storeEIN']);
    Route::post('/files/storeRoster/{id}', [GoogleController::class, 'storeRoster']);
    Route::post('/files/store990n/{id}', [GoogleController::class, 'store990N']);
    Route::post('/files/storeStatement1/{id}', [GoogleController::class, 'storeStatement1']);
    Route::post('/files/storeStatement2/{id}', [GoogleController::class, 'storeStatement2']);
    Route::post('/files/storeAward/{id}', [GoogleController::class, 'storeAward']);
    Route::post('/files/storeResources/{id}', [GoogleController::class, 'storeResources'])->name('store.resources');
    Route::post('/files/storeToolkit/{id}', [GoogleController::class, 'storeToolkit'])->name('store.toolkit');
    Route::post('/files/storePhotos/{id}', [GoogleController::class, 'storePhotos']);
});

// Forum Subscription Controller Routes...Used for Board & Coordinator Layouts
Route::middleware('auth')->group(function () {
    Route::get('/listadmin/chaptersubscriptionlist', [ForumSubscriptionController::class, 'showChapterListSubscriptions'])->name('forum.chaptersubscriptionlist');
    Route::get('/listadmin/coordinatorsubscriptionlist', [ForumSubscriptionController::class, 'showCoordinatorListSubscriptions'])->name('forum.coordinatorsubscriptionlist');
    Route::post('/listadmin/subscribecategory', [ForumSubscriptionController::class, 'subscribeCategory'])->name('forum.subscribecategory');
    Route::post('/listadmin/unsubscribecategory', [ForumSubscriptionController::class, 'unsubscribeCategory'])->name('forum.unsubscribecategory');
    Route::post('/listadmin/coordinatorlist/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddCoordinatorsList'])->name('forum.coordinatorlist.bulk-subscribe');
    Route::post('/listadmin/coordinatorboardlist/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddCoordinatorsBoardList'])->name('forum.coordinatorboardlist.bulk-subscribe');
    Route::post('/listadmin/coordinatorpublidannouncement/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddCoordinatorsPublicAnnounceements'])->name('forum.coordinatorpublidannouncement.bulk-subscribe');
    Route::post('/listadmin/coordinatorboardlist/bulk-unsubscribe', [ForumSubscriptionController::class, 'bulkRemoveCoordinatorsBoardList'])->name('forum.coordinatorboardlist.bulk-unsubscribe');
    Route::post('/listadmin/boardlist/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddBoardList'])->name('forum.boardlist.bulk-subscribe');
    Route::post('/listadmin/publcannouncements/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddPublicAnnouncements'])->name('forum.publcannouncements.bulk-subscribe');
    Route::post('/listadmin/boardlist/bulk-unsubscribe', [ForumSubscriptionController::class, 'bulkRemoveBoardList'])->name('forum.boardlist.bulk-unsubscribe');
    Route::post('/listadmin/boardboardlist/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddBoardBoardList'])->name('forum.boardboardlist.bulk-subscribe');
    Route::post('/listadmin/boardpublcannouncements/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddBoardPublicAnnouncements'])->name('forum.boardpublcannouncements.bulk-subscribe');
    Route::post('/listadmin/boardboardlist/bulk-unsubscribe', [ForumSubscriptionController::class, 'bulkRemoveBoardBoardList'])->name('forum.boardboardlist.bulk-unsubscribe');
    Route::post('/listadmin/boardpublcannouncements/bulk-unsubscribe', [ForumSubscriptionController::class, 'bulkRemoveBoardPublicAnnouncements'])->name('forum.boardpublcannouncements.bulk-unsubscribe');
    Route::get('/listadmin/boardlist', [ForumSubscriptionController::class, 'showChapterBoardlist'])->name('chapters.chapboardlist');
});

// Redirect for eLearning Courses...Used for Board & Coordinator Layouts
Route::middleware('auth')->group(function () {
    Route::get('/course/{course_id}/redirect', [ResourcesController::class, 'redirectToCourse'])->name('course.redirect');
    Route::get('/board/course/{course_id}/redirect', [BoardController::class, 'redirectToCourse'])->name('board.course.redirect');
    Route::get('/board-new/course/{course_id}/redirect', [BoardControllerNew::class, 'redirectToCourse'])->name('board-new.course.redirect');
});
