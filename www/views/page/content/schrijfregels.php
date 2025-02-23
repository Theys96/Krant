<?php
/**
 * @param string $schrijfregels
 * @return void
 */
function printSchrijfregels(string $schrijfregels): void
{
    $regels = explode("\n", str_replace("- ", "", $schrijfregels));
    $double = false;
    echo "<ul>";
    foreach ($regels as $regel) {
        if (ltrim($regel) == $regel) {
            if ($double) {
                echo "</ul>";
                $double = false;
            }
            echo "<li>". htmlspecialchars($regel) . "</li>";
        } else {
            if (!$double) {
                echo "<ul>";
                $double = true;
            }
            echo "<li>" . htmlspecialchars(ltrim($regel)) . "</li>";
        }
    }
    echo "</ul>";
}

/**
 * @var string $schrijfregels
 * */
?>
<h2>Schrijfregels</h2>
<div class='schrijfregels'>
<?php printSchrijfregels($schrijfregels) ?>
</div>
