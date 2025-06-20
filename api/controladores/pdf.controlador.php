<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

/*=============================================
Incluimos la libreria
=============================================*/
use Spipu\Html2Pdf\Html2Pdf;

class ControladorPdf
{

    /*=============================================
    PDF Factura / Boleta / Nota de venta (interno)
    =============================================*/
    public function CrearPdfDocumento($formato, $emisor, $cliente, $comprobante, $detalle, $protocol, $extra)
    {

        ob_start();

        if ($formato == 'ticket') {

            require_once "documents/libs/pdf/invoice-ticket.php";
            $nombrepdf = $emisor->ruc . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
            $html = ob_get_clean();
            $html2pdf = new Html2Pdf('P', array(77.5, 500), 'fr', true, 'UTF-8', 0);

        } else {

            require_once "documents/libs/pdf/invoice-a4.php";
            $nombrepdf = $emisor->ruc . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
            $html = ob_get_clean();
            $html2pdf = new Html2Pdf('P', 'a4', 'fr', true, 'UTF-8', 0);

        }

        $rutaPdf = dirname(__FILE__) . "/../documents/pdf/" . $emisor->ruc . "/invoice/" . $formato;

        /*=============================================
        Creamos la carpeta del pdf si no existe
        =============================================*/
        if (!file_exists($rutaPdf)) {
            mkdir($rutaPdf, 0777, true);
        }

        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->setTestTdInOnePage(true);
        $html2pdf->writeHTML($html);
        $html2pdf->output($rutaPdf . '/' . $nombrepdf . '.pdf', 'F');

        $this->pdf = $protocol .$_SERVER['HTTP_HOST'].'/documents/pdf/' .  $emisor->ruc . '/invoice/' . $formato . '/' . $nombrepdf . '.pdf';

    }

    /*=============================================
    PDF Nota de credito / Nota de debito
    =============================================*/
    public function CrearPdfNota($formato, $emisor, $cliente, $comprobante, $detalle, $protocol)
    {

        ob_start();

        if ($formato == 'ticket') {

            require_once "documents/libs/pdf/note-ticket.php";
            $nombrepdf = $emisor->ruc . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
            $html = ob_get_clean();
            $html2pdf = new Html2Pdf('P', array(77.5, 500), 'fr', true, 'UTF-8', 0);

        } else {

            require_once "documents/libs/pdf/note-a4.php";
            $nombrepdf = $emisor->ruc . '-' . $comprobante->tipoDoc . '-' . $comprobante->serie . '-' . $comprobante->correlativo;
            $html = ob_get_clean();
            $html2pdf = new Html2Pdf('P', 'a4', 'fr', true, 'UTF-8', 0);

        }

        $rutaPdf = dirname(__FILE__) . "/../documents/pdf/" . $emisor->ruc . "/note/" . $formato;

        /*=============================================
        Creamos la carpeta del pdf si no existe
        =============================================*/
        if (!file_exists($rutaPdf)) {
            mkdir($rutaPdf, 0777, true);
        }

        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->setTestTdInOnePage(true);
        $html2pdf->writeHTML($html);
        $html2pdf->output($rutaPdf . '/' . $nombrepdf . '.pdf', 'F');

        $this->pdf = $protocol .$_SERVER['HTTP_HOST'].'/documents/pdf/' .  $emisor->ruc . '/note/' . $formato . '/' . $nombrepdf . '.pdf';

    }

    /*=============================================
    PDF Guia de remision
    =============================================*/
    public function CrearPdfGuia($emisor, $datosGuia, $datosEnvio, $details, $protocol)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        ob_start();

        require_once "documents/libs/pdf/despatch-a4.php";
        $nombrepdf = $emisor->ruc . '-' . $datosGuia->tipoDoc . '-' . $datosGuia->serie . '-' . $datosGuia->correlativo;
        $html = ob_get_clean();
        $html2pdf = new Html2Pdf('P', 'a4', 'fr', true, 'UTF-8', 0);

        $rutaPdf = dirname(__FILE__) . "/../documents/pdf/" . $emisor->ruc . "/despatch/a4";

        /*=============================================
        Creamos la carpeta del pdf si no existe
        =============================================*/
        if (!file_exists($rutaPdf)) {
            mkdir($rutaPdf, 0777, true);
        }

        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->setTestTdInOnePage(true);
        $html2pdf->writeHTML($html);
        $html2pdf->output($rutaPdf . '/' . $nombrepdf . '.pdf', 'F');

        $this-> pdf = $protocol .$_SERVER['HTTP_HOST'].'/documents/pdf/' .  $emisor->ruc . '/despatch/a4/' . $nombrepdf . '.pdf';

    }

}