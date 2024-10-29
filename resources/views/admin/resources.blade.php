@extends('layouts.coordinator_theme')
<style>
    .grid {
    display: block; /* Masonry will handle the grid layout */
    width: 100%; /* Ensure grid takes full width of container */
}

.grid-item {
    width: 400px; /* Ensure grid items match the column width in Masonry options */
    margin-bottom: 20px; /* Add bottom margin to avoid overlap */
    box-sizing: border-box; /* Include padding and border in width */
}

.card {
    width: 100%; /* Ensure card takes full width of grid item */
    box-sizing: border-box; /* Include padding and border in width */
}
</style>

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
          <h1>Resources</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Resources</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('fail'))
        <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
        </div>
    @endif

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                  <div class="dropdown">
                      <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Chapter Resources
                      </h3>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('admin.toolkit') }}">Coordinator Toolkit</a>
                        <a class="dropdown-item" href="{{ route('admin.resources') }}">Chapter Resources</a>
                        @if ($assistConferenceCoordinatorCondition)
                        <a class="dropdown-item" href="{{ route('admin.downloads') }}">Download Reports</a>
                        @endif
                        @if ($regionalCoordinatorCondition)
                        <a class="dropdown-item" href="{{ route('admin.bugs') }}">MIMI Bugs & Wishes</a>
                        @endif
                        <a class="dropdown-item" href="https://momsclub.org/elearning/" target="_blank">eLearning</a>
                      </div>
                  </div>
              </div>
              <!-- /.card-header -->
          <div class="card-body">
        <div class="row">
            <p>&nbsp;&nbsp;Board members have the same list of links & file downloads available through their MIMI logins.</p>
        </div>
        @if($canEditFiles)
            <div class="row">
                &nbsp;&nbsp;<button type="button" class="btn bg-gradient-success" data-toggle="modal" data-target="#modal-task"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Add Resource</button>
            </div>
            <div class="row">&nbsp;</div>
        @endif
        <div class="row">

        <div class="grid">
            <!-- Grid item -->
            <div class="grid-item">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">BYLAWS</h3>
                    </div>
                        <div class="card-body">
                        @foreach($resources->where('category', 1) as $resourceItem)
                                <p>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </p>
                        @endforeach
                    </div>
                </div>
            </div>


            <!-- Grid item -->
            <div class="grid-item">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">FACT SHEETS</h3>
                    </div>
                        <div class="card-body">
                        @foreach($resources->where('category', 2) as $resourceItem)

                                <P>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </P>

                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Grid item -->
            <div class="grid-item">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">COPY READY MATERIAL</h3>
                    </div>
                        <div class="card-body">
                        @foreach($resources->where('category', 3) as $resourceItem)
                                <p>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </p>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Grid item -->
            <div class="grid-item">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">IDEAS AND INSPIRATION</h3>
                    </div>
                        <div class="card-body">
                        @foreach($resources->where('category', 4) as $resourceItem)
                                <p>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </p>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Grid item -->
            <div class="grid-item">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">CHAPTER RESOURCES</h3>
                    </div>
                        <div class="card-body">
                        @foreach($resources->where('category', 5) as $resourceItem)
                                <p>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </p>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Grid item -->
            <div class="grid-item">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">SAMPLE CHAPTER FILES</h3>
                    </div>
                        <div class="card-body">
                        @foreach($resources->where('category', 6) as $resourceItem)
                                <p>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </p>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Grid item -->
            <div class="grid-item">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">END OF YEAR<small>&nbsp;(AVAIL TO CHAPTERS JULY-DEC)</small></h3>
                    </div>
                        <div class="card-body">
                        @foreach($resources->where('category', 7) as $resourceItem)
                                <p>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </p>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



