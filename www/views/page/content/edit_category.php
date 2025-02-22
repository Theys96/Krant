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

    <input class='btn btn-primary' type='submit' value='Opslaan'/>
    <a class='btn btn-info' href='?action=categories'>Terug</a>
    <a class='btn btn-danger float-right' href='?action=categories&remove_category=<?php echo $category->id; ?>'>Verwijderen</a><br/>
</form>