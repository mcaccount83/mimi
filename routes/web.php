<?php

use App\Http\Controllers\AdminController;
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
use App\Http\Controllers\UserController;
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

// Login and Logout Routes...Used for Board & Coordinator Layouts
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('logout', [LoginController::class, 'logout']);

// Password Reset Routes...Used for Board & Coordinator Layouts
Route::get('password/request', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// UserControllert Routes...Used for Board & Coordinator Layouts
Route::get('/checkemail/{email}', [UserController::class, 'checkEmail'])->name('checkemail');
Route::post('/checkpassword', [UserController::class, 'checkCurrentPassword'])->name('checkpassword');
Route::put('/updatepassword', [UserController::class, 'updatePassword'])->name('updatepassword');
Route::get('/load-email-details/{chId}', [UserController::class, 'loadEmailDetails'])->name('load.email.details');
Route::get('/load-coordinator-list/{id}', [UserController::class, 'loadCoordinatorList'])->name('load.coordinator.list');

// Error Log Routes...Public, No login required
Route::get('admin/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');

Route::middleware(['auth'])->group(function () {
    Route::get('/payment-logs', [PaymentController::class, 'index'])->name('payment-logs.index');
    Route::get('/payment-logs/{id}', [PaymentController::class, 'show'])->name('payment-logs.show');
});

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

// Public Page Routes...Public, No login required
Route::get('/chapter-links', [PublicController::class, 'chapterLinks'])->name('chapter.links');
Route::get('/chapter-resources', [PublicController::class, 'chapterResources'])->name('board.resources');
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

// Admin Controller Routes...Coordinator Login Required
Route::get('/admin/reregdate', [AdminController::class, 'showReRegDate'])->name('admin.reregdate');
Route::get('/admin/reregdate/{id}', [AdminController::class, 'EditReRegDate'])->name('admin.editreregdate');
Route::post('/admin/updatereregdate/{id}', [AdminController::class, 'UpdateReRegDate'])->name('admin.updatereregdate');
Route::post('/admin/resetProbationSubmission', [AdminController::class, 'resetProbationSubmission'])->name('admin.resetProbationSubmission');
Route::get('/admin/eoy', [AdminController::class, 'showEOY'])->name('admin.eoy');
Route::post('/admin/resetdisbandedusers', [AdminController::class, 'resetDisbandedUsers'])->name('admin.resetdisbandedusers');
Route::post('/admin/resetoutgoingusers', [AdminController::class, 'resetOutgoingUsers'])->name('admin.resetoutgoingusers');
Route::post('/admin/resetyear', [AdminController::class, 'resetYear'])->name('resetyear');
Route::post('/admin/updateeoydatabase', [AdminController::class, 'updateEOYDatabase'])->name('admin.updateeoydatabase');
Route::post('/admin/updateeoydatabaseafter', [AdminController::class, 'updateEOYDatabaseAFTERTesting'])->name('admin.updateeoydatabaseafter');
Route::post('/admin/updatedatadatabase', [AdminController::class, 'updateDataDatabase'])->name('admin.updatedatadatabase');
Route::post('/admin/updateeoytesting', [AdminController::class, 'updateEOYTesting'])->name('admin.updateeoytesting');
Route::post('/admin/updateeoylive', [AdminController::class, 'updateEOYLive'])->name('admin.updateeoylive');
Route::post('/admin/updatesubscribelists', [AdminController::class, 'updateSubscribeLists'])->name('admin.updatesubscribelists');
Route::post('/admin/updateunsubscribelists', [AdminController::class, 'updateUnsubscribeLists'])->name('admin.updateunsubscribelists');
Route::get('/admin/chapterlist', [AdminController::class, 'listActiveChapters'])->name('admin.chapterlist');
Route::get('/admin/chapterlistzapped', [AdminController::class, 'listZappedChapters'])->name('admin.chapterlistzapped');
Route::get('/admin/googledrive', [AdminController::class, 'showGoogleDrive'])->name('admin.googledrive');
Route::post('/admin/updategoogledrive', [AdminController::class, 'updateGoogleDrive'])->name('admin.updategoogledrive');
Route::get('/admin/adminemail', [AdminController::class, 'showAdminEmail'])->name('admin.adminemail');
Route::post('/admin/updateadminemail', [AdminController::class, 'updateAdminEmail'])->name('admin.updateadminemail');
Route::post('/admin/updatechapterdelete', [AdminController::class, 'updateChapterDelete'])->name('admin.updatechapterdelete');
Route::post('/admin/updatecoordinatordelete', [AdminController::class, 'updateCoordinatorDelete'])->name('admin.updatecoordinatordelete');

Route::get('/admin/confreglist', [AdminController::class, 'showConfRegList'])->name('admin.confreglist');
// Update these routes in your web.php
Route::get('/admin/editconflist', [AdminController::class, 'editConfList'])->name('admin.editconflist');
Route::post('/admin/updateconflist', [AdminController::class, 'updateConfList'])->name('admin.updateconflist');
Route::post('/admin/storeconf', [AdminController::class, 'storeConf'])->name('admin.storeconf');
Route::delete('/admin/deleteconf/{id}', [AdminController::class, 'deleteConf'])->name('admin.deleteconf');


// Admin Controller Routes...Coordinator Login Required
Route::get('/adminreports/useradmin', [AdminController::class, 'showUserAdmin'])->name('adminreports.useradmin');
Route::get('/adminreports/duplicateuser', [AdminController::class, 'showDuplicate'])->name('adminreports.duplicateuser');
Route::get('/adminreports/duplicateboardid', [AdminController::class, 'showDuplicateId'])->name('adminreports.duplicateboardid');
Route::get('/adminreports/nopresident', [AdminController::class, 'showNoPresident'])->name('adminreports.nopresident');
Route::get('/adminreports/outgoingboard', [AdminController::class, 'showOutgoingBoard'])->name('adminreports.outgoingboard');
Route::get('/adminreports/disbandedboard', [AdminController::class, 'showDisbandedBoard'])->name('adminreports.disbandedboard');

// Resources Controller Routes...Coordinator Login Required
Route::get('/resources/bugs', [ResourcesController::class, 'showBugs'])->name('resources.bugs');
Route::post('/resources/addbugs', [ResourcesController::class, 'addBugs'])->name('resources.addbugs');
Route::post('/resources/updatebugs/{id}', [ResourcesController::class, 'updateBugs'])->name('resources.updatebugs');
Route::get('/resources/downloads', [ResourcesController::class, 'showDownloads'])->name('resources.downloads');
Route::get('/resources/resources', [ResourcesController::class, 'showResources'])->name('resources.resources');
Route::post('/resources/addresources', [ResourcesController::class, 'addResources'])->name('resources.addresources');
Route::post('/resources/updateresources/{id}', [ResourcesController::class, 'updateResources'])->name('resources.updateresources');
Route::get('/resources/toolkit', [ResourcesController::class, 'showToolkit'])->name('resources.toolkit');
Route::post('/resources/addtoolkit', [ResourcesController::class, 'addToolkit'])->name('resources.addtoolkit');
Route::post('/resources/updatetoolkit/{id}', [ResourcesController::class, 'updateToolkit'])->name('resources.updatetoolkit');

// Chapter Controller Routes...Coordinator Login Required
Route::get('/chapter/pendingchapterlist', [ChapterController::class, 'showPendingChapters'])->name('chapters.chaplistpending');
Route::get('/international/pendingchapterlist', [ChapterController::class, 'showIntPendingChapters'])->name('international.intchaplistpending');
Route::get('/pendingchapterdetailsedit/{id}', [ChapterController::class, 'editPendingChapterDetails'])->name('chapters.editpending');
Route::post('/pendingchapterdetailsupdate/{id}', [ChapterController::class, 'updatePendingChapterDetails'])->name('chapters.updatepending');
Route::post('/chapter/updateapprove', [ChapterController::class, 'updateApproveChapter'])->name('chapters.updateapprove');
Route::post('/chapter/updatedecline', [ChapterController::class, 'updateDeclineChapter'])->name('chapters.updatedecline');
Route::get('/chapter/declinedchapterlist', [ChapterController::class, 'showNotApprovedChapters'])->name('chapters.chaplistdeclined');
Route::get('/international/declinedchapterlist', [ChapterController::class, 'showIntNotApprovedChapters'])->name('international.intchaplistdeclined');
Route::get('/chapter/chapterlist', [ChapterController::class, 'showChapters'])->name('chapters.chaplist');
Route::get('/chapter/zapped', [ChapterController::class, 'showZappedChapter'])->name('chapters.chapzapped');
Route::get('/chapter/inquiries', [ChapterController::class, 'showChapterInquiries'])->name('chapters.chapinquiries');
Route::get('/chapter/inquirieszapped', [ChapterController::class, 'showZappedChapterInquiries'])->name('chapters.chapinquirieszapped');
Route::get('/international/chapter', [ChapterController::class, 'showIntChapter'])->name('international.intchapter');
Route::get('/international/chapterzapped', [ChapterController::class, 'showIntZappedChapter'])->name('international.intchapterzapped');
Route::get('/chapterdetails/{id}', [ChapterController::class, 'viewChapterDetails'])->name('chapters.view');
Route::get('/chapters/checkein', [ChapterController::class, 'checkEIN'])->name('chapters.checkein');
Route::post('/chapterdetails/updateein', [ChapterController::class, 'updateEIN'])->name('chapters.updateein');
Route::post('/chapter/updatedisband', [ChapterController::class, 'updateChapterDisband'])->name('chapters.updatechapdisband');
Route::post('/chapter/unzap', [ChapterController::class, 'updateChapterUnZap'])->name('chapters.updatechapterunzap');
Route::get('/chapternew', [ChapterController::class, 'addChapterNew'])->name('chapters.addnew');
Route::post('/chapternewupdate', [ChapterController::class, 'updateChapterNew'])->name('chapters.updatenew');
Route::get('/chapterdetailsedit/{id}', [ChapterController::class, 'editChapterDetails'])->name('chapters.edit');
Route::post('/chapterdetailsupdate/{id}', [ChapterController::class, 'updateChapterDetails'])->name('chapters.update');
Route::get('/chapterboardedit/{id}', [ChapterController::class, 'editChapterBoard'])->name('chapters.editboard');
Route::post('/chapterboardupdate/{id}', [ChapterController::class, 'updateChapterBoard'])->name('chapters.updateboard');
Route::get('/chapter/website', [ChapterController::class, 'showChapterWebsite'])->name('chapters.chapwebsite');
Route::get('/international/website', [ChapterController::class, 'showIntWebsite'])->name('international.chapwebsite');
Route::get('/chapter/socialmedia', [ChapterController::class, 'showRptSocialMedia'])->name('chapreports.chaprptsocialmedia');
Route::get('/international/socialmedia', [ChapterController::class, 'showIntSocialMedia'])->name('international.chaprptsocialmedia');
Route::get('/chapterwebsiteedit/{id}', [ChapterController::class, 'editChapterWebsite'])->name('chapters.editwebsite');
Route::post('/chapterwebsiteupdate/{id}', [ChapterController::class, 'updateChapterWebsite'])->name('chapters.updatewebsite');
Route::get('/chapter/boardlist', [ChapterController::class, 'showChapterBoardlist'])->name('chapters.chapboardlist');

// ChapterReport Controller Routes...Coordinator Login Required
Route::get('/chapterreports/chapterstatus', [ChapterReportController::class, 'showRptChapterStatus'])->name('chapreports.chaprptchapterstatus');
Route::get('/chapterreports/einstatus', [ChapterReportController::class, 'showRptEINstatus'])->name('chapreports.chaprpteinstatus');
Route::get('/chapterreports/inteinstatus', [ChapterReportController::class, 'showIntEINstatus'])->name('international.inteinstatus');
Route::get('/chapterirsedit/{id}', [ChapterController::class, 'editChapterIRS'])->name('chapters.editirs');
Route::post('/chapterirsupdate/{id}', [ChapterController::class, 'updateChapterIRS'])->name('chapters.updateirs');
Route::get('/chapterreports/newchapters', [ChapterReportController::class, 'showRptNewChapters'])->name('chapreports.chaprptnewchapters');
Route::get('/chapterreports/largechapters', [ChapterReportController::class, 'showRptLargeChapters'])->name('chapreports.chaprptlargechapters');
Route::get('/chapterreports/probation', [ChapterReportController::class, 'showRptProbation'])->name('chapreports.chaprptprobation');
Route::get('/chapterreports/coordinators', [ChapterReportController::class, 'showRptChapterCoordinators'])->name('chapreports.chaprptcoordinators');

// Email Controller Routes...Coordinator Login Required
Route::post('/chapter/sendstartup', [EmailController::class, 'sendChapterStartup'])->name('chapters.sendstartup');
Route::post('/chapter/sendnewchapter', [EmailController::class, 'sendNewChapterEmail'])->name('chapters.sendnewchapter');
Route::post('/chapter/sendchapter', [EmailController::class, 'sendChapterEmail'])->name('chapters.sendchapter');
Route::post('/chapter/sendchapterrereg', [EmailController::class, 'sendChapterReReg'])->name('chapters.sendchapterrereg');
Route::post('/chapter/sendchapterrereglate', [EmailController::class, 'sendChapterReRegLate'])->name('chapters.sendchapterrereglate');
Route::get('/eoy/boardreport/reminder', [EmailController::class, 'sendEOYBoardReportReminder'])->name('eoyreports.eoyboardreportreminder');
Route::get('/eoy/financialreport/reminder', [EmailController::class, 'sendEOYFinancialReportReminder'])->name('eoyreports.eoyfinancialreportreminder');
Route::get('/eoy/status/reminder', [EmailController::class, 'sendEOYStatusReminder'])->name('eoyreports.eoystatusreminder');



// Coordinator Controller Routes...Coordinator Login Required
Route::get('/coordinator/pending', [CoordinatorController::class, 'showPendingCoordinator'])->name('coordinators.coordpending');
Route::get('/international/pending', [CoordinatorController::class, 'showIntPendingCoordinator'])->name('international.intcoordpending');
Route::get('/coordapplication/{id}', [CoordinatorController::class, 'viewCoordApplication'])->name('coordinators.viewapplication');
Route::post('/pendingcoordinatordetailsupdate/{id}', [CoordinatorController::class, 'updatePendingCoordinatorDetails'])->name('coordinators.updatepending');
Route::post('/coordinator/updateapprove', [CoordinatorController::class, 'updateApproveApplication'])->name('coordinators.updateapprove');
Route::post('/coordinator/updatedecline', [CoordinatorController::class, 'updateRejectApplication'])->name('coordinators.updatereject');
Route::get('/coordinator/rejected', [CoordinatorController::class, 'showRejectedCoordinator'])->name('coordinators.coordrejected');
Route::get('/international/rejected', [CoordinatorController::class, 'showIntRejectedCoordinator'])->name('international.intcoordrejected');
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

// CoordinatorReport Controller Routes...Coordinator Login Required
Route::get('/coordreports/volunteerutilization', [CoordinatorReportController::class, 'showRptVolUtilization'])->name('coordreports.coordrptvolutilization');
Route::get('/coordreports/appreciation', [CoordinatorReportController::class, 'showRptAppreciation'])->name('coordreports.coordrptappreciation');
Route::get('/coordreports/birthdays', [CoordinatorReportController::class, 'showRptBirthdays'])->name('coordreports.coordrptbirthdays');
Route::get('/coordreports/reportingtree', [CoordinatorReportController::class, 'showRptReportingTree'])->name('coordreports.coordrptreportingtree');

// Export Controller Routes...Coordinator Login Required
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

// Board Controller Routes...Board Login Required
Route::get('/board/newchapterstatus/{id}', [BoardPendingController::class, 'showNewChapterStatus'])->name('board.newchapterstatus');
Route::get('/board/profile/{id}', [BoardController::class, 'editProfile'])->name('board.editprofile');
Route::post('/board/profileupdate/{id}', [BoardController::class, 'updateProfile'])->name('board.updateprofile');
// Route::get('/board/president/{id}', [BoardController::class, 'editPresident'])->name('board.editpresident');
// Route::post('/board/presidentupdate/{id}', [BoardController::class, 'updatePresident'])->name('board.updatepresident');
// Route::get('/board/member/{id}', [BoardController::class, 'editMember'])->name('board.editmember');
// Route::post('/board/memberupdate/{id}', [BoardController::class, 'updateMember'])->name('board.updatemember');
Route::get('/board/boardreport/{id}', [BoardController::class, 'editBoardReport'])->name('board.editboardreport');
Route::post('/board/boardreportupatea/{id}', [BoardController::class, 'updateBoardReport'])->name('board.updateboardreport');
// Route::get('/board/reregpayment/{id}', [BoardController::class, 'editReregistrationPaymentForm'])->name('board.editreregpayment');
// Route::get('/board/donation/{id}', [BoardController::class, 'editDonationForm'])->name('board.editdonate');
Route::get('/board/manual/{id}', [BoardController::class, 'editManualOrderForm'])->name('board.editmanual');
Route::get('/board/probation/{id}', [BoardController::class, 'editProbationSubmission'])->name('board.editprobation');
Route::post('/board/probationupdate/{id}', [BoardController::class, 'updateProbationSubmission'])->name('board.updateprobation');
Route::get('/board/m2mdonation/{id}', [BoardController::class, 'editM2MDonationForm'])->name('board.editm2mdonation');
Route::get('/board/resources/{id}', [BoardController::class, 'viewResources'])->name('board.viewresources');

// Financial Report Controller Routes...Board Login Required
Route::get('/board/financialreport/{id}', [FinancialReportController::class, 'editFinancialReport'])->name('board.editfinancialreport');
Route::post('/board/financialreportupdate/{id}', [FinancialReportController::class, 'updateFinancialReport'])->name('board.updatefinancialreport');
Route::get('/board/disbandchecklist/{id}', [FinancialReportController::class, 'editDisbandChecklist'])->name('board.editdisbandchecklist');
Route::post('/board/disbandchecklistupdate/{id}', [FinancialReportController::class, 'updateDisbandChecklist'])->name('board.updatedisbandchecklist');
Route::post('/board/disbandreportupdate/{id}', [FinancialReportController::class, 'updateDisbandReport'])->name('board.updatedisbandreport');

// EOYReports Controller Routes...Coordinator Login Required
Route::get('/eoy/status', [EOYReportController::class, 'showEOYStatus'])->name('eoyreports.eoystatus');
// Route::get('/eoy/status/reminder', [EOYReportController::class, 'sendEOYStatusReminder'])->name('eoyreports.eoystatusreminder');
Route::get('/eoy/editstatus/{id}', [EOYReportController::class, 'editEOYDetails'])->name('eoyreports.view');
Route::post('/eoy/updatestatus/{id}', [EOYReportController::class, 'updateEOYDetails'])->name('eoyreports.update');
Route::get('/eoy/boardreport', [EOYReportController::class, 'showEOYBoardReport'])->name('eoyreports.eoyboardreport');
// Route::get('/eoy/activateboardreport/{id}', [EOYReportController::class, 'activateBoard'])->name('eoyreports.activateboardreport');
// Route::get('/eoy/boardreport/reminder', [EOYReportController::class, 'sendEOYBoardReportReminder'])->name('eoyreports.eoyboardreportreminder');
Route::get('/eoy/editboardreport/{id}', [EOYReportController::class, 'editBoardReport'])->name('eoyreports.editboardreport');
Route::post('/eoy/editboardreport/{id}', [EOYReportController::class, 'editBoardReport'])->name('eoyreports.activateboardreport');
Route::post('eoy/updateboardreport/{id}', [EOYReportController::class, 'updateEOYBoardReport'])->name('eoyreports.updateboardreport');
Route::get('/eoy/financialreport', [EOYReportController::class, 'showEOYFinancialReport'])->name('eoyreports.eoyfinancialreport');
// Route::get('/eoy/financialreport/reminder', [EOYReportController::class, 'sendEOYFinancialReportReminder'])->name('eoyreports.eoyfinancialreportreminder');
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
Route::get('/eoy/irssubmission', [EOYReportController::class, 'showIRSSubmission'])->name('eoyreports.eoyirssubmission');
Route::get('/eoy/editirssubmission/{id}', [EOYReportController::class, 'editIRSSubmission'])->name('eoyreports.editirssubmission');
Route::post('/eoy/updateirssubmission/{id}', [EOYReportController::class, 'updateIRSSubmission'])->name('eoyreports.updateirssubmission');

Route::get('/eoy/irsintsubmission', [EOYReportController::class, 'showIRSIntSubmission'])->name('eoyreports.eoyirsintsubmission');


// PDF Controller Routes...Used for Board & Coordinator Layouts
Route::get('/board/chapteringoodstanding/pdf/{id}', [PDFController::class, 'generateGoodStanding'])->name('pdf.chapteringoodstanding');
Route::get('/board/financialreport/pdf/{id}', [PDFController::class, 'generateFinancialReport'])->name('pdf.financialreport');
Route::get('/financial-report-pdf', [PDFController::class, 'saveFinancialReport'])->name('pdf.financialreport');
Route::post('/generate-probation-letter', [PDFController::class, 'saveProbationLetter'])->name('pdf.generateProbationLetter');
Route::get('/board/newchapfaxcover/pdf/{id}', [PDFController::class, 'generateNewChapterFaxCover'])->name('pdf.newchapfaxcover');
Route::get('/board/eodeptfaxcover/pdf', [PDFController::class, 'generateEODeptFaxCover'])->name('pdf.eodeptfaxcover');
Route::get('/board/combinedsubordinatefiling/pdf', [PDFController::class, 'generateCombinedIRSSubordinateFiling'])->name('pdf.combinedsubordinatefiling');
Route::get('/board/combinedirsupdates/pdf', [PDFController::class, 'generateCombinedIRSUpdates'])->name('pdf.combinedirsupdates');
Route::get('/board/combinednamechangeletter/pdf/{id}', [PDFController::class, 'generateCombinedNameChangeLetter'])->name('pdf.combinednamechangeletter');
Route::get('/board/combinedirsfilingcorrections/pdf', [PDFController::class, 'generateCombinedIRSFilingCorrections'])->name('pdf.combinedirsfilingcorrections');

// Google Controller -- Uploading Files Routes...Used for Board & Coordinator Layouts
Route::post('/files/storeEIN/{id}', [GoogleController::class, 'storeEIN']);
Route::post('/files/storeRoster/{id}', [GoogleController::class, 'storeRoster']);
Route::post('/files/store990n/{id}', [GoogleController::class, 'store990N']);
Route::post('/files/storeStatement1/{id}', [GoogleController::class, 'storeStatement1']);
Route::post('/files/storeStatement2/{id}', [GoogleController::class, 'storeStatement2']);
Route::post('/files/storeAward/{id}', [GoogleController::class, 'storeAward']);
Route::post('/files/storeResources/{id}', [GoogleController::class, 'storeResources'])->name('store.resources');
Route::post('/files/storeToolkit/{id}', [GoogleController::class, 'storeToolkit'])->name('store.toolkit');

// Payment Controller Routes...Coordinator Login Required
Route::get('/board/reregpayment/{id}', [PaymentController::class, 'editReregistrationPaymentForm'])->name('board.editreregpayment');
Route::get('/board/donation/{id}', [PaymentController::class, 'editDonationForm'])->name('board.editdonate');
Route::post('/process-payment', [PaymentController::class, 'reRegistrationPayment'])->name('process.payment');
Route::post('/process-donation', [PaymentController::class, 'm2mPayment'])->name('process.donation');
Route::post('/process-manual', [PaymentController::class, 'manualPayment'])->name('process.manual');
Route::get('/chapter/reregistration', [PaymentReportController::class, 'showChapterReRegistration'])->name('chapters.chapreregistration');
Route::get('/international/reregistration', [PaymentReportController::class, 'showIntReRegistration'])->name('international.intregistration');
Route::get('/chapter/reregistrationreminder', [PaymentReportController::class, 'createChapterReRegistrationReminder'])->name('chapters.chapreregreminder');
Route::get('/chapter/reregistrationlatereminder', [PaymentReportController::class, 'createChapterReRegistrationLateReminder'])->name('chapters.chaprereglatereminder');
Route::get('/chapter/donations', [PaymentReportController::class, 'showRptDonations'])->name('chapreports.chaprptdonations');
Route::get('/international/donation', [PaymentReportController::class, 'showIntdonation'])->name('international.intdonation');
Route::get('/chapterpaymentedit/{id}', [PaymentReportController::class, 'editChapterPayment'])->name('chapters.editpayment');
Route::post('/chapterpaymentupdate/{id}', [PaymentReportController::class, 'updateChapterPayment'])->name('chapters.updatepayment');

// Forum Subscription Controller Routes...Used for Board & Coordinator Layouts
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
