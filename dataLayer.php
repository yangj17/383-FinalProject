<?php

require_once("credentials.php");

class Users {
    private $mysqli=null;

    public function connect() {
        $this->mysqli=mysqli_connect("localhost", $user,$password,"cse383");
        if (mysqli_connect_errno($mysqli)) {
            return new Result("FAIL",array("message"=>("Failed to connect to MySQL: " . mysqli_connect_error())));
        } else {
            return new Result("OK",array("message"=>"Database connection sucessful!"));
        }
    }

    public function authenticate($argUsername,$argPassword) {
        $loginUsername = htmlspecialchars($argUsername);
        $loginPassword = htmlspecialchars($argPassword);
        $connectionAttempt = $this->connect();
        $return=null;
        if ($connectionAttempt.getStatus()!="OK") {
            if ($stmt = $this->mysqli->prepare("SELECT password FROM users WHERE user=?")) {
                if (!$stmt->bind_param("s",$loginUsername)) {
                    $result = new Result("FAIL",array("message"=>"Failed to bind parameters: " . $loginUsername . ". Error: " . $this->mysqli->error));
                } else {
                  if (!$stmt->execute()) {
                    $result= new Result("FAIL",array("message"=>"Failed to execute query. Error: " . $this->mysqli->error));
                  } else {
                    $stmt->bind_result($resultPassword);
                    while ($stmt->fetch()) {
                      $userPassword = $resultPassword;
                    }
                    if (password_verify($loginPassword,$userPassword)) {
                        $result= new Result("OK",array("message"=>"Valid credentials"));
                    } else {
                        $result= new Result("INVALID",array("message"=>"Invalid credentials"));
                    }
                  }
                }
            } else {
                $result= new Result("FAIL",array("message"=>"Failed to prepare query. Error: " . $this->mysqli->error));
            }
            $this->mysqli->close();
        } else {
            return $connectionAttempt;
        }
        return $result;
    }
}

class keyNames {
    private $mysqli=null;

    public function connect() {
        $this->mysqli=mysqli_connect($GLOBALS['databaseHost'],$GLOBALS['databaseUser'],$GLOBALS['databasePassword'],$GLOBALS['databaseName']);
        if (mysqli_connect_errno($this->mysqli)) {
            return new Result("FAIL",array("message"=>("Failed to connect to MySQL: " . mysqli_connect_error())));
        } else {
            return new Result("OK",array("message"=>"Database connection sucessful!"));
        }
    }

    public function getKeyNames() {
        $connectionAttempt = $this->connect();
        $return=null;
        if ($connectionAttempt->getStatus()=="OK") {
            if ($stmt = $this->mysqli->prepare("SELECT keyName FROM KeyValue ORDER BY timestamp DESC")) {
                if (!$stmt->execute()) {
                    $result= new Result("FAIL",array("message"=>"Failed to execute query. Error: " . $this->mysqli->error));
                } else {
                    $stmt->bind_result($resultKeyName);
                    $data=array();
                    while ($stmt->fetch()) {
                        $temp=array("keyName"=>$resultKeyName);
                        array_push($data,$temp);
                    }
                    $result= new Result("OK",$data);
                }
            } else {
                $result= new Result("FAIL",array("message"=>"Failed to prepare query. Error: " . $this->mysqli->error));
            }
            $this->mysqli->close();
        } else {
            return $connectionAttempt;
        }
        return $result;
    }

    public function getKeyName($argKeyName) {
        $queryKeyName = htmlspecialchars($argKeyName);
        $connectionAttempt = $this->connect();
        $return=null;
        if ($connectionAttempt->getStatus()=="OK") {
            if ($stmt = $this->mysqli->prepare("SELECT value FROM KeyValue WHERE keyName=?")) {
                if (!$stmt->bind_param("s",$queryKeyName)) {
                    $result = new Result("FAIL",array("message"=>"Failed to bind parameters: " . $queryKeyName . ". Error: " . $this->mysqli->error));
                } else {
                  if (!$stmt->execute()) {
                    $result= new Result("FAIL",array("message"=>"Failed to execute query. Error: " . $this->mysqli->error));
                  } else {
                    $stmt->bind_result($resultValue);
                    $count=0;
                    while ($stmt->fetch()) {
                        $count++;
                    }
                    if ($count>0) {
                        $result= new Result("OK",array("value"=>$resultValue));
                    } else {
                        $result= new Result("NOT FOUND",array("message"=>"The keyName was not found in the database."));
                    }
                  }
                }
            } else {
                $result= new Result("FAIL",array("message"=>"Failed to prepare query. Error: " . $this->mysqli->error));
            }
            $this->mysqli->close();
        } else {
            return $connectionAttempt;
        }
        return $result;
    }

    public function insertKeyName($argKeyName,$argValue) {
        $inputKeyName = htmlspecialchars($argKeyName);
        $inputValue = htmlspecialchars($argValue);
        $connectionAttempt = $this->connect();
        $return=null;
        if ($connectionAttempt.getStatus()!="OK") {
            if ($stmt = $this->mysqli->prepare("INSERT INTO keyValues (keyName,value) VALUES(?,?)")) {
                if (!$stmt->bind_param("ss",$inputKeyName,$inputValue)) {
                    $result = new Result("FAIL",array("message"=>"Failed to bind parameters: " . $loginUsername . ". Error: " . $this->mysqli->error));
                } else {
                  if (!$stmt->execute()) {
                    $result= new Result("FAIL",array("message"=>"Failed to execute query. Error: " . $this->mysqli->error));
                  } else {
                    $result= new Result("OK",array("message"=>"New keyName/value created."));
                  }
                }
            } else {
                $result= new Result("FAIL",array("message"=>"Failed to prepare query. Error: " . $this->mysqli->error));
            }
            $this->mysqli->close();
        } else {
            return $connectionAttempt;
        }
        return $result;
    }
}

