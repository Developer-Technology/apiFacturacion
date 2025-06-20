<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

class PutController
{

    /*=============================================
    Peticion Put para editar datos
    =============================================*/
    public static function putData($table, $data, $id, $nameId)
    {

        $response = PutModel::putData($table, $data, $id, $nameId);

        $return = new PutController();
        $return->fncResponse($response);

    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {

        if (!empty($response)) {

            $json = array(
                "response" => array(
                    "success" => true,
                    "status" => 200,
                    "data" => $response
                )

            );

        } else {

            $json = array(
                "response" => array(
                    "success" => false,
                    "status" => 404,
                    "message" => "Not Found"
                )

            );

        }

        echo json_encode($json, http_response_code($json["response"]["status"]));

    }

}