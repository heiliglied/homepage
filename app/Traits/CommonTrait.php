<?php

namespace App\Traits;

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
	
	public function pagination(array $parameters) : array
	{
		/*
		parameters 
		totalCount = 총 페이지.
		pageSize = 한 페이지에 보여줄 목록 수
		showPages = 보여줄 페이지 수.
		currentPage = 현제 페이지 위치.
		skip = 화살표 버튼 추가 유무.
		*/		
		$totalPage = ceil($parameters['totalCount'] / $parameters['pageSize']);
		$pages = [];
		
		if($totalPage <= $parameters['showPages']) {
			for($i = 1; $i <= $totalPage; $i++)
			{
				$pages[] = $i;
			}
		} else {
			$endPage = $parameters['showPages'] >= $parameters['currentPage'] ? $parameters['showPages'] : $parameters['currentPage'];
			$endLoop = $endPage > $totalPage ? $totalPage : $endPage;
			$startPage = $endPage - $parameters['showPages'] + 1;
			$startLoop = $startPage < 1 ? 1 : $startPage;
			
			if($parameters['skip'] == false) {
				for($i = $startLoop; $i <= $endLoop; $i++)
				{
					$pages[] = $i;
				}
			} else {
				if($parameters['currentPage'] > 1) {
					array_push($pages, 'prev');
				}
				
				for($i = $startLoop; $i <= $endLoop; $i++) {
					array_push($pages, $i);
				}
				
				if($endLoop < $totalPage) {
					array_push($pages, 'next');
				}
			}
		}
		
		$result = [
			'totalPage' => $totalPage,
			'pages' => $pages,
		];
		
		return $result;
	}
}