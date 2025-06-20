<div class="content__header content__boxed overlapping">
    <div class="content__wrap">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pagar</li>

            </ol>
        </nav>
        <!-- END : Breadcrumb -->

        <h1 class="page-title mb-0 mt-2">Pagar</h1>
        <p class="lead">En esta sección podrás realizar el pago de tus suscripciones.</p>

    </div>

</div>

<div class="content__boxed">
    <div class="content__wrap">

        <div class="card">
        
            <form method="post" class="needs-validation" novalidate autocomplete="off">

                <div class="card-body">

                    <p>Realiza la carga tu comprobante en formato <b>".png"</b> o <b>".jpg"</b>.</p>

                    <div class="row">

                        <!--=====================================
                        Adjunto
                        ======================================-->
                        <div class="col-md-6">
                            <div class="form-floating mt-2 mb-3">
                                <input type="file" name="fav-emp" id="fav-emp" class="form-control" accept="image/*">
                                <label for="fav-emp" class="mb-2">Adjunto<sup class="text-danger">*</sup></label>
                            </div>
                        </div>

                        <div class="invalid-feedback">Este campo es obligatorio</div>

                        <!--=====================================
                        Monto
                        ======================================-->
                        <div class="col-md-2">
                            
                            <div class="form-group form-floating mt-2 mb-3">

                                <input 
                                type="number" 
                                class="form-control"
                                name="name-plan"
                                placeholder="Monto"
                                required>

                                <label>Monto <sup class="text-danger">*</sup></label>

                            </div>

                        </div>

                        <!--=====================================
                        Comprobante
                        ======================================-->
                        <div class="col-md-2">
                            
                            <div class="form-group form-floating mt-2 mb-3">

                                <input 
                                type="text" 
                                class="form-control"
                                name="name-plan"
                                placeholder="Comprobante Ejm: F001-1"
                                required>

                                <label>Comprobante Ejm: F001-1 <sup class="text-danger">*</sup></label>

                            </div>

                        </div>

                        <!--=====================================
                        Fecha Pago
                        ======================================-->
                        <div class="col-md-2">
                            
                            <div class="form-group form-floating mt-2 mb-3">

                                <input 
                                type="date" 
                                class="form-control"
                                name="name-plan"
                                placeholder="Fecha Pago"
                                required>

                                <label>Fecha Pago <sup class="text-danger">*</sup></label>

                            </div>

                        </div>
                        
                    </div>

                </div>

                <div class="card-footer">
                            
                    <div class="col-md-8 offset-md-2">

                        <div class="form-group mt-3">

                            <a href="/suscriptions" class="btn btn-default border text-left">Regresar</a>
                            
                            <button type="submit" class="btn btn-primary float-right saveBtn">Guardar</button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>
</div>