<?php
require '../php/tmUtils.php';

$dirArray = scandir("images");
array_splice($dirArray, 0, 2);
$puzzlePicks = 	randomSubset($dirArray, 36);
	echo "var tileReady = []; ";
	echo "var tileImage = []; ";
	echo "var tileName = []; ";
	$iCnt = 0;
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$dot = ".";
	foreach ($puzzlePicks as $p)
	{
		echo ", ".strpos($p, $dot);
		echo " ".substr($p, 0, 3);
//		echo "<br>strpos = ".substring($p, 0, strpos($p, $dot));
//		echo "<br>tileName ['".substr($chars, $iCnt++, 1)."'] = '".substring($p, 0, strchr($p, $dot))."'; 
	}
?>			
