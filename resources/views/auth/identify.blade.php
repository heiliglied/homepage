@extends('layouts.layout')

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	
	<div class="contents bg-white" id="identify_form">
		
	</div>
	
	<custom-data id="user_id" data="{{ $user->id }}" />
	<custom-data id="user_email" data="{{ $user->email }}" />
	<router-url id="checkToken" data="{{ route('checkToken') }}" />
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/auth/identify.js'])
@endsection