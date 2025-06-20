<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class DespatchesController
{

    /*=============================================
    Crear guia remitente
    =============================================*/
    public static function create()
    {

        if (isset($_POST['desp-serie'])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            /*=============================================
            Recibimos los datos
            =============================================*/
            $url = "despatch/remitent";
            $method = "POST";

            $items = array();			
					
            for($i = 0; $i < $_POST["inputDetails"]; $i++){

                $items[$i] = (object)["unidad"=>trim($_POST["item-und-product_".$i]),"codProducto"=>trim($_POST["item-cod-product_".$i]),"descripcion"=>trim($_POST["item-desc-product_".$i]),"codProdSunat"=>"","cantidad"=>trim($_POST["item-cant-product_".$i])];

            }

            $motivo = explode("_", $_POST["desp-datMotivo"]);

            if($_POST["dep-datModo"] == "02") {

                $conductor = array(
                    "tipoDoc" => $_POST["desp-condTipo"],
                    "numDoc" => $_POST["desp-condDoc"],
                    "nombres" => $_POST["desp-condNom"],
                    "apellidos" => $_POST["desp-condApe"],
                    "licencia" => $_POST["desp-condLic"]
                );

            } else {

                $conductor = "";

            }

            $fields = array(
                "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
                "datosGuia" => array(
                    "serie" => $_POST["desp-serie"],
                    "correlativo" => $_POST["desp-number"],
                    "fechaEmision" => $_POST["desp-femision"],
                    "horaEmision" => $_POST["desp-hemision"] . ":00",
                    "tipoDoc" => "09",
                    "observacion" => "",
                    "docBaja" => array(
                        "nroDoc" => "",
                        "tipoDoc" => ""
                    ),
                    "relDoc" => array(
                        "nroDoc" => $_POST["desp-relNum"],
                        "tipoDoc" => $_POST["desp-relSerie"]
                    ),
                    "destinatario" => array(
                        "tipoDoc" => $_POST["desp-destTipo"],
                        "numDoc" => $_POST["desp-destDoc"],
                        "nombreRazon" => $_POST["desp-destRazon"]
                    ),
                    "terceros" => array(
                        "tipoDoc" => $_POST["desp-terTipo"],
                        "numDoc" => $_POST["desp-terDoc"],
                        "nombreRazon" => $_POST["desp-terRazon"]
                    ),
                    "transportista" => array(
                        "tipoDoc" =>  $_POST["desp-tranTipo"],
                        "numDoc" => $_POST["desp-tranDoc"],
                        "nombreRazon" => $_POST["desp-tranRazon"],
                        "placa" => $_POST["desp-tranPlaca"],
                        "mtc" => $_POST["desp-tranMtc"],
                        "tipoDocChofer" => $_POST["desp-tranTipoT"],
                        "numDocChofer" => $_POST["desp-tranDocT"]
                    ),
                    "conductor" => $conductor
                ),
                "datosEnvio" => array(
                    "codTraslado" => $motivo[0],
                    "descTraslado" => $motivo[1],
                    "pesoTotal" => $_POST["desp-datPeso"],
                    "uniPesoTotal" => $_POST["dep-datMedida"],
                    "numBultos" => $_POST["desp-datBultos"],
                    "modTraslado" => $_POST["dep-datModo"],
                    "fechaTraslado" => $_POST["desp-datTraslado"],
                    "numContenedor" => "",
                    "codPuerto" => "",
                    "llegada" => array(
                        "ubigeo" => $_POST["desp-datUbigeo"],
                        "direccion" => $_POST["desp-datDir"]
                    )
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
                        fncSweetAlert("success", "' . $response->response->message . '", "/despatch-remitent");
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
    Crear guia transportista
    =============================================*/
    public static function createTransport()
    {

        if (isset($_POST['desp-serie'])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            /*=============================================
            Recibimos los datos
            =============================================*/
            $url = "despatch/transport";
            $method = "POST";

            $items = array();			
					
            for($i = 0; $i < $_POST["inputDetails"]; $i++){

                $items[$i] = (object)["unidad"=>trim($_POST["item-und-product_".$i]),"codProducto"=>trim($_POST["item-cod-product_".$i]),"descripcion"=>trim($_POST["item-desc-product_".$i]),"codProdSunat"=>"","cantidad"=>trim($_POST["item-cant-product_".$i])];

            }

            $motivo = explode("_", $_POST["desp-datMotivo"]);

            $fields = array(
                "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
                "datosGuia" => array(
                    "serie" => $_POST["desp-serie"],
                    "correlativo" => $_POST["desp-number"],
                    "fechaEmision" => $_POST["desp-femision"],
                    "horaEmision" => $_POST["desp-hemision"] . ":00",
                    "tipoDoc" => "31",
                    "observacion" => "",
                    "destinatario" => array(
                        "tipoDoc" => $_POST["desp-destTipo"],
                        "numDoc" => $_POST["desp-destDoc"],
                        "nombreRazon" => $_POST["desp-destRazon"]
                    ),
                    "transportista" => array(
                        "tipoDoc" =>  $_POST["desp-tranTipo"],
                        "numDoc" => $_POST["desp-tranDoc"],
                        "nombreRazon" => $_POST["desp-tranRazon"],
                        "placa" => $_POST["desp-tranPlaca"],
                        "mtc" => $_POST["desp-tranMtc"],
                        "tipoDocChofer" => $_POST["desp-tranTipoT"],
                        "numDocChofer" => $_POST["desp-tranDocT"]
                    ),
                    "conductor" => array(
                        "tipoDoc" => $_POST["desp-condTipo"],
                        "numDoc" => $_POST["desp-condDoc"],
                        "nombres" => $_POST["desp-condNom"],
                        "apellidos" => $_POST["desp-condApe"],
                        "licencia" => $_POST["desp-condLic"]
                    )
                ),
                "datosEnvio" => array(
                    "codTraslado" => $motivo[0],
                    "descTraslado" => $motivo[1],
                    "pesoTotal" => $_POST["desp-datPeso"],
                    "uniPesoTotal" => $_POST["dep-datMedida"],
                    "numBultos" => $_POST["desp-datBultos"],
                    "fechaTraslado" => $_POST["desp-datTraslado"],
                    "llegada" => array(
                        "ubigeo" => $_POST["desp-datUbigeo"],
                        "direccion" => $_POST["desp-datDir"]
                    )
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
                        fncSweetAlert("success", "' . $response->response->message . '", "/despatch-transport");
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
    public function sendSunat($idDesp)
    {
        
        session_start();

        $token = TemplateController::tokenSet();

        /*=============================================
        Obtenemos la data del documento
        =============================================*/
        $urlDesp = "despatches?select=*&linkTo=id_despatch&equalTo=" . $idDesp;
        $methodDesp = "GET";
        $fieldsDesp = array();
        $dataDesp = CurlController::requestSunat($urlDesp, $methodDesp, $fieldsDesp, $token)->response->data[0];

        $fields = array(
            "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
            "comprobante" => array(
                "tipoDoc" => $dataDesp->type_despatch,
                "serie" => $dataDesp->serie_despatch,
                "correlativo" => $dataDesp->number_despatch
            )
        );

        $data = json_encode($fields);

        /*=============================================
        Enviamos el documento
        =============================================*/
        $tokenSend = $_SESSION["empresa"]->token_empresa;

        $urlSend = "despatch/send";
        $methodSend = "POST";
        $dataSend = CurlController::requestSunat($urlSend, $methodSend, $data, $tokenSend);

        if($dataSend->response->message != "It is necessary to be in production") {

            if ($dataSend->response->success == true) {

                $dataUpt = "status_sunat_despatch=aceptado";

                $urlUpt = "despatches?id=" . $idDesp . "&nameId=id_despatch&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                $methodUpt = "PUT";
                $fieldsUpt = $dataUpt;

                $responseUpt = CurlController::requestSunat($urlUpt, $methodUpt, $fieldsUpt, $token);

            } else {

                $dataUpt = "status_sunat_despatch=rechazado";

                $urlUpt = "despatches?id=" . $idDesp . "&nameId=id_despatch&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                $methodUpt = "PUT";
                $fieldsUpt = $dataUpt;

                $responseUpt = CurlController::requestSunat($urlUpt, $methodUpt, $fieldsUpt, $token);

            }

        }

        echo json_encode($dataSend);

    }

    public function sendSunatTrans($idDesp)
    {
        
        session_start();

        $token = TemplateController::tokenSet();

        /*=============================================
        Obtenemos la data del documento
        =============================================*/
        $urlDesp = "transportists?select=*&linkTo=id_transportist&equalTo=" . $idDesp;
        $methodDesp = "GET";
        $fieldsDesp = array();
        $dataDesp = CurlController::requestSunat($urlDesp, $methodDesp, $fieldsDesp, $token)->response->data[0];

        $fields = array(
            "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
            "comprobante" => array(
                "tipoDoc" => $dataDesp->type_transportist,
                "serie" => $dataDesp->serie_transportist,
                "correlativo" => $dataDesp->number_transportist
            )
        );

        $data = json_encode($fields);

        /*=============================================
        Enviamos el documento
        =============================================*/
        $tokenSend = $_SESSION["empresa"]->token_empresa;

        $urlSend = "despatch/send";
        $methodSend = "POST";
        $dataSend = CurlController::requestSunat($urlSend, $methodSend, $data, $tokenSend);

        if($dataSend->response->message != "It is necessary to be in production") {

            if ($dataSend->response->success == true) {

                $dataUpt = "status_sunat_transportist=aceptado";

                $urlUpt = "transportists?id=" . $idDesp . "&nameId=id_transportist&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                $methodUpt = "PUT";
                $fieldsUpt = $dataUpt;

                $responseUpt = CurlController::requestSunat($urlUpt, $methodUpt, $fieldsUpt, $token);

            } else {

                $dataUpt = "status_sunat_transportist=rechazado";

                $urlUpt = "transportists?id=" . $idDesp . "&nameId=id_transportist&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                $methodUpt = "PUT";
                $fieldsUpt = $dataUpt;

                $responseUpt = CurlController::requestSunat($urlUpt, $methodUpt, $fieldsUpt, $token);

            }

        }

        echo json_encode($dataSend);

    }

}