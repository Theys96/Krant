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

<div class="stukje card mt-3 shadow-sm border-1">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo htmlspecialchars(cap($article->title, 40)); ?></h5>
        <small class="text-muted"><?php echo $authors; ?></small>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item p-2">
            <div class="d-flex justify-content-between">
                <?php if ($article->ready === true): ?>
                    <span><span class='badge badge-success'>Klaar</span> <b><?php echo count($article->checkers); ?></b> check(s): <?php echo (count($article->checkers) == 0 ? "" : ": ") . $checkers; ?></span>
                <?php else: ?>
                    <span class='badge badge-warning'>Niet klaar</span>
                <?php endif; ?>
                <span><p class="mb-0"><span class='badge badge-secondary'><?php echo htmlspecialchars($article->category?->name); ?></span></p></span>
            </div>
        </li>
        <li class="list-group-item p-2">
            <p class="text-muted mb-0">
                <i><?php echo htmlspecialchars(cap($article->contents, 75)); ?></i>
            </p>
        </li>
        <li class="list-group-item p-2 d-flex justify-content-between">
            <div class='emoji-reactions' data-article-id="<?php echo $article->id; ?>"></div>
            <span><b><?php echo $lengte = strlen($article->contents); ?></b> tekens</span>
        </li>
    </ul>
    <div class="card-footer d-flex justify-content-between">
        <?php if ($role != 2 && $article->status === Article::STATUS_OPEN): ?>
            <a class="btn btn-warning px-5" href="?action=edit&stukje=<?php echo $article->id; ?>">Wijzigen</a>
        <?php endif; if ($role == 2 && $article->status === Article::STATUS_OPEN && $article->ready === true && !in_array(Session::instance()->getUser()->id, $authors_ids)): ?>
            <a class="btn btn-warning px-5" href="?action=check&stukje=<?php echo $article->id; ?>">Nakijken</a>
        <?php endif; ?>
        <a class="btn btn-primary px-5" href="?action=read&stukje=<?php echo $article->id; ?>&source=<?php echo $list_type; ?>">Lezen</a>
        <?php if ($role == 3): ?>
            <?php if ($article->status === Article::STATUS_OPEN): ?>
               <a class="btn btn-danger px-5" href="?action=<?php echo $list_type; ?>&remove_article=<?php echo $article->id; ?>">Verwijderen</a>
            <?php elseif ($article->status !== Article::STATUS_DRAFT):?>
               <a class="btn btn-danger px-5" href="?action=<?php echo $list_type; ?>&open_article=<?php echo $article->id; ?>">Terugzetten</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
