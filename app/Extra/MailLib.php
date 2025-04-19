<?php

namespace App\Extra;

use Illuminate\Support\Facades\Mail;
use App\Mail\AuthenticateMail;
use App\Mail\TempPasswordMail;
use App\Mail\findIdMail;

class MailLib
{
	public function __construct() {}
	
	public function sendAuthMail(array $data)
	{
		Mail::to($data['email'])->send(new AuthenticateMail(['code' => $data['token']]));
	}
	
	public function tempPassword(array $data)
	{
		Mail::to($data['email'])->send(new TempPasswordMail(['password' => $data['password']]));
	}
	
	public function findId(array $data)
	{
		Mail::to($data['email'])->send(new findIdMail(['id' => $data['id']]));
	}
}
