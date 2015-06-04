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
//Shanghai awards are different, but the above processing needs to be done first, so now:
	if ($sc == 72)
	{
		$award = 3;
	}
	else if ($sc < 120)
	{
		$award = 2;
	}
	else
	{
		$award = 1;
	}			
//end of Shanghai special processing	
	global $blurb;
	$stats = "Your Score: ".$sc."<br>Award: ".$award."<br>".$stats;
	$temp = $player->processAward($award,$tsp);
	$message = $sc." points. ".$blurb[$award + 1].$temp;
	$players[trim($id)] = $player;
	writePlayerXML($players);
}
$dirArray = scandir("images");
array_splice($dirArray, 0, 2);
$puzzlePicks = 	randomSubset($dirArray, 36);
?>
<!doctype html>
<html>
<head>
	<title>Shanghai</title>
	<script src="../js/utils.js"></script>
	<LINK REL="StyleSheet" HREF="../styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
</head>
<body>
<table border=0 align="center"><tr>	
	<td width="10%"><img src="SHG.jpg" height="100" width="100"></td>
	<td  width="80%" class="biggest">Shanghai<br><span class="magenta10">by ToMarGames</span><br></td>
	<td width="10%"><img src="SHG.jpg" height="100" width="100"></td></tr>
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
<form name="gameForm" action="../SHG/" method="post">			
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
	<canvas id="dbCanvas" width="1100" height="650">
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
		echo "var stringMessage = 'Welcome to Shanghai!'; ";
	}
	echo "var tileReady = []; ";
	echo "var tileImage = []; ";
	echo "var tileName = []; ";
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$iCnt = 0;
	$dot = ".";
	foreach ($puzzlePicks as $p)
	{
		$names[$iCnt] = substr($p, 0, strpos($p, $dot));
		echo "tileReady['".substr($chars, $iCnt, 1)."'] = false; tileName['".substr($chars, $iCnt, 1)."'] = '".substr($p, 0, strpos($p, $dot))."'; tileImage['".substr($chars, $iCnt, 1)."'] = new Image(); tileImage['".substr($chars, $iCnt, 1)."'].onload = function () { tileReady['".substr($chars, $iCnt, 1)."'] = true; }; tileImage['".substr($chars, $iCnt++, 1)."'].src = 'images/".$p."'; ";
	}
?>			
	</script>
	<dialog id="dialog" style="background-color: #ccccff;">
  <form method="dialog">
    <p>Click who to search for, or use Escape key to cancel</p>
    <table>
<?php 
	for ($row = 0; $row < 6; $row++)
	{
		echo "<tr>";
		for ($i = $row; $i < 36; $i += 6)
		{
			echo '<td><button type="submit" value="'.substr($chars, $i, 1).'">'.substr($chars, $i, 1).' '.$names[$i].'</button></td>';
		}
		echo "</tr>";
	}		
?>		
	</table>
  </form></dialog>
	<script src="shanghai.js"></script>
</div>
</td></tr></table>
	<br>About the Game
	<ul class=green10>
		<li>There are 4 copies each of 36 different tiles.</li>
		<li>Remove matching pairs of tiles by clicking on tiles that are free.</li>
		<li>Tiles are free if they can slide to the right or the left.</li>
		<li>Light blue tiles are only one deep.</li>
		<li>Gold tiles are two deep.</li>
		<li>Dark green tiles are three deep.</li>
		<li>Red tiles are four deep.</li>
		<li>Most puzzles can be played down to zero tiles.</li>
		<li>Hints don't cost you anything, so you can use them to get started.</li>
		<li>Rather than a high score list, ToMarGames uses a ranking system based on all scores for that game.</li>
		<li>You move up through the ranks by earning stars.</li>
		<li>If you solve the puzzle, you earn a star.</li>
		<li>If you solve it in 72 moves, you earn 3 stars.</li>
		<li>If you come in under 120 moves, you earn 2 stars.</li>
	</ul>
</body>
</html>

