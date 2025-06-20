<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Mantemenos el input lleno si se encuetra un error
=============================================*/
if (isset($_POST["loginEmail"])) {

    $vUser = $_POST["loginEmail"];

} else {

    $vUser = '';

}

/*=============================================
Validamos el logo
=============================================*/
if($dataSett->logo_sistema_configuracion == '') {

    $imgSett = TemplateController::returnImgDefault('logo.png', '');

} else {

    $imgSett = TemplateController::returnImg('img/logo', $dataSett->logo_sistema_configuracion);

}

/*=============================================
Obtenemos las configuraciones adicionales
=============================================*/
foreach (json_decode($dataSett->extras_configuracion) as $key => $elementExtras) {

    $resetPass = $elementExtras->reset_pass;

}

if($resetPass == 'no') {


    echo '<script>
            fncFormatInputs();
            matPreloader("off");
            fncSweetAlert("close", "", "");
            fncSweetAlert("error", "You cannot access this section", "/");
        </script>';

} else {

?>

<div id="root" class="root front-container">

    <!-- CONTENTS -->
    <section id="content" class="content">

        <div class="content__boxed w-100 min-vh-100 d-flex flex-column align-items-center justify-content-center">

            <div class="content__wrap" style="min-width: 360px; max-width: 360px;">

                <div class="card shadow-lg">

                    <div class="card-body">

                        <div class="text-center">
                            <img src="<?php echo $imgSett ?>" alt="<?php echo $dataSett->nombre_sistema_configuracion ?>" class="mb-3 img-fluid hidden" style="height: 40px">
                            <h1 class="h3">Olvidé mi contraseña</h1>
                            <small class="text-muted">Te enviaremos una nueva contraseña a tu correo</small>
                        </div>

                        <form method="post" class="mt-4 needs-validation" novalidate autocomplete="off">

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="forgotEmail" name="forgotEmail" placeholder="Correo Electrónico<" value="<?php echo $vUser; ?>" autofocus required>
                                <label for="forgotEmail">Correo Electrónico</label>
                            </div>

                            <?php

                                /*=============================================
                                Controladores
                                =============================================*/
                                require_once "controllers/users.controller.php";

                                $forgot = new UsersController();
                                $forgot->forgot();

                            ?>

                            <div class="d-grid mt-4">
                                <button class="btn btn-primary" type="submit">Enviar Contraseña</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <a href="/" class="btn-link text-decoration-none text-primary">Regresar al Login</a>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- END - CONTENTS -->
</div>

<script src="views/assets/custom/forms/forms.js"></script>

<?php } ?>