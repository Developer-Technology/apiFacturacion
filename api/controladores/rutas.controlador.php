<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

/*=============================================
Incluimos la libreria
=============================================*/
use PHPMailer\PHPMailer\PHPMailer;

class ControladorRutas
{

    /*=============================================
    Ruta del sistema
    =============================================*/
    public static function path()
    {
        /* Produccion */
        //return "https://chanamoth.online/";

        /* Desarrollo */
        return "http://front.apifact.local/";

    }

    /*=============================================
    Ruta del API
    =============================================*/
    public static function api()
    {
        /* Produccion */
        //return "https://api.chanamoth.online/";

        /* Desarrollo */
        return "http://api.apifact.local/";

    }

    /*=============================================
    Ruta de las peticiones
    =============================================*/
    public function index()
    {

        include "rutas/rutas.php";

    }

    /*=============================================
    Creamos clave secreta unica tomando el ruc y hora de registro
    =============================================*/
    public static function secretKey($ruc)
    {

        $clave_secreta = date('H:i:s') . "RUC:c5LTA6WPbMwHhEabYu77nN9cn4VcMj";
        $iv = "0123456789abcdef";

        $ruc_encriptado = openssl_encrypt($ruc, "AES-256-CBC", $clave_secreta, OPENSSL_RAW_DATA, $iv);
        $ruc_encriptado = substr(rtrim(base64_encode($ruc_encriptado), "="), 0, 18);
        $caracteres = str_split($ruc_encriptado, 1);

        for ($i = 3; $i < count($caracteres); $i += 4) {

            array_splice($caracteres, $i, 0, "-");

        }

        $ruc_formateado = implode("", $caracteres);

        return rtrim(strtolower($ruc_formateado));

    }

    /*=============================================
    Creamos el token unico
    =============================================*/
    public static function tokenCreate($long)
    {
        $token = bin2hex(random_bytes($long));
        return $token;
    }

    /*=============================================
    Generamos un avatar con los datos del usuario en su registro
    =============================================*/
    public static function generateAvatar($name)
    {

        $words = explode('.', $name);
        $initial = '';

        foreach ($words as $word) {

            $initial .= strtoupper(substr($word, 0, 1));

        }

        $initial = substr($initial, 0, 2);

        $img = imagecreate(50, 50);

        $red = rand(0, 255);
        $green = rand(0, 255);
        $blue = rand(0, 255);

        $bgColor = imagecolorallocate($img, $red, $green, $blue);
        imagefill($img, 0, 0, $bgColor);

        $textColor = imagecolorallocate($img, 0, 0, 0);

        $imgWidth = imagesx($img);
        $imgHeight = imagesy($img);

        $font = 40;
        $textWidth = imagefontwidth($font) * strlen($initial);
        $textHeight = imagefontheight($font);
        $x = ($imgWidth - $textWidth) / 2;
        $y = ($imgHeight - $textHeight) / 2;

        imagestring($img, $font, $x, $y, $initial, $textColor);

        $avatarFilename = 'avatar_' . uniqid() . '.png';
        imagepng($img, 'views/assets/img/users/' . $avatarFilename);

        return $avatarFilename;

    }

