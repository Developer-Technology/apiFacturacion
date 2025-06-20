<!-- Table with toolbar -->
<div class="card">
    
    <form method="post" class="needs-validation" novalidate autocomplete="off">

        <div class="card-body">

            <?php

                require_once "controllers/users.controller.php";

                $create = new UsersController();
                $create -> create();

            ?>

            <div class="row">

                <!--=====================================
                Nombres
                ======================================-->
                <div class="col-md-4">
                    
                    <div class="form-group form-floating mt-2 mb-3">

                        <input 
                        type="text" 
                        class="form-control"
                        pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}"
                        name="name-user"
                        placeholder="Nombres"
                        required>

                        <label>Nombres <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Correo
                ======================================-->
                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="email"
                        class="form-control"
                        onchange="validateRepeat(event,'email','usuarios','email_usuario')"
                        placeholder="Correo Electrónico"
                        name="email-user"
                        required>

                        <label for="">Correo Electrónico <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <!--=====================================
                Telefono
                ======================================-->
                <div class="col-md-2">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="text"
                        class="form-control"
                        placeholder="Teléfono"
                        onchange="validateRepeat(event,'phone','usuarios','telefono_usuario')"
                        name="phone-user"
                        required>

                        <label for="">Teléfono <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <!--=====================================
                Rol
                ======================================-->
                <div class="col-md-2">

                    <div class="form-floating mt-2 mb-3">

                        <select name="rol-user" id="rol-user" class="form-select" required>
                            <option value="">Selecciona Rol</option>
                            <option value="1">Administrador</option>
                            <option value="2">Usuario</option>
                            <option value="3">Cliente</option>
                        </select>

                        <label for="rol-user">Rol <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <!--=====================================
                Contraseña
                ======================================-->
                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="password"
                        class="form-control"
                        placeholder="Contraseña"
                        name="password-user"
                        required>

                        <label for="">Contraseña <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="password"
                        class="form-control"
                        placeholder="Repite Contraseña"
                        name="repassword-user"
                        required>

                        <label for="">Repite Contraseña <sup class="text-danger">*</sup></label>
                    </div>

                </div>
                
            </div>

        </div>

        <div class="card-footer">
                    
            <div class="col-md-8 offset-md-2">

                <div class="form-group mt-3">

                    <a href="/users" class="btn btn-default border text-left">Regresar</a>
                    
                    <button type="submit" class="btn btn-primary float-right saveBtn">Guardar</button>

                </div>

            </div>

        </div>

    </form>

</div>
<!-- END : Table with toolbar -->