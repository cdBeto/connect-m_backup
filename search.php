<?php
include './library/configServer.php';
include './library/consulSQL.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Locales</title>
    <?php include './inc/link.php'; // Incluir archivo con enlaces de estilos y scripts?>
</head>
<body id="container-page-product">
    <?php include './inc/navbar.php'; // Incluir barra de navegación ?>
    <section id="store">
        <br>
        <div class="container">
            <div class="page-header">
                <h1>BÚSQUEDA DE LOCALES <small class="tittles-pages-logo"></small></h1>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-md-4 col-md-offset-8">
                        <form action="./search.php" method="GET">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
                                    <input type="text" id="addon1" class="form-control" name="term" required="" title="Escriba nombre o marca del producto">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-raised" type="submit">Buscar</button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $search = isset($_GET['term']) ? consultasSQL::clean_string($_GET['term']) : '';
            if ($search !== ''):
                $mysqli = new mysqli(SERVER, USER, PASS, BD);
                if ($mysqli->connect_error) {
                    die("Error de conexión: " . $mysqli->connect_error);
                }
                // Establecer el conjunto de caracteres a UTF-8
                $mysqli->set_charset("utf8");
                // Obtener el número de página actual, por defecto es 1
                $pagina = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
                $regpagina = 20; // Número de registros por página
                $inicio = ($pagina > 1) ? (($pagina * $regpagina) - $regpagina) : 0;

                // Preparar la consulta de productos usando parámetros para evitar inyecciones SQL
                $query = "
                    SELECT SQL_CALC_FOUND_ROWS * 
                    FROM producto 
                    WHERE NombreProd LIKE ? 
                       OR Modelo LIKE ? 
                       OR Marca LIKE ? 
                    LIMIT ?, ?";
                $stmt = $mysqli->prepare($query);
                $searchTerm = "%$search%";
                $stmt->bind_param('sssii', $searchTerm, $searchTerm, $searchTerm, $inicio, $regpagina);
                $stmt->execute();
                $result = $stmt->get_result();

                // Obtener el total de registros encontrados
                $totalRegistrosResult = $mysqli->query("SELECT FOUND_ROWS() AS total");
                $totalRegistros = $totalRegistrosResult->fetch_assoc()['total'];
                $numeropaginas = ceil($totalRegistros / $regpagina);

                // Verificar si hay productos para mostrar
                if ($result->num_rows >= 1):
                    echo '<div class="col-xs-12"><h3 class="text-center">Se muestran los productos con el nombre, marca o modelo <strong>"' . htmlspecialchars($search) . '"</strong></h3></div><br>';
                    while ($prod = $result->fetch_assoc()): ?>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <div class="thumbnail">
                                <img src="./assets/img-products/<?php echo (!empty($prod['Imagen']) && is_file("./assets/img-products/" . $prod['Imagen'])) ? $prod['Imagen'] : "default.png"; ?>">
                                <div class="caption">
                                    <h3><?php echo $prod['Marca']; ?></h3>
                                    <p><?php echo $prod['NombreProd']; ?></p>
                                    <!--<p>$<?php echo $prod['Precio']; ?></p> -->
                                    <p class="text-center">
                                        <a href="infoProd.php?CodigoProd=<?php echo $prod['CodigoProd']; ?>" class="btn btn-primary btn-raised btn-sm btn-block"><i class="fa fa-plus"></i>&nbsp; Detalles</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <div class="clearfix"></div>
                    <!-- Mostrar paginación si hay más de una página -->
                    <?php if ($numeropaginas > 1): ?>
                        <div class="text-center">
                            <ul class="pagination">
                              <!-- Botón de paginación para ir a la página anterior -->
                                <li class="<?php echo ($pagina == 1) ? 'disabled' : ''; ?>">
                                    <a <?php echo ($pagina != 1) ? 'href="search.php?term=' . $search . '&pag=' . ($pagina - 1) . '"' : ''; ?>>
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <!-- Generar los enlaces de paginación -->
                                <?php
                                for ($i = 1; $i <= $numeropaginas; $i++):
                                    if ($pagina == $i): ?>
                                        <li class="active"><a href="search.php?term=<?php echo $search; ?>&pag=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php else: ?>
                                        <li><a href="search.php?term=<?php echo $search; ?>&pag=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php endif;
                                endfor; ?>
                                <!-- Botón de paginación para ir a la página siguiente -->
                                <li class="<?php echo ($pagina == $numeropaginas) ? 'disabled' : ''; ?>">
                                    <a <?php echo ($pagina != $numeropaginas) ? 'href="search.php?term=' . $search . '&pag=' . ($pagina + 1) . '"' : ''; ?>>
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                  <!-- Mostrar mensaje si no se encontraron productos -->
                    <h2 class="text-center">Lo sentimos, no hemos encontrado locales con el nombre <strong>"<?php echo htmlspecialchars($search); ?>"</strong></h2>
                <?php endif;
                // Cerrar la consulta y la conexión a la base de datos
                $stmt->close();
                $mysqli->close();
            else: ?>
            <!-- Mostrar mensaje si el término de búsqueda está vacío -->
                <h2 class="text-center">Por favor escriba el nombre del local que desea buscar</h2>
            <?php endif; ?>
        </div>
    </section>
    <?php include './inc/footer.php'; ?>
</body>
</html>
