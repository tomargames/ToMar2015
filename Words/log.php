<?php
	$word = $_GET["entry"];
	$game = $_GET["source"];
	$name = $_GET["name"];
	$file = fopen("addWords.txt","a");
	fwrite($file,$word." ".$game." ".$name."\n");
	fclose($file);
?>