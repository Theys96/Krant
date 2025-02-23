<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\Log;
use App\Util\Singleton\ErrorHandler;
use App\Util\ViewRenderer;

/**
 * Feedback pagina.
 */
class Feedback extends LoggedInPage
{
    public function __construct()
    {
        if (isset($_POST['text'])) {
            $result = Log::logFeedback($_POST['text']);
            if ($result) {
                ErrorHandler::instance()->addMessage('Feedback verzonden.');
            } else {
                ErrorHandler::instance()->addError('Er is iets misgegaan bij het verzenden van de feedback.');
            }
        }
    }

    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.feedback', []);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
