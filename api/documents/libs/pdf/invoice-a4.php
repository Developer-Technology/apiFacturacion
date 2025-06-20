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
if ($comprobante->tipoMoneda == "PEN") {
    $tipoMoneda = "S/ ";
    $nombreMoneda = "SOLES";
} else {
    $tipoMoneda = '$USD ';
    $nombreMoneda = "DÓLARES AMERICANOS";
}

/*=============================================
    Definimos el nombre del documento
    =============================================*/
if ($comprobante->tipoDoc == "01") {
    $nombre_comprobante = "FACTURA ELECTRÓNICA";
} elseif ($comprobante->tipoDoc == "03") {
    $nombre_comprobante = "BOLETA DE VENTA ELECTRÓNICA";
} elseif ($comprobante->tipoDoc == "nt" || $comprobante->tipoDoc == "99") {
    $nombre_comprobante = "TICKET DE VENTA";
} elseif ($comprobante->tipoDoc == "cz" || $comprobante->tipoDoc == "97") {
    $nombre_comprobante = "COTIZACIÓN";
} else {
    $nombre_comprobante = "----";
}

if ($comprobante->tipoDoc == "02") {
    $nombre_comprobante = $tipo_comprobante["descripcion"];
}

if ($comprobante->tipoDoc == "00") {
    $nombre_comprobante = $tipo_comprobante["descripcion"];
}

/* Convertimos el total a texto */
$decimales = explode(".", number_format($comprobante->total, 2));
$entera = explode(".", $comprobante->total);
$totalTexto =
    ControladorRutas::convertir($entera[0]) .
    " CON " .
    $decimales[1] .
    "/100 SOLES";
?>

<style>

    #tabla-cabecera, #tabla-cliente, #tabla-items, #tabla-totales, .tabla-importes, .tabla-observacion{
        position: relative;
        width:100%;
        border-collapse: collapse;
    }

    #tabla-cabecera{
        text-align: center;
        letter-spacing: 0.5px;
        color: #333;
    }

    #tabla-cabecera h3{
        font-size: 16px;
        margin-bottom: 1px;
        color: #444;
    }

    #tabla-cliente td{
        border: 0.5px solid #333;
        padding: 7px;
        text-align: left;
        font-size: 12px;
        padding-left: 10px;
        letter-spacing: 1px;
    }

    #tabla-totales td{
        padding: 7px;
        text-align: left;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding-left: 10px;
    }

    .tabla-importes td{
        border: 0.5px solid #333;
        padding: 7px;
        text-align: right;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding-right: 10px;
    }

    #tabla-cliente{
        margin-top: 10px;
    }

    #tabla-items{
        margin-top: 10px;
    }

    #tabla-items th{
        border: 0.5px solid #333;
        padding: 6px;
        text-align: center;
        font-size: 10px;
        letter-spacing: 0.5px;
        padding-left: 10px;
        background: #d3d3d3;
    }

    #tabla-items td{
        border: 0.5px solid #333;
        padding: 6px;
        text-align: center;
        font-size: 10px;
        letter-spacing: 0.5px;
        padding-left: 10px;
    }

    .ruc-emisor{
        position: relative;
        border: 1px solid #666;
        border-radius: 10px;
        text-align: center;
        vertical-align: top;
    }

    .ruc-emisor h4{
        color: #444;
    }

    .v1{
        width: 1%;
    }

    .v2{
        width: 2%;
    }

    .v3{
        width: 3%;
    }

    .v4{
        width: 4%;
    }

    .v5{
        width: 5%;
    }

    .v7{
        width: 7%;
    }

    .v10{
        width: 10%;
    }

    .v15{
        width: 15%;
    }

    .v20{
        width: 20%;
    }

    .v25{
        width: 25%;
    }

    .v30{
        width: 30%;
    }

    .v31{
        width: 31%;
    }

    .v32{
        width: 32%;
    }

    .v35{
        width: 35%;
    }

    .v40{
        width: 40%;
    }

    .v41{
        width: 41%;
    }

    .v45{
        width: 45%;
    }

    .v50{
        width: 50%;
    }

    .v55{
        width: 55%;
    }

    .v60{
        width: 60%;
    }

    .v65{
        width: 65%;
    }

    .v70{
        width: 70%;
    }

    .v75{
        width: 75%;
    }

    .v80{
        width: 80%;
    }

    .v100{
        width: 100%;
    }

    .direccion{
        font-size: 10px;
    }

    .total-letras{
        width: 100%;
        border: 0.5px solid #333;
        font-size: 12px;
        text-align: right;
        border-radius: 7px;
        padding: 10px;
        margin-top: 5px;
        padding-right: 10px;
        font-weight: bold;
    }

    .tabla-observacion{
        position: relative;
        margin-top: 5px;
    }

    .tabla-observacion td{
        position: relative;
        vertical-align: baseline;
    }

    .tabla-tipo-pago{
        width: 70%;
        border-collapse: collapse;
    }

    .tabla-tipo-pago td{
        border-bottom: 0.5px solid #333;
    }

    .col{
        background-color: #999;
    }

    .pie-pag{
        padding: 10px;
        font-size: 12px;
        /*border: 0.5px solid #333;*/
        margin-top: 10px;
        /*border-radius: 10px;*/
    }

    .b-l{
        border-radius: 7px 0px 0px 0px;
    }

    .b-r{
        border-radius: 0px 7px 0px 0px;
    }

    .mayu{
        text-transform: uppercase;
    }

    .anulado-print{
        position: absolute;
        top:30%;
        left:23%;
        color: #FF7979;
        font-size: 30px;
        text-align: center;
        font-weight: bold
    }

    /* */
    .tableDiv {
        border: 0.5px solid #333;
        width: 360px;
        min-height: 90px;
        height: 95px;
        max-height: 130px;
        padding: 7px;
        border-radius: 7px;
        line-height: 23px;
    }

    .tableDiv span {
        line-height: 15px;
        font-size: 12px;
    }

    .tabla-observaciones{
        width: 100%;
        border: 0.5px solid #333;
        font-size: 10px;
        text-align: left;
        border-radius: 7px;
        padding: 10px;
        margin-top: 5px;
        padding-left: 10px;
        font-weight: bold;
    }

    .spanFooter {
        text-align: center;
        margin-top: 10px;
        font-size: 12px;
    }

