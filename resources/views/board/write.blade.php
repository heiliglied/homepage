@extends('layouts.layout')

@section('heads')
<style>
.section {
	position: relative;
	width: 90%;
	margin: 0 auto;
	padding: 20px;
}

.ck-editor__editable {
	min-height: 650px;
}

.ck-editor__editable p {
	margin: 0
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
	
	<list-url id="listUrl" data="{{ route('board') }}"></list-url>
	<upload-url id="uploadUrl" data="{{ route('imageUpload') }}"></upload-url>
	<upload-type id="uploadType" data="{{ $uploadType }}"></upload-type>
	<board-id id="boardID" data="{{ $boardID }}"></board-id>
	<board-regist id="boardRegist" data="{{ route('boardRegist') }}"></board-regist>
	<get-url id="getUrl" data="{{ route('getBoard') }}"></get-url>
	<delete-file id="deleteFileUrl" data="{{ route('deleteFile', ['id' => ':id']) }}"></delete-file>
	<download-file id="downloadFileUrl" data="{{ route('downloadFile', ['id' => ':id']) }}"></download-file>
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/board/write.js'])

@endsection