<?php

use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PDFController;
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
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes...
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Confirm Password
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

// Email Verification Routes...
Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

//Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

//Auth::routes();

// Route::middleware(['preventBackHistory'])->group(function () {
//     Route::get('/home', [HomeController::class, 'index'])->name('home');
//     Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
// });

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

/**
 * Routes for Custom Links
 */
Route::get('/checkemail/{id}', [ChapterController::class, 'checkEmail'])->name('check.email');
Route::get('/checkreportid/{id}', [ChapterController::class, 'checkReportId'])->name('check.reportid');
Route::get('/getregion/{id}', [CoordinatorController::class, 'getRegionList'])->name('get.region');
Route::get('/getreporting', [CoordinatorController::class, 'getReportingList'])->name('get.reporting');
Route::get('/getdirectreport', [CoordinatorController::class, 'getDirectReportingList'])->name('get.directreport');
Route::get('/getchapterprimary', [CoordinatorController::class, 'getChapterPrimaryFor'])->name('get.chapterprimary');
Route::get('/chapter-links', [ChapterController::class, 'chapterLinks'])->name('chapter.links');

/**
 * Routes for Google Upload Files
 */
Route::get('/files', [GoogleController::class, 'index']);
Route::post('/files/store/{id}', [GoogleController::class, 'store']);
Route::post('/files/storeEIN/{id}', [GoogleController::class, 'storeEIN']);
Route::post('/files/storeRoster/{id}', [GoogleController::class, 'storeRoster']);
Route::post('/files/store990n/{id}', [GoogleController::class, 'store990n']);
Route::post('/files/storeStatement1/{id}', [GoogleController::class, 'storeStatement1']);
Route::post('/files/storeStatement2/{id}', [GoogleController::class, 'storeStatement2']);
Route::get('/files/googletest/{id}', [GoogleController::class, 'show'])->name('files.googletest');

/**
 * Routes for Chapters
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
Route::get('/chapter/financial/{id}', [ChapterController::class, 'showFinancialReport'])->name('chapter.showfinancial');
Route::post('/chapter/financial/{id}', [ChapterController::class, 'storeFinancialReport'])->name('chapter.storefinancial');
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

/**
 * Routes for Coordinator
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
 * Routes for Exporting File
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
Route::get('/export/appreciation', [ExportController::class, 'ndexAppreciation'])->name('export.appreciation');
Route::get('/export/intchapter', [ExportController::class, 'indexInternationalChapter'])->name('export.intchapter');
Route::get('/export/intzapchapter', [ExportController::class, 'indexInternationalZapChapter'])->name('export.intzapchapter');
Route::get('/export/intcoordinator', [ExportController::class, 'indexIntCoordinator'])->name('export.intcoordinator');
Route::get('/export/intretcoordinator', [ExportController::class, 'indexIntRetCoordinator'])->name('export.intretcoordinator');
Route::get('/export/inteinstatus', [ExportController::class, 'indexIntEINStatus'])->name('export.inteinstatus');
Route::get('/export/intrereg', [ExportController::class, 'indexIntReReg'])->name('export.intrereg');
Route::get('/export/inteoystatus', [ExportController::class, 'indexIntEOYStatus'])->name('export.inteoystatus');
Route::get('/export/boardelection', [ExportController::class, 'indexBoardElection'])->name('export.boardelection');

/**
 * Routes for Board Memebers
 */
Route::post('/board/update/{id}', [BoardController::class, 'updatePresident'])->name('board.update');
Route::post('/member/update/{id}', [BoardController::class, 'updateMember'])->name('member.update');
Route::get('/boardinfo', [BoardController::class, 'showBoardInfo'])->name('boardinfo.showboardinfo');
Route::post('/boardinfo/{id}', [BoardController::class, 'createBoardInfo'])->name('boardinfo.createboardinfo');
Route::get('/board/financial/{id}', [BoardController::class, 'showFinancialReport'])->name('board.showfinancial');
Route::post('/board/financial/{id}', [BoardController::class, 'storeFinancialReport'])->name('board.storefinancial');
Route::get('/board/financial/print/{id}', [BoardController::class, 'printFinancialReport'])->name('board.printfinancial');
Route::get('/board/financial/print2/{id}', [BoardController::class, 'printFinancialReport2'])->name('board.printfinancial2');

/**
 * Routes for Reports
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
Route::get('/adminreports/duplicateuser', [ReportController::class, 'showDuplicate'])->name('report.duplicateuser');
Route::get('/adminreports/duplicateboardid', [ReportController::class, 'showDuplicateId'])->name('report.duplicateboardid');
Route::get('/adminreports/multipleboard', [ReportController::class, 'showMultiple'])->name('report.multipleboard');
Route::get('/adminreports/nopresident', [ReportController::class, 'showNoPresident'])->name('report.nopresident');
Route::get('/adminreports/outgoingboard', [ReportController::class, 'showOutgoingBoard'])->name('report.outgoingboard');
Route::post('/adminreports/outgoingactivate', [ReportController::class, 'storeActivateOutgoingBoard'])->name('report.outgoingactivate');

/**
 * Routes for PDF
 */
Route::get('/board/financial/pdf/{id}', [PDFController::class, 'generatePdf'])->name('pdf.financialreport');
Route::get('/chapter/financial/pdf/{id}', [PDFController::class, 'generatePdf'])->name('pdf.financialreport');
