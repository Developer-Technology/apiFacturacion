<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class LogoController
{

    /*=============================================
    Cargar logo
    =============================================*/
    public function logo($ruc)
    {

        if (isset($_POST["ruc-tenant"])) {

            /*=============================================
            Mensaje de carga
            =============================================*/
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            if($_POST["ruc-tenant"] == $ruc) {

                $select = "id_empresa,logo_empresa";
                $url = "empresas?select=" . $select . "&linkTo=ruc_empresa&equalTo=" . $ruc;
                $method = "GET";
                $token = TemplateController::tokenSet();
                $fields = array();
                $response = CurlController::requestSunat($url, $method, $fields, $token);

                if($response->response->success == true) {

                    /*=============================================
                    Validar la carga del logo
                    =============================================*/
                    if (isset($_FILES["file-logo"]["tmp_name"]) && !empty($_FILES["file-logo"]["tmp_name"])) {

                        /* Validamos si existe un archivo para eliminarlo */
                        if($response->response->data[0]->logo_empresa != NULL || $response->response->data[0]->logo_empresa != "") {
                            
                            /*=============================================
                            Borramos el archivo actual
                            =============================================*/
                            $urlDel = "file/delete";
                            $methodDel = "POST";
                            $fieldsDel = array(

                                "deleteUniqueFile" => $response->response->data[0]->logo_empresa,
                                "deleteDir" => "logo",
                                "deleteFol" => $ruc,
                                "deleteCod" => "",

                            );
                            $dataFielDel = json_encode($fieldsDel);

                            $deletePicture = CurlController::requestSunat($urlDel, $methodDel, $dataFielDel, $token);

                        }

                        /*=============================================
                        Obtenemos las dimensiones originales de la imagen
                        =============================================*/
                        list($width, $height) = getimagesize($_FILES["file-logo"]["tmp_name"]);

                        /*=============================================
                        Recogemos los datos
                        =============================================*/
                        $urlLogo = "logo";
                        $methodLogo = "POST";
                        $tokenLogo = $_SESSION["empresa"]->token_empresa;

                        $fieldsLogo = array(

                            "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
                            "file" => $_FILES["file-logo"]["tmp_name"],
                            "type" => $_FILES["file-logo"]["type"],
                            "width" => $width,
                            "height" => $height
                            
                        );

                        $dataLogo = json_encode($fieldsLogo);

                        $saveLogo = CurlController::requestSunat($urlLogo, $methodLogo, $dataLogo, $tokenLogo);

                        /*=============================================
                        Respuesta de la API
                        =============================================*/
                        if ($saveLogo->response->success == true) {

                            echo '<script>
                                    fncFormatInputs();
                                    matPreloader("off");
                                    fncSweetAlert("close", "", "");
                                    fncSweetAlert("success", "' . $saveLogo->response->message . '", "/businesses/logo");
                                </script>';

                        } else {

                            echo '<script>
                                    fncFormatInputs();
                                    matPreloader("off");
                                    fncSweetAlert("close", "", "");
                                    fncNotie(3, "' . $saveLogo->response->message . '");
                                </script>';

                        }

                    } else {

                        echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncNotie(3, "Debes seleccionar un archivo");
                            </script>';

                    }

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to save record");
                        </script>';

                }

            }

        }

    }

}