<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

class ControladorXML
{
 
    /*=============================================
    Generamos xml Factura / Boleta
    =============================================*/
    function CrearXMLFactura($nombrexml, $emisor, $cliente, $comprobante, $detalle)
    {
    
        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        $doc = new \DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'utf-8';

        /* Convertimos el total a texto */
        $decimales = explode(".", number_format($comprobante->total, 2));
        $entera = explode(".", $comprobante->total);
        $totalTexto = ControladorRutas::convertir($entera[0]) . ' CON ' . $decimales[1] . '/100 SOLES';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
                    <ext:UBLExtensions>
                        <ext:UBLExtension>
                            <ext:ExtensionContent />
                        </ext:UBLExtension>
                    </ext:UBLExtensions>
                    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
                    <cbc:CustomizationID>2.0</cbc:CustomizationID>
                    <cbc:ID>'.$comprobante->serie.'-'.$comprobante->correlativo.'</cbc:ID>
                    <cbc:IssueDate>'.$comprobante->fechaEmision.'</cbc:IssueDate>
                    <cbc:IssueTime>'.$comprobante->horaEmision.'</cbc:IssueTime>
                    <cbc:DueDate>'.$comprobante->fechaEmision.'</cbc:DueDate>
                    <cbc:InvoiceTypeCode listID="'.$comprobante->tipoOperacion.'">'.$comprobante->tipoDoc.'</cbc:InvoiceTypeCode>
                    <cbc:Note languageLocaleID="1000"><![CDATA['.$totalTexto.']]></cbc:Note>';

                    //  ==================BIENES SELVA================= ";
                    if(isset($comprobante->bienesSelva) && $comprobante->bienesSelva == 'si') {
                        
                    $xml.='<cbc:Note languageLocaleID="2001">BIENES TRANSFERIDOS EN LA AMAZONIA REGION SELVA PARA SER CONSUMIDOS EN LA MISMA</cbc:Note>';
                
                    }
                    
                    //  ==================SERVICIO SELVA================= ";
                    if(isset($comprobante->serviciosSelva) && $comprobante->serviciosSelva == 'si') {
                        
                    $xml.='<cbc:Note languageLocaleID="2002">SERVICIOS PRESTADOS EN LA AMAZONIA REGION SELVA PARA SER CONSUMIDOS EN LA MISMA</cbc:Note>';
                    
                    }

                    //  ==================OBSERVACIONES================= ";
                    if(isset($comprobante->observacion)) {
                        
                    $xml.= '<cbc:Note><![CDATA['.$comprobante->observacion.']]></cbc:Note>';
                
                    }
                    
                    $xml.='<cbc:DocumentCurrencyCode>'.$comprobante->tipoMoneda.'</cbc:DocumentCurrencyCode>
                            <cac:Signature>
                                <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                                <cbc:Note><![CDATA['.$emisor->nombreComercial.']]></cbc:Note>
                                <cac:SignatoryParty>
                                    <cac:PartyIdentification>
                                        <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                                    </cac:PartyIdentification>
                                    <cac:PartyName>
                                        <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                                    </cac:PartyName>
                                </cac:SignatoryParty>
                                <cac:DigitalSignatureAttachment>
                                    <cac:ExternalReference>
                                        <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
                                    </cac:ExternalReference>
                                </cac:DigitalSignatureAttachment>
                            </cac:Signature>
                            <cac:AccountingSupplierParty>
                                <cac:Party>
                                    <cac:PartyIdentification>
                                        <cbc:ID schemeID="'.$emisor->tipoDoc.'">'.$emisor->ruc.'</cbc:ID>
                                    </cac:PartyIdentification>
                                    <cac:PartyName>
                                        <cbc:Name><![CDATA['.$emisor->nombreComercial.']]></cbc:Name>
                                    </cac:PartyName>
                                    <cac:PartyLegalEntity>
                                        <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                                        <cac:RegistrationAddress>
                                            <cbc:ID>'.$emisor->address->ubigeo.'</cbc:ID>
                                            <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                                            <cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>
                                            <cbc:CityName>'.$emisor->address->provincia.'</cbc:CityName>
                                            <cbc:CountrySubentity>'.$emisor->address->departamento.'</cbc:CountrySubentity>
                                            <cbc:District>'.$emisor->address->distrito.'</cbc:District>
                                            <cac:AddressLine>
                                                <cbc:Line><![CDATA['.$emisor->address->direccion.']]></cbc:Line>
                                            </cac:AddressLine>
                                            <cac:Country>
                                                <cbc:IdentificationCode>'.$emisor->address->codigoPais.'</cbc:IdentificationCode>
                                            </cac:Country>
                                        </cac:RegistrationAddress>
                                    </cac:PartyLegalEntity>
                                </cac:Party>
                            </cac:AccountingSupplierParty>
                            <cac:AccountingCustomerParty>
                                <cac:Party>
                                    <cac:PartyIdentification>
                                        <cbc:ID schemeID="'.$cliente->tipoDoc.'">'.$cliente->numDoc.'</cbc:ID>
                                    </cac:PartyIdentification>
                                    <cac:PartyLegalEntity>
                                        <cbc:RegistrationName><![CDATA['.$cliente->rznSocial.']]></cbc:RegistrationName>
                                        <cac:RegistrationAddress>
                                            <cac:AddressLine>
                                                <cbc:Line><![CDATA['.$cliente->direccion.']]></cbc:Line>
                                            </cac:AddressLine>
                                            <cac:Country>
                                                <cbc:IdentificationCode>'.$cliente->codigoPais.'</cbc:IdentificationCode>
                                            </cac:Country>
                                        </cac:RegistrationAddress>
                                    </cac:PartyLegalEntity>
                                </cac:Party>
                            </cac:AccountingCustomerParty>';
                            
                            if($comprobante->tipoPago == 'Contado') {
                                
                            $xml.='<cac:PaymentTerms>
                                    <cbc:ID>FormaPago</cbc:ID>
                                    <cbc:PaymentMeansID>Contado</cbc:PaymentMeansID>
                                </cac:PaymentTerms>';
                            
                            } else {
                                
                            $xml.='<cac:PaymentTerms>
                                    <cbc:ID>FormaPago</cbc:ID>
                                    <cbc:PaymentMeansID>'.$comprobante->tipoPago.'</cbc:PaymentMeansID>
                                    <cbc:Amount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->total.'</cbc:Amount>
                                </cac:PaymentTerms>';
                                
                            $cuotas = $comprobante->cuotas;
                            $k = 0;
                            
                            foreach($cuotas as $cuota) {
                                
                            $fecha = $cuota->fechaCuota;
                            $fecha2 = str_replace('/', '-', $fecha);
                            $fecha_cuota = date('Y-m-d', strtotime($fecha2));
                            
                            $xml.='<cac:PaymentTerms>
                                    <cbc:ID>FormaPago</cbc:ID>
                                    <cbc:PaymentMeansID>Cuota00' . ++$k . '</cbc:PaymentMeansID>
                                    <cbc:Amount currencyID="' . $comprobante->tipoMoneda . '">' . $cuota->cuota . '</cbc:Amount>
                                    <cbc:PaymentDueDate>' . $fecha_cuota . '</cbc:PaymentDueDate>
                                </cac:PaymentTerms>';
                            
                            }
                        
                            }
                            
                            //  ==================PERCEPCION================= ";
                            if(isset($comprobante->percepcion) && $comprobante->percepcion->total > 0) {
                                
                            $xml.='<cac:PaymentTerms>
                                    <cbc:ID>Percepcion</cbc:ID>
                                    <cbc:Amount currencyID="' . $comprobante->tipoMoneda . '">'.$comprobante->percepcion->total.'</cbc:Amount>
                                </cac:PaymentTerms>
                                <cac:AllowanceCharge>
                                    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
                                    <cbc:AllowanceChargeReasonCode>' . $comprobante->percepcion->codigo . '</cbc:AllowanceChargeReasonCode>
                                    <cbc:MultiplierFactorNumeric>' . $comprobante->percepcion->porcentaje . '</cbc:MultiplierFactorNumeric>
                                    <cbc:Amount currencyID="' . $comprobante->tipoMoneda . '">' . $comprobante->percepcion->monto . '</cbc:Amount>
                                    <cbc:BaseAmount currencyID="' . $comprobante->tipoMoneda . '">' . $comprobante->percepcion->base . '</cbc:BaseAmount>
                                </cac:AllowanceCharge>';
                            
                            }

                            //  ==================RETENCION================= ";
                            if(isset($comprobante->retencion) && $comprobante->retencion->base > 0) {

                            $xml.='<cac:AllowanceCharge>
                                    <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                                    <cbc:AllowanceChargeReasonCode>' . $comprobante->retencion->codigo . '</cbc:AllowanceChargeReasonCode>
                                    <cbc:MultiplierFactorNumeric>' . $comprobante->retencion->porcentaje . '</cbc:MultiplierFactorNumeric>
                                    <cbc:Amount currencyID="' . $comprobante->tipoMoneda . '">' . $comprobante->retencion->monto . '</cbc:Amount>
                                    <cbc:BaseAmount currencyID="' . $comprobante->tipoMoneda . '">' . $comprobante->retencion->base . '</cbc:BaseAmount>
                                </cac:AllowanceCharge>';
                            
                            }
                            
                            //  ==================DESCUENTO GLOBAL================= ";
                            if(isset($comprobante->dsctoGlobal) && $comprobante->dsctoGlobal->descuento > 0) {
                                
                            $xml.='<cac:AllowanceCharge>
                                    <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                                    <cbc:AllowanceChargeReasonCode>'.$comprobante->dsctoGlobal->codigoTipo.'</cbc:AllowanceChargeReasonCode>
                                    <cbc:MultiplierFactorNumeric>'.$comprobante->dsctoGlobal->descuentoFactor.'</cbc:MultiplierFactorNumeric>
                                    <cbc:Amount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->dsctoGlobal->descuento.'</cbc:Amount>
                                    <cbc:BaseAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->dsctoGlobal->montoBase.'</cbc:BaseAmount>
                                </cac:AllowanceCharge>';
                            
                            }
                            
                            //  ==================IGV================= ";
                            $igv = round($comprobante->mtoIGV + isset($comprobante->icbper),2);

                            $xml.='<cac:TaxTotal>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$igv.'</cbc:TaxAmount>';

                            //  ==================GRAVADAS================= ";
                            if(isset($comprobante->mtoOperGravadas) && $comprobante->mtoOperGravadas > 0) {
                                
                            $xml.='<cac:TaxSubtotal>
                                    <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperGravadas.'</cbc:TaxableAmount>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoIGV.'</cbc:TaxAmount>
                                    <cac:TaxCategory>
                                        <cac:TaxScheme>
                                            <cbc:ID>1000</cbc:ID>
                                            <cbc:Name>IGV</cbc:Name>
                                            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                    </cac:TaxCategory>
                                </cac:TaxSubtotal>';
                            
                            }

                            //  ==================EXPORTACION================= ";
                            if(isset($comprobante->mtoExportacion) && $comprobante->mtoExportacion > 0) {
                                
                            $xml.='<cac:TaxSubtotal>
                                    <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoExportacion.'</cbc:TaxableAmount>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoIGV.'</cbc:TaxAmount>
                                    <cac:TaxCategory>
                                        <cac:TaxScheme>
                                            <cbc:ID>9995</cbc:ID>
                                            <cbc:Name>EXP</cbc:Name>
                                            <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                    </cac:TaxCategory>
                                </cac:TaxSubtotal>';
                            
                            }

                            //  ==================ICBPER================= ";
                            if(isset($comprobante->icbper) && $comprobante->icbper > 0) {
                            
                            $xml.='<cac:TaxSubtotal>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->icbper.'</cbc:TaxAmount>
                                    <cac:TaxCategory>
                                        <cac:TaxScheme>
                                            <cbc:ID schemeAgencyName="PE:SUNAT" schemeName="Codigo de tributos" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo05">7152</cbc:ID>
                                            <cbc:Name>ICBPER</cbc:Name>
                                            <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                    </cac:TaxCategory>
                                </cac:TaxSubtotal>';
                            
                            }

                            //  ==================EXONERADAS================= ";
                            if(isset($comprobante->mtoOperExoneradas) && $comprobante->mtoOperExoneradas > 0) {
                                
                            $xml.='<cac:TaxSubtotal>
                                    <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperExoneradas.'</cbc:TaxableAmount>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">0.00</cbc:TaxAmount>
                                    <cac:TaxCategory>
                                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                                        <cac:TaxScheme>
                                            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                                            <cbc:Name>EXO</cbc:Name>
                                            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                    </cac:TaxCategory>
                                </cac:TaxSubtotal>';
                            
                            }

                            //  ==================INAFECTAS================= ";
                            if(isset($comprobante->mtoOperInafectas) && $comprobante->mtoOperInafectas > 0) {
                                
                            $xml.='<cac:TaxSubtotal>
                                    <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperInafectas.'</cbc:TaxableAmount>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">0.00</cbc:TaxAmount>
                                    <cac:TaxCategory>
                                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                                        <cac:TaxScheme>
                                            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                                            <cbc:Name>INA</cbc:Name>
                                            <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                    </cac:TaxCategory>
                                </cac:TaxSubtotal>';
                            
                            }

                            //  ==================GRATUITAS================= ";
                            if(isset($comprobante->mtoOperGratuitas) && $comprobante->mtoOperGratuitas > 0) {
                                
                            $xml.='<cac:TaxSubtotal>
                                    <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperGratuitas.'</cbc:TaxableAmount>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->igvOp.'</cbc:TaxAmount>
                                    <cac:TaxCategory>
                                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                                        <cac:TaxScheme>
                                            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                                            <cbc:Name>GRA</cbc:Name>
                                            <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                    </cac:TaxCategory>
                                </cac:TaxSubtotal>';
                            
                            }

                            //  ==================TOTALES================= ";
                            /* Operaciones gravadas */
                            if(isset($comprobante->mtoOperGravadas)) {
                                $mtoOperGravadas = $comprobante->mtoOperGravadas;
                            } else {
                                $mtoOperGravadas = 0;
                            }

                            /* Operaciones exoneradas */
                            if(isset($comprobante->mtoOperExoneradas)) {
                                $mtoOperExoneradas = $comprobante->mtoOperExoneradas;
                            } else {
                                $mtoOperExoneradas = 0;
                            }

                            /* Operaciones inafectas */
                            if(isset($comprobante->mtoOperInafectas)) {
                                $mtoOperInafectas = $comprobante->mtoOperInafectas;
                            } else {
                                $mtoOperInafectas = 0;
                            }

                            /* Operaciones exportacion */
                            if(isset($comprobante->mtoExportacion)) {
                                $mtoExportacion = $comprobante->mtoExportacion;
                            } else {
                                $mtoExportacion = 0;
                            }

                            $total_antes_de_impuestos = round($mtoOperGravadas + $mtoOperExoneradas + $mtoOperInafectas + $mtoExportacion,2);
                            
                            $xml.='</cac:TaxTotal>
                                <cac:LegalMonetaryTotal>
                                    <cbc:LineExtensionAmount currencyID="'.$comprobante->tipoMoneda.'">'.$total_antes_de_impuestos.'</cbc:LineExtensionAmount>
                                    <cbc:TaxInclusiveAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->total.'</cbc:TaxInclusiveAmount>
                                    <cbc:PayableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->total.'</cbc:PayableAmount>
                                </cac:LegalMonetaryTotal>';
                            
                            //  ==================ITEMS================= ";
                            $nItem = 1;
                            
                            foreach($detalle as $k=>$v) {
                                
                            $xml.='<cac:InvoiceLine>
                                    <cbc:ID>'.$nItem++.'</cbc:ID>
                                    <cbc:InvoicedQuantity unitCode="'.$v->unidad.'">'.$v->cantidad.'</cbc:InvoicedQuantity>
                                    <cbc:LineExtensionAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoBaseIgv.'</cbc:LineExtensionAmount>
                                    <cac:PricingReference>
                                        <cac:AlternativeConditionPrice>
                                            <cbc:PriceAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoValorUnitario.'</cbc:PriceAmount>
                                            <cbc:PriceTypeCode>'.$v->tipoPrecio.'</cbc:PriceTypeCode>
                                        </cac:AlternativeConditionPrice>
                                    </cac:PricingReference>';

                            //  ==================DESCUENTO POR ITEMS================= ";
                            
                            if(isset($v->descuentos) && $v->descuentos->monto > 0) {
                                
                            $xml.='<cac:AllowanceCharge>
                                    <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                                    <cbc:AllowanceChargeReasonCode>'.$v->descuentos->codigoTipo.'</cbc:AllowanceChargeReasonCode>
                                    <cbc:MultiplierFactorNumeric>'.$v->descuentos->factor.'</cbc:MultiplierFactorNumeric>
                                    <cbc:Amount currencyID="'.$comprobante->tipoMoneda.'">'.$v->descuentos->monto.'</cbc:Amount>
                                    <cbc:BaseAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->descuentos->montoBase.'</cbc:BaseAmount>
                                </cac:AllowanceCharge>';
                            
                            }

                            //  ==================TOTALES================= ";
                            $xml.='<cac:TaxTotal>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->igvOpi.'</cbc:TaxAmount>
                                    <cac:TaxSubtotal>
                                        <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoBaseIgv.'</cbc:TaxableAmount>
                                        <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->igv.'</cbc:TaxAmount>
                                        <cac:TaxCategory>
                                            <cbc:Percent>'.$v->igvPorcent.'</cbc:Percent>
                                            <cbc:TaxExemptionReasonCode>'.$v->codeAfectAlt.'</cbc:TaxExemptionReasonCode>
                                            <cac:TaxScheme>
                                                <cbc:ID>'.$v->codeAfect.'</cbc:ID>
                                                <cbc:Name>'.$v->nameAfect.'</cbc:Name>
                                                <cbc:TaxTypeCode>'.$v->tipoAfect.'</cbc:TaxTypeCode>
                                            </cac:TaxScheme>
                                        </cac:TaxCategory>
                                    </cac:TaxSubtotal>';

                            //  ==================ICBPER================= ";
                            if(isset($v->icbper) && $v->icbper > 0) {
                                
                            $xml.='<cac:TaxSubtotal>
                                    <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->icbper.'</cbc:TaxAmount>
                                    <cbc:BaseUnitMeasure unitCode="NIU">'.$v->cantidad.'</cbc:BaseUnitMeasure>
                                    <cac:TaxCategory>
                                        <cbc:Percent>0.00</cbc:Percent>
                                        <cbc:PerUnitAmount currencyID="'.$comprobante->tipoMoneda.'">0.50</cbc:PerUnitAmount>
                                        <cac:TaxScheme>
                                            <cbc:ID>7152</cbc:ID>
                                            <cbc:Name>ICBPER</cbc:Name>
                                            <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                    </cac:TaxCategory>
                                </cac:TaxSubtotal>';
                            
                            }
                            
                            $xml.='</cac:TaxTotal>
                                    <cac:Item>
                                        <cbc:Description><![CDATA['.$v->descripcion.']]></cbc:Description>
                                        <cac:SellersItemIdentification>
                                            <cbc:ID>'.$v->codProducto.'</cbc:ID>
                                        </cac:SellersItemIdentification>
                                    </cac:Item>
                                    <cac:Price>
                                        <cbc:PriceAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoValorUnitario.'</cbc:PriceAmount>
                                    </cac:Price>
                                </cac:InvoiceLine>';
                            }
                        
        $xml.="</Invoice>";
        
        $doc->loadXML($xml);
        $doc->save($nombrexml . '.XML');
                    
    }
    
