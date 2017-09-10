<?php

// Add permissions
if($verb == 'post')
	{

	$management_base_url = $openapi['hsda-utility']['schemes'][0] . '://' . $openapi['hsda-utility']['host'] . $openapi['hsda-utility']['basePath'];
	$management_base_url = $management_base_url . '/services/';
	//echo "management url: " . $management_base_url . "<br />";
	
	// Send Auth Headers
	$headers = array('x-appid: ' . $admin_login,'x-appkey: ' . $admin_code);
	
	$http = curl_init();  
	curl_setopt($http, CURLOPT_URL, $management_base_url);  
	curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);   
	curl_setopt($http, CURLOPT_HTTPHEADER, $headers); 
	
	$output = curl_exec($http);
	//echo $output;
	$services = json_decode($output,true);	
	
	foreach($services as $service)
		{
			
		$name = $service['name'];
		$path = $service['path'];
		$verb = $service['name'];
		
		// some point we need admin
		if($name !='hsda-management' && $name !='hsda-meta')
			{
			
			$id = getGUID();
			
			// Build The Query To Insert
			$query = "INSERT INTO service(id,user_id,name,path,verb) VALUES";
			$query .= "('". $id . "','". $local_id . "','". $name . "','". $path . "','". $verb . "')";
			//echo "\n" . $query . "\n";
		
			// Execute Query
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$response = $conn->exec($query);
			//echo $response . "\n";			
			}
		}
		
	}

?>