<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

class PostModel
{

    /*=============================================
    Peticion POST para crear datos de forma dinámica
    =============================================*/
    public static function postData($table, $data)
    {

        $columns = "";
        $params = "";

        foreach ($data as $key => $value) {

            $columns .= $key . ",";

            $params .= ":" . $key . ",";

        }

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO $table ($columns) VALUES ($params)";

        $link = Conexion::conectar();
        $stmt = $link->prepare($sql);

        foreach ($data as $key => $value) {

            $stmt->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);

        }

        if ($stmt->execute()) {

            $response = array(

                "lastId" => $link->lastInsertId(),
                "comment" => "The process was successful",

            );

            return $response;

        } else {

            return $link->errorInfo();

        }

    }

}