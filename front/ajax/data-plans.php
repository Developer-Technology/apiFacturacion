<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

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
            $url = "planes?select=id_plan&linkTo=creado_plan&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"];
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

                    $linkTo = ["nombre_plan", "descripcion_plan", "creado_plan"];

                    $search = str_replace(" ", "_", $_POST['search']['value']);

                    foreach ($linkTo as $key => $value) {

                        $url = "planes?select=" . $select . "&linkTo=" . $value . "&search=" . $search . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length;

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
                $url = "planes?select=" . $select . "&linkTo=creado_plan&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length;

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

                    $actions = "";

                } else {

                    $actions = "<div class='d-flex flex-wrap justify-content-center gap-1'>
                                    <a href='/plans/edit/" . base64_encode($value->id_plan . "~" . $_GET["token"]) . "' class='btn btn-xs btn-icon btn-light waves-effect'>
                                        <i class='ti-pencil fs-5'></i>
                                    </a>
                                    <a href='javascript:void(0)' idItem='" . base64_encode($value->id_plan . "~" . $_GET["token"]) . "' table='planes' suffix='plan' deleteFile='no'' page='plans' class='btn btn-xs btn-icon btn-light waves-effect removeItem'>
                                        <i class='ti-trash fs-5'></i>
                                    </a>
                                </div>";

                    $actions = TemplateController::htmlClean($actions);
                }

                /*=============================================
                Obtenemos lo que contiene el plan
                =============================================*/
                $jsonPlan = $value->contiene_plan;
                $arrayPlan = json_decode($jsonPlan, true);
                foreach ($arrayPlan as $elementPlan) {

                    $totalCons = $elementPlan["consultas"];
                    $totalDocs = $elementPlan["documentos"];

                }
                $contienePlan = "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Consultas: " . TemplateController::capitalize($totalCons) . "</small>
                                    <small>Documentos: " . TemplateController::capitalize($totalDocs) . "</small>
                                </div>
                            </div>";
                $contienePlan = TemplateController::htmlClean($contienePlan);

                $nombre_plan = $value->nombre_plan;
                $descripcion_plan = $value->descripcion_plan;
                $precioPlan = $value->precio_plan;
                $creado_plan = TemplateController::fechaEsShort($value->creado_plan);

                $dataJson .= '{

            		"id_plan":"' . ($start + $key + 1) . '",
            		"nombre_plan":"' . TemplateController::capitalize($nombre_plan) . '",
                    "precio_plan":"' . $precioPlan . '",
            		"descripcion_plan":"' . TemplateController::capitalize($descripcion_plan) . '",
                    "contiene_plan":"' . $contienePlan . '",
            		"creado_plan":"' . $creado_plan . '",
            		"actions":"' . $actions . '"

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