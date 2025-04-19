<?php

namespace App\Extra;

use App\Services\UserService;
use App\Extra\MailLib;
use App\Traits\CommonTrait;

class AuthLib 
{
	use CommonTrait;
	
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
	
	public function getUserData(int $user_id)
	{
		$UserService = UserService::getInstance();
		return $UserService->getUserData($user_id);
	}
	
	public function updateUserData(array $user)
	{
		$update = [
			'name' => $user['name'],
			'email' => $user['email'],
		];
		
		if($user['password_change'] == 'Y') {
			if($user['password'] == '' || $user['password_confirmation'] == '') {
				return [
					'status' => 'error',
					'msg' => '비밀번호를 바르게 입력 해 주세요.',
				];
			}
			
			if($user['password'] != $user['password_confirmation']) {
				return [
					'status' => 'error',
					'msg' => '비밀번호가 일치하지 않습니다.',
				];
			}
			
			$update['password'] = bcrypt($user['password']);
			$update['password_change_date'] = date('Y-m-d');
		}
		
		$UserService = UserService::getInstance();
		
		try {
			$UserService->updateUser($user['uid'], $update);
			return [
				'status' => 'success',
				'msg' => '정보가 변경되었습니다.',
			];
		} catch(\Exception $e) {
			return [
				'status' => 'error',
				'msg' => '변경에 실패하였습니다.',
			];
		}
	}
	
	public function emailCheck(String $email)
	{
		$UserService = UserService::getInstance();
		$user = $UserService->emailCheck($email);
		
		if(!empty($user)) {
			$MailLib = new MailLib();
			$newPassword = $this->randomString('AZaz09', 10);
			try {
				$UserService->updateUser($user->id, ['password' => bcrypt($newPassword)]);
				$MailLib->tempPassword(['email' => $user->email, 'password' => $newPassword]);
			} catch(\Exception $e) {
				return [
					'status' => 'error',
					'msg' => '비밀번호 확인에 실패하였습니다.'
				];
			}
		}
		
		return [
			'status' => 'success',
			'msg' => '메일로 임시 비밀번호를 전송하였습니다.',
		];
	}
	
	public function idCheck(String $email)
	{
		$UserService = UserService::getInstance();
		$user = $UserService->emailCheck($email);
		
		if(!empty($user)) {
			$MailLib = new MailLib();
			try {
				$MailLib->findId(['email' => $user->email, 'id' => $user->user_id]);
			} catch(\Exception $e) {
				return [
					'status' => 'error',
					'msg' => '계정 확인에 실패하였습니다.'
				];
			}
		}
		
		return [
			'status' => 'success',
			'msg' => '메일로 ID를 전송하였습니다.',
		];
	}
}