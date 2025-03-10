<?php
require('../../conexion.php');
require_once('funciones.php');
session_start();

$response = ["Usuario" => "Usuario Desconocido"];

if (isset($_SESSION['User_ID'])) {
    $response["Usuario"] = hallarNombre($_SESSION['User_ID']);
} elseif (!empty($_GET['token'])) {
    $response["Usuario"] = hallarNombre($_GET['token']);
}

header('Content-Type: application/json');
echo json_encode($response);
?>
