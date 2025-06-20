<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

use Firebase\JWT\JWT;

class PostController
{

    /*=============================================
    Peticion POST para crear datos
    =============================================*/
    public static function postData($table, $data)
    {

        $response = PostModel::postData($table, $data);

        $return = new PostController();
        $return->fncResponse($response, null, null);

    }

    /*=============================================
    Peticion POST para registrar cliente
    =============================================*/
    public static function postRegisterC($table, $data, $suffix)
    {

        if (isset($data["name_" . $suffix]) && $data["name_" . $suffix] != null) {

            //$crypt = crypt($data["name_".$suffix], '$2a$07$azybxcags23425sdg23sdfhsd$');

            //$data["name_".$suffix] = $crypt;

            $response = PostModel::postData($table, $data);

            $return = new PostController();
            $return->fncResponse($response, null, $suffix);

        } else {

            /*=============================================
            Registro de usuarios desde APP externas
            =============================================*/
            $response = PostModel::postData($table, $data);

            if (isset($response["comment"]) && $response["comment"] == "The process was successful") {

                /*=============================================
                Validar que el usuario exista en BD
                =============================================*/
                $response = GetModel::getDataFilter($table, "*", "name_" . $suffix, $data["name_" . $suffix], null, null, null, null);

                if (!empty($response)) {

                    $token = Conexion::jwt($response[0]->{"id_" . $suffix}, $response[0]->{"name_" . $suffix});

                    $jwt = JWT::encode($token, "dfhsdfg34dfchs4xgsrsdry46");

                    /*=============================================
                    Actualizamos la base de datos con el Token del usuario
                    =============================================*/
                    $data = array(

                        "token_" . $suffix => $jwt,
                        "token_exp_" . $suffix => $token["exp"],

                    );

                    $update = PutModel::putData($table, $data, $response[0]->{"id_" . $suffix}, "id_" . $suffix);

                    if (isset($update["comment"]) && $update["comment"] == "The process was successful") {

                        $response[0]->{"token_" . $suffix} = $jwt;
                        $response[0]->{"token_exp_" . $suffix} = $token["exp"];

                        $return = new PostController();
                        $return->fncResponse($response, null, $suffix);

                    }

                }

            }

        }

    }

