<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

// Limpiar y obtener los datos del formulario
$nombre = consultasSQL::clean_string($_POST['nombre-login']);
$clave = consultasSQL::clean_string(md5($_POST['clave-login']));
$radio = consultasSQL::clean_string($_POST['optionsRadios']);

// Verificar que los campos no estén vacíos
if (!empty($nombre) && !empty($clave)) {
    // Verificar el tipo de usuario seleccionado
    if ($radio == "option2") {
        // Verificar las credenciales del administrador
        $verAdmin = ejecutarSQL::consultar("SELECT * FROM administrador WHERE Nombre='$nombre' AND Clave='$clave'");
        if (mysqli_num_rows($verAdmin) > 0) {
            $filaU = mysqli_fetch_array($verAdmin, MYSQLI_ASSOC);
            $_SESSION['nombreAdmin'] = $nombre;
            $_SESSION['claveAdmin'] = $clave;
            $_SESSION['UserType'] = "Admin";
            $_SESSION['adminID'] = $filaU['id'];
            echo '<script> location.href="index.php"; </script>';
        } else {
            echo 'Error: nombre o contraseña inválido';
        }
        mysqli_free_result($verAdmin);
    } elseif ($radio == "option1") {
        // Verificar las credenciales del cliente
        $verUser = ejecutarSQL::consultar("SELECT * FROM cliente WHERE Nombre='$nombre' AND Clave='$clave'");
        if (mysqli_num_rows($verUser) > 0) {
            $filaU = mysqli_fetch_array($verUser, MYSQLI_ASSOC);
            $_SESSION['nombreUser'] = $nombre;
            $_SESSION['claveUser'] = $clave;
            $_SESSION['UserType'] = "User";
            $_SESSION['UserNIT'] = $filaU['NIT'];
            echo '<script> location.href="index.php"; </script>';
        } else {
            echo 'Error: nombre o contraseña inválido';
        }
        mysqli_free_result($verUser);
    } else {
        echo 'Error: opción de usuario no válida';
    }
} else {
    echo 'Error: campo vacío. Intente nuevamente.';
}
?>
