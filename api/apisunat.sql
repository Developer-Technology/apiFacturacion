-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-07-2024 a las 05:24:21
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `apisunat`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `id_configuracion` int(11) NOT NULL,
  `nombre_sistema_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre_empresa_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `web_empresa_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `logo_sistema_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `favicon_sistema_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `id_sunat_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `clave_sunat_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `keywords_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `servidor_correo_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `usuario_correo_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `clave_correo_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `puerto_correo_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `seguridad_correo_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `activo_correo_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `paypal_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `culqi_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `qr_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `extras_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `facturacion_configuracion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `creado_configuracion` date DEFAULT NULL,
  `actualizado_configuracion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`id_configuracion`, `nombre_sistema_configuracion`, `nombre_empresa_configuracion`, `descripcion_configuracion`, `web_empresa_configuracion`, `logo_sistema_configuracion`, `favicon_sistema_configuracion`, `id_sunat_configuracion`, `clave_sunat_configuracion`, `keywords_configuracion`, `servidor_correo_configuracion`, `usuario_correo_configuracion`, `clave_correo_configuracion`, `puerto_correo_configuracion`, `seguridad_correo_configuracion`, `activo_correo_configuracion`, `paypal_configuracion`, `culqi_configuracion`, `qr_configuracion`, `extras_configuracion`, `facturacion_configuracion`, `creado_configuracion`, `actualizado_configuracion`) VALUES
(1, 'APIFACT', 'Developer technology', 'API REST FULL para la facturación electrónica SUNAT', 'https://developer-technology.net/', '', '', NULL, NULL, '[\"sunat\",\"facturacion\",\"api\",\"cpe\"]', NULL, NULL, NULL, NULL, NULL, 'no', '[{\"client_id\":\"AQlSK5pzTMhnsu-u9WCLvFYyONZ2IZHEP5Ft9nSH2xrhyQPeJ3BDCHhGiMjBMFrEDTozlAhQLuOdAUD5\",\"secret_key\":\"EFPeb__QkEQCxj3CunqXVk9nQn6XYdZRLyxIzXE89McT2AiCI_7kKwkJK-NMwfwL8lZW3hx7WDyK5pm8\"}]', '[{\"public_key\":\"\",\"secret_key\":\"\"}]', '[{\"qr_yape\":\"\",\"qr_plin\":\"\",\"cuenta_bancaria\":\"\",\"ncuenta_bancaria\":\"\"}]', '[{\"reset_pass\":\"no\",\"register_system\":\"no\",\"social_login\":\"no\",\"supabase\":\"no\",\"supabaseUrl\":\"\",\"supabaseKey\":\"\",\"supabasePass\":\"\"}]', '[{\"estado\":\"activo\",\"factura\":{\"serie\":\"F001\",\"correlativo\":1},\"empresa\":{\"ruc\":\"11111111111\",\"razonSocial\":\"EMPRESA DEMOSTRACION SAC\",\"nombreComercial\":\"DEMO\",\"departamento\":\"LIMA\",\"provincia\":\"LIMA\",\"distrito\":\"LOS OLIVOS\",\"ubigeo\":\"150117\",\"direccion\":\"AVENIDA DEMOSTRACION 132\",\"telefono\":\"999999999\",\"email\":\"demo@gmail.com\"},\"sunat\":{\"modo\":\"beta\",\"usuarioSol\":\"MODDATOS\",\"claveSol\":\"moddatos\",\"claveCertificado\":\"\",\"expiraCertificado\":\"\"}}]', '2023-03-09', '2024-07-02 03:22:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `despatches`
--

CREATE TABLE `despatches` (
  `id_despatch` int(11) NOT NULL,
  `id_empresa_despatch` int(11) NOT NULL,
  `serie_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `number_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `emision_despatch` datetime DEFAULT NULL,
  `type_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `observation_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `docbaja_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `reldoc_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `recipient_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `third_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `transport_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `conductor_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `datasend_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `items_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `status_sunat_despatch` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `creado_despatch` date DEFAULT NULL,
  `actualizado_despatch` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` int(11) NOT NULL,
  `ruc_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `razon_social_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre_comercial_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefono_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `email_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `id_plan_empresa` int(11) DEFAULT NULL,
  `consumo_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `direccion_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `departamento_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `provincia_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `distrito_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `ubigeo_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `logo_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `fase_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `usuario_sol_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `clave_sol_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `certificado_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `expira_certificado_empresa` date DEFAULT NULL,
  `clave_certificado_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `client_id` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `client_secret` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado_empresa` int(11) DEFAULT NULL,
  `token_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `clave_secreta_empresa` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `proxima_facturacion_empresa` date DEFAULT NULL,
  `creado_empresa` date DEFAULT NULL,
  `actualizado_empresa` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id_plan` int(11) NOT NULL,
  `nombre_plan` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_plan` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `precio_plan` float DEFAULT NULL,
  `contiene_plan` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `ventas_plan` int(11) DEFAULT 0,
  `creado_plan` date DEFAULT NULL,
  `actualizado_plan` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id_plan`, `nombre_plan`, `descripcion_plan`, `precio_plan`, `contiene_plan`, `ventas_plan`, `creado_plan`, `actualizado_plan`) VALUES
