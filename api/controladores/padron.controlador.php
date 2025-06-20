<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

/*=============================================
Incluimos la libreria
=============================================*/
use Peru\Jne\DniFactory;
use Peru\Sunat\RucFactory;

class ControladorPadron
{

    /*=============================================
    Consulta RUC
    =============================================*/
    public static function consultRuc($value)
    {

        $ruc = $value;

        $factory = new RucFactory();
        $cs = $factory->create();

        $company = $cs->get($ruc);

        if (!$company) {

            $json = array(

                'response' => array(
                    'success' => false,
                    'status' => 404,
                    'message' => 'Not Found.',
                ),

            );

            echo json_encode($json, http_response_code($json["response"]["status"]));

            return;
        }

        /*=============================================
        Obtenemos el ubigeo
        =============================================*/
        $ubigeos = file_get_contents("documents/json/ubigeos.json");
        $ubigeos = json_decode($ubigeos, true);

        $valUbigeo = null;

        foreach ($ubigeos as $key => $value) {

            if ($value["distrito"] == $company->distrito) {

                $valUbigeo = $value["inei"];
                break;

            }

        }

        $json = array(

            'response' => array(
                'success' => true,
                'status' => 200,
                'data' => array(
                    'ruc' => $company->ruc,
                    'razonSocial' => $company->razonSocial,
                    'nombreComercial' => $company->nombreComercial,
                    'tipo' => $company->tipo,
                    'estado' => $company->estado,
                    'condicion' => $company->condicion,
                    'direccion' => $company->direccion,
                    'departamento' => $company->departamento,
                    'provincia' => $company->provincia,
                    'distrito' => $company->distrito,
                    'ubigeo' => $valUbigeo,
                    'fechaInscripcion' => $company->fechaInscripcion,
                    'sistEmision' => $company->sistEmsion,
                    'sistContabilidad' => $company->sistContabilidad,
                    'actExterior' => $company->actExterior,
                ),
            ),

        );

        echo json_encode($json, http_response_code($json["response"]["status"]));

    }

    /*=============================================
    Consulta DNI
    =============================================*/
    public static function consultDni($value)
    {

        $dni = $value;

        $factory = new DniFactory();
        $cs = $factory->create();

        $person = $cs->get($dni);

        if (!$person) {

            $url = "https://apiperu.net/api/dni/plus/" . $value;
            //$url = "https://api.apis.net.pe/v2/reniec/dni?numero=" . $value;
            $method = "GET";
            $header = array(
                'Authorization: Bearer N3U21zRfasvOp94Ym6B8fwqrWtgTWjeCti8zlNmir80kcaP4ji',
                //'Authorization: Bearer apis-token-12460.dampv2jvrzHftfM4UkIFDUe50QFKzIKE',
                'Accept: application/json'
                //'Refer: https://apis.net.pe/consulta-dni-api'
            );
            $body = array();
            $response = ControladorCurl::consultaExterna($url, $method, $header, $body);

            if($response) {

                $json = array(

                    'response' => array(
                        'success' => true,
                        'status' => 200,
                        'data' => array(
                            'dni' => $response->data->numero,
                            'nombres' => $response->data->nombres,
                            'apellidoPaterno' => $response->data->apellido_paterno,
                            'apellidoMaterno' => $response->data->apellido_materno,
                            'nombreCompleto' => $response->data->apellido_paterno . ' ' . $response->data->apellido_materno . ' ' . $response->data->nombres,
                            'direccion' => $response->data->direccion,
                            'fechaNacimiento' => $response->data->fecha_nacimiento,
                            'codVerifica' => $response->data->codigo_verificacion,
                        ),
                    ),
        
                );

            } else {

                $json = array(

                    'response' => array(
                        'success' => false,
                        'status' => 404,
                        'message' => 'Not Found.',
                    ),
    
                );

            }

            echo json_encode($json, http_response_code($json["response"]["status"]));

            return;
        }

        $json = array(

            'response' => array(
                'success' => true,
                'status' => 200,
                'data' => array(
                    'dni' => $person->dni,
                    'nombres' => $person->nombres,
                    'apellidoPaterno' => $person->apellidoPaterno,
                    'apellidoMaterno' => $person->apellidoMaterno,
                    'nombreCompleto' => $person->apellidoPaterno . ' ' . $person->apellidoMaterno . ' ' . $person->nombres,
                    'codVerifica' => $person->codVerifica,
                ),
            ),

        );

        echo json_encode($json, http_response_code($json["response"]["status"]));

    }

    /*=============================================
    Consulta tipo de cambio
    =============================================*/
    public static function consultTipoCambio()
    {

        $url = 'https://www.sunat.gob.pe/a/txt/tipoCambio.txt';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $lineas = explode('|', $response);
        $fecha = $lineas[0];

        $n1 = $lineas[1];
        $n2 = $lineas[2];

        $compra = number_format($n1, 3);
        $venta = number_format($n2, 3);

        $fecha_objeto = date_create_from_format('d/m/Y', $fecha);
        $fecha_formateada = $fecha_objeto->format('Y-m-d');

        $json = array(

            'response' => array(
                'success' => true,
                'status' => 200,
                'data' => array(
                    'fecha' => $fecha_formateada,
                    'origen' => 'SUNAT',
                    'venta' => $venta,
                    'compra' => $compra,
                ),
            ),

        );

        echo json_encode($json, http_response_code($json["response"]["status"]));

    }

}