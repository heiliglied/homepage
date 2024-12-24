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
	
	<board-url id="boardUrl" data="{{ route('board') }}"></board-url>
	<list-url id="listUrl" data="{{ route('boardList') }}"></list-url>
	<view-url id="viewUrl" data="{{ route('getView') }}"></view-url>
	<modify-url id="modifyUrl" data="{{ route('boardModify', ['id' => ':id']) }}"></modify-url>
	<delete-url id="deleteUrl" data="{{ route('deleteBoard', ['id' => ':id']) }}"></delete-url>
	<censor-url id="censorUrl" data="{{ route('censorBoard', ['id' => ':id']) }}"></censor-url>
	<re_censor-url id="reCensorUrl" data="{{ route('replyCensor', ['id' => ':id']) }}"></re_censor-url>
	<reply-url id="replyUrl" data="{{ route('getReply') }}"></reply-url>
	<comment-url id="commentUrl" data="{{ route('setReply') }}"></comment-url>
	<board-id id="boardID" data="{{ $boardID }}"></board-id>
	<download-file id="downloadFileUrl" data="{{ route('downloadFile', ['id' => ':id']) }}"></download-file>
	<delReply-url id="delReplyUrl" data="{{ route('deleteReply', ['id' => ':id']) }}"></delReply-url>
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/board/view.js'])

@endsection