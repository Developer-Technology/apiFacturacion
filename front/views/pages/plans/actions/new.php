<!-- Table with toolbar -->
<div class="card">
    
    <form method="post" class="needs-validation" novalidate>

        <div class="card-body">

            <?php

                require_once "controllers/plans.controller.php";

                $create = new PlansController();
                $create -> create();

            ?>

            <div class="row">

                <!--=====================================
                Nombre
                ======================================-->
                <div class="col-md-6">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}"
                        onchange="validateRepeat(event,'text','brands','name_brand')"
                        name="name-plan"
                        placeholder="Nombre"
                        required>

                        <label>Nombre <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Precio
                ======================================-->
                <div class="col-md-2">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="number"
                        class="form-control"
                        placeholder="Precio"
                        name="price-plan"
                        required>

                        <label for="">Precio <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <!--=====================================
                Contenido
                ======================================-->
                <div class="col-md-2">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="number"
                        class="form-control"
                        placeholder="Consultas"
                        name="cons-plan"
                        required>

                        <label for="">Consultas <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <div class="col-md-2">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="number"
                        class="form-control"
                        placeholder="Documentos"
                        name="docs-plan"
                        required>

                        <label for="">Documentos <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <!--=====================================
                Descripción
                ======================================-->
                <div class="col-md-12">

                    <div class="form-group mt-2 mb-3">
                        
                        <label>Descripción <sup class="text-danger">*</sup></label>

                        <textarea
                        class="summernote"
                        name="description-plan"
                        required
                        ></textarea>

                        <div class="invalid-feedback">Este campo es obligatorio</div>

                    </div>

                </div>
                
            </div>

        </div>

        <div class="card-footer">
                    
            <div class="col-md-8 offset-md-2">

                <div class="form-group mt-3">

                    <a href="/plans" class="btn btn-default border text-left">Regresar</a>
                    
                    <button type="submit" class="btn btn-primary float-right saveBtn">Guardar</button>

                </div>

            </div>

        </div>

    </form>

</div>
<!-- END : Table with toolbar -->