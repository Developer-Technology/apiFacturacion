<!-- Table with toolbar -->
<div class="card">
    
    <form method="post" class="needs-validation" novalidate enctype="multipart/form-data" autocomplete="off">

        <div class="card-body">

            <?php

                require_once "controllers/voideds.controller.php";

                $create = new VoidedsController();
                $create -> create();

            ?>

            <div class="row">

                <h4>Datos del resumen</h4>
                <hr>

                <!--=====================================
                Serie
                ======================================-->
                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        name="sum-serie"
                        placeholder="Serie"
                        value="<?php echo date("Ymd") ?>"
                        readonly
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
                        name="sum-number"
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
                        name="sum-femision"
                        placeholder="F. Emisión"
                        required>

                        <label>F. Emisión <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <div class="col-md-2">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="date" 
                        class="form-control"
                        name="sum-fenvio"
                        placeholder="F. Envío"
                        required>

                        <label>F. Envío <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <h4 class="mt-2">Ítems</h4>
                <hr>

                <!--=====================================
                Items
                ======================================-->
                <div class="col-md-12">

                    <div class="form-group mt-2 mb-3">

                        <input type="hidden" name="inputBaja" value="1">

                        <div class="row mb-3 inputBaja">
                            
                            <!--=====================================
                            Tipo de documento
                            ======================================--> 
                            <div class="col-md-2">
                            
                                <div class="input-group">

                                    <div class="input-group-append">
                                        <span class="input-group-text bg-danger">
                                            <button type="button" class="btn btn-danger btn-xs border-0" onclick="removeInput(0,'inputBaja')"><i class="fa fa-trash"></i></button>
                                        </span>
                                    </div>

                                    <div class="input-group-append">
                                        <span class="input-group-text group__0">
                                            Tip Doc:
                                        </span>
                                    </div>

                                    <input
                                    class="form-control" 
                                    type="text"
                                    name="item-tipodoc-product_0"
                                    onchange="validateJS(event,'regex')"
                                    required>

                                </div>

                            </div>

                            <!--=====================================
                            Serie
                            ======================================--> 
                            <div class="col-md-2">
                            
                                <div class="input-group">

                                    <div class="input-group-append">
                                        <span class="input-group-text group__0">
                                            Serie:
                                        </span>
                                    </div>

                                    <input
                                    class="form-control" 
                                    type="text"
                                    name="item-serie-product_0"
                                    onchange="validateJS(event,'regex')"
                                    required>

                                </div>

                            </div>

                            <!--=====================================
                            Numero
                            ======================================--> 
                            <div class="col-md-2">
                            
                                <div class="input-group">

                                    <div class="input-group-append">
                                        <span class="input-group-text group__0">
                                            Num:
                                        </span>
                                    </div>

                                    <input
                                    class="form-control" 
                                    type="text"
                                    name="item-numero-product_0"
                                    onchange="validateJS(event,'regex')"
                                    required>

                                </div>

                            </div>

                            <!--=====================================
                            Moneda
                            ======================================--> 
                            <div class="col-md-6">
                            
                                <div class="input-group">

                                    <div class="input-group-append">
                                        <span class="input-group-text group__0">
                                            Motivo:
                                        </span>
                                    </div>

                                    <input
                                    class="form-control" 
                                    type="text"
                                    name="item-motivo-product_0"
                                    onchange="validateJS(event,'regex')"
                                    required>

                                </div>

                            </div>

                        </div>

                        <button type="button" class="btn btn-primary mb-2" onclick="addInput(this, 'inputBaja')">Agregar Ítem</button>

                    </div>

                </div>
                
            </div>

        </div>

        <div class="card-footer">
                    
            <div class="col-md-8 offset-md-2">

                <div class="form-group mt-3">

                    <a href="/voided" class="btn btn-default border text-left">Regresar</a>
                    
                    <button type="submit" class="btn btn-primary float-right saveBtn">Guardar</button>

                </div>

            </div>

        </div>

    </form>

</div>
<!-- END : Table with toolbar -->