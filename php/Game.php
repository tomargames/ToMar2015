<?php 
	static $highest;
	static $mean;
	static $std;
	static $blurb = Array("You lost a star.", "You broke even.", "You earned a star!", "Double stars!", "Triple stars!", "ERROR!");
	
class Game 
{ 
 private $Score = null;
 public function setScore($x) { $this->Score = $x; }
 public function getScore() { return $this->Score; }
 private $Count = null;
 public function setCount($x) { $this->Count = $x; }
 public function getCount() { return $this->Count; }
 private $Tsp = null;
 public function setTsp($x) { $this->Tsp = $x; }
 public function getTsp() { return $this->Tsp; }
} 
function GameFromXML()
{ 
	$arr = null;
	$table = simplexml_load_file('Game.xml');
	$total = 0;
	$count = 0;
	global $mean;
	global $highest;
	global $std;
	foreach($table->children() as $record)	
	{ 
		$x = new Game();
		foreach($record->children() as $attr) 
		{ 
			if ($attr->getName() == 'Score') 
	 		{ 
				$x->setScore(trim($attr)); 
			}  
	 		if ($attr->getName() == 'Count') 
	 		{ 
				$x->setCount(trim($attr)); 
			}  
	 		if ($attr->getName() == 'Tsp') 
	 		{ 
				$x->setTsp(trim($attr)); 
			}  
		  $highest = ($x->getScore() > $highest) ? $x->getScore() : $highest;
			for ($i = 0; $i < $x->getCount(); $i++)
			{
				$total += $x->getScore();
				$count += 1;
			}
		} 
		$arr[$x->getScore()] = $x; 
	} 
	$mean = round($total / $count);		
	$std = StandardDeviationFromMean($arr, $mean);
 	return $arr; 
} 
function writeGameXML($arr)	
{
	$xmlString = "<?xml version='1.0' encoding='ISO-8859-1'?><root>";
	foreach ($arr as $a) 
	{ 
		$t1 = '<Game>';
		$t1 = $t1.'<Score>'.$a->getScore().'</Score>'; 
		$t1 = $t1.'<Count>'.$a->getCount().'</Count>'; 
		$t1 = $t1.'<Tsp>'.$a->getTsp().'</Tsp>'; 
		$t1 = $t1.'</Game>'; $xmlString = $xmlString.$t1;	
	} 
	$xmlString = $xmlString.'</root>'; 
	$xml2 = str_ireplace('\\','',$xmlString); 
	$file=fopen('Game.xml','w'); 
	fwrite($file,$xml2); 
	fclose($file); 	
}
function StandardDeviationFromMean($s, $m)
{
	$total = 0;
	$count = 0;
	foreach ($s as $item)
	{
		for ($i = 0; $i < $item->getCount(); $i++)
		{
			$total += pow($item->getScore() - $m, 2);
			$count += 1;
		}	
	}
	return round(sqrt($total / $count));		
}
function CalculateAward($sc, $tsp, $gameArray)
{
	global $mean;
	global $highest;
	global $std;
	if (array_key_exists($sc, $tsp, $gameArray))
	{
		$score = $gameArray[$sc];
		if ($score->getTsp() != $tsp)
		{
			$score->setCount($score->getCount() + 1);
			$score->setTsp($tsp);
		}
		else
		{
			return 4;
		}		
	}
	else
	{
		$score = new Game();
		$score->setScore($sc);
		$score->setCount(1);
		$score->setTsp($tsp);
	}		
	$gameArray[$sc] = $score;
	usort($gameArray, 'sortGames');
	writeGameXML($gameArray);
	$min = $mean - $std;
	$m1 = $mean + $std;
	$m2 = $m1 + $std;
	if ($sc < $min)
	{
		return -1;
	}	
	if ($sc < $mean)
	{
		return 0;
	}	
	if ($sc < $m1)
	{
		return 1;
	}	
	if ($sc < $m2)
	{
		return 2;
	}
	return 3;
}	
function sortGames($f1, $f2) 
{
 $a = trim($f1->getScore());
 $b = trim($f2->getScore());
 return $a < $b ? 1 : -1; 
}
?> 
