<?php

namespace Util\Singleton;

use JetBrains\PhpStorm\NoReturn;
use Throwable;

/**
 * Handler voor errors, warnings en dergelijke.
 */
class ErrorHandler
{
    /** @var string[] */
    private array $errors = [];

    /** @var string[] */
    private array $warnings = [];

    /** @var string[] */
    private array $messages = [];

    /** @var ErrorHandler|null */
    private static ?ErrorHandler $instance = null;

    /** @var string FATAL_ERROR_TITLE */
    protected const FATAL_ERROR_TITLE = 'Oeps!';

    /**
     * @return ErrorHandler
     */
    public static function instance(): ErrorHandler
    {
        if (self::$instance === null) {
            self::$instance = new ErrorHandler();
        }
        return self::$instance;
    }

    /**
     * @param Throwable $exception
     * @return void
     */
    #[NoReturn] public static function exceptionHandler(Throwable $exception): void
    {
        ErrorHandler::instance()->throwFatal($exception->getMessage(), $exception->getFile(), $exception->getLine());
    }

    /**
     * @param string $message
     * @return void
     */
    #[NoReturn] public function throwFatal(string $message, ?string $file = null, ?int $line = null): void
    {
        $this->log("Fatal: " . $message);
        echo '<center>';
        echo '<h1>' . self::FATAL_ERROR_TITLE . '</h1>';
        echo '<p>Als je dit leest is er iets helemaal misgegaan (heeft Thijs dus iets verprutst). De foutmelding is:</p>';
        echo '<xmp>' . $message . '</xmp>';
        if ($file !== null) {
            echo '<p>(' . $file;
            if ($line !== null) {
                echo ':' . $line;
            }
            echo ')</p>';
        }
        if (Database::instance() && Database::instance()->getStoredQuery() !== null) {
            echo '<p>Laatste query: </p><xmp>' . Database::instance()->getStoredQuery() . '</xmp>';
        }
        echo '</center>';
        exit();
    }

    /**
     * @param string $message
     * @return void
     */
    function addError(string $message): void
    {
        $this->errors[] = $message;
        $this->log("Error: " . $message);
    }

    /**
     * @param string $message
     * @return void
     */
    function addWarning(string $message): void
    {
        $this->warnings[] = $message;
        $this->log("Warning: " . $message);
    }

    /**
     * @param string $message
     * @return void
     */
    function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @return int
     */
    function numErrors(): int
    {
        return count($this->errors);
    }

    /**
     * @return int
     */
    function numWarnings(): int
    {
        return count($this->warnings);
    }

    /**
     * @return int
     */
    function numMessages(): int
    {
        return count($this->messages);
    }

    /**
     * @return void
     */
    function printErrors(): void
    {
        echo "<center><h2 class='text-danger'>Error</h2>\n";
        echo "<p class='text-danger'>" . implode("<br />\n", $this->errors) . "</p>\n";
        echo "</center>";
        $this->errors = array();
    }

    /**
     * @return void
     */
    function printWarnings(): void
    {
        echo "<center><h2 class='text-warning'>Error</h2>\n";
        echo "<p class='text-warning'>" . implode("<br />\n", $this->warnings) . "</p>\n";
        echo "</center>";
        $this->warnings = array();
    }

    /**
     * @return void
     */
    function printMessages(): void
    {
        echo "<center>";
        echo "<p class='text-success'>" . implode("<br />\n", $this->messages) . "</p>\n";
        echo "</center>";
        $this->messages = array();
    }

    /**
     * @return void
     */
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

    /**
     * @return string
     */
    function printAllToString(): string
    {
        ob_start();
        $this->printAll();
        return ob_get_clean();
    }

    /**
     * @param $message
     * @return void
     */
    function log($message): void
    {
        // TODO: Implement.
    }
}