</style>

 <page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm">

    <page_header></page_header>

    <page_footer></page_footer>

    <!-- CABECERA COMPROBANTE=================== -->
    <table id="tabla-cabecera">

        <tr>

            <!-- LOGO================== -->
            <td class="v25">

                <?php if ($emisor->logo == "") {
                    echo '<img src="documents/img/default/logo.png" class="v100">';
                } else {
                    echo '<img src="documents/logo/' .
                        $emisor->ruc .
                        "/" .
                        $emisor->logo .
                        '" class="v100">';
                } ?>

            </td>
            <!--FIN LOGO================== -->

            <td class="v45" style="text-align: left;">
                <h3><?php echo $emisor->razonSocial; ?></h3>
                <label class="direccion"><?php echo $emisor->address
                    ->direccion; ?></label>
                <br>
                <span class="direccion">Telf. <?php echo $emisor->telefono; ?></span>
                <br>
                <span class="direccion">Mail. <?php echo $emisor->email; ?></span>
            </td>

            <td class="v30" style="text-align: left">
                <div class="ruc-emisor v100">
                    <h4>R.U.C. <?php echo $emisor->ruc; ?>
                    <br><br>
                    <?php echo $nombre_comprobante; ?>
                    <br><br>
                    <?php echo $comprobante->serie .
                        " - " .
                        str_pad(
                            $comprobante->correlativo,
                            8,
                            "0",
                            STR_PAD_LEFT
                        ); ?></h4>
                </div>
            </td>

        </tr>

    </table>
    <!--FIN CABECERA COMPROBANTE=================== -->

    <table>

        <tr>
            <td class="v100">
                <h3><?php echo $emisor->razonSocial; ?></h3>
                <span>Mail. <?php echo $emisor->address->direccion; ?></span>
            </td>
        </tr>

    </table>

    <!--CLIENTE COMPROBANTE=================== -->
    <br>

    <table>

        <tr>

            <td class="v50">
                <div class="tableDiv">
                    <span><b>CLIENTE: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><?php echo $cliente->rznSocial; ?></span>
                    <br>
                    <span><b>DOCUMENTO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><?php echo $cliente->numDoc; ?></span>
                    <br>
                    <span><b>DIRECCIÓN: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><?php echo $cliente->direccion; ?></span>
                </div>
            </td>

            <td class="v50">
                <div class="tableDiv">
                    <span><b>FECHA EMISIÓN: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><?php echo date(
                        "d/m/Y",
                        strtotime($comprobante->fechaEmision)
                    ) .
                        " " .
                        date(
                            "H:i:s",
                            strtotime($comprobante->horaEmision)
                        ); ?></span>
                    <br>
                    <span><b>FECHA VENCIMIENTO: &nbsp;&nbsp;&nbsp;&nbsp;</b><?php echo date(
                        "d/m/Y",
                        strtotime($comprobante->fechaEmision)
                    ); ?></span>
                    <br>
                    <span><b>MONEDA: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><?php echo $nombreMoneda; ?></span>
                    <br>
                    <span><b>FORMA DE PAGO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><?php echo $comprobante->tipoPago; ?></span>
                </div>
            </td>

        </tr>

    </table>
    <!--FIN CLIENTE COMPROBANTE=================== -->

    <!-- ITEMS COMPROBANTE=================== -->
    <table id="tabla-items">

        <tr class="">
            <th class="v1 b-l">N°</th>
            <th class="v7">CANT.</th>
            <th class="v7">UNIDAD</th>
            <th class="v10">CÓDIGO</th>
            <th class="v32">DESCRIPCIÓN</th>
            <th class="v10">V. UNIT.</th>
            <th class="v10">IGV</th>
            <th class="v10">P. UNIT.</th>
            <th class="v10 b-r">TOTAL</th>
        </tr>

        <?php
        $descuentosItems = 0.0;

        foreach ($detalle as $key => $fila) {
            $importe_total =
                $fila->mtoValorUnitario > 0
                    ? $fila->mtoPrecioUnitario
                    : number_format(0.0, 2); ?>

                <tr>
                    <td class="v1"><?php echo ++$key; ?></td>
                    <td class="v7"><?php echo $fila->cantidad; ?></td>
                    <td class="v7"><?php echo $fila->unidad; ?></td>
                    <td class="v10"><?php echo $fila->codProducto; ?></td>
                    <td class="v32"><?php echo $fila->descripcion; ?></td>
                    <td class="v10"><?php echo number_format(
                        $fila->mtoValorUnitario,
                        2
                    ); ?></td>
                    <td class="v10"><?php echo number_format(
                        $fila->mtoPrecioUnitario - $fila->mtoValorUnitario,
                        2
                    ); ?></td>
                    <td class="v10"><?php echo number_format(
                        $fila->mtoPrecioUnitario,
                        2
                    ); ?></td>
                    <td class="v10"><?php echo number_format(
                        $importe_total,
                        2
                    ); ?></td>
                </tr>

        <?php if (isset($fila->descuentos->monto)) {
            $descuentosItems += $fila->descuentos->monto;
        }
        }

        $descuentosItems;

        if (isset($comprobante->dsctoGlobal->descuento)) {
            $descuentos = round(
                $comprobante->dsctoGlobal->descuento + $descuentosItems,
                2
            );
        } else {
            $descuentos = 0.0;
        }
        ?>

    </table>
    <!-- FIN ITEMS COMPROBANTE=================== -->

    <div class="total-letras">
        <i><?php echo "SON: " . $totalTexto; ?></i>
    </div>

    <div class="tabla-observaciones">
        <?php echo "OBSERACIONES: " . $comprobante->observacion; ?>
    </div>

    <table id="tabla-totales">

        <tr>

            <td class="v65" style="padding: 0px;">

                <table class="tabla-observacion">

                    <tr>

                        <td class="v60">

                            <?php if (
                                isset($extra->datosCuenta) &&
                                $extra->datosCuenta->cuenta != ""
                            ): ?>
                                <b>CUENTA <?php echo $extra->datosCuenta
                                    ->banco; ?></b><br><br>
                                <b>N° Cuenta: </b><?php echo $extra->datosCuenta
                                    ->cuenta; ?><br>
                                <b>CCI: </b><?php echo $extra->datosCuenta
                                    ->cci; ?><br>
                                <b>Titular: </b><?php echo $extra->datosCuenta
                                    ->titular; ?><br>
                                <b>Yape: </b><?php echo $extra->datosCuenta
                                    ->yape; ?><br>
                                <b>OBS: </b><?php echo $extra->datosCuenta
                                    ->obs; ?>
                            <?php endif; ?>

                        </td>

                        <td class="v41">

                            <?php
