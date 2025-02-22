<?php

namespace App\Util\Singleton;

use App\Model\Log;
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
    public static function exceptionHandler(Throwable $exception): void
    {
        ErrorHandler::instance()->throwFatal($exception->getMessage(), $exception->getFile(), $exception->getLine());
    }

    /**
     * @param string $message
     * @param string|null $file
     * @param int|null $line
     * @return void
     */
    public function throwFatal(string $message, ?string $file = null, ?int $line = null): void
    {
        echo '<center>';
        echo '<h1>' . self::FATAL_ERROR_TITLE . '</h1>';
        echo '<p>Als je dit leest is er iets helemaal misgegaan (heeft Thijs dus iets verprutst). De foutmelding is:</p>';
        echo '<xmp>' . $message . '</xmp>';
        $log_message = 'Fatal: ' . $message . PHP_EOL;
        if ($file !== null) {
            echo '<p>(' . $file;
            $log_message .= ' ' . $file;
            if ($line !== null) {
                echo ':' . $line;
                $log_message .= ':' . $line;
            }
            $log_message .= PHP_EOL;
            echo ')</p>';
        }
        if (Database::instance()) {
            if (Database::instance()->getStoredQuery() !== null) {
                echo '<p>Laatste query: </p><xmp>' . Database::instance()->getStoredQuery() . '</xmp>';
                $log_message .= Database::instance()->getStoredQuery() . PHP_EOL;
            }
            Log::logError($log_message);
        }
        echo '</center>';
        exit();
    }

    /**
     * @param string $message
     * @return void
     */
    public function addError(string $message): void
    {
        $this->errors[] = $message;
        Log::logError($message);
    }

    /**
     * @param string $message
     * @return void
     */
    public function addWarning(string $message): void
    {
        $this->warnings[] = $message;
        Log::logWarning($message);
    }

    /**
     * @param string $message
     * @return void
     */
    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @return int
     */
    public function numErrors(): int
    {
        return count($this->errors);
    }

    /**
     * @return int
     */
    public function numWarnings(): int
    {
        return count($this->warnings);
    }

    /**
     * @return int
     */
    public function numMessages(): int
    {
        return count($this->messages);
    }

    /**
     * @return void
     */
    public function printErrors(): void
    {
        echo "<center><h2 class='text-danger'>Error</h2>\n";
        echo "<p class='text-danger'>" . implode("<br />\n", $this->errors) . "</p>\n";
        echo "</center>";
        $this->errors = array();
    }

    /**
     * @return void
     */
    public function printWarnings(): void
    {
        echo "<center><h2 class='text-warning'>Error</h2>\n";
        echo "<p class='text-warning'>" . implode("<br />\n", $this->warnings) . "</p>\n";
        echo "</center>";
        $this->warnings = array();
    }

    /**
     * @return void
     */
    public function printMessages(): void
    {
        echo "<center>";
        echo "<p class='text-success'>" . implode("<br />\n", $this->messages) . "</p>\n";
        echo "</center>";
        $this->messages = array();
    }

    /**
     * @return void
     */
    public function printAll(): void
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
    public function printAllToString(): string
    {
        ob_start();
        $this->printAll();
        return ob_get_clean();
    }
}