    /*=============================================
    Generamos xml Nota de credito
    =============================================*/
    function CrearXMLNotaCredito($nombrexml, $emisor, $cliente, $comprobante, $detalle)
    {
      /*=============================================
      Decodificamos los datos de la empresa
      =============================================*/
      $emisor = json_decode($emisor);

      $doc = new \DOMDocument();
      $doc->formatOutput = FALSE;
      $doc->preserveWhiteSpace = TRUE;
      $doc->encoding = 'utf-8';

      /* Convertimos el total a texto */
      $decimales = explode(".", number_format($comprobante->total, 2));
      $entera = explode(".", $comprobante->total);
      $totalTexto = ControladorRutas::convertir($entera[0]) . ' CON ' . $decimales[1] . '/100 SOLES';

      $xml = '<?xml version="1.0" encoding="UTF-8"?>
      <CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
         <ext:UBLExtensions>
            <ext:UBLExtension>
               <ext:ExtensionContent />
            </ext:UBLExtension>
         </ext:UBLExtensions>
         <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
         <cbc:CustomizationID>2.0</cbc:CustomizationID>
         <cbc:ID>'.$comprobante->serie.'-'.$comprobante->correlativo.'</cbc:ID>
         <cbc:IssueDate>'.$comprobante->fechaEmision.'</cbc:IssueDate>
         <cbc:IssueTime>00:00:01</cbc:IssueTime>
         <cbc:Note languageLocaleID="1000"><![CDATA['.$totalTexto.']]></cbc:Note>
         <cbc:DocumentCurrencyCode>'.$comprobante->tipoMoneda.'</cbc:DocumentCurrencyCode>
         <cac:DiscrepancyResponse>
            <cbc:ReferenceID>'.$comprobante->serieRef.'-'.$comprobante->correlativoRef.'</cbc:ReferenceID>
            <cbc:ResponseCode>'.$comprobante->codmotivo.'</cbc:ResponseCode>
            <cbc:Description>'.$comprobante->descripcion.'</cbc:Description>
         </cac:DiscrepancyResponse>
         <cac:BillingReference>
            <cac:InvoiceDocumentReference>
               <cbc:ID>'.$comprobante->serieRef.'-'.$comprobante->correlativoRef.'</cbc:ID>
               <cbc:DocumentTypeCode>'.$comprobante->tipoCompRef.'</cbc:DocumentTypeCode>
            </cac:InvoiceDocumentReference>
         </cac:BillingReference>
         <cac:Signature>
            <cbc:ID>'.$emisor->ruc.'</cbc:ID>
            <cbc:Note><![CDATA['.$emisor->nombreComercial.']]></cbc:Note>
            <cac:SignatoryParty>
               <cac:PartyIdentification>
                  <cbc:ID>'.$emisor->ruc.'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
               </cac:PartyName>
            </cac:SignatoryParty>
            <cac:DigitalSignatureAttachment>
               <cac:ExternalReference>
                  <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
               </cac:ExternalReference>
            </cac:DigitalSignatureAttachment>
         </cac:Signature>
         <cac:AccountingSupplierParty>
            <cac:Party>
               <cac:PartyIdentification>
                  <cbc:ID schemeID="'.$emisor->tipoDoc.'">'.$emisor->ruc.'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor->nombreComercial.']]></cbc:Name>
               </cac:PartyName>
               <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                  <cac:RegistrationAddress>
                     <cbc:ID>'.$emisor->address->ubigeo.'</cbc:ID>
                     <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                     <cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>
                     <cbc:CityName>'.$emisor->address->provincia.'</cbc:CityName>
                     <cbc:CountrySubentity>'.$emisor->address->departamento.'</cbc:CountrySubentity>
                     <cbc:District>'.$emisor->address->distrito.'</cbc:District>
                     <cac:AddressLine>
                        <cbc:Line><![CDATA['.$emisor->address->direccion.']]></cbc:Line>
                     </cac:AddressLine>
                     <cac:Country>
                        <cbc:IdentificationCode>'.$emisor->address->codigoPais.'</cbc:IdentificationCode>
                     </cac:Country>
                  </cac:RegistrationAddress>
               </cac:PartyLegalEntity>
            </cac:Party>
         </cac:AccountingSupplierParty>
         <cac:AccountingCustomerParty>';

           
            $xml.='<cac:Party>
               <cac:PartyIdentification>
                  <cbc:ID schemeID="'.$cliente->tipoDoc.'">'.$cliente->numDoc.'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA['.$cliente->rznSocial.']]></cbc:RegistrationName>
                  <cac:RegistrationAddress>
                     <cac:AddressLine>
                        <cbc:Line><![CDATA['.$cliente->direccion.']]></cbc:Line>
                     </cac:AddressLine>
                     <cac:Country>
                        <cbc:IdentificationCode>'.$cliente->codigoPais.'</cbc:IdentificationCode>
                     </cac:Country>
                  </cac:RegistrationAddress>
               </cac:PartyLegalEntity>
            </cac:Party>
         </cac:AccountingCustomerParty>';


