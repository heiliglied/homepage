@extends('layouts.layout')

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	
	<div class="contents bg-white" id="find_form">
		
	</div>
	
	<router-url id="loginUrl" data="{{ route('login') }}">
	<find-url id="idCheck" data="{{ route('idCheck') }}">
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/auth/findId.js'])
@endsection