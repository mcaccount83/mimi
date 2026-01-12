<?php

use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardPendingController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChapterReportController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\CoordinatorReportController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EOYReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\ForumSubscriptionController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentReportController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\TechReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserReportController;
use Illuminate\Support\Facades\Route;

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
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
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
Route::put('/updatepassword', [UserController::class, 'updatePassword'])->name('updatepassword');
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
// Route::get('/donation', [PublicController::class, 'editDonation'])->name('public.donation');
Route::post('/updatedonation', [PublicController::class, 'updateDonation'])->name('public.updatedonation');
Route::get('/donationsuccess', [PublicController::class, 'viewDonation'])->name('public.donationsuccess');
Route::post('/public-payment', [PublicController::class, 'processPublicPayment'])->name('public.payment');
Route::get('/newcoordinator', [PublicController::class, 'editNewCoordinator'])->name('public.newcoordinator');
Route::post('/updatenewcoordinator', [PublicController::class, 'updateNewCoordinator'])->name('public.updatenewcoordinator');
Route::get('/newcoordinatorsuccess', [PublicController::class, 'viewNewCoordinator'])->name('public.newcoordinatorsuccess');

// Allow error log to be viewed without login
Route::get('techreports/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');
// Tech Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/techreports/adminemail', [TechReportController::class, 'showAdminEmail'])->name('techreports.adminemail');
    Route::post('/techreports/updateadminemail', [TechReportController::class, 'updateAdminEmail'])->name('techreports.updateadminemail');
    Route::get('/techreports/googledrive', [TechReportController::class, 'showGoogleDrive'])->name('techreports.googledrive');
    Route::post('/techreports/updategoogledrive', [TechReportController::class, 'updateGoogleDrive'])->name('techreports.updategoogledrive');
    Route::get('/techreports/chapterlist', [TechReportController::class, 'listActiveChapters'])->name('techreports.chapterlist');
    Route::get('/techreports/chapterlistzapped', [TechReportController::class, 'listZappedChapters'])->name('techreports.chapterlistzapped');
    Route::get('/techreports/chapterlistpending', [TechReportController::class, 'listPendingChapters'])->name('techreports.chapterlistpending');
    Route::post('/techreports/updatechapterdelete', [TechReportController::class, 'updateChapterDelete'])->name('techreports.updatechapterdelete');
    Route::post('/techreports/updatecoordinatordelete', [TechReportController::class, 'updateCoordinatorDelete'])->name('techreports.updatecoordinatordelete');
    Route::post('/techreports/resetProbationSubmission', [TechReportController::class, 'resetProbationSubmission'])->name('techreports.resetProbationSubmission');
    Route::get('/techreports/eoy', [TechReportController::class, 'showEOY'])->name('techreports.eoy');
    Route::post('/techreports/eoy/resetyear', [TechReportController::class, 'resetYear'])->name('techreports.resetyear');
    Route::post('/techreports/eoy/resetdisbandedusers', [TechReportController::class, 'resetDisbandedUsers'])->name('techreports.resetdisbandedusers');
    Route::post('/techreports/eoy/resetoutgoingusers', [TechReportController::class, 'resetOutgoingUsers'])->name('techreports.resetoutgoingusers');
    Route::post('/techreports/eoy/updateeoydatabase', [TechReportController::class, 'updateEOYDatabase'])->name('techreports.updateeoydatabase');
    Route::post('/techreports/eoy/updateeoydatabaseafter', [TechReportController::class, 'updateEOYDatabaseAFTERTesting'])->name('techreports.updateeoydatabaseafter');
    Route::post('/techreports/eoy/updatedatadatabase', [TechReportController::class, 'updateDataDatabase'])->name('techreports.updatedatadatabase');
    Route::post('/techreports/eoy/updateeoytesting', [TechReportController::class, 'updateEOYTesting'])->name('techreports.updateeoytesting');
    Route::post('/techreports/eoy/updateeoylive', [TechReportController::class, 'updateEOYLive'])->name('techreports.updateeoylive');
    Route::post('/techreports/eoy/updateunsubscribelists', [TechReportController::class, 'updateUnsubscribeLists'])->name('techreports.updateunsubscribelists');
    Route::post('/techreports/eoy/updatesubscribelists', [TechReportController::class, 'updateSubscribeLists'])->name('techreports.updatesubscribelists');
});

