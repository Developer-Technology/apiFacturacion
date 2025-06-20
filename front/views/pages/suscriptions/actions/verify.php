<?php 
	
	if(isset($routesArray[3])) {
		
		$security = explode("~",base64_decode($routesArray[3]));
	
		if($security[1] == $_SESSION["user"]->token_usuario) {

			$select = "*";

			$url = "suscripciones?select=".$select."&linkTo=id_suscripcion&equalTo=".$security[0];
			$method = "GET";
			$fields = array();
            $token = TemplateController::tokenSet();

			$response = CurlController::requestSunat($url,$method,$fields, $token);
			
			if($response->response->status == 200) {

				$suscripcion = $response->response->data[0];

                if($suscripcion->adjunto_suscripcion != '' || $suscripcion->adjunto_suscripcion != NULL) {

                    if(!empty($_SESSION["empresa"])) {

                        echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("error", "Ya se cargó la constancia de pago", "/suscriptions");
                        </script>';

                    }

                }

			} else {

				echo '<script>
                        window.location = "/suscriptions";
                    </script>';

			}

		} else {

			echo '<script>
                    window.location = "/suscriptions";
                </script>';

		}

	}

?>

<div class="card">
        
    <form method="post" class="needs-validation" novalidate enctype="multipart/form-data" autocomplete="off">

        <input type="hidden" value="<?php echo $suscripcion->id_suscripcion ?>" name="idSuscription">

        <div class="card-body">

            <?php

                require_once "controllers/suscriptions.controller.php";

                $create = new SuscriptionsController();
                $create -> update($suscripcion->id_suscripcion);
                
            ?>

            <p>Realiza la carga tu comprobante en formato <b>".png"</b> o <b>".jpg"</b>.</p>

            <div class="row">

                <!--=====================================
                Monto
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="number" 
                        class="form-control"
                        name="pay-monto"
                        placeholder="Monto"
                        value="<?php echo $suscripcion->monto_pago_suscripcion ?>"
                        readonly>

                        <label>Monto <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Comprobante
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="pay-comprobante"
                        placeholder="N. Operación"
                        value="<?php echo $suscripcion->comprobante_suscripcion ?>"
                        readonly>

                        <label>N. Operación <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Fecha Pago
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="date" 
                        class="form-control"
                        name="pay-fechaPago"
                        placeholder="Fecha Pago"
                        value="<?php echo $suscripcion->fecha_pago_suscripcion ?>"
                        readonly>

                        <label>Fecha Pago <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Medio de Pago
                ======================================-->
                <div class="col-md-2">
                    <div class="form-floating mb-3 mt-2">
                        <select name="pay-metodo" id="pay-metodo" class="form-select" readonly>

                            <?php if($suscripcion->medio_pago_suscripcion == "Transferencia"): ?>
                                <option value="">Selecciona Método</option>
                                <option value="Transferencia" selected>Transferencia</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                                <option value="Paypal">Paypal</option>
                            <?php elseif ($suscripcion->medio_pago_suscripcion == "Yape"): ?>
                                <option value="">Selecciona Método</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Yape" selected>Yape</option>
                                <option value="Plin">Plin</option>
                                <option value="Paypal">Paypal</option>
                            <?php elseif ($suscripcion->medio_pago_suscripcion == "Plin"): ?>
                                <option value="">Selecciona Método</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin" selected>Plin</option>
                                <option value="Paypal">Paypal</option>
                            <?php elseif ($suscripcion->medio_pago_suscripcion == "Paypal"): ?>
                                <option value="">Selecciona Método</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                                <option value="Paypal" selected>Paypal</option>
                            <?php else: ?>
                                <option value="">Selecciona Método</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                                <option value="Paypal">Paypal</option>
                            <?php endif ?>

                        </select>
                        <label for="pay-metodo">Método Pago <sup style="color:red;">*</sup></label>
                    </div>
                </div>

                <?php if (!empty($_SESSION["admin"]) && empty($_SESSION["empresa"])): ?>
                    
                    <!--=====================================
                    Estado
                    ======================================-->
                    <div class="col-md-2">
                        <div class="form-floating mb-3 mt-2">
                            <select name="estado-pay" id="estado-pay" class="form-select" required>
                                <option value="">Selecciona Estado</option>
                                <option value="pagado">Aprobar</option>
                                <option value="rechazado">Rechazar</option>
                            </select>
                            <label for="estado-pay">Cambiar Estado <sup style="color:red;">*</sup></label>
                        </div>
                    </div>

                    <!--=====================================
                    Proxima Facturacion
                    ======================================-->
                    <div class="col-md-2">
                        <div class="form-floating mb-3 mt-2">
                            <select name="periodo-pay" id="periodo-pay" class="form-select" required>
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
                            <label for="periodo-pay">Selecciona Periodo <sup style="color:red;">*</sup></label>
                        </div>
                    </div>

                <?php endif ?>

                <p class="text-muted">El pago se aprobará en 1 o 2 días hábiles.</p>
                
            </div>

        </div>

        <div class="card-footer">
                    
            <div class="col-md-8 offset-md-2">

                <div class="form-group mt-3">

                    <a href="/suscriptions" class="btn btn-default border text-left">Regresar</a>
                    
                    <button type="submit" class="btn btn-primary float-right saveBtn">Guardar</button>

                </div>

            </div>

        </div>

    </form>

</div>