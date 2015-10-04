<?php
$a = $_POST["action"];
$k = "'".$_POST["game"]."'";
$a = "P";
$k = 'G106932376942135580175T1443755428855';
?>
<html>
<form name="gameForm" method="post" action="http://localhost/ToMar2015/BattleShip/battleship.php">			
<input type="hidden" name="pid" value="http://localhost/ToMar2015/BattleShip/silly.php">
<input type="hidden" name="game" value="<?php echo $k; ?>">
<input type="hidden" name="action" value="P111211311411511">
</form>
<?php
$a = $_POST["action"];
$k = "'".$_POST["game"]."'";
$a = "P";
$k = 'G106932376942135580175T1443755428855';
if ($a == null)
{
	echo "invalid call, exiting";
}
else
{		
	echo '<script> var action = "'.$a.'"; ';		
	echo 'document.gameForm.submit(); '; 	
	echo '</script>';
}	
?>	

</html>