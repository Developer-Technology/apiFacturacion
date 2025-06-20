<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/
	
	if(isset($routesArray[3])) {
		
		$security = explode("~",base64_decode($routesArray[3]));
	
		if($security[1] == $_SESSION["user"]->token_usuario) {

			$select = "*";

			$url = "relations?rel=empresas,planes&type=empresa,plan&select=".$select."&linkTo=id_empresa&equalTo=".$security[0];;
			$method = "GET";
			$fields = array();
            $token = TemplateController::tokenSet();

			$response = CurlController::requestSunat($url,$method,$fields, $token);
			
			if($response->response->status == 200) {

				$tenant = $response->response->data[0];

			} else {

				echo '<script>
                        window.location = "/tenants";
                    </script>';

			}

		} else {

			echo '<script>
                    window.location = "/tenants";
                </script>';

		}

	}

?>

<!-- Table with toolbar -->
<div class="card">
    
    <form method="post" class="needs-validation" novalidate autcomplete="off">

        <input type="hidden" value="11" id="doc-long">
        <input type="hidden" value="<?php echo $tenant->id_empresa ?>" name="idTenant">

        <div class="card-body">

            <?php

                require_once "controllers/tenants.controller.php";

                $create = new TenantsController();
                $create -> edit($tenant->id_empresa);

            ?>

            <div class="row">

                <div class="col-md-12">

                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control documento" id="doc-person" name="ruc-tenant" placeholder="RUC" maxlength="11" pattern="[0-9]{1,}" value="<?php echo $tenant->ruc_empresa; ?>" required disabled>
                                <label for="doc-person">RUC <span id="estado-ruc"></span> <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary mb-3" style="width: 100%;" disabled><i class="fa fa-search"></i></button>
                        </div>

                        <div class="col-md-5">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control razon-social" id="name-tenant" name="name-tenant" placeholder="Razón Social" required onKeyUp="this.value=this.value.toUpperCase();" value="<?php echo $tenant->razon_social_empresa; ?>" disabled>
                                <label for="name-tenant">Razón Social <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control nombre-comercial" id="nc-tenant" name="nc-tenant" placeholder="Nombre Comercial" required onKeyUp="this.value=this.value.toUpperCase();" value="<?php echo $tenant->nombre_comercial_empresa; ?>">
                                <label for="nc-tenant">Nombre Comercial <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control domicilio" id="address-tenant" name="address-tenant" placeholder="Dirección" required onKeyUp="this.value=this.value.toUpperCase();" value="<?php echo $tenant->direccion_empresa; ?>">
                                <label for="address-tenant">Dirección <span id="habido-ruc"></span> <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control departamento" id="dep-tenant" name="dep-tenant" placeholder="Departamento" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();" value="<?php echo $tenant->departamento_empresa; ?>">
                                <label for="dep-tenant">Departamento <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control provincia" id="pro-tenant" name="pro-tenant" placeholder="Provincia" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();" value="<?php echo $tenant->provincia_empresa; ?>">
                                <label for="pro-tenant">Provincia <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control distrito" id="dis-tenant" name="dis-tenant" placeholder="Distrito" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();" value="<?php echo $tenant->distrito_empresa; ?>">
                                <label for="dis-tenant">Distrito <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control ubigeo" id="ubi-tenant" name="ubi-tenant" placeholder="Ubigeo" pattern="[0-9]{1,}" onchange="validateJS(event, 'numbers')" required value="<?php echo $tenant->ubigeo_empresa; ?>">
                                <label for="ubi-tenant">Ubigeo <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="text" class="form-control phone-tenant" id="phone-tenant" name="phone-tenant" placeholder="Teléfono" pattern="[-\\(\\)\\0-9 ]{1,}" onchange="validateJS(event, 'phone')" required value="<?php echo $tenant->telefono_empresa; ?>">
                                <label for="phone-tenant">Teléfono <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3 mt-2">
                                <input type="email" class="form-control" id="email-tenant" name="email-tenant" placeholder="Correo Electrónico" required value="<?php echo $tenant->email_empresa; ?>">
                                <label for="email-tenant">Correo Electrónico <sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-floating mb-3 mt-2">
                                <input type="date" class="form-control" id="pfact-tenant" name="pfact-tenant" placeholder="Correo Electrónico" value="<?php echo $tenant->proxima_facturacion_empresa; ?>">
                                <label for="pfact-tenant">Prox. Facturación <sup class="text-danger">*</sup></label>
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

                                    <?php if ($value->id_plan == $tenant->id_plan_empresa): ?>

                                        <option value="<?php echo $tenant->id_plan_empresa ?>" selected><?php echo $tenant->nombre_plan ?></option>

                                    <?php else: ?>

                                        <option value="<?php echo $value->id_plan ?>" selected><?php echo $value->nombre_plan ?></option>
                                        
                                    <?php endif ?>

                                <?php endforeach ?>

                                </select>
                                <label for="plan-tenant">Selecciona Plan <sup style="color:red;">*</sup></label>
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

                                <select name="usuario-tenant" id="usuario-tenant" class="form-select select2" required disabled>

                                <?php foreach ($usuarios as $key => $value): ?>	

                                    <option value="<?php echo $value->id_usuario; ?>"><?php echo $value->nombres_usuario . ' - ' . $value->alias_usuario; ?></option>

                                <?php endforeach ?>

                                </select>
                                <label for="usuario-tenant">Asignar Usuario <sup style="color:red;">*</sup></label>
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