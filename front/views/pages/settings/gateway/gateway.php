<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

    /* Obtenemos los datos de Paypal */
    foreach (json_decode($dataSett->paypal_configuracion) as $key => $itemPaypal) {

        $clientId = $itemPaypal->client_id;
        $secret_key = $itemPaypal->secret_key;

    }

    /* Obtenemos los datos de Culqi */
    foreach (json_decode($dataSett->culqi_configuracion) as $key => $itemCulqi) {

        $pkC = $itemCulqi->public_key;
        $skC = $itemCulqi->secret_key;

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
            <button class="nav-link" type="button">Facturación</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/gateway','_self');">
            <button class="nav-link active" type="button">Pasarelas De Pago</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <div class="tab-pane fade show active">
            <h5 class="card-title">Pasarelas De Pago</h5>
            <p>Requerido para los pagos de planes</p>
        </div>

        <form method="post" class="needs-validation" novalidate autocomplete="off">

            <div class="accordion" id="_dm-defaultAccordion">

                <div class="accordion-item border">
                    <div class="accordion-header" id="_dm-defAccHeadingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#_dm-defAccCollapseOne" aria-expanded="true" aria-controls="_dm-defAccCollapseOne">
                            PayPal
                        </button>
                    </div>
                    <div id="_dm-defAccCollapseOne" class="accordion-collapse collapse show" aria-labelledby="_dm-defAccHeadingOne" data-bs-parent="#_dm-defaultAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mt-2 mb-3">
                                        <input type="text" class="form-control" name="client_id-paypal" placeholder="Client ID" value="<?php echo $clientId ?>">
                                        <label for="">Client ID</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-2 mb-3">
                                        <input type="text" class="form-control" name="secret_key-paypal" placeholder="Secret Key" value="<?php echo $secret_key ?>">
                                        <label for="">Secret Key</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item border hidden">
                    <div class="accordion-header" id="_dm-defAccHeadingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#_dm-defAccCollapseTwo" aria-expanded="false" aria-controls="_dm-defAccCollapseTwo">
                            Culqi
                        </button>
                    </div>
                    <div id="_dm-defAccCollapseTwo" class="accordion-collapse collapse" aria-labelledby="_dm-defAccHeadingTwo" data-bs-parent="#_dm-defaultAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mt-2 mb-3">
                                        <input type="text" class="form-control" name="public_key-culqi" placeholder="Public Key" value="<?php echo $pkC ?>">
                                        <label for="">Public Key</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-2 mb-3">
                                        <input type="text" class="form-control" name="secret_key-culqi" placeholder="Secret Key" value="<?php echo $skC ?>">
                                        <label for="">Secret Key</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php

                /*=============================================
                Controladores
                =============================================*/
                require_once "controllers/settings.controller.php";

                $edit = new SettingsController();
                $edit->editGateway();

            ?>

            <div class="col-md-12 mt-4 text-center">

                <a class="btn btn-danger" href="/settings/gateway">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>

            </div>

        </form>

    </div>

</div>