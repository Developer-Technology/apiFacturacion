<div id="root" class="root front-container">

    <!-- CONTENTS -->
    <section id="content" class="content">
        <div
            class="content__boxed card rounded-0 w-100 min-vh-100 d-flex flex-column align-items-stretch justify-content-center">
            <div class="content__wrap">

                <div class="text-center">
                    <div class="error-code page-title mb-3">404</div>
                    <h3 class="mb-4">
                        <div class="badge bg-info">Página no encontrada!</div>
                    </h3>
                    <p class="lead">Lo sentimos, la página que estás buscando no existe.</p>
                </div>

                <?php if($dataSett->web_empresa_configuracion != ''): ?>
                    <!-- Search form -->
                    <div class="col-md-8 offset-md-2 py-4">
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <a href="<?php echo $dataSett->web_empresa_configuracion ?>" target="_blank" class="btn btn-outline-primary btn-lg px-4 gap-3">Visita nuestro sitio Web</a>
                        </div>
                    </div>
                <?php endif ?>

                <!-- Action buttons -->
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" onclick="window.history.back()" class="btn btn-light">Regresar</button>
                    <a href="/" class="btn btn-primary">Ir al inicio</a>
                </div>
                <!-- END : Action buttons -->

            </div>
        </div>
    </section>

</div>