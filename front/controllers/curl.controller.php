<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class CurlController
{

    /*=============================================
    Ruta API
    =============================================*/
    public static function api()
    {
    
        /* Produccion */
        //return "https://api.chanamoth.online/";

        /* Desarrollo */
        return "http://api.apifact.local/";
        
    }

    /*=============================================
    Peticiones a la API SUNAT
    =============================================*/
    public static function requestSunat($url, $method, $fields, $token)
    {

        $curl = curl_init();

        if ($token != '') {

            $header = array(
                'Authorization: Bearer ' . $token,
            );

        } else {

            $header = array();

        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => CurlController::api() . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        return $response;

    }

    /*=============================================
    Peticiones supabase
    =============================================*/
    public static function supabase($url, $method, $fields, $token) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_HTTPHEADER => array(
            'apikey: ' . $token,
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        return $response;

    }

}