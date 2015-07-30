<?php
require 'tmUtils.php';
$file=fopen("../js/tmWords.txt","r") or exit("Unable to open file!");
while(!feof($file))
{
	$w = trim(fgets($file));
	if (strlen($w) == 4)
	{
		$fours[count($fours)] = $w;
	}
	else if (strlen($w) == 5)
	{
		$fives[count($fives)] = $w;
	}
	else if (strlen($w) == 3)
	{
		$threes[count($threes)] = $w;
	}
}
fclose($file);   
foreach($fours as $w4)
{
	if (noRepeatedLetters($w4))
	{		
		$char4 = str_split($w4);
		foreach($fives as $w5)
		{
			if (noRepeatedLetters($w5))
			{	
				$char5 = str_split($w5);
				if ($char4[1] == $char5[1])
				{		
					if ($char4[2] == $char5[4])
					{	
						if ($char4[3] == $char5[3])
						{
							echo $w4." ".$w5."<br>";
						}	
					}
				}	
			}	
		}	
	}
}	
?>	
