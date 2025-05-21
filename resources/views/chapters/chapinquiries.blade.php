@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Inquiries Chapter List')

<style>
    .hidden-column {
        display: none !important;
    }
    </style>

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Inquiries Active Chapter List
                        </h3>
                        @include('layouts.dropdown_menus.menu_chapters')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover">
              <thead>
			    <tr>
					<th>Details</th>
			        <th>COPY Inquiries Email</th>
					<th>YES Chapter Response</th>
					<th>Status</th>
                    <th>State</th>
                    <th>Chapter Name</th>
                    <th>Boundaries</th>
                    <th>Inquiries Notes</th>
                    <th>Inquiries Email</th>

                    <th class="hidden-column"></th>
                </tr>
                </thead>
                <tbody>
				<?php $row = 0;?>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center "><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                        <td class="text-center "><button type="button" class="btn btn-xs" onclick="return CopyEmail(<?php echo $row?>);" style="background-color: transparent; border: none;">
                            <i class="far fa-copy fa-lg text-primary" ></i></button></td>

                            <td class="text-center "><button type="button" class="btn btn-xs" onclick="return CopyInquiryResp(<?php echo $row?>);" style="background-color: transparent; border: none;">
                            <i class="far fa-copy fa-lg text-primary" ></i></button></td>
                            @switch($list->status_id)
                                @case(1)
                                    <td>OK</td>
                                    @break
                                @case(4)
                                    <td style="background-color: #dc3545; color: #ffffff;">Do Not Refer</td>
                                    @break
                                @case(5)
                                    <td style="background-color: #ffc107;">Probation</td>
                                    @break
                                @case(6)
                                    <td style="background-color: #dc3545; color: #ffffff;">Do Not Refer</td>
                                    @break
                            @endswitch
                            <td>
                                @if($list->state_id < 52)
                                    {{$list->state->state_short_name}}
                                @else
                                    {{$list->country->short_name}}
                                @endif
                            </td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->territory }}</td>
                        <td>{{ $list->inquiries_note }}</td>

						<?php
						    echo " <td id=email" . $row . ">" . $list->inquiries_contact . "</td> \n";
							{
                                echo " <td id=response" . $row . " class=\"hidden-column\">" .
                                    "Thanks for your interest in MOMS Club! You live in the boundaries of our MOMS Club of " . $list->name . ", " . $list->state->state_short_name . " chapter. I have forwarded your inquiry to them and you should hear within the next couple of days. If you don't hear, please let me know and I'll make sure they received your inquiry. If you would like to contact them directly yourself, you can reach them at " . $list->inquiries_contact . "."
						. "</td> \n";
						}
						?>

                    </tr>
					<?php $row++;?>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="card-body text-center">
              <button type="button" class="btn bg-gradient-primary" onclick="CopyNoChapter()" id="btnNoChapter" name="nochapter"><i class="fas fa-copy mr-2" ></i>Copy NO Chapter Response</button>
          </div>
		   <textarea display class="js-copytextarea" style="border: none; background-color: transparent; resize: none; outline: none; overflow:hidden; color:transparent" name="nochapter" id="nochapter"/>
           Thanks for your interest in MOMS Club.  I am sorry there is not a chapter in your area, but we would love to help you start one!

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

document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});

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
