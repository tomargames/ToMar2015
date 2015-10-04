<?php
require 'bFunctions.php';
$log=fopen('gamesLog.txt','w'); 
//rch * 5, then untested board - 35 bytes
$dummy = "110120130140150";
$playerBoard = "00000000000000000000";
$games = gamesFromXML(0);
writeGameXML($games,"B");
$action = $_POST["action"];				// you will always have an action
if ($action != null)
{
	fwrite($log,"action = ".$action."\n"); 
	if (firstChar($action) == "C")
	{	
		$games = createGame($log, $dummy.$playerBoard, $games, substr($action, 1, 1));		// will be 0 for bot, 1 for human player
	}	
	else 
	{	
		$k = "'".$_POST["game"]."'";
		fwrite($log,"game key is ".$k."\n"); 
		$game = $games[$k];
		fwrite($log,"found game ".$game->getTSP()."\n"); 
		$pid = $_POST["pid"];
		fwrite($log,"pid is ".$pid."\n"); 
		if (firstChar($action) == "A")
		{	
			if ($pid == $game->getPID2())
			{
				fwrite($log,$pid." accepted the game, setting status to P2.\n"); 
				$game->setStatus("P2".$dummy.$playerBoard.$dummy.$playerBoard);	//both players need to place boats
			}
			else
			{
				fwrite($log,$pid." did not match ".$game->getPID2().", game not accepted.\n"); 
			}	
		}	
		else if (firstChar($action) == "P")
		{
			$p0 = $game->getP1();
			fwrite($log,"p0 is ".$p0."\n"); 
			$p1 = $game->getP2();
			fwrite($log,"p1 is ".$p1."\n"); 
			$n0 = $game->getPID1();
			fwrite($log,"p0 is ".$n0."\n"); 
			$n1 = $game->getPID2();
			fwrite($log,"p1 is ".$n1."\n"); 
			if ($game->getStage() == "P")				// otherwise you shouldn't be here at all
			{
				if ($pid == $game->getPID1())
				{
					if ($game->getActor() != 1)    // if it's 0 or 2, this person needs to place
					{
						$p0 = "".substr($action, 1).$playerBoard;
						fwrite($log,"player0 ship placement is ".$p0."\n");
						if ($game->getActor() == 2)	 // will update status to P1 so other player can place
						{
							fwrite($log,"setting status to P1".$p0.$p1."\n");
							$game->setStatus("P1".$p0.$p1);
						}
						else					// all ships are placed, go to A status and pick who goes first	
						{
							fwrite($log,"all ships are placed, setting status to A.\n"); 
							$game->setStatus("A".mt_rand(0, 1).$p0.$p1);
						}
					}
					else
					{
						fwrite($log,"user has already placed ships!\n"); 
					}
				}	
				else if ($pid == $game->getPID2())
				{
					if ($game->getActor() != 0)    // if it's 1 or 2, this person needs to place
					{
						$p1 = "".substr($action, 1).$playerBoard;
						fwrite($log,"player1 ship placement is ".$p1."\n");
						if ($game->getActor() == 2)	 // will update status to P0 so other player can place
						{
							fwrite($log,"setting status to P0".$p0.$p1."\n");
							$game->setStatus("P0".$p0.$p1);
						}
						else					// all ships are place, go to A status and pick who goes first	
						{
							fwrite($log,"all ships are placed, setting status to A.\n"); 
							$game->setStatus("A".mt_rand(0, 1).$p0.$p1);
						}
					}
					else
					{
						fwrite($log,"user has already placed ships!\n"); 
					}
				}
				else
				{
					fwrite($log,"pid did not match any game participant!\n"); 
				}
			}	
			else
			{
				fwrite($log, "game stage of ".$game->getStage()." did not match action of P.\n");
			}	
		}
		else
		{
			fwrite($log, "unrecognized action.\n");
		}	
	}	
	writeGameXML($games,"A");
}	
else
{
	fwrite($log, "action was null.\n");
}	
fclose($log); 	

function createGame($log, $board, $games, $type)
{
	$pid1 = $_POST["pid1"];
	$pid2 = $_POST["pid2"];
	$name1 = $_POST["name1"];
	$name2 = $_POST["name2"];
	$tsp = $_POST["tsp"];
	fwrite($log,"creating new game\n".$pid1."\n".$pid2."\n".$tsp."\n"); 
	$games[$pid1.$tsp] = new bGame();
	$games[$pid1.$tsp]->setPID1($pid1); 
	$games[$pid1.$tsp]->setPID2($pid2); 
	$games[$pid1.$tsp]->setName1($name1); 
	$games[$pid1.$tsp]->setName2($name2); 
	$games[$pid1.$tsp]->setTSP($tsp);
	// /*first byte of status is gameStage (Connecting, Placing, Active, or Inactive)
	// -- if playing a bot(type == 0), connection is assumed, so status will be P
	// -- if playing a human(type == 1), status will be C until second player accepts game
	// C  means two-human game, player1 needs to accept
	// P0 means player0 only needs to place boats 
	// P1 means player1 only needs to place boats 
	// P2 means both humans need to place; will update to P0 or P1 when someone places
	// second byte of status is playerNumber whose action is required next (2 for either/both)
	// next 35 bytes are player0, then 35 bytes for player1 */
	if ($type == "0")
	{
		$status = "P2".$board.$board;	//player0 will place boats next
	}	
	else
	{	
		$status = "C";														//player1 must connect
	}	
	fwrite($log,"setting status to ".$status."\n"); 
	$games[$pid1.$tsp]->setStatus($status);
	return $games;
}	
?>
