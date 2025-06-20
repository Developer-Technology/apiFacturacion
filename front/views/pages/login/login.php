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
    $registerUser = $elementExtras->register_system;
    $loginSocial = $elementExtras->social_login;

}

?>

<div id="root" class="root front-container">

    <!-- CONTENTS -->
    <section id="content" class="content">

        <div class="content__boxed w-100 min-vh-100 d-flex flex-column align-items-center justify-content-center">

            <div class="content__wrap" style="min-width: 360px; max-width: 360px;">

                <div class="card shadow-lg">

                    <div class="card-body">

                        <div class="text-center">
                            <img src="<?php echo $imgSett ?>" alt="<?php echo $dataSett->nombre_sistema_configuracion ?>" class="mb-3 img-fluid" style="height: 40px">
                            <h1 class="h3"><?php echo $dataSett->nombre_sistema_configuracion ?></h1>
                            <small class="text-muted">Un producto de <?php echo $dataSett->nombre_empresa_configuracion ?></small>
                        </div>

                        <form method="post" class="mt-4 needs-validation" novalidate autocomplete="off">

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="loginEmail" name="loginEmail" placeholder="Correo Electrónico" value="<?php echo $vUser; ?>" autofocus required>
                                <label for="loginEmail">Correo Electrónico</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Contraseña" required>
                                <label for="loginPassword">Contraseña</label>
                            </div>

                            <div class="form-check">
                                <input id="remember" class="form-check-input" type="checkbox" onchange="rememberMe(event)">
                                <label for="remember" class="form-check-label">
                                    Recuérdame
                                </label>
                            </div>

                            <?php

                                /*=============================================
                                Controladores
                                =============================================*/
                                require_once "controllers/users.controller.php";

                                $login = new UsersController();
                                $login->login();

                            ?>

                            <div class="d-grid mt-4">
                                <button class="btn btn-primary" type="submit">Iniciar Sesión</button>
                            </div>
                        </form>

                        <?php if($registerUser == 'si' || $resetPass == 'si'): ?>
                        <div class="d-flex justify-content-between mt-4">
                            <?php if($registerUser == 'si'): ?>
                            <a href="/register" class="btn-link text-decoration-none text-primary">Crear una cuenta</a>
                            <?php endif ?>
                            <?php if($resetPass == 'si'): ?>
                            <a href="/forgot" class="btn-link text-decoration-none text-danger">Olvidé mi contraseña</a>
                            <?php endif ?>
                        </div>
                        <?php endif ?>

                        <?php if($loginSocial == 'si'): ?>
                        <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                            <h5 class="m-0">Ingresar con</h5>

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