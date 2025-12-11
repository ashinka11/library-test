<?php
    require("constants.php");
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME) or die("Connection failed: " . mysqli_connect_error());
    
    // Установка кодировки
    mysqli_set_charset($con, "utf8");
?>