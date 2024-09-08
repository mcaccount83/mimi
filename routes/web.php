<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
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

Route::put('/board/password', [BoardController::class, 'updatePassword'])->name('board.updatepassword');
Route::post('/board/check-password', [BoardController::class, 'checkCurrentPassword'])->name('board.checkpassword');
Route::put('/coordinator/password', [CoordinatorController::class, 'updatePassword'])->name('coordinator.updatepassword');
Route::post('/coordinator/check-password', [CoordinatorController::class, 'checkCurrentPassword'])->name('coordinator.checkpassword');
Route::post('/chapter/password', [ChapterController::class, 'updatePassword'])->name('chapter.updatepassword');

/**
 * Routes for Custom Links
 */
Route::get('/checkemail/{id}', [ChapterController::class, 'checkEmail'])->name('check.email');
Route::get('/checkreportid/{id}', [ChapterController::class, 'checkReportId'])->name('check.reportid');
Route::get('/getregion/{id}', [CoordinatorController::class, 'getRegionList'])->name('get.region');
Route::get('/getreporting', [CoordinatorController::class, 'getReportingList'])->name('get.reporting');
Route::get('/getdirectreport', [CoordinatorController::class, 'getDirectReportingList'])->name('get.directreport');
Route::get('/getchapterprimary', [CoordinatorController::class, 'getChapterPrimaryFor'])->name('get.chapterprimary');
// Route::get('/chapterlinks', [ChapterController::class, 'chapterLinks'])->name('chapter.links');

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');

Route::get('/test-500', function () { abort(500); });
Route::get('/test-404', function () { abort(404); });
Route::get('/test-403', function () { abort(403); });
Route::get('/test-401', function () { abort(401); });
Route::get('/test-419', function () { abort(419); });
Route::get('/test-429', function () { abort(429); });

/**
 * Routes for Public Pages
 */
Route::get('/chapter-links', [PublicController::class, 'chapterLinks'])->name('chapter.links');
Route::get('/chapter-resources', [PublicController::class, 'chapterResources'])->name('board.resources');

/**
 * Routes for Admin Controller
 */
Route::get('/admin/eoy', [AdminController::class, 'showEOY'])->name('admin.eoy');
Route::post('/admin/eoy/update/{id}', [AdminController::class, 'updateEOY'])->name('admin.eoyupdate');
Route::post('/admin/reset-year', [AdminController::class, 'resetYear'])->name('resetYear');
Route::get('/admin/reregdate', [AdminController::class, 'showReRegDate'])->name('admin.reregdate');
Route::get('/admin/reregdate/{id}', [AdminController::class, 'EditReRegDate'])->name('admin.editreregdate');
Route::post('/admin/updatereregdate/{id}', [AdminController::class, 'UpdateReRegDate'])->name('admin.updatereregdate');
Route::get('/admin/bugs', [AdminController::class, 'showBugs'])->name('admin.bugs');
Route::post('/admin/addbugs', [AdminController::class, 'addBugs'])->name('admin.addbugs');
Route::post('/admin/updatebugs/{id}', [AdminController::class, 'updateBugs'])->name('admin.updatebugs');
Route::get('/admin/resources', [AdminController::class, 'showResources'])->name('admin.resources');
Route::post('/admin/addresources', [AdminController::class, 'addResources'])->name('admin.addresources');
Route::post('/admin/updateresources/{id}', [AdminController::class, 'updateResources'])->name('admin.updateresources');
Route::get('/admin/toolkit', [AdminController::class, 'showToolkit'])->name('admin.toolkit');
Route::post('/admin/addtoolkit', [AdminController::class, 'addToolkit'])->name('admin.addtoolkit');
Route::post('/admin/updatetoolkit/{id}', [AdminController::class, 'updateToolkit'])->name('admin.updatetoolkit');
Route::get('/adminreports/mailqueue', [AdminController::class, 'showMailQueue'])->name('admin.mailqueue');
Route::get('/adminreports/duplicateuser', [AdminController::class, 'showDuplicate'])->name('admin.duplicateuser');
Route::get('/adminreports/duplicateboardid', [AdminController::class, 'showDuplicateId'])->name('admin.duplicateboardid');
Route::get('/adminreports/multipleboard', [AdminController::class, 'showMultiple'])->name('admin.multipleboard');
Route::get('/adminreports/nopresident', [AdminController::class, 'showNoPresident'])->name('admin.nopresident');
Route::get('/adminreports/outgoingboard', [AdminController::class, 'showOutgoingBoard'])->name('admin.outgoingboard');
Route::post('/adminreports/updateoutgoingboard', [AdminController::class, 'updateOutgoingBoard'])->name('admin.updateoutgoingboard');

