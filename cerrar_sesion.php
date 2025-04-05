<?php
// Romper la sesion al ser redirigido a esta pagina 
session_start();
session_unset();
session_destroy();
header("Location: index.php");
?>