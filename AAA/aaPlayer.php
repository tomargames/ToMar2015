<?php 
class aaPlayer 
{ 
	private $ID = null;
	public function setID($x) { $this->ID = $x; }
	public function getID() { return $this->ID; }
	private $Name = null;
	public function setName($x) { $this->Name = $x; }
	public function getName() { return $this->Name; }
	private $Level = 1;
	public function setLevel($x) { $this->Level = $x; }
	public function getLevel() { return $this->Level; }
	private $Start = 0;
	public function setStart($x) { $this->Start = $x; }
	public function getStart() { return $this->Start; }
	private $Tsp = "";
 	public function setTsp($x) { $this->Tsp = $x; }
 	public function getTsp() { return $this->Tsp; }
}
function PlayersFromXML() 	
{ 
 	$returnArray = null; 
	$table = simplexml_load_file('aa.xml');
	foreach($table->children() as $record)	
	{ 
 		$x = new aaPlayer();
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
  		if ($attr->getName() == 'StartTsp') 
  		{ 
 				$x->setStart(trim($attr)); 
 			}  
  		if ($attr->getName() == 'EndTsp') 
  		{ 
 				$x->setTsp(trim($attr)); 
 			}  
 		}
		$returnArray[$x->getID().$x->getStart()] = $x; 
 	} 
	$newArray = null; 
	usort($returnArray, 'sortArray'); 
	foreach($returnArray as $r)  
	{  	
		$newArray[$r->getID().$r->getStart()] = $r;  
	} 
	return $newArray; 
} 
function sortArray($f1, $f2) 
{
 $a = trim($f1->getLevel());
 $b = trim($f2->getLevel());
 return $a < $b ? 1 : -1; 
}
function getPlayer($id, $players)
{
	foreach ($players as $p)
	{
		if ($p->getID() == $id)
		{
			if (!is_finite($p->getTsp())) 		// will only be true if game is not active
			{
				return $p;
			}
		}	
	}
	$player = new aaPlayer();
	$player->setID($id);
	$player->setName($nm);
	return $player;
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
	 	$t1 = $t1.'<StartTsp>'.$a->getStart().'</StartTsp>'; 
	 	$t1 = $t1.'<EndTsp>'.$a->getTsp().'</EndTsp>'; 
	 	$t1 = $t1.'</Player>'; $xmlString = $xmlString.$t1;
	} 
	$xmlString = $xmlString.'</root>'; 
	$xml2 = str_ireplace('\\','',$xmlString); 
	$file=fopen('aa.xml','w'); 
	fwrite($file,$xml2); 
	fclose($file); 	
} 
?> 
