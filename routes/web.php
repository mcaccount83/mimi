<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChapterReportController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\CoordinatorReportController;
use App\Http\Controllers\EOYReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ForumSubscriptionController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViewAsBoardController;
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

// Login and Logout Routes...
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('logout', [LoginController::class, 'logout']);

// Password Reset Routes...
Route::get('password/request', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// UserControllert Routes...
Route::get('/checkemail/{email}', [UserController::class, 'checkEmail'])->name('checkemail');
Route::post('/checkpassword', [UserController::class, 'checkCurrentPassword'])->name('checkpassword');
Route::put('/updatepassword', [UserController::class, 'updatePassword'])->name('updatepassword');
Route::get('/load-email-details/{chId}', [UserController::class, 'loadEmailDetails'])->name('load.email.details');
Route::get('/load-coordinator-list/{id}', [UserController::class, 'loadCoordinatorList'])->name('load.coordinator.list');
// Route::get('/load-coord-email/{corId}', [UserController::class, 'loadCoordEmail'])->name('load.coord.email');
// Route::get('/load-conference-coord/{chConf}/{chPcid}', [UserController::class, 'loadConferenceCoord'])->name('load.conference.coord');
// Route::get('/load-user/{user_id}', [UserController::class, 'loadUser'])->name('load.user');

// Error Log Routes...
Route::get('admin/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');
// Queue Jobs Routes...
Route::get('admin/jobs', [MailController::class, 'index'])->name('queue-monitor::index');
// Route::get('', \romanzipp\QueueMonitor\Controllers\ShowQueueMonitorController::class)->name('queue-monitor::index');
Route::delete('monitors/{monitor}', \romanzipp\QueueMonitor\Controllers\DeleteMonitorController::class)->name('queue-monitor::destroy');
Route::patch('monitors/retry/{monitor}', \romanzipp\QueueMonitor\Controllers\RetryMonitorController::class)->name('queue-monitor::retry');
Route::delete('purge', \romanzipp\QueueMonitor\Controllers\PurgeMonitorsController::class)->name('queue-monitor::purge');
// Error Pages Test Routes...
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

// Public Page Routes...
Route::get('/chapter-links', [PublicController::class, 'chapterLinks'])->name('chapter.links');
Route::get('/chapter-resources', [PublicController::class, 'chapterResources'])->name('board.resources');

// Admin Controller Routes...
Route::get('/admin/eoy', [AdminController::class, 'showEOY'])->name('admin.eoy');
Route::post('/admin/resetyear', [AdminController::class, 'resetYear'])->name('resetyear');
Route::post('/admin/updateeoydatabase', [AdminController::class, 'updateEOYDatabase'])->name('admin.updateeoydatabase');
Route::post('/admin/updateeoydatabaseafter', [AdminController::class, 'updateEOYDatabaseAFTERTesting'])->name('admin.updateeoydatabaseafter');
Route::post('/admin/updatedatadatabase', [AdminController::class, 'updateDataDatabase'])->name('admin.updatedatadatabase');
// Route::post('/admin/updateeoycoordinator', [AdminController::class, 'updateEOYCoordinator'])->name('admin.updateeoycoordinator');
// Route::post('/admin/updateeoychapter', [AdminController::class, 'updateEOYChapter'])->name('admin.updateeoychapter');
Route::post('/admin/updateeoytesting', [AdminController::class, 'updateEOYTesting'])->name('admin.updateeoytesting');
Route::post('/admin/updateeoylive', [AdminController::class, 'updateEOYLive'])->name('admin.updateeoylive');
Route::post('/admin/updatesubscribelists', [AdminController::class, 'updateSubscribeLists'])->name('admin.updatesubscribelists');
Route::post('/admin/updateunsubscribelists', [AdminController::class, 'updateUnsubscribeLists'])->name('admin.updateunsubscribelists');


Route::get('/admin/reregdate', [AdminController::class, 'showReRegDate'])->name('admin.reregdate');
Route::get('/admin/reregdate/{id}', [AdminController::class, 'EditReRegDate'])->name('admin.editreregdate');
Route::post('/admin/updatereregdate/{id}', [AdminController::class, 'UpdateReRegDate'])->name('admin.updatereregdate');
Route::get('/resources/bugs', [AdminController::class, 'showBugs'])->name('admin.bugs');
Route::post('/resources/addbugs', [AdminController::class, 'addBugs'])->name('admin.addbugs');
Route::post('/resources/updatebugs/{id}', [AdminController::class, 'updateBugs'])->name('admin.updatebugs');
Route::get('/resources/downloads', [AdminController::class, 'showDownloads'])->name('admin.downloads');
Route::get('/resources/resources', [AdminController::class, 'showResources'])->name('admin.resources');
Route::post('/resources/addresources', [AdminController::class, 'addResources'])->name('admin.addresources');
Route::post('/resources/updateresources/{id}', [AdminController::class, 'updateResources'])->name('admin.updateresources');
Route::get('/resources/toolkit', [AdminController::class, 'showToolkit'])->name('admin.toolkit');
Route::post('/resources/addtoolkit', [AdminController::class, 'addToolkit'])->name('admin.addtoolkit');
Route::post('/resources/updatetoolkit/{id}', [AdminController::class, 'updateToolkit'])->name('admin.updatetoolkit');
Route::get('/admin/googledrive', [AdminController::class, 'showGoogleDrive'])->name('admin.googledrive');
Route::post('/admin/updategoogledrive', [AdminController::class, 'updateGoogleDrive'])->name('admin.updategoogledrive');
Route::get('/adminreports/duplicateuser', [AdminController::class, 'showDuplicate'])->name('admin.duplicateuser');
Route::get('/adminreports/duplicateboardid', [AdminController::class, 'showDuplicateId'])->name('admin.duplicateboardid');
// Route::get('/adminreports/multipleboard', [AdminController::class, 'showMultiple'])->name('admin.multipleboard');
Route::get('/adminreports/nopresident', [AdminController::class, 'showNoPresident'])->name('admin.nopresident');
Route::get('/adminreports/outgoingboard', [AdminController::class, 'showOutgoingBoard'])->name('admin.outgoingboard');
// Route::get('/adminreports/mailqueue', [AdminController::class, 'showMailQueue'])->name('admin.mailqueue');
// Route::post('/admin/eoy/update/{id}', [AdminController::class, 'updateEOY'])->name('admin.eoyupdate');
// Route::post('/adminreports/updateoutgoingboard', [AdminController::class, 'updateOutgoingBoard'])->name('admin.updateoutgoingboard');

// Payment Controller Routes...
Route::post('/process-payment', [PaymentController::class, 'reRegistrationPayment'])->name('process.payment');
Route::post('/process-donation', [PaymentController::class, 'm2mPayment'])->name('process.donation');

// PDF Controller Routes...
// Route::get('/board/financial/pdf/{id}', [PDFController::class, 'generatePdf'])->name('pdf.financialreport');
// Route::get('/chapter/financial/pdf/{id}', [PDFController::class, 'generatePdf'])->name('pdf.financialreport');

Route::get('/board/chapteringoodstanding/pdf/{id}', [PDFController::class, 'generateGoodStanding'])->name('pdf.chapteringoodstanding');
Route::get('/board/financialreport/pdf/{id}', [PDFController::class, 'generateFinancialReport'])->name('pdf.financialreport');
Route::get('/financial-report-pdf', [PDFController::class, 'saveFinancialReport'])->name('pdf.financialreport');
Route::post('/generate-probation-letter', [PDFController::class, 'saveProbationLetter'])->name('pdf.generateProbationLetter');

// Google Controller -- Uploading Files Routes...
Route::post('/files/storeEIN/{id}', [GoogleController::class, 'storeEIN']);
Route::post('/files/storeRoster/{id}', [GoogleController::class, 'storeRoster']);
Route::post('/files/store990n/{id}', [GoogleController::class, 'store990N']);
Route::post('/files/storeStatement1/{id}', [GoogleController::class, 'storeStatement1']);
Route::post('/files/storeStatement2/{id}', [GoogleController::class, 'storeStatement2']);
Route::post('/files/storeAward/{id}', [GoogleController::class, 'storeAward']);
Route::post('/files/storeResources/{id}', [GoogleController::class, 'storeResources'])->name('store.resources');
Route::post('/files/storeToolkit/{id}', [GoogleController::class, 'storeToolkit'])->name('store.toolkit');

// Mail Controller Routes...

// Chapter Controller Routes...
Route::get('/chapter/chapterlist', [ChapterController::class, 'showChapters'])->name('chapters.chaplist');
Route::get('/chapter/zapped', [ChapterController::class, 'showZappedChapter'])->name('chapters.chapzapped');
Route::get('/chapter/inquiries', [ChapterController::class, 'showChapterInquiries'])->name('chapters.chapinquiries');
Route::get('/chapter/inquirieszapped', [ChapterController::class, 'showZappedChapterInquiries'])->name('chapters.chapinquirieszapped');
Route::get('/international/chapter', [ChapterController::class, 'showIntChapter'])->name('international.intchapter');
Route::get('/international/chapterzapped', [ChapterController::class, 'showIntZappedChapter'])->name('international.intchapterzapped');
Route::get('/chapterdetails/{id}', [ChapterController::class, 'viewChapterDetails'])->name('chapters.view');
Route::get('/chapters/checkein', [ChapterController::class, 'checkEIN'])->name('chapters.checkein');
Route::post('/chapterdetails/updateein', [ChapterController::class, 'updateEIN'])->name('chapters.updateein');
Route::post('/chapter/sendnewchapter', [ChapterController::class, 'sendNewChapterEmail'])->name('chapters.sendnewchapter');
Route::post('/chapter/updatedisband', [ChapterController::class, 'updateChapterDisband'])->name('chapters.updatechapdisband');
Route::post('/chapter/unzap', [ChapterController::class, 'updateChapterUnZap'])->name('chapters.updatechapterunzap');
Route::get('/chapternew', [ChapterController::class, 'addChapterNew'])->name('chapters.addnew');
Route::post('/chapternewupdate', [ChapterController::class, 'updateChapterNew'])->name('chapters.updatenew');
Route::get('/chapterdetailsedit/{id}', [ChapterController::class, 'editChapterDetails'])->name('chapters.edit');
Route::post('/chapterdetailsupdate/{id}', [ChapterController::class, 'updateChapterDetails'])->name('chapters.update');
Route::get('/chapterboardedit/{id}', [ChapterController::class, 'editChapterBoard'])->name('chapters.editboard');
Route::post('/chapterboardupdate/{id}', [ChapterController::class, 'updateChapterBoard'])->name('chapters.updateboard');

Route::get('/chapter/website', [ChapterController::class, 'showChapterWebsite'])->name('chapters.chapwebsite');
Route::get('/chapter/socialmedia', [ChapterController::class, 'showRptSocialMedia'])->name('chapreports.chaprptsocialmedia');
Route::get('/chapterwebsiteedit/{id}', [ChapterController::class, 'editChapterWebsite'])->name('chapters.editwebsite');
Route::post('/chapterwebsiteupdate/{id}', [ChapterController::class, 'updateChapterWebsite'])->name('chapters.updatewebsite');
Route::get('/chapter/boardlist', [ChapterController::class, 'showChapterBoardlist'])->name('chapters.chapboardlist');
Route::get('/chapter/reregistration', [ChapterController::class, 'showChapterReRegistration'])->name('chapters.chapreregistration');
Route::get('/chapter/reregistrationreminder', [ChapterController::class, 'createChapterReRegistrationReminder'])->name('chapters.chapreregreminder');
Route::get('/chapter/reregistrationlatereminder', [ChapterController::class, 'createChapterReRegistrationLateReminder'])->name('chapters.chaprereglatereminder');
Route::get('/chapter/donations', [ChapterController::class, 'showRptDonations'])->name('chapreports.chaprptdonations');
Route::get('/international/donation', [ChapterController::class, 'showIntdonation'])->name('international.intdonation');
Route::get('/chapterpaymentedit/{id}', [ChapterController::class, 'editChapterPayment'])->name('chapters.editpayment');
Route::post('/chapterpaymentupdate/{id}', [ChapterController::class, 'updateChapterPayment'])->name('chapters.updatepayment');

Route::get('/chapterreports/chapterstatus', [ChapterReportController::class, 'showRptChapterStatus'])->name('chapreports.chaprptchapterstatus');
Route::get('/chapterreports/einstatus', [ChapterReportController::class, 'showRptEINstatus'])->name('chapreports.chaprpteinstatus');
Route::get('/chapterreports/inteinstatus', [ChapterReportController::class, 'showIntEINstatus'])->name('international.inteinstatus');
Route::get('/chapterirsedit/{id}', [ChapterController::class, 'editChapterIRS'])->name('chapters.editirs');
Route::post('/chapterirsupdate/{id}', [ChapterController::class, 'updateChapterIRS'])->name('chapters.updateirs');
Route::get('/chapterreports/newchapters', [ChapterReportController::class, 'showRptNewChapters'])->name('chapreports.chaprptnewchapters');
Route::get('/chapterreports/largechapters', [ChapterReportController::class, 'showRptLargeChapters'])->name('chapreports.chaprptlargechapters');
Route::get('/chapterreports/probation', [ChapterReportController::class, 'showRptProbation'])->name('chapreports.chaprptprobation');
Route::get('/chapterreports/coordinators', [ChapterReportController::class, 'showRptChapterCoordinators'])->name('chapreports.chaprptcoordinators');

// Route::get('/chapter/reregistrationpayment/{id}', [ChapterController::class, 'showChapterReRegistrationPayment'])->name('chapters.chapreregpayment');
// Route::post('/chapter/updatereregistrtionpayment/{id}', [ChapterController::class, 'updateChapterReRegistrationPayment'])->name('chapters.updatechapreregpayment');
// Route::get('/chapter/reregistrationnotes/{id}', [ChapterController::class, 'showChapterReRegistrationNotes'])->name('chapters.chapreregnotes');
// Route::post('/chapter/updatereregistrtionpaymentnotes/{id}', [ChapterController::class, 'updateChapterReRegistrationNotes'])->name('chapters.updatechapreregnotes');
// Route::get('/chapter/donationsview/{id}', [ChapterReportController::class, 'showRptDonationsView'])->name('chapreports.chaprptdonationsview');
// Route::post('/chapter/updatedonations/{id}', [ChapterReportController::class, 'updateRptDonations'])->name('chapreports.updatechaprptdonations');
// Route::get('/chapter/chapternew', [ChapterController::class, 'showChapterNew'])->name('chapters.chapnew');
// Route::post('/chapter/updatechapternew', [ChapterController::class, 'updateChapterNew'])->name('chapters.updatechapnew');
// Route::get('/chapter/chapterview/{id}', [ChapterController::class, 'showChapterView'])->name('chapters.chapview');;
// Route::post('/chapter/updatechapter/{id}', [ChapterController::class, 'updateChapter'])->name('chapters.updatechap');
// Route::get('/chapter/zappedview/{id}', [ChapterController::class, 'showZappedChapterView'])->name('chapters.chapzappedview');
// Route::post('/chapter/updatezapped/{id}', [ChapterController::class, 'updateZappedChapter'])->name('chapters.updatechapzapped');
// Route::get('/chapter/inquiriesview/{id}', [ChapterController::class, 'showChapterInquiriesView'])->name('chapters.chapinquiriesview');
// Route::get('/chapter/inquirieszappedview/{id}', [ChapterController::class, 'showZappedChapterInquiriesView'])->name('chapters.chapzappedinquiriesview');
// Route::get('/checkreportid/{id}', [ChapterController::class, 'checkReportId'])->name('check.reportid');
// Route::get('/chapter/getEmail{id}', [ChapterController::class, 'getEmailDetails'])->name('get.emaildetails');
// Route::get('/chapter/websiteview/{id}', [ChapterController::class, 'showChapterWebsiteView'])->name('chapters.chapwebsiteview');
// Route::post('/chapter/updatewebsite/{id}', [ChapterController::class, 'updateChapterWebsite'])->name('chapters.updatechapwebsite');
// Route::get('/chapterreports/einstatusview/{id}', [ChapterReportController::class, 'showRptEINstatusView'])->name('chapreports.chaprpteinstatusview');
// Route::post('/chapterreports/updateeinstatus/{id}', [ChapterReportController::class, 'updateRptEINstatus'])->name('chapreports.updatechaprpteinstatus');

// Coordinator Controller Routes...
Route::get('/coordinator/coordlist', [CoordinatorController::class, 'showCoordinators'])->name('coordinators.coordlist');
Route::get('/coordinator/retired', [CoordinatorController::class, 'showRetiredCoordinator'])->name('coordinators.coordretired');
Route::get('/international/coordinator', [CoordinatorController::class, 'showIntCoordinator'])->name('international.intcoord');
Route::get('/international/coordinatorretired', [CoordinatorController::class, 'showIntCoordinatorRetired'])->name('international.intcoordretired');
Route::get('/coordnew', [CoordinatorController::class, 'addCoordNew'])->name('coordinators.editnew');
Route::post('/coordnewupdate', [CoordinatorController::class, 'updateCoordNew'])->name('coordinators.updatenew');
Route::get('/coorddetails/{id}', [CoordinatorController::class, 'viewCoordDetails'])->name('coordinators.view');
Route::post('/coorddetails/sendbigsister', [CoordinatorController::class, 'sendBigSisterEmail'])->name('coordinators.sendbigsister');
Route::post('/coorddetails/updatecardsent', [CoordinatorController::class, 'updateCardSent'])->name('coordinators.updatecardsent');
Route::post('/coorddetails/updateonleave', [CoordinatorController::class, 'updateOnLeave'])->name('coordinators.updateonleave');
Route::post('/coorddetails/updateremoveleave', [CoordinatorController::class, 'updateRemoveLeave'])->name('coordinators.updateremoveleave');
Route::post('/coorddetails/updateretire', [CoordinatorController::class, 'updateRetire'])->name('coordinators.updateretire');
Route::post('/coorddetails/updateunretire', [CoordinatorController::class, 'updateUnRetire'])->name('coordinators.updateunretire');
Route::get('/coorddetailseditrole/{id}', [CoordinatorController::class, 'editCoordRole'])->name('coordinators.editrole');
Route::post('/coorddetailsupdaterole/{id}', [CoordinatorController::class, 'updateCoordRole'])->name('coordinators.updaterole');
Route::get('/coorddetailsedit/{id}', [CoordinatorController::class, 'editCoordDetails'])->name('coordinators.editdetails');
Route::post('/coorddetailsupdate/{id}', [CoordinatorController::class, 'updateCoordDetails'])->name('coordinators.updatedetails');
Route::get('/coorddetailseditrecognition/{id}', [CoordinatorController::class, 'editCoordRecognition'])->name('coordinators.editrecognition');
Route::post('/coorddetailsupdaterecognition/{id}', [CoordinatorController::class, 'updateCoordRecognition'])->name('coordinators.updaterecognition');
Route::get('/coordviewprofile', [CoordinatorController::class, 'viewCoordProfile'])->name('coordinators.viewprofile');
Route::get('/coordprofile', [CoordinatorController::class, 'editCoordProfile'])->name('coordinators.profile');
Route::post('/coordprofileupdate', [CoordinatorController::class, 'updateCoordProfile'])->name('coordinators.profileupdate');

Route::get('/coordreports/volunteerutilization', [CoordinatorReportController::class, 'showRptVolUtilization'])->name('coordreports.coordrptvolutilization');
Route::get('/coordreports/appreciation', [CoordinatorReportController::class, 'showRptAppreciation'])->name('coordreports.coordrptappreciation');
Route::get('/coordreports/birthdays', [CoordinatorReportController::class, 'showRptBirthdays'])->name('coordreports.coordrptbirthdays');
Route::get('/coordreports/reportingtree', [CoordinatorReportController::class, 'showRptReportingTree'])->name('coordreports.coordrptreportingtree');

// Route::get('/getregion/{id}', [CoordinatorController::class, 'getRegionList'])->name('get.region');
// Route::get('/getreporting', [CoordinatorController::class, 'getReportingList'])->name('get.reporting');
// Route::get('/getdirectreport', [CoordinatorController::class, 'getDirectReportingList'])->name('get.directreport');
// Route::get('/getchapterprimary', [CoordinatorController::class, 'getChapterPrimaryFor'])->name('get.chapterprimary');
// Route::get('/coordinator/dashboard', [CoordinatorController::class, 'showCoordinatorDashboard'])->name('coordinators.coorddashboard');
// Route::post('/coordinator/updatedashboard/{id}', [CoordinatorController::class, 'updateCoordinatorDashboard'])->name('coordinators.updatecoorddashboard');
// Route::get('/coordreports/coordinatortodo', [CoordinatorReportController::class, 'showRptCoordToDo'])->name('coordreports.coordrpttodo');
// Route::get('/international/coordinatortodo', [InternationalController::class, 'showIntCoordinatorToDo'])->name('international.intcoordtodo');
// Route::get('/coordinator/coordinatornew', [CoordinatorController::class, 'showCoordinatorNew'])->name('coordinators.coordnew');
// Route::post('/coordinator/updatecoordinatornew', [CoordinatorController::class, 'updateCoordinatorNew'])->name('coordinators.updatecoordnew');
// Route::get('/coordreports/birthdaysview/{id}', [CoordinatorReportController::class, 'showRptBirthdaysView'])->name('coordreports.coordrptbirthdaysview');
// Route::post('/coordreports/updatebirthdays/{id}', [CoordinatorReportController::class, 'updateRptBirthdays'])->name('coordreports.updatecoordrptbirthdays');
// Route::get('/coordinator/coordinatorview/{id}', [CoordinatorController::class, 'showCoordinatorView'])->name('coordinators.coordview');
// Route::post('/coordinator/updatecoordinator/{id}', [CoordinatorController::class, 'updateCoordinator'])->name('coordinators.updatecoord');
// Route::get('/coordinator/roleview/{id}', [CoordinatorController::class, 'showCoordinatorRoleView'])->name('coordinators.coordroleview');
// Route::post('/coordinator/updaterole/{id}', [CoordinatorController::class, 'updateCoordinatorRole'])->name('coordinators.updatecoordrole');
// Route::get('/coordinator/retiredview/{id}', [CoordinatorController::class, 'showRetiredCoordinatorView'])->name('coordinators.coordretiredview');
// Route::get('/coordinator/updateunretire/{id}', [CoordinatorController::class, 'updateUnretireCoordinator'])->name('coordinators.updatecoordunretire');
// Route::get('/coordinator/profile', [CoordinatorController::class, 'showCoordinatorProfile'])->name('coordinators.coordprofile');
// Route::post('/coordinator/updateprofile/{id}', [CoordinatorController::class, 'updateCoordinatorProfile'])->name('coordinators.updatecoordprofile');
// Route::get('/coordreports/appreciationview/{id}', [CoordinatorReportController::class, 'showRptAppreciationView'])->name('coordreports.coordrptappreciationview');
// Route::post('/coordreports/updateappreciation/{id}', [CoordinatorReportController::class, 'updateRptAppreciation'])->name('coordreports.updatecoordrptappreciation');

// International Controller Routes...
// Route::get('/international/chapterview/{id}', [InternationalController::class, 'showIntChapterView'])->name('international.intchapterview');
// Route::get('/international/chapterzappedview/{id}', [InternationalController::class, 'showIntZappedChapterView'])->name('international.intchapterzappedview');
// Route::get('/international/coordinatorview/{id}', [InternationalController::class, 'showIntCoordinatorView'])->name('international.intcoordview');
// Route::get('/iternational/coordinatorretiredview/{id}', [InternationalController::class, 'showIntCoordinatorRetiredView'])->name('international.intcoordretiredview');
// Route::get('/international/einstatusview/{id}', [InternationalController::class, 'showIntEINstatusView'])->name('international.inteinstatusview');
// Route::post('/international/updateeinstatus/{id}', [InternationalController::class, 'updateIntEINstatus'])->name('international.updateinteinstatus');

// EOYReports Controller Routes...
Route::get('/eoy/status', [EOYReportController::class, 'showEOYStatus'])->name('eoyreports.eoystatus');
Route::get('/eoy/status/reminder', [EOYReportController::class, 'sendEOYStatusReminder'])->name('eoyreports.eoystatusreminder');
Route::get('/eoy/editstatus/{id}', [EOYReportController::class, 'editEOYDetails'])->name('eoyreports.view');
Route::post('/eoy/updatestatus/{id}', [EOYReportController::class, 'updateEOYDetails'])->name('eoyreports.update');
Route::get('/eoy/boardreport', [EOYReportController::class, 'showEOYBoardReport'])->name('eoyreports.eoyboardreport');
Route::get('/eoy/activateboardreport/{id}', [EOYReportController::class, 'activateBoard'])->name('eoyreports.activateboardreport');
Route::get('/eoy/boardreport/reminder', [EOYReportController::class, 'sendEOYBoardReportReminder'])->name('eoyreports.eoyboardreportreminder');
Route::get('/eoy/editboardreport/{id}', [EOYReportController::class, 'editBoardReport'])->name('eoyreports.editboardreport');
Route::post('eoy/updateboardreport/{id}', [EOYReportController::class, 'updateEOYBoardReport'])->name('eoyreports.updateboardreport');
Route::get('/eoy/financialreport', [EOYReportController::class, 'showEOYFinancialReport'])->name('eoyreports.eoyfinancialreport');
Route::get('/eoy/financialreport/reminder', [EOYReportController::class, 'sendEOYFinancialReportReminder'])->name('eoyreports.eoyfinancialreportreminder');
Route::get('/eoy/reviewfinancialreport/{id}', [EOYReportController::class, 'reviewFinancialReport'])->name('eoyreports.reviewfinancialreport');
Route::post('/eoy/updatefinancialreport/{id}', [EOYReportController::class, 'updateEOYFinancialReport'])->name('eoyreports.updatefinancialreport');
Route::get('/eoy/unsubmit/{id}', [EOYReportController::class, 'updateUnsubmit']);
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

// Route::get('/eoy/financialreportview/{id}', [EOYReportController::class, 'showEOYFinancialReportView'])->name('eoyreports.eoyfinancialreportview');
// Route::get('/eoy/statusview/{id}', [EOYReportController::class, 'showEOYStatusView'])->name('eoyreports.eoystatusview');
// Route::post('/eoy/updatestatus/{id}', [EOYReportController::class, 'updateEOYStatus'])->name('eoyreports.eoyupdatestatus');
// Route::get('/eoy/attachmentsview/{id}', [EOYReportController::class, 'showEOYAttachmentsView'])->name('eoyreports.eoyattachmentsview');
// Route::post('/eoy/updateattachments/{id}', [EOYReportController::class, 'updateEOYAttachments'])->name('eoyreports.eoyupdateattachments');
// Route::get('/eoy/boundariesview/{id}', [EOYReportController::class, 'showEOYBoundariesView'])->name('eoyreports.eoyboundariesview');
// Route::post('/eoy/updateboundaries/{id}', [EOYReportController::class, 'updateEOYBoundaries'])->name('eoyreports.eoyupdateboundaries');
// Route::get('/eoy/awardsview/{id}', [EOYReportController::class, 'showEOYAwardsView'])->name('eoyreports.eoyawardsview');
// Route::post('/eoy/updateawards/{id}', [EOYReportController::class, 'updateEOYAwards'])->name('eoyreports.eoyupdateawards');

// Export Controller Routes...
Route::get('/export/chapter/{id}', [ExportController::class, 'indexChapter'])->name('export.chapter');
Route::get('/export/zapchapter', [ExportController::class, 'indexZappedChapter'])->name('export.zapchapter');
Route::get('/export/coordinator/{id}', [ExportController::class, 'indexCoordinator'])->name('export.coordinator');
Route::get('/export/retiredcoordinator', [ExportController::class, 'indexRetiredCoordinator'])->name('export.retiredcoordinator');
Route::get('/export/einstatus', [ExportController::class, 'indexEINStatus'])->name('export.einstatus');
Route::get('/export/irsfiling', [ExportController::class, 'indexInternationalIRSFiling'])->name('export.irsfiling');
Route::get('/export/rereg', [ExportController::class, 'indexReReg'])->name('export.rereg');
Route::get('/export/eoystatus', [ExportController::class, 'indexEOYStatus'])->name('export.eoystatus');
Route::get('/export/chaptercoordinator', [ExportController::class, 'indexChapterCoordinator'])->name('export.chaptercoordinator');
Route::get('/export/chapteraward/{id}', [ExportController::class, 'indexChapterAwardList'])->name('export.chapteraward');
Route::get('/export/appreciation', [ExportController::class, 'indexAppreciation'])->name('export.appreciation');
Route::get('/export/boardelection', [ExportController::class, 'indexBoardElection'])->name('export.boardelection');
Route::get('/export/intchapter', [ExportController::class, 'indexInternationalChapter'])->name('export.intchapter');
Route::get('/export/intzapchapter', [ExportController::class, 'indexInternationalZapChapter'])->name('export.intzapchapter');
Route::get('/export/intcoordinator', [ExportController::class, 'indexIntCoordinator'])->name('export.intcoordinator');
Route::get('/export/intretcoordinator', [ExportController::class, 'indexIntRetCoordinator'])->name('export.intretcoordinator');
Route::get('/export/inteinstatus', [ExportController::class, 'indexIntEINStatus'])->name('export.inteinstatus');
Route::get('/export/intrereg', [ExportController::class, 'indexIntReReg'])->name('export.intrereg');
Route::get('/export/inteoystatus', [ExportController::class, 'indexIntEOYStatus'])->name('export.inteoystatus');
Route::get('/export/boardlist', [ExportController::class, 'indexBoardList'])->name('export.boardlist');

// ViewAsBoard Controller Routes...  These pages do not have their own resourse/views, they use the board view pages
Route::get('/view/chapter/{id}', [ViewAsBoardController::class, 'showChapterView'])->name('viewas.viewchapterpresident');
Route::get('/view/chapterfinancial/{id}', [ViewAsBoardController::class, 'showChapterFinancialView'])->name('viewas.viewchapterfinancial');
Route::get('/view/chapterboardinfo/{id}', [ViewAsBoardController::class, 'showChapterBoardInfoView'])->name('viewas.viewchapterboardinfo');
Route::get('/view/chapterreregistration/{id}', [ViewAsBoardController::class, 'showChapterReregistrationView'])->name('viewas.viewchapterreregistration');

// Board Controller Routes...
Route::get('/board/president', [BoardController::class, 'showPresident'])->name('board.showpresident');
Route::post('/board/update/{id}', [BoardController::class, 'updatePresident'])->name('board.update');
Route::get('/board/member', [BoardController::class, 'showMember'])->name('board.showmember');
Route::post('/board/member/update/{id}', [BoardController::class, 'updateMember'])->name('member.update');
Route::get('/board/boardinfo', [BoardController::class, 'showBoardInfo'])->name('boardinfo.showboardinfo');
Route::post('/board/boardinfo/{id}', [BoardController::class, 'createBoardInfo'])->name('boardinfo.createboardinfo');
Route::get('/board/financial/{id}', [BoardController::class, 'showFinancialReport'])->name('board.showfinancial');
Route::post('/board/financial/{id}', [BoardController::class, 'storeFinancialReport'])->name('board.storefinancial');
Route::get('/board/reregpayment', [BoardController::class, 'showReregistrationPaymentForm'])->name('board.showreregpayment');
Route::get('/board/m2mdonation', [BoardController::class, 'showM2MDonationForm'])->name('board.showm2mdonation');
Route::get('/board/resources', [BoardController::class, 'showResources'])->name('board.resources');

// Forum Subscription Controller Routes...
Route::get('/forum/chaptersubscriptionlist', [ForumSubscriptionController::class, 'showChapterListSubscriptions'])->name('forum.chaptersubscriptionlist');
Route::get('/forum/coordinatorsubscriptionlist', [ForumSubscriptionController::class, 'showCoordinatorListSubscriptions'])->name('forum.coordinatorsubscriptionlist');
Route::get('/forum/internationalchaptersubscriptionlist', [ForumSubscriptionController::class, 'showInternationalChapterListSubscriptions'])->name('forum.internationalchaptersubscriptionlist');
Route::get('/forum/internationalcoordinatorsubscriptionlist', [ForumSubscriptionController::class, 'showInternationalCoordinatorListSubscriptions'])->name('forum.internationalcoordinatorsubscriptionlist');

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
