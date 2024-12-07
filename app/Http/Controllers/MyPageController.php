<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Extra\AuthLib;

class MyPageController extends Controller
{
    public function __construct() {}
	
	public function my(Request $request)
	{
		return view('my.view');
	}
	
	public function profile(Request $request)
	{
		$AuthLib = new AuthLib();
		$user = $AuthLib->getUserData(Auth::user()->id);
		
		return json_encode($user);
	}
	
	public function update(Request $request)
	{
		$user = [
			'uid' => Auth::user()->id,
			'name' => $request->name,
			'password_change' => $request->password_change ?? 'N',
			'password' => $request->password ?? '',
			'password_confirmation' => $request->password_confirmation ?? '',
			'email' => $request->email,
		];
		
		$AuthLib = new AuthLib();
		return $AuthLib->updateUserData($user);
	}
}