//if($comprobante->tipoDoc == '01' || $comprobante->tipoDoc == '03'):
?>

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

                                <img src="<?php echo "documents/qr/" .
                                    $ruc .
                                    "/" .
                                    $ruc .
                                    "-" .
                                    $tipo_documento .
                                    "-" .
                                    $serie .
                                    "-" .
                                    $correlativo .
                                    ".png"; ?>" style="width: 32mm; background-color: white; color: #000; border: none; padding:none">

                            <?php
//endif
?>

                        </td>

                    </tr>

                </table>

                <table class="tabla-tipo-pago">

                    <tr>
                        <td colspan="2">
                            <b>Información Adicional</b>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            LEYENDA
                        </td>
                    </tr>

                    <?php if ($comprobante->tipoPago == "Credito") { ?>

                        <tr>
                            <td class="v45">FORMA DE PAGO:</td>
                            <td class="v55">Crédito</td>
                        </tr>

                    <?php
                    $k = 1;
                    foreach ($comprobante->cuotas as $keyC => $item) {
                        $cuotas = $item->cuota;
                        $fecha_cuota = $item->fechaCuota;

                        echo '<tr>
                                        <td class="v45">Fecha de pago</td>
                                        <td class="v55">Cuota00' .
                            $k++ .
                            '</td>
                                    </tr>';
                        echo '<tr>
                                        <td class="v45">' .
                            $fecha_cuota .
                            '</td>
                                        <td class="v55">' .
                            number_format($cuotas, 2) .
                            '</td>
                                    </tr>';
                    }
                    ?>

                    <?php } else {echo '<tr><td class="v45">FORMA DE PAGO:</td>';
                        echo '<td class="v55">Contado</td></tr>';} ?>

                </table>

            </td>

            <?php
            /* Gravadas */
            if (isset($comprobante->mtoOperGravadas)) {
                $gravadas = $comprobante->mtoOperGravadas;
            } else {
                $gravadas = 0;
            }

            /* Exoneradas */
            if (isset($comprobante->mtoOperExoneradas)) {
                $exoneradas = $comprobante->mtoOperExoneradas;
            } else {
                $exoneradas = 0;
            }

            /* Inafectas */
            if (isset($comprobante->mtoOperInafectas)) {
                $inafectas = $comprobante->mtoOperInafectas;
            } else {
                $inafectas = 0;
            }

            /* Gratuitas */
            if (isset($comprobante->mtoOperGratuitas)) {
                $gratuitas = $comprobante->mtoOperGratuitas;
            } else {
                $gratuitas = 0;
            }

            /* Exportacion */
            if (isset($comprobante->mtoExportacion)) {
                $exportacion = $comprobante->mtoExportacion;
            } else {
                $exportacion = 0;
            }

            /* Icbper */
            if (isset($comprobante->icbper)) {
                $icbper = $comprobante->icbper;
            } else {
                $icbper = 0;
            }
            ?>

            <td class="v35">

                <table class="tabla-importes">

                    <tr>
                        <td class="b-l">OP. GRAVADAS: <?php echo $tipoMoneda; ?></td>
                        <td class="v40 b-r"><?php echo number_format(
                            $gravadas,
                            2
                        ); ?></td>
                    </tr>

                    <?php if (
                        $comprobante->tipoDoc == "01" ||
                        $comprobante->tipoDoc == "03"
                    ): ?>
                    <tr>
                        <td>OP. EXONERADAS: <?php echo $tipoMoneda; ?></td>
                        <td><?php echo number_format($exoneradas, 2); ?></td>
                    </tr>

                    <tr>
                        <td>OP. INAFECTAS: <?php echo $tipoMoneda; ?></td>
                        <td><?php echo number_format($inafectas, 2); ?></td>
                    </tr>

                    <tr>
                        <td>OP. GRATUITAS: <?php echo $tipoMoneda; ?></td>
                        <td><?php echo number_format($gratuitas, 2); ?></td>
                    </tr>

                    <tr>
                        <td>OP. EXPORTACIÓN: <?php echo $tipoMoneda; ?></td>
                        <td><?php echo number_format($exportacion, 2); ?></td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <td>DESCUENTO(-): <?php echo $tipoMoneda; ?></td>
                        <td><?php echo number_format($descuentos, 2); ?></td>
                    </tr>

                    <?php if (
                        $comprobante->tipoDoc == "01" ||
                        $comprobante->tipoDoc == "03"
                    ): ?>
                    <tr>
                        <td>ICBPER: <?php echo $tipoMoneda; ?></td>
                        <td><?php echo number_format($icbper, 2); ?></td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <td>IGV(18%): <?php echo $tipoMoneda; ?></td>
                        <td><?php echo number_format(
                            $comprobante->mtoIGV,
                            2
                        ); ?></td>
                    </tr>

                    <tr style="font-size: 20px; background: #d3d3d3;">
                        <td><b>TOTAL:</b></td>
                        <td><b><?php echo $tipoMoneda .
                            number_format($comprobante->total, 2); ?></b></td>
                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <?php if (
        $comprobante->tipoDoc == "01" ||
        $comprobante->tipoDoc == "03" ||
        $comprobante->tipoDoc == "nt" ||
        $comprobante->tipoDoc == "cz" ||
        $comprobante->tipoDoc == "99"
    ): ?>

        <div class="pie-pag">

            <?php if (isset($extra->usuario) && $extra->usuario != ""): ?>
            <b>USUARIO: <?php echo $extra->usuario .
                " " .
                date("d/m/Y", strtotime($comprobante->fechaEmision)) .
                " " .
                date("H:i:s", strtotime($comprobante->horaEmision)); ?></b><br>
            <?php endif; ?>

            <?php if (
                $comprobante->tipoDoc == "01" ||
                $comprobante->tipoDoc == "03"
            ): ?>
            Representación impresa de la <?php echo $nombre_comprobante; ?>. Autorizado mediante resolución N° 054-006-0001 490/SUNAT. Consulte su comprobante en https://www.sunat.gob.pe <br>
            <?php endif; ?>

            <?php
            if (
                $comprobante->tipoDoc == "01" ||
                $comprobante->tipoDoc == "03"
            ) {
                if ($emisor->modo == "beta") {
                    echo "<br><br><small><i>COMPROBANTE DE PRUEBA, NO TIENE NINGUNA VALIDEZ</i></small>";
                }

                if (
                    isset($comprobante->bienesSelva) &&
                    $comprobante->bienesSelva == "si"
                ) {
                    echo "<br>BIENES TRANSFERIDOS EN LA AMAZONIA REGION SELVA PARA SER CONSUMIDOS EN LA MISMA";
                }

                if (
                    isset($comprobante->serviciosSelva) &&
                    $comprobante->serviciosSelva == "si"
                ) {
                    echo "<br>SERVICIOS PRESTADOS EN LA AMAZONIA REGION SELVA PARA SER CONSUMIDOS EN LA MISMA";
                }
            }

            //Ingresamos el texto que no es un comprobante de pago electrónico valido
            if ($comprobante->tipoDoc == "99") {
                echo "<br><br><small><small><i>ESTE NO ES UN COMPROBANTE ELECTRONICO VALIDO, SOLICITE UNA BOLETA O FACTURA</i></small></small>";
            }

            //Agregamos los comentarios o notas adicionales
            if (isset($extra->comentario) && $extra->comentario != "") {
                echo "<br>-----<br>" . $extra->comentario;
            }

            //Agregamos la alerta que no es un comprobante de pago
            if ($comprobante->tipoDoc == "nt") {
                echo "<br><br>**<i><small>Este no es un comprobante electrónico, reclama tu boleta o factura.</small></i>";
            }
            ?>

        </div>

    <?php endif; ?>

    <div class="spanFooter">
        <b><?php echo $emisor->nombreSistema; ?></b>
        <br>
        Comprobante emitido a través de <?php echo $emisor->sistema; ?>
    </div>

</page>
