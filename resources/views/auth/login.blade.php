@extends('layouts.layout')

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	
	<div class="contents bg-white" id="login_form">
		
	</div>
	
	<router-url id="loginUrl" data="{{ route('signIn') }}">
	<find-url id="findPassword" data="{{ route('findPassword') }}">
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/auth/login.js'])
@endsection