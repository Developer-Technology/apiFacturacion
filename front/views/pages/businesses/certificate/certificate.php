<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Inlcuimos el modal de las credenciales
=============================================*/
include "views/modals/apiGre.php";

/*=============================================
Validamos el entorno
=============================================*/
if($dataTenants->fase_empresa == 'beta') {

    $required = '';
    $spanRequire = 'hidden';
    $readOnly = 'readonly';

} else {

    $required = 'required';
    $spanRequire = '';
    $readOnly = '';

}

?>

<div class="tab-base p-relative">

    <!-- Nav tabs -->
    <ul class="nav nav-callout">
        <li class="nav-item waves-effect" onclick="window.open('/businesses/general','_self');">
            <button class="nav-link" type="button">Datos Generales</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/businesses/logo','_self');">
            <button class="nav-link" type="button">Cargar Logo</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/businesses/certificate','_self');">
            <button class="nav-link active" type="button">Configurar Certificado</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <div class="tab-pane fade show active">
            <h5 class="card-title">Certificado Digital</h5>
            <p>Llena los campos del formulario y realiza la carga del certificado digital en formato <b>".pfx"</b>.</p>
        </div>

        <div>

            <form class="needs-validation" novalidate method="post" enctype="multipart/form-data" autocomplete="off">

                <input type="hidden" name="ruc-tenant" value="<?php echo $_SESSION["empresa"]->ruc_empresa ?>">

                <?php

                    require_once "controllers/certificate.controller.php";


                    $upLoad = new CertificateController();
                    $upLoad -> certificate($_SESSION["empresa"]->ruc_empresa);

                ?>

                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mt-2 mb-2">
                            <select name="fase-tenant" id="fase-tenant" class="form-select" onchange="cambiaEntorno(this)" required>

                                <?php if ($dataTenants->fase_empresa == 'produccion'): ?>

                                    <option value="produccion">Producción</option>
                                    <option value="beta">Beta (Pruebas)</option>

                                <?php else: ?>

                                    <option value="beta">Beta (Pruebas)</option>
                                    <option value="produccion">Producción</option>

                                <?php endif ?>

                            </select>
                            <label for="fase-tenant">Selecciona Entorno <sup style="color:red;">*</sup></label>
                        </div>
                    </div>

                    <div class="col-md-4">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control usuario-sol required readonly" name="usuario_sol-tenant" id="usuario_sol-tenant" placeholder="Usuario Sol" <?php echo $required ?> value="<?php echo $dataTenants->usuario_sol_empresa ?>" <?php echo $readOnly ?> onKeyUp="this.value=this.value.toUpperCase();">
                            <label for="usuario_sol-tenant">Usuario Sol <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control clave-sol required readonly" name="clave_sol-tenant" id="clave_sol-tenant" placeholder="Clave Sol" <?php echo $required ?> value="<?php echo $dataTenants->clave_sol_empresa ?>" <?php echo $readOnly ?>>
                            <label for="clave_sol-tenant">Clave Sol <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mt-2 mb-3">
                            <input type="file" name="file-certificate" id="file-certificate" class="form-control file-certificate required readonly" onchange="validatePfx(event)" <?php echo $required ?> <?php echo $readOnly ?>>
                            <label for="file-certificate" class="mb-2">Certificado (.pfx)<sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                        </div>
                    </div>

                    <div class="col-md-4">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control clave-certificate required readonly" name="clave_certificate-tenant" id="clave_certificate-tenant" placeholder="Clave Certificado" <?php echo $required ?> value="<?php echo $dataTenants->clave_certificado_empresa ?>" <?php echo $readOnly ?>>
                            <label for="clave_certificate-tenant">Clave Certificado <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-floating mt-2 mb-3">
                            <input type="date" class="form-control expired-certificate required readonly" name="expired_certificate-tenant" id="expired_certificate-tenant" placeholder="Expira Certificado" <?php echo $required ?> value="<?php echo $dataTenants->expira_certificado_empresa ?>" <?php echo $readOnly ?>>
                            <label for="expired_certificate-tenant">Expira Certificado <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                        </div>

                    </div>

                    <h5 class="card-title">Credenciales Para Guía De Remisión</h5>
                    <p>Requerido para el envío de las GRE por el API de la SUNAT. <a class="pointer text-primary" data-toggle="modal" data-target="#apiGre">¿Cómo consigo mis credenciales?</a></p>

                    <div class="col-md-4">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control client_id required readonly" name="client_id-tenant" id="client_id-tenant" placeholder="client_id" <?php echo $required ?> value="<?php echo $dataTenants->client_id ?>" <?php echo $readOnly ?>>
                            <label for="expired_certificate-tenant">client_id <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control client_secret required readonly" name="client_secret-tenant" id="client_secret-tenant" placeholder="client_secret" <?php echo $required ?> value="<?php echo $dataTenants->client_secret ?>" <?php echo $readOnly ?>>
                            <label for="expired_certificate-tenant">client_secret <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-12 mt-4 text-center">

                        <a class="btn btn-danger" href="/businesses/certificate">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>