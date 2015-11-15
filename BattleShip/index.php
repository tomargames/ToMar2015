<?php
require 'bFunctions.php';

// Step1: get id and name from post -- if no ID, require sign-in
// battleship.php will always send back id, nm, and ac. no ac means you just came in
$id = $_POST["id"];
$nm = $_POST["nm"];
$ac = $_POST["ac"];
$buts = null;
$registered = false;
if ($id == "")
{
	echo '<script> alert("Sign in to play games. Thanks!"); window.location = "../Menu"; </script>';
}
// Step2: get list of human players from XML
$players = playersFromXML("humans.xml");
// Step3: if R, register new player; otherwise, match player to their xml entry
if ("R" == $ac)
{
	$player = makePlayer($id, $nm);
	$players[count($players)] = $player;
	writePlayerXML($players, "humans.xml");
	$registered = true;
}
else
{	
	$player = $players[$id];
}	
if ($player == null)
{
	$registerPlayer = '<input type="button" style="background: #6666FF" onClick="newPlayer();" value="Register to play">';
}	
else
{
	$registered = true;
	$registerPlayer = $player->name;
	if ($player->ID == '106932376942135580175')
	{
		$registerPlayer = $registerPlayer.'<input type="button" style="background: #CC0033" onClick="admin();" value="admin">';
	}	
}
$bots = playersFromXML("bots.xml");
?>
<html>
<head>
	<title>BattleShip Playground</title>
	<script src="../js/utils.js"></script>
	<LINK REL="StyleSheet" HREF="../styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
<?php 
// Step4: if no action, then no game selected
if ($ac == null)
{
	$selectedGame = "''";
}
else if (firstChar($ac) == "E")
{
	echo 'ERROR: '.substr($ac, 1);
}
else if (firstChar($ac) == "G")
{
	$selectedGame = "'".$ac."'";
}
?>
<script>
var gameButtons = [];
var names = [];
var strings = [];
var rels = [];
<?php
echo 'var selectedGame = '.$selectedGame.'; ';
$games = getGames($id);
processGames($games);
?>		
</script>
</head>
<body>
<table border="0" width="100%" align="center"><tr>	
	<td width="30%" class="magenta10"><?php echo "Playing as: ".$registerPlayer ?></td>
	<td  width="40%" class="biggest">BattleShip Playground</td>
	<td width="30%" class="magenta10">by ToMarGames</td></tr>
</table>
<table border="0" align="center"><tr valign="top">
<td width="30%" align="left">
	<form name="menuForm" action="../" method="post">			
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="nm" value="<?php echo $nm; ?>">
		<input type="submit" style="background: #6666FF" value="ToMarGames Menu">
	</form>
	<form name="statForm" action="page2.php" method="post">			
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="nm" value="<?php echo $nm; ?>">
		<input type="submit" style="background: #7777FF" value="History">
	</form>
	<br>
	<table border=1>
		<tr><td colspan="5" class="magentah">Games</td></tr>
<?php displayGames($games); ?>	
	</table>	
	<br><br>
	<table border=1>
		<tr>
			<td class="magentah">Human Players</td>
			<td class="magentah">Games</td>
			<td class="magentah">Wins</td>	
			<td class="magentah"></td>	
		</tr>
<?php
	foreach ($players as $p)
	{
		if ($registered == true && $p->ID != $player->ID)
		{
				$tId = "'".$p->ID."'";
				$tName = "'".$p->name."'";
				$playButton = '<input type="button" style="background: #6666FF" onClick="startGame('.$tId.','.$tName.');" value="Challenge">';
		}
		else 
		{
			$playButton = "";
		}	
		echo "<tr>";
		echo "<td class='green10'>".$p->name."</td>";
		echo "<td class='green10'>".$p->gameCount."</td>";
		echo "<td class='green10'>".$p->winCount."</td>";
		echo "<td>".$playButton."</td>";
		echo "</tr>";
	}	
