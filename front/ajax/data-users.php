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
            $url = "usuarios?select=id_usuario&linkTo=creado_usuario&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"];
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

                    $linkTo = ["alias_usuario", "nombres_usuario", "email_usuario", "creado_usuario"];

                    $search = str_replace(" ", "_", $_POST['search']['value']);

                    foreach ($linkTo as $key => $value) {

                        $url = "usuarios?select=" . $select . "&linkTo=" . $value . "&search=" . $search . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length;

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
                $url = "usuarios?select=" . $select . "&linkTo=creado_usuario&between1=" . $_GET["between1"] . "&between2=" . $_GET["between2"] . "&orderBy=" . $orderBy . "&orderMode=" . $orderType . "&startAt=" . $start . "&endAt=" . $length;

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
                    Estado
                    =============================================*/
                    if ($value->estado_usuario == 1) {

                        $txtType = "Activo";

                    } else {

                        $txtType = "Inactivo";

                    }

                    /*=============================================
                    Datos usuario
                    =============================================*/
                    $dataUser = "<div class='d-flex flex-row'>
                                    <div class='d-flex flex-column'>
                                        <span>&nbsp;&nbsp;" . $value->alias_usuario . "</span>
                                        <small>&nbsp;&nbsp;" . $value->nombres_usuario . "</small>
                                    </div>
                                </div>";
                    $dataUser = TemplateController::htmlClean($dataUser);

                    /*=============================================
                    Verificado
                    =============================================*/
                    if ($value->verificado_usuario == 1) {

                        $txtVerificado = "Verificado";

                    } else {

                        $txtVerificado = "No Verificado";

                    }

                } else {

                    /*=============================================
                    Estado
                    =============================================*/
                    if ($value->id_usuario != 1) {

                        if ($value->estado_usuario == 1) {

                            $txtType = "<div class='custom-control custom-switch'><input type='checkbox' class='custom-control-input' id='switch" . $key . "' checked onchange='changeState(event, " . $value->id_usuario . ", `usuarios`, `usuario`)'><label class='custom-control-label pointer' for='switch" . $key . "'></label></div>";

                        } else {

                            $txtType = "<div class='custom-control custom-switch'><input type='checkbox' class='custom-control-input' id='switch" . $key . "' onchange='changeState(event," . $value->id_usuario . ", `usuarios`, `usuario`)'><label class='custom-control-label pointer' for='switch" . $key . "'></label></div>";

                        }

                    } else {

                        $txtType = "";

                    }

                    /*=============================================
                    Imagen
                    =============================================*/
                    if ($value->avatar_usuario == '') {

                        $imgUSer = "<img src='" . TemplateController::returnImgDefault('default.png', '') . "' class='thumb-sm rounded-circle mr-2' width='40'>";

                    } else {

                        $imgUSer = "<img src='" . TemplateController::returnImg('img/users', $value->avatar_usuario) . "' class='thumb-sm rounded-circle mr-2' width='40'>";

                    }

                    /*=============================================
                    Datos usuario
                    =============================================*/
                    $dataUser = "<div class='d-flex flex-row'>
                                    " . $imgUSer . "
                                    <div class='d-flex flex-column'>
                                        <span>&nbsp;&nbsp;" . $value->alias_usuario . "</span>
                                        <small>&nbsp;&nbsp;" . TemplateController::capitalize($value->nombres_usuario) . "</small>
                                    </div>
                                </div>";
                    $dataUser = TemplateController::htmlClean($dataUser);

                    /*=============================================
                    Vertificado
                    =============================================*/
                    if ($value->verificado_usuario == 1) {

                        $txtVerificado = "<span class='badge badge-success'>Verificado</span>";

                    } else {

                        $txtVerificado = "<span class='badge badge-danger'>No Verificado</span>";

                    }

                    if ($value->id_usuario != 1) {

                        $actions = "<div class='d-flex flex-wrap justify-content-center gap-1'>
                                        <a href='/users/edit/" . base64_encode($value->id_usuario . "~" . $_GET["token"]) . "' class='btn btn-xs btn-icon btn-light waves-effect'>
                                            <i class='ti-pencil fs-5'></i>
                                        </a>
                                        <a href='javascript:void(0)' idItem='" . base64_encode($value->id_usuario . "~" . $_GET["token"]) . "' table='usuarios' suffix='usuario' deleteFile='no'' page='users' class='btn btn-xs btn-icon btn-light waves-effect removeItem'>
                                            <i class='ti-trash fs-5'></i>
                                        </a>
                                    </div>";

                    $actions = TemplateController::htmlClean($actions);

                    } else {

                        $actions = "";

                    }

                }

                /*=============================================
                Rol
                =============================================*/
                if ($value->rol_usuario == 1) {

                    $txtRol = "Administrador";

                } else if ($value->rol_usuario == 2) {

                    $txtRol = "Usuario";

                } else if ($value->rol_usuario == 3) {

                    $txtRol = "Cliente";

                } else {

                    $txtRol = "---";

                }

                /*=============================================
                Datos contacto
                =============================================*/
                $dataContact = "<div class='d-flex flex-row'>
                                <div class='d-flex flex-column'>
                                    <small>Teléfono: " . $value->telefono_usuario . "</small>
                                    <small>Email: " . $value->email_usuario . "</small>
                                </div>
                            </div>";
                $dataContact = TemplateController::htmlClean($dataContact);

                /*=============================================
                Contamos empresas del usuario
                =============================================*/
                if ($value->id_empresa_usuario != null) {

                    $allEmp = json_decode($value->id_empresa_usuario, true);

                } else {

                    $allEmp = array();

                }

                $estado_usuario = $txtType;
                $datos_usuario = $dataUser;
                $contacto_usuario = $dataContact;
                $empresas_usuario = count($allEmp);
                $rol_usuario = $txtRol;
                $metodo_usuario = $value->metodo_usuario;
                $verificado_usuario = $txtVerificado;
                $creado_usuario = TemplateController::fechaEsShort($value->creado_usuario);

                $dataJson .= '{

            		"id_usuario":"' . ($start + $key + 1) . '",
            		"estado_usuario":"' . $estado_usuario . '",
            		"datos_usuario":"' . $datos_usuario . '",
                    "contacto_usuario":"' . $contacto_usuario . '",
                    "empresas_usuario":"' . $empresas_usuario . '",
                    "rol_usuario":"' . $rol_usuario . '",
                    "metodo_usuario":"' . $metodo_usuario . '",
                    "verificado_usuario":"' . $verificado_usuario . '",
            		"creado_usuario":"' . $creado_usuario . '",
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