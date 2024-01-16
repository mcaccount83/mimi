@extends('layouts.chapter_theme')

@section('content')
<div class="card-body">


<form action="{{ url('/files/storeEIN/'. $id) }}" method="post" enctype="multipart/form-data">
    @csrf
<div class="mb-3">
<input type="file" name='file' required>
@error('file')
    <span class="text-danger">{{ $message }}</span>
@enderror
</div>
<button type="submit">upload</button>
</form>


</div>

@endsection

@section('customscript')
<script>

</script>
