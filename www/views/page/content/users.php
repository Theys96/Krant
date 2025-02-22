<?php

use App\Model\User;

/**
 * @var User[] $active_users
 * @var User[] $archived_users
 * @var int $role
 */

/**
 * @param User[] $users
 * @param int $role
 * @param bool $active
 * @return void
 */
function printUserList(array $users, int $role, bool $active): void
{
    $row = true;
    foreach ($users as $user) {
        $color = $row ? '#AAAAAA' : '#DDDDDD';
        $row = !$row;

        echo "<div style='background-color: " . $color . "' class='row'>\n";
        echo "<div class='col-sm-8 d-flex justify-content-between py-2 pl-3'><span><b>" . htmlspecialchars($user->username) . "</b></span>";
        echo "<span>" . $user->getPermLevelName() . "</span>";
        echo "</div>";
        echo "<div class='col-sm-4 d-flex justify-content-end'>";
        if ($role === 3) {
            echo "<a class='p-1 btn btn-sm btn-primary mr-1 my-1' href='?action=edit_user&user=" . $user->id . "'>Aanpassen</a>";
        }
        if ($role === 3 && $active) {
            echo "<a class='p-1 btn btn-sm btn-danger mr-1 my-1' href='?action=users&deactivate=" . $user->id . "'>Deactiveren</a>";
        } elseif ($role === 3 && !$active) {
            echo "<a class='p-1 btn btn-sm btn-success mr-1 my-1' href='?action=users&activate=" . $user->id . "'>Activeren</a>";
        }
        echo "</div>";
        echo "</div>\n";
    }
}
?>
<h2>Gebruikers</h2>

<div class='my-5'>
<?php
printUserList($active_users, $role, true);
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

<h3 class="mt-5">Gedeactiveerde gebruikers</h3>

<div>
<?php
printUserList($archived_users, $role, false);
?>
</div>