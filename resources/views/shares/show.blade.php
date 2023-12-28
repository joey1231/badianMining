@extends('layouts.main')

@section('extra_css')
@endsection

@section('contents')
	<v-sharing-view hash_id="{{$share->hash_id}}" admin="yes" ></v-sharing-view>
@endsection

@section('extra_js')
@endsection
