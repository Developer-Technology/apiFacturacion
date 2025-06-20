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
            $url = "transportists?linkTo=creado_transportist&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&filterTo=id_empresa_transportist&inTo=" . $_GET["idTenant"];
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

                    $linkTo = ["serie_transportist", "number_transportist", "observation_transportist", "creado_transportist"];

                    $search = str_replace(" ", "_", $_POST['search']['value']);

                    foreach ($linkTo as $key => $value) {

                        $url = "transportists?select=" . $select . "&linkTo=" . $value . "&search=" . $search . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length . "&filterTo=id_empresa_transportist&inTo=" . $_GET["idTenant"];
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
                $url = "transportists?select=" . $select . "&linkTo=creado_transportist&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length . "&filterTo=id_empresa_transportist&inTo=" . $_GET["idTenant"];

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
            Obtenemos la data de la empresa
            =============================================*/
            $urlTenant = "empresas?select=*&linkTo=id_empresa&equalTo=" . $_GET["idTenant"];
            $methodTenant = "GET";
            $fieldsTenant = array();
            $dataTenant = CurlController::requestSunat($urlTenant, $methodTenant, $fieldsTenant, $token)->response->data[0];

            /*=============================================
            Recorremos la data
            =============================================*/
            foreach ($data as $key => $value) {

                if ($_GET["text"] == "flat") {

                    /* Validar el estado */
                    if ($value->status_sunat_transportist == 'aceptado') {

                        $estadoSunat = 'Aceptado';
    
                    } else if ($value->status_sunat_transportist == 'rechazado') {
    
                        $estadoSunat = 'Rechazado';
    
                    } else if ($value->status_sunat_transportist == 'reenviar') {

                        $estadoSunat = 'Reenviar';

                    } else {

                        $estadoSunat = 'Pendiente';

                    }

                    $actions = "";

                } else {

                    /* Validar el estado */
                    if ($value->status_sunat_transportist == 'aceptado') {

                        $estadoSunat = "<span class='badge badge-success'>Aceptado</span>";
                        $cdr = "<li><a class='dropdown-item' href='".TemplateController::srcImg()."cdr/".$dataTenant->ruc_empresa."/R-".$dataTenant->ruc_empresa."-31-".$value->serie_transportist."-".$value->number_transportist.".XML' target='_blank'>CDR</a></li>";
                        $enviar = "";
    
                    } else if ($value->status_sunat_transportist == 'rechazado') {
    
                        $estadoSunat = "<span class='badge badge-danger'>Rechazado</span>";
                        $cdr = "";
                        $enviar ="";
    
                    } else if ($value->status_sunat_transportist == 'reenviar') {

                        $estadoSunat = "<span class='badge badge-default'>Reenviar</span>";
                        $cdr = "";
                        $enviar = "<li><a class='dropdown-item pointer' onclick='sendSunatTra(" . $value->id_transportist . ")'>Enviar SUNAT</a></li>";

                    } else {

                        $estadoSunat = "<span class='badge badge-warning'>Pendiente</span>";
                        $cdr = "";
                        $enviar = "<li><a class='dropdown-item pointer' onclick='sendSunatTra(" . $value->id_transportist . ")'>Enviar SUNAT</a></li>";

                    }

                    $actions = "<div class='btn-group'>
                                    <button class='btn btn-outline-primary btn-sm dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                        Acciones
                                    </button>
                                    <ul class='dropdown-menu'>
                                        ".$enviar."
                                        <li><a class='dropdown-item' href='".TemplateController::srcImg()."pdf/".$dataTenant->ruc_empresa."/despatch/a4/".$dataTenant->ruc_empresa."-31-".$value->serie_transportist."-".$value->number_transportist.".pdf' target='_blank'>PDF A4</a></li>
                                        <li><a class='dropdown-item' href='".TemplateController::srcImg()."xml/".$dataTenant->ruc_empresa."/unsigned/".$dataTenant->ruc_empresa."-31-".$value->serie_transportist."-".$value->number_transportist.".XML' target='_blank'>XML Sin Firmar</a></li>
                                        <li><a class='dropdown-item' href='".TemplateController::srcImg()."xml/".$dataTenant->ruc_empresa."/signed/".$dataTenant->ruc_empresa."-31-".$value->serie_transportist."-".$value->number_transportist.".XML' target='_blank'>XML Firmado</a></li>
                                        ".$cdr."
                                    </ul>
                                </div>";

			        $actions = TemplateController::htmlClean($actions);

                }

                /*=============================================
                Obtenemos la data destinatario
                =============================================*/
                $jsonDest = $value->recipient_transportist;
                $arrayDest = json_decode($jsonDest, true);
                foreach ($arrayDest as $elementDest) {

                    $nroDocDest = $elementDest["numDoc"];
                    $tipoDocDest = $elementDest["tipoDoc"];
                    $nombreDest = $elementDest["nombreRazon"];

                }
                $dataDest = "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Tipo Doc: " . $tipoDocDest . "</small>
                                    <small>Nro Doc: " . $nroDocDest . "</small>
                                    <small>Nombre: " . TemplateController::capitalize($nombreDest) . "</small>
                                </div>
                            </div>";
                $dataDest = TemplateController::htmlClean($dataDest);

                $doc_transportist = $value->serie_transportist . ' - ' . $value->number_transportist;

                $dataJson .= '{

            		"id_transportist":"' . ($start + $key + 1) . '",
            		"doc_transportist":"' . $doc_transportist . '",
                    "emision_transportist":"' . $value->emision_transportist . '",
            		"dest_transportist":"' . $dataDest . '",
            		"estado_transportist":"' . $estadoSunat . '",
                    "creado_transportist":"' . TemplateController::fechaEsShort($value->creado_transportist) . '",
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