<?php

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

Route::get('/', function () {
    return view('welcome');

    return redirect('/login');
});

Auth::routes();

Route::middleware('preventBackHistory')->group(function () {
    Auth::routes();
    Route::get('/', function () {
        //return view('welcome');
        return redirect('/login');
    });
});

Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/home', 'HomeController@index')->name('home');
//Route::get('/home', 'ChapterController@index')->name('chapters.index');

/**
 * Routes for Custom Links
 */
Route::get('/datalist', 'ChapterController@showDatalist')->name('get.users');
Route::get('/checkemail/{id}', 'ChapterController@checkEmail');
Route::get('/checkreportid/{id}', 'ChapterController@checkReportId');
Route::get('/prezlist/{id}', 'ChapterController@addToPrezList');
Route::get('/cordprezlist/{id}', 'CoordinatorController@addToPrezList');
Route::get('/cordvollist/{id}', 'CoordinatorController@addToVolList');
Route::get('/getregion/{id}', 'CoordinatorController@getRegionList');
Route::get('/getreporting', 'CoordinatorController@getReportingList');
Route::get('/getdirectreport', 'CoordinatorController@getDirectReportingList');
Route::get('/getchapterprimary', 'CoordinatorController@getChapterPrimaryFor');
Route::get('/chapter-links', 'ChapterController@chapterLinks');

/**
 * Routes for Chapters
 */
//Route::get('/chapterlist', 'ChapterController@index');
Route::get('/chapter/list', 'ChapterController@list')->name('chapter.list');
Route::get('/chapter/create', 'ChapterController@create')->name('chapters.create');
Route::post('/chapter/create', 'ChapterController@store')->name('chapters.store');
Route::get('/chapter/zapped/view/{id}', 'ChapterController@showZappedChapterView');
Route::get('/chapter/edit/{id}', 'ChapterController@edit');
Route::post('/chapter/update/{id}', 'ChapterController@update')->name('chapters.update');
Route::get('/chapter/international', 'ChapterController@showIntChapter')->name('chapter.inter');
Route::get('/chapter/zapped', 'ChapterController@showZappedChapter')->name('chapter.zapped');
Route::get('/chapter/international/zap', 'ChapterController@showIntZappedChapter')->name('chapter.interzap');
Route::get('/chapter/international/zapped/view/{id}', 'ChapterController@showIntZappedChapterView');
Route::get('/chapter/unzap/{id}', 'ChapterController@unZappedChapter');
Route::get('/chapter/inquiries', 'ChapterController@showInquiriesChapter')->name('chapter.inquiries');
Route::get('/chapter/inquirieszapped', 'ChapterController@zappedInquiriesChapter')->name('chapter.inquirieszapped');
Route::get('/chapter/inquiriesview/{id}', 'ChapterController@inquiriesview')->name('chapter.inquiriesview');
Route::get('/chapter/website', 'ChapterController@showWebsiteChapter')->name('chapter.website');
Route::get('/chapter/website/edit/{id}', 'ChapterController@editWebsite');
Route::post('/chapter/website/update/{id}', 'ChapterController@updateWebsite')->name('chapter.updateweb');
Route::get('/chapter/view/{id}', 'ChapterController@showChapterView');
Route::post('/chapter/disband', 'ChapterController@chapterDisband');
Route::post('/chapter/resetpswd', 'ChapterController@chapterResetPassword');
Route::get('/chapter/financial/{id}', 'ChapterController@showFinancialReport')->name('chapter.showfinancial');
Route::post('/chapter/financial/{id}', 'ChapterController@storeFinancialReport')->name('chapter.storefinancial');
Route::get('chapter/boardinfo/{id}', 'ChapterController@showBoardInfo')->name('chapter.showboardinfo');
Route::post('chapter/boardinfo/{id}', 'ChapterController@createBoardInfo')->name('chapter.createboardinfo');
Route::get('/chapter/re-registration', 'ChapterController@showReRegistration')->name('chapter.registration');
Route::get('/chapter/re-registration/payment/{id}', 'ChapterController@showPayment')->name('chapter.payment');
Route::post('/chapter/payment/{id}', 'ChapterController@makePayment')->name('chapter.makepayment');
Route::get('/chapter/re-registration/notes/{id}', 'ChapterController@showReRegNotes')->name('chapter.reregnotes');
Route::post('/chapter/re-regnotes/{id}', 'ChapterController@makeReRegNotes')->name('chapter.makereregnotes');
Route::get('/chapter/m2mdonation/{id}', 'ChapterController@showDonation')->name('chapter.donation');
Route::post('/chapter/donation/{id}', 'ChapterController@createDonation')->name('chapter.createdonation');
Route::get('/chapter/boundaryview/{id}', 'ChapterController@boundaryview')->name('chapter.boundaryview');
Route::post('/chapter/updateboundary/{id}', 'ChapterController@updateBoundary')->name('chapter.updateboundary');
Route::get('/chapter/awardsview/{id}', 'ChapterController@awardsView')->name('chapter.awardsview');
Route::post('/chapter/updateawards/{id}', 'ChapterController@updateAwards')->name('chapter.updateawards');
Route::get('/chapter/re-registration/reminder', 'ChapterController@reminderReRegistration')->name('chapter.reminder');
Route::get('/chapter/re-registration/latereminder', 'ChapterController@lateReRegistration')->name('chapter.latereminder');
Route::get('/chapter/statusview/{id}', 'ChapterController@statusView')->name('chapter.statusview');
Route::post('/chapter/updatestatus/{id}', 'ChapterController@updateStatus')->name('chapter.updatestatus');

