<?php

// работает только так:
session_start();
$_SESSION = [];
session_destroy();
header("Location: /");


?>