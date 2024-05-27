<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

function sanitizeInput($input) {
    return consultasSQL::clean_string($input);
}

function checkUserExists($username) {
    $query = "SELECT * FROM cliente WHERE Nombre='$username'";
    $result = ejecutarSQL::consultar($query);
    return mysqli_num_rows($result) == 1;
}

function validatePasswords($oldPass, $newPass, $newPass2) {
    if ($newPass !== $newPass2) {
        echo '<script>swal("Ocurrió un error inesperado", "Las contraseñas que acaba de ingresar no coinciden", "error");</script>';
        exit();
    }
    return true;
}

function checkOldPassword($oldUser, $oldPass) {
    $hashedOldPass = md5($oldPass);
    $query = "SELECT * FROM cliente WHERE Nombre='$oldUser' AND Clave='$hashedOldPass'";
    $result = ejecutarSQL::consultar($query);
    return mysqli_num_rows($result) == 1;
}

function updateClientData($campos, $NIT) {
    if (consultasSQL::UpdateSQL("cliente", $campos, "NIT='$NIT'")) {
        $_SESSION['nombreUser'] = $_POST['clien-name'];
        echo '<script>
            swal({
                title: "Datos actualizados",
                text: "Tus datos han sido actualizados con éxito",
                type: "success",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    location.reload();
                } else {
                    location.reload();
                }
            });
        </script>';
    } else {
        echo '<script>swal("ERROR", "Ocurrió un error inesperado", "error");</script>';
    }
}

$NIT = sanitizeInput($_POST['clien-nit']);
$Nombre = sanitizeInput($_POST['clien-fullname']);
$Apellido = sanitizeInput($_POST['clien-lastname']);
$Telefono = sanitizeInput($_POST['clien-phone']);
$Email = sanitizeInput($_POST['clien-email']);
$Direccion = sanitizeInput($_POST['clien-dir']);

$oldUser = sanitizeInput($_POST['clien-old-name']);
$user = sanitizeInput($_POST['clien-name']);

$oldPass = sanitizeInput($_POST['clien-old-pass']);
$newPass = sanitizeInput($_POST['clien-new-pass']);
$newPass2 = sanitizeInput($_POST['clien-new-pass2']);

if ($oldUser !== $user && checkUserExists($user)) {
    echo '<script>swal("Ocurrió un error inesperado", "El nombre de usuario que ha ingresado ya se encuentra registrado en el sistema, por favor escriba otro e intente nuevamente", "error");</script>';
    exit();
}

if ($oldPass && $newPass && $newPass2) {
    validatePasswords($oldPass, $newPass, $newPass2);

    if (checkOldPassword($oldUser, $oldPass)) {
        $hashedNewPass = md5($newPass);
        $campos = "Nombre='$user',NombreCompleto='$Nombre',Apellido='$Apellido',Clave='$hashedNewPass',Direccion='$Direccion',Telefono='$Telefono',Email='$Email'";
    } else {
        echo '<script>swal("Ocurrió un error inesperado", "La contraseña actual no coincide con la que se encuentra registrada en el sistema", "error");</script>';
        exit();
    }
} else {
    $campos = "Nombre='$user',NombreCompleto='$Nombre',Apellido='$Apellido',Direccion='$Direccion',Telefono='$Telefono',Email='$Email'";
}

updateClientData($campos, $NIT);
?>
