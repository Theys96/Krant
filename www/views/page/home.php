<?php

/**
 * @var string $username
 * @var int    $role
 * @var string $errors
 * @var string $content
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="?action=list"><?php echo $username; ?></a>
    <button
            class="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarsExampleDefault"
            aria-controls="navbarsExampleDefault"
            aria-expanded="false"
            aria-label="Toggle navigation"
    >
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="?action=list">Stukjes</a>
            </li>
            <?php if (1 == $role || 3 == $role) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="?action=create">Schrijf</a>
                </li>
            <?php } ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="?action=admin" id="dropdown01" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">Beheer</a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                    <a class="dropdown-item" href="?action=overview">Overzicht</a>
                    <a class="dropdown-item" href="?action=categories">Categorieën</a>
                    <a class="dropdown-item" href="?action=drafts">Drafts</a>
                    <a class="dropdown-item" href="?action=bin">Prullenbak</a>
                    <a class="dropdown-item" href="?action=placed">Geplaatst</a>
                    <?php if (3 == $role) { ?>
                        <a class="dropdown-item" href="?action=editions">Edities</a>
                        <a class="dropdown-item" href="?action=users">Gebruikers</a>
                        <a class="dropdown-item" href="?action=feedbacklist">Feedback</a>
                        <a class="dropdown-item" href="?action=configuratie">Configuratie</a>
                    <?php } ?>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="?action=schrijfregels" target="_blank">Schrijfregels</a></li>
            <li class="nav-item"><a class="nav-link" href="?action=feedback">Feedback</a></li>
            <li class="nav-item"><a class="nav-link" href="?action=logout">Uitloggen</a></li>
        </ul>
    </div>
</nav>

<div class='jumbotron jumbotron-fluid mb-1' id='body'>
    <div id="content-container">
        <?php echo $errors; ?>
        <?php echo $content; ?>
    </div>
</div>