         $igv = round($comprobante->mtoIGV + $comprobante->icbper,2);
         $xml.='<cac:TaxTotal>
             <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$igv.'</cbc:TaxAmount>';
             
             if($comprobante->mtoOperGravadas > 0) {
                $xml.='<cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperGravadas.'</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoIGV.'</cbc:TaxAmount>
                <cac:TaxCategory>
                   <cac:TaxScheme>
                      <cbc:ID>1000</cbc:ID>
                      <cbc:Name>IGV</cbc:Name>
                      <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                   </cac:TaxScheme>
                </cac:TaxCategory>
             </cac:TaxSubtotal>';
           }
           if($comprobante->icbper > 0){
              $xml .= '  <cac:TaxSubtotal>
              <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->icbper.'</cbc:TaxAmount>
              <cac:TaxCategory>
                <cac:TaxScheme>
                  <cbc:ID schemeAgencyName="PE:SUNAT" schemeName="Codigo de tributos" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo05">7152</cbc:ID>
                  <cbc:Name>ICBPER</cbc:Name>
                  <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                </cac:TaxScheme>
              </cac:TaxCategory>
            </cac:TaxSubtotal>';
           }
       
             if($comprobante->mtoOperExoneradas>0){
                $xml.='<cac:TaxSubtotal>
                   <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperExoneradas.'</cbc:TaxableAmount>
                   <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">0.00</cbc:TaxAmount>
                   <cac:TaxCategory>
                      <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                      <cac:TaxScheme>
                         <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                         <cbc:Name>EXO</cbc:Name>
                         <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                      </cac:TaxScheme>
                   </cac:TaxCategory>
                </cac:TaxSubtotal>';
             }
       
             if($comprobante->mtoOperInafectas>0){
                $xml.='<cac:TaxSubtotal>
                   <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperInafectas.'</cbc:TaxableAmount>
                   <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">0.00</cbc:TaxAmount>
                   <cac:TaxCategory>
                      <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                      <cac:TaxScheme>
                         <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                         <cbc:Name>INA</cbc:Name>
                         <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                      </cac:TaxScheme>
                   </cac:TaxCategory>
                </cac:TaxSubtotal>';
             }
             if($comprobante->mtoOperGratuitas>0){
                $xml.='<cac:TaxSubtotal>
                   <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperGratuitas.'</cbc:TaxableAmount>
                   <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->igvOp.'</cbc:TaxAmount>
                   <cac:TaxCategory>
                      <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                      <cac:TaxScheme>
                         <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                         <cbc:Name>GRA</cbc:Name>
                         <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                      </cac:TaxScheme>
                   </cac:TaxCategory>
                </cac:TaxSubtotal>';
             }
            
             $total_antes_de_impuestos = round($comprobante->mtoOperGravadas+$comprobante->mtoOperExoneradas+$comprobante->mtoOperInafectas,2);
        
             $xml.='</cac:TaxTotal>
             <cac:LegalMonetaryTotal>
                <cbc:LineExtensionAmount currencyID="'.$comprobante->tipoMoneda.'">'.$total_antes_de_impuestos.'</cbc:LineExtensionAmount>
                <cbc:TaxInclusiveAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->total.'</cbc:TaxInclusiveAmount>
                <cbc:PayableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->total.'</cbc:PayableAmount>
             </cac:LegalMonetaryTotal>';

             $nItem = 1;
         foreach($detalle as $k=>$v){

            $xml.='<cac:CreditNoteLine>
               <cbc:ID>'.$nItem++.'</cbc:ID>
               <cbc:CreditedQuantity unitCode="'.$v->unidad.'">'.$v->cantidad.'</cbc:CreditedQuantity>
               <cbc:LineExtensionAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoValorUnitario.'</cbc:LineExtensionAmount>
               <cac:PricingReference>
                  <cac:AlternativeConditionPrice>
                     <cbc:PriceAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoPrecioUnitario.'</cbc:PriceAmount>
                     <cbc:PriceTypeCode>'.$v->tipoPrecio.'</cbc:PriceTypeCode>
                  </cac:AlternativeConditionPrice>
               </cac:PricingReference>';

               // if($v->descuentos->monto > 0):

               //    //  ==================DESCUENTO POR ITEMS=================  
   
               //      $xml.='<cac:AllowanceCharge>
               //       <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
               //       <cbc:AllowanceChargeReasonCode>'.$v->descuentos['codigoTipo'].'</cbc:AllowanceChargeReasonCode>
               //       <cbc:MultiplierFactorNumeric>'.$v->descuentos['factor'].'</cbc:MultiplierFactorNumeric>
               //       <cbc:Amount currencyID="'.$comprobante->tipoMoneda.'">'.$v->descuentos->monto.'</cbc:Amount>
               //       <cbc:BaseAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->descuentos['montoBase'].'</cbc:BaseAmount>
               //   </cac:AllowanceCharge>';
   
               //    //  ==================FIN DE DESCUENTO POR ITEMS=================  
               //       endif;


              $xml.='<cac:TaxTotal>
                     <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->igvOpi.'</cbc:TaxAmount>
                     <cac:TaxSubtotal>
                        <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoValorUnitario.'</cbc:TaxableAmount>
                        <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->igv.'</cbc:TaxAmount>
                        <cac:TaxCategory>
                           <cbc:Percent>'.$v->igvPorcent.'</cbc:Percent>
                           <cbc:TaxExemptionReasonCode>'.$v->codeAfectAlt.'</cbc:TaxExemptionReasonCode>
                           <cac:TaxScheme>
                              <cbc:ID>'.$v->codeAfect.'</cbc:ID>
                              <cbc:Name>'.$v->nameAfect.'</cbc:Name>
                              <cbc:TaxTypeCode>'.$v->tipoAfect.'</cbc:TaxTypeCode>
                           </cac:TaxScheme>
                        </cac:TaxCategory>
                     </cac:TaxSubtotal>';

                     if($v->icbper > 0):

                     $xml.='<cac:TaxSubtotal>
                     <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->icbper.'</cbc:TaxAmount>
                     <cbc:BaseUnitMeasure unitCode="NIU">'.$v->cantidad.'</cbc:BaseUnitMeasure>
                     <cac:TaxCategory>
                       <cbc:Percent>0.00</cbc:Percent>
                       <cbc:PerUnitAmount currencyID="'.$comprobante->tipoMoneda.'">0.30</cbc:PerUnitAmount>
                       <cac:TaxScheme>
                         <cbc:ID>7152</cbc:ID>
                         <cbc:Name>ICBPER</cbc:Name>
                         <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                       </cac:TaxScheme>
                     </cac:TaxCategory>
                   </cac:TaxSubtotal>';

                     endif;

              $xml.='</cac:TaxTotal>
                 <cac:Item>
                  <cbc:Description><![CDATA['.$v->descripcion.']]></cbc:Description>
                  <cac:SellersItemIdentification>
                     <cbc:ID>'.$v->codProducto.'</cbc:ID>
                  </cac:SellersItemIdentification>
               </cac:Item>
               <cac:Price>
                  <cbc:PriceAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoValorUnitario.'</cbc:PriceAmount>
               </cac:Price>
            </cac:CreditNoteLine>';
         }
         $xml.='</CreditNote>';

