@extends('layouts.coordinator_theme')

@section('page_title', 'Resources')
@section('breadcrumb', 'Coordinator Toolkit')

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

.swal-wide {
    width: 600px !important;
}
</style>

@section('content')

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
                        Coordinator Toolkit
                      </h3>
                      @include('layouts.dropdown_menus.menu_resources')
                  </div>
              </div>
              <!-- /.card-header -->
          <div class="card-body">
        <div class="row">
            <p>&nbsp;&nbsp;Additional Resources that may be helpful for Coordinators and that Chapters may need in spcific circumstances.</p>
        </div>
        @if($canEditFiles)
            <div class="row">
                &nbsp;&nbsp;<button type="button" class="btn bg-gradient-success" data-toggle="modal" data-target="#modal-task"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Add Toolkit Item</button>
            </div>
            <div class="row">&nbsp;</div>
        @endif

        <div class="row">
            <div class="grid">
                <!-- Grid item -->
                @foreach($toolkitCategories as $category)
                    @if($category->category_name != "EIN FILES (CC ONLY)" || $conferenceCoordinatorCondition || $einCondition)
                        <div class="grid-item">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $category->category_name }}</h3>
                                </div>
                                <div class="card-body">
                                    @foreach($resources->where('toolkitCategory.category_name', $category->category_name) as $resourceItem)
                                        <div class="col-md-12" style="margin-bottom: 5px;">
                                            @if ($resourceItem->link)
                                                <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                            @elseif ($resourceItem->file_path)
                                                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                    {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                                </a>
                                            @else
                                                {{ $resourceItem->name }}
                                            @endif
                                            @if($canEditFiles)
                                                <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($category->category_name == "RESOURCE FOR COORDINATORS")
                                        <div class="col-md-12" style="margin-bottom: 5px;">
                                            <a href="javascript:void(0)" onclick="showPositionAbbreviations()">MIMI Position Abbreviations</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    </div>
</div>
</div>

            <!-- Modal for MIMI Position Abriviations task -->
            <div class="modal fade" id="modal-positions">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"><strong>MIMI Position Abreviations</strong</h3>
                        </div>
                        <div class="modal-body">
                            <table>
                                <tr>
                                  <td><h4>BS</h4></td>
                                  <td><h4>Big Sister</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>AC</h4></td>
                                  <td><h4>Area Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>SC</h4></td>
                                  <td><h4>State Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>ARC</h4></td>
                                  <td><h4>Assistant Regional Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>RC</h4></td>
                                  <td><h4>Regional Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>ACC</h4></td>
                                  <td><h4>Assistant Conference Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>CC</h4></td>
                                  <td><h4>Conference Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>IC</h4></td>
                                  <td><h4>Inquiries Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>WR</h4></td>
                                  <td><h4>Website Reviewer</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>ReReg&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4></td>
                                  <td><h4>Re-registration Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>CDC</h4></td>
                                  <td><h4>Chapter Development Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>VC</h4></td>
                                  <td><h4>Volunteer Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>Corr</h4></td>
                                  <td><h4>Correspondence Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>SMC</h4></td>
                                  <td><h4>Conference Social Media Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>SPC</h4></td>
                                  <td><h4>Special Projects Coordinator</h4></td>
                                </tr>
                              </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
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
                                            @foreach($toolkitCategories as $category)
                                                <option value="{{ $category->id }}" {{ ($resourceItem->toolkitCategory && $resourceItem->toolkitCategory->id == $category->id) ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
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
                                    <div class="form-group versionField" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                                        <label for="fileVersion{{ $resourceItem->id }}">Version</label>
                                        <input type="text" class="form-control" id="fileVersion{{ $resourceItem->id }}" name="fileVersion" value="{{ $resourceItem->version }}">
                                    </div>
                                    <div class="form-group filePathField" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                                        File Path: <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                            {{ $resourceItem->file_path }}</a>
                                        {{-- <a href="{{ $resourceItem->file_path }}">{{ $resourceItem->file_path }}</a> --}}
                                    </div>
                                    <div class="form-group linkField" style="{{ $resourceItem->file_type == 2 ? 'display:block;' : 'display:none;' }}">
                                        <label for="link{{ $resourceItem->id }}">Link</label>
                                        <input type="text" class="form-control" id="link{{ $resourceItem->id }}" name="link" value="{{ $resourceItem->link }}">
                                    </div>
                                </div>
                            <div class="col-md-12">
                                <div class="form-group fileUpload" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                                    <input type="file" id="fileUpload{{ $resourceItem->id }}" class="form-control" name='fileUpload' required>
                                </div>
                            </div>
                        </form>
                                <div class="col-md-12"><br></div>
                                <div class="col-md-12">
                                <div class="form-group">
                                    Updated by <strong>{{ $resourceItem->updated_by }}</strong> on <strong>{{ \Illuminate\Support\Carbon::parse($resourceItem->updated_date)->format('m-d-Y') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                            <button type="button" class="btn btn-success" onclick="updateToolkitFile({{ $resourceItem->id }})"><i class="fas fa-save" ></i>&nbsp; Save changes</button>
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
                                        <select name="fileCategoryNew" class="form-control select2-bs4" style="width: 50%;" id="fileCategoryNew">
                                            @foreach($toolkitCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
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
                                    <div class="form-group versionFieldNew" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                                        <label for="fileVersionNew">Version</label>
                                        <input type="text" class="form-control" id="fileVersionNew" name="fileVersionNew">
                                    </div>
                                    <div class="form-group linkFieldNew" style="{{ $resourceItem->file_type == 2 ? 'display:block;' : 'display:none;' }}">
                                        <label for="linkNew">Link</label>
                                        <input type="text" class="form-control" id="linkNew" name="linkNew" >
                                    </div>
                                    <div class="form-group fileUploadNew" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                                        <input type="file" id="fileUploadNew" class="form-control" name="fileUploadNew" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                                <button type="button" class="btn btn-success" onclick="return addToolkitFile()"><i class="fas fa-save" ></i>&nbsp; Add Resource</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
    </section>
<!-- /.content -->
@endsection
@section('customscript')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>

@include('layouts.scripts.masonrygrid')

@endsection
