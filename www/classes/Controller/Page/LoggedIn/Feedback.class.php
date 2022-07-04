<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Log;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Feedback pagina.
 */
class Feedback extends LoggedIn
{
    public function __construct()
    {
        if (isset($_POST['text'])) {
            $result = Log::createNew(
                Log::TYPE_FEEDBACK,
                Session::instance()->getUser()->id,
                Session::instance()->getRole(),
                $_POST['text']
            );
            if ($result) {
                ErrorHandler::instance()->addMessage("Feedback verzonden. " . $result->timestamp?->format('Y-m-d H:i:s'));
            } else {
                ErrorHandler::instance()->addError("Er is iets misgegaan bij het verzenden van de feedback.");
            }
        }
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.feedback', []);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1,2,3];
    }
}
