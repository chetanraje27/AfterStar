<?php
session_start();
session_destroy();
header("Location: afterstar-website.html");
exit;
?>