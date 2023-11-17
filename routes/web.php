<?php

//use App\Http\Controllers\Auth;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ReportController;

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

//Route::view('/', 'welcome')->name('welcome');

//Route::middleware('preventBackHistory')->group(function () {
// Authentication Routes
//   Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
//  Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
//   Route::post('login', [LoginController::class, 'login']);
//    Route::post('logout', [LoginController::class, 'logout'])->name('user.logout');

// Registration Routes
//    Route::get('register', [Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
//    Route::post('register', [Auth\RegisterController::class, 'register']);

// Password Reset Routes
//   Route::get('password/reset', [Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
//   Route::post('password/email', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
//    Route::get('password/reset/{token}', [Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
//    Route::post('password/reset', [Auth\ResetPasswordController::class, 'reset']);
//    Route::post('password/update', [Auth\ResetPasswordController::class, 'update'])->name('password.update');

// Home Route
//    Route::get('/home', [HomeController::class, 'index'])->name('home');

// Your other custom routes can be defined here

//});

//Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::middleware(['preventBackHistory'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
});

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

/**
 * Routes for Custom Links
 */
Route::get('/datalist', [ChapterController::class, 'showDatalist'])->name('get.users');
Route::get('/checkemail/{id}', [ChapterController::class, 'checkEmail'])->name('check.email');
Route::get('/checkreportid/{id}', [ChapterController::class, 'checkReportId'])->name('check.reportid');
Route::get('/prezlist/{id}', [ChapterController::class, 'addToPrezList']);
Route::get('/cordprezlist/{id}', [CoordinatorController::class, 'addToPrezList']);
Route::get('/cordvollist/{id}', [CoordinatorController::class, 'addToVolList']);
Route::get('/getregion/{id}', [CoordinatorController::class, 'getRegionList'])->name('get.region');
Route::get('/getreporting', [CoordinatorController::class, 'getReportingList'])->name('get.reporting');
Route::get('/getdirectreport', [CoordinatorController::class, 'getDirectReportingList'])->name('get.directreport');
Route::get('/getchapterprimary', [CoordinatorController::class, 'getChapterPrimaryFor'])->name('get.chapterprimary');
Route::get('/chapter-links', [ChapterController::class, 'chapterLinks'])->name('chapter.links');

/**
 * Routes for Chapters
 */
