<?php
    
    /*-------------------------
    Autor: Developer Technology
    Web: www.developer-technology.net
    Mail: info@developer-technology.net
    ---------------------------*/

    /*=============================================
    Decodificamos los datos de la empresa
    =============================================*/
    $emisor = json_decode($emisor);

    /*=============================================
    Definimos el nombre de la moneda
    =============================================*/
    if($comprobante->tipoMoneda == 'PEN') {

        $tipoMoneda = 'S/ ';
        $nombreMoneda = 'SOLES';

    } else {

        $tipoMoneda = '$USD ';
        $nombreMoneda = 'DÓLARES AMERICANOS';

    }

    /*=============================================
    Definimos el nombre del documento
    =============================================*/
    if($comprobante->tipoDoc == '07') {

        $nombre_comprobante = 'NOTA DE CRÉDITO ELECTRÓNICA';

    } else if($comprobante->tipoDoc == '08') {

        $nombre_comprobante = 'NOTA DE DÉBITO ELECTRÓNICA';

    } else {

        $nombre_comprobante = 'ANULACIÓN DE VENTA';

    }

    /* Convertimos el total a texto */
    $decimales = explode(".", number_format($comprobante->total, 2));
    $entera = explode(".", $comprobante->total);
    $totalTexto = ControladorRutas::convertir($entera[0]) . ' CON ' . $decimales[1] . '/100 SOLES';

?>

<style>

    .logo{
        text-align: center;
    }

    .logo img{
        width: 40mm;
    }

    .empresa{
        text-align: center;
    }

    .empresa h3{
        font-size: 14px;
        margin:0;
        padding: 0;
        color: #3d4b43;
    }

    .empresa h3 span{
        font-size: 11px;
        font-weight: normal;
    }

    .numero-ruc{
        top: 30mm;
        text-align: center;
    }

    .numero-ruc h3{
        font-size: 14px;
        margin: 0.7mm;
        padding: 0;
        color: #3d4b43;
    }

    .tabledesc{
        width: 77.5mm;
    }

    .tabledesc .border{
        margin: 0mm;
        padding: 0mm;
        border-bottom: 0.1mm  dotted #3d4b43;
    }

    .tabledesc tr{
        width: 77.5mm;
    }

    .tabledesc tr th{
        font-size: 14px;
        padding: 0.2mm;
        margin: 1mm;
        padding-bottom:0mm;
        color: #3d4b43;
    }

    .tabledesc tr td{
        font-size: 12px;
        padding: 0.2mm;
        margin: 0;
    }

    .conte-detalles{
        margin-top: 1mm;
        border-bottom: 0.1mm  dotted #3d4b43;
    }

    .table-totales{
        width: 100%;   
        margin-top: 1mm; 
        border-bottom: 0.1mm  dotted #3d4b43;
    }

    .table-totales tr td{
        padding: 0.4mm;
        margin: 0.4mm;
        font-size: 13px;
        padding-bottom: 1mm
    }

    .table-cliente{
        width: 100%;
    }

    .table-cliente tr td{
        font-size: 12px;
        padding: 0.2mm;
        margin: 0;
    }

    .bar-code {
        margin-top: 1.5mm;
        text-align: center;
    }

    .en-letras {
        margin-top: 1mm;
        font-size: 9px;
    }

    .en-letras span {
        margin-top: 1mm;
        font-size: 9px;
    }

    .impresa{
        text-align: center;
        margin-top: 1.5mm;
        font-size: 9px; 
    }

    .mw282 {
        max-width: 282px;
    }

</style>

