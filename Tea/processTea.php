<?php
require 'teaDO.php';
$TeaDate = processDates(createDates());
$TeaPerson = processPeople(createPeople(), count($TeaDate), "N");
$t = new TeaDate();
$t->setId($_GET["code"]);
$t->setSource($_GET["host"]);
$t->setTeaDate($_GET["date"]);
$t->setLocation($_GET["location"]);
$TeaDate["".$t->getId()] = $t;
//write date file out
$xmlString = "<?xml version='1.0' encoding='ISO-8859-1'?><teaDates>";
foreach ($TeaDate as $td)
{
	$t1 = "<D><code>".$td->getId()."</code><location>".$td->getLocation()."</location><date>".$td->getTeaDate()."</date><host>".$td->getSource()."</host></D>";
	$xmlString = $xmlString.$t1;
}	
$xmlString = $xmlString."</teaDates>";
$xml2 = str_ireplace("\\","",$xmlString);
$file=fopen("teaDates.xml","w");
fwrite($file,$xml2);
fclose($file);
echo "Wrote date file, now starting people processing.<br>";
//put keys of attendees in the array below
$ppl = $_GET["guest"];
foreach ($ppl as $p)
{
	echo "processing ".$p."<br>";
	$tp = $TeaPerson[$p];
	echo "teas before = ".$tp->getTeas()."<br>";
	$tp->setTeas("1".substr($tp->getTeas(),1));
	echo "teas after  = ".$tp->getTeas()."<br>";
	echo $tp->getId()." ".$tp->getLast()." ".$tp->getFirst()." ".base_convert($temp,2,16)." ".$temp." "."<br>";
}
$xmlString = "<?xml version='1.0' encoding='ISO-8859-1'?><teaPeople>";
foreach ($TeaPerson as $tp)
{
	$t1 = "<P><code>".$tp->getId()."</code><last>".$tp->getLast()."</last><first>".$tp->getFirst()."</first><sex>".$tp->getSex()."</sex><type>".$tp->getSource()."</type><teas>".base_convert($tp->getTeas(),2,16)."</teas></P>";
	$xmlString = $xmlString.$t1;
}	
$xmlString = $xmlString."</teaPeople>";
$xml2 = str_ireplace("\\","",$xmlString);
$file=fopen("teaPeople.xml","w");
fwrite($file,$xml2);
fclose($file);
?>
