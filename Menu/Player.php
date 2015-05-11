<?php 
class Player { 
 private $ID = null;
 public function setID($x) { $this->ID = $x; }
 public function getID() { return $this->ID; }
 private $Name = null;
 public function setName($x) { $this->Name = $x; }
 public function getName() { return $this->Name; }
 private $Email = null;
 public function setEmail($x) { $this->Email = $x; }
 public function getEmail() { return $this->Email; }
 private $LastLogin = null;
 public function setLastLogin($x) { $this->LastLogin = $x; }
 public function getLastLogin() { return $this->LastLogin; }
 private $LastIP = null;
 public function setLastIP($x) { $this->LastIP = $x; }
 public function getLastIP() { return $this->LastIP; }
 private $Visits = null;
 public function setVisits($x) { $this->Visits = $x; }
 public function getVisits() { return $this->Visits; }
}

function thisPlayer($id, $nm, $players, $em)
{
	if (array_key_exists(trim($id),$players))
	{
		$player = $players[trim($id)];
		$player->setVisits($player->getVisits() + 1);
	}		
	else
	{
		$player = new Player();
		$player->setVisits("1");
	}	
	$player->setID($id);
	$player->setName($nm);
	if ($em > '')
	{
		$player->setEmail($em);
	}	
	$player->setLastLogin(date(DATE_ATOM));
	$player->setLastIP($_SERVER['REMOTE_ADDR']);
 	$players["".$player->getID()] = $player;
	writePlayerXML($players); 
	return $player;
}	
function PlayerFromXML() 	{ 
 $returnArray = ""; 
 $table = simplexml_load_file('Player.xml');
	foreach($table->children() as $record)	{ 
 $x = new Player();
 foreach($record->children() as $attr) { 
  if ($attr->getName() == 'ID') { 
 	$x->setID(trim($attr)); }  
  if ($attr->getName() == 'Name') { 
 	$x->setName(trim($attr)); }  
  if ($attr->getName() == 'Email') { 
 	$x->setEmail(trim($attr)); }  
  if ($attr->getName() == 'LastLogin') { 
 	$x->setLastLogin(trim($attr)); }  
  if ($attr->getName() == 'LastIP') { 
 	$x->setLastIP(trim($attr)); }  
  if ($attr->getName() == 'Visits') { 
 	$x->setVisits(trim($attr)); }  
 }  $returnArray["".trim($x->getID())] = $x; }
 usort($returnArray, 'sortArray');
 foreach($returnArray as $r)  {  	$newArray[$r->getID()] = $r;  } return $newArray; } 
 function writePlayerXML($arr)	{
$xmlString = "<?xml version='1.0' encoding='ISO-8859-1'?><root>";
 foreach ($arr as $a) { $t1 = '<Player>';
 $t1 = $t1.'<ID>'.$a->getID().'</ID>'; 
 $t1 = $t1.'<Name>'.$a->getName().'</Name>'; 
 $t1 = $t1.'<Email>'.$a->getEmail().'</Email>'; 
 $t1 = $t1.'<LastLogin>'.$a->getLastLogin().'</LastLogin>'; 
 $t1 = $t1.'<LastIP>'.$a->getLastIP().'</LastIP>'; 
 $t1 = $t1.'<Visits>'.$a->getVisits().'</Visits>'; 
 $t1 = $t1.'</Player>'; $xmlString = $xmlString.$t1; 	} 
	$xmlString = $xmlString.'</root>'; 
	$xml2 = str_ireplace('\\','',$xmlString); 
	$file=fopen('Player.xml','w'); 
	fwrite($file,$xml2); 
	fclose($file); 	} 
function sortArray($f1, $f2) {
 $a = trim(strtoupper ($f1->getName()));
 $b = trim(strtoupper ($f2->getName()));
 return $a > $b ? 1 : -1; }
function sortByDate($f1, $f2) {
 $a = trim(strtoupper ($f1->getLastLogin()));
 $b = trim(strtoupper ($f2->getLastLogin()));
 return $a < $b ? 1 : -1; }
?> 
