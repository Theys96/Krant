<?php
/**
 * @var string $schrijfregels
 * */

/**
 * @param string $schrijfregels
 * @return void
 */
function printSchrijfregels(string $schrijfregels): void{
    $regels = explode("\n", str_replace("- ", "", $schrijfregels));
    $double = False;
    echo "<ul>";
    foreach ($regels as $regel) {
        if(ltrim($regel) == $regel) {
            if ($double) {
                echo "</ul>";
                $double = False;
            }
            echo "<li>$regel</li>";
        } else {
            if (!$double) {
                echo "<ul>";
                $double = True;
            }
            echo "<li>" . ltrim($regel) . "</li>";
        }
    }
    echo "</ul>";
}
?>
<h2>Schrijfregels</h2>
<div class='schrijfregels'>
<?php printSchrijfregels($schrijfregels) ?>
</div>
