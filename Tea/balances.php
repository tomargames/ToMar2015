<?php
require 'teaDO.php';
$TeaDate = processDates(createDates());
$TeaPerson = processPeople(createPeople(), count($TeaDate), "N");
//put keys of attendees in the array below
$ppl = array('003','004','007','025','033','044','064');
foreach ($ppl as $p)
{
	$tp = $TeaPerson[$p];
	$tp->setTeas("1".substr($tp->getTeas(),1));
	echo $tp->getId()." ".$tp->getLast()." ".$tp->getFirst()." ".base_convert($temp,2,16)." ".$temp." "."<br>";
}
$xmlString = "<?xml version='1.0' encoding='ISO-8859-1'?><teaPeople>";
$counter = 1;
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
