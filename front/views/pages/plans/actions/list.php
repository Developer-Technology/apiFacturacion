<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

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

<!-- Table with toolbar -->
<div class="card">
    <div class="card-header mt-4 mb-3">
        <div class="row">

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

            <!-- Right Toolbar -->
            <div class="col-md-6 d-flex gap-1 align-items-center justify-content-md-end mb-3">

                <a href="/plans/new" class="btn btn-primary hstack gap-2 align-self-center">
                    <i class="demo-psi-add fs-5"></i>
                    <span class="vr"></span>
                    Nuevo Registro
                </a>

            </div>
            <!-- END : Right Toolbar -->

        </div>
    </div>

    <div class="card-body">

        <div class="table-responsive">
                
            <table id="adminsTable" class="table table-bordered table-striped table-hover tablePlans">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Descripción</th>
                        <th>Contiene</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            
            </table>

        </div>

    </div>
</div>
<!-- END : Table with toolbar -->

<script src="views/assets/custom/datatable/datatable.js"></script>