<?php
use Model\Article;
use Model\Category;
use Model\User;
use Util\Config;

/**
 * @var Category[] $categories
 * @var string $username
 * @var Article|null $article
 * @var string $title
 * @var bool $check_mode
 */

function printButtons($chars): void
{
    foreach ($chars as $char) {
        echo "<input type='button' class='px-4 btn btn-secondary' value='" . $char . "' onclick='insertChar(this)' />";
    }
}

$article_title = $article?->title;
$category_id = $article?->category?->id;
$contents = $article?->contents;
$context = $article?->context;
$ready = $article?->ready;
$mail = Config::MAIL;
?>
<h2><?php echo $title; ?></h2>

<form method='post' onSubmit='return Draft.plaats(this)'>
    <input type='hidden' name='draftid' id='draftid'/>
    <?php
    if ($article !== null) {
        echo "<input type='hidden' name='article_id' id='article_id' value='" . $article->id . "'/>";
    }
?>

    <div class='form-group'>
        <label for='title'>Titel</label>
        <input type='text' class='form-control input' name='title' id='title' value='<?php echo $article_title; ?>'/>
    </div>

    <div class='form-group'>
        <label for='user'>Auteur</label>
	<div class='form-control input'>
	<?php echo $article == null ? $username : $article->getAuthorsString(); ?>
	</div>
        <input type='hidden' class='form-control' id='user' value='<?php echo htmlspecialchars($username); ?>' disabled/>
    </div>

    <div class='form-group'>
        <label for='category'>Categorie</label>
        <select name='category' id='category' class='form-control'>
            <?php
        foreach ($categories as $category) {
            if ($category->id === $category_id) {
                $selected = true;
            } else {
                $selected = false;
            }
            echo "<option value='" . $category->id . ($selected ? "' selected" : "'") . ">" . htmlspecialchars($category->name) . "</option>\n";
        }
?>
        </select>
    </div>

    <div class='form-group'>
        <textarea id='text' class='form-control text input' name='text'><?php echo $contents; ?></textarea>
        <small class='float-right' id='charcount'></small>
    </div>

    <div class="btn-group my-1" role="group" aria-label="Basic example">
        <?php
        printButtons(array('&euml;', '&eacute;', '&egrave'));
?>
    </div>
    <div class="btn-group my-1" role="group" aria-label="Basic example">
        <?php
printButtons(array('&iuml;', '&auml;', '&ouml;', '&uuml;'));
?>
    </div>

    <div class='form-group'>
	<label for='context'>Context</label>
        <textarea id='context' class='form-control text input' name='context' placeholder='Schrijf hier een toelichting op het stukje als dat handig is. Dit komt niet in de krant.'><?php echo $context; ?></textarea>
    </div>

    <div class='mt-3 form-group'>
        <label class="custom-control custom-checkbox">
            <input type='checkbox' name='done' value='1' class="custom-control-input" <?php echo $ready === true ? ' checked' : ''; ?>/>
            <span class="custom-control-indicator"></span>
            <span class="custom-control-description">Dit stukje is klaar</span>
        </label>
    </div>

<?php
if ($check_mode) {
    echo "<input class='btn btn-primary' type='submit' value='Nagekeken' /> ";
    echo "<a class='btn btn-secondary' href='?action=list'>Niet nagekeken</a>";
} else {
    echo "<input class='btn btn-primary' type='submit' value='Opslaan'/> ";
    if ($mail != null) {
        echo "<button class='btn btn-secondary' type='button' id='mailbtn' value='$mail' onclick='sendMail()'>Mail Foto's</button>";
    }
}
if ($article !== null) {
    echo " <input class='btn btn-dark' id='emoji-button' type='button' value='😃' /> ";
    echo "<hr /><div id='emoji-reactions'></div>";
    echo "<div class='pop-up-bg'><emoji-picker class=\"emoji-pop-up light\"></emoji-picker></div>";
}
?>
    <span id='info'></span>
</form>
<script src='assets/js/draft.js'></script>
<script src='assets/js/editor.js'></script>
<script src='assets/js/emoji-react.js'></script>
<script>
    $(function () {
        Draft.init('.input');
        charCounter("#text", "#charcount");
    });
</script>
