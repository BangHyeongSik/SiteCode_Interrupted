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
		$pcname = htmlspecialchars($_SESSION['NAME']);
		$pcid = htmlspecialchars($_SESSION['ID']);
		if (empty($_POST['id']) || $_POST['id'] == '')
			$_POST['id'] = 0;
		if (empty($_POST['title']) || $_POST['title'] == '')
			$_POST['title'] = '';
	?>
		<script>
		function getType(){
			var title = document.getElementById('title').value;
			var special = /[\\"']/g;
			var entererror = /(\n\n)/g;
			title = title.replace(special, "\\$&");
			var description = document.getElementById('description').value;
			description = description.replace(special, "\\$&").replace(entererror, "\n");
			var num = <?php echo !empty($_POST['number']) ? $_POST['number'] : 0; ?>;
			var Type = <?php echo "\"".$_POST['Type']."\""; ?>;
			var id;
			
			if ( Type == 'Edit' ) {
				id = <?php echo $_POST['id']; ?>;
			}
			var form = document.createElement('form');
			form.setAttribute("method", "post");
			form.setAttribute("action", "postcontrol.php");

			var hiddenField;
			var nameArray = ['title', 'description', 'OTL', 'PID', 're', 'Type'];
			var valueArray = [title, description, oldTitle, 0, 'true', Type];
			if (Type == 'Edit') {
				nameArray = ['title', 'description', 'OTL', 'PID', 're', 'Type', 'id', 'number'];
				valueArray = [title, description, oldTitle, id, 'true', Type, id, num];
			}

			for ( var i=0; i<nameArray.length; i++ ) {
				hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", nameArray[i]);
				hiddenField.setAttribute("value", valueArray[i]);
				form.appendChild(hiddenField);
			}

			form.target = self;
			document.body.appendChild(form);
			if (title.length > 50 ) {
				alert("Enter a title at most 50 characters [Now:"+title.length+"]");
			} else {
				form.submit();
			}
		}	
	</script>
	<?php

		if (!isset($_POST['number']) || $_POST['number'] == '') $_POST['number'] = 0;

		if ($_POST['Type'] == 'Create') {
			if ($_SESSION['ID'] == Null)
				header('Location: notice.php');
		} else if ($_POST['Type'] == Null) {
			header('Location: notice.php');
		} else {
			$sql = "SELECT user FROM notice WHERE id={$_POST['id']}";
			$result = sql_result($sql);
			$row = mysqli_fetch_array($result)['user'];
			if ($_SESSION['NAME'] != 'root' && $_SESSION['NAME'] != $row)
				header('Location: notice.php');
			if ($_POST['Type'] == 'Delete') {
				$sql = "DELETE FROM notice WHERE id={$_POST['id']}";
				sql_result($sql);
				log_insert(addslashes($_SESSION['ID']), 'Delete Notice', addslashes($_POST['Title']));
				header('Location: notice.php');
			}
		}
		if (!empty($_POST['title']))
			$kor_title = str_split($_POST['title']);

		if (empty($_POST['title']) || empty($_POST['description']) || array_unique($kor_title) == ' ') {
			$return = 'Null';
		} else if ($kor_title[0] == ' ') {
			$return = 'First';
		}

		if ($_POST['Type'] == 'Create') {
			$back = "notice.php";
			print_r("<h1>Creating Your Own Post!</h1>");
		} else if ($_POST['Type'] == 'Edit') {
			$back = "notice.php?id={$_POST['id']}&number={$_POST['number']}";
			print_r("<h1>Edit Your Post!</h1>");
		}
		if (!isset($return)) {
			if ($_POST['Type'] == 'Create') {
				log_insert(addslashes($_SESSION['ID']), 'Create Notice', $_POST['title']);
				$sql = "INSERT INTO notice (title,description,user,created) VALUES('{$_POST['title']}', '{$_POST['description']}', '".addslashes($_SESSION['NAME'])."', NOW())";
			} else if ($_POST['Type'] == 'Edit') {
				log_insert(addslashes($_SESSION['ID']), 'Edit Notice', "Old : ".addslashes($_POST['OTL']).", ID : {$_POST['PID']}");
				$sql = "UPDATE notice SET title='{$_POST['title']}', description='".addslashes($_POST['description'])."' WHERE id={$_POST['PID']}";
			}
			sql_result($sql);
			header('Location: '.$back);
	}
	?>
	<h2>Notice Board</h2>
	<?php
		if (!empty($return) && !empty($_POST['re']) && $_POST['re'] == "true") {
			print_r(errorAlert('postcontrol', $return));
		}
		if ($_POST['Type'] == 'Edit') {
			$sql = "SELECT title,description FROM notice WHERE id={$_POST['id']}";
			$result = sql_result($sql);
			$row = mysqli_fetch_array($result);
			$title = htmlspecialchars(addslashes($row['title']));
			$description = htmlspecialchars(addslashes($row['description']));
		} else {
			$title = '';
			$description = '';
		}
?>
<table style="margin:auto; text-align:center;">
<tr><td colspan="2">
		<p>
		<input id="title" style="width:400px;" maxlength="50" type="text" name="title" placeholder="Title" value="<?php print_r(stripslashes($title));?>">
		</p>
		<p>
<textarea id="description" style="width:400px; height:500px;" type="text" name="description">
<?php print_r(stripslashes($description));?>
</textarea>
		</p>
</td></tr>
<tr><td style="text-align:left;">
	<form action="<?php print_r($back);?>" method="post">
		<?php print_r("<input type=\"hidden\" name=\"number\" value={$_POST['number']}>"); ?>
		<input style="width:150px" type="submit" value="Cancle">
	</form>
</td><td style="text-align:right;">

		<?php
	                print_r("<button onClick=\"getType();\" style=\"width:150px\">".$_POST['Type']."</button>");
                ?>

</td></tr>
</table>
	</div>
<script>var oldTitle = document.getElementById('title').value;</script>
</body>
</html>
