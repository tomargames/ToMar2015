<?php
$ac = $_POST["ac"];
//$ac = "SA150000000000000000000000033300000000300000000030000000000000000000000000000000030000000000000000000000500032220300000220230000020203000002002300000200232000022223332002000220200200020002022020000022020000000";
//$ac = "SA150000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000500032220300000220230000020203000002002300000200232000022223332002000220200200020002022020000022020000000";
?>
<html>
<form name="gameForm" method="post" action="battleship.php">			
<input type="hidden" name="id" value="<?php echo $_POST["id"]; ?>">
<input type="hidden" name="nm" value="<?php echo $_POST["nm"]; ?>">
<input type="hidden" name="ac">
<input type="hidden" name="gm">
</form>
<?php
//echo $ac."<br>";
$newAc = null;
$gm = null;
$hits = null;
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
//	echo "number of shots: ".$numberOfShots."<br>";
	if ($numberOfShots > 0)
	{	
		$numberOfShipsLeft = substr($g, 0, 1) * 1;
//		echo "number of ships left to sink: ".$numberOfShipsLeft."<br>";
		$board = Board::makeBoard();
		$board->markHits(substr($g, 1, 100));
		$shots = null;								// this is where we'll add potential shots
		if (count($hits) > 0)
		{	
			foreach ($hits as $h)
			{
				$h->worked = false;
			}
		}
//		echo "looking at ".count($hits)." hits<br>";
		while (count($hits) > 0)
		{	
			$h = array_shift($hits);
//			echo "hit is ".$h->show()."<br>";
			$found = false;
			for ($i = 0; $i < count($hits); $i++)
			{
//				echo "comparing with ".$hits[$i]->show()."<br>";
				// we're going to compare each hit with the first
				if ($h->row == $hits[$i]->row)
				{	
					if ($h->col == $hits[$i]->col - 1)			// same row, hit is to the east
					{		// shoot to the west of 0, and to the east of i
						$shots[count($shots)] = $board->board[$hits[$i]->row][$hits[$i]->col]->shootEast(); 
						$shots[count($shots)] = $board->board[$h->row][$h->col]->shootWest(); 
						$found = $hits[$i]->worked = true;
//						echo "shot east/west"."<br>";
					}
					else if ($h->col == $hits[$i]->col + 1)			// same row, hit is to the west
					{		// shoot to the west of i, and to the east of 0
						$shots[count($shots)] = $board->board[$hits[$i]->row][$hits[$i]->col]->shootWest(); 
						$shots[count($shots)] = $board->board[$h->row][$h->col]->shootEast(); 
						$found = $hits[$i]->worked = true;
//						echo "shot west/east"."<br>";
					}
				}	
				else if ($h->col == $hits[$i]->col)
				{	
					if ($h->row == $hits[$i]->row - 1)			// same col, hit is to the south
					{		
						$shots[count($shots)] = $board->board[$hits[$i]->row][$hits[$i]->col]->shootSouth(); 
						$shots[count($shots)] = $board->board[$h->row][$h->col]->shootNorth(); 
						$found = $hits[$i]->worked = true;
//						echo "shot north/south"."<br>";
					}
					else if ($h->row == $hits[$i]->row + 1)			// same col, hit is to the north
					{		
						$shots[count($shots)] = $board->board[$hits[$i]->row][$hits[$i]->col]->shootNorth(); 
						$shots[count($shots)] = $board->board[$h->row][$h->col]->shootSouth(); 
						$found = $hits[$i]->worked = true;
//						echo "shot south/north"."<br>";
					}
				}
			}	
			if ($found == false && $h->worked == false) 				//shoot all around
			{
//				echo "shooting all around it"."<br>";
				$shots[count($shots)] = $board->board[$h->row][$h->col]->shootNorth(); 
				$shots[count($shots)] = $board->board[$h->row][$h->col]->shootSouth();
				$shots[count($shots)] = $board->board[$h->row][$h->col]->shootEast(); 
				$shots[count($shots)] = $board->board[$h->row][$h->col]->shootWest();
			}	
		}	
		$acLen = 2 + 2 * $numberOfShots;
		$passCounter = 0;
		do
		{
			$passCounter += 1;
			if (count($shots) > 0)
			{	
				$sh = array_shift($shots);			// may be a shot, may be null
			}
			else
			{	
				// if the row is even, the col should be even, or vice versa
				$nr = mt_rand(0, 9);
				$nc = mt_rand(0, 9);
				if ($nc % 2 != $nr % 2)
				{
					if ($passCounter < 100)						// after 100 tries, go off-pattern in case out of moves
					{
						$nc = ($nc > 0) ? $nc - 1 : $nc + 1;
					}	
					$sh = Shot::makeShot($nr, $nc);
				}	
			}	
			if ($sh != null && $board->shoot($sh) == true)
			{
				$newAc = $newAc.$sh->pass();
//				echo $newAc."<br>";
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
echo '<script> document.gameForm.ac.value = "'.$newAc.'"; document.gameForm.gm.value = "'.$gm.'"; document.gameForm.submit(); </script>';		
//echo 'newAc is '.$newAc.'<br>';
		
class Shot
{
	public $row;
	public $col;
	public function pass()
	{
		return $this->row.$this->col;
	}
	public function show()
	{
		return $this->row.", ".$this->col;
	}
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
	public $worked;
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
		global $hits;	
		for ($row = 0; $row < 10; $row++)
		{
			for ($col = 0; $col < 10; $col++)
			{
				$this->board[$row][$col]->status = substr($boardString, $row * 10 + $col, 1) * 1;
				if ($this->board[$row][$col]->status == 3)
				{
					$hits[count($hits)] = Shot::makeShot($row, $col);
				}	
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