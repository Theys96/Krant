<?php
use Model\Category;

/**
 * @var Category[] $categories
 */
?>
<h2>Categorieën</h2>
<div class='px-3 mx-auto'>
<?php
$row = true;
foreach ($categories as $category)
	{
	$color = $row ? '#AAAAAA' : '#DDDDDD';
	$row = !$row;

	echo "<div style='background-color: " . $color . "' class='row'>\n";
	echo "<div class='col-4'><b>" . htmlspecialchars($category->name) . "</b></div>";
	echo "<div class='col-6'>" . htmlspecialchars($category->description) . "</div>";
	echo "<div class='col-2'>";
	echo "</div>";
	echo "</div>\n";
	}
?>
</div>
