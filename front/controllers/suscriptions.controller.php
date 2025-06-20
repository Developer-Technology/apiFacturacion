<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class SuscriptionsController
{

    /*=============================================
    Editar datos
    =============================================*/
    public function edit($id)
    {

        if (isset($_POST["idSuscription"])) {

            /*=============================================
            Mensaje de carga
            =============================================*/
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            if ($_POST["idSuscription"] == $id) {

                /*=============================================
                Obtenemos las dimensiones originales de la imagen
                =============================================*/
                list($width, $height) = getimagesize($_FILES["file-pay"]["tmp_name"]);

                /*=============================================
                Recogemos los datos
                =============================================*/
                $urlEdit = "paySuscription/" . $id;
                $methodEdit = "POST";
                $tokenEdit = TemplateController::tokenSet();
                $dataEdit = array(
                    "file" => $_FILES["file-pay"]["tmp_name"],
                    "type" => $_FILES["file-pay"]["type"],
                    "width" => $width,
                    "height" => $height,
                    "monto" => $_POST["pay-monto"],
                    "metodoPago" => $_POST["pay-metodo"],
                    "comprobante" => $_POST["pay-comprobante"],
                    "fechaPago" => $_POST["pay-fechaPago"],
                );
                $fieldsEdit = json_encode($dataEdit);

                $saveSuscription = CurlController::requestSunat($urlEdit, $methodEdit, $fieldsEdit, $tokenEdit);

                if($saveSuscription->response->success == true) {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("success", "' . $saveSuscription->response->message . '", "/suscriptions");
                        </script>';

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to edit registry");
                        </script>';
    
                }

            }

        }

    }

    /*=============================================
    carga el comprobante
    =============================================*/
    public function upload($id)
    {

        if (isset($_POST["idSuscription"])) {

            /*=============================================
            Mensaje de carga
            =============================================*/
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            if ($_POST["idSuscription"] == $id) {

                /*=============================================
                Obtenemos los datos de la suscripcion
                =============================================*/
                $select = "*";

                $url = "relations?rel=suscripciones,planes,empresas,usuarios&type=suscripcion,plan,empresa,usuario&select=" . $select . "&linkTo=id_suscripcion&equalTo=" . $id;
                $method = "GET";
                $token = TemplateController::tokenSet();
                $fields = array();

                $response = CurlController::requestSunat($url, $method, $fields, $token);

                if($response->response->success == true) {

                    /* Validamos si existe un archivo para eliminarlo */
                    if($response->response->data[0]->adjunto_suscripcion != NULL || $response->response->data[0]->adjunto_suscripcion != "") {
                        
                        /*=============================================
                        Borramos el archivo actual
                        =============================================*/
                        $urlDel = "file/delete";
                        $methodDel = "POST";
                        $fieldsDel = array(

                            "deleteUniqueFile" => $response->response->data[0]->adjunto_suscripcion,
                            "deleteDir" => "pagos",
                            "deleteFol" => $response->response->data[0]->ruc_empresa,
                            "deleteCod" => "",

                        );
                        $dataFielDel = json_encode($fieldsDel);

                        $deletePicture = CurlController::requestSunat($urlDel, $methodDel, $dataFielDel, $token);

                    }

                    /*=============================================
                    Obtenemos las dimensiones originales de la imagen
                    =============================================*/
                    //list($width, $height) = getimagesize($_FILES["file-pay"]["tmp_name"]);
                    $width = 600;
                    $height = 600;

                    /*=============================================
                    Recogemos los datos
                    =============================================*/
                    $url = "uploadsuscription";
                    $method = "POST";
                    $token = $response->response->data[0]->token_empresa;
                    $data = array(
                        "claveSecreta" => $response->response->data[0]->clave_secreta_empresa,
                        "file" => $_FILES["file-pay"]["tmp_name"],
                        "type" => $_FILES["file-pay"]["type"],
                        "width" => $width,
                        "height" => $height,
                        "id" => $id
                    );
                    $fields = json_encode($data);

                    $saveSuscription = CurlController::requestSunat($url, $method, $fields, $token);

                    /*=============================================
                    Respuesta de la API
                    =============================================*/
                    if ($saveSuscription->response->success == true) {

                        echo '<script>
                                    fncFormatInputs();
                                    matPreloader("off");
                                    fncSweetAlert("close", "", "");
                                    fncSweetAlert("success", "' . $saveSuscription->response->message . '", "/suscriptions");
                                </script>';

                    } else {

                        echo '<script>
                                    fncFormatInputs();
                                    matPreloader("off");
                                    fncSweetAlert("close", "", "");
                                    fncNotie(3, "' . $saveSuscription->response->message . '");
                                </script>';

                    }
                    
                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to edit registry");
                        </script>';
    
                }

            }

        }

    }

    /*=============================================
    Actualizar el estado
    =============================================*/
    public function update($id)
    {

        if (isset($_POST["idSuscription"])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            if ($_POST["idSuscription"] == $id) {

                /*=============================================
                Recogemos los datos
                =============================================*/
                $urlEdit = "updateSuscription/" . $id;
                $methodEdit = "POST";
                $tokenEdit = TemplateController::tokenSet();
                $dataEdit = array(
                    "estado" => $_POST["estado-pay"],
                    "periodo" => $_POST['periodo-pay'],
                    "usuario" => $_SESSION["user"]->alias_usuario
                );
                $fieldsEdit = json_encode($dataEdit);

                $saveSuscription = CurlController::requestSunat($urlEdit, $methodEdit, $fieldsEdit, $tokenEdit);

                if($saveSuscription->response->success == true) {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("success", "' . $saveSuscription->response->message . '", "/suscriptions");
                        </script>';

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to edit registry");
                        </script>';
    
                }

            }

        }

    }

    /*=============================================
    Actualizar el listado
    =============================================*/
    public function cron()
    {

        if (isset($_POST["cron"])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            $token = TemplateController::tokenSet();

            /*=============================================
            Peticion al API
            =============================================*/
            $url = "cron/suscriptions";
            $method = "POST";
            $fields = array();
            $response = CurlController::requestSunat($url, $method, $fields, $token);
            
            if($response->response->success == true){
            
                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncSweetAlert("success", "' . $response->response->message . '", "/suscriptions");
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

}