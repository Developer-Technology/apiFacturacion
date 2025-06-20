<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Mantemenos el input lleno si se encuetra un error
=============================================*/
if (isset($_POST["name-user"])) {

    $nameUser = $_POST["name-user"];
    $phoneUser = $_POST["phone-user"];

} else {

    $nameUser = '';
    $phoneUser = '';

}

/*=============================================
Filtro fecha tabla
=============================================*/
if(isset($_GET["start"]) && isset($_GET["end"])){

    $between1 = $_GET["start"];
    $between2 = $_GET["end"];
  
  }else{
  
    $between1 = date("Y-m-d", strtotime("-100000 day", strtotime(date("Y-m-d"))));
    $between2 = date("Y-m-d");
  
  }

?>

<input type="hidden" id="between1" value="<?php echo $between1 ?>">
<input type="hidden" id="between2" value="<?php echo $between2 ?>">

<div class="content__header content__boxed rounded-0">
    <div class="content__wrap d-md-flex align-items-start">

        <figure class="m-0">
            <div class="d-inline-flex align-items-center position-relative pt-xl-5 mb-3">
                <div class="flex-shrink-0">
                    <img class="img-xl rounded-circle" src="<?php echo $avatarUser ?>" loading="lazy">
                </div>
                <div class="flex-grow-1 ms-4">
                    <a class="h3 btn-link pointer"><?php echo $dataUsers->nombres_usuario ?></a>
                    <p class="text-muted m-0"><?php echo $dataUsers->email_usuario ?></p>
                </div>
            </div>

            <blockquote class="blockquote">
                <p><?php echo "$vector[$numero]"; ?></p>
            </blockquote>
        </figure>

        <?php if (empty($_SESSION["admin"])): ?>
            <button class="btn btn-default border" data-toggle="modal" data-target="#viewToken" style="position: absolute; right: 20px; top: 12%;"><i class="fa fa-eye"></i> <span class="vr"></span> Mis Credenciales</button>
        <?php endif ?>
        
    </div>

</div>

