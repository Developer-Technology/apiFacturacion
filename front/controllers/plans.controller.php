<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class PlansController
{

    /*=============================================
    Mostrar datos
    =============================================*/
    public static function dataPlan($value)
    {

        $url = "planes?select=*&linkTo=id_plan&equalTo=" . $value;
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
    public static function ctrListPlans($value)
    {

        $url = "plans/view";
        $method = "POST";
        $data = array(
            "id_plan" => $value,
        );
        $fields = json_encode($data);
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
    Crear datos
    =============================================*/
    public function create()
    {

        if (isset($_POST['name-plan'])) {

            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            $json[] = ['consultas' => $_POST['cons-plan'], 'documentos' => $_POST['docs-plan']];
            $content = json_encode($json);

            $url = "planes?token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
            $method = "POST";
            $fields = array(
                "nombre_plan" => $_POST["name-plan"],
                "descripcion_plan" => TemplateController::htmlClean($_POST["description-plan"]),
                "precio_plan" => $_POST["price-plan"],
                "contiene_plan" => $content,
                "creado_plan" => date('Y-m-d'),
            );
            $token = TemplateController::tokenSet();

            $response = CurlController::requestSunat($url, $method, $fields, $token);

            if ($response->response->success == true) {

                echo '<script>
                        fncFormatInputs();
                        matPreloader("off");
                        fncSweetAlert("close", "", "");
                        fncSweetAlert("success", "' . $response->response->data->comment . '", "/plans");
                    </script>';

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

    /*=============================================
    Editar datos
    =============================================*/
    public function edit($id)
    {

        if (isset($_POST["idPlan"])) {

            echo '<script>
					matPreloader("on");
					fncSweetAlert("loading", "Cargando...", "");
				</script>';

            if ($id == $_POST["idPlan"]) {

                $select = "*";

                $url = "planes?select=" . $select . "&linkTo=id_plan&equalTo=" . $id;
                $method = "GET";
                $fields = array();
                $token = TemplateController::tokenSet();

                $response = CurlController::requestSunat($url, $method, $fields, $token);

                if ($response->response->status == 200) {

                    // Decodificar el JSON
                    $json = $response->response->data[0]->contiene_plan;
                    $datosPlan = json_decode($json, true);

                    // Acceder y editar los valores del arreglo
                    $datosPlan[0]['consultas'] = $_POST["cons-plan"];
                    $datosPlan[0]['documentos'] = $_POST["docs-plan"];

                    // Codificar de nuevo el JSON
                    $json = json_encode($datosPlan);

                    /*=============================================
                    Agrupamos la información
                    =============================================*/
                    $dataUp = "nombre_plan=" . trim(TemplateController::capitalize($_POST["name-plan"])) . "&descripcion_plan=" . trim(TemplateController::htmlClean($_POST["description-plan"])) . "&precio_plan=" . $_POST["price-plan"] . "&contiene_plan=" . $json;

                    /*=============================================
                    Solicitud a la API
                    =============================================*/
                    $url = "planes?id=" . $id . "&nameId=id_plan&token=" . $_SESSION["user"]->token_usuario . "&table=usuarios&suffix=usuario";
                    $method = "PUT";
                    $fields = $dataUp;

                    $response = CurlController::requestSunat($url, $method, $fields, $token);

                    /*=============================================
                    Respuesta de la API
                    =============================================*/
                    if ($response->response->status == 200) {

                        echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncSweetAlert("success", "' . $response->response->data->comment . '", "/plans");
                            </script>';

                    } else {

                        echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncNotie(3, "Failed to edit registry");
                            </script>';

                    }

                } else {

                    echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Failed to edit registry");
                        </script>';

                }

            } else {

                echo '<script>
						fncFormatInputs();
						matPreloader("off");
						fncSweetAlert("close", "", "");
						fncNotie(3, "Failed to edit registry");
					</script>';

            }

        }

    }

}