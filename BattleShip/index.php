<?php
require 'bFunctions.php';

// Step1: get id and name from post -- if no ID, require sign-in
$id = $_POST["id"];
$nm = $_POST["nm"];
$ac = $_POST["ac"];
//echo "ac value is ".$ac."<br>";
$registered = false;
if ($id == "")
{
	echo '<script> alert("Sign in to play games. Thanks!"); window.location = "../Menu"; </script>';
}
// Step2: get list of human players from XML
$players = HumansFromXML();
// Step3: if R, register new player; otherwise, match player to their xml entry
if ("R" == $ac)
{
	$player = new bPlayer();
	$player->setID($id);
	$player->setName($nm);
	$players[count($players)] = $player;
	writeHumanXML($players);
	$registered = true;
}
else
{	
	$player = $players[$id];
}	
if ($player == null)
{
	$registerPlayer = '<input type="button" onClick="newPlayer();" value="Register to play">';
}	
else
{
	$registered = true;
	$registerPlayer = $player->getName();
}
$bots = BotsFromXML();
$games = gamesFromXML(1);
//print_r($games);
// Step4: if no action, then no game selected
if ($ac == null)
{
	$selectedGame = "''";
}
else
{
	$st = substr($ac, 0, 2);
	$selectedGame = "'".substr($ac, 2)."'";
	echo "st is ".$st.", selectedGame is ".$selectedGame."<br>";
	if ("C" == firstChar($ac))										// first reload since game creation
	{
		while ($games[$selectedGame] == null)
		{
			$games = gamesFromXML(1);
		}
	}
	else							// all others, you want to make sure the status has changed since you acted
	{
		if ($games[$selectedGame] != null)
		{	
			while (substr($games[$selectedGame]->getStatus(), 0, 2) == $st)
			{
//			echo substr($games[$selectedGame]->getStatus(), 0, 2)."<br>";
				$games = gamesFromXML(1);
			}
		}	
	}	
}
?>
<html>
<head>
	<title>BattleShip Playground</title>
	<script src="../js/utils.js"></script>
	<LINK REL="StyleSheet" HREF="../styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
	<script>
		var gameButtons = [];
		var names = [];
		var id2 = [];
		var strings = [];
		var rels = [];
<?php
	echo 'var selectedGame = '.$selectedGame.'; ';
	if (count($games) > 0)
	{	
		foreach ($games as $g)
		{
			echo 'strings['.$g->getPassKey().'] = "'.$g->getStatus().'"; ';
			echo 'id2['.$g->getPassKey().'] = "'.$g->getPID2().'"; ';
			echo 'names['.$g->getPassKey().'] = []; ';
			echo 'names['.$g->getPassKey().'][0] = "'.$g->getName1().'"; ';
			echo 'names['.$g->getPassKey().'][1] = "'.$g->getName2().'"; ';
			$buttonString = '<input type=button onClick=selectGame('.$g->getPassKey().'); value=Select>';
			if ($g->getPID1() == $id)
			{
				echo 'rels['.$g->getPassKey().'] = 0; ';
			}	
			else if ($g->getPID2() == $id)
			{
				echo 'rels['.$g->getPassKey().'] = 1; ';
				if ("C" == $g->getStage())
				{
					$buttonString = '<input type=button onClick=acceptGame('.$g->getPassKey().'); value=Accept>';
				}
			}	
			else
			{
				echo 'rels['.$g->getPassKey().'] = 2; ';
			}	
			echo 'gameButtons['.$g->getPassKey().'] = "'.$buttonString.'"; ';
		}
	}	
?>		
	</script>
</head>
<body>
<table border="0" width="100%" align="center"><tr>	
	<td width="30%" class="magenta10"><?php echo "Playing as: ".$registerPlayer ?></td>
	<td  width="40%" class="biggest">BattleShip Playground</td>
	<td width="30%" class="magenta10">by ToMarGames</td></tr>
