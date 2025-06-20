<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class TenantsController
{

    /*=============================================
    Mostrar datos
    =============================================*/
    public static function dataTenant($value)
    {

        $url = "empresas?select=*&linkTo=id_empresa&equalTo=" . $value;
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
    Listar datos
    =============================================*/
    public static function ctrListTenants()
    {

        $url = "empresas";
        $method = "GET";
        $fields = array();
        $token = TemplateController::tokenSet();

        $response = CurlController::requestSunat($url, $method, $fields, $token);

        if ($response->response->success == true) {

            $resultado = $response->response->data;

        } else {

            $resultado = "No encontrado";

        }

        return $resultado;

    }

    /*=============================================
    Crear empresa
    =============================================*/
    public function create($idTenants, $plan, $idPlan)
    {

        if (isset($_POST['ruc-tenant'])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            /*=============================================
            Creamos el token unico
            =============================================*/
            $tokenEmpresa = bin2hex(random_bytes(32));
            $tokenGetUser = TemplateController::tokenSet();

            /*=============================================
            Validamos los datos enviados de usuario asignado
            =============================================*/
            if ($idTenants != NULL) {

                $idTenants = $idTenants;

            } else {

                $urlGetUser = "usuarios?select=*&linkTo=id_usuario&equalTo=" . $_POST['usuario-tenant'];
                $methodGetUser = "GET";
                $fieldsGetUser = array();
                $responseGetUser = CurlController::requestSunat($urlGetUser, $methodGetUser, $fieldsGetUser, $tokenGetUser);

                $idTenants = $responseGetUser->response->data[0]->id_empresa_usuario;

            }

            /*=============================================
            Validamos los datos enviados para la redireccion
            =============================================*/
            if($plan != NULL && $idPlan != NULL) {

                $urlRegister = '/';

            } else {

                $urlRegister = '/tenants';

            }

            /*=============================================
            Verificamos si se envia un usuario desde el formulario
            =============================================*/
            if(isset($_POST['usuario-tenant'])) {

                $usuarioRegistro = $_POST['usuario-tenant'];

            } else {

                $usuarioRegistro = $_SESSION["user"]->id_ususario;

            }

            /*=============================================
            Validamos se se envia algun dato en el plan
            =============================================*/
            if($plan != NULL) {

                $plan = $plan;

            } else {

                $plan = $_POST['plan-tenant'];

            }

            /*=============================================
            Creamos la clave secreta
            =============================================*/
            $secretKey = TemplateController::secretKey($_POST["ruc-tenant"]);

            $url = "empresas?token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $method = "POST";
            $fields = array(
                "ruc_empresa" => $_POST["ruc-tenant"],
                "razon_social_empresa" => $_POST["name-tenant"],
                "nombre_comercial_empresa" => $_POST["nc-tenant"],
                "telefono_empresa" => $_POST["phone-tenant"],
                "email_empresa" => $_POST["email-tenant"],
                "id_plan_empresa" => $plan,
                "consumo_empresa" => '[]',
                "direccion_empresa" => $_POST["address-tenant"],
                "departamento_empresa" => $_POST["dep-tenant"],
                "provincia_empresa" => $_POST["pro-tenant"],
                "distrito_empresa" => $_POST["dis-tenant"],
                "ubigeo_empresa" => $_POST["ubi-tenant"],
                "fase_empresa" => 'beta',
                "usuario_sol_empresa" => 'MODDATOS',
                "clave_sol_empresa" => 'moddatos',
                "estado_empresa" => 1,
                //"expira_certificado_empresa" => "0000-00-00",
                "token_empresa" => $tokenEmpresa,
                "clave_secreta_empresa" => $secretKey,
                "creado_empresa" => date('Y-m-d'),
            );
            $token = TemplateController::tokenSet();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

            if ($response->response->success == true) {

                /*=============================================
                Actualizamos el usuario con la empresa
                =============================================*/
                $dataArray = $idTenants;

                if ($dataArray != null) {

                    $arr = json_decode($dataArray, true);
                    array_push($arr, array("id" => $response->response->data->lastId));
                    $tenants = json_encode($arr);

                } else {

                    $json[] = ['id' => $response->response->data->lastId];
                    $tenants = json_encode($json);

                }

                /* Obtenemos los datos del usuario */
                $urlGetUsuario = "usuarios?select=*&linkTo=id_usuario&equalTo=" . $_POST['usuario-tenant'];
                $methodGetUsuario = "GET";
                $fieldsGetUsuario = array();
                $tokenGetUsuario = TemplateController::tokenSet();
                $responseGetUsuario = CurlController::requestSunat($urlGetUsuario, $methodGetUsuario, $fieldsGetUsuario, $tokenGetUsuario);

                /* Actualizamos el listado de empresas */
                $urlUp = 'usuarios?id=' . $usuarioRegistro . '&nameId=id_usuario&token=' . $_SESSION["user"]->token_usuario . '&table=usuarios&suffix=usuario';
                $methodUp = "PUT";
                $fieldsUp = "id_empresa_usuario=" . $tenants;

                $responseU = CurlController::requestSunat($urlUp, $methodUp, $fieldsUp, $token);

                /*=============================================
                Creamos un id unico para las transferencias que no son paypal o culqui
                =============================================*/
                $transVenta = TemplateController::generateUniqueId();

                /*=============================================
                Obtenemos el precio del plan seleccionado
                =============================================*/
                $urlGetPlan = "planes?select=*&linkTo=id_plan&equalTo=" . $plan;
                $methodGetPlan = "GET";
                $fieldsGetPlan = array();
                $responseGetPlan = CurlController::requestSunat($urlGetPlan, $methodGetPlan, $fieldsGetPlan, $tokenGetUser);
                $montoPlan = $responseGetPlan->response->data[0]->precio_plan;
                $ventaPlan = $responseGetPlan->response->data[0]->ventas_plan;
                $sumaPlan = $ventaPlan + 1;

                /*=============================================
                Validamos se si envia algun limite del periodo del servicio
                =============================================*/
                if(isset($_POST['periodo-tenant'])) {

                    if ($_POST['periodo-tenant'] != 'unlimited') {

                        $fechaActual = date('Y-m-d'); // Obtener la fecha actual en formato 'YYYY-MM-DD'
                        $fechaNueva = date('Y-m-d', strtotime('+' . $_POST['periodo-tenant'] . ' month', strtotime($fechaActual))); // Aumentar un mes a la fecha actual
                        $precioPlan = $montoPlan * $_POST['periodo-tenant'];
    
                    } else {
    
                        $fechaNueva = '0000-00-00';
                        $precioPlan = $montoPlan;
    
                    }

                } else {

                    $fechaActual = date('Y-m-d'); // Obtener la fecha actual en formato 'YYYY-MM-DD'
                    $fechaNueva = date('Y-m-d', strtotime('+1 month', strtotime($fechaActual))); // Aumentar un mes a la fecha actual
                    $precioPlan = $montoPlan;

                }

                /* Moneda */
                if($_POST["metodo_pago-tenant"] == "paypal") {
                        
                    $monedaPago = "USD";

                } else {

                    $monedaPago = "PEN";

                }

                /*=============================================
                Validamos si el registro se hace desde el panel o por el usuario
                =============================================*/
                if($idPlan != NULL) {
                 
                    $urlSale = 'ventas?id=' . $idPlan . '&nameId=trans_venta&token=' . $_SESSION["user"]->token_usuario . '&table=usuarios&suffix=usuario';
                    $methodSale = "PUT";
                    $fieldsSale = "id_empresa_venta=" . $response->response->data->lastId;

                    $responseSale = CurlController::requestSunat($urlSale, $methodSale, $fieldsSale, $token);
                    
                } else {

                    /*=============================================
                    Obtenemos el tipo de cambio
                    =============================================*/
                    $urlTC = "exchange/consult";
                    $methodTC = "POST";
                    $dataTC = array();

                    $fieldsTC = $dataTC;
                    $tokenTC = TemplateController::tokenSet();

                    $responseTC = CurlController::requestSunat($urlTC, $methodTC, $fieldsTC, $tokenTC);

                    if ($responseTC->response->success == true) {

                        $tC = $responseTC->response->data->compra;
                        $tV = $responseTC->response->data->venta;

                    } else {

                        $tC = 1;
                        $tV = 1;

                    }

                    /*=============================================
                    Insertamos el registro de venta
                    =============================================*/
                    $urlSale = "ventas?token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                    $methodSale = "POST";
                    $fieldsSale = array(
                        "id_usuario_venta" => $usuarioRegistro,
                        "id_plan_venta" => $plan,
                        "id_empresa_venta" => $response->response->data->lastId,
                        "metodo_venta" => $_POST["metodo_pago-tenant"],
                        "trans_venta" => $transVenta,
                        "moneda_venta" => $monedaPago,
                        "monto_venta" => $precioPlan,
                        "tipo_cambio_venta" => $tC,
                        "estado_venta" => "pagado",
                        "creado_venta" => date('Y-m-d'),
                    );

                    $responseSale = CurlController::requestSunat($urlSale, $methodSale, $fieldsSale, $token);

                }

                /*=============================================
                Actualizamos las ventas de planes
                =============================================*/
                $urlUptPlan = 'planes?id=' . $plan . '&nameId=id_plan&token=' . $_SESSION["user"]->token_usuario . '&table=usuarios&suffix=usuario';
                $methodUptPlan = "PUT";
                $fieldsUptPlan = "ventas_plan=" . $sumaPlan;

                $responseUptPlan = CurlController::requestSunat($urlUptPlan, $methodUptPlan, $fieldsUptPlan, $token);

                /*=============================================
                Actualizamos la fecha proxima de facturacion
                =============================================*/
                $urlPf = 'empresas?id=' . $response->response->data->lastId . '&nameId=id_empresa&token=' . $_SESSION["user"]->token_usuario . '&table=usuarios&suffix=usuario';
                $methodPf = "PUT";
                $fieldsPf = "proxima_facturacion_empresa=" . $fechaNueva;

                $responsePf = CurlController::requestSunat($urlPf, $methodPf, $fieldsPf, $token);

                /*=============================================
                Validamos si se tiene conexión con Supabase
                =============================================*/
                $urlSet = "configuraciones";
                $methodSet = "GET";
                $fieldsSet = array();
                $tokenSet = TemplateController::tokenSet();

                $responseSet = CurlController::requestSunat($urlSet, $methodSet, $fieldsSet, $tokenSet);

                /*=============================================
                Obtenemos las configuraciones adicionales
                =============================================*/
                foreach (json_decode($responseSet->response->data[0]->extras_configuracion) as $key => $elementExtras) {

                    $supabase = $elementExtras->supabase;
                    $supabaseUrl = $elementExtras->supabaseUrl;
                    $supabaseKey = $elementExtras->supabaseKey;
                    $supabasePass = $elementExtras->supabasePass;

                }

                /*=============================================
                Capturar datos de la facturacion
                =============================================*/
                $jsonFacturacion = $responseSet->response->data[0]->facturacion_configuracion;
                $datosFacturacion = json_decode($jsonFacturacion, true);

                /*=============================================
                Insertamos el registro de la suscripcion
                =============================================*/
                $urlSusc = "suscripciones?token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                $methodSusc = "POST";
                $fieldsSusc = array(
                    "id_empresa_suscripcion" => $response->response->data->lastId,
                    "id_usuario_suscripcion" => $usuarioRegistro,
                    "id_plan_suscripcion" => $plan,
                    "trans_suscripcion" => $transVenta,
                    "fecha_emision_suscripcion" => date('Y-m-d'),
                    "fecha_pago_suscripcion" => date('Y-m-d'),
                    "monto_pago_suscripcion" => $precioPlan,
                    "medio_pago_suscripcion" => $_POST["metodo_pago-tenant"],
                    "comprobante_suscripcion" => $_POST["comprobante-tenant"],
                    "estado_suscripcion" => "pagado",
                    "creado_suscripcion" => date('Y-m-d'),
                );

                $responseSusc = CurlController::requestSunat($urlSusc, $methodSusc, $fieldsSusc, $token);

                /* Validamos si se tiene activo la generacion de comprobantes */
                if($datosFacturacion[0]['estado'] == 'activo' && $precioPlan != 0) {

                    /* Generamos la factura */
                    /*=============================================
                    Agrupamos los datos del item
                    =============================================*/
                    $baseIgv = ($precioPlan/1.18);
                    $valIgv = $precioPlan - $baseIgv;
                    /* Comprobante */
                    $comprobante = array(
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
                        "numDoc" => $_POST["ruc-tenant"],
                        "rznSocial" => $_POST["name-tenant"],
                        "direccion" => $_POST["address-tenant"]
                    );
                    /* Items */
                    $items[] = array(
                        "codProducto" => TemplateController::generarCodigoProducto($responseGetPlan->response->data[0]->nombre_plan, $responseGetPlan->response->data[0]->id_plan),
                        "descripcion" => "Servicio de facturación electrónoca " . $responseGetPlan->response->data[0]->nombre_plan,
                        "unidad" => "NIU",
                        "tipoPrecio" => "01",
                        "cantidad" => $_POST['periodo-tenant'],
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
                        "usuario" => $_SESSION["user"]->alias_usuario,
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
                        "comprobante" => $comprobante,
                        "cliente" => $cliente,
                        "items" => $items,
                        "extra" => $extras
                    );

                    $urlXml = 'invoice/create';
                    $methodXml = 'POST';
                    $fieldXml = json_encode($dataXml, true);

                    $responseXml = CurlController::requestSunat($urlXml, $methodXml, $fieldXml, $tokenSet);

                    /* Si se genera correctamente el xml */
                    if($responseXml->response->success == true) {

                        /* Enviamos a SUNAT */
                        $dataSend = array(
                            "comprobante" => array(
                                "tipoDoc" => "01",
                                "serie" => $datosFacturacion[0]['factura']['serie'],
                                "correlativo" => $datosFacturacion[0]['factura']['correlativo'],
                            )
                        );
                
                        $urlSend = 'invoice/send';
                        $methodSend = 'POST';
                        $fieldsSend = json_encode($dataSend, true);
                
                        $responseSend = CurlController::requestSunat($urlSend, $methodSend, $fieldsSend, $tokenSet);

                        /* Validamos si esta activo el envio de correo */
                        if($responseSet->response->data[0]->activo_correo_configuracion == "si") {

                            /*=============================================
                            Notificamos a la empresa por correo
                            =============================================*/
                            $aliasUsuario = $responseGetUsuario->response->data[0]->alias_usuario;
                            $emailUsuario = $responseGetUsuario->response->data[0]->email_usuario;
                            $urlMail = "email/send";
                            $methodMail = "POST";
                            $dataMail = array(
                                "nombre" => $aliasUsuario,
                                "asunto" => "Bienvenido | " . $_POST["ruc-tenant"],
                                "email" => $emailUsuario,
                                "mensaje" => "Te informamos que la suscripcion de la empresa " .$_POST["ruc-tenant"] . " ha sido procesada exitosamente, a continuacion adjuntamos tu comprobante electronico (CPE).",
                                "text" => "Ingresa a tu panel",
                                "url" => TemplateController::path(),
                                "adjunto" => array(
                                    array("archivo" => CurlController::api() . "documents/pdf/" . $datosFacturacion[0]['empresa']['ruc'] . "/invoice/a4/" . $datosFacturacion[0]['empresa']['ruc'] . "-01-" . $datosFacturacion[0]['factura']['serie'] . "-" . $datosFacturacion[0]['factura']['correlativo'] . ".pdf"),
                                    array("archivo" => CurlController::api() . "documents/xml/" . $datosFacturacion[0]['empresa']['ruc'] . "/signed/" . $datosFacturacion[0]['empresa']['ruc'] . "-01-" . $datosFacturacion[0]['factura']['serie'] . "-" . $datosFacturacion[0]['factura']['correlativo'] . ".XML"),
                                    array("archivo" => CurlController::api() . "documents/cdr/" . $datosFacturacion[0]['empresa']['ruc'] . "/R-" . $datosFacturacion[0]['empresa']['ruc'] . "-01-" . $datosFacturacion[0]['factura']['serie'] . "-" . $datosFacturacion[0]['factura']['correlativo'] . ".XML")
                                )
                            );
                            $fieldsMail = json_encode($dataMail);
                
                            $sendMail = CurlController::requestSunat($urlMail, $methodMail, $fieldsMail, $token);

                        }

                        /* Actualizamos el correlativo */
                        $datosFacturacion[0]['factura']['correlativo'] += 1;

                        // Codificar de nuevo el JSON completo
                        $jsonFacturacion = json_encode($datosFacturacion);

                        // Enviamos los datos al API
                        $urlUptCor = "configuraciones?id=1&nameId=id_configuracion&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                        $methodUptCor = "PUT";
                        $fieldsUptCor = "facturacion_configuracion=" . $jsonFacturacion;

                        $responseUptCor = CurlController::requestSunat($urlUptCor, $methodUptCor, $fieldsUptCor, $tokenSet);

                    }

                }

                /* Validamos la conexion con supabase */
                if($supabase == "si") {

                    /* Creacion de usuario */
                    $supUrl = $supabaseUrl . "/auth/v1/signup";
                    $randomEmail = TemplateController::generateRandomEmail($_POST["email-tenant"]);

                    $methodSup = "POST";
                    $fieldsSup = array(
                        "email" => $_POST["email-tenant"],
                        "password" => $_POST['ruc-tenant'],
                        "options" => array(
                            "data" => array(
                                "trigger_value" => false
                            )
                        )
                    );

                    $dataSup = json_encode($fieldsSup);
                    
                    $responseSup = CurlController::supabase($supUrl, $methodSup, $dataSup, $supabaseKey);

                    // $responseSupJson = json_encode($responseSup);

                    // // Imprimir en la consola del navegador
                    // echo "<script>console.log('Respuesta de Supabase: ', $responseSupJson);</script>";

                    /* Creacion de empresa */
                    if(isset($responseSup->access_token)) {

                        $supUrlTenant = $supabaseUrl . "/rest/v1/tb_empresas";

                        $methodSupTenant = "POST";
                        $fieldsSupTenant = array(
                            "em_razon_social" => $_POST["name-tenant"],
                            "em_direccion" => $_POST["address-tenant"],
                            "em_telefono" => $_POST["phone-tenant"],
                            "em_nombrecomercial" => $_POST["nc-tenant"],
                            "em_logo" => "",
                            "em_simbolomoneda" => "S/",
                            "em_ubigeo" => $_POST["ubi-tenant"],
                            "em_ruc" => $_POST["ruc-tenant"],
                            "em_token" => $tokenEmpresa,
                            "em_pass" => $secretKey,
                            "id_back" => $response->response->data->lastId,
                            "id_usuario" => $responseSup->user->id
                        );

                        $dataSupTenant = json_encode($fieldsSupTenant);
                        
                        $responseSupTenant = CurlController::supabase($supUrlTenant, $methodSupTenant, $dataSupTenant, $supabaseKey);

                        // $responseSupTenantjson = json_encode($responseSupTenant);

                        // // Imprimir en la consola del navegador
                        //  echo "<script>console.log('Respuesta de Supabase empresa creacion: ', $responseSupTenantjson);</script>";
                        

                        if(is_object($responseSupTenant) && isset($responseSupTenant->code)) {

                            echo '<script>
                                    fncFormatInputs();
                                    matPreloader("off");
                                    fncSweetAlert("close", "", "");
                                    fncSweetAlert("success", "' . $response->response->data->comment . '. Error al crear la empresa en Supabase: ' . $responseSupTenant->details . '", "' . $urlRegister . '");
                                </script>';
                        
                            return;

                        } else {
                            /* Hacemos update a users con la empresa */
                            $idUser = $responseSup->user->id;
                            $idEmp = $responseSupTenant[0]->id;

                            // $idUserjson = json_encode($idUser);
                            // // Imprimir en la consola del navegador
                            // echo "<script>console.log('Respuesta de Supabase id susuario: ', $idUserjson);</script>";

                            // $idEmpjson = json_encode($idEmp);
                            // // Imprimir en la consola del navegador
                            // echo "<script>console.log('Respuesta de Supabase id Empresa: ', $idEmpjson);</script>";

                            $supUrlUser = $supabaseUrl . "/rest/v1/users?id=eq." . $idUser;

                            $methodUser = "PATCH";

                            $fieldsUser = array(
                                "id_empresa" => $responseSupTenant[0]->id
                            );

                            $dataUser = json_encode($fieldsUser);
                            $responseUserUpdate = CurlController::supabase($supUrlUser, $methodUser, $dataUser, $supabaseKey);

                            // Verificamos la respuesta del PATCH
                            // $responseUserUpdateJson = json_encode($responseUserUpdate);
                            // echo "<script>console.log('Respuesta de Supabase user update: ', $responseUserUpdateJson);</script>";


                            /* Insertamos la suscripcion */
                            $supUrlSuscr = $supabaseUrl . "/rest/v1/tb_suscripciones";

                            $methodSuscr = "POST";
                            $fieldsSuscr = array(
                                "scr_fecha_emision" => date('Y-m-d'),
                                "scr_fecha_pago" => date('Y-m-d'),
                                "scr_estado_suscripcion" => "pagado",
                                "src_adjunto_suscripcion" => "",
                                "scr_monto_pago" => $precioPlan,
                                "scr_numero_operacion" => $_POST["comprobante-tenant"],
                                "id_back_plan" => $plan,
                                "id_back_empresa" => $response->response->data->lastId,
                                "id_back_suscripcion" => $responseSusc->response->data->lastId,
                                "id_back_usuario_suscripcion" => $usuarioRegistro,
                                "src_trans_suscripcion" => $transVenta,
                                "src_medio_pago" => $_POST["metodo_pago-tenant"],
                                "scr_pdf" => "",
                                "scr_xml" => "",
                                "scr_cdr" => "",
                                "id_empresa" => $responseSupTenant[0]->id
                            );

                            $dataSuscr = json_encode($fieldsSuscr);
                            
                            $responseSuscr = CurlController::supabase($supUrlSuscr, $methodSuscr, $dataSuscr, $supabaseKey);

                            if(is_object($responseSuscr) && isset($responseSuscr->code)) {

                                echo '<script>
                                        fncFormatInputs();
                                        matPreloader("off");
                                        fncSweetAlert("close", "", "");
                                        fncSweetAlert("success", "' . $response->response->data->comment . '. Error al insertar la suscripcion en Supabase: ' . $responseSuscr->details . '", "' . $urlRegister . '");
                                    </script>';
                            
                                return;

                            } else {

                                $supabaseSuscripcion = '[{"id_usuario":"'.$responseSup->user->id.'","id_empresa":"'.$responseSupTenant[0]->id.'","id_suscripcion":"'.$responseSuscr[0]->id.'"}]';
                                $urlUser = "suscripciones?id=" . $responseSusc->response->data->lastId . "&nameId=id_suscripcion&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                                $methodUser = "PUT";
                                $fieldsUser = "supabase_suscripcion=" . $supabaseSuscripcion;

                                $responseUser = CurlController::requestSunat($urlUser, $methodUser, $fieldsUser, $token);
                                
                                echo '<script>
                                        fncFormatInputs();
                                        matPreloader("off");
                                        fncSweetAlert("close", "", "");
                                        fncSweetAlert("success", "' . $response->response->data->comment . '", "' . $urlRegister . '");
                                    </script>';
                                
                                return;

                            }

                        }

                    } else {

                        echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncSweetAlert("success", "' . $response->response->data->comment . '. Error al crear el usuario en Supabase: ' . $responseSup->msg . '", "' . $urlRegister . '");
                            </script>';
                    
                        return;

                    }

                    print_r($responseSup);

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncSweetAlert("success", "' . $response->response->data->comment . '", "' . $urlRegister . '");
                        </script>';
                    
                    return;

                }

            } else {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncNotie(3, "Failed to save record");
                    </script>';
                    
                return;

            }

        }

    }

    /*=============================================
    Actualizar datos
    =============================================*/
    public static function updateTenant($id)
    {

        if (isset($_POST["id-tenant"])) {

            /*=============================================
            Mensaje de carga
            =============================================*/
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            /*=============================================
            Validamos la sintaxis del correo
            =============================================*/
            if (preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["mail-tenant"])) {

                if ($_POST["id-tenant"] == $id) {

                    $select = "*";

                    $url = "empresas?select=" . $select . "&linkTo=id_empresa&equalTo=" . $id;
                    $method = "GET";
                    $fields = array();
                    $token = TemplateController::tokenSet();

                    $response = CurlController::requestSunat($url, $method, $fields, $token);

                    if ($response->response->success == true) {

                        $data = "nombre_comercial_empresa=" . $_POST["nc-tenant"] . "&telefono_empresa=" . $_POST["phone-tenant"] . "&email_empresa=" . $_POST["mail-tenant"] . "&direccion_empresa=" . $_POST["address-tenant"] . "&departamento_empresa=" . $_POST["dep-tenant"] . "&provincia_empresa=" . $_POST["pro-tenant"] . "&distrito_empresa=" . $_POST["dis-tenant"] . "&ubigeo_empresa=" . $_POST["ubi-tenant"];

                        $url = "empresas?id=" . $id . "&nameId=id_empresa&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                        $method = "PUT";
                        $fields = $data;

                        $response = CurlController::requestSunat($url, $method, $fields, $token);

                        if ($response->response->success == true) {

                            echo '<script>
                                    fncFormatInputs();
                                    matPreloader("off");
                                    fncSweetAlert("close", "", "");
                                    fncSweetAlert("success", "' . $response->response->data->comment . '", "/businesses/general");
                                </script>';

                        } else {

                            echo '<script>
                                    fncFormatInputs();
                                    matPreloader("off");
                                    fncSweetAlert("close", "", "");
                                    fncNotie(3, "Failed to save record");
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

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to save record");
                        </script>';

                }

            } else {

                echo '<script>
						fncFormatInputs();
						matPreloader("off");
						fncSweetAlert("close", "", "");
						fncNotie(3, "Field syntax error");
					</script>';

            }

        }

    }

    /*=============================================
    Modificar empresas
    =============================================*/
    public static function edit($id)
    {

        if (isset($_POST["idTenant"])) {

            /*=============================================
            Mensaje de carga
            =============================================*/
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            /*=============================================
            Enviamos los datos
            =============================================*/
            if ($_POST["idTenant"] == $id) {

                $select = "*";

                $url = "empresas?select=" . $select . "&linkTo=id_empresa&equalTo=" . $id;
                $method = "GET";
                $fields = array();
                $token = TemplateController::tokenSet();

                $response = CurlController::requestSunat($url, $method, $fields, $token);

                if ($response->response->success == true) {

                    $data = "nombre_comercial_empresa=" . $_POST["nc-tenant"] . "&telefono_empresa=" . $_POST["phone-tenant"] . "&email_empresa=" . $_POST["email-tenant"] . "&direccion_empresa=" . $_POST["address-tenant"] . "&departamento_empresa=" . $_POST["dep-tenant"] . "&provincia_empresa=" . $_POST["pro-tenant"] . "&distrito_empresa=" . $_POST["dis-tenant"] . "&ubigeo_empresa=" . $_POST["ubi-tenant"] . "&proxima_facturacion_empresa=" . $_POST["pfact-tenant"] . "&id_plan_empresa=" . $_POST["plan-tenant"];

                    $url = "empresas?id=" . $id . "&nameId=id_empresa&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                    $method = "PUT";
                    $fields = $data;

                    $response = CurlController::requestSunat($url, $method, $fields, $token);

                    if ($response->response->success == true) {

                        echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncSweetAlert("success", "' . $response->response->data->comment . '", "/tenants");
                            </script>';

                    } else {

                        echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncNotie(3, "Failed to save record");
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