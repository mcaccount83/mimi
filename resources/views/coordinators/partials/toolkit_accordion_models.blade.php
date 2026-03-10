<!-- Modal for MIMI Position Abriviations task -->
            <div class="modal fade" id="modal-positions">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"><strong>MIMI Position Abbreviations</strong></h3>
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
                            <button type="button" class="btn btn-danger bg-gradient mb-2" data-bs-dismiss="modal"><i class="bi bi-x-lg me-2"></i>Close</button>
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
                                    <div class="mb-3">
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
                                <div class="mb-3">
                                    <label for="fileDescription">Description</label>
                                    <textarea class="form-control" id="fileDescription{{ $resourceItem->id }}">{{ $resourceItem->description }}</textarea>
                                </div>
                                </div>
                             <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="fileType{{ $resourceItem->id }}">File Type</label>
                                    <select class="form-control fileType" id="fileType{{ $resourceItem->id }}" name="fileType">
                                        <option value="1" {{ $resourceItem->file_type == 1 ? 'selected' : '' }}>Document to Download</option>
                                        <option value="2" {{ $resourceItem->file_type == 2 ? 'selected' : '' }}>Link to Webpage</option>
                                        <option value="3" {{ $resourceItem->file_type == 3 ? 'selected' : '' }}>Chapter Specific Route</option>
                                    </select>
                                </div>

                                <!-- Version field - only for file type 1 -->
                                <div class="mb-3 versionField" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                                    <label for="fileVersion{{ $resourceItem->id }}">Version</label>
                                    <input type="text" class="form-control" id="fileVersion{{ $resourceItem->id }}" name="fileVersion" value="{{ $resourceItem->version }}">
                                </div>

                                <!-- File path - only for file type 1 -->
                                <div class="mb-3 filePathField" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                                <label for="fileDescription">File Path: </label>
                                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                        {{ $resourceItem->file_path }}</a>
                                </div>

                                <!-- Link field - for file type 2 -->
                                <div class="mb-3 linkField" style="{{ $resourceItem->file_type == 2 ? 'display:block;' : 'display:none;' }}">
                                    <label for="link{{ $resourceItem->id }}">Link</label>
                                    <input type="text" class="form-control" id="link{{ $resourceItem->id }}" name="link" value="{{ $resourceItem->link }}" placeholder="https://example.com">
                                </div>

                                <!-- Route field - for file type 3 -->
                                <div class="mb-3 routeField" style="{{ $resourceItem->file_type == 3 ? 'display:block;' : 'display:none;' }}">
                                    <label for="route{{ $resourceItem->id }}">Route Name</label>
                                    <input type="text" class="form-control" id="route{{ $resourceItem->id }}" name="route" value="{{ $resourceItem->file_type == 3 ? $resourceItem->link : '' }}" placeholder="e.g., board.roster">
                                    <small class="form-text text-muted">Enter the route name. The chapter ID will be automatically passed from the URL.</small>
                                </div>
                            </div>

                            <!-- File upload - only for file type 1 -->
                            <div class="col-md-12">
                                <div class="mb-3 fileUpload" style="{{ $resourceItem->file_type == 1 ? 'display:block;' : 'display:none;' }}">
                                    <input type="file" id="fileUpload{{ $resourceItem->id }}" class="form-control" name='fileUpload'>
                                </div>
                            </div>
                        </form>
                                <div class="col-md-12"><br></div>
                                <div class="col-md-12">
                                <div class="mb-3">
                                    Updated by <strong>{{ $resourceItem->updatedBy->first_name }} {{ $resourceItem->updatedBy->last_name }}</strong> on <strong>{{ $resourceItem->updated_date }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger bg-gradient mb-2" data-bs-dismiss="modal"><i class="bi bi-x-lg me-2"></i>Close</button>
                            <button type="button" class="btn btn-success bg-gradient mb-2" onclick="updateToolkitFile({{ $resourceItem->id }})"><i class="bi bi-floppy-fill me-2"></i>Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Modal for adding task -->
             <div class="modal fade" id="modal-task">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Add a New Resource</h4>
                            </div>
                            <div class="modal-body">
                                <form id="addResourceForm">
                                    <div class="mb-3">
                                        <label>Category</label>
                                        <select name="fileCategoryNew" class="form-control select2-bs4" style="width: 50%;" id="fileCategoryNew">
                                            @foreach($toolkitCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fileNameNew">Name</label>
                                        <input type="text" class="form-control" id="fileNameNew" name="fileNameNew">
                                    </div>

                                    <div class="mb-3">
                                        <label for="fileDetailsNew">Description</label>
                                        <textarea class="form-control" id="fileDescriptionNew" name="fileDescriptionNew"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="fileTypeNew">File Type</label>
                                        <select class="form-control" id="fileTypeNew" name="fileTypeNew">
                                            <option value="" selected>Select file type</option>
                                            <option value="1">Document to Download</option>
                                            <option value="2">Link to Webpage</option>
                                            <option value="3">Chapter Specific Route</option>
                                        </select>
                                    </div>

                                    <!-- Version field - for file type 1 -->
                                    <div class="mb-3 versionFieldNew" style="display:none;">
                                        <label for="fileVersionNew">Version</label>
                                        <input type="text" class="form-control" id="fileVersionNew" name="fileVersionNew">
                                    </div>

                                    <!-- Link field - for file type 2 -->
                                    <div class="mb-3 linkFieldNew" style="display:none;">
                                        <label for="linkNew">Link</label>
                                        <input type="text" class="form-control" id="linkNew" name="linkNew" placeholder="https://example.com">
                                    </div>

                                    <!-- Route field - for file type 3 -->
                                    <div class="mb-3 routeFieldNew" style="display:none;">
                                        <label for="routeNew">Route Name</label>
                                        <input type="text" class="form-control" id="routeNew" name="routeNew" placeholder="e.g., board.roster">
                                        <small class="form-text text-muted">Enter the route name. The chapter ID will be automatically passed from the URL.</small>
                                    </div>

                                    <!-- File upload - for file type 1 -->
                                    <div class="mb-3 fileUploadNew" style="display:none;">
                                        <input type="file" id="fileUploadNew" class="form-control" name="fileUploadNew">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger bg-gradient mb-2" data-bs-dismiss="modal"><i class="bi bi-x-lg me-2"></i>Close</button>
                                <button type="button" class="btn btn-success bg-gradient mb-2" onclick="return addToolkitFile()"><i class="bi bi-floppy-fill me-2"></i>Add Resource</button>
                            </div>
                        </div>
                    </div>
                </div>
