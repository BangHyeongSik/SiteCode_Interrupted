<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src=http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js></script>
<title> Introducing Yourself</title>
<h1>Hello, Artist! Show me your skills!</h1>
<h3>If you want, you can download it!</h3>

<?php
	session_cache_limiter("private_no_expire");
	session_start();
        require('essential.php');
	
	$isPreview = false;
	$dotArray = [1, 2, 4, 6, 8, 10, 15, 20, 25, 800];
	$colorArray = [[ 0, 0, 0 ],
		[ 0, 0, 255 ],
		[ 0, 255, 0 ],
		[ 0, 255, 255 ],
		[ 255, 0, 0 ],
		[ 255, 0, 255 ],
		[ 255, 255, 0 ],
		[ 255, 255, 255 ],
		[ 127, 127, 127 ]];
	$colorNameArray = [ 'Black', 'Blue', 'Green', 'Cyan', 'Red', 'Magenta', 'Yellow', 'White' , 'Gray' ];
	echo "<table border=\"1\" style=\"width:auto; text-align:center; margin:auto; table-layout:fixed;\">";
	echo "<tr><td style=\"width:70%;\" rowspan=\"".ceil(count($colorArray)/2 + 2)."\">";
	echo "<canvas id=\"cnvs\" height=\"400\" width=\"400\" style=\"border: 1px solid black;\"></canvas>";
	echo "</td><td colspan=\"2\">";
	echo "<select style=\"font-size:20px;\"  id=\"dot\">";
	for($i = 0; $i < count($dotArray); $i++) {
		if ($dotArray[$i] == 10)
			echo "<option selected=\"selected\" style=\"font-size:20px;\" value=\"{$dotArray[$i]}\">{$dotArray[$i]}px</option>";
		else if ($i == count($dotArray)-1)
			echo "<option style=\"font-size:20px;\" value=\"{$dotArray[$i]}\">Paint</option>";
		else
			echo "<option style=\"font-size:20px;\" value=\"{$dotArray[$i]}\">{$dotArray[$i]}px</option>";
	}
	echo "</select>";
	echo "</td></tr><tr>";

	for($i = 0; $i < count($colorArray); $i++) {
		echo "<td style=\"width:15%\">";
		$temp = str_pad(dechex($colorArray[$i][0] * 65536 + $colorArray[$i][1] * 256 + $colorArray[$i][2]), 6, "0", STR_PAD_LEFT);
		$dec_temp = hexdec($temp);
		echo $colorNameArray[$i]."<br>";
		echo "<button value=\"{$dec_temp}\" class=\"colorBoard\" style=\"background-color:#".$temp."; width:50px; height:50px;\"></button>";
		if ($i % 2)
			echo "</tr><tr>";
			echo "</td>";
		}
	echo "</select>";
	echo "<td><table style=\"text-align:center; margin:auto;\"><tr><td>";
	echo "Custom<br>";
	echo "<input id=\"cuco\" style=\"width:60px;\" maxlength=\"6\" type=\"text\" placeholder=\"HexCode\"><br/>";
	echo "<button id=\"cucosub\" style=\"width:auto; height:20px;\">Find</button>";
	echo "</td></tr></table>";
	echo "</td></tr>";
	echo "<tr><td>";
	echo "<button id=\"Erase\" style=\"width:auto;\">Erase</button>";
	echo "</td><td>";
	echo "<button style=\"width:auto;\" onClick=\"imgReset();\">Reset</button>";
	echo "</td></tr>";
	echo "</table>";
	echo "<table style=\"width:40%; text-align:center; margin:auto;\"><tr><td style=\"text-align:left; margin:auto; width:70%;\">";
	echo "<form action=\"mainpage.php\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"Type\" value=\"View\">";
	echo "<button id=\"back\" style=\"width:150px;\" type=\"submit\">Back</button>";
	echo "</form>";
	echo "</td>";
	echo "<td style=\"text-align:right; margin:auto;\">";
	echo "<button style=\"width:150px;\" onClick=\"imgPreview();\">Preview</button>";
	echo "</td>";
	echo "<td style=\"text-align:right; margin:auto;\">";
	echo "<button style=\"width:150px;\" onClick=\"imgDownload();\">Download</button>";
	echo "</td>";
	echo "<td id=\"closeButton\"></td>";
	if($isPreview) {
		echo "<td style=\"text-align:right; margin:auot;\">";
		echo "<button style=\"width:150px;\" onClick=\"previewClose();\">Close</Button>";
		echo "</td>";
	}
	echo "</tr>";
	echo "<div id=\"preview\"></div>";
