<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class SettingsController
{

    /*=============================================
    Mostrar datos de las configuraciones
    =============================================*/
    public static function settings()
    {

        $url = "configuraciones";
        $method = "GET";
        $fields = array();
        $token = TemplateController::tokenSet();

        $response = CurlController::requestSunat($url, $method, $fields, $token);

        if ($response->response->success == true) {

            $resultado = $response->response->data[0];

        } else {

            $resultado = "No encontrado";

        }

        return $resultado;

    }

    /*=============================================
    Editar datos generales
    =============================================*/
    public function editGeneral()
    {

        if (isset($_POST["name-sys"])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            // Accedemos a los datos adicionales
            $urlGetSet = "configuraciones?select=*&linkTo=id_configuracion&equalTo=1";
            $methodGetSet = "GET";
            $fieldsGetSet = array();
            $tokenGetSet = TemplateController::tokenSet();
            $responseGetSet = CurlController::requestSunat($urlGetSet, $methodGetSet, $fieldsGetSet, $tokenGetSet);

            // Decodificar el JSON extras
            $jsonExtras = $response->response->data[0]->extras_configuracion;
            $datosExtras = json_decode($jsonExtras, true);

            // Acceder y editar los valores del arreglo
            $datosExtras[0]['reset_pass'] = $_POST["reset-sistema"];
            $datosExtras[0]['register_system'] = $_POST["registro-sistema"];
            $datosExtras[0]['social_login'] = $_POST["social-sistema"];
            $datosExtras[0]['supabase'] = $_POST["supabase-sistema"];
            $datosExtras[0]['supabaseUrl'] = $_POST["url-supabase"];
            $datosExtras[0]['supabaseKey'] = $_POST["key-supabase"];
            $datosExtras[0]['supabasePass'] = $_POST["pass-supabase"];

            // Codificar de nuevo el JSON
            $jsonExtras = json_encode($datosExtras);

            // Enviamos los datos al API
            $url = "configuraciones?id=1&nameId=id_configuracion&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $method = "PUT";
            $fields = "nombre_sistema_configuracion=" . $_POST["name-sys"] . "&nombre_empresa_configuracion=" . $_POST["name-emp"] . "&descripcion_configuracion=" . TemplateController::htmlClean($_POST["description-emp"]) . "&web_empresa_configuracion=" . $_POST["web-emp"] . "&id_sunat_configuracion=" . $_POST["id-sunat"] . "&clave_sunat_configuracion=" . $_POST["clave-sunat"] . "&keywords_configuracion=" . json_encode(explode(",", $_POST["kw-emp"])) . "&extras_configuracion=" . $jsonExtras;
            $token = TemplateController::tokenSet();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

            if ($response->response->success == true) {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncSweetAlert("success", "' . $response->response->data->comment . '", "/settings/general");
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

    /*=============================================
    Editar servidor correo
    =============================================*/
    public function editServer()
    {

        if (isset($_POST["host-server"])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            $url = "configuraciones?id=1&nameId=id_configuracion&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $method = "PUT";
            $fields = "servidor_correo_configuracion=" . $_POST["host-server"] . "&usuario_correo_configuracion=" . $_POST["user-server"] . "&clave_correo_configuracion=" . $_POST["pass-server"] . "&puerto_correo_configuracion=" . $_POST["port-server"] . "&seguridad_correo_configuracion=" . $_POST["sec-server"] . "&activo_correo_configuracion=" . $_POST["act-server"];
            $token = TemplateController::tokenSet();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

            if ($response->response->success == true) {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncSweetAlert("success", "' . $response->response->data->comment . '", "/settings/server");
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

    /*=============================================
    Editar Logo
    =============================================*/
    public function editLogo()
    {

        if (isset($_FILES["image-emp"]["tmp_name"])) {

            echo '<script>
					matPreloader("on");
					fncSweetAlert("loading", "Cargando...", "");
				</script>';

            $select = "id_configuracion,logo_sistema_configuracion";

            $url = "configuraciones?select=" . $select . "&linkTo=id_configuracion&equalTo=1";
            $method = "GET";
            $token = TemplateController::tokenSet();
            $fields = array();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

            if ($response->response->success == true) {

                /*=============================================
                Validar cambio imagen
                =============================================*/
                if (isset($_FILES["image-emp"]["tmp_name"]) && !empty($_FILES["image-emp"]["tmp_name"])) {

                    /*=============================================
                    Borramos el archivo actual
                    =============================================*/
                    $urlDel = "file/delete";
                    $methodDel = "POST";
                    $fieldsDel = array(

                        "deleteUniqueFile" => $response->response->data[0]->logo_sistema_configuracion,
                        "deleteDir" => "img",
                        "deleteFol" => "logo",
                        "deleteCod" => "",

                    );
                    $dataFielDel = json_encode($fieldsDel);

                    $deletePicture = CurlController::requestSunat($urlDel, $methodDel, $dataFielDel, $token);

                    /*=============================================
                    Guardamos el archivo enviado
                    =============================================*/
                    $urlUp = "file/upload";
                    $methodUp = "POST";
                    $fieldsUp = array(

                        "file" => $_FILES["image-emp"]["tmp_name"],
                        "type" => $_FILES["image-emp"]["type"],
                        "mode" => "",
                        "folder" => "documents/img/logo",
                        "name" => base64_encode("logo_emp_" . time()),
                        "width" => 400,
                        "height" => 84,

                    );
                    $dataFielUp = json_encode($fieldsUp);

                    $saveImageEmpr = CurlController::requestSunat($urlUp, $methodUp, $dataFielUp, $token)->response->file;

                } else {

                    $saveImageEmpr = $response->response->data[0]->logo_sistema_configuracion;

                }

                /*=============================================
                Agrupamos la información
                =============================================*/
                $dataUp = "logo_sistema_configuracion=" . $saveImageEmpr;

                /*=============================================
                Solicitud a la API
                =============================================*/
                $url = "configuraciones?id=1&nameId=id_configuracion&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                $method = "PUT";
                $fields = $dataUp;

                $response = CurlController::requestSunat($url, $method, $fields, $token);

                /*=============================================
                Respuesta de la API
                =============================================*/
                if ($response->response->status == 200) {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("success", "' . $response->response->data->comment . '", "/settings/logo");
                        </script>';

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to edit registry");
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

    /*=============================================
    Editar Favicon
    =============================================*/
    public function editFavicon()
    {

        if (isset($_FILES["fav-emp"]["tmp_name"])) {

            echo '<script>
					matPreloader("on");
					fncSweetAlert("loading", "Cargando...", "");
				</script>';

            $select = "id_configuracion,favicon_sistema_configuracion";

            $url = "configuraciones?select=" . $select . "&linkTo=id_configuracion&equalTo=1";
            $method = "GET";
            $token = TemplateController::tokenSet();
            $fields = array();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

            if ($response->response->success == true) {

                /*=============================================
                Validar cambio imagen
                =============================================*/
                if (isset($_FILES["fav-emp"]["tmp_name"]) && !empty($_FILES["fav-emp"]["tmp_name"])) {

                    /*=============================================
                    Borramos el archivo actual
                    =============================================*/
                    $urlDel = "file/delete";
                    $methodDel = "POST";
                    $fieldsDel = array(

                        "deleteUniqueFile" => $response->response->data[0]->favicon_sistema_configuracion,
                        "deleteDir" => "img",
                        "deleteFol" => "favicon",
                        "deleteCod" => "",

                    );
                    $dataFielDel = json_encode($fieldsDel);

                    $deletePicture = CurlController::requestSunat($urlDel, $methodDel, $dataFielDel, $token);

                    /*=============================================
                    Guardamos el archivo enviado
                    =============================================*/
                    $urlUp = "file/upload";
                    $methodUp = "POST";
                    $fieldsUp = array(

                        "file" => $_FILES["fav-emp"]["tmp_name"],
                        "type" => $_FILES["fav-emp"]["type"],
                        "mode" => "",
                        "folder" => "documents/img/favicon",
                        "name" => base64_encode("favicon_emp_" . time()),
                        "width" => 800,
                        "height" => 800,

                    );
                    $dataFielUp = json_encode($fieldsUp);

                    $saveImageEmpr = CurlController::requestSunat($urlUp, $methodUp, $dataFielUp, $token)->response->file;

                } else {

                    $saveImageEmpr = $response->response->data[0]->favicon_sistema_configuracion;

                }

                /*=============================================
                Agrupamos la información
                =============================================*/
                $dataUp = "favicon_sistema_configuracion=" . $saveImageEmpr;

                /*=============================================
                Solicitud a la API
                =============================================*/
                $url = "configuraciones?id=1&nameId=id_configuracion&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                $method = "PUT";
                $fields = $dataUp;

                $response = CurlController::requestSunat($url, $method, $fields, $token);

                /*=============================================
                Respuesta de la API
                =============================================*/
                if ($response->response->status == 200) {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("success", "' . $response->response->data->comment . '", "/settings/favicon");
                        </script>';

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to edit registry");
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

    /*=============================================
    Editar Pasarelas
    =============================================*/
    public function editGateway()
    {

        if (isset($_POST["client_id-paypal"])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            $select = "*";

            $url = "configuraciones?select=" . $select . "&linkTo=id_configuracion&equalTo=1";
            $method = "GET";
            $fields = array();
            $token = TemplateController::tokenSet();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

            if ($response->response->status == 200) {

                // Decodificar el JSON Paypal
                $jsonPaypal = $response->response->data[0]->paypal_configuracion;
                $datosPaypal = json_decode($jsonPaypal, true);

                // Acceder y editar los valores del arreglo
                $datosPaypal[0]['client_id'] = $_POST["client_id-paypal"];
                $datosPaypal[0]['secret_key'] = $_POST["secret_key-paypal"];

                // Codificar de nuevo el JSON
                $jsonPaypal = json_encode($datosPaypal);

                // Decodificar el JSON Culqi
                $jsonCulqi = $response->response->data[0]->culqi_configuracion;
                $datosCulqi = json_decode($jsonCulqi, true);

                // Acceder y editar los valores del arreglo
                $datosCulqi[0]['public_key'] = $_POST["public_key-culqi"];
                $datosCulqi[0]['secret_key'] = $_POST["secret_key-culqi"];

                // Codificar de nuevo el JSON
                $jsonCulqi = json_encode($datosCulqi);

                /*=============================================
                Solicitud a la API
                =============================================*/
                $url = "configuraciones?id=1&nameId=id_configuracion&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                $method = "PUT";
                $fields = "paypal_configuracion=" . $jsonPaypal . "&culqi_configuracion=" . $jsonCulqi;
                $token = TemplateController::tokenSet();

                $response = CurlController::requestSunat($url, $method, $fields, $token);

                if ($response->response->success == true) {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("success", "' . $response->response->data->comment . '", "/settings/gateway");
                        </script>';

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to edit registry");
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

    /*=============================================
    Editar Facturacion
    =============================================*/
    public function editBilling()
    {

        if (isset($_POST["ruc-system"])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            // Accedemos a los datos adicionales
            $urlGetSet = "configuraciones?select=*&linkTo=id_configuracion&equalTo=1";
            $methodGetSet = "GET";
            $fieldsGetSet = array();
            $tokenGetSet = TemplateController::tokenSet();
            $responseGetSet = CurlController::requestSunat($urlGetSet, $methodGetSet, $fieldsGetSet, $tokenGetSet);

            // Decodificar el JSON extras
            $jsonFacturacion = $response->response->data[0]->facturacion_configuracion;
            $datosFacturacion = json_decode($jsonFacturacion, true);

            // Acceder y editar los valores del arreglo
            $datosFacturacion[0]['estado'] = 'activo';
            $datosFacturacion[0]['factura']['serie'] = $_POST["serie-system"];
            $datosFacturacion[0]['factura']['correlativo'] = $_POST["number-system"];
            $datosFacturacion[0]['empresa']['ruc'] = $_POST["ruc-system"];
            $datosFacturacion[0]['empresa']['razonSocial'] = $_POST["rs-system"];
            $datosFacturacion[0]['empresa']['nombreComercial'] = $_POST["nc-system"];
            $datosFacturacion[0]['empresa']['departamento'] = $_POST["dep-system"];
            $datosFacturacion[0]['empresa']['provincia'] = $_POST["pro-system"];
            $datosFacturacion[0]['empresa']['distrito'] = $_POST["dis-system"];
            $datosFacturacion[0]['empresa']['ubigeo'] = $_POST["ubi-system"];
            $datosFacturacion[0]['empresa']['direccion'] = $_POST["address-system"];
            $datosFacturacion[0]['empresa']['telefono'] = $_POST["phone-system"];
            $datosFacturacion[0]['empresa']['email'] = $_POST["email-system"];
            $datosFacturacion[0]['sunat']['modo'] = $_POST["fase-system"];
            $datosFacturacion[0]['sunat']['usuarioSol'] = $_POST["usuario_sol-system"];
            $datosFacturacion[0]['sunat']['claveSol'] = $_POST["clave_sol-system"];
            $datosFacturacion[0]['sunat']['claveCertificado'] = $_POST["clave_certificate-system"];
            $datosFacturacion[0]['sunat']['expiraCertificado'] = $_POST["expired_certificate-system"];

            // Codificar de nuevo el JSON
            $jsonFacturacion = json_encode($datosFacturacion);

            // Enviamos los datos al API
            $url = "configuraciones?id=1&nameId=id_configuracion&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $method = "PUT";
            $fields = "facturacion_configuracion=" . $jsonFacturacion;

            $response = CurlController::requestSunat($url, $method, $fields, $tokenGetSet);

            // Validamos si se tiene el modo produccion
            if ($_POST['fase-system'] == 'produccion') {

                if (isset($_FILES["file-certificate"]["tmp_name"]) && !empty($_FILES["file-certificate"]["tmp_name"])) {

                    if($_FILES["file-certificate"]["type"] == "application/x-pkcs12") {

                        /*=============================================
                        Guardamos el archivo enviado
                        =============================================*/
                        $urlUp = "file/upload";
                        $methodUp = "POST";
                        $fieldsUp = array(

                            "file" => $_FILES["file-certificate"]["tmp_name"],
                            "type" => $_FILES["file-certificate"]["type"],
                            "mode" => NULL,
                            "width" => NULL,
                            "height" => NULL,
                            "folder" => "documents/certificado",
                            "name" => $_POST['ruc-system']

                        );
                        $dataFielUp = json_encode($fieldsUp);

                        $saveCertificate = CurlController::requestSunat($urlUp, $methodUp, $dataFielUp, $tokenGetSet)->response->file;

                    } else {

                        echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncNotie(3, "Invalid format");
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

            }

            if ($response->response->success == true) {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncSweetAlert("success", "' . $response->response->data->comment . '", "/settings/billing");
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