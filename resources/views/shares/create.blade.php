@extends('layouts.main')

@section('extra_css')
@endsection

@section('contents')
		<v-sharing-create admin="yes" token="{{ csrf_token() }}"></v-sharing-create>
@endsection

@section('extra_js')
@endsection
