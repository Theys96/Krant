<?php

use App\Model\Article;
use App\Model\ArticleReaction;
use App\Model\User;
use App\Util\Singleton\Session;

/**
 * @var Article $article
 * @var int     $role
 * @var string  $list_type
 */
$authors = $article->getAuthorsString();
$liveDrafters = User::getLiveDrafters($article->id);
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
$reactions = ArticleReaction::getByArticleIdGrouped($article->id);
?>

<div class="stukje card mt-3 shadow-sm border-1">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo htmlspecialchars(cap($article->title, 40)); ?></h5>
        <small class="text-muted"><?php echo $authors; ?></small>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item p-2">
            <div class="d-flex justify-content-between">
                <p class="mb-0">
                <?php if (true === $article->ready) { ?>
                    <span class='badge badge-success mr-2'>Klaar</span><b><?php echo count($article->checkers); ?></b> check(s)<?php echo (0 == count($article->checkers) ? '' : ': ').$checkers; ?>
                <?php } else { ?>
                    <span class='badge badge-warning'>Niet klaar</span>
                <?php } ?>
                </p>
                <div class="d-flex">
                <p class="mb-0">
                    <?php
                    if (true === $article->picture) {
                        echo "<span class='badge badge-secondary'>Foto</span>";
                    }
?>
                </p>
                <p class="mb-0">
                    <?php
if (true === $article->wjd) {
    echo "<span class='badge badge-secondary ml-1'>WJD</span>";
}
?>
                </p>
                <p class="mb-0">
                    <span class='badge badge-secondary ml-1'><?php echo htmlspecialchars($article->category?->name); ?></span>
                </p>
                </div>
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
        <?php if (2 != $role && Article::STATUS_OPEN === $article->status) { ?>
            <a class="btn btn-primary" href="?action=edit&stukje=<?php echo $article->id; ?>">Wijzigen</a>
        <?php }
        if (2 == $role && Article::STATUS_OPEN === $article->status && 0 == count($liveDrafters) && true === $article->ready && !in_array(Session::instance()->getUser()->id, $authors_ids)) { ?>
            <a class="btn btn-primary" href="?action=check&stukje=<?php echo $article->id; ?>">Nakijken</a>
        <?php } ?>
        <a class="btn btn-primary" href="?action=read&stukje=<?php echo $article->id; ?>&source=<?php echo $list_type; ?>">Lezen</a>
        <?php if (3 == $role) { ?>
            <?php if (Article::STATUS_OPEN === $article->status) { ?>
               <a class="btn btn-danger" href="?action=<?php echo $list_type; ?>&remove_article=<?php echo $article->id; ?>">Verwijderen</a>
            <?php } elseif (Article::STATUS_DRAFT !== $article->status) { ?>
               <a class="btn btn-warning" href="?action=<?php echo $list_type; ?>&open_article=<?php echo $article->id; ?>">Terugzetten</a>
            <?php } ?>
        <?php } ?>
    </div>
</div>
