<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\EOYReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InternationalController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoordinatorReportController;
use App\Http\Controllers\ChapterReportController;
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
// Route::get('logs', [LogViewerController::class, 'index'])->name('logs');
// Queue Jobs Routes...
Route::get('admin/jobs', [MailController::class, 'index'])->name('queue-monitor::index');
// Route::get('', \romanzipp\QueueMonitor\Controllers\ShowQueueMonitorController::class)->name('queue-monitor::index');
Route::delete('monitors/{monitor}', \romanzipp\QueueMonitor\Controllers\DeleteMonitorController::class)->name('queue-monitor::destroy');
Route::patch('monitors/retry/{monitor}', \romanzipp\QueueMonitor\Controllers\RetryMonitorController::class)->name('queue-monitor::retry');
Route::delete('purge', \romanzipp\QueueMonitor\Controllers\PurgeMonitorsController::class)->name('queue-monitor::purge');
// Error Pages Test Routes...
Route::get('/test-500', function () { abort(500); });
Route::get('/test-404', function () { abort(404); });
Route::get('/test-403', function () { abort(403); });
Route::get('/test-401', function () { abort(401); });
Route::get('/test-419', function () { abort(419); });
Route::get('/test-429', function () { abort(429); });

// Public Page Routes...
Route::get('/chapter-links', [PublicController::class, 'chapterLinks'])->name('chapter.links');
Route::get('/chapter-resources', [PublicController::class, 'chapterResources'])->name('board.resources');

// Admin Controller Routes...
Route::get('/admin/eoy', [AdminController::class, 'showEOY'])->name('admin.eoy');
Route::post('/admin/resetyear', [AdminController::class, 'resetYear'])->name('resetyear');
Route::post('/admin/updateeoydatabase', [AdminController::class, 'updateEOYDatabase'])->name('admin.updateeoydatabase');
Route::post('/admin/updatedatadatabase', [AdminController::class, 'updateDataDatabase'])->name('admin.updatedatadatabase');
Route::post('/admin/updateeoycoordinator', [AdminController::class, 'updateEOYCoordinator'])->name('admin.updateeoycoordinator');
Route::post('/admin/updateeoychapter', [AdminController::class, 'updateEOYChapter'])->name('admin.updateeoychapter');
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
Route::get('/adminreports/multipleboard', [AdminController::class, 'showMultiple'])->name('admin.multipleboard');
Route::get('/adminreports/nopresident', [AdminController::class, 'showNoPresident'])->name('admin.nopresident');
Route::get('/adminreports/outgoingboard', [AdminController::class, 'showOutgoingBoard'])->name('admin.outgoingboard');
// Route::get('/adminreports/mailqueue', [AdminController::class, 'showMailQueue'])->name('admin.mailqueue');
// Route::post('/admin/eoy/update/{id}', [AdminController::class, 'updateEOY'])->name('admin.eoyupdate');
// Route::post('/adminreports/updateoutgoingboard', [AdminController::class, 'updateOutgoingBoard'])->name('admin.updateoutgoingboard');

// Payment Controller Routes...
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('process.payment');
Route::post('/process-donation', [PaymentController::class, 'processDonation'])->name('process.donation');

// PDF Controller Routes...
Route::get('/board/financial/pdf/{id}', [PDFController::class, 'generatePdf'])->name('pdf.financialreport');
Route::get('/chapter/financial/pdf/{id}', [PDFController::class, 'generatePdf'])->name('pdf.financialreport');
Route::get('/board/chapteringoodstanding/pdf/{id}', [PDFController::class, 'generateGoodStanding'])->name('pdf.chapteringoodstanding');
Route::get('/chapter/disbandletter/pdf/{id}', [PDFController::class, 'generateDisbandLetter'])->name('pdf.disbandletter');

// Google Controller -- Uploading Files Routes...
Route::post('/files/storeEIN/{id}', [GoogleController::class, 'storeEIN']);
Route::post('/files/storeRoster/{id}', [GoogleController::class, 'storeRoster']);
Route::post('/files/store990n/{id}', [GoogleController::class, 'store990N']);
Route::post('/files/storeStatement1/{id}', [GoogleController::class, 'storeStatement1']);
Route::post('/files/storeStatement2/{id}', [GoogleController::class, 'storeStatement2']);
Route::post('/files/storeAward1/{id}', [GoogleController::class, 'storeAward1']);
Route::post('/files/storeAward2/{id}', [GoogleController::class, 'storeAward2']);
Route::post('/files/storeAward3/{id}', [GoogleController::class, 'storeAward3']);
Route::post('/files/storeAward4/{id}', [GoogleController::class, 'storeAward4']);
Route::post('/files/storeAward5/{id}', [GoogleController::class, 'storeAward5']);
Route::post('/files/storeResources/{id}', [GoogleController::class, 'storeResources'])->name('store.resources');
Route::post('/files/storeToolkit/{id}', [GoogleController::class, 'storeToolkit'])->name('store.toolkit');

