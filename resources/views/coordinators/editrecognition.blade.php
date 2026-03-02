@extends('layouts.mimi_theme')

@section('page_title', 'Coordinator Details')
@section('breadcrumb', 'Appreciation & Recognition')

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updaterecognition",$cdDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <div class="card card-primary card-outline">
                 <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                        <h3 class="mb-0">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                        <p class="mb-0">{{ $conferenceDescription }} Conference
                            @if ($regionLongName != "None")
                                , {{ $regionLongName }} Region
                            @endif
                        </p>
                    </div>
                  <ul class="list-group list-group-flush mb-3">
                      <li class="list-group-item">
                        @include('partials.coordinatorpositions')
                      </li>
                      <li class="list-group-item">
                          @include('partials.coordinatordates')
                      </li>
                <li class="list-group-item mt-3">
                     @include('partials.coordinatorstatus')
                </li>
                  </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <div class="card-header bg-transparent border-0">
                <h3>Appreciation & Recognition Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label"></label>
                            <label class="col-sm-3 col-form-label">Recognition Gift</label>
                            <label class="col-sm-2 me-5 col-form-label">Year Given</label>
                            <label class="col-sm-3 col-form-label">Recognition Gift</label>
                            <label class="col-sm-2 col-form-label">Year Given</label>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">&lt; 1 Year:</label>
                            <div class="col-sm-3">
                                <select name="recognition0" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition0 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year0"class="form-control" value="{{ $cdDetails->recognition->year0 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">1 Year:</label>
                            <div class="col-sm-3">
                                <select name="recognition1" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition1 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year1"class="form-control" value="{{ $cdDetails->recognition->year1 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">2 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition2" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition2 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year2"class="form-control" value="{{ $cdDetails->recognition->year2 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">3 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition3" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition3 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year3"class="form-control" value="{{ $cdDetails->recognition->year3 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">4 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition4" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition4 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year4"class="form-control" value="{{ $cdDetails->recognition->year4 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">5 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition5" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition5 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year5"class="form-control" value="{{ $cdDetails->recognition->year5 }}" >
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">6 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition6" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition6 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year6"class="form-control" value="{{ $cdDetails->recognition->year6 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">7 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition7" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition7 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year7"class="form-control" value="{{ $cdDetails->recognition->year7 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">8 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition8" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition8 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year8"class="form-control" value="{{ $cdDetails->recognition->year8 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">9 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition9" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition9 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 me-5">
                                <input type="text" name="year9"class="form-control" value="{{ $cdDetails->recognition->year9 }}" >
                            </div>

                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">10 Years+ or Top Tier:</label>
                            <div class="col-sm-9">
                                <textarea name="recognition_toptier" class="form-control" rows="4" >{{ $cdDetails->recognition->recognition_toptier }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mb-1">
                    <div class="col-md-12 d-flex align-items-center">
                        <label class="ms-2 col-form-label me-2">MC Gold Pin:</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="recognition_pin" id="recognition_pin" class="form-check-input"
                                {{$cdDetails->recognition->recognition_pin == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="recognition_pin"></label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="ms-2 col-form-label me-2">MC Necklace:</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="recognition_necklace" id="recognition_necklace" class="form-check-input"
                                    {{$cdDetails->recognition->recognition_necklace == 1 ? 'checked' : ''}}>
                                    <label class="form-check-label" for="recognition_necklace"></label>
                                </div>
                            </div>
                        </div>

                        </div>
                    </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center mt-3">
                <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save</button>
                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordreports.coordrptappreciation') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-gift-fill me-2"></i>Back to Appreciation Report</button>
                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.view', ['id' => $cdDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-people-fill me-2"></i>Back to Coordinator Details</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection

