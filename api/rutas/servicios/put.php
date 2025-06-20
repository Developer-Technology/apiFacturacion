<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

if (isset($_GET["id"]) && isset($_GET["nameId"])) {

    /*=============================================
    Capturamos los datos del formulario
    =============================================*/
    $data = array();
    parse_str(file_get_contents('php://input'), $data);

    /*=============================================
    Separar propiedades en un arreglo
    =============================================*/
    $columns = array();

    foreach (array_keys($data) as $key => $value) {

        array_push($columns, $value);

    }

    array_push($columns, $_GET["nameId"]);

    $columns = array_unique($columns);

    /*=============================================
    Validar la tabla y las columnas
    =============================================*/
    if (empty(Conexion::getColumnsData($table, $columns))) {

        $json = array(
            "response" => array(
                "success" => false,
                "status" => 400,
                "message" => "Error: Form fields do not match the database"
            )
        );

        echo json_encode($json, http_response_code($json["response"]["status"]));

        return;

    }

    if (isset($_GET["token"])) {

        /*=============================================
        Peticion POST para usuarios no autorizados
        =============================================*/
        if ($_GET["token"] == "no-token") {

            $response = new PutController();
            $response->putData($table, $data, $_GET["id"], $_GET["nameId"]);

            return;

        }

        if ($_GET["token"] == "no" && isset($_GET["except"])) {

            /*=============================================
            Validar la tabla y las columnas
            =============================================*/
            $columns = array($_GET["except"]);

            if (empty(Conexion::getColumnsData($table, $columns))) {

                $json = array(
                    "response" => array(
                        "success" => false,
                        "status" => 400,
                        "message" => "Error: Form fields do not match the database"
                    )
                );

                echo json_encode($json, http_response_code($json["response"]["status"]));

                return;

            }

            /*=============================================
            Solicitamos respuesta del controlador para crear datos en cualquier tabla
            =============================================*/
            $response = new PutController();
            $response->putData($table, $data, $_GET["id"], $_GET["nameId"]);

        /*=============================================
        Peticion PUT para usuarios autorizados
        =============================================*/
        } else {

            $tableToken = $_GET["table"] ?? "users";
            $suffix = $_GET["suffix"] ?? "user";

            $validate = ControladorRutas::tokenValidate($_GET["token"], $tableToken, $suffix);

            /*=============================================
            Solicitamos respuesta del controlador para editar datos en cualquier tabla
            =============================================*/
            if ($validate == "ok") {

                $response = new PutController();
                $response->putData($table, $data, $_GET["id"], $_GET["nameId"]);

            }

            /*=============================================
            Error cuando el token ha expirado
            =============================================*/
            if ($validate == "expired") {

                $json = array(
                    "response" => array(
                        "success" => false,
                        "status" => 303,
                        "message" => "Error: token has expired"
                    )
                );

                echo json_encode($json, http_response_code($json["response"]["status"]));

                return;

            }

            /*=============================================
            Error cuando el token no coincide en BD
            =============================================*/
            if ($validate == "no-auth") {

                $json = array(
                    "response" => array(
                        "success" => false,
                        "status" => 400,
                        "message" => "Error: user is not authorized"
                    )
                );

                echo json_encode($json, http_response_code($json["response"]["status"]));

                return;

            }

        }

    /*=============================================
    Error cuando no envía token
    =============================================*/
    } else {

        $json = array(
            "response" => array(
                "success" => false,
                "status" => 400,
                "message" => "Error: authorization required"
            )
        );

        echo json_encode($json, http_response_code($json["response"]["status"]));

        return;

    }

}