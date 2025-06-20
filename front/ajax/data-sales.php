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

            $orderByColumnIndex = $_POST['order'][0]['column']; //Índice de la columna de clasificación (0 basado en el índice, es decir, 0 es el primer registro)

            $orderBy = $_POST['columns'][$orderByColumnIndex]["data"]; //Obtener el nombre de la columna de clasificación de su índice

            $orderType = $_POST['order'][0]['dir']; // Obtener el orden ASC o DESC

            $start = $_POST["start"]; //Indicador de primer registro de paginación.

            $length = $_POST['length']; //Indicador de la longitud de la paginación.

            /*=============================================
            El total de registros de la data
            =============================================*/
            if (!empty($_SESSION["admin"])) {

                $url = "relations?rel=ventas,planes,usuarios&type=venta,plan,usuario&select=id_plan&linkTo=creado_plan&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"];

            } else {

                $url = "relations?rel=ventas,planes,usuarios&type=venta,plan,usuario&select=id_plan&linkTo=creado_plan&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&filterTo=id_usuario_venta&inTo=" . $_SESSION["user"]->id_usuario;

            }

            $token = TemplateController::tokenSet();
            $method = "GET";
            $fields = array();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

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

            if (!empty($_POST['search']['value'])) {

                if (preg_match('/^[0-9A-Za-zñÑáéíóú ]{1,}$/', $_POST['search']['value'])) {

                    $linkTo = ["nombre_plan", "trans_venta", "metodo_venta", "creado_venta", "alias_usuario"];

                    $search = str_replace(" ", "_", $_POST['search']['value']);

                    foreach ($linkTo as $key => $value) {

                        if (!empty($_SESSION["admin"])) {

                            $url = "relations?rel=ventas,planes,usuarios&type=venta,plan,usuario&select=" . $select . "&linkTo=" . $value . "&search=" . $search . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length;

                        } else {

                            $url = "relations?rel=ventas,planes,usuarios&type=venta,plan,usuario&select=" . $select . "&linkTo=" . $value . "&search=" . $search . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length . "&filterTo=id_usuario_venta&inTo=" . $_SESSION["user"]->id_usuario;

                        }

                        $data = CurlController::requestSunat($url, $method, $fields, $token)->response->data;

                        if (empty($data)) {

                            $data = array();
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

                    $url = "relations?rel=ventas,planes,usuarios&type=venta,plan,usuario&select=" . $select . "&linkTo=creado_plan&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length;

                } else {

                    $url = "relations?rel=ventas,planes,usuarios&type=venta,plan,usuario&select=" . $select . "&linkTo=creado_plan&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length . "&filterTo=id_usuario_venta&inTo=" . $_SESSION["user"]->id_usuario;

                }

                $data = CurlController::requestSunat($url, $method, $fields, $token)->response->data;

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
            $dataJson = '{

            	"Draw": ' . intval($draw) . ',
            	"recordsTotal": ' . $totalData . ',
            	"recordsFiltered": ' . $recordsFiltered . ',
            	"data": [';

            /*=============================================
            Recorremos la data
            =============================================*/
            foreach ($data as $key => $value) {

                if ($_GET["text"] == "flat") {

                    /* Validar si hay empresa asociada */
                    if ($value->id_empresa_venta != 0) {

                        $asignado = 'Asignado';
    
                    } else {
    
                        $asignado = 'No Asignado';
    
                    }

                    /* Validar el estado */
                    if ($value->estado_venta != 'pagado') {

                        $pagado = 'No Pagado';
    
                    } else {
    
                        $pagado = 'Pagado';
    
                    }

                } else {

                    /* Validar si hay empresa asociada */
                    if ($value->id_empresa_venta != 0) {

                        $asignado = "<span class='badge badge-info'>Asignado</span>";
    
                    } else {
    
                        $asignado = "<span class='badge badge-warning'>No Asignado</span>";
    
                    }

                    /* Validar el estado */
                    if ($value->estado_venta != 'pagado') {

                        $pagado = "<span class='badge badge-danger'>No Pagado</span>";
    
                    } else {
    
                        $pagado = "<span class='badge badge-success'>Pagado</span>";
    
                    }

                }

                $dataCompra = "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Método: " . TemplateController::capitalize($value->metodo_venta) . "</small>
                                    <small>Moneda: " . $value->moneda_venta . "</small>
                                    <small>Total: " . $value->monto_venta . "</small>
                                    <small>T. Cambio: " . $value->tipo_cambio_venta . "</small>
                                </div>
                            </div>";
                $dataCompra = TemplateController::htmlClean($dataCompra);

                $dataFirst = "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>ID: " . $value->trans_venta . "</small>
                                    <small>Usuario: " . $value->alias_usuario . "</small>
                                </div>
                            </div>";
                $dataFirst = TemplateController::htmlClean($dataFirst);
                
                $creado_plan = TemplateController::fechaEsShort($value->creado_plan);

                $dataJson .= '{

            		"id_venta":"' . ($start + $key + 1) . '",
            		"trans_venta":"' . $dataFirst . '",
                    "nombre_plan":"' . $value->nombre_plan . '",
            		"datos_compra":"' . $dataCompra . '",
                    "estado_venta":"' . $pagado . '",
            		"asignado_venta":"' . $asignado . '",
                    "creado_venta":"' . TemplateController::fechaEsShort($value->creado_venta) . '"

            	},';

            }

            $dataJson = substr($dataJson, 0, -1); // este substr quita el último caracter de la cadena, que es una coma, para impedir que rompa la tabla

            $dataJson .= ']}';

            echo $dataJson;
        }

    }

}

/*=============================================
Activar función DataTable
=============================================*/
$data = new DatatableController();
$data->data();