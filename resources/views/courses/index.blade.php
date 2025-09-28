@extends('layouts.public_theme')

<style>

</style>

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                        <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">


        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Courses
        </h2>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @if(is_array($courses) && count($courses) > 0)
                    <ul class="space-y-2">
                       {{-- Update your Blade template --}}
@foreach($courses as $course)
    <li>
        <a href="{{ $course['auto_login_url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-lg">
            {{ $course['title']['rendered'] }}
        </a>
    </li>
@endforeach
                    </ul>
                @else
                    <p>No courses found for your user type.</p>
                @endif
            </div>
        </div>
    </div>
</div>

                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>




