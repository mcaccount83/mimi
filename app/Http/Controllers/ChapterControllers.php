<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use DB;
use App\Chapter;
use App\Coordinator;
use App\User;
use App\FinancialReport;
use Mail;
use Session;

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->middleware('preventBackHistory'); 
        $this->middleware('auth')->except('logout','chapterLinks');
    }

    /**
     * Display the Active chapter list mapped with login coordinator
     */
    public function index()
    {
        //Get Coordinator Details
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
                            ->select('chapters.*','cd.first_name as cor_f_name','cd.last_name as cor_l_name','bd.first_name as bor_f_name','bd.last_name as bor_l_name','bd.email as bor_email','bd.phone as phone','st.state_short_name as state')
                            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                            ->where('chapters.is_active', '=', '1')
                            ->where('bd.board_position_id', '=', '1')
                            ->where('chapters.primary_coordinator_id', $corId)
                            ->orderBy('st.state_short_name','ASC')
                            ->orderBy('chapters.name','ASC')
                            ->get();
        if($_GET['check'] == 'yes')                         
            $checkBoxStatus = "checked";
        else
            $checkBoxStatus = "";

        $countList=count($chapterList);
        $data=array('countList'=>$countList,'chapterList' => $chapterList,'checkBoxStatus'=>$checkBoxStatus,'corId'=>$corId);
        return view('chapters.index')->with($data);
    }


    /**
     * Add New chapter list (View)
     */
     public function create(){
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        
        $stateArr = DB::table('state')
                    ->select('state.*')
                    ->orderBy('id','ASC')
                    ->get();
        $countryArr = DB::table('country')
                    ->select('country.*')
                    ->orderBy('id','ASC')
                    ->get();    
        $regionList = DB::table('region')
                    ->select('id','long_name')
                    ->where('conference_id', '=', $corConfId)
                    ->orderBy('long_name','ASC')
                    ->get();                 
        $primaryCoordinatorList = DB::table('coordinator_details as cd')
                                    ->select('cd.coordinator_id as cid','cd.first_name as cor_f_name','cd.last_name as cor_l_name','cp.short_title as pos')
                                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                                    ->where('cd.conference_id', '=', $corConfId)
                                    ->where('cd.position_id', '<=', '6')
                                    ->where('cd.position_id', '>=', '1')
                                    ->where('cd.is_active', '=', '1')
                                    ->orderBy('cd.first_name','ASC')
                                    ->get();    
        
        $foundedMonth = ['1'=>'JAN','2'=>'FEB','3'=>'MAR','4'=>'APR','5'=>'MAY','6'=>'JUN','7'=>'JUL','8'=>'AUG','9'=>'SEP','10'=>'OCT','11'=>'NOV','12'=>'DEC'];                   
        $currentMonth = date('m');
        $firstCharacter = $currentMonth[0];
        if($firstCharacter == '0')
            $currentMonth = $currentMonth[1];

        $currentYear = date('Y');
        $data=array('currentMonth'=>$currentMonth,'currentYear'=>$currentYear,'regionList'=>$regionList,'primaryCoordinatorList'=>$primaryCoordinatorList,'stateArr'=>$stateArr,'countryArr'=>$countryArr,'foundedMonth'=>$foundedMonth);
        
        return view('chapters.create')->with($data);
    }
    /**
     * Add New chapter list (Store)
     */
    public function store(Request $request)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $input = request()->all(); 
  
        if(isset($input['ch_linkstatus']))
            $input['ch_linkstatus'];
        else    
            $input['ch_linkstatus'] = 0;

        DB::beginTransaction();
        try{
                $chapterId = DB::table('chapters')->insertGetId(
                ['conference' => $corConfId,
                    'name' => $input['ch_name'],
                    'state' => $input['ch_state'],
                    'country' => $input['ch_country'],
                    'region' => $input['ch_region'],
                    'ein' => $input['ch_ein'],
                    'status' => $input['ch_status'],
                    'territory' => $input['ch_boundariesterry'],
                    'additional_info' => $input['ch_addinfo'],
                    'website_link_status' => $input['ch_linkstatus'],
                    'email' => $input['ch_email'],
                    'inquiries_contact' => $input['ch_inqemailcontact'],
                    'inquiries_note' => $input['ch_inqnote'],
                    'egroup' => $input['ch_onlinediss'],
                    'po_box' => $input['ch_pobox'],
                    'notes' => $input['ch_notes'],
                    'start_month_id' => $input['ch_founddate'],
                    'start_year' => $input['ch_foundyear'],
                    'next_renewal_year' => $input['ch_foundyear']+1,
                    'primary_coordinator_id' => $input['ch_primarycor'],
                    'founders_name' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
            ); 
        
            //President Info
            if(isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email']))
            {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1]
                );
            
                $boardIdArr = DB::table('board_details')
                                    ->select('board_details.board_id')
                                    ->orderBy('board_details.board_id','DESC')
                                    ->limit(1)
                                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;
                       
                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                    'board_id' => $boardId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'remember_token' => '',
                    'board_position_id' => 1,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country' => 'USA',
                    'phone' => $input['ch_pre_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
                );
            }
        
            //AVP Info
            if(isset($input['ch_avp_fname']) && isset($input['ch_avp_lname']) && isset($input['ch_avp_email']))
            {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_avp_fname'],
                    'last_name' => $input['ch_avp_lname'],
                    'email' => $input['required|ch_avp_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                                    ->select('board_details.board_id')
                                    ->orderBy('board_details.board_id','DESC')
                                    ->limit(1)
                                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;
                        
                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                    'board_id' => $boardId,
                    'first_name' => $input['ch_avp_fname'],
                    'last_name' => $input['ch_avp_lname'],
                    'email' => $input['required|ch_avp_email'],
                    'password' => Hash::make('TempPass4You'),
                    'remember_token' => '',
                    'board_position_id' => 2,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_avp_street'],
                    'city' => $input['ch_avp_city'],
                    'state' => $input['ch_avp_state'],
                    'zip' => $input['ch_avp_zip'],
                    'country' => 'USA',
                    'phone' => $input['ch_avp_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
                );
            }
            //MVP Info
            if(isset($input['ch_mvp_fname']) && isset($input['ch_mvp_lname']) && isset($input['ch_mvp_email']))
            {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_mvp_fname'],
                    'last_name' => $input['ch_mvp_lname'],
                    'email' => $input['ch_mvp_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                                    ->select('board_details.board_id')
                                    ->orderBy('board_details.board_id','DESC')
                                    ->limit(1)
                                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;
                        
                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                    'board_id' => $boardId,
                    'first_name' => $input['ch_mvp_fname'],
                    'last_name' => $input['ch_mvp_lname'],
                    'email' => $input['ch_mvp_email'],
                    'password' => Hash::make('TempPass4You'),
                    'remember_token' => '',
                    'board_position_id' => 3,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_mvp_street'],
                    'city' => $input['ch_mvp_city'],
                    'state' => $input['ch_mvp_state'],
                    'zip' => $input['ch_mvp_zip'],
                    'country' => 'USA',
                    'phone' => $input['ch_mvp_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
                );
            } 
            //TREASURER Info
            if(isset($input['ch_trs_fname']) && isset($input['ch_trs_lname']) && isset($input['ch_trs_email']))
            {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_trs_fname'],
                    'last_name' => $input['ch_trs_lname'],
                    'email' => $input['ch_trs_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                                    ->select('board_details.board_id')
                                    ->orderBy('board_details.board_id','DESC')
                                    ->limit(1)
                                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;
                        
                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                    'board_id' => $boardId,
                    'first_name' => $input['ch_trs_fname'],
                    'last_name' => $input['ch_trs_lname'],
                    'email' => $input['ch_trs_email'],
                    'password' => Hash::make('TempPass4You'),
                    'remember_token' => '',
                    'board_position_id' => 4,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_trs_street'],
                    'city' => $input['ch_trs_city'],
                    'state' => $input['ch_trs_state'],
                    'zip' => $input['ch_trs_zip'],
                    'country' => 'USA',
                    'phone' => $input['ch_trs_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
                );
            }  
            //Secretary Info
            if(isset($input['ch_sec_fname']) && isset($input['ch_sec_lname']) && isset($input['ch_sec_email']))
            {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_sec_fname'],
                    'last_name' => $input['ch_sec_lname'],
                    'email' => $input['ch_sec_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                                    ->select('board_details.board_id')
                                    ->orderBy('board_details.board_id','DESC')
                                    ->limit(1)
                                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;
                        
                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                    'board_id' => $boardId,
                    'first_name' => $input['ch_sec_fname'],
                    'last_name' => $input['ch_sec_lname'],
                    'email' => $input['ch_sec_email'],
                    'password' => Hash::make('TempPass4You'),
                    'remember_token' => '',
                    'board_position_id' => 5,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_sec_street'],
                    'city' => $input['ch_sec_city'],
                    'state' => $input['ch_sec_state'],
                    'zip' => $input['ch_sec_zip'],
                    'country' => 'USA',
                    'phone' => $input['ch_sec_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
                );
            }
            $cordInfo = DB::table('coordinator_details')
                            ->select('first_name','last_name','email')
                            ->where('is_active', '=', '1')
                            ->where('coordinator_id', $input['ch_primarycor'])
                            ->get();
            $state = DB::table('state')
                            ->select('state_short_name')
                            ->where('id', $input['ch_state'])
                            ->get();
            $to_email = [
                $cordInfo[0]->email,
                $input['ch_pre_email'],
            ];
             //$to_email = "rishi.dwivedi@otssolutions.com";

            $mailData = [
                    'chapter_name'=> $input['ch_name'],
                    'chapter_state' => $state[0]->state_short_name,
                    'cor_fname' => $cordInfo[0]->first_name,
                    'cor_lname' => $cordInfo[0]->last_name,
                    'updated_by' => date('Y-m-d H:i:s'),
            ];
            
            Mail::send('emails.chapteradd', $mailData,function($message) use ($to_email)
            {   
                $message->to($to_email, 'MOMS Club')->subject('New Chapter Added'); 
            });
            DB::commit();
        }
        catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            
            return redirect('/home')->with('fail', 'Something went wrong, Please try again..');
        }      
        return redirect('/home')->with('success', 'Chapter created successfully');
    }

    public function edit($id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corConfId = $corDetails['conference_id'];
        $corId = $corDetails['coordinator_id'];
        $positionid = $corDetails['position_id'];
        $financial_report_array = FinancialReport::find($id);
        if($financial_report_array){
        $reviewComplete = $financial_report_array['review_complete'];
            }
           else{
            $reviewComplete= NULL;
           }     
        $chapterList = DB::table('chapters as ch')
                            ->select('ch.*','bd.first_name','bd.last_name','bd.email as bd_email','bd.board_position_id','bd.street_address','bd.city','bd.zip','bd.phone','bd.state as bd_state','bd.user_id as user_id')
                            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
                            ->where('ch.is_active', '=', '1')
                            ->where('ch.id', '=', $id )
                            ->where('bd.board_position_id', '=', '1')
                            ->get();
        $corConfId = $chapterList[0]->conference;
        $corId = $chapterList[0]->primary_coordinator_id;
        $AVPDetails = DB::table('board_details as bd')
                            ->select('bd.first_name as avp_fname','bd.last_name as avp_lname','bd.email as avp_email','bd.board_position_id','bd.street_address as avp_addr','bd.city as avp_city','bd.zip as avp_zip','bd.phone as avp_phone','bd.state as avp_state','bd.user_id as user_id')
                            ->where('bd.chapter_id', '=', $id )
                            ->where('bd.board_position_id', '=', '2')
                            ->get();
        if(sizeof($AVPDetails) == 0){
                $AVPDetails[0] = array('avp_fname' =>'','avp_lname' =>'','avp_email' =>'','avp_addr' =>'','avp_city' =>'','avp_zip' =>'','avp_phone' =>'','avp_state' =>'','user_id' =>''); 
                $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('board_details as bd')
                            ->select('bd.first_name as mvp_fname','bd.last_name as mvp_lname','bd.email as mvp_email','bd.board_position_id','bd.street_address as mvp_addr','bd.city as mvp_city','bd.zip as mvp_zip','bd.phone as mvp_phone','bd.state as mvp_state','bd.user_id as user_id')
                            ->where('bd.chapter_id', '=', $id )
                            ->where('bd.board_position_id', '=', '3')
                            ->get();
        if(sizeof($MVPDetails) == 0){
            $MVPDetails[0] = array('mvp_fname' =>'','mvp_lname' =>'','mvp_email' =>'','mvp_addr' =>'','mvp_city' =>'','mvp_zip' =>'','mvp_phone' =>'','mvp_state' =>'','user_id' =>''); 
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('board_details as bd')
                            ->select('bd.first_name as trs_fname','bd.last_name as trs_lname','bd.email as trs_email','bd.board_position_id','bd.street_address as trs_addr','bd.city as trs_city','bd.zip as trs_zip','bd.phone as trs_phone','bd.state as trs_state','bd.user_id as user_id')
                            ->where('bd.chapter_id', '=', $id )
                            ->where('bd.board_position_id', '=', '4')
                            ->get();
        if(sizeof($TRSDetails) == 0){
            $TRSDetails[0] = array('trs_fname' =>'','trs_lname' =>'','trs_email' =>'','trs_addr' =>'','trs_city' =>'','trs_zip' =>'','trs_phone' =>'','trs_state' =>'','user_id' =>''); 
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }
        
        $SECDetails = DB::table('board_details as bd')
                            ->select('bd.first_name as sec_fname','bd.last_name as sec_lname','bd.email as sec_email','bd.board_position_id','bd.street_address as sec_addr','bd.city as sec_city','bd.zip as sec_zip','bd.phone as sec_phone','bd.state as sec_state','bd.user_id as user_id')
                            ->where('bd.chapter_id', '=', $id )
                            ->where('bd.board_position_id', '=', '5')
                            ->get();
        if(sizeof($SECDetails) == 0){
            $SECDetails[0] = array('sec_fname' =>'','sec_lname' =>'','sec_email' =>'','sec_addr' =>'','sec_city' =>'','sec_zip' =>'','sec_phone' =>'','sec_state' =>'','user_id' =>''); 
            $SECDetails = json_decode(json_encode($SECDetails));
        }

        $stateArr = DB::table('state')
                    ->select('state.*')
                    ->orderBy('id','ASC')
                    ->get();
        $countryArr = DB::table('country')
                    ->select('country.*')
                    ->orderBy('id','ASC')
                    ->get();    
        $regionList = DB::table('region')
                    ->select('id','long_name')
                    ->where('conference_id', '=', $corConfId)
                    ->orderBy('long_name','ASC')
                    ->get();
        

        $chapterEmailList = DB::table('board_details as bd')
                    ->select('bd.email as bor_email')
                    ->where('bd.chapter_id', '=', $id)
                    ->get();  
        $emailListCord="";
        foreach($chapterEmailList as $val){
            $email = $val->bor_email;
            $escaped_email=str_replace("'", "\\'", $email);
            if ($emailListCord==""){
                $emailListCord = $escaped_email;
            }
            else{
                $emailListCord .= ";" . $escaped_email;
            } 
        } 
        
        $cc_string="";
        $reportingList = DB::table('coordinator_reporting_tree')
                            ->select('*')
                            ->where('id', '=', $chapterList[0]->primary_coordinator_id)
                            ->get();
            foreach($reportingList as $key => $value)
            {
                $reportingList[$key] = (array) $value;
            }   
            $filterReportingList = array_filter($reportingList[0]);
            unset($filterReportingList['id']);
            unset($filterReportingList['layer0']);
            $filterReportingList = array_reverse($filterReportingList);
            $str = "";
            $array_rows=count($filterReportingList);
            $down_line_email="";
            foreach($filterReportingList as $key =>$val){
                // if($corId != $val && $val >1){
                if($val >1){
                    $corList = DB::table('coordinator_details as cd')
                                    ->select('cd.email as cord_email')
                                    ->where('cd.coordinator_id', '=', $val)
                                    ->where('cd.is_active', '=', 1)
                                    ->get();
                    if(sizeof($corList) > 0){
                        if ($down_line_email=="")
                            $down_line_email = $corList[0]->cord_email;
                        else
                            $down_line_email .= ";" . $corList[0]->cord_email;
                    }            
                }
            }
        $cc_string = "?cc=" . $down_line_email;

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
                    ->select('cd.coordinator_id as cid','cd.first_name as cor_f_name','cd.last_name as cor_l_name','cp.short_title as pos')
                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                    ->where('cd.conference_id', '=', $corConfId)
                    ->where('cd.position_id', '<=', '6')
                    ->where('cd.position_id', '>=', '1')
                    ->where('cd.is_active', '=', '1')
                    ->orderBy('cd.first_name','ASC')
                    ->get();                
       
        $foundedMonth = ['1'=>'JAN','2'=>'FEB','3'=>'MAR','4'=>'APR','5'=>'MAY','6'=>'JUN','7'=>'JUL','8'=>'AUG','9'=>'SEP','10'=>'OCT','11'=>'NOV','12'=>'DEC'];                   
        $currentMonth = $chapterList[0]->start_month_id;
        
        $data=array('positionid' => $positionid,'corId'=>$corId,'reviewComplete'=>$reviewComplete,'emailListCord'=>$emailListCord,'cc_string'=>$cc_string,'currentMonth'=>$currentMonth,'SECDetails'=>$SECDetails,'TRSDetails'=>$TRSDetails,'MVPDetails'=>$MVPDetails,'AVPDetails'=>$AVPDetails,'chapterList' => $chapterList,'regionList'=>$regionList,'primaryCoordinatorList'=>$primaryCoordinatorList,'stateArr'=>$stateArr,'countryArr'=>$countryArr,'foundedMonth'=>$foundedMonth);
        return view('chapters.edit')->with($data);
    }
    
//chapterupdate
    public function update(Request $request, $id)
    {

        $presInfoPre = DB::table('chapters')
                        ->select('chapters.*','cd.first_name as cor_f_name','cd.last_name as cor_l_name','cd.email as cor_email','bd.first_name as bor_f_name','bd.last_name as bor_l_name','bd.email as bor_email','bd.phone as phone','bd.street_address as street','bd.city as city','bd.zip as zip','st.state_short_name as state')
                        ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                        ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                        ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                        ->where('chapters.is_Active', '=', '1')
                        ->where('bd.board_position_id', '=', '1')
                        ->where('chapters.id', $id)
                        ->orderBy('chapters.id','DESC')
                        ->get();

        $AVPInfoPre = DB::table('chapters')
                            ->select('bd.first_name as bor_f_name','bd.last_name as bor_l_name','bd.email as bor_email')
                            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                            ->where('chapters.is_Active', '=', '1')
                            ->where('bd.board_position_id', '=', '2')
                            ->where('chapters.id', $id)
                            ->get();

        $MVPInfoPre = DB::table('chapters')
                            ->select('bd.first_name as bor_f_name','bd.last_name as bor_l_name','bd.email as bor_email')
                            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                            ->where('chapters.is_Active', '=', '1')
                            ->where('bd.board_position_id', '=', '3')
                            ->where('chapters.id', $id)
                            ->get();

        $tresInfoPre = DB::table('chapters')
                            ->select('bd.first_name as bor_f_name','bd.last_name as bor_l_name','bd.email as bor_email')
                            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                            ->where('chapters.is_Active', '=', '1')
                            ->where('bd.board_position_id', '=', '4')
                            ->where('chapters.id', $id)
                            ->get();

        $secInfoPre = DB::table('chapters')
                            ->select('bd.first_name as bor_f_name','bd.last_name as bor_l_name','bd.email as bor_email')
                            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                            ->where('chapters.is_Active', '=', '1')
                            ->where('bd.board_position_id', '=', '5')
                            ->where('chapters.id', $id)
                            ->get();

        $chapterId = $id;
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $positionid = $corDetails['position_id'];
        if($positionid < 5){
            $ch_state = $request->get('ch_hid_state');
            $ch_country = $request->get('ch_hid_country');
            $ch_region = $request->get('ch_hid_region');
            $ch_status = $request->get('ch_hid_status');
            $ch_pcid = $request->get('ch_hid_primarycor');
        }
        else{
            $ch_state = $request->get('ch_state');
            $ch_country = $request->get('ch_country');
            $ch_region = $request->get('ch_region');
            $ch_status = $request->get('ch_status');
            $ch_pcid = $request->get('ch_primarycor');
        }
        if($positionid == 7){
            $ch_month = $request->get('ch_founddate');
            $ch_foundyear = $request->get('ch_foundyear');
        }
        else{
            $ch_month = $request->get('ch_hid_founddate');
            $ch_foundyear = $request->get('ch_hid_foundyear');
        }
      
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $chapter = Chapter::find($chapterId);
        DB::beginTransaction();
        try{
            $chapter->name = $request->get('ch_name');
            $chapter->state = $ch_state;
            $chapter->country = $ch_country;
            $chapter->region = $ch_region;
            $chapter->ein = $request->get('ch_ein');
            $chapter->status = $ch_status;
            $chapter->territory = $request->get('ch_boundariesterry');
            $chapter->additional_info = $request->get('ch_addinfo');
            $chapter->website_url = $request->get('ch_website');
            $chapter->website_link_status = $request->get('ch_linkstatus');
            $chapter->email = $request->get('ch_email');                       
            $chapter->inquiries_contact = $request->get('ch_inqemailcontact');
            $chapter->inquiries_note = $request->get('ch_inqnote');
            $chapter->egroup = $request->get('ch_onlinediss');
            $chapter->po_box = $request->get('ch_pobox');
            $chapter->notes = $request->get('ch_notes');
            $chapter->po_box = $request->get('ch_pobox');
            $chapter->former_name = $request->get('ch_preknown');
            $chapter->sistered_by = $request->get('ch_sistered');
            $chapter->start_month_id = $ch_month;
            $chapter->start_year = $ch_foundyear;        
           // $chapter->next_renewal_year = $request->get('ch_foundyear')+1;
            $chapter->primary_coordinator_id = $ch_pcid; 
            //$chapter->founders_name = $request->get('ch_pre_fname').' '.$request->get('ch_pre_lname');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');                
        
            $chapter->save();

            $financial_report_array = FinancialReport::find($chapterId);
            if(!empty($financial_report_array))
            {
                DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [null,$chapterId]);
            } 

            //President Info
            if($request->get('ch_pre_fname') !='' && $request->get('ch_pre_lname') !='' && $request->get('ch_pre_email') !='')
            {
                $PREDetails = DB::table('board_details')
                                ->select('board_id','user_id')
                                ->where('chapter_id', '=', $chapterId)
                                ->where('board_position_id', '=', '1')
                                ->get();
                if(sizeof($PREDetails) != 0){
                    $userId = $PREDetails[0]->user_id;
                    $boardId = $PREDetails[0]->board_id;

                    $user = User::find($userId);
                    $user->first_name = $request->get('ch_pre_fname');
                    $user->last_name = $request->get('ch_pre_lname');
                    $user->email = $request->get('ch_pre_email');            
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();
                   
                    DB::table('board_details')
                            ->where('board_id', $boardId)
                            ->update(['first_name' => $request->get('ch_pre_fname'),
                                        'last_name' => $request->get('ch_pre_lname'),
                                        'email' => $request->get('ch_pre_email'),
                                        'street_address' => $request->get('ch_pre_street'),
                                        'city' => $request->get('ch_pre_city'),
                                        'state' => $request->get('ch_pre_state'),
                                        'zip' => $request->get('ch_pre_zip'),
                                        'country' => 'USA',
                                        'phone' => $request->get('ch_pre_phone'),
                                        'last_updated_by' => $lastUpdatedBy,
                                        'last_updated_date' => date('Y-m-d H:i:s')]);
                }      
            }
            //AVP Info
            // if($request->get('ch_avp_fname') !='' && $request->get('ch_avp_lname') !='' && $request->get('ch_avp_email') !='')
           // {
                $AVPDetails = DB::table('board_details')
                                ->select('board_id','user_id')
                                ->where('chapter_id', '=', $chapterId )
                                ->where('board_position_id', '=', '2')
                                ->get();
                if(sizeof($AVPDetails) != 0){
                    $userId = $AVPDetails[0]->user_id;
                    $boardId = $AVPDetails[0]->board_id;
                    if($request->get('AVPVacant') == 'on'){
                        //Delete Details of Board memebers
                        DB::table('board_details')
                                ->where('board_id',$boardId)
                                ->delete();
                        //Delete Details of Board memebers from users table
                        DB::table('users')
                            ->where('id',$userId)
                            ->delete();        
                    } 
                    else{
                        $user = User::find($userId);
                        $user->first_name = $request->get('ch_avp_fname');
                        $user->last_name = $request->get('ch_avp_lname');
                        $user->email = $request->get('ch_avp_email');            
                        $user->updated_at = date('Y-m-d H:i:s');
                        $user->save();

                        DB::table('board_details')
                                ->where('board_id', $boardId)
                                ->update(['first_name' => $request->get('ch_avp_fname'),
                                            'last_name' => $request->get('ch_avp_lname'),
                                            'email' => $request->get('ch_avp_email'),
                                            'street_address' => $request->get('ch_avp_street'),
                                            'city' => $request->get('ch_avp_city'),
                                            'state' => $request->get('ch_avp_state'),
                                            'zip' => $request->get('ch_avp_zip'),
                                            'country' => 'USA',
                                            'phone' => $request->get('ch_avp_phone'),
                                            'last_updated_by' => $lastUpdatedBy,
                                            'last_updated_date' => date('Y-m-d H:i:s')]);
                    }                        
                }
                else{
                    if($request->get('AVPVacant') != 'on'){
                        $userId = DB::table('users')->insertGetId(
                            ['first_name' => $request->get('ch_avp_fname'),
                            'last_name' => $request->get('ch_avp_lname'),
                            'email' => $request->get('ch_avp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                        );
        
                        $boardIdArr = DB::table('board_details')
                                            ->select('board_details.board_id')
                                            ->orderBy('board_details.board_id','DESC')
                                            ->limit(1)
                                            ->get();
                        $boardId = $boardIdArr[0]->board_id + 1;
                            
                        $board = DB::table('board_details')->insert(
                            ['user_id' => $userId,
                            'board_id' => $boardId,
                            'first_name' => $request->get('ch_avp_fname'),
                            'last_name' => $request->get('ch_avp_lname'),
                            'email' => $request->get('ch_avp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'remember_token' => '',
                            'board_position_id' => 2,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->get('ch_avp_street'),
                            'city' => $request->get('ch_avp_city'),
                            'state' => $request->get('ch_avp_state'),
                            'zip' => $request->get('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                        );
                    }    
                }      
           // }
            //MVP Info
            //if($request->get('ch_mvp_fname') !='' && $request->get('ch_mvp_lname') !='' && $request->get('ch_mvp_email') !='')
            //{
                $MVPDetails = DB::table('board_details')
                                ->select('board_id','user_id')
                                ->where('chapter_id', '=', $chapterId )
                                ->where('board_position_id', '=', '3')
                                ->get();
                if(sizeof($MVPDetails) != 0){
                    $userId = $MVPDetails[0]->user_id;
                    $boardId = $MVPDetails[0]->board_id;
                    i