<?php

if(!empty($_SESSION["empresa"])) {

    $titulo = "Mi Suscripción";
    $texto = "En esta sección puedes elegir tu plan, ver tu historial de pagos y pagar tu suscripción.";

} else {

    $titulo = "Suscripciones";
    $texto = "En esta sección puedes administrar, descargar y aprobar los pagos.";

}


?>

<div class="content__header content__boxed overlapping">
    <div class="content__wrap">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item"><a href="/">Inicio</a></li>

                <?php if (isset($routesArray[2])): ?>

                    <?php if ($routesArray[2] == "pay" || $routesArray[2] == "upload" || $routesArray[2] == "verify"): ?>

                        <li class="breadcrumb-item"><a href="/suscriptions"><?php echo $titulo; ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $txtBread ?></li>

                    <?php endif?>

                <?php else: ?>

                    <li class="breadcrumb-item active" aria-current="page"><?php echo $titulo; ?></li>

                <?php endif?>

            </ol>
        </nav>
        <!-- END : Breadcrumb -->

        <h1 class="page-title mb-0 mt-2"><?php echo $titulo; ?></h1>

        <?php if(!empty($_SESSION["empresa"])): ?>
            <button class="btn btn-default border" data-toggle="modal" data-target="#viewToken" style="position: absolute; right: 20px; top: 12%;"><i class="fa fa-eye"></i> <span class="vr"></span> Mis Credenciales</button>
        <?php endif ?>

        <p class="lead"><?php echo $texto; ?></p>

    </div>

</div>

<div class="content__boxed">
    <div class="content__wrap">

    <?php

    if (isset($routesArray[2])) {

        if ($routesArray[2] == "pay" || $routesArray[2] == "upload" || $routesArray[2] == "verify") {

            include "actions/" . $routesArray[2] . ".php";

        }

    } else {

        include "actions/list.php";

    }

    ?>

    </div>

</div>