<?php
session_start(); 
include '../library/configServer.php';
include '../library/consulSQL.php';

// Limpieza de datos de entrada
$NumDepo = consultasSQL::clean_string($_POST['NumDepo']);
$tipoenvio = consultasSQL::clean_string($_POST['tipo-envio']);
$Cedclien = consultasSQL::clean_string($_POST['Cedclien']);
$comprobante = $_FILES['comprobante'];
$comprobanteDir = "../assets/comprobantes/";
$comprobanteMaxSize = 5120; // Tamaño máximo del archivo en KB

// Verificación de la existencia del cliente
$verdata = ejecutarSQL::consultar("SELECT * FROM cliente WHERE NIT='$Cedclien'");
if (mysqli_num_rows($verdata) >= 1) {
    // Verificación del archivo adjunto
    if (!empty($comprobante['type'])) {
        $validTypes = ['image/jpeg', 'image/png'];
        if (in_array($comprobante['type'], $validTypes)) {
            if (($comprobante['size'] / 1024) <= $comprobanteMaxSize) {
                chmod($comprobanteDir, 0777);
                $extPicture = ($comprobante['type'] == 'image/jpeg') ? '.jpg' : '.png';
                $numV = mysqli_num_rows(ejecutarSQL::consultar("SELECT * FROM venta"));
                $comprobanteF = "comprobante_" . ($numV + 1) . $extPicture;
                
                if (!move_uploaded_file($comprobante['tmp_name'], $comprobanteDir . $comprobanteF)) {
                    echo '<script>swal("ERROR", "No se pudo subir el archivo adjunto", "error");</script>';
                    exit();
                }
            } else {
                echo '<script>swal("ERROR", "El tamaño del adjunto es muy grande", "error");</script>';
                exit();
            }
        } else {
            echo '<script>swal("ERROR", "El formato del adjunto es invalido, por favor verifica e intenta nuevamente", "error");</script>';
            exit();
        }
    } else {
        $comprobanteF = "Sin archivo adjunto";
    }

    // Procesamiento del carrito de compras
    if (!empty($_SESSION['carro'])) {
        $StatusV = "Pendiente";
        $suma = 0;
        foreach ($_SESSION['carro'] as $item) {
            $consulta = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='{$item['producto']}'");
            while ($fila = mysqli_fetch_array($consulta, MYSQLI_ASSOC)) {
                $tp = number_format($fila['Precio'] - ($fila['Precio'] * ($fila['Descuento'] / 100)), 2, '.', '');
                $suma += $tp * $item['cantidad'];
            }
            mysqli_free_result($consulta);
        }

        // Inserción de datos en la tabla de ventas
        if (consultasSQL::InsertSQL("venta", "Fecha, NIT, TotalPagar, Estado, NumeroDeposito, TipoEnvio, Adjunto", "'".date('d-m-Y')."','$Cedclien','$suma','$StatusV','$NumDepo','$tipoenvio','$comprobanteF'")) {
            $verId = ejecutarSQL::consultar("SELECT * FROM venta WHERE NIT='$Cedclien' ORDER BY NumPedido DESC LIMIT 1");
            $fila = mysqli_fetch_array($verId, MYSQLI_ASSOC);
            $Numpedido = $fila['NumPedido'];
            mysqli_free_result($verId);

            // Inserción de datos en la tabla de detalles de la venta y actualización de stock
            foreach ($_SESSION['carro'] as $carro) {
                $preP = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='{$carro['producto']}'");
                $filaP = mysqli_fetch_array($preP, MYSQLI_ASSOC);
                $pref = number_format($filaP['Precio'] - ($filaP['Precio'] * ($filaP['Descuento'] / 100)), 2, '.', '');
                consultasSQL::InsertSQL("detalle", "NumPedido, CodigoProd, CantidadProductos, PrecioProd", "'$Numpedido', '{$carro['producto']}', '{$carro['cantidad']}', '$pref'");
                mysqli_free_result($preP);

                $prodStock = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='{$carro['producto']}'");
                while ($fila = mysqli_fetch_array($prodStock, MYSQLI_ASSOC)) {
                    $existencias = $fila['Stock'];
                    $existenciasRest = $carro['cantidad'];
                    consultasSQL::UpdateSQL("producto", "Stock=('$existencias'-'$existenciasRest')", "CodigoProd='{$carro['producto']}'");
                }
                mysqli_free_result($prodStock);
            }

            // Vaciado del carrito de compras
            unset($_SESSION['carro']);
            echo '<script>
            swal({
                title: "Pedido realizado",
                text: "El pedido se ha realizado con éxito",
                type: "success",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    location.reload();
                } else {
                    location.reload();
                }
            });
            </script>';
        } else {
            echo '<script>swal("ERROR", "Ha ocurrido un error inesperado", "error");</script>';
        }
    } else {
        echo '<script>swal("ERROR", "No has seleccionado ningún producto, revisa el carrito de compras", "error");</script>';
    }
} else {
    echo '<script>swal("ERROR", "El ID de usuario es incorrecto, no esta registrado con ningun cliente", "error");</script>';
}
mysqli_free_result($verdata);
?>
