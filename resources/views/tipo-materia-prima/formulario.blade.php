@extends('layouts.app')

@section('title')
{{ $title }}
@endsection()

@section('content')
<div>
    @include('layouts.form')
</div>
@endsection
