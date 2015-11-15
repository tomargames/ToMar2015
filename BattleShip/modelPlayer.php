<?php
$ac = $_POST["ac"];
//uncomment the following line to simulate input from battleship.php
//$ac = "SA1520000000000300000000002000000000030000000000200000000000000000000000000000000000000000000000000000005100000000001000aa00000100000000081000000008010000000800044440002222200060000000006000000000600000000";
?>
<html>
<form name="gameForm" method="post" action="http://www.tomargames.com/ToMar2015/BattleShip/battleship.php">			
<input type="hidden" name="id" value="<?php echo $_POST["id"]; ?>">
<input type="hidden" name="nm" value="<?php echo $_POST["nm"]; ?>">
<input type="hidden" name="ac">
<input type="hidden" name="gm">
</form>
<?php
$newAc = null;
$gm = null;
/*
<form name="gameForm" method="post" action="http://localhost/ToMar2015/BattleShip/battleship.php">			
	If you would like to create your own Bot player, here are some things to know:
		1. This is a working player that does everything randomly, and you can feel free to modify it.
		2. It communicates via the gameForm above, and uses javascript to populate and submit the form.
		3. This player is written in PHP, and includes some PHP classes for your convenience.
		4. You can use those classes as is, modify them, or throw them out completely.
		5. Your process only needs to post the appropriate information, by whatever means you choose.
		6. When you register your Bot (before you start developing), you'll give me a url to post to.
		7. Your player will live on your own server, so you can collect information as you need it and not bother me.
		8. Until your Bot is ready for prime time, it will live in Development mode, and will appear only to you.
		
	Bot player will accept a post from battleship.php, and will post back to it using gameForm.
	The id and nm fields will always be filled in, and posted back untouched.
	(They represent the human player that your Bot is in a game with, and who will see the results.)
	The gm field will hold the gameId for the Posting and Shooting actions.
	Bot players will always be player1, playing a human player0.
	The ac field will be one of the following:
	A[gameId] -- when a game is created, and you send it back untouched to accept the game
	P[gameId] -- the response to this is ship placement
		1. Put the gameId in the gm field (you get it from the ac coming in, after the P)
		2. Response must start with P1 (signaling that player1's ship placement follows)
		3. For each of 5 boats, send row(0 - 9), column(0 - 9), and 0 for vertical or 1 for horizontal
		4. If the ship placement is rejected, you should see an error message on the screen
	S[nextActor][player0 number of ships][player0 board][player1 number of ships][player1 board]
		1. SA1 will always be in the first 3 columns
		2. player0 number of ships will be in column 4, in case you need it
		3. player0 board is the next 100 columns (5 - 104) -- this is the one you shoot at
		4. each character represents a square on the board, untested(0), miss(2), or hit(3)
		5. your number of ships left alive is in column 105, and this is how many shots you get
		6. the current status of your board follows, starting in column 106
*/
if (substr($ac, 0, 1) == "A")
{
// if A, you just need to accept the game, so post the same ac value back to battleship.php
	echo '<script> document.gameForm.ac.value = "'.$ac.'"; document.gameForm.submit(); </script>';		
}
else if (substr($ac, 0, 1) == "S")					// you need to send back shots
{
	$gm = $_POST["gm"];
	$newAc = "S1";
	$g = substr($ac, 3);					// this leaves you with 101 bytes for each player 
	// 101 = 1 (number of shots) + 100 bytes with status of each square
	// 0 is untested  2 is a miss   3 is a hit
	// you are player 1, so character at position 101 is your number of shots
	$numberOfShots = substr($g, 101, 1) * 1;
	if ($numberOfShots > 0)
	{	
		$numberofShipsLeft = substr($g, 0, 1);
		$board = Board::makeBoard();
		$board->markHits(substr($g, 1, 100));
		$acLen = 2 + 2 * $numberOfShots;
		do
		{
			$sh = Shot::makeShot(mt_rand(0, 9), mt_rand(0, 9));
			if ($board->shoot($sh) == true)
			{
				$newAc = $newAc.$sh->row.$sh->col;
			}
		}	while(strlen($newAc) < $acLen);	
	}
	else
	{
		echo "This should not happen, Silly has lost the game.";
		$newAc = "ERROR";
	}	
}
else if (substr($ac, 0, 1) == "P")
{
// if P, you put your ships after P1 (bot is always player1) in ac
//   15 characters: 5 instances of row(0-9), column(0-9), and 0(vertical) or 1(horizontal)
//	 put the gameId in gm, and then back to battleship.php
	$gm = substr($ac, 1);
	$newAc = "P1";
	$board = Board::makeBoard();
	for ($s = 0; $s < 5; $s++)
	{
		do
		{
			$board->erase($s + 1);
			$r = mt_rand(0, 9);
			$c = mt_rand(0, 9);
			$h = mt_rand(0, 1);
		} while ($board->place($s, $r, $c, $h) == false);
		$newAc = $newAc.$r.$c.$h;
	}
}
//if you comment out this line, it won't post back to battleship.php
echo '<script> document.gameForm.ac.value = "'.$newAc.'"; document.gameForm.gm.value = "'.$gm.'"; document.gameForm.submit(); </script>';		
//echo 'newAc is '.$newAc.'<br>';

