<nav class="navbar navbar-expand-md navbar-light">
	<a class="navbar-brand" href="/">Idea Factory</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="collapsibleNavbar">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('jsView') }}">짭피들러</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('board') }}">아이디어 보드</a>
			</li>
		</ul>
		<ul class="navbar-nav ml-auto">
			@if(!auth()->check())
			<li class="nav-item">
				<a class="nav-link" href="{{ route('login') }}">로그인</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('register') }}">가입</a>
			</li>
			@else
			<li class="nav-item">
				<a class="nav-link" href="{{ route('myView') }}">내페이지</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('logout') }}">로그아웃</a>
			</li>
			@endif
		</ul>
	</div>
</nav>