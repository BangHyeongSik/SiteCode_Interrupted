<!DOCTYPE html>
<script type="text/javascript">
	function fn_press_han(key) {
		if(event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46) return;
		key.value = key.value.replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g, "");
	}
</script>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src=http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js></script>
	<title>Introducing Yourself</title>
</head>
<body>
	<div>
	<?php
		session_cache_limiter("private_no_expire");
		session_start();
		require('essential.php');

		if (!empty($_SESSION['ID']) && $_SESSION['ID'] != Null)
			header('Location: mainpage.php');
		if (!empty($_POST['NNAME']) && !empty($_POST['NID']))
			$sql = "SELECT id FROM userinfo WHERE uname='".addslashes($_POST['NNAME'])."' OR uid='".addslashes($_POST['NID'])."'";

		if (empty($_POST['NID']) || empty($_POST['NPW']) || empty($_POST['NCOPW']) || empty($_POST['NNAME']) || strpos($_POST['NID'], ' ') || strpos($_POST['NPW'], ' ') || strpos($_POST['NNAME'], ' '))
			$return = 'Null';
		else if (mysqli_fetch_array(sql_result($sql))['id'])
			$return = 'IDNAME';
		else if ($_POST['NPW'] != $_POST['NCOPW'])
			$return = 'PW';
		else if (mb_strlen($_POST['NNAME'], 'utf-8') > 24)
			$return = 'MNAME';
		else if (mb_strlen($_POST['NID'], 'utf-8') > 16)
			$return = 'MID';
		else if (mb_strlen($_POST['NPW']) < 6)
			$return = 'LPW';
		else if (mb_strlen($_POST['NPW']) > 16)
			$return = 'MPW';
	?>
	<h1>Register Your Own Account!</h1>
	<h2>Let's make your account!</h2>
	
	<?php
		if (!empty($_POST['re']) && $_POST['re'] == "true") {
			print_r(errorAlert('register', $return));
		}

		if ($return == Null) {
			log_insert($_POST['NID'], 'Account Created','');
			$sql = "INSERT INTO userinfo (uid,upw,uname,created) VALUES('".addslashes($_POST['NID'])."', '".addslashes($_POST['NPW'])."', '".addslashes($_POST['NNAME'])."', NOW())";
			sql_result($sql);
			$sql = "INSERT INTO profile (uid, note) VALUES('".addslashes($_POST['NID'])."', 'Let\'s Introduce Yourself!')";
			sql_result($sql);
			$_SESSION['return'] = 'First';
			header('Location: login.php');
		}
?>

	<form action="register.php" method="post">
		<input id="uname" class="button" maxlength="24" type="test" name="NNAME" placeholder="Username (Less than 24)"><br>
		<input id="uid" style="ime-mode: disabled;" class="button" onkeyup="fn_press_han(this);" onfocus="fn_press_han(this);" maxlength="16" type="text" name="NID" placeholder="ID (Less than 16)"><br>
		<input id="upass" class="button" maxlength="16" type="password" name="NPW" placeholder="Password (6-16)"><br>
		<input id="uconpass" class="button" maxlength="16" type="password" name="NCOPW" placeholder="Confirm Password"><br>
		<input type="hidden" name="re" value="true">
		<input id="register" style="width: 150px;" type="submit" value="Enter">
	</form>
	
	<?php
		makeSimpleButton("login.php", "Cancel", 150, false);
	?>
	<br>
	<span style="color: red;">Warning : If you <strong>DELETE</strong> your account, All your post will be <strong>REMOVED!</strong></span>
	</div>
</body>
</html>
