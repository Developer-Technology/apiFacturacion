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

    table{
        width: 100%;
        border-collapse: collapse;
    }

    .h

    .datos-cliente{
        width: 100%;
        border-collapse: collapse;
        margin-top: 2mm
    }

    .datos-cliente td{
        border: .5px solid #000;
        padding: 10px;
        font-size: 11px;
        padding-top: 7px;
        padding-bottom: 7px;
        color: #000;
    }

    .v100{
        width: 100%;
    }

    .full-contenedor{
        position: absolute;
    }

    .first{
        border-radius: 5px 0px 0px 0px;
    }

    .last{
        border-radius: 0px 5px 0px 0px;
    }

    .descripcion-productoss th{
        padding: 2mm;
        text-align: center;
        color: #000;
        font-size: 11px;
        border: 0.5px solid black;
    }

    .descripcion-productoss td{
        padding: 1mm;
        font-size: 11px;
        color: #242424;
        border: .5px solid #000;
        text-align: center;
    }

    .montototal{
        width: 100%;
        margin-top: 5px;
    }

    .montototal td{
        width: 186.2mm;
        border-radius: 5px;
        padding: 5px;
        border: .5px solid #000;
        color: #242424;
        font-size: 8px;
        padding-left: 5mm;
    }
    .barcode{
        border: none;
        margin-top: 15px;
    }

    .totales{
        width: 100%;
        border-collapse: collapse;
    }

    .totales td{
        font-size: 13px;
        padding: 1mm;
        text-align: left;
        color: #242424;
    }

    .barc-resumen{
        margin: 10px;
        width: 100%;
        background-color: white;
    }

</style>

