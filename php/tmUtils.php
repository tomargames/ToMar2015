<?php
function formatNumber($number, $digits)
{
	$temp = 	"000000000" . trim($number);
	return substr($temp, strlen($temp) - $digits);
}
function debug($str)
{
	echo $str."<br>";
}		
function randomSubset($arr, $numPicks)
{
	$rArray = null;
	for ($i = 0; $i < $numPicks; $i++)
	{
		$rPick = mt_rand(0, count($arr) - 1);
		$rArray[$i] = $arr[$rPick];
		array_splice($arr, $rPick, 1);
	}
	return $rArray;
}
function noRepeatedLetters($s)
{
	$chars = str_split($s);
	for ($i = 0; $i < strlen($s); $i++)
	{
		for ($j = $i + 1; $j < strlen($s); $j++)
		{
			if ($chars[$i] == $chars[$j])
			{
				return false;
			}	
		}	
	}	
	return true;
}
function tmDate($secs)
{
	$tArray = getdate(substr($secs, 0, 10));
	return $tArray[year]."-".$tArray[mon]."-".$tArray[mday];
}
?>