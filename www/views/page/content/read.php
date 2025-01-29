<?php

use Jfcherng\Diff\Factory\RendererFactory;
use Model\Article;
use Model\ArticleChange;
use Model\User;
use Util\ViewRenderer;

/**
 * @var Article $article
 * @var ArticleChange[] $article_changes
 * @var int $role
 * @var string $source
 */

$checkers = array_map(
    static function (User $author): string {
        return $author->username;
    },
    $article->checkers
);
$authors = $article->getAuthorsString();
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
<div class='row'>
    <div class='col-sm-12 tekst'><?php echo nl2br(htmlspecialchars($article->contents)); ?></div>
</div>
<?php
if ($article->context != "") {
    echo "<div class='row'>";
    echo "<div class='col-sm-12'><b>Context</b></div>";
    echo "<div class='col-sm-12 text-grey'>" . nl2br(htmlspecialchars($article->context)) . "</div>";
    echo "</div>";
} ?>
<div class='row'>
    <div class='col-sm-4'><b>Klaar</b></div>
    <div class='col-sm-8'><?php echo ($article->ready == 1) ? 'Ja' : 'Nee'; ?></div>
</div>

<center>
    <?php if ($role === 3 && $article->status === Article::STATUS_OPEN && $article->ready): ?>
        <form class="d-inline" method="post" action="?action=list&place_article=<?php echo $_GET['stukje']; ?>">
            <input class='btn btn-primary my-2 px-5' type='submit' value='Plaats'/>
        </form>
    <?php endif; ?>
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
    'language' => 'dut',
    'lineNumbers' => false,
]);
for ($i = 0; $i < count($ids); $i++) {
    echo ViewRenderer::render_view('partial.article_change_entry', [
        'article_change' => $article_changes[$ids[$i]],
        'previous_article_change' => $i + 1 < count($ids) ? $article_changes[$ids[$i + 1]] : null,
        'diff_renderer' => $diff_renderer
    ]);
}
?>