//Route::get('/chapterlist', 'ChapterController@index');
Route::get('/chapter/list', [ChapterController::class, 'list'])->name('chapter.list');
Route::get('/chapter/create', [ChapterController::class, 'create'])->name('chapters.create');
Route::post('/chapter/create', [ChapterController::class, 'store'])->name('chapters.store');
Route::get('/chapter/zapped/view/{id}', [ChapterController::class, 'showZappedChapterView']);
Route::get('/chapter/edit/{id}', [ChapterController::class, 'edit']);
Route::post('/chapter/update/{id}', [ChapterController::class, 'update'])->name('chapters.update');
Route::get('/chapter/international', [ChapterController::class, 'showIntChapter'])->name('chapter.inter');
Route::get('/chapter/zapped', [ChapterController::class, 'showZappedChapter'])->name('chapter.zapped');
Route::post('/chapter/updatezapped/{id}', [ChapterController::class, 'updateZappedChapter'])->name('chapter.updatezapped');
Route::get('/chapter/international/zap', [ChapterController::class, 'showIntZappedChapter'])->name('chapter.interzap');
Route::get('/chapter/international/zapped/view/{id}', [ChapterController::class, 'showIntZappedChapterView']);
Route::get('/chapter/international/view/{id}', [ChapterController::class, 'showIntChapterView']);
Route::get('/chapter/unzap/{id}', [ChapterController::class, 'unZappedChapter']);
Route::get('/chapter/inquiries', [ChapterController::class, 'showInquiriesChapter'])->name('chapter.inquiries');
Route::get('/chapter/inquirieszapped', [ChapterController::class, 'zappedInquiriesChapter'])->name('chapter.inquirieszapped');
Route::get('/chapter/inquiriesview/{id}', [ChapterController::class, 'inquiriesview'])->name('chapter.inquiriesview');
Route::get('/chapter/website', [ChapterController::class, 'showWebsiteChapter'])->name('chapter.website');
Route::get('/chapter/website/edit/{id}', [ChapterController::class, 'editWebsite']);
Route::post('/chapter/website/update/{id}', [ChapterController::class, 'updateWebsite'])->name('chapter.updateweb');
Route::get('/chapter/view/{id}', [ChapterController::class, 'showChapterView']);
//Route::post('/chapter/disband', [ChapterController::class, 'chapterDisband']);
Route::post('/chapter/disband', [ChapterController::class, 'chapterDisband'])->name('chapter.disband');
Route::post('/chapter/resetpswd', [ChapterController::class, 'chapterResetPassword'])->name('chapter.resetpswd');
Route::get('/chapter/financial/{id}', [ChapterController::class, 'showFinancialReport'])->name('chapter.showfinancial');
Route::post('/chapter/financial/{id}', [ChapterController::class, 'storeFinancialReport'])->name('chapter.storefinancial');
Route::get('chapter/boardinfo/{id}', [ChapterController::class, 'showBoardInfo'])->name('chapter.showboardinfo');
Route::post('chapter/boardinfo/{id}', [ChapterController::class, 'createBoardInfo'])->name('chapter.createboardinfo');
Route::get('/chapter/re-registration', [ChapterController::class, 'showReRegistration'])->name('chapter.registration');
Route::get('/chapter/re-registration/payment/{id}', [ChapterController::class, 'showPayment'])->name('chapter.payment');
Route::post('/chapter/payment/{id}', [ChapterController::class, 'makePayment'])->name('chapter.makepayment');
Route::get('/chapter/re-registration/notes/{id}', [ChapterController::class, 'showReRegNotes'])->name('chapter.reregnotes');
Route::post('/chapter/re-regnotes/{id}', [ChapterController::class, 'makeReRegNotes'])->name('chapter.makereregnotes');
Route::get('/chapter/m2mdonation/{id}', [ChapterController::class, 'showDonation'])->name('chapter.donation');
Route::post('/chapter/donation/{id}', [ChapterController::class, 'createDonation'])->name('chapter.createdonation');
Route::get('/chapter/boundaryview/{id}', [ChapterController::class, 'boundaryview'])->name('chapter.boundaryview');
Route::post('/chapter/updateboundary/{id}', [ChapterController::class, 'updateBoundary'])->name('chapter.updateboundary');
Route::get('/chapter/awardsview/{id}', [ChapterController::class, 'awardsView'])->name('chapter.awardsview');
Route::post('/chapter/updateawards/{id}', [ChapterController::class, 'updateAwards'])->name('chapter.updateawards');
Route::get('/chapter/re-registration/reminder', [ChapterController::class, 'reminderReRegistration'])->name('chapter.reminder');
Route::get('/chapter/re-registration/latereminder', [ChapterController::class, 'lateReRegistration'])->name('chapter.latereminder');
Route::get('/chapter/statusview/{id}', [ChapterController::class, 'statusView'])->name('chapter.statusview');
Route::post('/chapter/updatestatus/{id}', [ChapterController::class, 'updateStatus'])->name('chapter.updatestatus');

/**
 * Routes for Coordinator
 */
