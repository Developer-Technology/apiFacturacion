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
            $url = "relations?rel=empresas,planes&type=empresa,plan&select=id_empresa&linkTo=creado_empresa&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"];
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

                    $linkTo = ["ruc_empresa", "razon_social_empresa", "nombre_comercial_empresa", "creado_empresa"];

                    $search = str_replace(" ", "_", $_POST['search']['value']);

                    foreach ($linkTo as $key => $value) {

                        $url = "relations?rel=empresas,planes&type=empresa,plan&select=" . $select . "&linkTo=" . $value . "&search=" . $search . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length;

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
                $url = "relations?rel=empresas,planes&type=empresa,plan&select=" . $select . "&linkTo=creado_empresa&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length;

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

                    /*=============================================
                    Logo
                    =============================================*/
                    if($value->logo_empresa == '') {

                        $imgTenant = "---";

                    } else {

                        $imgTenant = $value->logo_empresa;

                    }

                    /*=============================================
                    Entorno
                    =============================================*/
                    $dataFase = TemplateController::capitalize($value->fase_empresa);

                    /*=============================================
                    Estado
                    =============================================*/
                    if ($value->estado_empresa == 1) {

                        $txtType = "Activo";
    
                    } else {
    
                        $txtType = "Inactivo";
    
                    }

                    /*=============================================
                    Datos empresa
                    =============================================*/
                    $dataTenant = "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <span>" . $value->ruc_empresa . "</span>
                                        <small>" . $value->razon_social_empresa . "</small>
                                    </div>
                                </div>";
                    $dataTenant = TemplateController::htmlClean($dataTenant);

                } else {

                    /*=============================================
                    Logo
                    =============================================*/
                    if($value->logo_empresa == '') {

                        $imgTenant = "<img src='".TemplateController::returnImgDefault('logo.png', '')."' style='height:30px'>";

                    } else {

                        $imgTenant = "<img src='".TemplateController::returnImg('logo/'.$value->ruc_empresa, $value->logo_empresa)."' style='height:30px'>";

                    }

                    /*=============================================
                    Entorno
                    =============================================*/
                    if($value->fase_empresa == 'beta') {

                        $badge = 'warning';

                    } else {

                        $badge = 'success';

                    }

                    /*=============================================
                    Entorno
                    =============================================*/
                    $dataFase = "<span class='badge badge-" . $badge . "'>" . TemplateController::capitalize($value->fase_empresa) . "</span>";
                    $dataFase = TemplateController::htmlClean($dataFase);

                    /*=============================================
                    State
                    =============================================*/
                    if ($value->estado_empresa == 1) {

                        $txtType = "<div class='custom-control custom-switch'><input type='checkbox' class='custom-control-input' id='switch" . $key . "' checked onchange='changeState(event," . $value->id_empresa . ", `empresas`, `empresa`)'><label class='custom-control-label pointer' for='switch" . $key . "'></label></div>";

                    } else {

                        $txtType = "<div class='custom-control custom-switch'><input type='checkbox' class='custom-control-input' id='switch" . $key . "' onchange='changeState(event," . $value->id_empresa . ", `empresas`, `empresa`)'><label class='custom-control-label pointer' for='switch" . $key . "'></label></div>";
                    
                    }

                    $actions = "<div class='btn-group'>
                                    <button type='button' class='btn btn-outline-primary dropdown-toggle btn-xs waves-effect' data-bs-toggle='dropdown' aria-expanded='false'>Acciones</button>
                                    <ul class='dropdown-menu'>
                                        <li><a class='dropdown-item' href='/redirect/" . base64_encode($value->id_empresa . '~' . $_GET["token"]) . "'>Panel Empresa</a></li>
                                        <li><a class='dropdown-item' href='/tenants/edit/" . base64_encode($value->id_empresa . "~" . $_GET["token"]) . "'>Editar Registro</a></li>
                                        <li><a class='dropdown-item removeItem' idItem='" . base64_encode($value->id_empresa . "~" . $_GET["token"]) . "' table='empresas' suffix='empresa' deleteFile='no'' page='tenants'>Eliminar Registro</a></li>
                                    </ul>
                                </div>";

                    $actions = TemplateController::htmlClean($actions);

                    /*=============================================
                    Datos empresa
                    =============================================*/
                    $dataTenant = "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <span><a href='/redirect/" . base64_encode($value->id_empresa . '~' . $_GET["token"]) . "'>" . $value->ruc_empresa . "</a></span>
                                        <small>" . $value->razon_social_empresa . "</small>
                                    </div>
                                </div>";
                    $dataTenant = TemplateController::htmlClean($dataTenant);

                }

                /*=============================================
                Datos consumo
                =============================================*/
                if($value->consumo_empresa != '[]') {

                    foreach (json_decode($value->consumo_empresa) as $keyT => $cons) {

                        $realCons = $cons->consultas;
                        $realDocs = $cons->documentos;
                
                    }

                } else {

                    $realCons = 0;
                    $realDocs = 0;

                }

                /*=============================================
                Datos fecha
                =============================================*/
                if($value->proxima_facturacion_empresa == '0000-00-00') {

                    $proxFact = '----';

                } else {

                    $proxFact = TemplateController::fechaEsShort($value->proxima_facturacion_empresa);

                }

                $dataFecha = "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Creado: " . TemplateController::fechaEsShort($value->creado_empresa) . "</small>
                                    <small>Prox. Fact.: " . $proxFact . "</small>
                                </div>
                            </div>";
                $dataFecha = TemplateController::htmlClean($dataFecha);
                
                /*=============================================
                Datos consumo
                =============================================*/
                $dataConsu = "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Consultas: " . $realCons . "</small>
                                    <small>Documentos: " . $realDocs . "</small>
                                </div>
                            </div>";
                $dataConsu = TemplateController::htmlClean($dataConsu);

                $estado_empresa = $txtType;
                $logo_empresa = $imgTenant;
                $ruc_empresa = $dataTenant;
                $fase_empresa = $dataFase;
                $id_plan_empresa = $value->nombre_plan;
                $consultas_empresa = $dataConsu;
                //$creado_empresa = TemplateController::fechaEsShort($value->creado_empresa);

                $dataJson .= '{

            		"id_empresa":"' . ($start + $key + 1) . '",
            		"estado_empresa":"' . $estado_empresa . '",
                    "logo_empresa":"' . $logo_empresa . '",
            		"ruc_empresa":"' . $ruc_empresa . '",
                    "fase_empresa":"' . $fase_empresa . '",
                    "plan_empresa":"' . $id_plan_empresa . '",
                    "consumo_plan":"' . $consultas_empresa . '",
            		"creado_empresa":"' . $dataFecha . '",
                    "acciones":"' . $actions . '"
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