@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-4">Hello {{$user->{'name'} }}</div>
    </div>
@endsection
