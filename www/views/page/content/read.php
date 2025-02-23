<?php

use App\Model\Article;
use App\Model\ArticleChange;
use App\Model\User;
use App\Util\ViewRenderer;
use Jfcherng\Diff\Factory\RendererFactory;

/**
 * @var Article         $article
 * @var ArticleChange[] $article_changes
 * @var int             $role
 * @var string          $source
 */
$checkers = array_map(
    static function (User $author): string {
        return $author->username;
    },
    $article->checkers
);
$authors = $article->getAuthorsString();
?>

<div class="alert alert-info fixed-bottom text-center" style="display: none" role="alert" id="copy-message"></div>

<h2 class='mb-3'>Stukje lezen</h2>
<div class='row'>
    <?php
    if (count($checkers) > 0) {
        echo "<div class='col-sm-12'>Nagekeken door ".htmlspecialchars(implode(', ', $checkers)).'.</div>';
    }
?>
    <div class='col-sm-4'><b>Titel</b></></div>
    <?php
    if (3 == $role) {
        echo "<div class='col-sm-8 click-text' data-toggle='tooltip' data-placement='left' title='Kopi&euml;ren' onclick='copyText(this, \"Titel gekopieerd.\")'>".htmlspecialchars($article->title).'</div>';
    } else {
        echo "<div class='col-sm-8'>".htmlspecialchars($article->title).'</div>';
    }
?>
    <div class='col-sm-4'><b>Auteur</b></div>
    <div class='col-sm-8'><?php echo $authors; ?></div>
    <div class='col-sm-4'><b>Categorie</b></div>
    <div class='col-sm-8'><?php echo $article->category?->name; ?></div>
    <div class='col-sm-4'><b>Klaar</b></div>
    <div class='col-sm-8'><?php echo (1 == $article->ready) ? 'Ja' : 'Nee'; ?></div>
</div>
<div class='row mt-4'>
    <?php
if (3 == $role) {
    echo "<div class='col-sm-12 tekst click-text' data-toggle='tooltip' data-placement='left' title='Kopi&euml;ren' onclick='copyText(this, \"Tekst gekopieerd.\")'>".nl2br(htmlspecialchars($article->contents)).'</div>';
} else {
    echo "<div class='col-sm-12 tekst'>".nl2br(htmlspecialchars($article->contents)).'</div>';
}
?>
</div>
<?php
if ('' != $article->context) {
    echo "<div class='row'>";
    echo "<div class='col-sm-12'><b>Context</b></div>";
    echo "<div class='col-sm-12 text-grey'>".nl2br(htmlspecialchars($article->context)).'</div>';
    echo '</div>';
} ?>

<center>
    <?php if (3 === $role && Article::STATUS_OPEN === $article->status && $article->ready) { ?>
        <form class="d-inline" method="post" action="?action=list&place_article=<?php echo $_GET['stukje']; ?>">
            <input class='btn btn-primary my-2 px-5' type='submit' value='Plaats'/>
        </form>
    <?php } ?>
    <a class='btn btn-info px-5' href='?action=<?php echo $source; ?>'>Terug</a>
</center>

<hr />

<input type="hidden" id="article_id" value="<?php echo $article->id; ?>" />
<div class='emoji-reactions' data-article-id="<?php echo $article->id; ?>"></div>

<hr />

<h3 class="text-center my-3">Geschiedenis</h3>
<?php
$ids = array_keys($article_changes);
$diff_renderer = RendererFactory::make('SideBySide', $rendererOptions = [
    'language' => [
        'old_version' => 'Oud',
        'new_version' => 'Nieuw',
        'differences' => 'Verschillen',
    ],
    'lineNumbers' => false,
]);
for ($i = 0; $i < count($ids); ++$i) {
    echo ViewRenderer::render_view('partial.article_change_entry', [
        'article_change' => $article_changes[$ids[$i]],
        'previous_article_change' => $i + 1 < count($ids) ? $article_changes[$ids[$i + 1]] : null,
        'diff_renderer' => $diff_renderer,
    ]);
}
?>
<script src='assets/js/copy.js'></script>
