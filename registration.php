<!DOCTYPE html>
<html lang="es">
<head>
    <title>Registro</title>
    <?php include './inc/link.php'; ?>
    <link rel="stylesheet" href="./assets/css/registration.css">
</head>
<body id="container-page-registration">
    <?php include './inc/navbar.php'; ?>
    <section id="form-registration">
        <div class="container">
            <div class="page-header">
                <h1>REGISTRO <small class="tittles-pages-logo"></small></h1>
            </div>
            <div class="row">
                <div class="col-sm-5 text-center">
                    <figure>
                        <img src="./assets/img/img-registration.png" alt="store" class="img-responsive">
                    </figure>
                </div>
                <div class="col-sm-7">
                    <div id="container-form">
                        <p class="text-center lead">Registro de Clientes</p>
                        <form class="FormCatElec" action="process/regclien.php" role="form" method="POST" data-form="save">
                            <div class="container-fluid">
                                <div class="row">
                                    <?php
                                    function createInput($icon, $label, $type, $name, $pattern = "", $title, $maxlength, $additionalClasses = "") {
                                        echo '
                                        <div class="col-xs-12' . ($additionalClasses ? ' ' . $additionalClasses : '') . '">
                                            <div class="form-group label-floating">
                                                <label class="control-label"><i class="fa ' . $icon . '" aria-hidden="true"></i>&nbsp; ' . $label . '</label>
                                                <input class="form-control" type="' . $type . '" name="' . $name . '" ' . ($pattern ? 'pattern="' . $pattern . '" ' : '') . 'title="' . $title . '" maxlength="' . $maxlength . '" required>
                                            </div>
                                        </div>';
                                    }

                                    createInput('fa-address-card-o', 'Ingrese su número de cliente', 'text', 'clien-nit', '[0-9]{1,15}', 'Ingrese su número de Usuario. Solamente números', '15');
                                    createInput('fa-user', 'Ingrese sus nombres', 'text', 'clien-fullname', '[a-zA-Z ]{1,50}', 'Ingrese sus nombres (solamente letras)', '50', 'col-sm-6');
                                    createInput('fa-user', 'Ingrese sus apellidos', 'text', 'clien-lastname', '[a-zA-Z ]{1,50}', 'Ingrese sus apellidos (solamente letras)', '50', 'col-sm-6');
                                    createInput('fa-mobile', 'Ingrese su número telefónico', 'tel', 'clien-phone', '', 'Ingrese su número telefónico. Mínimo 8 dígitos máximo 15', '15', 'col-sm-6');
                                    createInput('fa-envelope-o', 'Ingrese su Email', 'email', 'clien-email', '', 'Ingrese la dirección de su Email', '50', 'col-sm-6');
                                    createInput('fa-home', 'Ingrese su dirección', 'text', 'clien-dir', '', 'Ingrese la dirección en la que reside actualmente', '100');
                                    ?>
                                    <div class="col-xs-12">
                                        <legend><i class="fa fa-lock"></i> &nbsp; Datos de la cuenta</legend>
                                    </div>
                                    <?php
                                    createInput('fa-user-circle-o', 'Ingrese su nombre de usuario', 'text', 'clien-name', '[a-zA-Z0-9]{1,9}', 'Ingrese su nombre de usuario. Máximo 9 caracteres (solamente letras y números sin espacios)', '9');
                                    createInput('fa-lock', 'Introduzca una contraseña', 'password', 'clien-pass', '', 'Defina una contraseña para iniciar sesión', '');
                                    createInput('fa-lock', 'Repita la contraseña', 'password', 'clien-pass2', '', 'Repita la contraseña', '');
                                    ?>
                                </div>
                            </div>
                            <p><button type="submit" class="btn btn-primary btn-block btn-raised">Registrarse</button></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include './inc/footer.php'; ?>
    <script src="./assets/js/registration.js"></script>
</body>
</html>
