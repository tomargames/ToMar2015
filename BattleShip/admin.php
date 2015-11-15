<?php
require 'bFunctions.php';

// page will not work unless it's my id
// if ac = D, gm will be deleted
// if ac = O, all O games will be deleted, but written to gamesArchive.xml
$id = $_POST["id"];
$nm = $_POST["nm"];
$ac = $_POST["ac"];
$gm = $_POST["gm"];
if ($id != '106932376942135580175')
{
	echo "Sign in as tomargames to use this page.<br>";
}
else
{	
	$games = gamesFromXML("A");
	$players = playersFromXML("humans.xml");
	$bots = playersFromXML("bots.xml");
	if (count($games) > 0)
	{
?>		
<html>
<head>
	<title>BattleShip Admin</title>
	<script src="../js/utils.js"></script>
	<LINK REL="StyleSheet" HREF="../styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
</head>
<body>
<table border="0" width="100%" align="center"><tr>	
	<td width="10%" class="magenta10"><?php echo "action is ".$ac; ?></td>
	<td width="70%" class="biggest">BattleShip Playground Admin</td>
	<td width="20%" align="center">
	<form name="menuForm" action="index.php" method="post">			
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="nm" value="<?php echo $nm; ?>">
		<input type="submit" style="background: #FFFFCC" value="BattleShip">
	</form></td></tr>
</table>
<form name="adminForm" method="post">
	<input type="hidden" name="gm">
	<input type="hidden" name="ac">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<input type="hidden" name="nm" value="<?php echo $nm; ?>">
</form>
<table border="1">
<?php
		$newGames = null;
		$oldGames = gamesFromXML("O");
		$aCount = 0;
		$message = "Action is ".$ac.".<br>";
		$message = $message."Game is ".$gm.".<br>";
		foreach ($games as $g)
		{
//		$message = $message."Looking for ".$gm.", game is ".$g->getPassKey().".<br>";
			if ($ac == "O" && $g->getStage() == "O")
			{
				$oldGames[$g->getPassKey()] = $g;
				$aCount += 1;
				$message = $message."Archived ".$g->getPassKey()." ".$g->PID1." ".$g->PID2.".<br>";
				updatePlayer($g->PID1, $g->getActor() == 0);
				updatePlayer($g->PID2, $g->getActor() == 1);
			}	
			else if ($ac == "D" && $gm == $g->getPassKey())
			{
				echo "<tr><td>".$gm."</td><td>".$g->Name1."</td><td>".$g->Name2."</td><td>".$g->getStage()."</td><td>deleted</td></tr>";
				$message = $message."Deleted ".$g->getPassKey().".<br>";
				$aCount += 1;
			}	
			else 
			{
				$newGames[$g->getPassKey()] = $g;
				echo "<tr><td>".$g->getPassKey()."</td><td>".$g->Name1."</td><td>".$g->Name2."</td><td>".$g->getStage()."</td><td><input style='background: #CCCCFF' type=button onClick=deleteGame(".$g->getPassKey()."); value=Delete></td></tr>";
			}	
		}
		if ($aCount > 0)
		{
			writeGameXML($games, "B");			// back up existing games file
			$message = $message."Backed up current game file.<br>";
			writeGameXML($newGames, "A");			// active games to games.xml
			$message = $message."Wrote new current file.<br>";
			if ($ac == "O")
			{
				writeGameXML($oldGames, "O");			// active games to games.xml
				writePlayerXML($players, "humans.xml");
				writePlayerXML($bots, "bots.xml");
				$message = $message."Wrote archive file and updated player files.<br>";
			}	
		}
	}	
}
function updatePlayer($pid, $winner)
{
	// add 1 to games, add 1 to wins if winner
	global $players, $bots;
	if (is_numeric($pid))
	{	
		$players[$pid]->gameCount += 1;
		if ($winner == true)
		{
			$players[$pid]->winCount += 1;
		}	
	}
	else
	{	
		$bots[$pid]->gameCount += 1;
		if ($winner == true)
		{
			$bots[$pid]->winCount += 1;
		}	
	}
}
?>
</table>
<table><tr valign="top"><td>
<input type="button" style="background: #CCCCFF" onClick="archiveOldGames();" value="Archive">
</td><td>
<?php echo $message; ?>
</td></tr></table>
<script>
function archiveOldGames()
{
	if (window.confirm("Move games that are over to the archive file?") == true) 
	{
		document.adminForm.ac.value = "O"; 
		document.adminForm.submit(); 	
	}	
}
function deleteGame(gmId)
{
	if (window.confirm("Delete game '" + gmId + "'?") == true) 
	{
		document.adminForm.ac.value = "D"; 
		document.adminForm.gm.value = "'" + gmId + "'"; 
		document.adminForm.submit(); 	
	}	
}
</script>
</body>
</html>
