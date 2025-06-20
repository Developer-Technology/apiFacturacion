<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class VoidedsController
{

    /*=============================================
    Crear
    =============================================*/
    public static function create()
    {

        if (isset($_POST['sum-serie'])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            /*=============================================
            Recibimos los datos
            =============================================*/
            $url = "voided/send";
            $method = "POST";

            $items = array();			
					
            for($i = 0; $i < $_POST["inputBaja"]; $i++){

                $items[$i] = (object)["tipodoc"=>trim($_POST["item-tipodoc-product_".$i]),"serie"=>trim($_POST["item-serie-product_".$i]),"correlativo"=>trim($_POST["item-numero-product_".$i]),"motivo"=>trim($_POST["item-motivo-product_".$i])];

            }

            $fields = array(
                "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
                "cabecera" => array(
                    "tipodoc" => "RA",
                    "serie" => date("Ymd"),
                    "correlativo" => $_POST["sum-number"],
                    "fechaEmision" => $_POST["sum-femision"],
                    "fechaEnvio" => $_POST["sum-fenvio"]
                ),
                "items" => $items
            );
            $token = $_SESSION["empresa"]->token_empresa;

            $data = json_encode($fields);

            $response = CurlController::requestSunat($url, $method, $data, $token);

            if ($response->response->success == true) {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncSweetAlert("success", "' . $response->response->message . '", "/voided");
                    </script>';

            } else {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncNotie(3, "' . $response->response->message . '");
                    </script>';

            }

        }

    }

    /*=============================================
    Enviar sunat
    =============================================*/
    public function sendSunat($idVoided)
    {
        
        session_start();

        $token = TemplateController::tokenSet();

        /*=============================================
        Obtenemos la data del documento
        =============================================*/
        $urlDesp = "voideds?select=*&linkTo=id_voided&equalTo=" . $idVoided;
        $methodDesp = "GET";
        $fieldsDesp = array();
        $dataDesp = CurlController::requestSunat($urlDesp, $methodDesp, $fieldsDesp, $token)->response->data[0];

        $fields = array(
            "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
            "cabecera" => array(
                "tipodoc" => $dataDesp->type_voided,
                "serie" => $dataDesp->serie_voided,
                "correlativo" => $dataDesp->number_voided
            )
        );

        $data = json_encode($fields);

        /*=============================================
        Enviamos el documento
        =============================================*/
        $tokenSend = $_SESSION["empresa"]->token_empresa;

        $urlSend = "voided/status";
        $methodSend = "POST";
        $dataSend = CurlController::requestSunat($urlSend, $methodSend, $data, $tokenSend);

        if ($dataSend->response->success == true) {

            $dataUpt = "status_sunat_voided=aceptado";

            $urlUpt = "voideds?id=" . $idVoided . "&nameId=id_voided&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $methodUpt = "PUT";
            $fieldsUpt = $dataUpt;

            $responseUpt = CurlController::requestSunat($urlUpt, $methodUpt, $fieldsUpt, $token);

        } else {

            $dataUpt = "status_sunat_voided=rechazado";

            $urlUpt = "voideds?id=" . $idVoided . "&nameId=id_voided&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $methodUpt = "PUT";
            $fieldsUpt = $dataUpt;

            $responseUpt = CurlController::requestSunat($urlUpt, $methodUpt, $fieldsUpt, $token);

        }
        echo json_encode($dataSend);

    }

}