    /*=============================================
    Peticion POST para registrar usuario
    =============================================*/
    public static function postRegister($table, $data, $suffix)
    {

        /*=============================================
        Validar que el usuario exista en BD
        =============================================*/
        $response = GetModel::getDataFilter($table, "*", "email_" . $suffix, $data["email_" . $suffix], null, null, null, null);

        if (empty($response)) {

            if (isset($data["clave_" . $suffix]) && $data["clave_" . $suffix] != null) {

                /* Encriptamos la contraseña */
                $crypt = crypt($data["clave_" . $suffix], '$2a$07$azybxcags23425sdg23sdfhsd$');

                $data["clave_" . $suffix] = $crypt;

                $response = PostModel::postData($table, $data);

                /*=============================================
                Generamos el avatar
                =============================================*/
                $username = strtolower(explode("@", $data["email_" . $suffix])[0]);
                $avatar = $_SERVER['DOCUMENT_ROOT'] . '/documents/img/default/default.png';
                $upload = FilesController::fileData($avatar, "Avatar", "documents/img/users", $username, "base64", 60, 60);

                /*=============================================
                Recogemos los datos del sistema
                =============================================*/
                $responseSet = GetModel::getDataFilter("configuraciones", "*", "id_configuracion", 1, null, null, null, null);

                $nameSystem = $responseSet[0]->nombre_sistema_configuracion;

                /*=============================================
                Generamos el correo
                =============================================*/
                $name = $data["email_" . $suffix];
                $subject = "Verifica tu cuenta";
                $email = $data["email_" . $suffix];
                $message = "Debemos verificar tu cuenta para que puedas acceder a <b>" . $nameSystem . "</b>";
                $text = "Haz clic en este enlace para verificar tu cuenta";
                /* Ruta donde se encuentra el sistema */
                $url = ControladorRutas::path() . 'verify/' . base64_encode($data["email_" . $suffix] . '~' . date('Y-m-d H:i:s') . '~c5LTA6WPbMwHhEabYu77nN9cn4VcMj' . '~' . uniqid());

                $sendEmail = ControladorRutas::sendEmail($name, $subject, $data["email_" . $suffix], $message, $text, $url, null);

                if ($sendEmail == "ok") {

                    $json = array(

                        "response" => array(
                            "success" => true,
                            "status" => 200,
                            "data" => array(
                                "comment" => "The process was successful",
                                "avatar" => $upload,
                            ),
                        ),

                    );

                    /*=============================================
                    Imprimimos la respuesta
                    =============================================*/
                    echo json_encode($json, http_response_code($json["response"]["status"]));

                } else {

                    $response = null;
                    $return = new PostController();
                    $return->fncResponse($response, "Error sending mail", $suffix);

                }

            } else {

                /*=============================================
                Registro de usuarios desde APP externas
                =============================================*/
                $response = PostModel::postData($table, $data);

                if (isset($response["comment"]) && $response["comment"] == "The process was successful") {

                    /*=============================================
                    Validar que el usuario exista en BD
                    =============================================*/
                    $response = GetModel::getDataFilter($table, "*", "email_" . $suffix, $data["email_" . $suffix], null, null, null, null);

                    if (!empty($response)) {

                        $token = Conexion::jwt($response[0]->{"id_" . $suffix}, $response[0]->{"email_" . $suffix});

                        $jwt = JWT::encode($token, "dfhsdfg34dfchs4xgsrsdry46");

                        /*=============================================
                        Actualizamos la base de datos con el Token del usuario
                        =============================================*/
                        $data = array(

                            "token_" . $suffix => $jwt,
                            "token_exp_" . $suffix => $token["exp"],

                        );

                        $update = PutModel::putData($table, $data, $response[0]->{"id_" . $suffix}, "id_" . $suffix);

                        if (isset($update["comment"]) && $update["comment"] == "The process was successful") {

                            $response[0]->{"token_" . $suffix} = $jwt;
                            $response[0]->{"token_exp_" . $suffix} = $token["exp"];

                            $return = new PostController();
                            $return->fncResponse($response, null, $suffix);

                        }

                    }

                }

            }

        } else {

            $response = null;
            $return = new PostController();
            $return->fncResponse($response, "The email already exists in our database", $suffix);

        }

    }

    /*=============================================
    Peticion POST para login de usuario
    =============================================*/
    public static function postLogin($table, $data, $suffix)
    {

        /*=============================================
        Validar que el usuario exista en BD
        =============================================*/
        $response = GetModel::getDataFilter($table, "*", "email_" . $suffix, $data["email_" . $suffix], null, null, null, null);

        if (!empty($response)) {

            if ($response[0]->{"clave_" . $suffix} != null) {

                /*=============================================
                Encriptamos la contraseña
                =============================================*/
                $crypt = crypt($data["clave_" . $suffix], '$2a$07$azybxcags23425sdg23sdfhsd$');

                if ($response[0]->{"clave_" . $suffix} == $crypt) {

                    $token = ControladorRutas::jwt($response[0]->{"id_" . $suffix}, $response[0]->{"email_" . $suffix});

                    $jwt = JWT::encode($token, "dfhsdfg34dfchs4xgsrsdry46");

                    /*=============================================
                    Actualizamos la base de datos con el Token del usuario
                    =============================================*/
                    $data = array(

                        "token_" . $suffix => $jwt,
                        "token_exp_" . $suffix => $token["exp"],

                    );

                    $update = PutModel::putData($table, $data, $response[0]->{"id_" . $suffix}, "id_" . $suffix);

                    if (isset($update["comment"]) && $update["comment"] == "The process was successful") {

                        $response[0]->{"token_" . $suffix} = $jwt;
                        $response[0]->{"token_exp_" . $suffix} = $token["exp"];

                        $return = new PostController();
                        $return->fncResponse($response, null, $suffix);

                    }

                } else {

                    $response = null;
                    $return = new PostController();
                    $return->fncResponse($response, "Incorrect password", $suffix);

                }

            } else {

                /*=============================================
                Actualizamos el token para usuarios logueados desde app externas
                =============================================*/
                $token = Conexion::jwt($response[0]->{"id_" . $suffix}, $response[0]->{"email_" . $suffix});

                $jwt = JWT::encode($token, "dfhsdfg34dfchs4xgsrsdry46");

                $data = array(

                    "token_" . $suffix => $jwt,
                    "token_exp_" . $suffix => $token["exp"],

                );

                $update = PutModel::putData($table, $data, $response[0]->{"id_" . $suffix}, "id_" . $suffix);

                if (isset($update["comment"]) && $update["comment"] == "The process was successful") {

                    $response[0]->{"token_" . $suffix} = $jwt;
                    $response[0]->{"token_exp_" . $suffix} = $token["exp"];

                    $return = new PostController();
                    $return->fncResponse($response, null, $suffix);

                }

            }

        } else {

            $response = null;
            $return = new PostController();
            $return->fncResponse($response, "Wrong email", $suffix);

        }

    }

