<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class SummariesController
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
            $url = "summary/send";
            $method = "POST";

            $items = array();			
					
            for($i = 0; $i < $_POST["inputResumen"]; $i++){

                $items[$i] = (object)["tipodoc"=>"03","serie"=>trim($_POST["item-serie-product_".$i]),"correlativo"=>trim($_POST["item-numero-product_".$i]),"condicion"=>"1","moneda"=>trim($_POST["item-moneda-product_".$i]),"importe_total"=>trim($_POST["item-total-product_".$i]),"op_gravadas"=>trim($_POST["item-gravada-product_".$i]),"op_exoneradas"=>trim($_POST["item-exonerada-product_".$i]),"op_inafectas"=>trim($_POST["item-inafecta-product_".$i]),"op_gratuitas"=>trim($_POST["item-gratuita-product_".$i]),"igv_total"=>trim($_POST["item-igv-product_".$i]),"tipo_total"=>trim($_POST["item-tipototal-product_".$i]),"codeAfect"=>trim($_POST["item-codafectacion-product_".$i]),"nameAfect"=>trim($_POST["item-nomafectacion-product_".$i]),"tipoAfect"=>trim($_POST["item-tipoafectacion-product_".$i]),"coddoc"=>trim($_POST["item-tipocliente-product_".$i]),"numdoc"=>trim($_POST["item-doccliente-product_".$i])];

            }

            $fields = array(
                "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
                "cabecera" => array(
                    "tipodoc" => "RC",
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
                        fncSweetAlert("success", "' . $response->response->message . '", "/summary");
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
    public function sendSunat($idSum)
    {
        
        session_start();

        $token = TemplateController::tokenSet();

        /*=============================================
        Obtenemos la data del documento
        =============================================*/
        $urlDesp = "summaries?select=*&linkTo=id_summary&equalTo=" . $idSum;
        $methodDesp = "GET";
        $fieldsDesp = array();
        $dataDesp = CurlController::requestSunat($urlDesp, $methodDesp, $fieldsDesp, $token)->response->data[0];

        $fields = array(
            "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
            "cabecera" => array(
                "tipodoc" => $dataDesp->type_summary,
                "serie" => $dataDesp->serie_summary,
                "correlativo" => $dataDesp->number_summary
            )
        );

        $data = json_encode($fields);

        /*=============================================
        Enviamos el documento
        =============================================*/
        $tokenSend = $_SESSION["empresa"]->token_empresa;

        $urlSend = "summary/status";
        $methodSend = "POST";
        $dataSend = CurlController::requestSunat($urlSend, $methodSend, $data, $tokenSend);

        if ($dataSend->response->success == true) {

            $dataUpt = "status_sunat_summary=aceptado";

            $urlUpt = "summaries?id=" . $idSum . "&nameId=id_summary&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $methodUpt = "PUT";
            $fieldsUpt = $dataUpt;

            $responseUpt = CurlController::requestSunat($urlUpt, $methodUpt, $fieldsUpt, $token);

        } else {

            $dataUpt = "status_sunat_summary=rechazado";

            $urlUpt = "summaries?id=" . $idSum . "&nameId=id_summary&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $methodUpt = "PUT";
            $fieldsUpt = $dataUpt;

            $responseUpt = CurlController::requestSunat($urlUpt, $methodUpt, $fieldsUpt, $token);

        }
        echo json_encode($dataSend);

    }

}