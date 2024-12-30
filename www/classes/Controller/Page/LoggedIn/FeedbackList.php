<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Category;
use Model\Log;
use Model\User;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Feedback overzicht pagina.
 */
class FeedbackList extends LoggedIn
{
    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.feedbacklist', [
            'feedback' => Log::getByType(Log::TYPE_FEEDBACK)
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [3];
    }
}
