<?php
namespace Util\Singleton;

Class ErrorHandler {
	
	private $errors = array();
	private $warnings = array();
	private $messages = array();
	public $session;

	private static ?ErrorHandler $instance = null;

	public static function instance(): ErrorHandler
	{
		if (self::$instance === null) {
			self::$instance = new ErrorHandler();
		}
		return self::$instance;
	}

	function throwFatal($message) {
		$this->log("Fatal: " . $message);
		echo "<center>\n";
			echo "<h1>Fatal error</h1>\n";
			echo "<p>" . $message . "</p>\n";
		echo "</center>\n";
		exit();
	}

	function throwError($message) {
		$this->errors[] = $message;
		$this->log("Error: " . $message);
	}

	function throwWarning($message) {
		$this->warnings[] = $message;
		$this->log("Warning: " . $message);
	}

	function throwMessage($message) {
		$this->messages[] = $message;
	}

	function numErrors() {
		return count($this->errors);
	}

	function numWarnings() {
		return count($this->warnings);
	}

	function numMessages() {
		return count($this->messages);
	}

	function printErrors() {
		echo "<center><h2 class='text-danger'>Error</h2>\n";
		echo "<p class='text-danger'>" . implode("<br />\n", $this->errors) . "</p>\n";
		echo "</center>";
		$this->errors = array();
	}

	function printWarnings() {
		echo "<center><h2 class='text-warning'>Waarschuwing</h2>\n";
		echo "<p class='text-warning'>" . implode("<br />\n", $this->warnings) . "</p>\n";
		echo "</center>";
		$this->warnings = array();
	}

	function printMessages() {
		echo "<center>";
		echo "<p class='text-success'>" . implode("<br />\n", $this->messages) . "</p>\n";
		echo "</center>";
		$this->messages = array();
	}

	function printAll() {
		if ($this->numErrors()) {
			$this->printErrors();
		}
		if ($this->numWarnings()) {
			$this->printWarnings();
		}
		if ($this->numMessages()) {
			$this->printMessages();
		}
	}

	function arrayAll() {
		$array = array('error' => '', 'warning' => '', 'message' => '');
		if ($this->numErrors()) {
			ob_start();
			$this->printErrors();
			$array['error'] = ob_get_clean();
		}
		if ($this->numWarnings()) {
			ob_start();
			$this->printWarnings();
			$array['warning'] = ob_get_clean();
		}
		if ($this->numMessages()) {
			ob_start();
			$this->printMessages();
			$array['message'] = ob_get_clean();
		}
		return $array;
	}

	function log($message) {
		if (isset($this->session)) {
			$this->session->log($message);
		}
	}
}
?>