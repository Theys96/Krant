<?php

use App\Model\ArticleChange;
use Jfcherng\Diff\Differ;
use Jfcherng\Diff\Renderer\AbstractRenderer;

/**
 * @var ArticleChange      $article_change;
 * @var ArticleChange|null $previous_article_change;
 * @var AbstractRenderer   $diff_renderer
 */
$diff_split_pattern = "/(?<=[.?])\s/ui";

$updates = [];
if (null !== $previous_article_change) {
    if ($article_change->changed_title !== $previous_article_change->changed_title) {
        $updates[] = sprintf(
            'De titel is aangepast van <b>%s</b> naar <b>%s</b>.<br />',
            $previous_article_change->changed_title,
            $article_change->changed_title
        );
    }
    if ($article_change->changed_category_id !== $previous_article_change->changed_category_id) {
        $updates[] = sprintf(
            'De categorie is aangepast van %s naar %s.<br />',
            null === $previous_article_change->changed_category ? '<i>geen categorie</i>' : '<b>'.$previous_article_change->changed_category->name.'</b>',
            null === $article_change->changed_category ? '<i>geen categorie</i>' : '<b>'.$article_change->changed_category->name.'</b>'
        );
    }
    if ($article_change->changed_ready !== $previous_article_change->changed_ready) {
        $updates[] = sprintf(
            'Het stukje is gemarkeerd als <b>%s</b>.<br />',
            $article_change->changed_ready ? 'klaar' : 'niet klaar'
        );
    }
}

if (null === $previous_article_change || $article_change->changed_contents !== $previous_article_change->changed_contents) {
    $differ = new Differ(
        preg_split($diff_split_pattern, null === $previous_article_change ? '' : $previous_article_change->changed_contents),
        preg_split($diff_split_pattern, $article_change->changed_contents),
        ['context' => Differ::CONTEXT_ALL]
    );
    $updates[] = '<hr />Tekst'.$diff_renderer->render($differ);
}

if (null === $previous_article_change || $article_change->changed_context !== $previous_article_change->changed_context) {
    $differ = new Differ(
        preg_split($diff_split_pattern, null === $previous_article_change ? '' : $previous_article_change->changed_context),
        preg_split($diff_split_pattern, $article_change->changed_context),
        ['context' => Differ::CONTEXT_ALL]
    );
    $updates[] = '<hr />Context'.$diff_renderer->render($differ);
}
?>

<?php
if (!empty($updates) || ArticleChange::CHANGE_TYPE_EDIT !== $article_change->update_type_id) {
    ?>
<div class="card my-2">
    <div class="card-header">
        <span class="float-right small">
          <?php echo $article_change->timestamp->format('d-m-\'y H:i:s'); ?>
        </span>
        <?php
            echo "<a class='font-weight-normal' data-toggle='collapse' href='#collapsedChange-".$article_change->id."' role='button' aria-expanded='false' aria-controls='collapsedChange-".$article_change->id."'>".
                sprintf($article_change->update_type_description, '<b>'.htmlspecialchars($article_change->user->username).'</b>').'</a>';
    ?>
    </div>
    <div class='collapse' id='collapsedChange-<?php echo $article_change->id; ?>'>
    <?php
    if (!empty($updates)) {
        echo "<div class='card-body'>".PHP_EOL;
        echo implode(PHP_EOL, $updates).PHP_EOL;
        echo '</div>'.PHP_EOL;
    }
    ?>
    </div>
</div>
<?php
}
?>