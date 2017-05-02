<?php 
// logout.php
session_start();

unset($_SESSION['identity']);
header('Location: index.php');
exit;