<div class="modal fade" id="viewToken" tabindex="-1" role="dialog" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Mis Credenciales</h5>
                <button type="button" class="close waves-effect" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
            
                <div class="form-floating mb-3 mt-2">
                    <input type="text" class="form-control" readonly value="<?php echo $dataTenants->token_empresa ?>">
                    <label for="">Token De Acceso</label>
                </div>

                <div class="form-floating mb-3 mt-2">
                    <input type="text" class="form-control" readonly value="<?php echo $dataTenants->clave_secreta_empresa ?>">
                    <label for="">Clave Secreta</label>
                </div>
            
            </div>

            <div class="modal-footer">
                <a class="btn btn-primary" href="<?php echo TemplateController::downloadPotman() ?>" target="_blank">Descargar Postman</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
            </div>

        </div>
        
    </div>

</div>