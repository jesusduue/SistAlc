<?php
// layouts/proteger_vista.php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['nom_usu']) || !isset($_SESSION['nom_rol'])) {
    header('Location: ../../index.php');
    exit;
}

$nombre_usuario = $_SESSION['nom_usu'];
$nombre_rol = $_SESSION['nom_rol'];

function restringirAcceso($rolRequerido) {
    global $nombre_rol;
    if ($nombre_rol !== $rolRequerido) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    icon: "error",
                    title: "Acceso denegado",
                    text: "No tienes permisos para acceder a esta vista.",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: "Volver al inicio"
                }).then(() => {
                    window.location.href = "../dashboard.php";
                });
            });
        </script>';
        exit;
    }
}
// Puedes llamar a esta función en cada vista que necesite protección
// restringirAcceso('Administrador'); // Por ejemplo, para restringir a administradores 
?>