<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm">
    
    <page_header></page_header>
    
    <page_footer></page_footer>
    
    <div class="full-contenedor">
        
        <div>
            
            <table class="head">
                
                <tr>

                    <td style="width:45mm;text-align:center;vertical-align: middle;">
                        <?php
                        
                            if($emisor->logo == '') {

                                echo '<img src="documents/img/default/logo.png" class="v100">';

                            } else {
                                
                                echo '<img src="documents/logo/' . $emisor->ruc . '/' . $emisor->logo . '" class="v100">';
                            
                            }

                        ?>
                    </td>

                    <td style="text-align:left; width:92mm;vertical-align: middle">
                        <div style="text-align:center;font-size:18px;width:100%; font-weight:bold"><?php echo $emisor->razonSocial; ?></div>
                        <div style="text-align:center;font-size:12px;width:100%"><?php echo $emisor->address->direccion; ?> <br>Telf. <?php echo $emisor->telefono; ?></div>
                    </td>
                    
                    <td style="text-align:left;  width: 55mm">
                        <div style="text-align:center; border: .3px solid #000;width: 55mm; padding:.5mm 2mm .5mm 2mm; border-radius:10px;">
                            <h4 style="margin-botton:0px;">R.U.C. <?php echo $emisor->ruc; ?>
                                <br><br>
                                <?php echo $nombre_comprobante; ?>
                                <br><br>
                                <?php echo $comprobante->serie . ' - ' .str_pad( $comprobante->correlativo,8,"0",STR_PAD_LEFT); ?>
                            </h4>
                        </div>
                    </td>
                
                </tr>
            
            </table>
        
        </div>
        
        <table  class="datos-cliente">
            
            <tr class="clie">
                <td style="width:30mm" class="first">FECHA DE EMISIÓN:</td>
                <td style="width:148mm" class="last"><?php echo  date("d/m/Y", strtotime($comprobante->fechaEmision)) . ' ' . date("H:i:s", strtotime($comprobante->horaEmision)); ?></td>
            </tr>

            <tr class="clie">
                <td>CLIENTE:</td><td><?php echo $cliente->rznSocial ?></td>
            </tr>
            
            <tr class="clie">
                <td>DOCUMENTO:</td>
                <td><?php echo $cliente->numDoc ?></td>
            </tr>
            
            <tr class="clie">
                <td>DIRECCIÓN:</td>
                <td><?php echo $cliente->direccion ?></td>
            </tr>
            
            <tr class="clie">
                <td>TIPO DE MONEDA:</td>
                <td><?php echo $nombreMoneda ?></td>
            </tr>
        
        </table>
        
        <table  class="descripcion-productoss" style="margin-top: 2mm">
        
            <tr>
                <th style="width:13mm" class="first">ITEM</th>
                <th style="width:13mm">CANTIDAD</th>
                <th style="width:83mm;">DESCRIPCIÓN</th>            
                <th style="width:25mm">VALOR U.</th>
                <th style="width:25mm" class="last">IMPORTE</th>
            </tr>
            
            <?php

                $descuentosItems = 0.00;
                foreach($detalle as $key => $fila) { 

                    $importe_total = ($fila->mtoValorUnitario > 0) ? $fila->mtoPrecioUnitario : number_format(0.00,2);             
            ?>
                    <tr>
                        <td style="text-align:center"><?php echo ++$key ;?></td>
                        <td style="text-align:center"><?php echo $fila->cantidad; ?></td>
                        <td style="padding-left:12px"><?php echo $fila->descripcion ;?></td>
                        <td style="padding-left:12px"><?php echo $tipoMoneda.' '.number_format($fila->mtoValorUnitario,2);?></td>
                        <td style="padding-left:12px"><?php echo $tipoMoneda.' '.number_format($importe_total,2); ?></td>
                    </tr>
                        
            <?php

                    $descuentosItems += $fila->descuentos->monto;
                    
                }
                
                $descuentosItems;
                
                $descuentos = round($comprobante->dsctoGlobal->descuento + $descuentosItems,2);

            ?>

        </table>
        
        <table class="montototal">
            
            <tr>
                <td><?php echo $totalTexto; ?></td>
            </tr>
        
        </table>
        
        <table class="barc-resumen">

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
            
            <tr>

                <td style="width:30mm;">
                    <img src="<?php echo 'documents/qr/' . $ruc . '/' .  $ruc . '-' . $tipo_documento . '-' . $serie . '-' . $correlativo . '.png'; ?>" style="width: 26mm; background-color: white; color: #000; border: none; padding:none">
                </td>

                <td  style="width:88mm; padding:10px">
                    <h5>Observación: </h5>
                    <label style="font-size:10px;">Consulte su documento electrónico en: https://www.sunat.gob.pe</label>
                </td>

                <td  style="width:55mm;vertical-align: top;">

                    <div style=";width:100%;margin:0">

                        <table class="totales" style=";width:100%;margin:0;">

                            <tr>
                                <td style="width:30mm;">Op. Gravada:</td>
                                <td style="width:25mm;padding-left:12px"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoOperGravadas,2);?></td>
                            </tr>
                            
                            <tr>
                                <td style="width:30mm;">Op. Exonerada:</td>
                                <td style="width:25mm;padding-left:12px"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoOperExoneradas,2);?></td>
                            </tr>
                            
                            <tr>
                                <td style="width:30mm;">Op. Inafectas:</td>
                                <td style="width:25mm;padding-left:12px"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoOperInafectas,2);?></td>
                            </tr>
                            
                            <tr>
                                <td style="width:30mm;">Op. Gratuitas:</td>
                                <td style="width:25mm;padding-left:12px"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoOperGratuitas,2);?></td>
                            </tr>
                            
                            <tr>
                                <td>Descuento:</td>
                                <td style="padding-left:12px"><?php echo $tipoMoneda.' '. number_format($descuentos,2);?></td>
                            </tr>
                            
                            <tr>
                                <td>IGV(18.00%):</td>
                                <td style="padding-left:12px"><?php echo $tipoMoneda.' '.number_format($comprobante->mtoIGV,2);?></td>
                            </tr>
                            
                            <tr>
                                <td>Total:</td>
                                <td style="padding-left:12px"><?php echo  $tipoMoneda.' '.number_format($comprobante->total,2);?></td>
                            </tr>
                        
                        </table>
                    
                    </div>
                
                </td>
            
            </tr>
        
        </table>
        
        <div style=";width:100mm; padding:5px;padding-left: 16px; font-size:10px">
        
            <?php

                if($comprobante->tipoCompRef == "01") {

                    $nombreComprobante = "FACTURA";

                }

                if($comprobante->tipoCompRef == "03") {

                    $nombreComprobante = "BOLETA";

                }

                echo 'Motivo de emisión: '.$comprobante->codmotivo.' - '.$comprobante->descripcion.'<br>';
                echo 'Documento relacionado: '.$nombreComprobante.' '.$comprobante->serieRef.'-'.str_pad($comprobante->correlativoRef, 8, '0', STR_PAD_LEFT);

                if($emisor->modo == 'beta') {

                    echo "<br><small><i>COMPROBANTE DE PRUEBA, NO TIENE NINGUNA VALIDEZ</i></small>";
            
                }
                
            ?>
            
        </div>
    
    </div>

</page>