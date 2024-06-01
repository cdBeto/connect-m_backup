<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

function renderBreadcrumb() {
    return '
    <ul class="breadcrumb" style="margin-bottom: 5px;">
        <li>
            <a href="configAdmin.php?view=product">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; Nuevo producto
            </a>
        </li>
        <li>
            <a href="configAdmin.php?view=productlist">
                <i class="fa fa-list-ol" aria-hidden="true"></i> &nbsp; Productos en tienda
            </a>
        </li>
    </ul>';
}

function renderPagination($pagina, $numeropaginas) {
    $paginationHTML = '<div class="text-center"><ul class="pagination">';
    $prevDisabled = $pagina == 1 ? 'disabled' : '';
    $nextDisabled = $pagina == $numeropaginas ? 'disabled' : '';

    $paginationHTML .= '<li class="'.$prevDisabled.'"><a href="configAdmin.php?view=productlist&pag='.($pagina-1).'"><span aria-hidden="true">&laquo;</span></a></li>';

    for ($i = 1; $i <= $numeropaginas; $i++) {
        $active = $pagina == $i ? 'active' : '';
        $paginationHTML .= '<li class="'.$active.'"><a href="configAdmin.php?view=productlist&pag='.$i.'">'.$i.'</a></li>';
    }

    $paginationHTML .= '<li class="'.$nextDisabled.'"><a href="configAdmin.php?view=productlist&pag='.($pagina+1).'"><span aria-hidden="true">&raquo;</span></a></li>';
    $paginationHTML .= '</ul></div>';

    return $paginationHTML;
}

function getProducts($mysqli, $inicio, $regpagina) {
    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM producto LIMIT $inicio, $regpagina";
    return mysqli_query($mysqli, $query);
}

function getTotalRegistros($mysqli) {
    $query = "SELECT FOUND_ROWS()";
    $result = mysqli_query($mysqli, $query);
    return mysqli_fetch_array($result, MYSQLI_ASSOC)["FOUND_ROWS()"];
}

function getCategoryName($codigoCat) {
    $categ = ejecutarSQL::consultar("SELECT Nombre FROM categoria WHERE CodigoCat='$codigoCat'");
    $datc = mysqli_fetch_array($categ, MYSQLI_ASSOC);
    return $datc['Nombre'];
}

function getProviderName($NITProveedor) {
    $prov = ejecutarSQL::consultar("SELECT NombreProveedor FROM proveedor WHERE NITProveedor='$NITProveedor'");
    $datp = mysqli_fetch_array($prov, MYSQLI_ASSOC);
    return $datp['NombreProveedor'];
}

function buildProductTableContent($productos, &$cr) {
    $content = '';
    while ($prod = mysqli_fetch_array($productos, MYSQLI_ASSOC)) {
        $categoryName = getCategoryName($prod['CodigoCat']);
        $providerName = getProviderName($prod['NITProveedor']);

        $content .= '
            <tr>
                <td class="text-center">'.$cr.'</td>
                <td class="text-center">'.$prod['CodigoProd'].'</td>
                <td class="text-center">'.$prod['NombreProd'].'</td>
                <td class="text-center">'.$categoryName.'</td>
                <td class="text-center">'.$prod['Precio'].'</td>
                <td class="text-center">'.$prod['Modelo'].'</td>
                <td class="text-center">'.$prod['Marca'].'</td>
                <td class="text-center">'.$prod['Stock'].'</td>
                <td class="text-center">'.$providerName.'</td>
                <td class="text-center">'.$prod['Estado'].'</td>
                <td class="text-center">
                    <a href="configAdmin.php?view=productinfo&code='.$prod['CodigoProd'].'" class="btn btn-raised btn-xs btn-success">Actualizar</a>
                </td>
                <td class="text-center">
                    <form action="process/delprod.php" method="POST" class="FormCatElec" data-form="delete">
                        <input type="hidden" name="prod-code" value="'.$prod['CodigoProd'].'">
                        <button type="submit" class="btn btn-raised btn-xs btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        ';
        $cr++;
    }
    return $content;
}

$mysqli = mysqli_connect(SERVER, USER, PASS, BD);
mysqli_set_charset($mysqli, "utf8");

$pagina = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
$regpagina = 30;
$inicio = ($pagina > 1) ? (($pagina * $regpagina) - $regpagina) : 0;

$productos = getProducts($mysqli, $inicio, $regpagina);
$totalregistros = getTotalRegistros($mysqli);
$numeropaginas = ceil($totalregistros / $regpagina);

$cr = $inicio + 1;
$content = buildProductTableContent($productos, $cr);
?>

<p class="lead"></p>
<?php echo renderBreadcrumb(); ?>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <br><br>
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4>Productos en tienda</h4></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Código</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Categoría</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center">Modelo</th>
                                <th class="text-center">Marca</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Local</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Actualizar</th>
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
