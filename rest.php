<?php
		$GLOBALS['userPk'] = "";
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
		
		function updateItems() {
			try {
				$body = getJson();
				$itemPk = $body["pk"];
				$token = $body["token"];
				$Item = new Items();
				$userPk = $Item -> getUser($token);
				if($userPk == "Fail1" || $userPk == "Fail2" || $userPk == "Fail3") {
					return $userPk;
				}
				$GLOBALS['userPk']=$userPk;
				$result = $Item -> updateItems($itemPk, $userPk);
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

		function getSummary($token) {
			$Item = new Items();
			$userPk = $GLOBALS['userPk'];
			$result = $Item -> getSummary($userPk);
			return $result;

		}

		function getItemsByUser($token) {
			$Item = new Items();
			$userPk = $GLOBALS['userPk'];
			$result = $Item -> getItemsByUser($userPk);
			return $result;
		}

		function getItems() {
			try {
				$Item = new Items();
				$result = $Item -> getItems();
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
				elseif($_SERVER['PATH_INFO']== "/v1/items") {
					$result=updateItems();
					if($result == "OK") {
						$return = array();
						$return['status']="OK";
						$return['msg']="Consumed Item";
						print json_encode($return);
					}
					elseif($result != "OK") {
						$return = array();
						$return['status'] = $result;
						$return['msg'] = "Failed to consume item";
						print json_encode($return);
					}
				}	
			}
		}
		elseif($method=="GET") {
			if (isset($_SERVER['PATH_INFO'])) {
			$path = explode("/", $_SERVER['PATH_INFO']);
				if($path[2]=="items") {
					if(count($path)<4) {
						$result=getItems();
						$return=array();
						$return['status']="OK";
						$return['msg']="Items Retrieved";
						$return['items']=$result;
						print json_encode($return);
					}
					else {
						$token = $path[3];
						$result = getItemsByUser($token);
						$return=array();
						$return['status']="OK";
						$return['msg']="List of Items";
						$return['items']=$result;
						print json_encode($return);
					}
				}
				elseif($path[2]=="itemsSummary") {
					$token = $path[3];
					$result = getSummary($token);
					$return=array();
					$return['status']="OK";
					$return['msg']="Summary Retrieved";
					$return['summary']=$result;
					print json_encode($return);
				}
			}
		}

?>