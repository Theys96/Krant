<?php
use Model\Article;
use Model\Category;

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
$category_id = $article?->category->id;
$contents = $article?->contents;
$ready = $article?->ready;
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
        <input type='text' class='form-control' id='user' value='<?php echo htmlspecialchars($username); ?>' disabled/>
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
        echo "<a class='btn btn-secondary' href='?action=list'>Niet nagekeken</a><br />";
    } else {
        echo "<input class='btn btn-primary' type='submit' value='Opslaan'/><br/>";
    }
    ?>
    <span id='info'></span>
</form>
<script src='assets/js/draft.js'></script>
<script src='assets/js/editor.js'></script>
<script>
    $(function () {
        Draft.init('.input');
        charCounter("#text", "#charcount");
    });
</script>
