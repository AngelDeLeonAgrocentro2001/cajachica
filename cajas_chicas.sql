-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-03-2025 a las 18:38:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cajas_chicas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cuenta_contable` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `accesos`
--

INSERT INTO `accesos` (`id`, `id_usuario`, `id_cuenta_contable`) VALUES
(5, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `id_liquidacion` int(11) DEFAULT NULL,
  `id_detalle_liquidacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` enum('APROBADO','RECHAZADO','EXPORTADO_SAP') NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_accion` varchar(50) NOT NULL,
  `usuario_nombre` varchar(100) NOT NULL,
  `detalles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `id_liquidacion`, `id_detalle_liquidacion`, `id_usuario`, `accion`, `comentario`, `fecha`, `tipo_accion`, `usuario_nombre`, `detalles`) VALUES
(1, 1, 1, 1, '', NULL, '2025-03-01 16:00:00', '', '', NULL),
(2, 1, 2, 1, '', NULL, '2025-03-02 17:00:00', '', '', NULL),
(8, 4, NULL, 2, 'APROBADO', NULL, '2025-03-06 14:14:09', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(9, 4, NULL, 2, 'APROBADO', NULL, '2025-03-06 14:28:35', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por encargado'),
(11, 1, 1, 2, 'APROBADO', NULL, '2025-03-06 15:03:45', 'ACTUALIZADO', 'Encargado 1', 'Detalle de liquidación actualizado'),
(12, 1, NULL, 2, 'APROBADO', NULL, '2025-03-06 15:03:45', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(14, 1, NULL, 4, 'APROBADO', NULL, '2025-03-06 15:30:41', 'ACTUALIZADO', 'Contador 1', 'Liquidación actualizada por usuario'),
(15, 1, 1, 4, 'APROBADO', NULL, '2025-03-06 16:45:12', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'Revisión aprobada'),
(17, 2, NULL, 2, 'APROBADO', NULL, '2025-03-06 17:17:32', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(19, 1, 2, 4, 'APROBADO', NULL, '2025-03-06 17:29:38', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'Revisión aprobada'),
(20, 1, NULL, 4, 'APROBADO', NULL, '2025-03-06 17:29:38', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'Liquidación autorizada'),
(23, 2, NULL, 2, 'APROBADO', NULL, '2025-03-06 18:00:50', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(25, 2, NULL, 2, 'APROBADO', NULL, '2025-03-06 18:01:01', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(27, 2, NULL, 2, 'APROBADO', NULL, '2025-03-06 18:01:11', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(29, 2, 6, 1, 'APROBADO', NULL, '2025-03-06 18:03:25', 'CREADO', 'Administrador', 'Detalle de liquidación creado por encargado'),
(30, 2, 6, 4, 'APROBADO', NULL, '2025-03-06 18:04:11', 'RECHAZADO_POR_CONTABILIDAD', 'Contador 1', 'no completo'),
(31, 1, 1, 1, 'APROBADO', NULL, '2025-03-06 18:30:37', 'AUTORIZADO_POR_CONTABILIDAD', 'Administrador', 'Autorizado por admin'),
(32, 1, NULL, 1, 'APROBADO', NULL, '2025-03-06 18:30:37', 'AUTORIZADO_POR_CONTABILIDAD', 'Administrador', 'Liquidación autorizada'),
(33, 1, 2, 1, 'APROBADO', NULL, '2025-03-06 18:31:20', 'DESCARTADO', 'Administrador', 'se descarto'),
(34, 1, NULL, 1, 'APROBADO', NULL, '2025-03-06 18:31:20', 'PENDIENTE_CORRECCIÓN', 'Administrador', 'Liquidación marcada para corrección'),
(35, 1, 1, 1, 'APROBADO', NULL, '2025-03-06 18:31:32', 'AUTORIZADO_POR_CONTABILIDAD', 'Administrador', 'autorisado'),
(36, 1, NULL, 1, 'APROBADO', NULL, '2025-03-06 18:31:32', 'PENDIENTE_CORRECCIÓN', 'Administrador', 'Liquidación marcada para corrección'),
(37, 4, 7, 1, 'APROBADO', NULL, '2025-03-06 18:33:22', 'CREADO', 'Administrador', 'Detalle de liquidación creado por encargado'),
(38, 1, NULL, 3, 'APROBADO', NULL, '2025-03-06 18:51:33', 'ACTUALIZADO', 'Supervisor 1', 'Liquidación actualizada por usuario'),
(39, 2, 8, 1, 'APROBADO', NULL, '2025-03-06 19:08:12', 'CREADO', 'Administrador', 'Detalle de liquidación creado por encargado'),
(40, 2, 8, 4, 'APROBADO', NULL, '2025-03-06 19:09:35', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'Acturizado por contabilidad'),
(43, 4, 7, 1, 'APROBADO', NULL, '2025-03-06 19:16:43', 'RECHAZADO_POR_CONTABILIDAD', 'Administrador', 'sfs'),
(46, 2, 8, 1, 'APROBADO', NULL, '2025-03-06 19:17:57', 'RECHAZADO_POR_CONTABILIDAD', 'Administrador', ' cc'),
(47, 1, 1, 1, 'APROBADO', NULL, '2025-03-06 19:18:13', 'RECHAZADO_POR_CONTABILIDAD', 'Administrador', 'ccc'),
(48, 1, NULL, 1, 'APROBADO', NULL, '2025-03-06 19:18:13', 'PENDIENTE_CORRECCIÓN', 'Administrador', 'Liquidación marcada para corrección'),
(49, 1, 1, 1, 'APROBADO', NULL, '2025-03-06 19:18:22', 'AUTORIZADO_POR_CONTABILIDAD', 'Administrador', 'cxcx'),
(50, 1, NULL, 1, 'APROBADO', NULL, '2025-03-06 19:18:22', 'PENDIENTE_CORRECCIÓN', 'Administrador', 'Liquidación marcada para corrección'),
(51, 1, 9, 1, 'APROBADO', NULL, '2025-03-06 19:19:15', 'CREADO', 'Administrador', 'Detalle de liquidación creado por encargado'),
(52, 1, 9, 1, 'APROBADO', NULL, '2025-03-06 19:20:04', 'ACTUALIZADO', 'Administrador', 'Detalle de liquidación actualizado'),
(53, 1, 9, 4, 'APROBADO', NULL, '2025-03-06 19:20:45', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'Actorizado por contabilidad'),
(54, 1, NULL, 4, 'APROBADO', NULL, '2025-03-06 19:20:45', 'PENDIENTE_CORRECCIÓN', 'Contador 1', 'Liquidación marcada para corrección'),
(55, 4, 10, 1, 'APROBADO', NULL, '2025-03-06 20:14:13', 'CREADO', 'Administrador', 'Detalle de liquidación creado por encargado'),
(56, 4, 10, 1, 'APROBADO', NULL, '2025-03-06 20:16:11', 'AUTORIZADO_POR_CONTABILIDAD', 'Administrador', 'admin'),
(57, 4, 11, 1, 'APROBADO', NULL, '2025-03-06 20:17:58', 'CREADO', 'Administrador', 'Detalle de liquidación creado por encargado'),
(58, 4, 11, 1, 'APROBADO', NULL, '2025-03-06 20:47:46', 'AUTORIZADO_POR_ADMIN', 'Administrador', 'por admin'),
(59, 4, 11, 1, 'APROBADO', NULL, '2025-03-06 20:48:14', 'AUTORIZADO_POR_ADMIN', 'Administrador', 'Autorizado'),
(60, 4, 12, 1, 'APROBADO', NULL, '2025-03-06 20:50:58', 'CREADO', 'Administrador', 'Detalle de liquidación creado por encargado'),
(61, 4, 12, 4, 'APROBADO', NULL, '2025-03-06 20:51:46', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'Aprobado'),
(62, 4, 12, 1, 'APROBADO', NULL, '2025-03-06 20:52:32', 'AUTORIZADO_POR_ADMIN', 'Administrador', 'aprovada por admin'),
(63, 5, NULL, 2, 'APROBADO', NULL, '2025-03-07 21:25:04', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(64, 5, NULL, 2, 'APROBADO', NULL, '2025-03-07 21:25:14', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por usuario'),
(65, 5, NULL, 4, 'APROBADO', NULL, '2025-03-07 21:29:20', 'ACTUALIZADO', 'Contador 12', 'Liquidación actualizada por usuario'),
(66, 5, NULL, 3, 'APROBADO', NULL, '2025-03-07 21:29:58', 'ACTUALIZADO', 'Supervisor 1', 'Liquidación actualizada por usuario'),
(67, 4, NULL, 4, 'APROBADO', NULL, '2025-03-07 21:43:55', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 12', 'esta en orden'),
(68, 4, NULL, 4, 'APROBADO', NULL, '2025-03-07 21:53:11', 'EXPORTADO', 'Contador 12', 'Liquidación exportada a SAP como liquidacion_4_20250307_225311.csv'),
(69, 5, 13, 1, 'APROBADO', NULL, '2025-03-10 15:13:57', 'CREADO', 'Administrador', 'Detalle de liquidación creado por encargado'),
(70, 5, NULL, 1, 'APROBADO', NULL, '2025-03-10 15:41:25', 'EXPORTADO', 'Administrador', 'Liquidación exportada a SAP como liquidacion_5_20250310_164125.csv'),
(71, 5, NULL, 1, 'APROBADO', NULL, '2025-03-10 16:39:51', 'EXPORTADO', 'Administrador', 'Liquidación reexportada a SAP como liquidacion_5_20250310_173951.csv'),
(72, 2, NULL, 1, 'APROBADO', NULL, '2025-03-10 16:42:05', 'AUTORIZADO_POR_CONTABILIDAD', 'Administrador', 'Autorizado por admin'),
(73, 2, NULL, 1, 'APROBADO', NULL, '2025-03-10 16:42:13', 'EXPORTADO', 'Administrador', 'Liquidación exportada a SAP como liquidacion_2_20250310_174213.csv'),
(74, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-10 18:40:37', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: contador2@example.com'),
(75, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-10 18:41:45', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: contador2@example.com'),
(76, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-10 18:41:53', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: contador1@example.com'),
(77, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-10 18:43:00', 'ELIMINAR_USUARIO', 'Administrador', 'Usuario eliminado: contador2@example.com'),
(78, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-11 13:37:53', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: encargado2@example.com'),
(79, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-11 13:38:05', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: encargado2@example.com'),
(80, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-11 13:38:15', 'ELIMINAR_USUARIO', 'Administrador', 'Usuario eliminado: encargado2@example.com'),
(81, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-11 13:41:37', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: contador2@example.com'),
(82, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-11 13:41:59', 'ELIMINAR_USUARIO', 'Administrador', 'Usuario eliminado: contador2@example.com'),
(83, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-11 14:20:20', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: angel.deleon@agrocentro.com'),
(84, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-11 15:21:09', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: angel.deleon@agrocentro.com'),
(85, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-11 15:32:29', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: angel.deleon@agrocentro.com'),
(86, NULL, NULL, 8, 'APROBADO', NULL, '2025-03-11 15:59:46', 'ACTUALIZAR_USUARIO', 'Angel De Leon', 'Usuario actualizado: angel.deleon@agrocentro.com'),
(89, 4, NULL, 1, 'APROBADO', NULL, '2025-03-14 14:14:36', 'CREAR_FACTURA', 'Administrador', '{\"cuenta_id\":51,\"base_id\":1,\"numero_factura\":\"FACT-005\",\"fecha\":\"2025-03-14\",\"proveedor\":\"Proveedor AAA\",\"monto\":1500,\"estado\":\"PENDIENTE\"}'),
(90, 4, NULL, 1, 'APROBADO', NULL, '2025-03-14 14:16:11', 'AUTORIZAR_FACTURA', 'Administrador', '{\"accion\":\"APROBADO\",\"comentario\":\"por admin\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(91, 4, NULL, 1, 'APROBADO', NULL, '2025-03-14 14:17:07', 'PAGAR_FACTURA', 'Administrador', '{\"accion\":\"PAGADA\",\"comentario\":\"Pagada\",\"estado_anterior\":\"APROBADO\",\"estado_nuevo\":\"PAGADA\"}'),
(92, 5, NULL, 1, 'APROBADO', NULL, '2025-03-14 14:47:41', 'CREAR_FACTURA', 'Administrador', '{\"cuenta_id\":1,\"base_id\":1,\"numero_factura\":\"FACT-006\",\"fecha\":\"2025-03-14\",\"proveedor\":\"Proveedor AAA\",\"monto\":3000,\"estado\":\"PENDIENTE\"}'),
(96, 5, NULL, 1, 'APROBADO', NULL, '2025-03-14 14:50:53', 'AUTORIZAR_FACTURA', 'Administrador', '{\"accion\":\"APROBADO\",\"comentario\":\"Aprobada\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(98, 5, NULL, 4, 'APROBADO', NULL, '2025-03-14 14:52:00', 'RECHAZAR_FACTURA_CONTABILIDAD', 'Contador 1', '{\"accion\":\"RECHAZADO\",\"comentario\":\"falto pago\",\"estado_anterior\":\"APROBADO\",\"estado_nuevo\":\"RECHAZADO\"}'),
(110, NULL, 11, 1, 'APROBADO', NULL, '2025-03-14 15:48:14', 'CREAR_FACTURA', 'Administrador', '{\"cuenta_id\":51,\"base_id\":1,\"numero_factura\":\"FACT-0022\",\"fecha\":\"2025-03-14\",\"proveedor\":\"Proveedor AAA\",\"monto\":1500,\"estado\":\"PENDIENTE\"}'),
(111, NULL, 11, 3, 'APROBADO', NULL, '2025-03-14 15:49:50', 'AUTORIZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"APROBADO\",\"comentario\":\"Aprbada\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(112, NULL, 11, 4, 'APROBADO', NULL, '2025-03-14 15:50:28', 'PAGAR_FACTURA', 'Contador 1', '{\"accion\":\"PAGADA\",\"comentario\":\"se aprobo\",\"estado_anterior\":\"APROBADO\",\"estado_nuevo\":\"PAGADA\"}'),
(113, 1, NULL, 1, 'APROBADO', NULL, '2025-03-14 15:51:44', 'AUTORIZADO_POR_CONTABILIDAD', 'Administrador', 'se autorizo'),
(114, 1, NULL, 1, 'APROBADO', NULL, '2025-03-14 15:51:48', 'EXPORTADO', 'Administrador', 'Liquidación exportada a SAP como liquidacion_1_20250314_165148.csv'),
(115, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-14 17:16:09', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: angel.deleon@agrocentro.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bases`
--

