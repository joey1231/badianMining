@extends('layouts.main')

@section('extra_css')
@endsection

@section('contents')
	<v-sales-view hash_id="{{$sale->hash_id}}" admin="yes" ></v-sales-view>
@endsection

@section('extra_js')
@endsection
