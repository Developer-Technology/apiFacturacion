<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Mantemenos el input lleno si se encuetra un error
=============================================*/
if (isset($_POST["registerEmail"])) {

    $vUser = $_POST["registerEmail"];

} else {

    $vUser = '';

}

if (isset($_POST["registerName"])) {

    $nUser = $_POST["registerName"];

} else {

    $nUser = '';

}

if (isset($_POST["registerPhone"])) {

    $pUser = $_POST["registerPhone"];

} else {

    $pUser = '';

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

    $registerUser = $elementExtras->register_system;
    $loginSocial = $elementExtras->social_login;


}

if($registerUser == 'no') {


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
                            <h1 class="h3">Crea una cuenta nueva</h1>
                            <small class="text-muted">¡Únete a la comunidad <?php echo $dataSett->nombre_sistema_configuracion ?>!<br> Configuremos tu cuenta.</small>
                        </div>

                        <form method="post" class="mt-4 needs-validation" novalidate autocomplete="off">

                            <div class="row">

                                <div class="col-md-12">

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="registerName" name="registerName" placeholder="Nombres" autofocus value="<?php echo $nUser ?>" required>
                                        <label for="registerName">Nombres <sup class="text-danger">*</sup></label>
                                    </div>

                                </div>

                                <div class="col-md-12">

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="registerPhone" name="registerPhone" placeholder="Teléfono" autofocus value="<?php echo $pUser ?>" required>
                                        <label for="registerPhone">Teléfono <sup class="text-danger">*</sup></label>
                                    </div>

                                </div>

                                <div class="col-md-12">

                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="registerEmail" name="registerEmail" placeholder="Correo Electrónico" value="<?php echo $vUser ?>" onchange="validateRepeat(event,'text','usuarios','email_usuario')" required>
                                        <label for="registerEmail">Correo Electrónico <sup class="text-danger">*</sup></label>
                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="registerPassword" name="registerPassword" placeholder="Contraseña" required>
                                        <label for="registerPassword">Contraseña <sup class="text-danger">*</sup></label>
                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="confirmPass" name="confirmPass" placeholder="Confirmar" required>
                                        <label for="confirmPass">Confirmar <sup class="text-danger">*</sup></label>
                                    </div>                            

                                </div>

                            </div>

                            <div class="form-check">
                                <input id="_dm-registerCheck" class="form-check-input" type="checkbox" required>
                                <label for="_dm-registerCheck" class="form-check-label">
                                    Acepto los <a href="/terms" class="btn-link text-decoration-underline">términos y condiciones</a> <sup class="text-danger">*</sup>
                                </label>
                            </div>

                            <?php

                                /*=============================================
                                Controladores
                                =============================================*/
                                require_once "controllers/users.controller.php";

                                $register = new UsersController();
                                $register->register();

                            ?>

                            <div class="d-grid mt-4">
                                <button class="btn btn-primary" type="submit">Registrarme</button>
                            </div>
                        </form>

                        <div class="d-flex justify-content-between mt-4">
                            ¿Ya tienes una cuenta?
                            <a href="/" class="btn-link text-decoration-none text-primary">Inicia sesión</a>
                        </div>

                        <?php if($loginSocial == 'si'): ?>
                        <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                            <h5 class="m-0">Registrarme con</h5>

                            <!-- Social media buttons -->
                            <div class="ms-3">
                                <a href="#" class="btn btn-sm btn-icon btn-hover btn-primary text-inherit">
                                    <i class="demo-psi-facebook fs-5"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-icon btn-hover btn-danger text-inherit">
                                    <i class="demo-psi-google-plus fs-5"></i>
                                </a>
                            </div>
                            <!-- END : Social media buttons -->

                        </div>
                        <?php endif ?>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- END - CONTENTS -->
</div>

<script src="views/assets/custom/forms/forms.js"></script>

<?php } ?>