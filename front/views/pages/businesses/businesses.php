<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

require_once "controllers/tenants.controller.php";

if (empty($_SESSION["empresa"])):

    /* Se valida que venga un dato */
    if (isset($routesArray[2])) {

        $security = explode("~", base64_decode($routesArray[2]));

        /* Se valida que la sesion de usuario no este caduco */
        if ($security[1] == $_SESSION["user"]->token_usuario) {

            $select = "*";

            $url = "ventas?select=" . $select . "&linkTo=trans_venta,id_usuario_venta&equalTo=" . $security[2] . "," . $security[3];
            $method = "GET";
            $fields = array();
            $token = TemplateController::tokenSet();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

            if ($response->response->status == 200) {

                $ventas = $response->response->data[0];

                /* Se valida que el ID de venta no tenga empresa relacionada */
                if($ventas->id_empresa_venta != 0) {

                    echo '<script>
                            fncSweetAlert("error", "This sale id is already associated with a company, please make another purchase", "/cart");
                        </script>';

                }

                if($security[0] != $ventas->id_plan_venta) {

                    echo '<script>
                            fncSweetAlert("error", "The sales id entered does not correspond to the selected plan", "/cart");
                        </script>';

                }

            } else {

                echo '<script>
                        window.location = "/cart";
                    </script>';

            }

        } else {

            echo '<script>
                    window.location = "/cart";
                </script>';

        }

    } else {

        echo '<script>
                window.location = "/cart";
            </script>';

    }

    ?>

	<div id="root" class="root mn--max hd--expanded mn--sticky">

	    <section id="content" class="content">

	        <div class="content__boxed">
	            <div class="content__wrap">

	                <div class="row">

	                    <div class="col-md-3 mb-3">

	                        <!-- Basic card -->
	                        <div class="card h-100">
	                            <div style="background-color: #25476a; height: 125px; border-radius: 10px;"></div>
	                            <div class="position-relative p-3">
	                                <div class="position-absolute top-0 start-50 translate-middle text-white">
	                                    <img class="img-lg rounded-circle border border-white border-3" src="<?php echo $avatarUser ?>">
	                                </div>
	                            </div>

	                            <div class="text-center mt-4">
	                                <a class="h5 btn-link pointer"><?php echo $dataUsers->nombres_usuario; ?></a>
	                                <p class="text-opacity-75 mb-0"><?php echo $dataUsers->email_usuario; ?></p>
	                            </div>

	                            <div class="card-body">
	                                <div class="list-group list-group-borderless">
	                                    <a class="list-group-item list-group-item-action pointer"><i class="fa fa-phone fs-4 me-2"></i> <b>Tel&eacute;fono: </b><?php echo $dataUsers->telefono_usuario; ?></a>
	                                    <a class="list-group-item list-group-item-action pointer"><i class="fa fa-calendar-o fs-4 me-2"></i> <b>Registrado: </b><?php echo TemplateController::fechaEsShort($dataUsers->creado_usuario); ?></a>
	                                    <a class="list-group-item list-group-item-action pointer"><i class="fa fa-user-o fs-4 me-2"></i> <b>Alias: </b><?php echo $dataUsers->alias_usuario; ?></a>
	                                </div>
	                            </div>

	                            <div class="d-grid p-2">
	                                <a href="/logout" class="btn btn-danger">Cerrar Sesión</a>
	                            </div>
	                        </div>
	                        <!-- END : Basic card -->

	                    </div>

	                    <div class="col-md-9 mb-3">

	                        <!-- Card with header -->
	                        <form method="post" class="card h-100 needs-validation" novalidate autocomplete="off">

	                            <input type="hidden" value="11" id="doc-long">

	                            <div class="card-header">
	                                <span style="font-size: 16px"><?php echo $dataSett->nombre_sistema_configuracion ?></span>
	                            </div>

	                            <div class="card-body">

	                                <div class="row">

	                                    <div class="col-md-12">

	                                        <div class="row">

	                                            <div class="col-md-3">
	                                                <div class="form-floating mb-3">
	                                                    <input type="text" class="form-control documento" id="doc-person" name="ruc-tenant" placeholder="RUC" maxlength="11" pattern="[0-9]{1,}" onchange="validateRepeat(event,'text','empresas','ruc_empresa')" required>
	                                                    <label for="doc-person">RUC <span id="estado-ruc"></span> <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-1">
	                                                <button type="button" class="btn btn-primary mb-3" onclick="sendAjaxConsult('ruc')" style="width: 100%;"><i class="fa fa-search"></i></button>
	                                            </div>

	                                            <div class="col-md-8">
	                                                <div class="form-floating mb-3">
	                                                    <input type="text" class="form-control razon-social" id="name-tenant" name="name-tenant" placeholder="Razón Social" required onKeyUp="this.value=this.value.toUpperCase();">
	                                                    <label for="name-tenant">Razón Social <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-4">
	                                                <div class="form-floating mb-3 mt-2">
	                                                    <input type="text" class="form-control nombre-comercial" id="nc-tenant" name="nc-tenant" placeholder="Nombre Comercial" required onKeyUp="this.value=this.value.toUpperCase();">
	                                                    <label for="nc-tenant">Nombre Comercial <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-8">
	                                                <div class="form-floating mb-3 mt-2">
	                                                    <input type="text" class="form-control domicilio" id="address-tenant" name="address-tenant" placeholder="Dirección" required onKeyUp="this.value=this.value.toUpperCase();">
	                                                    <label for="address-tenant">Dirección <span id="habido-ruc"></span> <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-3">
	                                                <div class="form-floating mb-3 mt-2">
	                                                    <input type="text" class="form-control departamento" id="dep-tenant" name="dep-tenant" placeholder="Departamento" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
	                                                    <label for="dep-tenant">Departamento <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-3">
	                                                <div class="form-floating mb-3 mt-2">
	                                                    <input type="text" class="form-control provincia" id="pro-tenant" name="pro-tenant" placeholder="Provincia" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
	                                                    <label for="pro-tenant">Provincia <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-3">
	                                                <div class="form-floating mb-3 mt-2">
	                                                    <input type="text" class="form-control distrito" id="dis-tenant" name="dis-tenant" placeholder="Distrito" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}" onchange="validateJS(event,'text')" required onKeyUp="this.value=this.value.toUpperCase();">
	                                                    <label for="dis-tenant">Distrito <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-3">
	                                                <div class="form-floating mb-3 mt-2">
	                                                    <input type="text" class="form-control ubigeo" id="ubi-tenant" name="ubi-tenant" placeholder="Ubigeo" pattern="[0-9]{1,}" onchange="validateJS(event, 'numbers')" required>
	                                                    <label for="ubi-tenant">Ubigeo <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-4">
	                                                <div class="form-floating mb-3 mt-2">
	                                                    <input type="text" class="form-control phone-tenant" id="phone-tenant" name="phone-tenant" placeholder="Teléfono" pattern="[-\\(\\)\\0-9 ]{1,}" onchange="validateJS(event, 'phone')" required>
	                                                    <label for="phone-tenant">Teléfono <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <div class="col-md-8">
	                                                <div class="form-floating mb-3 mt-2">
	                                                    <input type="email" class="form-control" id="email-tenant" name="email-tenant" placeholder="Correo Electrónico" required>
	                                                    <label for="email-tenant">Correo Electrónico <sup class="text-danger">*</sup></label>
	                                                </div>
	                                            </div>

	                                            <input type="hidden" value="<?php echo $ventas->id_plan_venta ?>" name="plan-tenant">
												<input type="hidden" value="1" name="periodo-tenant">
												<input type="hidden" value="<?php echo $ventas->metodo_venta ?>" name="metodo_pago-tenant">
												<input type="hidden" value="<?php echo $ventas->trans_venta ?>" name="comprobante-tenant">

	                                        </div>

	                                    </div>

	                                </div>

	                            </div>

	                            <?php

                                    $create = new TenantsController();
                                    $create->create($dataUsers->id_empresa_usuario, $security[0], $security[2]);

                                ?>

	                            <div class="card-footer text-center">
	                                <a class="btn btn-danger" href="/cart">Regresar</a>
	                                <button type="submit" class="btn btn-primary">Guardar</button>
	                            </div>

	                        </form>
	                        <!-- END : Card with header -->

	                    </div>

	                </div>

	            </div>
	        </div>

	    </section>

	</div>

	<?php else: ?>

