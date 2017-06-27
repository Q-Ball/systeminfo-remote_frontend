<?php

$dbhost = "IPADDRESS";
$dbuser = "USER";
$dbpass = "PASSWORD";
$dbname = "DBNAME";

function ping($pingHost) {
	exec("ping -n 2 " . $pingHost, $pingOutput, $pingResult);
	return $pingResult;
}

try {
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$query = $db->query("SELECT * FROM `status`");
	$result = $query->fetchAll(PDO::FETCH_ASSOC); $result = array_filter($result);
	foreach ($result as &$value) {
		if (ping($value["pcname"]) === 0) {
			$value["status"] = "online";
		} else {
			$value["status"] = "offline";
		}
	}
	echo json_encode($result);
} catch(PDOException $e) {
	echo $e->getMessage(); // Remove or change message in production code
}

?>