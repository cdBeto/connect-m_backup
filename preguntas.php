<?php
include './library/configServer.php';
include './library/consulSQL.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Preguntas y respuestas</title>
    <?php include './inc/link.php'; ?>
</head>
<body id="container-page-product">
    <?php include './inc/navbar.php'; ?>
    <section id="store">
        <br>
        <div class="container">
            <div class="page-header">
                <h1>Preguntas y respuestas <small class="tittles-pages-logo"></small></h1>
            </div>
            <div class="page-header text-center">
                <h2>Centro de Ayuda</h2>
            </div>
            <div class="page-header text-center">
                <h4>En Connect M, nos comprometemos a ser sus expertos calificados para todas tus necesidades. Consulta la siguiente lista de preguntas frecuentes para obtener algunas respuestas útiles. Si no encuentras lo que necesitas, avísanos y te contactaremos pronto.</h4>
            </div>
            <div class="faq-section">
                <div class="page-header text-center">
                    <h2>¿Ofrecemos Garantía?</h2>
                </div>
                <div class="page-header text-center">
                    <h4>No ofrecemos una garantía directamente ya que somos una plataforma intermediaria que conecta a los usuarios con los locales de reparación. Sin embargo, todos los locales que aparecen en nuestra plataforma han sido evaluados y seleccionados cuidadosamente para garantizar que cumplan con estándares de calidad. Además, puedes consultar las reseñas y calificaciones de otros usuarios para tomar una decisión informada. En caso de que algo salga mal, cada local tiene sus propias políticas de garantía que puedes revisar antes de contratar sus servicios.</h4>
                </div>
            </div>
            <div class="faq-section">
                <div class="page-header text-center">
                    <h2>¿Ofrecen Consultas Virtuales?</h2>
                </div>
                <div class="page-header text-center">
                    <h4>Sí, algunos de los locales de reparación con los que trabajamos ofrecen consultas virtuales para diagnosticar problemas preliminares y proporcionar estimaciones de costos. Puedes encontrar esta información en los perfiles de los locales dentro de nuestra plataforma y agendar una consulta virtual si el local dispone de este servicio.</h4>
                </div>
            </div>
            <div class="faq-section">
                <div class="page-header text-center">
                    <h2>Adicional</h2>
                </div>
                <div class="page-header text-center">
                    <h4>Para garantizar una experiencia segura y satisfactoria en nuestra plataforma, contamos con políticas y condiciones claras que protegen a los usuarios. En caso de cualquier inconveniente, estamos disponibles para mediar y ayudarte a resolver el problema con el local correspondiente.</h4>
                </div>
            </div>
        </div>
    </section>
    <?php include './inc/footer.php'; ?>
</body>
</html>
