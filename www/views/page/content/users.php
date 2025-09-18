<?php

use App\Model\User;

/**
 * @param User[] $users
 */
function printUserList(array $users, int $role, bool $active): void
{
    $row = true;
    foreach ($users as $user) {
        $color = $row ? 'table-color1' : 'table-color2';
        $row = !$row;

        echo "<div class='row ".$color."'>\n";
        echo "<div class='col-sm-8 d-flex justify-content-between py-2 pl-3'><span><b>".htmlspecialchars($user->username).'</b></span>';
        echo '<span>'.$user->getPermLevelName().'</span>';
        echo '</div>';
        echo "<div class='col-sm-4 d-flex justify-content-end'>";
        if (3 === $role) {
            echo "<a class='p-1 btn btn-sm btn-primary mr-1 my-1' href='?action=edit_user&user=".$user->id."'>Aanpassen</a>";
            if ($active) {
                echo "<a class='p-1 btn btn-sm btn-danger mr-1 my-1' href='?action=users&deactivate=".$user->id."'>Deactiveren</a>";
            } else {
                echo "<a class='p-1 btn btn-sm btn-success mr-1 my-1' href='?action=users&activate=".$user->id."'>Activeren</a>";
            }
        }
        echo '</div>';
        echo "</div>\n";
    }
}

/**
 * @var User[] $active_users
 * @var User[] $archived_users
 * @var int    $role
 */
?>
<h2>Gebruikers</h2>

<div class='my-5'>
<?php
printUserList($active_users, $role, true);
?>
</div>

<?php
if (3 == $role) {
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
}
?>

<h3 class="mt-5">Gedeactiveerde gebruikers</h3>

<div>
<?php
printUserList($archived_users, $role, false);
?>
</div>