<?php
require 'bFunctions.php';

$id = $_POST["id"];
$nm = $_POST["nm"];
$players = playersFromXML("humans.xml");
$bots = playersFromXML("bots.xml");
$selectedGame = "''";
?>
<html>
<head>
	<title>BattleShip Playground Archives</title>
	<script src="../js/utils.js"></script>
	<LINK REL="StyleSheet" HREF="../styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
	<style>
	.highlight
	{
		background-color: #FFFFCC;
		color: #DD0000;
		font-size: 10pt;
		text-align: left;
	}
	</style>
<script>
var gameButtons = [];
var names = [];
var strings = [];
var rels = [];
<?php
echo 'var selectedGame = '.$selectedGame.'; ';
$oldGames = getArchive();
processGames($oldGames);
?>		
</script>
</head>
<body>
<table border="0" width="100%" align="center"><tr>	
	<td width="20%">
	<form name="menuForm" action="../" method="post">			
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="nm" value="<?php echo $nm; ?>">
		<input type="submit" style="background: #6666FF" value="ToMarGames Menu">
	</form>
	<form name="gameForm" action="index.php" method="post">			
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="nm" value="<?php echo $nm; ?>">
		<input type="submit" style="background: #FFFFCC" value="BattleShip">
	</form></td>	
	<td  width="80%" class="biggest">BattleShip Playground Archives</td>
</table>
<table><tr valign="top"><td width="20%">
	<table border=1>
		<tr>
			<td class="magentah">Bot Players</td>
			<td class="magentah">Games</td>
			<td class="magentah">Wins</td>	
		</tr>
<?php
	foreach ($bots as $p)
	{
		if ($p->dev == null)
		{
			echo "<tr>";
			echo "<td class='green10'>".$p->name."</td>";
			echo "<td class='green10'>".$p->gameCount."</td>";
			echo "<td class='green10'>".$p->winCount."</td>";
			echo "</tr>";
		}		
	}	
?>	
	</table>	
	<br><br>
	<table border=1>
		<tr>
			<td class="magentah">Human Players</td>
			<td class="magentah">Games</td>
			<td class="magentah">Wins</td>	
		</tr>
<?php
	foreach ($players as $p)
	{
		echo "<tr>";
		echo "<td class='green10'>".$p->name."</td>";
		echo "<td class='green10'>".$p->gameCount."</td>";
		echo "<td class='green10'>".$p->winCount."</td>";
		echo "</tr>";
	}	
?>	
	</table>	
	<br><br>
	<table border=1>
		<tr><td colspan="5" class="magentah">Archived Games</td></tr>
<?php displayGames($oldGames); ?>	
	</table>	
</td>
<td width="1">&nbsp; </td>		
<td>
<div id="app"> 
	<canvas id="bCanvas" width="900" height="520" style="border:1px solid #000000;"> 
		Your browser does not support the canvas element.
	</canvas>
	<script src="battleship.js"></script>
</div>
</td></tr></table>
<script>
<?php 
if ($selectedGame != "''")
{
	echo 'populateDisplay('.$selectedGame.'); ';
}	
function displayGames($gArray)
{
	global $buts, $selectedGame;
	if (count($gArray) > 0)
	{	
		foreach ($gArray as $g)
		{
			if ($g->getPassKey() == $selectedGame)
			{	
				$playButton = "";
			}	
			else
			{	
				$playButton = $buts[$g->getPassKey()];
			}	
			// figure out who won the game -- second byte of status
			if ($g->getActor() == 0)
			{
				$c1 = "'highlight'";
				$c2 = "'black10'";
			}	
			else
			{
				$c2 = "'highlight'";
				$c1 = "'black10'";
			}	
			echo "<tr><td class=".$c1.">".$g->Name1."</td><td class=".$c2.">".$g->Name2."</td><td class='black10'>".gameDate($g->TSP)."</td><td id=".$g->getPassKey().">".$playButton."</td></tr>";
		}	
	}
}
function processGames($gArray)
{
	global $buts, $id;
	if (count($gArray) > 0)
	{	
		foreach ($gArray as $g)
		{
			echo 'strings['.$g->getPassKey().'] = "'.$g->Status.'"; ';
			echo 'names['.$g->getPassKey().'] = []; ';
			echo 'names['.$g->getPassKey().'][0] = "'.$g->Name1.'"; ';
			echo 'names['.$g->getPassKey().'][1] = "'.$g->Name2.'"; ';
			echo 'rels['.$g->getPassKey().'] = 2; ';
			$buttonString = '<input style=\'background: #CCCCFF\' type=button onClick=selectGame('.$g->getPassKey().'); value=View>';
			echo 'gameButtons['.$g->getPassKey().'] = "'.$buttonString.'"; ';
			$buts[$g->getPassKey()] = $buttonString;
		}
	}
}	
?>	
function selectGame(passKey)
{
	if (selectedGame != '')
	{
		document.getElementById(selectedGame).innerHTML = gameButtons[selectedGame];
	}	
	document.getElementById(passKey).innerHTML = "";
	populateDisplay(passKey);
}	
</script>
</html>