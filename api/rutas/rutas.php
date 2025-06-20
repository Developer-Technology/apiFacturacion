<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

date_default_timezone_set('America/Lima');
$fechaHoy = date('Y-m-d');

/*=============================================
Recibimos los datos en json
=============================================*/
$data = json_decode(file_get_contents("php://input"));

/*=============================================
Exploramos la ruta para la peticion
=============================================*/
$arrayRutas = explode("/", $_SERVER['REQUEST_URI']);

/*=============================================
Recorremos la cabecera
=============================================*/
$authorizationHeader = null;

if (isset($_SERVER['Authorization'])) {

    $authorizationHeader = $_SERVER['Authorization'];

} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {

    $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

} elseif (function_exists('apache_request_headers')) {

    $requestHeaders = apache_request_headers();
    $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

    if (isset($requestHeaders['Authorization'])) {

        $authorizationHeader = $requestHeaders['Authorization'];

    }

}

/*=============================================
Verificamos si la peticion viene con SSL
=============================================*/
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {

    $protocol = 'https://';

} else {

    $protocol = 'http://';

}

/*=============================================
Extraemos el token de la cabecera
=============================================*/
if ($authorizationHeader) {

    $authorizationHeaderParts = explode(' ', $authorizationHeader);

    if (count($authorizationHeaderParts) == 2 && $authorizationHeaderParts[0] == 'Bearer') {

        $accessToken = $authorizationHeaderParts[1];
        $accessTokenSt = $authorizationHeaderParts[1];

    }

} else {

    $accessToken = null;
    $accessTokenSt = null;

}

/*=============================================
Validamos que exista el token en la base de datos
=============================================*/
$validToken = GetModel::getDataFilter("empresas", "*", "token_empresa", $accessToken, null, null, null, null);

/*=============================================
Validamos la clave cuando se envia peticiones
=============================================*/
if (isset($data->claveSecreta)) {

    /*=============================================
    Validamos la claveSecreta
    =============================================*/
    $clEmpresas = GetModel::getDataFilter("empresas", "*", "clave_secreta_empresa", $data->claveSecreta, null, null, null, null);

}

/*=============================================
Recogemos los datos del sistema
=============================================*/
$validTokenSt = GetModel::getDataFilter("configuraciones", "*", "id_configuracion", 1, null, null, null, null);

/*=============================================
Obtenemos las configuraciones adicionales
=============================================*/
foreach (json_decode($validTokenSt[0]->extras_configuracion) as $key => $elementExtras) {

    $supabase = $elementExtras->supabase;
    $supabaseUrl = $elementExtras->supabaseUrl;
    $supabaseKey = $elementExtras->supabaseKey;
    $supabasePass = $elementExtras->supabasePass;

}

