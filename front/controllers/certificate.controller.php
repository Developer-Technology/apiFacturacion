<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

class CertificateController
{

    /*=============================================
    Cargar certificado digital
    =============================================*/
    public function certificate($ruc)
    {

        if (isset($_POST["ruc-tenant"])) {

            /*=============================================
            Mensaje de carga
            =============================================*/
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Cargando...", "");
                </script>';

            if ($_POST["ruc-tenant"] == $ruc) {

                /*=============================================
                Recogemos los datos
                =============================================*/
                $url = "certificate";
                $method = "POST";
                $token = $_SESSION["empresa"]->token_empresa;
                $data = array(
                    "claveSecreta" => $_SESSION["empresa"]->clave_secreta_empresa,
                    "file" => $_FILES["file-certificate"]["tmp_name"],
                    "type" => $_FILES["file-certificate"]["type"],
                    "faseEmpresa" => $_POST["fase-tenant"],
                    "usuarioSol" => $_POST["usuario_sol-tenant"],
                    "claveSol" => $_POST["clave_sol-tenant"],
                    "claveCertificado" => $_POST["clave_certificate-tenant"],
                    "expiraCertificado" => $_POST["expired_certificate-tenant"],
                    "clientIdGR" => $_POST["client_id-tenant"],
                    "clientSecretGR" => $_POST["client_secret-tenant"],
                );
                $fields = json_encode($data);

                $saveCertificate = CurlController::requestSunat($url, $method, $fields, $token);

                /*=============================================
                Respuesta de la API
                =============================================*/
                if ($saveCertificate->response->success == true) {

                    echo '<script>
								fncFormatInputs();
								matPreloader("off");
								fncSweetAlert("close", "", "");
								fncSweetAlert("success", "' . $saveCertificate->response->message . '", "/businesses/certificate");
							</script>';

                } else {

                    echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncNotie(3, "' . $saveCertificate->response->message . '");
                            </script>';

                }

            }

        }

    }

}