<?php
include './library/configServer.php';
include './library/consulSQL.php';
include './inc/link.php';

function displayMessage($message) {
    echo '<p class="text-center lead">' . $message . '</p>';
}

function displayPaymentModal() {
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1">
                <p class="text-center lead">Selecciona un metodo de pago</p>
                <img class="img-responsive center-all-contens" src="assets/img/credit-card.png">
                <p class="text-center">
                    <button class="btn btn-lg btn-raised btn-success btn-block" data-toggle="modal" data-target="#PagoModalTran">Transaccion Bancaria</button>
                </p>
            </div>
        </div>
    </div>
    <?php
}

function displayOrderTable($consultaC) {
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Envío</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($rw = mysqli_fetch_array($consultaC, MYSQLI_ASSOC)) {
                            ?>
                            <tr>
                                <td><?php echo $rw['Fecha']; ?></td>
                                <td>$<?php echo $rw['TotalPagar']; ?></td>
                                <td>
                                    <?php
                                    switch ($rw['Estado']) {
                                        case 'Enviado':
                                            echo "En camino";
                                            break;
                                        case 'Pendiente':
                                            echo "En espera";
                                            break;
                                        case 'Entregado':
                                            echo "Entregado";
                                            break;
                                        default:
                                            echo "Sin información";
                                            break;
                                    }
                                    ?>
                                </td>
                                <td><?php echo $rw['TipoEnvio']; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

function displayBankInfoForm($datBank) {
    ?>
    <p>Por favor haga el deposito en la siguiente cuenta de banco e ingrese el numero de deposito que se le proporciono.</p><br>
    <p>
        <strong>Nombre del banco:</strong> <?php echo $datBank['NombreBanco']; ?><br>
        <strong>Numero de cuenta:</strong> <?php echo $datBank['NumeroCuenta']; ?><br>
        <strong>Nombre del beneficiario:</strong> <?php echo $datBank['NombreBeneficiario']; ?><br>
        <strong>Tipo de cuenta:</strong> <?php echo $datBank['TipoCuenta']; ?><br><br>
    </p>
    <?php
}

function displayPaymentForm($userType, $userNIT = '') {
    ?>
    <div class="form-group">
        <label>Numero de deposito</label>
        <input class="form-control" type="text" name="NumDepo" placeholder="Numero de deposito" maxlength="50" required="">
    </div>
    <div class="form-group">
        <span>Tipo De Envio</span>
        <select class="form-control" name="tipo-envio" data-toggle="tooltip" data-placement="top" title="Elige El Tipo De Envio">
            <option value="" disabled="" selected="">Selecciona una opción</option>
            <option value="Recoger Por Tienda">Recoger Por Tienda</option>
            <option value="Envio Por Currier">Envio Gratis</option> 
        </select>
    </div>
    <?php if ($userType == "Admin") { ?>
    <div class="form-group">
        <label>DNI del cliente</label>
        <input class="form-control" type="text" name="Cedclien" placeholder="DNI del cliente" maxlength="15" required="">
    </div>
    <?php } else { ?>
    <input type="hidden" name="Cedclien" value="<?php echo $userNIT; ?>">
    <?php } ?>
    <div class="form-group">
        <input type="file" name="comprobante">
        <div class="input-group">
            <input type="text" readonly="" class="form-control" placeholder="Seleccione la imagen del comprobante...">
            <span class="input-group-btn input-group-sm">
                <button type="button" class="btn btn-fab btn-fab-mini">
                    <i class="fa fa-file-image-o" aria-hidden="true"></i>
                </button>
            </span>
        </div>
        <p class="help-block"><small>Tipos de archivos admitidos, imágenes .jpg y .png. Máximo 5 MB</small></p>
    </div>
    <?php
}

session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pedidos</title>
</head>
<body id="container-page-index">
    <?php include './inc/navbar.php'; ?>

    <section id="container-pedido">
        <div class="container">
            <div class="page-header">
                <h1>PEDIDOS <small class="tittles-pages-logo"></small></h1>
            </div>
            <br><br><br>
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                    <?php
                    if ($_SESSION['UserType'] == "Admin" || $_SESSION['UserType'] == "User") {
                        if (isset($_SESSION['carro'])) {
                            displayPaymentModal();
                        } else {
                            displayMessage("No tienes pedidos pendientes de pago");
                        }
                    } else {
                        displayMessage("Inicia sesión para realizar pedidos");
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
        if ($_SESSION['UserType'] == "User") {
            $consultaC = ejecutarSQL::consultar("SELECT * FROM venta WHERE NIT='" . $_SESSION['UserNIT'] . "'");
            ?>
            <div class="container" style="margin-top: 70px;">
                <div class="page-header">
                    <h1>Mis pedidos</h1>
                </div>
            </div>
            <?php
            if (mysqli_num_rows($consultaC) >= 1) {
                displayOrderTable($consultaC);
            } else {
                displayMessage("No tienes ningun pedido realizado");
            }
            mysqli_free_result($consultaC);
        }
        ?>

        <!-- Payment Modal -->
        <div class="modal fade" id="PagoModalTran" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <form class="modal-content FormCatElec" action="process/confirmcompra.php" method="POST" role="form" data-form="save">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Pago por transaccion bancaria</h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        $consult1 = ejecutarSQL::consultar("SELECT * FROM cuentabanco");
                        if (mysqli_num_rows($consult1) >= 1) {
                            $datBank = mysqli_fetch_array($consult1, MYSQLI_ASSOC);
                            displayBankInfoForm($datBank);
                            displayPaymentForm($_SESSION['UserType'], $_SESSION['UserNIT']);
                        } else {
                            displayMessage("Ocurrió un error: Parece ser que no se ha configurado las cuentas de banco");
                        }
                        mysqli_free_result($consult1);
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm btn-raised" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm btn-raised">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php include './inc/footer.php'; ?>
    <div class="ResForm"></div>
</body>
</html>
