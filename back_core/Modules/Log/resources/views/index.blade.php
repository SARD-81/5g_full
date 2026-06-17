@extends('log::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('log.name') !!}</p>
@endsection
