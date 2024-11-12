<?php 

return [
	'user_rank' => [
		'0' => 'admin',
		'1' => 'user',
	],
	
	'default_user_rank' => 1,
	
	'password_regex' => "/^(?=.*[a-zA-Z])((?=.*\d)(?=.*\W)).{8,20}$/",
];