@foreach($resources as $resourceItem)
<!-- Modal for editing task -->
<div class="modal fade" id="editResourceModal{{ $resourceItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editResourceModal{{ $resourceItem->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                    <h3 class="modal-title" id="#editResourceModal{{ $resourceItem->id }}Label">{{ $resourceItem->name }}</h3>
                </div>
            <div class="modal-body">
                <form>
                    <div class="col-md-12">
                    <div class="form-group">
                        <label for="fileCategory{{ $resourceItem->id }}">Category</label>
                        <select name="fileCategory" class="form-control select2-bs4" style="width: 50%;" id="fileCategory{{ $resourceItem->id }}" disabled>
                            <option value="1" {{ $resourceItem->category == 1 ? 'selected' : '' }}>Bylaws</option>
                            <option value="2" {{ $resourceItem->category == 2 ? 'selected' : '' }}>Fact Sheets</option>
                            <option value="3" {{ $resourceItem->category == 3 ? 'selected' : '' }}>Copy Ready Materials</option>
                            <option value="4" {{ $resourceItem->category == 4 ? 'selected' : '' }}>Ideas & Inspiration</option>
                            <option value="5" {{ $resourceItem->category == 5 ? 'selected' : '' }}>Chapter Resources</option>
                            <option value="6" {{ $resourceItem->category == 6 ? 'selected' : '' }}>Sample Chapter Files</option>
                            <option value="7" {{ $resourceItem->category == 7 ? 'selected' : '' }}>End of Year</option>
                        </select>
                    </div>
                    </div>
                    <div class="col-md-12">
                    <div class="form-group">
                        <label for="fileDescription">Description</label>
                        <textarea class="form-control" id="fileDescription{{ $resourceItem->id }}">{{ $resourceItem->description }}</textarea>
                    </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="fileType{{ $resourceItem->id }}">File Type</label>
                            <select class="form-control fileType" id="fileType{{ $resourceItem->id }}" name="fileType">
                                <option value="1" {{ $resourceItem->file_type == 1 ? 'selected' : '' }}>Document to Download</option>
                                <option value="2" {{ $resourceItem->file_type == 2 ? 'selected' : '' }}>Link to Webpage</option>
                            </select>
                        </div>
                        <div class="form-group versionField" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                            <label for="fileVersion{{ $resourceItem->id }}">Version</label>
                            <input type="text" class="form-control" id="fileVersion{{ $resourceItem->id }}" name="fileVersion" value="{{ $resourceItem->version }}">
                        </div>
                        <div class="form-group filePathField" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                            File Path: <a href="{{ $resourceItem->file_path }}">{{ $resourceItem->file_path }}</a>
                        </div>
                        <div class="form-group linkField" style="{{ $resourceItem->file_type === 2 ? 'display:block;' : 'display:none;' }}">
                            <label for="link{{ $resourceItem->id }}">Link</label>
                            <input type="text" class="form-control" id="link{{ $resourceItem->id }}" name="link" value="{{ $resourceItem->link }}">
                        </div>
                    </div>
                <div class="col-md-12">
                    <div class="form-group fileUpload" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                        <input type="file" id="fileUpload{{ $resourceItem->id }}" class="form-control" name='fileUpload' required>
                    </div>
                </div>
            </form>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12">
                    <div class="form-group">
                        Updated by <strong>{{ $resourceItem->updated_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($resourceItem->updated_date)->format('m-d-Y') }}</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                <button type="button" class="btn btn-success" onclick="updateFile({{ $resourceItem->id }})"><i class="fas fa-save" ></i>&nbsp; Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding task -->
 <div class="modal fade" id="modal-task">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add a New Resource</h4>
                </div>
                <div class="modal-body">
                    <form id="addResourceForm">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="fileCategoryNew" class="form-control select2-bs4" style="width: 50%;" id="fileCategoryNew" >
                                <option value="1" >Bylaws</option>
                                <option value="2" >Fact Sheets</option>
                                <option value="3" >Copy Ready Materials</option>
                                <option value="4" >Ideas & Inspiration</option>
                                <option value="5" >Chapter Resources</option>
                                <option value="6" >Sample Chapter Files</option>
                                <option value="7" >End of Year</option>
                            </select>
                            </div>
                        <div class="form-group">
                            <label for="fileNameNew">Name</label>
                            <input type="text" class="form-control" id="fileNameNew" name="fileNameNew">
                        </div>

                        <div class="form-group">
                            <label for="fileDetailsNew">Description</label>
                            <textarea class="form-control" id="fileDescriptionNew" name="fileDescriptionNew"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="fileTypeNew">File Type</label>
                            <select class="form-control" id="fileTypeNew" name="fileTypeNew">
                                <option value="" selected>Select file type</option>
                                <option value="1">Document to Download</option>
                                <option value="2">Link to Webpage</option>
                            </select>

                        </div>
                        <div class="form-group versionFieldNew" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                            <label for="fileVersionNew">Version</label>
                            <input type="text" class="form-control" id="fileVersionNew" name="fileVersionNew">
                        </div>
                        <div class="form-group linkFieldNew" style="{{ $resourceItem->file_type === 2 ? 'display:block;' : 'display:none;' }}">
                            <label for="linkNew">Link</label>
                            <input type="text" class="form-control" id="linkNew" name="linkNew" >
                        </div>
                        <div class="form-group fileUploadNew" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                            <input type="file" id="fileUploadNew" class="form-control" name="fileUploadNew" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                    <button type="button" class="btn btn-success" onclick="return addFile()"><i class="fas fa-save" ></i>&nbsp; Add Resource</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endforeach
