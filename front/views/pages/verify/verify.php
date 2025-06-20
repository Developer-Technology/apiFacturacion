<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Controladores
=============================================*/
require_once "controllers/curl.controller.php";

if (isset($routesArray[2])) {

    echo '<script>
                matPreloader("on");
                fncSweetAlert("loading", "Cargando...", "");
            </script>';

    $security = explode("~", base64_decode($routesArray[2]));
    $verify = $security[0];

    /*=============================================
    Validamos que el usuario si exista
    =============================================*/
    $url = "usuarios?linkTo=email_usuario&equalTo=" . $verify . "&select=id_usuario,verificado_usuario";
    $method = "GET";
    $fields = array();
    $token = TemplateController::tokenSet();

    $item = CurlController::requestSunat($url, $method, $fields, $token);

    if (!empty($item)) {

        if ($item->response->success == true) {

            /*=============================================
            Verificamos si la cuenta no ha sido validada
            =============================================*/
            if ($item->response->data[0]->verificado_usuario != 1) {

                /*=============================================
                Actualizar el campo de verificación
                =============================================*/
                $url = "usuarios?id=" . $item->response->data[0]->id_usuario . "&nameId=id_usuario&token=no&except=verificado_usuario";
                $method = "PUT";
                $fields = "verificado_usuario=1";

                $verificationUser = CurlController::requestSunat($url, $method, $fields, $token);

                if ($verificationUser->response->success == true) {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("success", "Your account has been successfully verified, now you can login", "/");
                        </script>';

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("error", "Could not verify account", "/");
                        </script>';

                }

            } else {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncSweetAlert("error", "This account has already been verified, please login", "/");
                    </script>';

            }

        } else {

            echo '<div class="alert alert-error alert-bg alert-button alert-block show-code-action mb-4">
                    <h4 class="alert-title">Opps!</h4>
                    <p>No se pudo verificar la cuenta, el correo electrónico no existe</p>
                    <a class="btn btn-link btn-close pointer" aria-label="button">
                        <i class="close-icon"></i>
                    </a>
                </div>';

        }

    } else {

        echo '<script>
                fncFormatInputs();
                matPreloader("off");
                fncSweetAlert("close", "", "");
                fncSweetAlert("error", "Failed to verify account, email does not exist", "/");
            </script>';

    }

} else {

    echo '<script>
            fncFormatInputs();
            matPreloader("off");
            fncSweetAlert("close", "", "");
            fncSweetAlert("error", "You have not entered the account to verify", "/");
        </script>';

}