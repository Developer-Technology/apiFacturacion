<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

require_once "get.modelo.php";

class PutModel
{

    /*=============================================
    Peticion Put para editar datos de forma dinámica
    =============================================*/
    public static function putData($table, $data, $id, $nameId)
    {

        /*=============================================
        Validar el ID
        =============================================*/
        $response = GetModel::getDataFilter($table, $nameId, $nameId, $id, null, null, null, null);

        if (empty($response)) {

            return null;

        }

        /*=============================================
        Actualizamos registros
        =============================================*/
        $set = "";

        foreach ($data as $key => $value) {

            $set .= $key . " = :" . $key . ",";

        }

        $set = substr($set, 0, -1);

        $sql = "UPDATE $table SET $set WHERE $nameId = :$nameId";

        $link = Conexion::conectar();
        $stmt = $link->prepare($sql);

        foreach ($data as $key => $value) {

            $stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);

        }

        $stmt->bindParam(":" . $nameId, $id, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $response = array(

                "comment" => "The process was successful",
            );

            return $response;

        } else {

            return $link->errorInfo();

        }

    }

}