/**
 * Routes for Coordinator
 */
Route::get('userData', 'CoordinatorController@updateemail')->name('coordinator.upretired');
Route::get('/coordinator/dashboard/', 'CoordinatorController@showDashboard')->name('coordinator.showdashboard');
Route::post('/coordinator/dashboard/{id}', 'CoordinatorController@updateDashboard')->name('coordinator.updatedashboard');
Route::get('/coordinatorlist', 'CoordinatorController@index')->name('coordinator.list');
Route::get('/coordinator/retired', 'CoordinatorController@showRetiredCoordinator')->name('coordinator.retired');
Route::get('/coordinator/unretired/{id}', 'CoordinatorController@showUnretiredCoordinator')->name('coordinator.unretired');
Route::get('/coordinator/international', 'CoordinatorController@showIntCoordinator')->name('coordinator.inter');
Route::get('/coordinator/retiredinternational', 'CoordinatorController@showIntRetCoordinator')->name('coordinator.retinter');
Route::get('/coordinator/create', 'CoordinatorController@create')->name('coordinator.create');
Route::post('/coordinator/create', 'CoordinatorController@store')->name('coordinator.store');
Route::get('/coordinator/edit/{id}', 'CoordinatorController@edit')->name('coordinator.edit');
Route::post('/coordinator/update2/{id}', 'CoordinatorController@update2')->name('coordinator.update2');
Route::post('/coordinator/update/{id}', 'CoordinatorController@update')->name('coordinator.update');
Route::get('/coordinator/retired/view/{id}', 'CoordinatorController@showRetiredCoordinatorView');
Route::get('/coordinator/role/{id}', 'CoordinatorController@showChangeRole')->name('coordinator.role');
Route::post('/coordinator/update/role/{id}', 'CoordinatorController@updateRole')->name('coordinator.updaterole');
Route::get('/coordinator/profile/', 'CoordinatorController@showProfile')->name('coordinator.showprofile');
Route::post('/coordinator/profile/{id}', 'CoordinatorController@updateProfile')->name('coordinator.updateprofile');
Route::get('/coordinator/appreciation/{id}', 'CoordinatorController@appreciation')->name('coordinator.appreciation');
Route::post('/coordinator/updateappreciation/{id}', 'CoordinatorController@updateAppreciation')->name('coordinator.updateappreciation');
Route::get('/coordinator/birthday/{id}', 'CoordinatorController@birthday')->name('coordinator.birthday');
Route::post('/coordinator/updatebirthday/{id}', 'CoordinatorController@updateBirthday')->name('coordinator.updatebirthday');

/**
 * Routes for Exporting File
 */
//Route::get('/export/chapter', 'ExportController@exportChapter')->name('export.chapter');
Route::get('/export/chapter/{id}', 'ExportController@exportChapter')->name('export.chapter');
Route::get('/export/zapchapter', 'ExportController@exportZappedChapter')->name('export.zapchapter');
Route::get('/export/coordinator/{id}', 'ExportController@exportCoordinator')->name('export.coordinator');
Route::get('/export/retiredcoordinator', 'ExportController@exportRetiredCoordinator')->name('export.retiredcoordinator');
Route::get('/export/einstatus', 'ExportController@exportEINStatus')->name('export.einstatus');
Route::get('/export/rereg', 'ExportController@exportReReg')->name('export.rereg');
Route::get('/export/eoystatus', 'ExportController@exportEOYStatus')->name('export.eoystatus');
Route::get('/export/chaptercoordinator', 'ExportController@exportChapterCoordinator')->name('export.chaptercoordinator');
Route::get('/export/chapteraward/{id}', 'ExportController@exportChapterAwardList')->name('export.chapteraward');
Route::get('/export/appreciation', 'ExportController@exportAppreciation')->name('export.appreciation');

