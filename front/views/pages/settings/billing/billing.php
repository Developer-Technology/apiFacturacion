<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

    /*=============================================
    Inlcuimos el modal de las credenciales
    =============================================*/
    include "views/modals/apiSunat.php";

    /*=============================================
    Obtenemos las configuraciones de facturacion
    =============================================*/
    foreach (json_decode($dataSett->facturacion_configuracion) as $key => $elementFacturacion) {

        $estado = $elementFacturacion->estado;

        /* Empresa */
        $ruc = $elementFacturacion->empresa->ruc;
        $razonSocial = $elementFacturacion->empresa->razonSocial;
        $nombreComercial = $elementFacturacion->empresa->nombreComercial;
        $departamento = $elementFacturacion->empresa->departamento;
        $provincia = $elementFacturacion->empresa->provincia;
        $distrito = $elementFacturacion->empresa->distrito;
        $ubigeo = $elementFacturacion->empresa->ubigeo;
        $direccion = $elementFacturacion->empresa->direccion;
        $telefono = $elementFacturacion->empresa->telefono;
        $email = $elementFacturacion->empresa->email;

        /* Factura */
        $serie = $elementFacturacion->factura->serie;
        $correlativo = $elementFacturacion->factura->correlativo;

        /* SUNAT */
        $modo = $elementFacturacion->sunat->modo;
        $usuarioSol = $elementFacturacion->sunat->usuarioSol;
        $claveSol = $elementFacturacion->sunat->claveSol;
        $claveCertificado = $elementFacturacion->sunat->claveCertificado;
        $expiraCertificado = $elementFacturacion->sunat->expiraCertificado;

    }

    /*=============================================
    Validamos el entorno
    =============================================*/
    if($modo == 'beta') {

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
        <li class="nav-item waves-effect" onclick="window.open('/settings/general','_self');">
            <button class="nav-link" type="button">General</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/server','_self');">
            <button class="nav-link" type="button">Servidor Correo</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/logo','_self');">
            <button class="nav-link" type="button">Logo</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/favicon','_self');">
            <button class="nav-link" type="button">Favicon</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/billing','_self');">
            <button class="nav-link active" type="button">Facturación</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/gateway','_self');">
            <button class="nav-link" type="button">Pasarelas De Pago</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <form method="post" class="needs-validation" novalidate enctype="multipart/form-data" autocomplete="off">

            <input type="hidden" value="11" id="doc-long">

            <div class="row mb-2">

                <h5 class="card-title">Datos De La Empresa</h5>
                <p>Requerido para los datos del emisor de la factura.</p>

                <div class="col-md-12">

                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control documento" id="doc-person" name="ruc-system" placeholder="RUC" maxlength="11" pattern="[0-9]{1,}" value="<?php echo $ruc ?>" required>
                                <label for="doc-person">RUC <span id="estado-ruc"></span> <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary mb-3" onclick="sendAjaxConsult('ruc')" style="width: 100%;"><i class="fa fa-search"></i></button>
                        </div>

                        <div class="col-md-5">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control razon-social" id="name-tenant" name="rs-system" placeholder="Razón Social" value="<?php echo $razonSocial ?>" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="name-tenant">Razón Social <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control nombre-comercial" id="nc-tenant" name="nc-system" placeholder="Nombre Comercial" value="<?php echo $nombreComercial ?>" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="nc-tenant">Nombre Comercial <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control domicilio" id="address-tenant" name="address-system" placeholder="Dirección" value="<?php echo $direccion ?>" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="address-tenant">Dirección <span id="habido-ruc"></span> <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control departamento" id="dep-tenant" name="dep-system" placeholder="Departamento" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" value="<?php echo $departamento ?>" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="dep-tenant">Departamento <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control provincia" id="pro-tenant" name="pro-system" placeholder="Provincia" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" value="<?php echo $provincia ?>" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="pro-tenant">Provincia <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control distrito" id="dis-tenant" name="dis-system" placeholder="Distrito" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" value="<?php echo $distrito ?>" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="dis-tenant">Distrito <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control ubigeo" id="ubi-tenant" name="ubi-system" placeholder="Ubigeo" pattern="[0-9]{1,}" onchange="validateJS(event, 'numbers')" value="<?php echo $ubigeo ?>" required>
                                <label for="ubi-tenant">Ubigeo <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control phone-tenant" id="phone-tenant" name="phone-system" placeholder="Teléfono" pattern="[-\\(\\)\\0-9 ]{1,}" onchange="validateJS(event, 'phone')" value="<?php echo $telefono ?>" required>
                                <label for="phone-tenant">Teléfono <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3 mt-2">
                                <input type="email" class="form-control" id="email-tenant" name="email-system" placeholder="Correo Electrónico" value="<?php echo $email ?>" required>
                                <label for="email-tenant">Correo Electrónico <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div class="row mb-2">

                <h5 class="card-title">Datos De La Factura</h5>
                <p>Requerido para generar las facturas al recibir y/o aprobar un pago.</p>

                <div class="col-md-12">

                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="serie-system" name="serie-system" placeholder="Serie" value="<?php echo $serie ?>" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="serie-system">Serie <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="number-system" name="number-system" placeholder="Correlativo" value="<?php echo $correlativo ?>" pattern="[0-9]{1,}" onchange="validateJS(event, 'numbers')" required>
                                <label for="number-system">Correlativo <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div class="row">

                <h5 class="card-title">Datos SUNAT</h5>
                <p>Requerido para la firma y envío de las facturas generadas.</p>

                <div class="col-md-12">

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-floating mt-2 mb-3">
                                <select name="fase-system" id="fase-tenant" class="form-select" onchange="cambiaEntorno(this)" required>

                                <?php if ($modo == 'produccion'): ?>

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
                                <input type="text" class="form-control usuario-sol required readonly" name="usuario_sol-system" id="usuario_sol-tenant" placeholder="Usuario Sol" <?php echo $required ?> value="<?php echo $usuarioSol ?>" <?php echo $readOnly ?> onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="usuario_sol-tenant">Usuario Sol <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-floating mt-2 mb-3">
                                <input type="text" class="form-control clave-sol required readonly" name="clave_sol-system" id="clave_sol-tenant" placeholder="Clave Sol" <?php echo $required ?> value="<?php echo $claveSol ?>" <?php echo $readOnly ?>>
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
                                <input type="text" class="form-control clave-certificate required readonly" name="clave_certificate-system" id="clave_certificate-tenant" placeholder="Clave Certificado" <?php echo $required ?> value="<?php echo $claveCertificado ?>" <?php echo $readOnly ?>>
                                <label for="clave_certificate-tenant">Clave Certificado <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-floating mt-2 mb-3">
                                <input type="date" class="form-control expired-certificate required readonly" name="expired_certificate-system" id="expired_certificate-tenant" placeholder="Expira Certificado" <?php echo $required ?> value="<?php echo $expiraCertificado ?>" <?php echo $readOnly ?>>
                                <label for="expired_certificate-tenant">Expira Certificado <sup class="text-danger <?php echo $spanRequire ?>">*</sup></label>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <hr>

            <div class="row">

                <?php

                    /*=============================================
                    Controladores
                    =============================================*/
                    require_once "controllers/settings.controller.php";

                    $edit = new SettingsController();
                    $edit->editBilling();

                ?>

                <!--=====================================
                Botones
                ======================================-->
                <div class="col-md-12 mt-4 text-center">

                    <a class="btn btn-danger" href="/settings/billing">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>

                </div>

            </div>

        </form>

    </div>

</div>

<script src="views/assets/custom/forms/forms.js"></script>