// Mail Controller Routes...
Route::post('/mail/chapterwelcome/{id}', [MailController::class, 'createNewChapterEmail'])->name('mail.chapterwelcome');

// Chapter Controller Routes...
Route::get('/chapter/chapterlist', [ChapterController::class, 'showChapters'])->name('chapters.chaplist');
Route::get('/chapter/chapternew', [ChapterController::class, 'showChapterNew'])->name('chapters.chapnew');
Route::post('/chapter/updatechapternew', [ChapterController::class, 'updateChapterNew'])->name('chapters.updatechapnew');
Route::get('/chapter/chapterview/{id}', [ChapterController::class, 'showChapterView'])->name('chapters.chapview');;
Route::post('/chapter/updatechapter/{id}', [ChapterController::class, 'updateChapter'])->name('chapters.updatechap');
Route::post('/chapter/updatedisband', [ChapterController::class, 'updateChapterDisband'])->name('chapters.updatechapdisband');
Route::get('/chapter/zapped', [ChapterController::class, 'showZappedChapter'])->name('chapters.chapzapped');
// Route::get('/chapter/zappedview/{id}', [ChapterController::class, 'showZappedChapterView'])->name('chapters.chapzappedview');
// Route::post('/chapter/updatezapped/{id}', [ChapterController::class, 'updateZappedChapter'])->name('chapters.updatechapzapped');
Route::post('/chapter/unzap/{id}', [ChapterController::class, 'updateChapterUnZap'])->name('unzap');
Route::get('/chapter/reregistration', [ChapterController::class, 'showChapterReRegistration'])->name('chapters.chapreregistration');
Route::get('/chapter/reregistrationpayment/{id}', [ChapterController::class, 'showChapterReRegistrationPayment'])->name('chapters.chapreregpayment');
Route::post('/chapter/updatereregistrtionpayment/{id}', [ChapterController::class, 'updateChapterReRegistrationPayment'])->name('chapters.updatechapreregpayment');
Route::get('/chapter/reregistrationnotes/{id}', [ChapterController::class, 'showChapterReRegistrationNotes'])->name('chapters.chapreregnotes');
Route::post('/chapter/updatereregistrtionpaymentnotes/{id}', [ChapterController::class, 'updateChapterReRegistrationNotes'])->name('chapters.updatechapreregnotes');
Route::get('/chapter/reregistrationreminder', [ChapterController::class, 'createChapterReRegistrationReminder'])->name('chapters.chapreregreminder');
Route::get('/chapter/reregistrationlatereminder', [ChapterController::class, 'createChapterReRegistrationLateReminder'])->name('chapters.chaprereglatereminder');
Route::get('/chapter/inquiries', [ChapterController::class, 'showChapterInquiries'])->name('chapters.chapinquiries');
// Route::get('/chapter/inquiriesview/{id}', [ChapterController::class, 'showChapterInquiriesView'])->name('chapters.chapinquiriesview');
Route::get('/chapter/inquirieszapped', [ChapterController::class, 'showZappedChapterInquiries'])->name('chapters.chapinquirieszapped');
// Route::get('/chapter/inquirieszappedview/{id}', [ChapterController::class, 'showZappedChapterInquiriesView'])->name('chapters.chapzappedinquiriesview');
Route::get('/chapter/website', [ChapterController::class, 'showChapterWebsite'])->name('chapters.chapwebsite');
Route::get('/chapter/websiteview/{id}', [ChapterController::class, 'showChapterWebsiteView'])->name('chapters.chapwebsiteview');
Route::post('/chapter/updatewebsite/{id}', [ChapterController::class, 'updateChapterWebsite'])->name('chapters.updatechapwebsite');
Route::get('/chapter/boardlist', [ChapterController::class, 'showChapterBoardlist'])->name('chapters.chapboardlist');
// ChapterReport Controller Routes...
Route::get('/chapterreports/chapterstatus', [ChapterReportController::class, 'showRptChapterStatus'])->name('chapreports.chaprptchapterstatus');
Route::get('/chapterreports/einstatus', [ChapterReportController::class, 'showRptEINstatus'])->name('chapreports.chaprpteinstatus');
Route::get('/chapterreports/einstatusview/{id}', [ChapterReportController::class, 'showRptEINstatusView'])->name('chapreports.chaprpteinstatusview');
Route::post('/chapterreports/updateeinstatus/{id}', [ChapterReportController::class, 'updateRptEINstatus'])->name('chapreports.updatechaprpteinstatus');
Route::get('/chapterreports/newchapters', [ChapterReportController::class, 'showRptNewChapters'])->name('chapreports.chaprptnewchapters');
Route::get('/chapterreports/largechapters', [ChapterReportController::class, 'showRptLargeChapters'])->name('chapreports.chaprptlargechapters');
Route::get('/chapterreports/probation', [ChapterReportController::class, 'showRptProbation'])->name('chapreports.chaprptprobation');
Route::get('/chapter/donations', [ChapterReportController::class, 'showRptDonations'])->name('chapreports.chaprptdonations');
Route::get('/chapter/donationsview/{id}', [ChapterReportController::class, 'showRptDonationsView'])->name('chapreports.chaprptdonationsview');
Route::post('/chapter/updatedonations/{id}', [ChapterReportController::class, 'updateRptDonations'])->name('chapreports.updatechaprptdonations');
Route::get('/chapterreports/socialmedia', [ChapterReportController::class, 'showRptSocialMedia'])->name('chapreports.chaprptsocialmedia');
Route::get('/chapterreports/coordinators', [ChapterReportController::class, 'showRptChapterCoordinators'])->name('chapreports.chaprptcoordinators');
// Route::get('/checkreportid/{id}', [ChapterController::class, 'checkReportId'])->name('check.reportid');
// Route::get('/chapter/getEmail{id}', [ChapterController::class, 'getEmailDetails'])->name('get.emaildetails');

