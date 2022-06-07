<?php
if (!$page_loaded) {return;}

if (isset($_POST['adduser']))
	{
	$Users->addUser($_POST['adduser'], $_POST['perm_level'], $Error);
	}

if (isset($_GET['deluser']))
	{
	$Users->delUser($_GET['deluser'], $Error);
	}

$users = $Users->getUsers(null, $Error);

$Error->printAll();
?>

<h2>Gebruikers</h2>
<h3>Gebruiker toevoegen</h3>
<form method='post' action='index.php?action=users'>
<div class='form-group'>
	<label for='name'>Naam</label>
	<input type='text' class='form-control' name='adduser' id='name' />
</div>
<div class='form-group'>
	<label for='perm_level'>Permissie</label>
	<select name='perm_level' class='form-control' id='perm_level'>
		<option value='1'>1 (<?php echo $roles[1] ?>)</option>
		<option value='2'>2 (<?php echo $roles[2] ?>)</option>
		<option value='3'>3 (<?php echo $roles[3] ?>)</option>
	</select>
</div>
<input class='btn btn-primary' type='submit' value='Toevoegen' />
</form>
<br />

<div class='px-3 mx-auto'>
<?php
$row = true;
foreach ($users as $user)
	{
	$color = $row ? '#AAAAAA' : '#DDDDDD';
	$row = !$row;
	
	echo "<div style='background-color: " . $color . "' class='row'>\n";
	echo "<div class='col-5'><b>" . htmlspecialchars($user['username']) . "</b></div>";
	echo "<div class='col-5'>" . $roles[$user['perm_level']] . "</div>";
	echo "<div class='col-2'><a class='text-danger'  href='?action=users&deluser=" . $user['id'] . "'>X</a></div>";
	echo "</div>\n";
	}
?>
</div>
