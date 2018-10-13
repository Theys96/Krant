	<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="?action=lijst">Krant</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="?action=lijst">Stukjes</a>
          </li>
          <?php if ($Session->role == 1 || $Session->role == 3) : ?>
          <li class="nav-item">
            <a class="nav-link" href="?action=schrijf">Schrijf</a>
          </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="?action=cats">Categorieën</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="?action=schrijfregels">Schrijfregels</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="?action=admin" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Beheer</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="?action=drafts">Drafts</a>
              <a class="dropdown-item" href="?action=bin">Prullenbak</a>
              <a class="dropdown-item" href="?action=archive">Geplaatst</a>
              <?php if ($Session->role == 3) : ?>
              <a class="dropdown-item" href="?action=users">Gebruikers</a>
              <a class="dropdown-item" href="?action=versionctrl">Versies</a>
              <a class="dropdown-item" href="?action=feedbacklist">Feedback</a>
              <?php endif; ?>
            </div>
          </li>
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="?action=feedback">Feedback</a></li>
        	<li class="nav-item"><a class="nav-link" href="?action=logout">Uitloggen</a></li>
        </ul>
      </div>
    </nav>

<div class='jumbotron' id='body'>
<?php
$action = (isset($_GET['action']) ? $_GET['action'] : 'lijst');
if (isset($action)) {
	if (isset($actions[$action])) {
		$page = $actions[$action];
		if (in_array($Session->role, $page['level'])) {
			/* Load page! */
			$page_loaded = true;
			foreach ($page['include'] as $include) {
				include $include;
			}
		} else {
			/* Niet het juiste level voor actie */
			$levels = $roles[$page['level'][0]];
			for ($i = 1; $i < count($page['level']); $i++) {
				$levels .= " of " . $roles[$page['level'][$i]];
			}
			$Error->throwError("Geen permissie. Log in als " . $levels . ".");
		}
	} else {
	/* Actie bestaat niet */
	$Error->throwError("Deze pagina bestaat niet.");
	}
}

$Error->printAll();
?>
</div>