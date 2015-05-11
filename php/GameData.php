<?php 
require 'Game.php';
$games = GameFromXML();
usort($games, 'sortArray');
writeGameXML($games);
?>
<html>
<head>
	<title>Scores</title>
	<LINK REL="StyleSheet" HREF="../styles.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
</head>
<body>
	<table border=1>
<?php		
foreach ($games as $g)
{
	echo '<tr><td>'.$g->getScore().'</td><td>'.$g->getCount().'</td></tr>';
}	
function sortArray($f1, $f2) {
 $a = trim($f1->getScore());
 $b = trim($f2->getScore());
 return $a < $b ? 1 : -1; }
?>
</table>
</body>
</html>	