<div class="content__boxed">
    <div class="content__wrap">
        <div class="d-md-flex gap-4">

            <!-- Sidebar -->
            <div class="w-md-200px flex-shrink-0">

                <h5>Acerca De</h5>
                <ul class="list-unstyled mb-3">
                    <li class="mb-2"><i class="fa fa-calendar fs-5 me-3"></i> <?php echo TemplateController::fechaEsShort($dataUsers->creado_usuario) ?></li>
                    <li class="mb-2"><i class="fa fa-check fs-5 me-3"></i> <?php echo $dataUsers->metodo_usuario ?></li>
                    <li class="mb-2"><i class="fa fa-phone fs-5 me-3"></i><?php echo $dataUsers->telefono_usuario ?></li>
                </ul>

                <h5 class="mt-5">Enlaces De Interés</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="https://qlab.pe" target="_blank" class="btn btn-xs btn-outline-light text-nowrap">Web</a>
                    <a href="https://qlab.pe" target="_blank" class="btn btn-xs btn-outline-light text-nowrap">Tienda</a>
                    <a href="https://qlab.pe/facturacion-electronica/" target="_blank" class="btn btn-xs btn-outline-light text-nowrap">Facturación Electrónica</a>
                </div>

            </div>
            <!-- END : Sidebar -->

            <div class="vr d-none"></div>

            <!-- Content -->
            <div class="flex-fill">

                <div class="card mb-3">
                    <div class="card-body">

                        <form method="post" class="needs-validation" novalidate enctype="multipart/form-data" autocomplete="off">

                            <div class="row">

                                <h4 class="mb-3">Datos Personales</h4>

                                <div class="col-md-4">

                                    <div class="col-md-6 hidden">
                                        <div class="form-floating mb-3">
                                            <input type="file" name="file-user" id="file-user" class="form-control" accept="image/*" onchange="validateImageJS(event,'changePicture')">
                                            <label for="file-user" class="mb-2">Logo (.png / .jpg)<sup class="text-danger">*</sup></label>
                                        </div>
                                    </div>
                                
                                    <div class="user-avatar-section text-center">
                                        <label for="file-user">
                                            <div class=" d-flex align-items-center flex-column">
                                                <img class="img-fluid rounded-circle border changePicture" src="<?php echo $avatarUser ?>" height="100" width="100" />
                                            </div>
                                        </label>
                                        <br>
                                        <small class="text-muted">Recomendado 800 * 800 pixeles</small>
                                    </div>
                                
                                </div>

                                <div class="col-md-8">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="name-user" name="name-user" placeholder="Nombres" required value="<?php echo $dataUsers->nombres_usuario ?>">
                                                <label for="name-user">Nombres <sup class="text-danger">*</sup></label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="phone-user" name="phone-user" placeholder="Teléfono" required value="<?php echo $dataUsers->telefono_usuario ?>">
                                                <label for="phone-user">Teléfono <sup class="text-danger">*</sup></label>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="mail-user" name="mail-user" placeholder="Correo Electrónico" readonly value="<?php echo $dataUsers->email_usuario ?>">
                                                <label for="mail-user">Correo Electrónico</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="alias-user" name="alias-user" placeholder="Alias" readonly value="<?php echo $dataUsers->alias_usuario ?>">
                                                <label for="alias-user">Alias</label>
                                            </div>
                                        </div>
                                        
                                    </div>

                                </div>

                            </div>

                            <?php

                                $changePass = UsersController::updateProfile($_SESSION["user"]->id_usuario);

                            ?>

                            <hr>
                            <div class="row">

                                <div class="col-md-12 text-center">
                                    <a href="/profile" class="btn btn-danger">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>

                            </div>

                        </form>

                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">

                        <form method="post" class="needs-validation" novalidate autocomplete="off">

                            <div class="row">

                                <h4 class="mb-3">Cambiar Contraseña</h4>

                                <div class="col-md-12">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control" id="actual-pass" name="actual-pass" placeholder="Contraseña Actual" required>
                                                <label for="actual-pass">Contraseña Actual <sup class="text-danger">*</sup></label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control" id="new-pass" name="new-pass" placeholder="Nueva Contraseña" required>
                                                <label for="new-pass">Nueva Contraseña <sup class="text-danger">*</sup></label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control" id="confirm-pass" name="confirm-pass" placeholder="Confirmar Contraseña" required>
                                                <label for="confirm-pass">Confirmar Contraseña <sup class="text-danger">*</sup></label>
                                            </div>
                                        </div>
                                        
                                    </div>

                                </div>

                            </div>

                            <?php

                                $changePass = UsersController::changePass($_SESSION["user"]->id_usuario, $dataUsers->clave_usuario);

                            ?>

                            <hr>
                            <div class="row">

                                <div class="col-md-12 text-center">
                                    <a href="/profile" class="btn btn-danger">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>

                            </div>

                        </form>

                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">

                        <form method="post" class="needs-validation" novalidate autocomplete="off">

                            <div class="row">

                                <h4 class="mb-3">Mis Compras</h4>

                                <!-- Left toolbar -->
                                <div class="col-md-6 d-flex gap-1 align-items-center mb-3">
                                    
                                    <div class="d-flex mr-2"> 
                                        <span class="mr-2 mt-1">Exportar</span>
                                        <input type="checkbox" name="my-checkbox" data-bootstrap-switch data-off-color="light" data-on-color="dark" data-size="mini" data-handle-width="70" onchange="reportActive(event)">
                                    </div>     
                                
                                    <div class="input-group">
                                        <button type="button" class="btn btn-default float-right" id="daterange-btn">
                                            <i class="fa fa-calendar-o mr-2"></i> 
                                            <?php if($between1 < "2000"){ echo "Inicio"; }else{ echo $between1; } ?> - <?php echo $between2 ?>
                                            <i class="fa fa-caret-down ml-2"></i>
                                        </button>
                                    </div>

                                </div>
                                <!-- END : Left toolbar -->

                                <div class="col-md-12">

                                    <div class="row">

                                        <div class="table-responsive">
                                            
                                            <table id="adminsTable" class="table table-bordered table-striped table-hover tableSales">
                                
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>ID Compra</th>
                                                        <th>Plan</th>
                                                        <th>Compra</th>
                                                        <th>Estado</th>
                                                        <th>Asignado</th>
                                                        <th>Fecha</th>
                                                    </tr>
                                                </thead>
                                            
                                            </table>
                                
                                        </div>
                                        
                                    </div>

                                </div>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
            <!-- END : Content -->

        </div>

    </div>
</div>

<script src="views/assets/custom/forms/forms.js"></script>
<script src="views/assets/custom/datatable/datatable.js"></script>