<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

class GetController
{

    /*=============================================
    Peticiones GET sin filtro
    =============================================*/
    public static function getData($table, $select, $orderBy, $orderMode, $startAt, $endAt)
    {

        $response = GetModel::getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);

    }

    /*=============================================
    Peticiones GET con filtro
    =============================================*/
    public static function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {

        $response = GetModel::getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);

    }

    /*=============================================
    Peticiones GET sin filtro entre tablas relacionadas
    =============================================*/
    public static function getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt)
    {

        $response = GetModel::getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);

    }

    /*=============================================
    Peticiones GET con filtro entre tablas relacionadas
    =============================================*/
    public static function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {

        $response = GetModel::getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);

    }

    /*=============================================
    Peticiones GET para el buscador sin relaciones
    =============================================*/
    public static function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {

        $response = GetModel::getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);

    }

    /*=============================================
    Peticiones GET para el buscador entre tablas relacionadas
    =============================================*/
    public static function getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {

        $response = GetModel::getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt);

        $return = new GetController();
        $return->fncResponse($response);

    }

    /*=============================================
    Peticiones GET para selección de rangos
    =============================================*/
    public static function getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {

        $response = GetModel::getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);

        $return = new GetController();
        $return->fncResponse($response);

    }

    /*=============================================
    Peticiones GET para selección de rangos con relaciones
    =============================================*/
    public static function getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {

        $response = GetModel::getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);

        $return = new GetController();
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
                    "total" => count($response),
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