Route::get('userData', [CoordinatorController::class, 'updateemail'])->name('coordinator.upretired');
Route::get('/coordinator/dashboard/', [CoordinatorController::class, 'showDashboard'])->name('coordinator.showdashboard');
Route::post('/coordinator/dashboard/{id}', [CoordinatorController::class, 'updateDashboard'])->name('coordinator.updatedashboard');
Route::get('/coordinatorlist', [CoordinatorController::class, 'index'])->name('coordinator.list');
Route::get('/coordinator/retired', [CoordinatorController::class, 'showRetiredCoordinator'])->name('coordinator.retired');
Route::get('/coordinator/unretired/{id}', [CoordinatorController::class, 'showUnretiredCoordinator'])->name('coordinator.unretired');
Route::get('/coordinator/international', [CoordinatorController::class, 'showIntCoordinator'])->name('coordinator.inter');
Route::get('/coordinator/international/view/{id}', [CoordinatorController::class, 'showIntCoordinatorView'])->name('coordinator.interview');
Route::get('/coordinator/retiredinternational', [CoordinatorController::class, 'showIntRetCoordinator'])->name('coordinator.retinter');
Route::get('/coordinator/retiredinternational/view/{id}', [CoordinatorController::class, 'showIntRetCoordinatorView'])->name('coordinator.retinterview');
Route::get('/coordinator/create', [CoordinatorController::class, 'create'])->name('coordinator.create');
Route::post('/coordinator/create', [CoordinatorController::class, 'store'])->name('coordinator.store');
Route::get('/coordinator/edit/{id}', [CoordinatorController::class, 'edit'])->name('coordinator.edit');
Route::post('/coordinator/update2/{id}', [CoordinatorController::class, 'update2'])->name('coordinator.update2');
Route::post('/coordinator/update/{id}', [CoordinatorController::class, 'update'])->name('coordinator.update');
Route::get('/coordinator/retired/view/{id}', [CoordinatorController::class, 'showRetiredCoordinatorView']);
Route::get('/coordinator/role/{id}', [CoordinatorController::class, 'showChangeRole'])->name('coordinator.role');
Route::post('/coordinator/update/role/{id}', [CoordinatorController::class, 'updateRole'])->name('coordinator.updaterole');
Route::get('/coordinator/profile/', [CoordinatorController::class, 'showProfile'])->name('coordinator.showprofile');
Route::post('/coordinator/profile/{id}', [CoordinatorController::class, 'updateProfile'])->name('coordinator.updateprofile');
Route::get('/coordinator/appreciation/{id}', [CoordinatorController::class, 'appreciation'])->name('coordinator.appreciation');
Route::post('/coordinator/updateappreciation/{id}', [CoordinatorController::class, 'updateAppreciation'])->name('coordinator.updateappreciation');
Route::get('/coordinator/birthday/{id}', [CoordinatorController::class, 'birthday'])->name('coordinator.birthday');
Route::post('/coordinator/updatebirthday/{id}', [CoordinatorController::class, 'updateBirthday'])->name('coordinator.updatebirthday');

/**
 * Routes for Exporting File
 */
//Route::get('/export/chapter', 'ExportController@exportChapter')->name('export.chapter');
Route::get('/export/chapter/{id}', [ExportController::class, 'exportChapter'])->name('export.chapter');
Route::get('/export/zapchapter', [ExportController::class, 'exportZappedChapter'])->name('export.zapchapter');
Route::get('/export/coordinator/{id}', [ExportController::class, 'exportCoordinator'])->name('export.coordinator');
Route::get('/export/retiredcoordinator', [ExportController::class, 'exportRetiredCoordinator'])->name('export.retiredcoordinator');
Route::get('/export/einstatus', [ExportController::class, 'exportEINStatus'])->name('export.einstatus');
Route::get('/export/rereg', [ExportController::class, 'exportReReg'])->name('export.rereg');
Route::get('/export/eoystatus', [ExportController::class, 'exportEOYStatus'])->name('export.eoystatus');
Route::get('/export/chaptercoordinator', [ExportController::class, 'exportChapterCoordinator'])->name('export.chaptercoordinator');
Route::get('/export/chapteraward/{id}', [ExportController::class, 'exportChapterAwardList'])->name('export.chapteraward');
Route::get('/export/appreciation', [ExportController::class, 'exportAppreciation'])->name('export.appreciation');
Route::get('/export/intchapter', [ExportController::class, 'exportInternationalChapter'])->name('export.intchapter');
Route::get('/export/intzapchapter', [ExportController::class, 'exportInternationalZapChapter'])->name('export.intzapchapter');
Route::get('/export/intcoordinator', [ExportController::class, 'exportIntCoordinator'])->name('export.intcoordinator');
Route::get('/export/intretcoordinator', [ExportController::class, 'exportIntRetCoordinator'])->name('export.intretcoordinator');
Route::get('/export/inteinstatus', [ExportController::class, 'exportIntEINStatus'])->name('export.inteinstatus');
Route::get('/export/intrereg', [ExportController::class, 'exportIntReReg'])->name('export.intrereg');
Route::get('/export/inteoystatus', [ExportController::class, 'exportIntEOYStatus'])->name('export.inteoystatus');
Route::get('/export/boardelection', [ExportController::class, 'exportBoardElection'])->name('export.boardelection');

