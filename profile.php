<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Introducing Yourself</title>
</head>
<?php
	session_cache_limiter("private_no_expired");
	session_start();
	require('essential.php');
	if (!empty($_GET['id'])) {
		$_GET['id'] = urldecode($_GET['id']);
	}
?>
<script>
function sendPost(){
	var note = document.getElementById('note').value;
	var address = document.getElementById('address').value;
	var email = document.getElementById('email').value;
	var form = document.createElement("form");
	var submitSite = document.getElementById('emailCheck');
	var uid = <?php echo "\"".addslashes($_SESSION['ID'])."\""; ?>;
	var emailCheck = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	var special = /[\\'"]/g;

	note = note.replace(special, "\\$&");
	address = address.replace(special, "\\$&");
	email = email.replace(special, "\\$&");

	form.setAttribute("method", "Post");
	form.setAttribute("action", "profile.php?id="+uid);
	if (!emailCheck.test(email) && email != '') {
		alert("Invalid Email");
	} else if (note.length >= 300) {
		alert("Enter a Note at most 300 characters [Now:"+note.length+"]");
	} else if (address.length >= 50) {
		alert("Enter an Address at most 50 characters [Now:"+address.length+"]");
	} else if (email.length >= 50) {
		alert("Enter an E-mail at most 50 characters or Leave blank [Now:"+email.length+"]");
	} else { 
		var hiddenField;
		var nameArray = ['Type', 're', 'nnote', 'naddress', 'nemail'];
		var valueArray = ['View', 'true', note, address, email];
		for (var i = 0; i < nameArray.length; i++) {
			hiddenField = document.createElement("input");
			hiddenField.setAttribute("type", "hidden");
			hiddenField.setAttribute("name", nameArray[i]);
			hiddenField.setAttribute("value", valueArray[i]);
			form.appendChild(hiddenField);
		}

		form.target = self;
		document.body.appendChild(form);

		form.submit();
	}
	
}
</script>
<body>
	<div onselectstart='return false;'>
	<?php
		if (!isset($_SESSION['ID']) && $_SESSION['ID'] == '')
			header('Location: mainpage.php');

		$sql = "SELECT * FROM userinfo WHERE uid='".addslashes($_GET['id'])."'";
		$result = sql_result($sql);
		$row = mysqli_fetch_array($result);
		
//		if ($row['id'] == 0 && $_GET['id'] != 'root')
//			header('Location: mainpage.php');

		if (!isset($_POST['Type']) || $_POST['Type'] == ''){
			$_POST['Type'] = 'View';
		}
		if ($_POST['Type'] == 'Edit' ){
			$state = '';
			$background = '#FFFFFF';
		} else if ($_POST['Type'] == 'View'){
			$state = 'readonly';
			$background = '#EEE6C4';
		}

		if (!empty($_POST['re']) && $_POST['re'] == 'true'){
			$_POST['re'] == Null;
			$sql = "UPDATE profile SET note='{$_POST['nnote']}', address='{$_POST['naddress']}', email='{$_POST['nemail']}' WHERE uid='".addslashes($row['uid'])."'";
			sql_result($sql);
		}

		$user = htmlspecialchars($row['uname']);
		$sql = "SELECT * FROM profile WHERE uid='".addslashes($_GET['id'])."'";
		$result = sql_result($sql);
		$profile = mysqli_fetch_array($result);
		$note = htmlspecialchars($profile['note']);
		$address = htmlspecialchars($profile['address']);
		$email = htmlspecialchars($profile['email']);
		$cid = htmlspecialchars($_SESSION['ID']);
		if ((empty($profile['id']) || $profile['id'] == 0) && !empty($user) && $_SESSION['ID'] == $row['uid']){
			$sql = "INSERT INTO profile (uid, note) VALUES('".addslashes($_SESSION['ID'])."', 'Let\'s Introduce Yourself!')";
			sql_result($sql);
			header('Location: profile.php?id='.$_SESSION['ID']);
		}

		
		if ($row['uname'][strlen($row['uname']) - 1] == 's')
			print_r("<h1>Welcome to {$user}' Profile!</h1>");
		else
			print_r("<h1>Welcome to {$user}'s Profile!</h1>");
		echo "<h2>Let's Introduce Yourself!</h2>";
		$sql = "SELECT * FROM notice WHERE user=\"".addslashes($row['uname'])."\" ORDER BY created DESC";
		$result = sql_result($sql);
		$totalpost = mysqli_num_rows($result);
		$sql = "SELECT id,title,created FROM notice WHERE user=\"".addslashes($row['uname'])."\" ORDER BY created DESC LIMIT 10";
		$result = sql_result($sql);
		$currentpost = mysqli_num_rows($result);

		if (!empty($_SESSION['return'])) {
			echo "<span class=\"warn\"> Not existing ID </span><br>";
			$_SESSION['return'] = '';
		 }
		if ($row['id'] == 0 && $_GET['id'] != 'root'){
			
			$_SESSION['return'] = 'None';
			header('Location: profile.php?id='.$_SESSION['ID']);
		}

		print_r("<table style=\"text-align:left; margin:auto;\">
			<tr>
				<form action=\"profile.php\" method=\"get\">
				<input type=\"text\" name=\"id\" placeholder=\"Enter the User ID!\">
				<input type=\"submit\" value=\"Search\">
				</form>
			</tr>
			</table>");
		print_r("<table style=\"table-layout:fixed; width:50%; text-align:center; margin:auto;\" border=1>
			<tr>
				<td width=\"200px\" height=\"210px\" rowspan=\"3\">");
		if (empty($profile['image']))
			$image = "tiger.jpg";
		else
			$image = $profile['image'];
		echo "<img oncontextmenu='return false;' ondragstart='return false;' width=200px height=200px src=\"{$image}\" alt=\"프로필 사진 이었던 것...\">";
		print_r("
				</td>
				
				<th width=\"250px\">
					Username
				</th>
				
				<td width=\"250px\">
					{$user}
				</td>
			</tr>
			
			<tr>
				<th>
					I D
				</th>

				<td>
					".htmlspecialchars($_GET['id'])."
				</td>
			</tr>

			<tr>
				<th>
					Registered Date
				</th>
				
				<td>
					{$row['created']}
				</td>
			</tr>
			
			<tr>
				<td colspan=\"3\" align=\"left\">
					Note
				</td>
			</tr>

			<tr>
				<td colspan=\"3\" align=\"center\">
					<textarea {$state} id=\"note\" border=0 name=\"nnote\" style=\"height:200px; width:99%; background-color:{$background}; border:none;\">
{$note}</textarea>
				</td>
			</tr>
			
			<tr>
				<td colspan=\"2\" height=75 style=\"margin:3px; text-align:left;\">
					Total Post ({$totalpost}) 
				</td>
				<td style=\"text-align:left;\">
					Address
				</td>
			</tr>
			
			<tr>
				<td colspan=\"2\" rowspan=\"3\">
					<table height=225 width=100% border=1 style=\"table-layout:fixed; word-break:break-all;\" >
						<tr>
							<td width=17% rowspan=\"10\">
								Newest<br>Post(s)
							</td>");
						for ($i = 0; $i < 10; $i++) {
							$post = mysqli_fetch_array($result);
							if ($i != 0)
								echo "<tr>";
							echo "<td width=75% height=22 style=\"text-align:left; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;\">";
							echo "<a href=\"notice.php?id={$post['id']}\">";
							echo htmlspecialchars($post['title']);
							echo "</a>";
							echo "</td>";
							echo "<td width=25%>";
							echo substr($post['created'], 0, 10);
							echo "</td>";
							echo "</tr>";
						}
		print_r("
					</table>
				</td>
				</td>
				<td style=\"text-align:center;\">
                                        <input {$state} id=\"address\" style=\"width:98%;  background-color:{$background}; text-align:center;\" name=\"naddress\" type=\"text\" value=\"{$address}\">
                                </td>

			</tr>
			
			<tr>
				<td style=\"text-align:left;\">
					E-Mail
				</td>
			</tr>
			<tr>
				<td>
					<input {$state} id=\"email\" style=\"width:98%; background-color:{$background}; text-align:center;\" name=\"nemail\" type=\"text\" value=\"{$email}\">
				</td>
			</tr>
			</table>");
		echo "<table style=\"text-align:center; margin:0 auto;\"><tr><td>";
		if ($_POST['Type'] == 'View') {
			makeSimpleButton("mainpage.php", "Main", 150, false);
			echo "</td><td>";
			if ($_SESSION['ID'] == $_GET['id']){
				echo "<form action=\"profile.php?id={$cid}\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"Type\" value=\"Edit\">";
				echo "<input style=\"width:150px;\"  type=\"submit\" value=\"Edit\">";
				echo "</form>";
			}
		} else if($_POST['Type'] == 'Edit') {
			echo "<form action=\"profile.php?id={$cid}\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"Type\" value=\"View\">";
			echo "<input style=\"width:150px;\" type=\"submit\" value=\"Cancle\">";
			echo "</form>";
			echo "</td><td>";
			echo "<button style=\"width:150px;\" onClick=\"sendPost();\">Edit</button>";
		}
		echo "</td></tr></table>";
	?>
	</div>
</body>
</html>
