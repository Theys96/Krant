<?php

use App\Model\Article;
use App\Model\Category;
use App\Model\User;

/**
 * @param array<int, string> $chars
 */
function printButtons(array $chars): void
{
    foreach ($chars as $char) {
        echo "<input type='button' class='px-4 btn btn-secondary' value='".$char."' onclick='insertChar(this)' />";
    }
}

/**
 * @var Category[]   $categories
 * @var string       $username
 * @var Article|null $article
 * @var string       $title
 * @var bool         $check_mode
 * @var string|null  $mail
 */
$liveDrafters = null == $article ? [] : User::getLiveDrafters($article->id, $username);
$article_title = $article?->title;
$category_id = $article?->category?->id;
$contents = $article?->contents;
$context = $article?->context;
$ready = $article?->ready;
$picture = $article?->picture;
$wjd = $article?->wjd;
$ignore_warning = isset($_GET['ignore_warning']) ? true : false;
$open = $ignore_warning || $liveDrafters == null;
?>
<div class="text-center mb-5">
<?php
if (!$open) {
    $names = implode(', ', array_column($liveDrafters, 'username'));
    $warning = htmlspecialchars($names).(count($liveDrafters) > 1 ? ' hebben ' : ' heeft ').'het stukje open.';
    echo "<div class='alert alert-danger' role='alert'><p>$warning</p>";
    echo '<p>Weet je zeker dat je het stukje nu ook wil bewerken? <br> Het tegelijkertijd bewerken van stukjes kan leiden tot het verlies van aanpassingen!</p>';
    echo "<a class='btn btn-danger mr-1' href='?action=".$_GET['action'].'&stukje='.$article->id."&ignore_warning=1'>Ja ik weet het zeker</a></div>";
}
?>
    <span id='info'></span>
</div>

<h2><?php echo $title; ?></h2>

<form method='post' onSubmit='return Draft.plaats(this)'>
    <input type='hidden' name='draftid' id='draftid'/>
    <?php
    if (null !== $article) {
        echo "<input type='hidden' name='article_id' id='article_id' value='".$article->id."'/>";
    }
?>

    <div class='form-group'>
        <label for='title'>Titel</label>
        <input type='text' <?php echo $open ? '' : 'disabled'; ?>  class='form-control input' name='title' id='title' value='<?php echo $article_title; ?>'/>
    </div>

    <div class='form-group'>
        <label for='user'>Auteur</label>
        <input type='text' class='form-control input' id='author' value='<?php echo null == $article ? $username : $article->getAuthorsString(); ?>' disabled/>
        <input type='hidden' class='form-control' id='user' value='<?php echo htmlspecialchars($username); ?>' disabled/>
    </div>

    <div class='form-group'>
        <label for='category'>Categorie</label>
        <select <?php echo $open ? '' : 'disabled'; ?> required name='category' id='category' class='form-control'>
            <option disabled hidden selected value=''>Kies een categorie</option>
            <?php
        foreach ($categories as $category) {
            if ($category->id === $category_id) {
                $selected = true;
            } else {
                $selected = false;
            }
            echo "<option value='".$category->id.($selected ? "' selected" : "'").'>'.htmlspecialchars($category->name).(strlen($category->description) > 0 ? (' - '.$category->description) : '')."</option>\n";
        }
?>
        </select>
    </div>

    <div class='form-group'>
        <textarea id='text' <?php echo $open ? '' : 'disabled'; ?>  class='form-control text input' name='text'><?php echo $contents; ?></textarea>
        <small class='float-right' id='charcount'></small>

        <div class="btn-group my-2" role="group" aria-label="Basic example">
            <?php
            if ($open) {
                printButtons(['&euml;', '&eacute;', '&egrave']);
            }
?>
        </div>
        <div class="btn-group my-1" role="group" aria-label="Basic example">
            <?php
            if ($open) {
                printButtons(['&iuml;', '&auml;', '&ouml;', '&uuml;']);
            }
?>
        </div>
    </div>

    <div class='form-group'>
	<label for='context'>Context</label>
        <textarea id='context' <?php echo $open ? '' : 'disabled'; ?> class='form-control text input' name='context' placeholder='Schrijf hier een toelichting op het stukje als dat handig is. Dit komt niet in de krant.'><?php echo $context; ?></textarea>
    </div>

    <div class='mt-3 form-group'>
        <div class="custom-control custom-checkbox">
            <input type='checkbox' <?php echo $open ? '' : 'disabled'; ?> name='done' value='1' id="done-checkbox" class="custom-control-input" <?php echo true === $ready ? ' checked' : ''; ?>/>
            <label class="custom-control-label" for="done-checkbox">Dit stukje is klaar</label>
        </div>
        <div class="custom-control custom-checkbox">
            <input type='checkbox' <?php echo $open ? '' : 'disabled'; ?>  name='picture' value='1' id="picture-checkbox" class="custom-control-input" <?php echo true === $picture ? ' checked' : ''; ?>/>
            <label class="custom-control-label" for="picture-checkbox">Dit stukje heeft een foto</label>
        </div>
        <div class="custom-control custom-checkbox">
            <input type='checkbox' <?php echo $open ? '' : 'disabled'; ?> name='wjd' value='1' id="wjd-checkbox" class="custom-control-input" <?php echo true === $wjd ? ' checked' : ''; ?>/>
            <label class="custom-control-label" for="wjd-checkbox">Dit zijn Wist je Datjes</label>
        </div>
    </div>

<?php
if ($check_mode) {
    if ($open) {
        echo "<input class='btn btn-primary' type='submit' value='Nagekeken' /> ";
    }
    echo "<a class='btn btn-secondary' href='?action=list'>Niet nagekeken</a>";
} else {
    if ($open) {
        echo "<input class='btn btn-primary' type='submit' value='Opslaan'/> ";
    }
    echo "<a class='btn btn-secondary mr-1' href='?action=list'>Niet opslaan</a>";
    if (null != $mail && $open) {
        echo "<button class='btn btn-secondary' type='button' id='mailbtn' value='$mail' onclick='sendMail()'>Mail Foto's</button>";
    }
}
if (null !== $article) {
    echo "<hr /><div class='emoji-reactions' data-article-id='".$article->id."'></div>";
}

?>
</form>
<script src='assets/js/draft.js'></script>
<script src='assets/js/editor.js'></script>
<script>
    $(function () {
        charCounter("#text", "#charcount");
    });
    <?php
    if ($open) {
        echo "$(function () {
        Draft.init('.input');
        });";
    }
?>
</script>
