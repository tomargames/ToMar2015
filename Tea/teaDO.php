<?php
function formatNumber($number, $digits)
{
	$temp = 	"000000000" . $number;
	return substr($temp, strlen($temp) - $digits);
}
function createPeople()
{
	return simplexml_load_file("teaPeople.xml");
}
function createDates()
{
	return simplexml_load_file("teaDates.xml");
}
function processPeople($table,$numTeas,$sort)
{
  	foreach($table->children() as $record)		// each $record is a detail record
	{
		$person = new TeaPerson();
		foreach($record->children() as $attr)			// each $attr is an attribute
  		{
			if ($attr->getName() == "code")
			{
				$person->setId($attr);
			}	
			elseif ($attr->getName() == "last")
			{
				$person->setLast($attr);
			}	
 			elseif ($attr->getName() == "first")
			{
				$person->setFirst($attr);
			}	
			elseif ($attr->getName() == "sex")
			{
				$person->setSex($attr);
			}	
  		elseif ($attr->getName() == "type")
			{
				$person->setSource($attr);
			}	
	 		elseif ($attr->getName() == "pic")
			{
				$person->setPic($attr);
			}	
			elseif ($attr->getName() == "teas")
			{
				$t = "000000000000000000000000000000000000000000".base_convert($attr,16,2);
				$person->setTeas(substr($t,strlen($t) - $numTeas));
				$person->setTotal(substr_count($t,"1")); 
			}	
   		}
  		$teaPerson["".$person->getId()] = $person;
  	}
  	if ($sort == "Y")
  	{
		usort($teaPerson, "sortByName");
	}	
  	return $teaPerson;
}	
function sortByName($personA, $personB)
{
	$a = strtoupper($personA->getLast()." ".$personA->getFirst()); 
	$b = strtoupper($personB->getLast()." ".$personB->getFirst());  
	return ($a > $b) ? 1 :-1;
}	
function processDates($table)
{
	$displayDates = "";
  	foreach($table->children() as $record)		// each $record is a detail record
	{
		$tDate = new TeaDate();
		foreach($record->children() as $attr)			// each $attr is an attribute
  		{
			if ($attr->getName() == "code")
			{
				$tDate->setId($attr);
			}	
			elseif ($attr->getName() == "location")
			{
				$tDate->setLocation($attr);
			}	
 			elseif ($attr->getName() == "date")
			{
				$tDate->setTeaDate($attr);
			}	
 			elseif ($attr->getName() == "host")
			{
				$tDate->setSource($attr);
			}	
   		}
  		$teaDate["".$tDate->getId()] = $tDate; 
  	}
  	return $teaDate;
}	
class TeaObject 
{
	private $id = "";
	private $source = "";

	public function getObType()
	{
		return $this->obType;
	}	
	public function getTypeId()
	{
		return $this->obType.$this->id;
	}	
	public function setId($id)
	{
		$this->id = $id;
	}	
	public function getId()
	{
		return $this->id;
	}
	public function setSource($id)
	{
		$this->source = $id;
	}	
	public function getSource()
	{
		return $this->source;
	}
}		
class TeaDate extends TeaObject
{
	private $location = "";			
	private $teaDate = "";
	public $obType = "D";

	public function setLocation($location)
	{
		$this->location = $location;
	}	
	public function getLocation()
	{
		return $this->location;
	}
	public function setTeaDate($teaDate)
	{
		$this->teaDate = $teaDate;
	}	
	public function getTeaDate()
	{
		return $this->teaDate;
	}		
}	
class TeaPerson extends TeaObject 
{
	public $obType = "P";
	private $first = "";
	private $last = "";
	private $sex = "";	
	private $teas = "";  	// this will be an array of TeaDate keys
	private $total = 0;
	private $pic = "";
	
	public function getPic()
	{
		return $this->pic;
	}
	public function getTotal()
	{
		return $this->total;
	}
	public function setPic($t)	
	{
		$this->pic = $t;
	}	
	public function setTotal($t)	
	{
		$this->total = $t;
	}	
	public function setFirst($first)
	{
		$this->first = $first;
	}		
	public function getFirst()
	{
		return $this->first;
	}
	public function setLast($last)
	{
		$this->last = $last;
	}	
	public function getLast()
	{
		return $this->last;
	}
	public function setSex($sex)
	{
		$this->sex = $sex;
	}	
	public function getSex()
	{
		return $this->sex;
	}
	public function setTeas($teas)
	{
		$this->teas = $teas;
	}	
	public function getTeas()
	{
		return $this->teas;
	}			
}	
?>