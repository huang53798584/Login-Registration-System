<?php
require_once 'core/init.php';

if(Input::exists()){
	if(Token::check(Input::get('token'))){
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'username' => array('required' => true),
			'password' => array('required' => true)
		));

		if($validation->passed()){
			//Log user in
			$user = new User();

			$remember = (Input::get('remember') === 'on') ? true : false;
			$login = $user->login(Input::get('username'), Input::get('password'), $remember);

			if($login){
				Redirect::to('index.php');
			}else{
				echo '<p>Sorry, logging in failed.</p>';
			}
		}else{
			foreach($validation->errors() as $error){
				echo $error . '<br />';
			}
		}
	}
}
?>

<form action="" method="POST">
	<div class="field">
		<p>
			<label for="username">Username:</label><br />
			<input type="text" name="username" id="username" autocomplete="off" />
		</p>
	</div>

	<div class="field">
		<p>
			<label for="password">Password:</label><br />
			<input type="password" name="password" id="password" autocomplete="off" />
		</p>
	</div>

	<div class="field">
		<p>
			<label for="remember">
			<input type="checkbox" name="remember" id="remember" />Remember me
			</label>
		</p>
	</div>

	<input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
	<input type="submit" value="Log in" />

</form>