// Coordinator Controller Routes...
Route::get('/coordinator/dashboard', [CoordinatorController::class, 'showCoordinatorDashboard'])->name('coordinators.coorddashboard');
Route::post('/coordinator/updatedashboard/{id}', [CoordinatorController::class, 'updateCoordinatorDashboard'])->name('coordinators.updatecoorddashboard');
Route::get('/coordinator/coordlist', [CoordinatorController::class, 'showCoordinators'])->name('coordinators.coordlist');
Route::get('/coordinator/coordinatornew', [CoordinatorController::class, 'showCoordinatorNew'])->name('coordinators.coordnew');
Route::post('/coordinator/updatecoordinatornew', [CoordinatorController::class, 'updateCoordinatorNew'])->name('coordinators.updatecoordnew');
Route::get('/coordinator/coordinatorview/{id}', [CoordinatorController::class, 'showCoordinatorView'])->name('coordinators.coordview');
Route::post('/coordinator/updatecoordinator/{id}', [CoordinatorController::class, 'updateCoordinator'])->name('coordinators.updatecoord');
Route::get('/coordinator/roleview/{id}', [CoordinatorController::class, 'showCoordinatorRoleView'])->name('coordinators.coordroleview');
Route::post('/coordinator/updaterole/{id}', [CoordinatorController::class, 'updateCoordinatorRole'])->name('coordinators.updatecoordrole');
Route::get('/getregion/{id}', [CoordinatorController::class, 'getRegionList'])->name('get.region');
Route::get('/getreporting', [CoordinatorController::class, 'getReportingList'])->name('get.reporting');
Route::get('/getdirectreport', [CoordinatorController::class, 'getDirectReportingList'])->name('get.directreport');
Route::get('/getchapterprimary', [CoordinatorController::class, 'getChapterPrimaryFor'])->name('get.chapterprimary');
Route::get('/coordinator/retired', [CoordinatorController::class, 'showRetiredCoordinator'])->name('coordinators.coordretired');
// Route::get('/coordinator/retiredview/{id}', [CoordinatorController::class, 'showRetiredCoordinatorView'])->name('coordinators.coordretiredview');
Route::get('/coordinator/updateunretire/{id}', [CoordinatorController::class, 'updateUnretireCoordinator'])->name('coordinators.updatecoordunretire');
// Route::get('/coordinator/profile', [CoordinatorController::class, 'showCoordinatorProfile'])->name('coordinators.coordprofile');
// Route::post('/coordinator/updateprofile/{id}', [CoordinatorController::class, 'updateCoordinatorProfile'])->name('coordinators.updatecoordprofile');
// CoordinatorReport Controller Routes...
Route::get('/coordreports/volunteerutilization', [CoordinatorReportController::class, 'showRptVolUtilization'])->name('coordreports.coordrptvolutilization');
Route::get('/coordreports/coordinatortodo', [CoordinatorReportController::class, 'showRptCoordToDo'])->name('coordreports.coordrpttodo');
Route::get('/coordreports/appreciation', [CoordinatorReportController::class, 'showRptAppreciation'])->name('coordreports.coordrptappreciation');
Route::get('/coordreports/appreciationview/{id}', [CoordinatorReportController::class, 'showRptAppreciationView'])->name('coordreports.coordrptappreciationview');
Route::post('/coordreports/updateappreciation/{id}', [CoordinatorReportController::class, 'updateRptAppreciation'])->name('coordreports.updatecoordrptappreciation');
Route::get('/coordreports/birthdays', [CoordinatorReportController::class, 'showRptBirthdays'])->name('coordreports.coordrptbirthdays');
Route::get('/coordreports/birthdaysview/{id}', [CoordinatorReportController::class, 'showRptBirthdaysView'])->name('coordreports.coordrptbirthdaysview');
Route::post('/coordreports/updatebirthdays/{id}', [CoordinatorReportController::class, 'updateRptBirthdays'])->name('coordreports.updatecoordrptbirthdays');
Route::get('/coordreports/reportingtree', [CoordinatorReportController::class, 'showRptReportingTree'])->name('coordreports.coordrptreportingtree');

