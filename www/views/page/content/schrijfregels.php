<?php
function printSchrijfregels(string $schrijfregels): void
{
    echo '<ul>';
    $regels = explode("\n", str_replace('- ', '', $schrijfregels));
    $double = false;
    echo "<li>Schrijf de naam van groepen zoals in <a href='?action=groepen'>dit overzicht.</a></li>";
    foreach ($regels as $regel) {
        if (ltrim($regel) == $regel) {
            if ($double) {
                echo '</ul>';
                $double = false;
            }
            echo '<li>'.htmlspecialchars($regel).'</li>';
        } else {
            if (!$double) {
                echo '<ul>';
                $double = true;
            }
            echo '<li>'.htmlspecialchars(ltrim($regel)).'</li>';
        }
    }
    echo '</ul>';
}

/**
 * @var string $schrijfregels
 */
?>
<h2>Schrijfregels</h2>
<div class='schrijfregels'>
    <?php printSchrijfregels($schrijfregels); ?>
</div>