<?php

if (isset($routesArray[2])) {

    if ($routesArray[2] == "general") {

        $nameBread = 'Datos Generales';

    } elseif ($routesArray[2] == "logo") {

        $nameBread = 'Cargar Logo';

    } elseif ($routesArray[2] == "certificate") {

        $nameBread = 'Configurar Certificado';

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
                <li class="breadcrumb-item">Consultas</li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $nameBread ?></li>

            </ol>
        </nav>
        <!-- END : Breadcrumb -->

        <h1 class="page-title mb-0 mt-2">
            <?php echo $nameBread ?>
            <button class="btn btn-default border" data-toggle="modal" data-target="#viewToken" style="position: absolute; right: 20px; top: 12%;"><i class="fa fa-eye"></i> <span class="vr"></span> Mis Credenciales</button>
        </h1>
        <p class="lead">En esta sección podrás configurar los parámetros de tu empresa.</p>

    </div>

</div>

<div class="content__boxed">

    <div class="content__wrap">

        <?php

if (isset($routesArray[2])) {

    if ($routesArray[2] == "general" ||
        $routesArray[2] == "logo" ||
        $routesArray[2] == "certificate"
    ) {

        include $routesArray[2] . "/" . $routesArray[2] . ".php";

    } else {

        echo '<script>
                        window.location = "/businesses/general";
                    </script>';

    }

} else {

    echo '<script>
                        window.location = "/businesses/general";
                    </script>';

}

?>

    </div>

</div>

<?php endif?>

<script src="views/assets/custom/forms/forms.js"></script>