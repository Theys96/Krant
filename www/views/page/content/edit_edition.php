<?php

use Model\Edition;

/**
 * @var Edition $edition
 */
?>
<h2>Editie aanpassen</h2>

<form method='post' action="?action=editions&edit_edition=<?php echo $edition->id; ?>">
    <div class='form-group'>
        <label for='name'>Naam</label>
        <input type='text' class='form-control input' name='edit_name' id='name'
               value='<?php echo $edition->name; ?>'/>
    </div>

    <div class='form-group'>
        <label for='description'>Beschrijving</label>
        <input type='text' class='form-control input' name='edit_description' id='description'
               value='<?php echo $edition->description; ?>'/>
    </div>

    <input class='btn btn-primary' type='submit' value='Opslaan'/>
    <a class='btn btn-info' href='?action=editions'>Terug</a>
</form>