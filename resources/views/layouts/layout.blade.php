<!DOCTYPE html>
<html lang={{ env('APP_LOCALE', 'ko') }}>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>@yield('title', 'Idea Factory')</title>
		<link rel="stylesheet" href="/css/reset.css" />
		<link rel="stylesheet" href="/css/bootstrap.core.css" />
		@vite('resources/css/app.css')
		@yield('heads')
	</head>
	<body @yield('body_class')>
		@yield('contents')
	</body>
	@yield('scripts')
<html>