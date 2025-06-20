<div class="tab-base p-relative">

    <!-- Nav tabs -->
    <ul class="nav nav-callout">
        <li class="nav-item waves-effect" onclick="window.open('/businesses/general','_self');">
            <button class="nav-link" type="button">Datos Generales</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/businesses/logo','_self');">
            <button class="nav-link active" type="button">Cargar Logo</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/businesses/certificate','_self');">
            <button class="nav-link" type="button">Configurar Certificado</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <div class="tab-pane fade show active">
            <h5 class="card-title">Cargar Logo</h5>
            <p>Realiza la carga del logo de la empresa en formato <b>".png"</b> o <b>".jpg"</b>.</p>
        </div>

        <div>

            <form class="needs-validation" novalidate method="post" enctype="multipart/form-data" autocomplete="off">

                <input type="hidden" name="ruc-tenant" value="<?php echo $_SESSION["empresa"]->ruc_empresa ?>">

                <?php

                    require_once "controllers/logo.controller.php";

                    $upLoad = new LogoController();
                    $upLoad -> logo($_SESSION["empresa"]->ruc_empresa);

                    if($dataTenants->logo_empresa == '') {

                        $imgTenant = TemplateController::returnImgDefault('logo.png', '');

                    } else {

                        $imgTenant = TemplateController::returnImg('logo/'.$_SESSION["empresa"]->ruc_empresa, $dataTenants->logo_empresa);

                    }

                ?>

                <div class="row">

                    <div class="col-md-6 hidden">
                        <div class="form-floating mb-3">
                            <input type="file" name="file-logo" id="file-logo" class="form-control" accept="image/*" onchange="validateImageJS(event,'changePicture')">
                            <label for="file-logo" class="mb-2">Logo (.png / .jpg)<sup class="text-danger">*</sup></label>
                        </div>
                    </div>

                    <div class="col-md-12 text-center">
                        <label for="file-logo" class="d-flex justify-content-center">
                                    
                            <figure class="text-center py-3">
                                
                                <img src="<?php echo $imgTenant ?>" class="img-fluid changePicture img__register">

                            </figure>

                        </label>
                        <small class="py-3 mb-3 text-muted">Recomendado 400 x 84 pixeles</small>
                    </div>

                    <div class="invalid-feedback">Este campo es obligatorio</div>

                    <div class="col-md-12 mt-4 text-center">

                        <a class="btn btn-danger" href="/businesses/logo">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>