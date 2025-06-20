<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

class ControladorSunat
{

    public $mensajeError;
    public $coderror;
    public $xml;
    public $xmlb64;
    public $cdrb64;
    public $codrespuesta;
    public $hash;
    public $ticketS;
    public $code;

    /*=============================================
    Firmar Factura / Boleta
    =============================================*/
    public function FirmarComprobanteElectronico($emisor, $nombre, $ruta_archivo_xml, $ruta_firmado, $rutacertificado = null)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Definimos el entorno
        =============================================*/
        if ($emisor->modo == 'beta') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = 'certificado_prueba.pfx';
            $wsS = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
            $pass_certificado = 'ceti';

        }

        if ($emisor->modo == 'produccion') {

            $usuario_sol = $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = $emisor->ruc . '.pfx';
            $wsS = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl';
            $pass_certificado = $emisor->claveCertificado;

        }

        /*=============================================
        Firmamos el documento
        =============================================*/
        $objfirma = new ControladorSignature();
        $flg_firma = 0;
        $ruta = $ruta_archivo_xml . $nombre . '.XML';

        $ruta_firma = $rutacertificado . 'certificado/' . $certificado;
        $pass_firma = $pass_certificado;

        /*=============================================
        Guardamos el documento firmado
        =============================================*/
        $ruta_archivo_firmado = $ruta_firmado . $nombre;

        $resp = $objfirma->signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma, $ruta_archivo_firmado);
        global $hash_short; // 1. Creamos la variable global
        $hash_short = $resp['hash_cpe']; // 2. Asignamos un valor a la variable

    }

    /*=============================================
    Enviar Factura / Boleta
    =============================================*/
    public function EnviarComprobanteElectronico($emisor, $nombre, $ruta_archivo_xml, $ruta_archivo_cdr, $rutacertificado = null)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Definimos el entorno
        =============================================*/
        if ($emisor->modo == 'beta') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = 'certificado_prueba.pfx';
            $wsS = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
            $pass_certificado = 'ceti';

        }

        if ($emisor->modo == 'produccion') {

            $usuario_sol = $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = $emisor->ruc . '.pfx';
            $wsS = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl';
            $pass_certificado = $emisor->claveCertificado;

        }

        /*=============================================
        Obtenemos el hash del documento firmado
        =============================================*/
        $ruta = $ruta_archivo_xml . $nombre . '.XML';
        $original = file_get_contents($ruta);
        $xml_signed = new \DOMDocument();
        $xml_signed->loadXML($original);
        $digestValue = $xml_signed->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $hash_short = $digestValue;

        $this->xml = $nombre . '.XML';

        /*=============================================
        Zipeamos el documento
        =============================================*/
        $zip = new \ZipArchive();
        $nombrezip = $nombre . ".ZIP";
        $rutazip = $ruta_archivo_xml . $nombrezip;

        if ($zip->open($rutazip, \ZipArchive::CREATE) === true) {

            $zip->addFile($ruta, $nombre . '.XML');
            $zip->close();

        }

        /*=============================================
        Enviamos el zip a la ws de sunat
        =============================================*/
        $ws = $wsS;
        $ruta_archivo = $rutazip;
        $nombre_archivo = $nombrezip;
        $contenido_del_zip = base64_encode(file_get_contents($ruta_archivo)); //codificar y convertir en texto el .zip

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <soapenv:Header>
                        <wsse:Security>
                            <wsse:UsernameToken>
                                <wsse:Username>' . $emisor->ruc . $usuario_sol . '</wsse:Username>
                                <wsse:Password>' . $clave_sol . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                        <ser:sendBill>
                            <fileName>' . $nombre_archivo . '</fileName>
                            <contentFile>' . $contenido_del_zip . '</contentFile>
                        </ser:sendBill>
                        </soapenv:Body>
                    </soapenv:Envelope>';

        $header = array(
            "Content-type: text/xml; charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-lenght: " . strlen($xml_envio),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        //para ejecutar los procesos de forma local en windows
        //enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem"); //solo en local, si estas en el servidor web con ssl comentar esta línea

        $response = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $estadofe = "0";

        /*=============================================
        Eliminamos el archivo zipeado
        =============================================*/
        if (file_exists($rutazip)) {

            unlink($rutazip);

        }

        /*=============================================
        Obtenemos la respuesta
        =============================================*/
        if ($httpcode == 200) {

            $doc = new \DOMDocument();
            $doc->loadXML($response);

            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {

                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;

                $cdr = base64_decode($cdr);
                file_put_contents($ruta_archivo_cdr . 'R-' . $nombrezip, $cdr);

                //$this->cdrb64 = "R-" . $nombrezip;

                $zip = new \ZipArchive();

                if ($zip->open($ruta_archivo_cdr . 'R-' . $nombrezip) === true) {

                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $nombre . '.XML');
                    $zip->close();

                    $this->xmlb64 = "R-" . $nombre . '.XML';

                }

                /*=============================================
                Eliminamos el archivo zipeado
                =============================================*/
                if (file_exists($ruta_archivo_cdr . 'R-' . $nombrezip)) {

                    unlink($ruta_archivo_cdr . 'R-' . $nombrezip);

                }

                $xml_decode = file_get_contents($ruta_archivo_cdr . 'R-' . $nombre . '.XML') or die("Error: Cannot create object");
                $xml_decode = str_replace('<ar:', '<cac:', $xml_decode);
                $xml_decode = str_replace('</ar:', '</cac:', $xml_decode);
                $xml_decode = str_replace('<cbc:', '<cac:', $xml_decode);
                $xml_decode = str_replace('</cbc:', '</cac:', $xml_decode);
                $xml_decode = str_replace('<ar:', '<', $xml_decode);
                $xml_decode = str_replace('</ar:', '</', $xml_decode);
                $xml_decode = str_replace('<cac:', '<', $xml_decode);
                $xml_decode = str_replace('</cac:', '</', $xml_decode);
                $xml_decode = str_replace('<ext:', '<', $xml_decode);
                $xml_decode = str_replace('</ext:', '</', $xml_decode);
                $xml_decode = simplexml_load_string(utf8_encode($xml_decode));
                // $xml_decode = json_decode(json_encode((array)$xml_decode), true);

                function xmlarray($xmlObject, $out = array())
                {
                    foreach ((array) $xmlObject as $index => $node) {

                        $out[$index] = (is_object($node)) ? xmlarray($node) : $node;

                    }

                    return $out;

                }

                $xml_decode = xmlarray($xml_decode);

                $cod_hash = $xml_decode["UBLExtensions"]["UBLExtension"]["ExtensionContent"]["Signature"]["SignedInfo"]["Reference"]["DigestValue"];
                $responseCode = $xml_decode['DocumentResponse']['Response']['ResponseCode'];
                $description = $xml_decode['DocumentResponse']['Response']['Description'];

                if ($responseCode == 0) {

                    $estadofe = '1';

                } else {

                    $estadofe = $responseCode;

                }

                $this->code = $responseCode;
                $this->hash = $hash_short;
                $this->success = true;
                $this->mensajeError = $description;
                $this->cdrb64 = "R-" . $nombre . '.XML';

            } else {

                $estadofe = '2';
                $codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $code = preg_replace('/[^0-9]/', '', $codigo);

                if ($code >= 2000 && $code <= 3999) {

                    $this->hash = '';
                    $this->success = false;
                    $this->coderror = $codigo;
                    $this->mensajeError = $mensaje;
                    $this->codrespuesta = $estadofe;
                    $this->cdrb64 = '';
                    $this->code = $code;

                } else {

                    $this->hash = '';
                    $this->success = false;
                    $this->coderror = '';
                    $this->mensajeError = '';
                    $this->codrespuesta = 3;
                    $this->cdrb64 = '';
                    $this->code = $code;

                }

            }

        } else {

            $estadofe = "3";
            $this->hash = '';
            $this->success = 'error';
            $this->cdrb64 = '';
            $this->code = '';
            $this->codrespuesta = $estadofe;
            $this->mensajeError = 'No responde el servidor de SUNAT, por favor intenta el reenvío en unos minutos';

        }

        curl_close($ch);

    }

    /*=============================================
    Enviar Retencion / Percepcion
    =============================================*/
    public function EnviarComprobanteElectronicoPercepcion($emisor, $nombre, $ruta_archivo_xml, $ruta_archivo_cdr, $rutacertificado = null)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Definimos el entorno
        =============================================*/
        if ($emisor->modo == 'beta') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = 'certificado_prueba.pfx';
            $wsS = 'https://e-beta.sunat.gob.pe/ol-ti-itemision-otroscpe-gem-beta/billService';
            $pass_certificado = 'ceti';

        }

        if ($emisor->modo == 'produccion') {

            $usuario_sol = $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = $emisor->ruc . '.pfx';
            $wsS = 'https://e-factura.sunat.gob.pe/ol-ti-itemision-otroscpe-gem/billService';
            $pass_certificado = $emisor->claveCertificado;

        }

        /*=============================================
        Obtenemos el hash del documento firmado
        =============================================*/
        $ruta = $ruta_archivo_xml . $nombre . '.XML';
        $original = file_get_contents($ruta);
        $xml_signed = new \DOMDocument();
        $xml_signed->loadXML($original);
        $digestValue = $xml_signed->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $hash_short = $digestValue;

        $this->xml = $nombre . '.XML';

        /*=============================================
        Zipeamos el documento
        =============================================*/
        $zip = new \ZipArchive();
        $nombrezip = $nombre . ".ZIP";
        $rutazip = $ruta_archivo_xml . $nombrezip;

        if ($zip->open($rutazip, \ZipArchive::CREATE) === true) {

            $zip->addFile($ruta, $nombre . '.XML');
            $zip->close();

        }

        /*=============================================
        Enviamos el zip a la ws de sunat
        =============================================*/
        $ws = $wsS;
        $ruta_archivo = $rutazip;
        $nombre_archivo = $nombrezip;
        $contenido_del_zip = base64_encode(file_get_contents($ruta_archivo)); //codificar y convertir en texto el .zip

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <soapenv:Header>
                        <wsse:Security>
                            <wsse:UsernameToken>
                                <wsse:Username>' . $emisor->ruc . $usuario_sol . '</wsse:Username>
                                <wsse:Password>' . $clave_sol . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                        <ser:sendBill>
                            <fileName>' . $nombre_archivo . '</fileName>
                            <contentFile>' . $contenido_del_zip . '</contentFile>
                        </ser:sendBill>
                        </soapenv:Body>
                    </soapenv:Envelope>';

        $header = array(
            "Content-type: text/xml; charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-lenght: " . strlen($xml_envio),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        //para ejecutar los procesos de forma local en windows
        //enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem"); //solo en local, si estas en el servidor web con ssl comentar esta línea

        $response = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $estadofe = "0";

        /*=============================================
        Eliminamos el archivo zipeado
        =============================================*/
        if (file_exists($rutazip)) {

            unlink($rutazip);

        }

        /*=============================================
        Obtenemos la respuesta
        =============================================*/
        if ($httpcode == 200) {

            $doc = new \DOMDocument();
            $doc->loadXML($response);

            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {

                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;

                $cdr = base64_decode($cdr);
                file_put_contents($ruta_archivo_cdr . 'R-' . $nombrezip, $cdr);

                //$this->cdrb64 = "R-" . $nombrezip;

                $zip = new \ZipArchive();

                if ($zip->open($ruta_archivo_cdr . 'R-' . $nombrezip) === true) {

                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $nombre . '.XML');
                    $zip->close();

                    $this->xmlb64 = "R-" . $nombre . '.XML';

                }

                /*=============================================
                Eliminamos el archivo zipeado
                =============================================*/
                if (file_exists($ruta_archivo_cdr . 'R-' . $nombrezip)) {

                    unlink($ruta_archivo_cdr . 'R-' . $nombrezip);

                }

                $xml_decode = file_get_contents($ruta_archivo_cdr . 'R-' . $nombre . '.XML') or die("Error: Cannot create object");
                $xml_decode = str_replace('<ar:', '<cac:', $xml_decode);
                $xml_decode = str_replace('</ar:', '</cac:', $xml_decode);
                $xml_decode = str_replace('<cbc:', '<cac:', $xml_decode);
                $xml_decode = str_replace('</cbc:', '</cac:', $xml_decode);
                $xml_decode = str_replace('<ar:', '<', $xml_decode);
                $xml_decode = str_replace('</ar:', '</', $xml_decode);
                $xml_decode = str_replace('<cac:', '<', $xml_decode);
                $xml_decode = str_replace('</cac:', '</', $xml_decode);
                $xml_decode = str_replace('<ext:', '<', $xml_decode);
                $xml_decode = str_replace('</ext:', '</', $xml_decode);
                $xml_decode = simplexml_load_string(utf8_encode($xml_decode));
                // $xml_decode = json_decode(json_encode((array)$xml_decode), true);

                function xmlarray($xmlObject, $out = array())
                {
                    foreach ((array) $xmlObject as $index => $node) {

                        $out[$index] = (is_object($node)) ? xmlarray($node) : $node;

                    }

                    return $out;

                }

                $xml_decode = xmlarray($xml_decode);

                $cod_hash = $xml_decode["UBLExtensions"]["UBLExtension"]["ExtensionContent"]["Signature"]["SignedInfo"]["Reference"]["DigestValue"];
                $responseCode = $xml_decode['DocumentResponse']['Response']['ResponseCode'];
                $description = $xml_decode['DocumentResponse']['Response']['Description'];

                if ($responseCode == 0) {

                    $estadofe = '1';

                } else {

                    $estadofe = $responseCode;

                }

                $this->code = $responseCode;
                $this->hash = $hash_short;
                $this->success = true;
                $this->mensajeError = $description;
                $this->cdrb64 = "R-" . $nombre . '.XML';

            } else {

                $estadofe = '2';
                $codigo = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('detail')->item(0)->nodeValue;
                $code = preg_replace('/[^0-9]/', '', $codigo);

                if ($code >= 2000 && $code <= 3999) {

                    $this->hash = $hash_short;
                    $this->success = false;
                    $this->coderror = $codigo;
                    $this->mensajeError = $mensaje;
                    $this->codrespuesta = $estadofe;
                    $this->cdrb64 = '';
                    $this->code = $code;

                } else {

                    $this->hash = $hash_short;
                    $this->success = false;
                    $this->coderror = $code;
                    $this->mensajeError = $mensaje;
                    $this->codrespuesta = 3;
                    $this->cdrb64 = '';
                    $this->code = $code;

                }

            }

        } else {

            $estadofe = "3";
            $this->hash = $hash_short;
            $this->success = 'error';
            $this->cdrb64 = '';
            $this->code = '';
            $this->codrespuesta = $estadofe;
            $this->mensajeError = 'No responde el servidor de SUNAT, por favor intenta el reenvío en unos minutos';

        }

        curl_close($ch);

    }

    /*=============================================
    Generar Token para envío de guía de remisión
    =============================================*/
    public function generarTokenGuiaRemision($emisor)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Iniciamos el curl al API
        =============================================*/
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientessol/' . $emisor->client_id . '/oauth2/token/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=password&scope=https%3A%2F%2Fapi-cpe.sunat.gob.pe&client_id=' . $emisor->client_id . '&client_secret=' . $emisor->client_secret . '&username=' . $emisor->ruc . $emisor->usuarioSol . '&password=' . $emisor->claveSol,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        /*=============================================
        Decodificamos la respuesta
        =============================================*/
        $response = json_decode($response);
        error_reporting(0);

        /*=============================================
        Mostramos la respuesta
        =============================================*/
        if ($response && $response->access_token) {

            $this->success = true;
            $this->token = $response->access_token;

        } else {

            $this->success = false;
            $this->message = $response->error_description;

        }

    }

    /*=============================================
    Enviar Guia de remision API
    =============================================*/
    public function EnviarGuiaRemisionApi($emisor, $nombre, $ruta_archivo_xml)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Obtenemos el hash del documento firmado
        =============================================*/
        $ruta = $ruta_archivo_xml . $nombre . '.XML';
        $original = file_get_contents($ruta);
        $xml_signed = new \DOMDocument();
        $xml_signed->loadXML($original);
        $digestValue = $xml_signed->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $hash_short = $digestValue;

        /*=============================================
        Generamos el token
        =============================================*/
        $curlToken = curl_init();

        curl_setopt_array($curlToken, array(
            CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientessol/' . $emisor->client_id . '/oauth2/token/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=password&scope=https%3A%2F%2Fapi-cpe.sunat.gob.pe&client_id=' . $emisor->client_id . '&client_secret=' . $emisor->client_secret . '&username=' . $emisor->ruc . $emisor->usuarioSol . '&password=' . $emisor->claveSol,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $responseToken = curl_exec($curlToken);

        curl_close($curlToken);

        /*=============================================
        Decodificamos la respuesta
        =============================================*/
        $responseToken = json_decode($responseToken);

        /*=============================================
        Mostramos la respuesta
        =============================================*/
        if ($responseToken && $responseToken->access_token) {

            $token = $responseToken->access_token;

        } else {

            $token = '';

        }

        /*=============================================
        Zipeamos el documento
        =============================================*/
        $zip = new \ZipArchive();
        $ruta = $ruta_archivo_xml . $nombre . '.XML';
        $nombrezip = $nombre . ".zip";
        $rutazip = $ruta_archivo_xml . $nombre . ".zip";

        if ($zip->open($rutazip, \ZipArchive::CREATE) === true) {

            $zip->addFile($ruta, $nombre . '.XML');
            $zip->close();

        }

        $ruta_archivo = $rutazip;
        $nombre_archivo = $nombrezip;
        $contenido_del_zip = base64_encode(file_get_contents($ruta_archivo));
        $hash_de_zip = hash_file('sha256', $ruta_archivo);

        /*=============================================
        Generamos el ticket
        =============================================*/
        $dataSend = array(
            "archivo" => array(
                "nomArchivo" => $nombre_archivo,
                "arcGreZip" => $contenido_del_zip,
                "hashZip" => $hash_de_zip,
            ),
        );

        $data = json_encode($dataSend);

        $curlTiket = curl_init();

        curl_setopt_array($curlTiket, array(
            CURLOPT_URL => 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/' . $nombre,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ),
        ));

        $responseTicket = curl_exec($curlTiket);

        curl_close($curlTiket);

        $decodeTicket = json_decode($responseTicket);

        $this->success = true;
        $this->data = $decodeTicket;

    }

    /*=============================================
    Consultar ticket Guia de remision API
    =============================================*/
    public function ConsultarTicketGuiaRemisionApi($emisor, $ticket, $nombre, $ruta_archivo_cdr)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Obtenemos el hash del documento firmado
        =============================================*/
        $ruta = 'documents/xml/' . $emisor->ruc . '/signed/' . $nombre . '.XML';
        $original = file_get_contents($ruta);
        $xml_signed = new \DOMDocument();
        $xml_signed->loadXML($original);
        $digestValue = $xml_signed->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $hash_short = $digestValue;

        /*=============================================
        Generamos el token
        =============================================*/
        $curlToken = curl_init();

        curl_setopt_array($curlToken, array(
            CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientessol/' . $emisor->client_id . '/oauth2/token/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=password&scope=https%3A%2F%2Fapi-cpe.sunat.gob.pe&client_id=' . $emisor->client_id . '&client_secret=' . $emisor->client_secret . '&username=' . $emisor->ruc . $emisor->usuarioSol . '&password=' . $emisor->claveSol,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $responseToken = curl_exec($curlToken);

        curl_close($curlToken);

        /*=============================================
        Decodificamos la respuesta
        =============================================*/
        $responseToken = json_decode($responseToken);

        /*=============================================
        Mostramos la respuesta
        =============================================*/
        if ($responseToken && $responseToken->access_token) {

            $token = $responseToken->access_token;

        } else {

            $token = '';

        }

        /*=============================================
        Enviamos el documento
        =============================================*/
        $curlEnvio = curl_init();

        curl_setopt_array($curlEnvio, array(
            CURLOPT_URL => 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/' . $ticket,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
            ),
        ));

        $responseEnvio = curl_exec($curlEnvio);

        curl_close($curlEnvio);

        $decodeEnvio = json_decode($responseEnvio);

        /*=============================================
        Eliminamos el archivo zipeado (xml firmado)
        =============================================*/
        $rutaXml = 'documents/xml/' . $emisor->ruc . '/signed/' . $nombre . '.zip';

        if (file_exists($rutaXml)) {

            unlink($rutaXml);

        }

        if ($decodeEnvio->codRespuesta == 0) {

            $base64_zip = $decodeEnvio->arcCdr;

            // Decodificar el contenido Base64 del archivo ZIP
            $zip_content = base64_decode($base64_zip);

            // Guardar el contenido en un archivo ZIP
            $file_path = $ruta_archivo_cdr . "R-" . $nombre . ".ZIP";
            file_put_contents($file_path, $zip_content);

            $zip = new \ZipArchive();

            if ($zip->open($file_path) === true) {

                $zip->extractTo($ruta_archivo_cdr, 'R-' . $nombre . '.xml');
                $zip->close();

                $this->xmlb64 = "R-" . $nombre . '.XML';

            }

            /*=============================================
            Eliminamos el archivo zipeado (cdr)
            =============================================*/
            if (file_exists($file_path)) {

                unlink($file_path);

            }

            $xml_decode = file_get_contents($ruta_archivo_cdr . 'R-' . $nombre . '.xml') or die("Error: Cannot create object");
            $xml_decode = str_replace('<ar:', '<cac:', $xml_decode);
            $xml_decode = str_replace('</ar:', '</cac:', $xml_decode);
            $xml_decode = str_replace('<cbc:', '<cac:', $xml_decode);
            $xml_decode = str_replace('</cbc:', '</cac:', $xml_decode);
            $xml_decode = str_replace('<ar:', '<', $xml_decode);
            $xml_decode = str_replace('</ar:', '</', $xml_decode);
            $xml_decode = str_replace('<cac:', '<', $xml_decode);
            $xml_decode = str_replace('</cac:', '</', $xml_decode);
            $xml_decode = str_replace('<ext:', '<', $xml_decode);
            $xml_decode = str_replace('</ext:', '</', $xml_decode);
            $xml_decode = simplexml_load_string(utf8_encode($xml_decode));
            // $xml_decode = json_decode(json_encode((array)$xml_decode), true);

            function xmlarray($xmlObject, $out = array())
            {
                foreach ((array) $xmlObject as $index => $node) {

                    $out[$index] = (is_object($node)) ? xmlarray($node) : $node;

                }

                return $out;

            }

            $xml_decode = xmlarray($xml_decode);

            $cod_hash = $xml_decode["UBLExtensions"]["UBLExtension"]["ExtensionContent"]["Signature"]["SignedInfo"]["Reference"]["DigestValue"];
            $responseCode = $xml_decode['DocumentResponse']['Response']['ResponseCode'];
            $description = $xml_decode['DocumentResponse']['Response']['Description'];

            /*=============================================
            Imprimos la respuesta
            =============================================*/
            $this->hash = $hash_short;
            $this->success = true;
            $this->xml = $nombre . '.XML';
            $this->cdrb64 = 'R-' . $nombre . '.XML';
            $this->code = $responseCode;
            $this->data = $description;

        } else {

            $this->code = $decodeEnvio->codRespuesta;
            $this->success = false;
            $this->data = $decodeEnvio->error->desError;

        }

    }

    /*=============================================
    Enviar Guia de remision WS // Obsoleto
    =============================================*/
    public function EnviarGuiaRemision($emisor, $nombre, $ruta_archivo_xml, $ruta_archivo_cdr, $rutacertificado = null)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Definimos el entorno
        =============================================*/
        if ($emisor->modo == 'beta') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = 'certificado_prueba.pfx';
            $wsS = 'https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService';
            $pass_certificado = 'ceti';

        }
        if ($emisor->modo == 'produccion') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = $emisor->ruc . '.pfx';
            $wsS = 'https://e-guiaremision.sunat.gob.pe/ol-ti-itemision-guia-gem/billService?wsdl';
            $pass_certificado = $emisor->claveCertificado;

        }

        /*=============================================
        Obtenemos el hash del documento firmado
        =============================================*/
        $ruta = $ruta_archivo_xml . $nombre . '.XML';
        $original = file_get_contents($ruta);
        $xml_signed = new \DOMDocument();
        $xml_signed->loadXML($original);
        $digestValue = $xml_signed->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $hash_short = $digestValue;

        //$ruta = $ruta_archivo_xml . $nombre . '.XML';
        $this->xml = $nombre . '.XML';

        /*=============================================
        Zipeamos el documento
        =============================================*/
        $zip = new \ZipArchive();

        $nombrezip = $nombre . ".ZIP";
        $rutazip = $ruta_archivo_xml . $nombre . ".ZIP";

        if ($zip->open($rutazip, \ZipArchive::CREATE) === true) {

            $zip->addFile($ruta, $nombre . '.XML');
            $zip->close();

        }

        /*=============================================
        Enviamos el zip a la ws de sunat
        =============================================*/
        $ws = $wsS;
        $ruta_archivo = $rutazip;
        $nombre_archivo = $nombrezip;
        $contenido_del_zip = base64_encode(file_get_contents($ruta_archivo));

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <soapenv:Header>
                        <wsse:Security>
                            <wsse:UsernameToken>
                                <wsse:Username>' . $emisor->ruc . $usuario_sol . '</wsse:Username>
                                <wsse:Password>' . $clave_sol . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>
                        </soapenv:Header>
                        <soapenv:Body>
                        <ser:sendBill>
                            <fileName>' . $nombre_archivo . '</fileName>
                            <contentFile>' . $contenido_del_zip . '</contentFile>
                        </ser:sendBill>
                        </soapenv:Body>
                    </soapenv:Envelope>';

        $header = array(
            "Content-type: text/xml; charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-lenght: " . strlen($xml_envio),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        //para ejecutar los procesos de forma local en windows
        //enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem"); //solo en local, si estas en el servidor web con ssl comentar esta línea

        if (curl_error($ch) === false) {

            echo "Error: " . curl_error($ch);

        } else {

            $response = curl_exec($ch);

        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $estadofe = "0";

        if ($httpcode == 200) {

            $doc = new \DOMDocument();
            $doc->loadXML($response);

            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {

                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;

                $cdr = base64_decode($cdr);
                file_put_contents($ruta_archivo_cdr . 'R-' . $nombrezip, $cdr);

                $this->cdrb64 = "R-" . $nombrezip;

                $zip = new \ZipArchive();

                if ($zip->open($ruta_archivo_cdr . 'R-' . $nombrezip) === true) {

                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $nombre . '.XML');
                    $zip->close();

                    $this->xmlb64 = "R-" . $nombre . '.XML';

                }

                $xml_decode = file_get_contents($ruta_archivo_cdr . 'R-' . $nombre . '.XML') or die("Error: Cannot create object");
                $xml_decode = str_replace('<ar:', '<cac:', $xml_decode);
                $xml_decode = str_replace('</ar:', '</cac:', $xml_decode);
                $xml_decode = str_replace('<cbc:', '<cac:', $xml_decode);
                $xml_decode = str_replace('</cbc:', '</cac:', $xml_decode);
                $xml_decode = str_replace('<ar:', '<', $xml_decode);
                $xml_decode = str_replace('</ar:', '</', $xml_decode);
                $xml_decode = str_replace('<cac:', '<', $xml_decode);
                $xml_decode = str_replace('</cac:', '</', $xml_decode);
                $xml_decode = str_replace('<ext:', '<', $xml_decode);
                $xml_decode = str_replace('</ext:', '</', $xml_decode);
                $xml_decode = simplexml_load_string(utf8_encode($xml_decode));
                // $xml_decode = json_decode(json_encode((array)$xml_decode), true);

                function xmlarray($xmlObject, $out = array())
                {

                    foreach ((array) $xmlObject as $index => $node) {

                        $out[$index] = (is_object($node)) ? xmlarray($node) : $node;

                    }

                    return $out;

                }

                $xml_decode = xmlarray($xml_decode);

                $cod_hash = $xml_decode["UBLExtensions"]["UBLExtension"]["ExtensionContent"]["Signature"]["SignedInfo"]["Reference"]["DigestValue"];
                $responseCode = $xml_decode['DocumentResponse']['Response']['ResponseCode'];
                $description = $xml_decode['DocumentResponse']['Response']['Description'];

                if ($responseCode == 0) {

                    $estadofe = '1';

                } else {

                    $estadofe = $responseCode;

                }

                $this->codrespuesta = $estadofe;
                $this->code = $responseCode;
                $this->hash = $hash_short;
                $this->success = true;
                $this->mensajeError = $description;

            } else {

                $estadofe = '2';
                $codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;

                $code = preg_replace('/[^0-9]/', '', $codigo);
                if ($code >= 2000 && $code <= 3999) {

                    $this->hash = $hash_short;
                    $this->success = false;
                    $this->coderror = $codigo;
                    $this->mensajeError = $mensaje;
                    $this->codrespuesta = $estadofe;
                    $this->cdrb64 = '';
                    $this->code = $code;

                } else {

                    $this->hash = $hash_short;
                    $this->success = false;
                    $this->cdrb64 = '';
                    $this->coderror = '';
                    $this->mensajeError = $mensaje;
                    $this->codrespuesta = 3;
                    $this->code = $code;

                }

            }

        } else {

            $estadofe = "3";
            $this->hash = '';
            $this->success = 'error';
            $this->cdrb64 = '';
            $this->code = '';
            $this->codrespuesta = $estadofe;
            $this->mensajeError = 'No responde el servidor de SUNAT, por favor intenta el reenvío en unos minutos';

        }

        curl_close($ch);

    }

    /*=============================================
    Enviar Resumen diario
    =============================================*/
    public function EnviarResumenComprobantes($emisor, $nombrexml, $ruta_archivo_xml, $rutacertificado = null)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Definimos el entorno
        =============================================*/
        if ($emisor->modo == 'beta') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = 'certificado_prueba.pfx';
            $wsS = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
            $pass_certificado = 'ceti';

        }

        if ($emisor->modo == 'produccion') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = $emisor->ruc . '.pfx';
            $wsS = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl';
            $pass_certificado = $emisor->claveCertificado;

        }

        /*=============================================
        Zipeamos el documento
        =============================================*/
        $ruta = $ruta_archivo_xml . $nombrexml . '.XML';
        $zip = new \ZipArchive();

        $nombrezip = $nombrexml . ".ZIP";
        $rutazip = $ruta_archivo_xml . $nombrexml . ".ZIP";

        if ($zip->open($rutazip, \ZIPARCHIVE::CREATE) === true) {

            $zip->addFile($ruta, $nombrexml . '.XML');
            $zip->close();

        }

        /*=============================================
        Enviamos el zip a la ws de sunat
        =============================================*/
        $ws = $wsS;

        $ruta_archivo = $rutazip;
        $nombre_archivo = $nombrezip;

        $contenido_del_zip = base64_encode(file_get_contents($ruta_archivo));

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				 <soapenv:Header>
				 	<wsse:Security>
				 		<wsse:UsernameToken>
				 			<wsse:Username>' . $emisor->ruc . $usuario_sol . '</wsse:Username>
				 			<wsse:Password>' . $clave_sol . '</wsse:Password>
				 		</wsse:UsernameToken>
				 	</wsse:Security>
				 </soapenv:Header>
				 <soapenv:Body>
				 	<ser:sendSummary>
				 		<fileName>' . $nombre_archivo . '</fileName>
				 		<contentFile>' . $contenido_del_zip . '</contentFile>
				 	</ser:sendSummary>
				 </soapenv:Body>
				</soapenv:Envelope>';

        $header = array(
            "Content-type: text/xml; charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-lenght: " . strlen($xml_envio),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //para ejecutar los procesos de forma local en windows
        //enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");

        $response = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $estadofe = "0";

        $ticket = "0";
        if ($httpcode == 200) {

            $doc = new \DOMDocument();
            $doc->loadXML($response);

            if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {

                $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;
                $this->ticketS = $ticket;

            } else {

                $codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;

            }

        } else {

            echo curl_error($ch);

        }

        curl_close($ch);
        return $ticket;

    }

    /*=============================================
    Consultar ticket Resumen / Baja
    =============================================*/
    public function ConsultarTicket($emisor, $cabecera, $nombrexml, $ticket, $ruta_archivo_xml, $ruta_archivo_cdr, $datos_comprobante)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Definimos el entorno
        =============================================*/
        if ($emisor->modo == 'beta') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = 'certificado_prueba.pfx';
            $wsS = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
            $pass_certificado = 'ceti';

        }

        if ($emisor->modo == 'produccion') {

            $usuario_sol = $emisor->usuarioSol;
            $clave_sol = $emisor->claveSol;
            $certificado = $emisor->ruc . '.pfx';
            $wsS = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl';
            $pass_certificado = $emisor->claveCertificado;

        }

        $ws = $wsS;
        $nombre = $nombrexml;
        $nombre_xml = $nombre . ".XML";

        /*=============================================
        Obtenemos el hash del documento firmado
        =============================================*/
        $ruta = $ruta_archivo_xml . $nombre_xml;
        $original = file_get_contents($ruta);
        $xml_signed = new \DOMDocument();
        $xml_signed->loadXML($original);
        $digestValue = $xml_signed->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $hash_short = $digestValue;

        $this->xml = $nombre_xml;

        $ruta_firma = "documents/certificado/" . $certificado;
        $pass_firma = $pass_certificado;

        /*=============================================
        Zipeamos el documento
        =============================================*/
        $zip = new \ZipArchive();
        $nombrezip = $nombrexml . ".ZIP";
        $rutazip = $ruta_archivo_xml . $nombrexml . ".ZIP";

        if ($zip->open($rutazip, \ZIPARCHIVE::CREATE) === true) {

            $zip->addFile($ruta, $nombre_xml);
            $zip->close();

        }

        /*=============================================
        Enviamos el zip a la ws de sunat
        =============================================*/
        $ruta_archivo = $rutazip;
        $nombre_archivo = $nombrezip;

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                    <wsse:Username>' . $emisor->ruc . $usuario_sol . '</wsse:Username>
                    <wsse:Password>' . $clave_sol . '</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:getStatus>
                    <ticket>' . $ticket . '</ticket>
                </ser:getStatus>
            </soapenv:Body>
        </soapenv:Envelope>';

        $header = array(
            "Content-type: text/xml; charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-lenght: " . strlen($xml_envio),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //para ejecutar los procesos de forma local en windows
        //enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $estadofe = "0";

        /*=============================================
        Eliminamos el archivo zipeado
        =============================================*/
        if (file_exists($rutazip)) {

            unlink($rutazip);

        }

        /*=============================================
        Obtenemos la respuesta
        =============================================*/
        if ($httpcode == 200) {

            $doc = new \DOMDocument();
            $doc->loadXML($response);

            if (isset($doc->getElementsByTagName('content')->item(0)->nodeValue)) {

                $cdr = $doc->getElementsByTagName('content')->item(0)->nodeValue;
                $cdr = base64_decode($cdr);
                file_put_contents($ruta_archivo_cdr . "R-" . $nombre_archivo, $cdr);
                //$this->cdrb64 = "R-" . $nombrezip;

                $zip = new \ZipArchive;

                if ($zip->open($ruta_archivo_cdr . "R-" . $nombre_archivo) === true) {

                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $nombrexml . '.XML');
                    $zip->close();

                    $this->xmlb64 = "R-" . $nombrexml . '.XML';

                }

                /*=============================================
                Eliminamos el archivo zipeado
                =============================================*/
                if (file_exists($ruta_archivo_cdr . 'R-' . $nombre_archivo)) {

                    unlink($ruta_archivo_cdr . 'R-' . $nombre_archivo);

                }

                $xml_decode = file_get_contents($ruta_archivo_cdr . 'R-' . $nombre . '.XML') or die("Error: Cannot create object");
                $xml_decode = str_replace('<ar:', '<cac:', $xml_decode);
                $xml_decode = str_replace('</ar:', '</cac:', $xml_decode);
                $xml_decode = str_replace('<cbc:', '<cac:', $xml_decode);
                $xml_decode = str_replace('</cbc:', '</cac:', $xml_decode);
                $xml_decode = str_replace('<ar:', '<', $xml_decode);
                $xml_decode = str_replace('</ar:', '</', $xml_decode);
                $xml_decode = str_replace('<cac:', '<', $xml_decode);
                $xml_decode = str_replace('</cac:', '</', $xml_decode);
                $xml_decode = str_replace('<ext:', '<', $xml_decode);
                $xml_decode = str_replace('</ext:', '</', $xml_decode);
                $xml_decode = simplexml_load_string(utf8_encode($xml_decode));
                // $xml_decode = json_decode(json_encode((array)$xml_decode), true);

                function xmlarray($xmlObject, $out = array())
                {

                    foreach ((array) $xmlObject as $index => $node) {

                        $out[$index] = (is_object($node)) ? xmlarray($node) : $node;

                    }

                    return $out;

                }

                $xml_decode = xmlarray($xml_decode);

                $cod_hash = $xml_decode["UBLExtensions"]["UBLExtension"]["ExtensionContent"]["Signature"]["SignedInfo"]["Reference"]["DigestValue"];
                $responseCode = $xml_decode['DocumentResponse']['Response']['ResponseCode'];
                $description = $xml_decode['DocumentResponse']['Response']['Description'];

                if ($responseCode == 0) {

                    $estadofe = '1';

                } else {

                    $estadofe = $responseCode;

                }

                /*$this->success = true;
                $this->codrespuesta = $estadofe;*/

                $this->hash = $hash_short;
                $this->success = true;
                $this->code = $responseCode;
                $this->cdrb64 = "R-" . $nombre_xml;
                //$this->cdrb64 = '';
                $this->mensajeError = $description;
                $this->codrespuesta = $estadofe;

            } else {

                $estadofe = '2';
                $codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;

                $this->hash = '';
                $this->success = false;
                $this->code = $codigo;
                $this->cdrb64 = '';
                $this->mensajeError = $mensaje;
                $this->codrespuesta = $estadofe;

            }

        } else {

            $estadofe = '3';
            $msjError = curl_error($ch);
            $this->codrespuesta = $estadofe;
            $this->success = "error";
            $this->mensajeError = "No responde el servidor de SUNAT, por favor intenta el reenvío en unos minutos";
            $this->hash = '';
            $this->cdrb64 = '';
            $this->code = '';

        }

        curl_close($ch);
    }

    /*=============================================
    Consultar documento
    =============================================*/
    public function consultarComprobante($emisor, $comprobante)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        /*=============================================
        Definimos el entorno
        =============================================*/
        $usuario_sol = $emisor->usuarioSol;
        $clave_sol = $emisor->claveSol;
        $wsS = 'https://e-factura.sunat.gob.pe/ol-it-wsconscpegem/billConsultService';

        /*=============================================
        Enviamos los datos
        =============================================*/
        $ws = $wsS;
        $soapUser = "";
        $soapPassword = "";

        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
				xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
					<soapenv:Header>
						<wsse:Security>
							<wsse:UsernameToken>
								<wsse:Username>' . $emisor->ruc . $usuario_sol . '</wsse:Username>
								<wsse:Password>' . $clave_sol . '</wsse:Password>
							</wsse:UsernameToken>
						</wsse:Security>
					</soapenv:Header>
					<soapenv:Body>
						<ser:getStatus>
							<rucComprobante>' . $emisor->ruc . '</rucComprobante>
							<tipoComprobante>' . $comprobante->tipoDoc . '</tipoComprobante>
							<serieComprobante>' . $comprobante->serie . '</serieComprobante>
							<numeroComprobante>' . $comprobante->correlativo . '</numeroComprobante>
						</ser:getStatus>
					</soapenv:Body>
				</soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-length: " . strlen($xml_post_string),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //para ejecutar los procesos de forma local en windows
        //enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpcode == 200) {

            $doc = new \DOMDocument();
            $doc->loadXML($response);

            $this->success = true;
            $this->code = $doc->getElementsByTagName('statusCode')->item(0)->nodeValue;
            $this->description = $doc->getElementsByTagName('statusMessage')->item(0)->nodeValue;

            // Obtener el valor del TotalVenta del XML devuelto
            $xpath = new \DOMXPath($doc);
            $xpath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
            $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $totalVenta = $xpath->query('//cac:LegalMonetaryTotal/cbc:PayableAmount')->item(0)->nodeValue;

            // Asignar el valor del TotalVenta a la propiedad del objeto
            $this->totalVenta = $totalVenta;

        } else {

            $estadofe = '3';
            $msjError = curl_error($ch);
            $this->codrespuesta = $estadofe;
            $this->success = "error";
            $this->description = "No responde el servidor de SUNAT, por favor inténtalo en unos minutos";
            $this->hash = '';
            $this->cdrb64 = '';
            $this->code = '';

        }

        curl_close($ch);

    }

    /*=============================================
    Generar Token para consumo del api público de sunat
    =============================================*/
    public function generarToken($config)
    {

        /*=============================================
        Iniciamos el curl al API
        =============================================*/
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientesextranet/' . $config[0]->id_sunat_configuracion . '/oauth2/token/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials&scope=https%3A%2F%2Fapi.sunat.gob.pe%2Fv1%2Fcontribuyente%2Fcontribuyentes&client_id=' . $config[0]->id_sunat_configuracion . '&client_secret=' . $config[0]->clave_sunat_configuracion,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        /*=============================================
        Decodificamos la respuesta
        =============================================*/
        $response = json_decode($response);
        error_reporting(0);

        /*=============================================
        Mostramos la respuesta
        =============================================*/
        if ($response && $response->access_token) {

            $this->success = true;
            $this->token = $response->access_token;

        } else {

            $this->success = false;
            $this->message = $response->error_description;

        }

    }

    /*=============================================
    Consultar documento api público de sunat
    =============================================*/
    public function consultarComprobanteApi($emisor, $comprobante, $token)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sunat.gob.pe/v1/contribuyente/contribuyentes/'.$emisor->ruc.'/validarcomprobante',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "numRuc": "'.$comprobante->rucEmisor.'",
                "codComp": "'.$comprobante->codComp.'",
                "numeroSerie": "'.$comprobante->serie.'",
                "numero": "'.$comprobante->numero.'",
                "fechaEmision": "'.$comprobante->fechaEmision.'",
                "monto": "'.$comprobante->monto.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$token,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //echo $response;

        /*=============================================
        Decodificamos la respuesta
        =============================================*/
        $response = json_decode($response);

        $this->code = $response->data->estadoCp;

        if($response->data->estadoCp == 0) {

            $this->message = "EL COMPROBANTE NO EXISTE";
            $this->codeRuc = null;
            $this->messageRuc = null;
            $this->codeDom = null;
            $this->messageDom = null;

        } else if($response->data->estadoCp == 1) {

            $this->message = "EL COMPROBANTE EXISTE Y ESTÁ ACEPTADO";
            $this->codeRuc = $response->data->estadoRuc;
            $this->codeDom = $response->data->condDomiRuc;

            if($response->data->estadoRuc == '00') {

                $this->messageRuc = "EL RUC SE ENCUENTRA ACTIVO";

            } else if($response->data->estadoRuc == '01') {

                $this->messageRuc = "EL RUC SE ENCUENTRA CON BAJA PROVISIONAL";

            } else if($response->data->estadoRuc == '02') {

                $this->messageRuc = "EL RUC SE ENCUENTRA CON BAJA PROV. POR OFICIO";

            } else if($response->data->estadoRuc == '03') {

                $this->messageRuc = "EL RUC SE ENCUENTRA CON SUSPENSION TEMPORAL";

            } else if($response->data->estadoRuc == '10') {

                $this->messageRuc = "EL RUC SE ENCUENTRA CON BAJA DEFINITIVA";

            } else if($response->data->estadoRuc == '11') {

                $this->messageRuc = "EL RUC SE ENCUENTRA CON BAJA DE OFICIO";

            } else if($response->data->estadoRuc == '22') {

                $this->messageRuc = "EL RUC SE ENCUENTRA INHABILITADO-VENT.UNICA";

            }

            if($response->data->condDomiRuc == '00') {

                $this->messageDom = "EL DOMICILIO SE ENCUENTRA HABIDO";

            } else if($response->data->condDomiRuc == '09') {

                $this->messageDom = "EL DOMICILIO SE ENCUENTRA PENDIENTE";

            } else if($response->data->condDomiRuc == '11') {

                $this->messageDom = "EL DOMICILIO SE ENCUENTRA POR VERIFICAR";

            } else if($response->data->condDomiRuc == '12') {

                $this->messageDom = "EL DOMICILIO SE ENCUENTRA NO HABIDO";

            } else if($response->data->condDomiRuc == '20') {

                $this->messageDom = "EL DOMICILIO SE ENCUENTRA NO HALLADO";

            }

        } else if($response->data->estadoCp == 2) {

            $this->message = "EL COMPROBANTE EXISTE Y ESTÁ ANULADO";
            $this->codeRuc = null;
            $this->messageRuc = null;
            $this->codeDom = null;
            $this->messageDom = null;

        } else if($response->data->estadoCp == 3) {

            $this->message = "EL COMPROBANTE EXISTE Y ESTÁ AUTORIZADO";
            $this->codeRuc = null;
            $this->messageRuc = null;
            $this->codeDom = null;
            $this->messageDom = null;

        } else if($response->data->estadoCp == 4) {

            $this->message = "EL COMPROBANTE EXISTE Y NO ESTÁ AUTORIZADO";
            $this->codeRuc = null;
            $this->messageRuc = null;
            $this->codeDom = null;
            $this->messageDom = null;

        }

    }

}