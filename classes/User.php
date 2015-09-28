<?php
class User{
	private $_db,
			$_data,
			$_sessionName,
			$_cookieName,
			$_isLoggedIn;

	public function __construct($user = null){
		$this->_db = DB::getInstance();

		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');

		if(!$user){
			if(Session::exists($this->_sessionName)){
				$user = Session::get(Config::get('session/session_name'));

				if($this->find($user)){
					$this->_isLoggedIn = true;
				}
			}
		}else{
			$this->find($user);
		}
	}	

	public function update($field = array(), $id = null){
		if(!$id && $this->isLoggedIn()){
			$id = $this->data()->id;
		}

		if(!$this->_db->update('users', $id, $field)){
			throw new Exception('There was a problem updating.');
		}
	}

	public function create($fields = array()){
		if(!$this->_db->insert('users', $fields)){
			throw new Exception('There was a problem creating an account.');
		}
	}

	public function find($user = null){
		if($user){
			$field = (is_numeric($user)) ? 'id' : 'username';
			$data = $this->_db->get('users', array($field, '=', $user));

			if($data->count()){
				$this->_data = $data->first();
				return true;
			}
		}

		return false;
	}

	public function login($username = null, $password = null, $remember = false){
		// remember me login
		if(!$username && !$password && $this->exists()){
			//log user in
			Session::put($this->_sessionName, $this->data()->id);
		// submit form login
		}else{
			$user = $this->find($username);
			
			if($user){
				if($this->data()->password === Hash::make($password, $this->data()->salt)){
					//log in successfully, set a session
					Session::put($this->_sessionName, $this->data()->id);

					if($remember){
						$hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));

						if(!$hashCheck->count()){
							$hash = Hash::unique();
							$this->_db->insert('users_session', array(
								'user_id' => $this->data()->id,
								'hash' => $hash
							));
						}else{
							$hash = $hashCheck->first()->hash;
						}

						Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
					}

					return true;
				}
			}
		}

		return false;
	}

	public function exists(){
		return (!empty($this->_data)) ? true : false;
	}

	public function logout(){
		// remove data from database
		$this->_db->delete('users_session', array('user_id', '=', $this->data()->id));
		
		Session::delete($this->_sessionName);
		Cookie::delete($this->_cookieName);
	}

	public function data(){
		return $this->_data;
	}

	public function isLoggedIn(){
		return $this->_isLoggedIn;
	}
}