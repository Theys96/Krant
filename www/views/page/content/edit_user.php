<?php

use Model\User;

/**
 * @var User $user
 */
?>
<h2>Gebruiker aanpassen</h2>

<form method='post' action="?action=users&edit_user=<?php echo $user->id; ?>">
    <div class='form-group'>
        <label for='name'>Naam</label>
        <input type='text' class='form-control input' name='edit_name' id='name'
               value='<?php echo $user->username; ?>'/>
    </div>

    <div class='form-group'>
        <label for='perm_level'>Rol</label>
        <select name='edit_perm_level' id='perm_level' class='form-control'>
            <option value='1' <?php echo $user->perm_level === 1 ? 'selected' : ''; ?>>Schrijver</option>
            <option value='2' <?php echo $user->perm_level === 2 ? 'selected' : ''; ?>>Nakijker</option>
            <option value='3' <?php echo $user->perm_level === 3 ? 'selected' : ''; ?>>Beheerder</option>
        </select>
    </div>

    <div class='mt-3 form-group'>
        <label class="custom-control custom-checkbox">
            <input type='checkbox' name='edit_active' value='1'
                   class="custom-control-input" <?php echo $user->active === true ? ' checked' : ''; ?>/>
            <span class="custom-control-indicator"></span>
            <span class="custom-control-description">Actief</span>
        </label>
    </div>

    <div class='form-group'>
        <label class="custom-control custom-checkbox">
            <input type='checkbox' name='edit_alt_css' value='1'
                   class="custom-control-input" <?php echo $user->alt_css === true ? ' checked' : ''; ?>/>
            <span class="custom-control-indicator"></span>
            <span class="custom-control-description">Comic Sans</span>
        </label>
    </div>

    <input class='btn btn-primary' type='submit' value='Opslaan'/>
    <a class='btn btn-info' href='?action=users'>Terug</a>
</form>