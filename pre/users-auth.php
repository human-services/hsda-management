<?php
$override = 1;
$Query = "SELECT s.id,user_id,s.name,s.path,s.verb FROM user u join service s on u.id = s.user_id WHERE u.login = '" . $_get['login'] . "' and u.code = '" . $_get['code'] . "';";
//echo $Query;
$results = $conn->query($Query);
if(count($results) > 0)
	{
	$ReturnObject['access']	= 1;
	foreach ($results as $row)
		{
		$ReturnObject[$row['name']][$row['path']][$row['verb']] = 1;
		}
	}
else
	{
	$ReturnObject['access']	= 0;
	}
?>