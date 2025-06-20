<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

require_once "controllers/tenants.controller.php";

if (empty($_SESSION["empresa"])):

?>

<?php
    
    /* Obtenemos los datos de Paypal */
    foreach (json_decode($dataSett->paypal_configuracion) as $key => $itemPaypal) {

        if($itemPaypal->client_id != '') {
         
            echo '<script src="https://www.paypal.com/sdk/js?client-id=' . $itemPaypal->client_id . '&currency=USD"></script>';
            
        }

    }

    /* Obtenemos los datos de Culqi */
    foreach (json_decode($dataSett->culqi_configuracion) as $key => $itemCulqi) {

        if($itemCulqi->public_key && $itemCulqi->secret_key != '') {
         
            echo '<script src="https://checkout.culqi.com/js/v3"></script>';
            $pkC = $itemCulqi->public_key;
            $skC = $itemCulqi->secret_key;
            
        }

    }

    /* Consultamos el tipo de cambio */
    $url = "exchange/consult";
    $method = "POST";
    $data = array();

    $fields = $data;
    $token = '0312b11cfe3df2ca85728026f8a81da8f53110f6e828030cce3c9a1a8dc6f1bf';

    $responseExchange = CurlController::requestSunat($url, $method, $fields, $token);

    if ($responseExchange->response->success == true) {

        $tC = $responseExchange->response->data->compra;
        $tV = $responseExchange->response->data->venta;

    } else {

        $tC = 1;
        $tV = 1;

    }
    
?>

