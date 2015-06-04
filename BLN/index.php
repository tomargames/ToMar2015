<?php
require '../php/Player.php';
require '../php/Game.php';
require '../php/tmUtils.php';

// Step1: get id and name from url
global $id;
global $nm;
$id = $_POST["id"];
$nm = $_POST["nm"];
if ($id == "")
{
	echo '<script> alert("Sign in to play games. Thanks!"); window.location = "../Menu"; </script>';
}
// Step2: read player file and identify player
$players = PlayerFromXML();
$player = thisPlayer($id, $nm, $players);
// Step3: read game file and build stats coming in
global $mean;
global $highest;
global $std;
$games = GameFromXML();
$stats = "Highest: ".$highest."<br>Average: ".$mean."<br>StdDev: ".$std."<br>";
// Step4: if there's a score coming in, process it
$sc = $_POST["score"];
$message = "NONE";
if (is_finite($sc) && $sc > 0)
{
	$tsp = $_POST["tsp"];
	$award = CalculateAward($sc, $tsp.$id, $games);
	global $blurb;
	$stats = "Your Score: ".$sc."<br>Award: ".$award."<br>".$stats;
	$temp = $player->processAward($award,$tsp);
	$message = $sc." points. ".$blurb[$award + 1].$temp;
	$players[trim($id)] = $player;
	writePlayerXML($players);
}
?>
<!doctype html>
<html>
<head>
	<title>Ballooney!</title>
	<script src="../js/utils.js"></script>
	<LINK REL="StyleSheet" HREF="../styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
</head>
<body>
<table border=0 align="center"><tr>	
	<td width="10%"><img src="BLN.jpg" height="100" width="100"></td>
	<td  width="80%" class="biggest">Ballooney!<br><span class="magenta10">by ToMarGames</span><br></td>
	<td width="10%"><img src="BLN.jpg" height="100" width="100"></td></tr>
</table>
<table border="0" align="center"><tr>
<td width="30%" align="left">
<table border=2>
<?php 
	$pr = "";
	for ($i = 0; $i < $player->getStars(); $i++)
	{
		$pr = $pr."* ";
	}		
	echo "<tr><td class=green12>Player</td><td class=big>".$nm."</td></tr>";		
	echo "<tr><td class=green12>Rank</td><td class=big>".$player->getLevel()."</td></tr>";		
	echo "<tr><td class=green12>Progress</td><td class=big>".$pr."</td></tr>";
	echo "<tr><td class=magenta8 colspan=2>".$stats."</td></tr>";
?>
</table>
<br><br>
<form name="menuForm" action="../" method="post">			
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
<input type="submit" value="ToMarGames Menu"><br><br>	
</form>
<form name="gameForm" action="../BLN/" method="post">			
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="nm" value="<?php echo $nm; ?>">
<input type="hidden" name="score">
<input type="hidden" name="tsp">
</form>
<table border=1>
	<tr><td colspan="3" class="magentah">Players</td></tr>
	<tr><td class="greenh">Name</td><td class="greenh">Rank</td><td class="greenh">Games</td></tr>
<?php
	foreach ($players as $p)
	{
		echo "<tr><td class='green10'>".$p->getName()."</td><td class='green10num'>".$p->getLevel()."</td><td class='green10num'>".$p->getGames()."</td></tr>";
	}	
?>	
</table>	
</td>
<td width="1">&nbsp; </td>		
<td>
<div id="app"> 
	<canvas id="dbCanvas" width="900" height="650">
		Your browser does not support the canvas element.
	</canvas>
	<script>
<?php
	if ("NONE" !== $message)
	{
		echo "var stringMessage = '".$message."'; ";
	}
	else	
	{
		echo "var stringMessage = 'Welcome to Ballooney!'; ";
	}
?>			
	</script>
	<script src="ballooney.js"></script>
</div>
</td></tr></table>
	<br>About the Game
	<ul class=green10>
		<li>Click on the balloons to pop them.</li>
		<li>You can pop a group of same-colored balloons as long as at least two are adjacent.</li>
		<li>The more balloons popped in a single group, the more points you earn for that pop.</li>
		<li>Time is not a factor in this game -- relax and pop balloons.</li>
		<li>Bonus points for leaving fewer than 25 balloons on the board.</li>
		<li>If you clear the board, you go to the next level.</li>
		<li>Points for each move are the number of balloons selected, squared, multiplied by level number.</li>
		<li>Rather than a high score list, ToMarGames uses a ranking system based on all scores for this game.</li>
		<li>You move up through the ranks by earning stars.</li>
		<li>Each game, your score will be measured against the mean score of all games played so far.</li>
		<li>To earn a star, you must score higher than the mean.</li>
		<li>If you score more than the mean plus standard deviation, you can earn multiple stars.</li>
		<li>If you score less than the mean minus standard deviation, you will lose a star.</li>
		<li>When you reach 5 stars, you will advance to the next rank, and come into the rank with two stars.</li>
		<li>If you have no stars, and you lose a star, you will descend to the rank below, and come in with two stars.</li>
	</ul>
</body>
</html>

