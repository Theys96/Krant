<?php

namespace Util;

use Controller\API\Draft\NewDraft;
use Controller\API\Draft\UpdateDraft;
use Controller\API\ExceptionResponse;
use Controller\Page\LoggedIn;
use Controller\Page\Login;
use Controller\Page\Logout;
use Controller\Response;
use Exception;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;

/**
 * Controller router.
 */
class Router
{
    /** @var string[] */
    private array $actions = [
        'categories' => LoggedIn\Categories::class,
        'create' => LoggedIn\Create::class,
        'edit' => LoggedIn\Edit::class,
        'check' => LoggedIn\Check::class,
        'list' => LoggedIn\ArticleList\Open::class,
        'read' => LoggedIn\Read::class,
        'drafts' => LoggedIn\ArticleList\Drafts::class,
        'placed' => LoggedIn\ArticleList\Placed::class,
        'bin' => LoggedIn\ArticleList\Bin::class,
        'schrijfregels' => LoggedIn\Schrijfregels::class,
        'feedback' => LoggedIn\Feedback::class,
        'edit_category' => LoggedIn\EditCategory::class,
        'users' => LoggedIn\Users::class,
        'edit_user' => LoggedIn\EditUser::class,
        'feedbacklist' => LoggedIn\FeedbackList::class,
        'editions' => LoggedIn\Editions::class,
        'edit_edition' => LoggedIn\EditEdition::class,
        'migrate_edition' => LoggedIn\MigrateEdition::class,
        'overview' => LoggedIn\Overview::class,
    ];

    /**
     * @return Response
     * @throws Exception
     */
    public function get_page_controller_instance(): Response
    {
        if (Session::instance()->isLoggedIn()) {
            if (isset($_GET['action'])) {
                if ($_GET['action'] === 'logout') {
                    $response = new Logout();
                } elseif (array_key_exists($_GET['action'], $this->actions)) {
                    $response = new $this->actions[$_GET['action']]();
                    if ($response instanceof LoggedIn && !in_array(
                            Session::instance()->getRole(),
                            $response->allowed_roles()
                        )) {
                        ErrorHandler::instance()->addWarning('Deze pagina is niet toegankelijk voor deze rol.');
                        $response = new LoggedIn\ArticleList\Open();
                    }
                } else {
                    ErrorHandler::instance()->addWarning('Pagina niet gevonden.');
                    $response = new LoggedIn\ArticleList\Open();
                }
            } else {
                $response = new LoggedIn\ArticleList\Open();
            }
        } else {
            $response = new Login();
        }

        if (!is_a($response, Response::class)) {
            throw new Exception('Pagina kon niet worden geladen.');
        }
        return $response;
    }

    /**
     * @return Response
     */
    public function get_api_controller_instance(): Response
    {
        if (!array_key_exists('action', $_REQUEST)) {
            return new ExceptionResponse(500, 'Action not found.');
        }
        return match ($_REQUEST['action']) {
            'new_draft' => new NewDraft(),
            'update_draft' => new UpdateDraft(),
            default => new ExceptionResponse(500, 'Action not found.'),
        };
    }
}