/*=============================================
Cuando no se envia peticiones
=============================================*/
if (count(array_filter($arrayRutas)) == 0) {

    /*=============================================
    Cuando no se hace ninguna peticion a la API
    =============================================*/
    $json = array(

        "response" => array(
            "success" => false,
            "status" => 404,
            "message" => "Not Found",
        ),

    );

    /*=============================================
    Imprimimos la respuesta
    =============================================*/
    echo json_encode($json, http_response_code($json["response"]["status"]));

    return;

} else {

    /*=============================================
    Validamos que el token coincida con la empresa (claveSecreta)
    =============================================*/
    if ($validToken && $clEmpresas && $accessToken == $clEmpresas[0]->token_empresa) {

        /*=============================================
        Capturar datos de la empresa
        =============================================*/
        $dataCompany = array(
            /* Para generar XML */
            "tipoDoc" => "6",
            "ruc" => $clEmpresas[0]->ruc_empresa,
            "razonSocial" => $clEmpresas[0]->razon_social_empresa,
            "nombreComercial" => $clEmpresas[0]->nombre_comercial_empresa,
            "address" => array(
                "codigoPais" => "PE",
                "departamento" => $clEmpresas[0]->departamento_empresa,
                "provincia" => $clEmpresas[0]->provincia_empresa,
                "distrito" => $clEmpresas[0]->distrito_empresa,
                "direccion" => $clEmpresas[0]->direccion_empresa,
                "ubigeo" => $clEmpresas[0]->ubigeo_empresa,
            ),
            /* Para enviar XML / Consultar CPE */
            "modo" => $clEmpresas[0]->fase_empresa,
            "usuarioSol" => $clEmpresas[0]->usuario_sol_empresa,
            "claveSol" => $clEmpresas[0]->clave_sol_empresa,
            /* Para guia de remision remitente / transportista */
            "client_id" => $clEmpresas[0]->client_id,
            "client_secret" => $clEmpresas[0]->client_secret,
            "claveCertificado" => $clEmpresas[0]->clave_certificado_empresa,
            /* Para PDF */
            "telefono" => $clEmpresas[0]->telefono_empresa,
            "email" => $clEmpresas[0]->email_empresa,
            "logo" => $clEmpresas[0]->logo_empresa,
            "sistema" => $validTokenSt[0]->web_empresa_configuracion,
            "nombreSistema" => $validTokenSt[0]->nombre_sistema_configuracion
        );

        /*=============================================
        Obtenemos los datos del consumo del periodo actual
        =============================================*/
        if ($clEmpresas[0]->consumo_empresa != '[]') {

            foreach (json_decode($clEmpresas[0]->consumo_empresa) as $key => $cons) {

                $periodo = $cons->periodo;
                $consultasEmpresa = $cons->consultas;
                $documentosEmpresa = $cons->documentos;

            }

        } else {

            $periodo = date('m-Y');
            $consultasEmpresa = 0;
            $documentosEmpresa = 0;

        }

        /*=============================================
        Validamos se envie la cabecera con el token
        =============================================*/
        if (!isset(getallheaders()["Authorization"]) || getallheaders()["Authorization"] != 'Bearer ' . $accessToken) {

            /*=============================================
            Cuando viene el token
            =============================================*/
            $json = array(

                "response" => array(
                    "success" => false,
                    "status" => 400,
                    "message" => "Not authorized",
                ),

            );

            /*=============================================
            Imprimimos la respuesta
            =============================================*/
            echo json_encode($json, http_response_code($json["response"]["status"]));

            return;

        } else if ($clEmpresas[0]->estado_empresa != 1) {

            /*=============================================
            Cuando el estado no es activo
            =============================================*/
            $json = array(

                "response" => array(
                    "success" => false,
                    "status" => 400,
                    "message" => "The company is not active",
                ),

            );

            /*=============================================
            Imprimimos la respuesta
            =============================================*/
            echo json_encode($json, http_response_code($json["response"]["status"]));

        } else if ($clEmpresas[0]->proxima_facturacion_empresa >= $fechaHoy || $clEmpresas[0]->proxima_facturacion_empresa == '0000-00-00') {

            /*=============================================
            Obtenemos los datos del plan
            =============================================*/
            $valuePlan = $clEmpresas[0]->id_plan_empresa;
            $dataPlans = GetModel::getDataFilter("planes", "*", "id_plan", $valuePlan, null, null, null, null);

            $jsonPlan = $dataPlans[0]->contiene_plan;

            $arrayPlan = json_decode($jsonPlan, true);

            /*=============================================
            Validamos la cantidad de consultas y documentos
            =============================================*/
            foreach ($arrayPlan as $elementPlan) {

                /*=============================================
                Validamos la cantidad de consultas
                =============================================*/
                if ($elementPlan["consultas"] != "ilimitado") {

                    $totalCons = $elementPlan["consultas"];

                } else {

                    $totalCons = 999999999;

                }

                /*=============================================
                Validamos la cantidad de documentos
                =============================================*/
                if ($elementPlan["documentos"] != "ilimitado") {

                    $totalDocs = $elementPlan["documentos"];

                } else {

                    $totalDocs = 999999999;

                }

            }

            /*=============================================
            Cuando pasamos solo un indice en el array $arrayRutas
            =============================================*/
            if (count(array_filter($arrayRutas)) == 1) {

                /*=============================================
                Realizamos la peticion desde el indice
                =============================================*/
                if (array_filter($arrayRutas)[1] == "consult") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos las consultas realizadas con el plan
                        =============================================*/
                        if ($consultasEmpresa > $totalCons) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the consultations allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            /*=============================================
                            Capturar datos
                            =============================================*/
                            $company = json_encode($dataCompany);
                            $comprobante = $data->comprobante;

                            /*=============================================
                            Generamos el token para la consulta
                            =============================================*/
                            $generateToken = new ControladorSunat();
                            $generateToken->generarToken($validTokenSt);

                            if ($generateToken->success == true) {

                                $consultaCompApi = new ControladorSunat();
                                $consultaCompApi->consultarComprobanteApi($company, $comprobante, $generateToken->token);

                                $json = array(

                                    "response" => array(
                                        "success" => true,
                                        "status" => 200,
                                        "data" => array(
                                            "estadoCp" => $consultaCompApi->code . " - " . $consultaCompApi->message,
                                            "estadoRuc" => $consultaCompApi->codeRuc . " - " . $consultaCompApi->messageRuc,
                                            "condDomiRuc" => $consultaCompApi->codeDom . " - " . $consultaCompApi->messageDom,
                                        ),
                                    ),

                                );

                                /*=============================================
                                Actualizamos el contador de consultas
                                =============================================*/
                                $current_period = date('m-Y');

                                // Verificamos si el período actual ya está presente en el JSON
                                $found = false;
                                // Decodificar el JSON
                                $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                                $datosConsumo = json_decode($jsonConsumo, true);
                                // Recorremos el array
                                foreach ($datosConsumo as &$item) {

                                    if ($item['periodo'] === $current_period) {

                                        $item['consultas'] = $item['consultas'] + 1;
                                        $found = true;
                                        break;

                                    }

                                }
                                // Si el período actual no está presente, agregamos un nuevo elemento al array
                                if (!$found) {

                                    $arr = $datosConsumo;
                                    array_push($arr, array('periodo' => $current_period, 'consultas' => 1, 'documentos' => 0));
                                    $datosConsumo = $arr;
                                }
                                // Codificar de nuevo el JSON
                                $jsonConsumo = json_encode($datosConsumo);

                                $data = array(

                                    "consumo_empresa" => $jsonConsumo,

                                );
                                $updatConsult = PutModel::putData("empresas", $data, $accessToken, "token_empresa");

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "We could not generate the token, check if your SUNAT API data is correct",
                                    ),

                                );

                            }

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "qr") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Indicamos que es una imagen png
                        =============================================*/
                        header('Content-type: image/png');

                        /*=============================================
                        Creamos el qr
                        =============================================*/
                        $rta_qr = "documents/qrConsult/" . $dataCompany["ruc"];
                        $nameQr = $dataCompany["ruc"] . "-" . $data->tipo . "-" . $data->serie . "-" . $data->correlativo;
                        $text_qr = $dataCompany["ruc"] . "|" . $data->tipo . " | " . $data->serie . " | " . $data->correlativo . " | " . $data->igv . " | " . $data->total . " | " . $data->fechaEmision . " | " . $data->clienteTipo . " | " . $data->clienteNumero . " | ";
                        $ruta_qr = "documents/qrConsult/" . $dataCompany["ruc"] . "/" . $nameQr . ".png";

                        /*=============================================
                        Creamos el directorio si no existe
                        =============================================*/
                        if (!file_exists($rta_qr)) {
                            mkdir($rta_qr, 0777, true);
                        }

                        /*=============================================
                        Generamos el qr
                        =============================================*/
                        QRcode::png($text_qr, $ruta_qr, 'Q', 6, 0);

                        /*=============================================
                        Abrimos el qr
                        =============================================*/
                        readfile($ruta_qr);

                        /*=============================================
                        Eliminamos el qr y el directorio para no saturar el servidor
                        =============================================*/
                        unlink($ruta_qr);
                        rmdir($rta_qr);

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "certificate") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        if ($data->faseEmpresa != '') {

                            if ($data->faseEmpresa == 'producccion' && $data->usuarioSol == '' && $data->claveSol == '' && $data->claveCertificado == '' && $data->expiraCertificado == '' && $data->clientIdGR == '' && $data->clientSecretGR == '') {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "Complete the fields",
                                    ),

                                );

                            } else {

                                $newName = $dataCompany["ruc"];

                                $datos_certificado = array(
                                    "fase_empresa" => $data->faseEmpresa,
                                    "certificado_empresa" => $newName,
                                    "usuario_sol_empresa" => $data->usuarioSol,
                                    "clave_sol_empresa" => $data->claveSol,
                                    "clave_certificado_empresa" => $data->claveCertificado,
                                    "expira_certificado_empresa" => $data->expiraCertificado,
                                    "client_id" => $data->clientIdGR,
                                    "client_secret" => $data->clientSecretGR,
                                );

                                //Validamos la fase enviada para realizar los cambios
                                if ($data->faseEmpresa == 'produccion') {

                                    if (isset($data->file) && !empty($data->file)) {

                                        if ($data->type == "application/x-pkcs12") {

                                            $updateCertificate = PutModel::putData("empresas", $datos_certificado, $data->claveSecreta, "clave_secreta_empresa");

                                        } else {

                                            $json = array(

                                                "response" => array(
                                                    "success" => false,
                                                    "status" => 400,
                                                    "message" => "Invalid format",
                                                ),
                
                                            );
                
                                            /*=============================================
                                            Imprimimos la respuesta
                                            =============================================*/
                                            echo json_encode($json, http_response_code($json["response"]["status"]));

                                        }

                                    } else {

                                        $json = array(

                                            "response" => array(
                                                "success" => false,
                                                "status" => 400,
                                                "message" => "Select a file to upload",
                                            ),
            
                                        );
            
                                        /*=============================================
                                        Imprimimos la respuesta
                                        =============================================*/
                                        echo json_encode($json, http_response_code($json["response"]["status"]));

                                    }

                                } else {

                                    $updateCertificate = PutModel::putData("empresas", $datos_certificado, $data->claveSecreta, "clave_secreta_empresa");

                                }

                                if (isset($updateCertificate["comment"]) && $updateCertificate["comment"] == "The process was successful") {

                                    $upload = FilesController::fileData($data->file, $data->type, "documents/certificado", $newName, null, null, null);

                                    $json = array(

                                        'response' => array(
                                            'success' => true,
                                            'status' => 200,
                                            'name' => $newName,
                                            'message' => 'The certificate has been uploaded successfully',
                                        ),

                                    );

                                } else {

                                    $json = array(

                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "message" => "Error loading company digital certificate",
                                        ),

                                    );

                                }

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You must indicate a phase",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "editsuscription") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
        
                        /*=============================================
                        Capturar datos
                        =============================================*/
                        if (isset($data->file) && !empty($data->file)) {
        
                            if ($data->type == "image/jpeg" || $data->type == "image/png") {
        
                                if ($data->id == '' || $data->monto == '' || $data->comprobante == '' || $data->fechaPago == '' || $data->metodoPago == '') {
        
                                    $json = array(
        
                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "message" => "Complete the fields",
                                        ),
        
                                    );
        
                                } else {
        
                                    if ($data->type == "image/png") {
        
                                        $ext = ".png";
        
                                    } else {
        
                                        $ext = ".jpg";
        
                                    }
        
                                    $newName = base64_encode($dataCompany["ruc"] . "~" . $data->id . "~" . $data->comprobante);
                                    $newName2 = base64_encode($dataCompany["ruc"] . "~" . $data->id . "~" . $data->comprobante) . $ext;
                                    $idPago = ControladorRutas::generateUniqueId();
        
                                    $datos_suscripcion = array(
                                        "trans_suscripcion" => $idPago,
                                        "fecha_pago_suscripcion" => $data->fechaPago,
                                        "monto_pago_suscripcion" => $data->monto,
                                        "medio_pago_suscripcion" => $data->metodoPago,
                                        "comprobante_suscripcion" => $data->comprobante,
                                        "adjunto_suscripcion" => $newName2,
                                        "estado_suscripcion" => "pendiente",
                                    );
        
                                    $updateSuscripcion = PutModel::putData("suscripciones", $datos_suscripcion, $data->id, "id_suscripcion");
        
                                    if (isset($updateSuscripcion["comment"]) && $updateSuscripcion["comment"] == "The process was successful") {
        
                                        /*=============================================
                                        Creamos la carpeta de pagos si no existe
                                        =============================================*/
                                        $ruta = 'documents/pagos/' . $dataCompany["ruc"];
        
                                        if (!file_exists($ruta)) {
                                            mkdir($ruta, 0777, true);
                                        }
        
                                        /*=============================================
                                        Cargamos el archivo en la carpeta creada
                                        =============================================*/
                                        $upload = FilesController::fileData($data->file, $data->type, $ruta, $newName, null, $data->width, $data->height);
        
                                        $json = array(
        
                                            'response' => array(
                                                'success' => true,
                                                'status' => 200,
                                                'name' => $newName,
                                                'message' => 'The pay for suscription has been uploaded successfully'
                                            ),
        
                                        );
        
                                    } else {
        
                                        $json = array(
        
                                            "response" => array(
                                                "success" => false,
                                                "status" => 400,
                                                "message" => "Error loading pay",
                                            ),
        
                                        );
        
                                    }
        
                                }
        
                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));
                            
                            } else {
        
                                $json = array(
        
                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "Invalid format",
                                    ),
        
                                );
        
                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));
        
                            }
        
                        } else {
        
                            $json = array(
        
                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Select a file to upload",
                                ),
        
                            );
        
                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));
        
                        }
        
                    } else {
        
                        $json = array(
        
                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),
        
                        );
        
                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));
        
                        return;
        
                    }
        
                } else if (array_filter($arrayRutas)[1] == "uploadsuscription") {
        
                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
        
                        /*=============================================
                        Capturar datos
                        =============================================*/
                        if (isset($data->file) && !empty($data->file)) {
        
                            if ($data->type == "image/jpeg" || $data->type == "image/png") {
        
                                if ($data->type == "image/png") {
        
                                    $ext = ".png";
        
                                } else {
        
                                    $ext = ".jpg";
        
                                }
        
                                /*=============================================
                                Recogemos la suscripcion
                                =============================================*/
                                $getSuscr = GetModel::getDataFilter("suscripciones", "*", "id_suscripcion", $data->id, null, null, null, null);
        
                                $newName = base64_encode($dataCompany["ruc"] . "~" . $data->id . "~" . $getSuscr[0]->comprobante_suscripcion . "~" . time());
                                $newName2 = base64_encode($dataCompany["ruc"] . "~" . $data->id . "~" . $getSuscr[0]->comprobante_suscripcion . "~" . time()) . $ext;
                                $idPago = ControladorRutas::generateUniqueId();
        
                                $datos_suscripcion = array(
                                    "adjunto_suscripcion" => $newName2,
                                );
        
                                $updateSuscripcion = PutModel::putData("suscripciones", $datos_suscripcion, $data->id, "id_suscripcion");
        
                                if (isset($updateSuscripcion["comment"]) && $updateSuscripcion["comment"] == "The process was successful") {
        
                                    /*=============================================
                                    Creamos la carpeta de pagos si no existe
                                    =============================================*/
                                    $ruta = 'documents/pagos/' . $dataCompany["ruc"];
        
                                    if (!file_exists($ruta)) {
                                        mkdir($ruta, 0777, true);
                                    }
        
                                    /*=============================================
                                    Cargamos el archivo en la carpeta creada
                                    =============================================*/
                                    $upload = FilesController::fileData($data->file, $data->type, $ruta, $newName, null, $data->width, $data->height);
        
                                    $json = array(
        
                                        'response' => array(
                                            'success' => true,
                                            'status' => 200,
                                            'name' => $newName,
                                            'message' => 'The pay for suscription has been uploaded successfully',
                                        ),
        
                                    );
        
                                } else {
        
                                    $json = array(
        
                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "message" => "Error loading pay",
                                        ),
        
                                    );
        
                                }
        
                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));
                            
                            } else {
        
                                $json = array(
        
                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "Invalid format",
                                    ),
        
                                );
        
                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));
        
                            }
        
                        } else {
        
                            $json = array(
        
                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Select a file to upload",
                                ),
        
                            );
        
                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));
        
                        }
        
                    } else {
        
                        $json = array(
        
                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),
        
                        );
        
                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));
        
                        return;
        
                    }
        
                } else if (array_filter($arrayRutas)[1] == "logo") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        if (isset($data->file) && !empty($data->file)) {

                            if ($data->type == "image/jpeg" || $data->type == "image/png" || $data->type == "image/gif") {

                                $newName = base64_encode($dataCompany["ruc"] . '_' . uniqid() . '_' . time() . '.png');

                                $datos_logo = array(
                                    "logo_empresa" => $newName . '.png',
                                );

                                $updateLogo = PutModel::putData("empresas", $datos_logo, $data->claveSecreta, "clave_secreta_empresa");

                                if (isset($updateLogo["comment"]) && $updateLogo["comment"] == "The process was successful") {

                                    $upload = FilesController::fileData($data->file, $data->type, "documents/logo/" . $dataCompany["ruc"], $newName, null, $data->width, $data->height);

                                    $json = array(

                                        'response' => array(
                                            'success' => true,
                                            'status' => 200,
                                            'name' => $newName,
                                            'message' => 'The logo has been uploaded successfully',
                                        ),

                                    );

                                } else {

                                    $json = array(

                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "message" => "Error loading company logo",
                                        ),

                                    );

                                }

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "Invalid format",
                                    ),

                                );

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Select a file to upload",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 400,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else {

                    $json = array(

                        "response" => array(
                            "success" => false,
                            "status" => 404,
                            "message" => "Not Found",
                        ),

                    );

                    echo json_encode($json, http_response_code($json["response"]["status"]));

                    return;

                }

            } else if (count(array_filter($arrayRutas)) == 2) {

                /*=============================================
                Cuando se hace peticiones de los indices
                =============================================*/
                if (array_filter($arrayRutas)[1] == "invoice" && array_filter($arrayRutas)[2] == "create") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos los documentos generados con el plan
                        =============================================*/
                        if ($documentosEmpresa > $totalDocs) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the documents allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            /*=============================================
                            Capturar datos del json
                            =============================================*/
                            $company = json_encode($dataCompany);
                            $comprobante = $data->comprobante;
                            $client = $data->cliente;
                            $details = $data->items;
                            $extra = $data->extra;
                            $ruta = 'documents/xml/' . $dataCompany["ruc"] . '/unsigned/';

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta)) {
                                mkdir($ruta, 0777, true);
                            }

                            /*=============================================
                            Indicamos el nombre del xml
                            =============================================*/
                            $nombrexml = $dataCompany["ruc"] . '-' . $data->comprobante->tipoDoc . '-' . $data->comprobante->serie . '-' . $data->comprobante->correlativo;

                            /*=============================================
                            Creamos el XML
                            =============================================*/
                            $registro = new ControladorXML();
                            $registro->CrearXMLFactura($ruta . $nombrexml, $company, $client, $comprobante, $details);

                            /*=============================================
                            Creamos el qr
                            =============================================*/
                            $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                            $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                            $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                            if (!file_exists($rta_qr)) {
                                mkdir($rta_qr, 0777, true);
                            }

                            QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                            /*=============================================
                            Firmamos el XML
                            =============================================*/
                            $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';

                            if (!file_exists($rutaSigned)) {
                                mkdir($rutaSigned, 0777, true);
                            }

                            $firmado = new ControladorSunat();
                            $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                            /*=============================================
                            Creamos el pdf A4
                            =============================================*/
                            $crPdfA4 = new ControladorPdf();
                            $crPdfA4->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);

                            /*=============================================
                            Creamos el pdf Ticket
                            =============================================*/
                            $crPdfTc = new ControladorPdf();
                            $crPdfTc->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($registro) {

                                $json = array(

                                    'response' => array(
                                        "success" => true,
                                        "status" => 200,
                                        'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $nombrexml . '.XML',
                                        'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $nombrexml . '.XML',
                                        "pdf-a4" => $crPdfA4->pdf,
                                        "pdf-ticket" => $crPdfTc->pdf,
                                        "message" => 'Document ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            }

                            /*=============================================
                            Actualizamos el contador de documentos
                            =============================================*/
                            $current_period = date('m-Y');

                            // Verificamos si el período actual ya está presente en el JSON
                            $found = false;
                            // Decodificar el JSON
                            $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                            $datosConsumo = json_decode($jsonConsumo, true);
                            // Recorremos el array
                            foreach ($datosConsumo as &$item) {

                                if ($item['periodo'] === $current_period) {

                                    $item['documentos'] = $item['documentos'] + 1;
                                    $found = true;
                                    break;

                                }

                            }
                            // Si el período actual no está presente, agregamos un nuevo elemento al array
                            if (!$found) {

                                $arr = $datosConsumo;
                                array_push($arr, array('periodo' => $current_period, 'consultas' => 0, 'documentos' => 1));
                                $datosConsumo = $arr;

                            }
                            // Codificar de nuevo el JSON
                            $jsonConsumo = json_encode($datosConsumo);

                            $data = array(

                                "consumo_empresa" => $jsonConsumo,

                            );
                            $updatDocument = PutModel::putData("empresas", $data, $accessToken, "token_empresa");

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "invoice" && array_filter($arrayRutas)[2] == "send") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);

                        /*=============================================
                        Indicamos el nombre del xml
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $data->comprobante->tipoDoc . '-' . $data->comprobante->serie . '-' . $data->comprobante->correlativo;
                        $ruta_archivo_xml = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';
                        $rutaCompleta = $ruta_archivo_xml . $nombrexml . '.XML';

                        /*=============================================
                        Validamos que exista el XML firmado para el envio
                        =============================================*/
                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Indicamos la ruta donde se almacena el cdr
                            =============================================*/
                            $ruta_archivo_cdr = 'documents/cdr/' . $dataCompany["ruc"] . '/';

                            /*=============================================
                            Creamos la carpeta del cdr si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_cdr)) {
                                mkdir($ruta_archivo_cdr, 0777, true);
                            }

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_xml)) {
                                mkdir($ruta_archivo_xml, 0777, true);
                            }

                            /*=============================================
                            Enviamos el comprobante
                            =============================================*/
                            $api = new ControladorSunat();
                            $api->EnviarComprobanteElectronico($company, $nombrexml, $ruta_archivo_xml, $ruta_archivo_cdr, "documents/");

                            if ($data->comprobante->tipoDoc == "07" || $data->comprobante->tipoDoc == "08") {

                                $urlDoc = "note";

                            } else {

                                $urlDoc = "invoice";

                            }

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($api->success == true) {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 200,
                                        'hash' => $api->hash,
                                        'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $api->xml,
                                        'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $api->xml,
                                        'pdf-a4' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/' . $urlDoc . '/a4/' . $nombrexml . '.pdf',
                                        'pdf-ticket' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/' . $urlDoc . '/ticket/' . $nombrexml . '.pdf',
                                        'cdr' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/cdr/' . $dataCompany["ruc"] . '/' . $api->cdrb64,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 400,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We cannot send the document because it does not exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "invoice" && array_filter($arrayRutas)[2] == "a4") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
                        $ruta_xml = "documents/xml/" . $dataCompany["ruc"] . "/signed/";
                        $rutaCompleta = $ruta_xml . $nombrexml . ".XML";

                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Creamos el pdf
                            =============================================*/
                            $registro = new ControladorPdf();
                            $registro->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);

                            if ($registro) {

                                $json = array(

                                    "response" => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "There was an error creating the PDF",
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We can't create the PDF because the document doesn't exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "invoice" && array_filter($arrayRutas)[2] == "ticket") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
                        $ruta_xml = "documents/xml/" . $dataCompany["ruc"] . "/signed/";
                        $rutaCompleta = $ruta_xml . $nombrexml . ".XML";

                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Creamos el pdf
                            =============================================*/
                            $registro = new ControladorPdf();
                            $registro->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                            if ($registro) {

                                $json = array(

                                    "response" => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "There was an error creating the PDF",
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We can't create the PDF because the document doesn't exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "notaventa" && array_filter($arrayRutas)[2] == "a4") /* Nota de venta */ {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;

                        /*=============================================
                        Creamos el qr
                        =============================================*/
                        $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                        $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                        $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                        if (!file_exists($rta_qr)) {
                            mkdir($rta_qr, 0777, true);
                        }

                        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                        /*=============================================
                        Creamos el pdf
                        =============================================*/
                        $registro = new ControladorPdf();
                        $registro->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);

                        if ($registro) {

                            $json = array(

                                "response" => array(
                                    "success" => true,
                                    "status" => 200,
                                    "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    'pdf-a4' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/invoice/a4/' . $nombrexml . '.pdf',
                                ),

                            );

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "There was an error creating the PDF",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "notaventa" && array_filter($arrayRutas)[2] == "ticket") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;

                        /*=============================================
                        Creamos el qr
                        =============================================*/
                        $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                        $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                        $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                        if (!file_exists($rta_qr)) {
                            mkdir($rta_qr, 0777, true);
                        }

                        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                        /*=============================================
                        Creamos el pdf
                        =============================================*/
                        $registro = new ControladorPdf();
                        $registro->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                        if ($registro) {

                            $json = array(

                                "response" => array(
                                    "success" => true,
                                    "status" => 200,
                                    "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    'pdf-ticket' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/invoice/ticket/' . $nombrexml . '.pdf',
                                ),

                            );

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "There was an error creating the PDF",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "notaventa" && array_filter($arrayRutas)[2] == "pdf") /* Nota de venta */ {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;

                        /*=============================================
                        Creamos el qr
                        =============================================*/
                        $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                        $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                        $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                        if (!file_exists($rta_qr)) {
                            mkdir($rta_qr, 0777, true);
                        }

                        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                        /*=============================================
                        Creamos el pdf
                        =============================================*/
                        $registro = new ControladorPdf();
                        $registro->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);
                        $registro->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                        if ($registro) {

                            $json = array(

                                "response" => array(
                                    "success" => true,
                                    "status" => 200,
                                    "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    'pdf-a4' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/invoice/a4/' . $nombrexml . '.pdf',
                                    'pdf-ticket' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/invoice/ticket/' . $nombrexml . '.pdf',
                                ),

                            );

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "There was an error creating the PDF",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "cotizacion" && array_filter($arrayRutas)[2] == "a4") /* Cotizacion */ {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;

                        /*=============================================
                        Creamos el qr
                        =============================================*/
                        $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                        $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                        $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                        if (!file_exists($rta_qr)) {
                            mkdir($rta_qr, 0777, true);
                        }

                        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                        /*=============================================
                        Creamos el pdf
                        =============================================*/
                        $registro = new ControladorPdf();
                        $registro->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);
                        
                        if ($registro) {

                            $json = array(

                                "response" => array(
                                    "success" => true,
                                    "status" => 200,
                                    "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    'pdf-a4' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/invoice/a4/' . $nombrexml . '.pdf',
                                ),

                            );

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "There was an error creating the PDF",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "cotizacion" && array_filter($arrayRutas)[2] == "ticket") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;

                        /*=============================================
                        Creamos el qr
                        =============================================*/
                        $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                        $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                        $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                        if (!file_exists($rta_qr)) {
                            mkdir($rta_qr, 0777, true);
                        }

                        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                        /*=============================================
                        Creamos el pdf
                        =============================================*/
                        $registro = new ControladorPdf();
                        $registro->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                        if ($registro) {

                            $json = array(

                                "response" => array(
                                    "success" => true,
                                    "status" => 200,
                                    "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    'pdf-ticket' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/invoice/ticket/' . $nombrexml . '.pdf',
                                ),

                            );

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "There was an error creating the PDF",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "cotizacion" && array_filter($arrayRutas)[2] == "pdf") /* Cotizacion */ {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;

                        /*=============================================
                        Creamos el qr
                        =============================================*/
                        $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                        $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                        $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                        if (!file_exists($rta_qr)) {
                            mkdir($rta_qr, 0777, true);
                        }

                        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                        /*=============================================
                        Creamos el pdf
                        =============================================*/
                        $registro = new ControladorPdf();
                        $registro->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);
                        $registro->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                        if ($registro) {

                            $json = array(

                                "response" => array(
                                    "success" => true,
                                    "status" => 200,
                                    "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    'pdf-a4' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/invoice/a4/' . $nombrexml . '.pdf',
                                    'pdf-ticket' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/invoice/ticket/' . $nombrexml . '.pdf',
                                ),

                            );

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "There was an error creating the PDF",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "perception" && array_filter($arrayRutas)[2] == "create") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos los documentos generados con el plan
                        =============================================*/
                        if ($documentosEmpresa > $totalDocs) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the documents allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            /*=============================================
                            Capturar datos del json
                            =============================================*/
                            $company = json_encode($dataCompany);
                            $comprobante = $data->comprobante;
                            $client = $data->cliente;
                            $documentos = $data->documentos;
                            $ruta = 'documents/xml/' . $dataCompany["ruc"] . '/unsigned/';

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta)) {
                                mkdir($ruta, 0777, true);
                            }

                            /*=============================================
                            Indicamos el nombre del xml
                            =============================================*/
                            $nombrexml = $dataCompany["ruc"] . '-' . $data->comprobante->tipoDoc . '-' . $data->comprobante->serie . '-' . $data->comprobante->correlativo;

                            /*=============================================
                            Creamos el XML
                            =============================================*/
                            $registro = new ControladorXML();
                            $registro->CrearXMLPercepcion($ruta . $nombrexml, $company, $client, $comprobante, $documentos);

                            /*=============================================
                            Creamos el qr
                            =============================================*/
                            $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                            $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                            $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                            if (!file_exists($rta_qr)) {
                                mkdir($rta_qr, 0777, true);
                            }

                            QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                            /*=============================================
                            Firmamos el XML
                            =============================================*/
                            $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';

                            if (!file_exists($rutaSigned)) {
                                mkdir($rutaSigned, 0777, true);
                            }

                            $firmado = new ControladorSunat();
                            $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($registro) {

                                $json = array(

                                    'response' => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'XML ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            }

                            /*=============================================
                            Actualizamos el contador de documentos
                            =============================================*/
                            $current_period = date('m-Y');

                            // Verificamos si el período actual ya está presente en el JSON
                            $found = false;
                            // Decodificar el JSON
                            $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                            $datosConsumo = json_decode($jsonConsumo, true);
                            // Recorremos el array
                            foreach ($datosConsumo as &$item) {

                                if ($item['periodo'] === $current_period) {

                                    $item['documentos'] = $item['documentos'] + 1;
                                    $found = true;
                                    break;

                                }

                            }
                            // Si el período actual no está presente, agregamos un nuevo elemento al array
                            if (!$found) {

                                $arr = $datosConsumo;
                                array_push($arr, array('periodo' => $current_period, 'consultas' => 0, 'documentos' => 1));
                                $datosConsumo = $arr;
                            }
                            // Codificar de nuevo el JSON
                            $jsonConsumo = json_encode($datosConsumo);

                            $data = array(

                                "consumo_empresa" => $jsonConsumo,

                            );
                            $updatDocument = PutModel::putData("empresas", $data, $accessToken, "token_empresa");

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "perception" && array_filter($arrayRutas)[2] == "send") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);

                        /*=============================================
                        Indicamos el nombre del xml
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $data->comprobante->tipoDoc . '-' . $data->comprobante->serie . '-' . $data->comprobante->correlativo;
                        $ruta_archivo_xml = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';
                        $rutaCompleta = $ruta_archivo_xml . $nombrexml . '.XML';

                        /*=============================================
                        Validamos que exista el XML firmado para el envio
                        =============================================*/
                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Indicamos la ruta donde se almacena el cdr
                            =============================================*/
                            $ruta_archivo_cdr = 'documents/cdr/' . $dataCompany["ruc"] . '/';

                            /*=============================================
                            Creamos la carpeta del cdr si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_cdr)) {
                                mkdir($ruta_archivo_cdr, 0777, true);
                            }

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_xml)) {
                                mkdir($ruta_archivo_xml, 0777, true);
                            }

                            /*=============================================
                            Enviamos el comprobante
                            =============================================*/
                            $api = new ControladorSunat();
                            $api->EnviarComprobanteElectronicoPercepcion($company, $nombrexml, $ruta_archivo_xml, $ruta_archivo_cdr, "documents/");

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($api->success == true) {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 200,
                                        'hash' => $api->hash,
                                        'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $api->xml,
                                        'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $api->xml,
                                        'cdr' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/cdr/' . $dataCompany["ruc"] . '/' . $api->cdrb64,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 400,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We cannot send the document because it does not exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "retention" && array_filter($arrayRutas)[2] == "create") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos los documentos generados con el plan
                        =============================================*/
                        if ($documentosEmpresa > $totalDocs) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the documents allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            /*=============================================
                            Capturar datos del json
                            =============================================*/
                            $company = json_encode($dataCompany);
                            $comprobante = $data->comprobante;
                            $client = $data->cliente;
                            $documentos = $data->documentos;
                            $ruta = 'documents/xml/' . $dataCompany["ruc"] . '/unsigned/';

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta)) {
                                mkdir($ruta, 0777, true);
                            }

                            /*=============================================
                            Indicamos el nombre del xml
                            =============================================*/
                            $nombrexml = $dataCompany["ruc"] . '-' . $data->comprobante->tipoDoc . '-' . $data->comprobante->serie . '-' . $data->comprobante->correlativo;

                            /*=============================================
                            Creamos el XML
                            =============================================*/
                            $registro = new ControladorXML();
                            $registro->CrearXMLRetencion($ruta . $nombrexml, $company, $client, $comprobante, $documentos);

                            /*=============================================
                            Creamos el qr
                            =============================================*/
                            $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                            $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                            $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                            if (!file_exists($rta_qr)) {
                                mkdir($rta_qr, 0777, true);
                            }

                            QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                            /*=============================================
                            Firmamos el XML
                            =============================================*/
                            $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';

                            if (!file_exists($rutaSigned)) {
                                mkdir($rutaSigned, 0777, true);
                            }

                            $firmado = new ControladorSunat();
                            $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($registro) {

                                $json = array(

                                    'response' => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'XML ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            }

                            /*=============================================
                            Actualizamos el contador de documentos
                            =============================================*/
                            $current_period = date('m-Y');

                            // Verificamos si el período actual ya está presente en el JSON
                            $found = false;
                            // Decodificar el JSON
                            $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                            $datosConsumo = json_decode($jsonConsumo, true);
                            // Recorremos el array
                            foreach ($datosConsumo as &$item) {

                                if ($item['periodo'] === $current_period) {

                                    $item['documentos'] = $item['documentos'] + 1;
                                    $found = true;
                                    break;

                                }

                            }
                            // Si el período actual no está presente, agregamos un nuevo elemento al array
                            if (!$found) {

                                $arr = $datosConsumo;
                                array_push($arr, array('periodo' => $current_period, 'consultas' => 0, 'documentos' => 1));
                                $datosConsumo = $arr;
                            }
                            // Codificar de nuevo el JSON
                            $jsonConsumo = json_encode($datosConsumo);

                            $data = array(

                                "consumo_empresa" => $jsonConsumo,

                            );
                            $updatDocument = PutModel::putData("empresas", $data, $accessToken, "token_empresa");

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "voided" && array_filter($arrayRutas)[2] == "send") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos los documentos generados con el plan
                        =============================================*/
                        if ($documentosEmpresa > $totalDocs) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the documents allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            /*=============================================
                            Capturar datos
                            =============================================*/
                            $company = json_encode($dataCompany);
                            $cabecera = $data->cabecera;
                            $items = $data->items;
                            $ruta = "documents/xml/" . $dataCompany["ruc"] . "/unsigned/";

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta)) {
                                mkdir($ruta, 0777, true);
                            }

                            /*=============================================
                            Indicamos el nombre del xml
                            =============================================*/
                            $nombrexml = $dataCompany["ruc"] . '-' . $data->cabecera->tipodoc . '-' . $data->cabecera->serie . '-' . $data->cabecera->correlativo;

                            /*=============================================
                            Creamos el XML
                            =============================================*/
                            $registroBaja = new ControladorXML();
                            $registroBaja->CrearXmlBajaDocumentos($company, $cabecera, $items, $ruta . $nombrexml);

                            /*=============================================
                            Firmamos el XML
                            =============================================*/
                            $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';
                            $firmado = new ControladorSunat();
                            $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($registroBaja) {

                                $json = array(

                                    'response' => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'XML ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            }

                            /*=============================================
                            Actualizamos el contador de documentos
                            =============================================*/
                            $current_period = date('m-Y');

                            // Verificamos si el período actual ya está presente en el JSON
                            $found = false;
                            // Decodificar el JSON
                            $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                            $datosConsumo = json_decode($jsonConsumo, true);
                            // Recorremos el array
                            foreach ($datosConsumo as &$item) {

                                if ($item['periodo'] === $current_period) {

                                    $item['documentos'] = $item['documentos'] + 1;
                                    $found = true;
                                    break;

                                }

                            }
                            // Si el período actual no está presente, agregamos un nuevo elemento al array
                            if (!$found) {

                                $arr = $datosConsumo;
                                array_push($arr, array('periodo' => $current_period, 'consultas' => 0, 'documentos' => 1));
                                $datosConsumo = $arr;
                            }
                            // Codificar de nuevo el JSON
                            $jsonConsumo = json_encode($datosConsumo);

                            $dataUpt = array(

                                "consumo_empresa" => $jsonConsumo,

                            );
                            $updatDocument = PutModel::putData("empresas", $dataUpt, $accessToken, "token_empresa");

                            /*=============================================
                            Insertamos el documento
                            =============================================*/
                            $dataPost = array(

                                "id_empresa_voided" => $clEmpresas[0]->id_empresa,
                                "type_voided" => $data->cabecera->tipodoc,
                                "serie_voided" => $data->cabecera->serie,
                                "number_voided" => $data->cabecera->correlativo,
                                "emision_voided" => $data->cabecera->fechaEmision,
                                "items_voided" => json_encode($data->items),
                                "status_sunat_voided" => "Pendiente",
                                "creado_voided" => date("Y-m-d"),

                            );
                            $insertDocument = PostModel::postData("voideds", $dataPost);

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "voided" && array_filter($arrayRutas)[2] == "status") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $cabecera = $data->cabecera;

                        /*=============================================
                        Indicamos el nombre del xml
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $cabecera->tipodoc . '-' . $cabecera->serie . '-' . $cabecera->correlativo;
                        $ruta_archivo_xml = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';
                        $rutaCompleta = $ruta_archivo_xml . $nombrexml . '.XML';

                        /*=============================================
                        Validamos que exista el XML firmado para el envio
                        =============================================*/
                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Indicamos la ruta donde se almacena el cdr
                            =============================================*/
                            $ruta_archivo_cdr = 'documents/cdr/' . $dataCompany["ruc"] . '/';

                            /*=============================================
                            Creamos la carpeta del cdr si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_cdr)) {
                                mkdir($ruta_archivo_cdr, 0777, true);
                            }

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_xml)) {
                                mkdir($ruta_archivo_xml, 0777, true);
                            }

                            /*=============================================
                            Enviamos el comprobante
                            =============================================*/
                            $api = new ControladorSunat();
                            $ticket = $api->EnviarResumenComprobantes($company, $nombrexml, $ruta_archivo_xml, "documents/");

                            /*=============================================
                            Obtenemos los datos del comprobante
                            =============================================*/
                            $datos_comprobante = array(
                                'codigocomprobante' => $cabecera->tipodoc,
                                'serie' => $cabecera->serie,
                                'correlativo' => $cabecera->correlativo,
                            );

                            /*=============================================
                            Consultamos el ticket
                            =============================================*/
                            $api->ConsultarTicket($company, $cabecera, $nombrexml, $ticket, $ruta_archivo_xml, $ruta_archivo_cdr, $datos_comprobante);

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($api->success == true) {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 200,
                                        'hash' => $api->hash,
                                        'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $api->xml,
                                        'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $api->xml,
                                        'cdr' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/cdr/' . $dataCompany["ruc"] . '/' . $api->cdrb64,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 400,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We cannot send the document because it does not exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "summary" && array_filter($arrayRutas)[2] == "send") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos los documentos generados con el plan
                        =============================================*/
                        if ($documentosEmpresa > $totalDocs) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the documents allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            /*=============================================
                            Capturar datos
                            =============================================*/
                            $company = json_encode($dataCompany);
                            $cabecera = $data->cabecera;
                            $items = $data->items;
                            $ruta = "documents/xml/" . $dataCompany["ruc"] . "/unsigned/";

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta)) {
                                mkdir($ruta, 0777, true);
                            }

                            /*=============================================
                            Indicamos el nombre del xml
                            =============================================*/
                            $nombrexml = $dataCompany["ruc"] . '-' . $data->cabecera->tipodoc . '-' . $data->cabecera->serie . '-' . $data->cabecera->correlativo;

                            /*=============================================
                            Creamos el XML
                            =============================================*/
                            $registroBaja = new ControladorXML();
                            $registroBaja->CrearXMLResumenDocumentos($company, $cabecera, $items, $ruta . $nombrexml);

                            /*=============================================
                            Firmamos el XML
                            =============================================*/
                            $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';
                            $firmado = new ControladorSunat();
                            $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($registroBaja) {

                                $json = array(

                                    'response' => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'XML ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            }

                            /*=============================================
                            Actualizamos el contador de documentos
                            =============================================*/
                            $current_period = date('m-Y');

                            // Verificamos si el período actual ya está presente en el JSON
                            $found = false;
                            // Decodificar el JSON
                            $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                            $datosConsumo = json_decode($jsonConsumo, true);
                            // Recorremos el array
                            foreach ($datosConsumo as &$item) {

                                if ($item['periodo'] === $current_period) {

                                    $item['documentos'] = $item['documentos'] + 1;
                                    $found = true;
                                    break;

                                }

                            }
                            // Si el período actual no está presente, agregamos un nuevo elemento al array
                            if (!$found) {

                                $arr = $datosConsumo;
                                array_push($arr, array('periodo' => $current_period, 'consultas' => 0, 'documentos' => 1));
                                $datosConsumo = $arr;
                            }
                            // Codificar de nuevo el JSON
                            $jsonConsumo = json_encode($datosConsumo);

                            $dataUpt = array(

                                "consumo_empresa" => $jsonConsumo,

                            );
                            $updatDocument = PutModel::putData("empresas", $dataUpt, $accessToken, "token_empresa");

                            /*=============================================
                            Insertamos el documento
                            =============================================*/
                            $dataPost = array(

                                "id_empresa_summary" => $clEmpresas[0]->id_empresa,
                                "type_summary" => $data->cabecera->tipodoc,
                                "serie_summary" => $data->cabecera->serie,
                                "number_summary" => $data->cabecera->correlativo,
                                "emision_summary" => $data->cabecera->fechaEmision,
                                "items_summary" => json_encode($data->items),
                                "status_sunat_summary" => "Pendiente",
                                "creado_summary" => date("Y-m-d"),

                            );
                            $insertDocument = PostModel::postData("summaries", $dataPost);

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "summary" && array_filter($arrayRutas)[2] == "status") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $cabecera = $data->cabecera;

                        /*=============================================
                        Indicamos el nombre del xml
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $cabecera->tipodoc . '-' . $cabecera->serie . '-' . $cabecera->correlativo;
                        $ruta_archivo_xml = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';
                        $rutaCompleta = $ruta_archivo_xml . $nombrexml . '.XML';

                        /*=============================================
                        Validamos que exista el XML firmado para el envio
                        =============================================*/
                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Indicamos la ruta donde se almacena el cdr
                            =============================================*/
                            $ruta_archivo_cdr = 'documents/cdr/' . $dataCompany["ruc"] . '/';

                            /*=============================================
                            Creamos la carpeta del cdr si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_cdr)) {
                                mkdir($ruta_archivo_cdr, 0777, true);
                            }

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_xml)) {
                                mkdir($ruta_archivo_xml, 0777, true);
                            }

                            /*=============================================
                            Enviamos el comprobante
                            =============================================*/
                            $api = new ControladorSunat();
                            $ticket = $api->EnviarResumenComprobantes($company, $nombrexml, $ruta_archivo_xml, "documents/");

                            /*=============================================
                            Obtenemos los datos del comprobante
                            =============================================*/
                            $datos_comprobante = array(
                                'codigocomprobante' => $cabecera->tipodoc,
                                'serie' => $cabecera->serie,
                                'correlativo' => $cabecera->correlativo,
                            );

                            /*=============================================
                            Consultamos el ticket
                            =============================================*/
                            $api->ConsultarTicket($company, $cabecera, $nombrexml, $ticket, $ruta_archivo_xml, $ruta_archivo_cdr, $datos_comprobante);

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($api->success == true) {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 200,
                                        'hash' => $api->hash,
                                        'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $api->xml,
                                        'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $api->xml,
                                        'cdr' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/cdr/' . $dataCompany["ruc"] . '/' . $api->cdrb64,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 400,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We cannot send the document because it does not exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "despatch" && array_filter($arrayRutas)[2] == "remitent") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Validamos el entorno
                        =============================================*/
                        if ($dataCompany["modo"] == "produccion") {

                            /*=============================================
                            Comparamos los documentos generados con el plan
                            =============================================*/
                            if ($documentosEmpresa > $totalDocs) {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "You have exceeded the documents allowed in your plan",
                                    ),

                                );

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                            } else {

                                /*=============================================
                                Capturar datos
                                =============================================*/
                                $company = json_encode($dataCompany);
                                $datosGuia = $data->datosGuia;
                                $datosEnvio = $data->datosEnvio;
                                $details = $data->items;
                                $ruta = 'documents/xml/' . $dataCompany["ruc"] . '/unsigned/';

                                /*=============================================
                                Creamos la carpeta del xml si no existe
                                =============================================*/
                                if (!file_exists($ruta)) {
                                    mkdir($ruta, 0777, true);
                                }

                                /*=============================================
                                Indicamos el nombre del xml
                                =============================================*/
                                $nombrexml = $dataCompany["ruc"] . '-' . $datosGuia->tipoDoc . '-' . $datosGuia->serie . '-' . $datosGuia->correlativo;

                                /*=============================================
                                Creamos el XML
                                =============================================*/
                                $registro = new ControladorXML();
                                $registro->CrearXMLGuiaRemision($ruta . $nombrexml, $company, $datosGuia, $datosEnvio, $details);

                                /*=============================================
                                Creamos el qr
                                =============================================*/
                                $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                                $text_qr = $dataCompany["ruc"] . "|" . $datosGuia->tipoDoc . " | " . $datosGuia->serie . " | " . $datosGuia->correlativo;
                                $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                                if (!file_exists($rta_qr)) {
                                    mkdir($rta_qr, 0777, true);
                                }

                                QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                                /*=============================================
                                Firmamos el XML
                                =============================================*/
                                $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';

                                if (!file_exists($rutaSigned)) {
                                    mkdir($rutaSigned, 0777, true);
                                }

                                $firmado = new ControladorSunat();
                                $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                                /*=============================================
                                Creamos el pdf
                                =============================================*/
                                $crPdfA4 = new ControladorPdf();
                                $crPdfA4->CrearPdfGuia($company, $datosGuia, $datosEnvio, $details, $protocol);

                                /*=============================================
                                Retornamos la respuesta
                                =============================================*/
                                if ($registro) {

                                    $json = array(

                                        "response" => array(
                                            "success" => true,
                                            "status" => 200,
                                            "xml-unsigned" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $nombrexml . '.XML',
                                            "xml-signed" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $nombrexml . '.XML',
                                            "pdf-a4" => $crPdfA4->pdf,
                                            "message" => "Document " . $nombrexml . " created successfully",
                                        ),

                                    );

                                }

                                /*=============================================
                                Actualizamos el contador de documentos
                                =============================================*/
                                $current_period = date('m-Y');

                                // Verificamos si el período actual ya está presente en el JSON
                                $found = false;
                                // Decodificar el JSON
                                $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                                $datosConsumo = json_decode($jsonConsumo, true);
                                // Recorremos el array
                                foreach ($datosConsumo as &$item) {

                                    if ($item['periodo'] === $current_period) {

                                        $item['documentos'] = $item['documentos'] + 1;
                                        $found = true;
                                        break;

                                    }

                                }
                                // Si el período actual no está presente, agregamos un nuevo elemento al array
                                if (!$found) {

                                    $arr = $datosConsumo;
                                    array_push($arr, array('periodo' => $current_period, 'consultas' => 0, 'documentos' => 1));
                                    $datosConsumo = $arr;
                                }
                                // Codificar de nuevo el JSON
                                $jsonConsumo = json_encode($datosConsumo);

                                $dataUpt = array(

                                    "consumo_empresa" => $jsonConsumo,

                                );
                                $updatDocument = PutModel::putData("empresas", $dataUpt, $accessToken, "token_empresa");

                                /*=============================================
                                Insertamos el documento
                                =============================================*/
                                if (isset($data->datosGuia->conductor)) {

                                    $dataCond = "[" . json_encode($data->datosGuia->conductor) . "]";

                                } else {

                                    $dataCond = "";

                                }

                                $dataPost = array(

                                    "id_empresa_despatch" => $clEmpresas[0]->id_empresa,
                                    "serie_despatch" => $data->datosGuia->serie,
                                    "number_despatch" => $data->datosGuia->correlativo,
                                    "emision_despatch" => $data->datosGuia->fechaEmision . " " . $data->datosGuia->horaEmision,
                                    "type_despatch" => $data->datosGuia->tipoDoc,
                                    "observation_despatch" => $data->datosGuia->observacion,
                                    "docbaja_despatch" => "[" . json_encode($data->datosGuia->docBaja) . "]",
                                    "reldoc_despatch" => "[" . json_encode($data->datosGuia->relDoc) . "]",
                                    "recipient_despatch" => "[" . json_encode($data->datosGuia->destinatario) . "]",
                                    "third_despatch" => "[" . json_encode($data->datosGuia->terceros) . "]",
                                    "transport_despatch" => "[" . json_encode($data->datosGuia->transportista) . "]",
                                    "conductor_despatch" => $dataCond,
                                    "datasend_despatch" => "[" . json_encode($data->datosEnvio) . "]",
                                    "items_despatch" => json_encode($data->items),
                                    "status_sunat_despatch" => "Pendiente",
                                    "creado_despatch" => date("Y-m-d"),

                                );
                                $insertDocument = PostModel::postData("despatches", $dataPost);

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                //echo json_encode($json, http_response_code($json["response"]["status"]));

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "It is necessary to be in production",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "despatch" && array_filter($arrayRutas)[2] == "transport") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Validamos el entorno
                        =============================================*/
                        if ($dataCompany["modo"] == "produccion") {

                            /*=============================================
                            Comparamos los documentos generados con el plan
                            =============================================*/
                            if ($documentosEmpresa > $totalDocs) {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "You have exceeded the documents allowed in your plan",
                                    ),

                                );

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                            } else {

                                /*=============================================
                                Capturar datos
                                =============================================*/
                                $company = json_encode($dataCompany);
                                $datosGuia = $data->datosGuia;
                                $datosEnvio = $data->datosEnvio;
                                $details = $data->items;
                                $ruta = 'documents/xml/' . $dataCompany["ruc"] . '/unsigned/';

                                /*=============================================
                                Creamos la carpeta del xml si no existe
                                =============================================*/
                                if (!file_exists($ruta)) {
                                    mkdir($ruta, 0777, true);
                                }

                                /*=============================================
                                Indicamos el nombre del xml
                                =============================================*/
                                $nombrexml = $dataCompany["ruc"] . '-' . $datosGuia->tipoDoc . '-' . $datosGuia->serie . '-' . $datosGuia->correlativo;

                                /*=============================================
                                Creamos el XML
                                =============================================*/
                                $registro = new ControladorXML();
                                $registro->CrearXMLGuiaRemisionTransportista($ruta . $nombrexml, $company, $datosGuia, $datosEnvio, $details);

                                /*=============================================
                                Creamos el qr
                                =============================================*/
                                $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                                $text_qr = $dataCompany["ruc"] . "|" . $datosGuia->tipoDoc . " | " . $datosGuia->serie . " | " . $datosGuia->correlativo;
                                $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                                if (!file_exists($rta_qr)) {
                                    mkdir($rta_qr, 0777, true);
                                }

                                QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                                /*=============================================
                                Firmamos el XML
                                =============================================*/
                                $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';

                                if (!file_exists($rutaSigned)) {
                                    mkdir($rutaSigned, 0777, true);
                                }

                                $firmado = new ControladorSunat();
                                $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                                /*=============================================
                                Creamos el pdf
                                =============================================*/
                                $crPdfA4 = new ControladorPdf();
                                $crPdfA4->CrearPdfGuia($company, $datosGuia, $datosEnvio, $details, $protocol);

                                /*=============================================
                                Retornamos la respuesta
                                =============================================*/
                                if ($registro) {

                                    $json = array(

                                        "response" => array(
                                            "success" => true,
                                            "status" => 200,
                                            "xml-unsigned" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $nombrexml . '.XML',
                                            "xml-signed" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $nombrexml . '.XML',
                                            "pdf-a4" => $crPdfA4->pdf,
                                            "message" => "Document " . $nombrexml . " created successfully",
                                        ),

                                    );

                                }

                                /*=============================================
                                Actualizamos el contador de documentos
                                =============================================*/
                                $current_period = date('m-Y');

                                // Verificamos si el período actual ya está presente en el JSON
                                $found = false;
                                // Decodificar el JSON
                                $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                                $datosConsumo = json_decode($jsonConsumo, true);
                                // Recorremos el array
                                foreach ($datosConsumo as &$item) {

                                    if ($item['periodo'] === $current_period) {

                                        $item['documentos'] = $item['documentos'] + 1;
                                        $found = true;
                                        break;

                                    }

                                }
                                // Si el período actual no está presente, agregamos un nuevo elemento al array
                                if (!$found) {

                                    $arr = $datosConsumo;
                                    array_push($arr, array('periodo' => $current_period, 'consultas' => 0, 'documentos' => 1));
                                    $datosConsumo = $arr;
                                }
                                // Codificar de nuevo el JSON
                                $jsonConsumo = json_encode($datosConsumo);

                                $dataUpdt = array(

                                    "consumo_empresa" => $jsonConsumo,

                                );
                                $updatDocument = PutModel::putData("empresas", $dataUpdt, $accessToken, "token_empresa");

                                /*=============================================
                                Insertamos el documento
                                =============================================*/
                                $dataPost = array(

                                    "id_empresa_transportist" => $clEmpresas[0]->id_empresa,
                                    "serie_transportist" => $data->datosGuia->serie,
                                    "number_transportist" => $data->datosGuia->correlativo,
                                    "emision_transportist" => $data->datosGuia->fechaEmision . " " . $data->datosGuia->horaEmision,
                                    "type_transportist" => $data->datosGuia->tipoDoc,
                                    "observation_transportist" => $data->datosGuia->observacion,
                                    "recipient_transportist" => "[" . json_encode($data->datosGuia->destinatario) . "]",
                                    "transport_transportist" => "[" . json_encode($data->datosGuia->transportista) . "]",
                                    "conductor_transportist" => "[" . json_encode($data->datosGuia->conductor) . "]",
                                    "datasend_transportist" => "[" . json_encode($data->datosEnvio) . "]",
                                    "items_transportist" => json_encode($data->items),
                                    "status_sunat_transportist" => "Pendiente",
                                    "creado_transportist" => date("Y-m-d"),

                                );
                                $insertDocument = PostModel::postData("transportists", $dataPost);

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                //echo json_encode($json, http_response_code($json["response"]["status"]));

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "It is necessary to be in production",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "despatch" && array_filter($arrayRutas)[2] == "token") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Validamos el entorno
                        =============================================*/
                        if ($dataCompany["modo"] == "produccion") {

                            /*=============================================
                            Capturar datos
                            =============================================*/
                            $company = json_encode($dataCompany);

                            /*=============================================
                            Generamos el token para el envio
                            =============================================*/
                            $generateToken = new ControladorSunat();
                            $generateToken->generarTokenGuiaRemision($company);

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($generateToken->success == true) {

                                $json = array(

                                    'response' => array(
                                        'success' => $generateToken->success,
                                        'status' => 200,
                                        'token' => $generateToken->token,
                                    ),

                                );

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => false,
                                        'status' => 400,
                                        'message' => $generateToken->message,
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "It is necessary to be in production",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "despatch" && array_filter($arrayRutas)[2] == "send") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Validamos el entorno
                        =============================================*/
                        if ($dataCompany["modo"] == "produccion") {

                            /*=============================================
                            Capturar datos
                            =============================================*/
                            $company = json_encode($dataCompany);

                            /*=============================================
                            Indicamos el nombre del xml
                            =============================================*/
                            $nombrexml = $dataCompany["ruc"] . '-' . $data->comprobante->tipoDoc . '-' . $data->comprobante->serie . '-' . $data->comprobante->correlativo;
                            $ruta_archivo_xml = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';
                            $rutaCompleta = $ruta_archivo_xml . $nombrexml . '.XML';

                            /*=============================================
                            Validamos que exista el XML firmado para el envio
                            =============================================*/
                            if (file_exists($rutaCompleta)) {

                                /*=============================================
                                Creamos la carpeta del xml si no existe
                                =============================================*/
                                if (!file_exists($ruta_archivo_xml)) {
                                    mkdir($ruta_archivo_xml, 0777, true);
                                }

                                /*=============================================
                                Enviamos el comprobante
                                =============================================*/
                                $api = new ControladorSunat();
                                $api->EnviarGuiaRemisionApi($company, $nombrexml, $ruta_archivo_xml);

                                /*=============================================
                                Retornamos la respuesta
                                =============================================*/
                                if ($api->success == true) {

                                    /*=============================================
                                    Consultamos el ticket
                                    =============================================*/
                                    $ticket = $api->data->numTicket;
                                    $ruta_archivo_cdr = 'documents/cdr/' . $dataCompany["ruc"] . '/';
                                    $consultTicket = new ControladorSunat();
                                    $consultTicket->ConsultarTicketGuiaRemisionApi($company, $ticket, $nombrexml, $ruta_archivo_cdr);

                                    $json = array(

                                        "response" => array(
                                            'success' => $consultTicket->success,
                                            'status' => 200,
                                            'hash' => $consultTicket->hash,
                                            'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $consultTicket->xml,
                                            'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $consultTicket->xml,
                                            'cdr' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/cdr/' . $dataCompany["ruc"] . '/' . $consultTicket->cdrb64,
                                            'pdf-a4' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/despatch/a4/' . $nombrexml . '.pdf',
                                            'code' => $consultTicket->code,
                                            'message' => $consultTicket->data,
                                        ),

                                    );

                                } else {

                                    $json = array(

                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "data" => $api->data,
                                        ),

                                    );

                                }

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "We cannot send the document because it does not exist",
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "It is necessary to be in production",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "despatch" && array_filter($arrayRutas)[2] == "consult") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Validamos el entorno
                        =============================================*/
                        if ($dataCompany["modo"] == "produccion") {

                            /*=============================================
                            Capturar datos
                            =============================================*/
                            $company = json_encode($dataCompany);
                            $ruta_archivo_cdr = 'documents/cdr/' . $dataCompany["ruc"] . '/';
                            $ticket = $data->ticket;
                            $nombre = $data->nombreDoc;

                            /*=============================================
                            Creamos la carpeta del cdr si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_cdr)) {
                                mkdir($ruta_archivo_cdr, 0777, true);
                            }

                            /*=============================================
                            Consultamos el ticket
                            =============================================*/
                            $api = new ControladorSunat();
                            $api->ConsultarTicketGuiaRemisionApi($company, $ticket, $nombre, $ruta_archivo_cdr);

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($api->success == true) {

                                $json = array(

                                    "response" => array(
                                        'success' => $api->success,
                                        'status' => 200,
                                        'hash' => $api->hash,
                                        'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $api->xml,
                                        'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $api->xml,
                                        'pdf-a4' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/despatch/a4/' . $nombre . '.pdf',
                                        'cdr' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/cdr/' . $dataCompany["ruc"] . '/' . $api->cdrb64,
                                        'code' => $api->code,
                                        'message' => $api->data,
                                    ),

                                );

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "code" => $api->code,
                                        "message" => $api->data,
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "It is necessary to be in production",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "despatch" && array_filter($arrayRutas)[2] == "a4") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $emisor = json_encode($dataCompany);
                        $datosGuia = $data->datosGuia;
                        $datosEnvio = $data->datosEnvio;
                        $details = $data->items;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $datosGuia->tipoDoc . '-' . $datosGuia->serie . '-' . $datosGuia->correlativo;
                        $ruta_xml = "documents/xml/" . $dataCompany["ruc"] . "/signed/";
                        $rutaCompleta = $ruta_xml . $nombrexml . ".XML";

                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Creamos el pdf
                            =============================================*/
                            $registro = new ControladorPdf();
                            $registro->CrearPdfGuia($emisor, $datosGuia, $datosEnvio, $details, $protocol);

                            if ($registro) {

                                $json = array(

                                    "response" => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "There was an error creating the PDF",
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We can't create the PDF because the document doesn't exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, true);

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "note" && array_filter($arrayRutas)[2] == "create") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos los documentos generados con el plan
                        =============================================*/
                        if ($documentosEmpresa > $totalDocs) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the documents allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            /*=============================================
                            Capturar datos
                            =============================================*/
                            $company = json_encode($dataCompany);
                            $comprobante = $data->comprobante;
                            $client = $data->cliente;
                            $detalle = $data->items;
                            $ruta = "documents/xml/" . $dataCompany["ruc"] . "/unsigned/";

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta)) {
                                mkdir($ruta, 0777, true);
                            }

                            /*=============================================
                            Indicamos el nombre del xml
                            =============================================*/
                            $nombre = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;

                            /*=============================================
                            Generamos el xml
                            =============================================*/
                            $generadoXML = new ControladorXML();

                            /*=============================================
                            Creamos el qr
                            =============================================*/
                            $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                            $text_qr = $dataCompany["ruc"] . "|" . $data->comprobante->tipoDoc . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                            $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombre . ".png";

                            if (!file_exists($rta_qr)) {
                                mkdir($rta_qr, 0777, true);
                            }

                            QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                            /*=============================================
                            Condicionamos el tipo de nota
                            =============================================*/
                            if ($comprobante->tipoDoc == '07') {

                                /*=============================================
                                Generamos el XML
                                =============================================*/
                                $generadoXML->CrearXMLNotaCredito($ruta . $nombre, $company, $client, $comprobante, $detalle);

                            } else {

                                /*=============================================
                                Generamos el XML
                                =============================================*/
                                $generadoXML->CrearXMLNotaDebito($ruta . $nombre, $company, $client, $comprobante, $detalle);

                            }

                            /*=============================================
                            Firmamos el XML
                            =============================================*/
                            $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';

                            if (!file_exists($rutaSigned)) {
                                mkdir($rutaSigned, 0777, true);
                            }

                            $firmado = new ControladorSunat();
                            $firmado->FirmarComprobanteElectronico($company, $nombre, $ruta, $rutaSigned, "documents/");

                            /*=============================================
                            Creamos el pdf A4
                            =============================================*/
                            $crPdfA4 = new ControladorPdf();
                            $crPdfA4->CrearPdfNota('a4', $company, $client, $comprobante, $detalle, $protocol);

                            /*=============================================
                            Creamos el pdf Ticket
                            =============================================*/
                            $crPdfTc = new ControladorPdf();
                            $crPdfTc->CrearPdfNota('ticket', $company, $client, $comprobante, $detalle, $protocol);

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($generadoXML) {

                                $json = array(

                                    'response' => array(
                                        "success" => true,
                                        "status" => 200,
                                        'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $nombre . '.XML',
                                        'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $nombre . '.XML',
                                        "pdf-a4" => $crPdfA4->pdf,
                                        "pdf-ticket" => $crPdfTc->pdf,
                                        "message" => 'Document ' . $nombre . ' created successfully',
                                    ),

                                );

                            }

                            /*=============================================
                            Actualizamos el contador de documentos
                            =============================================*/
                            $current_period = date('m-Y');

                            // Verificamos si el período actual ya está presente en el JSON
                            $found = false;
                            // Decodificar el JSON
                            $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                            $datosConsumo = json_decode($jsonConsumo, true);
                            // Recorremos el array
                            foreach ($datosConsumo as &$item) {

                                if ($item['periodo'] === $current_period) {

                                    $item['documentos'] = $item['documentos'] + 1;
                                    $found = true;
                                    break;

                                }

                            }
                            // Si el período actual no está presente, agregamos un nuevo elemento al array
                            if (!$found) {

                                $arr = $datosConsumo;
                                array_push($arr, array('periodo' => $current_period, 'consultas' => 0, 'documentos' => 1));
                                $datosConsumo = $arr;
                            }
                            // Codificar de nuevo el JSON
                            $jsonConsumo = json_encode($datosConsumo);

                            $data = array(

                                "consumo_empresa" => $jsonConsumo,

                            );
                            $updatDocument = PutModel::putData("empresas", $data, $accessToken, "token_empresa");

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "note" && array_filter($arrayRutas)[2] == "a4") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;

                        /*=============================================
                        Validamos que el tipo de documento sea una nota de credito o debito
                        =============================================*/
                        if ($comprobante->tipoDoc == '07' || $comprobante->tipoDoc == '08') {

                            /*=============================================
                            Validamos que exista el XML para obtener su QR
                            =============================================*/
                            $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
                            $ruta_xml = "documents/xml/" . $dataCompany["ruc"] . "/signed/";
                            $rutaCompleta = $ruta_xml . $nombrexml . ".XML";

                            if (file_exists($rutaCompleta)) {

                                /*=============================================
                                Creamos el pdf
                                =============================================*/
                                $registro = new ControladorPdf();
                                $registro->CrearPdfNota('a4', $company, $client, $comprobante, $details, $protocol);

                                if ($registro) {

                                    $json = array(

                                        "response" => array(
                                            "success" => true,
                                            "status" => 200,
                                            "message" => 'PDF ' . $nombrexml . ' created successfully',
                                        ),

                                    );

                                } else {

                                    $json = array(

                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "message" => "There was an error creating the PDF",
                                        ),

                                    );

                                }

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "We can't create the PDF because the document doesn't exist",
                                    ),

                                );

                            }

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Invalid document type",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "note" && array_filter($arrayRutas)[2] == "ticket") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;

                        /*=============================================
                        Validamos que el tipo de documento sea una nota de credito o debito
                        =============================================*/
                        if ($comprobante->tipoDoc == '07' || $comprobante->tipoDoc == '08') {

                            /*=============================================
                            Validamos que exista el XML para obtener su QR
                            =============================================*/
                            $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
                            $ruta_xml = "documents/xml/" . $dataCompany["ruc"] . "/signed/";
                            $rutaCompleta = $ruta_xml . $nombrexml . ".XML";

                            if (file_exists($rutaCompleta)) {

                                /*=============================================
                                Creamos el pdf
                                =============================================*/
                                $registro = new ControladorPdf();
                                $registro->CrearPdfNota('ticket', $company, $client, $comprobante, $details, $protocol);

                                if ($registro) {

                                    $json = array(

                                        "response" => array(
                                            "success" => true,
                                            "status" => 200,
                                            "message" => 'PDF ' . $nombrexml . ' created successfully',
                                        ),

                                    );

                                } else {

                                    $json = array(

                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "message" => "There was an error creating the PDF",
                                        ),

                                    );

                                }

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "We can't create the PDF because the document doesn't exist",
                                    ),

                                );

                            }

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Invalid document type",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "consult" && array_filter($arrayRutas)[2] == "exchange") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos las consultas realizadas con el plan
                        =============================================*/
                        if ($consultasEmpresa > $totalCons) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the consultations allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            /*=============================================
                            Taremos los datos
                            =============================================*/
                            $data = ControladorPadron::consultTipoCambio();

                            /*=============================================
                            Actualizamos el contador de consultas
                            =============================================*/
                            $current_period = date('m-Y');

                            // Verificamos si el período actual ya está presente en el JSON
                            $found = false;
                            // Decodificar el JSON
                            $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                            $datosConsumo = json_decode($jsonConsumo, true);
                            // Recorremos el array
                            foreach ($datosConsumo as &$item) {

                                if ($item['periodo'] === $current_period) {

                                    $item['consultas'] = $item['consultas'] + 1;
                                    $found = true;
                                    break;

                                }

                            }
                            // Si el período actual no está presente, agregamos un nuevo elemento al array
                            if (!$found) {

                                $arr = $datosConsumo;
                                array_push($arr, array('periodo' => $current_period, 'consultas' => 1, 'documentos' => 0));
                                $datosConsumo = $arr;
                            }
                            // Codificar de nuevo el JSON
                            $jsonConsumo = json_encode($datosConsumo);

                            $data = array(

                                "consumo_empresa" => $jsonConsumo,

                            );
                            $updatConsult = PutModel::putData("empresas", $data, $accessToken, "token_empresa");

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 400,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    }

                } else {

                    $json = array(

                        "response" => array(
                            "success" => false,
                            "status" => 404,
                            "message" => "Not Found",
                        ),

                    );

                    /*=============================================
                    Imprimimos la respuesta
                    =============================================*/
                    echo json_encode($json, http_response_code($json["response"]["status"]));

                    return;

                }

            } else if (count(array_filter($arrayRutas)) == 3) {

                if (array_filter($arrayRutas)[1] == "consult" && array_filter($arrayRutas)[2] == "ruc" && array_filter($arrayRutas)[3] == $arrayRutas[3]) {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos las consultas realizadas con el plan
                        =============================================*/
                        if ($consultasEmpresa > $totalCons) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the consultations allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            if (empty($arrayRutas[3]) || $arrayRutas[3] == '') {

                                $json = array(

                                    'response' => array(
                                        'success' => false,
                                        'status' => 400,
                                        'message' => 'Enter the RUC to search.',
                                    ),

                                );

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                                return;

                            } else {

                                /*=============================================
                                Validamos la longitud
                                =============================================*/
                                if (strlen($arrayRutas[3]) == 11) {

                                    /*=============================================
                                    Taremos los datos
                                    =============================================*/
                                    $value = $arrayRutas[3];
                                    $data = ControladorPadron::consultRuc($value);

                                    /*=============================================
                                    Actualizamos el contador de consultas
                                    =============================================*/
                                    $current_period = date('m-Y');

                                    // Verificamos si el período actual ya está presente en el JSON
                                    $found = false;
                                    // Decodificar el JSON
                                    $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                                    $datosConsumo = json_decode($jsonConsumo, true);
                                    // Recorremos el array
                                    foreach ($datosConsumo as &$item) {

                                        if ($item['periodo'] === $current_period) {

                                            $item['consultas'] = $item['consultas'] + 1;
                                            $found = true;
                                            break;

                                        }

                                    }
                                    // Si el período actual no está presente, agregamos un nuevo elemento al array
                                    if (!$found) {

                                        $arr = $datosConsumo;
                                        array_push($arr, array('periodo' => $current_period, 'consultas' => 1, 'documentos' => 0));
                                        $datosConsumo = $arr;
                                    }
                                    // Codificar de nuevo el JSON
                                    $jsonConsumo = json_encode($datosConsumo);

                                    $data = array(

                                        "consumo_empresa" => $jsonConsumo,

                                    );
                                    $updatConsult = PutModel::putData("empresas", $data, $accessToken, "token_empresa");

                                } else {

                                    $json = array(

                                        'response' => array(
                                            'success' => false,
                                            'status' => 400,
                                            'message' => 'The RUC number entered is invalid.',
                                        ),

                                    );

                                    /*=============================================
                                    Imprimimos la respuesta
                                    =============================================*/
                                    echo json_encode($json, http_response_code($json["response"]["status"]));

                                }

                            }

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    }

                } else if (array_filter($arrayRutas)[1] == "consult" && array_filter($arrayRutas)[2] == "dni" && array_filter($arrayRutas)[3] == $arrayRutas[3]) {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Comparamos las consultas realizadas con el plan
                        =============================================*/
                        if ($consultasEmpresa > $totalCons) {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You have exceeded the consultations allowed in your plan",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            if (!isset($arrayRutas[3]) || $arrayRutas[3] == '') {

                                $json = array(

                                    'response' => array(
                                        'success' => false,
                                        'status' => 400,
                                        'message' => 'Enter the DNI to search.',
                                    ),

                                );

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                                return;

                            } else {

                                /*=============================================
                                Validamos la longitud
                                =============================================*/
                                if (strlen($arrayRutas[3]) == 8) {

                                    /*=============================================
                                    Taremos los datos
                                    =============================================*/
                                    $value = $arrayRutas[3];
                                    $data = ControladorPadron::consultDni($value);

                                    /*=============================================
                                    Actualizamos el contador de consultas
                                    =============================================*/
                                    $current_period = date('m-Y');

                                    // Verificamos si el período actual ya está presente en el JSON
                                    $found = false;
                                    // Decodificar el JSON
                                    $jsonConsumo = $clEmpresas[0]->consumo_empresa;
                                    $datosConsumo = json_decode($jsonConsumo, true);
                                    // Recorremos el array
                                    foreach ($datosConsumo as &$item) {

                                        if ($item['periodo'] === $current_period) {

                                            $item['consultas'] = $item['consultas'] + 1;
                                            $found = true;
                                            break;

                                        }

                                    }
                                    // Si el período actual no está presente, agregamos un nuevo elemento al array
                                    if (!$found) {

                                        $arr = $datosConsumo;
                                        array_push($arr, array('periodo' => $current_period, 'consultas' => 1, 'documentos' => 0));
                                        $datosConsumo = $arr;
                                    }
                                    // Codificar de nuevo el JSON
                                    $jsonConsumo = json_encode($datosConsumo);

                                    $data = array(

                                        "consumo_empresa" => $jsonConsumo,

                                    );
                                    $updatConsult = PutModel::putData("empresas", $data, $accessToken, "token_empresa");

                                } else {

                                    $json = array(

                                        'response' => array(
                                            'success' => false,
                                            'status' => 400,
                                            'message' => 'The DNI number entered is invalid.',
                                        ),

                                    );

                                    /*=============================================
                                    Imprimimos la respuesta
                                    =============================================*/
                                    echo json_encode($json, http_response_code($json["response"]["status"]));

                                }

                            }

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    }

                } else {

                    $json = array(

                        "response" => array(
                            "success" => false,
                            "status" => 404,
                            "message" => "Not Found",
                        ),

                    );

                    /*=============================================
                    Imprimimos la respuesta
                    =============================================*/
                    echo json_encode($json, http_response_code($json["response"]["status"]));

                    return;

                }

            }

        } else if (array_filter($arrayRutas)[1] == "editsuscription") {

            /*=============================================
            Peticiones POST
            =============================================*/
            if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                /*=============================================
                Capturar datos
                =============================================*/
                if (isset($data->file) && !empty($data->file)) {

                    if ($data->type == "image/jpeg" || $data->type == "image/png") {

                        if ($data->id == '' || $data->monto == '' || $data->comprobante == '' || $data->fechaPago == '' || $data->metodoPago == '') {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Complete the fields",
                                ),

                            );

                        } else {

                            if ($data->type == "image/png") {

                                $ext = ".png";

                            } else {

                                $ext = ".jpg";

                            }

                            $newName = base64_encode($dataCompany["ruc"] . "~" . $data->id . "~" . $data->comprobante);
                            $newName2 = base64_encode($dataCompany["ruc"] . "~" . $data->id . "~" . $data->comprobante) . $ext;
                            $idPago = ControladorRutas::generateUniqueId();

                            $datos_suscripcion = array(
                                "trans_suscripcion" => $idPago,
                                "fecha_pago_suscripcion" => $data->fechaPago,
                                "monto_pago_suscripcion" => $data->monto,
                                "medio_pago_suscripcion" => $data->metodoPago,
                                "comprobante_suscripcion" => $data->comprobante,
                                "adjunto_suscripcion" => $newName2,
                                "estado_suscripcion" => "pendiente",
                            );

                            $updateSuscripcion = PutModel::putData("suscripciones", $datos_suscripcion, $data->id, "id_suscripcion");

                            if (isset($updateSuscripcion["comment"]) && $updateSuscripcion["comment"] == "The process was successful") {

                                /*=============================================
                                Creamos la carpeta de pagos si no existe
                                =============================================*/
                                $ruta = 'documents/pagos/' . $dataCompany["ruc"];

                                if (!file_exists($ruta)) {
                                    mkdir($ruta, 0777, true);
                                }

                                /*=============================================
                                Cargamos el archivo en la carpeta creada
                                =============================================*/
                                $upload = FilesController::fileData($data->file, $data->type, $ruta, $newName, null, $data->width, $data->height);

                                $json = array(

                                    'response' => array(
                                        'success' => true,
                                        'status' => 200,
                                        'name' => $newName,
                                        'message' => 'The pay for suscription has been uploaded successfully'
                                    ),

                                );

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "Error loading pay",
                                    ),

                                );

                            }

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));
                    
                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 400,
                                "message" => "Invalid format",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    }

                } else {

                    $json = array(

                        "response" => array(
                            "success" => false,
                            "status" => 400,
                            "message" => "Select a file to upload",
                        ),

                    );

                    /*=============================================
                    Imprimimos la respuesta
                    =============================================*/
                    echo json_encode($json, http_response_code($json["response"]["status"]));

                }

            } else {

                $json = array(

                    "response" => array(
                        "success" => false,
                        "status" => 405,
                        "message" => "Method not allowed",
                    ),

                );

                /*=============================================
                Imprimimos la respuesta
                =============================================*/
                echo json_encode($json, http_response_code($json["response"]["status"]));

                return;

            }

        } else if (array_filter($arrayRutas)[1] == "uploadsuscription") {

            /*=============================================
            Peticiones POST
            =============================================*/
            if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                /*=============================================
                Capturar datos
                =============================================*/
                if (isset($data->file) && !empty($data->file)) {

                    if ($data->type == "image/jpeg" || $data->type == "image/png") {

                        if ($data->type == "image/png") {

                            $ext = ".png";

                        } else {

                            $ext = ".jpg";

                        }

                        /*=============================================
                        Recogemos la suscripcion
                        =============================================*/
                        $getSuscr = GetModel::getDataFilter("suscripciones", "*", "id_suscripcion", $data->id, null, null, null, null);

                        $newName = base64_encode($dataCompany["ruc"] . "~" . $data->id . "~" . $getSuscr[0]->comprobante_suscripcion);
                        $newName2 = base64_encode($dataCompany["ruc"] . "~" . $data->id . "~" . $getSuscr[0]->comprobante_suscripcion) . $ext;
                        $idPago = ControladorRutas::generateUniqueId();

                        $datos_suscripcion = array(
                            "adjunto_suscripcion" => $newName2,
                        );

                        $updateSuscripcion = PutModel::putData("suscripciones", $datos_suscripcion, $data->id, "id_suscripcion");

                        if (isset($updateSuscripcion["comment"]) && $updateSuscripcion["comment"] == "The process was successful") {

                            /*=============================================
                            Creamos la carpeta de pagos si no existe
                            =============================================*/
                            $ruta = 'documents/pagos/' . $dataCompany["ruc"];

                            if (!file_exists($ruta)) {
                                mkdir($ruta, 0777, true);
                            }

                            /*=============================================
                            Cargamos el archivo en la carpeta creada
                            =============================================*/
                            $upload = FilesController::fileData($data->file, $data->type, $ruta, $newName, null, $data->width, $data->height);

                            $json = array(

                                'response' => array(
                                    'success' => true,
                                    'status' => 200,
                                    'name' => $newName,
                                    'message' => 'The pay for suscription has been uploaded successfully',
                                ),

                            );

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Error loading pay",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));
                    
                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 400,
                                "message" => "Invalid format",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    }

                } else {

                    $json = array(

                        "response" => array(
                            "success" => false,
                            "status" => 400,
                            "message" => "Select a file to upload",
                        ),

                    );

                    /*=============================================
                    Imprimimos la respuesta
                    =============================================*/
                    echo json_encode($json, http_response_code($json["response"]["status"]));

                }

            } else {

                $json = array(

                    "response" => array(
                        "success" => false,
                        "status" => 405,
                        "message" => "Method not allowed",
                    ),

                );

                /*=============================================
                Imprimimos la respuesta
                =============================================*/
                echo json_encode($json, http_response_code($json["response"]["status"]));

                return;

            }

        } else {

            /*=============================================
            Cuando no se renueva plan
            =============================================*/
            $json = array(

                "response" => array(
                    "success" => false,
                    "status" => 400,
                    "message" => "Your plan has expired, please make a purchase",
                ),

            );

            /*=============================================
            Imprimimos la respuesta
            =============================================*/
            echo json_encode($json, http_response_code($json["response"]["status"]));

        }

    } else {

        /*=============================================
        Capturar datos de la facturacion
        =============================================*/
        $jsonFacturacion = $validTokenSt[0]->facturacion_configuracion;
        $datosFacturacion = json_decode($jsonFacturacion, true);

        $dataCompany = array(
            /* Para generar XML */
            "tipoDoc" => "6",
            "ruc" => $datosFacturacion[0]['empresa']['ruc'],
            "razonSocial" => $datosFacturacion[0]['empresa']['razonSocial'],
            "nombreComercial" => $datosFacturacion[0]['empresa']['nombreComercial'],
            "address" => array(
                "codigoPais" => "PE",
                "departamento" => $datosFacturacion[0]['empresa']['departamento'],
                "provincia" => $datosFacturacion[0]['empresa']['provincia'],
                "distrito" => $datosFacturacion[0]['empresa']['distrito'],
                "direccion" => $datosFacturacion[0]['empresa']['direccion'],
                "ubigeo" => $datosFacturacion[0]['empresa']['ubigeo'],
            ),
            /* Para enviar XML / Consultar CPE */
            "modo" => $datosFacturacion[0]['sunat']['modo'],
            "usuarioSol" => $datosFacturacion[0]['sunat']['usuarioSol'],
            "claveSol" => $datosFacturacion[0]['sunat']['claveSol'],
            /* Para PDF */
            "telefono" => $datosFacturacion[0]['empresa']['telefono'],
            "email" => $datosFacturacion[0]['empresa']['email'],
            "logo" => $validTokenSt[0]->logo_sistema_configuracion,
            "sistema" => $validTokenSt[0]->web_empresa_configuracion,
            "nombreSistema" => $validTokenSt[0]->nombre_sistema_configuracion
        );

        /*=============================================
        Validamos se envie la cabecera con el token
        =============================================*/
        if (!isset(getallheaders()["Authorization"]) || getallheaders()["Authorization"] != 'Bearer ' . Conexion::apikey()) {

            /*=============================================
            Cuando no viene el token
            =============================================*/
            $json = array(

                "response" => array(
                    "success" => false,
                    "status" => 401,
                    "message" => "Not authorized",
                ),

            );

            /*=============================================
            Imprimimos la respuesta
            =============================================*/
            echo json_encode($json, http_response_code($json["response"]["status"]));

            return;

        } else {

            /*=============================================
            Cuando pasamos solo un indice en el array $arrayRutas
            =============================================*/
            if (count(array_filter($arrayRutas)) == 1 && isset($_SERVER['REQUEST_METHOD'])) {

                $table = explode("?", $arrayRutas[1])[0];

                /*=============================================
                Peticiones GET
                =============================================*/
                if ($_SERVER['REQUEST_METHOD'] == "GET") {

                    include "servicios/get.php";

                }

                /*=============================================
                Peticiones POST
                =============================================*/
                if ($_SERVER['REQUEST_METHOD'] == "POST") {

                    include "servicios/post.php";

                }

                /*=============================================
                Peticiones PUT
                =============================================*/
                if ($_SERVER['REQUEST_METHOD'] == "PUT") {

                    include "servicios/put.php";

                }

                /*=============================================
                Peticiones DELETE
                =============================================*/
                if ($_SERVER['REQUEST_METHOD'] == "DELETE") {

                    include "servicios/delete.php";

                }

            } else if (count(array_filter($arrayRutas)) == 2) {

                if (array_filter($arrayRutas)[1] == "file" && array_filter($arrayRutas)[2] == "upload") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $upload = FilesController::fileData($data->file, $data->type, $data->folder, $data->name, $data->mode, $data->width, $data->height);

                        if ($upload != 'error') {

                            $json = array(

                                "response" => array(
                                    "success" => true,
                                    "status" => 200,
                                    "file" => $upload,
                                    "message" => "File uploaded successfully",
                                ),

                            );

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Error loading file",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 400,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "file" && array_filter($arrayRutas)[2] == "delete") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        if (isset($data->deleteUniqueFile) && !empty($data->deleteUniqueFile)) {

                            $delete = FilesController::deleteUniqData($data->deleteUniqueFile, $data->deleteDir, $data->deleteFol, $data->deleteCod);

                            if ($delete == 'ok') {

                                $json = array(

                                    "response" => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => "File deleted successfully",
                                    ),

                                );

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "Error deleting file",
                                    ),

                                );

                            }

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Select a file to upload",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 400,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "exchange" && array_filter($arrayRutas)[2] == "consult") {
    
                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
        
                        /*=============================================
                        Taremos los datos
                        =============================================*/
                        $data = ControladorPadron::consultTipoCambio();
        
                    } else {
        
                        $json = array(
        
                            "response" => array(
                                "success" => false,
                                "status" => 400,
                                "message" => "Method not allowed",
                            ),
        
                        );
        
                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));
    
                        return;
        
                    }
        
                } else if (array_filter($arrayRutas)[1] == "ruc" && array_filter($arrayRutas)[2] == $arrayRutas[2]) {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        if (empty($arrayRutas[2]) || $arrayRutas[2] == '') {

                            $json = array(

                                'response' => array(
                                    'success' => false,
                                    'status' => 400,
                                    'message' => 'Enter the RUC to search.',
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                            return;

                        } else {

                            /*=============================================
                            Validamos la longitud
                            =============================================*/
                            if (strlen($arrayRutas[2]) == 11) {

                                /*=============================================
                                Taremos los datos
                                =============================================*/
                                $value = $arrayRutas[2];
                                $data = ControladorPadron::consultRuc($value);

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => false,
                                        'status' => 400,
                                        'message' => 'The RUC number entered is invalid.',
                                    ),

                                );

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                            }

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));
                        
                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "dni" && array_filter($arrayRutas)[2] == $arrayRutas[2]) {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        if (!isset($arrayRutas[2]) || $arrayRutas[2] == '') {

                            $json = array(

                                'response' => array(
                                    'success' => false,
                                    'status' => 400,
                                    'message' => 'Enter the DNI to search.',
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                            return;

                        } else {

                            /*=============================================
                            Validamos la longitud
                            =============================================*/
                            if (strlen($arrayRutas[2]) == 8) {

                                /*=============================================
                                Taremos los datos
                                =============================================*/
                                $value = $arrayRutas[2];
                                $data = ControladorPadron::consultDni($value);

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => false,
                                        'status' => 400,
                                        'message' => 'The DNI number entered is invalid.',
                                    ),

                                );

                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                                return;

                            }

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    }

                } else if (array_filter($arrayRutas)[1] == "email" && array_filter($arrayRutas)[2] == "send") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        if ($data->nombre != '' || $data->asunto != '' || $data->email != '' || $data->mensaje != '') {

                            /*=============================================
                            Generamos el correo
                            =============================================*/
                            $name = $data->nombre;
                            $subject = $data->asunto;
                            $email = $data->email;
                            $message = $data->mensaje;
                            $text = $data->text;
                            /* Ruta donde se encuentra el sistema */
                            $url = $data->url;
                            /* Archivos */
                            if($data->adjunto != NULL) {
                                
                                $files = array();

                                foreach ($data->adjunto as $adjunto) {
                                    $files[] = array("archivo" => $adjunto->archivo);
                                }

                            } else {

                                $files = NULL;

                            }

                            $sendEmail = ControladorRutas::sendEmail($name, $subject, $email, $message, $text, $url, $files);

                            if($sendEmail == 'ok') {

                                $json = array(

                                    'response' => array(
                                        'success' => true,
                                        'status' => 200,
                                        'message' => $sendEmail
                                    ),
    
                                );

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => true,
                                        'status' => 400,
                                        'message' => $sendEmail
                                    ),
    
                                );

                            }

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Complete the fields",
                                ),

                            );

                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 400,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "invoice" && array_filter($arrayRutas)[2] == "create") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos del json
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;
                        $ruta = 'documents/xml/' . $dataCompany["ruc"] . '/unsigned/';

                        /*=============================================
                        Creamos la carpeta del xml si no existe
                        =============================================*/
                        if (!file_exists($ruta)) {
                            mkdir($ruta, 0777, true);
                        }

                        /*=============================================
                        Indicamos el nombre del xml
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $data->comprobante->tipoDoc . '-' . $data->comprobante->serie . '-' . $data->comprobante->correlativo;

                        /*=============================================
                        Creamos el XML
                        =============================================*/
                        $registro = new ControladorXML();
                        $registro->CrearXMLFactura($ruta . $nombrexml, $company, $client, $comprobante, $details);

                        /*=============================================
                        Creamos el qr
                        =============================================*/
                        $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                        $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $data->comprobante->serie . " | " . $data->comprobante->correlativo . " | " . $data->comprobante->mtoIGV . " | " . $data->comprobante->total . " | " . $data->comprobante->fechaEmision . " | " . $data->cliente->tipoDoc . " | " . $data->cliente->numDoc . " | ";
                        $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                        if (!file_exists($rta_qr)) {
                            mkdir($rta_qr, 0777, true);
                        }

                        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                        /*=============================================
                        Firmamos el XML
                        =============================================*/
                        $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';

                        if (!file_exists($rutaSigned)) {
                            mkdir($rutaSigned, 0777, true);
                        }

                        $firmado = new ControladorSunat();
                        $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                        /*=============================================
                        Creamos el pdf A4
                        =============================================*/
                        $crPdfA4 = new ControladorPdf();
                        $crPdfA4->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);

                        /*=============================================
                        Creamos el pdf Ticket
                        =============================================*/
                        $crPdfTc = new ControladorPdf();
                        $crPdfTc->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                        /*=============================================
                        Retornamos la respuesta
                        =============================================*/
                        if ($registro) {

                            $json = array(

                                'response' => array(
                                    "success" => true,
                                    "status" => 200,
                                    'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $nombrexml . '.XML',
                                    'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $nombrexml . '.XML',
                                    "pdf-a4" => $crPdfA4->pdf,
                                    "pdf-ticket" => $crPdfTc->pdf,
                                    "message" => 'Document ' . $nombrexml . ' created successfully',
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "invoice" && array_filter($arrayRutas)[2] == "send") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);

                        /*=============================================
                        Indicamos el nombre del xml
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $data->comprobante->tipoDoc . '-' . $data->comprobante->serie . '-' . $data->comprobante->correlativo;
                        $ruta_archivo_xml = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';
                        $rutaCompleta = $ruta_archivo_xml . $nombrexml . '.XML';

                        /*=============================================
                        Validamos que exista el XML firmado para el envio
                        =============================================*/
                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Indicamos la ruta donde se almacena el cdr
                            =============================================*/
                            $ruta_archivo_cdr = 'documents/cdr/' . $dataCompany["ruc"] . '/';

                            /*=============================================
                            Creamos la carpeta del cdr si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_cdr)) {
                                mkdir($ruta_archivo_cdr, 0777, true);
                            }

                            /*=============================================
                            Creamos la carpeta del xml si no existe
                            =============================================*/
                            if (!file_exists($ruta_archivo_xml)) {
                                mkdir($ruta_archivo_xml, 0777, true);
                            }

                            /*=============================================
                            Enviamos el comprobante
                            =============================================*/
                            $api = new ControladorSunat();
                            $api->EnviarComprobanteElectronico($company, $nombrexml, $ruta_archivo_xml, $ruta_archivo_cdr, "documents/");

                            if ($data->comprobante->tipoDoc == "07" || $data->comprobante->tipoDoc == "08") {

                                $urlDoc = "note";

                            } else {

                                $urlDoc = "invoice";

                            }

                            /*=============================================
                            Retornamos la respuesta
                            =============================================*/
                            if ($api->success == true) {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 200,
                                        'hash' => $api->hash,
                                        'xml-unsigned' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $api->xml,
                                        'xml-signed' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $api->xml,
                                        'pdf-a4' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/' . $urlDoc . '/a4/' . $nombrexml . '.pdf',
                                        'pdf-ticket' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/pdf/' . $dataCompany["ruc"] . '/' . $urlDoc . '/ticket/' . $nombrexml . '.pdf',
                                        'cdr' => $protocol . $_SERVER['HTTP_HOST'] . '/documents/cdr/' . $dataCompany["ruc"] . '/' . $api->cdrb64,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            } else {

                                $json = array(

                                    'response' => array(
                                        'success' => $api->success,
                                        'status' => 400,
                                        'code' => $api->code,
                                        'message' => $api->mensajeError,
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We cannot send the document because it does not exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "invoice" && array_filter($arrayRutas)[2] == "a4") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
                        $ruta_xml = "documents/xml/" . $dataCompany["ruc"] . "/signed/";
                        $rutaCompleta = $ruta_xml . $nombrexml . ".XML";

                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Creamos el pdf
                            =============================================*/
                            $registro = new ControladorPdf();
                            $registro->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);

                            if ($registro) {

                                $json = array(

                                    "response" => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "There was an error creating the PDF",
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We can't create the PDF because the document doesn't exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "invoice" && array_filter($arrayRutas)[2] == "ticket") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        $company = json_encode($dataCompany);
                        $comprobante = $data->comprobante;
                        $client = $data->cliente;
                        $details = $data->items;
                        $extra = $data->extra;

                        /*=============================================
                        Validamos que exista el XML para obtener su QR
                        =============================================*/
                        $nombrexml = $dataCompany["ruc"] . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
                        $ruta_xml = "documents/xml/" . $dataCompany["ruc"] . "/signed/";
                        $rutaCompleta = $ruta_xml . $nombrexml . ".XML";

                        if (file_exists($rutaCompleta)) {

                            /*=============================================
                            Creamos el pdf
                            =============================================*/
                            $registro = new ControladorPdf();
                            $registro->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                            if ($registro) {

                                $json = array(

                                    "response" => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => 'PDF ' . $nombrexml . ' created successfully',
                                    ),

                                );

                            } else {

                                $json = array(

                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "There was an error creating the PDF",
                                    ),

                                );

                            }

                        } else {

                            $json = array(

                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "We can't create the PDF because the document doesn't exist",
                                ),

                            );

                        }

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "cron" && array_filter($arrayRutas)[2] == "suscriptions") {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Recogemos los datos de las suscripciones
                        =============================================*/
                        $allSuscriptions = GetModel::getRelData("suscripciones,planes,empresas,usuarios", "suscripcion,plan,empresa,usuario", "*", "id_suscripcion", "DESC", null, null);
                    
                        if ($allSuscriptions) {
                    
                            /*=============================================
                            Agrupamos las suscripciones por empresa
                            =============================================*/
                            $suscripcionesPorEmpresa = array();
                            foreach ($allSuscriptions as $key => $value) {
                                if (!isset($suscripcionesPorEmpresa[$value->id_empresa_suscripcion])) {
                                    $suscripcionesPorEmpresa[$value->id_empresa_suscripcion] = array();
                                }
                                $suscripcionesPorEmpresa[$value->id_empresa_suscripcion][] = $value;
                            }
                    
                            /*=============================================
                            Ejecutamos el proceso de suscripciones
                            =============================================*/
                            $responseData = array(); // Array para almacenar los datos de respuesta exitosos
                            $errorMessage = null;    // Variable para almacenar un mensaje de error si no se encuentra ninguna suscripción
                    
                            foreach ($suscripcionesPorEmpresa as $idEmpresa => $suscripciones) {
                                $ultimaSuscripcion = reset($suscripciones);
                    
                                if ($ultimaSuscripcion && $ultimaSuscripcion->estado_suscripcion == "pagado") {
                    
                                    /*=============================================
                                    Formateamos la fecha de próxima facturación
                                    =============================================*/
                                    if ($ultimaSuscripcion->proxima_facturacion_empresa == '0000-00-00' || $ultimaSuscripcion->proxima_facturacion_empresa == '1969-12-31') {
                                        $prxFact = '----';
                                        $fechaValidacion = '----';
                                    } else {
                                        $prxFact = ControladorRutas::fechaEsShort($ultimaSuscripcion->proxima_facturacion_empresa);
                                        $fechaExpira = $prxFact;
                                        $fechaValidacion = date('Y-m-d', strtotime('-5 days', strtotime($fechaExpira)));
                                    }
                    
                                    /*=============================================
                                    Validamos la fecha actual y la fecha de validación
                                    =============================================*/
                                    if (strtotime($fechaHoy) >= strtotime($fechaValidacion)) {
                    
                                        /*=============================================
                                        Insertamos el registro de la suscripción
                                        =============================================*/
                                        $insertSuscription = array(
                                            "id_empresa_suscripcion" => $ultimaSuscripcion->id_empresa_suscripcion,
                                            "id_usuario_suscripcion" => $ultimaSuscripcion->id_usuario_suscripcion,
                                            "id_plan_suscripcion" => $ultimaSuscripcion->id_plan_empresa,
                                            "fecha_emision_suscripcion" => $fechaHoy,
                                            "monto_pago_suscripcion" => $ultimaSuscripcion->precio_plan,
                                            "estado_suscripcion" => "no pagado",
                                            "creado_suscripcion" => $fechaHoy,
                                        );
                                        $responseSusc = PostModel::postData("suscripciones", $insertSuscription);
                    
                                        if ($responseSusc["comment"] == "The process was successful") {
                    
                                            /*=============================================
                                            Enviar notificación por correo si está habilitado
                                            =============================================*/
                                            $messageEmail = 'No notification was sent by mail';
                                            if ($validTokenSt[0]->activo_correo_configuracion == "si") {
                                                $name = $ultimaSuscripcion->alias_usuario;
                                                $subject = "Proximo pago | " . $ultimaSuscripcion->ruc_empresa;
                                                $email = $ultimaSuscripcion->email_usuario;
                                                $message = "Te informamos que la suscripcion de la empresa " . $ultimaSuscripcion->ruc_empresa . " finaliza el " . $prxFact . ", para realizar el pago ingresa a tu panel.";
                                                $text = "Ingresa a tu panel";
                                                $url = ControladorRutas::path();
                    
                                                $sendEmail = ControladorRutas::sendEmail($name, $subject, $email, $message, $text, $url, NULL);

                                                if ($sendEmail == 'ok') {

                                                    $messageEmail = 'It was notified by mail';

                                                } else {

                                                    $messageEmail = "Error sending mail:" . $sendEmail;

                                                }

                                            }
                    
                                            /*=============================================
                                            Enviar a supabase si está habilitado
                                            =============================================*/
                                            if ($supabase == "si" && $ultimaSuscripcion->supabase_suscripcion != '') {

                                                /*=============================================
                                                Obtenemos los datos de supabase
                                                =============================================*/
                                                foreach (json_decode($ultimaSuscripcion->supabase_suscripcion) as $key => $elementSupaBase) {
                    
                                                    $idUsuario = $elementSupaBase->id_usuario;
                                                    $idEmpresa = $elementSupaBase->id_empresa;
                                                    $idSuscripcion = $elementSupaBase->id_suscripcion;
                    
                                                }

                                                $supUrlSuscr = $supabaseUrl . "/rest/v1/tb_suscripciones";
                                                $methodSuscr = "POST";
                                                $fieldsSuscr = array(
                                                    "scr_fecha_emision" => $fechaHoy,
                                                    "scr_estado_suscripcion" => "no pagado",
                                                    "src_adjunto_suscripcion" => "",
                                                    "scr_monto_pago" => $ultimaSuscripcion->precio_plan,
                                                    "scr_numero_operacion" => "",
                                                    "id_back_plan" => $ultimaSuscripcion->id_plan_suscripcion,
                                                    "id_back_empresa" => $ultimaSuscripcion->id_empresa_suscripcion,
                                                    "id_back_suscripcion" => $responseSusc["lastId"],
                                                    "id_back_usuario_suscripcion" => $ultimaSuscripcion->id_usuario_suscripcion,
                                                );
                                                $dataSuscr = json_encode($fieldsSuscr);
                                                $responseSuscr = ControladorCurl::supabase($supUrlSuscr, $methodSuscr, $dataSuscr, $supabaseKey);

                                                if(is_object($responseSuscr) && isset($responseSuscr->code)) {

                                                    $msgSupabase = $responseSuscr;
                                    
                                                } else {
                                    
                                                    $supabaseSuscripcion = '[{"id_usuario":"'.$idUsuario.'","id_empresa":"'.$idEmpresa.'","id_suscripcion":"'.$responseSuscr[0]->id.'"}]';
                                                    $data = array(
                                                        "supabase_suscripcion" => $supabaseSuscripcion,
                                                    );
                                                    $updatSusc = PutModel::putData("suscripciones", $data, $responseSusc["lastId"], "id_suscripcion");
                                        
                                                    $msgSupabase = "It was inserted into Supabase";
                                    
                                                }
                                            } else {
                                                
                                                $msgSupabase = "Not connected to Supabase";

                                            }
                    
                                            /*=============================================
                                            Agregamos el resultado de esta suscripción al array de respuesta
                                            =============================================*/
                                            $responseData[] = array(
                                                "lastId" => $responseSusc["lastId"],
                                                //"comment" => "The process was successful",
                                                "emailNotification" => $messageEmail,
                                                "supabase" => $msgSupabase,
                                            );
                                        }
                    
                                    }
                                } else {
                                    // Almacena un mensaje de error si no se encontraron suscripciones para actualizar
                                    $errorMessage = "No data was found to update in the companies";
                                }
                            }
                    
                            /*=============================================
                            Generamos la respuesta final
                            =============================================*/
                            if (!empty($responseData)) {
                                // Respuesta exitosa con datos acumulados
                                $json = array(
                                    "response" => array(
                                        "success" => true,
                                        "status" => 200,
                                        "message" => "The process was successful",
                                        "data" => $responseData
                                    )
                                );
                            } else {
                                // Respuesta de error si no hubo ningún dato para actualizar
                                $json = array(
                                    "response" => array(
                                        "success" => false,
                                        "status" => 404,
                                        "message" => $errorMessage ?? "No subscriptions found"
                                    )
                                );
                            }
                    
                            /*=============================================
                            Imprimimos la respuesta completa
                            =============================================*/
                            echo json_encode($json, http_response_code(200));
                    
                        } else {
                            // Respuesta en caso de que no se encuentren suscripciones
                            $json = array(
                                "response" => array(
                                    "success" => false,
                                    "status" => 404,
                                    "message" => "No se encontraron suscripciones",
                                ),
                            );
                    
                            /*=============================================
                            Imprimimos la respuesta en caso de error
                            =============================================*/
                            echo json_encode($json, http_response_code(404));
                        }

                    } else {

                        $json = array(

                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),

                        );

                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));

                        return;

                    }

                } else if (array_filter($arrayRutas)[1] == "paySuscription" && array_filter($arrayRutas)[2] == $arrayRutas[2]) {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        /*=============================================
                        Recogemos los datos de la suscripcion
                        =============================================*/
                        $dataSuscription = GetModel::getRelDataFilter("suscripciones,planes,empresas,usuarios", "suscripcion,plan,empresa,usuario", "*", "id_suscripcion", $arrayRutas[2], null, null, null, null);

                        /*=============================================
                        Capturar datos
                        =============================================*/
                        if (isset($data->file) && !empty($data->file)) {
        
                            if ($data->type == "image/jpeg" || $data->type == "image/png") {
        
                                if (array_filter($arrayRutas)[2] == '' || $data->monto == '' || $data->comprobante == '' || $data->fechaPago == '' || $data->metodoPago == '') {
        
                                    $json = array(
        
                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "message" => "Complete the fields",
                                        ),
        
                                    );
        
                                } else {
        
                                    if ($data->type == "image/png") {
        
                                        $ext = ".png";
        
                                    } else {
        
                                        $ext = ".jpg";
        
                                    }
        
                                    $newName = base64_encode($dataCompany["ruc"] . "~" . $arrayRutas[2] . "~" . $data->comprobante);
                                    $newName2 = base64_encode($dataCompany["ruc"] . "~" . $arrayRutas[2] . "~" . $data->comprobante) . $ext;
                                    $idPago = ControladorRutas::generateUniqueId();
        
                                    $datos_suscripcion = array(
                                        "trans_suscripcion" => $idPago,
                                        "fecha_pago_suscripcion" => $data->fechaPago,
                                        "monto_pago_suscripcion" => $data->monto,
                                        "medio_pago_suscripcion" => $data->metodoPago,
                                        "comprobante_suscripcion" => $data->comprobante,
                                        "adjunto_suscripcion" => $newName2,
                                        "estado_suscripcion" => "pendiente",
                                    );
        
                                    $updateSuscripcion = PutModel::putData("suscripciones", $datos_suscripcion, $arrayRutas[2], "id_suscripcion");
        
                                    if (isset($updateSuscripcion["comment"]) && $updateSuscripcion["comment"] == "The process was successful") {
        
                                        /*=============================================
                                        Creamos la carpeta de pagos si no existe
                                        =============================================*/
                                        $ruta = 'documents/pagos/' . $dataSuscription[0]->ruc_empresa;
        
                                        if (!file_exists($ruta)) {
                                            mkdir($ruta, 0777, true);
                                        }
        
                                        /*=============================================
                                        Cargamos el archivo en la carpeta creada
                                        =============================================*/
                                        $upload = FilesController::fileData($data->file, $data->type, $ruta, $newName, null, $data->width, $data->height);
        
                                        /*=============================================
                                        Actualizamos la venta del plan
                                        =============================================*/
                                        $sumaPlan = $dataSuscription[0]->ventas_plan + 1;
                                        $dataUptSusc = array(
                                            "ventas_plan" => $sumaPlan,
                                        );
                                        $updatPlans = PutModel::putData("planes", $dataUptSusc, $dataSuscription[0]->id_plan, "id_plan");

                                        if(isset($updatPlans["comment"]) && $updatPlans["comment"] == "The process was successful") {
                                           
                                            /*=============================================
                                            Enviar notificación por correo si está habilitado
                                            =============================================*/
                                            $messageEmail = 'No notification was sent by mail';
                                            if ($validTokenSt[0]->activo_correo_configuracion == "si") {
                                                $name = $dataSuscription[0]->alias_usuario;
                                                $subject = "Se ha cargado un pago | " . $dataSuscription[0]->ruc_empresa;
                                                $email = ControladorRutas::email();
                                                $message = "Hola, <br> Se ha recibido el pago de la empresa " . $dataSuscription[0]->ruc_empresa . " por el monto de " . $data->monto . ", recuerda que tienes 1 a 2 dias habiles para validar el pago.";
                                                $text = "Ingresa a tu panel";
                                                $url = ControladorRutas::path();
                    
                                                $sendEmail = ControladorRutas::sendEmail($name, $subject, $email, $message, $text, $url, NULL);

                                                if ($sendEmail == 'ok') {

                                                    $messageEmail = 'It was notified by mail';

                                                } else {

                                                    $messageEmail = "Error sending mail:" . $sendEmail;

                                                }

                                            }

                                            /*=============================================
                                            Verificamos si esta habilitada la opción de supabase
                                            =============================================*/
                                            if($supabase == "si" && $dataSuscription[0]->supabase_suscripcion != '') {

                                                /*=============================================
                                                Obtenemos los datos de supabase
                                                =============================================*/
                                                foreach (json_decode($dataSuscription[0]->supabase_suscripcion) as $key => $elementSupaBase) {
                    
                                                    $idUsuario = $elementSupaBase->id_usuario;
                                                    $idEmpresa = $elementSupaBase->id_empresa;
                                                    $idSuscripcion = $elementSupaBase->id_suscripcion;
                    
                                                }
                    
                                                if ($data->type == "image/png") {
                                
                                                    $ext = ".png";
                        
                                                } else {
                        
                                                    $ext = ".jpg";
                        
                                                }
                        
                                                /* Editamos la suscripcion */
                                                $supUrlSuscr = $supabaseUrl . "/rest/v1/tb_suscripciones?id=eq.". $idSuscripcion;
                                                $methodSuscr = "PATCH";
                                                $fieldsSuscr = array(
                                                    "scr_fecha_pago" => $data->fechaPago,
                                                    "scr_estado_suscripcion" => "pendiente",
                                                    "src_adjunto_suscripcion" => ControladorRutas::api() . $ruta . "/" . $newName . $ext,
                                                    "scr_monto_pago" => $data->monto,
                                                    "scr_numero_operacion" => $data->comprobante,
                                                    "src_medio_pago" => $data->metodoPago
                                                );
                        
                                                $dataSuscr = json_encode($fieldsSuscr);
                                                
                                                $responseSuscr = ControladorCurl::supabase($supUrlSuscr, $methodSuscr, $dataSuscr, $supabaseKey);
                        
                        
                                                if(is_object($responseSuscr) && isset($responseSuscr->code)) {
                                                
                                                        $json = array(
        
                                                            'response' => array(
                                                                'success' => true,
                                                                'status' => 200,
                                                                'name' => $newName,
                                                                'emailNotification' => $messageEmail,
                                                                'supabase' => $responseSuscr,
                                                                'message' => 'The pay for suscription has been uploaded successfully'
                                                            ),
                        
                                                        );
                        
                                                } else {
                        
                                                    $json = array(
        
                                                        'response' => array(
                                                            'success' => true,
                                                            'status' => 200,
                                                            'name' => $newName,
                                                            'emailNotification' => $messageEmail,
                                                            'supabase' => 'It was inserted into Supabase',
                                                            'message' => 'The pay for suscription has been uploaded successfully'
                                                        ),
                    
                                                    );
                        
                                                }
                        
                                            } else {
                        
                                                $json = array(
        
                                                    'response' => array(
                                                        'success' => true,
                                                        'status' => 200,
                                                        'name' => $newName,
                                                        "emailNotification" => $messageEmail,
                                                        'message' => 'The pay for suscription has been uploaded successfully'
                                                    ),
                
                                                );
                        
                                            }
                                            
                                        }
        
                                    } else {
        
                                        $json = array(
        
                                            "response" => array(
                                                "success" => false,
                                                "status" => 400,
                                                "message" => "Error loading pay",
                                            ),
        
                                        );
        
                                    }
        
                                }
        
                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));
                            
                            } else {
        
                                $json = array(
        
                                    "response" => array(
                                        "success" => false,
                                        "status" => 400,
                                        "message" => "Invalid format",
                                    ),
        
                                );
        
                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));
        
                            }
        
                        } else {
        
                            $json = array(
        
                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "Select a file to upload",
                                ),
        
                            );
        
                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));
        
                        }
        
                    } else {
        
                        $json = array(
        
                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),
        
                        );
        
                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));
        
                        return;
        
                    }
        
                } else if (array_filter($arrayRutas)[1] == "updateSuscription" && array_filter($arrayRutas)[2] == $arrayRutas[2]) {

                    /*=============================================
                    Peticiones POST
                    =============================================*/
                    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                        if($data->estado != '') {

                            /*=============================================
                            Recogemos los datos de la suscripcion
                            =============================================*/
                            $dataSuscription = GetModel::getRelDataFilter("suscripciones,planes,empresas,usuarios", "suscripcion,plan,empresa,usuario", "*", "id_suscripcion", $arrayRutas[2], null, null, null, null);

                            if($dataSuscription) {

                                $datos_suscripcion = array(
                                    "estado_suscripcion" => $data->estado,
                                );

                                $updateSuscripcion = PutModel::putData("suscripciones", $datos_suscripcion, $arrayRutas[2], "id_suscripcion");

                                /*=============================================
                                Texto para el correo
                                =============================================*/
                                if($data->estado == "pagado") {
                                    $estadoTxt = "Aprobado";
                                } else {
                                    $estadoTxt = "Rechazado";
                                }

                                if (isset($updateSuscripcion["comment"]) && $updateSuscripcion["comment"] == "The process was successful") {

                                    /*=============================================
                                    Validamos si se envia un periodo
                                    =============================================*/
                                    if(isset($data->periodo)) {

                                        if ($data->periodo != 'unlimited') {
                    
                                            $fechaCaduca = $dataSuscription[0]->proxima_facturacion_empresa; // Obtener la fecha actual en formato 'YYYY-MM-DD'
                                            $fechaNueva = date('Y-m-d', strtotime('+' . $data->periodo . ' month', strtotime($fechaCaduca))); // Aumentar un mes a la fecha actual
                                            /* Pago del plan */
                                            $precioPlan = $dataSuscription[0]->precio_plan * $data->periodo;
                
                                        } else {
                        
                                            $fechaNueva = '0000-00-00';
                                            /* Pago del plan */
                                            $precioPlan = $dataSuscription[0]->precio_plan;
                        
                                        }
                    
                                    } else {
                    
                                        $fechaCaduca = $dataSuscription[0]->proxima_facturacion_empresa; // Obtener la fecha actual en formato 'YYYY-MM-DD'
                                        $fechaNueva = date('Y-m-d', strtotime('+1 month', strtotime($fechaCaduca))); // Aumentar un mes a la fecha actual
                                        /* Pago del plan */
                                        $precioPlan = $dataSuscription[0]->precio_plan;
                                        
                                    }

                                    /*=============================================
                                    Actualizamos la proxima facturacion en base al periodo
                                    =============================================*/
                                    $datos_prox_fact = array(
                                        "proxima_facturacion_empresa" => $fechaNueva,
                                    );
    
                                    $updateProxFact = PutModel::putData("empresas", $datos_prox_fact, $dataSuscription[0]->id_empresa, "id_empresa");

                                    /*=============================================
                                    Moneda
                                    =============================================*/
                                    if($dataSuscription[0]->medio_pago_suscripcion == "paypal") {
                                        $monedaPago = "USD";
                                    } else {
                                        $monedaPago = "PEN";
                                    }

                                    /*=============================================
                                    Validamos si se tiene activo la generacion de comprobantes
                                    =============================================*/
                                    if($datosFacturacion[0]['estado'] == 'activo' && $precioPlan != 0 && $estadoTxt == 'Aprobado') {

                                        /* Generamos la factura */
                                        /*=============================================
                                        Agrupamos los datos del item
                                        =============================================*/
                                        $baseIgv = ($precioPlan/1.18);
                                        $valIgv = $precioPlan - $baseIgv;
                                        /* Comprobante */
                                        $invoice = array(
                                            "tipoOperacion" => "0101",
                                            "tipoDoc" => "01",
                                            "serie" => $datosFacturacion[0]['factura']['serie'],
                                            "correlativo" => $datosFacturacion[0]['factura']['correlativo'],
                                            "observacion" => "",
                                            "fechaEmision" => date('Y-m-d'),
                                            "horaEmision" => date("H:i:s"),
                                            "tipoMoneda" => $monedaPago,
                                            "tipoPago" => "Contado",
                                            "total" => number_format($precioPlan,2),
                                            "mtoIGV" => number_format($valIgv,2),
                                            "igvOp" => "0",
                                            "mtoOperGravadas" => number_format($baseIgv,2)
                                        );
                                        /* Cliente */
                                        $cliente = array(
                                            "codigoPais" => "PE",
                                            "tipoDoc" => "6",
                                            "numDoc" => $dataSuscription[0]->ruc_empresa,
                                            "rznSocial" => $dataSuscription[0]->razon_social_empresa,
                                            "direccion" => $dataSuscription[0]->direccion_empresa
                                        );
                                        /* Items */
                                        $items[] = array(
                                            "codProducto" => ControladorRutas::generarCodigoProducto($dataSuscription[0]->nombre_plan, $dataSuscription[0]->id_plan),
                                            "descripcion" => "Servicio de facturación electrónoca " . $dataSuscription[0]->nombre_plan,
                                            "unidad" => "NIU",
                                            "tipoPrecio" => "01",
                                            "cantidad" => $data->periodo,
                                            "mtoBaseIgv" => number_format($baseIgv,2),
                                            "mtoValorUnitario" => number_format($baseIgv,2),
                                            "mtoPrecioUnitario" => number_format($precioPlan,2),
                                            "codeAfectAlt" => "10",
                                            "codeAfect" => "1000",
                                            "nameAfect" => "IGV",
                                            "tipoAfect" => "VAT",
                                            "igvPorcent" => "18",
                                            "igv" => number_format($valIgv,2),
                                            "igvOpi" => number_format($valIgv,2)
                                        );
                                        /* Extras */
                                        $extras = array(
                                            "comentario" => "",
                                            "usuario" => $data->usuario,
                                            "datosCuenta" => array(
                                                "banco" => "",
                                                "cuenta" => "",
                                                "cci" => "",
                                                "titular" => "",
                                                "yape" => "",
                                                "obbs" => ""
                                            )
                                        );
                                        /* Agrupamos todos los datos */
                                        $dataXml = array(
                                            "comprobante" => $invoice,
                                            "cliente" => $cliente,
                                            "items" => $items,
                                            "extra" => $extras
                                        );

                                        /* Codificamos los datos del comprobante */
                                        $invoice_encoded = json_encode($invoice);
                                        $invoice_decoded = json_decode($invoice_encoded);

                                        /* Codificamos los datos del cliente */
                                        $cliente_encoded = json_encode($cliente);
                                        $cliente_decoded = json_decode($cliente_encoded);

                                        /* Codificamos los datos del prdocuto (plan) */
                                        $items_encoded = json_encode($items);
                                        $items_decoded = json_decode($items_encoded);

                                        /* Codificamos los datos extras */
                                        $extras_encoded = json_encode($extras);
                                        $extras_decoded = json_decode($extras_encoded);
                                        
                                        /*=============================================
                                        Capturar datos del json
                                        =============================================*/
                                        $company = json_encode($dataCompany);
                                        $comprobante = $invoice_decoded;
                                        $client = $cliente_decoded;
                                        $details = $items_decoded;
                                        $extra = $extras_decoded;
                                        $ruta = 'documents/xml/' . $dataCompany["ruc"] . '/unsigned/';

                                        /*=============================================
                                        Creamos la carpeta del xml si no existe
                                        =============================================*/
                                        if (!file_exists($ruta)) {
                                            mkdir($ruta, 0777, true);
                                        }

                                        /*=============================================
                                        Indicamos el nombre del xml
                                        =============================================*/
                                        $nombrexml = $dataCompany["ruc"] . '-' . $invoice["tipoDoc"] . '-' . $invoice["serie"] . '-' . $invoice["correlativo"];

                                        /*=============================================
                                        Creamos el XML
                                        =============================================*/
                                        $registro = new ControladorXML();
                                        $registro->CrearXMLFactura($ruta . $nombrexml, $company, $client, $comprobante, $details);

                                        /*=============================================
                                        Creamos el qr
                                        =============================================*/
                                        $rta_qr = "documents/qr/" . $dataCompany["ruc"];
                                        $text_qr = $dataCompany["ruc"] . "|" . $dataCompany["tipoDoc"] . " | " . $comprobante->serie . " | " . $comprobante->correlativo . " | " . $comprobante->mtoIGV . " | " . $comprobante->total . " | " . $comprobante->fechaEmision . " | " . $client->tipoDoc . " | " . $client->numDoc . " | ";
                                        $ruta_qr = "documents/qr/" . $dataCompany["ruc"] . "/" . $nombrexml . ".png";

                                        if (!file_exists($rta_qr)) {
                                            mkdir($rta_qr, 0777, true);
                                        }

                                        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);

                                        /*=============================================
                                        Firmamos el XML
                                        =============================================*/
                                        $rutaSigned = 'documents/xml/' . $dataCompany["ruc"] . '/signed/';

                                        if (!file_exists($rutaSigned)) {
                                            mkdir($rutaSigned, 0777, true);
                                        }

                                        $firmado = new ControladorSunat();
                                        $firmado->FirmarComprobanteElectronico($company, $nombrexml, $ruta, $rutaSigned, "documents/");

                                        /*=============================================
                                        Creamos el pdf A4
                                        =============================================*/
                                        $crPdfA4 = new ControladorPdf();
                                        $crPdfA4->CrearPdfDocumento('a4', $company, $client, $comprobante, $details, $protocol, $extra);

                                        /*=============================================
                                        Creamos el pdf Ticket
                                        =============================================*/
                                        $crPdfTc = new ControladorPdf();
                                        $crPdfTc->CrearPdfDocumento('ticket', $company, $client, $comprobante, $details, $protocol, $extra);

                                        /*=============================================
                                        Retornamos la respuesta
                                        =============================================*/
                                        if ($registro) {

                                            /*=============================================
                                            Actualizamos el correlativo
                                            =============================================*/
                                            $datosFacturacion[0]['factura']['correlativo'] += 1;

                                            // Codificar de nuevo el JSON completo
                                            $jsonFacturacion = json_encode($datosFacturacion);

                                            $datos_correlativo = array(
                                                "facturacion_configuracion" => $jsonFacturacion,
                                            );
            
                                            $updateCorrelativo = PutModel::putData("configuraciones", $datos_correlativo, "1", "id_configuracion");

                                            /*=============================================
                                            Adjuntamos los archivos para el correo
                                            =============================================*/
                                            $adjuntoCorreo = array(
                                                array("archivo" => ControladorRutas::api() . "documents/pdf/" . $datosFacturacion[0]['empresa']['ruc'] . "/invoice/a4/" . $datosFacturacion[0]['empresa']['ruc'] . "-01-" . $datosFacturacion[0]['factura']['serie'] . "-" . $datosFacturacion[0]['factura']['correlativo'] . ".pdf"),
                                                array("archivo" => ControladorRutas::api() . "documents/xml/" . $datosFacturacion[0]['empresa']['ruc'] . "/signed/" . $datosFacturacion[0]['empresa']['ruc'] . "-01-" . $datosFacturacion[0]['factura']['serie'] . "-" . $datosFacturacion[0]['factura']['correlativo'] . ".XML"),
                                                array("archivo" => ControladorRutas::api() . "documents/cdr/" . $datosFacturacion[0]['empresa']['ruc'] . "/R-" . $datosFacturacion[0]['empresa']['ruc'] . "-01-" . $datosFacturacion[0]['factura']['serie'] . "-" . $datosFacturacion[0]['factura']['correlativo'] . ".XML")
                                            );

                                            /*=============================================
                                            Enviamos a SUNAT
                                            =============================================*/
                                            $rutaCompleta = $rutaSigned . $nombrexml . '.XML';

                                            /*=============================================
                                            Validamos que exista el XML firmado para el envio
                                            =============================================*/
                                            if (file_exists($rutaCompleta)) {

                                                /*=============================================
                                                Indicamos la ruta donde se almacena el cdr
                                                =============================================*/
                                                $ruta_archivo_cdr = 'documents/cdr/' . $dataCompany["ruc"] . '/';

                                                /*=============================================
                                                Creamos la carpeta del cdr si no existe
                                                =============================================*/
                                                if (!file_exists($ruta_archivo_cdr)) {
                                                    mkdir($ruta_archivo_cdr, 0777, true);
                                                }

                                                /*=============================================
                                                Creamos la carpeta del xml si no existe
                                                =============================================*/
                                                if (!file_exists($rutaSigned)) {
                                                    mkdir($rutaSigned, 0777, true);
                                                }

                                                /*=============================================
                                                Enviamos el comprobante
                                                =============================================*/
                                                $api = new ControladorSunat();
                                                $api->EnviarComprobanteElectronico($company, $nombrexml, $rutaSigned, $ruta_archivo_cdr, "documents/");

                                                if ($comprobante->tipoDoc == "07" || $comprobante->tipoDoc == "08") {

                                                    $urlDoc = "note";

                                                } else {

                                                    $urlDoc = "invoice";

                                                }

                                                /*=============================================
                                                Retornamos la respuesta
                                                =============================================*/
                                                if ($api->success == true) {

                                                    $json = array(

                                                        'response' => array(
                                                            "success" => true,
                                                            "status" => 200,
                                                            "message" => "Updated subscription and document " . $nombrexml . " created successfully",
                                                            "invoice" => array(
                                                                "hash" => $api->hash,
                                                                "xml-unsigned" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $nombrexml . '.XML',
                                                                "xml-signed" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $nombrexml . '.XML',
                                                                "cdr" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/cdr/' . $dataCompany["ruc"] . '/' . $api->cdrb64,
                                                                "pdf-a4" => $crPdfA4->pdf,
                                                                "pdf-ticket" => $crPdfTc->pdf,
                                                                "code" => $api->code,
                                                                "message" => $api->mensajeError,
                                                            )
                                                        ),

                                                    );

                                                } else {

                                                    $json = array(

                                                        'response' => array(
                                                            'success' => $api->success,
                                                            'status' => 400,
                                                            'message' => "Updated subscription and document " . $nombrexml . " created successfully",
                                                            "invoice" => array(
                                                                "xml-unsigned" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/unsigned/' . $nombrexml . '.XML',
                                                                "xml-signed" => $protocol . $_SERVER['HTTP_HOST'] . '/documents/xml/' . $dataCompany["ruc"] . '/signed/' . $nombrexml . '.XML',
                                                                "pdf-a4" => $crPdfA4->pdf,
                                                                "pdf-ticket" => $crPdfTc->pdf,
                                                                "code" => $api->code,
                                                                "message" => $api->mensajeError,
                                                            )
                                                        ),

                                                    );

                                                }

                                            } else {

                                                $json = array(

                                                    'response' => array(
                                                        "success" => true,
                                                        "status" => 200,
                                                        "message" => "Updated subscription but cannot send the document because it does not exist"
                                                    ),

                                                );

                                            }

                                            /*=============================================
                                            Imprimimos la respuesta
                                            =============================================*/
                                            echo json_encode($json, http_response_code($json["response"]["status"]));

                                        } else {

                                            $json = array(
            
                                                "response" => array(
                                                    "success" => true,
                                                    "status" => 200,
                                                    "message" => "Subscription updated, but there was an error generating the receipt",
                                                ),
                            
                                            );
                            
                                            /*=============================================
                                            Imprimimos la respuesta
                                            =============================================*/
                                            echo json_encode($json, http_response_code($json["response"]["status"]));

                                        }

                                    } else {

                                        /*=============================================
                                        Adjuntos en el correo
                                        =============================================*/
                                        $adjuntoCorreo = "";

                                    }

                                    /*=============================================
                                    Enviar el correo electronico
                                    =============================================*/
                                    $messageEmail = 'No notification was sent by mail';
                                    if ($validTokenSt[0]->activo_correo_configuracion == "si") {
                                        $name = $dataSuscription[0]->alias_usuario;
                                        $subject = "Pago " . $estadoTxt . " | " . $dataSuscription[0]->ruc_empresa;
                                        $email = $dataSuscription[0]->email_usuario;
                                        $message = "Te informamos que el estado de pago correspondiente a la empresa " . $dataSuscription[0]->ruc_empresa . " ha sido " . $estadoTxt . ", a continuacion adjuntamos tu comprobante electronico (CPE).";
                                        $text = "Ingresa a tu panel";
                                        $url = ControladorRutas::path();
                                        $adjunto = $adjuntoCorreo;
            
                                        $sendEmail = ControladorRutas::sendEmail($name, $subject, $email, $message, $text, $url, $adjunto);

                                        if ($sendEmail == 'ok') {

                                            $messageEmail = 'It was notified by mail';

                                        } else {

                                            $messageEmail = "Error sending mail:" . $sendEmail;

                                        }

                                    }

                                    /*=============================================
                                    Actualizamos supabase si está activo
                                    =============================================*/
                                    if($supabase == "si" && $dataSuscription[0]->supabase_suscripcion != '') {

                                        /*=============================================
                                        Obtenemos los datos de supabase
                                        =============================================*/
                                        foreach (json_decode($dataSuscription[0]->supabase_suscripcion) as $key => $elementSupaBase) {
            
                                            $idUsuario = $elementSupaBase->id_usuario;
                                            $idEmpresa = $elementSupaBase->id_empresa;
                                            $idSuscripcion = $elementSupaBase->id_suscripcion;
            
                                        }

                                        /*=============================================
                                        Editamos el estado de la suscripcion
                                        =============================================*/
                                        $supUrlSuscr = $supabaseUrl . "/rest/v1/tb_suscripciones?id=eq.". $idSuscripcion;
                                        $methodSuscr = "PATCH";
                                        $fieldsSuscr = array(
                                            "scr_estado_suscripcion" => $estadoTxt
                                        );

                                        $dataSuscr = json_encode($fieldsSuscr);
                                        
                                        $responseSuscr = ControladorCurl::supabase($supUrlSuscr, $methodSuscr, $dataSuscr, $supabaseKey);

                                    }

                                } else {

                                    $json = array(
            
                                        "response" => array(
                                            "success" => false,
                                            "status" => 400,
                                            "message" => "Failed to update subscription",
                                        ),
                    
                                    );
                    
                                    /*=============================================
                                    Imprimimos la respuesta
                                    =============================================*/
                                    echo json_encode($json, http_response_code($json["response"]["status"]));

                                }

                            } else {

                                $json = array(
            
                                    "response" => array(
                                        "success" => false,
                                        "status" => 404,
                                        "message" => "Subscription not found",
                                    ),
                
                                );
                
                                /*=============================================
                                Imprimimos la respuesta
                                =============================================*/
                                echo json_encode($json, http_response_code($json["response"]["status"]));

                            }

                        } else {

                            $json = array(
        
                                "response" => array(
                                    "success" => false,
                                    "status" => 400,
                                    "message" => "You must enter the state",
                                ),
            
                            );
            
                            /*=============================================
                            Imprimimos la respuesta
                            =============================================*/
                            echo json_encode($json, http_response_code($json["response"]["status"]));

                        }

                    } else {
        
                        $json = array(
        
                            "response" => array(
                                "success" => false,
                                "status" => 405,
                                "message" => "Method not allowed",
                            ),
        
                        );
        
                        /*=============================================
                        Imprimimos la respuesta
                        =============================================*/
                        echo json_encode($json, http_response_code($json["response"]["status"]));
        
                        return;
        
                    }

                } else {

                    $json = array(

                        "response" => array(
                            "success" => false,
                            "status" => 404,
                            "message" => "Not Found",
                        ),

                    );

                    /*=============================================
                    Imprimimos la respuesta
                    =============================================*/
                    echo json_encode($json, http_response_code($json["response"]["status"]));

                    return;

                }

            } else {

                /*=============================================
                Cuando no se hace ninguna peticion a la API
                =============================================*/
                $json = array(

                    "response" => array(
                        "success" => false,
                        "status" => 404,
                        "message" => "Not Found",
                    ),

                );

                /*=============================================
                Imprimimos la respuesta
                =============================================*/
                echo json_encode($json, http_response_code($json["response"]["status"]));

                return;

            }

        }

    }

}
