@extends('layouts.layout')

@section('heads')
<style>
.fiddler {
	background-color: transparent !important;
}

.fiddler_contents {
	position: relative;
	width: 100%;
	min-height: calc(100% - 110px);
}

.list_layer {
	position: absolute;
	right: 0;
	width: 200px;
	height: auto;
	background-color: white;
	z-index: 20;
	display: none;
	border: solid 2px black;
}

#myList a {
	color: blue;
}
</style>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.52.0/min/vs/editor/editor.main.min.css">
@endsection

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	
	<div class="contents bg-white" id="tester_form">
		
	</div>
	
	<code-key id="codeKey" data="{{ $code_key }}"></code-key>
	<save-url id="saveUrl" data="{{ route('saveTestCode') }}"></save-url>
	<run-url id="runUrl" data="{{ route('runCode') }}"></run-url>
	<load-url id="loadUrl" data="{{ route('loadCode') }}"></load-url>
	<view-url id="viewUrl" data="{{ route('viewCode') }}"></view-url>
	<delete-url id="deleteUrl" data="{{ route('deleteCode') }}"></delete-url>
</div>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/vue/jstester/view.js'])
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.26.1/min/vs/loader.min.js"></script>
<script>
require.config({
	paths: {
	  'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.26.1/min/vs'
	}
});

require(["vs/editor/editor.main"], () => {
	monaco.editor.create(document.getElementById('html_area'), {
		value: '',
		language: 'html',
		theme: 'vs-dark',
	});
	monaco.editor.create(document.getElementById('css_area'), {
		value: '',
		language: 'css',
		theme: 'vs-dark',
	});
	monaco.editor.create(document.getElementById('js_area'), {
		value: '',
		language: 'javascript',
		theme: 'vs-dark',
	});
});

</script>
-->
@endsection