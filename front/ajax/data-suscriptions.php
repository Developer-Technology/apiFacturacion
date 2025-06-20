<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Iniciamos la sesion
=============================================*/
session_start();

/*=============================================
Requerimos los controladores
=============================================*/
require_once "../controllers/curl.controller.php";
require_once "../controllers/template.controller.php";

class DatatableController
{
    public function data()
    {
        if (!empty($_POST)) {
            /*=============================================
            Capturando y organizando las variables POST de DT
            =============================================*/
            $draw = $_POST["draw"]; //Contador utilizado por DataTables para garantizar que los retornos de Ajax de las solicitudes de procesamiento del lado del servidor sean dibujados en secuencia por DataTables

            $orderByColumnIndex = $_POST["order"][0]["column"]; //Índice de la columna de clasificación (0 basado en el índice, es decir, 0 es el primer registro)

            $orderBy = $_POST["columns"][$orderByColumnIndex]["data"]; //Obtener el nombre de la columna de clasificación de su índice

            $orderType = $_POST["order"][0]["dir"]; // Obtener el orden ASC o DESC

            $start = $_POST["start"]; //Indicador de primer registro de paginación.

            $length = $_POST["length"]; //Indicador de la longitud de la paginación.

            /*=============================================
            El total de registros de la data
            =============================================*/
            if (!empty($_SESSION["admin"])) {
                $url =
                    "relations?rel=suscripciones,planes,empresas,usuarios&type=suscripcion,plan,empresa,usuario&select=id_suscripcion&linkTo=creado_suscripcion&between1=" .
                    $_GET["between1"] .
                    "&between2=" .
                    $_GET["between2"];
            } else {
                $url =
                    "relations?rel=suscripciones,planes,empresas,usuarios&type=suscripcion,plan,empresa,usuario&select=id_suscripcion&linkTo=creado_suscripcion&between1=" .
                    $_GET["between1"] .
                    "&between2=" .
                    $_GET["between2"] .
                    "&filterTo=id_empresa&inTo=" .
                    $_GET["idTenant"];
            }

            $token = TemplateController::tokenSet();
            $method = "GET";
            $fields = [];

            $response = CurlController::requestSunat(
                $url,
                $method,
                $fields,
                $token
            );

            if ($response->response->status == 200) {
                $totalData = $response->response->total;
            } else {
                echo '{"data": []}';

                return;
            }

            /*=============================================
            Búsqueda de datos
            =============================================*/
            $select = "*";

            if (!empty($_POST["search"]["value"])) {
                if (
                    preg_match(
                        '/^[0-9A-Za-zñÑáéíóú ]{1,}$/',
                        $_POST["search"]["value"]
                    )
                ) {
                    $linkTo = [
                        "nombre_plan",
                        "trans_suscripcion",
                        "medio_pago_suscripcion",
                        "creado_suscripcion",
                        "alias_usuario",
                    ];

                    $search = str_replace(" ", "_", $_POST["search"]["value"]);

                    foreach ($linkTo as $key => $value) {
                        if (!empty($_SESSION["admin"])) {
                            $url =
                                "relations?rel=suscripciones,planes,empresas,usuarios&type=suscripcion,plan,empresa,usuario&select=" .
                                $select .
                                "&linkTo=" .
                                $value .
                                "&search=" .
                                $search .
                                "&orderBy=" .
                                $orderBy .
                                "&orderMode=" .
                                $orderType .
                                "&startAt=" .
                                $start .
                                "&endAt=" .
                                $length;
                        } else {
                            $url =
                                "relations?rel=suscripciones,planes,empresas,usuarios&type=suscripcion,plan,empresa,usuario&select=" .
                                $select .
                                "&linkTo=" .
                                $value .
                                "&search=" .
                                $search .
                                "&orderBy=" .
                                $orderBy .
                                "&orderMode=" .
                                $orderType .
                                "&startAt=" .
                                $start .
                                "&endAt=" .
                                $length .
                                "&filterTo=id_empresa&inTo=" .
                                $_GET["idTenant"];
                        }

                        $data = CurlController::requestSunat(
                            $url,
                            $method,
                            $fields,
                            $token
                        )->response->data;

                        if (empty($data)) {
                            $data = [];
                            $recordsFiltered = count($data);
                        } else {
                            $data = $data;
                            $recordsFiltered = count($data);

                            break;
                        }
                    }
                } else {
                    echo '{"data": []}';

                    return;
                }
            } else {
                /*=============================================
                Seleccionar datos
                =============================================*/
                if (!empty($_SESSION["admin"])) {
                    $url =
                        "relations?rel=suscripciones,planes,empresas,usuarios&type=suscripcion,plan,empresa,usuario&select=" .
                        $select .
                        "&linkTo=creado_suscripcion&between1=" .
                        $_GET["between1"] .
                        "&between2=" .
                        $_GET["between2"] .
                        "&orderBy=" .
                        $orderBy .
                        "&orderMode=" .
                        $orderType .
                        "&startAt=" .
                        $start .
                        "&endAt=" .
                        $length;
                } else {
                    $url =
                        "relations?rel=suscripciones,planes,empresas,usuarios&type=suscripcion,plan,empresa,usuario&select=" .
                        $select .
                        "&linkTo=creado_suscripcion&between1=" .
                        $_GET["between1"] .
                        "&between2=" .
                        $_GET["between2"] .
                        "&orderBy=" .
                        $orderBy .
                        "&orderMode=" .
                        $orderType .
                        "&startAt=" .
                        $start .
                        "&endAt=" .
                        $length .
                        "&filterTo=id_empresa&inTo=" .
                        $_GET["idTenant"];
                }

                $data = CurlController::requestSunat(
                    $url,
                    $method,
                    $fields,
                    $token
                )->response->data;

                $recordsFiltered = $totalData;
            }

            /*=============================================
            Cuando la data viene vacía
            =============================================*/
            if (empty($data)) {
                echo '{"data": []}';

                return;
            }

            /*=============================================
            Construimos el dato JSON a regresar
            =============================================*/
            $dataJson =
                '{

            	"Draw": ' .
                intval($draw) .
                ',
            	"recordsTotal": ' .
                $totalData .
                ',
            	"recordsFiltered": ' .
                $recordsFiltered .
                ',
            	"data": [';

            /*=============================================
            Recorremos la data
            =============================================*/
            foreach ($data as $key => $value) {
                /*=============================================
                Definimos la zona horaria
                =============================================*/
                date_default_timezone_set("America/Lima");
                $fechaHoy = date("Y-m-d");

                if ($_GET["text"] == "flat") {
                    /* Validar el estado */
                    if ($value->estado_suscripcion == "pagado") {
                        $pagado =
                            "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <small>Pagado</small>
                                        <small>ID: " .
                            $value->trans_suscripcion .
                            "</small>
                                    </div>
                                </div>";
                        $pagar = "";
                    } elseif ($value->estado_suscripcion == "pendiente") {
                        $pagado =
                            "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <small>En Revisión</small>
                                        <small>ID: " .
                            $value->trans_suscripcion .
                            "</small>
                                    </div>
                                </div>";
                        $pagar = "";
                    } elseif ($value->estado_suscripcion == "rechazado") {
                        $pagado =
                            "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <small>Rechazado</small>
                                        <small>ID: " .
                            $value->trans_suscripcion .
                            "</small>
                                    </div>
                                </div>";
                        $pagar = "";
                    } else {
                        if (
                            strtotime($fechaHoy) >
                            strtotime($value->proxima_facturacion_empresa)
                        ) {
                            $pagado = "Deuda";
                            $pagar = "";
                        } else {
                            $pagado = "Pendiente";
                            $pagar = "";
                        }
                    }

                    $descargar = "";
                    $acciones = "";
                } else {
                    /* Validar el estado */
                    if ($value->estado_suscripcion == "pagado") {
                        $pagado =
                            "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <span class='badge badge-success'>Pagado</span>
                                        <small>ID: " .
                            $value->trans_suscripcion .
                            "</small>
                                    </div>
                                </div>";
                        if (
                            !empty($_SESSION["admin"]) &&
                            empty($_SESSION["empresa"])
                        ) {
                            $pagar =
                                "<button class='btn btn-xs btn-success' disabled><i class='fa fa-check'></i> Aprobado</button>";
                        } else {
                            $pagar =
                                "<button class='btn btn-xs btn-dark' disabled>Pagar</button>";
                        }
                    } elseif ($value->estado_suscripcion == "pendiente") {
                        $pagado =
                            "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <span class='badge badge-info'>En Revisión</span>
                                        <small>ID: " .
                            $value->trans_suscripcion .
                            "</small>
                                    </div>
                                </div>";

                        if (
                            !empty($_SESSION["admin"]) &&
                            empty($_SESSION["empresa"])
                        ) {
                            $pagar =
                                "<a href='/suscriptions/verify/" .
                                base64_encode(
                                    $value->id_suscripcion .
                                        "~" .
                                        $_GET["token"]
                                ) .
                                "' class='btn btn-xs btn-info'><i class='fa fa-clock'></i> Verificar</a>";
                        } else {
                            $pagar =
                                "<button class='btn btn-xs btn-dark' disabled>Pagar</button>";
                        }
                    } elseif ($value->estado_suscripcion == "rechazado") {
                        $pagado =
                            "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <span class='badge badge-danger'>Rechazado</span>
                                        <small>ID: " .
                            $value->trans_suscripcion .
                            "</small>
                                    </div>
                                </div>";
                        if (
                            !empty($_SESSION["admin"]) &&
                            empty($_SESSION["empresa"])
                        ) {
                            $pagar =
                                "<button class='btn btn-xs btn-danger' disabled><i class='fa fa-ban'></i> Rechazado</button>";
                        } else {
                            $pagar =
                                "<button class='btn btn-xs btn-dark' disabled>Pagar</button>";
                        }
                    } else {
                        if (
                            strtotime($fechaHoy) >
                            strtotime($value->proxima_facturacion_empresa)
                        ) {
                            $pagado =
                                "<div class='d-flex flex-row'>
                                        <div class='d-flex flex-column'>
                                            <span class='badge badge-danger'>Deuda</span>
                                            <small>ID: " .
                                $value->trans_suscripcion .
                                "</small>
                                        </div>
                                    </div>";
                            if (
                                !empty($_SESSION["admin"]) &&
                                empty($_SESSION["empresa"])
                            ) {
                                $pagar =
                                    "<button class='btn btn-xs btn-danger' disabled><i class='fa fa-dollar'></i> Deuda</button>";
                            } else {
                                $pagar =
                                    "<a href='/suscriptions/pay/" .
                                    base64_encode(
                                        $value->id_suscripcion .
                                            "~" .
                                            $_GET["token"]
                                    ) .
                                    "' class='btn btn-xs btn-danger'>Pagar Deuda</a>";
                            }
                        } else {
                            $pagado =
                                "<div class='d-flex flex-row'>
                                        <div class='d-flex flex-column'>
                                            <span class='badge badge-warning'>Pendiente</span>
                                            <small>ID: " .
                                $value->trans_suscripcion .
                                "</small>
                                        </div>
                                    </div>";
                            if (
                                !empty($_SESSION["admin"]) &&
                                empty($_SESSION["empresa"])
                            ) {
                                $pagar =
                                    "<button class='btn btn-xs btn-warning' disabled><i class='fa fa-dollar'></i> Pagar</button>";
                            } else {
                                $pagar =
                                    "<a href='/suscriptions/pay/" .
                                    base64_encode(
                                        $value->id_suscripcion .
                                            "~" .
                                            $_GET["token"]
                                    ) .
                                    "' class='btn btn-xs btn-success'>Pagar</a>";
                            }
                        }
                    }

                    /*=============================================
                    Cargar / Descargar comprobante
                    =============================================*/

                    /* Validamos si existe el comprobante */
                    if (
                        $value->adjunto_suscripcion != "" ||
                        $value->adjunto_suscripcion != null
                    ) {
                        $urlDownload =
                            TemplateController::srcImg() .
                            "pagos/" .
                            $value->ruc_empresa .
                            "/" .
                            $value->adjunto_suscripcion;
                        $descargar =
                            "<a href='" .
                            $urlDownload .
                            "' target='_blank' class='btn btn-xs btn-dark'><i class='fa fa-download'></i> Descargar</a>";
                    } else {
                        $descargar =
                            "<button class='btn btn-xs btn-dark' disabled><i class='fa fa-download'></i> Descargar</button>";
                    }

                    $acciones =
                        "<a href='/suscriptions/upload/" .
                        base64_encode(
                            $value->id_suscripcion . "~" . $_GET["token"]
                        ) .
                        "' class='btn btn-xs btn-dark'><i class='fa fa-upload'></i> Cargar</a>";
                }

                $pagado = TemplateController::htmlClean($pagado);
                $descargar = TemplateController::htmlClean($descargar);
                $acciones = TemplateController::htmlClean($acciones);

                if (!empty($_SESSION["admin"]) && empty($_SESSION["empresa"])) {
                    $empresa =
                        "<small>Empresa: " . $value->ruc_empresa . "</small>";
                } else {
                    $empresa = "";
                }

                $dataCompra =
                    "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Método: " .
                    TemplateController::capitalize(
                        $value->medio_pago_suscripcion
                    ) .
                    "</small>
                                    <small>Documento: " .
                    $value->comprobante_suscripcion .
                    "</small>
                                    " .
                    $empresa .
                    "
                                    <small> " .
                    $value->razon_social_empresa .
                    " </small>
                                </div>
                            </div>";
                $dataCompra = TemplateController::htmlClean($dataCompra);

                $dataPlan =
                    "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Plan: " .
                    $value->nombre_plan .
                    "</small>
                                    <small>Precio: " .
                    $value->precio_plan .
                    "</small>
                                </div>
                            </div>";
                $dataPlan = TemplateController::htmlClean($dataPlan);

                $dataFecha =
                    "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Emisión: " .
                    TemplateController::fechaEsShort(
                        $value->fecha_emision_suscripcion
                    ) .
                    "</small>
                                    <small>Venicimiento: " .
                    TemplateController::fechaEsShort(
                        $value->proxima_facturacion_empresa
                    ) .
                    "</small>
                                </div>
                            </div>";
                $dataFecha = TemplateController::htmlClean($dataFecha);

                $creado_suscripcion = TemplateController::fechaEsShort(
                    $value->creado_suscripcion
                );

                if (
                    $value->fecha_pago_suscripcion == "0000-00-00" ||
                    $value->fecha_pago_suscripcion == "1969-12-31" ||
                    $value->fecha_pago_suscripcion == null
                ) {
                    $fechaPago = "----";
                } else {
                    $fechaPago = TemplateController::fechaEsShort(
                        $value->fecha_pago_suscripcion
                    );
                }

                $dataJson .=
                    '{

            		"id_suscripcion":"' .
                    ($start + $key + 1) .
                    '",
            		"fecha_emision":"' .
                    $dataFecha .
                    '",
                    "fecha_pago":"' .
                    $fechaPago .
                    '",
            		"datos_compra":"' .
                    $dataCompra .
                    '",
                    "monto_pago":"' .
                    $value->monto_pago_suscripcion .
                    '",
                    "plan_suscripcion":"' .
                    $dataPlan .
                    '",
            		"estado_suscripcion":"' .
                    $pagado .
                    '",
                    "descargar_suscripcion":"' .
                    $descargar .
                    '",
                    "acciones":"' .
                    $acciones .
                    '",
                    "pago_suscripcion":"' .
                    $pagar .
                    '"

            	},';
            }

            $dataJson = substr($dataJson, 0, -1); // este substr quita el último caracter de la cadena, que es una coma, para impedir que rompa la tabla

            $dataJson .= "]}";

            echo $dataJson;
        }
    }
}

/*=============================================
Activar función DataTable
=============================================*/
$data = new DatatableController();
$data->data();
