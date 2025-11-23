@extends('layouts.coordinator_theme')

@section('page_title', 'Resources')
@section('breadcrumb', 'Chapter Resources')

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
                      @include('layouts.dropdown_menus.menu_resources')
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
            {{-- @foreach($resourceCategories as $category)
                <div class="grid-item">
                    <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ $category->category_name }}</h3>
                    </div>
                        <div class="card-body">
                            @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
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
                        </div>
                    </div>
                </div>
            @endforeach --}}
            @foreach($resourceCategories as $category)
    <div class="grid-item">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">{{ $category->category_name }}</h3>
            </div>
            <div class="card-body">
                @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
                <div class="col-md-12" style="margin-bottom: 5px;">
                    @if ($resourceItem->file_type == 2)
                        {{-- External Link --}}
                        <a href="{{ $resourceItem->link }}" target="_blank">
                            {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                        </a>
                    @elseif ($resourceItem->file_type == 3)
                        {{-- Laravel Route - Just show title, no link for admin --}}
                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                        <span style="font-size: smaller; color: #6c757d;">(Chapter Specific Route)</span>
                    @elseif ($resourceItem->file_type == 1)
                        {{-- File Download --}}
                        <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                            {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                        </a>
                    @else
                        {{-- Fallback for no file type --}}
                        {{ $resourceItem->name }}
                    @endif
                    @if($canEditFiles)
                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach

        </div>
    </div>
</div>


{{-- @foreach($resources as $resourceItem)
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
                                @foreach($resourceCategories as $category)
                                    <option value="{{ $category->id }}" {{ ($resourceItem->resourceCategory && $resourceItem->resourceCategory->id == $category->id) ? 'selected' : '' }}>
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
                        Updated by <strong>{{ $resourceItem->updated_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($resourceItem->updated_date)->format('m-d-Y') }}</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                <button type="button" class="btn btn-success" onclick="updateResourceFile({{ $resourceItem->id }})"><i class="fas fa-save" ></i>&nbsp; Save changes</button>
            </div>
        </div>
    </div>
</div> --}}

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
                                @foreach($resourceCategories as $category)
                                    <option value="{{ $category->id }}" {{ ($resourceItem->resourceCategory && $resourceItem->resourceCategory->id == $category->id) ? 'selected' : '' }}>
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
                                <option value="3" {{ $resourceItem->file_type == 3 ? 'selected' : '' }}>Laravel Route</option>
                            </select>
                        </div>

                        <!-- Version field - only for file type 1 -->
                        <div class="form-group versionField" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                            <label for="fileVersion{{ $resourceItem->id }}">Version</label>
                            <input type="text" class="form-control" id="fileVersion{{ $resourceItem->id }}" name="fileVersion" value="{{ $resourceItem->version }}">
                        </div>

                        <!-- File path - only for file type 1 -->
                        <div class="form-group filePathField" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                        <label for="fileDescription">File Path: </label>
                           <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                {{ $resourceItem->file_path }}</a>
                        </div>

                        <!-- Link field - for file type 2 -->
                        <div class="form-group linkField" style="{{ $resourceItem->file_type == 2 ? 'display:block;' : 'display:none;' }}">
                            <label for="link{{ $resourceItem->id }}">Link</label>
                            <input type="text" class="form-control" id="link{{ $resourceItem->id }}" name="link" value="{{ $resourceItem->link }}" placeholder="https://example.com">
                        </div>

                        <!-- Route field - for file type 3 -->
                        <div class="form-group routeField" style="{{ $resourceItem->file_type == 3 ? 'display:block;' : 'display:none;' }}">
                            <label for="route{{ $resourceItem->id }}">Route Name</label>
                            <input type="text" class="form-control" id="route{{ $resourceItem->id }}" name="route" value="{{ $resourceItem->file_type == 3 ? $resourceItem->link : '' }}" placeholder="e.g., board.roster">
                            <small class="form-text text-muted">Enter the route name. The chapter ID will be automatically passed from the URL.</small>
                        </div>
                    </div>

                    <!-- File upload - only for file type 1 -->
                    <div class="col-md-12">
                        <div class="form-group fileUpload" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                            <input type="file" id="fileUpload{{ $resourceItem->id }}" class="form-control" name='fileUpload'>
                        </div>
                    </div>
                </form>
                <div class="col-md-12"><br></div>
                <div class="col-md-12">
                    <div class="form-group">
                        {{-- Updated by <strong>{{ $resourceItem->updated_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($resourceItem->updated_date)->format('m-d-Y') }}</strong> --}}
                        Updated by <strong>{{ $resourceItem->updatedBy->first_name }} {{ $resourceItem->updatedBy->last_name }}</strong> on <strong>{{ \Illuminate\Support\Carbon::parse($resourceItem->updated_date)->format('m-d-Y') }}</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp; Close</button>
                <button type="button" class="btn btn-success" onclick="updateResourceFile({{ $resourceItem->id }})"><i class="fas fa-save"></i>&nbsp; Save changes</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- <!-- Modal for adding task -->
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
                                @foreach($resourceCategories as $category)
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
                    <button type="button" class="btn btn-success" onclick="return addResourceFile()"><i class="fas fa-save" ></i>&nbsp; Add Resource</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
 --}}

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
                            @foreach($resourceCategories as $category)
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
                            <option value="3">Laravel Route</option>
                        </select>
                    </div>

                    <!-- Version field - for file type 1 -->
                    <div class="form-group versionFieldNew" style="display:none;">
                        <label for="fileVersionNew">Version</label>
                        <input type="text" class="form-control" id="fileVersionNew" name="fileVersionNew">
                    </div>

                    <!-- Link field - for file type 2 -->
                    <div class="form-group linkFieldNew" style="display:none;">
                        <label for="linkNew">Link</label>
                        <input type="text" class="form-control" id="linkNew" name="linkNew" placeholder="https://example.com">
                    </div>

                    <!-- Route field - for file type 3 -->
                    <div class="form-group routeFieldNew" style="display:none;">
                        <label for="routeNew">Route Name</label>
                        <input type="text" class="form-control" id="routeNew" name="routeNew" placeholder="e.g., board.roster">
                        <small class="form-text text-muted">Enter the route name. The chapter ID will be automatically passed from the URL.</small>
                    </div>

                    <!-- File upload - for file type 1 -->
                    <div class="form-group fileUploadNew" style="display:none;">
                        <input type="file" id="fileUploadNew" class="form-control" name="fileUploadNew">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp; Close</button>
                <button type="button" class="btn btn-success" onclick="return addResourceFile()"><i class="fas fa-save"></i>&nbsp; Add Resource</button>
            </div>
        </div>
    </div>
</div>

{{-- @endforeach --}}
</section>
<!-- /.content -->
@endsection
@section('customscript')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>

@include('layouts.scripts.masonrygrid')

@endsection
