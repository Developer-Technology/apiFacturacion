<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class SalesController
{

    /*=============================================
    Creación XML Boleta / Factura
    =============================================*/
    public function createGravada()
    {

        if (isset($_POST["type-sale"])) {
            
            /*=============================================
            Obtenemos los datos de la empresa
            =============================================*/
            require_once "controllers/tenants.controller.php";
        
            $value = $_SESSION["empresa"]->id_empresa;
        
            $dataTenants = TenantsController::dataTenant($value);

            /*=============================================
            Mensaje de carga
            =============================================*/
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            /*=============================================
            Agrupamos los datos del item
            =============================================*/
            $detalles[] = array(
                "descripcion" => "PRODUCTO 1",
                "codProducto" => "PR001",
                "unidad" => "NIU",
                "tipoPrecio" => "01",
                "cantidad" => "1",
                "mtoBaseIgv" => "100",
                "mtoValorUnitario" => "100",
                "mtoPrecioUnitario" => "118",
                "codeAfectAlt" => 10,
                "codeAfect" => 1000,
                "nameAfect" => "IGV",
                "tipoAfect" => "VAT",
                "igvPorcent" => number_format(18, 1),
                "igv" => 18,
                "igvOpi" => 18,
                "icbper" => 0.00,
                "descuentos" => array(
                    "monto" => 0,
                    "codigoTipo" => "",
                    "factor" => "",
                    "montoBase" => "",
                ),
            );

            /*=============================================
            Agrupamos la informacion de las cuotas
            =============================================*/
            $cuotas[] = array(
                "cuota" => "",
                "fechaCuota" => "",
            );

            /*=============================================
            Agrupamos la informacion
            =============================================*/
            $dataXml = array(
                "comprobante" => array(
                    "tipoDoc" => $_POST["type-sale"],
                    "serie" => $_POST["serie-sale"],
                    "correlativo" => $_POST["number-sale"],
                    "fechaEmision" => date("Y-m-d", strtotime($_POST["date-sale"])),
                    "horaEmision" => date('H:i:s'),
                    "tipoMoneda" => $_POST["mon-sale"],
                    "tipoPago" => "Contado",
                    "cuotas" => $cuotas,
                    "total" => 118,
                    "dsctoGlobal" => array(
                        "descuento" => 0,
                        "codigoTipo" => "",
                        "descuentoFactor" => "",
                        "montoBase" => "",
                    ),
                    "mtoIGV" => 18,
                    "icbper" => 0,
                    "mtoOperGravadas" => 100,
                    "mtoOperExoneradas" => 0,
                    "mtoOperInafectas" => 0,
                    "mtoOperGratuitas" => 0,
                    "bienesSelva" => "no",
                    "serviciosSelva" => "no",
                    "totalTexto" => "SON CIENTO DIECIOCHO CON 00/100 SOLES",
                ),
                "company" => array(
                    "tipoDoc" => 6,
                    "ruc" => $dataTenants["ruc_empresa"],
                    "razonSocial" => $dataTenants["razon_social_empresa"],
                    "nombreComercial" => $dataTenants["nombre_comercial_empresa"],
                    "address" => array(
                        "codigoPais" => "PE",
                        "departamento" => $dataTenants["departamento_empresa"],
                        "provincia" => $dataTenants["provincia_empresa"],
                        "distrito" => $dataTenants["distrito_empresa"],
                        "direccion" => $dataTenants["direccion_empresa"],
                        "ubigeo" => $dataTenants["ubigeo_empresa"],
                    ),
                ),
                "client" => array(
                    "codigoPais" => "PE",
                    "tipoDoc" => $_POST["type_client-sale"],
                    "numDoc" => $_POST["doc_client-sale"],
                    "rznSocial" => $_POST["name_client-sale"],
                    "direccion" => $_POST["address_client-sale"],
                ),
                "details" => $detalles,
            );

            /*=============================================
            Agrupamos los datos para el envio
            =============================================*/
            $dataSend = array(
                "rucTenant" => $dataTenants["ruc_empresa"],
                "urlFact" => TemplateController::srcImg(),
                "tipoDoc" => $_POST["type-sale"],
                "serie" => $_POST["serie-sale"],
                "correlativo" => $_POST["number-sale"],
            );
            $jsonData = json_encode($dataSend);

            /*=============================================
            Creamos el xml
            =============================================*/
            $urlXml = 'invoice/create';
            $methodXml = 'POST';
            $token = $dataTenants["token_empresa"];
            $fieldXml = json_encode($dataXml, true);

            $responseXml = CurlController::requestSunat($urlXml, $methodXml, $fieldXml, $token);

            if ($responseXml) {

                if ($responseXml->response->success == true) {

                    /*=============================================
                    Agrupamos los datos de las cuotas
                    =============================================*/
                    $cuotas[] = array(
                        "cuota" => "",
                        "fechaCuota" => "",
                    );

                    /*=============================================
                    Validamos el logo de la empresa
                    =============================================*/
                    if($dataTenants["logo_empresa"] == '') {

                        $imgTenant = TemplateController::returnImgDefault('logo-blank.png', '');

                    } else {

                        $imgTenant = $dataTenants["logo_empresa"];

                    }

                    /*=============================================
                    Creamos el pdf A4
                    =============================================*/
                    $dataPdf = array(
                        "comprobante" => array(
                            "tipoDoc" => $_POST["type-sale"],
                            "serie" => $_POST["serie-sale"],
                            "correlativo" => $_POST["number-sale"],
                            "fechaEmision" => date("Y-m-d", strtotime($_POST["date-sale"])),
                            "horaEmision" => date("H:i:s"),
                            "tipoMoneda" => $_POST["mon-sale"],
                            "tipoPago" => $_POST["method-sale"],
                            "cuotas" => $cuotas,
                            "total" => 118,
                            "dsctoGlobal" => array(
                                "descuento" => 0,
                            ),
                            "mtoIGV" => 18,
                            "icbper" => 0,
                            "mtoOperGravadas" => 100,
                            "mtoOperExoneradas" => 0,
                            "mtoOperInafectas" => 0,
                            "mtoOperGratuitas" => 0,
                            "bienesSelva" => "no",
                            "serviciosSelva" => "no",
                            "totalTexto" => "SON CIENTO DIECIOCHO CON 00/100 SOLES",
                        ),
                        "company" => array(
                            "tipoDoc" => 6,
                            "ruc" => $dataTenants["ruc_empresa"],
                            "razonSocial" => $dataTenants["razon_social_empresa"],
                            "telefono" => $dataTenants["telefono_empresa"],
                            "logo" => $imgTenant,
                            "address" => array(
                                "direccion" => $dataTenants["direccion_empresa"]
                            ),
                        ),
                        "client" => array(
                            "tipoDoc" => $_POST["type_client-sale"],
                            "numDoc" => $_POST["doc_client-sale"],
                            "rznSocial" => $_POST["name_client-sale"],
                            "direccion" => $_POST["address_client-sale"],
                        ),
                        "details" => $detalles,
                    );

                    $urlA4 = 'invoice/a4';
                    $methodA4 = 'POST';
                    $fieldA4 = json_encode($dataPdf, true);

                    $responseA4 = CurlController::requestSunat($urlA4, $methodA4, $fieldA4, $token);

                    /*=============================================
                    Creamos el pdf ticket
                    =============================================*/
                    $urlTicket = 'invoice/ticket';
                    $methodTicket = 'POST';
                    $fieldTicket = json_encode($dataPdf, true);

                    $responseTicket = CurlController::requestSunat($urlTicket, $methodTicket, $fieldTicket, $token);

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            Swal.fire({
                                icon: "success",
                                title: "Bien hecho!",
                                text: "' . $responseXml->response->message . '",
                                confirmButtonText: "Firmar y Enviar a SUNAT"
                            }).then((result) => {
                                sendSunat(' . $jsonData . ');
                                });
                        </script>';

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "' . $responseXml->response->message . '");
                        </script>';

                }

            } else {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncNotie(3, "Error al general el XML");
                    </script>';

            }

        }

    }

    /*=============================================
    Envio a SUNAT Facturas / Boletas
    =============================================*/
    public function sendSunat($data)
    {

        session_start();
        /*=============================================
        Obtenemos los datos de la empresa
        =============================================*/
        require_once "curl.controller.php";
        require_once "tenants.controller.php";
        require_once "../models/tenants.model.php";

        $item = "id_empresa";
        $value = $_SESSION["empresa"]["id_empresa"];

        $dataTenants = TenantsController::ctrListTenants($item, $value);

        $dataSend = array(
            "comprobante" => array(
                "tipoDoc" => $data["typeSale"],
                "serie" => $data["serieSale"],
                "correlativo" => $data["numberSale"],
            ),
            "company" => array(
                "tipoDoc" => 6,
                "ruc" => $dataTenants["ruc_empresa"],
                "modo" => $dataTenants["fase_empresa"],
                "usuarioSol" => $dataTenants["usuario_sol_empresa"],
                "claveSol" => $dataTenants["clave_sol_empresa"],
                "claveCertificado" => $dataTenants["clave_certificado_empresa"],
            ),
        );

        $urlSend = 'invoice/send';
        $methodSend = 'POST';
        $token = $dataTenants["token_empresa"];
        $fieldsSend = json_encode($dataSend, true);

        $responseSend = CurlController::requestSunat($urlSend, $methodSend, $fieldsSend, $token);

        if ($responseSend) {

            echo json_encode($responseSend);

        }

    }

    /*=============================================
    Creación XML Nota Crédito / Débito
    =============================================*/
    public function createNotes()
    {

        if (isset($_POST["type-sale"])) {

            /*=============================================
            Obtenemos los datos de la empresa
            =============================================*/
            require_once "controllers/tenants.controller.php";
            require_once "models/tenants.model.php";

            $item = "id_empresa";
            $value = $_SESSION["empresa"]["id_empresa"];

            $dataTenants = TenantsController::ctrListTenants($item, $value);

            /*=============================================
            Mensaje de carga
            =============================================*/
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            /*=============================================
            Agrupamos los datos del item
            =============================================*/
            $detalles[] = array(
                "descripcion" => "PRODUCTO 1",
                "codProducto" => "PR001",
                "unidad" => "NIU",
                "tipoPrecio" => "01",
                "cantidad" => "1",
                "mtoBaseIgv" => "100",
                "mtoValorUnitario" => "100",
                "mtoPrecioUnitario" => "118",
                "codeAfectAlt" => 10,
                "codeAfect" => 1000,
                "nameAfect" => "IGV",
                "tipoAfect" => "VAT",
                "igvPorcent" => number_format(18, 1),
                "igv" => 18,
                "igvOpi" => 18,
                "icbper" => 0.00,
                "descuentos" => array(
                    "monto" => 0,
                    "codigoTipo" => "",
                    "factor" => "",
                    "montoBase" => "",
                ),
            );

            /*=============================================
            Agrupamos la informacion de las cuotas
            =============================================*/
            $cuotas[] = array(
                "cuota" => "",
                "fechaCuota" => "",
            );

            /*=============================================
            Agrupamos la informacion
            =============================================*/
            $dataXml = array(
                "comprobante" => array(
                    "tipoDoc" => $_POST["type-sale"],
                    "serie" => $_POST["serie-sale"],
                    "correlativo" => $_POST["number-sale"],
                    "codmotivo" => "01",
                    "descripcion" => "ERROR DE EMISION",
                    "serieRef" => "F001",
                    "correlativoRef" => "28",
                    "tipoCompRef" => "01",
                    "fechaEmision" => date("Y-m-d", strtotime($_POST["date-sale"])),
                    "horaEmision" => date('H:i:s'),
                    "tipoMoneda" => $_POST["mon-sale"],
                    "tipoPago" => "Contado",
                    "cuotas" => $cuotas,
                    "total" => 118,
                    "dsctoGlobal" => array(
                        "descuento" => 0,
                        "codigoTipo" => "",
                        "descuentoFactor" => "",
                        "montoBase" => "",
                    ),
                    "mtoIGV" => 18,
                    "icbper" => 0,
                    "mtoOperGravadas" => 100,
                    "mtoOperExoneradas" => 0,
                    "mtoOperInafectas" => 0,
                    "mtoOperGratuitas" => 0,
                    "bienesSelva" => "no",
                    "serviciosSelva" => "no",
                    "totalTexto" => "SON CIENTO DIECIOCHO CON 00/100 SOLES",
                ),
                "company" => array(
                    "tipoDoc" => 6,
                    "ruc" => $dataTenants["ruc_empresa"],
                    "modo" => $dataTenants["fase_empresa"],
                    "usuarioSol" => $dataTenants["usuario_sol_empresa"],
                    "claveSol" => $dataTenants["clave_sol_empresa"],
                    "claveCertificado" => $dataTenants["clave_certificado_empresa"],
                    "razonSocial" => $dataTenants["razon_social_empresa"],
                    "nombreComercial" => $dataTenants["nombre_comercial_empresa"],
                    "address" => array(
                        "codigoPais" => "PE",
                        "departamento" => $dataTenants["departamento_empresa"],
                        "provincia" => $dataTenants["provincia_empresa"],
                        "distrito" => $dataTenants["distrito_empresa"],
                        "direccion" => $dataTenants["direccion_empresa"],
                        "ubigeo" => $dataTenants["ubigeo_empresa"],
                    ),
                ),
                "client" => array(
                    "codigoPais" => "PE",
                    "tipoDoc" => $_POST["type_client-sale"],
                    "numDoc" => $_POST["doc_client-sale"],
                    "rznSocial" => $_POST["name_client-sale"],
                    "direccion" => $_POST["address_client-sale"],
                ),
                "details" => $detalles,
            );

            /*=============================================
            Creamos el xml
            =============================================*/
            $urlXml = 'note/send';
            $methodXml = 'POST';
            $token = $dataTenants["token_empresa"];
            $fieldXml = json_encode($dataXml, true);

            $responseXml = CurlController::requestSunat($urlXml, $methodXml, $fieldXml, $token);

            if ($responseXml) {

                if ($responseXml->response->success == true) {

                    /*=============================================
                    Agrupamos los datos de las cuotas
                    =============================================*/
                    $cuotas[] = array(
                        "cuota" => "",
                        "fechaCuota" => "",
                    );

                    /*=============================================
                    Validamos el logo de la empresa
                    =============================================*/
                    if($dataTenants["logo_empresa"] == '') {

                        $imgTenant = TemplateController::returnImgDefault('logo-blank.png', '');

                    } else {

                        $imgTenant = $dataTenants["logo_empresa"];

                    }

                    /*=============================================
                    Creamos el pdf A4
                    =============================================*/
                    $dataPdf = array(
                        "comprobante" => array(
                            "tipoDoc" => $_POST["type-sale"],
                            "serie" => $_POST["serie-sale"],
                            "correlativo" => $_POST["number-sale"],
                            "fechaEmision" => date("Y-m-d", strtotime($_POST["date-sale"])),
                            "horaEmision" => date("H:i:s"),
                            "tipoMoneda" => $_POST["mon-sale"],
                            "codmotivo" => "01",
                            "descripcion" => "ANULACIÓN DE LA OPERACIÓN",
                            "serieRef" => "F001",
                            "correlativoRef" => "1",
                            "tipoCompRef" => "01",
                            "tipoPago" => $_POST["method-sale"],
                            "cuotas" => $cuotas,
                            "total" => 118,
                            "dsctoGlobal" => array(
                                "descuento" => 0,
                            ),
                            "mtoIGV" => 18,
                            "icbper" => 0,
                            "mtoOperGravadas" => 100,
                            "mtoOperExoneradas" => 0,
                            "mtoOperInafectas" => 0,
                            "mtoOperGratuitas" => 0,
                            "bienesSelva" => "no",
                            "serviciosSelva" => "no",
                            "totalTexto" => "SON CIENTO DIECIOCHO CON 00/100 SOLES",
                        ),
                        "company" => array(
                            "tipoDoc" => 6,
                            "ruc" => $dataTenants["ruc_empresa"],
                            "razonSocial" => $dataTenants["razon_social_empresa"],
                            "telefono" => $dataTenants["telefono_empresa"],
                            "logo" => $imgTenant,
                            "address" => array(
                                "direccion" => $dataTenants["direccion_empresa"]
                            ),
                        ),
                        "client" => array(
                            "tipoDoc" => $_POST["type_client-sale"],
                            "numDoc" => $_POST["doc_client-sale"],
                            "rznSocial" => $_POST["name_client-sale"],
                            "direccion" => $_POST["address_client-sale"],
                        ),
                        "details" => $detalles,
                    );

                    $urlA4 = 'note/a4';
                    $methodA4 = 'POST';
                    $fieldA4 = json_encode($dataPdf, true);

                    $responseA4 = CurlController::requestSunat($urlA4, $methodA4, $fieldA4, $token);

                    /*=============================================
                    Creamos el pdf ticket
                    =============================================*/
                    $urlTicket = 'note/ticket';
                    $methodTicket = 'POST';
                    $fieldTicket = json_encode($dataPdf, true);

                    $responseTicket = CurlController::requestSunat($urlTicket, $methodTicket, $fieldTicket, $token);

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            Swal.fire({
                                allowOutsideClick: false,
                                title: "Bien hecho!",
                                icon: "success",
                                html: `<div class="alert alert-success" role="alert" style="font-size: 14px;">' . $responseXml->response->message . '</div><a class="pointer btn btn-primary waves-effect" onclick="printSale(' . $dataTenants["ruc_empresa"] . ','  . $_POST["type-sale"] . ',1)">Print Ticket</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="pointer btn btn-primary waves-effect" onclick="printSale(' . $dataTenants["ruc_empresa"] . ','  . $_POST["type-sale"] . ',2)">Print A4</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="pointer btn btn-primary waves-effect" onclick="printSale(' . $dataTenants["ruc_empresa"] . ','  . $_POST["type-sale"] . ',3)">Ver XML</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="pointer btn btn-primary waves-effect" onclick="printSale(' . $dataTenants["ruc_empresa"] . ','  . $_POST["type-sale"] . ',4)">Ver CDR</a>`,
                                showConfirmButton: true,
                                confirmButtonColor: "#df5645",
                                confirmButtonText: "Cerrar Ventana",
                                showCancelButton: false,
                              }).then((result) => {
                                location.reload();
                            });
                        </script>';

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "' . $responseXml->response->message . '");
                        </script>';

                }

            } else {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncNotie(3, "Error al general el XML");
                    </script>';

            }

        }

    }

}
