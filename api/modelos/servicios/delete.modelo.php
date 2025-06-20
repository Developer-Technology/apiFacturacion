<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

require_once "get.modelo.php";

class DeleteModel
{

    /*=============================================
    Peticion Delete para eliminar datos de forma dinámica
    =============================================*/
    public static function deleteData($table, $id, $nameId)
    {

        /*=============================================
        Validar el ID
        =============================================*/
        $response = GetModel::getDataFilter($table, $nameId, $nameId, $id, null, null, null, null);

        if (empty($response)) {

            return null;

        }

        /*=============================================
        Eliminamos registros
        =============================================*/
        $sql = "DELETE FROM $table WHERE $nameId = :$nameId";

        $link = Conexion::conectar();
        $stmt = $link->prepare($sql);

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