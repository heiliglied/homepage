@extends('layouts.layout')

@section('heads')
<style>
.mypage {
	width: 90%;
	margin: 0 auto;
	padding-top: 2%;
}
</style>
@endsection

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	
	<div class="contents bg-white" id="mypage">
		
	</div>
	
	<user-profile id="userProfile" data="{{ route('myProfile') }}"></user-profile>
	<update-profile id="updateProfile" data="{{ route('updateProfile') }}"></update-profile>
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/my/view.js'])

@endsection