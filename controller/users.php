<?php

require_once('db.php');
require_once('../model/response.php');

try {

	$writeDB = DB::connectWriteDB();

} catch (PDOException $ex) {
	error_log("Connection Error:" . $ex, 0);
	$response = new Response();
	$response->setHttpStatusCode(500);
	$response->setSuccess(false);
	$response->addMessage("Database connection error");
	$response->send();
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$response = new Response();
	$response->setHttpStatusCode(405);
	$response->setSuccess(false);
	$response->addMessage("Request method not allowed");
	$response->send();
	exit;
}

if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
	$response = new Response();
	$response->setHttpStatusCode(400);
	$response->setSuccess(false);
	$response->addMessage("Content type header not set to JSON");
	$response->send();
	exit;
}

$rawPostData = file_get_contents('php://input');

if (!$jsonData = json_decode($rawPostData)) {
	$response = new Response();
	$response->setHttpStatusCode(400);
	$response->setSuccess(false);
	$response->addMessage("Request body is not valid JSON");
	$response->send();
	exit;
}

if (!isset($jsonData->fullname) || !isset($jsonData->username) || !isset($jsonData->password)) {
	$response = new Response();
	$response->setHttpStatusCode(400);
	$response->setSuccess(false);
	(!isset($jsonData->fullname) ? $response->addMessage("Full name not supplied") : false);
	(!isset($jsonData->username) ? $response->addMessage("Username not supplied") : false);
	(!isset($jsonData->password) ? $response->addMessage("Password not supplied") : false);
	$response->send();
	exit;
}

if (strlen($jsonData->fullname) < 1 || strlen($jsonData->fullname) > 255 || strlen($jsonData->username) < 1 || strlen($jsonData->username) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 255) {
	$response = new Response();
	$response->setHttpStatusCode(400);
	$response->setSuccess(false);
	(!isset($jsonData->fullname) < 1 ? $response->addMessage("Full name cannot be blank") : false);
	(!isset($jsonData->fullname) > 255 ? $response->addMessage("Full name cannot be greater than 255 characters") : false);
	(!isset($jsonData->username) < 1 ? $response->addMessage("Full name cannot be blank") : false);
	(!isset($jsonData->username) > 255 ? $response->addMessage("Full name cannot be greater than 255 characters") : false);
	(!isset($jsonData->password) < 1 ? $response->addMessage("Full name cannot be blank") : false);
	(!isset($jsonData->password) > 255 ? $response->addMessage("Full name cannot be greater than 255 characters") : false);
	$response->send();
	exit;
}

$fullname = trim($jsonData->fullname);
$username = trim($jsonData->username);
$password = $jsonData->password;

try {

	$query = $writeDB->prepare('select id from tblusers');
	
} catch (PDOException $ex) {
	error_log("Database query error " . $ex, 0);
	$response = new Response();
	$response->setHttpStatusCode(500);
	$response->setSuccess(false);
	$response->addMessage("There was an issue creating a user account - please try again.");
	exit;
}