?>
	<script>
	var cnvs;
	var ctx;
	function fillChar(n, digits, chr) {
		var temp = '';
		n = n.toString();

		if (n.length < digits) {
			for (var i=0; i < digits - n.length; i++)
				temp += chr;
		}
		return temp + n;
	}
	$(document).ready(function() {
		cnvs = document.getElementById('cnvs');
		ctx = cnvs.getContext('2d');
		ers = document.getElementById('Erase');
		var isDraw = false;
		var isErase = false;
		var dot = 10;
		var r = 0, g = 0, b = 0;
		var selectColor = 'rgb(0,0,0)';
		$('.colorBoard').bind('click', function(){getColor($(this).val())});
	        $('#dot').bind('change', function(){dot = $('#dot').val(); });
	        $('#cucosub').bind('click', function() {getColor(parseInt($('#cuco').val(), 16))});
		
		function getColor(color) {
			isErase = false;
			if ( 0 <= color && color <= 0xffffff ) {
	                	r = color / 65536;
	                	g = color / 256 % 256;
        	        	b = color % 256;
                		selectColor = 'rgb('+r+','+g+','+b+')';
			} else alert("You enter the wrong number (0 ~ ffffff)");
		}

		ers.onclick = function(e) {
			isErase = true;
		}

		cnvs.onmousemove = function(e) {
			if(isDraw) {
				if(!isErase)
					draw(e);
				else
					erase(e);
			}
				
      		}

		cnvs.onmousedown = function(e) {
			if(e.button == 0) {
				isDraw = true;
				if (!isErase) {
					draw(e);
				} else {
					erase(e);
				}
               		}
       		}

		onmouseup = function(e) {
               		isDraw = false;
       		}

		function draw(e) {
          		ctx.fillStyle = selectColor;
              		ctx.fillRect(e.offsetX-dot/2, e.offsetY-dot/2, dot, dot);
		}

		function erase(e) {
			ctx.clearRect(e.offsetX-dot/2, e.offsetY-dot/2, dot, dot);
			ctx.beginPath();
		}
	});

	function imgClose() {
		var cTag = document.getElementById('preview');
		var site = document.getElementById('imageSite');
		cTag.removeChild(site);

		var closeMenu = document.getElementById('closeButton');
		var closeButton = document.getElementById('close');
		closeMenu.removeChild(closeButton);
	}

	function imgPreview() {
		var bTag = document.getElementById('preview');
		var preview = document.createElement("img");

		var closeMenu = document.getElementById('closeButton');
		var closeButton = document.createElement("button");
		var close = document.createTextNode("Close");

		if ( document.getElementById('imageSite') ) {
			var site = document.getElementById('imageSite');
			bTag.removeChild(site);
		}

		closeButton.setAttribute("style", "width:150px;");
		closeButton.setAttribute("onClick", "imgClose();");
		closeButton.setAttribute("id", "close");
		closeButton.appendChild(close);
		closeMenu.appendChild(closeButton);

		preview.setAttribute("src", cnvs.toDataURL());
		preview.setAttribute("id", "imageSite");
		preview.setAttribute("style", "border: 1px solid;");
		bTag.appendChild(preview);
	}

	function imgDownload() {
	        var now = new Date();
		var imageName = fillChar(now.getFullYear(), 4, '0') + ( fillChar(now.getMonth() + 1, 2, '0') ) + fillChar(now.getDate(), 2, '0') +
		        "-" + fillChar(now.getHours()*3600 + now.getMinutes()*60 +now.getSeconds(), 8, '0');
	        var aTag = document.createElement('a');
		aTag.style = "display:none;";
		aTag.href = cnvs.toDataURL();
		aTag.download = imageName+".png";
		aTag.click();
	}

	function imgReset() {
		ctx.clearRect(0,0, cnvs.width, cnvs.height);
	}
</script>
</body>
</html>