Route::get('/export/intchapter', 'ExportController@exportInternationalChapter')->name('export.intchapter');
Route::get('/export/intzapchapter', 'ExportController@exportInternationalZapChapter')->name('export.intzapchapter');
Route::get('/export/intcoordinator', 'ExportController@exportIntCoordinator')->name('export.intcoordinator');
Route::get('/export/intretcoordinator', 'ExportController@exportIntRetCoordinator')->name('export.intretcoordinator');
Route::get('/export/inteinstatus', 'ExportController@exportIntEINStatus')->name('export.inteinstatus');
Route::get('/export/intrereg', 'ExportController@exportIntReReg')->name('export.intrereg');
Route::get('/export/inteoystatus', 'ExportController@exportIntEOYStatus')->name('export.inteoystatus');

Route::get('/export/boardelection', 'ExportController@exportBoardElection')->name('export.boardelection');

/**
 * Routes for Board Memebers
 */
Route::post('/board/update/{id}', 'BoardController@update')->name('board.update');
Route::post('/member/update/{id}', 'BoardController@memberUpdate')->name('member.update');
Route::get('/boardinfo', 'BoardController@showBoardInfo')->name('boardinfo.showboardinfo');
Route::post('/boardinfo/{id}', 'BoardController@createBoardInfo')->name('boardinfo.createboardinfo');
Route::get('/board/financial/{id}', 'BoardController@showFinancialReport')->name('board.showfinancial');
Route::post('/board/financial/{id}', 'BoardController@storeFinancialReport')->name('board.storefinancial');
Route::get('/board/financial/print/{id}', 'BoardController@printFinancialReport')->name('board.printfinancial');
Route::get('/board/financial/print2/{id}', 'BoardController@printFinancialReport2')->name('board.printfinancial2');

/**
 * Routes for Reports
 */
Route::get('/reports/chapterstatus', 'ReportController@showChapterStatus')->name('report.chapterstatus');
Route::get('/reports/chapternew', 'ReportController@showChapterNew')->name('report.chapternew');
Route::get('/reports/chapterlarge', 'ReportController@showChapterLarge')->name('report.chapterlarge');
Route::get('/reports/chapterprobation', 'ReportController@showChapterProbation')->name('report.chapterprobation');
Route::get('/reports/chaptercoordinators', 'ReportController@showChapterCoordinators')->name('report.chaptercoordinators');
Route::get('/reports/coordinatortodo', 'ReportController@showCoordinatorToDo')->name('report.coordinatortodo');
Route::get('/reports/intcoordinatortodo', 'ReportController@showIntCoordinatorToDo')->name('report.intcoordinatortodo');
Route::get('/reports/chaptervolunteer', 'ReportController@showChapterVolunteer')->name('report.chaptervolunteer');
Route::get('/reports/reportingtree', 'ReportController@showReportingTree')->name('report.reportingtree');
Route::get('/reports/appreciation', 'ReportController@showAppreciation')->name('report.appreciation');
Route::get('/reports/birthday', 'ReportController@showBirthday')->name('report.birthday');
Route::get('/reports/intm2mdonation', 'ReportController@intM2Mdonation')->name('report.intm2mdonation');
Route::get('/reports/m2mdonation', 'ReportController@showM2Mdonation')->name('report.m2mdonation');
Route::get('/reports/inteinstatus', 'ReportController@intEINstatus')->name('report.inteinstatus');
Route::get('/reports/einstatus', 'ReportController@showEINstatus')->name('report.einstatus');
Route::get('/reports/boardlist', 'ReportController@showBoardlist')->name('report.boardlist');
Route::get('/reports/socialmedia', 'ReportController@showSocialMedia')->name('report.socialmedia');
Route::get('/yearreports/review', 'ReportController@showReportToReview')->name('report.review');
Route::get('/yearreports/boardinfo', 'ReportController@showReportToBoardInfo')->name('report.boardinfo');
Route::get('/yearreports/boardnotification', 'ReportController@boardNotification')->name('report.boardnotification');
Route::get('/yearreports/chapteraward', 'ReportController@showReportToBoardInfo')->name('report.awards');
Route::get('/yearreports/boundaryissue', 'ReportController@showReportToIssues')->name('report.issues');
Route::get('/yearreports/chapterawards', 'ReportController@showChapterAwards')->name('report.awards');
//Route::get('/resources','ReportController@resources')->name('resources');
Route::get('/yearreports/eoystatus', 'ReportController@showEOYStatus')->name('report.eoystatus');
Route::get('/reports/downloads', 'ReportController@showDownloads')->name('report.downloads');
Route::get('/adminreports/duplicateuser', 'ReportController@showDuplicate')->name('report.duplicateuser');
Route::get('/adminreports/duplicateboardid', 'ReportController@showDuplicateId')->name('report.duplicateboardid');
Route::get('/adminreports/multipleboard', 'ReportController@showMultiple')->name('report.multipleboard');
Route::get('/adminreports/nopresident', 'ReportController@showNoPresident')->name('report.nopresident');
Route::get('/yearreports/addawards', 'ReportController@addAwards')->name('report.addawards');

/**
 * Routes for PDF
 */
Route::get('/myPDF', 'PDFController@generatePDF')->name('myPDF');