?>	
	</table>	
	<br><br>
	<table border=1>
		<tr>
			<td class="magentah">Bot Players</td>
			<td class="magentah">Games</td>
			<td class="magentah">Wins</td>	
			<td class="magentah"></td>	
		</tr>
<?php
	foreach ($bots as $p)
	{
		$playButton = "";
		if ($player != null)
		{	
			if ($p->dev == null || $p->dev == $player->ID)
			{
				$tId = "'".$p->ID."'";
				$tName = "'".$p->name."'";
				$playButton = '<input type="button" style="background: #6666FF" onClick="startGame('.$tId.','.$tName.');" value="Start Game">';
				echo "<tr>";
				echo "<td class='green10'>".$p->name."</td>";
				echo "<td class='green10'>".$p->gameCount."</td>";
				echo "<td class='green10'>".$p->winCount."</td>";
				echo "<td>".$playButton."</td>";
				echo "</tr>";
			}
		}		
	}	
?>	
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
<form name="reloadForm" method="post">
<input type="hidden" name="ac">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
</form>
<form name="gameForm" method="post" action="battleship.php">
<input type="hidden" name="ac">
<input type="hidden" name="gm">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
<input type="hidden" name="pid2">
<input type="hidden" name="name2">
</form>
<script>
<?php 
if ($selectedGame != "''")
{
	echo 'populateDisplay('.$selectedGame.'); ';
}	
if ($player->ID == '106932376942135580175')
{
?>
function admin()
{
	if (window.confirm("Go to admin page?") == true) 
	{
		document.reloadForm.action = "admin.php";
		document.reloadForm.submit();
	}
}	
<?php	
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
			echo "<tr><td class='green10'>".$g->Name1."</td><td class='green10'>".$g->Name2."</td><td class='green10'>".gameDate($g->TSP)."</td><td id=".$g->getPassKey().">".$playButton."</td></tr>";
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
			echo 'rels['.$g->getPassKey().'] = '.$g->getRel($id).'; ';
			$buttonString = '<input type=button onClick=selectGame('.$g->getPassKey().'); value=View>';
			if ("C" == $g->getStage())
			{
				if  ($g->getRel($id) == 1)
				{	
					$buttonString = '<input style=\'background: #FF3399\' type=button onClick=acceptGame('.$g->getPassKey().'); value=Accept>';
				}
				else if ($g->getRel($id) == 0)
				{
					$buttonString = '<input style=\'background: #6666FF\' type=button onClick=selectGame('.$g->getPassKey().'); value=Play>';
				}	
			}
			else if ($g->getRel($id) == 2 || $g->getStage() == "O")
			{
				$buttonString = '<input style=\'background: #CCCCFF\' type=button onClick=selectGame('.$g->getPassKey().'); value=View>';
			}
			else if ($g->getRel($id) == $g->getActor())
			{
				$buttonString = '<input style=\'background: #33CC33\' type=button onClick=selectGame('.$g->getPassKey().'); value=Play>';
			}
			else
			{
				$buttonString = '<input style=\'background: #6666FF\' type=button onClick=selectGame('.$g->getPassKey().'); value=Play>';
			}
			echo 'gameButtons['.$g->getPassKey().'] = "'.$buttonString.'"; ';
			$buts[$g->getPassKey()] = $buttonString;
		}
	}
}	
?>	
function startGame(opp, name)
{
	if (window.confirm("Start a game with " + name + "?") == true) 
	{
		document.gameForm.pid2.value = opp;
		document.gameForm.name2.value = name;
		document.gameForm.ac.value = "C";
		document.gameForm.submit();
	}	
}
function acceptGame(passKey)
{
	document.gameForm.ac.value = "A" + passKey;
	document.gameForm.submit();
}
function selectGame(passKey)
{
	if (selectedGame != '')
	{
		document.getElementById(selectedGame).innerHTML = gameButtons[selectedGame];
	}	
	document.getElementById(passKey).innerHTML = "";
	populateDisplay(passKey);
}	
function newPlayer()
{
	document.reloadForm.ac.value = "R";
	document.reloadForm.submit();
}
</script>
</html>