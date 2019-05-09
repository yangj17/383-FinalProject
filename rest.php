<?php
    require("dataLayer.php");
    header("Content-Type: Application/json");

    function getJson() {
		$jsonStringIn = file_get_contents('php://input');
		$json = array();
		$response = array();
		try {
			$json = json_decode($jsonStringIn,true);
			return $json;
		}
		catch (Exception $e) {
			header("HTTP/1.0 500 Invalid content -> probably invalid JSON format");
			$response['status']="fail";
			$response['message']=$e->getMessage();
			print json_encode($response);
			exit;
		}
	}

    function AuthenticateToken() {
			$body = getJson();
			$username = $body["username"];
			$password = $body["password"];
			$response = array();
			try {
				$Token = new Tokens();
				$result = $Token -> authenticate($username, $password);
				return $result;
			}
			catch (Exception $e) {
				header("HTTP/1.0 500 Invalid content -> probably invalid JSON format");
				$response['status']="fail";
				$response['message']=$e->getMessage();
				print json_encode($response);
				exit;
			}
		}
		
		function getItems($token) {
			
		}

    $method = $_SERVER['REQUEST_METHOD'];
    if($method=="POST") {
			if (isset($_SERVER['PATH_INFO'])) {
				if($_SERVER['PATH_INFO']== "/v1/user") {
								$result = AuthenticateToken();
								$return = array();
								if($result->getStatus()=="OK") {
									$return["status"]="OK";
									$array = $result -> getData();
									$return["message"]=$array["message"];
									$return["token"]=$array["token"];
								}
								else {
									$return["status"]="Fail";
									$array = $result ->getData();
									$return["message"]=$array["message"];
								}
								print json_encode($return);
				}
				$path = explode("/", $_SERVER['PATH_INFO']);
				if($path[1]=="items") {
					$token = $path[2];
					getItems($token);
				}
      }
    }

?>