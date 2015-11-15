<?php
require 'bFunctions.php';
$id = $_POST["id"];
$nm = $_POST["nm"];
$ac = $_POST["ac"];
$gm = $_POST["gm"];	// will not have for actions Connect and Accept, will have for Place and Shoot
date_default_timezone_set("America/New_York");
$log=fopen('log'.date("ymd").'.txt','a'); 
$gameId = null;
?>
<html>
<form name="gameForm" method="post" action="../BattleShip/">
<input type="hidden" name="ac">
<input type="hidden" name="gm">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
</form>
<?php
if ($ac == null)
{
	writeToLog(false, "action is null, bailing");
}
else
{
	$games = gamesFromXML("A");
	writeGameXML($games,"B");					// back up xml before run
	if ($ac == "C")
	{
		createGame();
	}
	else if (validateInput() == true)
	{
		$logentry = "ac is ".$ac;
		if (firstChar($ac) == "A")
		{	
			$games[$gameId]->Status = ("P2");
			writeToLog(true, $logentry.", accepted by ".$games[$gameId]->PID2.", set status to P2"); 
		}
		else if (firstChar($ac) == "S")
		{ // second byte is who the user is, and it should equal the nextActor in the game
			$rel = substr($ac, 1, 1) * 1;
			$games[$gameId]->unpackStatus();
			if ($rel == $games[$gameId]->nextActor)
			{
				$shotString = substr($ac, 2);
				if (is_numeric($shotString))
				{
					if (strlen($shotString) == 2 * $games[$gameId]->shipsLeft[$rel])
					{	
						$opp = ($rel == 0) ? 1 : 0;
						$shootingOkay = true;
						for ($i = 0; $i < $games[$gameId]->shipsLeft[$rel]; $i++)
						{
							if ($games[$gameId]->boards[$opp]->shoot(substr($shotString, 0, 2)) == true)
							{
								$shotString = substr($shotString, 2);
							}
							else
							{
								writeToLog(false, $logentry.", shot ".$i." not UNTESTED");
								$shootingOkay = false;
								break;
							}		
						}
						if ($shootingOkay == true)
						{
							$returnStatus = "A".$opp;						// opponent will play next
							for ($player = 0; $player < 2; $player++)
							{
								// transfer the 15 bytes of ship placement directly from current status
								$returnStatus = $returnStatus.substr($games[$gameId]->Status, 2 + ($player * 35), 15);
								$returnStatus = $returnStatus.$games[$gameId]->boards[$player]->packStatus();
							}	
							$games[$gameId]->Status = $returnStatus;
						}	
						writeToLog($shootingOkay, $logentry.", player".$rel." shot, indicator is ".$shootingOkay);
						// before ending the turn, check to see if all boats were sunk
						$games[$gameId]->unpackStatus();
						if ($games[$gameId]->shipsLeft[0] == 0)
						{
							$games[$gameId]->Status = "O1".substr($games[$gameId]->Status, 2);
						}	
						else if ($games[$gameId]->shipsLeft[1] == 0)
						{
							$games[$gameId]->Status = "O0".substr($games[$gameId]->Status, 2);
						}	
						// this is where code to call bot player will go
						else if ($rel == 0 && !is_numeric($games[$gameId]->PID2))	// if it is bot's turn
						{
							writeGameXML($games,"A");
							$p2 = "'".$games[$gameId]->PID2."'";
							$games[$gameId]->unpackStatus();
							$s2 = "S".$games[$gameId]->deliverToClient(1);
							echo '<script> document.gameForm.action = '.$p2.'; document.gameForm.ac.value = "'.$s2.'"; document.gameForm.gm.value = "'.$gm.'"; document.gameForm.submit(); </script>';
							$gameId = null;	
						}
					}	
					else
					{
						writeToLog(false, $logentry.", shotstring ".$shotString." not correct length of ".(2 * $games[$gameId]->shipsLeft[$rel]));
					}		
				}
				else
				{
					writeToLog(false, $logentry.", shotstring ".$shotString." not numeric");
				}		
			}
			else
			{
				writeToLog(false, $logentry.", mismatch of nextActor to rel");
			}		
		}	
		else if (firstChar($ac) == "P")
		{
			if (validateShips(substr($ac, 2)) == true)
			{	
				// look at the status of the game, which players need to place boats, make sure it matches
				$st = substr($games[$gameId]->Status, 0, 2);  // first 2 chars
				if ($st == "P2")					// both players need to place
				{
					if ($user == 0)
					{
						$games[$gameId]->Status = ("P1".substr($ac,2));
						writeToLog(true, $logentry.", player0 placed ships, status is P1".substr($ac, 2)); 
						// this is where code to call bot player will go
						if (!is_numeric($games[$gameId]->PID2))		// will be true if player1 is a bot
						{
							writeGameXML($games,"A");
							$p2 = "'".$games[$gameId]->PID2."'";
							echo '<script> document.gameForm.action = '.$p2.'; document.gameForm.ac.value = "P'.$gm.'"; document.gameForm.submit(); </script>';
							$gameId = null;	
						}
					}
					else
					{
						$games[$gameId]->Status = ("P0".substr($ac,2));
						writeToLog(true, $logentry.", player1 placed ships, status is P0".substr($ac, 2)); 
					}	
				}
				else if (($st == "P1" && $user == 1) || ($st == "P0" && $user == 0))	
				{ // this player's ships are in substr($ac, 2) 
					// other player's ships are in substr($games[$gameId]->getStatus(), 2)
					// status will be A then playerToGoFirst then player0 35 (15 plus 20) then player1 35
					$sts[$user] = substr($ac, 2)."00000000000000000000";
					$sts[$opp] = substr($games[$gameId]->Status, 2)."00000000000000000000";
					$firstPlayer = mt_rand(0, 1);
					$games[$gameId]->Status = ("A".$firstPlayer.$sts[0].$sts[1]);
					writeToLog(true, $logentry.", all ships placed, status set to ".$games[$gameId]->Status); 
					if ($firstPlayer == 1)
					{
						// this is where code to call bot player will go
						if (!is_numeric($games[$gameId]->PID2))		// will be true if player1 is a bot
						{
							writeGameXML($games,"A");
							$p2 = "'".$games[$gameId]->PID2."'";
							$games[$gameId]->unpackStatus();
							$s2 = "S".$games[$gameId]->deliverToClient(1);
							echo '<script> document.gameForm.action = '.$p2.'; document.gameForm.ac.value = "'.$s2.'"; document.gameForm.gm.value = "'.$gm.'"; document.gameForm.submit(); </script>';
							$gameId = null;	
						}
					}	
				}
				else
				{
					writeToLog(false, $ac." action could not be performed, game status was ".$st); 
				}	
			}
			else
			{
				writeToLog(false, $ac." ship placement not valid"); 
			}			
		}
		else
		{
			writeToLog(false, $ac." action could not be performed, unknown action.");
		}
	}
	if ($gameId != null)				// if you are sending to a bot, set gameId to null to bypass this ending
	{
		writeGameXML($games,"A");
		echo '<script> document.gameForm.ac.value = '.$gameId.'; 	document.gameForm.submit(); </script>';
//	echo '<script> document.gameForm.ac.value = '.$gameId.';  </script>';
	}	
}	
fclose($log);
function validateShips($shipString)
{
	$board = bBoard::makeBoard();
	$charCounter = 0;
	for ($s = 0; $s < 5; $s++)
	{
		$str = substr($shipString, $charCounter, 3);
		$charCounter += 3;
		if ($board->place($s, $str, "P") == false)
		{	
			return false;
		}
	}
	return true;
}
function validateInput()
{	// will validate ac and gm, will use id and games[gameId] to populate user and opp
	global $ac, $gm, $id, $gameId, $user, $opp, $games;
	
	if (firstChar($ac) == "A")				// don't need user and opp for Accept
	{
		$gm = substr($ac, 1);
	}	
	if ($gm == null)
	{
		writeToLog(false, $ac." action could not be performed, no game specified"); 
		return false;
	}	
	$gameId = "'".$gm."'";
	if ($games[$gameId] == null)
	{
		writeToLog(false, $ac." action could not be performed, could not find game record for ".$gameId); 
		$gameId = null;
		print_r($games);
		return false;
	}
	if (firstChar($ac) == "A")				// don't need user and opp for Accept
	{
		return true;
	}	
	$user = substr($ac, 1, 1) * 1;	// player number placing boats, game status should match up
	$opp = ($user == 0) ? 1 : 0;
	return true;
}
function createGame()
{
	global $games, $gameId, $id, $nm;
	$tsp = date("ymdhis");					// timestamp for gameId
	$g = "G".$id."T".$tsp;
	$gameId = "'G".$id."T".$tsp."'";
	$games[$gameId] = new bGame();
	$games[$gameId]->PID1 = ($id); 
	$games[$gameId]->PID2 = ($_POST["pid2"]); 
	$games[$gameId]->Name1 = ($nm); 
	$games[$gameId]->Name2 = ($_POST["name2"]); 
	$games[$gameId]->TSP = ($tsp);
	$games[$gameId]->Status = ("C");
	writeToLog(true, "created game with ".$games[$gameId]->PID2);
	if (!is_numeric($games[$gameId]->PID2))				// will be true if player1 is a bot
	{
		writeGameXML($games,"A");
		$p2 = "'".$games[$gameId]->PID2."'";
		echo '<script> document.gameForm.action = '.$p2.'; document.gameForm.ac.value = "A'.$g.'"; document.gameForm.submit(); </script>';
		$gameId = null;	
	}
}
function writeToLog($ok, $msg)
{
	global $log, $gameId, $gm;
	fwrite($log, date("ymdhis")." ".$gm.": ".$msg."\n"); 
	if (!$ok)
	{
		echo $gameId.": ".$msg;
		$gameId = null;
	}	
}
?>
</html>