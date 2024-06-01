<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

function renderPagination($pagina, $numeropaginas) {
    $paginationHTML = '<div class="text-center"><ul class="pagination">';

    $prevDisabled = $pagina == 1 ? 'disabled' : '';
    $nextDisabled = $pagina == $numeropaginas ? 'disabled' : '';

    $paginationHTML .= '<li class="'.$prevDisabled.'"><a href="configAdmin.php?view=adminlist&pag='.($pagina-1).'"><span aria-hidden="true">&laquo;</span></a></li>';

    for ($i = 1; $i <= $numeropaginas; $i++) {
        $active = $pagina == $i ? 'active' : '';
        $paginationHTML .= '<li class="'.$active.'"><a href="configAdmin.php?view=adminlist&pag='.$i.'">'.$i.'</a></li>';
    }

    $paginationHTML .= '<li class="'.$nextDisabled.'"><a href="configAdmin.php?view=adminlist&pag='.($pagina+1).'"><span aria-hidden="true">&raquo;</span></a></li>';
    $paginationHTML .= '</ul></div>';

    return $paginationHTML;
}

function getAdmins($mysqli, $inicio, $regpagina) {
    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM administrador WHERE id!='1' LIMIT $inicio, $regpagina";
    return mysqli_query($mysqli, $query);
}

function getTotalRegistros($mysqli) {
    $query = "SELECT FOUND_ROWS()";
    $result = mysqli_query($mysqli, $query);
    return mysqli_fetch_array($result, MYSQLI_ASSOC)["FOUND_ROWS()"];
}

function buildAdminTableContent($administradores, &$inicio) {
    $content = '';
    while ($adm = mysqli_fetch_array($administradores, MYSQLI_ASSOC)) {
        $content .= '
            <tr>
                <td class="text-center">'.($inicio+1).'</td>
                <td class="text-center">'.$adm['Nombre'].'</td>
                <td class="text-center">
                    <form action="process/deladmin.php" method="POST" class="FormCatElec" data-form="delete">
                        <input type="hidden" name="admin-code" value="'.$adm['id'].'">
                        <button type="submit" class="btn btn-raised btn-xs btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        ';
        $inicio++;
    }
    return $content;
}

$mysqli = mysqli_connect(SERVER, USER, PASS, BD);
mysqli_set_charset($mysqli, "utf8");

$pagina = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
$regpagina = 30;
$inicio = ($pagina > 1) ? (($pagina * $regpagina) - $regpagina) : 0;

$administradores = getAdmins($mysqli, $inicio, $regpagina);
$totalregistros = getTotalRegistros($mysqli);
$numeropaginas = ceil($totalregistros / $regpagina);

$content = buildAdminTableContent($administradores, $inicio);
?>

<p class="lead"></p>
<ul class="breadcrumb" style="margin-bottom: 5px;">
    <li><a href="configAdmin.php?view=admin"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; Nuevo Administrador</a></li>
    <li><a href="configAdmin.php?view=adminlist"><i class="fa fa-list-ol" aria-hidden="true"></i> &nbsp; Administradores del sistema</a></li>
    <li><a href="configAdmin.php?view=account"><i class="fa fa-address-card" aria-hidden="true"></i> &nbsp; Mi cuenta</a></li>
</ul>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <br><br>
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4>Administradores del sistema</h4></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Nombre</th>
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
