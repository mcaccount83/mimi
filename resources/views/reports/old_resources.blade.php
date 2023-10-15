@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Conference 2 Resources
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Conference 2 Resources</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Conference 2 Resources</h3>
             
            </div>
            <!-- /.box-header -->
            
            <div class="box-body table-responsive">
              <fieldset>
		
		<?php

			echo "<hr>";
			echo "<center><h4> Coordinator Files </h4></center>";
		
			echo "<p align=\"center\">";
			echo "<iframe src='https://onedrive.live.com/embed?cid=06BAA7C06619EDDC&resid=6BAA7C06619EDDC%216039&authkey=AEgPYfAwBp6g5QY&em=2&wdAr=1.7777777777777777' width='610px' height='367px' frameborder='0'>This is an embedded <a target='_blank' href='https://office.com'>Microsoft Office</a> presentation, powered by <a target='_blank' href='https://office.com/webapps'>Office Online</a>.</iframe>";		
			echo "</p>";   
			
			echo "<p align=\"center\">";
			echo "<a href='https://docs.google.com/document/d/1OpHdLssVqqJ96vo3fgcAK5dPDdeC0thKIU4cm6T4Z7Q/export?format=pdf' class=\"link-4\">Forwarding Asana Emails from a Gmail Account</a>";
			echo "</p>";        

			echo "<p align=\"center\">";
			echo "<a href='https://docs.google.com/document/d/1vKqvyYHhI9ceXoEumwAmEkS4RwylDp4x6zCA1ScUUFA/export?format=pdf' class=\"link-4\">International Contacts and Mailing Information</a>";
			echo "</p>";        

			echo "<p align=\"center\">";
			echo "<a href='https://drive.google.com/uc?export=download&id=0ByhUugQp-NXca2pRYnNhbU9abUE' class=\"link-4\">Reimbursement Request</a>";
			echo "</p>";        
			
			echo "<p align=\"center\">";
			echo "<a href='https://drive.google.com/uc?export=download&id=0ByhUugQp-NXcRWdJNTBjTW9QMEU' class=\"link-4\">US Conference Map</a>";
			echo "</p>";        
			
			///////////////////////////////////////////
			echo "<hr>";
			echo "<center><h4> Chapter Files </h4></center>";
		
			echo "<p align=\"center\">";
			echo "<a href='https://docs.google.com/document/d/1wn3YzBCSy_ueZl1lM4yMtY-krkEA-UsnmkqBIOsrYQU/export?format=doc' class=\"link-4\">Membership/Liability Form</a>";
			echo "</p>";        

			echo "<p align=\"center\">";
			echo "<a href='http://momsclub.org/chapterinfo/roster_template.xlsx' class=\"link-4\" target=\"_blank\">Excel Roster</a>";
			echo "</p>";     

			echo "<p align=\"center\">";
			echo "<a href='https://drive.google.com/uc?export=download&id=0ByhUugQp-NXcV01FZ09KeU14ODA' class=\"link-4\">Sample Accounting</a>";
			echo "</p>";        
			
			echo "<p align=\"center\">";
			echo "<a href='https://drive.google.com/uc?export=download&id=0ByhUugQp-NXcRjk5NGUzMnA2TDg' class=\"link-4\">Sample Budget</a>";
			echo "</p>";        
			   
			///////////////////////////////////////////
			echo "<hr>";
			echo "<center><h4> Annual Report Instructions </h4></center>";
		
			echo "<p align=\"center\">";
			echo "<a href='https://drive.google.com/uc?export=download&id=0ByhUugQp-NXcWTV5OTNFa3BGd2s' class=\"link-4\">Annual Report Flowchart</a>";
			echo "</p>";        

			echo "<p align=\"center\">";
			echo "<a href='https://docs.google.com/document/d/1l0jDVdla2hXO0FKFi7dbrw6MbenLk1MsybzbV-rIFzY/export?format=pdf' class=\"link-4\">Annual Report Instructions</a>";
			echo "</p>";     


            if ((Session::get('positionid')>=4 && Session::get('positionid')<=7)||(Session::get('positionid')==11 || Session::get('secpositionid')==11)){
				echo "<hr>";
					echo "<center><h4> New Chapter Files </h4></center>";
		
				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1aQ1LuJccPXVzxoDFsi5D4Rob-RZPG-cM5DLKfm_edjw/export?format=doc' class=\"link-4\">Authorization Letter</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://drive.google.com/uc?export=download&id=0ByhUugQp-NXceEtMZWhGQzNVc2M' class=\"link-4\">Group Exemption Letter</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://drive.google.com/uc?export=download&id=0ByhUugQp-NXcU2tQZV9zUlQ5d00' class=\"link-4\">EIN Instructions</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://drive.google.com/uc?export=download&id=0ByhUugQp-NXcNHA5MUJJamRabVU' class=\"link-4\">EIN Application</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1WKBkCK80338J0yLPMztZpkkvk1OeFSecT0Tba9G3og0/export?format=doc' class=\"link-4\">Letterhead</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1IYMye_XsfoLnEdTXI8vLM3SfAWlgfcYsV1BIEzGrOj0/export?format=doc' class=\"link-4\">Formerly Known As Authorization</a>";
				 echo "</p>";        

            }

            if (Session::get('positionid')>=5 && Session::get('positionid')<=7){
				echo "<hr>";
					echo "<center><h4> Probation Letters </h4></center>";
		
				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1wZt9YdXPODSNqc1AcSlj9j4LDxVxZExVs4e5jav_nWk/export?format=doc' class=\"link-4\">Probation No Annual Report</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1nevexvgpyZHfki7FRFyDFn9yTB2kZVjhSOe4diJioJk/export?format=doc' class=\"link-4\">Probation Fundraising</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1nCvIi9sC0L8PpPjxclupP-4Kq0oN7O0LeqRYsbFE5Fo/export?format=doc' class=\"link-4\">Probation Excess Party Expense</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1SWdkwo-SdW-E7dzkcmYs-NPsH2cat9ZX2lXP1_yTmkM/export?format=doc' class=\"link-4\">Warning Excess Party Expense</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1LXlHvAWFsQ_VHL1pcvbOkVyzkQcYNShOHpLDjGF6FY0/export?format=doc' class=\"link-4\">Probation Release</a>";
				 echo "</p>";        

            }

            if (Session::get('positionid')>=5 && Session::get('positionid')<=7){
				echo "<hr>";
					echo "<center><h4> Disbanding Files </h4></center>";
		
				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1jfYTnI_qNB76Vf7rNHdfzhGu6PWMNp9AkXyjt_GcKyw/export?format=doc' class=\"link-4\">It Only Takes ONE</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1ayeKaICFINQT1qUB4Ot9hKrZ07-KQTdRutz1xoB0e3Q/export?format=doc' class=\"link-4\">Vote to Disband</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1f5888MqUzJqQ6aeWAlKOTMnJ5g0rOaByxwFj5kC9JbE/export?format=doc' class=\"link-4\">Disband Chapter Letter</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1oonXnAp3d-pCn6syoaTigGyNxkh7VQbBviyLu4Ik5bs/export?format=doc' class=\"link-4\">Disband Chapter No Annual Report</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1xE3O0QzR5GIj1vPwWh9lqL7Wb4vubtQ_RNIsw6tgeYI/export?format=doc' class=\"link-4\">Disband Chapter No Communication</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/16WsAqXIh0qmXccwLz9LyFhAbrFNwaa8cXhdaITBgQtA/export?format=doc' class=\"link-4\">Disband Chapter Non-Payment</a>";
				 echo "</p>";        

				 echo "<p align=\"center\">";
				 echo "<a href='https://docs.google.com/document/d/1SUCIakYr7oD3zMzwbrmE8sSm-CZ2CkqH05aDkGF2amE/export?format=doc' class=\"link-4\">Disband Chapter Not Started</a>";
				 echo "</p>";        

            }
        ?> 

		 <hr>
         
		   

        </fieldset>
            </div>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>    
    <!-- Main content -->
    
    <!-- /.content -->
 
@endsection
