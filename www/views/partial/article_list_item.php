<?php
use Model\Article;
use Model\User;
use Util\Singleton\Session;

/**
 * @var Article $article
 * @var int $role
 * @var string $list_type
 */

$authors = $article->getAuthorsString();
$checkers = htmlspecialchars(implode(', ', array_map(
    static function (User $author): string {
        return $author->username;
    },
    $article->checkers
)));
$authors_ids = array_map(
    static function (User $checker): int {
        return $checker->id;
    },
    $article->authors
);
$reactions = \Model\ArticleReaction::getByArticleIdGrouped($article->id);
?>

<div class='stukje my-2 mx-1 row pt-1'>
    <div class='col-md-6'>
        <div class='row'>
            <div class='col-sm-7'><h4><b><?php echo htmlspecialchars(cap($article->title, 40)); ?></b></h4></div>
            <div class='col-sm-5 text-right'><?php echo $authors; ?></div>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='row'>
            <div class='col-7'><?php echo htmlspecialchars($article->category?->name); ?></div>
            <div class='col-5 text-right'><?php echo ($article->ready === true) ? "klaar" : "niet klaar"; ?></div>
        </div>
    </div>
    <div class='col-12 mb-2 text-center text-grey'><i><?php echo htmlspecialchars(cap($article->contents, 75)); ?></i></div>
    <div class='col-6'><b><?php echo $lengte = strlen($article->contents); ?></b> tekens</div>
    <div class='col-6 text-right'><b><?php echo count($article->checkers); ?></b> check(s)<?php echo (count($article->checkers) == 0 ? "" : ": ") . $checkers; ?></div>
    <div class='col-12'>
        <div class='row justify-content-center'>
            <?php if ($role != 2 && $article->status === Article::STATUS_OPEN): ?>
                <div class='col-4 px-1 text-center'><a class='btn btn-warning py-1 my-1 w-100' href='?action=edit&stukje=<?php echo $article->id; ?>'>Wijzigen</a></div>
            <?php endif;
if ($role == 2 && $article->status === Article::STATUS_OPEN && $article->ready === true && !in_array(Session::instance()->getUser()->id, $authors_ids)): ?>
                <div class='col-4 px-1 text-center'><a class='btn btn-warning py-1 my-1 w-100' href='?action=check&stukje=<?php echo $article->id; ?>'>Nakijken</a></div>
            <?php endif; ?>
            <?php if ($role == 3): ?>
                <?php if ($article->status === Article::STATUS_OPEN): ?>
                    <div class='col-4 px-1 text-center'><a class='btn btn-danger py-1 my-1 w-100' href='?action=<?php echo $list_type; ?>&remove_article=<?php echo $article->id; ?>'>Verwijderen</a></div>
                <?php elseif ($article->status !== Article::STATUS_DRAFT):?>
                    <div class='col-4 px-1 text-center'><a class='btn btn-danger py-1 my-1 w-100' href='?action=<?php echo $list_type; ?>&open_article=<?php echo $article->id; ?>'>Terugzetten</a></div>
                <?php endif; ?>
            <?php endif; ?>
            <div class='col-4 px-1 text-center'><a class='btn btn-primary py-1 my-1 w-100' href='?action=read&stukje=<?php echo $article->id; ?>&source=<?php echo $list_type; ?>'>Lezen</a></div>
        </div>
    </div>
    <div class='col-12 pb-1'>
        <div class="row justify-content-left">
            <div class='emoji-reactions' data-article-id="<?php echo $article->id; ?>"></div>
        </div>
    </div>
</div>
