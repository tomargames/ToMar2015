<?php 
require '../php/tmUtils.php';

class bPlayer 
{ 
	private $ID = null;
	public function setID($x) { $this->ID = $x; }
	public function getID() { return $this->ID; }
	private $Name = null;
	public function setName($x) { $this->Name = $x; }
	public function getName() { return $this->Name; }
}
class bBot
{
	private $ID = null;
	public function setID($x) { $this->ID = $x; }
	public function getID() { return $this->ID; }
	private $Name = null;
	public function setName($x) { $this->Name = $x; }
	public function getName() { return $this->Name; }
	private $URL = null;
	public function setURL($x) { $this->URL = $x; }
	public function getURL() { return $this->URL; }
	private $Dev = null;
	public function setDev($x) { $this->Dev = $x; }
	public function getDev() { return $this->Dev; }
}
class bGame
{
	private $PID1 = null;
	public function setPID1($x) { $this->PID1 = $x; }
	public function getPID1() { return $this->PID1; }
	private $PID2 = null;
	public function setPID2($x) { $this->PID2 = $x; }
	public function getPID2() { return $this->PID2; }
	private $Name1 = null;
	public function setName1($x) { $this->Name1 = $x; }
	public function getName1() { return $this->Name1; }
	private $Name2 = null;
	public function setName2($x) { $this->Name2 = $x; }
	public function getName2() { return $this->Name2; }
	private $TSP = null;
	public function setTSP($x) { $this->TSP = $x; }
	public function getTSP() { return $this->TSP; }
	private $Status = null;
	public function setStatus($x) { $this->Status = $x; }
	public function getStatus() { return $this->Status; }
	public function getStage() { return substr($this->Status, 0, 1); }
	public function getActor() { return substr($this->Status, 1, 1); }
	public function getP1() { return substr($this->Status, 2, 35); }
	public function getP2() { return substr($this->Status, 37, 35); }
	public function getKey() { return $this->PID1.$this->TSP; }
	public function getPassKey() { return "'G".$this->PID1."T".$this->TSP."'"; }
}
function HumansFromXML() 	
{ 
 	$returnArray = null; 
	$table = simplexml_load_file('humans.xml');
	foreach($table->children() as $record)	
	{ 
 		$x = new bPlayer();
 		foreach($record->children() as $attr) 
 		{ 
  		if ($attr->getName() == 'ID') 
  		{ 
 				$x->setID(trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Name') 
  		{ 
 				$x->setName(trim($attr)); 
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
function writeHumanXML($arr)	
{
	$xmlString = "<?xml version='1.0' encoding='ISO-8859-1'?><root>";
 	foreach ($arr as $a) 
 	{ 
 		$t1 = '<Player>';
	 	$t1 = $t1.'<ID>'.$a->getID().'</ID>'; 
	 	$t1 = $t1.'<Name>'.$a->getName().'</Name>'; 
	 	$t1 = $t1.'</Player>'; $xmlString = $xmlString.$t1;
	} 
	$xmlString = $xmlString.'</root>'; 
	$xml2 = str_ireplace('\\','',$xmlString); 
	$file=fopen('humans.xml','w'); 
	fwrite($file,$xml2); 
	fclose($file); 	
} 
function writeGameXML($arr, $type)	
{
	$xmlString = "<root>";
	echo count($arr);
	if (count($arr) > 0)
	{	
		foreach ($arr as $a) 
		{ 
			$t1 = '<Game>';
			$t1 = $t1.'<PID1>'.$a->getPID1().'</PID1>'; 
			$t1 = $t1.'<PID2>'.$a->getPID2().'</PID2>'; 
			$t1 = $t1.'<Name1>'.$a->getName1().'</Name1>'; 
			$t1 = $t1.'<Name2>'.$a->getName2().'</Name2>'; 
			$t1 = $t1.'<TSP>'.$a->getTSP().'</TSP>'; 
			$t1 = $t1.'<Status>'.$a->getStatus().'</Status>'; 
			$t1 = $t1.'</Game>'; $xmlString = $xmlString.$t1;
		}
	}		
	$xmlString = $xmlString.'</root>'; 
	$xml2 = str_ireplace('\\','',$xmlString); 
	if ("B" == $type)
	{	
		$file=fopen('gamesBackup.xml','w'); 
	}
	else
	{	
		$file=fopen('games.xml','w'); 
	}
	fwrite($file,$xml2); 
	fclose($file); 	
} 
function BotsFromXML() 	
{ 
 	$returnArray = null; 
	$table = simplexml_load_file('bots.xml');
	foreach($table->children() as $record)	
	{ 
 		$x = new bBot();
 		foreach($record->children() as $attr) 
 		{ 
  		if ($attr->getName() == 'ID') 
  		{ 
 				$x->setID(trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Name') 
  		{ 
 				$x->setName(trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Dev') 
  		{ 
 				$x->setDev(trim($attr)); 
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
function gamesFromXML($cvt)
{
 	$returnArray = null; 
	$table = simplexml_load_file("games.xml");
	foreach($table->children() as $record)	
	{ 
 		$x = new bGame();
		echo "";										
 		foreach($record->children() as $attr) 
 		{ 
//		echo $attr->getName()."&nbsp; ".$attr."<br>";
  		if ($attr->getName() == 'PID1') 
  		{ 
 				$x->setPID1(trim($attr)); 
 			}  
  		else if ($attr->getName() == 'PID2') 
  		{ 
 				$x->setPID2(trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Name1') 
  		{ 
 				$x->setName1(trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Name2') 
  		{ 
 				$x->setName2(trim($attr)); 
 			}  
  		else if ($attr->getName() == 'TSP') 
  		{ 
 				$x->setTSP(trim($attr)); 
 			}  
  		else if ($attr->getName() == 'Status') 
  		{
				if ($cvt == 0)
				{
					$x->setStatus(trim($attr)); 
				}
				else
				{			
					$st = substr(trim($attr), 0, 2);
					$ptr = 2;
					for ($p = 0; $p < 2; $p++)
					{	
						$st = $st.substr(trim($attr), $ptr, 15);
						$ptr += 15;
						for ($i = 0; $i < 20; $i++)
						{	
							$chunk = substr(trim($attr), $ptr++, 1);
							$st = $st.formatNumber(base_convert($chunk, 32, 2), 5);
						}
					}	
					$x->setStatus($st); 
				}	
 			}  
 		}
		$returnArray[$x->getPassKey()] = $x; 
 	} 
	return $returnArray;
}
function sortArray($f1, $f2) 
{
 $a = trim($f1->getName());
 $b = trim($f2->getName());
 return $f1 < $f2 ? 1 : -1; 
}
?> 