</table>
<table border="0" align="center"><tr>
<td width="30%" align="left">
	<form name="menuForm" action="../" method="post">			
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<input type="hidden" name="nm" value="<?php echo $nm; ?>">
	<input type="submit" value="ToMarGames Menu"><br><br>	
	</form>
	<br><br>
	<table border=1>
		<tr><td colspan="5" class="magentah">Games</td></tr>
<?php
	if (count($games) > 0)
	{	
		foreach ($games as $g)
		{
			if ($g->getPassKey() == $selectedGame)
			{	
				$playButton = "";
			}	
			else if ($g->getStage() == "C" && $g->getPID2() == $id)
			{	
				$playButton = '<input type="button" onClick="acceptGame('.$g->getPassKey().');" value="Accept">';
			}		
			else
			{	
				$playButton = '<input type="button" onClick="selectGame('.$g->getPassKey().');" value="Select">';
			}		
			echo "<tr><td class='green10'>".$g->getName1()."</td><td class='green10'>".$g->getName2()."</td><td class='green10'>".$g->getStage()."</td><td id=".$g->getPassKey().">".$playButton."</td></tr>";
		}	
	}	
?>	
	</table>	
	<br><br>
	<table border=1>
		<tr><td colspan="4" class="magentah">Human Players</td></tr>
<?php
	foreach ($players as $p)
	{
		if ($registered == true && $p->getID() != $player->getID())
		{
				$tId = "'".$p->getId()."'";
				$tName = "'".$p->getName()."'";
				$playButton = '<input type="button" onClick="startGame('.$tId.','.$tName.', 1);" value="Challenge">';
		}
		else 
		{
			$playButton = "";
		}	
		echo "<tr><td class='green10'>".$p->getName()."</td><td>".$playButton."</td></tr>";
	}	
?>	
	</table>	
	<br><br>
	<table border=1>
		<tr><td colspan="4" class="magentah">Bot Players</td></tr>
<?php
	foreach ($bots as $p)
	{
		$playButton = "";
		if ($player != null)
		{	
			if ($p->getDev() == null || $p->getDev() == $player->getID())
			{
				$tId = "'".$p->getId()."'";
				$tName = "'".$p->getName()."'";
				$playButton = '<input type="button" onClick="startGame('.$tId.','.$tName.', 0);" value="Start Game">';
			}
		}		
		echo "<tr><td class='green10'>".$p->getName()."</td><td>".$playButton."</td></tr>";
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
<form name="reloadForm" action="../BattleShip/" method="post">			
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
<input type="hidden" name="ac">
</form>
<form name="gameForm" method="post">			
</form>
<script>
<?php 
if ($selectedGame != "''")
{
	echo 'populateDisplay('.$selectedGame.'); ';
}	
?>	
function startGame(opp, name, type)
{
	if (window.confirm("Start a game with " + name + "?") == true) 
	{
		var formElement = document.querySelector("gameForm");
		var formData = new FormData(formElement);
		var request = new XMLHttpRequest();
		var tsp = Date.now();
		request.open("POST", "battleship.php");
		formData.append("pid1","<?php echo $id ?>");
		formData.append("pid2", opp);
		formData.append("name1","<?php echo $nm ?>");
		formData.append("name2", name);
		formData.append("tsp", tsp);
		formData.append("action", "C" + type);
		request.send(formData);	
		document.reloadForm.ac.value = "C G<?php echo $id ?>T" + tsp;
		document.reloadForm.submit();
	}	
}
function acceptGame(passKey)
{
	var formElement = document.querySelector("gameForm");
	var formData = new FormData(formElement);
	var request = new XMLHttpRequest();
	request.open("POST", "battleship.php");
	formData.append("pid","<?php echo $id ?>");
	formData.append("game", passKey);
	formData.append("action", "A");
	request.send(formData);	
	document.reloadForm.ac.value = "A " + passKey;
	document.reloadForm.submit();
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