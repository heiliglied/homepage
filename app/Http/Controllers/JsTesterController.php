<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Extra\TesterLib;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class JsTesterController extends Controller
{
    public function __construct() {}
	
	public function view(Request $request)
	{
		return view('jstester.view', ['code_key' => $request->code_key ?? '']);
	}
	
	public function saveTestCode(Request $request)
	{
		$TesterLib = new TesterLib();
		
		return $TesterLib->saveTester(
			[
				'user_id' => Auth::user()->id ?? 0,
				'session_id' => $request->cookie,				
				'view_name' => $request->name,
				'html' => $request->html,
				'css' => $request->css,
				'js' => $request->js,
			]
		);
	}
	
	public function runCode(Request $request)
	{
		$html = $request->html ?? '';
		$css = $request->css ?? '';
		$js = $request->js ?? '';
		
		return View::make('jstester.load', ['html' => $html, 'css' => $css, 'js' => $js])->render();
	}
	
	public function loadCode(Request $request)
	{
		$page = $request->page ?? 1;
		$user_id = Auth::user()->id ?? 0;
		$session_id = $request->cookie ?? '';
		
		$TesterLib = new TesterLib();
		return $TesterLib->loadTester(
			[
				'page' => $page,
				'user_id' => $user_id,
				'session_id' => $session_id,
			]
		);
	}
	
	public function viewCode(Request $request)
	{
		$TesterLib = new TesterLib();
		$codes = $TesterLib->viewTester($request->id);
		
		$html = $codes['html'] == null ? '' : $codes['html'];
		$css = $codes['css'] == null ? '' : $codes['css'];
		$js = $codes['js'] == null ? '' : $codes['js'];
		
		return [
			'html' => $html,
			'css' => $css,
			'js' => $js,
			'view' => View::make('jstester.load', ['html' => $html, 'css' => $css, 'js' => $js])->render(),
		];
	}
	
	public function deleteCode(Request $request)
	{
		$TesterLib = new TesterLib();
		return $TesterLib->deleteTester($request->id);
	}
}