    /*=============================================
    Funcion para enviar correo
    =============================================*/
    public static function sendEmail($name, $subject, $email, $message, $txt, $url, $attachments)
    {

        /*=============================================
        Definimos la zona horaria
        =============================================*/
        date_default_timezone_set("America/Lima");

        /*=============================================
        Recogemos los datos del sistema
        =============================================*/
        $dataSett = GetModel::getDataFilter("configuraciones", "*", "id_configuracion", 1, null, null, null, null);

        /* Validamos si tiene web */
        if($dataSett[0]->web_empresa_configuracion != '') {
            
            $team = '<a href="' . $dataSett[0]->web_empresa_configuracion . '">' . $dataSett[0]->nombre_empresa_configuracion . '</a>';

        } else {

            $team = $dataSett[0]->nombre_empresa_configuracion;

        }

        /* Validamos si tiene logo */
        if($dataSett[0]->logo_sistema_configuracion == '') {

            $imgSett = ControladorRutas::path() . 'documents/img/default/logo.png';

        } else {

            $imgSett = ControladorRutas::path() . 'documents/img/logo/' . $dataSett[0]->logo_sistema_configuracion;

        }

        $mail = new PHPMailer;
        $mail->Charset = "UTF-8";
        /* */
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $dataSett[0]->servidor_correo_configuracion;
        $mail->Username = $dataSett[0]->usuario_correo_configuracion;
        $mail->Password = $dataSett[0]->clave_correo_configuracion;
        $mail->SMTPSecure = $dataSett[0]->seguridad_correo_configuracion;
        $mail->Port = $dataSett[0]->puerto_correo_configuracion;
        /* */
        $mail->setFrom(ControladorRutas::email(), $dataSett[0]->nombre_empresa_configuracion);
        $mail->Subject = "Hola " . $name . " - " . $subject;
        $mail->addAddress($email);
        $mail->msgHTML('

			<div>
                <img src="' . $imgSett . '" height="40"><br>
				Hola, ' . $name . ':
				<p>' . $message . '</p>
				<a href="' . $url . '">' . $txt . '</a>
				Atentamente, el equipo de <b>' . $team . '</b>.<br>
				Gracias
			</div>

		');

        if($attachments != NULL) {

            // Adjuntar múltiples archivos
            foreach ($attachments as $attachment) {
                $filePath = parse_url($attachment['archivo'], PHP_URL_PATH);
                $filePath = $_SERVER['DOCUMENT_ROOT'] . $filePath; // Ajustar la ruta según sea necesario
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath);
                }
            }

        }

        $send = $mail->Send();

