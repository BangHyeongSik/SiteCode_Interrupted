<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Introducing Yourself</title>
</head>
<?php
	session_cache_limiter("private_no_expire");
	session_start();
	require('essential.php');

	function isRight($userID, $userPW) {
		$userID = addslashes($userID);
		$userPW = addslashes($userPW);
		$sql = "SELECT upw,uname FROM userinfo WHERE uid='".$userID."'";
		$result = sql_result($sql);
		$row = mysqli_fetch_array($result);
		$rightPW = addslashes($row['upw']);
		log_insert($userID, 'Login Attempt', '');
		$lResult = $rightPW == $userPW;
		if ($lResult) {
			log_insert($userID, 'Login Success', '');
			$_SESSION['NAME'] = $row['uname'];
		}
		return $lResult;
	}
	
?>
<body>
	<div>
	<h1>Welcome To 'Introducing Yourself' Website!</h1>
	<h2>Login</h2>
<?php
		if (!empty($_POST['ID']) && !empty($_POST['PW'])) {
			$check = isRight($_POST['ID'], $_POST['PW']);
			if ($check) {
				$_SESSION['ID'] = $_POST['ID'];
			} else
				$return = 'Failed';
		} else {
			$return = 'Null';
		}

		if (!empty($_SESSION['return'])) {
			$return = $_SESSION['return'];
			$_SESSION['return'] = Null;
			$_POST['re'] = 'true';
		} else if (!empty($_POST['return'])) {
			$return = $_POST['return'];
			$_POST['re'] = 'true';
			if ($return == 'Logout') {
				$_SESSION['ID'] = Null;
				$_SESSION['NAME'] = Null;
			}
		}
		


		if (!empty($_SESSION['ID']))
			header('Location: mainpage.php');
		else if (!empty($_POST['re']) && $_POST['re'] == "true"){
			print_r(errorAlert('login', $return));
			if (!empty($_SESSION['FPW']))
				$_SESSION['FPW'] = Null;
		}
	?>
	<form action="login.php" method="post">
		<input type="hidden" name="re" value="true">
		<table style="margin: auto; text-align: center;"> <tr> <td>
				<input class="button" style="ime-mode: disabled;" tabindex="1" onkeyup="fn_press_han(this);" onfocus="fn_press_han(this);"  maxlength="16" type="text" name="ID" placeholder="ID">
			</td> <td rowspan="2">
				<input style="width:auto; height:50px" type="submit" value="Login">
			</td> </tr>
			<tr> <td>
				<input class="button" tabindex="2" maxlength="16" type="password" name="PW" placeholder="Password">
		</td> </tr> </table>
	</form>


	<?php
		makeSimpleButton("register.php", "Register", 200, false);
		makeSimpleButton("notice.php", "NoticeBoard", 200, true);
	?>
	<h5><a id = "warn" href="findpass.php">Do you forget your password?</a></h5>
	</div>
		<script type="text/javascript">
		function fn_press_han(key) {
			if(event.KeyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.KeyCode == 39 || event.keyCode == 46) return;
			key.value = key.value.replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g, "");
		}
		</script>
		
</body>
</html>
