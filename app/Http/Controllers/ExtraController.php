<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Extra\JobLib;

class ExtraController extends Controller
{
    public function __construct() {}
	
	public function csrfReset(Request $request)
	{
		session()->regenerate();
		return response()->json(['token' => csrf_token()]);
	}
	
	public function getAsyncData(Request $request)
	{
		header('Content-Type: application/json; charset=utf-8');
		header('Connection: keep-alive'); // 연결 유지
		header('Cache-Control: no-cache'); // 캐시 방지
		header('X-Accel-Buffering: no'); // 이 요청만 nginx 버퍼링 끔 gzip on/off 상관없이 동작함.
		
		ini_set('output_buffering', 'off');
		ini_set('zlib.output_compression', 'Off');
		while (ob_get_level()) {
			ob_end_flush();
		}
		for($i = 0; $i < 10; $i++) {
			$data = json_encode(['status' => 'loading', 'msg' => $i]);
			echo $data . "\r\n"; // 청크 데이터
			flush();
			sleep(1); // 작업 지연
		}
		
		$endData = json_encode(['status' => 'finish', 'msg' => 'E.N.D']);
		echo $endData . "\r\n";
		flush();
	}
	
	public function likepubsub(Request $request)
	{
		$parameter = $request->json()->all();
		$jobkey = $parameter['jobkey'] ?? '';
		$type = $parameter['type'] ?? '';
		
		if($jobkey == '' || $type == '') {
			return [
				'status' => 'error',
				'msg' => '파라미터가 없습니다.',
			];
		}
		
		$JobLib = new JobLib();
		if($type == 'create') {
			return $JobLib->createData($jobkey);
		} else if($type == 'check') {
			return $JobLib->jobCheck($jobkey);
		} else {
			return $JobLib->deleteJob($jobkey);
		}
	}
}
