@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Inquiries Chapter List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Inquiries Chapter List</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Inquiries Chapter</h3>
              </div>
            <!-- /.box-header -->
            
            <div class="box-body table-responsive">
              <table id="chapterlist_inquiry" class="table table-bordered table-hover">
              <thead> 
			    <tr>
					<th>View Details</th>
			        <th>COPY Inquiries Email</th>
					<th>YES Chapter Response</th>
					<th>Status</th>
                    <th>State</th>
                    <th>Chapter Name</th>
                    <th>Boundaries</th>
                    <th>Inquiries Notes</th>
                    <th>Inquiries Email</th>

					<th style="display:none"></th> 
                </tr>
                </thead>
                <tbody>
				<?php $row = 0;?>
                @foreach($inquiriesList as $list)
                  <tr>
						<td><center><a href="<?php echo url("/chapter/inquiriesview/{$list->id}") ?>"><i class="fa fa-eye" aria-hidden="true"></i></a></center></td>
                        <td><center><button type="button" class="btn btn-xs" onclick="return CopyEmail(<?php echo $row?>);"><i class="fa fa-copy" aria-hidden="true"></i></button></center></td>
						
						<td><center><button type="button" class="btn btn-xs" onclick="return CopyInquiryResp(<?php echo $row?>);"><i class="fa fa-copy" aria-hidden="true"></i></button></center></td>
						<!--<td><a href="mailto:{{ $list->inq_con }}"><i class="fa fa-envelope" aria-hidden="true"></i></a></td>
						<td><a href="mailto:{{ $list->inq_con }}" onclick="return CopyInquiryResp(<?php echo $row?>);"><i class="fa fa-envelope" aria-hidden="true"></i></a></td>-->
						<!--<td><a href="mailto:{{ $list->inq_con }}"><i class="fa fa-envelope" aria-hidden="true"></i></a></td>-->
						<td bgcolor="<?php 
							if($list->status=='4' || $list->status=='6')
									echo "#FF0000";
							elseif($list->status=='5')
									echo "#ffff00";
									
							?>">
							@if($list->status=='4' || $list->status=='6')
							DNR
							@elseif($list->status=='5')
							Prob
							@else
								OK
							@endif
						</td>
                        <td>{{ $list->state }}</td>
                        <td>{{ $list->chapter_name }}</td>
                        <td>{{ $list->terry }}</td>
                        <td>{{ $list->inq_note }}</td>

                        <!--<td><a href="mailto:{{ $list->inq_con }}">{{ $list->inq_con }}</a></td>-->
						<?php 
						    echo " <td id=email" . $row . ">" . $list->inq_con . "</td> \n";
							{													
							echo " <td  id=response" . $row . " style=\"display:none;\">" . 
								"Thanks for your interest in MOMS Club! You live in the boundaries of our MOMS Club of " . $list->chapter_name . ", " . $list->state . " chapter. I have forwarded your inquiry to them and you should hear within the next couple of days. If you don't hear, please let me know and I'll make sure they received your inquiry. If you would like to contact them directly yourself, you can reach them at " . $list->inq_con . "."
						. "</td> \n";
						}
						?>

                    </tr>
					<?php $row++;?>
                  @endforeach
                  </tbody>
                </table>
            </div>
           
            <div class="box-body text-center">
          
              <button type="button" class="btn btn-themeBlue margin" onclick="CopyNoChapter()" id="btnNoChapter" name="nochapter">Copy NO Chapter Response</button>
              
			
          </div>
		   <textarea display class="js-copytextarea" style="border: none; background-color: transparent; resize: none; outline: none; overflow:hidden; color:transparent" name="nochapter" id="nochapter"/>Thanks for your interest in MOMS Club.  I am sorry there is not a chapter in your area, but we would love to help you start one!

            The idea of starting a new chapter can be intimidating, but it is actually very easy. Each of our chapters was started by just one mom who wanted to meet other at-home moms in her community!
 
            When you register a MOMS Club, you receive a MOMS Club manual, which helps you step by step through starting a new chapter.  But, that’s not all!   You also are assigned a special MOMS Club volunteer to help you whenever you need it.   
 
            If you’d like more information – check out our website (https://momsclub.org/start-a-chapter/). </textarea>
          <!-- /.box -->
        </div>
      </div>
    </section>    
    <!-- Main content -->
    
    <!-- /.content -->
 
@endsection
@section('customscript')
<script>
function test() {
alert('test');
return false;
}
function CopyEmail(elementId){
		
		// Create a "hidden" input
		var aux = document.createElement("input");
		
		var elementName = "email" + elementId;
		
		// Assign it the value of the specified element
		aux.setAttribute("value", document.getElementById(elementName).innerHTML);
		
		// Append it to the body
		document.body.appendChild(aux);
		
		// Highlight its content
		aux.select();
		
		// Copy the highlighted text
		document.execCommand("copy");
		
		// Remove it from the body
		document.body.removeChild(aux);

		return false;

	}
function CopyNoChapter(){
		
		  var copyTextarea = document.querySelector('.js-copytextarea');
		  copyTextarea.select();
		
		  try {
			var successful = document.execCommand('copy');
			var msg = successful ? 'successful' : 'unsuccessful';
			console.log('Copying text command was ' + msg);
		  } catch (err) {
			console.log('Oops, unable to copy');
		  }

		  clearSelection();		

		return false;

	}
function clearSelection() {
		var sel;
		if ( (sel = document.selection) && sel.empty ) {
			sel.empty();
		} else {
			if (window.getSelection) {
				window.getSelection().removeAllRanges();
			}
			var activeEl = document.activeElement;
			if (activeEl) {
				var tagName = activeEl.nodeName.toLowerCase();
				if ( tagName == "textarea" || (tagName == "input" && activeEl.type == "text") ) {
					// Collapse the selection to the end
					activeEl.selectionStart = activeEl.selectionEnd;
				}
			}
		}
	}	
	function CopyInquiryResp(elementId){

		// Create a "hidden" input
		var aux = document.createElement("input");
		
		var elementName = "response" + elementId;
		
		// Assign it the value of the specified element
		aux.setAttribute("value", document.getElementById(elementName).innerHTML);
		
		// Append it to the body
		document.body.appendChild(aux);
		
		// Highlight its content
		aux.select();
		
		// Copy the highlighted text
		document.execCommand("copy");
		
		// Remove it from the body
		document.body.removeChild(aux);

		return false;

	}
</script>
@endsection