/**
 * Routes for Payment Controller (Payment Processing)
 */
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('process.payment');
Route::post('/process-donation', [PaymentController::class, 'processDonation'])->name('process.donation');

/**
 * Routes for Google Congroler (Uploading Files)
 */
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

/**
 * Routes for Chapter Controller
 */
Route::get('/chapter/list', [ChapterController::class, 'index'])->name('chapter.list');
Route::get('/chapter/create', [ChapterController::class, 'create'])->name('chapters.create');
Route::post('/chapter/create', [ChapterController::class, 'store'])->name('chapters.store');
Route::get('/chapter/edit/{id}', [ChapterController::class, 'edit']);
Route::post('/chapter/update/{id}', [ChapterController::class, 'update'])->name('chapters.update');
Route::get('/chapter/getEmail{id}', [ChapterController::class, 'getEmailDetails'])->name('get.emaildetails');
Route::get('/chapter/zapped/view/{id}', [ChapterController::class, 'showZappedChapterView']);
Route::get('/chapter/international', [ChapterController::class, 'showIntChapter'])->name('chapter.inter');
Route::get('/chapter/zapped', [ChapterController::class, 'showZappedChapter'])->name('chapter.zapped');
Route::post('/chapter/updatezapped/{id}', [ChapterController::class, 'updateZappedChapter'])->name('chapter.updatezapped');
Route::get('/chapter/international/zap', [ChapterController::class, 'showIntZappedChapter'])->name('chapter.interzap');
Route::get('/chapter/international/zapped/view/{id}', [ChapterController::class, 'showIntZappedChapterView']);
Route::get('/chapter/international/view/{id}', [ChapterController::class, 'showIntChapterView']);
Route::get('/chapter/unzap/{id}', [ChapterController::class, 'storeUnZappedChapter']);
Route::get('/chapter/inquiries', [ChapterController::class, 'showInquiriesChapter'])->name('chapter.inquiries');
Route::get('/chapter/inquirieszapped', [ChapterController::class, 'showZappedInquiries'])->name('chapter.inquirieszapped');
Route::get('/chapter/inquiriesview/{id}', [ChapterController::class, 'showInquiries'])->name('chapter.inquiriesview');
Route::get('/chapter/website', [ChapterController::class, 'showWebsiteChapter'])->name('chapter.website');
Route::get('/chapter/website/edit/{id}', [ChapterController::class, 'editWebsite']);
Route::post('/chapter/website/update/{id}', [ChapterController::class, 'updateWebsite'])->name('chapter.updateweb');
Route::post('/chapter/disband', [ChapterController::class, 'storeChapterDisband'])->name('chapter.disband');
Route::post('/chapter/resetpswd', [ChapterController::class, 'updateChapterResetPassword'])->name('chapter.resetpswd');
Route::post('/chapter/resetpassword', [ChapterController::class, 'updateResetPassword'])->name('chapter.resetpassword');
Route::get('/chapter/financial/{id}', [ChapterController::class, 'showFinancialReport'])->name('chapter.showfinancial');
Route::post('/chapter/financial/{id}', [ChapterController::class, 'storeFinancialReport'])->name('chapter.storefinancial');
Route::get('/chapter/clearreview/{id}', [ChapterController::class, 'updateClearReview']);
Route::get('/chapter/unsubmit/{id}', [ChapterController::class, 'updateUnsubmit']);


