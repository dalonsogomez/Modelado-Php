<?php
    $conex = mysqli_connect('localhost', 'root', '') or die (mysqli_error($conex));
    mysqli_select_db($conex, "gimnasio") or die (mysqli_error($conex));
?>
