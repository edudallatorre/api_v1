<?php 
// Task Model Object

// empty TaskException class so we can catch task errors
class TaskException extends Exception { }

class Task {

	private $_id;
	private $_title;
	private $_description;
	private $_deadline;	
	private $_completed;

	public function __construct($id, $title, $description, $deadline, $completed) {
		$this->setID($id);
		$this->setTitle($title);
		$this->setDescription($description);
		$this->setDeadline($deadline);
		$this->setCompleted($completed);
	}

	public function getID() {
		return $this->_id;
	}

	public function getTitle() {
		return $this->_title;
	}

	public function getDescription() {
		return $this->_description;
	}

	public function getDeadline() {
		return $this->_deadline;
	}

	public function getCompleted() {
		return $this->_completed;
	}

	public function setID($id) {
		if(($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)) {
			throw new taskException("Task ID error");
		}
		$this->_id = $id;
	}

	public function setTitle($title) {
		if(strlen($title) < 1 || strlen($title) > 255) {
			throw new taskException("Task title error");
		}
		$this->_title = $title;
	}

	public function setDescription($description) {
		if(($description !== null) && (strlen($description) == 0 || strlen($description) > 16777215)) {
			throw new taskException("Task description error");
		}
		$this->_description = $description;
	}

	public function setDeadline($deadline) {
		if(($deadline !== null) && !date_create_from_format('d/m/Y H:i', $deadline) || date_format(date_create_from_format('d/m/Y H:i', $deadline), 'd/m/Y H:i') != $deadline) {
			throw new taskException("Task deadline date and time error");
	  }
	  $this->_deadline = $deadline;
	}

	public function setCompleted($completed) {
		if(strtoupper($completed) !== 'Y' && strtoupper($completed) !== 'N') {
			throw new taskException("Task completed is not Y or N");
		}
		$this->_completed = strtoupper($completed);
	}

	public function returnTaskAsArray() {
		$task = array();
		$task['id'] = $this->getID();
		$task['title'] = $this->getTitle();
		$task['description'] = $this->getDescription();
		$task['deadline'] = $this->getDeadline();
		$task['completed'] = $this->getCompleted();
		return $task;
	}
  
}