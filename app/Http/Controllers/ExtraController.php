<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExtraController extends Controller
{
    public function __construct() {}
	
	public function csrfReset(Request $request)
	{
		session()->regenerate();
		return response()->json(['token' => csrf_token()]);
	}
}