// Admin Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/adminreports/intpaymentlist', [AdminReportController::class, 'intPaymentList'])->name('adminreports.intpaymentlist');
    Route::get('/adminreports/paymentlist', [AdminReportController::class, 'paymentList'])->name('adminreports.paymentlist');
    Route::get('/adminreports/paymentdetails/{id}', [AdminReportController::class, 'paymentDetails'])->name('adminreports.paymentdetails');
    Route::get('/adminreports/reregdate', [AdminReportController::class, 'showReRegDate'])->name('adminreports.reregdate');
    Route::get('/adminreports/intreregdate', [AdminReportController::class, 'showIntReRegDate'])->name('adminreports.intreregdate');
    Route::get('/adminreports/reregdate/{id}', [AdminReportController::class, 'EditReRegDate'])->name('adminreports.editreregdate');
    Route::post('/adminreports/updatereregdate/{id}', [AdminReportController::class, 'UpdateReRegDate'])->name('adminreports.updatereregdate');
    Route::get('/adminreports/downloads', [ResourcesController::class, 'showDownloads'])->name('adminreports.downloads');
    Route::get('/adminreports/bugs', [ResourcesController::class, 'showBugs'])->name('adminreports.bugs');
    Route::post('/adminreports/addbugs', [ResourcesController::class, 'addBugs'])->name('adminreports.addbugs');
    Route::post('/adminreports/updatebugs/{id}', [ResourcesController::class, 'updateBugs'])->name('adminreports.updatebugs');
});

// User Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::post('/userreports/updateuserdelete', [UserController::class, 'updateUserDelete'])->name('userreports.updateuserdelete');
    Route::get('/userreports/useradmin', [UserReportController::class, 'showUserAdmin'])->name('userreports.useradmin');
    Route::get('/userreports/duplicateuser', [UserReportController::class, 'showDuplicate'])->name('userreports.duplicateuser');
    Route::get('/userreports/duplicateboardid', [UserReportController::class, 'showDuplicateId'])->name('userreports.duplicateboardid');
    Route::get('/userreports/nopresident', [UserReportController::class, 'showNoPresident'])->name('userreports.nopresident');
    Route::get('/userreports/nopresidentinactive', [UserReportController::class, 'showNoPresidentInactive'])->name('userreports.nopresidentinactive');
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

