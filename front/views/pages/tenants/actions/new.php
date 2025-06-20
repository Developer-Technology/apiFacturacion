<!-- Table with toolbar -->
<div class="card">
    
    <form method="post" class="needs-validation" novalidate autcomplete="off">

        <input type="hidden" value="11" id="doc-long">

        <div class="card-body">

            <?php

                require_once "controllers/tenants.controller.php";

                $create = new TenantsController();
                $create->create(NULL, NULL, NULL);

            ?>

            <div class="row">

                <div class="col-md-12">

                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control documento" id="doc-person" name="ruc-tenant" placeholder="RUC" maxlength="11" pattern="[0-9]{1,}" onchange="validateRepeat(event,'text','empresas','ruc_empresa')" required>
                                <label for="doc-person">RUC <span id="estado-ruc"></span> <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary mb-3" onclick="sendAjaxConsult('ruc')" style="width: 100%;"><i class="fa fa-search"></i></button>
                        </div>

                        <div class="col-md-5">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control razon-social" id="name-tenant" name="name-tenant" placeholder="Razón Social" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="name-tenant">Razón Social <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control nombre-comercial" id="nc-tenant" name="nc-tenant" placeholder="Nombre Comercial" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="nc-tenant">Nombre Comercial <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control domicilio" id="address-tenant" name="address-tenant" placeholder="Dirección" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="address-tenant">Dirección <span id="habido-ruc"></span> <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control departamento" id="dep-tenant" name="dep-tenant" placeholder="Departamento" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="dep-tenant">Departamento <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control provincia" id="pro-tenant" name="pro-tenant" placeholder="Provincia" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="pro-tenant">Provincia <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control distrito" id="dis-tenant" name="dis-tenant" placeholder="Distrito" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
                                <label for="dis-tenant">Distrito <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control ubigeo" id="ubi-tenant" name="ubi-tenant" placeholder="Ubigeo" pattern="[0-9]{1,}" onchange="validateJS(event, 'numbers')" required>
                                <label for="ubi-tenant">Ubigeo <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control phone-tenant" id="phone-tenant" name="phone-tenant" placeholder="Teléfono" pattern="[-\\(\\)\\0-9 ]{1,}" onchange="validateJS(event, 'phone')" required>
                                <label for="phone-tenant">Teléfono <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3 mt-2">
                                <input type="email" class="form-control" id="email-tenant" name="email-tenant" placeholder="Correo Electrónico" required>
                                <label for="email-tenant">Correo Electrónico <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">

                                <?php 

                                    $url = "planes?select=id_plan,nombre_plan,precio_plan&orderBy=precio_plan&orderMode=ASC";
                                    $method = "GET";
                                    $fields = array();
                                    $token = TemplateController::tokenSet();
                                    $plans = CurlController::requestSunat($url, $method, $fields, $token)->response->data;

                                ?>

                                <select name="plan-tenant" id="plan-tenant" class="form-select" required>

                                <?php foreach ($plans as $key => $value): ?>	

                                    <option value="<?php echo $value->id_plan; ?>"><?php echo $value->nombre_plan . ' - S/' . $value->precio_plan; ?></option>

                                <?php endforeach ?>

                                </select>
                                <label for="plan-tenant">Selecciona Plan <sup style="color:red;">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <select name="periodo-tenant" id="periodo-tenant" class="form-select" required>
                                    <option value="1">1 Mes</option>
                                    <option value="2">2 Meses</option>
                                    <option value="3">3 Meses</option>
                                    <option value="4">4 Meses</option>
                                    <option value="5">5 Meses</option>
                                    <option value="6">6 Meses</option>
                                    <option value="7">7 Meses</option>
                                    <option value="8">8 Meses</option>
                                    <option value="9">9 Meses</option>
                                    <option value="10">10 Meses</option>
                                    <option value="11">11 Meses</option>
                                    <option value="12">12 Meses</option>
                                    <option value="unlimited">Sin Límite</option>
                                </select>
                                <label for="periodo-tenant">Selecciona Periodo <sup style="color:red;">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3 mt-2">

                                <?php 

                                    $urlUser = "usuarios?select=id_usuario,nombres_usuario,alias_usuario&orderBy=id_usuario&orderMode=ASC";
                                    $methodUser = "GET";
                                    $fieldsUser = array();
                                    $tokenUser = TemplateController::tokenSet();
                                    $usuarios = CurlController::requestSunat($urlUser, $methodUser, $fieldsUser, $tokenUser)->response->data;

                                ?>

                                <select name="usuario-tenant" id="usuario-tenant" class="form-select select2" required>

                                <?php foreach ($usuarios as $key => $value): ?>	

                                    <option value="<?php echo $value->id_usuario; ?>"><?php echo $value->nombres_usuario . ' - ' . $value->alias_usuario; ?></option>

                                <?php endforeach ?>

                                </select>
                                <label for="usuario-tenant">Asignar Usuario <sup style="color:red;">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <select name="metodo_pago-tenant" id="metodo_pago-tenant" class="form-select" required>
                                    <option value="">Selecciona Método</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Yape">Yape</option>
                                    <option value="Plin">Plin</option>
                                    <option value="Paypal">Paypal</option>
                                </select>
                                <label for="metodo_pago-tenant">Método Pago <sup style="color:red;">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control" id="comprobante-tenant" name="comprobante-tenant" placeholder="N. Operación">
                                <label for="comprobante-tenant">N. Operación</label>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="card-footer">
                    
            <div class="col-md-8 offset-md-2">

                <div class="form-group mt-3">

                    <a href="/tenants" class="btn btn-default border text-left">Regresar</a>
                    
                    <button type="submit" class="btn btn-primary float-right saveBtn">Guardar</button>

                </div>

            </div>

        </div>

    </form>

</div>
<!-- END : Table with toolbar -->
<script src="views/assets/custom/forms/forms.js"></script>