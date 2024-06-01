<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

function renderPagination($pagina, $numeropaginas) {
    $paginationHTML = '<div class="text-center"><ul class="pagination">';
    $prevDisabled = $pagina == 1 ? 'disabled' : '';
    $nextDisabled = $pagina == $numeropaginas ? 'disabled' : '';

    $paginationHTML .= '<li class="'.$prevDisabled.'"><a href="configAdmin.php?view=order&pag='.($pagina-1).'"><span aria-hidden="true">&laquo;</span></a></li>';

    for ($i = 1; $i <= $numeropaginas; $i++) {
        $active = $pagina == $i ? 'active' : '';
        $paginationHTML .= '<li class="'.$active.'"><a href="configAdmin.php?view=order&pag='.$i.'">'.$i.'</a></li>';
    }

    $paginationHTML .= '<li class="'.$nextDisabled.'"><a href="configAdmin.php?view=order&pag='.($pagina+1).'"><span aria-hidden="true">&raquo;</span></a></li>';
    $paginationHTML .= '</ul></div>';

    return $paginationHTML;
}

function getOrders($mysqli, $inicio, $regpagina) {
    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM venta LIMIT $inicio, $regpagina";
    return mysqli_query($mysqli, $query);
}

function getTotalRegistros($mysqli) {
    $query = "SELECT FOUND_ROWS()";
    $result = mysqli_query($mysqli, $query);
    return mysqli_fetch_array($result, MYSQLI_ASSOC)["FOUND_ROWS()"];
}

function buildOrderTableContent($orders, &$cr) {
    $content = '';
    while ($order = mysqli_fetch_array($orders, MYSQLI_ASSOC)) {
        $cliente = getClientName($order['NIT']);
        $comprobanteLink = is_file("./assets/comprobantes/".$order['Adjunto']) 
            ? '<a href="./assets/comprobantes/'.$order['Adjunto'].'" target="_blank" class="btn btn-raised btn-xs btn-info btn-block">Comprobante</a>' 
            : '';

        $content .= '
            <tr>
                <td class="text-center">'.$cr.'</td>
                <td class="text-center">'.$order['NumeroDeposito'].'</td>
                <td class="text-center">'.$order['Fecha'].'</td>
                <td class="text-center">'.$cliente.'</td>
                <td class="text-center">'.$order['TotalPagar'].'</td>
                <td class="text-center">'.$order['Estado'].'</td>
                <td class="text-center">'.$order['TipoEnvio'].'</td>
                <td class="text-center">
                    <a href="#!" class="btn btn-raised btn-xs btn-success btn-block btn-up-order" data-code="'.$order['NumPedido'].'">Actualizar</a>
                    '.$comprobanteLink.'
                    <a href="./report/factura.php?id='.$order['NumPedido'].'" class="btn btn-raised btn-xs btn-primary btn-block" target="_blank">Imprimir</a>
                </td>
                <td class="text-center">
                    <form action="process/delPedido.php" method="POST" class="FormCatElec" data-form="delete">
                        <input type="hidden" name="num-pedido" value="'.$order['NumPedido'].'">
                        <button type="submit" class="btn btn-raised btn-xs btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        ';
        $cr++;
    }
    return $content;
}

function getClientName($NIT) {
    $conUs = ejecutarSQL::consultar("SELECT Nombre FROM cliente WHERE NIT='$NIT'");
    $UsP = mysqli_fetch_array($conUs, MYSQLI_ASSOC);
    return $UsP['Nombre'];
}

$mysqli = mysqli_connect(SERVER, USER, PASS, BD);
mysqli_set_charset($mysqli, "utf8");

$pagina = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
$regpagina = 30;
$inicio = ($pagina > 1) ? (($pagina * $regpagina) - $regpagina) : 0;

$pedidos = getOrders($mysqli, $inicio, $regpagina);
$totalregistros = getTotalRegistros($mysqli);
$numeropaginas = ceil($totalregistros / $regpagina);

$cr = $inicio + 1;
$content = buildOrderTableContent($pedidos, $cr);
?>

<p class="lead"></p>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <br><br>
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4>Pedidos de la tienda</h4></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">N. Deposito</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Cliente</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Envío</th>
                                <th class="text-center">Opciones</th>
                                <th class="text-center">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $content; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($numeropaginas >= 1): ?>
                    <?php echo renderPagination($pagina, $numeropaginas); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-order" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="padding: 15px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title text-center text-primary" id="myModalLabel">Actualizar estado del pedido</h5>
            </div>
            <form action="./process/updatePedido.php" method="POST" class="FormCatElec" data-form="update">
                <div id="OrderSelect"></div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-raised btn-sm">Actualizar</button>
                    <button type="button" class="btn btn-danger btn-raised btn-sm" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.btn-up-order').on('click', function(e){
        e.preventDefault();
        var code = $(this).attr('data-code');
        $.ajax({
            url: './process/checkOrder.php',
            type: 'POST',
            data: {code: code},
            success: function(data){
                $('#OrderSelect').html(data);
                $('#modal-order').modal({
                    show: true,
                    backdrop: "static"
                });
            }
        });
        return false;
    });
});
</script>
