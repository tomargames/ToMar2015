<?php 
class Game { 
 private $ID = null;
 public function setID($x) { $this->ID = $x; }
 public function getID() { return $this->ID; }
 private $Name = null;
 public function setName($x) { $this->Name = $x; }
 public function getName() { return $this->Name; }
 private $Desc = null;
 public function setDesc($x) { $this->Desc = $x; }
 public function getDesc() { return $this->Desc; }
 private $Type = null;
 public function setType($x) { $this->Type = $x; }
 public function getType() { return $this->Type; }
} 
function GameFromXML() 	{ 
 $returnArray = null; 
 $table = simplexml_load_file('Game.xml');
	foreach($table->children() as $record)	{ 
 $x = new Game();
 foreach($record->children() as $attr) { 
  if ($attr->getName() == 'ID') { 
 	$x->setID(trim($attr)); }  
  if ($attr->getName() == 'Name') { 
 	$x->setName(trim($attr)); }  
  if ($attr->getName() == 'Type') { 
 	$x->setType(trim($attr)); }  
  if ($attr->getName() == 'Desc') { 
 	$x->setDesc(trim($attr)); }  
 } $returnArray[$x->getID()] = $x; } 
 return $returnArray; } 
function writeGameXML($arr)	
{
	$xmlString = "<?xml version='1.0' encoding='ISO-8859-1'?><root>";
	foreach ($arr as $a) 
	{ 
		$t1 = '<Game>';
	 	$t1 = $t1.'<ID>'.$a->getID().'</ID>'; 
	 	$t1 = $t1.'<Name>'.$a->getName().'</Name>'; 
	 	$t1 = $t1.'<Desc>'.$a->getDesc().'</Desc>'; 
	 	$t1 = $t1.'<Type>'.$a->getType().'</Type>'; 
	 	$t1 = $t1.'</Game>'; 
	 	$xmlString = $xmlString.$t1;
	} 
	$xmlString = $xmlString.'</root>'; 
	$xml2 = str_ireplace('\\','',$xmlString); 
	$file=fopen('Game.xml','w'); 
	fwrite($file,$xml2); 
	fclose($file); 	
}
?>
 