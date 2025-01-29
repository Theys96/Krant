<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Model\Log;
use Util\ViewRenderer;

/**
 * Feedback overzicht pagina.
 */
class FeedbackList extends LoggedInPage
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
