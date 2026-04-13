<?php

use App\Model\Category;

/**
 * @var Category $category
 */
?>
<h2>Categorie aanpassen</h2>

<form method='post' action="?action=categories&edit_category=<?php echo $category->id; ?>">
    <div class='form-group'>
        <label for='name'>Naam</label>
        <input type='text' class='form-control input' name='edit_name' id='name'
               value='<?php echo $category->name; ?>'/>
    </div>

    <div class='form-group'>
        <label for='description'>Beschrijving</label>
        <input type='text' class='form-control input' name='edit_description' id='description'
               value='<?php echo $category->description; ?>'/>
    </div>

    <p class='mb-0'>Aantal blokjes in het overzicht voor:</p>
    <div class='d-flex justify-content-between'>
	    <div class='form-group mr-1'>
		    <label for='edit_article_number'>Stukjes</label>
		    <input type='number' id='edit_article_number' class='form-control' name='edit_article_number' required  min='0' value='<?php echo $category->article_number; ?>'></input>
	    </div>
	    <div class='form-group mr-1'>
		    <label for='edit_picture_number'>Foto's</label>
		    <input type='number' id='edit_picture_number' class='form-control' name='edit_picture_number' required  min='0' value='<?php echo $category->picture_number; ?>'></input>
	    </div>
	    <div class='form-group'>
		    <label for='edit_wjd_number'>Wist je Datjes</label>
		    <input type='number' id='edit_wjd_number' class='form-control' name='edit_wjd_number' required min='0' value='<?php echo $category->wjd_number; ?>'></input>
	    </div>
    </div>

    <input class='btn btn-primary' type='submit' value='Opslaan'/>
    <a class='btn btn-info' href='?action=categories'>Terug</a>
    <a class='btn btn-danger float-right' href='?action=categories&remove_category=<?php echo $category->id; ?>'>Verwijderen</a><br/>
</form>