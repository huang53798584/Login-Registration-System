<?php
require_once 'core/init.php';

if(Input::exists()){
	if(Token::check(Input::get('token'))){
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'username' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
				'unique' => 'users',
				'numstart' => true
			),
			'password' => array(
				'required' => true,
				'min' => 6		
			),
			'password_again' => array(
				'required' => true,
				'matches' => 'password'
			),
			'name' => array(
				'required' => true,
				'min' => 2,
				'max' => 50
			)
		));

		if($validation->passed()){
			// register user
			$user = new User();

			$salt = Hash::salt(32);

			try{
				$user->create(array(
					'username' => Input::get('username'),
					'password' => Hash::make(Input::get('password'), $salt),
					'salt' => $salt,
					'name' => Input::get('name'),
					'joined' => date('Y-m-d H:i:s')
				));

				Session::flash('home', 'You have been registered and can now log in!');
				Redirect::to('index.php');
			}catch(Exception $e){
				echo '<p>Sorry, logging in failed</p>';
			}
		}else{
			foreach($validation->errors() as $error){
				echo $error, '<br />';
			}	
		}
	}
}
?>

<form action="" method="POST">
	<div class="field">
		<p>
			<label for="username">Username*:</label><br />
			<input type="text" name="username" id="username" value="<?php echo escape(Input::get('username')); ?>" autocomplete="off">
		</p>
	</div>

	<div class="field">
		<p>
			<label for="password">Enter password*:</label><br />
			<input type="password" name="password" id="password">
		</p>
	</div>

	<div class="field">
		<p>
			<label for="password_again">Re-enter password*:</label><br />
			<input type="password" name="password_again" id="password_again">
		</p>
	</div>

	<div class="field">
		<p>
			<label for="name">Name*:</label><br />
			<input type="text" name="name" id="name" value="<?php echo escape(Input::get('name')); ?>">
		</p>
	</div>

	<input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
	<input type="submit" value="Register" />
</form>