// International Controller Routes...
Route::get('/international/chapter', [InternationalController::class, 'showIntChapter'])->name('international.intchapter');
// Route::get('/international/chapterview/{id}', [InternationalController::class, 'showIntChapterView'])->name('international.intchapterview');
Route::get('/international/chapterzapped', [InternationalController::class, 'showIntZappedChapter'])->name('international.intchapterzapped');
// Route::get('/international/chapterzappedview/{id}', [InternationalController::class, 'showIntZappedChapterView'])->name('international.intchapterzappedview');
Route::get('/international/coordinator', [InternationalController::class, 'showIntCoordinator'])->name('international.intcoord');
// Route::get('/international/coordinatorview/{id}', [InternationalController::class, 'showIntCoordinatorView'])->name('international.intcoordview');
Route::get('/international/coordinatorretired', [InternationalController::class, 'showIntCoordinatorRetired'])->name('international.intcoordretired');
// Route::get('/iternational/coordinatorretiredview/{id}', [InternationalController::class, 'showIntCoordinatorRetiredView'])->name('international.intcoordretiredview');
Route::get('/international/coordinatortodo', [InternationalController::class, 'showIntCoordinatorToDo'])->name('international.intcoordtodo');
Route::get('/international/einstatus', [InternationalController::class, 'showIntEINstatus'])->name('international.inteinstatus');
Route::get('/international/einstatusview/{id}', [InternationalController::class, 'showIntEINstatusView'])->name('international.inteinstatusview');
Route::post('/international/updateeinstatus/{id}', [InternationalController::class, 'updateIntEINstatus'])->name('international.updateinteinstatus');
Route::get('/international/donation', [InternationalController::class, 'showIntdonation'])->name('international.intdonation');

