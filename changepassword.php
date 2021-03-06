<?php
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn()){
	Redirect::to('index.php');
}

if(Input::exists()){
	if(Token::check(Input::get('token'))){
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'password_current' => array(
				'required' => true,
				'min' => 6
			),
			'password_new' => array(
				'required' => true,
				'min' => 6
			),
			'password_new_again' => array(
				'required' => true,
				'min' => 6,
				'matches' => 'password_new'
			)
		));

		if($validation->passed()){
			// change password
			if(Hash::make(Input::get('password_current'), $user->data()->salt) !== $user->data()->password){
				echo 'Your current password is wrong.';
			}else{
				$salt = Hash::salt(32);
			
				try{
					$user->update(array(
						'password' => Hash::make(Input::get('password_new'), $salt),
						'salt' => $salt
					));

					Session::flash('home', 'Your password has been updated.');
					Redirect::to('index.php');
				}catch(Exception $e){
					die($e->getMessage());
				}
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
			<label for="password_current">Current password:</label><br />
			<input type="password" name="password_current" id="password_current" />
		</p>
	</div>

	<div class="field">
		<p>
			<label for="password_new">New password:</label><br />
			<input type="password" name="password_new" id="password_new" />
		</p>
	</div>

	<div class="field">
		<p>
			<label for="password_new_again">New password again:</label><br />
			<input type="password" name="password_new_again" id="password_new_again" />
		</p>
	</div>

	<input type="submit" value="Change" />
	<input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />

</form>