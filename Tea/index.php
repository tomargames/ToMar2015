<?php
require 'teaDO.php';
$TeaDate = processDates(createDates());
$TeaPerson = processPeople(createPeople(), count($TeaDate), "Y");
//print_r($TeaPerson);
echo "<script> var teaCount = ".count($TeaDate)."; </script>";
?>
<!doctype html>
<html>
<head>
	<title>Tea with dee and marie</title>
	<LINK REL="StyleSheet" HREF="tea.css?<?php echo rand(); ?>"  TYPE="text/css" TITLE="ToMar Style" MEDIA="screen">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
</head>
<body>
<script>	
var people = new Array();
var dates = new Array();
var counts = new Array();
var attendees = new Array();
for (var i = 0; i < teaCount; i++)
{
	var tKey = (i < 9) ? "0"+(i+1) : "" + (i+1);
	counts[tKey] = 0;
	attendees[tKey] = '';
}	
<?php
	foreach ($TeaPerson as $tp)
	{
		echo "people['".$tp->getId()."'] = '".$tp->getFirst()." ".$tp->getLast()."'; "; 
		echo "var t = '".$tp->getTeas()."'; ";
		echo "for (var i = t.length; i > -1; i--) { ";
		echo "if ('1' == t.substr(i, 1)) { ";
		echo "var tt = t.length - i; ";
		echo "var tKey = (tt < 10) ? '0'+tt : '' + tt; ";
		echo " counts[tKey] += 1; ";
		echo " attendees[tKey] += '".$tp->getId()."' } }";	
	}
	foreach ($TeaDate	 as $td)
	{
		echo "dates['".$td->getId()."'] = '".$td->getTeaDate()."'; ";
	}		
?>
</script>	
<table border=0 align="center"><tr>	
	<td width="20%"><img src="Tea.jpg"></td>
	<td  width="60%" class="biggest">Tea with dee and marie<br></td>
	<td width="20%"><img src="Tea.jpg"></td></tr>
</table>
<table border=0 align="center">
	<tr valign="top">
		<td><table border=1>
		<tr><td>ID</td>
			<td>Date</td>
			<td>Location</td>
			<td>Host</td>
			<td>Who Came</td>
		</tr>		
<?php
	foreach ($TeaDate	 as $td)
	{
		echo "<tr><td class=black10>".$td->getId();
		echo "</td><td class=black10>".$td->getTeaDate();
		echo "</td><td class=black10>".$td->getLocation();
		echo "</td><td class=black10><script>document.write(people['".$td->getSource()."']); </script>";
		echo "</td><td class=black10num id='D".$td->getId()."4'><a class=magenta10 href=javascript:showPeople('".$td->getId()."')><script>document.write(counts['".$td->getId()."']); </script></a></td></tr>";
	}		
?>
		</table></td>
		<td width="50"> </td>
		<td><table border=1>
		<tr><td>ID</td>
			<td>Name</td>
			<td>Bridge</td>
			<td>Teas</td>
		</tr>		
<?php
	foreach ($TeaPerson	 as $tp)
	{
		if ($tp->getPic() != "")
		{
			echo "<tr><td><img src='../SHG/images/".$tp->getPic().".png'>";
			echo "</td><td class=black10>".$tp->getFirst()." ".$tp->getLast();
			echo "</td><td class=black10><script>document.write(people['".$tp->getSource()."']); </script>";
			echo "</td><td class=black10num id='".$tp->getTypeId()."5'><a class=magenta10 href=javascript:showTeas('".$tp->getTypeId()."5".$tp->getTeas()."');>".$tp->getTotal()."</a></td></tr>";
		}	
	}		
?>
		</table>			
		</td>
	</tr>
</table>	
</body>	
<script>
function showPeople(str)
{
	var tdOut = "";
	var dKey = "#D" + str + "4";
	var strIn = attendees[str];
	for (var i = 0; i < strIn.length; i += 3)
	{
		var pKey = strIn.substr(i, 3);
		tdOut += people[pKey] + "<br>";
	}
	$(dKey).html(tdOut);	
}	
function showTeas(str)
{
	var k = "#" + str.substr(0, 5);
	var t = str.substr(5);
	var tdOut = "";
	for (var i = t.length;i > -1; i--)
	{
		if ("1" == t.substr(i, 1))
		{
			tt = t.length - i;
			tKey = (tt < 10) ? "0"+tt : "" + tt;
			tdOut += dates[tKey] + "<br>";
		}
	}		
	$(k).html(tdOut);
}
</script>
</html> 