    /*=============================================
    Peticion POST para forgot password
    =============================================*/
    public static function postForgot($table, $data, $suffix)
    {

        /*=============================================
        Validar que el usuario exista en BD
        =============================================*/
        $response = GetModel::getDataFilter($table, "*", "email_" . $suffix, $data["email_" . $suffix], null, null, null, null);

        if (!empty($response)) {

            /*=============================================
            Generamos el la nueva clave del usuario
            =============================================*/
            $generatePass = ControladorRutas::generar_clave(16);

            /*=============================================
            Actualizamos la base de datos con la nueva contraseña
            =============================================*/
            $newPass = crypt($generatePass, '$2a$07$azybxcags23425sdg23sdfhsd$');

            $dataPass = array(

                "clave_" . $suffix => $newPass,

            );

            $update = PutModel::putData($table, $dataPass, $response[0]->{"id_" . $suffix}, "id_" . $suffix);

            if (isset($update["comment"]) && $update["comment"] == "The process was successful") {

                /*=============================================
                Recogemos los datos del sistema
                =============================================*/
                $responseSet = GetModel::getDataFilter("configuraciones", "*", "id_configuracion", 1, null, null, null, null);

                $nameSystem = $responseSet[0]->nombre_sistema_configuracion;
                $nameEmp = $responseSet[0]->nombre_empresa_configuracion;
                $web = $responseSet[0]->web_empresa_configuracion;

                /*=============================================
                Generamos el correo
                =============================================*/
                $name = $data["email_" . $suffix];
                $subject = "Nueva Clave De Acceso";
                $email = $data["email_" . $suffix];
                $message = "Hemos generado una nueva contraseña para que accedas a <b>" . $nameSystem . "</b>, puedes cambiarla desde tu perfil.<br>Tu nueva contraseña es: <b>" . $generatePass . "</b>";
                $text = "";
                /* Ruta donde se encuentra el sistema */
                $url = "";

                $sendEmail = ControladorRutas::sendEmail($name, $subject, $data["email_" . $suffix], $message, $text, $url);

                if ($sendEmail == "ok") {

                    $json = array(

                        "response" => array(
                            "success" => true,
                            "status" => 200,
                            "data" => array(
                                "comment" => $update["comment"],
                                "newPass" => $generatePass,
                            ),
                        ),

                    );

                    /*=============================================
                    Imprimimos la respuesta
                    =============================================*/
                    echo json_encode($json, http_response_code($json["response"]["status"]));

                } else {

                    $response = null;
                    $return = new PostController();
                    $return->fncResponse($response, "Error sending mail", $suffix);

                }

            }

        } else {

            $response = null;
            $return = new PostController();
            $return->fncResponse($response, "The email does not exist in our database", $suffix);

        }

    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response, $error, $suffix)
    {

        if (!empty($response)) {

            /*=============================================
            Quitamos la contraseña de la respuesta
            =============================================*/
            if (isset($response[0]->{"clave_" . $suffix})) {

                unset($response[0]->{"clave_" . $suffix});

            }

            $json = array(

                "response" => array(
                    "success" => true,
                    "status" => 200,
                    "data" => $response,
                ),

            );

        } else {

            if ($error != null) {

                $json = array(

                    "response" => array(
                        "success" => false,
                        "status" => 400,
                        "message" => $error,
                    ),

                );

            } else {

                $json = array(

                    "response" => array(
                        "success" => false,
                        "status" => 404,
                        "message" => "Not Found",
                    ),

                );
            }

        }

        echo json_encode($json, http_response_code($json["response"]["status"]));

    }

}
