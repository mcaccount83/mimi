@extends('layouts.app')

@section('content')
    <h1>{{ $post->title }}</h1>
    <p>{{ $post->content }}</p>
    <!-- Add more details like author, creation date, etc. -->
    <a href="{{ route('boardlist.index') }}">Back to BoardList</a>
@endsection
