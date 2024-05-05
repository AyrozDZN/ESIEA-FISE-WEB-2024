<?php
include_once 'functions/controller.php';
include_once 'templates/parts/header.php';
$controller = new Controller();
$controller->handleRequest();
include_once 'templates/parts/footer.php';
?>