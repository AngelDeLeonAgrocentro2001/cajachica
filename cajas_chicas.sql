-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-04-2025 a las 20:06:00
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
(4015, 15, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4016, 16, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-22 15:31:58'),
(4017, 16, NULL, 'create_detalles', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-22 15:31:58'),
(4018, 16, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-22 15:31:58'),
(4019, 16, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-22 15:31:58');

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
(1, 27, NULL, 1, 'APROBADO', NULL, '2025-04-08 16:43:39', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(2, 27, 54, 1, 'RECHAZADO', NULL, '2025-04-08 16:44:06', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(3, 27, 55, 1, 'RECHAZADO', NULL, '2025-04-08 16:44:29', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(4, 27, 56, 1, 'RECHAZADO', NULL, '2025-04-08 16:45:15', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(5, 27, 56, 1, 'RECHAZADO', NULL, '2025-04-08 16:45:22', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: Fact-003'),
(6, 27, NULL, 1, 'APROBADO', NULL, '2025-04-08 16:45:35', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(7, 27, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:46:28', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo '),
(8, 21, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:46:39', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(9, 21, NULL, 3, 'RECHAZADO', NULL, '2025-04-08 16:46:54', 'RECHAZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta imcompleto '),
(10, 23, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:47:06', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(11, 23, NULL, 3, 'RECHAZADO', NULL, '2025-04-08 16:47:17', 'DESCARTADO', 'Supervisor 1', 'Liquidación marcada para corrección'),
(12, 27, NULL, 4, 'APROBADO', NULL, '2025-04-08 16:48:23', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'Esta completado '),
(13, 23, NULL, 4, 'APROBADO', NULL, '2025-04-08 16:48:39', 'FINALIZADO', 'Contador 1', 'Liquidación finalizada por encargado'),
(14, 23, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:49:20', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta completo '),
(15, 23, NULL, 4, 'RECHAZADO', NULL, '2025-04-08 16:49:54', 'RECHAZADO_POR_CONTABILIDAD', 'Contador 1', 'no esta completo falta un dato '),
(16, 24, NULL, 4, 'APROBADO', NULL, '2025-04-08 16:50:36', 'FINALIZADO', 'Contador 1', 'Liquidación finalizada por encargado'),
(17, 25, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:51:32', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(18, 26, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:51:34', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(19, 20, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:51:37', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(20, 24, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:51:48', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'ok'),
(21, 25, NULL, 3, 'RECHAZADO', NULL, '2025-04-08 16:52:06', 'RECHAZADO_POR_SUPERVISOR', 'Supervisor 1', 'no completo '),
(22, 26, NULL, 3, 'RECHAZADO', NULL, '2025-04-08 16:52:23', 'DESCARTADO', 'Supervisor 1', 'Liquidación marcada para corrección'),
(23, 26, NULL, 3, 'APROBADO', NULL, '2025-04-08 16:53:10', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(24, 26, NULL, 3, 'RECHAZADO', NULL, '2025-04-08 16:53:25', 'DESCARTADO', 'Supervisor 1', 'Liquidación marcada para corrección'),
(25, 26, NULL, 1, 'APROBADO', NULL, '2025-04-08 18:09:04', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(26, 26, NULL, 3, 'APROBADO', NULL, '2025-04-08 18:09:40', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Autorizado'),
(27, 24, NULL, 4, 'APROBADO', NULL, '2025-04-08 20:20:17', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta completo'),
(28, 26, NULL, 4, 'RECHAZADO', NULL, '2025-04-08 20:20:31', 'RECHAZADO_POR_CONTABILIDAD', 'Contador 1', 'no esta completo '),
(29, 20, NULL, 3, 'APROBADO', NULL, '2025-04-08 20:59:31', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'completo'),
(30, 20, NULL, 4, 'APROBADO', NULL, '2025-04-08 21:00:07', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta completo '),
(31, 28, NULL, 4, 'APROBADO', NULL, '2025-04-08 21:18:04', 'CREADO', 'Contador 1', 'Liquidación creada por encargado'),
(32, 28, 57, 4, 'RECHAZADO', NULL, '2025-04-08 21:18:32', 'CREAR_DETALLE', 'Contador 1', 'Factura creada: FACT-004'),
(33, 28, NULL, 4, 'APROBADO', NULL, '2025-04-09 12:46:46', 'FINALIZADO', 'Contador 1', 'Liquidación finalizada por encargado'),
(34, 29, NULL, 1, 'APROBADO', NULL, '2025-04-09 15:00:19', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(35, 29, 58, 1, 'RECHAZADO', NULL, '2025-04-09 15:01:08', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(36, 29, 59, 1, 'RECHAZADO', NULL, '2025-04-09 17:47:34', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-003'),
(37, 29, 60, 1, 'RECHAZADO', NULL, '2025-04-09 17:48:31', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(38, 29, 61, 1, 'RECHAZADO', NULL, '2025-04-09 17:49:11', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-00432'),
(39, 29, 58, 1, 'RECHAZADO', NULL, '2025-04-09 17:49:24', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-004'),
(40, 29, NULL, 1, 'APROBADO', NULL, '2025-04-09 17:50:00', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(41, 28, NULL, 3, 'RECHAZADO', NULL, '2025-04-09 17:50:59', 'RECHAZADO_POR_SUPERVISOR', 'Supervisor 1', 'no esta completo'),
(42, 29, NULL, 3, 'APROBADO', NULL, '2025-04-09 17:56:40', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta completo '),
(43, 29, NULL, 4, 'APROBADO', NULL, '2025-04-09 17:57:37', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'todo esta completo '),
(44, 12, NULL, 4, 'APROBADO', NULL, '2025-04-09 18:45:56', 'FINALIZADO', 'Contador 1', 'Liquidación finalizada por encargado'),
(45, 12, NULL, 3, 'APROBADO', NULL, '2025-04-09 18:46:34', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo'),
(46, 19, NULL, 1, 'APROBADO', NULL, '2025-04-09 18:47:32', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(47, 19, NULL, 3, 'APROBADO', NULL, '2025-04-09 18:48:34', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta compelto'),
(48, 18, NULL, 3, 'RECHAZADO', NULL, '2025-04-09 18:49:02', 'RECHAZADO_POR_SUPERVISOR', 'Supervisor 1', 'no esta completo'),
(49, 17, NULL, 1, 'APROBADO', NULL, '2025-04-09 18:52:23', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(50, 17, NULL, 3, 'APROBADO', NULL, '2025-04-09 18:52:36', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta completo'),
(51, 16, NULL, 1, 'APROBADO', NULL, '2025-04-09 18:53:07', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(52, 16, NULL, 3, 'APROBADO', NULL, '2025-04-09 18:53:30', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta completo'),
(53, 11, NULL, 1, 'APROBADO', NULL, '2025-04-09 19:03:16', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(54, 11, NULL, 3, 'APROBADO', NULL, '2025-04-09 19:03:50', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta completo'),
(55, 11, NULL, 4, 'APROBADO', NULL, '2025-04-09 19:04:36', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta todo en orden'),
(56, 12, NULL, 1, 'APROBADO', NULL, '2025-04-09 20:28:13', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(57, 12, NULL, 3, 'APROBADO', NULL, '2025-04-09 20:29:10', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta todo en orden '),
(58, 12, NULL, 4, 'APROBADO', NULL, '2025-04-09 20:29:57', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta todo en orden por contador perez'),
(59, 30, NULL, 1, 'APROBADO', NULL, '2025-04-09 22:03:27', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(60, 30, 62, 1, 'RECHAZADO', NULL, '2025-04-10 15:04:35', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(61, 30, NULL, 1, 'APROBADO', NULL, '2025-04-10 15:09:11', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(62, 30, NULL, 3, 'APROBADO', NULL, '2025-04-10 15:24:49', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta todo en orden '),
(63, 30, NULL, 4, 'APROBADO', NULL, '2025-04-10 15:25:22', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta completo '),
(64, 30, NULL, 4, 'EXPORTADO_SAP', NULL, '2025-04-10 15:26:48', 'EXPORTADO', 'Contador 1', 'Liquidación exportada a SAP como liquidacion_30_20250410_172648.csv'),
(65, 31, NULL, 1, 'APROBADO', NULL, '2025-04-10 16:21:59', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(66, 31, 63, 1, 'RECHAZADO', NULL, '2025-04-10 16:23:02', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(67, 31, 64, 1, 'RECHAZADO', NULL, '2025-04-10 16:23:57', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(68, 31, 65, 1, 'RECHAZADO', NULL, '2025-04-10 16:24:43', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-0054'),
(69, 31, NULL, 1, 'APROBADO', NULL, '2025-04-10 16:25:13', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(70, 31, NULL, 3, 'APROBADO', NULL, '2025-04-10 16:26:12', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo '),
(71, 31, NULL, 4, 'APROBADO', NULL, '2025-04-10 16:26:57', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta todo en orden '),
(72, 19, 40, 3, 'RECHAZADO', NULL, '2025-04-10 16:29:38', 'ELIMINAR_DETALLE', 'Supervisor 1', 'Factura eliminada: FACT-00433'),
(73, 19, 39, 3, 'RECHAZADO', NULL, '2025-04-10 16:29:59', 'ELIMINAR_DETALLE', 'Supervisor 1', 'Factura eliminada: FACT-004'),
(74, 19, 66, 3, 'RECHAZADO', NULL, '2025-04-10 16:30:42', 'CREAR_DETALLE', 'Supervisor 1', 'Factura creada: FACT-004'),
(75, 19, NULL, 3, 'APROBADO', NULL, '2025-04-10 16:31:04', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(76, 19, NULL, 3, 'APROBADO', NULL, '2025-04-10 16:32:16', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta en orden'),
(77, 19, NULL, 4, 'APROBADO', NULL, '2025-04-10 16:32:54', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta bien '),
(78, 9, NULL, 1, 'APROBADO', NULL, '2025-04-10 16:47:07', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(79, 32, NULL, 1, 'APROBADO', NULL, '2025-04-10 16:48:34', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(80, 32, 67, 1, 'RECHAZADO', NULL, '2025-04-10 16:48:54', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(81, 32, 68, 1, 'RECHAZADO', NULL, '2025-04-10 16:49:16', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(94, 34, NULL, 1, 'APROBADO', NULL, '2025-04-10 17:20:34', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(95, 34, 74, 1, 'RECHAZADO', NULL, '2025-04-10 17:21:03', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(96, 34, NULL, 3, 'APROBADO', NULL, '2025-04-10 17:26:46', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(97, 35, NULL, 1, 'APROBADO', NULL, '2025-04-10 17:31:33', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(98, 35, 75, 1, 'RECHAZADO', NULL, '2025-04-10 17:31:58', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(99, 35, 76, 1, 'RECHAZADO', NULL, '2025-04-10 17:33:09', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-00433'),
(100, 35, NULL, 1, 'APROBADO', NULL, '2025-04-10 17:58:17', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(101, 32, NULL, 3, 'APROBADO', NULL, '2025-04-10 18:04:37', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta bien '),
(102, 36, NULL, 1, 'APROBADO', NULL, '2025-04-10 18:34:23', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(103, 36, 77, 1, 'RECHAZADO', NULL, '2025-04-10 18:34:50', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(104, 36, NULL, 1, 'APROBADO', NULL, '2025-04-10 18:35:01', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(105, 36, NULL, 4, 'RECHAZADO', NULL, '2025-04-10 18:36:45', 'DESCARTADO', 'Contador 1', 'Liquidación marcada para corrección'),
(106, 36, NULL, 4, 'APROBADO', NULL, '2025-04-10 18:37:05', 'FINALIZADO', 'Contador 1', 'Liquidación finalizada por encargado'),
(107, 36, NULL, 3, 'RECHAZADO', NULL, '2025-04-10 18:37:49', 'RECHAZADO_POR_SUPERVISOR', 'Supervisor 1', 'no cumple'),
(108, 37, NULL, 1, 'APROBADO', NULL, '2025-04-10 18:43:11', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(109, 37, 78, 1, 'RECHAZADO', NULL, '2025-04-10 18:43:31', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(110, 37, 79, 1, 'RECHAZADO', NULL, '2025-04-10 18:44:42', 'CREAR_DETALLE', 'Administrador', 'Factura creada: 1221'),
(111, 37, NULL, 1, 'APROBADO', NULL, '2025-04-10 18:44:50', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(112, 37, NULL, 3, 'APROBADO', NULL, '2025-04-10 18:45:21', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'sds'),
(113, 37, NULL, 4, 'APROBADO', NULL, '2025-04-10 18:46:19', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta en orden'),
(114, 38, NULL, 1, 'APROBADO', NULL, '2025-04-10 18:50:18', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(115, 38, 80, 1, 'RECHAZADO', NULL, '2025-04-10 18:50:41', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(116, 38, NULL, 1, 'APROBADO', NULL, '2025-04-10 18:50:53', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(117, 39, NULL, 1, 'APROBADO', NULL, '2025-04-10 18:58:58', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(118, 39, 81, 1, 'RECHAZADO', NULL, '2025-04-10 18:59:21', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(119, 39, 82, 1, 'RECHAZADO', NULL, '2025-04-10 18:59:56', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-0043'),
(120, 39, NULL, 1, 'APROBADO', NULL, '2025-04-10 19:00:08', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(121, 40, NULL, 1, 'APROBADO', NULL, '2025-04-10 21:24:54', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(122, 40, 83, 1, 'RECHAZADO', NULL, '2025-04-10 21:25:38', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(123, 40, 84, 1, 'RECHAZADO', NULL, '2025-04-10 21:26:38', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-0042'),
(124, 40, 85, 1, 'RECHAZADO', NULL, '2025-04-10 21:27:17', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(125, 40, NULL, 1, 'APROBADO', NULL, '2025-04-10 21:27:32', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(126, 41, NULL, 1, 'APROBADO', NULL, '2025-04-10 21:39:38', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(127, 41, 86, 1, 'RECHAZADO', NULL, '2025-04-10 21:40:06', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(128, 41, NULL, 1, 'APROBADO', NULL, '2025-04-10 21:40:13', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(129, 41, NULL, 3, 'APROBADO', NULL, '2025-04-10 21:40:50', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta completo'),
(130, 41, NULL, 4, 'APROBADO', NULL, '2025-04-10 21:41:36', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'todo en orden'),
(131, 42, NULL, 1, 'APROBADO', NULL, '2025-04-10 21:44:15', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(132, 42, 87, 1, 'RECHAZADO', NULL, '2025-04-10 21:45:05', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(133, 42, 88, 1, 'RECHAZADO', NULL, '2025-04-10 21:45:40', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(134, 42, 89, 1, 'RECHAZADO', NULL, '2025-04-10 21:46:26', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(135, 42, NULL, 1, 'APROBADO', NULL, '2025-04-10 21:46:36', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(136, 42, NULL, 3, 'APROBADO', NULL, '2025-04-10 21:47:49', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta completo'),
(137, 42, NULL, 4, 'APROBADO', NULL, '2025-04-10 21:48:17', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'completo '),
(138, 43, NULL, 1, 'APROBADO', NULL, '2025-04-10 21:49:58', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(139, 43, 90, 1, 'RECHAZADO', NULL, '2025-04-10 21:50:24', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(140, 43, 91, 1, 'RECHAZADO', NULL, '2025-04-10 21:51:17', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-0063'),
(141, 43, 92, 1, 'RECHAZADO', NULL, '2025-04-10 21:51:55', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(142, 44, NULL, 1, 'APROBADO', NULL, '2025-04-10 21:59:11', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(143, 44, 93, 1, 'RECHAZADO', NULL, '2025-04-10 21:59:56', 'CREAR_DETALLE', 'Administrador', 'Factura creada: 3323'),
(144, 44, 94, 1, 'RECHAZADO', NULL, '2025-04-10 22:00:28', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(145, 44, 95, 1, 'RECHAZADO', NULL, '2025-04-10 22:01:06', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(146, 44, NULL, 1, 'APROBADO', NULL, '2025-04-10 22:18:46', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(147, 44, NULL, 3, 'APROBADO', NULL, '2025-04-10 22:19:15', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien'),
(148, 44, NULL, 4, 'APROBADO', NULL, '2025-04-10 22:19:43', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'bien'),
(149, 45, NULL, 1, 'APROBADO', NULL, '2025-04-10 22:20:03', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(150, 45, 96, 1, 'RECHAZADO', NULL, '2025-04-10 22:20:30', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(151, 45, 97, 1, 'RECHAZADO', NULL, '2025-04-10 22:21:05', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(152, 45, 98, 1, 'RECHAZADO', NULL, '2025-04-10 22:21:50', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004rww'),
(153, 46, NULL, 1, 'APROBADO', NULL, '2025-04-10 22:22:54', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(154, 46, 99, 1, 'RECHAZADO', NULL, '2025-04-10 22:23:21', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(155, 46, 100, 1, 'RECHAZADO', NULL, '2025-04-10 22:23:53', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(156, 46, 101, 1, 'RECHAZADO', NULL, '2025-04-10 22:24:38', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004ewwe'),
(157, 46, NULL, 1, 'APROBADO', NULL, '2025-04-10 22:24:43', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(158, 45, 97, 1, 'RECHAZADO', NULL, '2025-04-11 14:30:29', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-006'),
(159, 47, NULL, 1, 'APROBADO', NULL, '2025-04-11 14:32:52', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(160, 47, 102, 1, 'RECHAZADO', NULL, '2025-04-11 14:33:34', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(161, 47, 103, 1, 'RECHAZADO', NULL, '2025-04-11 14:34:11', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(162, 47, 104, 1, 'RECHAZADO', NULL, '2025-04-11 14:35:00', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(163, 48, NULL, 1, 'APROBADO', NULL, '2025-04-11 14:39:09', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(164, 48, 105, 1, 'RECHAZADO', NULL, '2025-04-11 14:39:54', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(165, 48, 106, 1, 'RECHAZADO', NULL, '2025-04-11 14:40:41', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(166, 48, 107, 1, 'RECHAZADO', NULL, '2025-04-11 14:41:22', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(167, 47, NULL, 1, 'APROBADO', NULL, '2025-04-11 16:02:22', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(168, 48, NULL, 1, 'APROBADO', NULL, '2025-04-11 16:02:25', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(169, 49, NULL, 1, 'APROBADO', NULL, '2025-04-11 16:02:39', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(170, 50, NULL, 1, 'APROBADO', NULL, '2025-04-11 16:03:02', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(171, 50, 108, 1, 'RECHAZADO', NULL, '2025-04-11 16:04:07', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(172, 50, 109, 1, 'RECHAZADO', NULL, '2025-04-11 16:04:53', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(173, 51, NULL, 1, 'APROBADO', NULL, '2025-04-11 16:21:14', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(174, 51, 110, 1, 'RECHAZADO', NULL, '2025-04-11 16:21:46', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-001'),
(175, 52, NULL, 1, 'APROBADO', NULL, '2025-04-11 16:23:54', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(176, 52, 111, 1, 'RECHAZADO', NULL, '2025-04-11 16:24:21', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(177, 52, 112, 1, 'RECHAZADO', NULL, '2025-04-11 16:24:42', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(178, 52, 113, 1, 'RECHAZADO', NULL, '2025-04-11 16:25:19', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(179, 52, 112, 1, 'RECHAZADO', NULL, '2025-04-11 16:25:38', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: Fact-003'),
(180, 52, 113, 1, 'RECHAZADO', NULL, '2025-04-11 17:06:38', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-006'),
(181, 52, 112, 1, 'RECHAZADO', NULL, '2025-04-11 17:07:17', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada:                 Fact-003              '),
(182, 52, NULL, 1, 'APROBADO', NULL, '2025-04-11 17:25:02', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(183, 50, NULL, 1, 'APROBADO', NULL, '2025-04-11 17:25:42', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(184, 51, NULL, 1, 'APROBADO', NULL, '2025-04-11 17:25:46', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(185, 45, NULL, 1, 'APROBADO', NULL, '2025-04-11 17:25:59', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(186, 53, NULL, 1, 'APROBADO', NULL, '2025-04-11 17:26:12', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(187, 53, 114, 1, 'RECHAZADO', NULL, '2025-04-11 17:27:14', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003'),
(188, 53, 114, 1, 'RECHAZADO', NULL, '2025-04-11 17:28:25', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada:                 Fact-003              '),
(189, 53, 115, 1, 'RECHAZADO', NULL, '2025-04-11 17:32:11', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0033'),
(190, 53, 116, 1, 'RECHAZADO', NULL, '2025-04-11 17:45:01', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(191, 53, NULL, 1, 'APROBADO', NULL, '2025-04-11 17:59:41', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(197, 43, 92, 1, 'RECHAZADO', NULL, '2025-04-12 06:03:38', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: FACT-004'),
(198, 43, 92, 1, 'RECHAZADO', NULL, '2025-04-12 06:04:04', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: FACT-004'),
(199, 43, 92, 1, 'RECHAZADO', NULL, '2025-04-12 06:04:10', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-004'),
(200, 43, NULL, 1, 'APROBADO', NULL, '2025-04-12 06:04:38', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(201, 55, NULL, 1, 'APROBADO', NULL, '2025-04-12 06:06:45', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(202, 55, 118, 1, 'RECHAZADO', NULL, '2025-04-12 06:07:28', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(203, 55, 119, 1, 'RECHAZADO', NULL, '2025-04-12 06:09:06', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(204, 55, 120, 1, 'RECHAZADO', NULL, '2025-04-12 06:09:56', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(205, 56, NULL, 1, 'APROBADO', NULL, '2025-04-12 14:55:37', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(206, 56, 123, 1, 'RECHAZADO', NULL, '2025-04-12 14:56:14', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(207, 56, 124, 1, 'RECHAZADO', NULL, '2025-04-12 14:56:58', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-002'),
(208, 56, 125, 1, 'RECHAZADO', NULL, '2025-04-12 14:57:42', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(209, 57, NULL, 1, 'APROBADO', NULL, '2025-04-13 05:44:40', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(210, 57, 126, 1, 'RECHAZADO', NULL, '2025-04-13 05:45:17', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(211, 57, 127, 1, 'RECHAZADO', NULL, '2025-04-13 05:46:11', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(212, 57, 127, 1, 'RECHAZADO', NULL, '2025-04-13 05:46:27', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: Fact-0031'),
(213, 57, 128, 1, 'RECHAZADO', NULL, '2025-04-13 05:48:00', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-003122'),
(214, 57, NULL, 1, 'APROBADO', NULL, '2025-04-13 05:50:42', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(215, 58, NULL, 1, 'APROBADO', NULL, '2025-04-14 15:09:08', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(216, 58, 129, 1, 'RECHAZADO', NULL, '2025-04-14 15:09:38', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(217, 58, 130, 1, 'RECHAZADO', NULL, '2025-04-14 15:10:15', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(218, 58, 131, 1, 'RECHAZADO', NULL, '2025-04-14 15:11:03', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(219, 58, 132, 1, 'RECHAZADO', NULL, '2025-04-14 15:11:46', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-00313'),
(220, 58, NULL, 1, 'APROBADO', NULL, '2025-04-14 15:11:57', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(221, 58, NULL, 3, 'APROBADO', NULL, '2025-04-14 15:13:22', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta todo en orden'),
(222, 58, NULL, 4, 'APROBADO', NULL, '2025-04-14 15:14:02', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta completo '),
(223, 59, NULL, 1, 'APROBADO', NULL, '2025-04-14 15:19:58', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(224, 59, 133, 1, 'RECHAZADO', NULL, '2025-04-14 15:20:22', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(225, 59, NULL, 1, 'APROBADO', NULL, '2025-04-14 15:20:29', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(226, 60, NULL, 1, 'APROBADO', NULL, '2025-04-14 15:22:33', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(227, 60, 134, 1, 'RECHAZADO', NULL, '2025-04-14 15:23:11', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(228, 60, 135, 1, 'RECHAZADO', NULL, '2025-04-14 15:24:03', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(229, 60, 136, 1, 'RECHAZADO', NULL, '2025-04-14 15:24:55', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031343'),
(230, 60, NULL, 1, 'APROBADO', NULL, '2025-04-14 15:25:53', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(231, 60, NULL, 3, 'APROBADO', NULL, '2025-04-14 15:26:45', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo'),
(232, 60, NULL, 4, 'APROBADO', NULL, '2025-04-14 15:27:23', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'todo esta completo'),
(233, 61, NULL, 1, 'APROBADO', NULL, '2025-04-14 15:33:19', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(234, 61, 137, 1, 'RECHAZADO', NULL, '2025-04-14 15:34:09', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(235, 61, NULL, 1, 'APROBADO', NULL, '2025-04-14 15:34:43', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(236, 61, NULL, 3, 'APROBADO', NULL, '2025-04-14 15:35:38', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo'),
(237, 61, NULL, 4, 'APROBADO', NULL, '2025-04-14 15:36:13', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta en orden '),
(238, 62, NULL, 1, 'APROBADO', NULL, '2025-04-14 21:01:37', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(239, 62, 138, 1, 'RECHAZADO', NULL, '2025-04-14 21:09:05', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(240, 62, NULL, 1, 'APROBADO', NULL, '2025-04-14 21:09:53', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(241, 62, NULL, 3, 'APROBADO', NULL, '2025-04-14 21:15:09', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo'),
(242, 63, NULL, 1, 'APROBADO', NULL, '2025-04-14 21:16:06', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(243, 63, 139, 1, 'RECHAZADO', NULL, '2025-04-14 21:16:33', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(244, 63, 140, 1, 'RECHAZADO', NULL, '2025-04-14 21:16:54', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004434'),
(245, 63, NULL, 1, 'APROBADO', NULL, '2025-04-14 21:17:01', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(246, 63, NULL, 3, 'APROBADO', NULL, '2025-04-14 21:17:56', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'Esta completo'),
(247, 64, NULL, 1, 'APROBADO', NULL, '2025-04-15 14:14:36', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(248, 64, 141, 1, 'RECHAZADO', NULL, '2025-04-15 14:17:18', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(249, 64, 142, 1, 'RECHAZADO', NULL, '2025-04-15 14:17:58', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006322'),
(250, 64, NULL, 1, 'APROBADO', NULL, '2025-04-15 14:18:20', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(251, 65, NULL, 1, 'APROBADO', NULL, '2025-04-15 15:49:08', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(252, 65, 143, 1, 'RECHAZADO', NULL, '2025-04-21 14:35:45', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(253, 65, 144, 1, 'RECHAZADO', NULL, '2025-04-21 15:01:35', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(254, 65, 145, 1, 'RECHAZADO', NULL, '2025-04-21 15:08:48', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-00433'),
(255, 65, 146, 1, 'RECHAZADO', NULL, '2025-04-21 15:09:59', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031'),
(256, 65, 147, 1, 'RECHAZADO', NULL, '2025-04-21 15:12:01', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004333'),
(257, 65, 143, 1, 'RECHAZADO', NULL, '2025-04-21 15:12:17', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: FACT-006'),
(258, 65, 143, 1, 'RECHAZADO', NULL, '2025-04-21 16:09:47', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-006'),
(259, 65, 144, 1, 'RECHAZADO', NULL, '2025-04-21 16:09:53', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-004'),
(260, 65, 145, 1, 'RECHAZADO', NULL, '2025-04-21 16:09:58', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: FACT-00433'),
(261, 65, 148, 1, 'RECHAZADO', NULL, '2025-04-21 16:11:53', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004311'),
(262, 65, 149, 1, 'RECHAZADO', NULL, '2025-04-21 16:20:55', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(263, 65, 149, 1, 'RECHAZADO', NULL, '2025-04-21 16:21:31', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: FACT-004'),
(264, 65, 149, 1, 'RECHAZADO', NULL, '2025-04-21 16:22:43', 'ACTUALIZAR_DETALLE', 'Administrador', 'Factura actualizada: FACT-004'),
(265, 65, 146, 1, 'RECHAZADO', NULL, '2025-04-21 16:31:20', 'ELIMINAR_DETALLE', 'Administrador', 'Factura eliminada: Fact-0031'),
(266, 65, 150, 1, 'RECHAZADO', NULL, '2025-04-21 16:33:54', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-001'),
(267, 65, NULL, 1, 'APROBADO', NULL, '2025-04-21 16:40:12', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(268, 66, NULL, 1, 'APROBADO', NULL, '2025-04-21 16:44:11', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(269, 66, 151, 1, 'RECHAZADO', NULL, '2025-04-21 16:45:20', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(270, 66, 152, 1, 'RECHAZADO', NULL, '2025-04-21 16:46:02', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(271, 65, NULL, 3, 'APROBADO', NULL, '2025-04-21 16:59:28', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'esta en orden '),
(272, 65, NULL, 4, 'APROBADO', NULL, '2025-04-21 17:00:19', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta en orden '),
(273, 65, NULL, 4, 'EXPORTADO_SAP', NULL, '2025-04-21 17:00:26', 'EXPORTADO', 'Contador 1', 'Liquidación exportada a SAP como liquidacion_65_20250421_190026.csv'),
(274, 64, 141, 3, 'RECHAZADO', NULL, '2025-04-21 17:27:45', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no esta completo '),
(275, 64, 142, 3, 'RECHAZADO', NULL, '2025-04-21 17:27:45', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no esta completo '),
(276, 64, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 17:27:45', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(277, 64, NULL, 1, 'APROBADO', NULL, '2025-04-21 17:29:16', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(278, 64, 141, 3, 'RECHAZADO', NULL, '2025-04-21 17:30:04', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no esta completo '),
(279, 64, 142, 3, 'RECHAZADO', NULL, '2025-04-21 17:30:04', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no esta completo '),
(280, 64, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 17:30:04', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(281, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 17:39:08', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(282, 67, 153, 1, 'RECHAZADO', NULL, '2025-04-21 17:40:00', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(283, 67, 154, 1, 'RECHAZADO', NULL, '2025-04-21 17:41:11', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(284, 67, 155, 1, 'RECHAZADO', NULL, '2025-04-21 17:41:48', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(285, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 17:42:05', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(286, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 17:42:49', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay '),
(287, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 17:42:49', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay '),
(288, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 17:42:49', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay '),
(289, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 17:42:49', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(290, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 17:43:17', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(291, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 17:43:48', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no completo'),
(292, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 17:43:48', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no completo'),
(293, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 17:43:48', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no completo'),
(294, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 17:43:48', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(295, 67, NULL, 3, 'APROBADO', NULL, '2025-04-21 17:44:39', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(296, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 17:44:51', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(297, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 17:44:51', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(298, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 17:44:51', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(299, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 17:44:51', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(300, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 18:01:53', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(301, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 18:02:19', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(302, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 18:02:19', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(303, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 18:02:19', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(304, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 18:02:19', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(305, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 18:07:58', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(306, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 18:08:23', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(307, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 18:08:23', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(308, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 18:08:23', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(309, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 18:08:23', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(310, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 18:21:25', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(311, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 18:21:39', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(312, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 18:21:39', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(313, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 18:21:39', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(314, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 18:21:39', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(315, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 18:22:19', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(316, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 18:22:43', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay cambios'),
(317, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 18:22:43', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay cambios'),
(318, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 18:22:43', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay cambios'),
(319, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 18:22:43', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(320, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 18:33:18', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(321, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 18:33:47', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(322, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 18:33:47', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(323, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 18:33:47', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(324, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 18:33:47', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(325, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 18:34:28', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(326, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 18:44:08', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(327, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 18:44:08', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(328, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 18:44:08', 'ENVIADO_A_CORRECCION', 'Supervisor 1', ''),
(329, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 18:44:08', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(330, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 18:44:20', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(331, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 18:44:51', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay cambios'),
(332, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 18:44:51', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay cambios'),
(333, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 18:44:51', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no hay cambios'),
(334, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 18:44:51', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Liquidación enviada a corrección'),
(335, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 18:45:16', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(336, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 20:38:14', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no esta completo'),
(337, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 20:38:14', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no esta completo'),
(338, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 20:38:14', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'no esta completo'),
(339, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 20:38:14', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Todos los detalles fueron enviados a corrección'),
(340, 67, NULL, 3, 'APROBADO', NULL, '2025-04-21 20:39:10', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(341, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 20:39:31', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'No seleccionado - enviado a corrección'),
(342, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 20:39:31', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'No seleccionado - enviado a corrección'),
(343, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 20:39:31', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'No seleccionado - enviado a corrección'),
(344, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 20:39:31', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Todos los detalles fueron enviados a corrección'),
(345, 67, NULL, 3, 'APROBADO', NULL, '2025-04-21 20:39:52', 'FINALIZADO', 'Supervisor 1', 'Liquidación finalizada por encargado'),
(346, 67, 153, 3, 'RECHAZADO', NULL, '2025-04-21 20:40:17', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'No seleccionado - enviado a corrección'),
(347, 67, 154, 3, 'RECHAZADO', NULL, '2025-04-21 20:40:17', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'No seleccionado - enviado a corrección'),
(348, 67, 155, 3, 'RECHAZADO', NULL, '2025-04-21 20:40:17', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'No seleccionado - enviado a corrección'),
(349, 67, NULL, 3, 'RECHAZADO', NULL, '2025-04-21 20:40:17', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Todos los detalles fueron enviados a corrección'),
(350, 67, NULL, 1, 'APROBADO', NULL, '2025-04-21 20:42:19', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(351, 67, NULL, 3, 'APROBADO', NULL, '2025-04-21 20:42:37', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien'),
(352, 67, NULL, 4, 'APROBADO', NULL, '2025-04-21 20:46:26', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'esta completo'),
(353, 68, NULL, 1, 'APROBADO', NULL, '2025-04-21 20:47:01', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(354, 68, 156, 1, 'RECHAZADO', NULL, '2025-04-21 20:47:52', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(355, 68, 157, 1, 'RECHAZADO', NULL, '2025-04-21 20:49:19', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-005'),
(356, 68, NULL, 1, 'APROBADO', NULL, '2025-04-21 20:49:56', 'FINALIZADO', 'Administrador', 'Liquidación finalizada por encargado'),
(357, 69, NULL, 1, 'APROBADO', NULL, '2025-04-21 20:53:49', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(358, 69, 158, 1, 'RECHAZADO', NULL, '2025-04-21 21:16:14', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(359, 70, NULL, 1, 'APROBADO', NULL, '2025-04-22 15:14:58', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(360, NULL, NULL, 16, 'RECHAZADO', NULL, '2025-04-22 15:31:58', 'ASIGNAR_PERMISOS', 'Encargado 2', 'Permisos asignados a usuario encargado2@example.com desde rol ID 2: create_liquidaciones, create_detalles, manage_facturas, manage_cajachica'),
(361, NULL, NULL, 1, 'APROBADO', NULL, '2025-04-22 15:31:58', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: encargado2@example.com'),
(362, 70, 159, 1, 'RECHAZADO', NULL, '2025-04-22 15:41:39', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(365, 72, NULL, 1, 'APROBADO', NULL, '2025-04-22 16:07:34', 'CREADO', 'Administrador', 'Liquidación creada por encargado'),
(366, 72, 160, 1, 'RECHAZADO', NULL, '2025-04-22 17:37:39', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-004'),
(367, 72, 161, 1, 'RECHAZADO', NULL, '2025-04-22 17:38:48', 'CREAR_DETALLE', 'Administrador', 'Factura creada: FACT-006'),
(368, 72, 162, 1, 'RECHAZADO', NULL, '2025-04-22 17:39:38', 'CREAR_DETALLE', 'Administrador', 'Factura creada: Fact-0031');

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
(2, 'Caja chica 2', 0.00, 0.00, 6000.00, 6000.00, 2, 3, 2, 'ACTIVA', '2025-03-21 21:30:21', '2025-04-22 15:35:02'),
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
(3, '100', 'Angel', 'ACTIVO', '2025-03-21 21:44:22', '2', 1);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_centro_costo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `cuentas_contables`
--

INSERT INTO `cuentas_contables` (`id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`, `id_centro_costo`) VALUES
(1, 'Centro de Costos 1', 'Centro de costos para pruebas', 'ACTIVO', '2025-03-21 18:56:42', '2025-03-21 18:56:42', NULL),
(2, 'Centro de Costos 2', 'Costo de viajes ', 'ACTIVO', '2025-03-23 18:24:56', '2025-03-23 18:24:56', NULL),
(3, 'Centro de Costos 3', 'prueba ', 'ACTIVO', '2025-03-25 20:37:32', '2025-04-21 20:52:35', NULL);

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
  `estado` enum('EN_PROCESO','PENDIENTE_AUTORIZACION','PENDIENTE_REVISION_CONTABILIDAD','RECHAZADO_AUTORIZACION','RECHAZADO_POR_CONTABILIDAD','FINALIZADO','DESCARTADO','ENVIADO_A_CORRECCION') NOT NULL DEFAULT 'EN_PROCESO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rutas_archivos` text DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `serie` varchar(50) DEFAULT NULL,
  `dpi` varchar(50) DEFAULT NULL,
  `id_centro_costo` int(11) DEFAULT NULL,
  `id_cuenta_contable` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `detalle_liquidaciones`
--

INSERT INTO `detalle_liquidaciones` (`id`, `id_liquidacion`, `no_factura`, `regimen`, `c_costo`, `nit_proveedor`, `tipo_documento`, `nombre_proveedor`, `fecha`, `bien_servicio`, `t_gasto`, `codigo_ccta`, `descripcion_factura`, `p_unitario`, `iva`, `total_factura`, `idp`, `inguat`, `porcentajeiva`, `porcentajeidp`, `tipo_combustible`, `estado`, `created_at`, `updated_at`, `rutas_archivos`, `cantidad`, `serie`, `dpi`, `id_centro_costo`, `id_cuenta_contable`) VALUES
(1, 1, 'FACT-001', NULL, NULL, '123456-7', 'FACTURA', 'Proveedor Prueba', '2025-03-21', 'Materiales de Oficina', 'OPERATIVO', NULL, 'Compra de materiales', 800.00, 96.00, 896.00, NULL, NULL, 12.00, NULL, NULL, '', '2025-03-21 20:06:59', '2025-03-21 20:06:59', '[]', NULL, NULL, NULL, NULL, NULL),
(2, 1, 'Fact-002', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 2', '2025-03-20', 'servicio', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-21 20:19:50', '2025-03-23 18:22:15', '[\"uploads\\/67ddc9e5f41d6_Captura.PNG\",\"uploads\\/67dddac1d92b3_adaptar.JPG\",\"uploads\\/67dddac1d98af_Captura.PNG\"]', NULL, NULL, NULL, NULL, NULL),
(3, 3, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Miguel perez', '2025-03-23', 'servicio', 'Gasto Operativo', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, 'RECHAZADO_POR_CONTABILIDAD', '2025-03-23 20:09:38', '2025-03-23 20:17:10', '[\"uploads\\/67e06a8250300_Captura.PNG\"]', NULL, NULL, NULL, NULL, NULL),
(4, 5, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-03-27', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 333.00, NULL, 443.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-28 17:22:53', '2025-03-28 17:22:53', '[]', NULL, NULL, NULL, NULL, NULL),
(5, 5, 'Fact-002', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-03-21', 'servicio', 'gasolina', NULL, NULL, 323.00, NULL, 232.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-28 17:23:41', '2025-03-28 17:23:41', '[]', NULL, NULL, NULL, NULL, NULL),
(6, 5, 'FACT-005', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-03-27', 'servicio', 'gasolina', NULL, NULL, 300.00, NULL, 300.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-28 20:40:43', '2025-03-28 20:40:43', '[]', NULL, NULL, NULL, NULL, NULL),
(7, 5, 'FACT-005', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-03-29', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-28 20:43:08', '2025-03-28 20:43:08', '[]', NULL, NULL, NULL, NULL, NULL),
(8, 6, 'FACT-006', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-03-31', 'bien', 'gasolina', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-31 21:24:56', '2025-03-31 21:24:56', '[]', NULL, NULL, NULL, NULL, NULL),
(9, 6, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'sara', '2025-03-30', 'servicio', 'gas', NULL, NULL, 2000.00, NULL, 2000.00, NULL, NULL, NULL, NULL, NULL, '', '2025-03-31 21:25:19', '2025-03-31 21:25:19', '[]', NULL, NULL, NULL, NULL, NULL),
(12, 9, 'Fact-002', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-02 17:38:32', '2025-04-02 17:38:32', '[]', NULL, NULL, NULL, NULL, NULL),
(13, 9, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 5', '2025-04-01', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-02 17:39:03', '2025-04-02 17:39:03', '[]', NULL, NULL, NULL, NULL, NULL),
(15, 9, 'FACT-005', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-04-01', 'Servicio Prueba 4', 'gas', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-02 18:16:26', '2025-04-02 18:16:26', '[]', NULL, NULL, NULL, NULL, NULL),
(16, 9, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', 'Servicio Prueba 5', 'gasolina', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-02 18:16:57', '2025-04-02 18:16:57', '[]', NULL, NULL, NULL, NULL, NULL),
(17, 9, 'Fact-0031', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-03-31', 'servicio', 'gasolina', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-02 20:43:54', '2025-04-02 20:43:54', '[]', NULL, NULL, NULL, NULL, NULL),
(18, 9, 'FACT-006', NULL, NULL, NULL, 'FACTURA', 'Miguel', '2025-04-01', 'servicio', 'gasolina', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-03 14:16:38', '2025-04-03 14:16:38', '[]', NULL, NULL, NULL, NULL, NULL),
(19, 9, 'FACT-004', NULL, NULL, NULL, 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', 'Servicio Prueba 4', 'gasolina', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-03 15:35:09', '2025-04-03 15:35:09', '[]', NULL, NULL, NULL, NULL, NULL),
(20, 9, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', 'servicio', 'COMBUSTIBLE', NULL, NULL, 333.00, NULL, 333.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-03 17:39:03', '2025-04-03 17:39:03', '[]', NULL, NULL, NULL, NULL, NULL),
(22, 11, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel', '2025-04-02', 'servicio', 'Combustible', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-03 18:44:48', '2025-04-03 18:44:48', '[]', NULL, NULL, NULL, NULL, NULL),
(23, 11, 'FACT-005', NULL, NULL, '23232323444', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 4', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-03 20:45:44', '2025-04-03 20:45:44', '[]', NULL, NULL, NULL, NULL, NULL),
(24, 11, 'FACT-006', NULL, NULL, '55565656', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 6', 'Combustible', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-03 20:59:32', '2025-04-03 20:59:32', '[]', NULL, NULL, NULL, NULL, NULL),
(25, 2, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-03-10', 'Servicio Prueba 5', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 13:56:34', '2025-04-04 13:56:34', '[]', NULL, NULL, NULL, NULL, NULL),
(26, 2, 'FACT-006', NULL, NULL, '232323233', 'RECIBO', 'Miguel', '2025-03-10', 'servicio', 'Alimentos', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 13:57:05', '2025-04-04 13:57:05', '[]', NULL, NULL, NULL, NULL, NULL),
(27, 12, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel', '2025-04-02', 'servicio', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 15:23:56', '2025-04-04 15:23:56', '[]', NULL, NULL, NULL, NULL, NULL),
(28, 12, 'FACT-006', NULL, NULL, '232323232232', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 4', 'Gasto Operativo', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 15:24:21', '2025-04-04 15:24:21', '[]', NULL, NULL, NULL, NULL, NULL),
(29, 12, 'FACT-005', NULL, NULL, '232323231', 'FACTURA', 'Miguel', '2025-04-01', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 15:46:23', '2025-04-04 15:46:23', '[]', NULL, NULL, NULL, NULL, NULL),
(30, 12, 'FACT-00433', NULL, NULL, '232323232223', 'FACTURA', 'Miguel', '2025-04-02', 'servicio', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 15:51:18', '2025-04-04 15:51:18', '[]', NULL, NULL, NULL, NULL, NULL),
(33, 15, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel', '2025-04-03', 'Servicio Prueba 5', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 16:46:51', '2025-04-04 16:46:51', '[]', NULL, NULL, NULL, NULL, NULL),
(34, 16, 'FACT-004', NULL, NULL, '34343', 'FACTURA', 'Miguel', '2025-04-03', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 17:00:54', '2025-04-04 17:00:54', '[]', NULL, NULL, NULL, NULL, NULL),
(35, 16, 'FACT-006', NULL, NULL, '100', 'RECIBO', 'Proveedor Prueba 4', '2025-04-02', 'servicio', 'Gasto Operativo', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 17:01:17', '2025-04-04 17:01:17', '[]', NULL, NULL, NULL, NULL, NULL),
(36, 17, 'FACT-006', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 4', 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 17:29:41', '2025-04-04 17:29:41', '[]', NULL, NULL, NULL, NULL, NULL),
(37, 18, 'FACT-004', NULL, NULL, '232323232223', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'servicio', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 18:36:55', '2025-04-04 18:36:55', '[]', NULL, NULL, NULL, NULL, NULL),
(38, 18, 'Fact-003', NULL, NULL, '23232323222334242', 'RECIBO', 'Proveedor Prueba 4', '2025-04-02', 'Servicio Prueba 4', 'Alimentos', NULL, NULL, 300.00, NULL, 300.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 18:37:27', '2025-04-04 18:37:27', '[]', NULL, NULL, NULL, NULL, NULL),
(41, 20, 'FACT-004', NULL, NULL, '232323232223', 'FACTURA', 'Proveedor Prueba 4', '2025-04-02', 'servicio', 'Alimentos', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 21:04:48', '2025-04-04 21:04:48', '[]', NULL, NULL, NULL, NULL, NULL),
(42, 20, 'FACT-00433', NULL, NULL, '2323232322235335', 'RECIBO', 'Miguel', '2025-04-02', 'servicio', 'Hospedaje', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-04 21:05:41', '2025-04-04 21:05:41', '[]', NULL, NULL, NULL, NULL, NULL),
(43, 21, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-04-06', 'servicio', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-07 18:43:45', '2025-04-07 18:44:07', '[]', NULL, NULL, NULL, NULL, NULL),
(44, 21, 'FACT-0040', NULL, NULL, '2323232340', 'RECIBO', 'Proveedor Prueba 4', '2025-04-07', 'Servicio Prueba 4', 'Alimentos', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-07 18:45:00', '2025-04-07 18:45:00', '[]', NULL, NULL, NULL, NULL, NULL),
(45, 21, 'Fact-0031', NULL, NULL, '232323232223', 'COMPROBANTE', 'Proveedor Prueba 4', '2025-04-06', 'Servicio Prueba 4', 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-07 18:45:36', '2025-04-07 18:45:36', '[]', NULL, NULL, NULL, NULL, NULL),
(46, 22, 'FACT-005', NULL, NULL, '232323232223', 'FACTURA', 'Proveedor Prueba 5', '2025-04-04', 'Servicio Prueba 6', 'Hospedaje', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-07 18:50:15', '2025-04-07 18:50:15', '[]', NULL, NULL, NULL, NULL, NULL),
(47, 23, 'Fact-0031', NULL, NULL, '232323232223', 'FACTURA', 'Proveedor Prueba 6', '2025-04-06', 'Servicio Prueba 2', 'Hospedaje', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-07 18:58:27', '2025-04-07 18:58:27', '[]', NULL, NULL, NULL, NULL, NULL),
(48, 24, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 6', '2025-04-03', 'Servicio Prueba 4', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-07 20:19:22', '2025-04-07 20:19:22', '[]', NULL, NULL, NULL, NULL, NULL),
(49, 24, 'FACT-0047', NULL, NULL, '2323232332', 'RECIBO', 'sara', '2025-04-06', 'servicio', 'Combustible', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-07 20:20:19', '2025-04-07 20:20:19', '[]', NULL, NULL, NULL, NULL, NULL),
(51, 25, 'FACT-0044', NULL, NULL, '232323232223333', 'FACTURA', 'Miguel', '2025-04-07', 'Servicio Prueba 5', 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-07 21:15:22', '2025-04-07 21:15:22', '[]', NULL, NULL, NULL, NULL, NULL),
(52, 26, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Proveedor Prueba 4', '2025-04-06', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-08 14:23:36', '2025-04-08 14:23:36', '[]', NULL, NULL, NULL, NULL, NULL),
(53, 26, 'FACT-0043', NULL, NULL, '23232323', 'RECIBO', 'Proveedor Prueba 4', '2025-04-06', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, '', '2025-04-08 14:24:17', '2025-04-08 14:24:17', '[]', NULL, NULL, NULL, NULL, NULL),
(54, 27, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel', '2025-04-08', 'Servicio Prueba 4', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-08 16:44:06', '2025-04-08 16:44:06', '[]', NULL, NULL, NULL, NULL, NULL),
(55, 27, 'FACT-006', NULL, NULL, '232323232223', 'RECIBO', 'Miguel', '2025-04-08', 'servicio', 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-08 16:44:29', '2025-04-08 16:44:29', '[]', NULL, NULL, NULL, NULL, NULL),
(57, 28, 'FACT-004', NULL, NULL, '23232323', 'FACTURA', 'Miguel perez', '2025-04-07', 'servicio', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-08 21:18:32', '2025-04-08 21:18:32', '[]', NULL, NULL, NULL, NULL, NULL),
(59, 29, 'FACT-003', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-09 17:47:34', '2025-04-09 17:47:34', '[]', 0, '', NULL, NULL, NULL),
(60, 29, 'Fact-003', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-07', NULL, 'Hospedaje', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-09 17:48:31', '2025-04-09 17:48:31', '[]', 0, '', '3007243060101', NULL, NULL),
(61, 29, 'FACT-00432', NULL, NULL, '2333443', 'COMPROBANTE', 'Proveedor Prueba 4', '2025-04-07', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-09 17:49:11', '2025-04-09 17:49:11', '[]', 2, '2', NULL, NULL, NULL),
(62, 30, 'FACT-004', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-07', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 15:04:35', '2025-04-10 15:04:35', '[]', 0, '', '2333443', 2, NULL),
(63, 31, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-09', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 16:23:02', '2025-04-10 16:23:02', '[]', 0, '', NULL, 2, NULL),
(64, 31, 'FACT-005', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Alimentos', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 16:23:57', '2025-04-10 16:23:57', '[]', 0, '', '3007243060101', 1, NULL),
(65, 31, 'FACT-0054', NULL, NULL, '2333443', 'COMPROBANTE', 'Miguel perez', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 16:24:43', '2025-04-10 16:24:43', '[]', 20, '3424242sdx', NULL, 2, NULL),
(66, 19, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel perez', '2025-04-02', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 16:30:42', '2025-04-10 16:30:42', '[]', 0, '', NULL, 2, NULL),
(67, 32, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Combustible', NULL, NULL, 500.00, NULL, 500.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 16:48:54', '2025-04-10 16:48:54', '[]', 0, '', NULL, 1, NULL),
(68, 32, 'FACT-004', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 16:49:16', '2025-04-10 16:49:16', '[]', 0, '', '2333443', 2, NULL),
(74, 34, 'FACT-006', NULL, NULL, '                2323', 'FACTURA', 'Miguel perez', '2025-04-08', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 17:21:03', '2025-04-10 17:21:03', '', 2, '', NULL, NULL, NULL),
(75, 35, 'FACT-004', NULL, NULL, '2333443', 'COMPROBANTE', 'Miguel', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 17:31:58', '2025-04-10 17:31:58', '[]', 323, '211', NULL, 1, NULL),
(76, 35, 'FACT-00433', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-09', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 17:33:09', '2025-04-10 17:33:09', '[]', 0, '', NULL, 1, NULL),
(77, 36, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Miguel perez', '2025-04-08', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 18:34:50', '2025-04-10 18:34:50', '[]', 0, '', NULL, 2, NULL),
(78, 37, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Miguel perez', '2025-04-01', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 18:43:31', '2025-04-10 18:43:31', '[]', 0, '', NULL, 2, NULL),
(79, 37, '1221', NULL, NULL, '2333443', 'COMPROBANTE', 'Miguel', '2025-04-01', NULL, 'Alimentos', NULL, NULL, 12.00, NULL, 12.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 18:44:42', '2025-04-10 18:44:42', '[]', 12121, '12', NULL, 1, NULL),
(80, 38, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-01', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 199.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 18:50:41', '2025-04-10 18:50:41', '[]', 0, '', NULL, 2, NULL),
(81, 39, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 18:59:21', '2025-04-10 18:59:21', '[]', 0, '', NULL, 1, NULL),
(82, 39, 'FACT-0043', NULL, NULL, '2333443', 'COMPROBANTE', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 18:59:56', '2025-04-10 18:59:56', '[]', 32, '32', NULL, 2, NULL),
(83, 40, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:25:38', '2025-04-10 21:25:38', '[\"uploads\\/67f83752c121a_archivo.JPG\"]', 0, '', NULL, 1, NULL),
(84, 40, 'FACT-0042', NULL, NULL, '2333443', 'COMPROBANTE', 'Miguel perez', '2025-04-08', NULL, 'Alimentos', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:26:38', '2025-04-10 21:26:38', '[\"uploads\\/67f8378e268bd_capturaproceso.JPG\"]', 2, '332sa', NULL, 2, NULL),
(85, 40, 'FACT-005', NULL, NULL, NULL, 'RECIBO', 'Miguel', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:27:17', '2025-04-10 21:27:17', '[]', 0, '', '121212121', 2, NULL),
(86, 41, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:40:06', '2025-04-10 21:40:06', '[\"uploads\\/67f83ab6618b7_duplicado.JPG\"]', 0, '', NULL, 2, NULL),
(87, 42, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:45:05', '2025-04-10 21:45:05', '[\"uploads\\/67f83be196d26_archivo.JPG\"]', 0, '', NULL, 2, NULL),
(88, 42, 'Fact-003', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:45:40', '2025-04-10 21:45:40', '[]', 0, '', '242424242', 1, NULL),
(89, 42, 'FACT-005', NULL, NULL, '2333443', 'COMPROBANTE', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Alimentos', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:46:26', '2025-04-10 21:46:26', '[\"uploads\\/67f83c32bf3cf_duplicado.JPG\"]', 2, '4242ss', NULL, 2, NULL),
(90, 43, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:50:24', '2025-04-10 21:50:24', '[\"uploads\\/67f83d20eb360_archivo.JPG\"]', 0, '', NULL, 2, NULL),
(91, 43, 'FACT-0063', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Alimentos', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:51:17', '2025-04-10 21:51:17', '[\"uploads\\/67f83d55e29bb_capturaproceso.JPG\"]', 0, '', '2333443', 2, NULL),
(93, 44, '3323', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-09', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 21:59:56', '2025-04-10 21:59:56', '[\"uploads\\/67f83f5c1a56d_archivo.JPG\"]', 0, '', NULL, 2, NULL),
(94, 44, 'FACT-005', NULL, NULL, NULL, 'RECIBO', 'Miguel perez', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 22:00:28', '2025-04-10 22:00:28', '[\"uploads\\/67f83f7cdaa95_duplicado.JPG\"]', 0, '', '2333443', 1, NULL),
(95, 44, 'FACT-004', NULL, NULL, '                2323', 'COMPROBANTE', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 22:01:06', '2025-04-10 22:01:06', '[\"uploads\\/67f83fa203023_capturaproceso.JPG\"]', 4, '343', NULL, 1, NULL),
(96, 45, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-09', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 22:20:30', '2025-04-10 22:20:30', '[\"uploads\\/67f8442e45b6f_archivo.JPG\"]', 0, '', NULL, 1, NULL),
(98, 45, 'FACT-004rww', NULL, NULL, '2333443', 'COMPROBANTE', 'Miguel', '2025-04-08', NULL, 'Combustible', NULL, NULL, 300.00, NULL, 300.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 22:21:50', '2025-04-10 22:21:50', '[\"uploads\\/67f8447ef1d6b_archivo.JPG\"]', 1, '32e2', NULL, 2, NULL),
(99, 46, 'Fact-003', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 22:23:21', '2025-04-10 22:23:21', '[\"uploads\\/67f844d9ce8a6_capturaproceso.JPG\"]', 0, '', NULL, 1, NULL),
(100, 46, 'FACT-004', NULL, NULL, NULL, 'RECIBO', 'Miguel perez', '2025-04-09', NULL, 'Combustible', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 22:23:53', '2025-04-10 22:23:53', '[\"uploads\\/67f844f9a72f1_capturaproceso.JPG\"]', 0, '', '                232323232223              ', 2, NULL),
(101, 46, 'FACT-004ewwe', NULL, NULL, '2333443', 'COMPROBANTE', 'Miguel', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-10 22:24:38', '2025-04-10 22:24:38', '[\"uploads\\/67f84526becd4_duplicado.JPG\"]', 10, '10dhd', NULL, 2, NULL),
(102, 47, 'FACT-004', NULL, NULL, '3r3r3r', 'FACTURA', 'miguel', '2025-04-09', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 14:33:34', '2025-04-11 14:33:34', '[\"uploads\\/67f9283e607b1_archivo.JPG\"]', 0, '', NULL, 1, NULL),
(103, 47, 'Fact-003', NULL, NULL, NULL, 'RECIBO', 'perez', '2025-04-07', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 14:34:11', '2025-04-11 14:34:11', '[\"uploads\\/67f928639b841_archivo.JPG\"]', 0, '', '3242', 2, NULL),
(104, 47, 'FACT-006', NULL, NULL, '424242', 'COMPROBANTE', 'perez', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 14:35:00', '2025-04-11 14:35:00', '[\"uploads\\/67f928941def6_duplicado.JPG\"]', 3, '232asa', NULL, 1, NULL),
(105, 48, 'FACT-006', NULL, NULL, '3131', 'FACTURA', 'Miguel', '2025-04-09', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 14:39:54', '2025-04-11 14:39:54', '[\"uploads\\/67f929ba1539d_archivo.JPG\"]', 0, '', NULL, 2, NULL),
(106, 48, 'FACT-004', NULL, NULL, NULL, 'RECIBO', 'pepe', '2025-04-09', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 14:40:41', '2025-04-11 14:40:41', '[\"uploads\\/67f929e9bff41_duplicado.JPG\"]', 0, '', '24242424', 2, NULL),
(107, 48, 'Fact-0031', NULL, NULL, '323223', 'COMPROBANTE', 'perez', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 14:41:22', '2025-04-11 14:41:22', '[\"uploads\\/67f92a128ed54_capturaproceso.JPG\"]', 2, '232daa', NULL, 1, NULL),
(108, 50, 'Fact-003', NULL, NULL, '32322', 'FACTURA', 'Proveedor Prueba 5', '2025-04-10', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 16:04:07', '2025-04-11 16:04:07', '[\"uploads\\/67f93d7741257_duplicado.JPG\"]', 0, '', NULL, 2, NULL),
(109, 50, 'FACT-006', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-09', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 16:04:53', '2025-04-11 16:04:53', '[]', 0, '', '3232', 2, NULL),
(110, 51, 'Fact-001', NULL, NULL, '32323', 'FACTURA', 'Miguel perez', '2025-04-01', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 16:21:46', '2025-04-11 16:21:46', '[\"uploads\\/67f9419a30222_archivo.JPG\"]', 0, '', NULL, 2, NULL),
(111, 52, 'FACT-005', NULL, NULL, '2424442', 'FACTURA', 'Miguel perez', '2025-04-09', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 16:24:21', '2025-04-11 16:24:21', '[\"uploads\\/67f9423527d8c_archivo.JPG\"]', 0, '', NULL, 2, NULL),
(112, 52, '                Fact-003              ', NULL, NULL, NULL, 'RECIBO', '                Miguel     s         ', '2025-04-02', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 16:24:42', '2025-04-11 17:07:17', '[]', 0, '', '                N/A              ', 1, NULL),
(114, 53, '                Fact-003              ', NULL, NULL, '                3333', 'FACTURA', '                Miguel perez              ', '2025-04-09', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 17:27:14', '2025-04-11 17:28:25', '[]', 0, '', NULL, 1, NULL),
(115, 53, 'Fact-0033', NULL, NULL, NULL, 'RECIBO', 'rwrw', '2025-04-08', NULL, 'Gasto Operativo', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 17:32:11', '2025-04-11 17:32:11', '[\"uploads\\/67f9521b46e82_103.JPG\"]', 0, '', '2232', 2, NULL),
(116, 53, 'FACT-006', NULL, NULL, '1575', 'COMPROBANTE', '232', '2025-04-08', NULL, 'Combustible', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-11 17:45:01', '2025-04-11 17:45:01', '[]', 33, '23', NULL, 2, NULL),
(118, 55, 'FACT-004', NULL, NULL, '2323232', 'FACTURA', 'Proveedor Prueba 4', '2025-04-09', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-12 06:07:28', '2025-04-12 06:07:28', '[]', 0, '', NULL, 2, NULL),
(119, 55, 'FACT-005', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-12 06:09:06', '2025-04-12 06:09:06', '[]', 0, '', '1312323', 1, NULL),
(120, 55, 'FACT-006', NULL, NULL, '33434', 'COMPROBANTE', 'Proveedor Prueba 5', '2025-04-08', NULL, 'otros...', NULL, NULL, 300.00, NULL, 300.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-12 06:09:56', '2025-04-12 06:09:56', '[]', 2, '32asa', NULL, 2, NULL),
(123, 56, 'FACT-004', NULL, NULL, '1411', 'FACTURA', 'Proveedor Prueba 4', '2025-04-09', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-12 14:56:14', '2025-04-12 14:56:14', '[]', 0, '', NULL, 2, NULL),
(124, 56, 'FACT-002', NULL, NULL, NULL, 'RECIBO', '100', '2025-04-09', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-12 14:56:58', '2025-04-12 14:56:58', '[]', 0, '', '1w1131', 1, NULL),
(125, 56, 'Fact-0031', NULL, NULL, '2324242', 'COMPROBANTE', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Combustible', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-12 14:57:42', '2025-04-12 14:57:42', '[]', 2, '232', NULL, 1, NULL),
(126, 57, 'FACT-005', NULL, NULL, '32242', 'FACTURA', 'Proveedor Prueba 5', '2025-04-08', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-13 05:45:17', '2025-04-13 05:45:17', '[\"uploads\\/67fb4f6dc5a26_103.JPG\"]', 0, '', NULL, 1, NULL),
(127, 57, 'Fact-0031', NULL, NULL, NULL, 'RECIBO', 'Miguel', '2025-04-09', NULL, 'otros...', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-13 05:46:11', '2025-04-13 05:46:27', '[\"uploads\\/67fb4fb3a6b2a_capturaproceso.JPG\"]', 0, '', '42424242', 2, NULL),
(128, 57, 'Fact-003122', NULL, NULL, '32323', 'COMPROBANTE', 'pepe', '2025-04-09', NULL, 'Hospedaje', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-13 05:48:00', '2025-04-13 05:48:00', '[\"uploads\\/67fb50106a280_archivo.JPG\"]', 2, '32daa', NULL, 1, NULL),
(129, 58, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:09:38', '2025-04-14 15:09:38', '[\"uploads\\/67fd253238805_103.JPG\"]', 0, '', NULL, 1, NULL),
(130, 58, 'FACT-005', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:10:15', '2025-04-14 15:10:15', '[\"uploads\\/67fd2557dcdb1_capturaproceso.JPG\"]', 0, '', '4343435353', 2, NULL),
(131, 58, 'Fact-0031', NULL, NULL, '34343', 'COMPROBANTE', 'Miguel', '2025-04-08', NULL, 'Alimentos', NULL, NULL, 300.00, NULL, 300.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:11:03', '2025-04-14 15:11:03', '[\"uploads\\/67fd2587ac004_archivo.JPG\"]', 2, '323ssa', NULL, 1, NULL),
(132, 58, 'Fact-00313', NULL, NULL, '2424242', 'FACTURA', 'Proveedor Prueba 5', '2025-04-12', NULL, 'Alimentos', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:11:46', '2025-04-14 15:11:46', '[]', 0, '', NULL, 1, NULL),
(133, 59, 'Fact-0031', NULL, NULL, '2424242', 'FACTURA', 'Proveedor Prueba 6', '2025-04-07', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:20:22', '2025-04-14 15:20:22', '[\"uploads\\/67fd27b691441_103.JPG\"]', 0, '', NULL, 1, NULL),
(134, 60, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:23:11', '2025-04-14 15:23:11', '[\"uploads\\/67fd285fcfa2f_103.JPG\"]', 0, '', NULL, 1, NULL),
(135, 60, 'Fact-0031', NULL, NULL, NULL, 'RECIBO', 'Miguel', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:24:03', '2025-04-14 15:24:03', '[\"uploads\\/67fd289328f4c_archivo.JPG\"]', 0, '', '44242', 2, NULL),
(136, 60, 'Fact-0031343', NULL, NULL, '2333443', 'COMPROBANTE', 'Miguel', '2025-04-08', NULL, 'Alimentos', NULL, NULL, 300.00, NULL, 300.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:24:55', '2025-04-14 15:24:55', '[\"uploads\\/67fd28c7bc40c_capturaproceso.JPG\"]', 2, '24242', NULL, 1, NULL),
(137, 61, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 15:34:09', '2025-04-14 15:34:09', '[\"uploads\\/67fd2af113c5d_103.JPG\"]', 0, '', NULL, 1, NULL),
(138, 62, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 21:09:05', '2025-04-14 21:09:05', '[\"uploads\\/67fd7971dbccf_archivo.JPG\"]', 0, '', NULL, 1, NULL),
(139, 63, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-07', NULL, 'Hospedaje', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 21:16:33', '2025-04-14 21:16:33', '[\"uploads\\/67fd7b31b18d6_103.JPG\"]', 0, '', NULL, 1, NULL),
(140, 63, 'FACT-004434', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Gasto Operativo', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-14 21:16:54', '2025-04-14 21:16:54', '[]', 0, '', '242424', 2, NULL),
(141, 64, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Combustible', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-15 14:17:18', '2025-04-15 14:17:18', '[\"uploads\\/67fe6a6e93fd5_reporte_resumen (10).pdf\"]', 0, '', NULL, 1, NULL),
(142, 64, 'FACT-006322', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 200.00, NULL, 200.00, NULL, NULL, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-15 14:17:58', '2025-04-15 14:17:58', '[\"uploads\\/67fe6a9672472_103.JPG\"]', 0, '', '2333443', 2, NULL),
(147, 65, 'FACT-004333', NULL, NULL, '3r3r3r', 'FACTURA', 'Miguel', '2025-04-02', NULL, 'Combustible', NULL, NULL, 300.00, 36.00, 336.00, 0.00, 0.00, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-21 15:12:01', '2025-04-21 15:12:01', '[]', 0, '', NULL, 2, NULL),
(148, 65, 'FACT-004311', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Alimentos', NULL, NULL, 100.00, 12.00, 112.00, 0.00, 0.00, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-21 16:11:53', '2025-04-21 16:11:53', '[\"uploads\\/68066e49cb723_cuentaerror.JPG\"]', 0, '', NULL, 2, NULL),
(149, 65, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-02', NULL, 'Combustible', NULL, NULL, 100.00, 12.00, 132.00, 20.00, 0.00, NULL, NULL, 'Diesel', 'EN_PROCESO', '2025-04-21 16:20:55', '2025-04-21 16:22:43', '[\"uploads\\/6806706780ea6_errorfacturas.JPG\"]', 10, '', NULL, 1, NULL),
(150, 65, 'Fact-001', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-02', NULL, 'Combustible', NULL, NULL, 100.00, 12.00, 126.10, 14.10, 0.00, NULL, NULL, 'Gasolina', 'EN_PROCESO', '2025-04-21 16:33:54', '2025-04-21 16:33:54', '[\"uploads\\/6806737256665_enorden.JPG\"]', 3, '', NULL, 2, NULL),
(151, 66, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Hospedaje', NULL, NULL, 100.00, 12.00, 122.00, 0.00, 10.00, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-21 16:45:20', '2025-04-21 16:45:20', '[\"uploads\\/6806762047a69_cuentaerror.JPG\"]', 0, '', NULL, 2, NULL),
(152, 66, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Miguel perez', '2025-04-15', NULL, 'Combustible', NULL, NULL, 100.00, 12.00, 126.10, 14.10, 0.00, NULL, NULL, 'Gasolina', 'EN_PROCESO', '2025-04-21 16:46:02', '2025-04-21 16:46:02', '[]', 3, '', NULL, 2, NULL),
(153, 67, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel perez', '2025-04-15', NULL, 'Gasto Operativo', NULL, NULL, 100.00, 12.00, 159.00, 47.00, 0.00, NULL, NULL, 'Gasolina', 'ENVIADO_A_CORRECCION', '2025-04-21 17:40:00', '2025-04-21 20:40:17', '[\"uploads\\/680682f0d9059_cuentaerror.JPG\"]', 10, '', NULL, 2, NULL),
(154, 67, 'FACT-006', NULL, NULL, NULL, 'RECIBO', 'Miguel perez', '2025-04-14', NULL, 'otros...', NULL, NULL, 100.00, 0.00, 100.00, 0.00, 0.00, NULL, NULL, NULL, 'ENVIADO_A_CORRECCION', '2025-04-21 17:41:11', '2025-04-21 20:40:17', '[]', 0, '', '3007243060101', 2, NULL),
(155, 67, 'FACT-005', NULL, NULL, '2424242', 'COMPROBANTE', 'Proveedor Prueba 4', '2025-04-14', NULL, 'Alimentos', NULL, NULL, 100.00, 0.00, 100.00, 0.00, 0.00, NULL, NULL, NULL, 'ENVIADO_A_CORRECCION', '2025-04-21 17:41:48', '2025-04-21 20:40:17', '[\"uploads\\/6806835c8cbfc_errorfacturas.JPG\"]', 2, 'erer33', NULL, 2, NULL),
(156, 68, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-15', NULL, 'Hospedaje', NULL, NULL, 100.00, 12.00, 122.00, 0.00, 10.00, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-21 20:47:52', '2025-04-21 20:47:52', '[\"uploads\\/6806aef810dd8_errorfacturas.JPG\"]', 0, '', NULL, 2, NULL),
(157, 68, 'FACT-005', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-15', NULL, 'Combustible', NULL, NULL, 200.00, 24.00, 252.20, 28.20, 0.00, NULL, NULL, 'Gasolina', 'EN_PROCESO', '2025-04-21 20:49:19', '2025-04-21 20:49:19', '[\"uploads\\/6806af4f6238a_duplicado3.JPG\"]', 6, '', NULL, 1, NULL),
(158, 69, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-08', NULL, 'Combustible', NULL, NULL, 125.00, 15.00, 154.10, 14.10, 0.00, NULL, NULL, 'Gasolina', 'EN_PROCESO', '2025-04-21 21:16:14', '2025-04-21 21:16:14', '[\"uploads\\/6806b59e7f82f_prueba .pdf\"]', 3, '', NULL, 2, NULL),
(159, 70, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-15', NULL, 'Combustible', NULL, NULL, 100.00, 12.00, 126.10, 14.10, 0.00, NULL, NULL, 'Gasolina', 'EN_PROCESO', '2025-04-22 15:41:39', '2025-04-22 15:41:39', '[\"uploads\\/6807b8b369bc8_prueba .pdf\"]', 3, '', NULL, 2, NULL),
(160, 72, 'FACT-004', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-15', NULL, 'Hospedaje', NULL, NULL, 81.97, 9.84, 100.00, 0.00, 8.20, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-22 17:37:39', '2025-04-22 17:37:39', '[]', 0, '', NULL, 2, NULL),
(161, 72, 'FACT-006', NULL, NULL, '2333443', 'FACTURA', 'Miguel', '2025-04-15', NULL, 'Combustible', NULL, NULL, 202.23, 24.27, 250.00, 23.50, 0.00, NULL, NULL, 'Gasolina', 'EN_PROCESO', '2025-04-22 17:38:48', '2025-04-22 17:38:48', '[]', 5, '', NULL, 1, NULL),
(162, 72, 'Fact-0031', NULL, NULL, '2333443', 'FACTURA', 'Proveedor Prueba 4', '2025-04-10', NULL, 'Alimentos', NULL, NULL, 44.64, 5.36, 50.00, 0.00, 0.00, NULL, NULL, NULL, 'EN_PROCESO', '2025-04-22 17:39:38', '2025-04-22 17:39:38', '[\"uploads\\/6807d45adb1d3_cuentaerror.JPG\"]', 0, '', NULL, 1, NULL);

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
  `tipo_impuesto` enum('IVA','IDP','INGUAT') NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `impuestos`
--

INSERT INTO `impuestos` (`id`, `nombre`, `tipo_impuesto`, `porcentaje`, `estado`, `creado_en`) VALUES
(1, 'IVA', 'IVA', 12.00, 'ACTIVO', '2025-04-15 18:21:17'),
(2, 'IDP', 'IDP', 1.00, 'ACTIVO', '2025-04-15 18:21:17'),
(3, 'INGUAT', 'INGUAT', 5.00, 'ACTIVO', '2025-04-15 18:21:17');

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
  `estado` enum('EN_PROCESO','PENDIENTE_AUTORIZACION','PENDIENTE_REVISION_CONTABILIDAD','FINALIZADO','RECHAZADO_AUTORIZACION','RECHAZADO_POR_CONTABILIDAD') NOT NULL DEFAULT 'EN_PROCESO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exportado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `liquidaciones`
--

INSERT INTO `liquidaciones` (`id`, `id_caja_chica`, `id_centros_de_costos`, `fecha_creacion`, `fecha_inicio`, `fecha_fin`, `monto_total`, `estado`, `created_at`, `updated_at`, `exportado`) VALUES
(1, 1, 0, '2025-03-21', '2025-03-01', '2025-03-21', 1000.00, 'EN_PROCESO', '2025-03-21 20:06:47', '2025-04-08 16:32:23', 1),
(2, 2, 0, '2025-03-21', '2025-03-07', '2025-03-14', 200.00, 'EN_PROCESO', '2025-03-21 20:15:47', '2025-04-08 16:32:23', 0),
(3, 3, 0, '2025-03-23', '2025-03-08', '2025-03-23', 1000.00, 'EN_PROCESO', '2025-03-23 20:08:25', '2025-04-08 16:32:23', 0),
(4, 3, 0, '2025-03-25', '2025-03-01', '2025-03-25', 1000.00, 'EN_PROCESO', '2025-03-25 21:24:01', '2025-04-08 16:32:23', 0),
(5, 1, 0, '2025-03-27', '2025-03-01', '2025-03-27', 1975.00, 'EN_PROCESO', '2025-03-27 21:47:13', '2025-04-08 16:32:23', 0),
(6, 1, 0, '2025-03-31', '2025-03-08', '2025-03-31', 5000.00, 'EN_PROCESO', '2025-03-31 21:23:40', '2025-04-08 16:32:23', 0),
(9, 1, 0, '2025-04-02', '2025-03-01', '2025-04-02', 3133.00, 'PENDIENTE_AUTORIZACION', '2025-04-02 17:38:04', '2025-04-10 16:47:07', 0),
(11, 2, 0, '2025-04-03', '2025-04-01', '2025-04-04', 800.00, 'FINALIZADO', '2025-04-03 18:42:20', '2025-04-09 19:04:36', 0),
(12, 1, 0, '2025-04-04', '2025-03-31', '2025-04-04', 600.00, 'FINALIZADO', '2025-04-04 14:37:27', '2025-04-09 20:29:57', 0),
(15, 1, 0, '2025-04-01', '2025-03-31', '2025-04-05', 100.00, 'PENDIENTE_AUTORIZACION', '2025-04-04 16:46:17', '2025-04-09 20:25:53', 0),
(16, 1, 0, '2025-04-04', '2025-04-01', '2025-04-04', 300.00, 'PENDIENTE_AUTORIZACION', '2025-04-04 17:00:32', '2025-04-10 16:46:54', 0),
(17, 1, 0, '2025-04-04', '2025-04-01', '2025-04-03', 100.00, 'PENDIENTE_AUTORIZACION', '2025-04-04 17:29:18', '2025-04-10 16:46:03', 0),
(18, 2, 0, '2025-04-01', '2025-04-01', '2025-04-04', 400.00, 'EN_PROCESO', '2025-04-04 18:36:08', '2025-04-09 20:27:19', 0),
(19, 1, 0, '2025-04-04', '2025-04-01', '2025-04-04', 100.00, 'FINALIZADO', '2025-04-04 18:44:21', '2025-04-10 16:32:54', 0),
(20, 1, 0, '2025-04-04', '2025-04-01', '2025-04-04', 600.00, 'FINALIZADO', '2025-04-04 21:04:08', '2025-04-08 21:00:07', 1),
(21, 1, 0, '2025-04-07', '2025-04-01', '2025-04-07', 400.00, 'RECHAZADO_AUTORIZACION', '2025-04-07 18:42:01', '2025-04-08 16:46:54', 1),
(22, 2, 0, '2025-04-07', '2025-04-01', '2025-04-07', 500.00, 'RECHAZADO_POR_CONTABILIDAD', '2025-04-07 18:49:48', '2025-04-07 18:54:35', 0),
(23, 3, 0, '2025-04-07', '2025-03-31', '2025-04-07', 1000.00, 'RECHAZADO_POR_CONTABILIDAD', '2025-04-07 18:58:00', '2025-04-08 16:49:54', 0),
(24, 3, 0, '2025-04-07', '2025-04-01', '2025-04-07', 600.00, 'FINALIZADO', '2025-04-07 20:18:42', '2025-04-08 20:20:17', 0),
(25, 2, 0, '2025-04-07', '2025-04-01', '2025-04-07', 100.00, 'RECHAZADO_AUTORIZACION', '2025-04-07 21:12:47', '2025-04-08 16:52:06', 0),
(26, 1, 0, '2025-04-07', '2025-04-01', '2025-04-07', 200.00, 'RECHAZADO_POR_CONTABILIDAD', '2025-04-07 21:34:23', '2025-04-08 20:20:31', 0),
(27, 3, 0, '2025-04-08', '2025-04-01', '2025-04-08', 200.00, 'FINALIZADO', '2025-04-08 16:43:39', '2025-04-08 16:48:23', 0),
(28, 1, 0, '2025-04-08', '2025-04-01', '2025-04-08', 100.00, 'RECHAZADO_AUTORIZACION', '2025-04-08 21:18:04', '2025-04-09 17:50:59', 0),
(29, 1, 0, '2025-04-09', '2025-04-01', '2025-04-09', 400.00, 'FINALIZADO', '2025-04-09 15:00:19', '2025-04-09 17:57:37', 0),
(30, 1, 0, '2025-04-10', '2025-04-01', '2025-04-09', 100.00, 'FINALIZADO', '2025-04-09 22:03:27', '2025-04-10 15:26:48', 1),
(31, 3, 0, '2025-04-10', '2025-04-01', '2025-04-09', 400.00, 'FINALIZADO', '2025-04-10 16:21:59', '2025-04-10 16:26:57', 0),
(32, 2, 0, '2025-04-10', '2025-04-01', '2025-04-11', 600.00, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-04-10 16:48:34', '2025-04-10 18:04:37', 0),
(34, 1, 0, '2025-04-10', '2025-04-01', '2025-04-12', 100.00, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-04-10 17:20:34', '2025-04-10 18:03:53', 0),
(35, 1, 0, '2025-04-10', '2025-04-01', '2025-04-09', 200.00, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-04-10 17:31:33', '2025-04-10 17:58:49', 0),
(36, 3, 0, '2025-04-03', '2025-04-01', '2025-04-10', 100.00, 'RECHAZADO_AUTORIZACION', '2025-04-10 18:34:23', '2025-04-10 18:37:49', 0),
(37, 1, 0, '2025-04-10', '2025-04-01', '2025-04-01', 112.00, 'FINALIZADO', '2025-04-10 18:43:11', '2025-04-10 18:46:19', 0),
(38, 1, 0, '2025-04-10', '2025-04-01', '2025-04-10', 199.00, 'PENDIENTE_AUTORIZACION', '2025-04-10 18:50:18', '2025-04-10 18:50:53', 0),
(39, 1, 0, '2025-04-10', '2025-04-01', '2025-04-10', 200.00, 'PENDIENTE_AUTORIZACION', '2025-04-10 18:58:57', '2025-04-10 19:00:08', 0),
(40, 3, 0, '2025-04-10', '2025-04-01', '2025-04-10', 300.00, 'PENDIENTE_AUTORIZACION', '2025-04-10 21:24:54', '2025-04-10 21:27:32', 0),
(41, 1, 0, '2025-04-10', '2025-04-01', '2025-04-17', 100.00, 'FINALIZADO', '2025-04-10 21:39:38', '2025-04-10 21:41:36', 0),
(42, 1, 0, '2025-04-10', '2025-04-01', '2025-04-09', 500.00, 'FINALIZADO', '2025-04-10 21:44:15', '2025-04-10 21:48:17', 0),
(43, 1, 0, '2025-04-10', '2025-04-01', '2025-04-10', 300.00, 'PENDIENTE_AUTORIZACION', '2025-04-10 21:49:58', '2025-04-12 06:04:38', 0),
(44, 1, 0, '2025-04-10', '2025-04-01', '2025-04-10', 300.00, 'FINALIZADO', '2025-04-10 21:59:11', '2025-04-10 22:19:43', 0),
(45, 1, 0, '2025-04-11', '2025-04-01', '2025-04-17', 400.00, 'PENDIENTE_AUTORIZACION', '2025-04-10 22:20:03', '2025-04-11 17:25:59', 0),
(46, 1, 0, '2025-04-11', '2025-04-01', '2025-04-11', 400.00, 'PENDIENTE_AUTORIZACION', '2025-04-10 22:22:54', '2025-04-10 22:24:43', 0),
(47, 1, 0, '2025-04-11', '2025-04-01', '2025-04-11', 300.00, 'PENDIENTE_AUTORIZACION', '2025-04-11 14:32:52', '2025-04-11 16:02:22', 0),
(48, 1, 0, '2025-04-11', '2025-04-01', '2025-04-12', 400.00, 'PENDIENTE_AUTORIZACION', '2025-04-11 14:39:09', '2025-04-11 16:02:25', 0),
(49, 1, 0, '2025-04-04', '2025-04-01', '2025-04-10', 0.00, 'EN_PROCESO', '2025-04-11 16:02:39', '2025-04-11 16:02:39', 0),
(50, 1, 0, '2025-04-11', '2025-04-01', '2025-04-10', 200.00, 'PENDIENTE_AUTORIZACION', '2025-04-11 16:03:02', '2025-04-11 17:25:42', 0),
(51, 1, 0, '2025-04-11', '2025-04-01', '2025-04-10', 100.00, 'PENDIENTE_AUTORIZACION', '2025-04-11 16:21:14', '2025-04-11 17:25:46', 0),
(52, 1, 0, '2025-04-11', '2025-04-01', '2025-04-12', 200.00, 'PENDIENTE_AUTORIZACION', '2025-04-11 16:23:54', '2025-04-11 17:25:02', 0),
(53, 1, 0, '2025-04-11', '2025-04-01', '2025-04-12', 500.00, 'PENDIENTE_AUTORIZACION', '2025-04-11 17:26:12', '2025-04-11 17:59:41', 0),
(55, 1, 0, '2025-04-12', '2025-04-01', '2025-04-11', 600.00, 'EN_PROCESO', '2025-04-12 06:06:45', '2025-04-12 06:09:56', 0),
(56, 3, 0, '2025-04-12', '2025-04-01', '2025-04-11', 400.00, 'EN_PROCESO', '2025-04-12 14:55:37', '2025-04-12 14:57:42', 0),
(57, 3, 0, '2025-04-13', '2025-04-01', '2025-04-11', 500.00, 'PENDIENTE_AUTORIZACION', '2025-04-13 05:44:40', '2025-04-13 05:50:42', 0),
(58, 1, 0, '2025-04-14', '2025-04-01', '2025-04-14', 700.00, 'FINALIZADO', '2025-04-14 15:09:08', '2025-04-14 15:14:02', 0),
(59, 1, 0, '2025-04-14', '2025-04-01', '2025-04-14', 100.00, 'PENDIENTE_AUTORIZACION', '2025-04-14 15:19:58', '2025-04-14 15:20:29', 0),
(60, 1, 0, '2025-04-14', '2025-04-01', '2025-04-14', 600.00, 'FINALIZADO', '2025-04-14 15:22:33', '2025-04-14 15:27:23', 0),
(61, 3, 0, '2025-04-14', '2025-04-01', '2025-04-14', 100.00, 'FINALIZADO', '2025-04-14 15:33:19', '2025-04-14 15:36:13', 0),
(62, 1, 0, '2025-04-14', '2025-04-01', '2025-04-14', 100.00, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-04-14 21:01:37', '2025-04-14 21:15:09', 0),
(63, 1, 0, '2025-04-14', '2025-04-01', '2025-04-14', 300.00, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-04-14 21:16:06', '2025-04-14 21:17:56', 0),
(64, 1, 0, '2025-04-15', '2025-04-01', '2025-04-15', 300.00, 'EN_PROCESO', '2025-04-15 14:14:36', '2025-04-21 17:30:04', 0),
(65, 1, 0, '2025-04-15', '2025-04-01', '2025-04-15', 706.10, 'FINALIZADO', '2025-04-15 15:49:08', '2025-04-21 17:00:26', 1),
(66, 1, 0, '2025-04-01', '2025-04-01', '2025-04-21', 248.10, 'EN_PROCESO', '2025-04-21 16:44:11', '2025-04-21 16:46:02', 0),
(67, 1, 0, '2025-04-21', '2025-04-01', '2025-04-21', 359.00, 'FINALIZADO', '2025-04-21 17:39:08', '2025-04-21 20:46:26', 0),
(68, 1, 0, '2025-04-21', '2025-04-01', '2025-04-21', 374.20, 'PENDIENTE_AUTORIZACION', '2025-04-21 20:47:01', '2025-04-21 20:49:56', 0),
(69, 1, 1, '2025-04-21', '2025-04-01', '2025-04-21', 154.10, 'EN_PROCESO', '2025-04-21 20:53:49', '2025-04-22 15:13:45', 0),
(70, 2, 2, '2025-04-22', '2025-04-01', '2025-04-22', 126.10, 'EN_PROCESO', '2025-04-22 15:14:58', '2025-04-22 15:45:22', 0),
(72, 2, 0, '2025-04-22', '2025-04-01', '2025-04-29', 400.00, 'EN_PROCESO', '2025-04-22 16:07:34', '2025-04-22 17:39:38', 0);

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
(326, 2, 'autorizar_liquidaciones', 'ACTIVO', '2025-03-27 21:39:30'),
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
(1176, 4, 'autorizar_liquidaciones', 'INACTIVO', '2025-04-01 18:50:56'),
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cuenta_contable_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `tipos_gastos`
--

INSERT INTO `tipos_gastos` (`id`, `name`, `description`, `estado`, `created_at`, `updated_at`, `cuenta_contable_id`) VALUES
(1, 'Gasto Operativo', 'gasolina', 'ACTIVO', '2025-03-21 20:18:54', '2025-03-23 18:21:45', NULL),
(2, 'Combustible', 'Combustible', 'ACTIVO', '2025-04-03 15:57:57', '2025-04-03 15:57:57', NULL),
(3, 'Hospedaje', 'Hospedaje', 'ACTIVO', '2025-04-03 15:58:29', '2025-04-03 15:58:29', NULL),
(4, 'Alimentos', 'Alimentos', 'ACTIVO', '2025-04-03 15:58:40', '2025-04-03 15:58:40', NULL),
(5, 'otros...', 'otros...', 'ACTIVO', '2025-04-03 15:58:57', '2025-04-03 15:58:57', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_gasto_impuestos`
--

CREATE TABLE `tipo_gasto_impuestos` (
  `id` int(11) NOT NULL,
  `tipo_gasto_id` int(11) NOT NULL,
  `impuesto_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_gasto_impuestos`
--

INSERT INTO `tipo_gasto_impuestos` (`id`, `tipo_gasto_id`, `impuesto_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-04-15 18:21:48', '2025-04-15 18:21:48'),
(2, 1, 2, '2025-04-15 18:21:48', '2025-04-15 18:21:48'),
(3, 1, 3, '2025-04-15 18:21:48', '2025-04-15 18:21:48');

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
(15, 'Pepe', 'pepe@gmail.com', '$2y$10$N1S2vLvPqDk6/lXmgcoXoeGlWWx6XUbZZN5TWSs8QsG74Ok1w8ypG', 4, '2025-04-02 14:35:36', '2025-04-07 21:09:28'),
(16, 'Encargado 2', 'encargado2@example.com', '$2y$10$KqqbMsE9fIjDxMUH2BaC1.Zx5AF2Vef/sdEJtTnmOBDyhY/hUO10i', 2, '2025-04-22 15:31:58', '2025-04-22 15:31:58');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cuenta_centro_costo` (`id_centro_costo`);

--
-- Indices de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detalle_liquidacion_id` (`id_liquidacion`),
  ADD KEY `fk_detalle_centro_costo` (`id_centro_costo`),
  ADD KEY `fk_detalle_cuenta_contable` (`id_cuenta_contable`);

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
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `fk_tipos_gastos_cuenta_contable` (`cuenta_contable_id`);

--
-- Indices de la tabla `tipo_gasto_impuestos`
--
ALTER TABLE `tipo_gasto_impuestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_gasto_id` (`tipo_gasto_id`,`impuesto_id`),
  ADD KEY `impuesto_id` (`impuesto_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4020;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=369;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

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
-- AUTO_INCREMENT de la tabla `tipo_gasto_impuestos`
--
ALTER TABLE `tipo_gasto_impuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
-- Filtros para la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD CONSTRAINT `fk_cuenta_centro_costo` FOREIGN KEY (`id_centro_costo`) REFERENCES `centros_costos` (`id`);

--
-- Filtros para la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  ADD CONSTRAINT `fk_detalle_centro_costo` FOREIGN KEY (`id_centro_costo`) REFERENCES `centros_costos` (`id`),
  ADD CONSTRAINT `fk_detalle_cuenta_contable` FOREIGN KEY (`id_cuenta_contable`) REFERENCES `cuentas_contables` (`id`),
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
-- Filtros para la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  ADD CONSTRAINT `fk_tipos_gastos_cuenta_contable` FOREIGN KEY (`cuenta_contable_id`) REFERENCES `cuentas_contables` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tipo_gasto_impuestos`
--
ALTER TABLE `tipo_gasto_impuestos`
  ADD CONSTRAINT `tipo_gasto_impuestos_ibfk_1` FOREIGN KEY (`tipo_gasto_id`) REFERENCES `tipos_gastos` (`id`),
  ADD CONSTRAINT `tipo_gasto_impuestos_ibfk_2` FOREIGN KEY (`impuesto_id`) REFERENCES `impuestos` (`id`);

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
