<?php
require_once CALDERA_FORM_PATH . 'lib/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();




add_action('wp_ajax_caldera_form_submit', 'caldera_form_submit');
add_action('wp_ajax_nopriv_caldera_form_submit', 'caldera_form_submit');

function caldera_form_submit()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_send_json_error('Método no permitido.');
    }

    global $wpdb;

    $table = $wpdb->prefix . 'ambacar_libro_reclamaciones';

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
    ];

    $inserted = $wpdb->insert($table, $data);

    if (!$inserted) {
        wp_send_json_error('Error al guardar en la base de datos.');
    }

    $insert_id = $wpdb->insert_id;

    // Clave secreta para encriptar (guárdala segura en tu plugin)
    $secret_key = 'tu_clave_secreta_1234'; // Cambia esto por una clave segura

    // Encriptar el ID insertado
    $encrypted_id = encrypt_id($insert_id, $secret_key);

    // Enviar correos (opcional, igual que antes)

    wp_send_json_success([
        'message' => 'Formulario enviado correctamente.',
        'id' => $encrypted_id,
    ]);
}




function encrypt_id($id, $key) {
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($id, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    return base64_encode($iv.$hmac.$ciphertext_raw);
}

function decrypt_id($encrypted, $key) {
    $c = base64_decode($encrypted);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $original_id = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    if (hash_equals($hmac, $calcmac)) {
        return $original_id;
    }
    return false;
}