// Chapter Controller Routes...Coordinator Login Required
Route::middleware('auth')->group(function () {
    Route::get('/chapter/chapterlist', [ChapterController::class, 'showChapters'])->name('chapters.chaplist');
    Route::get('/chapter/zapped', [ChapterController::class, 'showZappedChapter'])->name('chapters.chapzapped');
    Route::get('/chapter/inquiries', [ChapterController::class, 'showChapterInquiries'])->name('chapters.chapinquiries');
    Route::get('/chapter/inquirieszapped', [ChapterController::class, 'showZappedChapterInquiries'])->name('chapters.chapinquirieszapped');
    Route::get('/chapter/details/{id}', [ChapterController::class, 'viewChapterDetails'])->name('chapters.view');
    Route::get('/chapters/checkein', [ChapterController::class, 'checkEIN'])->name('chapters.checkein');
    Route::post('/chapter/details/updateein', [ChapterController::class, 'updateEIN'])->name('chapters.updateein');
    Route::post('/chapter/updatedisband', [ChapterController::class, 'updateChapterDisband'])->name('chapters.updatechapdisband');
    Route::post('/chapter/unzap', [ChapterController::class, 'updateChapterUnZap'])->name('chapters.updatechapterunzap');
    Route::get('/chapter/detailsedit/{id}', [ChapterController::class, 'editChapterDetails'])->name('chapters.edit');
    Route::post('/chapter/detailsupdate/{id}', [ChapterController::class, 'updateChapterDetails'])->name('chapters.update');
    Route::get('/chapter/boardedit/{id}', [ChapterController::class, 'editChapterBoard'])->name('chapters.editboard');
    Route::post('/chapter/boardupdate/{id}', [ChapterController::class, 'updateChapterBoard'])->name('chapters.updateboard');
    Route::get('/online/website', [ChapterController::class, 'showChapterWebsite'])->name('chapters.chapwebsite');
    Route::get('/online/socialmedia', [ChapterController::class, 'showRptSocialMedia'])->name('chapters.chapsocialmedia');
    Route::get('/online/websiteedit/{id}', [ChapterController::class, 'editChapterWebsite'])->name('chapters.editwebsite');
    Route::post('/online/websiteupdate/{id}', [ChapterController::class, 'updateChapterWebsite'])->name('chapters.updatewebsite');
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
    // Route::get('/coordreports/intreportingtree', [CoordinatorReportController::class, 'showIntRptReportingTree'])->name('coordreports.intcoordrptreportingtree');
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
    Route::get('/eoy/status', [EOYReportController::class, 'showEOYStatus'])->name('eoyreports.eoystatus');
    Route::get('/eoy/editstatus/{id}', [EOYReportController::class, 'editEOYDetails'])->name('eoyreports.view');
    Route::post('/eoy/updatestatus/{id}', [EOYReportController::class, 'updateEOYDetails'])->name('eoyreports.update');
    Route::get('/eoy/boardreport', [EOYReportController::class, 'showEOYBoardReport'])->name('eoyreports.eoyboardreport');
    Route::get('/eoy/editboardreport/{id}', [EOYReportController::class, 'editBoardReport'])->name('eoyreports.editboardreport');
    Route::post('/eoy/editboardreport/{id}', [EOYReportController::class, 'editBoardReport'])->name('eoyreports.activateboardreport');
    Route::post('eoy/updateboardreport/{id}', [EOYReportController::class, 'updateEOYBoardReport'])->name('eoyreports.updateboardreport');
    Route::get('/eoy/financialreport', [EOYReportController::class, 'showEOYFinancialReport'])->name('eoyreports.eoyfinancialreport');
    Route::get('/eoy/reviewfinancialreport/{id}', [EOYReportController::class, 'reviewFinancialReport'])->name('eoyreports.reviewfinancialreport');
    Route::post('/eoy/updatefinancialreport/{id}', [EOYReportController::class, 'updateEOYFinancialReport'])->name('eoyreports.updatefinancialreport');
    Route::get('/eoy/unsubmit/{id}', [EOYReportController::class, 'updateUnsubmit']);
    Route::get('/eoy/unsubmitfinal/{id}', [EOYReportController::class, 'updateUnsubmitFinal']);
    Route::get('/eoy/clearreview/{id}', [EOYReportController::class, 'updateClearReview']);
    Route::get('/eoy/attachments', [EOYReportController::class, 'showEOYAttachments'])->name('eoyreports.eoyattachments');
    Route::get('/eoy/editattachments/{id}', [EOYReportController::class, 'editEOYAttachments'])->name('eoyreports.editattachments');
    Route::post('/eoy/updateattachments/{id}', [EOYReportController::class, 'updateEOYAttachments'])->name('eoyreports.updateattachments');
    Route::get('/eoy/boundaries', [EOYReportController::class, 'showEOYBoundaries'])->name('eoyreports.eoyboundaries');
    Route::get('/eoy/editboundaries/{id}', [EOYReportController::class, 'editEOYBoundaries'])->name('eoyreports.editboundaries');
    Route::post('/eoy/updateboundaries/{id}', [EOYReportController::class, 'updateEOYBoundaries'])->name('eoyreports.updateboundaries');
    Route::get('/eoy/awards', [EOYReportController::class, 'showEOYAwards'])->name('eoyreports.eoyawards');
    Route::get('/eoy/editawards/{id}', [EOYReportController::class, 'editEOYAwards'])->name('eoyreports.editawards');
    Route::post('/eoy/updateawards/{id}', [EOYReportController::class, 'updateEOYAwards'])->name('eoyreports.updateawards');
    Route::get('/eoy/irssubmission', [EOYReportController::class, 'showIRSSubmission'])->name('eoyreports.eoyirssubmission');
    Route::get('/eoy/editirssubmission/{id}', [EOYReportController::class, 'editIRSSubmission'])->name('eoyreports.editirssubmission');
    Route::post('/eoy/updateirssubmission/{id}', [EOYReportController::class, 'updateIRSSubmission'])->name('eoyreports.updateirssubmission');
});