</section>
<!-- /.content -->
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>

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


$(document).ready(function() {
    var elem = document.querySelector('.grid');
    var msnry = new Masonry(elem, {
        itemSelector: '.grid-item',
        columnWidth: 400, // Set a fixed column width (adjust as needed)
        gutter: 20, // Set gutter for spacing between items
        percentPosition: true
    });
});
</script>
<script>
    $(document).ready(function() {
        $('.fileType').change(function() {
            var selectedType = $(this).val();
            var resourceId = $(this).attr('id').replace('fileType', '');

            if (selectedType === '1') {
                $('.versionField').show();
                $('.filePathField').show();
                $('.fileUpload').show();
                $('.filePathBlock').show();
                $('.linkField').hide();
            } else if (selectedType === '2') {
                $('.linkField').show();
                $('.versionField').hide();
                $('.filePathField').hide();
                $('.filePathBlock').hide();
                $('.fileUpload').hide();
            }
        });

        $('.linkFieldNew').hide();
        $('.versionFieldNew').hide();
        $('.fileUploadNew').hide();

        $('#fileTypeNew').change(function() {
            var selectedType = $(this).val();

            if (selectedType === '1') {
                $('.versionFieldNew').show();
                $('.fileUploadNew').show();
                $('.linkFieldNew').hide();
            } else if (selectedType === '2') {
                $('.linkFieldNew').show();
                $('.versionFieldNew').hide();
                $('.fileUploadNew').hide();
            }
        });
    });

    function addFile() {
    var fileCategoryNew = document.getElementById('fileCategoryNew').value;
    var fileNameNew = document.getElementById('fileNameNew').value;
    var fileDescriptionNew = document.getElementById('fileDescriptionNew').value;
    var fileTypeNew = document.getElementById('fileTypeNew').value;
    var fileVersionNew = document.getElementById('fileVersionNew').value;
    var linkNew = document.getElementById('linkNew').value;

      // Initialize an array to collect validation errors
      let validationErrors = [];

    // Collect validation errors
    if (fileCategoryNew === '') {
        validationErrors.push('Category is Required.');
    }
    if (fileNameNew === '') {
        validationErrors.push('Name is Required.');
    }
    if (fileNameNew.length > 50) {
        validationErrors.push('Name cannot exceed 50 characters.');
    }
    if (fileDescriptionNew === '') {
        validationErrors.push('Description is Required.');
    }
    if (fileDescriptionNew.length > 500) {
        validationErrors.push('Description cannot exceed 500 characters.');
    }
    if (fileTypeNew === '') {
        validationErrors.push('File Type is Required.');
    }
    if (fileTypeNew === '1' && fileVersionNew === '') {
        validationErrors.push('File Version is Required.');
    }
    if (fileTypeNew === '2' && linkNew === '') {
        validationErrors.push('Link is Required.');
    }

    // Check if there are any validation errors
    if (validationErrors.length > 0) {
        Swal.fire({
            title: 'Error!',
            html: validationErrors.join('<br>'),
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-danger'
            }
        });
        return false; // Prevent form submission
    }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize the FormData object
    var formData = new FormData();
    formData.append('fileCategoryNew', fileCategoryNew);
    formData.append('fileNameNew', fileNameNew);
    formData.append('fileDescriptionNew', fileDescriptionNew);
    formData.append('fileTypeNew', fileTypeNew);

    if (fileTypeNew === '1') {
        formData.append('fileVersionNew', fileVersionNew);
    } else if (fileTypeNew === '2') {
        formData.append('linkNew', linkNew); // Include the link
    }

    // Send an AJAX request to Laravel backend to create a new toolkit
    $.ajax({
            url: '{{ route('admin.addresources') }}',
            method: 'POST',
            data: formData,
            processData: false, // Required for FormData
            contentType: false, // Required for FormData
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                var id = response.id;
                var fileType = response.file_type;

        // Check if file type requires further processing
        if (fileType === '1') {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we update the toolkit.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    var formData = new FormData();
                    formData.append('file', document.getElementById('fileUploadNew').files[0]);

                    // Send an AJAX request to upload the file to Google Drive
                    $.ajax({
                        url: '{{ route('store.resources', '') }}' + '/' + id,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Resource & File added successfully.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error!', 'File upload failed. Please try again.', 'error');
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Resource added successfully.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error!', 'Resource add failed. Please try again.', 'error');
                console.error(xhr.responseText);
            }
        });

  // Close the modal
  $('#modal-task').modal('hide');

// Prevent form submission
return false;
}

function updateFile(id) {
    var file = document.getElementById('fileUpload' + id).files[0];
    var fileDescription = document.getElementById('fileDescription' + id).value;
    var fileType = document.getElementById('fileType' + id).value;
    var fileVersion = document.getElementById('fileVersion' + id).value;
    var link = document.getElementById('link' + id).value;

    var formData = new FormData();
    formData.append('file', file);
    formData.append('fileDescription', fileDescription);
    formData.append('fileType', fileType);
    formData.append('fileVersion', fileVersion);
    formData.append('link', link);

    // Initialize an array to collect validation errors
    let validationErrors = [];

    // Collect validation errors
    if (fileDescription === '') {
        validationErrors.push('Description is Required.');
    }
    if (fileDescription.length > 500) {
        validationErrors.push('Description cannot exceed 500 characters.');
    }
    if (fileType === '2' && link === '') {
        validationErrors.push('Link is Required.');
    }

    // Check if there are any validation errors
    if (validationErrors.length > 0) {
        Swal.fire({
            title: 'Error!',
            html: validationErrors.join('<br>'), // Combine all errors into a single string with line breaks
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-danger' // Add your custom button class here
            }
        });
        return false; // Prevent form submission
    }

    // Continue with the form submission process if no errors
    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send an AJAX request to update the task
    $.ajax({
        url: '{{ route('admin.updateresources', '') }}' + '/' + id,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            // Check if file type requires further processing
            if (fileType === '1') {
                // Show the processing Swal
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we update the resources.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // Send an AJAX request to upload the file to Google Drive
            $.ajax({
                    url: '{{ route('store.resources', '') }}' + '/' + id,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Resource & File updated successfully.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload(); // Reload the page to reflect changes
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error!', 'File upload failed. Please try again.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Success!',
                    text: 'Resource updated successfully.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload(); // Reload the page to reflect changes
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Resource update failed. Please try again.', 'error');
            console.error(xhr.responseText);
        }
    });

    // Close the modal
    $('#editResourceModal' + id).modal('hide');

    // Prevent form submission
    return false;
}

</script>
</html>
