<?php
session_start();
session_unset();
session_destroy();
header("Location: /arepas-erp2/public/login.php");
exit;