// Board Controller Routes...Board Login Required
Route::middleware('auth')->group(function () {
    Route::get('/board/newchapterstatus/{id}', [BoardPendingController::class, 'showNewChapterStatus'])->name('board.newchapterstatus');
    Route::get('/board/profile/{id}', [BoardController::class, 'editProfile'])->name('board.editprofile');
    Route::post('/board/profileupdate/{id}', [BoardController::class, 'updateProfile'])->name('board.updateprofile');
    Route::get('/board/boardreport/{id}', [BoardController::class, 'editBoardReport'])->name('board.editboardreport');
    Route::post('/board/boardreportupatea/{id}', [BoardController::class, 'updateBoardReport'])->name('board.updateboardreport');
    Route::get('/board/manual/{id}', [BoardController::class, 'editManualOrderForm'])->name('board.editmanual');
    Route::get('/board/probation/{id}', [BoardController::class, 'editProbationSubmission'])->name('board.editprobation');
    Route::post('/board/probationupdate/{id}', [BoardController::class, 'updateProbationSubmission'])->name('board.updateprobation');
    // Route::get('/board/m2mdonation/{id}', [BoardController::class, 'editM2MDonationForm'])->name('board.editm2mdonation');
    Route::get('/board/resources/{id}', [BoardController::class, 'viewResources'])->name('board.viewresources');
    Route::get('/board/elearning/{id}', [BoardController::class, 'viewELearning'])->name('board.viewelearning');
});

// Financial Report Controller Routes...Board Login Required
Route::middleware('auth')->group(function () {
    Route::get('/board/financialreport/{id}', [FinancialReportController::class, 'editFinancialReport'])->name('board.editfinancialreport');
    Route::post('/board/financialreportupdate/{id}', [FinancialReportController::class, 'updateFinancialReport'])->name('board.updatefinancialreport');
    Route::get('/board/disbandchecklist/{id}', [FinancialReportController::class, 'editDisbandChecklist'])->name('board.editdisbandchecklist');
    Route::post('/board/disbandchecklistupdate/{id}', [FinancialReportController::class, 'updateDisbandChecklist'])->name('board.updatedisbandchecklist');
    Route::post('/board/disbandreportupdate/{id}', [FinancialReportController::class, 'updateDisbandReport'])->name('board.updatedisbandreport');
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
});

// Payment Controller Routes...Used for Board & Coordinator Layouts
Route::middleware('auth')->group(function () {
    Route::post('/process-payment', [PaymentController::class, 'reRegistrationPayment'])->name('process.payment');
    Route::post('/process-donation', [PaymentController::class, 'm2mPayment'])->name('process.donation');
    Route::post('/process-manual', [PaymentController::class, 'manualPayment'])->name('process.manual');
    Route::get('/board/reregpayment/{id}', [PaymentController::class, 'editReregistrationPaymentForm'])->name('board.editreregpayment');
    Route::get('/board/donation/{id}', [PaymentController::class, 'editDonationForm'])->name('board.editdonate');
    Route::get('/payment/reregistration', [PaymentReportController::class, 'showChapterReRegistration'])->name('payment.chapreregistration');
    Route::get('/payment/donations', [PaymentReportController::class, 'showRptDonations'])->name('payment.chapdonations');
    Route::get('/payment/chapterpaymentedit/{id}', [PaymentReportController::class, 'editChapterPayment'])->name('payment.editpayment');
    Route::post('/payment/chapterpaymentupdate/{id}', [PaymentReportController::class, 'updateChapterPayment'])->name('payment.updatepayment');
    Route::get('/payment/reregistrationreminder', [PaymentReportController::class, 'createChapterReRegistrationReminder'])->name('payment.chapreregreminder');
    Route::get('/payment/reregistrationlatereminder', [PaymentReportController::class, 'createChapterReRegistrationLateReminder'])->name('payment.chaprereglatereminder');
});