Route::get('chapter/boardinfo/{id}', [ChapterController::class, 'showBoardInfo'])->name('chapter.showboardinfo');
Route::post('chapter/boardinfo/{id}', [ChapterController::class, 'createBoardInfo'])->name('chapter.createboardinfo');
Route::get('/chapter/re-registration', [ChapterController::class, 'showReRegistration'])->name('chapter.registration');
Route::get('/chapter/re-registration/payment/{id}', [ChapterController::class, 'showPayment'])->name('chapter.payment');
Route::post('/chapter/payment/{id}', [ChapterController::class, 'createPayment'])->name('chapter.makepayment');
Route::get('/chapter/re-registration/notes/{id}', [ChapterController::class, 'showReRegNotes'])->name('chapter.reregnotes');
Route::post('/chapter/re-regnotes/{id}', [ChapterController::class, 'createReRegNotes'])->name('chapter.makereregnotes');
Route::get('/chapter/m2mdonation/{id}', [ChapterController::class, 'showDonation'])->name('chapter.donation');
Route::post('/chapter/donation/{id}', [ChapterController::class, 'createDonation'])->name('chapter.createdonation');
Route::get('/chapter/boundaryview/{id}', [ChapterController::class, 'showBoundary'])->name('chapter.boundaryview');
Route::post('/chapter/updateboundary/{id}', [ChapterController::class, 'updateBoundary'])->name('chapter.updateboundary');
Route::get('/chapter/awardsview/{id}', [ChapterController::class, 'showAwardsView'])->name('chapter.awardsview');
Route::post('/chapter/updateawards/{id}', [ChapterController::class, 'updateAwards'])->name('chapter.updateawards');
Route::get('/chapter/re-registration/reminder', [ChapterController::class, 'createReminderReRegistration'])->name('chapter.reminder');
Route::get('/chapter/re-registration/latereminder', [ChapterController::class, 'createLateReRegistration'])->name('chapter.latereminder');
Route::get('/chapter/statusview/{id}', [ChapterController::class, 'showStatusView'])->name('chapter.statusview');
Route::post('/chapter/updatestatus/{id}', [ChapterController::class, 'updateStatus'])->name('chapter.updatestatus');
Route::get('/chapter/einnotes/{id}', [ChapterController::class, 'showEinNotes'])->name('chapter.einnotes');
Route::post('/chapter/einnotes/update/{id}', [ChapterController::class, 'createEinNotes'])->name('chapter.makeeinnotes');

/**
 * Routes for Coordinator Controller
 */
Route::get('/coordinatorlist', [CoordinatorController::class, 'index'])->name('coordinator.list');
Route::get('/coordinator/create', [CoordinatorController::class, 'create'])->name('coordinator.create');
Route::post('/coordinator/create', [CoordinatorController::class, 'store'])->name('coordinator.store');
Route::get('/coordinator/edit/{id}', [CoordinatorController::class, 'edit'])->name('coordinator.edit');
Route::post('/coordinator/update2/{id}', [CoordinatorController::class, 'update2'])->name('coordinator.update2');
Route::post('/coordinator/update/{id}', [CoordinatorController::class, 'update'])->name('coordinator.update');
Route::get('/coordinator/dashboard/', [CoordinatorController::class, 'showDashboard'])->name('coordinator.showdashboard');
Route::post('/coordinator/dashboard/{id}', [CoordinatorController::class, 'updateDashboard'])->name('coordinator.updatedashboard');
Route::get('/coordinator/retired', [CoordinatorController::class, 'showRetiredCoordinator'])->name('coordinator.retired');
Route::get('/coordinator/unretired/{id}', [CoordinatorController::class, 'showUnretiredCoordinator'])->name('coordinator.unretired');
Route::get('/coordinator/international', [CoordinatorController::class, 'showIntCoordinator'])->name('coordinator.inter');
Route::get('/coordinator/international/view/{id}', [CoordinatorController::class, 'showIntCoordinatorView'])->name('coordinator.interview');
Route::get('/coordinator/retiredinternational', [CoordinatorController::class, 'showIntRetCoordinator'])->name('coordinator.retinter');
Route::get('/coordinator/retiredinternational/view/{id}', [CoordinatorController::class, 'showIntRetCoordinatorView'])->name('coordinator.retinterview');
Route::get('/coordinator/retired/view/{id}', [CoordinatorController::class, 'showRetiredCoordinatorView']);
Route::get('/coordinator/role/{id}', [CoordinatorController::class, 'showChangeRole'])->name('coordinator.role');
Route::post('/coordinator/update/role/{id}', [CoordinatorController::class, 'updateRole'])->name('coordinator.updaterole');
Route::get('/coordinator/profile/', [CoordinatorController::class, 'showProfile'])->name('coordinator.showprofile');
Route::post('/coordinator/profile/{id}', [CoordinatorController::class, 'updateProfile'])->name('coordinator.updateprofile');
Route::get('/coordinator/appreciation/{id}', [CoordinatorController::class, 'showAppreciation'])->name('coordinator.appreciation');
Route::post('/coordinator/updateappreciation/{id}', [CoordinatorController::class, 'updateAppreciation'])->name('coordinator.updateappreciation');
Route::get('/coordinator/birthday/{id}', [CoordinatorController::class, 'showBirthday'])->name('coordinator.birthday');
Route::post('/coordinator/updatebirthday/{id}', [CoordinatorController::class, 'updateBirthday'])->name('coordinator.updatebirthday');

