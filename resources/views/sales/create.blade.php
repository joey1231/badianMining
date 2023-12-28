@extends('layouts.main')

@section('extra_css')
@endsection

@section('contents')
		<v-sales-create admin="yes" token="{{ csrf_token() }}"></v-sales-create>
@endsection

@section('extra_js')
@endsection
