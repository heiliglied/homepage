@extends('layouts.layout')

@section('contents')
<div class="wrapper bg-white">
	<div class="navigation bg-aliceblue">
	@include('layouts.partial.nav')
	</div>
	<div class="contents bg-white">
		<div class="titlebox">
			<div class="welcome">
				Welcome<br/> To Idea Factory
			</div>
		</div>
	</div>
</div>
@endsection
