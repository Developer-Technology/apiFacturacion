<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

if (isset($_GET["id"]) && isset($_GET["nameId"])) {

    $columns = array($_GET["nameId"]);

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

    /*=============================================
    Peticion DELETE para usuarios autorizados
    =============================================*/
    if (isset($_GET["token"])) {

        $tableToken = $_GET["table"] ?? "usuarios";
        $suffix = $_GET["suffix"] ?? "usuario";

        $validate = ControladorRutas::tokenValidate($_GET["token"], $tableToken, $suffix);

        /*=============================================
        Solicitamos respuesta del controlador para eliminar datos en cualquier tabla
        =============================================*/
        if ($validate == "ok") {

            $response = new DeleteController();
            $response->deleteData($table, $_GET["id"], $_GET["nameId"]);

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
                    "message" => "Error: The user is not authorized"
                )
            );

            echo json_encode($json, http_response_code($json["response"]["status"]));

            return;

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