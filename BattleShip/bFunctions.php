<?php 
require '../php/tmUtils.php';

class bBoard
{
	public static $lengths = array(5, 4, 3, 3, 2);
	public $board = null;
	public static function makeBoard()
	{
		$b = new bBoard();
		for ($row = 0; $row < 10; $row++)
		{
			for ($col = 0; $col < 10; $col++)
			{
				$b->board[$row][$col] = bSquare::makeSquare($row, $col);
			}
		}	
		return $b;
	}
	public function packStatus()
	{
		$returnString = "";
		$subString = "";
		for ($row = 0; $row < 10; $row++)
		{
			for ($col = 0; $col < 10; $col++)
			{
				$subString = $subString.$this->board[$row][$col]->status;
				if (strlen($subString) == 5)
				{
					$returnString = $returnString.base_convert($subString, 2, 32);
					$subString = "";
				}	
			}
		}
		return $returnString;	
	}
	public function getShipLength($s)
	{
		return self::$lengths[$s];
	}
	public function encode($rel, $p)
	{
		$returnStatus = "";
		for ($row = 0; $row < 10; $row++)
		{
			for ($col = 0; $col < 10; $col++)
			{
				if ($rel == $p || $rel == 3)
				{	
					$returnStatus = $returnStatus.$this->board[$row][$col]->encode();
				}
				else		
				{	
					$returnStatus = $returnStatus.$this->board[$row][$col]->mask();
				}
			}
		}
//	echo "bBoard.encode function returning ".$returnStatus."<br>"; 
		return $returnStatus;	
	}
	public function markHits($boardString)
	{	// string will be 20 characters, base32, convert to 100 binary
		$newString = "";
		for ($i = 0; $i < 20; $i++)
		{
			$newString = $newString.formatNumber(base_convert(substr($boardString, $i, 1), 32, 2), 5);
		}
//	echo "bBoard.markHits function, expanded board is ".$newString."<br>"; 
		for ($row = 0; $row < 10; $row++)
		{
			for ($col = 0; $col < 10; $col++)
			{
				$this->board[$row][$col]->status = substr($newString, $row * 10 + $col, 1) * 1;
			}
		}	
	}
	function shoot($str)
	{
		$r = substr($str, 0, 1);
		$c = substr($str, 1, 1);
		if ($this->board[$r][$c]->status == 0)
		{
			$this->board[$r][$c]->status = 1;
			return true;
		}
		return false;
	}
	function validSquare($r, $c)
	{
		if ($r > -1 && $r < 10)
		{
			if ($c > -1 && $c < 10)
			{
				return true;
			}
		}	
		return false;
	}
	public function place($id, $shipString, $stage)
	{
		$notFound = false;
		$safe = true;
		$r = substr($shipString, 0, 1);
		$c = substr($shipString, 1, 1);
		$h = substr($shipString, 2, 1);
		for ($i = 0; $i < self::$lengths[$id]; $i++)
		{
			if ($this->validSquare($r, $c) == false)
			{
				return false;
			}	
			if ($this->board[$r][$c]->contents == NOSHIP)
			{
				$this->board[$r][$c]->contents = ($id + 1);
			}
			else if ($stage == "P")
			{
				return false;
			}	
			if ($this->board[$r][$c]->status == UNTESTED)
			{
				$notFound = true;
			}	
			$c = ($h == 1) ? $c + 1 : $c;
			$r = ($h == 1) ? $r : $r + 1;
		}
		if ($stage == "A")
		{	
			return $notFound;
		}
		return true;	
	}	
}
class bSquare
{
	public $row;
	public $col;
	public $status;
	public $contents;
	public static function makeSquare($r, $c)
	{
		$s = new bSquare();
		$s->row = $r;
		$s->col = $c;
		$s->status = 0;
		$s->contents = 0;
		return $s;
	}
	public function mask()
	{ // if square is untested, just return it; otherwise, show hit or miss
		if ($this->status == 0)
		{
			return 0;														
		}	
		// square has been tested, so it will be either a hit or a miss
		return ($this->contents > 0) ? 3 : 2;
	}
	public function encode()
	{
		$t = 2 * $this->contents + $this->status;
		return base_convert($t, 10, 12);
	}
	public function decode($t)
	{
		$n = base_convert($t, 12, 10);
		$this->status = $n % 2;
		$this->contents = floor($n / 2);
	}
}
class bPlayer 
{
	public $ID = null;								// humans have numeric ID, bots have URL as ID
	public $name = null;
	public $dev = null;								// if filled with an ID, bot is in test mode, no stats kept
	public $gameCount = 0;
	public $winCount = 0;
	public function makePlayer($ID, $name)
	{
		$p = new bPlayer();
		$p->ID = $ID;
		$p->name = $name;
		return $p;
	}
}
class bGame
{
	public $PID1 = null;
	public $PID2 = null;
	public $Name1 = null;
	public $Name2 = null;
	public $TSP = null;
	public $Status = null;
	public $gameStage = null;
	public $nextActor = null;
	public $boards = null;
	public $shipsLeft = null;
	
