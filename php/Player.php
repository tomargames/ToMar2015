<?php 
class Player 
{ 
	 private $ID = null;
	 public function setID($x) { $this->ID = $x; }
	 public function getID() { return $this->ID; }
	 private $Name = null;
	 public function setName($x) { $this->Name = $x; }
	 public function getName() { return $this->Name; }
	 private $Level = null;
	 public function setLevel($x) { $this->Level = $x; }
	 public function getLevel() { return $this->Level; }
	 private $Stars = null;
	 public function setStars($x) { $this->Stars = $x; }
	 public function getStars() { return $this->Stars; }
	 private $Games = null;
	 public function setGames($x) { $this->Games = $x; }
	 public function getGames() { return $this->Games; }
	 private $Tsp = null;
 	 public function setTsp($x) { $this->Tsp = $x; }
 	 public function getTsp() { return $this->Tsp; }
	function processAward($award,$tsp)
	{
		  if ($this->getTsp() == $tsp)
		  {
		  	return "duplicate!";
		  }
		  else
		  {	
				$this->setGames($this->getGames() + 1);
				$this->setTsp($tsp);
				$stars = $this->getStars() + $award;
				if ($stars > 4)
				{
					$this->setLevel($this->getLevel() + 1);
					$this->setStars(2);
					return " Ranking up to ".$this->getLevel()."!";
				}
				elseif ($stars < 0)
				{
					if ($this->getLevel() < 2)
					{
						$this->setStars(0);
						return " Keep trying! ";
					}
					else
					{	
						$this->setLevel($this->getLevel() - 1);
						$this->setStars(2);
						return " Ranking down to ".$this->getLevel().".";
					}	
				}
				else
				{
					$this->setStars($stars);
					return "";
				}
			}	
	}
}
function thisPlayer($id, $nm, $players)
{
	if (array_key_exists($id,$players))
	{
		$player = $players[$id];
		$player->setName($nm);
	}		
	else
	{
		$player = new Player();
		$player->setID($id);
		$player->setName($nm);
		$player->setGames(0);
		$player->setLevel(1);
		$player->setStars(2);
		$players[$id] = $player;
	}
	return $player;
}	 
function PlayerFromXML() 	
{ 
 	$returnArray = null; 
 	$table = simplexml_load_file('Player.xml');
	foreach($table->children() as $record)	
	{ 
 		$x = new Player();
 		foreach($record->children() as $attr) 
 		{ 
  		if ($attr->getName() == 'ID') 
  		{ 
 				$x->setID(trim($attr)); 
 			}  
  		if ($attr->getName() == 'Name') 
  		{ 
 				$x->setName(trim($attr)); 
 			}  
  		if ($attr->getName() == 'Level') 
  		{ 
 				$x->setLevel(trim($attr)); 
 			}  
  		if ($attr->getName() == 'Stars') 
  		{ 
 				$x->setStars(trim($attr)); 
 			}  
  		if ($attr->getName() == 'Games') 
  		{ 
 				$x->setGames(trim($attr)); 
 			}
			if ($attr->getName() == 'Tsp') 
			{ 
				$x->setTsp(trim($attr)); 
			}  
 		} 
 		$returnArray[$x->getID()] = $x; 
 	} 
	$newArray = null; 
	usort($returnArray, 'sortArray'); 
	foreach($returnArray as $r)  
	{  	
		$newArray[$r->getID()] = $r;  
	} 
	return $newArray; 
} 
function writePlayerXML($arr)	
{
	$xmlString = "<?xml version='1.0' encoding='ISO-8859-1'?><root>";
 	foreach ($arr as $a) 
 	{ 
 		$t1 = '<Player>';
	 	$t1 = $t1.'<ID>'.$a->getID().'</ID>'; 
	 	$t1 = $t1.'<Name>'.$a->getName().'</Name>'; 
	 	$t1 = $t1.'<Level>'.$a->getLevel().'</Level>'; 
	 	$t1 = $t1.'<Stars>'.$a->getStars().'</Stars>'; 
	 	$t1 = $t1.'<Games>'.$a->getGames().'</Games>'; 
	 	$t1 = $t1.'<Tsp>'.$a->getTsp().'</Tsp>'; 
	 	$t1 = $t1.'</Player>'; $xmlString = $xmlString.$t1;
	} 
	$xmlString = $xmlString.'</root>'; 
//	echo ("<script> alert('writing ".$xmlString."'); </script>");
//	debug("this should be an alert.");
	$xml2 = str_ireplace('\\','',$xmlString); 
	$file=fopen('Player.xml','w'); 
	fwrite($file,$xml2); 
	fclose($file); 	
} 
function sortArray($f1, $f2) {
 $a = trim($f1->getLevel());
 $b = trim($f2->getLevel());
 return $a < $b ? 1 : -1; }
?> 