        if (!$send) {

            return $mail->ErrorInfo;

        } else {

            return "ok";

        }

    }

    /*=============================================
    Función para mayúscula inicial
    =============================================*/
    public static function capitalize($value)
    {

        $value = mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
        return $value;

    }

    /*=============================================
    Convertir fecha a español
    =============================================*/
    public static function fechaEsShort($fecha)
    {

        $fecha = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia = date('l', strtotime($fecha));
        $mes = date('F', strtotime($fecha));
        $anio = date('Y', strtotime($fecha));
        $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);

        return $nombreMes . " " . $numeroDia . ", " . $anio;

    }

    /*=============================================
    Generar Token de Autenticación
    =============================================*/
    public static function jwt($id, $email)
    {

        $time = time();

        $token = array(

            "iat" => $time, //Tiempo en que inicia el token
            "exp" => $time + (60 * 60 * 24), // Tiempo en que expirará el token (1 día)
            "data" => [

                "id" => $id,
                "email" => $email,
            ],

        );

        return $token;
    }

    /*=============================================
    Validar el token de seguridad
    =============================================*/
    public static function tokenValidate($token, $table, $suffix)
    {

        /*=============================================
        Traemos el usuario de acuerdo al token
        =============================================*/
        $user = GetModel::getDataFilter($table, "token_exp_" . $suffix, "token_" . $suffix, $token, null, null, null, null);

        if (!empty($user)) {

            /*=============================================
            Validamos que el token no haya expirado
            =============================================*/

            $time = time();

            if ($time < $user[0]->{"token_exp_" . $suffix}) {

                return "ok";

            } else {

                return "expired";
            }

        } else {

            return "no-auth";

        }

    }

    /*=============================================
    Genera una contraseña aleatoria
    =============================================*/
    public static function generar_clave($longitud)
    {

        $cadena = "[^A-Z0-9]";
        return substr(preg_replace($cadena, "", sha1(md5(rand()))) .
            preg_replace($cadena, "", sha1(md5(rand()))) .
            preg_replace($cadena, "", sha1(md5(rand()))),
            0, $longitud);

    }

    /*=============================================
    Creamos un id unico para las transacciones que no sean paypal o culqui
    =============================================*/
    public static function generateUniqueId($length = 17) {

        $microtime = microtime(true);
        $baseTime = str_replace('.', '', $microtime);
        $randomString = bin2hex(random_bytes(5));
        $uniqueId = $baseTime . $randomString;
        
        if (strlen($uniqueId) > $length) {
            $uniqueId = substr($uniqueId, 0, $length);
        }
        
        return $uniqueId;
        
    }

    /*=============================================
    Creamos un correo unico aleatorio
    =============================================*/
    public static function generateRandomEmail($email) {
        // Divide el correo en el usuario y el dominio
        list($user, $domain) = explode('@', $email);
        
        // Genera una parte única usando random_bytes
        $uniquePart = bin2hex(random_bytes(4));
        
        // Crea el correo único
        $uniqueEmail = $user . '.' . $uniquePart . '@' . $domain;
        
        return $uniqueEmail;
    }

    /*=============================================
    Email del super admin
    =============================================*/
    public static function email()
    {

        return "admin@tukifac.pe";

    }

    /*=============================================
    Creamos un codigo unico para los servicios
    =============================================*/
    public static function generarCodigoProducto($nombreProducto, $idProducto) {
        // Tomar las primeras tres letras de cada palabra del nombre del producto
        $partesNombre = explode(' ', $nombreProducto);
        $codigo = '';
        foreach ($partesNombre as $parte) {
            $codigo .= substr($parte, 0, 3);
        }
    
        // Añadir un número secuencial único
        $codigo .= str_pad($idProducto, 5, '0', STR_PAD_LEFT); // Rellenar con ceros a la izquierda hasta tener 5 dígitos
    
        return strtoupper($codigo); // Convertir a mayúsculas
    }

    /*=============================================
    Convertimos numeros a letras
    =============================================*/
    public static function unidad($numuero)
    {
        switch ($numuero) {
            case 9:
                {
                    $numu = "NUEVE";
                    break;
                }
            case 8:
                {
                    $numu = "OCHO";
                    break;
                }
            case 7:
                {
                    $numu = "SIETE";
                    break;
                }
            case 6:
                {
                    $numu = "SEIS";
                    break;
                }
            case 5:
                {
                    $numu = "CINCO";
                    break;
                }
            case 4:
                {
                    $numu = "CUATRO";
                    break;
                }
            case 3:
                {
                    $numu = "TRES";
                    break;
                }
            case 2:
                {
                    $numu = "DOS";
                    break;
                }
            case 1:
                {
                    $numu = "UN";
                    break;
                }
            case 0:
                {
                    $numu = "";
                    break;
                }
        }
        return $numu;
    }

    public static function decena($numdero)
    {

        if ($numdero >= 90 && $numdero <= 99) {
            $numd = "NOVENTA ";
            if ($numdero > 90) {
                $numd = $numd . "Y " . (ControladorRutas::unidad($numdero - 90));
            }

        } else if ($numdero >= 80 && $numdero <= 89) {
            $numd = "OCHENTA ";
            if ($numdero > 80) {
                $numd = $numd . "Y " . (ControladorRutas::unidad($numdero - 80));
            }

        } else if ($numdero >= 70 && $numdero <= 79) {
            $numd = "SETENTA ";
            if ($numdero > 70) {
                $numd = $numd . "Y " . (ControladorRutas::unidad($numdero - 70));
            }

        } else if ($numdero >= 60 && $numdero <= 69) {
            $numd = "SESENTA ";
            if ($numdero > 60) {
                $numd = $numd . "Y " . (ControladorRutas::unidad($numdero - 60));
            }

        } else if ($numdero >= 50 && $numdero <= 59) {
            $numd = "CINCUENTA ";
            if ($numdero > 50) {
                $numd = $numd . "Y " . (ControladorRutas::unidad($numdero - 50));
            }

        } else if ($numdero >= 40 && $numdero <= 49) {
            $numd = "CUARENTA ";
            if ($numdero > 40) {
                $numd = $numd . "Y " . (ControladorRutas::unidad($numdero - 40));
            }

        } else if ($numdero >= 30 && $numdero <= 39) {
            $numd = "TREINTA ";
            if ($numdero > 30) {
                $numd = $numd . "Y " . (ControladorRutas::unidad($numdero - 30));
            }

        } else if ($numdero >= 20 && $numdero <= 29) {
            if ($numdero == 20) {
                $numd = "VEINTE ";
            } else {
                $numd = "VEINTI" . (ControladorRutas::unidad($numdero - 20));
            }

        } else if ($numdero >= 10 && $numdero <= 19) {
            switch ($numdero) {
                case 10:
                    {
                        $numd = "DIEZ ";
                        break;
                    }
                case 11:
                    {
                        $numd = "ONCE";
                        break;
                    }
                case 12:
                    {
                        $numd = "DOCE";
                        break;
                    }
                case 13:
                    {
                        $numd = "TRECE";
                        break;
                    }
                case 14:
                    {
                        $numd = "CATORCE";
                        break;
                    }
                case 15:
                    {
                        $numd = "QUINCE";
                        break;
                    }
                case 16:
                    {
                        $numd = "DIECISEIS";
                        break;
                    }
                case 17:
                    {
                        $numd = "DIECISIETE";
                        break;
                    }
                case 18:
                    {
                        $numd = "DIECIOCHO";
                        break;
                    }
                case 19:
                    {
                        $numd = "DIECINUEVE";
                        break;
                    }
            }
        } else {
            $numd = ControladorRutas::unidad($numdero);
        }

        return $numd;
    }

    public static function centena($numc)
    {
        if ($numc >= 100) {
            if ($numc >= 900 && $numc <= 999) {
                $numce = "NOVECIENTOS ";
                if ($numc > 900) {
                    $numce = $numce . (ControladorRutas::decena($numc - 900));
                }

            } else if ($numc >= 800 && $numc <= 899) {
                $numce = "OCHOCIENTOS ";
                if ($numc > 800) {
                    $numce = $numce . (ControladorRutas::decena($numc - 800));
                }

            } else if ($numc >= 700 && $numc <= 799) {
                $numce = "SETECIENTOS ";
                if ($numc > 700) {
                    $numce = $numce . (ControladorRutas::decena($numc - 700));
                }

            } else if ($numc >= 600 && $numc <= 699) {
                $numce = "SEISCIENTOS ";
                if ($numc > 600) {
                    $numce = $numce . (ControladorRutas::decena($numc - 600));
                }

            } else if ($numc >= 500 && $numc <= 599) {
                $numce = "QUINIENTOS ";
                if ($numc > 500) {
                    $numce = $numce . (ControladorRutas::decena($numc - 500));
                }

            } else if ($numc >= 400 && $numc <= 499) {
                $numce = "CUATROCIENTOS ";
                if ($numc > 400) {
                    $numce = $numce . (ControladorRutas::decena($numc - 400));
                }

            } else if ($numc >= 300 && $numc <= 399) {
                $numce = "TRESCIENTOS ";
                if ($numc > 300) {
                    $numce = $numce . (ControladorRutas::decena($numc - 300));
                }

            } else if ($numc >= 200 && $numc <= 299) {
                $numce = "DOSCIENTOS ";
                if ($numc > 200) {
                    $numce = $numce . (ControladorRutas::decena($numc - 200));
                }

            } else if ($numc >= 100 && $numc <= 199) {
                if ($numc == 100) {
                    $numce = "CIEN ";
                } else {
                    $numce = "CIENTO " . (ControladorRutas::decena($numc - 100));
                }

            }
        } else {
            $numce = ControladorRutas::decena($numc);
        }

        return $numce;
    }

    public static function miles($nummero)
    {
        if ($nummero >= 1000 && $nummero < 2000) {
            $numm = "MIL " . (ControladorRutas::centena($nummero % 1000));
        }
        if ($nummero >= 2000 && $nummero < 10000) {
            $numm = ControladorRutas::unidad(Floor($nummero / 1000)) . " MIL " . (ControladorRutas::centena($nummero % 1000));
        }
        if ($nummero < 1000) {
            $numm = ControladorRutas::centena($nummero);
        }

        return $numm;
    }

    public static function decmiles($numdmero)
    {
        if ($numdmero == 10000) {
            $numde = "DIEZ MIL";
        }

        if ($numdmero > 10000 && $numdmero < 20000) {
            $numde = ControladorRutas::decena(Floor($numdmero / 1000)) . "MIL " . (ControladorRutas::centena($numdmero % 1000));
        }
        if ($numdmero >= 20000 && $numdmero < 100000) {
            $numde = ControladorRutas::decena(Floor($numdmero / 1000)) . " MIL " . (ControladorRutas::miles($numdmero % 1000));
        }
        if ($numdmero < 10000) {
            $numde = ControladorRutas::miles($numdmero);
        }

        return $numde;
    }

    public static function cienmiles($numcmero)
    {
        if ($numcmero == 100000) {
            $num_letracm = "CIEN MIL";
        }

        if ($numcmero >= 100000 && $numcmero < 1000000) {
            $num_letracm = ControladorRutas::centena(Floor($numcmero / 1000)) . " MIL " . (ControladorRutas::centena($numcmero % 1000));
        }
        if ($numcmero < 100000) {
            $num_letracm = ControladorRutas::decmiles($numcmero);
        }

        return $num_letracm;
    }

    public static function millon($nummiero)
    {
        if ($nummiero >= 1000000 && $nummiero < 2000000) {
            $num_letramm = "UN MILLON " . (ControladorRutas::cienmiles($nummiero % 1000000));
        }
        if ($nummiero >= 2000000 && $nummiero < 10000000) {
            $num_letramm = ControladorRutas::unidad(Floor($nummiero / 1000000)) . " MILLONES " . (ControladorRutas::cienmiles($nummiero % 1000000));
        }
        if ($nummiero < 1000000) {
            $num_letramm = ControladorRutas::cienmiles($nummiero);
        }

        return $num_letramm;
    }

    public static function decmillon($numerodm)
    {
        if ($numerodm == 10000000) {
            $num_letradmm = "DIEZ MILLONES";
        }

        if ($numerodm > 10000000 && $numerodm < 20000000) {
            $num_letradmm = ControladorRutas::decena(Floor($numerodm / 1000000)) . "MILLONES " . (ControladorRutas::cienmiles($numerodm % 1000000));
        }
        if ($numerodm >= 20000000 && $numerodm < 100000000) {
            $num_letradmm = ControladorRutas::decena(Floor($numerodm / 1000000)) . " MILLONES " . (ControladorRutas::millon($numerodm % 1000000));
        }
        if ($numerodm < 10000000) {
            $num_letradmm = ControladorRutas::millon($numerodm);
        }

        return $num_letradmm;
    }

    public static function cienmillon($numcmeros)
    {
        if ($numcmeros == 100000000) {
            $num_letracms = "CIEN MILLONES";
        }

        if ($numcmeros >= 100000000 && $numcmeros < 1000000000) {
            $num_letracms = ControladorRutas::centena(Floor($numcmeros / 1000000)) . " MILLONES " . (ControladorRutas::millon($numcmeros % 1000000));
        }
        if ($numcmeros < 100000000) {
            $num_letracms = ControladorRutas::decmillon($numcmeros);
        }

        return $num_letracms;
    }

    public static function milmillon($nummierod)
    {
        if ($nummierod >= 1000000000 && $nummierod < 2000000000) {
            $num_letrammd = "MIL " . (ControladorRutas::cienmillon($nummierod % 1000000000));
        }
        if ($nummierod >= 2000000000 && $nummierod < 10000000000) {
            $num_letrammd = ControladorRutas::unidad(Floor($nummierod / 1000000000)) . " MIL " . (ControladorRutas::cienmillon($nummierod % 1000000000));
        }
        if ($nummierod < 1000000000) {
            $num_letrammd = ControladorRutas::cienmillon($nummierod);
        }

        return $num_letrammd;
    }

    public static function convertir($numero)
    {
        $numf = ControladorRutas::milmillon($numero);
        return $numf;
    }

}