<?php

if(isset($routesArray[2])){

    if($routesArray[2] == "general"){

        $nameBread = 'Configuración General';

    } elseif($routesArray[2] == "server") {

        $nameBread = 'Servidor De Correo';

    } elseif($routesArray[2] == "logo") {

        $nameBread = 'Cargar Logo';

    } elseif($routesArray[2] == "favicon") {

        $nameBread = 'Cargar Favicon';

    } elseif($routesArray[2] == "gateway") {

        $nameBread = 'Pasarelas De Pago';

    } elseif($routesArray[2] == "billing") {

        $nameBread = 'Facturación';

    } else {
        
        $nameBread = '----';

    }

} else {

    $nameBread = '----';

}

?>

<div class="content__header content__boxed overlapping">

    <div class="content__wrap">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item">Configuraciones</li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $nameBread ?></li>

            </ol>
        </nav>
        <!-- END : Breadcrumb -->

        <h1 class="page-title mb-0 mt-2">
            <?php echo $nameBread ?>
        </h1>
        <p class="lead">En esta sección podrás configurar los parámentros del sistema.</p>

    </div>

</div>

<div class="content__boxed">

    <div class="content__wrap">

        <?php 

            if(isset($routesArray[2])){

                if($routesArray[2] == "general" ||
                    $routesArray[2] == "server" ||
                    $routesArray[2] == "logo" ||
                    $routesArray[2] == "gateway" ||
                    $routesArray[2] == "billing" ||
                    $routesArray[2] == "favicon"
                ){

                    include $routesArray[2]."/".$routesArray[2].".php";

                } else {

                    echo '<script>
                        window.location = "/settings/general";
                    </script>'; 

                }

            }else{

                echo '<script>
                        window.location = "/settings/general";
                    </script>'; 

            }

        ?>

    </div>
    
</div>