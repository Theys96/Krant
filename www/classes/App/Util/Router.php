<?php

namespace App\Util;

use App\Controller\API\Draft\NewDraft;
use App\Controller\API\Draft\UpdateDraft;
use App\Controller\API\ExceptionResponse;
use App\Controller\API\Reactions\AddReaction;
use App\Controller\API\Reactions\FetchReactions;
use App\Controller\LoggedIn;
use App\Controller\Page\LoggedIn\ArticleList\Bin;
use App\Controller\Page\LoggedIn\ArticleList\Drafts;
use App\Controller\Page\LoggedIn\ArticleList\Open;
use App\Controller\Page\LoggedIn\ArticleList\Placed;
use App\Controller\Page\LoggedIn\Categories;
use App\Controller\Page\LoggedIn\Check;
use App\Controller\Page\LoggedIn\Create;
use App\Controller\Page\LoggedIn\Edit;
use App\Controller\Page\LoggedIn\EditCategory;
use App\Controller\Page\LoggedIn\EditConfiguration;
use App\Controller\Page\LoggedIn\EditEdition;
use App\Controller\Page\LoggedIn\Editions;
use App\Controller\Page\LoggedIn\EditUser;
use App\Controller\Page\LoggedIn\Feedback;
use App\Controller\Page\LoggedIn\FeedbackList;
use App\Controller\Page\LoggedIn\MigrateEdition;
use App\Controller\Page\LoggedIn\Overview;
use App\Controller\Page\LoggedIn\Read;
use App\Controller\Page\LoggedIn\Schrijfregels;
use App\Controller\Page\LoggedIn\Users;
use App\Controller\Page\Login;
use App\Controller\Page\Logout;
use App\Controller\Page\Minigame;
use App\Controller\Response;
use App\Util\Singleton\ErrorHandler;
use App\Util\Singleton\Session;
use Exception;

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
        'minigame' => Minigame::class,
        'configuratie' => EditConfiguration::class,
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
            'add_reaction' => new AddReaction(),
            'fetch_reactions' => new FetchReactions(),
            default => new ExceptionResponse(500, 'Action not found.'),
        };
    }
}
