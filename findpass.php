<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Introducing Yourself</title>
</head>
<body>
	<div>
	<h1>You Forgot Your Password!</h1>
	<?php
		session_cache_limiter("private_no_expire");
		session_start();
		require('essential.php');

		if ($_SESSION['ID'])
			header('Location: mainpage.php');
		else {
			$sql = "SELECT id FROM userinfo WHERE uname='".addslashes($_POST['FNAME'])."' AND uid='".addslashes($_POST['FID'])."'";
			if (empty($_POST['FNAME']) || empty($_POST['FID']))
				$return = 'Null';
			else if ($_POST['FNAME'] == 'root' || $_POST['FID'] == 'root')
				$return = 'Root';
			else if (mysqli_fetch_array(sql_result($sql))['id'] == Null)
				$return = 'Wrong';
		}
	?>
	<h2>Enter the following info to find your password</h2>
	<?php
		if ($_POST['re'] == "true") {
			print_r(errorAlert('findpass', $return));
		} 
		if ($return == Null) {
			$sql = "SELECT upw FROM userinfo WHERE uid='".addslashes($_POST['FID'])."'";
			$result = sql_result($sql);
			$_SESSION['FPW'] = mysqli_fetch_array($result)['upw'];
			$_SESSION['return'] = 'Find';
			header('Location: login.php');
		}
	?>
	<form action="findpass.php" method="post">
		<input class="button" type="text" name="FNAME" placeholder="Username"> <br>
		<input style="ime-mode: disabled;" class="button" type="text" name="FID" placeholder="ID"> <br>
		<input type="hidden" name="re" value="true">
		<input style="width: 150px;" type="submit" value="Find">
	</form>
	<?php
		makeSimpleButton("login.php", "Back", 150, false);
	?>
	</div>
</body>
</html>
