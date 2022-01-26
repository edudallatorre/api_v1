<?php

require_once('Task.php');

try {
	$task = new Task(1, "Title Here", "Description Here", "15/07/2021 09:06", "N");
	header('Content-type: application/json;charset=UTF-8');
	echo json_encode($task->returnTaskAsArray());
}
catch (TaskException $ex) {
	echo "Error: ".$ex->getMessage();
}