// EOYReports Controller Routes...
Route::get('/eoy/status', [EOYReportController::class, 'showEOYStatus'])->name('eoyreports.eoystatus');
Route::get('/eoy/status/reminder', [EOYReportController::class, 'showEOYStatusReminder'])->name('eoyreports.eoystatusreminder');
Route::get('/eoy/statusview/{id}', [EOYReportController::class, 'showEOYStatusView'])->name('eoyreports.eoystatusview');
Route::post('/eoy/updatestatus/{id}', [EOYReportController::class, 'updateEOYStatus'])->name('eoyreports.eoyupdatestatus');
Route::get('/eoy/boardreport', [EOYReportController::class, 'showEOYBoardReport'])->name('eoyreports.eoyboardreport');
Route::get('/eoy/boardreport/reminder', [EOYReportController::class, 'showEOYBoardReportReminder'])->name('eoyreports.eoyboardreportreminder');
Route::get('/eoy/boardreportview/{id}', [EOYReportController::class, 'showEOYBoardReportView'])->name('eoyreports.eoyboardreportview');
Route::post('eoy/updateboardreport/{id}', [EOYReportController::class, 'updateEOYBoardReport'])->name('eoyreports.eoyupdateboardreport');
Route::get('/eoy/financialreport', [EOYReportController::class, 'showEOYFinancialReport'])->name('eoyreports.eoyfinancialreport');
Route::get('/eoy/financialreport/reminder', [EOYReportController::class, 'showEOYFinancialReportReminder'])->name('eoyreports.eoyfinancialreportreminder');
Route::get('/eoy/financialreportview/{id}', [EOYReportController::class, 'showEOYFinancialReportView'])->name('eoyreports.eoyfinancialreportview');
Route::post('/eoy/updatefinancialreport/{id}', [EOYReportController::class, 'updateEOYFinancialReport'])->name('eoyreports.eoyupdatefinancialreport');
Route::get('/eoy/unsubmit/{id}', [EOYReportController::class, 'updateUnsubmit']);
Route::get('/eoy/clearreview/{id}', [EOYReportController::class, 'updateClearReview']);
Route::get('/eoy/attachments', [EOYReportController::class, 'showEOYAttachments'])->name('eoyreports.eoyattachments');
Route::get('/eoy/attachmentsview/{id}', [EOYReportController::class, 'showEOYAttachmentsView'])->name('eoyreports.eoyattachmentsview');
Route::post('/eoy/updateattachments/{id}', [EOYReportController::class, 'updateEOYAttachments'])->name('eoyreports.eoyupdateattachments');
Route::get('/eoy/boundaries', [EOYReportController::class, 'showEOYBoundaries'])->name('eoyreports.eoyboundaries');
Route::get('/eoy/boundariesview/{id}', [EOYReportController::class, 'showEOYBoundariesView'])->name('eoyreports.eoyboundariesview');
Route::post('/eoy/updateboundaries/{id}', [EOYReportController::class, 'updateEOYBoundaries'])->name('eoyreports.eoyupdateboundaries');
Route::get('/eoy/awards', [EOYReportController::class, 'showEOYAwards'])->name('eoyreports.eoyawards');
Route::get('/eoy/awardsview/{id}', [EOYReportController::class, 'showEOYAwardsView'])->name('eoyreports.eoyawardsview');
Route::post('/eoy/updateawards/{id}', [EOYReportController::class, 'updateEOYAwards'])->name('eoyreports.eoyupdateawards');

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
Route::post('/member/update/{id}', [BoardController::class, 'updateMember'])->name('member.update');
Route::get('/boardinfo', [BoardController::class, 'showBoardInfo'])->name('boardinfo.showboardinfo');
Route::post('/boardinfo/{id}', [BoardController::class, 'createBoardInfo'])->name('boardinfo.createboardinfo');
Route::get('/board/financial/{id}', [BoardController::class, 'showFinancialReport'])->name('board.showfinancial');
Route::post('/board/financial/{id}', [BoardController::class, 'storeFinancialReport'])->name('board.storefinancial');
Route::get('/board/reregpayment', [BoardController::class, 'showReregistrationPaymentForm'])->name('board.showreregpayment');
Route::get('/board/m2mdonation', [BoardController::class, 'showM2MDonationForm'])->name('board.showm2mdonation');
Route::get('/board/resources', [BoardController::class, 'showResources'])->name('board.resources');

// BoardList Controller -- Forum Routes...
// Route::get('/boardlist', [BoardListController::class, 'index'])->name('boardlist.index');
// Route::get('/boardlist/{id}', [BoardListController::class, 'show'])->name('boardlist.show');
// Add routes for other methods as needed

Route::get('/chapterdetails/{id}', [ChapterController::class, 'viewChapterDetails'])->name('chapters.view');
Route::post('/chapterdetails/updateEIN/{id}', [ChapterController::class, 'updateEIN'])->name('chapters.updateein');



Route::get('/chapterdetailsedit/{id}', [ChapterController::class, 'editChapterDetails'])->name('chapters.edit');
Route::post('/chapterdetailsupdate/{id}', [ChapterController::class, 'updateChapterDetails'])->name('chapters.update');
Route::get('/chapterboardedit/{id}', [ChapterController::class, 'editChapterBoard'])->name('chapters.editboard');
Route::post('/chapterboardupdate/{id}', [ChapterController::class, 'updateChapterBoard'])->name('chapters.updateboard');





Route::get('/coorddetails/{id}', [CoordinatorController::class, 'viewCoordDetails'])->name('coordinators.view');
Route::get('/coordprofile', [CoordinatorController::class, 'viewCoordProfile'])->name('coordinators.profile');
Route::post('/coordprofileupdate', [CoordinatorController::class, 'updateCoordProfile'])->name('coordinators.profileupdate');




