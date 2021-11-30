<?php

require_once('db.php');
require_once('../model/Task.php');
require_once('../model/Response.php');

try {
	$writeDB = DB::connectWriteDB();
	$readDB = DB::connectReadDB();
}
catch(PDOException $ex) {
	error_log("Connection error -" .$ex, 0);
	$response = new Response();
	$response->setHttpStatusCode(500);
	$response->setSuccess(false);
	$response->addMessage("Database connection error");
	$response->send();
	exit();
}

// /task.php?taskid=1
if(array_key_exists("taskid", $_GET)) {

	$taskid = $_GET['taskid'];

	if($taskid == '' || !is_numeric($taskid)) {
		$response = new Response();
		$response->setHttpStatusCode(400);
		$response->setSuccess(false);
		$response->addMessage("Task ID cannot be blank or must be numeric");
		$response->send();
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] === 'GET') {

		try {
			// Checking the variable undefined = $readDB
			$query = $readDB->prepare('select id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, completed from tbltasks where id = :taskid');
			$query->bindParam(':taskid', $taskid, PDO::PARAM_INT);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				$response = new Response();
				$response->setHttpStatusCode(404);
				$response->setSuccess(false);
				$response->addMessage("Task not found");
				$response->send();
				exit;
			}

			while($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['completed']);
				$taskArray[] = $task->returnTaskAsArray();
			}

			$returnData = array();
			$returnData['rows_returned'] = $rowCount;
			$returnData['tasks'] = $taskArray;

			$response = new Response();
			$response->setHttpStatusCode(200);
			$response->setSuccess(true);
			$response->toCache(true);
			$response->setData($returnData);
			$response->send();
			exit;
		}

		catch(TaskException $ex) {
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage($ex->getMessage());
			$response->send();
			exit;
		}

		catch(PDOException $ex) {
			error_log("Database query error -" .$ex, 0);
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage("Failed to get task");
			$response->send();
			exit();
		}

	}
	elseif($_SERVER['REQUEST_METHOD'] === 'DELETE') {

		try {

			$query = $writeDB->prepare('delete from tbltasks where id = :taskid');
			$query->bindParam(':taskid', $taskid, PDO::PARAM_INT);
			$query->execute();

			$rowCount = $query->rowCount();

			if($rowCount === 0) {
				$response = new Response();
				$response->setHttpStatusCode(404);
				$response->setSuccess(false);
				$response->addMessage("Task not found");
				$response->send();
				exit;
			}

			$response = new Response();
			$response->setHttpStatusCode(200);
			$response->setSuccess(true);
			$response->addMessage("Task deleted");
			$response->send();
			exit;
		}
		catch(PDOException $ex) {
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage("Failed to delete task");
			$response->send();
			exit;
		}

	}
	elseif($_SERVER['REQUEST_METHOD'] === 'PATCH') {
	}
	else {
		$response = new Response();
		$response->setHttpStatusCode(405);
		$response->setSuccess(false);
		$response->addMessage("Request method not allowed");
		$response->send();
		exit;
	}
}
elseif(array_key_exists("completed", $_GET)) {

	$completed = $_GET['completed'];

	if($completed !== 'Y' && $completed !== 'N') {
		$response = new Response();
		$response->setHttpStatusCode(400);
		$response->setSuccess(false);
		$response->addMessage("Complete filter must be Y or N");
		$response->send();
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] === 'GET') {

		try {

			$query = $readDB->prepare('select id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i" ) as deadline, completed from tbltasks where completed = :completed');
			$query->bindParam(':completed', $completed, PDO::PARAM_STR);
			$query->execute();

			$rowCount = $query->rowCount();

			$taskArray = array();

			while($row = $query->fetch(PDO::FETCH_ASSOC)) {

				$task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['completed']);
				$taskArray[] = $task->returnTaskAsArray();
			}

			$returnData = array();
			$returnData['rows_returned'] = $rowCount;
			$returnData['tasks'] = $taskArray;

			$response = new Response();
			$response->setHttpStatusCode(200);
			$response->setSuccess(true);
			$response->toCache(true);
			$response->setData($returnData);
			$response->send();
			exit;
		}
		catch(PDOException $ex) {
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage($ex->getMessage());
			$response->send();
			exit;
		}
		catch(PDOException $ex) {
			error_log("Database query error -".$ex, 0);
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage($ex->getMessage());
			$response->send();
			exit;
		}

	}
	else {
		$response = new Response();
		$response->setHttpStatusCode(405);
		$response->setSuccess(false);
		$response->addMessage("Request method not allowed");
		$response->send();
		exit;
	}

}
elseif(array_key_exists("page", $_GET)) {

	if($_SERVER['REQUEST_METHOD'] === 'GET') {

		$page = $_GET['page'];

		if($page == '' || !is_numeric($page)) {
			$response = new Response();
			$response->setHttpStatusCode(400);
			$response->setSuccess(false);
			$response->addMessage("Page number cannot be blank and must be numeric");
			exit;
		}

		$limitPerPage = 20;

		try {
			$query = $readDB->prepare('select count(id) as totalNoOfTasks from tbltasks');
			$query->execute();

			$row = $query->fetch(PDO::FETCH_ASSOC);

			$tasksCount = intval($row['totalNoOfTasks']);

			$numOfPages = ceil($tasksCount/$limitPerPage);

			if($numOfPages == 0) {
				$numOfPages = 1;
			}

			if($page > $numOfPages || $page == 0) {
				$response = new Response();
				$response->setHttpStatusCode(404);
				$response->setSuccess(false);
				$response->addMessage("Page not found");
				$response->send();
				exit;
			}

			$offset = ($page == 1 ? 0 : ($limitPerPage*($page-1)));
			
			$query = $readDB->prepare('select id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, completed from tbltasks limit :pglimit offset :offset');
			$query->bindParam(':pglimit', $limitPerPage, PDO::PARAM_INT);
			$query->bindParam('offset', $offset, PDO::PARAM_INT);
			$query->execute();

			$rowCount = $query->rowCount();

			$taskArray = array();

			while($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['completed']);

				$taskArray[] = $task->returnTaskAsArray();

				$returnData = array();
				$returnData['rows_returned'] = $rowCount;
				$returnData['total_rows'] = $tasksCount;
				$returnData['total_pages'] = $numOfPages;
				($page < $numOfPages ? $returnData['has_next_page'] = true : $returnData['has_next_page'] = false);
				($page > 1 ? $returnData['has_previous_page'] = true : $returnData['has_previous_page'] = false);
				$returnData['tasks'] = $taskArray;

				$response = new Response();
				$response->setHttpStatusCode(200);
				$response->setSuccess(true);
				$response->toCache(true);
				$response->setData($returnData);
				$response->send();
				exit;
			}

		}
		catch(TaskException $ex) {
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage($ex->getMessage());
			$response->send();
			exit;
		}
		catch(PDOException $ex) {
			error_log("Database query error - " . $ex, 0);
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage("Failed to get tasks");
			$response->send();
			exit;
		}

	}
	else {
		$response = new Response();
		$response->setHttpStatusCode(405);
		$response->setSuccess(false);
		$response->addMessage("Request method not allowed");
		$response->send();
		exit;
	}
}
elseif(empty($_GET)) {

	if($_SERVER['REQUEST_METHOD'] === 'GET') {

		try {
			
			$query = $readDB->prepare('select id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, completed from tbltasks');
			$query->execute();

			$rowCount = $query->rowCount();

			$taskArray = array();

			while($row = $query->fetch(PDO::FETCH_ASSOC)) {

				$task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['completed']);
				$taskArray[] = $task->returnTaskAsArray();
			}

			$returnData = array();
			$returnData['rows_returned'] = $rowCount;
			$returnData['tasks'] = $taskArray;

			$response = new Response();
			$response->setHttpStatusCode(200);
			$response->setSuccess(true);
			$response->toCache(true);
			$response->setData($returnData);
			$response->send();
			exit;
		}
		catch (TaskException $ex) {
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage($ex->getMessage());
			$response->send();
			exit;
		}
		catch (TaskException $ex) {
			error_log("Database query error -".$ex, 0);
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage("Failed to get tasks");
			$response->send();
			exit;
		}

	}
	elseif($_SERVER['REQUEST_METHOD'] === 'POST') {

		try {

			if($_SERVER['CONTENT_TYPE'] !== 'application/json') {
				$response = new Response();
				$response->setHttpStatusCode(400);
				$response->setSuccess(false);
				$response->addMessage("Content type header is not set to JSON");
				$response->send();
				exit;
			}

			$rawPOSTData = file_get_contents('php://input');

			if(!$jasonData = json_decode($rawPOSTData)) {
				$response = new Response();
				$response->setHttpStatusCode(400);
				$response->setSuccess(false);
				$response->addMessage("Request body is not valid JSON");
				$response->send();
				exit;
			}

			if(!isset($jasonData->title) || !isset($jsonData->completed)) {// call jasondata obj and title
				$response = new Response();
				$response->setHttpStatusCode(400);
				$response->setSuccess(false);
				(!isset($jasonData->title) ? $response->addMessage("Title field is mandatory and must be provided") : false);
				(!isset($jasonData->completed) ? $response->addMessage("Completed field is mandatory and must be provided") : false);
				$response->send();
				exit;
			}

		$newTask = new Task(null, $jasonData->title, (isset($jasonData->description) ? $jasonData->description : null), (isset($jasonData->deadline) ? $jasonData->deadline : null), $jasonData->completed);

		$title = $newTask->getTitle();
		$description = $newTask->getDescription();
		$deadline = $newTask->getDeadline();
		$completed = $newTask->getCompleted();

		$query = $writeDB->prepare('insert into tbltasks (title, description, deadline, completed) values (:title, :description, STR_TO_DATE(:deadline, \'%d%m%Y %H:%i\'), :completed)');
		$query->bindParam(':title', $title, PDO::PARAM_STR);
		$query->bindParam(':description', $description, PDO::PARAM_STR);
		$query->bindParam(':deadline', $deadline, PDO::PARAM_STR);
		$query->bindParam(':completed', $completed, PDO::PARAM_STR);
		$query->execute();

		$rowCount = $query->rowCount();

		if($rowCount === 0) {
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage("Failed to create task");
			$response->send();
			exit;
		}

		$lastTaskID = $writeDB->lastInsertId();

		$query = $writeDB->prepare('select id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, completed from tbltasks where id = :taskid');
		$query->bindParam(':taskid', $lastTaskID, PDO::PARAM_INT);
		$query->execute();

		$rowCount = $query->rowCount();

		if($rowCount === 0) {
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage("Failed to retrieve task after creation");
			$response->send();
			exit;
		}

		$taskArray = array();

		while($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['completed']);
			$taskArray[] = $task->returnTaskAsArray();
		}

		$returnData = array();
		$returnData['rows_return'] = $rowCount;
		$returnData['tasks'] = $taskArray;

		$response = new Response();
		$response->setHttpStatusCode(201);
		$response->setSuccess(true);
		$response->addMessage("Task created");
		$response->setData($returnData);
		$response->send();
		exit;
		}

		catch(TaskException $ex) {
			$response = new Response();
			$response->setHttpStatusCode(400);
			$response->setSuccess(false);
			$response->addMessage($ex->getMessage());
			$response->send();
			exit;
		}
		catch(PDOException $ex) {
			error_log("Database query error - ".$ex, 0);
			$response = new Response();
			$response->setHttpStatusCode(500);
			$response->setSuccess(false);
			$response->addMessage("Failed to insert task into database - check submitted data for errors");
			$response->send();
			exit;
		}
	}
	else {
		$response = new Response();
		$response->setHttpStatusCode(405);
		$response->setSuccess(false);
		$response->addMessage("Request method not allowed");
		$response->send();
		exit;
	}
	

}
else {
	$response = new Response();
	$response->setHttpStatusCode(404);
	$response->setSuccess(false);
	$response->addMessage("Endpoint not found");
	$response->send();
	exit;
}

// /tasks/page/3
// task.php?page=3
