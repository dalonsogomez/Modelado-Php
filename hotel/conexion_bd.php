<?php
    $conex = mysqli_connect('localhost', 'root', '') or die (mysqli_error($conex));
    mysqli_select_db($conex, "hotel") or die (mysqli_error($conex));
?>