/**
 * Routes for Export Controller
 */
Route::get('/export/chapter/{id}', [ExportController::class, 'indexChapter'])->name('export.chapter');
Route::get('/export/zapchapter', [ExportController::class, 'indexZappedChapter'])->name('export.zapchapter');
Route::get('/export/coordinator/{id}', [ExportController::class, 'indexCoordinator'])->name('export.coordinator');
Route::get('/export/retiredcoordinator', [ExportController::class, 'indexRetiredCoordinator'])->name('export.retiredcoordinator');
Route::get('/export/einstatus', [ExportController::class, 'indexEINStatus'])->name('export.einstatus');
Route::get('/export/rereg', [ExportController::class, 'indexReReg'])->name('export.rereg');
Route::get('/export/eoystatus', [ExportController::class, 'indexEOYStatus'])->name('export.eoystatus');
Route::get('/export/chaptercoordinator', [ExportController::class, 'indexChapterCoordinator'])->name('export.chaptercoordinator');
Route::get('/export/chapteraward/{id}', [ExportController::class, 'indexChapterAwardList'])->name('export.chapteraward');
Route::get('/export/appreciation', [ExportController::class, 'indexAppreciation'])->name('export.appreciation');
Route::get('/export/intchapter', [ExportController::class, 'indexInternationalChapter'])->name('export.intchapter');
Route::get('/export/intzapchapter', [ExportController::class, 'indexInternationalZapChapter'])->name('export.intzapchapter');
Route::get('/export/intcoordinator', [ExportController::class, 'indexIntCoordinator'])->name('export.intcoordinator');
Route::get('/export/intretcoordinator', [ExportController::class, 'indexIntRetCoordinator'])->name('export.intretcoordinator');
Route::get('/export/inteinstatus', [ExportController::class, 'indexIntEINStatus'])->name('export.inteinstatus');
Route::get('/export/intrereg', [ExportController::class, 'indexIntReReg'])->name('export.intrereg');
Route::get('/export/inteoystatus', [ExportController::class, 'indexIntEOYStatus'])->name('export.inteoystatus');
Route::get('/export/boardelection', [ExportController::class, 'indexBoardElection'])->name('export.boardelection');
Route::get('/export/boardlist', [ExportController::class, 'indexBoardList'])->name('export.boardlist');

/**
 * Routes for Board Controller
 */
Route::post('/board/update/{id}', [BoardController::class, 'updatePresident'])->name('board.update');
Route::post('/member/update/{id}', [BoardController::class, 'updateMember'])->name('member.update');
Route::get('/boardinfo', [BoardController::class, 'showBoardInfo'])->name('boardinfo.showboardinfo');
Route::post('/boardinfo/{id}', [BoardController::class, 'createBoardInfo'])->name('boardinfo.createboardinfo');
Route::get('/board/financial/{id}', [BoardController::class, 'showFinancialReport'])->name('board.showfinancial');
Route::post('/board/financial/{id}', [BoardController::class, 'storeFinancialReport'])->name('board.storefinancial');
Route::get('/board/reregpayment', [BoardController::class, 'showReregistrationPaymentForm'])->name('board.showreregpayment');
Route::get('/board/m2mdonation', [BoardController::class, 'showM2MDonationForm'])->name('board.showm2mdonation');
Route::get('/board/resources', [BoardController::class, 'showResources'])->name('board.resources');

/**
 * Routes for Report Controller
 */