class Shot
{
	public $row;
	public $col;
	public static function makeShot($r, $c)
	{
		$s = new Shot();
		if ($r > -1 && $r < 10)
		{
			if ($c > -1 && $c < 10)
			{
				$s->row = $r;
				$s->col = $c;
				return $s;
			}
		}	
		return null;
	}
}

class Square
{
	public $shot;
	public $status;
	public $contents;
	public static function makeSquare($r, $c)
	{
		$s = new Square();
		$s->shot = Shot::makeShot($r, $c);
		$s->status = 0;
		$s->contents = 0;
		return $s;
	}
	public function shootNorth()
	{
		return Shot::makeShot($this->shot->row - 1, $this->shot->col);
	}
	public function shootSouth()
	{
		return Shot::makeShot($this->shot->row + 1, $this->shot->col);
	}
	public function shootEast()
	{
		return Shot::makeShot($this->shot->row, $this->shot->col + 1);
	}
	public function shootWest()
	{
		return Shot::makeShot($this->shot->row, $this->shot->col - 1);
	}
}
class Board
{
	public static $lengths = array(5, 4, 3, 3, 2);
	public $board = null;
	
	public static function makeBoard()
	{
		$b = new Board();
		for ($row = 0; $row < 10; $row++)
		{
			for ($col = 0; $col < 10; $col++)
			{
				$b->board[$row][$col] = Square::makeSquare($row, $col);
			}
		}	
		return $b;
	}
	public function markHits($boardString)
	{
		for ($row = 0; $row < 10; $row++)
		{
			for ($col = 0; $col < 10; $col++)
			{
				$this->board[$row][$col]->status = substr($boardString, $row * 10 + $col, 1) * 1;
			}
		}	
	}
	function shoot($shot)
	{
		if ($this->board[$shot->row][$shot->col]->status == 0)
		{
			$this->board[$shot->row][$shot->col]->status = 1;
			return true;
		}
		return false;
	}
	public function place($id, $r, $c, $h)
	{
		$safe = true;
		for ($i = 0; $i < self::$lengths[$id]; $i++)
		{
			if (Shot::makeShot($r, $c) == null)
			{
				return false;
			}	
			if ($this->board[$r][$c]->contents == NOSHIP)
			{
				$this->board[$r][$c]->contents = ($id + 1);
			}
			else 
			{
				return false;
			}	
			$c = ($h == 1) ? $c + 1 : $c;
			$r = ($h == 1) ? $r : $r + 1;
		}
		return true;	
	}	
	function erase($ch)
	{
		for ($row = 0; $row < 10; $row++)
		{
			for ($col = 0; $col < 10; $col++)
			{
				if ($this->board[$row][$col]->contents == $ch)
				{
					$this->board[$row][$col]->contents = 0;
				}	
			}
		}
	}	
}
?>	
</html>