(1, 'Ilimitado', 'Consultas ilimitadas', 150, '[{\"consultas\":\"ilimitado\",\"documentos\":\"ilimitado\"}]', 0, '2023-02-23', '2024-07-02 03:22:42'),
(2, 'Estándar', 'Solo para desarrollo', 50, '[{\"consultas\":\"1000\",\"documentos\":\"1000\"}]', 0, '2023-03-01', '2024-04-15 01:15:48'),
(3, 'Gratis', 'Plan gratis', 0, '[{\"consultas\":\"100\",\"documentos\":\"100\"}]', 0, '2023-03-01', '2024-06-09 21:15:06'),
(4, 'Premium', 'Plan premium', 100, '[{\"consultas\":\"2000\",\"documentos\":\"2000\"}]', 0, '2023-03-29', '2024-06-24 03:39:27'),
(5, 'Básico', 'plan basico', 30, '[{\"consultas\":\"500\",\"documentos\":\"500\"}]', 0, '2023-03-29', '2024-04-15 01:15:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `summaries`
--

CREATE TABLE `summaries` (
  `id_summary` int(11) NOT NULL,
  `id_empresa_summary` int(11) NOT NULL,
  `type_summary` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `serie_summary` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `number_summary` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `emision_summary` datetime DEFAULT NULL,
  `items_summary` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `status_sunat_summary` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `creado_summary` date DEFAULT NULL,
  `actualizado_summary` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripciones`
--

CREATE TABLE `suscripciones` (
  `id_suscripcion` int(11) NOT NULL,
  `id_empresa_suscripcion` int(11) NOT NULL,
  `id_usuario_suscripcion` int(11) NOT NULL,
  `id_plan_suscripcion` int(11) NOT NULL,
  `trans_suscripcion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_emision_suscripcion` date NOT NULL,
  `fecha_pago_suscripcion` date DEFAULT NULL,
  `monto_pago_suscripcion` double NOT NULL,
  `medio_pago_suscripcion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `comprobante_suscripcion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado_suscripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `adjunto_suscripcion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `supabase_suscripcion` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `creado_suscripcion` date NOT NULL,
  `actualizado_suscripcion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transportists`
--

CREATE TABLE `transportists` (
  `id_transportist` int(11) NOT NULL,
  `id_empresa_transportist` int(11) NOT NULL,
  `serie_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `number_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `emision_transportist` datetime DEFAULT NULL,
  `type_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `observation_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `recipient_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `transport_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `conductor_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `datasend_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `items_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `status_sunat_transportist` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `creado_transportist` date DEFAULT NULL,
  `actualizado_transportist` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `alias_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `clave_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `email_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombres_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefono_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `avatar_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `id_empresa_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `rol_usuario` int(11) DEFAULT NULL,
  `estado_usuario` int(11) DEFAULT NULL,
  `metodo_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `verificado_usuario` int(11) DEFAULT NULL,
  `token_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `token_exp_usuario` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `creado_usuario` date DEFAULT NULL,
  `actualizado_usuario` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `alias_usuario`, `clave_usuario`, `email_usuario`, `nombres_usuario`, `telefono_usuario`, `avatar_usuario`, `id_empresa_usuario`, `rol_usuario`, `estado_usuario`, `metodo_usuario`, `verificado_usuario`, `token_usuario`, `token_exp_usuario`, `creado_usuario`, `actualizado_usuario`) VALUES
(1, 'admin', '$2a$07$azybxcags23425sdg23sdeeKAqt96CqhlXh4xR.Kd9524vrpGvri6', 'admin@admin.com', 'Super Administrador', '999999999', '', '[]', 1, 1, 'Panel', 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MTk3ODk4NTIsImV4cCI6MTcxOTg3NjI1MiwiZGF0YSI6eyJpZCI6IjEiLCJlbWFpbCI6Im1hcmlvLnJvamFzLmNoYW5hbW90aEBnbWFpbC5jb20ifX0.ttLccYFNFfQGyE92xWn3_N-Dmcsfh_PmQxoN0T7IhRY', '1719876252', '2023-02-24', '2024-07-02 03:23:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_usuario_venta` int(11) NOT NULL,
  `id_plan_venta` int(11) NOT NULL,
  `id_empresa_venta` int(11) NOT NULL,
  `metodo_venta` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `trans_venta` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `moneda_venta` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `monto_venta` double DEFAULT NULL,
  `tipo_cambio_venta` double DEFAULT NULL,
  `estado_venta` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `creado_venta` date DEFAULT NULL,
  `actualizado_venta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `voideds`
--

CREATE TABLE `voideds` (
  `id_voided` int(11) NOT NULL,
  `id_empresa_voided` int(11) NOT NULL,
  `type_voided` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `serie_voided` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `number_voided` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `emision_voided` datetime DEFAULT NULL,
  `items_voided` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `status_sunat_voided` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `creado_voided` date DEFAULT NULL,
  `actualizado_voided` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `despatches`
--
ALTER TABLE `despatches`
  ADD PRIMARY KEY (`id_despatch`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id_plan`);

--
-- Indices de la tabla `summaries`
--
ALTER TABLE `summaries`
  ADD PRIMARY KEY (`id_summary`);

--
-- Indices de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD PRIMARY KEY (`id_suscripcion`);

--
-- Indices de la tabla `transportists`
--
ALTER TABLE `transportists`
  ADD PRIMARY KEY (`id_transportist`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`);

--
-- Indices de la tabla `voideds`
--
ALTER TABLE `voideds`
  ADD PRIMARY KEY (`id_voided`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `despatches`
--
ALTER TABLE `despatches`
  MODIFY `id_despatch` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `summaries`
--
ALTER TABLE `summaries`
  MODIFY `id_summary` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  MODIFY `id_suscripcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transportists`
--
ALTER TABLE `transportists`
  MODIFY `id_transportist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `voideds`
--
ALTER TABLE `voideds`
  MODIFY `id_voided` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
