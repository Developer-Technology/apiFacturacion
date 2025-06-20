<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/
	
	if(isset($routesArray[3])) {
		
		$security = explode("~",base64_decode($routesArray[3]));
	
		if($security[1] == $_SESSION["user"]->token_usuario) {

			$select = "*";

			$url = "usuarios?select=".$select."&linkTo=id_usuario&equalTo=".$security[0];;
			$method = "GET";
			$fields = array();
            $token = TemplateController::tokenSet();

			$response = CurlController::requestSunat($url,$method,$fields, $token);
			
			if($response->response->status == 200) {

				$usuario = $response->response->data[0];

			} else {

				echo '<script>
                        window.location = "/users";
                    </script>';

			}

		} else {

			echo '<script>
                    window.location = "/users";
                </script>';

		}

	}

?>

<!-- Table with toolbar -->
<div class="card">
    
    <form method="post" class="needs-validation" novalidate autocomplete="off">
        
        <input type="hidden" value="<?php echo $usuario->id_usuario ?>" name="idUser">

        <div class="card-body">

            <?php

                require_once "controllers/users.controller.php";

                $create = new UsersController();
                $create -> edit($usuario->id_usuario);

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
                        value="<?php echo $usuario->nombres_usuario ?>"
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
                        value="<?php echo $usuario->email_usuario ?>"
                        required
                        disabled>

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
                        value="<?php echo $usuario->telefono_usuario ?>"
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

                            <?php if($usuario->rol_usuario == '1'): ?>
                                <option value="1" selected>Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="3">Cliente</option>
                            <?php elseif($usuario->rol_usuario == '2'): ?>
                                <option value="1">Administrador</option>
                                <option value="2" selected>Usuario</option>
                                <option value="3">Cliente</option>
                            <?php elseif($usuario->rol_usuario == '3'): ?>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="3" selected>Cliente</option>
                            <?php else: ?>
                                <option value="">Selecciona Rol</option>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="3">Cliente</option>
                            <?php endif ?>

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
                        name="password-user">

                        <label for="">Contraseña <sup class="text-danger">*</sup></label>
                    </div>

                </div>

                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">
                        <input
                        type="password"
                        class="form-control"
                        placeholder="Repite Contraseña"
                        name="repassword-user">

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