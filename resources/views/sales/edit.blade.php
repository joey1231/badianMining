@extends('layouts.main')

@section('extra_css')
@endsection

@section('contents')
	<v-sales-edit hash_id="{{$sale->hash_id}}" admin="yes" token="{{ csrf_token() }}"></v-sales-edit>
@endsection

@section('extra_js')
@endsection