<page backtop="1.5mm" backbottom="1.5mm" backleft="1.5mm" backright="1.5mm">
    
    <page_header></page_header>
    
    <page_footer></page_footer>
    
    <?php
                    
        if($emisor->logo == '') {

            echo '<br><br>';

        } else {
            
            echo '<img src="documents/logo/' . $emisor->ruc . '/' . $emisor->logo . '" class="mw282"><br><br>';
        
        }

    ?>
    
    <div class="empresa">
        
        <h3>
            <?php echo $emisor->razonSocial; ?>
            <br>
            <span><?php echo $emisor->address->direccion; ?></span>
            <br>
            <span>Telf. <?php echo $emisor->telefono; ?></span>
        </h3>
    
    </div>
    
    <div class="numero-ruc">
        
        <h3><?php echo $emisor->ruc; ?></h3>
        <h3><?php echo $nombre_comprobante; ?></h3>
        <h3><?php echo $comprobante->serie . ' - ' .str_pad( $comprobante->correlativo,8,"0",STR_PAD_LEFT); ?></h3>
    
    </div>
    
    <div class="conte-cliente">
        
        <div style="width:100%; ">

            <?php

                if($comprobante->tipoCompRef == "01") {

                    $nombreComprobante = "FACTURA";

                }

                if($comprobante->tipoCompRef == "03") {

                    $nombreComprobante = "BOLETA";

                }

                echo 'Motivo: '.$comprobante->codmotivo.' - '.$comprobante->descripcion.'<br>';
                echo 'Relacionado: '.$nombreComprobante.' '.$comprobante->serieRef.'-'.str_pad($comprobante->correlativoRef, 8, '0', STR_PAD_LEFT);
            
            ?>
            
        </div>
        
        <table class="table-cliente" >
            
            <tr>
                <td style="width:29mm; text-align:left;">Fecha de Emisión: </td>
                <td style="width:42mm; text-align:left;"><?php echo date("d/m/Y", strtotime($comprobante->fechaEmision)) . ' ' . date("H:i:s", strtotime($comprobante->horaEmision)); ?></td>
            </tr>
        
        </table>

        <table class="table-cliente">

            <tr>
                <td style="width:15mm; text-align:left;">CLIENTE: </td>
                <td style="width:56mm; text-align:left;"><?php echo $cliente->rznSocial ?></td>
            </tr>

            <tr>
                <td style="width:15mm; text-align:left;">DOCUM.: </td>
                <td style="width:56mm; text-align:left;"><?php echo $cliente->numDoc ?></td>
            </tr>

            <tr>
                <td style="width:15mm; text-align:left;">DIRECC.: </td>
                <td style="width:56mm; text-align:left;"><?php echo $cliente->direccion ?></td>
            </tr>

            <tr>
                <td style="width:15mm; text-align:left;">MONEDA: </td>
                <td style="width:56mm; text-align:left;"><?php echo $nombreMoneda ?></td>
            </tr>

        </table>
    
    </div>
    
    <div class="conte-detalles">
        
        <table class="tabledesc">
            
            <thead class="header">

                <tr class="tr-head">
                    <th style="text-align:center; width:10mm;">Cant.</th>
                    <th style="text-align:center; width:30mm;">Descripción</th>
                    <th style="text-align:center; width:13mm;">Precio</th>
                    <th style="text-align:center; width:13mm;">Importe</th>
                </tr>

                <tr>
                    <th colspan="4" class="border"></th>
                </tr>
            
            </thead>
            
            <tbody>
                
                <?php 

                    $descuentosItems = 0.00;
                    foreach($detalle as $key => $fila) {

                        $importe_total = ($fila->mtoValorUnitario > 0) ? $fila->mtoPrecioUnitario : number_format(0.00,2);           
                
                ?>
                        <tr>
                            <td style="width:10mm; text-align:center;"><?php echo $fila->cantidad; ?></td>
                            <td style="width:30mm; text-align:center;"><?php echo $fila->descripcion; ?></td>
                            <td style="width:14mm;"><?php echo $tipoMoneda.' '.number_format($fila->mtoValorUnitario,2); ?></td>
                            <td style="width:14mm;"><?php echo $tipoMoneda.' '.number_format($importe_total,2); ?></td>
                        </tr>
                        
                <?php
                
                        $descuentosItems += $fila->descuentos->monto;

                    }

                   $descuentosItems;  
    
                   $descuentos = round($comprobante->dsctoGlobal->descuento + $descuentosItems,2);

                ?>
                
            </tbody>
        
        </table>
    
    </div>
    
    <div class="conte-totales">

        <table class="table-totales">

            <tr>
                <td style="width:49mm; text-align:right;">Gravadas: </td>
                <td style="width:21mm; text-align:left"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoOperGravadas,2);?></td>
            </tr>

            <tr>
                <td style="width:49mm; text-align:right;">Exoneradas: </td>
                <td style="width:21mm; text-align:left"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoOperExoneradas,2);?></td>
            </tr>

            <tr>
                <td style="width:49mm; text-align:right;">Inafectas: </td>
                <td style="width:21mm; text-align:left"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoOperInafectas,2);?></td>
            </tr>

            <tr>
                <td style="width:49mm; text-align:right;">Gratuitas: </td>
                <td style="width:21mm; text-align:left"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoOperGratuitas,2);?></td>
            </tr>

            <tr>
                <td style="width:49mm; text-align:right;">Descuento Total: </td>
                <td style="width:21mm; text-align:left"><?php echo $tipoMoneda.' '.number_format($descuentos,2);?></td>
            </tr>

            <tr>
                <td style="width:49mm; text-align:right;">IGV(18%): </td>
                <td style="width:21mm; text-align:left"><?php echo  $tipoMoneda.' '.number_format($comprobante->mtoIGV,2);?></td>
            </tr>

            <tr>
                <td style="width:49mm; text-align:right;">Total: </td>
                <td style="width:21mm; text-align:left"><?php echo  $tipoMoneda.' '.number_format($comprobante->total,2);?></td>
            </tr>

        </table>
    
    </div>
    
    <div class="en-letras">
        <span><?php echo $totalTexto; ?></span>
    </div>
    
    <div class="bar-code">

        <?php

            $ruc = $emisor->ruc;
            $tipo_documento = $comprobante->tipoDoc;
            $serie = $comprobante->serie;
            $correlativo = $comprobante->correlativo;
            $igv = $comprobante->mtoIGV;
            $total = $comprobante->total;
            $fecha = $comprobante->fechaEmision;
            $tipodoccliente = $cliente->tipoDoc;
            $nro_doc_cliente = $cliente->numDoc;

        ?>

        <img src="<?php echo 'documents/qr/' . $ruc . '/' .  $ruc . '-' . $tipo_documento . '-' . $serie . '-' . $correlativo . '.png'; ?>" style="width: 28mm; background-color: white; color: #000; border: none; padding:none">
    
    </div>
    
    <div class="impresa">
        Representación impresa de la Factura Electrónica.
        <br>
        Consulte su documento en: https://www.sunat.gob.pe

        <?php
        
        if($emisor->modo == 'beta') {

            echo "<br><small><i>COMPROBANTE DE PRUEBA, NO TIENE NINGUNA VALIDEZ</i></small>";
    
        }

        ?>
    </div>

</page>