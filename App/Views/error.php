<?php
$title = "Error | " . $code;
include $base_page;
?>

<div class="d-flex flex-column justify-content-center align-items-center" style="min-height:80vh">
    <h2><?php echo $code . " | " . $message; ?></h2>
</div>
</body>
</html>
