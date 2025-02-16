<?php

use Model\User;

/**
 * @var User $user
 * @var User[] $users
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

    <div class='form-group'>
        <label for='alt_css'>Style</label>
	    <select name='edit_alt_css' id='alt_css' class='form-control'>
            <option value='0' <?php echo $user->alt_css === 0 ? 'selected' : ''; ?>>Normaal</option>
            <option value='1' <?php echo $user->alt_css === 1 ? 'selected' : ''; ?>>Comic Sans</option>
            <option value='2' <?php echo $user->alt_css === 2 ? 'selected' : ''; ?>>Roze</option>
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

    <input class='btn btn-primary' type='submit' value='Opslaan'/>
    <a class='btn btn-info' href='?action=users'>Terug</a>
</form>
<br>
<h2>Gebruiker mergen</h2>
<form method='post' action="?action=users&merge_user=<?php echo $user->id; ?>">
    <div class='form-group mt-3'>
        <label for='user2'>Welke gebruiker moet deze gebruiker worden</label>
        <select class='form-control' id='user2' name='user2' required>
        <option disabled selected hidden value=''>Selecteer een gebruiker</option>
        <?php
        foreach ($users as $user2) {
            if ($user != $user2) {
                echo "<option value='$user2->id'>$user2->username</option>";
            }
        }
?>
        </select>
    </div>
    <input class='btn btn-primary' type='submit' value='Merge'/>
    <a class='btn btn-info' href='?action=users'>Terug</a>
</form>