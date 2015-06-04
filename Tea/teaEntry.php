<?php
require 'teaDO.php';
$TeaDate = processDates(createDates());
$TeaPerson = processPeople(createPeople(), count($TeaDate), "N");
?>
<!doctype html>
<html>
<head>
	<title>Tea Entry</title>
</head>
<body>
<form id="entryForm" action="processTea.php">
		Tea ID: <input type="text" name="code" value="<?php echo (count($TeaDate)+1) ?>"> <br>
		Location: <input type="text" name="location"> <br>
		Host ID: <input type="text" name="host"> <br>
		Date: <input type="text" name="date"> <br>
		<br>
		<br>
		<input type="button" value="Submit" onClick="entryForm.submit();"><br><br>
<?php 
	foreach ($TeaPerson as $tp)
	{
		echo '<input type="checkbox" name="guest[]" value="'.$tp->getId().'"> '.$tp->getId().' '.$tp->getFirst().' '.$tp->getLast().'<br>';
	}
?>		
}	
</form>	
</body>
</html>