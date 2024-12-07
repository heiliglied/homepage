<?php

namespace App\Extra;

use App\Services\TesterService;
use App\Traits\CommonTrait;

class TesterLib
{
	use CommonTrait;
	
	public function __construct() {}
	
	public function saveTester(array $data)
	{
		$TesterService = TesterService::getInstance();
		$i = 0;
		
		while(true) {			
			$new_code = $this->randomString('AZaz', 24);
			$duplicate_count = $TesterService->getDuplicateCode($new_code);
			if($duplicate_count < 1) {
				break;
			}
		}
		
		$code = json_encode(
			[
				'html' => $data['html'],
				'css' => $data['css'],
				'js' => $data['js'],
			]
		);
		
		$tester = [
			'user_id' => $data['user_id'],
			'session_id' => $data['session_id'],
			'page_key' => $new_code,
			'view_name' => $data['view_name'] ?? $new_code,
			'code' => $code,
		];
		
		try {
			$TesterService->save($tester);			
			return [
				'status' => 'success',
				'msg' => '코드가 저장 되었습니다.',
			];
		} catch(\Exception $e) {
			print_r($e->getMessage());
			exit;
			return [
				'status' => 'error',
				'msg' => '저장에 실패하였습니다.',
			];
		}
	}
	
	public function loadTester(array $data)
	{
		$TesterService = TesterService::getInstance();
		$list = [];
		$count = 0;
		$pageSize = 5;
		$showPages = 5;
		if($data['user_id'] == 0) {
			$list = $TesterService->getTesterList('session_id', $data['session_id'], ($data['page'] - 1) * $pageSize, $pageSize)->toArray();
			$count = $TesterService->getTesterCount('session_id', $data['session_id']);
		} else {
			$list = $TesterService->getTesterList('user_id', $data['user_id'], ($data['page'] - 1) * $pageSize, $pageSize)->toArray();
			$count = $TesterService->getTesterCount('user_id', $data['user_id']);
		}
		
		$paging_params = [
			'totalCount' => $count,
			'pageSize' => $pageSize,
			'showPages' => $showPages,
			'currentPage' => $data['page'],
			'skip' => true,
		];
		
		$pagination = $this->pagination($paging_params);
		
		return [
			'list' => $list,
			'paging' => $pagination,
		];
	}
	
	public function viewTester(int $id)
	{
		$TesterService = TesterService::getInstance();
		$testCode = $TesterService->getTesterCode($id);
		return json_decode($testCode->code, true);
	}
	
	public function deleteTester(int $id)
	{
		$TesterService = TesterService::getInstance();
		try {
			$TesterService->deleteTesterCode($id);
			return 'success';
		} catch(\Exception $e) {
			return 'error';
		}
	}
}