      $doc->loadXML($xml);
      $doc->save($nombrexml.'.XML');
    
    }
    
    /*=============================================
    Generamos xml Nota de debito
    =============================================*/
    function CrearXMLNotaDebito($nombrexml, $emisor, $cliente, $comprobante, $detalle)
    {

      /*=============================================
      Decodificamos los datos de la empresa
      =============================================*/
      $emisor = json_decode($emisor);

      $doc = new \DOMDocument();
      $doc->formatOutput = FALSE;
      $doc->preserveWhiteSpace = TRUE;
      $doc->encoding = 'utf-8';

      /* Convertimos el total a texto */
      $decimales = explode(".", number_format($comprobante->total, 2));
      $entera = explode(".", $comprobante->total);
      $totalTexto = ControladorRutas::convertir($entera[0]) . ' CON ' . $decimales[1] . '/100 SOLES';

      $xml = '<?xml version="1.0" encoding="UTF-8"?>
      <DebitNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
         <ext:UBLExtensions>
            <ext:UBLExtension>
               <ext:ExtensionContent />
            </ext:UBLExtension>
         </ext:UBLExtensions>
         <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
         <cbc:CustomizationID>2.0</cbc:CustomizationID>
         <cbc:ID>'.$comprobante->serie.'-'.$comprobante->correlativo.'</cbc:ID>
         <cbc:IssueDate>'.$comprobante->fechaEmision.'</cbc:IssueDate>
         <cbc:IssueTime>00:00:03</cbc:IssueTime>
         <cbc:Note languageLocaleID="1000"><![CDATA['.$totalTexto.']]></cbc:Note>
         <cbc:DocumentCurrencyCode>'.$comprobante->tipoMoneda.'</cbc:DocumentCurrencyCode>
         <cac:DiscrepancyResponse>
            <cbc:ReferenceID>'.$comprobante->serieRef.'-'.$comprobante->correlativoRef.'</cbc:ReferenceID>
            <cbc:ResponseCode>'.$comprobante->codmotivo.'</cbc:ResponseCode>
            <cbc:Description>'.$comprobante->descripcion.'</cbc:Description>
         </cac:DiscrepancyResponse>
         <cac:BillingReference>
            <cac:InvoiceDocumentReference>
               <cbc:ID>'.$comprobante->serieRef.'-'.$comprobante->correlativoRef.'</cbc:ID>
               <cbc:DocumentTypeCode>'.$comprobante->tipoCompRef.'</cbc:DocumentTypeCode>
            </cac:InvoiceDocumentReference>
         </cac:BillingReference>
         <cac:Signature>
            <cbc:ID>'.$emisor->ruc.'</cbc:ID>
            <cbc:Note><![CDATA['.$emisor->nombreComercial.']]></cbc:Note>
            <cac:SignatoryParty>
               <cac:PartyIdentification>
                  <cbc:ID>'.$emisor->ruc.'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
               </cac:PartyName>
            </cac:SignatoryParty>
            <cac:DigitalSignatureAttachment>
               <cac:ExternalReference>
                  <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
               </cac:ExternalReference>
            </cac:DigitalSignatureAttachment>
         </cac:Signature>
         <cac:AccountingSupplierParty>
            <cac:Party>
               <cac:PartyIdentification>
                  <cbc:ID schemeID="'.$emisor->tipoDoc.'">'.$emisor->ruc.'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor->nombreComercial.']]></cbc:Name>
               </cac:PartyName>
               <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                  <cac:RegistrationAddress>
                     <cbc:ID>'.$emisor->address->ubigeo.'</cbc:ID>
                     <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                     <cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>
                     <cbc:CityName>'.$emisor->address->provincia.'</cbc:CityName>
                     <cbc:CountrySubentity>'.$emisor->address->departamento.'</cbc:CountrySubentity>
                     <cbc:District>'.$emisor->address->distrito.'</cbc:District>
                     <cac:AddressLine>
                        <cbc:Line><![CDATA['.$emisor->address->direccion.']]></cbc:Line>
                     </cac:AddressLine>
                     <cac:Country>
                        <cbc:IdentificationCode>'.$emisor->address->codigoPais.'</cbc:IdentificationCode>
                     </cac:Country>
                  </cac:RegistrationAddress>
               </cac:PartyLegalEntity>
            </cac:Party>
         </cac:AccountingSupplierParty>
            <cac:AccountingCustomerParty>
            <cac:Party>
               <cac:PartyIdentification>
                  <cbc:ID schemeID="'.$cliente->tipoDoc.'">'.$cliente->numDoc.'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA['.$cliente->rznSocial.']]></cbc:RegistrationName>
                  <cac:RegistrationAddress>
                     <cac:AddressLine>
                        <cbc:Line><![CDATA['.$cliente->direccion.']]></cbc:Line>
                     </cac:AddressLine>
                     <cac:Country>
                        <cbc:IdentificationCode>'.$cliente->codigoPais.'</cbc:IdentificationCode>
                     </cac:Country>
                  </cac:RegistrationAddress>
               </cac:PartyLegalEntity>
            </cac:Party>
         </cac:AccountingCustomerParty>
         <cac:TaxTotal>
            <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoIGV.'</cbc:TaxAmount>
            <cac:TaxSubtotal>
               <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperGravadas.'</cbc:TaxableAmount>
               <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoIGV.'</cbc:TaxAmount>
               <cac:TaxCategory>
                  <cac:TaxScheme>
                     <cbc:ID>1000</cbc:ID>
                     <cbc:Name>IGV</cbc:Name>
                     <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                  </cac:TaxScheme>
               </cac:TaxCategory>
            </cac:TaxSubtotal>';
            
            if($comprobante->mtoOperExoneradas>0){
               $xml.='<cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperExoneradas.'</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">0.00</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                        <cbc:Name>EXO</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
            }

            if($comprobante->mtoOperInafectas>0){
               $xml.='<cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->mtoOperInafectas.'</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">0.00</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                        <cbc:Name>INA</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
            }
            
         $xml .= '</cac:TaxTotal>
         <cac:RequestedMonetaryTotal>
            <cbc:PayableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->total.'</cbc:PayableAmount>
         </cac:RequestedMonetaryTotal>';
         
         $nItem = 1;
         foreach($detalle as $k=>$v){

            $xml.='<cac:DebitNoteLine>
               <cbc:ID>'.$nItem++.'</cbc:ID>
               <cbc:DebitedQuantity unitCode="'.$v->unidad.'">'.$v->cantidad.'</cbc:DebitedQuantity>
               <cbc:LineExtensionAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoValorUnitario.'</cbc:LineExtensionAmount>
               <cac:PricingReference>
                  <cac:AlternativeConditionPrice>
                     <cbc:PriceAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoPrecioUnitario.'</cbc:PriceAmount>
                     <cbc:PriceTypeCode>'.$v->tipoPrecio.'</cbc:PriceTypeCode>
                  </cac:AlternativeConditionPrice>
               </cac:PricingReference>
               <cac:TaxTotal>
                  <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->igv.'</cbc:TaxAmount>
                  <cac:TaxSubtotal>
                     <cbc:TaxableAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoValorUnitario.'</cbc:TaxableAmount>
                     <cbc:TaxAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->igv.'</cbc:TaxAmount>
                     <cac:TaxCategory>
                        <cbc:Percent>'.$v->igvPorcent.'</cbc:Percent>
                        <cbc:TaxExemptionReasonCode>'.$v->codeAfectAlt.'</cbc:TaxExemptionReasonCode>
                        <cac:TaxScheme>
                           <cbc:ID>'.$v->codeAfect.'</cbc:ID>
                           <cbc:Name>'.$v->nameAfect.'</cbc:Name>
                           <cbc:TaxTypeCode>'.$v->tipoAfect.'</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                     </cac:TaxCategory>
                  </cac:TaxSubtotal>
               </cac:TaxTotal>
               <cac:Item>
                  <cbc:Description><![CDATA['.$v->descripcion.']]></cbc:Description>
                  <cac:SellersItemIdentification>
                     <cbc:ID>'.$v->codProducto.'</cbc:ID>
                  </cac:SellersItemIdentification>
               </cac:Item>
               <cac:Price>
                  <cbc:PriceAmount currencyID="'.$comprobante->tipoMoneda.'">'.$v->mtoValorUnitario.'</cbc:PriceAmount>
               </cac:Price>
            </cac:DebitNoteLine>';
         
         }

            $xml.='</DebitNote>';

            $doc->loadXML($xml);
            $doc->save($nombrexml.'.XML');
        
    }
    
    /*=============================================
    Generamos xml Resumen diario
    =============================================*/
    function CrearXMLResumenDocumentos($emisor, $cabecera, $detalle, $nombrexml)
    {
      
      /*=============================================
      Decodificamos los datos de la empresa
      =============================================*/
      $emisor = json_decode($emisor);

      $doc = new \DOMDocument();
      $doc->formatOutput = FALSE;
      $doc->preserveWhiteSpace = TRUE;
      $doc->encoding = 'utf-8';   

      $xml = '<?xml version="1.0" encoding="UTF-8"?>
         <SummaryDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2">
          <ext:UBLExtensions>
              <ext:UBLExtension>
                  <ext:ExtensionContent />
              </ext:UBLExtension>
          </ext:UBLExtensions>
          <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
          <cbc:CustomizationID>1.1</cbc:CustomizationID>
          <cbc:ID>'.$cabecera->tipodoc.'-'.$cabecera->serie.'-'.$cabecera->correlativo.'</cbc:ID>
          <cbc:ReferenceDate>'.$cabecera->fechaEmision.'</cbc:ReferenceDate>
          <cbc:IssueDate>'.$cabecera->fechaEnvio.'</cbc:IssueDate>
          <cac:Signature>
              <cbc:ID>'.$cabecera->tipodoc.'-'.$cabecera->serie.'-'.$cabecera->correlativo.'</cbc:ID>
              <cac:SignatoryParty>
                  <cac:PartyIdentification>
                      <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                  </cac:PartyIdentification>
                  <cac:PartyName>
                      <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                  </cac:PartyName>
              </cac:SignatoryParty>
              <cac:DigitalSignatureAttachment>
                  <cac:ExternalReference>
                      <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
                  </cac:ExternalReference>
              </cac:DigitalSignatureAttachment>
          </cac:Signature>
          <cac:AccountingSupplierParty>
              <cbc:CustomerAssignedAccountID>'.$emisor->ruc.'</cbc:CustomerAssignedAccountID>
              <cbc:AdditionalAccountID>'.$emisor->tipoDoc.'</cbc:AdditionalAccountID>
              <cac:Party>
                  <cac:PartyLegalEntity>
                      <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                  </cac:PartyLegalEntity>
              </cac:Party>
          </cac:AccountingSupplierParty>';
  
          $item = 1;
          foreach ($detalle as $k => $v) {
             $xml.='<sac:SummaryDocumentsLine>
                 <cbc:LineID>'.$item++.'</cbc:LineID>
                 <cbc:DocumentTypeCode>'.$v->tipodoc.'</cbc:DocumentTypeCode>
                 <cbc:ID>'.$v->serie.'-'.$v->correlativo.'</cbc:ID>
                 <cac:AccountingCustomerParty>
                  <cbc:CustomerAssignedAccountID>'.$v->numdoc.'</cbc:CustomerAssignedAccountID>
                  <cbc:AdditionalAccountID>'.$v->coddoc.'</cbc:AdditionalAccountID>
                  </cac:AccountingCustomerParty>
                 <cac:Status>
                    <cbc:ConditionCode>'.$v->condicion.'</cbc:ConditionCode>
                 </cac:Status> 

                 <sac:TotalAmount currencyID="'.$v->moneda.'">'.$v->importe_total.'</sac:TotalAmount>';
                if($v->op_gravadas > 0){
                $xml.='<sac:BillingPayment>
                           <cbc:PaidAmount currencyID="'.$v->moneda.'">'.$v->op_gravadas.'</cbc:PaidAmount>
                           <cbc:InstructionID>01</cbc:InstructionID>
                       </sac:BillingPayment>';
                  }
                if($v->op_exoneradas > 0){
                $xml.='<sac:BillingPayment>
                           <cbc:PaidAmount currencyID="'.$v->moneda.'">'.$v->op_exoneradas.'</cbc:PaidAmount>
                           <cbc:InstructionID>02</cbc:InstructionID>
                       </sac:BillingPayment>';
                  }
                if($v->op_inafectas > 0){
                $xml.='<sac:BillingPayment>
                           <cbc:PaidAmount currencyID="'.$v->moneda.'">'.$v->op_inafectas.'</cbc:PaidAmount>
                           <cbc:InstructionID>03</cbc:InstructionID>
                       </sac:BillingPayment>';
                  }
                if($v->op_gratuitas > 0){
                $xml.='<sac:BillingPayment>
                           <cbc:PaidAmount currencyID="'.$v->moneda.'">'.$v->op_gratuitas.'</cbc:PaidAmount>
                           <cbc:InstructionID>05</cbc:InstructionID>
                       </sac:BillingPayment>';
                  }
                     
                     $xml.='<cac:TaxTotal>
                     <cbc:TaxAmount currencyID="'.$v->moneda.'">'.$v->igv_total.'</cbc:TaxAmount>';

                     
                     if($v->codeAfect!='1000'){
                     $xml.='<cac:TaxSubtotal>
                         <cbc:TaxAmount currencyID="'.$v->moneda.'">'.$v->igv_total.'</cbc:TaxAmount>
                         <cac:TaxCategory>
                             <cac:TaxScheme>
                                 <cbc:ID>'.$v->codeAfect.'</cbc:ID>
                                 <cbc:Name>'.$v->nameAfect.'</cbc:Name>
                                 <cbc:TaxTypeCode>'.$v->tipoAfect.'</cbc:TaxTypeCode>
                             </cac:TaxScheme>
                         </cac:TaxCategory>
                     </cac:TaxSubtotal>';
                    }
  
                     $xml.='<cac:TaxSubtotal>
                         <cbc:TaxAmount currencyID="'.$v->moneda.'">'.$v->igv_total.'</cbc:TaxAmount>
                         <cac:TaxCategory>
                             <cac:TaxScheme>
                                 <cbc:ID>1000</cbc:ID>
                                 <cbc:Name>IGV</cbc:Name>
                                 <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                             </cac:TaxScheme>
                         </cac:TaxCategory>
                     </cac:TaxSubtotal>';
  
                 $xml.='</cac:TaxTotal>
             </sac:SummaryDocumentsLine>';
          }
          
        $xml.='</SummaryDocuments>';
  
        $doc->loadXML($xml);
        $doc->save($nombrexml.'.XML');
    
    }
    
    /*=============================================
    Generamos xml Comunicacion de baja
    =============================================*/
    function CrearXmlBajaDocumentos($emisor, $cabecera, $detalle, $nombrexml)
    {

      /*=============================================
      Decodificamos los datos de la empresa
      =============================================*/
      $emisor = json_decode($emisor);

      $doc = new \DOMDocument();
      $doc->formatOutput = FALSE;
      $doc->preserveWhiteSpace = TRUE;
      $doc->encoding = 'utf-8';   

      $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <VoidedDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
          <ext:UBLExtensions>
              <ext:UBLExtension>
                  <ext:ExtensionContent />
              </ext:UBLExtension>
          </ext:UBLExtensions>
          <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
          <cbc:CustomizationID>1.0</cbc:CustomizationID>
          <cbc:ID>'.$cabecera->tipodoc.'-'.$cabecera->serie.'-'.$cabecera->correlativo.'</cbc:ID>
          <cbc:ReferenceDate>'.$cabecera->fechaEmision.'</cbc:ReferenceDate>
          <cbc:IssueDate>'.$cabecera->fechaEnvio.'</cbc:IssueDate>
          <cac:Signature>
              <cbc:ID>'.$cabecera->tipodoc.'-'.$cabecera->serie.'-'.$cabecera->correlativo.'</cbc:ID>
              <cac:SignatoryParty>
                  <cac:PartyIdentification>
                      <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                  </cac:PartyIdentification>
                  <cac:PartyName>
                      <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                  </cac:PartyName>
              </cac:SignatoryParty>
              <cac:DigitalSignatureAttachment>
                  <cac:ExternalReference>
                      <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
                  </cac:ExternalReference>
              </cac:DigitalSignatureAttachment>
          </cac:Signature>
          <cac:AccountingSupplierParty>
              <cbc:CustomerAssignedAccountID>'.$emisor->ruc.'</cbc:CustomerAssignedAccountID>
              <cbc:AdditionalAccountID>'.$emisor->tipoDoc.'</cbc:AdditionalAccountID>
              <cac:Party>
                  <cac:PartyLegalEntity>
                      <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                  </cac:PartyLegalEntity>
              </cac:Party>
          </cac:AccountingSupplierParty>';
  
          $item = 1;
          foreach ($detalle as $k => $v) {
             $xml.='<sac:VoidedDocumentsLine>
                 <cbc:LineID>'.$item++.'</cbc:LineID>
                 <cbc:DocumentTypeCode>'.$v->tipodoc.'</cbc:DocumentTypeCode>
                 <sac:DocumentSerialID>'.$v->serie.'</sac:DocumentSerialID>
                 <sac:DocumentNumberID>'.$v->correlativo.'</sac:DocumentNumberID>
                 <sac:VoidReasonDescription><![CDATA['.$v->motivo.']]></sac:VoidReasonDescription>
             </sac:VoidedDocumentsLine>';
          }
          
        $xml.='</VoidedDocuments>';
  
        $doc->loadXML($xml);
        $doc->save($nombrexml.'.XML');
    
    }
    
    /*=============================================
    Generamos Guia de remision remitente
    =============================================*/
    function CrearXMLGuiaRemision($nombrexml, $emisor, $datosGuia, $datosEnvio, $detalle)
    {
   
        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        $doc = new \DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'utf-8';
        
        $xml = '<?xml version="1.0" encoding="utf-8" standalone="no"?>
                    <DespatchAdvice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                        xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
                        xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
                        xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
                        xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
                        xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2">
            <ext:UBLExtensions>
                <ext:UBLExtension>
                    <ext:ExtensionContent/>
                </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
            <cbc:CustomizationID>2.0</cbc:CustomizationID>
            <cbc:ID>'.$datosGuia->serie.'-'.$datosGuia->correlativo.'</cbc:ID>
            <cbc:IssueDate>'.$datosGuia->fechaEmision.'</cbc:IssueDate>
            <cbc:IssueTime>'.$datosGuia->horaEmision.'</cbc:IssueTime>
            <cbc:DespatchAdviceTypeCode listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01"
                                        listName="Tipo de Documento" listAgencyName="PE:SUNAT">'.$datosGuia->tipoDoc.'</cbc:DespatchAdviceTypeCode>';


            if ($datosGuia->observacion != ''):
                $xml.= 'cbc:Note><![CDATA['.$datosGuia->observacion.']]></cbc:Note>';
            endif;

            $xml.= '<cac:Signature>
                <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                <cbc:Note>'.$emisor->nombreComercial.'</cbc:Note>
                <cac:SignatoryParty>
                    <cac:PartyIdentification>
                        <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                    </cac:PartyName>
                </cac:SignatoryParty>
                <cac:DigitalSignatureAttachment>
                    <cac:ExternalReference>
                        <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
                    </cac:ExternalReference>
                </cac:DigitalSignatureAttachment>
            </cac:Signature>';

            if (isset($datosGuia->docBaja) && $datosGuia->docBaja->nroDoc != ''):
                $xml.= '<cac:OrderReference>
                    <cbc:ID>'.$datosGuia->docBaja->nroDoc.'</cbc:ID>
                    <cbc:OrderTypeCode listAgencyName="PE:SUNAT" listName="SUNAT:Identificador de Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">'.$datosGuia->docBaja->tipoDoc.'</cbc:OrderTypeCode>
                </cac:OrderReference>';
                endif;
            
            if (isset($datosGuia->relDoc) && $datosGuia->relDoc->nroDoc != ''):
                $xml.= '<cac:AdditionalDocumentReference>
                    <cbc:ID>'.$datosGuia->relDoc->nroDoc.'</cbc:ID>
                    <cbc:DocumentTypeCode listAgencyName="PE:SUNAT" listName="SUNAT:Identificador de documento relacionado" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo21">'.$datosGuia->relDoc->tipoDoc.'</cbc:DocumentTypeCode>
                    <cbc:DocumentType>Factura</cbc:DocumentType>
                    <cac:IssuerParty>
                    <cac:PartyIdentification>
                    <cbc:ID schemeID="6" schemeAgencyName="PE:SUNAT" schemeName="Documento de Identidad" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$datosGuia->remitente->ruc.'</cbc:ID>
                    </cac:PartyIdentification>
                    </cac:IssuerParty>
                </cac:AdditionalDocumentReference>';
            endif;

            $xml.= '<cac:DespatchSupplierParty>
                <cac:Party>
                    <cac:PartyIdentification>
                        <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                schemeAgencyName="PE:SUNAT"
                                schemeName="Documento de Identidad"
                                schemeID="6">'.$emisor->ruc.'</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:DespatchSupplierParty>

            <cac:DeliveryCustomerParty>
                <cac:Party>
                    <cac:PartyIdentification>
                        <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                schemeAgencyName="PE:SUNAT"
                                schemeName="Documento de Identidad"
                                schemeID="'.$datosGuia->destinatario->tipoDoc.'">'.$datosGuia->destinatario->numDoc.'</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA['.$datosGuia->destinatario->nombreRazon.']]></cbc:RegistrationName>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:DeliveryCustomerParty>';

            if (isset($datosGuia->terceros) && $datosGuia->terceros->tipoDoc != ''):
                $xml.= '<cac:SellerSupplierParty>
                    <cbc:CustomerAssignedAccountID schemeID="'.$datosGuia->terceros->tipoDoc.'">'.$datosGuia->terceros->numDoc.'</cbc:CustomerAssignedAccountID>
                    <cac:Party>
                        <cac:PartyLegalEntity>
                            <cbc:RegistrationName><![CDATA['.$datosGuia->terceros->nombreRazon.']]></cbc:RegistrationName>
                        </cac:PartyLegalEntity>
                    </cac:Party>
                </cac:SellerSupplierParty>';
            endif;

            $xml.= '<cac:Shipment>
                <!-- ID OBLIGATORIO POR UBL -->
                <cbc:ID>1</cbc:ID>
                <!-- MOTIVO DEL TRASLADO -->
                <cbc:HandlingCode listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo20"
                                listName="Motivo de traslado"
                                listAgencyName="PE:SUNAT">'.$datosEnvio->codTraslado.'</cbc:HandlingCode>';

                //if($datosEnvio->descTraslado != ''):
                    //$xml.= '<cbc:Information>'.$datosEnvio->descTraslado.'</cbc:Information>';
                //endif;

                $xml.= '<cbc:HandlingInstructions>'.$datosEnvio->descTraslado.'</cbc:HandlingInstructions>
                <!-- PESO BRUTO TOTAL DE LA CARGA-->';

                $xml.= '<cbc:GrossWeightMeasure
                    unitCode="'.$datosEnvio->uniPesoTotal.'">'.$datosEnvio->pesoTotal.'</cbc:GrossWeightMeasure>';

                if ($datosEnvio->numBultos > 0):
                    $xml.='<cbc:TotalTransportHandlingUnitQuantity>'.$datosEnvio->numBultos.'</cbc:TotalTransportHandlingUnitQuantity>';
                endif;

                $xml.= '<cac:ShipmentStage>
                    <!-- MODALIDAD DE TRASLADO -->
                    <cbc:TransportModeCode listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo18"
                                        listName="Modalidad de traslado"
                                        listAgencyName="PE:SUNAT">'.$datosEnvio->modTraslado.'</cbc:TransportModeCode>
                    <!-- FECHA DE INICIO DEL TRASLADO o FECHA DE ENTREGA DE BIENES AL TRANSPORTISTA -->
                    <cac:TransitPeriod>
                        <cbc:StartDate>'.$datosEnvio->fechaTraslado.'</cbc:StartDate>
                    </cac:TransitPeriod>';

                    if($datosEnvio->modTraslado == '01'):
                        $xml.= '<cac:CarrierParty>
                            <cac:PartyIdentification>
                                <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                        schemeAgencyName="PE:SUNAT"
                                        schemeName="Documento de Identidad"
                                        schemeID="'.$datosGuia->transportista->tipoDoc.'">'.$datosGuia->transportista->numDoc.'</cbc:ID>
                            </cac:PartyIdentification>
                            <cac:PartyLegalEntity>
                                <!-- NOMBRE/RAZON SOCIAL DEL TRANSPORTISTA-->
                                <cbc:RegistrationName><![CDATA['.$datosGuia->transportista->nombreRazon.']]></cbc:RegistrationName>
                                <!-- NUMERO DE REGISTRO DEL MTC -->
                                <cbc:CompanyID>'.$datosGuia->transportista->mtc.'</cbc:CompanyID>
                            </cac:PartyLegalEntity>
                        </cac:CarrierParty>';
                    endif;

                    if($datosEnvio->modTraslado == '02'):
                    $xml.= '<!-- CONDUCTOR PRINCIPAL -->
                        <cac:DriverPerson>
                            <!-- TIPO Y NUMERO DE DOCUMENTO DE IDENTIDAD -->
                            <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                    schemeAgencyName="PE:SUNAT"
                                    schemeName="Documento de Identidad"
                                    schemeID="'.$datosGuia->conductor->tipoDoc.'">'.$datosGuia->conductor->numDoc.'</cbc:ID>
                            <!-- NOMBRES -->
                            <cbc:FirstName>'.$datosGuia->conductor->nombres.'</cbc:FirstName>
                            <!-- APELLIDOS -->
                            <cbc:FamilyName>'.$datosGuia->conductor->apellidos.'</cbc:FamilyName>
                            <!-- TIPO DE CONDUCTOR: PRINCIPAL -->
                            <cbc:JobTitle>Principal</cbc:JobTitle>
                            <cac:IdentityDocumentReference>
                                <!-- LICENCIA DE CONDUCIR -->
                                <cbc:ID>'.$datosGuia->conductor->licencia.'</cbc:ID>
                            </cac:IdentityDocumentReference>
                        </cac:DriverPerson>';
                    endif;

                $xml.= '</cac:ShipmentStage>
                <cac:Delivery>
                    <!-- DIRECCION DEL PUNTO DE LLEGADA -->
                    <cac:DeliveryAddress>
                        <!-- UBIGEO DE LLEGADA -->
                        <cbc:ID schemeAgencyName="PE:INEI"
                                schemeName="Ubigeos">'.$datosEnvio->llegada->ubigeo.'</cbc:ID>
                        <!-- CODIGO DE ESTABLECIMIENTO ANEXO DE LLEGADA -->';

                        if($datosGuia->destinatario->tipoDoc === '6'):
                            $xml.= '<cbc:AddressTypeCode listAgencyName="PE:SUNAT"
                                            listName="Establecimientos anexos"
                                            listID="'.$datosGuia->destinatario->numDoc.'">0000</cbc:AddressTypeCode>';
                        endif;

                        $xml.= '<cac:AddressLine>
                            <cbc:Line><![CDATA['.$datosEnvio->llegada->direccion.']]></cbc:Line>
                        </cac:AddressLine>
                    </cac:DeliveryAddress>';

                    if ($datosEnvio->numContenedor != ''):
                        $xml.= '<cac:TransportHandlingUnit>
                                <cbc:ID>'.$datosEnvio->numContenedor.'</cbc:ID>
                            </cac:TransportHandlingUnit>';
                    endif;

                    $xml.= '<cac:Despatch>
                        <!-- DIRECCION DEL PUNTO DE PARTIDA -->
                        <cac:DespatchAddress>
                            <!-- UBIGEO DE PARTIDA -->
                            <cbc:ID schemeAgencyName="PE:INEI"
                                    schemeName="Ubigeos">'.$emisor->address->ubigeo.'</cbc:ID>
                            <!-- CODIGO DE ESTABLECIMIENTO ANEXO DE PARTIDA -->
                            <cbc:AddressTypeCode listName="Establecimientos anexos"
                                                listAgencyName="PE:SUNAT"
                                                listID="'.$emisor->ruc.'">0000</cbc:AddressTypeCode>
                            <!-- DIRECCION COMPLETA Y DETALLADA DE PARTIDA -->
                            <cac:AddressLine>
                                <cbc:Line><![CDATA['.$emisor->address->direccion.']]></cbc:Line>
                            </cac:AddressLine>
                        </cac:DespatchAddress>
                    </cac:Despatch>
                </cac:Delivery>';

                if ($datosEnvio->codPuerto != ''):
                    $xml.='<cac:FirstArrivalPortLocation>
                            <cbc:ID>'.$datosEnvio->codPuerto.'</cbc:ID>
                        </cac:FirstArrivalPortLocation>';
                endif;

                if($datosEnvio->modTraslado === '02'):

                    $xml.= '<cac:TransportHandlingUnit>
                    <cac:TransportEquipment>
                        <!-- VEHICULO PRINCIPAL -->
                        <!-- PLACA - VEHICULO PRINCIPAL -->
                        <cbc:ID>'.$datosGuia->transportista->placa.'</cbc:ID>
                    </cac:TransportEquipment>
                    </cac:TransportHandlingUnit>';

                endif;

            $xml.= '</cac:Shipment>
            <!-- DETALLES DE BIENES A TRASLADAR -->';
            $items = 1;
            foreach($detalle as $k => $v):
                $xml.= '<cac:DespatchLine>
                    <!-- NUMERO DE ORDEN DEL ITEM -->
                    <cbc:ID>'.$items++.'</cbc:ID>
                    <!-- CANTIDAD -->
                    <cbc:DeliveredQuantity unitCode="'.$v->unidad.'"
                                        unitCodeListAgencyName="United Nations Economic Commission for Europe"
                                        unitCodeListID="UN/ECE rec 20">'.$v->cantidad.'</cbc:DeliveredQuantity>
                    <cac:OrderLineReference>
                        <cbc:LineID>'.$items++.'</cbc:LineID>
                    </cac:OrderLineReference>
                    <cac:Item>
                        <cbc:Description><![CDATA['.$v->descripcion.']]></cbc:Description>
                        <cac:SellersItemIdentification>
                            <cbc:ID><![CDATA['.$v->codProducto.']]></cbc:ID>
                        </cac:SellersItemIdentification>';

                        if ($v->codProdSunat != ''):
                            $xml.='<cac:CommodityClassification>
                                        <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">'.$v->codProdSunat.'</cbc:ItemClassificationCode>
                                    </cac:CommodityClassification>';
                        endif;

                    $xml.= '</cac:Item>
                </cac:DespatchLine>';
            endforeach;
        $xml.= '</DespatchAdvice>';

        $doc->loadXML($xml);
        $doc->save($nombrexml.'.XML');
    
    }
    
    /*=============================================
    Generamos Guia de remision transportista
    =============================================*/
    function CrearXMLGuiaRemisionTransportista($nombrexml, $emisor, $datosGuia, $datosEnvio, $detalle)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        $doc = new \DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'utf-8';

        $xml = '<?xml version="1.0" encoding="utf-8" standalone="no"?>
                    <DespatchAdvice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                        xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
                        xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
                        xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
                        xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
                        xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2">
            <ext:UBLExtensions>
                <ext:UBLExtension>
                    <ext:ExtensionContent/>
                </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
            <cbc:CustomizationID>2.0</cbc:CustomizationID>
            <cbc:ID>'.$datosGuia->serie.'-'.$datosGuia->correlativo.'</cbc:ID>
            <cbc:IssueDate>'.$datosGuia->fechaEmision.'</cbc:IssueDate>
            <cbc:IssueTime>'.$datosGuia->horaEmision.'</cbc:IssueTime>
            <cbc:DespatchAdviceTypeCode listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01"
                                        listName="Tipo de Documento"
                                        listAgencyName="PE:SUNAT">'.$datosGuia->tipoDoc.'</cbc:DespatchAdviceTypeCode>';

            if($datosGuia->observacion):
                $xml.= '<cbc:Note><![CDATA['.$datosGuia->observacion.']]></cbc:Note>';
            endif;

            $xml.= '<cac:Signature>
                <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                <cbc:Note><![CDATA['.$emisor->nombreComercial.']]></cbc:Note>
                <cac:SignatoryParty>
                    <cac:PartyIdentification>
                        <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                    </cac:PartyName>
                </cac:SignatoryParty>
                <cac:DigitalSignatureAttachment>
                    <cac:ExternalReference>
                        <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
                    </cac:ExternalReference>
                </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <!-- DATOS DEL EMISOR (TRANSPORTISTA) -->
            <cac:DespatchSupplierParty>
                <cac:Party>
                    <cac:PartyIdentification>
                        <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                schemeAgencyName="PE:SUNAT"
                                schemeName="Documento de Identidad"
                                schemeID="'.$emisor->tipoDoc.'">'.$emisor->ruc.'</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:DespatchSupplierParty>
            <!-- DATOS DEL RECEPTOR (DESTINATARIO) -->
            <cac:DeliveryCustomerParty>
                <cac:Party>
                    <cac:PartyIdentification>
                        <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                schemeAgencyName="PE:SUNAT"
                                schemeName="Documento de Identidad"
                                schemeID="'.$datosGuia->destinatario->tipoDoc.'">'.$datosGuia->destinatario->numDoc.'</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA['.$datosGuia->destinatario->nombreRazon.']]></cbc:RegistrationName>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:DeliveryCustomerParty>';
        //    <!-- DATOS DE QUIEN PAGA EL SERVICIO -->
        //<cac:OriginatorCustomerParty>--}}
        //    <cac:Party>--}}
        //        <cac:PartyIdentification>--}}
        //            <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"--}}
        //                    schemeAgencyName="PE:SUNAT"--}}
        //                    schemeName="Documento de Identidad"--}}
        //                    schemeID="6">10417844398</cbc:ID>--}}
        //        </cac:PartyIdentification>--}}
        //        <cac:PartyLegalEntity>--}}
        //            <cbc:RegistrationName>ERIQUE GASPAR CARLOS ALFREDO</cbc:RegistrationName>--}}
        //        </cac:PartyLegalEntity>--}}
        //    </cac:Party>--}}
        //</cac:OriginatorCustomerParty>--}}
            $xml.= '<cac:Shipment>
                <!-- ID OBLIGATORIO POR UBL -->
                <cbc:ID>1</cbc:ID>
                <!-- PESO BRUTO TOTAL DE LA CARGA-->
                <cbc:GrossWeightMeasure
                    unitCode="'.$datosEnvio->uniPesoTotal.'">'.$datosEnvio->pesoTotal.'</cbc:GrossWeightMeasure>
                <cac:ShipmentStage>
                    <!-- FECHA DE INICIO DEL TRASLADO -->
                    <cac:TransitPeriod>
                        <cbc:StartDate>'.$datosEnvio->fechaTraslado.'</cbc:StartDate>
                    </cac:TransitPeriod>
                    <!-- CONDUCTOR PRINCIPAL -->
                        <cac:DriverPerson>
                            <!-- TIPO Y NUMERO DE DOCUMENTO DE IDENTIDAD -->
                            <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                    schemeAgencyName="PE:SUNAT"
                                    schemeName="Documento de Identidad"
                                    schemeID="'.$datosGuia->conductor->tipoDoc.'">'.$datosGuia->conductor->numDoc.'</cbc:ID>
                            <!-- NOMBRES -->
                            <cbc:FirstName>'.$datosGuia->conductor->nombres.'</cbc:FirstName>
                            <!-- APELLIDOS -->
                            <cbc:FamilyName>'.$datosGuia->conductor->apellidos.'</cbc:FamilyName>
                            <!-- TIPO DE CONDUCTOR: PRINCIPAL -->
                            <cbc:JobTitle>Principal</cbc:JobTitle>
                            <cac:IdentityDocumentReference>
                                <!-- LICENCIA DE CONDUCIR -->
                                <cbc:ID>'.$datosGuia->conductor->licencia.'</cbc:ID>
                            </cac:IdentityDocumentReference>
                        </cac:DriverPerson>
                </cac:ShipmentStage>
                <cac:Delivery>
                    <!-- DIRECCION DEL PUNTO DE LLEGADA -->
                    <cac:DeliveryAddress>
                        <!-- UBIGEO DE LLEGADA -->
                        <cbc:ID schemeAgencyName="PE:INEI"
                                schemeName="Ubigeos">'.$datosEnvio->llegada->ubigeo.'</cbc:ID>
                        <cac:AddressLine>
                            <cbc:Line><![CDATA['.$datosEnvio->llegada->direccion.']]></cbc:Line>
                        </cac:AddressLine>
                    </cac:DeliveryAddress>
                    <cac:Despatch>
                        <!-- DIRECCION DEL PUNTO DE PARTIDA -->
                        <cac:DespatchAddress>
                            <!-- UBIGEO DE PARTIDA -->
                            <cbc:ID schemeAgencyName="PE:INEI"
                                    schemeName="Ubigeos">'.$emisor->address->ubigeo.'</cbc:ID>
                            <!-- DIRECCION COMPLETA Y DETALLADA DE PARTIDA -->
                            <cac:AddressLine>
                                <cbc:Line><![CDATA['.$emisor->address->direccion.']]></cbc:Line>
                            </cac:AddressLine>
                        </cac:DespatchAddress>
                        <!-- DATOS DEL REMITENTE -->
                        <cac:DespatchParty>
                            <cac:PartyIdentification>
                                <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                        schemeAgencyName="PE:SUNAT"
                                        schemeName="Documento de Identidad"
                                        schemeID="'.$datosGuia->transportista->tipoDoc.'">'.$datosGuia->transportista->numDoc.'</cbc:ID>
                            </cac:PartyIdentification>
                            <cac:PartyLegalEntity>
                                <cbc:RegistrationName><![CDATA['.$datosGuia->transportista->nombreRazon.']]></cbc:RegistrationName>
                            </cac:PartyLegalEntity>
                        </cac:DespatchParty>
                    </cac:Despatch>
                </cac:Delivery>
                <cac:TransportHandlingUnit>
                    <!-- NUMERO DE CONTENEDOR -->
                    <cbc:ID>-</cbc:ID>
                    <cac:TransportEquipment>
                        <!-- VEHICULO PRINCIPAL -->
                        <!-- PLACA - VEHICULO PRINCIPAL -->
                        <cbc:ID>'.$datosGuia->transportista->placa.'</cbc:ID>
                    </cac:TransportEquipment>
                </cac:TransportHandlingUnit>
            </cac:Shipment>
            <!-- DETALLES DE BIENES A TRASLADAR -->';

            $items = 1;
            foreach($detalle as $k => $v):

                $xml.= '<cac:DespatchLine>
                    <!-- NUMERO DE ORDEN DEL ITEM -->
                    <cbc:ID>'.$items++.'</cbc:ID>
                    <!-- CANTIDAD -->
                    <cbc:DeliveredQuantity unitCode="'.$v->unidad.'"
                                        unitCodeListAgencyName="United Nations Economic Commission for Europe"
                                        unitCodeListID="UN/ECE rec 20">'.$v->cantidad.'</cbc:DeliveredQuantity>
                    <cac:OrderLineReference>
                        <cbc:LineID>'.$items++.'</cbc:LineID>
                    </cac:OrderLineReference>
                    <cac:Item>
                        <cbc:Description><![CDATA['.$v->descripcion.']]></cbc:Description>
                        <cac:SellersItemIdentification>
                            <cbc:ID><![CDATA['.$v->codProducto.']]></cbc:ID>
                        </cac:SellersItemIdentification>';

                        if ($v->codProdSunat != ''):
                            $xml.='<cac:CommodityClassification>
                                        <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">'.$v->codProdSunat.'</cbc:ItemClassificationCode>
                                    </cac:CommodityClassification>';
                        endif;

                    $xml.= '</cac:Item>
                </cac:DespatchLine>';

            endforeach;

        $xml.= '</DespatchAdvice>';

        $doc->loadXML($xml);
        $doc->save($nombrexml.'.XML');
    
    }
    
    /*=============================================
    Generamos xml Percepcion
    =============================================*/
    function CrearXMLPercepcion($nombrexml, $emisor, $cliente, $comprobante, $documentos)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        $doc = new \DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'utf-8';

        $xml = '<?xml version="1.0" encoding="utf-8" standalone="no"?>
                    <Perception xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:Perception-1"
                    xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
                    xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
                    xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
                    xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
                    xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1">
            <ext:UBLExtensions>
                <ext:UBLExtension>
                    <ext:ExtensionContent/>
                </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
            <cbc:CustomizationID>1.0</cbc:CustomizationID>
            <cac:Signature>
                <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                <cbc:Note>'.$emisor->nombreComercial.'</cbc:Note>
                <cac:SignatoryParty>
                    <cac:PartyIdentification>
                        <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                    </cac:PartyName>
                </cac:SignatoryParty>
                <cac:DigitalSignatureAttachment>
                    <cac:ExternalReference>
                        <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
                    </cac:ExternalReference>
                </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cbc:ID>'.$comprobante->serie.'-'.$comprobante->correlativo.'</cbc:ID>
            <cbc:IssueDate>'.$comprobante->fechaEmision.'</cbc:IssueDate>
            <cbc:IssueTime>'.$comprobante->horaEmision.'</cbc:IssueTime>
            <cac:AgentParty>
                <cac:PartyIdentification>
                    <cbc:ID schemeID="'.$emisor->tipoDoc.'">'.$emisor->ruc.'</cbc:ID>
                </cac:PartyIdentification>
                <cac:PartyName>
                    <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                </cac:PartyName>
                <cac:PostalAddress>
                    <cbc:ID>'.$emisor->address->ubigeo.'</cbc:ID>
                    <cbc:StreetName><![CDATA['.$emisor->address->direccion.']]></cbc:StreetName>
                    <cbc:CityName>'.$emisor->address->departamento.'</cbc:CityName>
                    <cbc:CountrySubentity>'.$emisor->address->provincia.'</cbc:CountrySubentity>
                    <cbc:District>'.$emisor->address->distrito.'</cbc:District>
                    <cac:Country>
                        <cbc:IdentificationCode>'.$emisor->address->codigoPais.'</cbc:IdentificationCode>
                    </cac:Country>
                </cac:PostalAddress>
                <cac:PartyLegalEntity>
                    <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                </cac:PartyLegalEntity>
            </cac:AgentParty>
            <cac:ReceiverParty>
                <cac:PartyIdentification>
                    <cbc:ID schemeID="'.$cliente->tipoDoc.'">'.$cliente->numDoc.'</cbc:ID>
                </cac:PartyIdentification>
                <cac:PartyLegalEntity>
                    <cbc:RegistrationName><![CDATA['.$cliente->rznSocial.']]></cbc:RegistrationName>
                </cac:PartyLegalEntity>
            </cac:ReceiverParty>
            <sac:SUNATPerceptionSystemCode>'.$comprobante->tipoPercepcion.'</sac:SUNATPerceptionSystemCode>
            <sac:SUNATPerceptionPercent>'.$comprobante->porcentajePercepcion.'</sac:SUNATPerceptionPercent>';

            if($comprobante->observacion):
                $xml.= '<cbc:Note><![CDATA['.$comprobante->observacion.']]></cbc:Note>';
            endif;

            $xml.= '<cbc:TotalInvoiceAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->totales->totalPercibido.'</cbc:TotalInvoiceAmount>
            <sac:SUNATTotalCashed currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->totales->totalpagado.'</sac:SUNATTotalCashed>';

            foreach($documentos as $key => $documento):

                $xml.= '<sac:SUNATPerceptionDocumentReference>
                    <cbc:ID schemeID="'.$documento->tipoDoc.'">'.$documento->serie.'-'.$documento->correlativo.'</cbc:ID>
                    <cbc:IssueDate>'.$documento->fechaEmision.'</cbc:IssueDate>
                    <cbc:TotalInvoiceAmount currencyID="'.$documento->tipoMoneda.'">'.$documento->totalDocumento.'</cbc:TotalInvoiceAmount>';

                    if($documento->pagos):

                        $nItem = 1;
                        foreach($documento->pagos as $key => $payment):

                            $xml.= '<cac:Payment>
                                <cbc:ID>'.$nItem++.'</cbc:ID>
                                <cbc:PaidAmount currencyID="'.$payment->tipoMoneda.'">'.$payment->totalPago.'</cbc:PaidAmount>
                                <cbc:PaidDate>'.$payment->fechaPago.'</cbc:PaidDate>
                            </cac:Payment>';

                        endforeach;

                    endif;

                    if($documento->totalPercibido && $documento->totalPagar && $documento->fechaPercepcion):

                        $xml.= '<sac:SUNATPerceptionInformation>
                            <sac:SUNATPerceptionAmount currencyID="'.$documento->tipoMoneda.'">'.$documento->totalPercibido.'</sac:SUNATPerceptionAmount>
                            <sac:SUNATPerceptionDate>'.$documento->fechaPercepcion.'</sac:SUNATPerceptionDate>
                            <sac:SUNATNetTotalCashed currencyID="'.$documento->tipoMoneda.'">'.$documento->totalPagar.'</sac:SUNATNetTotalCashed>';

                            if($documento->tipoCambio):

                                $xml.= '<cac:ExchangeRate>
                                    <cbc:SourceCurrencyCode>'.$documento->tipoCambio->codMoneda.'</cbc:SourceCurrencyCode>
                                    <cbc:TargetCurrencyCode>'.$documento->tipoCambio->tipoMoneda.'</cbc:TargetCurrencyCode>
                                    <cbc:CalculationRate>'.$documento->tipoCambio->factor.'</cbc:CalculationRate>
                                    <cbc:Date>'.$documento->tipoCambio->fecha.'</cbc:Date>
                                </cac:ExchangeRate>';

                            endif;

                        $xml.= '</sac:SUNATPerceptionInformation>';

                    endif;

                $xml.= '</sac:SUNATPerceptionDocumentReference>';

            endforeach;

        $xml.= '</Perception>';

        $doc->loadXML($xml);
        $doc->save($nombrexml.'.XML');
    
    }
    
    /*=============================================
    Generamos xml Retencion
    =============================================*/
    function CrearXMLRetencion($nombrexml, $emisor, $cliente, $comprobante, $documentos)
    {

        /*=============================================
        Decodificamos los datos de la empresa
        =============================================*/
        $emisor = json_decode($emisor);

        $doc = new \DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'utf-8';

        $xml = '<?xml version="1.0" encoding="utf-8" standalone="no"?>
                <Retention xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:Retention-1"
                xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
                xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
                xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
                xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
                xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1">
            <ext:UBLExtensions>
                <ext:UBLExtension>
                    <ext:ExtensionContent/>
                </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
            <cbc:CustomizationID>1.0</cbc:CustomizationID>
            <cac:Signature>
                <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                <cbc:Note>'.$emisor->nombreComercial.'</cbc:Note>
                <cac:SignatoryParty>
                    <cac:PartyIdentification>
                        <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                    </cac:PartyName>
                </cac:SignatoryParty>
                <cac:DigitalSignatureAttachment>
                    <cac:ExternalReference>
                        <cbc:URI>#BYDEVELOPERTECHNOLOGY</cbc:URI>
                    </cac:ExternalReference>
                </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cbc:ID>'.$comprobante->serie.'-'.$comprobante->correlativo.'</cbc:ID>
            <cbc:IssueDate>'.$comprobante->fechaEmision.'</cbc:IssueDate>
            <cbc:IssueTime>'.$comprobante->horaEmision.'</cbc:IssueTime>
            <cac:AgentParty>
                <cac:PartyIdentification>
                    <cbc:ID schemeID="'.$emisor->tipoDoc.'">'.$emisor->ruc.'</cbc:ID>
                </cac:PartyIdentification>
                <cac:PartyName>
                    <cbc:Name><![CDATA['.$emisor->razonSocial.']]></cbc:Name>
                </cac:PartyName>
                <cac:PostalAddress>
                    <cbc:ID>'.$emisor->address->ubigeo.'</cbc:ID>
                    <cbc:StreetName><![CDATA['.$emisor->address->direccion.']]></cbc:StreetName>
                    <cbc:CityName>'.$emisor->address->departamento.'</cbc:CityName>
                    <cbc:CountrySubentity>'.$emisor->address->provincia.'</cbc:CountrySubentity>
                    <cbc:District>'.$emisor->address->distrito.'</cbc:District>
                    <cac:Country>
                        <cbc:IdentificationCode>'.$emisor->address->codigoPais.'</cbc:IdentificationCode>
                    </cac:Country>
                </cac:PostalAddress>
                <cac:PartyLegalEntity>
                    <cbc:RegistrationName><![CDATA['.$emisor->razonSocial.']]></cbc:RegistrationName>
                </cac:PartyLegalEntity>
            </cac:AgentParty>
            <cac:ReceiverParty>
                <cac:PartyIdentification>
                    <cbc:ID schemeID="'.$cliente->tipoDoc.'">'.$cliente->numDoc.'</cbc:ID>
                </cac:PartyIdentification>
                <cac:PartyLegalEntity>
                    <cbc:RegistrationName><![CDATA['.$cliente->rznSocial.']]></cbc:RegistrationName>
                </cac:PartyLegalEntity>
            </cac:ReceiverParty>
            <sac:SUNATRetentionSystemCode>'.$comprobante->tipoRetencion.'</sac:SUNATRetentionSystemCode>
            <sac:SUNATRetentionPercent>'.$comprobante->porcentajeRetencion.'</sac:SUNATRetentionPercent>';

            if($comprobante->observacion):
                $xml.= '<cbc:Note><![CDATA['.$comprobante->observacion.']]></cbc:Note>';
            endif;

            $xml.= '<cbc:TotalInvoiceAmount currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->totales->totalRetenido.'</cbc:TotalInvoiceAmount>
            <sac:SUNATTotalPaid currencyID="'.$comprobante->tipoMoneda.'">'.$comprobante->totales->totalpagado.'</sac:SUNATTotalPaid>';

            foreach($documentos as $key => $documento):

                $xml.= '<sac:SUNATRetentionDocumentReference>
                    <cbc:ID schemeID="'.$documento->tipoDoc.'">'.$documento->serie.'-'.$documento->correlativo.'</cbc:ID>
                    <cbc:IssueDate>'.$documento->fechaEmision.'</cbc:IssueDate>
                    <cbc:TotalInvoiceAmount currencyID="'.$documento->tipoMoneda.'">'.$documento->totalDocumento.'</cbc:TotalInvoiceAmount>';

                    if($documento->pagos):

                        $nItem = 1;
                        foreach($documento->pagos as $key => $payment):

                            $xml.= '<cac:Payment>
                                <cbc:ID>'.$nItem++.'</cbc:ID>
                                <cbc:PaidAmount currencyID="'.$payment->tipoMoneda.'">'.$payment->totalPago.'</cbc:PaidAmount>
                                <cbc:PaidDate>'.$payment->fechaPago.'</cbc:PaidDate>
                            </cac:Payment>';

                        endforeach;

                    endif;

                    if($documento->totalRetenido && $documento->totalPagar && $documento->fechaRetencion):

                        $xml.= '<sac:SUNATRetentionInformation>
                            <sac:SUNATRetentionAmount currencyID="'.$documento->tipoMoneda.'">'.$documento->totalRetenido.'</sac:SUNATRetentionAmount>
                            <sac:SUNATRetentionDate>'.$documento->fechaRetencion.'</sac:SUNATRetentionDate>
                            <sac:SUNATNetTotalPaid currencyID="'.$documento->tipoMoneda.'">'.$documento->totalPagar.'</sac:SUNATNetTotalPaid>';

                            if($documento->tipoCambio):

                                $xml.= '<cac:ExchangeRate>
                                    <cbc:SourceCurrencyCode>'.$documento->tipoCambio->codMoneda.'</cbc:SourceCurrencyCode>
                                    <cbc:TargetCurrencyCode>'.$documento->tipoCambio->tipoMoneda.'</cbc:TargetCurrencyCode>
                                    <cbc:CalculationRate>'.$documento->tipoCambio->factor.'</cbc:CalculationRate>
                                    <cbc:Date>'.$documento->tipoCambio->fecha.'</cbc:Date>
                                </cac:ExchangeRate>';

                            endif;

                        $xml.= '</sac:SUNATRetentionInformation>';

                    endif;

                $xml.= '</sac:SUNATRetentionDocumentReference>';

            endforeach;

        $xml.= '</Retention>';

        $doc->loadXML($xml);
        $doc->save($nombrexml.'.XML');
    
    }
   
}