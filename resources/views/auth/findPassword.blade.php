@extends('layouts.layout')

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	
	<div class="contents bg-white" id="find_form">
		
	</div>
	
	<router-url id="emailCheck" data="{{ route('emailCheck') }}">
	<find-url id="findId" data="{{ route('findId') }}">
	<login-url id="loginUrl" data="{{ route('login') }}">
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/auth/findPassword.js'])
@endsection