@extends('layouts.layout')

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	
	<div class="contents bg-white" id="register_form">
		
	</div>
	
	<router-url id="signUp" data="{{ route('signUp') }}" />
	<router-url id="findUrl" data="{{ route('findUser') }}" />
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/auth/register.js'])
@endsection