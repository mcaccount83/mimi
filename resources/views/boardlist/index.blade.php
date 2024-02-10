@extends('layouts.boardlist_theme')

<!-- AdminLTE CSS -->
<link rel="stylesheet" href="{{ asset('node_modules/admin-lte/dist/css/adminlte.min.css') }}">

<!-- AdminLTE JS -->
<script src="{{ asset('node_modules/admin-lte/dist/js/adminlte.min.js') }}"></script>

@section('content')
<section class="content-header">
    <h1>
    BoardList
     <small>List</small>
     </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('boardlist.index') }}"><i class="fa fa-home"></i>BoardList</a></li>
      <li class="active">List</li>
    </ol>
  </section>
  <!-- Main content -->
    @foreach ($posts as $post)
        <div>
            <h2>{{ $post->title }}</h2>
            <p>{{ $post->content }}</p>
            <!-- Add more details like author, creation date, etc. -->
            <a href="{{ route('boardlist.show', $post->id) }}">View Post</a>
        </div>
    @endforeach
@endsection
