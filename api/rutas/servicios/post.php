<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

if (isset($_POST)) {

    /*=============================================
    Separar propiedades en un arreglo
    =============================================*/
    $columns = array();

    foreach (array_keys($_POST) as $key => $value) {

        array_push($columns, $value);

    }

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

    $response = new PostController();

    /*=============================================
    Peticion POST para registrar cliente
    =============================================*/
    if (isset($_GET["registerC"]) && $_GET["registerC"] == true) {

        $suffix = $_GET["suffix"] ?? "client";

        $response->postRegisterC($table, $_POST, $suffix);

    /*=============================================
    Peticion POST para registrar usuario
    =============================================*/
    } else if (isset($_GET["register"]) && $_GET["register"] == true) {

        $suffix = $_GET["suffix"] ?? "usuario";

        $response->postRegister($table, $_POST, $suffix);

    /*=============================================
    Peticion POST para login de usuario
    =============================================*/
    } else if (isset($_GET["login"]) && $_GET["login"] == true) {

        $suffix = $_GET["suffix"] ?? "usuario";

        $response->postLogin($table, $_POST, $suffix);

    /*=============================================
    Peticion POST para forgot password
    =============================================*/
    } else if (isset($_GET["forgot"]) && $_GET["forgot"] == true) {

        $suffix = $_GET["suffix"] ?? "usuario";

        $response->postForgot($table, $_POST, $suffix);

    } else {

        if (isset($_GET["token"])) {

            /*=============================================
            Peticion POST para usuarios no autorizados
            =============================================*/
            if ($_GET["token"] == "no-token") {

                $response->postData($table, $_POST);

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
                $response->postData($table, $_POST);

            /*=============================================
            Peticion POST para usuarios autorizados
            =============================================*/
            } else {

                $tableToken = $_GET["table"] ?? "usuarios";
                $suffix = $_GET["suffix"] ?? "usuario";

                $validate = ControladorRutas::tokenValidate($_GET["token"], $tableToken, $suffix);

                /*=============================================
                Solicitamos respuesta del controlador para crear datos en cualquier tabla
                =============================================*/
                if ($validate == "ok") {

                    $response->postData($table, $_POST);

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

}