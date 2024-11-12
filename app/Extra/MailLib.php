<?php

namespace App\Extra;

use Illuminate\Support\Facades\Mail;
use App\Mail\AuthenticateMail;

class MailLib
{
	public function __construct() {}
	
	public function sendAuthMail(array $data)
	{
		Mail::to($data['email'])->send(new AuthenticateMail(['code' => $data['token']]));
	}
}
