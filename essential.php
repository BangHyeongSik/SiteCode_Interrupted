<meta charset="utf-8">
<?php
	function sql_result($sql) {
		$conn = mysqli_connect('localhost', 'marung13', 'hs28741830!#', 'marung13');
		$result = mysqli_query($conn, $sql);
		return $result;
	}

	function log_insert($id, $kind, $detail) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$sql = "INSERT INTO log (uip,uid,kind,details,log_date) VALUES('{$ip}','{$id}','{$kind}','{$detail}',NOW())";
		sql_result($sql);
	}

	function errorAlert($where, $error) {
		if ($where == 'login') {
			if ($error == 'First')
				return "Registered successfully";
			else if ($error == 'Delete')
				return "Deleted successfully";
			else if ($error == 'Change')
				return "Changed successfully, Please login again";
			else if ($error == 'Find')
				return "Your password is ".htmlspecialchars($_SESSION['FPW']);
			else if ($error == 'Failed')
				return "<span class=\"warn\">Login Failed</span>";
			else if ($error == 'Null')
				return "<span class=\"warn\">Enter ID and Password</span>";
			else if ($error == 'Logout')
				return "Logout successfully";
		}

		else if ($where == 'register') {
			if ($error == 'IDNAME')
				return "<span class=\"warn\">Username or ID already exists</span>";
			else if ($error == 'PW')
				return "<span class=\"warn\">Password is not same</span>";
			else if ($error == 'Null')
				return "<span class=\"warn\">Enter all info without blank</span>";
			else if ($error == 'MNAME')
				return "<span class=\"warn\">Enter a Username at most 24 characters</span>";
			else if ($error == 'MID')
				return "<span class=\"warn\">Enter a ID at most 16 characters</span>";
			else if ($error == 'LPW')
				return "<span class=\"warn\">Enter a Password at least 6 characters</span>";
			else if ($error == 'MPW')
				return "<span class=\"warn\">Enter a Password ar most 16 characters</span>";
		}

		else if ($where == 'findpass') {
			if ($error == 'Null')
				return "<span class=\"warn\">Enter all info without blank</span>";
			else if ($error == 'Wrong')
				return "<span class=\"warn\">Info is not matching</span>";
			else if ($error == 'Root')
				return "<span class=\"warn\">You can't find 'root' account</span>";
		}

		else if ($where == 'postcontrol') {
			if ($error == 'Null')
				return "<span class=\"warn\">Enter the contents</span>";
			else if ($error == 'First')
				return "<span class=\"warn\">Don't enter the blank in beginning of the title</span>";
		}

		else if ($where == 'accountcontrol') {
			if ($error == 'Wrong')
				return "<span class=\"warn\">Wrong password</span>";
			else if ($error == 'PW')
				return "<span class=\"warn\">Password is not same</span>";
			else if ($error == 'Name')
				return "<span class=\"warn\">Username already exists or Password is strange</span>";
			else if ($error == 'Current')
				return "<span class=\"warn\">Same as current username</span>";
			else if ($error == 'Null')
				return "<span class=\"warn\">Enter Username or Password to change</span>";
		}

		else if ($where == 'search') {
			if ($error == 'Null')
				return "<br>Search word has been initialized";
		}
	}

	function makeSimpleButton($where, $name, $width, $isList) {
		if ($isList)
			return 
			print_r("<form action=\"{$where}\" method=\"post\">
			<input type=\"hidden\" name=\"number\" value=\"0\">
			<input style=\"width:{$width}px;\" type=\"submit\" value=\"{$name}\"> </form>");
		else
			return print_r("<form action=\"{$where}\" method=\"post\">
			<input style=\"width:{$width}px;\" type=\"submit\" value=\"{$name}\"> </form>");
	}

	function searchDevice($siteName, $optionArray, $ordering, $type, $find, $desc, $orderArray, $ofind) {

		if ($desc == '')
			$return = 'Null';
		if (!empty($_POST['re']) && $_POST['re'] == "true" && !empty($return)) {
			print_r(errorAlert('search', $return));
		}
		print_r("<table><tr>");
		print_r("<form action=\"{$siteName}\" method=\"post\">");
		print_r("<br>Find : ");

		print_r("<select name=\"find\">");
		for ($i = 0; $i < count($optionArray); $i++) {
			if ($optionArray[$i] == $find)
				$select = " selected=\"selected\"";
			else
				$select = "";
			print_r("<option value=\"{$optionArray[$i]}\"".$select.">".$optionArray[$i]."</option>");
		}
		print_r("</select>");

		print_r("<input type=\"text\" name=\"desc\" placeholder=\"Enter the word\">");
		print_r("<input type=\"hidden\" name=\"re\" value=\"true\">");
		if ($type != Null)
			print_r("<input type=\"hidden\" name=\"Type\" value=\"$type\">");
		print_r("<input style=\"width:75px\" type=\"submit\" value=\"Search\">");
		print_r("</form>");


		print_r("<form action=\"{$siteName}\" method=\"post\">");
		echo "<select name=\"ofind\">";
		for ($i = 0; $i < count($orderArray); $i++) {
			if ($orderArray[$i] == $ofind)
				$select = " selected=\"selected\"";
			else
				$select = "";
			echo "<option value=\"{$orderArray[$i]}\"".$select.">".$orderArray[$i]."</option>";
		}
		echo "</select>";

		print_r("<input type=\"hidden\" name=\"order\" value=\"$ordering\">");
		echo "<input type=\"hidden\" name=\"anofind\" value=\"$ofind\">";
		if ($type != Null)
			print_r("<input type=\"hidden\" name=\"Type\" value=\"$type\">");
		print_r("<input type=\"hidden\" name=\"find\" value=\"$find\">
				<input type=\"hidden\" name=\"desc\" value=\"$desc\">");
		print_r("<input type=\"hidden\" name=\"Change\" value=\"true\">");
		print_r("<input style=\"width:75px\" type=\"submit\" value=\"Order\">");
		print_r("</form>");
		print_r("</tr></table>");
	}

	function buttonArray($site, $page, $lastNum, $ordering, $find, $desc, $type, $ofind) {
		$name = ["First", "Previous", "Next", "Last"];
		$lastPage = ceil($lastNum / 10) - 1;
		$previous = ($page > 0) ? ($page - 1) : $page;
		$next = ($page < $lastPage) ? ($page + 1) : $page;
		$num = [0, $previous, $next, $lastPage];
		print_r("<table><tr>");
		for ($i = 0; $i < count($name); $i++) {
			print_r("<form action=\"{$site}\" method=\"post\">");
			if (!empty($type))
				print_r("<input type=\"hidden\" name=\"Type\" value={$type}>");
			print_r("
				<input type=\"hidden\" name=\"number\" value=$num[$i]>
				<input type=\"hidden\" name=\"order\" value=\"$ordering\">
				<input type=\"hidden\" name=\"find\" value=\"$find\">
				<input type=\"hidden\" name=\"desc\" value=\"$desc\">
				<input type=\"hidden\" name=\"ofind\" value=\"$ofind\">
				<input style=\"width:75px\" type=\"submit\" value=$name[$i]></form>");
		}
		print_r("</tr></table>");
	}

	function sqlOutput($titleArray, $dataArray, $sizeArray, $height, $dataName, $page, $ex, $addon) {
	// ex배열 [0] = html코드 시각화, [1] = 텍스트 정렬, [2] = 주소
		$str = '';
		for ($i = 0; $i < count($dataArray); $i++) { //추출할 데이터
			$str = $str.$dataArray[$i];
			if ($i < count($dataArray) - 1)
				$str = $str.',';
		}
		if (!isset($page)) $page = 0;

		$number = lastStandingNum($dataName, $addon);
		$firnum = (int)$page * 10;
		$lasnum = ($firnum + 10 < $number) ? ($firnum + 10) : $number;

		if ($addon == "")
			$addon = " ";

		$sql = "SELECT {$str} FROM {$dataName}".$addon."LIMIT {$firnum}, 10"; //현재 출력할 데이터
		$result = sql_result($sql);
	
		$firnum += 1;

		print_r("<table style=\"width: 70%; margin: auto; text-align: center; table-layout:fixed;\" border=1>");
		print_r("<tr>");

		for ($i = 0; $i < count($dataArray); $i++)
			print_r("<th style=\"width:{$sizeArray[$i]}px\">{$titleArray[$i]}</th>");
		print_r("</tr>");

		while ($row = mysqli_fetch_array($result)) {
			print_r("<tr>");
			for ($i = 0; $i < count($row)/2; $i++) {
				$finally = "<td height={$height}";
			        $style = " style=\"width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;";
				if ($ex[$i][0] == true)
					$row[$i] = htmlspecialchars($row[$i]);

				if ($ex[$i][1] != Null)
					$style = $style." text-align:{$ex[$i][1]};";
				
				$finally = $finally.$style."\">";
				if ($ex[$i][2] != Null)
					$finally = $finally."<a href=".$ex[$i][2]."id={$row['id']}&number={$page}>".$row[$i]."</a></td>";
				else
					$finally = $finally.$row[$i]."</td>";
				print_r($finally);
			}
			print_r("</tr>");
		}
		print_r("</table>");
	}

	function lastStandingNum($dataName, $addon) {
		if ($addon == Null) $addon = ' ';
		$sql = "SELECT id FROM {$dataName}".$addon; //전체 데이터 열 개수 
		$result = sql_result($sql);
		$number = mysqli_num_rows($result);
		return $number;
	}
?>
<noscript>
	You need to enable Javascript!
	To enable, Join <a href="https://enable-javascript.com/ko/">https://enable-javascript.com/ko/</a>
</noscript>
