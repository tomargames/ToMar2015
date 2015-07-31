<?php
require '../php/tmUtils.php';
require 'aaPlayer.php';
// Step1: get id and name from url
global $id;
global $nm;
$id = $_POST["id"];
$nm = $_POST["nm"];
if ($id == "")
{
	echo '<script> alert("Sign in to play games. Thanks!"); window.location = "../Menu"; </script>';
}
$players = PlayersFromXML();
$player = getPlayer($id, $players);
?>
<!doctype html>
<html>
<head>
	<title>Anchors Away!</title>
	<script src="../js/utils.js"></script>
	<LINK REL="StyleSheet" HREF="../styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
</head>
<body>
<table border=0 align="center"><tr>	
	<td width="10%"><img src="AAA.jpg" height="100" width="100"></td>
	<td  width="80%" class="biggest">Anchors Away!<br><span class="magenta10">by ToMarGames</span><br></td>
	<td width="10%"><img src="AAA.jpg" height="100" width="100"></td></tr>
</table>
<table border="0" align="center"><tr>
<td width="30%" align="left">
<table border=2>
<?php 
	echo "<tr><td class=green12>Player</td><td class=big>".$nm."</td></tr>";		
	echo "<tr><td class=green12>Level</td><td class=big>".$player->getLevel()."</td></tr>";		
?>
</table>
<br><br>
<form name="menuForm" action="../" method="post">			
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
<input type="submit" value="ToMarGames Menu"><br><br>	
</form>
<form name="reloadForm" action="../AAA/" method="post">			
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
</form>
<form name="gameForm" action="../AAA/aa.php" method="post">			
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
<input type="hidden" name="level" value="<?php echo $player->getLevel()?>">
<input type="hidden" name="start" value="<?php echo $player->getStart()?>">
<input type="hidden" name="tsp">
</form>
<table border=1>
	<tr><td colspan="4" class="magentah">Players</td></tr>
	<tr><td class="greenh">Name</td><td class="greenh">Lvl</td><td class="greenh">Start</td><td class="greenh">End</td></tr>
<?php
	foreach ($players as $p)
	{
		$status = (is_finite($p->getTsp())) ? tmDate($p->getTsp()) : "playing";
		echo "<tr><td class='green10'>".$p->getName()."</td><td class='green10num'>".$p->getLevel()."</td><td class='green10num'>".tmDate($p->getStart())."</td><td class='green10num'>".$status."</td></tr>";
	}	
?>	
</table>	
</td>
<td width="1">&nbsp; </td>		
<td>
<div id="app"> 
<!--	<canvas id="dbCanvas" width="1000" height="600" style="border:1px solid #000000;"> -->
	<canvas id="dbCanvas" width="1000" height="600">
		Your browser does not support the canvas element.
	</canvas>
	<script>
<?php
	echo "var savedGame = '".$player->getTsp()."';";
	echo "var words = []; ";
	$iCnt = 0;
	$file=fopen("../js/tmWords.txt","r") or exit("Unable to open file!");
 	while(!feof($file))
 	{
 		$w = trim(fgets($file));
		if (strlen($w) > 3)
		{
			if (strlen($w) < 7)
			{
				echo "words[".$iCnt++."] = '".strtoupper($w)."'; ";
			}	
		}	
 	}
	fclose($file);   
?>			
	</script>
	<script src="aa.js"></script>
</div>
</td></tr></table>
	<br>About the Game
	<ul class=green10>
		<li>In each level, create words using the letters you're given.</li>
		<li>4- and 5-letter words will have 2 anchors; 6-letter words will have 3 anchors.</li>
		<li>Any combination of valid words will pass the level; you don't have to match the words used to create the puzzle.</li>
		<li>When you pass the level, it will show you the words used to create the puzzle.</li>
		<li>The WORDS button will show you words that could possibly fit in the patterns you have been given.</li>
		<li>The HINT button will compare your solution to the words used to create the puzzle, and put back letters that don't match.</li>
		<li>There are unlimited levels. Your game will be listed as "in progress" until you GIVE UP on a level.</li>
		<li>If you GIVE UP, your game will be closed out at the level you reached, and a new game will start (at Level 1).</li>
	</ul>
</body>
</html>