CREATE TABLE `bases` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `data_base` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `bases`
--

INSERT INTO `bases` (`id`, `nombre`, `data_base`) VALUES
(1, 'AGROCENTRO GUATEMALA', NULL),
(2, 'AGROCENTRO HONDURAS', NULL),
(3, 'CINDECO EL SALVADOR', NULL),
(4, 'CINDECO NICARAGUA', NULL),
(5, 'AGROCENTRO MEXICO', NULL),
(6, 'AGROCENTRO PANAMA', NULL),
(7, 'AGRICOLA FECUNDA', NULL),
(8, 'AGRICOLA CONTINENTAL', NULL),
(9, 'KORSA CHEMICALS', NULL),
(10, 'BLACK BULL BIKES', NULL),
(11, 'CINDECO GUATEMALA', NULL),
(12, 'GT BRANDMASTER 2016', NULL),
(13, 'Agrocentro Colombia', NULL),
(14, 'Wohlhabenheit Holdings Corp.', NULL),
(15, 'AGROCENTRO GUATEMALA TEST', NULL),
(16, 'AGROCENTRO GUATEMALA', NULL),
(17, 'AGROCENTRO HONDURAS', NULL),
(18, 'CINDECO EL SALVADOR', NULL),
(19, 'CINDECO NICARAGUA', NULL),
(20, 'AGROCENTRO MEXICO', NULL),
(21, 'AGROCENTRO PANAMA', NULL),
(22, 'AGRICOLA FECUNDA', NULL),
(23, 'AGRICOLA CONTINENTAL', NULL),
(24, 'KORSA CHEMICALS', NULL),
(25, 'BLACK BULL BIKES', NULL),
(26, 'CINDECO GUATEMALA', NULL),
(27, 'GT BRANDMASTER 2016', NULL),
(28, 'Agrocentro Colombia', NULL),
(29, 'Wohlhabenheit Holdings Corp.', NULL),
(30, 'AGROCENTRO GUATEMALA TEST', NULL),
(31, 'AGROCENTRO GUATEMALA', NULL),
(32, 'AGROCENTRO HONDURAS', NULL),
(33, 'CINDECO EL SALVADOR', NULL),
(34, 'CINDECO NICARAGUA', NULL),
(35, 'AGROCENTRO MEXICO', NULL),
(36, 'AGROCENTRO PANAMA', NULL),
(37, 'AGRICOLA FECUNDA', NULL),
(38, 'AGRICOLA CONTINENTAL', NULL),
(39, 'KORSA CHEMICALS', NULL),
(40, 'BLACK BULL BIKES', NULL),
(41, 'CINDECO GUATEMALA', NULL),
(42, 'GT BRANDMASTER 2016', NULL),
(43, 'Agrocentro Colombia', NULL),
(44, 'Wohlhabenheit Holdings Corp.', NULL),
(45, 'AGROCENTRO GUATEMALA TEST', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_chicas`
--

CREATE TABLE `cajas_chicas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_actual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_asignado` decimal(10,2) NOT NULL,
  `monto_disponible` decimal(10,2) NOT NULL,
  `id_usuario_encargado` int(11) NOT NULL,
  `id_supervisor` int(11) NOT NULL,
  `estado` enum('ACTIVA','INACTIVA') DEFAULT 'ACTIVA',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `cajas_chicas`
--

INSERT INTO `cajas_chicas` (`id`, `nombre`, `monto_inicial`, `monto_actual`, `monto_asignado`, `monto_disponible`, `id_usuario_encargado`, `id_supervisor`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Caja1', 0.00, 0.00, 0.00, 0.00, 2, 2, '', '2025-03-06 13:45:56', '2025-03-06 13:45:56'),
(2, 'Caja 2', 0.00, 0.00, 0.00, 0.00, 4, 1, 'ACTIVA', '2025-03-05 20:51:10', '2025-03-05 20:51:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_contables`
--

CREATE TABLE `cuentas_contables` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(10) DEFAULT '5',
  `base_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `cuentas_contables`
--

INSERT INTO `cuentas_contables` (`id`, `codigo`, `nombre`, `estado`, `creado_en`, `tipo`, `base_id`) VALUES
(1, '1002', 'Cuenta de Activos E', 'ACTIVO', '2025-03-04 15:25:51', '5', 1),
(2, '2001', 'Cuenta de Pasivos', 'ACTIVO', '2025-03-04 15:25:51', '5', 2),
(3, '3001', 'Cuenta de Capital Social Final 2', 'ACTIVO', '2025-03-04 15:27:45', '5', 3),
(14, '3002', 'Encargado 2', 'INACTIVO', '2025-03-11 21:10:26', '5', 4),
(49, '999', 'Angel', 'ACTIVO', '2025-03-13 16:19:40', '6', 1),
(50, '4001', 'gastos ', 'ACTIVO', '2025-03-13 17:35:00', '8', 1),
(51, '5000', 'COLABORADORES', 'INACTIVO', '2025-03-14 12:39:54', '50', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_liquidaciones`
--

CREATE TABLE `detalle_liquidaciones` (
  `id` int(11) NOT NULL,
  `id_liquidacion` int(11) NOT NULL,
  `no_factura` varchar(50) NOT NULL,
  `regimen` varchar(50) DEFAULT NULL,
  `c_costo` varchar(50) DEFAULT NULL,
  `nit_proveedor` varchar(20) DEFAULT NULL,
  `nombre_proveedor` varchar(100) DEFAULT NULL,
  `fecha` date NOT NULL,
  `bien_servicio` varchar(255) DEFAULT NULL,
  `t_gasto` varchar(50) DEFAULT NULL,
  `codigo_ccta` varchar(50) DEFAULT NULL,
  `descripcion_factura` text DEFAULT NULL,
  `p_unitario` decimal(10,2) DEFAULT NULL,
  `iva` decimal(10,2) DEFAULT NULL,
  `total_factura` decimal(10,2) NOT NULL,
  `idp` decimal(10,2) DEFAULT NULL,
  `inguat` decimal(10,2) DEFAULT NULL,
  `porcentajeiva` decimal(5,2) DEFAULT NULL,
  `porcentajeidp` decimal(5,2) DEFAULT NULL,
  `tipo_combustible` varchar(50) DEFAULT NULL,
  `estado` enum('PENDIENTE','EN_REVISIÓN','AUTORIZADO_POR_SUPERVISOR','RECHAZADO_POR_SUPERVISOR','AUTORIZADO_POR_CONTABILIDAD','RECHAZADO_POR_CONTABILIDAD','DESCARTADO') NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rutas_archivos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `detalle_liquidaciones`
--

INSERT INTO `detalle_liquidaciones` (`id`, `id_liquidacion`, `no_factura`, `regimen`, `c_costo`, `nit_proveedor`, `nombre_proveedor`, `fecha`, `bien_servicio`, `t_gasto`, `codigo_ccta`, `descripcion_factura`, `p_unitario`, `iva`, `total_factura`, `idp`, `inguat`, `porcentajeiva`, `porcentajeidp`, `tipo_combustible`, `estado`, `created_at`, `updated_at`, `rutas_archivos`) VALUES
(1, 1, 'FAC-001', NULL, NULL, NULL, 'Proveedor 1', '2025-03-01', 'Materiales Oficina', 'OPERATIVO', NULL, NULL, 100.00, NULL, 150.00, NULL, NULL, NULL, NULL, NULL, 'AUTORIZADO_POR_CONTABILIDAD', '2025-03-06 13:47:06', '2025-03-06 19:18:22', '[]'),
(2, 1, 'FAC-002', NULL, NULL, NULL, 'Proveedor 2', '2025-03-02', 'Reparación Equipo', 'MANTENIMIENTO', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'DESCARTADO', '2025-03-06 13:47:06', '2025-03-06 18:31:20', '[]'),
(6, 2, 'FACT-006', NULL, NULL, NULL, 'miguel', '2025-03-05', 'Servicio Prueba 5', 'OPERATIVO', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'RECHAZADO_POR_CONTABILIDAD', '2025-03-06 18:03:25', '2025-03-06 18:04:11', '[\"uploads\\/67c9e36ddb66f_Captura.PNG\"]'),
(7, 4, 'FACT-004', NULL, NULL, NULL, 'Proveedor Prueba 4', '2025-03-05', 'Servicio Prueba 4', 'OPERATIVO', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'RECHAZADO_POR_CONTABILIDAD', '2025-03-06 18:33:22', '2025-03-06 19:16:43', '[\"uploads\\/67c9ea7257511_Captura.PNG\"]'),
(8, 2, 'FACT-006', NULL, NULL, NULL, 'CARLOS ', '2025-03-06', 'Servicio Prueba 5', 'ADMINISTRATIVO', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, 'RECHAZADO_POR_CONTABILIDAD', '2025-03-06 19:08:12', '2025-03-06 19:17:57', '[\"uploads\\/67c9f29cac3c9_ajax.jpg\"]'),
(9, 1, 'Fact-002', NULL, NULL, NULL, 'Proveedor Prueba 6', '2025-03-06', 'Servicio Prueba 2', 'OPERATIVO', NULL, NULL, 333.00, NULL, 333.00, NULL, NULL, NULL, NULL, NULL, 'AUTORIZADO_POR_CONTABILIDAD', '2025-03-06 19:19:15', '2025-03-06 19:20:45', '[\"uploads\\/67c9f53329fda_PHPMYADMIN.png\"]'),
(10, 4, 'FACT-004', NULL, NULL, NULL, 'Proveedor Prueba 4', '2025-03-06', 'Servicio Prueba 6', 'OPERATIVO', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'AUTORIZADO_POR_CONTABILIDAD', '2025-03-06 20:14:13', '2025-03-06 20:16:11', '[]'),
(11, 4, 'dqdq', NULL, NULL, NULL, 'qsq', '2025-03-13', 'qsq', 'OPERATIVO', NULL, NULL, 111.00, NULL, 111.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-06 20:17:58', '2025-03-06 20:47:46', '[]'),
(12, 4, 'Fact-0031', NULL, NULL, NULL, 'MARCOS', '2025-03-06', 'Servicio Prueba 5', 'OPERATIVO', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-06 20:50:58', '2025-03-06 20:52:32', '[\"uploads\\/67ca0ab269b52_ajax.jpg\"]'),
(13, 5, 'FACT-004', NULL, NULL, NULL, 'Miguel', '2025-03-10', 'servicio de llantas ', 'OPERATIVO', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-10 15:13:57', '2025-03-10 15:13:57', '[\"uploads\\/67cf01b505a0b_Captura33.PNG\"]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `numero_factura` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `proveedor` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `base_id` int(11) NOT NULL,
  `cuenta_id` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `numero_factura`, `fecha`, `proveedor`, `monto`, `base_id`, `cuenta_id`, `estado`, `created_at`) VALUES
(1, 'FACT-002', '2025-03-12', 'Proveedor AA', 500.00, 4, 49, 'PAGADA', '2025-03-12 20:40:56'),
(2, 'FACT-003', '2025-03-13', 'Otto', 1000.00, 5, 51, 'PAGADA', '2025-03-14 12:41:43'),
(3, 'FACT-004', '2025-03-14', 'Proveedor AA', 2000.00, 2, 49, 'CANCELADA', '2025-03-14 13:20:37'),
(4, 'FACT-005', '2025-03-14', 'Proveedor AAA', 1500.00, 1, 51, 'PAGADA', '2025-03-14 14:14:36'),
(5, 'FACT-006', '2025-03-14', 'Proveedor AAA', 3000.00, 1, 1, 'RECHAZADO', '2025-03-14 14:47:41'),
(6, 'FACT-007', '2025-03-14', 'Proveedor AAA', 2500.00, 2, 49, 'RECHAZADO', '2025-03-14 14:48:18'),
(7, 'FACT-008', '2025-03-14', 'Proveedor AAA', 800.00, 4, 51, 'RECHAZADO', '2025-03-14 15:08:35'),
(8, 'FACT-009', '2025-03-14', 'Proveedor AAA', 9000.00, 2, 51, 'PAGADA', '2025-03-14 15:11:18'),
(9, 'FACT-0010', '2025-03-13', 'Proveedor AAA', 50000.00, 4, 49, 'PAGADA', '2025-03-14 15:18:52'),
(10, 'FACT-0011', '2025-03-13', 'Proveedor AAA', 900.00, 2, 1, 'PAGADA', '2025-03-14 15:25:11'),
(11, 'FACT-0022', '2025-03-14', 'Proveedor AAA', 1500.00, 1, 51, 'PAGADA', '2025-03-14 15:48:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos`
--

CREATE TABLE `impuestos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `impuestos`
--

INSERT INTO `impuestos` (`id`, `nombre`, `porcentaje`, `estado`, `creado_en`) VALUES
(1, 'Angel', 12.00, 'ACTIVO', '2025-03-10 15:29:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidaciones`
--

CREATE TABLE `liquidaciones` (
  `id` int(11) NOT NULL,
  `id_caja_chica` int(11) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `monto_total` decimal(10,2) DEFAULT 0.00,
  `estado` enum('PENDIENTE','AUTORIZADO_POR_SUPERVISOR','RECHAZADO_POR_SUPERVISOR','EN_REVISIÓN_CONTABILIDAD','AUTORIZADO_POR_CONTABILIDAD','RECHAZADO_POR_CONTABILIDAD','PENDIENTE_CORRECCIÓN') NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exportado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `liquidaciones`
--

INSERT INTO `liquidaciones` (`id`, `id_caja_chica`, `fecha_creacion`, `monto_total`, `estado`, `created_at`, `updated_at`, `exportado`) VALUES
(1, 1, '2025-03-01', 1500.00, 'AUTORIZADO_POR_CONTABILIDAD', '2025-03-06 13:46:45', '2025-03-14 15:51:48', 1),
(2, 2, '2025-03-02', 2000.00, 'AUTORIZADO_POR_CONTABILIDAD', '2025-03-06 13:46:45', '2025-03-10 16:42:13', 1),
(4, 1, '2025-03-05', 200.00, 'AUTORIZADO_POR_CONTABILIDAD', '2025-03-06 14:14:09', '2025-03-07 21:53:11', 1),
(5, 1, '2025-03-06', 1000.00, 'AUTORIZADO_POR_SUPERVISOR', '2025-03-07 21:25:04', '2025-03-10 15:41:25', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `estado`, `creado_en`) VALUES
(1, 'ADMIN', 'Administrador del sistema', 'ACTIVO', '2025-03-04 14:22:33'),
(2, 'ENCARGADO_CAJA_CHICA', 'Encargado de caja chica', 'ACTIVO', '2025-03-04 14:22:33'),
(3, 'SUPERVISOR_AUTORIZADOR', 'Supervisor autorizador', 'ACTIVO', '2025-03-04 14:22:33'),
(4, 'CONTABILIDAD', 'contabilidad', 'ACTIVO', '2025-03-04 14:22:33'),
(8, 'Angel De Leon', 'Admin', 'ACTIVO', '2025-03-11 14:22:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_gastos`
--

CREATE TABLE `tipos_gastos` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `tipos_gastos`
--

INSERT INTO `tipos_gastos` (`id`, `name`, `description`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'OPERATIVO', NULL, 'ACTIVO', '2025-03-05 20:28:37', '2025-03-05 20:28:37'),
(2, 'ADMINISTRATIVO', NULL, 'ACTIVO', '2025-03-05 20:28:37', '2025-03-05 20:28:37'),
(3, 'MANTENIMIENTO de vehiculo', 'Se realizo mantenimoento de vehiculo ', 'ACTIVO', '2025-03-05 20:28:37', '2025-03-10 17:21:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `id_rol`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@example.com', '$2y$10$O6SOYvh8GIgVYhFB4kP.QOz6WDK1rbh/c49Btx9ttlydoVCT8e7FO', 1, '2025-02-28 21:39:35', '2025-03-04 16:39:07'),
(2, 'Encargado 1', 'encargado1@example.com', '$2y$10$arxyBaOnstSEoqZilvck7eIVHpRq8bBNvx58d5jqomPl.sKozZ4SK', 2, '2025-02-28 21:39:35', '2025-03-04 14:37:53'),
(3, 'Supervisor 1', 'supervisor1@example.com', '$2y$10$arxyBaOnstSEoqZilvck7eIVHpRq8bBNvx58d5jqomPl.sKozZ4SK', 3, '2025-02-28 21:39:35', '2025-03-04 14:37:53'),
(4, 'Contador 1', 'contador1@example.com', '$2y$10$arxyBaOnstSEoqZilvck7eIVHpRq8bBNvx58d5jqomPl.sKozZ4SK', 4, '2025-02-28 21:39:35', '2025-03-10 18:41:53'),
(8, 'Angel De León', 'angel.deleon@agrocentro.com', '$2y$10$6VyL6jBE2mKWpkeKIb.p/e.LVDNXaQhrxrP0c0FIHd.6tbp0Ipjs2', 1, '2025-03-11 14:20:20', '2025-03-14 17:16:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `accesos_ibfk_2` (`id_cuenta_contable`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_liquidacion` (`id_liquidacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `auditoria_ibfk_2` (`id_detalle_liquidacion`);

--
-- Indices de la tabla `bases`
--
ALTER TABLE `bases`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario_encargado` (`id_usuario_encargado`),
  ADD KEY `id_supervisor` (`id_supervisor`);

--
-- Indices de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_cuenta_base` (`base_id`);

--
-- Indices de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detalle_liquidacion_id` (`id_liquidacion`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD KEY `fk_facturas_cuenta_id` (`cuenta_id`),
  ADD KEY `fk_facturas_base_id` (`base_id`);

--
-- Indices de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_caja_chica` (`id_caja_chica`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de la tabla `bases`
--
ALTER TABLE `bases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD CONSTRAINT `accesos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `accesos_ibfk_2` FOREIGN KEY (`id_cuenta_contable`) REFERENCES `cuentas_contables` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`id_liquidacion`) REFERENCES `liquidaciones` (`id`),
  ADD CONSTRAINT `auditoria_ibfk_2` FOREIGN KEY (`id_detalle_liquidacion`) REFERENCES `detalle_liquidaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auditoria_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  ADD CONSTRAINT `cajas_chicas_ibfk_1` FOREIGN KEY (`id_usuario_encargado`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `cajas_chicas_ibfk_2` FOREIGN KEY (`id_supervisor`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD CONSTRAINT `fk_cuenta_base` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`);

--
-- Filtros para la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  ADD CONSTRAINT `fk_detalle_liquidacion_id` FOREIGN KEY (`id_liquidacion`) REFERENCES `liquidaciones` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`),
  ADD CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_contables` (`id`),
  ADD CONSTRAINT `fk_facturas_base_id` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`),
  ADD CONSTRAINT `fk_facturas_cuenta_id` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_contables` (`id`);

--
-- Filtros para la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  ADD CONSTRAINT `liquidaciones_ibfk_1` FOREIGN KEY (`id_caja_chica`) REFERENCES `cajas_chicas` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
