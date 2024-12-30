<?php

use Model\User;

/**
 * @var User[] $users
 * @var int $role
 */
?>
<h2>Gebruikers</h2>

<div class='px-3 mx-auto my-5'>
    <?php
    $row = true;
foreach ($users as $user) {
    $color = $row ? '#AAAAAA' : '#DDDDDD';
    $row = !$row;

    echo "<div style='background-color: " . $color . "' class='row'>\n";
    echo "<div class='col-4 " . ($user->active ? '' : 'text-grey font-italic') . "'><b>" . htmlspecialchars($user->username) . "</b></div>";
    echo "<div class='col-6 " . ($user->active ? '' : 'text-grey font-italic') . "'>" . $user->getPermLevelName() . "</div>";
    echo "<div class='col-2'>";
    if ($role === 3) {
        echo "<a href='?action=edit_user&user=" . $user->id . "'>Aanpassen</a>";
    }
    echo "</div>";
    echo "</div>\n";
}
?>
</div>

<?php
if ($role == 3) :
    ?>
    <form method='post'>
        <h3>Gebruiker toevoegen</h3>
        <div class='form-group'>
            <label for='name'>Naam</label>
            <input id='name' name='new_name' class='form-control' type='text'/>
        </div>
        <div class='form-group'>
            <label for='perm_level'>Rol</label>
            <select name='new_perm_level' id='perm_level' class='form-control'>
                <option value='1'>Schrijver</option>
                <option value='2'>Nakijker</option>
                <option value='3'>Beheerder</option>
            </select>
        </div>
        <input type='submit' class='btn btn-primary' value='Toevoegen'/>
    </form>
<?php
endif;
?>
