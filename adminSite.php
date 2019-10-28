<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>AdminSite</title>
</head>
<body>
	<div>
	<?php
		session_cache_limiter("private_no_expire");
		session_start();
		require('essential.php');

		if ($_SESSION['ID'] != 'root' || !isset($_POST['Type'])) {
			header('Location: mainpage.php');
		} else {
			if (!empty($_POST['Change']) && $_POST['Change'] == 'True'){
				if ($_POST['Type'] == 'log') {
					$_POST['Type'] = 'userinfo';
				} else if ($_POST['Type'] == 'userinfo') {
					$_POST['Type'] = 'log';
				}
			}
		}
		print_r("<h1>AdminSite - {$_POST['Type']}</h1>");
		print_r("<h2>Hello, Administrator! What do you want to do?</h2>");

		if (isset($_POST['find'])) {
			$find = $_POST['find'];
			$desc = htmlspecialchars($_POST['desc']);
			if ($_POST['find'] == 'IP')
				$find = 'uip';
			else if ($_POST['find'] == 'User ID')
				$find = 'uid';
			else if ($_POST['find'] == 'Logged Date')
				$find = 'log_date';
			else if ($_POST['find'] == 'User Name')
				$find = 'uname';
			else if ($_POST['find'] == 'Registered Date')
				$find = 'created';
		} else {
			$find = 'uip';
			$desc = '';
			$_POST['find'] = 'IP';
		}

		if (empty($_POST['ofind']) || $_POST['ofind'] == '') {
	        	$ofind = 'ID';
			$_POST['ofind'] = 'ID';
		} else
			$ofind = $_POST['ofind'];

		if (empty($_POST['anofind']) || $_POST['anofind'] == '')
			$_POST['anofind'] = $_POST['ofind'];

		if (empty($_POST['order']) || $_POST['ofind'] != $_POST['anofind']) {
			$ordering = 'DESC';
		} else if (!empty($_POST['Change']) && $_POST['Change'] == 'true') {
			if ($_POST['order'] == 'DESC') {
				$ordering = 'ASC';
			}
			else if ($_POST['order'] == 'ASC') {
				$ordering = 'DESC';
			}
		} else {
			$ordering = $_POST['order'];
		}
		if (!empty($desc))
			$fdesc = '#'.$desc;
		else 
			$fdesc = '';
		if ( $find == 'log_date' || $find == 'created' || $find == 'kind' || $find == 'details' )
			$fdesc = "%{$fdesc}%";
		
		
		if ($_POST['Type'] == 'log')
                        $orderArray = ["ID", "IP", "User ID", "Kind", "Logged Date"];
                else if ($_POST['Type'] == 'userinfo')
                        $orderArray = ["ID", "User ID", "User Name", "Registered Date"];

		if (isset($ofind)) {
			if ($ofind == 'ID')
				$ofind = 'id';
			else if ($ofind == 'IP')
				$ofind = 'uip';
			else if ($ofind == 'User ID')
				$ofind = 'uid';
			else if ($ofind == 'Kind')
				$ofind = 'kind';
			else if ($ofind == 'Logged Date')
				$ofind = 'log_date';
			else if ($ofind == 'User Name')
				$ofind = 'uname';
			else if ($ofind == 'Registered Date')
				$ofind = 'created';
			}
		
		$addon = ((empty($desc) || $desc == '')) ? " ORDER BY {$ofind} {$ordering} " : " WHERE {$find} LIKE '".addslashes($fdesc)."' ESCAPE '#' ORDER BY {$ofind} {$ordering} ";
		if (!isset($_POST['number']) || $_POST['number'] == '') $current_number = 0;
		else $current_number = $_POST['number'];


		if ($_POST['Type'] == 'log')
			$option = ["IP", "User ID", "Kind", "Details", "Logged Data"];
		else if ($_POST['Type'] == 'userinfo')
			$option = ["User ID", "User Name", "Registered Date"];

		$chbtn = ($_POST['Type'] == 'log') ? 'UserList' : 'LogHistory';
		print_r("<form action=\"adminSite.php\" method=\"post\">
			<input type=\"hidden\" name=\"Change\" value=\"True\">
			<input type=\"hidden\" name=\"Type\" value=\"{$_POST['Type']}\">
			<input type=\"submit\" value=\"Go to {$chbtn}\">
			</form>");

		if ($_POST['Type'] == 'log') {
			$title = ['ID', 'IP', 'User ID', 'Kind', 'Details', 'Logged Date'];
			$data = ['id', 'uip', 'uid', 'kind', 'details', 'log_date'];
			$size = [50, 110, 200, 150, 400, 175];
			$ex = [[false, 'center', ''],
					[false, 'center', ''],
					[true, 'center', ''],
					[false, '', ''],
					[true, '', ''],
					[false, '', '']];
			$height = 20;
		} else if ($_POST['Type'] == 'userinfo') {
			$title = ['ID', 'User ID', 'User Password', 'Username', 'Registered Date'];
			$data = ['id', 'uid', 'upw', 'uname', 'created'];
			$size = [50, 200, 200, 300, 175];
			$ex = [[false, 'center', ''],
					[true, 'center', ''],
					[true, 'center', ''],
					[true, 'center', ''],
					[false, '', '']];
			$height = 20;
		}
		$desc = htmlspecialchars($desc);
		searchDevice('adminSite.php', $option, $ordering, $_POST['Type'], $_POST['find'], $desc, $orderArray, $_POST['ofind']);
		sqlOutput($title, $data, $size, $height, $_POST['Type'], $current_number, $ex, $addon);
		$lastNum = lastStandingNum($_POST['Type'],  $addon);
		$lastPage = ceil($lastNum / 10) - 1;
		if ($lastPage == -1)
				$current_number = -1;
		print_r(($current_number+1)."/".($lastPage+1)."<br>");
		buttonArray('adminSite.php', $current_number, $lastNum, $ordering, $_POST['find'], $desc, $_POST['Type'], $_POST['ofind']);

		makeSimpleButton("mainpage.php", "Main", 100, false);
	?>
	</div>
</body>
</html>
