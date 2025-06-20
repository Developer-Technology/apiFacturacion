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
            $url = "voideds?linkTo=creado_voided&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&filterTo=id_empresa_voided&inTo=" . $_GET["idTenant"];
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

                    $linkTo = ["serie_voided", "number_voided", "type_voided", "creado_voided"];

                    $search = str_replace(" ", "_", $_POST['search']['value']);

                    foreach ($linkTo as $key => $value) {

                        $url = "voideds?select=" . $select . "&linkTo=" . $value . "&search=" . $search . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length . "&filterTo=id_empresa_voided&inTo=" . $_GET["idTenant"];
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
                $url = "voideds?select=" . $select . "&linkTo=creado_voided&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length . "&filterTo=id_empresa_voided&inTo=" . $_GET["idTenant"];

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
                    if ($value->status_sunat_voided == 'aceptado') {

                        $estadoSunat = 'Aceptado';
    
                    } else if ($value->status_sunat_voided == 'rechazado') {
    
                        $estadoSunat = 'Rechazado';
    
                    } else if ($value->status_sunat_voided == 'reenviar') {

                        $estadoSunat = 'Reenviar';

                    } else {

                        $estadoSunat = 'Pendiente';

                    }

                    $actions = "";

                } else {

                    /* Validar el estado */
                    if ($value->status_sunat_voided == 'aceptado') {

                        $estadoSunat = "<span class='badge badge-success'>Aceptado</span>";
                        $cdr = "<li><a class='dropdown-item' href='".TemplateController::srcImg()."cdr/".$dataTenant->ruc_empresa."/R-".$dataTenant->ruc_empresa."-RA-".$value->serie_voided."-".$value->number_voided.".XML' target='_blank'>CDR</a></li>";
                        $enviar = "";
    
                    } else if ($value->status_sunat_voided == 'rechazado') {
    
                        $estadoSunat = "<span class='badge badge-danger'>Rechazado</span>";
                        $cdr = "";
                        $enviar ="";
    
                    } else if ($value->status_sunat_voided == 'reenviar') {

                        $estadoSunat = "<span class='badge badge-default'>Reenviar</span>";
                        $cdr = "";
                        $enviar = "<li><a class='dropdown-item pointer' onclick='sendSunatBaja(" . $value->id_voided . ")'>Enviar SUNAT</a></li>";

                    } else {

                        $estadoSunat = "<span class='badge badge-warning'>Pendiente</span>";
                        $cdr = "";
                        $enviar = "<li><a class='dropdown-item pointer' onclick='sendSunatBaja(" . $value->id_voided . ")'>Enviar SUNAT</a></li>";

                    }

                    $actions = "<div class='btn-group'>
                                    <button class='btn btn-outline-primary btn-sm dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                        Acciones
                                    </button>
                                    <ul class='dropdown-menu'>
                                        ".$enviar."
                                        <li><a class='dropdown-item' href='".TemplateController::srcImg()."xml/".$dataTenant->ruc_empresa."/unsigned/".$dataTenant->ruc_empresa."-RA-".$value->serie_voided."-".$value->number_voided.".XML' target='_blank'>XML Sin Firmar</a></li>
                                        <li><a class='dropdown-item' href='".TemplateController::srcImg()."xml/".$dataTenant->ruc_empresa."/signed/".$dataTenant->ruc_empresa."-RA-".$value->serie_voided."-".$value->number_voided.".XML' target='_blank'>XML Firmado</a></li>
                                        ".$cdr."
                                    </ul>
                                </div>";

			        $actions = TemplateController::htmlClean($actions);

                }

                $doc_voided = $value->type_voided . $value->serie_voided . $value->number_voided;

                $dataJson .= '{

            		"id_voided":"' . ($start + $key + 1) . '",
            		"doc_voided":"' . $doc_voided . '",
                    "emision_voided":"' . $value->emision_voided . '",
            		"estado_voided":"' . $estadoSunat . '",
                    "creado_voided":"' . TemplateController::fechaEsShort($value->creado_voided) . '",
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