<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

/*=============================================
Incluimos la libreria
=============================================*/
require_once 'documents/signature/XMLSecurityKey.php';
require_once 'documents/signature/XMLSecurityDSig.php';
require_once 'documents/signature/XMLSecEnc.php';

class ControladorSignature
{

    public function signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma, $ruta_firmado)
    {

        $doc = new \DOMDocument();

        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = true;
        $doc->load($ruta);

        $objDSig = new \XMLSecurityDSig(false);
        $objDSig->setCanonicalMethod(\XMLSecurityDSig::C14N);
        $options['force_uri'] = true;
        $options['id_name'] = 'ID';
        $options['overwrite'] = false;

        $objDSig->addReference($doc, \XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), $options);
        $objKey = new \XMLSecurityKey(\XMLSecurityKey::RSA_SHA1, array('type' => 'private'));

        $pfx = file_get_contents($ruta_firma);
        $key = array();

        openssl_pkcs12_read($pfx, $key, $pass_firma);
        $objKey->loadKey($key["pkey"]);
        $objDSig->add509Cert($key["cert"], true, false);
        $objDSig->sign($objKey, $doc->documentElement->getElementsByTagName("ExtensionContent")->item($flg_firma));

        $atributo = $doc->getElementsByTagName('Signature')->item(0);
        $atributo->setAttribute('Id', 'SignatureSP');

        //===================rescatamos Codigo(HASH_CPE)==================
        $hash_cpe = $doc->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $firma_cpe = $doc->getElementsByTagName('SignatureValue')->item(0)->nodeValue;

        $doc->save($ruta_firmado . '.XML');
        $resp['respuesta'] = 'ok';
        $resp['hash_cpe'] = $hash_cpe;
        $resp['firma_cpe'] = $firma_cpe;
        return $resp;

    }
    
}