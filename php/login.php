<?php 
// 1. If already logged in
	require 'openid.php';
	try 
	{
			$site = "http://www.tomargames.com/ToMar2012/Scoring/";
	    $openid = new LightOpenID($site);
    	if(!$openid->mode) 
    	{
        	if(isset($_GET['login'])) 
        	{
            	$openid->identity = 'https://www.google.com/accounts/o8/id';
				$openid->required = array('contact/email','namePerson/first', 'namePerson/last');
           		header('Location: ' . $openid->authUrl());
			}
			else
			{
				echo '<form action="?login" method="get"></form>';
				echo '<script> document.forms[0].submit(); </script>';
			}
    	} 
    	elseif($openid->mode == 'cancel') 
    	{
        	echo 'User has canceled authentication!';
    	} 
    	else 
    	{
        	if ($openid->validate())
        	{
        		$usr = $openid->identity;
						$idx = stripos($usr,"=") + 1;
						$id = substr($usr, $idx);
        		$data = $openid->getAttributes();
        		$name = $data['namePerson/first']." ".$data['namePerson/last'];
        		$email = $data['contact/email'];
						echo '<form action="'.$site.'" method="get">';
						echo '<input type="hidden" name="id" value="'.$id.'">';	
						echo '<input type="hidden" name="em" value="'.$email.'">';	
						echo '<input type="hidden" name="nm" value="'.$name.'">';	
						echo '</form>';
						if ($name < "A")
						{
							echo "<script> var blankName = ''; ";
							echo "while (blankName == '') ";
							echo " { blankName = prompt('Not a Google user? What would you like to call yourself? '); } ";
							echo " document.forms[0].nm.value = blankName; ";
							echo " </script>";
						}	
						echo '<script> document.forms[0].submit(); </script>';
        	}
        	else
        	{
        		echo 'Invalid UserID';
        	}		
    	}
	} catch(ErrorException $e) 
	{
    	echo $e->getMessage();
	}
?>
