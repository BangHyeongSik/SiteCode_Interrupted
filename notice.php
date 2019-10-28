<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
<?php
		session_cache_limiter('public');
		session_start();
		require('essential.php');

		if (!empty($_GET['id'])) {
			if ($_GET['number'] == Null)
				header('Location: notice.php?id='.$_GET['id'].'&number=0');
			$sql = "SELECT * FROM notice WHERE id={$_GET['id']}";
			$result = sql_result($sql);
			$row = mysqli_fetch_array($result);
			if ($row['title'] == Null)
				header('Location: notice.php');
		}
	?>
	<title>
		<?php
			if (empty($_GET['id'])) {
				print_r("Introducing Yourself");
			} else {
				print_r("{$row['title']}");
			}
		?>
	</title>
</head>
<body>
	<div>
	<h1>Notice Board</h1>
	<?php
		if (empty($_GET['id'])) {
			if (!isset($_SESSION['ID']))
				print_r("<h3 id=\"warn\">You are not 'Login', So you only can see post.</h3>");
			else {
				$pname = htmlspecialchars($_SESSION['NAME']);
				print_r("<br><h3>Enjoy your time, {$pname}!</h3>");
			}

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

			$title = ['ID', 'Title', 'User', 'Created'];
			$data = ['id', 'title', 'user', 'created'];
			$size = [50, 375, 300, 160];
			$ex = [[false, 'center', ''],
			[true, 'left', "notice.php?"],
			[true, 'center', ''],
			[false, 'center', '']];

			$searchArray = ['Title', 'Description', 'User', 'Created'];

			if (isset($_POST['find'])) {
				$find = $_POST['find'];
				$desc = htmlspecialchars($_POST['desc']);
				if ($find == 'Title')
					$find = 'title';
				else if ($find == 'Description')
					$find = 'description';
				else if ($find == 'User')
					$find = 'user';
				else if ($find == 'Created')
					$find = 'created';
			} else {
				$find = '';
				$desc = '';
			}

			if (!isset($_POST['find']))
				$_POST['find'] = '';


			if (!isset($_POST['number']) || $_POST['number'] == '') $current_number = 0;
			else $current_number = $_POST['number'];

			if ( !empty($desc) )
				$fdesc = '#'.$desc;

			if ( $find == 'created' || $find == 'description' || $find == 'title')
				$fdesc ="%{$fdesc}%";

			if (!empty($_POST['ofind']) && $_POST['ofind'] != '')
				$ofind = $_POST['ofind'];
			else
				$ofind = '';
			
			$orderArray = ['ID', 'Title', 'User', 'Created'];
			
			if (empty($ofind) && $ofind == '')
				$ofind = 'ID';

			if (isset($ofind)) {
				if ($ofind == 'ID')
					$ofind = 'id';
				else if ($ofind == 'Title')
					$ofind = 'title';
				else if ($ofind == 'User')
					$ofind = 'user';
				else if ($ofind == 'Created')
					$ofind = 'created';
			} else {
				$ofind = '';
			}
			if (!isset($_POST['ofind']))
		                $_POST['ofind'] = '';


			
			$addon = (empty($desc) || $desc == '') ? " ORDER BY {$ofind} {$ordering} " : " WHERE {$find} LIKE '".addslashes($fdesc)."' ESCAPE '#' ORDER BY {$ofind} {$ordering} ";

			searchDevice('notice.php', $searchArray, $ordering, '', $_POST['find'], $desc, $orderArray, $_POST['ofind']);
			sqlOutput($title, $data, $size, 10, 'notice', $current_number, $ex, $addon);
			$lastNum = lastStandingNum('notice',  $addon);
			$lastPage = ceil($lastNum / 10) - 1;
			if ($lastPage == -1)
				$current_number = -1;
			print_r(($current_number+1)."/".($lastPage+1)."<br>");
			buttonArray('notice.php', $current_number, $lastNum, $ordering, $_POST['find'], $desc, '', $_POST['ofind']);

			print_r("<table style=\"text-align:center; margin:auto;\"><tr><td>");
			makeSimpleButton("mainpage.php", "Main", 75, false);
			echo "</td><td>";
			if (!empty($_SESSION['ID']) && $_SESSION['ID'] != Null) {
				print_r("<form action=\"postcontrol.php\" method=\"post\">
					<input type=\"hidden\" name=\"number\" value={$current_number}>
					<input type=\"hidden\" name=\"Type\" value=\"Create\">
					<input style=\"width:75px\"type=\"submit\" value=\"Create\"></form>");
			} else {
				makeSimpleButton("login.php", "Login", 75, false);
			}
			print_r("</td></tr></table>");
		} else {
			print_r("<table border=1 style=\"table-layout:fixed; width:70%; margin:auto; text-align:center;\"><tr>");
			print_r("<th style=\"width: 75px;\">ID</th>
				<th style=\"width: 400px;\">Title</th>
				<th style=\"width: 200px;\">User</th>
				<th style=\"width: 160px;\">Created</th></tr>");
			print_r("<tr><td style=\"text-align: center; \">{$row['id']}</td>
				<td style=\"width: 100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;\">".htmlspecialchars($row['title'])."</td>
				<td style=\"text-align: center; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;\">".htmlspecialchars($row['user'])."</td>
				<td>{$row['created']}</td></tr>");
			print_r("<tr style=\"height:500px;\"><td style=\"text-align: center;\">Main<br>Text</td>
				<td colspan=\"3\" style=\"vertical-align: top; text-align: left; white-space:pre-wrap; word-break:break-word; word-wrap:break-word;\">".htmlspecialchars($row['description'])."</td></tr>");
			print_r("</table>");
			print_r("<table>");
			print_r("<form action=\"notice.php\" method=\"post\">
				<input type=\"hidden\" name=\"number\" value=\"{$_GET['number']}\">
				<input style=\"width:150px;\" type=\"submit\" value=\"Back\">
				</form>");
			if($row['user'] == $_SESSION['NAME'] || $_SESSION['NAME'] == 'root') {
				print_r("<form action=\"postcontrol.php\" method=\"post\">
					<input type=\"hidden\" name=\"id\" value=\"{$_GET['id']}\">
					<input type=\"hidden\" name=\"number\" value=\"{$_GET['number']}\">
					<input type=\"hidden\" name=\"Type\" value=\"Edit\">
					<input style=\"width:150px\" type=\"submit\" value=\"Edit\"></form>");
				print_r("<form action=\"postcontrol.php\" method=\"post\">
					<input type=\"hidden\" name=\"id\" value=\"{$_GET['id']}\">
					<input type=\"hidden\" name=\"Title\" value=\"".stripslashes(htmlspecialchars($row['title']))."\">
					<input type=\"hidden\" name=\"Type\" value=\"Delete\">
					<input style=\"width:150px\" type=\"submit\" value=\"Delete\"></form>");
			}
			print_r("</table>");
		}
	?>
	</div>
</body>
</html>
