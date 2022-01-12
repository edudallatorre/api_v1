<?php

require_once('Response.php');

//response success
// $response = new Response();
// $response->setSuccess(true);
// $response->setHttpStatusCode(200);
// $response->addMessage("Test Message 1");
// $response->addMessage("Test Message 2");
// $response->send();

// response error
$response = new Response();
$response->setSuccess(false);
$response->setHttpStatusCode(404);
$response->addMessage("Error with value");
$response->send();