<div id="root" class="root mn--max hd--expanded mn--sticky">

    <section id="content" class="content">

        <div class="content__boxed">
            <div class="content__wrap">

                <div class="row">

                    <div class="col-md-12">

                        <form method="post" class="needs-validation" novalidate onsubmit="return checkout('new')">

                            <input type="hidden" id="idUser" value="<?php echo $_SESSION["user"]->id_usuario ?>">
                            
                            <div class="d-flex flex-column align-items-streth align-items-md-center justify-content-center">
                                <div class="card">

                                    <div class="card-header">
                                        <b>Tipo de cambio:</b> Compra <?php echo $tC ?> - Venta <?php echo $tV ?>
                                    </div>

                                    <div class="card-body d-md-inline-flex justify-content-center gap-4 p-4">

                                        <!-- Simple Pricing table - Personal -->
                                        <?php 

                                            $url = "planes?select=id_plan,nombre_plan,precio_plan,contiene_plan&orderBy=precio_plan&orderMode=ASC";
                                            $method = "GET";
                                            $fields = array();
                                            $token = TemplateController::tokenSet();

                                            $planes = CurlController::requestSunat($url, $method, $fields,$token)->response->data;

                                        ?>
                                        <?php foreach ($planes as $key => $value): ?>

                                            <div class="w-md-200px">
                                                <div class="text-center">
                                                    <h5><?php echo TemplateController::capitalize($value->nombre_plan) ?></h5>
                                                    <div class="mt-4">
                                                        <span class="h3 m-0">S/</span>
                                                        <span class="display-2 h4 fw-bold"><?php echo $value->precio_plan ?></span>
                                                        <span>/Mes</span>
                                                    </div>
                                                </div>

                                                <ul class="list-group list-group-borderless mt-4">

                                                    <?php
                                                    
                                                        $contPlan = $value->contiene_plan;
                                                        $jsonContPlan = json_decode($contPlan);

                                                        foreach ($jsonContPlan as $key => $itemPlan) {

                                                            echo '<li class="list-group-item">
                                                                    <i class=" demo-pli-yes fs-5 me-2 text-primary"></i>
                                                                    Consultas <b>' . TemplateController::capitalize($itemPlan->consultas) . '</b>
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <i class=" demo-pli-yes fs-5 me-2 text-primary"></i>
                                                                    Documentos <b>' . TemplateController::capitalize($itemPlan->documentos) . '</b>
                                                                </li>';

                                                        }
                                                    
                                                    ?>

                                                </ul>

                                                <div class="d-grid mt-4">
                                                    <button type="button" class="btn btn-outline-primary waves-effect" onclick="funcGet(event, 'planes', '<?php echo $value->id_plan ?>', 'id_plan')">Seleccionar <span class="idText text_<?php echo $value->id_plan ?>" style="display: none;"><br><small class="text-muted">(Seleccionado)</small></span></button>
                                                </div>
                                            </div>
                                            <hr class="d-md-none my-5">
                                            
                                        <?php endforeach ?>
                                        
                                        <input type="hidden" id="plan-tenant" name="plan-tenant" value="0">
                                        <input type="hidden" id="plan-sale" name="plan-sale" value="0">
                                        <input type="hidden" id="mon-sale" name="mon-sale" value="0">
                                        <input type="hidden" id="plan-name" name="plan-sale" value="0">
                                        <input type="hidden" id="tipo-cambio" name="tipo-cambio" value="<?php echo $tC ?>">
                                        <!-- END : Simple Pricing table - Personal -->

                                    </div>

                                    <div class="row button_next mb-3" style="padding: 10px; display: none;">

                                        <div class="col-md-8"></div>

                                        <div class="col-md-4">

                                            <div class="accordion" id="_dm-defaultAccordion">
                                                <div class="accordion-item border">
                                                    <div class="accordion-header" id="_dm-defAccHeadingOne">
                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#_dm-defAccCollapseOne" aria-expanded="true" aria-controls="_dm-defAccCollapseOne">
                                                            Procesar Compra
                                                        </button>
                                                    </div>
                                                    <div id="_dm-defAccCollapseOne" class="accordion-collapse collapse show" aria-labelledby="_dm-defAccHeadingOne" data-bs-parent="#_dm-defaultAccordion">
                                                        <div class="accordion-body">
                                                            <p class="text-muted namePlan"></p>
                                                            <h5 class="totalOrder" total="0">0</h5>

                                                            <div class="form-group" id="payMent">

                                                                <div class="ps-radio">

                                                                    <input 
                                                                    type="radio" 
                                                                    id="pay-paypal" 
                                                                    name="payment-method"
                                                                    value="paypal" 
                                                                    onchange="changeMethodPaid(event)"
                                                                    checked>

                                                                    <label for="pay-paypal">Pagar con PayPal</label>

                                                                </div>

                                                            </div>

                                                            <button type="submit" class="btn btn-primary mt-4">Continuar</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item border">
                                                    <div class="accordion-header" id="_dm-defAccHeadingTwo">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#_dm-defAccCollapseTwo" aria-expanded="false" aria-controls="_dm-defAccCollapseTwo">
                                                            Ya tengo un ID de compra para este plan
                                                        </button>
                                                    </div>
                                                    <div id="_dm-defAccCollapseTwo" class="accordion-collapse collapse" aria-labelledby="_dm-defAccHeadingTwo" data-bs-parent="#_dm-defaultAccordion">
                                                        <div class="accordion-body">
                                                            <p class="text-muted namePlan"></p>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" placeholder="Ingresa el ID para continuar" id="idSale"autocomplete="off">
                                                                <button class="btn btn-info" type="button" onclick="gotoCreate()">Continuar</button>
                                                            </div>
                                                            <p class="mt-2 text-muted"><i><small>Nota: Si no recueda el ID de compra, contáctate con el administrador del servicio.</small></i></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>

                                        </div>

                                    </div>

                                    <div class="card-footer">
                                        <div class="row">

                                            <div class="col-md-12 text-center">
                                                <a class="btn btn-danger" style="margin-right: 15px; margin-left: 15px;" href="/">Regresar</a>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </section>

</div>

<?php else: ?>

    <script>
        window.location = "";
    </script>

<?php endif ?>

<script src="views/assets/custom/forms/forms.js"></script>