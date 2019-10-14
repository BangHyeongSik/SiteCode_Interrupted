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

		if (!empty($_SESSION['ID'])) {
			print_r("<h1>Welcome '".htmlspecialchars($_SESSION['NAME'])."'!</h1>");
			print_r("<h3>Here is the function for you!</h3>");
			makeSimpleButton("notice.php", "NoticeBoard", 150, true);
			makeSimpleButton("artist.php", "DrawingBoard", 150, false);
			echo "<form action=\"profile.php?id=".$_SESSION['ID']."\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"Type\" value=\"View\">";
			echo "<input class=\"button\" type=\"submit\" value=\"My Profile\">";
			echo "</form>";
			print_r("<form action=\"login.php\" method=\"post\">
				<input type=\"hidden\" name=\"return\" value=\"Logout\">
				<input class=\"button\" type=\"submit\" value=\"Logout\">
				</form>");

			if ($_SESSION['ID'] != 'root') {
				print_r("<form action=\"accountcontrol.php\" method=\"post\">
					<input type=\"hidden\" name=\"Type\" value=\"Change\">
					<input class=\"button\" type=\"submit\" value=\"ChangeAccount\">
					</form>");
				print_r("<form action=\"accountcontrol.php\" method=\"post\">
					<input type=\"hidden\" name=\"Type\" value=\"Delete\">
					<input class=\"button\" type=\"submit\" value=\"DeleteAccount\">
					</form>");
			} else {
				print_r("<form action=\"adminSite.php\" method=\"post\">
					<input type=\"hidden\" name=\"Type\" value=\"log\">
					<input type=\"hidden\" name=\"number\" value=\"0\">
					<input class=\"button\" type=\"submit\" value=\"LogHistory\">
					</form>");
				print_r("<form action=\"adminSite.php\" method=\"post\">
					<input type=\"hidden\" name=\"Type\" value=\"userinfo\">
					<input type=\"hidden\" name=\"number\" value=\"0\">
					<input class=\"button\" type=\"submit\" value=\"Userlist\">
					</form>");
			}
		} else {
			header('Location: login.php');
		}
	?>
	</div>
</body>
</html>