Route::get('/reports/chapterstatus', [ReportController::class, 'showChapterStatus'])->name('report.chapterstatus');
Route::get('/reports/chapternew', [ReportController::class, 'showChapterNew'])->name('report.chapternew');
Route::get('/reports/chapterlarge', [ReportController::class, 'showChapterLarge'])->name('report.chapterlarge');
Route::get('/reports/chapterprobation', [ReportController::class, 'showChapterProbation'])->name('report.chapterprobation');
Route::get('/reports/chaptercoordinators', [ReportController::class, 'showChapterCoordinators'])->name('report.chaptercoordinators');
Route::get('/reports/coordinatortodo', [ReportController::class, 'showCoordinatorToDo'])->name('report.coordinatortodo');
Route::get('/reports/intcoordinatortodo', [ReportController::class, 'showIntCoordinatorToDo'])->name('report.intcoordinatortodo');
Route::get('/reports/chaptervolunteer', [ReportController::class, 'showChapterVolunteer'])->name('report.chaptervolunteer');
Route::get('/reports/reportingtree', [ReportController::class, 'showReportingTree'])->name('report.reportingtree');
Route::get('/reports/appreciation', [ReportController::class, 'showAppreciation'])->name('report.appreciation');
Route::get('/reports/birthday', [ReportController::class, 'showBirthday'])->name('report.birthday');
Route::get('/reports/intm2mdonation', [ReportController::class, 'showIntM2Mdonation'])->name('report.intm2mdonation');
Route::get('/reports/m2mdonation', [ReportController::class, 'showM2Mdonation'])->name('report.m2mdonation');
Route::get('/reports/inteinstatus', [ReportController::class, 'showIntEINstatus'])->name('report.inteinstatus');
Route::get('/reports/einstatus', [ReportController::class, 'showEINstatus'])->name('report.einstatus');
Route::get('/reports/boardlist', [ReportController::class, 'showBoardlist'])->name('report.boardlist');
Route::get('/reports/socialmedia', [ReportController::class, 'showSocialMedia'])->name('report.socialmedia');
Route::get('/reports/downloads', [ReportController::class, 'showDownloads'])->name('report.downloads');
Route::get('/yearreports/review', [ReportController::class, 'showReportToReview'])->name('report.review');
Route::get('/yearreports/boardinfo', [ReportController::class, 'showReportToBoardInfo'])->name('report.boardinfo');
Route::get('/yearreports/boardinfo/reminder', [ReportController::class, 'showReminderBoardInfo'])->name('report.boardinforeminder');
Route::get('/yearreports/review/reminder', [ReportController::class, 'showreminderFinancialReport'])->name('report.financialreminder');
Route::get('/yearreports/status/reminder', [ReportController::class, 'showReminderEOYReportsLate'])->name('report.eoylatereminder');
Route::get('/yearreports/chapteraward', [ReportController::class, 'showChapterAwards'])->name('report.awards');
Route::get('/yearreports/boundaryissue', [ReportController::class, 'showReportToIssues'])->name('report.issues');
Route::get('/yearreports/chapterawards', [ReportController::class, 'showChapterAwards'])->name('report.chapterawards');
Route::get('/yearreports/eoystatus', [ReportController::class, 'showEOYStatus'])->name('report.eoystatus');
Route::get('/yearreports/addawards', [ReportController::class, 'showAddAwards'])->name('report.addawards');

/**
 * Routes for PDF Controller
 */
Route::get('/board/financial/pdf/{id}', [PDFController::class, 'generatePdf'])->name('pdf.financialreport');
Route::get('/chapter/financial/pdf/{id}', [PDFController::class, 'generatePdf'])->name('pdf.financialreport');
Route::get('/board/chapteringoodstanding/pdf/{id}', [PDFController::class, 'generateGoodStanding'])->name('pdf.chapteringoodstanding');
Route::get('/chapter/disbandletter/pdf/{id}', [PDFController::class, 'generateDisbandLetter'])->name('pdf.disbandletter');

/**
 * Routes for BoardList Controller
 */
//  Route::get('/boardlist', [BoardListController::class, 'index'])->name('boardlist.index');
// Route::get('/boardlist/{id}', [BoardListController::class, 'show'])->name('boardlist.show');
// Add routes for other methods as needed
