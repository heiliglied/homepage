<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Extra\AuthLib;
use App\Traits\CommonTrait;

//use MaxMind\Db\Reader as IpReader;

use App\Services\UserService;

class AuthController extends Controller
{
	use AuthenticatesUsers, CommonTrait;
	
    public function __construct() {}
	
	public function register(Request $request)
	{
		/*
		$reader = new IpReader(storage_path('app/GeoLite2-Country.mmdb'));
		$record = $reader->get($request->ip()); or ->country
		print_r($record['country']['iso_code']);
		exit;
		*/
		
		return view('auth.register');
	}
	
	public function login(Request $request)
	{
		return view('auth.login');
	}
	
	public function findUser(Request $request)
	{
		if($request->user_id == '') {
			return $status = 'void';
		}			
		
		$UserService = UserService::getInstance();
		$user = $UserService->findUser($request->user_id ?? '');
		
		$status = '';
		
		if($user) {
			$status = 'disable';
		} else {
			$status = 'enable';
		}
		
		return $status;
	}
	
	public function signUp(Request $request)
	{
		$valid_chk = $this->validator($request->all());

		if(!$valid_chk->fails()) {
			$default_rank = config('settings.default_user_rank');
			
			$user = [
				'user_id' => $request->user_id,
				'password' => $request->password,
				'roll' => 2,
				'email' => $request->email,
			];
			
			$AuthLib = new AuthLib();
			$result = $AuthLib->registerUser($user);
		} else {			
			return [
				'status' => 'error',
				'msg' => '가입에 실패하였습니다.',
			];
		}
	}
	
	public function signIn(Request $request)
	{
		$user_id = $request->user_id;
		$password = $request->password;
		
		if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            //return $this->sendLockoutResponse($request);
			return [
				'status' => 'error',
				'msg' => __('auth.throttle', ['seconds' => '60']),
			];
        }

		//Auth::attempt[]
        if ($this->attemptLogin($request)) {
			//$user = User::where('user_id', $request->user_id)->first();
			//$user->createToken('userToken')->plainTextToken;
            //return $this->sendLoginResponse($request);
			$intendedUrl = session()->get('url.intended', '/');
			
			//if(Auth::user()->roll > 1) {
				if(Auth::user()->email_verified_at == '' || Auth::user()->email_verified_at == '0000-00-00') {
					try 
					{
						//인증 코드를 포함한 메일 발송. 작성필요.
						$AuthLib = new AuthLib();
						$result = $AuthLib->emailAuthenticate(
							[
								'email' => Auth::user()->email,
								'token' => $this->randomString('AZaz09', 6),
							]
						);
						
						if($result['status'] == 'success') {
							$result['url'] = route('identify');
						}
						
						return $result;
						
					} catch(\Exception $e) {
						return [
							'status' => 'error',
							'msg' => '로그인에 실패하였습니다.',
						];
					}
				}
			//}
			
			return [
				'status' => 'success',
				'url' => $intendedUrl,
			];
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
	}
	
	public function findId(Request $request)
	{
		return view('auth.findId');
	}
	
	public function findPassword(Request $request)
	{
		return view('auth.findPassword');
	}
	
	public function findAuth(Request $request)
	{
		
	}
	
	public function identify(Request $request)
	{
		$user = Auth::user();
		
		return view('auth.identify', ['user' => $user]);
	}
	
	public function checkToken(Request $request)
	{
		$id = $request->id;
		$email = $request->email;
		$token = $request->token;
		
		$AuthLib = new AuthLib();
		$result = $AuthLib->checkToken(['id' => $id, 'email' => $email, 'token' => $token]);
		
		return $result;
	}
	
	public function logout(Request $request)
	{
		$this->guard()->logout();
		$request->session()->invalidate();
		
		return redirect('/');
		
		/*
		try {
			$this->guard()->logout();
			$request->session()->invalidate();
		} catch(\Exception $e) {
			return [
				'status' => 'error',
				'msg' => '오류가 발생하였습니다.'
			];
		}
		return [
			'status' => 'success',
			'msg' => '로그아웃 되었습니다.'
		];
		*/
	}
	
	private function validation(array $data)
	{
		return Validator::make($data, [
			'name' => ['required', 'string', 'max:20'],
            'user_id' => ['required', 'string', 'email', 'max:120', 'unique:users', 'regex:/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'], //영숫자 혼합. 대소문자 안가림.
		]);
	}
	
	protected function credentials(Request $request)
	{
		return [
			'user_id' => $request->user_id,
			'password' => $request->password,
		];
	}
	
	protected function sendFailedLoginResponse()
	{
		return [
			'status' => 'error',
			'msg' => '아이디, 혹은 비밀번호를 확인 해 주세요.'
		];
	}
	
	//$this->guard()->attempt()값에 사용하는 값들.
	protected function guard()
    {
        return Auth::guard('web');
    }
	
	//인증에 필요한 id값. 기본은 email
	public function username()
	{
		return 'user_id';
	}
}
