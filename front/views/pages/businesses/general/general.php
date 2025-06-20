<div class="tab-base p-relative">

    <!-- Nav tabs -->
    <ul class="nav nav-callout">
        <li class="nav-item waves-effect" onclick="window.open('/businesses/general','_self');">
            <button class="nav-link active" type="button">Datos Generales</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/businesses/logo','_self');">
            <button class="nav-link" type="button">Cargar Logo</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/businesses/certificate','_self');">
            <button class="nav-link" type="button">Configurar Certificado</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <div class="tab-pane fade show active">
            <h5 class="card-title">Datos Generales</h5>
            <p>Llena los campos del formulario para configurar la empresa.</p>
        </div>

        <div>

            <form class="needs-validation" novalidate method="post" autocomplete="off">

                <input type="hidden" name="id-tenant" value="<?php echo $_SESSION["empresa"]->id_empresa ?>">

                <div class="row">

                    <div class="col-md-2">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="ruc-tenant" id="ruc-tenant" placeholder="RUC" value="<?php echo $dataTenants->ruc_empresa ?>" readonly>
                            <label for="ruc-tenant">RUC <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="name-tenant" id="name-tenant" placeholder="Razón Social" value="<?php echo $dataTenants->razon_social_empresa ?>" readonly>
                            <label for="name-tenant">Razón Social <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="nc-tenant" id="nc-tenant" placeholder="Nombre Comercial" value="<?php echo $dataTenants->nombre_comercial_empresa ?>" required>
                            <label for="nc-tenant">Nombre Comercial <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="phone-tenant" id="phone-tenant" placeholder="Teléfono" value="<?php echo $dataTenants->telefono_empresa ?>" pattern="[-\\(\\)\\0-9 ]{1,}" onchange="validateJS(event, 'phone')" required>
                            <label for="phone-tenant">Teléfono <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="dep-tenant" id="dep-tenant" placeholder="Departamento" value="<?php echo $dataTenants->departamento_empresa ?>" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
                            <label for="dep-tenant">Departamento <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="pro-tenant" id="pro-tenant" placeholder="Provincia" value="<?php echo $dataTenants->provincia_empresa ?>" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
                            <label for="pro-tenant">Provincia <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="dis-tenant" id="dis-tenant" placeholder="Distrito" value="<?php echo $dataTenants->distrito_empresa ?>" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
                            <label for="dis-tenant">Distrito <sup class="text-danger">*</sup></label>
                        </div>

                    </div>
                    
                    <div class="col-md-3">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="ubi-tenant" id="ubi-tenant" placeholder="Ubigeo" value="<?php echo $dataTenants->ubigeo_empresa ?>" pattern="[0-9]{1,}" onchange="validateJS(event, 'numbers')" required>
                            <label for="ubi-tenant">Ubigeo <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-floating mt-2 mb-3">
                            <input type="text" class="form-control" name="address-tenant" id="address-tenant" placeholder="Dirección" value="<?php echo $dataTenants->direccion_empresa ?>" required onKeyUp="this.value=this.value.toUpperCase();">
                            <label for="address-tenant">Dirección <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-floating mt-2 mb-3">
                            <input type="email" class="form-control" name="mail-tenant" id="mail-tenant" placeholder="Correo Electrónico" value="<?php echo $dataTenants->email_empresa ?>" required>
                            <label for="mail-tenant">Correo Electrónico <sup class="text-danger">*</sup></label>
                        </div>

                    </div>

                    <?php

                        require_once "controllers/tenants.controller.php";


                        $edita = new TenantsController();
                        $edita -> updateTenant($_SESSION["empresa"]->id_empresa);

                    ?>

                    <div class="col-md-12 mt-4 text-center">

                        <a class="btn btn-danger" href="/businesses/general">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>