<?php
// Hook para recibir el formulario por AJAX
add_action('wp_ajax_caldera_form_submit', 'caldera_form_submit');
add_action('wp_ajax_nopriv_caldera_form_submit', 'caldera_form_submit');

function caldera_form_submit()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_send_json_error('Método no permitido.');
    }

    global $wpdb;

    $table = $wpdb->prefix . 'caldera_form';

    // Sanitización y preparación de datos
    $data = [
        'nombres'         => sanitize_text_field($_POST['nombres'] ?? ''),
        'apellidos'       => sanitize_text_field($_POST['apellidos'] ?? ''),
        'tipo_doc'        => sanitize_text_field($_POST['tipo_doc'] ?? ''),
        'numero_doc'      => sanitize_text_field($_POST['numero_doc'] ?? ''),
        'telefono'        => sanitize_text_field($_POST['telefono'] ?? ''),
        'correo'          => sanitize_email($_POST['correo'] ?? ''),
        'distrito'        => sanitize_text_field($_POST['distrito'] ?? ''),
        'direccion'       => sanitize_textarea_field($_POST['direccion'] ?? ''),
        'menor_edad'      => isset($_POST['menor_edad']) ? 1 : 0,
        'tipo_bien'       => sanitize_text_field($_POST['tipo_bien'] ?? ''),
        'marca'           => sanitize_text_field($_POST['marca'] ?? ''),
        'local'           => sanitize_text_field($_POST['local'] ?? ''),
        'area'            => sanitize_text_field($_POST['area'] ?? ''),
        'tipo_moneda'     => sanitize_text_field($_POST['tipo_moneda'] ?? ''),
        'monto'           => is_numeric($_POST['monto'] ?? null) ? floatval($_POST['monto']) : 0,
        'tipo_reclamo'    => sanitize_text_field($_POST['tipo_reclamo'] ?? ''),
        'detalle'         => sanitize_textarea_field($_POST['detalle'] ?? ''),
        'pedido'          => sanitize_textarea_field($_POST['pedido'] ?? ''),
        'fecha_respuesta' => sanitize_text_field($_POST['fecha_respuesta'] ?? ''),
    ];

    // Guardar en la base de datos
    $inserted = $wpdb->insert($table, $data);

    if (!$inserted) {
        wp_send_json_error('Error al guardar en la base de datos.');
    }

    // Obtener correos de configuración
    $emails = get_option('caldera_form_emails', '');
    $recipients = array_filter(array_map('trim', explode(',', $emails)));

    // Preparar mensaje de correo
    $subject = 'Nuevo reclamo recibido - Libro de Reclamaciones';
    $message = "Se ha enviado un nuevo formulario:\n\n";

    foreach ($data as $key => $value) {
        $field_name = ucwords(str_replace('_', ' ', $key));
        $message .= "{$field_name}: {$value}\n";
    }

    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    foreach ($recipients as $email) {
        if (is_email($email)) {
            wp_mail($email, $subject, $message, $headers);
        }
    }

    wp_send_json_success('Formulario enviado correctamente.');
}
