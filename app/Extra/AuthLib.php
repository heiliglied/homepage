<?php

namespace App\Extra;

use App\Services\UserService;
use App\Extra\MailLib;

class AuthLib 
{
	public function __construct() {}
	
	public function registerUser(array $data)
	{
		$UserService = UserService::getInstance();
		$status = 'success';
		$msg = '가입이 승인 되었습니다.';
		
		$now = \Carbon\Carbon::now();
		
		try {
			$data['password_change_date'] = $now->toDateString();
			$UserService->registerUser($data);
			
			return [
				'status' => $status,
				'msg' => $msg,
			];
		} catch(\Exception $e) {			
			$status = 'error';
			$msg = '가입에 실패하였습니다.';
			$error_code = $e->errorInfo[1];
			if($error_code == 1062) {
				$msg = '중복된 ID입니다.';
			}
			
			return [
				'status' => $status,
				'msg' => $msg,
			];
		}
	}
	
	public function emailAuthenticate(array $data)
	{
		$UserService = UserService::getInstance();
		$MailLib = new MailLib();
		try {
			//리셋 코드에 비교용 값 저장.
			$UserService->createToken($data);
			$MailLib->sendAuthMail($data);
			
			return [
				'status' => 'success',
				'msg' => '인증 코드가 발송되었습니다.'
			];
		} catch(\Exception $e) {
			print_r($e->getMessage());
			exit;
			return [
				'status' => 'error',
				'msg' => '코드 발송에 실패하였습니다.'
			];
		}		
	}
	
	public function checkToken(array $data)
	{
		$now = \Carbon\Carbon::now();
		$UserService = UserService::getInstance();
		$token = $UserService->checkToken(
			[
				'email' => $data['email'],
				'token' => $data['token'],
				'created_at' => $now->subMinutes(30),
			]
		);
		
		if($token > 0) {
			$UserService->updateUser(
				$data['id'],
				[
					'email_verified_at' => $now->toDateTimeString(),
				]
			);
			
			return [
				'status' => 'success',
				'msg' => '회원 정보가 인증되었습니다.',
			];
		} else {			
			return [
				'status' => 'error',
				'msg' => '잘못된 인증 번호입니다.',
			];
		}
	}
}