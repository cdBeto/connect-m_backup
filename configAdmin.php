<?php
include './library/configServer.php';
include './library/consulSQL.php';
include './process/securityPanel.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Admin</title>
    <?php include './inc/link.php'; ?>
</head>
<body id="container-page-configAdmin">
    <?php include './inc/navbar.php'; ?>
    
    <section id="prove-product-cat-config">
        <div class="container">
            <div class="page-header">
                <h1>Panel de administración <small class="tittles-pages-logo"></small></h1>
            </div>

            <!-- Barra de navegacion -->
            <ul class="nav nav-tabs nav-justified" style="margin-bottom: 15px;">
                <?php 
                $tabs = [
                    "product" => ["icon" => "fa-cubes", "label" => "Productos"],
                    "provider" => ["icon" => "fa-truck", "label" => "Locales"],
                    "category" => ["icon" => "fa-shopping-basket", "label" => "Categorías"],
                    "admin" => ["icon" => "fa-users", "label" => "Administradores"],
                    "order" => ["icon" => "fa-shopping-cart", "label" => "Pedidos"],
                    "bank" => ["icon" => "fa-university", "label" => "Cuenta bancaria"],
                    "account" => ["icon" => "fa-address-card", "label" => "Mi cuenta"]
                ];

                foreach ($tabs as $view => $data) {
                    echo "<li><a href=\"configAdmin.php?view={$view}\"><i class=\"fa {$data['icon']}\" aria-hidden=\"true\"></i> &nbsp; {$data['label']}</a></li>";
                }
                ?>
            </ul>

            <!-- Contentido -->
            <?php
            $content = $_GET['view'] ?? '';
            $whiteList = [
                "product", "productlist", "productinfo", 
                "provider", "providerlist", "providerinfo", 
                "category", "categorylist", "categoryinfo", 
                "admin", "adminlist", "order", 
                "bank", "account"
            ];

            if ($content) {
                if (in_array($content, $whiteList) && is_file("./admin/{$content}-view.php")) {
                    include "./admin/{$content}-view.php";
                } else {
                    echo '<h2 class="text-center">Lo sentimos, la opción que ha seleccionado no se encuentra disponible</h2>';
                }
            } else {
                echo '<h2 class="text-center">Para empezar, por favor escoja una opción del menú de administración</h2>';
            }
            ?>
        </div>
    </section>

    <?php include './inc/footer.php'; ?>
</body>
</html>