class Result {
    public $status=null;
    public $data=null;

    public function __construct($argStatus,$argData) {
        $this->status = $argStatus;
        $this->data = $argData;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getData() {
        return $this->data;
    }
}

class Tokens {
    private $mysqli=null;
    private $mysqliAux=null;

    public function connect() {
        $this->mysqli=mysqli_connect($GLOBALS['databaseHost'],$GLOBALS['databaseUser'],$GLOBALS['databasePassword'],$GLOBALS['databaseName']);
        if (mysqli_connect_errno($this->mysqli)) {
            return new Result("FAIL",array("message"=>("Failed to connect to MySQL: " . mysqli_connect_error())));
        } else {
            return new Result("OK",array("message"=>"Database connection sucessful!"));
        }
    }

    public function connectAuxiliary() {
        $this->mysqliAux=mysqli_connect($GLOBALS['databaseHost'],$GLOBALS['databaseUser'],$GLOBALS['databasePassword'],$GLOBALS['databaseName']);
        if (mysqli_connect_errno($this->mysqliAux)) {
            return new Result("FAIL",array("message"=>("Failed to connect to MySQL: " . mysqli_connect_error())));
        } else {
            return new Result("OK",array("message"=>"Database connection sucessful!"));
        }
    }

    public function authenticate($argUsername,$argPassword) {
        $loginUsername = htmlspecialchars($argUsername);
        $loginPassword = htmlspecialchars($argPassword);
        $connectionAttempt = $this->connect();
        $return=null;
        if ($connectionAttempt->getStatus()=="OK") {
            if ($stmt = $this->mysqli->prepare("SELECT password FROM users WHERE user=?")) {
                if (!$stmt->bind_param("s",$loginUsername)) {
                    $result = new Result("FAIL",array("message"=>"Failed to bind parameters: " . $loginUsername . ". Error: " . $this->mysqli->error));
                } else {
                  if (!$stmt->execute()) {
                    $result= new Result("FAIL",array("message"=>"Failed to execute query. Error: " . $this->mysqli->error));
                  } else {
                    $stmt->bind_result($resultPassword);
                    $userPassword="";
                    while ($stmt->fetch()) {
                      $userPassword = $resultPassword;
                    }
                    if (password_verify($loginPassword,$userPassword)) {
                        $token=password_hash($loginUsername,PASSWORD_DEFAULT);
                        $tokenCreationResult = $this->insertToken($loginUsername,$token);
                        if ($tokenCreationResult->getStatus()=="OK") {
                            $result= new Result("OK",array("message"=>"Valid credentials","token"=>$token));
                        } else {
                            $result= new Result("FAIL",$tokenCreationResult->getData());
                        }
                    } else {
                        $result= new Result("INVALID",array("message"=>"Invalid credentials"));
                    }
                  }
                }
            } else {
                $result= new Result("FAIL",array("message"=>"Failed to prepare query. Error: " . $this->mysqli->error));
            }
            $this->mysqli->close();
        } else {
            return $connectionAttempt;
        }
        return $result;
    }

    private function insertToken($argUsername,$argToken) {
        $connectionAttempt = $this->connectAuxiliary();
        $return=null;
        if ($connectionAttempt->getStatus()=="OK") {
            if ($stmt = $this->mysqliAux->prepare("INSERT INTO tokens (user,token) VALUES(?,?)")) {
                if (!$stmt->bind_param("ss",$argUsername,$argToken)) {
                    $result = new Result("FAIL",array("message"=>"Failed to bind parameters: " . $argUsername . ". Error: " . $this->mysqliAux->error));
                } else {
                  if (!$stmt->execute()) {
                    $result= new Result("FAIL",array("message"=>"Failed to execute query. Error: " . $this->mysqliAux->error));
                  } else {
                    $result= new Result("OK",array("message"=>"New token created."));
                  }
                }
            } else {
                $result= new Result("FAIL",array("message"=>"Failed to prepare query. Error: " . $this->mysqliAux->error));
            }
            $this->mysqliAux->close();
        } else {
            return $connectionAttempt;
        }
        return $result;
    }

    public function validateToken($argToken) {
        // This function will be removed for Lab 11
        $token=htmlspecialchars($argToken);
        $connectionAttempt = $this->connect();
        $return=null;
        if ($connectionAttempt.getStatus()!="OK") {
            if ($stmt = $this->mysqli->prepare("SELECT user FROM tokens WHERE token=?")) {
                if (!$stmt->bind_param("s",$token)) {
                    $result = new Result("FAIL",array("message"=>"Failed to bind parameters: " . $token . ". Error: " . $this->mysqli->error));
                } else {
                  if (!$stmt->execute()) {
                    $result= new Result("FAIL",array("message"=>"Failed to execute query. Error: " . $this->mysqli->error));
                  } else {
                    $stmt->bind_result($resultUser);
                    $count=0;
                    while ($stmt->fetch()) {
                        $count++;
                    }
                    if ($count>0) {
                        $result= new Result("OK",array("message"=>"Valid token"));
                    } else {
                        $result= new Result("INVALID",array("message"=>"Invalid token"));
                    }
                  }
                }
            } else {
                $result= new Result("FAIL",array("message"=>"Failed to prepare query. Error: " . $this->mysqli->error));
            }
            $this->mysqli->close();
        } else {
            return $connectionAttempt;
        }
        return $result;
    }
}

?>