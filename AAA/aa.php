<?php
require 'aaPlayer.php';

$players = PlayersFromXML();
$id = $_POST["id"];
$nm = $_POST["nm"];
$level = $_POST["level"];
$start = $_POST["start"];
$tsp = $_POST["tsp"];
$player = new aaPlayer();
$player->setID($id);
$player->setName($nm);
$player->setStart($start);
$player->setLevel($level);
$player->setTsp($tsp);
$players[$player->getID().$player->getStart()] = $player; 
writePlayerXML($players);
?>