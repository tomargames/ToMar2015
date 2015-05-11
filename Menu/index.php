<?php
require 'Player.php';
require 'Game.php';
global $id;
global $nm;
global $ctg;

date_default_timezone_set("America/New_York");
if(isset($_POST["ctg"]))
{
	$ctg = $_POST["ctg"];
}
else
{
	 $ctg = "J";
}	 
$d = substr(date("r"), 0, strpos(date("r"),"-")); 
$games = GameFromXML();
?>
<!DOCTYPE HTML>
<html lang="en">   
<meta charset="utf-8">   
<head>	
	<title>ToMarGames</title>
	<script src="https://apis.google.com/js/platform.js" async defer></script>
	<script src="../js/utils.js"></script>
	<meta name="google-signin-client_id" content="932688745244-i4vfeap5jgu8id5dagrc49786vvs0qrf.apps.googleusercontent.com">
	<LINK REL="StyleSheet" HREF="menu.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
</head>	
<body>
<table width=100% cellpadding="0" cellspacing="0"><tr>
<td width=20% class="outer"></td>
<td width=60%>
	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td class="fineprint10" width="20%"><?php echo $d ?></td>
		<td class="banner" width="60%">ToMarGames</td>
		<td class="fineprint10" width="20%">
			<script>
				var tmName = getCookie("name");
				var tmId = getCookie("id");
				if (tmName == "")
				{
					document.write('<span class="g-signin2" data-onsuccess="onSignIn"> </span>');
				}
				else
				{		
					document.write(tmName + '<br><a class=fineprint10 href="javascript:changeUser();"> (change user)</a>');
				}	
			</script>		
		</td>
	</tr>
	<tr>
		<td valign="top" width="20%">
			<table cellpadding="1" cellspacing="1">
<?php
		$codes = array("J", "W", "L", "F", "S");
		$descs = array("Just Games", "Word Games", "Logic/Math Games", "Fast Games", "Slow Games");
		for ($i = 0; $i < 5; $i++)
		{
			if ($codes[$i] == $ctg)
			{
				$class = "menuselected";
				$rest = ">".$descs[$i];
			}
			else
			{
				$class = "menubutton";
				$rest = "><a class=".$class." href=javascript:menuFilter('".$codes[$i]."');>".$descs[$i]."</a>";
			}		
			echo "<tr><td class=".$class.$rest."</td></tr>";
		}
?>			
				<tr><td class=menubutton><a class=menubutton href="../PNT">ToMar Pentathlon</a></td></tr>
				<tr><td class=menubutton><a class=menubutton href="android.html">Games for Android</a></td></tr>  
				<tr><td class=menubutton><a class=menubutton href="about.html">About ToMarGames</a></td></tr>  
				</tr>
			</table>	
		</td>
		<td span=2>
			<table>
<?php
	foreach ($games as $g)
	{
			if (strpos($g->getType(), $ctg) > -1)
			{
				$lk = "javascript:playGame('".$g->getID()."');";
				$dd = "<a href=javascript:playGame('".$g->getID()."');>".$g->getDesc()."</a>";
				echo "<tr><td><a href=".$lk."><img height=100 width=100 src=../".$g->getID()."/".$g->getID().".jpg></a>";
				echo "</td><td class=blue10><a href=".$lk." class=green12L>".$g->getName()."</a><br>".$dd."</td></tr><tr><td colspan=3><hr></td></tr>";
			}		
	}		
?>
		</table>
		</td>
	</tr>

	<tr>
		<td class="fineprint8" width="20%"></td>
		<td class="fineprint8" width="60%">ToMarGames build brain cells!<br>Copyright &copy; 2015 ToMarGames<br>No rights reserved; feel free to use as you like.<br><?php echo $_SERVER['REMOTE_ADDR'] ?></td>
		<td class="fineprint8" width="20%"></td>
	</tr>
	</table>
	<form method="post" name="process">
	<input type="hidden" name="id" value='<?php echo $_COOKIE["id"] ?>'>	
	<input type="hidden" name="nm" value='<?php echo $_COOKIE["name"] ?>'>
	<input type="hidden" name="score" value="0">
	<input type="hidden" name="ctg" value='<?php echo $ctg ?>'>	
	</form>	
	
</td>				
<td width=20% class="outer"></td></tr></table>			
<script>  
function playGame(game)
{
	document.process.action = '../' + game + '/';
	document.process.submit();
}
function menuFilter(c)
{
	document.process.ctg.value = c;
	document.process.action = "../Menu/";
	document.process.submit();
}			
</script> 
</body>
</html>