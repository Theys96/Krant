<?php

use Model\Article;
use Model\User;

/**
 * @var Article $article
 * @var int $role
 * @var string $source
 */

$checkers = array_map(
    static function (User $author): string {
        return $author->username;
    },
    $article->checkers
);
$authors = htmlspecialchars(implode(', ', array_map(
    static function (User $author): string {
        return $author->username;
    },
    $article->authors
)));
?>
<h2 class='mb-3'>Stukje lezen</h2>
<div class='row'>
    <?php
    if (count($checkers) > 0) {
        echo "<div class='col-sm-12'>Nagekeken door " . htmlspecialchars(implode(", ", $checkers)) . ".</div>";
    }
    ?>
    <div class='col-sm-4'><b>Titel</b></b></div>
    <div class='col-sm-8'><?php echo htmlspecialchars($article->title); ?></div>
    <div class='col-sm-4'><b>Auteur</b></div>
    <div class='col-sm-8'><?php echo $authors; ?></div>
    <div class='col-sm-4'><b>Categorie</b></div>
    <div class='col-sm-8'><?php echo $article->category?->name; ?></div>
</div>
<?php echo nl2br(htmlspecialchars($article->contents)); ?>
<div class='row'>
    <div class='col-sm-4'><b>Klaar</b></div>
    <div class='col-sm-8'><?php echo ($article->ready == 1) ? 'Ja' : 'Nee'; ?></div>
</div>

<center>
    <?php if ($role === 3 && $article->status === Article::STATUS_OPEN && $article->ready): ?>
        <form method="post" action="?action=list&place_article=<?php echo $_GET['stukje']; ?>">
            <input class='btn btn-primary my-2 px-5' type='submit' value='Plaats'/>
            <a class='btn btn-info px-5' href='?action=<?php echo $source; ?>'>Terug</a>
        </form>
    <?php else: ?>
        <a class='btn btn-info px-5' href='?action=<?php echo $source; ?>'>Terug</a>
    <?php endif; ?>
</center>
