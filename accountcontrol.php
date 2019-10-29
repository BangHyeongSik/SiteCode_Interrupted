<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Introducing Yourself</title>
</head>
<body>
	<div>
	<?php
		session_cache_limiter("private_no_expire");
		session_start();
		require('essential.php');

		if (!isset($_SESSION['ID']) || !isset($_POST['Type']))
			header('Location: mainpage.php');

		$sql = "SELECT upw FROM userinfo WHERE uid='".addslashes($_SESSION['ID'])."'";
		$PW = mysqli_fetch_array(sql_result($sql))['upw'];
		if (!empty($_POST['CNAME'])) {
			$sql = "SELECT id FROM userinfo WHERE uname='".addslashes($_POST['CNAME'])."'";
			$NAME = mysqli_fetch_array(sql_result($sql))['id'];
		}

		if (!empty($_POST['PW']) && $PW == $_POST['PW']) {
			if ($_POST['Type'] == 'Change') {
				if ((empty($_POST['CNAME']) && (empty($_POST['CPW']) || empty($_POST['CCPW']))) || empty($_POST['PW']) || strpos($_POST['CNAME'], ' ') || strpos($_POST['CPW'], ' '))
					$return = 'Null';
				else if ($_POST['CNAME'] == $_SESSION['NAME'])
					$return = 'Current';
				else if (!empty($NAME) || $_POST['CNAME'] == 'root')
					$return = 'Name';
				else if ($_POST['CPW'] != $_POST['CCPW'])
					$return = 'PW';
			}
		} else {
			$return = 'Wrong';
		}

		if ($_POST['Type'] == 'Change') {
			print_r("<h1>Change your account!</h1>");
			print_r("<h3>Enter what you want to change</h3>");
		}
		else if ($_POST['Type'] == 'Delete') {
			print_r("<h1>Thank you for using, See you later!</h1>");
			print_r("<h3>If you want to delete this account, Enter your password.</h3>");
		}

		if (!empty($_POST['re']) && $_POST['re'] == "true" && !empty($return)) {
			print_r(errorAlert('accountcontrol', $return));	
		}
		if (!isset($return)) {
			if ($_POST['Type'] == 'Change') {
				if (empty($_POST['CPW']))
					$_POST['CPW'] = $PW;
				if (empty($_POST['CNAME']))
					$_POST['CNAME'] = $_SESSION['NAME'];
				log_insert(addslashes($_SESSION['ID']),'Account Changed',"Old : ".addslashes($_SESSION['NAME']));
				$sql = "UPDATE userinfo SET upw='".addslashes($_POST['CPW'])."', uname='".addslashes($_POST['CNAME'])."' WHERE uname='".addslashes($_SESSION['NAME'])."'";
				sql_result($sql);
				$sql = "UPDATE notice SET user='".addslashes($_POST['CNAME'])."' WHERE user='".addslashes($_SESSION['NAME'])."'";
				sql_result($sql);
			} else if ($_POST['Type'] == 'Delete') {
				$sql = "DELETE FROM profile WHERE uid='".addslashes($_SESSION['ID'])."'";
				$result = sql_result($sql);
				$sql = "DELETE FROM userinfo WHERE uid='".addslashes($_SESSION['ID'])."'";
				$result = sql_result($sql);
				$sql = "SELECT id FROM notice WHERE user='".addslashes($_SESSION['NAME'])."'";
				$row = mysqli_num_rows(sql_result($sql));
				$sql = "DELETE FROM notice WHERE user='".addslashes($_SESSION['NAME'])."'";
				$result = sql_result($sql);
				log_insert(addslashes($_SESSION['ID']), "Account Deleted", "{$row}"." Post(s) Deleted");
			}
				$_SESSION['ID'] = Null;
				$_SESSION['NAME'] = Null;
				$_SESSION['return'] = $_POST['Type'];
				header('Location: mainpage.php');
		}


		if ($_POST['Type'] == 'Change') {
			print_r("<br>
			<input maxlength=24 id=\"newname\" class=\"button\" type=\"text\" name=\"CNAME\" placeholder=\"New Username\"><br>
			<input maxlength=16 id=\"pw\" class=\"button\" type=\"password\" name=\"PW\" placeholder=\"Current Password\"><br>
			<input maxlength=16 id=\"newpw\" class=\"button\" type=\"password\" name=\"CPW\" placeholder=\"New Password\"><br>
			<input maxlength=16 id=\"newcopw\" class=\"button\" type=\"password\" name=\"CCPW\" placeholder=\"Confirm New Password\"><br> ");
			echo "<button class=\"button\" onclick=\"accountCheck();\"> {$_POST['Type']} </button>";
		}
		else if ($_POST['Type'] == 'Delete') {
			print_r("<form action=\"accountcontrol.php\" method=\"post\">");
			print_r("<input class=\"button\" type=\"password\" name=\"PW\" placeholder=\"Password\"><br>");
			echo "<input type=\"hidden\" name=\"Type\" value=\"{$_POST['Type']}\">";
			echo "<input type=\"hidden\" name=\"re\" value=\"true\">";
			echo "<input class=\"button\" type=\"submit\" value=\"{$_POST['Type']}\">";
			echo "</form>";
		}

		makeSimpleButton("mainpage.php", "Back", 150, false);

		if ($_POST['Type'] == 'Delete')
			print_r("<span style=\"color: red;\">Warning : If you <strong>DELETE</strong> your account, All your post will be <strong>REMOVED!</strong></span>");
	?>
	</div>
	<script>
		function accountCheck() {
			var nname = document.getElementById('newname').value;
			var opw = document.getElementById('pw').value;
			var npw = document.getElementById('newpw').value;
			var conpw = document.getElementById('newcopw').value;

			if (nname.length > 24)
				alert("Enter a Username at most 24 characters");
			else if ((nname.length == 0 || npw.length != 0) && npw.length < 6)
				alert("Enter a Password at least 6 characters");
			else if (npw.length > 16)
				alert("Enter a Password at least 16 characters");
			else {
				var form = document.createElement('form');
				form.setAttribute('method', 'post');
				form.setAttribute('action', 'accountcontrol.php');
				var hiddenfield;
				var nameArray = ['Type', 're', 'CNAME', 'PW', 'CPW', 'CCPW'];
				var valueArray = ['Change', 'true', nname, opw, npw, conpw];
				for (var i=0;i < nameArray.length;i++) {
					hiddenfield = document.createElement('input');
					hiddenfield.setAttribute("type","hidden");
					hiddenfield.setAttribute('name',nameArray[i]);
					hiddenfield.setAttribute('value',valueArray[i]);
					form.appendChild(hiddenfield);
				}
				document.body.appendChild(form);
				form.submit();

			}
		}
	
	</script>

</body>
</html>
