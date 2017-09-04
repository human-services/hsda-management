<?php$request = $app->request();$override = 0;$_body = $request->getBody();$_body = json_decode($_body,true);	$_body = filter_var_array($_body,FILTER_SANITIZE_STRING);$id = filter_var($id);// grab this path$api = $openapi['hsda-default']['paths'][$route];// grab this path$definitions = $openapi['hsda-default']['definitions'];// load up the parameters (type,name,description,default)$parameters = $api[$verb]['parameters'];// load of up the responses$responses = $api[$verb]['responses'];$response_200 = $responses['200'];// grab our schema$schema_ref = $response_200['schema']['items']['$ref'];$schema = str_replace("#/definitions/","",$schema_ref);$schema_properties = $definitions[$schema]['properties'];// Load any pre extensions for this routeif (file_exists($prepath)) 	{	include $prepath;	}// override primary queryif($override==0)	{		$query = "UPDATE " . $schema . " SET ";		foreach($schema_properties as $field => $value)		{		if(isset($value['type']) && $value['type'] != 'array')			{					if(isset($_body[$field]))				{				$query .= $field . "='" . $_body[$field] . "',";				}			}		else			{			// Deal With Array				}					}	$query = substr($query,0,strlen($query)-1);		$query .= " WHERE id = '" . $id . "'";	//echo $query;		// Execute Query	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	$response = $conn->exec($query);	//echo $response . "\n";			// Return Values	$F = array();	foreach($schema_properties as $field => $value)		{		$F['id'] = $id;			if(isset($value['type']) && $value['type'] != 'array')			{				if(isset($_body[$field]))				{						$F[$field] = filter_var($_body[$field], FILTER_SANITIZE_STRING);				}			}		else			{			// Deal With Array							$path_count_array = explode("/",$route);				$path_count = count($path_count_array);				$core_path = $path_count_array[1];			$core_path = substr($core_path,0,strlen($core_path)-1);			//echo "path: " . $core_path . "<br />";			//echo "path count: " . $path_count . "<br />";													$sub_schema_ref = $value['items']['$ref'];			$sub_schema = str_replace("#/definitions/","",$sub_schema_ref);			$sub_schema_properties = $definitions[$sub_schema]['properties'];			//echo $sub_schema . "\n";			//var_dump($sub_schema_properties);								foreach($_body[$field] as $sub_body)				{									$query = "UPDATE " . $sub_schema . " SET ";								// Values				$value_string = "";				foreach($sub_schema_properties as $sub_field_2 => $sub_value_2)					{					if(isset($sub_value_2['type']))						{									if(isset($sub_body[$sub_field_2]))							{										$query .= $sub_field_2 . "='" . $sub_body[$sub_field_2] . "',";							}						}					else						{						// Deal With Array							}						}					$query = substr($query,0,strlen($query)-1);								// Build The Query To Insert				$query .= " WHERE id = '" . $sub_body['id'] . "'";				//echo "\n" . $query . "\n";								// Execute Query			    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			    $response = $conn->exec($query);				//echo $response . "\n";												}						$F[$field] = $_body[$field];						}					}	}	$ReturnObject = $F;// Load any post extensions for this path	if (file_exists($postpath)) 	{	include $postpath;	}//echo $head['ACCEPT'] . "<br />";if(isset($head['ACCEPT']) && $head['ACCEPT'] == 'text/csv')	{	$app->response()->header("Content-Type", "text/csv");		$return_csv = generateCsv($ReturnObject);	echo $return_csv;	}elseif(isset($head['ACCEPT']) && $head['ACCEPT'] == 'application/xml')	{	$app->response()->header("Content-Type", "application/xml");		$return_xml = arrayToXml($ReturnObject);	echo $return_xml;	}else	{	$app->response()->header("Content-Type", "application/json");	echo stripslashes(format_json(json_encode($ReturnObject)));	}