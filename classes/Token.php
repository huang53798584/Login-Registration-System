<?php

// CSRF protection
class Token{
	// generate a random string and put it in $_SESSION['token']
	public static function generate(){
		return Session::put(Config::get('session/token_name'), base64_encode(openssl_random_pseudo_bytes(32)));
	}

	public static function check($token){
		$tokenName = Config::get('session/token_name');

		if(Session::exists($tokenName) && $token === Session::get($tokenName)){
			Session::delete($tokenName);
			return true;
		}

		return false;
	}
}