	public function deliverToClient($rel)
	{
		$returnStatus = $this->gameStage.$this->nextActor;
		for ($player = 0; $player < 2; $player++)
		{
			$returnStatus = $returnStatus.$this->shipsLeft[$player];
			$returnStatus = $returnStatus.$this->boards[$player]->encode($rel, $player);
		}
		return $returnStatus;	
	}
	public function unpackStatus()
	{
		// this completely decodes the status, without regard for rel
		$this->gameStage = substr($this->Status, 0, 1);
		$this->nextActor = substr($this->Status, 1, 1) * 1;
		// next 35 chars are player0 ships + board hits, then 35 for player1
		for ($player = 0; $player < 2; $player++)
		{
			$this->shipsLeft[$player] = 0;
			$this->boards[$player] = bBoard::makeBoard();
			$this->boards[$player]->markHits(substr($this->Status, 2 + ($player * 35) + 15, 20));
			for ($s = 0; $s < 5; $s++)
			{
				if ($this->boards[$player]->place($s, substr($this->Status, 2 + ($player * 35) + (3 * $s), 3), "A") == true)
				{
					$this->shipsLeft[$player] += 1;
				}	
			}	
		}	
	}
	public function getStage() { return substr($this->Status, 0, 1); }
	public function getActor() { return substr($this->Status, 1, 1); }
	public function getP1() { return substr($this->Status, 2, 35); }
	public function getP2() { return substr($this->Status, 37, 35); }
	public function getKey() { return $this->PID1.$this->TSP; }
	public function getPassKey() { return "'G".$this->PID1."T".$this->TSP."'"; }
	public function getRel($id)
	{
		if ($this->PID1 == $id)
		{
			return 0;
		}	
		else if ($this->PID2 == $id) 
		{
			return 1;
		}	
		else
		{
			return 2;
		}	
	}
}
function writePlayerXML($arr, $fileName)	
{
	$xmlString = "<root>";
 	foreach ($arr as $a) 
 	{ 
 		$t1 = '<Player>';
	 	$t1 = $t1.'<ID>'.$a->ID.'</ID>'; 
	 	$t1 = $t1.'<name>'.$a->name.'</name>'; 
		if ($a->dev != null)
		{
			$t1 = $t1.'<dev>'.$a->dev.'</dev>'; 
		}	
	 	$t1 = $t1.'<games>'.$a->gameCount.'</games>'; 
	 	$t1 = $t1.'<wins>'.$a->winCount.'</wins>'; 
	 	$t1 = $t1.'</Player>'; $xmlString = $xmlString.$t1;
	} 
	$xmlString = $xmlString.'</root>'; 
	$xml2 = str_ireplace('\\','',$xmlString); 
	$file=fopen($fileName,'w'); 
	fwrite($file,$xml2); 
	fclose($file); 	
} 
function writeGameXML($arr, $type)	
{
	$xmlString = "<root>";
//echo count($arr);
	if (count($arr) > 0)
	{	
		foreach ($arr as $a) 
		{ 
			$t1 = '<Game>';
			$t1 = $t1.'<PID1>'.$a->PID1.'</PID1>'; 
			$t1 = $t1.'<PID2>'.$a->PID2.'</PID2>'; 
			$t1 = $t1.'<Name1>'.$a->Name1.'</Name1>'; 
			$t1 = $t1.'<Name2>'.$a->Name2.'</Name2>'; 
			$t1 = $t1.'<TSP>'.$a->TSP.'</TSP>'; 
			$t1 = $t1.'<Status>'.$a->Status.'</Status>'; 
			$t1 = $t1.'</Game>'; $xmlString = $xmlString.$t1;
		}
	}		
	$xmlString = $xmlString.'</root>'; 
	$xml2 = str_ireplace('\\','',$xmlString); 
	if ("B" == $type)
	{	
		$file=fopen('gamesBackup.xml','w'); 
	}
	else if ("O" == $type)
	{	
		$file=fopen('gamesArchive.xml','w'); 
	}
	else
	{	
		$file=fopen('games.xml','w'); 
	}
	fwrite($file,$xml2); 
	fclose($file); 	
} 
function playersFromXML($fileName) 	
{ 
 	$returnArray = null; 
	$table = simplexml_load_file($fileName);
	foreach($table->children() as $record)	
	{ 
 		$x = new bPlayer();
 		foreach($record->children() as $attr) 
 		{ 
  		if ($attr->getName() == 'ID') 
  		{ 
 				$x->ID = trim($attr); 
 			}  
  		else if ($attr->getName() == 'name') 
  		{ 
 				$x->name = trim($attr); 
 			}  
  		else if ($attr->getName() == 'dev') 
  		{ 
 				$x->dev = trim($attr); 
 			}  
  		else if ($attr->getName() == 'games') 
  		{ 
 				$x->gameCount = trim($attr); 
 			}  
  		else if ($attr->getName() == 'wins') 
  		{ 
 				$x->winCount = trim($attr); 
 			}  
 		}
		$returnArray[$x->ID] = $x; 
 	} 
	$newArray = null; 
	usort($returnArray, 'sortArray'); 
	foreach($returnArray as $r)  
	{  	
		$newArray[$r->ID] = $r;  
	} 
	return $newArray; 
}
function getGames($pid) 
{
	$games = gamesFromXML("A");
	// if status is anything but A, you don't need to do anything
	foreach ($games as $g)
	{
		if ($g->getStage() == "A")				// active game, need to convert based on pid
		{
			if ($pid == $g->PID1)
			{
				$rel = 0;
			}	
			else if ($pid == $g->PID2)
			{
				$rel = 1;
			}	
			else
			{
				$rel = 2;
			}	
			$g->unpackStatus();
			$g->Status = $g->deliverToClient($rel);
		}
		else if ($g->getStage() == "O")    // game is over, can see everything now
		{
			$g->unpackStatus();
			$g->Status = $g->deliverToClient(3);
		}
	}
	return $games;
}
function getArchive()
{
	$games = gamesFromXML("O");
	foreach ($games as $g)
	{
		$g->unpackStatus();
		$g->Status = $g->deliverToClient(3);
	}
	return $games;
}
function gamesFromXML($type)
{
 	$returnArray = null;
	if ($type == "O")
	{
		$table = simplexml_load_file("gamesArchive.xml");
	}	
	else
	{
		$table = simplexml_load_file("games.xml");
	}	
	foreach($table->children() as $record)	
	{ 
 		$x = new bGame();
 		foreach($record->children() as $attr) 
 		{ 
  		if ($attr->getName() == 'PID1') 
  		{ 
 				$x->PID1 = (trim($attr)); 
 			}  
  		else if ($attr->getName() == 'PID2') 
  		{ 
 				$x->PID2 = (trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Name1') 
  		{ 
 				$x->Name1 = (trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Name2') 
  		{ 
 				$x->Name2 = (trim($attr)); 
 			}  
  		else if ($attr->getName() == 'TSP') 
  		{ 
 				$x->TSP = (trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Status') 
  		{
				$x->Status = (trim($attr)); 
			}	
		}
		$returnArray[$x->getPassKey()] = $x; 
 	} 
	$newArray = null; 
	usort($returnArray, 'sortArray'); 
	foreach($returnArray as $r)  
	{  	
		$newArray[$r->getPassKey()] = $r;  
	} 
	return $newArray; 
}
function sortArray($f1, $f2) 
{
	$a = trim($f1->Status);
	$b = trim($f2->Status);
	return ($a > $b) ? 1 : -1;
}
function gameDate($s)
{
	return substr($s, 2, 2)."/".substr($s, 4, 2)."/".substr($s, 0, 2);
}
/* $games = getGames('106932376942135580175');
foreach ($games as $g)
{
	echo $g->Status."<br>";
}
 */
?> 
