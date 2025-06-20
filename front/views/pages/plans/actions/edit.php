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

			$url = "planes?select=".$select."&linkTo=id_plan&equalTo=".$security[0];;
			$method = "GET";
			$fields = array();
            $token = TemplateController::tokenSet();

			$response = CurlController::requestSunat($url,$method,$fields, $token);
			
			if($response->response->status == 200) {

				$plan = $response->response->data[0];

                /*=============================================
                Obtenemos lo que contiene el plan
                =============================================*/
                $jsonPlan = $plan->contiene_plan;
                $arrayPlan = json_decode($jsonPlan, true);
                foreach ($arrayPlan as $elementPlan) {

                    $totalCons = $elementPlan["consultas"];
                    $totalDocs = $elementPlan["documentos"];

                }

			} else {

				echo '<script>
                        window.location = "/plans";
                    </script>';

			}

		} else {

			echo '<script>
                    window.location = "/plans";
                </script>';

		}

	}

?>

<!-- Table with toolbar -->
<div class="card">
    
    <form method="post" class="needs-validation" novalidate autocomplete="off">

        <input type="hidden" value="<?php echo $plan->id_plan ?>" name="idPlan">

        <div class="card-body">

            <?php

                require_once "controllers/plans.controller.php";

                $create = new PlansController();
                $create -> edit($plan->id_plan);

            ?>

            <div class="row">

                <!--=====================================
                Nombre
                ======================================-->
                <div class="col-md-6">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}"
                        onchange="validateJS(event,'text')"
                        name="name-plan"
                        placeholder="Nombre"
                        value="<?php echo $plan->nombre_plan ?>"
                        required>

                        <label>Nombre <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Precio
                ======================================-->
                <div class="col-md-2">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="number"
                        class="form-control"
                        placeholder="Precio"
                        name="price-plan"
                        value="<?php echo $plan->precio_plan ?>"
                        required>

                        <label for="">Precio <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <!--=====================================
                Contenido
                ======================================-->
                <div class="col-md-2">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="number"
                        class="form-control"
                        placeholder="Consultas"
                        name="cons-plan"
                        value="<?php echo $totalCons ?>"
                        required>

                        <label for="">Consultas <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <div class="col-md-2">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="number"
                        class="form-control"
                        placeholder="Documentos"
                        name="docs-plan"
                        value="<?php echo $totalDocs ?>"
                        required>

                        <label for="">Documentos <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <!--=====================================
                Descripción
                ======================================-->
                <div class="col-md-12">

                    <div class="form-group mt-2 mb-3">
                        
                        <label>Descripción <sup class="text-danger">*</sup></label>

                        <textarea
                        class="summernote"
                        name="description-plan"
                        required
                        ><?php echo $plan->descripcion_plan ?></textarea>

                        <div class="invalid-feedback">Este campo es obligatorio</div>

                    </div>

                </div>
                
            </div>

        </div>

        <div class="card-footer">
                    
            <div class="col-md-8 offset-md-2">

                <div class="form-group mt-3">

                    <a href="/plans" class="btn btn-default border text-left">Regresar</a>
                    
                    <button type="submit" class="btn btn-primary float-right saveBtn">Guardar</button>

                </div>

            </div>

        </div>

    </form>

</div>
<!-- END : Table with toolbar -->