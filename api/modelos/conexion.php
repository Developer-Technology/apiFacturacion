<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

class Conexion
{

    /*=============================================
	Información de la base de datos
	=============================================*/
	static public function infoDatabase(){

        /* Produccion */
		/*$infoDB = array(

			"database" => "apifact",
			"user" => "user",
			"pass" => "password"

		);*/

        /* Desarrollo */
        $infoDB = array(

			"database" => "apifact",
			"user" => "root",
			"pass" => ""

		);

		return $infoDB;

	}

    /*=============================================
	Conexión a la base de datos
	=============================================*/
	static public function conectar(){


		try{

			$link = new PDO(
				"mysql:host=localhost;dbname=".Conexion::infoDatabase()["database"],
				Conexion::infoDatabase()["user"], 
				Conexion::infoDatabase()["pass"]
			);

			$link->exec("set names utf8");

		}catch(PDOException $e){

			die("Error: ".$e->getMessage());

		}

		return $link;

	}

    public static function conectar_old()
    {

        $link = new PDO("mysql:host=localhost;dbname=apisunat",
            "root",
            "",
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        );

        return $link;

    }

    /*=============================================
    APIKEY
    =============================================*/
    public static function apikey()
    {

        return "0312b11cfe3df2ca85728026f8a81da8f53110f6e828030cce3c9a1a8dc6f1bf";

    }

    /*=============================================
    Validar existencia de una tabla en la bd
    =============================================*/

    public static function getColumnsData($table, $columns)
    {

        /*=============================================
        Traer el nombre de la base de datos
        =============================================*/

        $database = Conexion::infoDatabase()["database"];

        /*=============================================
        Traer todas las columnas de una tabla
        =============================================*/

        $validate = Conexion::conectar()
            ->query("SELECT COLUMN_NAME AS item FROM information_schema.columns WHERE table_schema = '$database' AND table_name = '$table'")
            ->fetchAll(PDO::FETCH_OBJ);

        /*=============================================
        Validamos existencia de la tabla
        =============================================*/

        if (empty($validate)) {

            return null;

        } else {

            /*=============================================
            Ajuste de selección de columnas globales
            =============================================*/

            if ($columns[0] == "*") {

                array_shift($columns);

            }

            /*=============================================
            Validamos existencia de columnas
            =============================================*/

            $sum = 0;

            foreach ($validate as $key => $value) {

                $sum += in_array($value->item, $columns);

            }

            return $sum == count($columns) ? $validate : null;

        }

    }

}
