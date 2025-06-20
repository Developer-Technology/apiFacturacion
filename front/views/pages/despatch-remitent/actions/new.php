<!-- Table with toolbar -->
<div class="card">
    
    <form method="post" class="needs-validation" novalidate enctype="multipart/form-data" autocomplete="off">

        <div class="card-body">

            <?php

                require_once "controllers/despatches.controller.php";

                $create = new DespatchesController();
                $create -> create();

            ?>

            <div class="row">

                <h4>Datos de la guía</h4>
                <hr>

                <!--=====================================
                Serie
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-serie"
                        placeholder="Serie"
                        required>

                        <label>Serie <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Correlativo
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="number" 
                        class="form-control"
                        name="desp-number"
                        placeholder="Correlativo"
                        min="1"
                        required>

                        <label>Correlativo <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Emision
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="date" 
                        class="form-control"
                        name="desp-femision"
                        placeholder="F. Emisión"
                        required>

                        <label>F. Emisión <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="time" 
                        class="form-control"
                        name="desp-hemision"
                        placeholder="H. Emisión"
                        required>

                        <label>H. Emisión <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Relacionado
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-relSerie"
                        placeholder="Serie Rel">

                        <label>Serie Rel</label>

                    </div>

                </div>

                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="number" 
                        class="form-control"
                        name="desp-relNum"
                        placeholder="Num. Rel">

                        <label>Num. Rel</label>

                    </div>

                </div>

                <h4 class="mt-2">Destinatario</h4>
                <hr>

                <!--=====================================
                Tipo
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <select
                        name="desp-destTipo"
                        class="form-select"
                        required
                        >

                            <option value="">Selecciona</option>
                            <option value="6">RUC</option>
                            <option value="1">DNI</option>

                        </select>

                        <label>Tipo <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Documento
                ======================================-->
                <div class="col-md-2">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="text"
                        class="form-control"
                        pattern="[0-9]{1,}"
                        name="desp-destDoc"
                        placeholder="Documento"
                        required>

                        <label>Documento <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Razon Social
                ======================================-->
                <div class="col-md-4">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-destRazon"
                        placeholder="Razon Social"
                        required>

                        <label>Razón Social <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <h4 class="mt-2">Terceros</h4>
                <hr>

                <!--=====================================
                Tipo
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <select
                        name="desp-terTipo"
                        class="form-select"
                        >

                            <option value="">Selecciona</option>
                            <option value="6">RUC</option>
                            <option value="1">DNI</option>

                        </select>

                        <label>Tipo</label>

                    </div>

                </div>

                <!--=====================================
                Documento
                ======================================-->
                <div class="col-md-2">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="text"
                        class="form-control"
                        pattern="[0-9]{1,}"
                        name="desp-terDoc"
                        placeholder="Documento">

                        <label>Documento</label>

                    </div>

                </div>

                <!--=====================================
                Razon Social
                ======================================-->
                <div class="col-md-4">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-terRazon"
                        placeholder="Razon Social">

                        <label>Razón Social</label>

                    </div>

                </div>

                <h4 class="mt-2">Transportista</h4>
                <hr>

                <!--=====================================
                Tipo
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <select
                        name="desp-tranTipo"
                        class="form-select"
                        required
                        >

                            <option value="">Selecciona</option>
                            <option value="6">RUC</option>
                            <option value="1">DNI</option>

                        </select>

                        <label>Tipo <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Documento
                ======================================-->
                <div class="col-md-2">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="text"
                        class="form-control"
                        pattern="[0-9]{1,}"
                        name="desp-tranDoc"
                        placeholder="Documento"
                        required>

                        <label>Documento <span id="estado-ruc"></span> <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Razon Social
                ======================================-->
                <div class="col-md-4">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-tranRazon"
                        placeholder="Razon Social"
                        required>

                        <label>Razón Social <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Placa
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-tranPlaca"
                        placeholder="Placa"
                        required>

                        <label>Placa <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                MTC
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-tranMtc"
                        placeholder="MTC">

                        <label>MTC</label>

                    </div>

                </div>

                <!--=====================================
                Tipo
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <select
                        name="desp-tranTipoT"
                        class="form-select"
                        required
                        >

                            <option value="">Selecciona</option>
                            <option value="06">RUC</option>
                            <option value="01">DNI</option>

                        </select>

                        <label>Tipo <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Documento
                ======================================-->
                <div class="col-md-2">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="text"
                        class="form-control"
                        pattern="[0-9]{1,}"
                        name="desp-tranDocT"
                        placeholder="Documento"
                        required>

                        <label>Documento <span id="estado-ruc"></span> <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <h4 class="mt-2">Datos de envío</h4>
                <hr>

                <!--=====================================
                Motivo
                ======================================-->
                <div class="col-md-4">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <select
                        name="desp-datMotivo"
                        class="form-select"
                        required
                        >

                            <option value="">Selecciona</option>
                            <option value="01_VENTA">VENTA</option>
                            <option value="14_VENTA SUJETA A CONFIRMACION DEL COMPRADOR">VENTA SUJETA A CONFIRMACION DEL COMPRADOR</option>
                            <option value="02_COMPRA">COMPRA</option>
                            <option value="04_TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA">TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA</option>
                            <option value="18_TRASLADO EMISOR ITINERANTE CP">TRASLADO EMISOR ITINERANTE CP</option>
                            <option value="08_IMPORTACION">IMPORTACION</option>
                            <option value="09_EXPORTACION">EXPORTACION</option>
                            <option value="19_TRASLADO A ZONA PRIMARIA">TRASLADO A ZONA PRIMARIA</option>
                            <option value="13_OTROS">OTROS</option>

                        </select>

                        <label>Motivo <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Peso
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="number" 
                        class="form-control"
                        name="desp-datPeso"
                        placeholder="Peso"
                        required>

                        <label>Peso <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Medida
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="dep-datMedida"
                        placeholder="Un. Medida"
                        required>

                        <label>Un. Medida <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Modo
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <select
                        name="dep-datModo"
                        class="form-select"
                        onchange="modoT(this)"
                        required
                        >

                            <option value="">Selecciona</option>
                            <option value="01">T. Público</option>
                            <option value="02">T. Privado</option>

                        </select>

                        <label>Modo <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                F. Traslado
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="date" 
                        class="form-control"
                        name="desp-datTraslado"
                        placeholder="F. Traslado"
                        required>

                        <label>F. Traslado <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Bultos
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="number" 
                        class="form-control"
                        name="desp-datBultos"
                        placeholder="Bultos"
                        required>

                        <label>N. Bultos <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Ubigeo
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-datUbigeo"
                        placeholder="Ubigeo"
                        required>

                        <label>Ubigeo <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Dirección
                ======================================-->
                <div class="col-md-6">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="desp-datDir"
                        placeholder="Dirección"
                        required>

                        <label>Dirección <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <div class="row hidden" id="conductor">

                    <h4 class="mt-2">Conductor</h4>
                    <hr>

                    <!--=====================================
                    Tipo
                    ======================================-->
                    <div class="col-md-2">
                        
                        <div class="form-group form-floating mt-2 mb-3">

                            <select
                            name="desp-condTipo"
                            class="form-select tipoCond"
                            >

                                <option value="">Selecciona</option>
                                <option value="6">RUC</option>
                                <option value="1">DNI</option>

                            </select>

                            <label>Tipo <sup class="text-danger">*</sup></label>

                        </div>

                    </div>

                    <!--=====================================
                    Documento
                    ======================================-->
                    <div class="col-md-2">

                        <div class="form-group form-floating mt-2 mb-3">

                            <input
                            type="text"
                            class="form-control docCond"
                            pattern="[0-9]{1,}"
                            name="desp-condDoc"
                            placeholder="Documento">

                            <label>Documento <sup class="text-danger">*</sup></label>

                        </div>

                    </div>

                    <!--=====================================
                    Nombres
                    ======================================-->
                    <div class="col-md-3">
                        
                        <div class="form-group form-floating mt-2 mb-3">

                            <input 
                            type="text" 
                            class="form-control nomComd"
                            name="desp-condNom"
                            placeholder="Nombres">

                            <label>Nombres <sup class="text-danger">*</sup></label>

                        </div>

                    </div>

                    <!--=====================================
                    Apellidos
                    ======================================-->
                    <div class="col-md-3">
                        
                        <div class="form-group form-floating mt-2 mb-3">

                            <input 
                            type="text" 
                            class="form-control apeCond"
                            name="desp-condApe"
                            placeholder="Apellidos">

                            <label>Apellidos <sup class="text-danger">*</sup></label>

                        </div>

                    </div>

                    <!--=====================================
                    Licencia
                    ======================================-->
                    <div class="col-md-2">
                        
                        <div class="form-group form-floating mt-2 mb-3">

                            <input 
                            type="text" 
                            class="form-control licCond"
                            name="desp-condLic"
                            placeholder="Licencia">

                            <label>Licencia <sup class="text-danger">*</sup></label>

                        </div>

                    </div>

                </div>

                <h4 class="mt-2">Ítems</h4>
                <hr>

                <!--=====================================
                Items
                ======================================-->
                <div class="col-md-12">

                    <div class="form-group mt-2 mb-3">

                        <input type="hidden" name="inputDetails" value="1">

                        <div class="row mb-3 inputDetails">

                            <!--=====================================
                            Unidad
                            ======================================--> 
                            <div class="col-md-2">
                            
                                <div class="input-group">

                                    <div class="input-group-append">
                                        <span class="input-group-text bg-danger">
                                            <button type="button" class="btn btn-danger btn-xs border-0" onclick="removeInput(0,'inputDetails')"><i class="fa fa-trash"></i></button>
                                        </span>
                                    </div>

                                    <div class="input-group-append">
                                        <span class="input-group-text group__0">
                                            Und:
                                        </span>
                                    </div>

                                    <input
                                    class="form-control" 
                                    type="text"
                                    name="item-und-product_0"
                                    onchange="validateJS(event,'regex')"
                                    required>

                                </div>

                            </div>

                            <!--=====================================
                            Codigo
                            ======================================--> 
                            <div class="col-md-2">
                            
                                <div class="input-group">

                                    <div class="input-group-append">
                                        <span class="input-group-text group__0">
                                            Cod:
                                        </span>
                                    </div>

                                    <input
                                    class="form-control" 
                                    type="text"
                                    name="item-cod-product_0"
                                    onchange="validateJS(event,'regex')"
                                    required>

                                </div>

                            </div>

                            <!--=====================================
                            Descripcion
                            ======================================--> 
                            <div class="col-md-6">
                            
                                <div class="input-group">

                                    <div class="input-group-append">
                                        <span class="input-group-text group__0">
                                            Desc:
                                        </span>
                                    </div>

                                    <input
                                    class="form-control" 
                                    type="text"
                                    name="item-desc-product_0"
                                    onchange="validateJS(event,'regex')"
                                    required>

                                </div>

                            </div>

                            <!--=====================================
                            Cantidad
                            ======================================--> 
                            <div class="col-md-2">
                            
                                <div class="input-group">

                                    <div class="input-group-append">
                                        <span class="input-group-text group__0">
                                            Cant:
                                        </span>
                                    </div>

                                    <input
                                    class="form-control" 
                                    type="number"
                                    min="1"
                                    name="item-cant-product_0"
                                    onchange="validateJS(event,'regex')"
                                    required>

                                </div>

                            </div>

                        </div>

                        <button type="button" class="btn btn-primary mb-2" onclick="addInput(this, 'inputDetails')">Agregar Ítem</button>

                    </div>

                </div>
                
            </div>

        </div>

        <div class="card-footer">
                    
            <div class="col-md-8 offset-md-2">

                <div class="form-group mt-3">

                    <a href="/despatch-remitent" class="btn btn-default border text-left">Regresar</a>
                    
                    <button type="submit" class="btn btn-primary float-right saveBtn">Guardar</button>

                </div>

            </div>

        </div>

    </form>

</div>
<!-- END : Table with toolbar -->