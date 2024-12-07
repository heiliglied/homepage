@extends('layouts.layout')

@section('heads')
<style>
.section {
	position: relative;
	width: 90%;
	margin: 0 auto;
	padding: 20px;
}
</style>
@endsection

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	
	<div class="contents bg-white" id="board">
		
	</div>
	
	<write-url id="writeUrl" data="{{ route('boardWrite') }}"></write-url>
	<list-url id="listUrl" data="{{ route('boardList') }}"></list-url>
	<view-url id="viewUrl" data="{{ route('boardView', ['id' => ':id']) }}"></view-url>
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/board/list.js'])

@endsection