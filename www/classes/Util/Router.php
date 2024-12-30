<?php

namespace Util;

use Controller\API\Draft\NewDraft;
use Controller\API\Draft\UpdateDraft;
use Controller\API\ExceptionResponse;
use Controller\LoggedIn;
use Controller\Page\LoggedIn\ArticleList\Bin;
use Controller\Page\LoggedIn\ArticleList\Drafts;
use Controller\Page\LoggedIn\ArticleList\Open;
use Controller\Page\LoggedIn\ArticleList\Placed;
use Controller\Page\LoggedIn\Categories;
use Controller\Page\LoggedIn\Check;
use Controller\Page\LoggedIn\Create;
use Controller\Page\LoggedIn\Edit;
use Controller\Page\LoggedIn\EditCategory;
use Controller\Page\LoggedIn\EditEdition;
use Controller\Page\LoggedIn\Editions;
use Controller\Page\LoggedIn\EditUser;
use Controller\Page\LoggedIn\Feedback;
use Controller\Page\LoggedIn\FeedbackList;
use Controller\Page\LoggedIn\MigrateEdition;
use Controller\Page\LoggedIn\Overview;
use Controller\Page\LoggedIn\Read;
use Controller\Page\LoggedIn\Schrijfregels;
use Controller\Page\LoggedIn\Users;
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
        'categories' => Categories::class,
        'create' => Create::class,
        'edit' => Edit::class,
        'check' => Check::class,
        'list' => Open::class,
        'read' => Read::class,
        'drafts' => Drafts::class,
        'placed' => Placed::class,
        'bin' => Bin::class,
        'schrijfregels' => Schrijfregels::class,
        'feedback' => Feedback::class,
        'edit_category' => EditCategory::class,
        'users' => Users::class,
        'edit_user' => EditUser::class,
        'feedbacklist' => FeedbackList::class,
        'editions' => Editions::class,
        'edit_edition' => EditEdition::class,
        'migrate_edition' => MigrateEdition::class,
        'overview' => Overview::class,
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
                        $response = new Open();
                    }
                } else {
                    ErrorHandler::instance()->addWarning('Pagina niet gevonden.');
                    $response = new Open();
                }
            } else {
                $response = new Open();
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
