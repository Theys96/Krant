<?php

use App\Model\Log;

/**
 * @var Log[] $feedback
 */
?>
<h2>Feedback</h2>

<div class='px-3 mx-auto my-5'>
    <?php
    $row = true;
foreach ($feedback as $feedback_item) {
    $color = $row ? 'table-color1' : 'table-color2';
    $row = !$row;

    echo "<div class='row ".$color."'>\n";
    echo "<div class='col-3'>".$feedback_item->timestamp->format('Y-m-d H:i:s').'</div>';
    echo "<div class='col-2'><b>".htmlspecialchars($feedback_item->user?->username).'</b></div>';
    echo "<div class='col-7'>".htmlspecialchars($feedback_item->message).'</div>';
    echo "</div>\n";
}
?>
</div>