// Forum Subscription Controller Routes...Used for Board & Coordinator Layouts
Route::middleware('auth')->group(function () {
    Route::get('/forum/chaptersubscriptionlist', [ForumSubscriptionController::class, 'showChapterListSubscriptions'])->name('forum.chaptersubscriptionlist');
    Route::get('/forum/coordinatorsubscriptionlist', [ForumSubscriptionController::class, 'showCoordinatorListSubscriptions'])->name('forum.coordinatorsubscriptionlist');
    Route::post('/forum/subscribecategory', [ForumSubscriptionController::class, 'subscribeCategory'])->name('forum.subscribecategory');
    Route::post('/forum/unsubscribecategory', [ForumSubscriptionController::class, 'unsubscribeCategory'])->name('forum.unsubscribecategory');
    Route::post('/forum/coordinatorlist/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddCoordinatorsList'])->name('forum.coordinatorlist.bulk-subscribe');
    Route::post('/forum/coordinatorboardlist/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddCoordinatorsBoardList'])->name('forum.coordinatorboardlist.bulk-subscribe');
    Route::post('/forum/coordinatorpublidannouncement/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddCoordinatorsPublicAnnounceements'])->name('forum.coordinatorpublidannouncement.bulk-subscribe');
    Route::post('/forum/coordinatorboardlist/bulk-unsubscribe', [ForumSubscriptionController::class, 'bulkRemoveCoordinatorsBoardList'])->name('forum.coordinatorboardlist.bulk-unsubscribe');
    Route::post('/forum/boardlist/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddBoardList'])->name('forum.boardlist.bulk-subscribe');
    Route::post('/forum/publcannouncements/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddPublicAnnouncements'])->name('forum.publcannouncements.bulk-subscribe');
    Route::post('/forum/boardlist/bulk-unsubscribe', [ForumSubscriptionController::class, 'bulkRemoveBoardList'])->name('forum.boardlist.bulk-unsubscribe');
    Route::post('/forum/boardboardlist/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddBoardBoardList'])->name('forum.boardboardlist.bulk-subscribe');
    Route::post('/forum/boardpublcannouncements/bulk-subscribe', [ForumSubscriptionController::class, 'bulkAddBoardPublicAnnouncements'])->name('forum.boardpublcannouncements.bulk-subscribe');
    Route::post('/forum/boardboardlist/bulk-unsubscribe', [ForumSubscriptionController::class, 'bulkRemoveBoardBoardList'])->name('forum.boardboardlist.bulk-unsubscribe');
    Route::post('/forum/boardpublcannouncements/bulk-unsubscribe', [ForumSubscriptionController::class, 'bulkRemoveBoardPublicAnnouncements'])->name('forum.boardpublcannouncements.bulk-unsubscribe');
    Route::get('/forum/boardlist', [ForumSubscriptionController::class, 'showChapterBoardlist'])->name('chapters.chapboardlist');
});

// Redirect for eLearning Courses...Used for Board & Coordinator Layouts
Route::middleware('auth')->group(function () {
    Route::get('/course/{course_id}/redirect', [ResourcesController::class, 'redirectToCourse'])->name('course.redirect');
    Route::get('/board/course/{course_id}/redirect', [BoardController::class, 'redirectToCourse'])->name('board.course.redirect');
});
