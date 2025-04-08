-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-04-2025 a las 16:52:10
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
-- Estructura de tabla para la tabla `accesos_permisos`
--

CREATE TABLE `accesos_permisos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `permiso` varchar(50) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `origen` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `accesos_permisos`
--

INSERT INTO `accesos_permisos` (`id`, `id_usuario`, `id_modulo`, `permiso`, `estado`, `origen`, `created_at`) VALUES
(1776, 2, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:49:51'),
(1777, 2, NULL, 'create_detalles', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:49:51'),
(1778, 2, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:49:51'),
(1779, 2, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:49:51'),
(1784, 1, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1785, 1, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1786, 1, NULL, 'create_detalles', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1787, 1, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1788, 1, NULL, 'manage_accesos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1789, 1, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1790, 1, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1791, 1, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1792, 1, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1793, 1, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1794, 1, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1795, 1, NULL, 'manage_reportes', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1796, 1, NULL, 'manage_roles', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1797, 1, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1798, 1, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1799, 1, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1800, 1, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1801, 1, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1802, 8, NULL, 'autorizar_facturas', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1803, 8, NULL, 'autorizar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1804, 8, NULL, 'create_detalles', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1805, 8, NULL, 'create_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1806, 8, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1807, 8, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1808, 8, NULL, 'manage_cajachica', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1809, 8, NULL, 'manage_centros_costos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1810, 8, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1811, 8, NULL, 'manage_facturas', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1812, 8, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1813, 8, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1814, 8, NULL, 'manage_roles', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1815, 8, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1816, 8, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1817, 8, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1818, 8, NULL, 'revisar_facturas', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1819, 8, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1820, 3, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1821, 3, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1822, 3, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1823, 3, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1824, 3, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1825, 3, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1826, 3, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1827, 4, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1828, 4, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1829, 4, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1830, 4, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1831, 4, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1832, 4, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1833, 4, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1834, 4, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1835, 4, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1836, 4, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1889, 10, NULL, 'autorizar_facturas', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1890, 10, NULL, 'autorizar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1891, 10, NULL, 'create_detalles', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1892, 10, NULL, 'create_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1893, 10, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1894, 10, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1895, 10, NULL, 'manage_cajachica', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1896, 10, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1897, 10, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1898, 10, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1899, 10, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1900, 10, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1901, 10, NULL, 'manage_roles', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1902, 10, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1903, 10, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1904, 10, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1905, 10, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1906, 10, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(2372, 10, NULL, 'manage_centros_costos', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2373, 10, NULL, 'manage_facturas', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2374, 10, NULL, 'revisar_facturas', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2375, 10, NULL, 'revisar_liquidaciones', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2376, 10, NULL, 'manage_cuentas_contables', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2377, 10, NULL, 'manage_impuestos', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2378, 10, NULL, 'manage_tipos_gastos', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2473, 1, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2474, 1, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2475, 1, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2476, 1, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2477, 1, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2478, 1, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2479, 1, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2480, 1, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2481, 1, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2482, 1, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2483, 1, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2484, 1, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2485, 1, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2486, 1, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2487, 1, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2488, 1, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2489, 1, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2490, 1, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2582, 8, NULL, 'autorizar_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2583, 8, NULL, 'autorizar_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2584, 8, NULL, 'create_detalles', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2585, 8, NULL, 'create_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2586, 8, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2587, 8, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2588, 8, NULL, 'manage_cajachica', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2589, 8, NULL, 'manage_centros_costos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2590, 8, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2591, 8, NULL, 'manage_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2592, 8, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2593, 8, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2594, 8, NULL, 'manage_roles', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2595, 8, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2596, 8, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2597, 8, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2598, 8, NULL, 'revisar_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2599, 8, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2646, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-02 15:19:08'),
(2647, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-02 15:19:08'),
(2669, 3, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2670, 3, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2671, 3, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2672, 3, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2673, 3, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2674, 3, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2675, 3, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2676, 3, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2703, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 18:38:52'),
(2704, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 18:38:52'),
(2742, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 18:42:04'),
(2743, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 18:42:04'),
(2760, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 18:42:22'),
(2761, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 18:42:22'),
(2778, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 18:43:10'),
(2779, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 18:43:10'),
(2820, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 18:47:42'),
(2821, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 18:47:42'),
(2852, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 18:48:54'),
(2853, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 18:48:54'),
(2866, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 18:49:14'),
(2867, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 18:49:14'),
(2899, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 18:50:49'),
(2901, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 18:50:49'),
(2931, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:15:40'),
(2933, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:15:40'),
(3026, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:18:48'),
(3028, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:18:48'),
(3114, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:21:05'),
(3115, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:21:05'),
(3140, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:21:46'),
(3141, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:21:46'),
(3166, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:21:57'),
(3167, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:21:57'),
(3201, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:23:32'),
(3204, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:23:32'),
(3233, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:23:50'),
(3234, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:23:50'),
(3263, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:24:03'),
(3264, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:24:03'),
(3314, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:24:51'),
(3315, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:24:51'),
(3387, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3388, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3389, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3390, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3391, 14, NULL, 'listar_bases', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3392, 14, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3393, 14, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3394, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3395, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3396, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3397, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3398, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3399, 14, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3400, 14, NULL, 'manage_roles', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3401, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3402, 14, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3403, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3404, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3405, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3438, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:50:28'),
(3439, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:50:28'),
(3521, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:53:43'),
(3522, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:53:43'),
(3595, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:55:16'),
(3596, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:55:16'),
(3697, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:57:11'),
(3698, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:57:11'),
(3727, 8, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3728, 8, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3729, 8, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3730, 8, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3731, 8, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3732, 8, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3733, 8, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3734, 8, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3735, 8, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3736, 8, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3737, 8, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3738, 8, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3739, 8, NULL, 'manage_roles', 'ACTIVO', 'MANUAL', '2025-04-04 20:59:03'),
(3740, 8, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3741, 8, NULL, 'manage_usuarios', 'ACTIVO', 'MANUAL', '2025-04-04 20:59:03'),
(3742, 8, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3743, 8, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3744, 8, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3745, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3746, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3747, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3748, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3749, 14, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3750, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3751, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3752, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3753, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3754, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3755, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3756, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3757, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3758, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3759, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3760, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3761, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3762, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3781, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3782, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3783, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3784, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3785, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3786, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3787, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3788, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3789, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3790, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3791, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3792, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3793, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3794, 14, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3795, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3796, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3797, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3798, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3817, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3818, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3819, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3820, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3821, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3822, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3823, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3824, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3825, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3826, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3827, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3828, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3829, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3830, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3831, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3832, 14, NULL, 'manage_accesos', 'INACTIVO', 'MANUAL', '2025-04-04 21:02:18'),
(3833, 14, NULL, 'manage_auditoria', 'INACTIVO', 'MANUAL', '2025-04-04 21:02:18'),
(3834, 14, NULL, 'manage_reportes', 'INACTIVO', 'MANUAL', '2025-04-04 21:02:18'),
(3984, 4, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3985, 4, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3986, 4, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3987, 4, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3988, 4, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3989, 4, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3990, 4, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3991, 4, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3992, 4, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3993, 4, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3994, 4, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3995, 4, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3996, 4, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3997, 10, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3998, 10, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3999, 10, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4000, 10, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4001, 10, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4002, 10, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4003, 15, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4004, 15, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4005, 15, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4006, 15, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4007, 15, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4008, 15, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4009, 15, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4010, 15, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4011, 15, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4012, 15, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4013, 15, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4014, 15, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4015, 15, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `id_liquidacion` int(11) DEFAULT NULL,
  `id_detalle_liquidacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` enum('APROBADO','RECHAZADO','EXPORTADO_SAP','ELIMINADO') NOT NULL,
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
(1, 1, NULL, 1, 'APROBADO', 'Liquidación creada', '2025-03-21 20:07:21', 'CREADO', 'Administrador', 'Liquidación creada por admin'),
(2, 2, NULL, 2, 'APROBADO', NULL, '2025-03-21 20:15:47', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(3, 1, 2, 2, 'APROBADO', NULL, '2025-03-21 20:19:50', 'CREADO', 'Encargado 1', 'Detalle de liquidación creado por encargado'),
(4, NULL, 2, 1, 'APROBADO', NULL, '2025-03-21 20:21:01', 'CREAR_FACTURA', 'Administrador', '{\"cuenta_id\":1,\"base_id\":1,\"numero_factura\":\"FACT-003\",\"fecha\":\"2025-03-20\",\"proveedor\":\"Proveedor AAA\",\"monto\":1000,\"estado\":\"PENDIENTE\"}'),
(5, NULL, 1, 3, 'APROBADO', NULL, '2025-03-21 20:21:42', 'AUTORIZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"APROBADO\",\"comentario\":\"fue aprobada\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(6, NULL, 2, 3, 'APROBADO', NULL, '2025-03-21 20:21:59', 'RECHAZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"RECHAZADO\",\"comentario\":\"fue rechazada\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"RECHAZADO\"}'),
(7, 2, NULL, 2, 'APROBADO', NULL, '2025-03-21 21:29:29', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por usuario'),
(8, 1, 2, 2, 'APROBADO', NULL, '2025-03-21 21:31:45', 'ACTUALIZADO', 'Encargado 1', 'Detalle de liquidación actualizado'),
(9, 1, NULL, 2, 'APROBADO', NULL, '2025-03-21 21:31:45', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(10, NULL, 1, 3, 'APROBADO', NULL, '2025-03-21 21:54:37', 'PAGAR_FACTURA', 'Supervisor 1', '{\"accion\":\"PAGADA\",\"comentario\":\"\",\"estado_anterior\":\"APROBADO\",\"estado_nuevo\":\"PAGADA\"}'),
(11, 2, NULL, 2, 'APROBADO', NULL, '2025-03-23 18:16:16', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por usuario'),
(12, 1, 2, 2, 'APROBADO', NULL, '2025-03-23 18:22:15', 'ACTUALIZADO', 'Encargado 1', 'Detalle de liquidación actualizado'),
(13, 1, NULL, 2, 'APROBADO', NULL, '2025-03-23 18:22:15', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(14, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-23 18:28:52', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: usuario@gmail.com'),
(15, 2, NULL, 3, 'APROBADO', NULL, '2025-03-23 18:30:53', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'Autorizado por supervisor'),
(16, NULL, 3, 2, 'APROBADO', NULL, '2025-03-23 20:03:35', 'CREAR_FACTURA', 'Encargado 1', '{\"cuenta_id\":1,\"base_id\":1,\"numero_factura\":\"FACT-002\",\"fecha\":\"2025-03-23\",\"proveedor\":\"Pepe\",\"monto\":1000,\"estado\":\"PENDIENTE\"}'),
(17, NULL, 3, 3, 'APROBADO', NULL, '2025-03-23 20:04:31', 'AUTORIZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"APROBADO\",\"comentario\":\"Fue aprobada satisfactoriamente\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(18, NULL, 3, 4, 'APROBADO', NULL, '2025-03-23 20:05:39', 'PAGAR_FACTURA', 'Contador 1', '{\"accion\":\"PAGADA\",\"comentario\":\"fue pagada y aprobada en contabilidad\",\"estado_anterior\":\"APROBADO\",\"estado_nuevo\":\"PAGADA\"}'),
(19, 3, NULL, 2, 'APROBADO', NULL, '2025-03-23 20:08:25', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(20, 3, 3, 2, 'APROBADO', NULL, '2025-03-23 20:09:38', 'CREADO', 'Encargado 1', 'Detalle de liquidación creado por encargado'),
(21, 3, NULL, 3, 'APROBADO', NULL, '2025-03-23 20:10:39', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'Autorizado por supervisor '),
(22, NULL, 4, 1, 'APROBADO', NULL, '2025-03-23 20:12:17', 'CREAR_FACTURA', 'Administrador', '{\"cuenta_id\":1,\"base_id\":2,\"numero_factura\":\"FACT-004\",\"fecha\":\"2025-03-23\",\"proveedor\":\"Proveedor AA\",\"monto\":1000,\"estado\":\"PENDIENTE\"}'),
(23, NULL, 4, 3, 'APROBADO', NULL, '2025-03-23 20:13:05', 'AUTORIZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"APROBADO\",\"comentario\":\"Esta aprobada\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(24, 1, NULL, 4, 'APROBADO', NULL, '2025-03-23 20:16:38', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'autorizado '),
(25, 3, 3, 4, 'APROBADO', NULL, '2025-03-23 20:17:10', 'RECHAZADO_POR_CONTABILIDAD', 'Contador 1', 'no esta completo'),
(26, NULL, 5, 1, 'APROBADO', NULL, '2025-03-25 20:44:54', 'CREAR_FACTURA', 'Administrador', '{\"cuenta_id\":3,\"base_id\":2,\"numero_factura\":\"FACT-005\",\"fecha\":\"2025-03-24\",\"proveedor\":\"CARLOS\",\"monto\":300,\"estado\":\"PENDIENTE\"}'),
(27, 4, NULL, 8, 'APROBADO', NULL, '2025-03-25 21:24:01', 'CREADO', 'Angel De León', 'Liquidación creada por encargado'),
(28, NULL, 6, 8, 'APROBADO', NULL, '2025-03-25 21:25:48', 'CREAR_FACTURA', 'Angel De León', '{\"cuenta_id\":2,\"base_id\":1,\"numero_factura\":\"1311\",\"fecha\":\"2025-03-25\",\"proveedor\":\"Carlor\",\"monto\":500,\"estado\":\"PENDIENTE\"}'),
(29, 4, NULL, 2, 'APROBADO', NULL, '2025-03-26 18:30:30', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por usuario'),
(30, 4, NULL, 2, 'APROBADO', NULL, '2025-03-26 18:30:56', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por usuario'),
(31, 2, NULL, 2, 'APROBADO', NULL, '2025-03-26 18:53:47', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por usuario'),
(32, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-26 21:13:02', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(33, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-26 21:46:23', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(34, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-26 21:46:44', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(36, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-26 21:53:50', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(38, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 11:30:10', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(40, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 11:30:31', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(42, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 11:31:02', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(44, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 11:31:28', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(46, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 11:32:34', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(48, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 11:32:51', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(50, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 11:33:40', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(52, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 12:21:52', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(54, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 12:22:04', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(56, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 12:38:37', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(57, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:14:46', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(58, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:15:26', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(59, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:23:51', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(60, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:24:19', 'CREAR_ROL', 'Administrador', 'Rol creado: prueba'),
(62, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:24:30', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(63, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:31:21', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: prueba'),
(64, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:32:43', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: prueba'),
(65, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:34:07', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: prueba'),
(66, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:35:34', 'CREAR_ROL', 'Administrador', 'Rol creado: encargado,contador'),
(68, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:35:44', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(70, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 13:42:41', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(72, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:11:39', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(73, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:11:52', 'ELIMINAR_ROL', 'Administrador', 'Rol eliminado: encargado,contador'),
(74, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:12:39', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: prueba'),
(76, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:13:21', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(77, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:13:41', 'ELIMINAR_ROL', 'Administrador', 'Rol eliminado: prueba'),
(79, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:13:53', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(81, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:14:22', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(83, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:14:54', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(85, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:15:31', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(87, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:16:22', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(90, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:46:39', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(91, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 14:54:30', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos asignados a usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(92, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:54:30', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: omar@gmail.com'),
(93, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 14:56:36', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos asignados a usuario omar@gmail.com desde rol ID 3: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(94, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 14:56:36', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: omar@gmail.com'),
(96, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 15:42:21', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(98, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 15:53:11', 'CREAR_ROL', 'Administrador', 'Rol creado: Admin junior'),
(100, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 15:53:35', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(115, NULL, NULL, 2, 'APROBADO', NULL, '2025-03-27 16:40:17', 'ASIGNAR_PERMISOS', 'Encargado 1', 'Permisos actualizados para usuario encargado1@example.com desde rol ID 2: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(116, NULL, NULL, 2, 'APROBADO', NULL, '2025-03-27 16:40:40', 'ASIGNAR_MODULOS', 'Encargado 1', 'Módulos asignados a usuario encargado1@example.com: create_liquidaciones, create_detalles, manage_cajachica, manage_facturas'),
(118, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 16:54:47', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(119, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 16:54:59', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(120, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 16:55:30', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(129, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:17:15', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(131, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:17:44', 'ASIGNAR_MODULOS', 'Contador 1', 'Módulos asignados a usuario contador1@example.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_facturas, revisar_facturas, manage_centros_costos'),
(132, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:18:32', 'ASIGNAR_MODULOS', 'Contador 1', 'Módulos asignados a usuario contador1@example.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(134, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 17:32:16', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(136, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 17:35:02', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(140, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:39:32', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, manage_auditoria'),
(143, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:42:43', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_tipos_gastos, manage_auditoria'),
(145, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:43:01', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_tipos_gastos, manage_auditoria'),
(147, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:43:49', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos'),
(149, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:44:17', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: manage_facturas, manage_centros_costos, manage_auditoria'),
(151, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:44:39', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: manage_facturas, manage_centros_costos, manage_auditoria'),
(153, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:44:59', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: manage_facturas, manage_centros_costos, manage_auditoria'),
(155, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:45:34', 'ASIGNAR_MODULOS', 'Contador 1', 'Módulos asignados a usuario contador1@example.com: manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(157, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 17:54:46', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(159, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 17:57:27', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos asignados a usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(160, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 17:57:27', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: omar@gmail.com'),
(161, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 17:58:26', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(162, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:59:01', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: manage_facturas, manage_centros_costos, manage_auditoria, revisar_facturas'),
(164, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 17:59:51', 'ASIGNAR_MODULOS', 'Contador 1', 'Módulos asignados a usuario contador1@example.com: manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(165, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:17:37', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos asignados a usuario omar@gmail.com desde descripción del rol ID 9: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(166, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 18:17:37', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(167, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:18:00', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos asignados a usuario omar@gmail.com desde descripción del rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(168, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 18:18:00', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(169, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 18:20:11', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: manage_facturas, manage_centros_costos, manage_auditoria, revisar_facturas'),
(171, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 18:20:47', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_facturas, manage_centros_costos, manage_auditoria'),
(173, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 18:21:18', 'ASIGNAR_MODULOS', 'Contador 1', 'Módulos asignados a usuario contador1@example.com: revisar_facturas, manage_centros_costos'),
(174, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 18:22:48', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: manage_auditoria'),
(176, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:23:42', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, manage_reportes, manage_centros_costos, manage_tipos_gastos'),
(177, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:24:30', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_reportes'),
(178, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:25:43', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_reportes'),
(179, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:26:34', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_reportes'),
(180, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:26:57', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(181, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:27:25', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_reportes, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(182, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:27:47', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_reportes, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(183, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:28:07', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_reportes'),
(184, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:28:24', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_reportes'),
(185, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:28:55', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_reportes'),
(186, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:29:21', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_reportes'),
(187, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 18:29:36', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas'),
(188, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 20:36:27', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_detalles_liquidaciones, manage_reportes, manage_auditoria'),
(190, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:37:43', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_reportes, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_auditoria, manage_facturas, revisar_facturas'),
(191, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:38:00', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_tipos_gastos'),
(192, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:39:52', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_facturas, manage_auditoria, manage_facturas, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones'),
(193, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:40:02', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_facturas, manage_auditoria, manage_facturas, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones'),
(194, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:46:16', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_facturas, manage_auditoria, manage_facturas, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones'),
(195, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:46:36', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_tipos_gastos'),
(196, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:47:57', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_facturas, revisar_facturas, manage_centros_costos'),
(197, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:48:23', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_facturas, revisar_facturas, manage_centros_costos'),
(198, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:48:43', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(199, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:50:23', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_facturas, manage_facturas, manage_centros_costos'),
(200, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:50:36', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_facturas, manage_facturas, manage_centros_costos'),
(201, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:52:43', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_facturas, manage_facturas, manage_centros_costos'),
(202, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:54:42', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_facturas, revisar_facturas, manage_centros_costos'),
(203, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 20:56:09', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas'),
(205, NULL, NULL, 4, 'APROBADO', NULL, '2025-03-27 20:56:20', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_detalles_liquidaciones, manage_reportes, manage_auditoria'),
(207, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:57:58', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(208, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:58:42', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(209, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 20:59:18', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_facturas, manage_centros_costos'),
(210, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:00:08', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: revisar_facturas, manage_centros_costos'),
(211, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:09:32', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos asignados a usuario omar@gmail.com desde descripción del rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(212, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-27 21:09:32', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(213, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:10:30', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_cajachica'),
(214, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:10:52', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_cajachica'),
(215, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:11:05', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_cajachica'),
(216, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:11:25', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_cajachica'),
(217, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:11:36', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(218, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:11:56', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_cajachica, manage_facturas'),
(219, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:13:05', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_auditoria, manage_facturas'),
(220, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:13:22', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_facturas'),
(221, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:13:42', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(222, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:21:08', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_facturas, manage_cajachica'),
(223, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:21:19', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_facturas, manage_cajachica'),
(224, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:21:45', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_facturas'),
(225, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:22:29', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_facturas, manage_cajachica'),
(226, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:22:51', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_facturas, manage_cajachica'),
(227, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:23:06', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_cajachica, manage_facturas'),
(228, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-27 21:38:56', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: manage_facturas, manage_cajachica'),
(229, NULL, NULL, 2, 'APROBADO', NULL, '2025-03-27 21:39:31', 'ASIGNAR_PERMISOS', 'Encargado 1', 'Permisos actualizados para usuario encargado1@example.com desde rol ID 2: manage_cajachica'),
(244, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 13:07:58', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(247, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 13:10:42', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(249, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 13:44:22', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(251, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 13:44:48', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(252, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:19:21', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(253, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:19:21', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(254, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:19:54', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(255, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:19:54', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(256, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:20:41', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(257, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:20:41', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(258, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:21:08', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(259, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:21:08', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(260, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:21:39', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, create_liquidaciones'),
(261, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:22:13', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(262, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:22:13', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(263, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:22:31', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(264, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:22:31', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(265, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:22:57', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, create_liquidaciones'),
(266, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:23:30', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(267, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:23:30', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(268, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:23:58', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, manage_accesos'),
(269, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:24:12', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(270, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:25:03', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(271, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:25:03', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(272, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:25:26', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(273, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:25:26', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(274, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-28 14:26:04', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica, autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(275, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 14:26:04', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(277, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 15:09:03', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(278, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 15:09:18', 'ELIMINAR_ROL', 'Administrador', 'Rol eliminado: Admin junior'),
(279, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 15:10:04', 'CREAR_ROL', 'Administrador', 'Rol creado: Admin junior'),
(281, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 15:10:26', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(284, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 15:14:51', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(287, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 15:15:49', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(289, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-28 15:16:31', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin junior'),
(291, 5, 4, 1, 'APROBADO', NULL, '2025-03-28 17:22:53', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(292, 5, 5, 1, 'APROBADO', NULL, '2025-03-28 17:23:41', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-002'),
(293, 5, 6, 1, 'APROBADO', NULL, '2025-03-28 20:40:43', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(294, 5, 7, 1, 'APROBADO', NULL, '2025-03-28 20:43:08', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(295, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 20:48:53', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(296, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 20:48:53', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(297, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 20:49:55', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(298, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 20:50:43', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(299, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 20:50:43', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(300, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 20:51:33', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_auditoria, manage_reportes'),
(301, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 20:52:12', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(302, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 20:52:12', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(303, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 20:52:39', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica, manage_auditoria'),
(304, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 20:52:50', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(305, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 21:09:07', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(306, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 21:09:07', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(307, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 21:09:36', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(308, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 21:10:17', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(309, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 21:10:17', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: ROL_TEST'),
(310, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 21:10:35', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica, revisar_facturas'),
(311, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 21:11:31', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_facturas'),
(312, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 21:12:01', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(313, NULL, NULL, 10, 'APROBADO', NULL, '2025-03-31 21:12:23', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_facturas, revisar_facturas'),
(314, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 21:17:38', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: andersson.perez@agrocentro.com'),
(316, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 21:19:04', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(317, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 21:19:37', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(320, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-31 21:21:31', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(321, 6, NULL, 1, 'APROBADO', NULL, '2025-03-31 21:23:40', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(322, 6, 8, 1, 'APROBADO', NULL, '2025-03-31 21:24:56', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(323, 6, 9, 1, 'APROBADO', NULL, '2025-03-31 21:25:19', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(327, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 14:22:03', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(328, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 14:22:55', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(332, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 14:35:26', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(335, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 18:23:30', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(337, NULL, NULL, 2, 'APROBADO', NULL, '2025-04-01 18:49:51', 'ASIGNAR_PERMISOS', 'Encargado 1', 'Permisos actualizados para usuario encargado1@example.com desde rol ID 2: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(339, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 18:50:21', 'ASIGNAR_PERMISOS', 'Administrador', 'Permisos actualizados para usuario admin@example.com desde rol ID 1: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(340, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-01 18:50:21', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 1: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(341, NULL, NULL, 3, 'APROBADO', NULL, '2025-04-01 18:50:35', 'ASIGNAR_PERMISOS', 'Supervisor 1', 'Permisos actualizados para usuario supervisor1@example.com desde rol ID 3: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(342, NULL, NULL, 4, 'APROBADO', NULL, '2025-04-01 18:50:56', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(345, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 18:55:41', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Supervisos 2'),
(347, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 18:55:56', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Supervisor 2'),
(349, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 18:56:16', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(350, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-01 19:06:22', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 9: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(351, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 19:06:22', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Supervisor 1'),
(353, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 19:06:46', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(355, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 19:07:48', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(356, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-01 19:08:11', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos predeterminados asignados a usuario omar@gmail.com desde rol ID 1: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(357, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 19:08:11', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: omar@gmail.com'),
(364, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 20:48:39', 'CREAR_ROL', 'Administrador', 'Rol creado: encargado'),
(365, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 20:48:52', 'CREAR_ROL', 'Administrador', 'Rol creado: encargado 2'),
(367, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 20:49:06', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(369, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 20:49:12', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(371, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 20:58:38', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(373, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 21:00:36', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com');
INSERT INTO `auditoria` (`id`, `id_liquidacion`, `id_detalle_liquidacion`, `id_usuario`, `accion`, `comentario`, `fecha`, `tipo_accion`, `usuario_nombre`, `detalles`) VALUES
(375, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 21:20:19', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(377, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 21:22:13', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(379, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 21:28:22', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: elian@gmail.com'),
(381, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 21:58:47', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: andersson.perez@agrocentro.com'),
(383, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 21:58:55', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: elian@gmail.com'),
(384, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-01 21:59:03', 'ELIMINAR_ROL', 'Administrador', 'Rol eliminado: encargado 2'),
(386, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 12:52:30', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: usuario@gmail.com'),
(387, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 12:53:00', 'ELIMINAR_ROL', 'Administrador', 'Rol eliminado: encargado'),
(388, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 12:53:03', 'ELIMINAR_ROL', 'Administrador', 'Rol eliminado: Supervisor 2'),
(389, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 12:53:06', 'ELIMINAR_ROL', 'Administrador', 'Rol eliminado: Supervisor 1'),
(390, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 12:53:11', 'ELIMINAR_ROL', 'Administrador', 'Rol eliminado: Angel De Leon'),
(391, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:07:16', 'ELIMINAR_USUARIO', 'Administrador', 'Usuario eliminado: elian@gmail.com'),
(392, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:07:40', 'ELIMINAR_USUARIO', 'Administrador', 'Usuario eliminado: usuario@gmail.com'),
(393, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:07:43', 'ELIMINAR_USUARIO', 'Administrador', 'Usuario eliminado: andersson.perez@agrocentro.com'),
(395, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-02 13:08:17', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos asignados a usuario omar@gmail.com desde rol ID 3: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(396, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:08:17', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: omar@gmail.com'),
(398, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:08:31', 'ELIMINAR_USUARIO', 'Administrador', 'Usuario eliminado: pepe@gmail.com'),
(399, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:16:37', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: angel.deleon@agrocentro.com'),
(400, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:16:44', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: omar@gmail.com'),
(401, NULL, NULL, 4, 'APROBADO', NULL, '2025-04-02 13:24:17', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(402, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:24:17', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(403, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-02 13:24:17', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(404, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:25:05', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos asignados a usuario angel.deleon@agrocentro.com desde rol ID 3: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(405, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:25:05', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: angel.deleon@agrocentro.com'),
(406, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 13:25:10', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: angel.deleon@agrocentro.com'),
(407, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:27:36', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: revisar_facturas, manage_centros_costos'),
(408, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:28:53', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: create_liquidaciones, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(409, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:29:24', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(410, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:30:22', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: create_liquidaciones, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(411, NULL, NULL, 4, 'APROBADO', NULL, '2025-04-02 13:31:09', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(412, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:31:09', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(413, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-02 13:31:09', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(414, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:32:43', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: create_liquidaciones, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(415, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:33:06', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(416, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:33:51', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: create_liquidaciones, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(417, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:34:06', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(418, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:34:20', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: create_liquidaciones, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(419, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:34:35', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(420, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:44:41', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: create_liquidaciones, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(421, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:45:15', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(422, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:45:47', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: revisar_facturas, manage_centros_costos'),
(423, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:46:04', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_facturas, revisar_facturas, manage_centros_costos'),
(424, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:46:34', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_facturas, revisar_facturas, manage_centros_costos'),
(425, NULL, NULL, 4, 'APROBADO', NULL, '2025-04-02 13:47:03', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_facturas, manage_facturas, manage_centros_costos'),
(426, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 13:47:03', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 4: revisar_facturas, manage_facturas, manage_centros_costos'),
(427, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-02 13:47:03', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 4: revisar_facturas, manage_facturas, manage_centros_costos'),
(428, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:06:43', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_centros_costos'),
(429, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:07:05', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_facturas, revisar_facturas, manage_centros_costos'),
(430, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:07:21', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_centros_costos'),
(431, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-02 14:07:46', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(432, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:20:23', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_facturas, revisar_facturas, manage_centros_costos'),
(433, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:24:42', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_centros_costos'),
(434, NULL, NULL, 4, 'APROBADO', NULL, '2025-04-02 14:27:09', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(435, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:27:09', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(436, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-02 14:27:09', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(437, NULL, NULL, 4, 'APROBADO', NULL, '2025-04-02 14:28:16', 'ASIGNAR_MODULOS', 'Contador 1', 'Módulos asignados a usuario contador1@example.com: revisar_facturas, manage_centros_costos'),
(438, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:28:57', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(439, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:29:16', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(440, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:29:50', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(441, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:30:07', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(442, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:30:23', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(443, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:30:33', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_facturas, revisar_facturas'),
(444, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:30:47', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: revisar_facturas, manage_centros_costos'),
(445, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:30:58', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, revisar_facturas, manage_centros_costos'),
(446, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 14:31:29', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(447, NULL, NULL, 10, 'APROBADO', NULL, '2025-04-02 14:32:38', 'ASIGNAR_MODULOS', 'Omar ', 'Módulos asignados a usuario omar@gmail.com: revisar_detalles_liquidaciones, manage_reportes, manage_auditoria'),
(448, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 14:33:27', 'CREAR_ROL', 'Administrador', 'Rol creado: Contador admin'),
(449, NULL, NULL, 14, 'APROBADO', NULL, '2025-04-02 14:34:09', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos asignados a usuario miguel@gmail.com desde rol ID 17: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(450, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 14:34:09', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: miguel@gmail.com'),
(451, NULL, NULL, 15, 'APROBADO', NULL, '2025-04-02 14:35:36', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos asignados a usuario pepe@gmail.com desde rol ID 17: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(452, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 14:35:36', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: pepe@gmail.com'),
(453, NULL, NULL, 14, 'APROBADO', NULL, '2025-04-02 14:35:59', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 17: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, manage_roles, manage_usuarios'),
(454, NULL, NULL, 15, 'APROBADO', NULL, '2025-04-02 14:35:59', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 17: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, manage_roles, manage_usuarios'),
(455, NULL, NULL, 15, 'APROBADO', NULL, '2025-04-02 14:38:50', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: manage_reportes, manage_auditoria'),
(456, NULL, NULL, 14, 'APROBADO', NULL, '2025-04-02 14:40:16', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones'),
(457, NULL, NULL, 14, 'APROBADO', NULL, '2025-04-02 14:41:07', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas'),
(458, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:09:32', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos asignados a usuario angel.deleon@agrocentro.com desde rol ID 1: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(459, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 15:09:32', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: angel.deleon@agrocentro.com'),
(460, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 15:10:27', 'ASIGNAR_PERMISOS', 'Administrador', 'Permisos actualizados para usuario admin@example.com desde rol ID 1: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(461, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:10:27', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 1: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(462, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:11:17', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(463, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:11:54', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_cajachica, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(464, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:12:11', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(465, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:12:29', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(466, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:12:53', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_centros_costos'),
(467, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:13:06', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: autorizar_facturas, revisar_facturas, manage_centros_costos'),
(468, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 15:14:08', 'CREAR_ROL', 'Administrador', 'Rol creado: Admin Junior'),
(469, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:14:33', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos asignados a usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(470, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 15:14:33', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: angel.deleon@agrocentro.com'),
(471, NULL, NULL, 14, 'APROBADO', NULL, '2025-04-02 15:16:30', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos asignados a usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(472, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-02 15:16:30', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: miguel@gmail.com'),
(473, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:17:10', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(474, NULL, NULL, 14, 'APROBADO', NULL, '2025-04-02 15:17:11', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(475, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:17:34', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: manage_centros_costos, revisar_facturas'),
(476, NULL, NULL, 14, 'APROBADO', NULL, '2025-04-02 15:17:34', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: manage_centros_costos, revisar_facturas'),
(477, NULL, NULL, 8, 'APROBADO', NULL, '2025-04-02 15:19:08', 'ASIGNAR_MODULOS', 'Angel De León', 'Módulos asignados a usuario angel.deleon@agrocentro.com: manage_roles, manage_usuarios, revisar_facturas, manage_centros_costos'),
(478, NULL, NULL, 14, 'APROBADO', NULL, '2025-04-02 15:56:48', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: manage_reportes, revisar_facturas, manage_centros_costos'),
(479, 6, 11, 1, 'APROBADO', NULL, '2025-04-02 16:04:12', 'ELIMINADO', 'Administrador', 'Detalle de liquidación eliminado'),
(482, 6, 10, 1, 'RECHAZADO', NULL, '2025-04-02 16:37:09', 'ELIMINADO', 'Administrador', 'Detalle de liquidación eliminado'),
(486, 9, NULL, 1, 'APROBADO', NULL, '2025-04-02 17:38:04', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(487, 9, 12, 1, 'RECHAZADO', NULL, '2025-04-02 17:38:32', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-002'),
(488, 9, 13, 1, 'RECHAZADO', NULL, '2025-04-02 17:39:03', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(489, 9, 14, 1, 'RECHAZADO', NULL, '2025-04-02 18:13:54', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(490, 9, 14, 1, 'RECHAZADO', NULL, '2025-04-02 18:14:13', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-005'),
(491, 9, 15, 1, 'RECHAZADO', NULL, '2025-04-02 18:16:26', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(492, 9, 16, 1, 'RECHAZADO', NULL, '2025-04-02 18:16:57', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(493, 9, 17, 1, 'RECHAZADO', NULL, '2025-04-02 20:43:54', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(494, 9, 18, 1, 'RECHAZADO', NULL, '2025-04-03 14:16:38', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(495, 9, NULL, 1, 'APROBADO', NULL, '2025-04-03 14:25:33', 'ACTUALIZADO', 'Administrador', 'Liquidación actualizada por usuario'),
(496, 9, 19, 1, 'RECHAZADO', NULL, '2025-04-03 15:35:10', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(497, 9, 20, 1, 'RECHAZADO', NULL, '2025-04-03 17:39:03', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(498, 9, 21, 1, 'RECHAZADO', NULL, '2025-04-03 18:08:05', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-007'),
(499, 9, 21, 1, 'RECHAZADO', NULL, '2025-04-03 18:08:19', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-007'),
(502, 11, NULL, 1, 'APROBADO', NULL, '2025-04-03 18:42:21', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(503, 11, 22, 1, 'RECHAZADO', NULL, '2025-04-03 18:44:48', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(504, 11, 23, 1, 'RECHAZADO', NULL, '2025-04-03 20:45:44', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(505, 11, 24, 1, 'RECHAZADO', NULL, '2025-04-03 20:59:32', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(506, 2, 25, 1, 'RECHAZADO', NULL, '2025-04-04 13:56:34', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(507, 2, 26, 1, 'RECHAZADO', NULL, '2025-04-04 13:57:05', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(508, 12, NULL, 1, 'APROBADO', NULL, '2025-04-04 14:37:27', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(509, 12, 27, 1, 'RECHAZADO', NULL, '2025-04-04 15:23:56', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(510, 12, 28, 1, 'RECHAZADO', NULL, '2025-04-04 15:24:21', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(511, 12, 29, 1, 'RECHAZADO', NULL, '2025-04-04 15:46:23', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(512, 2, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 15:49:27', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(513, 12, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 15:49:52', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(514, 12, NULL, 1, 'APROBADO', NULL, '2025-04-04 15:50:38', 'ACTUALIZADO', 'Administrador', 'Liquidación actualizada por usuario'),
(515, 12, 30, 1, 'RECHAZADO', NULL, '2025-04-04 15:51:18', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-00433'),
(516, 12, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 15:51:26', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(519, 11, NULL, 2, 'RECHAZADO', NULL, '2025-04-04 15:53:53', 'FINALIZADO', 'Encargado 1', 'Liquidación finalizada por encargado'),
(520, 11, NULL, 3, 'RECHAZADO', NULL, '2025-04-04 16:07:35', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'Esta autorizado'),
(521, 12, NULL, 1, 'APROBADO', NULL, '2025-04-04 16:09:09', 'ACTUALIZADO', 'Administrador', 'Liquidación actualizada por usuario'),
(522, 12, 30, 1, 'RECHAZADO', NULL, '2025-04-04 16:09:27', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: FACT-00433'),
(523, 12, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 16:09:36', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(524, 12, NULL, 3, 'RECHAZADO', NULL, '2025-04-04 16:10:00', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'esta completo'),
(532, 15, NULL, 2, 'APROBADO', NULL, '2025-04-04 16:46:17', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(533, 15, 33, 2, 'RECHAZADO', NULL, '2025-04-04 16:46:51', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: FACT-004'),
(534, 15, NULL, 2, 'RECHAZADO', NULL, '2025-04-04 16:47:03', 'FINALIZADO', 'Encargado 1', 'Liquidación finalizada por encargado'),
(535, 15, NULL, 3, 'RECHAZADO', NULL, '2025-04-04 16:47:28', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'Esta correcto'),
(540, 16, NULL, 2, 'APROBADO', NULL, '2025-04-04 17:00:32', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(541, 16, 34, 2, 'RECHAZADO', NULL, '2025-04-04 17:00:54', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: FACT-004'),
(542, 16, 35, 2, 'RECHAZADO', NULL, '2025-04-04 17:01:17', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: FACT-006'),
(543, 16, NULL, 2, 'RECHAZADO', NULL, '2025-04-04 17:01:26', 'FINALIZADO', 'Encargado 1', 'Liquidación finalizada por encargado'),
(544, 16, NULL, 3, 'RECHAZADO', NULL, '2025-04-04 17:01:44', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'Esta completo'),
(545, 6, NULL, 2, 'RECHAZADO', NULL, '2025-04-04 17:04:52', 'FINALIZADO', 'Encargado 1', 'Liquidación finalizada por encargado'),
(546, 6, NULL, 3, 'RECHAZADO', NULL, '2025-04-04 17:05:22', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'esta completo'),
(547, NULL, NULL, 4, 'RECHAZADO', NULL, '2025-04-04 17:06:44', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(548, NULL, NULL, 10, 'RECHAZADO', NULL, '2025-04-04 17:06:44', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos'),
(549, NULL, NULL, 4, 'RECHAZADO', NULL, '2025-04-04 17:07:35', 'ASIGNAR_MODULOS', 'Contador 1', 'Módulos asignados a usuario contador1@example.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, revisar_facturas, manage_centros_costos'),
(550, NULL, NULL, 3, 'RECHAZADO', NULL, '2025-04-04 17:28:54', 'ASIGNAR_MODULOS', 'Supervisor 1', 'Módulos asignados a usuario supervisor1@example.com: create_liquidaciones, manage_cuentas_contables, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_facturas, autorizar_facturas, revisar_facturas'),
(551, 17, NULL, 3, 'APROBADO', NULL, '2025-04-04 17:29:18', 'CREADO', 'Supervisor 1', 'Liquidación creada por encargado'),
(552, 17, 36, 3, 'RECHAZADO', NULL, '2025-04-04 17:29:41', 'CREAR_DETALLE', 'Supervisor 1', 'Factura creada: FACT-006'),
(553, 17, NULL, 3, 'RECHAZADO', NULL, '2025-04-04 17:29:51', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(554, 17, NULL, 3, 'APROBADO', NULL, '2025-04-04 17:30:26', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'autorizado'),
(555, 9, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 18:08:17', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(556, NULL, NULL, 4, 'RECHAZADO', NULL, '2025-04-04 18:25:24', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, autorizar_liquidaciones, manage_accesos'),
(557, NULL, NULL, 10, 'RECHAZADO', NULL, '2025-04-04 18:25:24', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, autorizar_liquidaciones, manage_accesos'),
(558, 9, NULL, 4, 'APROBADO', NULL, '2025-04-04 18:26:11', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'Autorizado'),
(559, 18, NULL, 1, 'APROBADO', NULL, '2025-04-04 18:36:08', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(560, 18, NULL, 1, 'APROBADO', NULL, '2025-04-04 18:36:25', 'ACTUALIZADO', 'Administrador', 'Liquidación actualizada por usuario'),
(561, 18, NULL, 1, 'APROBADO', NULL, '2025-04-04 18:36:35', 'ACTUALIZADO', 'Administrador', 'Liquidación actualizada por usuario'),
(562, 18, 37, 1, 'RECHAZADO', NULL, '2025-04-04 18:36:55', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(563, 18, 38, 1, 'RECHAZADO', NULL, '2025-04-04 18:37:27', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(564, 18, NULL, 1, 'APROBADO', NULL, '2025-04-04 18:37:40', 'ACTUALIZADO', 'Administrador', 'Liquidación actualizada por usuario'),
(565, 18, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 18:37:49', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(566, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 18:38:52', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: create_detalles, create_liquidaciones, listar_bases, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, revisar_facturas, manage_roles, manage_usuarios'),
(567, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:38:52', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: create_detalles, create_liquidaciones, listar_bases, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, revisar_facturas, manage_reportes'),
(568, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:39:18', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos asignados a usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(569, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-04 18:39:18', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: miguel@gmail.com'),
(570, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 18:42:04', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, revisar_facturas, manage_roles, manage_usuarios'),
(571, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:42:04', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_facturas, manage_centros_costos'),
(572, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 18:42:22', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, revisar_facturas, manage_roles, manage_usuarios'),
(573, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:42:22', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, revisar_facturas'),
(574, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 18:43:10', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, revisar_facturas, manage_roles, manage_usuarios'),
(575, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:43:10', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, revisar_facturas'),
(576, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:43:34', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, revisar_facturas, manage_centros_costos'),
(577, 19, NULL, 14, 'APROBADO', NULL, '2025-04-04 18:44:21', 'CREADO', 'Miguel', 'Liquidación creada por encargado'),
(578, 19, 39, 14, 'RECHAZADO', NULL, '2025-04-04 18:44:43', 'CREAR_DETALLE', 'Miguel', 'Factura creada: FACT-004'),
(579, 19, 40, 14, 'RECHAZADO', NULL, '2025-04-04 18:45:24', 'CREAR_DETALLE', 'Miguel', 'Factura creada: FACT-00433'),
(580, 19, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:45:39', 'FINALIZADO', 'Miguel', 'Liquidación finalizada por encargado'),
(581, 19, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 18:46:17', 'AUTORIZADO_POR_ADMIN', 'Administrador', 'esta bien'),
(582, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:47:04', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(583, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 18:47:42', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_tipos_gastos, revisar_facturas, revisar_liquidaciones, manage_roles, manage_usuarios'),
(584, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:47:42', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_tipos_gastos, revisar_facturas, revisar_liquidaciones'),
(585, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:48:02', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(586, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 18:48:54', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, manage_roles, manage_usuarios'),
(587, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:48:54', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(588, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 18:49:14', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos, manage_roles, manage_usuarios'),
(589, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:49:14', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_cuentas_contables, manage_impuestos, manage_tipos_gastos'),
(590, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:50:03', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles'),
(591, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:50:22', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos'),
(592, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:50:39', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles'),
(593, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 18:50:49', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_cuentas_contables, manage_impuestos, manage_roles, manage_tipos_gastos, manage_usuarios'),
(594, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:50:49', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: create_detalles, create_liquidaciones, manage_cajachica, manage_cuentas_contables, manage_impuestos, manage_roles, manage_tipos_gastos'),
(595, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 18:51:19', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, autorizar_liquidaciones, revisar_liquidaciones'),
(596, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:15:40', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(597, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:15:40', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(598, NULL, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 20:15:40', 'ACTUALIZAR_ROL', 'Administrador', 'Rol actualizado: Admin Junior'),
(599, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:15:57', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos asignados a usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(600, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-04 20:15:57', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: miguel@gmail.com'),
(601, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:16:04', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos asignados a usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(602, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-04 20:16:04', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: pepe@gmail.com'),
(603, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:18:27', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(604, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:18:48', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(605, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:18:48', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(606, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:18:48', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(607, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:19:54', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos');
INSERT INTO `auditoria` (`id`, `id_liquidacion`, `id_detalle_liquidacion`, `id_usuario`, `accion`, `comentario`, `fecha`, `tipo_accion`, `usuario_nombre`, `detalles`) VALUES
(608, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:20:38', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(609, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:21:05', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas, manage_roles, manage_usuarios'),
(610, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:21:05', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas'),
(611, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:21:05', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(612, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:21:46', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas, manage_roles, manage_usuarios'),
(613, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:21:46', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas'),
(614, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:21:46', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas'),
(615, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:21:57', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas, manage_roles, manage_usuarios'),
(616, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:21:57', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas'),
(617, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:21:57', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas'),
(618, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:22:54', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(619, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:23:32', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, manage_roles'),
(620, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:23:32', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, create_liquidaciones, manage_roles'),
(621, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:23:32', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas'),
(622, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:23:50', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas, manage_roles, manage_usuarios'),
(623, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:23:50', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas, create_liquidaciones, manage_roles, manage_usuarios'),
(624, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:23:50', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas'),
(625, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:24:03', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, create_liquidaciones, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas, manage_roles, manage_usuarios'),
(626, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:24:03', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, create_liquidaciones, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas, manage_roles, manage_usuarios'),
(627, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:24:03', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, create_liquidaciones, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, revisar_detalles_liquidaciones, revisar_facturas'),
(628, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:24:27', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(629, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:24:43', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(630, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:24:51', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, create_liquidaciones, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas'),
(631, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:24:51', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, create_liquidaciones, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas'),
(632, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:24:51', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, create_liquidaciones, manage_accesos, manage_auditoria, manage_centros_costos, manage_facturas, manage_reportes, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas'),
(633, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:25:12', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, manage_roles, manage_usuarios, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(634, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:25:47', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(635, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:45:08', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(636, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:45:28', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos asignados a usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(637, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-04 20:45:28', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: miguel@gmail.com'),
(638, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:45:32', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos asignados a usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, listar_bases, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(639, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-04 20:45:32', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: pepe@gmail.com'),
(640, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:50:28', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones, manage_roles, manage_usuarios'),
(641, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:50:28', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(642, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:50:28', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(643, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:52:15', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(644, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:52:33', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(645, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:52:51', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(646, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:53:43', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones, manage_roles, manage_usuarios'),
(647, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:53:43', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(648, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:53:43', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(649, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:54:15', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(650, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:54:31', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(651, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:55:16', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones, manage_roles, manage_usuarios'),
(652, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:55:16', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(653, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:55:16', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(654, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:55:37', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(655, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:56:09', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(656, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:56:37', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(657, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:56:54', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(658, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:57:11', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones, manage_roles, manage_usuarios'),
(659, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:57:11', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(660, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:57:11', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_tipos_gastos, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(661, NULL, NULL, 8, 'RECHAZADO', NULL, '2025-04-04 20:59:03', 'ASIGNAR_PERMISOS', 'Angel De León', 'Permisos actualizados para usuario angel.deleon@agrocentro.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(662, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:59:03', 'ASIGNAR_PERMISOS', 'Miguel', 'Permisos actualizados para usuario miguel@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(663, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:59:03', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 18: autorizar_facturas, autorizar_liquidaciones, create_detalles, create_liquidaciones, manage_accesos, manage_auditoria, manage_cajachica, manage_centros_costos, manage_cuentas_contables, manage_facturas, manage_impuestos, manage_reportes, manage_roles, manage_tipos_gastos, manage_usuarios, revisar_detalles_liquidaciones, revisar_facturas, revisar_liquidaciones'),
(664, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 20:59:13', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(665, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 20:59:17', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(666, NULL, NULL, 14, 'RECHAZADO', NULL, '2025-04-04 21:02:18', 'ASIGNAR_MODULOS', 'Miguel', 'Módulos asignados a usuario miguel@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(667, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-04 21:03:01', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(668, 20, NULL, 1, 'APROBADO', NULL, '2025-04-04 21:04:08', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(669, 20, 41, 1, 'RECHAZADO', NULL, '2025-04-04 21:04:48', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(670, 20, 42, 1, 'RECHAZADO', NULL, '2025-04-04 21:05:41', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-00433'),
(671, 20, NULL, 1, 'RECHAZADO', NULL, '2025-04-04 21:06:15', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(672, 20, NULL, 3, 'APROBADO', NULL, '2025-04-04 21:07:06', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo'),
(673, 20, NULL, 1, 'EXPORTADO_SAP', NULL, '2025-04-04 21:07:36', 'EXPORTADO', 'Administrador', 'Liquidación exportada a SAP como liquidacion_20_20250404_230736.csv'),
(674, 21, NULL, 1, 'APROBADO', NULL, '2025-04-07 18:42:01', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(675, 21, 43, 1, 'RECHAZADO', NULL, '2025-04-07 18:43:45', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(676, 21, 43, 1, 'RECHAZADO', NULL, '2025-04-07 18:44:07', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: FACT-004'),
(677, 21, 44, 1, 'RECHAZADO', NULL, '2025-04-07 18:45:00', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-0040'),
(678, 21, 45, 1, 'RECHAZADO', NULL, '2025-04-07 18:45:36', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(679, 21, NULL, 1, 'RECHAZADO', NULL, '2025-04-07 18:47:04', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(680, 21, NULL, 3, 'APROBADO', NULL, '2025-04-07 18:48:19', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completado '),
(681, 21, NULL, 1, 'EXPORTADO_SAP', NULL, '2025-04-07 18:48:33', 'EXPORTADO', 'Administrador', 'Liquidación exportada a SAP como liquidacion_21_20250407_204833.csv'),
(682, 22, NULL, 1, 'APROBADO', NULL, '2025-04-07 18:49:48', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(683, 22, 46, 1, 'RECHAZADO', NULL, '2025-04-07 18:50:15', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(684, 22, NULL, 1, 'RECHAZADO', NULL, '2025-04-07 18:50:49', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(685, 22, NULL, 4, 'RECHAZADO', NULL, '2025-04-07 18:54:35', 'RECHAZADO_POR_CONTABILIDAD', 'Contador 1', 'Esta incompleto '),
(686, 23, NULL, 1, 'APROBADO', NULL, '2025-04-07 18:58:00', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(687, 23, 47, 1, 'RECHAZADO', NULL, '2025-04-07 18:58:27', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(688, 23, NULL, 1, 'RECHAZADO', NULL, '2025-04-07 18:58:36', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(689, 23, NULL, 3, 'RECHAZADO', NULL, '2025-04-07 18:59:41', 'RECHAZADO_POR_SUPERVISOR', 'Supervisor 1', 'Datos incorrectos '),
(690, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 20:12:42', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(691, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 20:14:00', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, manage_reportes, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(692, 24, NULL, 15, 'APROBADO', NULL, '2025-04-07 20:18:42', 'CREADO', 'Pepe', 'Liquidación creada por encargado'),
(693, 24, 48, 15, 'RECHAZADO', NULL, '2025-04-07 20:19:22', 'CREAR_DETALLE', 'Pepe', 'Factura creada: FACT-004'),
(694, 24, 49, 15, 'RECHAZADO', NULL, '2025-04-07 20:20:19', 'CREAR_DETALLE', 'Pepe', 'Factura creada: FACT-0047'),
(695, 24, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 20:52:02', 'FINALIZADO', 'Pepe', 'Liquidación finalizada por encargado'),
(696, 24, NULL, 3, 'APROBADO', NULL, '2025-04-07 20:53:20', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo'),
(697, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 20:53:58', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(698, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 20:54:43', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(699, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 21:04:19', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, create_detalles, manage_cajachica, manage_impuestos, manage_cuentas_contables, manage_tipos_gastos, manage_roles, manage_usuarios, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_reportes, manage_auditoria, manage_accesos, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(700, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 21:07:25', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos asignados a usuario pepe@gmail.com desde rol ID 3: autorizar_liquidaciones, autorizar_facturas, manage_cuentas_contables, manage_facturas, revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas'),
(701, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-07 21:07:25', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: pepe@gmail.com'),
(702, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 21:08:21', 'ASIGNAR_MODULOS', 'Pepe', 'Módulos asignados a usuario pepe@gmail.com: create_liquidaciones, manage_cuentas_contables, autorizar_liquidaciones, revisar_liquidaciones, revisar_detalles_liquidaciones, manage_facturas, autorizar_facturas, revisar_facturas, manage_centros_costos'),
(703, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-07 21:09:28', 'ACTUALIZAR_USUARIO', 'Administrador', 'Usuario actualizado: pepe@gmail.com'),
(704, NULL, NULL, 4, 'RECHAZADO', NULL, '2025-04-07 21:10:02', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, autorizar_liquidaciones, manage_accesos'),
(705, NULL, NULL, 10, 'RECHAZADO', NULL, '2025-04-07 21:10:02', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, autorizar_liquidaciones, manage_accesos'),
(706, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 21:10:02', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, autorizar_liquidaciones, manage_accesos'),
(707, NULL, NULL, 4, 'RECHAZADO', NULL, '2025-04-07 21:10:27', 'ASIGNAR_PERMISOS', 'Contador 1', 'Permisos actualizados para usuario contador1@example.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, autorizar_liquidaciones, create_liquidaciones, manage_accesos'),
(708, NULL, NULL, 10, 'RECHAZADO', NULL, '2025-04-07 21:10:27', 'ASIGNAR_PERMISOS', 'Omar ', 'Permisos actualizados para usuario omar@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, autorizar_liquidaciones, create_liquidaciones, manage_accesos'),
(709, NULL, NULL, 15, 'RECHAZADO', NULL, '2025-04-07 21:10:27', 'ASIGNAR_PERMISOS', 'Pepe', 'Permisos actualizados para usuario pepe@gmail.com desde rol ID 4: revisar_liquidaciones, revisar_detalles_liquidaciones, revisar_facturas, manage_reportes, manage_auditoria, manage_cuentas_contables, manage_facturas, manage_centros_costos, manage_impuestos, manage_tipos_gastos, autorizar_liquidaciones, create_liquidaciones, manage_accesos'),
(710, 25, NULL, 1, 'APROBADO', NULL, '2025-04-07 21:12:47', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(711, 25, 50, 1, 'RECHAZADO', NULL, '2025-04-07 21:13:36', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(712, 25, 51, 1, 'RECHAZADO', NULL, '2025-04-07 21:15:22', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-0044'),
(713, 25, 50, 1, 'RECHAZADO', NULL, '2025-04-07 21:16:03', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-004'),
(714, 25, NULL, 1, 'RECHAZADO', NULL, '2025-04-07 21:19:55', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(715, 25, NULL, 15, 'APROBADO', NULL, '2025-04-07 21:25:16', 'AUTORIZADO_POR_CONTABILIDAD', 'Pepe', 'esta completo '),
(716, 26, NULL, 1, 'APROBADO', NULL, '2025-04-07 21:34:23', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(717, 26, 52, 1, 'RECHAZADO', NULL, '2025-04-08 14:23:36', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(718, 26, 53, 1, 'RECHAZADO', NULL, '2025-04-08 14:24:17', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-0043');

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
(2, 'AGROCENTRO HONDURAS', NULL);

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
  `id_centro_costo` int(11) DEFAULT NULL,
  `estado` enum('ACTIVA','INACTIVA') DEFAULT 'ACTIVA',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `cajas_chicas`
--

INSERT INTO `cajas_chicas` (`id`, `nombre`, `monto_inicial`, `monto_actual`, `monto_asignado`, `monto_disponible`, `id_usuario_encargado`, `id_supervisor`, `id_centro_costo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Caja Chica 1', 5000.00, 5000.00, 5000.00, 5000.00, 2, 3, 1, 'ACTIVA', '2025-03-21 20:05:25', '2025-03-21 20:05:25'),
(2, 'Caja chica 2', 0.00, 0.00, 6000.00, 6000.00, 2, 3, 1, 'ACTIVA', '2025-03-21 21:30:21', '2025-03-21 21:30:59'),
(3, 'Caja Chica 3', 0.00, 0.00, 5000.00, 5000.00, 10, 3, 3, 'ACTIVA', '2025-03-23 20:07:30', '2025-03-26 20:34:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centros_costos`
--

CREATE TABLE `centros_costos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(10) DEFAULT '5',
  `base_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `centros_costos`
--

INSERT INTO `centros_costos` (`id`, `codigo`, `nombre`, `estado`, `creado_en`, `tipo`, `base_id`) VALUES
(1, '1001', 'Cuenta de Activos', 'ACTIVO', '2025-03-21 20:05:53', '5', 1),
(2, '2001', 'Cuenta de Pasivos', 'ACTIVO', '2025-03-21 20:05:53', '5', 2),
(3, '100', 'Angel', 'INACTIVO', '2025-03-21 21:44:22', '2', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_contables`
--

CREATE TABLE `cuentas_contables` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `cuentas_contables`
--

INSERT INTO `cuentas_contables` (`id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Centro de Costos 1', 'Centro de costos para pruebas', 'ACTIVO', '2025-03-21 18:56:42', '2025-03-21 18:56:42'),
(2, 'Centro de Costos 2', 'Costo de viajes ', 'ACTIVO', '2025-03-23 18:24:56', '2025-03-23 18:24:56'),
(3, 'Centro de Costos 3', 'prueba ', 'INACTIVO', '2025-03-25 20:37:32', '2025-03-25 20:37:32');

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
  `tipo_documento` varchar(50) NOT NULL DEFAULT 'FACTURA',
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
  `estado` enum('PENDIENTE','EN_REVISIÓN','AUTORIZADO_POR_ADMIN','RECHAZADO_POR_ADMIN','AUTORIZADO_POR_CONTABILIDAD','RECHAZADO_POR_CONTABILIDAD','AUTORIZADO_POR_SUPERVISOR','RECHAZADO_POR_SUPERVISOR','DESCARTADO') NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rutas_archivos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `detalle_liquidaciones`
--

INSERT INTO `detalle_liquidaciones` (`id`, `id_liquidacion`, `no_factura`, `regimen`, `c_costo`, `nit_proveedor`, `tipo_documento`, `nombre_proveedor`, `fecha`, `bien_servicio`, `t_gasto`, `codigo_ccta`, `descripcion_factura`, `p_unitario`, `iva`, `total_factura`, `idp`, `inguat`, `porcentajeiva`, `porcentajeidp`, `tipo_combustible`, `estado`, `created_at`, `updated_at`, `rutas_archivos`) VALUES
(1, 1, 'FACT-001', NULL, NULL, '123456-7', 'FACTURA', 'Proveedor Prueba', '2025-03-21', 'Materiales de Oficina', 'OPERATIVO', NULL, 'Compra de materiales', 800.00, 96.00, 896.00, NULL, NULL, 12.00, NULL, NULL, 'PENDIENTE', '2025-03-21 20:06:59', '2025-03-21 20:06:59', '[]'),
(2, 1, 'Fact-002', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 2', '2025-03-20', 'servicio', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-21 20:19:50', '2025-03-23 18:22:15', '[\"uploads\\/67ddc9e5f41d6_Captura.PNG\",\"uploads\\/67dddac1d92b3_adaptar.JPG\",\"uploads\\/67dddac1d98af_Captura.PNG\"]'),
(3, 3, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Miguel perez', '2025-03-23', 'servicio', 'Gasto Operativo', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, 'RECHAZADO_POR_CONTABILIDAD', '2025-03-23 20:09:38', '2025-03-23 20:17:10', '[\"uploads\\/67e06a8250300_Captura.PNG\"]'),
(4, 5, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-03-27', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 333.00, NULL, 443.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-28 17:22:53', '2025-03-28 17:22:53', '[]'),
(5, 5, 'Fact-002', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-03-21', 'servicio', 'gasolina', NULL, NULL, 323.00, NULL, 232.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-28 17:23:41', '2025-03-28 17:23:41', '[]'),
(6, 5, 'FACT-005', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-03-27', 'servicio', 'gasolina', NULL, NULL, 300.00, NULL, 300.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-28 20:40:43', '2025-03-28 20:40:43', '[]'),
(7, 5, 'FACT-005', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-03-29', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-28 20:43:08', '2025-03-28 20:43:08', '[]'),
(8, 6, 'FACT-006', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-03-31', 'bien', 'gasolina', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-31 21:24:56', '2025-03-31 21:24:56', '[]'),
(9, 6, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'sara', '2025-03-30', 'servicio', 'gas', NULL, NULL, 2000.00, NULL, 2000.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-31 21:25:19', '2025-03-31 21:25:19', '[]'),
(12, 9, 'Fact-002', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-02 17:38:32', '2025-04-02 17:38:32', '[]'),
(13, 9, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 5', '2025-04-01', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-02 17:39:03', '2025-04-02 17:39:03', '[]'),
(15, 9, 'FACT-005', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-04-01', 'Servicio Prueba 4', 'gas', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-02 18:16:26', '2025-04-02 18:16:26', '[]'),
(16, 9, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', 'Servicio Prueba 5', 'gasolina', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-02 18:16:57', '2025-04-02 18:16:57', '[]'),
(17, 9, 'Fact-0031', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-03-31', 'servicio', 'gasolina', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-02 20:43:54', '2025-04-02 20:43:54', '[]'),
(18, 9, 'FACT-006', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-04-01', 'servicio', 'gasolina', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-03 14:16:38', '2025-04-03 14:16:38', '[]'),
(19, 9, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-03 15:35:09', '2025-04-03 15:35:09', '[]'),
(20, 9, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', 'servicio', 'COMBUSTIBLE', NULL, NULL, 333.00, NULL, 333.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-03 17:39:03', '2025-04-03 17:39:03', '[]'),
(22, 11, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel', '2025-04-02', 'servicio', 'Combustible', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-03 18:44:48', '2025-04-03 18:44:48', '[]'),
(23, 11, 'FACT-005', NULL, NULL, '23232323444', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 4', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-03 20:45:44', '2025-04-03 20:45:44', '[]'),
(24, 11, 'FACT-006', NULL, NULL, '55565656', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 6', 'Combustible', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-03 20:59:32', '2025-04-03 20:59:32', '[]'),
(25, 2, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-03-10', 'Servicio Prueba 5', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 13:56:34', '2025-04-04 13:56:34', '[]'),
(26, 2, 'FACT-006', NULL, NULL, '232323233', 'RECIBO', 'Miguel', '2025-03-10', 'servicio', 'Alimentos', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 13:57:05', '2025-04-04 13:57:05', '[]'),
(27, 12, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel', '2025-04-02', 'servicio', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 15:23:56', '2025-04-04 15:23:56', '[]'),
(28, 12, 'FACT-006', NULL, NULL, '232323232232', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 4', 'Gasto Operativo', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 15:24:21', '2025-04-04 15:24:21', '[]'),
(29, 12, 'FACT-005', NULL, NULL, '232323231', 'FACTURA', 'Miguel', '2025-04-01', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 15:46:23', '2025-04-04 15:46:23', '[]'),
(30, 12, 'FACT-00433', NULL, NULL, '232323232223', 'FACTURA', 'Miguel', '2025-04-02', 'servicio', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 15:51:18', '2025-04-04 15:51:18', '[]'),
(33, 15, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel', '2025-04-03', 'Servicio Prueba 5', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 16:46:51', '2025-04-04 16:46:51', '[]'),
(34, 16, 'FACT-004', NULL, NULL, '34343', 'FACTURA', 'Miguel', '2025-04-03', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 17:00:54', '2025-04-04 17:00:54', '[]'),
(35, 16, 'FACT-006', NULL, NULL, '100', 'RECIBO', 'Proveedor Prueba 4', '2025-04-02', 'servicio', 'Gasto Operativo', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 17:01:17', '2025-04-04 17:01:17', '[]'),
(36, 17, 'FACT-006', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 4', 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 17:29:41', '2025-04-04 17:29:41', '[]'),
(37, 18, 'FACT-004', NULL, NULL, '232323232223', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'servicio', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 18:36:55', '2025-04-04 18:36:55', '[]'),
(38, 18, 'Fact-003', NULL, NULL, '23232323222334242', 'RECIBO', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 4', 'Alimentos', NULL, NULL, 300.00, NULL, 300.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 18:37:27', '2025-04-04 18:37:27', '[]'),
(39, 19, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel perez', '2025-04-02', 'Servicio Prueba 5', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 18:44:43', '2025-04-04 18:44:43', '[]'),
(40, 19, 'FACT-00433', NULL, NULL, '232323232223', 'FACTURA', 'Miguel', '2025-04-03', 'Servicio Prueba 2', 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 18:45:24', '2025-04-04 18:45:24', '[]'),
(41, 20, 'FACT-004', NULL, NULL, '232323232223', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'servicio', 'Alimentos', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 21:04:48', '2025-04-04 21:04:48', '[]'),
(42, 20, 'FACT-00433', NULL, NULL, '2323232322235335', 'RECIBO', 'Miguel', '2025-04-02', 'servicio', 'Hospedaje', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-04 21:05:41', '2025-04-04 21:05:41', '[]'),
(43, 21, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-04-06', 'servicio', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-07 18:43:45', '2025-04-07 18:44:07', '[]'),
(44, 21, 'FACT-0040', NULL, NULL, '2323232340', 'RECIBO', 'Proveedor Prueba 4', '2025-04-07', 'Servicio Prueba 4', 'Alimentos', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-07 18:45:00', '2025-04-07 18:45:00', '[]'),
(45, 21, 'Fact-0031', NULL, NULL, '232323232223', 'COMPROBANTE', 'Proveedor Prueba 4', '2025-04-06', 'Servicio Prueba 4', 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-07 18:45:36', '2025-04-07 18:45:36', '[]'),
(46, 22, 'FACT-005', NULL, NULL, '232323232223', 'FACTURA', 'Proveedor Prueba 5', '2025-04-04', 'Servicio Prueba 6', 'Hospedaje', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-07 18:50:15', '2025-04-07 18:50:15', '[]'),
(47, 23, 'Fact-0031', NULL, NULL, '232323232223', 'FACTURA', 'Proveedor Prueba 6', '2025-04-06', 'Servicio Prueba 2', 'Hospedaje', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-07 18:58:27', '2025-04-07 18:58:27', '[]'),
(48, 24, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 6', '2025-04-03', 'Servicio Prueba 4', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-07 20:19:22', '2025-04-07 20:19:22', '[]'),
(49, 24, 'FACT-0047', NULL, NULL, '2323232332', 'RECIBO', 'sara', '2025-04-06', 'servicio', 'Combustible', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-07 20:20:19', '2025-04-07 20:20:19', '[]'),
(51, 25, 'FACT-0044', NULL, NULL, '232323232223333', 'FACTURA', 'Miguel', '2025-04-07', 'Servicio Prueba 5', 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-07 21:15:22', '2025-04-07 21:15:22', '[]'),
(52, 26, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-04-06', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-08 14:23:36', '2025-04-08 14:23:36', '[]'),
(53, 26, 'FACT-0043', NULL, NULL, '23232323', 'RECIBO', 'Proveedor Prueba 4', '2025-04-06', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-04-08 14:24:17', '2025-04-08 14:24:17', '[]');

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
(1, 'FACT-001', '2025-03-21', 'Proveedor Prueba', 1000.00, 1, 1, 'PAGADA', '2025-03-21 20:07:11'),
(2, 'FACT-003', '2025-03-20', 'Proveedor AAA', 1000.00, 1, 1, 'RECHAZADO', '2025-03-21 20:21:01'),
(3, 'FACT-002', '2025-03-23', 'Pepe', 1000.00, 1, 1, 'PAGADA', '2025-03-23 20:03:35'),
(4, 'FACT-004', '2025-03-23', 'Proveedor AA', 1000.00, 2, 1, 'APROBADO', '2025-03-23 20:12:17'),
(5, 'FACT-005', '2025-03-24', 'CARLOS', 300.00, 2, 3, 'PENDIENTE', '2025-03-25 20:44:54'),
(6, '1311', '2025-03-25', 'Carlor', 500.00, 1, 2, 'PENDIENTE', '2025-03-25 21:25:48');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidaciones`
--

CREATE TABLE `liquidaciones` (
  `id` int(11) NOT NULL,
  `id_caja_chica` int(11) NOT NULL,
  `id_centros_de_costos` int(11) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT 0.00,
  `estado` enum('PENDIENTE','AUTORIZADO_POR_ADMIN','RECHAZADO_POR_ADMIN','AUTORIZADO_POR_CONTABILIDAD','RECHAZADO_POR_CONTABILIDAD','AUTORIZADO_POR_SUPERVISOR','RECHAZADO_POR_SUPERVISOR','PENDIENTE_CORRECCIÓN','DESCARTADO','FINALIZADO') NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exportado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `liquidaciones`
--

INSERT INTO `liquidaciones` (`id`, `id_caja_chica`, `id_centros_de_costos`, `fecha_creacion`, `fecha_inicio`, `fecha_fin`, `monto_total`, `estado`, `created_at`, `updated_at`, `exportado`) VALUES
(1, 1, 0, '2025-03-21', '2025-03-01', '2025-03-21', 1000.00, 'AUTORIZADO_POR_CONTABILIDAD', '2025-03-21 20:06:47', '2025-03-27 14:21:47', 1),
(2, 2, 0, '2025-03-21', '2025-03-07', '2025-03-14', 200.00, '', '2025-03-21 20:15:47', '2025-04-04 15:49:27', 0),
(3, 3, 0, '2025-03-23', '2025-03-08', '2025-03-23', 1000.00, '', '2025-03-23 20:08:25', '2025-03-23 20:10:39', 0),
(4, 3, 0, '2025-03-25', '2025-03-01', '2025-03-25', 1000.00, 'AUTORIZADO_POR_ADMIN', '2025-03-25 21:24:01', '2025-03-26 18:30:56', 0),
(5, 1, 0, '2025-03-27', '2025-03-01', '2025-03-27', 1975.00, 'AUTORIZADO_POR_ADMIN', '2025-03-27 21:47:13', '2025-03-28 20:43:08', 0),
(6, 1, 0, '2025-03-31', '2025-03-08', '2025-03-31', 5000.00, '', '2025-03-31 21:23:40', '2025-04-04 17:05:22', 0),
(9, 1, 0, '2025-04-02', '2025-03-01', '2025-04-02', 3133.00, 'AUTORIZADO_POR_CONTABILIDAD', '2025-04-02 17:38:04', '2025-04-04 18:26:11', 0),
(11, 2, 0, '2025-04-03', '2025-04-01', '2025-04-04', 800.00, '', '2025-04-03 18:42:20', '2025-04-04 16:07:35', 0),
(12, 1, 0, '2025-04-04', '2025-03-31', '2025-04-04', 600.00, '', '2025-04-04 14:37:27', '2025-04-04 16:10:00', 0),
(15, 1, 0, '2025-04-01', '2025-03-31', '2025-04-05', 100.00, '', '2025-04-04 16:46:17', '2025-04-04 16:47:28', 0),
(16, 1, 0, '2025-04-04', '2025-04-01', '2025-04-04', 300.00, '', '2025-04-04 17:00:32', '2025-04-04 17:01:44', 0),
(17, 1, 0, '2025-04-04', '2025-04-01', '2025-04-03', 100.00, 'AUTORIZADO_POR_SUPERVISOR', '2025-04-04 17:29:18', '2025-04-04 17:30:26', 0),
(18, 2, 0, '2025-04-01', '2025-04-01', '2025-04-04', 400.00, 'FINALIZADO', '2025-04-04 18:36:08', '2025-04-04 18:37:49', 0),
(19, 1, 0, '2025-04-04', '2025-04-01', '2025-04-04', 200.00, 'AUTORIZADO_POR_ADMIN', '2025-04-04 18:44:21', '2025-04-04 18:46:17', 0),
(20, 1, 0, '2025-04-04', '2025-04-01', '2025-04-04', 600.00, 'AUTORIZADO_POR_SUPERVISOR', '2025-04-04 21:04:08', '2025-04-04 21:07:36', 1),
(21, 1, 0, '2025-04-07', '2025-04-01', '2025-04-07', 400.00, 'AUTORIZADO_POR_SUPERVISOR', '2025-04-07 18:42:01', '2025-04-07 18:48:33', 1),
(22, 2, 0, '2025-04-07', '2025-04-01', '2025-04-07', 500.00, 'RECHAZADO_POR_CONTABILIDAD', '2025-04-07 18:49:48', '2025-04-07 18:54:35', 0),
(23, 3, 0, '2025-04-07', '2025-03-31', '2025-04-07', 1000.00, 'RECHAZADO_POR_SUPERVISOR', '2025-04-07 18:58:00', '2025-04-07 18:59:41', 0),
(24, 3, 0, '2025-04-07', '2025-04-01', '2025-04-07', 600.00, 'AUTORIZADO_POR_SUPERVISOR', '2025-04-07 20:18:42', '2025-04-07 20:53:20', 0),
(25, 2, 0, '2025-04-07', '2025-04-01', '2025-04-07', 100.00, 'AUTORIZADO_POR_CONTABILIDAD', '2025-04-07 21:12:47', '2025-04-07 21:25:16', 0),
(26, 1, 0, '2025-04-07', '2025-04-01', '2025-04-07', 200.00, 'PENDIENTE', '2025-04-07 21:34:23', '2025-04-08 14:24:17', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `permiso_predeterminado` varchar(50) DEFAULT NULL,
  `ruta` varchar(255) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `nombre`, `permiso_predeterminado`, `ruta`, `estado`, `created_at`) VALUES
(1, 'Gestión de Liquidaciones', 'create_liquidaciones', 'index.php?controller=liquidacion&action=list', 'ACTIVO', '2025-03-21 20:06:05'),
(2, 'Gestión de Liquidaciones Detalles', 'create_detalles', 'index.php?controller=detalleliquidacion&action=list', 'ACTIVO', '2025-03-21 20:06:05'),
(3, 'Gestión de Cajas Chicas', 'manage_cajachica', '', 'ACTIVO', '2025-03-21 20:06:05'),
(4, 'Gestión de Facturas', 'manage_facturas', 'index.php?controller=factura&action=list', 'ACTIVO', '2025-03-21 20:06:05'),
(5, 'Autorizar Facturas', 'autorizar_facturas', 'index.php?controller=factura&action=list&mode=autorizar', 'ACTIVO', '2025-03-21 20:06:05'),
(6, 'Gestión de Impuestos', 'manage_impuestos', 'index.php?controller=impuesto&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(7, 'Gestión de Cuentas Contables', 'manage_cuentas_contables', 'index.php?controller=cuentacontable&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(8, 'Gestión de Tipos de Gastos', 'manage_tipos_gastos', 'index.php?controller=tipogasto&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(9, 'Gestión de Centros de Costos', 'manage_centros_costos', 'index.php?controller=centrocosto&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(10, 'Gestión de Roles', 'manage_roles', 'index.php?controller=rol&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(11, 'Gestión de Usuarios', 'manage_usuarios', 'index.php?controller=usuario&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(12, 'Autorizar Liquidaciones', 'autorizar_liquidaciones', 'index.php?controller=liquidacion&action=list&mode=autorizar', 'ACTIVO', '2025-03-21 20:47:24'),
(13, 'Revisar Liquidaciones', 'revisar_liquidaciones', 'index.php?controller=liquidacion&action=list&mode=revisar', 'ACTIVO', '2025-03-21 20:47:24'),
(14, 'Revisar Detalles de Liquidaciones', 'revisar_detalles_liquidaciones', 'index.php?controller=detalleliquidacion&action=revisar', 'ACTIVO', '2025-03-21 20:47:24'),
(15, 'Generar Reportes', 'manage_reportes', 'index.php?controller=reportes&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(16, 'Consultar Auditoría', 'manage_auditoria', 'index.php?controller=auditoria&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(17, 'Administración de Accesos', 'manage_accesos', 'index.php?controller=acceso&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(18, 'Revisar Facturas', 'revisar_facturas', 'index.php?controller=factura&action=list&mode=revisar', 'ACTIVO', '2025-03-21 20:47:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `descripcion`, `creado_en`) VALUES
(1, 'create_liquidaciones', 'Permite crear liquidaciones', '2025-03-21 20:06:19'),
(2, 'create_detalles', 'Permite crear detalles de liquidaciones', '2025-03-21 20:06:19'),
(3, 'manage_cajachica', 'Permite gestionar cajas chicas', '2025-03-21 20:06:19'),
(4, 'manage_facturas', 'Permite gestionar facturas', '2025-03-21 20:06:19'),
(5, 'autorizar_facturas', 'Permite autorizar facturas', '2025-03-21 20:06:19'),
(6, 'manage_impuestos', 'Permite gestionar impuestos', '2025-03-21 20:46:41'),
(7, 'manage_cuentas_contables', 'Permite gestionar cuentas contables', '2025-03-21 20:46:41'),
(8, 'manage_tipos_gastos', 'Permite gestionar tipos de gastos', '2025-03-21 20:46:41'),
(9, 'manage_centros_costos', 'Permite gestionar centros de costos', '2025-03-21 20:46:41'),
(10, 'manage_roles', 'Permite gestionar roles', '2025-03-21 20:46:41'),
(11, 'manage_usuarios', 'Permite gestionar usuarios', '2025-03-21 20:46:41'),
(12, 'autorizar_liquidaciones', 'Permite autorizar liquidaciones', '2025-03-21 20:46:41'),
(13, 'revisar_liquidaciones', 'Permite revisar liquidaciones', '2025-03-21 20:46:41'),
(14, 'revisar_detalles_liquidaciones', 'Permite revisar detalles de liquidaciones', '2025-03-21 20:46:41'),
(15, 'manage_reportes', 'Permite generar reportes', '2025-03-21 20:46:41'),
(16, 'manage_auditoria', 'Permite consultar auditoría', '2025-03-21 20:46:41'),
(17, 'manage_accesos', 'Permite administrar accesos', '2025-03-21 20:46:41'),
(18, 'revisar_facturas', 'Permite revisar facturas', '2025-03-21 20:46:41'),
(19, 'listar_bases', 'Permite listar bases de centros de costos', '2025-04-02 15:35:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

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
(3, 'SUPERVISOR', 'Supervisor autorizador', 'ACTIVO', '2025-03-04 14:22:33'),
(4, 'CONTABILIDAD', 'contabilidad', 'ACTIVO', '2025-03-04 14:22:33'),
(17, 'Contador admin', 'sera Contador', 'ACTIVO', '2025-04-02 14:33:27'),
(18, 'Admin Junior', 'sera Admin junior se limita accesos y permisos', 'ACTIVO', '2025-04-02 15:14:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `id` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `permiso` varchar(100) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`id`, `id_rol`, `permiso`, `estado`, `created_at`) VALUES
(33, 4, 'manage_impuestos', 'ACTIVO', '2025-03-27 17:17:15'),
(34, 4, 'manage_cuentas_contables', 'ACTIVO', '2025-03-27 17:17:15'),
(35, 4, 'manage_tipos_gastos', 'ACTIVO', '2025-03-27 17:17:15'),
(36, 4, 'revisar_liquidaciones', 'ACTIVO', '2025-03-27 17:17:15'),
(37, 4, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-03-27 17:17:15'),
(38, 4, 'manage_reportes', 'ACTIVO', '2025-03-27 17:17:15'),
(39, 4, 'manage_facturas', 'ACTIVO', '2025-03-27 17:17:15'),
(40, 4, 'revisar_facturas', 'ACTIVO', '2025-03-27 17:17:15'),
(41, 4, 'manage_centros_costos', 'ACTIVO', '2025-03-27 17:17:15'),
(42, 4, 'manage_auditoria', 'ACTIVO', '2025-03-27 17:17:15'),
(318, 2, 'create_liquidaciones', 'ACTIVO', '2025-03-27 21:39:30'),
(319, 2, 'create_detalles', 'ACTIVO', '2025-03-27 21:39:30'),
(320, 2, 'manage_cajachica', 'ACTIVO', '2025-03-27 21:39:30'),
(321, 2, 'manage_impuestos', 'INACTIVO', '2025-03-27 21:39:30'),
(322, 2, 'manage_cuentas_contables', 'INACTIVO', '2025-03-27 21:39:30'),
(323, 2, 'manage_tipos_gastos', 'INACTIVO', '2025-03-27 21:39:30'),
(324, 2, 'manage_roles', 'INACTIVO', '2025-03-27 21:39:30'),
(325, 2, 'manage_usuarios', 'INACTIVO', '2025-03-27 21:39:30'),
(326, 2, 'autorizar_liquidaciones', 'INACTIVO', '2025-03-27 21:39:30'),
(327, 2, 'revisar_liquidaciones', 'INACTIVO', '2025-03-27 21:39:30'),
(328, 2, 'revisar_detalles_liquidaciones', 'INACTIVO', '2025-03-27 21:39:30'),
(329, 2, 'manage_reportes', 'INACTIVO', '2025-03-27 21:39:30'),
(330, 2, 'manage_auditoria', 'INACTIVO', '2025-03-27 21:39:30'),
(331, 2, 'manage_accesos', 'INACTIVO', '2025-03-27 21:39:30'),
(332, 2, 'manage_facturas', 'ACTIVO', '2025-03-27 21:39:30'),
(333, 2, 'autorizar_facturas', 'INACTIVO', '2025-03-27 21:39:30'),
(334, 2, 'revisar_facturas', 'INACTIVO', '2025-03-27 21:39:30'),
(335, 2, 'manage_centros_costos', 'INACTIVO', '2025-03-27 21:39:30'),
(1132, 1, 'create_liquidaciones', 'ACTIVO', '2025-04-01 18:50:21'),
(1133, 1, 'create_detalles', 'ACTIVO', '2025-04-01 18:50:21'),
(1134, 1, 'manage_cajachica', 'ACTIVO', '2025-04-01 18:50:21'),
(1135, 1, 'manage_impuestos', 'ACTIVO', '2025-04-01 18:50:21'),
(1136, 1, 'manage_cuentas_contables', 'ACTIVO', '2025-04-01 18:50:21'),
(1137, 1, 'manage_tipos_gastos', 'ACTIVO', '2025-04-01 18:50:21'),
(1138, 1, 'manage_roles', 'ACTIVO', '2025-04-01 18:50:21'),
(1139, 1, 'manage_usuarios', 'ACTIVO', '2025-04-01 18:50:21'),
(1140, 1, 'autorizar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:21'),
(1141, 1, 'revisar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:21'),
(1142, 1, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-04-01 18:50:21'),
(1143, 1, 'manage_reportes', 'ACTIVO', '2025-04-01 18:50:21'),
(1144, 1, 'manage_auditoria', 'ACTIVO', '2025-04-01 18:50:21'),
(1145, 1, 'manage_accesos', 'ACTIVO', '2025-04-01 18:50:21'),
(1146, 1, 'manage_facturas', 'ACTIVO', '2025-04-01 18:50:21'),
(1147, 1, 'autorizar_facturas', 'ACTIVO', '2025-04-01 18:50:21'),
(1148, 1, 'revisar_facturas', 'ACTIVO', '2025-04-01 18:50:21'),
(1149, 1, 'manage_centros_costos', 'ACTIVO', '2025-04-01 18:50:21'),
(1150, 3, 'create_liquidaciones', 'ACTIVO', '2025-04-01 18:50:35'),
(1151, 3, 'create_detalles', 'INACTIVO', '2025-04-01 18:50:35'),
(1152, 3, 'manage_cajachica', 'INACTIVO', '2025-04-01 18:50:35'),
(1153, 3, 'manage_impuestos', 'INACTIVO', '2025-04-01 18:50:35'),
(1154, 3, 'manage_cuentas_contables', 'ACTIVO', '2025-04-01 18:50:35'),
(1155, 3, 'manage_tipos_gastos', 'INACTIVO', '2025-04-01 18:50:35'),
(1156, 3, 'manage_roles', 'INACTIVO', '2025-04-01 18:50:35'),
(1157, 3, 'manage_usuarios', 'INACTIVO', '2025-04-01 18:50:35'),
(1158, 3, 'autorizar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:35'),
(1159, 3, 'revisar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:35'),
(1160, 3, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-04-01 18:50:35'),
(1161, 3, 'manage_reportes', 'INACTIVO', '2025-04-01 18:50:35'),
(1162, 3, 'manage_auditoria', 'INACTIVO', '2025-04-01 18:50:35'),
(1163, 3, 'manage_accesos', 'INACTIVO', '2025-04-01 18:50:35'),
(1164, 3, 'manage_facturas', 'ACTIVO', '2025-04-01 18:50:35'),
(1165, 3, 'autorizar_facturas', 'ACTIVO', '2025-04-01 18:50:35'),
(1166, 3, 'revisar_facturas', 'ACTIVO', '2025-04-01 18:50:35'),
(1167, 3, 'manage_centros_costos', 'ACTIVO', '2025-04-01 18:50:35'),
(1168, 4, 'create_liquidaciones', 'ACTIVO', '2025-04-01 18:50:56'),
(1169, 4, 'create_detalles', 'INACTIVO', '2025-04-01 18:50:56'),
(1170, 4, 'manage_cajachica', 'INACTIVO', '2025-04-01 18:50:56'),
(1174, 4, 'manage_roles', 'INACTIVO', '2025-04-01 18:50:56'),
(1175, 4, 'manage_usuarios', 'INACTIVO', '2025-04-01 18:50:56'),
(1176, 4, 'autorizar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:56'),
(1181, 4, 'manage_accesos', 'ACTIVO', '2025-04-01 18:50:56'),
(1183, 4, 'autorizar_facturas', 'INACTIVO', '2025-04-01 18:50:56'),
(1389, 17, 'create_liquidaciones', 'INACTIVO', '2025-04-02 14:35:59'),
(1390, 17, 'create_detalles', 'INACTIVO', '2025-04-02 14:35:59'),
(1391, 17, 'manage_cajachica', 'INACTIVO', '2025-04-02 14:35:59'),
(1392, 17, 'manage_impuestos', 'ACTIVO', '2025-04-02 14:35:59'),
(1393, 17, 'manage_cuentas_contables', 'ACTIVO', '2025-04-02 14:35:59'),
(1394, 17, 'manage_tipos_gastos', 'ACTIVO', '2025-04-02 14:35:59'),
(1395, 17, 'manage_roles', 'ACTIVO', '2025-04-02 14:35:59'),
(1396, 17, 'manage_usuarios', 'ACTIVO', '2025-04-02 14:35:59'),
(1397, 17, 'autorizar_liquidaciones', 'INACTIVO', '2025-04-02 14:35:59'),
(1398, 17, 'revisar_liquidaciones', 'ACTIVO', '2025-04-02 14:35:59'),
(1399, 17, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-04-02 14:35:59'),
(1400, 17, 'manage_reportes', 'ACTIVO', '2025-04-02 14:35:59'),
(1401, 17, 'manage_auditoria', 'ACTIVO', '2025-04-02 14:35:59'),
(1402, 17, 'manage_accesos', 'INACTIVO', '2025-04-02 14:35:59'),
(1403, 17, 'manage_facturas', 'ACTIVO', '2025-04-02 14:35:59'),
(1404, 17, 'autorizar_facturas', 'INACTIVO', '2025-04-02 14:35:59'),
(1405, 17, 'revisar_facturas', 'ACTIVO', '2025-04-02 14:35:59'),
(1406, 17, 'manage_centros_costos', 'ACTIVO', '2025-04-02 14:35:59'),
(1619, 18, 'create_liquidaciones', 'ACTIVO', '2025-04-04 20:15:40'),
(1620, 18, 'create_detalles', 'ACTIVO', '2025-04-04 20:15:40'),
(1621, 18, 'manage_cajachica', 'ACTIVO', '2025-04-04 20:15:40'),
(1622, 18, 'manage_impuestos', 'ACTIVO', '2025-04-04 20:15:40'),
(1623, 18, 'manage_cuentas_contables', 'ACTIVO', '2025-04-04 20:15:40'),
(1624, 18, 'manage_tipos_gastos', 'ACTIVO', '2025-04-04 20:15:40'),
(1625, 18, 'manage_roles', 'ACTIVO', '2025-04-04 20:15:40'),
(1626, 18, 'manage_usuarios', 'ACTIVO', '2025-04-04 20:15:40'),
(1627, 18, 'autorizar_liquidaciones', 'ACTIVO', '2025-04-04 20:15:40'),
(1628, 18, 'revisar_liquidaciones', 'ACTIVO', '2025-04-04 20:15:40'),
(1629, 18, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-04-04 20:15:40'),
(1630, 18, 'manage_reportes', 'ACTIVO', '2025-04-04 20:15:40'),
(1631, 18, 'manage_auditoria', 'ACTIVO', '2025-04-04 20:15:40'),
(1632, 18, 'manage_accesos', 'ACTIVO', '2025-04-04 20:15:40'),
(1633, 18, 'manage_facturas', 'ACTIVO', '2025-04-04 20:15:40'),
(1634, 18, 'autorizar_facturas', 'ACTIVO', '2025-04-04 20:15:40'),
(1635, 18, 'revisar_facturas', 'ACTIVO', '2025-04-04 20:15:40'),
(1636, 18, 'manage_centros_costos', 'ACTIVO', '2025-04-04 20:15:40'),
(1658, 18, 'listar_bases', 'INACTIVO', '2025-04-04 20:21:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_documentos`
--

CREATE TABLE `tipos_documentos` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_documentos`
--

INSERT INTO `tipos_documentos` (`id`, `name`, `description`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'FACTURA', 'Documento de tipo factura', 'ACTIVO', '2025-04-03 18:33:36', '2025-04-03 18:33:36'),
(2, 'RECIBO', 'Documento de tipo recibo', 'ACTIVO', '2025-04-03 18:33:36', '2025-04-03 18:33:36'),
(3, 'COMPROBANTE', 'Documento de tipo comprobante', 'ACTIVO', '2025-04-03 18:33:36', '2025-04-03 18:33:36');

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
(1, 'Gasto Operativo', 'gasolina', 'ACTIVO', '2025-03-21 20:18:54', '2025-03-23 18:21:45'),
(2, 'Combustible', 'Combustible', 'ACTIVO', '2025-04-03 15:57:57', '2025-04-03 15:57:57'),
(3, 'Hospedaje', 'Hospedaje', 'ACTIVO', '2025-04-03 15:58:29', '2025-04-03 15:58:29'),
(4, 'Alimentos', 'Alimentos', 'ACTIVO', '2025-04-03 15:58:40', '2025-04-03 15:58:40'),
(5, 'otros...', 'otros...', 'ACTIVO', '2025-04-03 15:58:57', '2025-04-03 15:58:57');

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
(8, 'Angel De León', 'angel.deleon@agrocentro.com', '$2y$10$ww572tm5VQaYafnfdvtNp.x0VgO87zuwjquezWJn4iYE8Pfexadra', 18, '2025-03-11 14:20:20', '2025-04-02 15:14:33'),
(10, 'Omar ', 'omar@gmail.com', '$2y$10$RRI9rARJHg2bKODIK0WOMOYKqzxdSpngia8Ny7lphCPLWk1G8dC/e', 4, '2025-03-20 20:45:37', '2025-04-02 13:16:44'),
(14, 'Miguel', 'miguel@gmail.com', '$2y$10$MheThlhiC3PBD01vDzYH1egIa7qjusfNXMVsi5nkgLeKUihvrHTR6', 18, '2025-04-02 14:34:09', '2025-04-02 15:16:30'),
(15, 'Pepe', 'pepe@gmail.com', '$2y$10$N1S2vLvPqDk6/lXmgcoXoeGlWWx6XUbZZN5TWSs8QsG74Ok1w8ypG', 4, '2025-04-02 14:35:36', '2025-04-07 21:09:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_modulos`
--

CREATE TABLE `usuario_modulos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_modulo` varchar(50) NOT NULL,
  `estado` varchar(10) DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos_permisos`
--
ALTER TABLE `accesos_permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_modulo` (`id_modulo`);

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
  ADD KEY `fk_cajas_chicas_centro_costo` (`id_centro_costo`),
  ADD KEY `fk_cajas_chicas_usuario_encargado` (`id_usuario_encargado`),
  ADD KEY `fk_cajas_chicas_supervisor` (`id_supervisor`);

--
-- Indices de la tabla `centros_costos`
--
ALTER TABLE `centros_costos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_cuenta_base` (`base_id`);

--
-- Indices de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_permiso_nombre` (`nombre`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rol_permiso` (`id_rol`,`permiso`);

--
-- Indices de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
-- Indices de la tabla `usuario_modulos`
--
ALTER TABLE `usuario_modulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos_permisos`
--
ALTER TABLE `accesos_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4016;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=719;

--
-- AUTO_INCREMENT de la tabla `bases`
--
ALTER TABLE `bases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `centros_costos`
--
ALTER TABLE `centros_costos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1895;

--
-- AUTO_INCREMENT de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `usuario_modulos`
--
ALTER TABLE `usuario_modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos_permisos`
--
ALTER TABLE `accesos_permisos`
  ADD CONSTRAINT `accesos_permisos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accesos_permisos_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_accesos_permisos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_auditoria_liquidacion` FOREIGN KEY (`id_liquidacion`) REFERENCES `liquidaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  ADD CONSTRAINT `cajas_chicas_ibfk_1` FOREIGN KEY (`id_usuario_encargado`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `cajas_chicas_ibfk_2` FOREIGN KEY (`id_supervisor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_centro_costo` FOREIGN KEY (`id_centro_costo`) REFERENCES `cuentas_contables` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_supervisor` FOREIGN KEY (`id_supervisor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_usuario_encargado` FOREIGN KEY (`id_usuario_encargado`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `centros_costos`
--
ALTER TABLE `centros_costos`
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
  ADD CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`cuenta_id`) REFERENCES `centros_costos` (`id`),
  ADD CONSTRAINT `fk_facturas_base_id` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`),
  ADD CONSTRAINT `fk_facturas_cuenta_id` FOREIGN KEY (`cuenta_id`) REFERENCES `centros_costos` (`id`);

--
-- Filtros para la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  ADD CONSTRAINT `liquidaciones_ibfk_1` FOREIGN KEY (`id_caja_chica`) REFERENCES `cajas_chicas` (`id`);

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `usuario_modulos`
--
ALTER TABLE `usuario_modulos`
  ADD CONSTRAINT `usuario_modulos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
