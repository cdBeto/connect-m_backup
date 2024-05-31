<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

function isValidImage($imgType, $imgSize, $imgMaxSize) {
    $validTypes = ['image/jpeg' => '.jpg', 'image/png' => '.png'];
    if (!array_key_exists($imgType, $validTypes)) {
        echo '<script>swal("ERROR", "El formato de la imagen del producto es inválido, solo se admiten archivos con la extensión .jpg y .png", "error");</script>';
        return false;
    }
    if (($imgSize / 1024) > $imgMaxSize) {
        echo '<script>swal("ERROR", "Ha excedido el tamaño máximo de la imagen, tamaño máximo es de 5MB", "error");</script>';
        return false;
    }
    return $validTypes[$imgType];
}

function moveImage($tmpName, $imgFinalName) {
    chmod('../assets/img-products/', 0777);
    if (!move_uploaded_file($tmpName, "../assets/img-products/" . $imgFinalName)) {
        echo '<script>swal("ERROR", "Ha ocurrido un error al cargar la imagen", "error");</script>';
        return false;
    }
    return true;
}

function insertProduct($productData) {
    if (consultasSQL::InsertSQL("producto", implode(", ", array_keys($productData)), implode(", ", array_map(function($value) { return "'$value'"; }, array_values($productData))))) {
        echo '<script>
            swal({
                title: "Producto registrado",
                text: "El producto se añadió a la tienda con éxito",
                type: "success",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                location.reload();
            });
        </script>';
    } else {
        echo '<script>swal("ERROR", "Ocurrió un error inesperado, por favor intente nuevamente", "error");</script>';
    }
}

$codeProd = consultasSQL::clean_string($_POST['prod-codigo']);
$nameProd = consultasSQL::clean_string($_POST['prod-name']);
$cateProd = consultasSQL::clean_string($_POST['prod-categoria']);
$priceProd = consultasSQL::clean_string($_POST['prod-price']);
$modelProd = consultasSQL::clean_string($_POST['prod-model']);
$marcaProd = consultasSQL::clean_string($_POST['prod-marca']);
$stockProd = consultasSQL::clean_string($_POST['prod-stock']);
$codePProd = consultasSQL::clean_string($_POST['prod-codigoP']);
$estadoProd = consultasSQL::clean_string($_POST['prod-estado']);
$adminProd = consultasSQL::clean_string($_POST['admin-name']);
$descProd = consultasSQL::clean_string($_POST['prod-desc-price']);
$imgName = $_FILES['img']['name'];
$imgType = $_FILES['img']['type'];
$imgSize = $_FILES['img']['size'];
$imgMaxSize = 5120;

if ($codeProd && $nameProd && $cateProd && $priceProd && $modelProd && $marcaProd && $stockProd && $codePProd) {
    $verificar = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='$codeProd'");
    if (mysqli_num_rows($verificar) <= 0) {
        $imgEx = isValidImage($imgType, $imgSize, $imgMaxSize);
        if ($imgEx !== false) {
            $imgFinalName = $codeProd . $imgEx;
            if (moveImage($_FILES['img']['tmp_name'], $imgFinalName)) {
                $productData = [
                    'CodigoProd' => $codeProd,
                    'NombreProd' => $nameProd,
                    'CodigoCat' => $cateProd,
                    'Precio' => $priceProd,
                    'Descuento' => $descProd,
                    'Modelo' => $modelProd,
                    'Marca' => $marcaProd,
                    'Stock' => $stockProd,
                    'NITProveedor' => $codePProd,
                    'Imagen' => $imgFinalName,
                    'Nombre' => $adminProd,
                    'Estado' => $estadoProd
                ];
                insertProduct($productData);
            }
        }
    } else {
        echo '<script>swal("ERROR", "El código de producto que acaba de ingresar ya está registrado en el sistema, por favor ingrese otro código de producto distinto", "error");</script>';
    }
} else {
    echo '<script>swal("ERROR", "Los campos no deben de estar vacíos, por favor verifique e intente nuevamente", "error");</script>';
}
mysqli_free_result($verificar);
?>
