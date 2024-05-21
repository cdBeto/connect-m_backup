<?php
include './library/configServer.php';
include './library/consulSQL.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>LOCALES</title>
    <?php include './inc/link.php'; ?>
</head>

<body id="container-page-product">
    <?php include './inc/navbar.php'; ?>
    <section id="infoproduct">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <h1><small class="tittles-pages-logo"></small></h1>
                </div>
                <?php 
                    $CodigoProducto=consultasSQL::clean_string($_GET['CodigoProd']);
                    $productoinfo=  ejecutarSQL::consultar("SELECT producto.CodigoProd,producto.NombreProd,producto.CodigoCat,categoria.Nombre,producto.Precio,producto.Descuento,producto.Stock,producto.Imagen FROM categoria INNER JOIN producto ON producto.CodigoCat=categoria.CodigoCat  WHERE CodigoProd='".$CodigoProducto."'");
                    while($fila=mysqli_fetch_array($productoinfo, MYSQLI_ASSOC)){
                        echo '
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="text-center">Información del local</h3>
                                <br><br>
                                <h4><strong>Nombre: </strong>'.$fila['NombreProd'].'</h4><br>';
                        echo '
                            <a href="https://www.facebook.com/" target="_blank">
                                <i class="fa fa-facebook" aria-hidden="true">&nbsp; Facebook </i> 
                            </a>
                            </a><br>
                            <a href="https://twitter.com/" target="_blank">
                                <i class="fa fa-twitter" aria-hidden="true">&nbsp; Twitter </i>
                            </a><br>
                            <a href="https://www.youtube.com/" target="_blank">
                                <i class="fa fa-youtube-play" aria-hidden="true">&nbsp; YouTube </i>
                            </a><br>
                            <a href="https://www.instagram.com/" target="_blank">
                                <i class="fa fa-instagram" aria-hidden="true">&nbsp; Instagram </i>
                            </a><br>';
                                if($fila['Stock']>=1){
                                    if($_SESSION['nombreAdmin']!="" || $_SESSION['nombreUser']!=""){
                                        echo '<form action="process/carrito.php" method="POST" class="FormCatElec" data-form="">
                                            <input type="hidden" value="'.$fila['CodigoProd'].'" name="codigo">
                                            <label class="text-center"><small></small></label>
                                            
                                        </form>
                                        <div class="ResForm"></div>';
                                    }else{
                                        //echo '<p class="text-center"><small>Para agregar productos al carrito de compras debes iniciar sesion</small></p><br>';
                                        //echo '<button class="btn btn-lg btn-raised btn-info btn-block" data-toggle="modal" data-target=".modal-login"><i class="fa fa-user"></i>&nbsp;&nbsp; Iniciar sesion</button>';
                                    }
                                }else{
                                    echo '<p class="text-center text-danger lead">No existe este local</p><br>';
                                }
                                if($fila['Imagen']!="" && is_file("./assets/img-products/".$fila['Imagen'])){ 
                                    $imagenFile="./assets/img-products/".$fila['Imagen']; 
                                }else{ 
                                    $imagenFile="./assets/img-products/default.png"; 
                                }
                                echo '<br>
                                <a href="product.php" class="btn btn-lg btn-primary btn-raised btn-block"><i class="fa fa-mail-reply"></i>&nbsp;&nbsp;Regresar a la tienda</a>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <br><br><br>
                                <img class="img-responsive" src="'.$imagenFile.'">
                            </div>';
                    }
                ?>
            </div>
        </div>
    </section>

    <?php include './inc/footer.php'; ?>

</body>

</html>