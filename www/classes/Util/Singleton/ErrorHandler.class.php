<?php
namespace Util\Singleton;

use JetBrains\PhpStorm\NoReturn;
use Throwable;

Class ErrorHandler {
	
	private array $errors = array();
	private array $warnings = array();
	private array $messages = array();

	private static ?ErrorHandler $instance = null;

    protected const FATAL_ERROR_TITLE = 'Oeps!';

	public static function instance(): ErrorHandler
	{
		if (self::$instance === null) {
			self::$instance = new ErrorHandler();
		}
		return self::$instance;
	}

    #[NoReturn] public static function exceptionHandler(Throwable $exception): void
    {
        ErrorHandler::instance()->throwFatal($exception->getMessage());
    }

    /**
     * @param string $message
     * @return void
     */
	#[NoReturn] public function throwFatal(string $message): void
    {
		$this->log("Fatal: " . $message);
		echo '<center>';
			echo '<h1>' . self::FATAL_ERROR_TITLE . '</h1>';
			echo '<p>' . $message . '</p>';
		echo '</center>';
		exit();
	}

	function addError($message): void
    {
		$this->errors[] = $message;
		$this->log("Error: " . $message);
	}

	function addWarning($message): void
    {
		$this->warnings[] = $message;
		$this->log("Warning: " . $message);
	}

	function addMessage($message): void
    {
		$this->messages[] = $message;
	}

	function numErrors(): int
    {
		return count($this->errors);
	}

	function numWarnings(): int
    {
		return count($this->warnings);
	}

	function numMessages(): int
    {
		return count($this->messages);
	}

	function printErrors(): void
    {
		echo "<center><h2 class='text-danger'>Error</h2>\n";
		echo "<p class='text-danger'>" . implode("<br />\n", $this->errors) . "</p>\n";
		echo "</center>";
		$this->errors = array();
	}

	function printWarnings(): void
    {
		echo "<center><h2 class='text-warning'>Waarschuwing</h2>\n";
		echo "<p class='text-warning'>" . implode("<br />\n", $this->warnings) . "</p>\n";
		echo "</center>";
		$this->warnings = array();
	}

	function printMessages(): void
    {
		echo "<center>";
		echo "<p class='text-success'>" . implode("<br />\n", $this->messages) . "</p>\n";
		echo "</center>";
		$this->messages = array();
	}

	function printAll(): void
    {
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

    function printAllToString(): string
    {
        ob_start();
        $this->printAll();
        return ob_get_clean();
    }

	function log($message) {
        // TODO: Implement.
	}
}
