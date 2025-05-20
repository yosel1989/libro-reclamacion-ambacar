<?php
/**
 * Plugin Name: Caldera Form
 * Description: Libro de Reclamaciones con pasos y envío por correo.
 * Version: 1.0
 * Author: Tu Nombre
 */

if (!defined('ABSPATH')) exit;

define('CALDERA_FORM_PATH', plugin_dir_path(__FILE__));
define('CALDERA_FORM_OPTION_EMAILS', 'caldera_form_emails'); // ✅ Definimos la constante

// Activación: crea la tabla
register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table = $wpdb->prefix . 'caldera_form';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombres VARCHAR(100),
        apellidos VARCHAR(100),
        tipo_doc VARCHAR(20),
        numero_doc VARCHAR(30),
        correo VARCHAR(100),
        telefono VARCHAR(30),
        distrito VARCHAR(100),
        direccion TEXT,
        menor_edad TINYINT(1),
        tipo_bien VARCHAR(50),
        marca VARCHAR(100),
        local VARCHAR(100),
        area VARCHAR(100),
        tipo_moneda VARCHAR(10),
        monto DECIMAL(10,2),
        tipo_reclamo VARCHAR(50),
        detalle TEXT,
        pedido TEXT,
        fecha_respuesta DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
});

// Incluir archivos
include_once CALDERA_FORM_PATH . 'form-handler.php';
include_once CALDERA_FORM_PATH . 'admin-settings.php';

// Shortcode para insertar formulario
add_shortcode('caldera_form', function () {
    ob_start();
    include CALDERA_FORM_PATH . 'form-template.php';
    return ob_get_clean();
});

// Cargar JS y CSS
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('caldera-form-style', plugins_url('assets/style.css', __FILE__));
    wp_enqueue_script('caldera-form-js', plugins_url('assets/script.js', __FILE__), ['jquery'], null, true);
    wp_localize_script('caldera-form-js', 'caldera_form_ajax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});
