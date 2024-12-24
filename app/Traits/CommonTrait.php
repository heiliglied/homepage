<?php

namespace App\Traits;

use Illuminate\Support\Facades\View;

trait CommonTrait
{
	public function randomString($type = '', $len = 10)
	{
		$lowercase = 'abcdefghijklmnopqrstuvwxyz';
		$uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$numeric = '0123456789';
		$special = '!@#$%^&-_';
		$key = '';
		$token = '';
		if ($type == '') {
			$key = $lowercase.$uppercase.$numeric;
		} else {
			if (strpos($type,'09') > -1) $key .= $numeric;
			if (strpos($type,'az') > -1) $key .= $lowercase;
			if (strpos($type,'AZ') > -1) $key .= $uppercase;
			if (strpos($type,'$') > -1) $key .= $special;
		}
		for ($i = 0; $i < $len; $i++) {
			$token .= $key[mt_rand(0, strlen($key) - 1)];
		}
		return $token;
	}

    function gen_uuid_v4() {
		return sprintf('%08x-%04x-%04x-%04x-%04x%08x',
			mt_rand(0, 0xffffffff),
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff), mt_rand(0, 0xffffffff)
		);
	}
	
	public function b_agination(array $parameters) : array
	{
		/*
		parameters 
		totalCount = 총 목록 수.
		pageSize = 한 페이지에 보여줄 목록 수
		showPages = 보여줄 페이지 수.
		currentPage = 현재 페이지 위치.
		arrow = prev, next 화살표 추가.
		skip = first, last 페이지 이동 추가.
		template = 템플릿 사용 유무.
		script = 호출할 자바스크립트가 있을 경우 스크립트명.
		*/
		
		$pageSize = $parameters['pageSize'] ?? 10;
		$showPages = $parameters['showPages'] ?? 5;
		$currentPage = $parameters['currentPage'] ?? 1;
		$arrow = $parameters['arrow'] ?? true;
		$skip = $parameters['skip'] ?? true;
		$template = $parameters['template'] ?? '';
		$script = $parameters['script'] ?? '';
		
		$result = [
			'status' => 'success',
			'pages' => [],
			'totalPage' => 0,
			'currentPage' => $currentPage,
			'template' => '',
			'first' => false,
			'last' => false,
			'prev' => false,
			'next' => false,
		];
		
		try {
			$totalCount = $parameters['totalCount'] ?? 0;		
			if($totalCount == 0) {
				throw new \Exception;
			}
			
			$totalPage = ceil($totalCount / $pageSize);
			$result['totalPage'] = $totalPage;
			$pages = [];
			
			if($totalPage <= $showPages) {
				for($i = 1; $i <= $totalPage; $i++)
				{
					$pages[] = $i;
				}
			} else {
				$startPage = floor(($currentPage - 1) / $showPages) * $showPages + 1;
				$endPage = min($startPage + $showPages - 1, $totalPage);
				$currentBlock = ceil($currentPage / $showPages);
				$allBlock = ceil($totalPage / $showPages);
				
				//현재 페이지가 1보다 클 때.
				if($currentPage > 1) {
					if(($currentBlock > 1) && $skip == true) {
						$result['first'] = true;
					}
					
					//이전 페이지 화살표 추가
					if($arrow == true) {
						$result['prev'] = true;
					}
				}
				
				for($i = $startPage; $i <= $endPage; $i++) {
					array_push($pages, $i);
				}
				
				//현재 페이지가 마지막 페이지보다 작을 때.
				if($currentPage < $totalPage) {
					if($arrow == true) {
						$result['next'] = true;
					}
					
					if(($currentBlock < $allBlock) && $skip == true) {
						$result['last'] = true;
					}
				}
			}
			
			if($template != '') {
				$result['template'] = View::make($template, ['result' => $result])->render();
			}
			
			return $result;
			
		} catch(\Exception $e) {
			$result['status'] = 'error';
			return $result;
		}
	}
	
	public function pagination(array $parameters) : array
	{
		/*
		parameters 
		totalCount = 총 목록 수.
		pageSize = 한 페이지에 보여줄 목록 수
		showPages = 보여줄 페이지 수.
		currentPage = 현재 페이지 위치.
		arrow = prev, next 화살표 추가.
		skip = first, last 페이지 이동 추가.
		template = 템플릿 사용 유무.
		script = 호출할 자바스크립트가 있을 경우 스크립트명.
		*/
		
		$pageSize = $parameters['pageSize'] ?? 10;
		$showPages = $parameters['showPages'] ?? 5;
		$currentPage = $parameters['currentPage'] ?? 1;
		$arrow = $parameters['arrow'] ?? true;
		$skip = $parameters['skip'] ?? true;
		$template = $parameters['template'] ?? '';
		$script = $parameters['script'] ?? '';
		
		$result = [
			'status' => 'success',
			'pages' => [],
			'totalPage' => 0,
			'currentPage' => $currentPage,
			'template' => '',
			'first' => false,
			'last' => false,
			'prev' => false,
			'next' => false,
		];
		
		try {
			$totalCount = $parameters['totalCount'] ?? 0;		
			if($totalCount == 0) {
				throw new \Exception;
			}
			
			$totalPage = ceil($totalCount / $pageSize);
			$result['totalPage'] = $totalPage;
			$pages = [];
			
			if($totalPage <= $showPages) {
				for($i = 1; $i <= $totalPage; $i++)
				{
					$pages[] = $i;
				}
			} else {
				$endPage = $showPages >= $currentPage ? $showPages : $currentPage;
				$endLoop = $endPage > $totalPage ? $totalPage : $endPage;
				$startPage = $endPage - $showPages + 1;
				$startLoop = $startPage < 1 ? 1 : $startPage;
				
				//현재 페이지가 1보다 클 때.
				if($currentPage > 1) {
					//현재 페이지가 2 블럭 이상일 때 첫페이지 이동 작성.
					if((ceil($currentPage / $showPages) > 1) && $skip == true) {
						array_push($pages, 'first');
						$result['first'] = true;
					}
					
					//이전 페이지 화살표 추가
					if($arrow == true) {
						array_push($pages, 'prev');
						$result['prev'] = true;
					}
				}
				
				//페이지 배열 추가
				for($i = $startLoop; $i <= $endLoop; $i++) {
					array_push($pages, $i);
				}
				
				//현재 페이지가 마지막 페이지보다 작을 때
				if($currentPage < $totalPage) {
					//다음 페이지 화살표 추가
					if($arrow == true) {
						array_push($pages, 'next');
						$result['next'] = true;
					}
					
					//현재 페이지 블럭이 마지막 블럭보다 작을 떄 
					$lastBlockStart = floor(($totalPage - 1) / $showPages) * $showPages + 1; 
					if(($currentPage < $lastBlockStart) && $skip == true) {
						array_push($pages, 'last');
						$result['last'] = true;
					}
				}
			}
			
			$result['pages'] = $pages;
			
			if($template != '') {
				$result['template'] = View::make($template, ['result' => $result])->render();
			}
			
			return $result;
			
		} catch(\Exception $e) {
			$result['status'] = 'error';
			return $result;
		}
	}
}