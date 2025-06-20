<footer class="mt-auto">
    <div class="content__boxed">
        <div class="content__wrap py-3 py-md-1 d-flex flex-column flex-md-row align-items-md-center text-center">
            <div class="text-nowrap mb-md-0">
                <small>
                    Copyright &copy; 2014 - <?php echo date('Y') ?>
                    <?php if($dataSett->web_empresa_configuracion != ''): ?>
                        <a href="<?php echo $dataSett->web_empresa_configuracion ?>" target="_blank" class="ms-1 btn-link text-primary"><?php echo $dataSett->nombre_empresa_configuracion ?></a>
                    <?php else: ?>
                        <?php echo $dataSett->nombre_empresa_configuracion ?>
                    <?php endif ?>
                </small>
            </div>
            <nav class="nav flex-column gap-1 flex-md-row gap-md-3 ms-md-auto hidden-xs" style="row-gap: 0 !important;">
                <small>
                    Powered by <a class="btn-link text-primary" href="https://qlab.pe" target="_blank">Qlab</a>
                </small>
            </nav>
        </div>
    </div>
</footer>