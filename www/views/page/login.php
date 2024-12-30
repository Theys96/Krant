<?php
use Model\User;

/**
 * @var User[] $users
 * @var array $passwords
 * @var string $errors
 */
?>
<script>
$(function() {
	$('.step2').hide();
	$('#password').hide();
	$('#submit').hide();
	});

function unsetRole()
	{
	$('.step2').hide();
	$('#password').hide();
	$('#submit').hide();
	$('#navDiv').fadeIn();
	}

function setRole(setRole)
	{
	$('.step2').hide();
	$('#navDiv').hide();
	$('#submit').fadeIn();
	switch(setRole)
		{
		case 1:
			role = 'schrijven';
			$('#role1').fadeIn();
			$('[name="role"]').val('1');
			<?php
            if (isset($passwords[1])) {
                echo "$('#password').fadeIn();";
            }
?>
			
		break;
		case 2:
			role = 'nakijken';
			$('#role2').fadeIn();
			$('[name="role"]').val('2');
			<?php
if (isset($passwords[2])) {
    echo "$('#password').fadeIn();";
}
?>
			
		break;
		case 3:
			role = 'beheren';
			$('#role3').fadeIn();
			$('[name="role"]').val('3');
			<?php
if (isset($passwords[3])) {
    echo "$('#password').fadeIn();";
}
?>
			
		break;
		}
	}
</script>

<div id='body' class='jumbotron'>
    <center>
        <form method='post' action='index.php'>
            <div id='navDiv'>
                <h1 class='mb-3'>Kies wat je gaat doen</h1>
                <div class='row'>
                    <div class='col-md-4'>
                        <a class='role m-2' onClick='setRole(1)' href='#'>Schrijven</a>
                    </div>
                    <div class='col-md-4'>
                        <a class='role m-2' onClick='setRole(2)' href='#'>Nakijken</a>
                    </div>
                    <div class='col-md-4'>
                        <a class='role m-2' onClick='setRole(3)' href='#'>Beheren</a>
                    </div>
                </div>
                <input type='hidden' name='role' value='none'>
            </div>

            <div class='step2 form-group' id='role1'>
                <h1>Schrijven</h1>
                <label for='user1'>Kies je naam:</label>
                <select name='user[1]' id='user1' class='username form-control w-50'>
                    <?php
        foreach ($users as $user) {
            if ($user->perm_level >= 1) {
                echo "<option value='" . $user->id . "'>" . $user->username . "</option>\n";
            }
        }
?>
                </select>
            </div>

            <div class='step2 form-group' id='role2'>
                <h1>Nakijken</h1>
                <label for='user2'>Kies je naam:</label>
                <select name='user[2]' id='user2' class='username form-control w-50'>
                    <?php
foreach ($users as $user) {
    if ($user->perm_level >= 2) {
        echo "<option value='" . $user->id . "'>" . $user->username . "</option>\n";
    }
}
?>
                </select>
            </div>

            <div class='step2 form-group' id='role3'>
                <h1>Beheren</h1>
                <label for='user3'>Kies je naam:</label>
                <select name='user[3]' id='user3' class='username form-control w-50'>
                    <?php
foreach ($users as $user) {
    if ($user->perm_level >= 3) {
        echo "<option value='" . $user->id . "'>" . $user->username . "</option>\n";
    }
}
?>
                </select>
            </div>

            <div id='password' class='form-group'>
                <p>Vul je wachtwoord in:</p>
                <input type='password' class='form-control w-50' name='password' />
            </div>

            <br />

            <div id='submit'>
                <input type='submit' class='btn btn-primary' value='Ok' />
                <p class='m-3'><a onClick='unsetRole()' href='#'>Terug</a></p>
            </div>
        </form>
    </center>
    <?php
    echo $errors;
?>
</div>
