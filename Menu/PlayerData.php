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
 private $Games = null;
 public function setGames($x) { $this->Games = $x; }
 public function getGames() { return $this->Games; }
 }
 ?>
<html>
<head>	
	<title>ToMarGames 2012</title>
	<LINK REL="StyleSheet" HREF="styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
	</head>
<body>
<div>
	<table width=100% cellpadding=0 cellspacing=0><tr>
		<td class="fineprint">ToMarGames build brain cells!</td>
		<td class="banner">ToMarGames 2013</td>
		<td class="fineprint">ToMarGames build brain cells!</td>
	</tr></table>
</div>

	<table border=1>	
<?php
	date_default_timezone_set("America/New_York");
	$dirArray = scandir(".");
	$players[0] = new Player();
	foreach ($dirArray as $d)
	{
		if (strlen($d) == 3)
		{
//			if ("Tea" != $d && "BLN" != $d)
			if ("Tea" != $d)
			{
				$inputFile = $d.'/Player.xml';
				$t = simplexml_load_file($inputFile);
				print_r($t);
				// this loop makes sure the player is in the $players array
				foreach($t->children() as $record)	
				{ 
 					foreach($record->children() as $attr) 
 					{ 
  						if ($attr->getName() == 'ID') 
  						{ 
								if (!(array_key_exists(trim($attr),$players)))
								{
									$player = new Player();
									$player->setID($attr);
								 	$players["".$player->getID()] = $player;
								}
							}
					}
				}				 	
				foreach($t->children() as $record)	
				{ 
 					foreach($record->children() as $attr) 
 					{ 
  						if ($attr->getName() == 'ID') 
  						{ 
								if (array_key_exists(trim($attr),$players))
								{
									$player = $players[trim($attr)];
									$player->setGames($player->getGames()." ".$d);
								}		
								else
								{
									echo "Something is very wrong.";
								}
							}
  						if ($attr->getName() == 'Name') 
  						{ 
								$player->setName($attr);
							}	
							if ($attr->getName() == 'Email') 
  						{ 
								$player->setEmail($attr);
							}	
					}	
				}
			}
		}
	}
	$ctr = 0;			
	foreach ($players as $p)
	{
		echo "<tr><td class=blue10num>".(++$ctr)."</td><td class=black10>".$p->getId()."</td><td class=blue10>".$p->getName()."</td><td class=green10>".$p->getEmail()."</td><td class=green10>".$p->getGames()."</td></tr>";
	}	
?>
	</table>
</body>
</html>	