/**
 * Routes for Board Memebers
 */
Route::post('/board/update/{id}', [BoardController::class, 'update'])->name('board.update');
Route::post('/member/update/{id}', [BoardController::class, 'memberUpdate'])->name('member.update');
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
Route::get('/reports/intm2mdonation', [ReportController::class, 'intM2Mdonation'])->name('report.intm2mdonation');
Route::get('/reports/m2mdonation', [ReportController::class, 'showM2Mdonation'])->name('report.m2mdonation');
Route::get('/reports/inteinstatus', [ReportController::class, 'intEINstatus'])->name('report.inteinstatus');
Route::get('/reports/einstatus', [ReportController::class, 'showEINstatus'])->name('report.einstatus');
Route::get('/reports/boardlist', [ReportController::class, 'showBoardlist'])->name('report.boardlist');
Route::get('/reports/socialmedia', [ReportController::class, 'showSocialMedia'])->name('report.socialmedia');
Route::get('/reports/downloads', [ReportController::class, 'showDownloads'])->name('report.downloads');
Route::get('/yearreports/review', [ReportController::class, 'showReportToReview'])->name('report.review');
Route::get('/yearreports/boardinfo', [ReportController::class, 'showReportToBoardInfo'])->name('report.boardinfo');
Route::get('/yearreports/boardinfo/reminder', [ReportController::class, 'reminderBoardInfo'])->name('report.boardinforeminder');
Route::get('/yearreports/review/reminder', [ReportController::class, 'reminderFinancialReport'])->name('report.financialreminder');
Route::get('/yearreports/status/reminder', [ReportController::class, 'reminderEOYReportsLate'])->name('report.eoylatereminder');
Route::get('/yearreports/chapteraward', [ReportController::class, 'showChapterAwards'])->name('report.awards');
Route::get('/yearreports/boundaryissue', [ReportController::class, 'showReportToIssues'])->name('report.issues');
Route::get('/yearreports/chapterawards', [ReportController::class, 'showChapterAwards'])->name('report.chapterawards');
Route::get('/yearreports/eoystatus', [ReportController::class, 'showEOYStatus'])->name('report.eoystatus');
Route::get('/yearreports/addawards', [ReportController::class, 'addAwards'])->name('report.addawards');
Route::get('/adminreports/duplicateuser', [ReportController::class, 'showDuplicate'])->name('report.duplicateuser');
Route::get('/adminreports/duplicateboardid', [ReportController::class, 'showDuplicateId'])->name('report.duplicateboardid');
Route::get('/adminreports/multipleboard', [ReportController::class, 'showMultiple'])->name('report.multipleboard');
Route::get('/adminreports/nopresident', [ReportController::class, 'showNoPresident'])->name('report.nopresident');

/**
 * Routes for PDF
 */
Route::get('/board/financialPDF/{id}', [PDFController::class, 'financialReport'])->name('FinancialReport');
