<?php
// Asegúrate de estar en el admin
if (!defined('ABSPATH') || !is_admin()) {
    return;
}

require_once CALDERA_FORM_PATH . 'class-caldera-form-table.php';
require_once CALDERA_FORM_PATH . 'lib/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

add_action('admin_menu', 'caldera_form_admin_menu');

function caldera_form_admin_menu() {
    add_menu_page(
        'Libro de Reclamaciones',
        'Libro de Reclamaciones',
        'manage_options',
        'libro-reclamaciones',
        'caldera_form_entries_page',
        'dashicons-feedback',
        30
    );
    
    add_submenu_page(
        'libro-reclamaciones',
        'Registros',
        'Registros',
        'manage_options',
        'libro-reclamaciones',
        'caldera_form_entries_page'
    );
    
    add_submenu_page(
        'libro-reclamaciones',
        'Correos de notificación',
        'Correos de notificación',
        'manage_options',
        'libro-reclamaciones-settings',
        'caldera_form_settings_page'
    );
}

function caldera_form_settings_page() {
    if (isset($_POST['caldera_emails'])) {
        $raw_emails = explode(',', $_POST['caldera_emails']);
        $emails = array_filter(array_map('sanitize_email', $raw_emails));
        update_option(CALDERA_FORM_OPTION_EMAILS, implode(',', $emails));
        echo '<div class="updated"><p>Correos actualizados correctamente.</p></div>';
    }

    $emails = esc_attr(get_option(CALDERA_FORM_OPTION_EMAILS, ''));
    ?>
    <div class="wrap">
        <h1>Configuración de Correos</h1>
        <form method="post">
            <label for="caldera_emails">Correos de destino (separados por coma):</label><br>
            <input type="text" name="caldera_emails" id="caldera_emails" value="<?php echo $emails; ?>" style="width: 100%; max-width: 600px;">
            <p class="description">Ejemplo: ejemplo@correo.com, otro@correo.com</p>
            <br>
            <input type="submit" class="button button-primary" value="Guardar">
        </form>
    </div>
    <?php
}

function caldera_form_entries_page() {
    $table = new Caldera_Form_Table();

    if (isset($_POST['export_excel'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ambacar_libro_reclamaciones';
        $registros = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=reclamos.csv");
    
        $output = fopen("php://output", "w");
        if (!empty($registros)) {
            fputcsv($output, array_keys($registros[0]));
            foreach ($registros as $registro) {
                fputcsv($output, $registro);
            }
        }
        fclose($output);
        exit;
    }
?>
    <div class="wrap">
        <h1>Libro de Reclamaciones</h1>
        <form method="get">
            <input type="hidden" name="page" value="libro-reclamaciones" />
            <?php
            $table->prepare_items();
            $table->search_box('Buscar', 'search_id');
            $table->display();
            ?>
        </form>
    </div>
<?php
}

/**
 * Genera el PDF con Dompdf usando los datos del reclamo
 * @param array $datos Array asociativo con los datos del reclamo
 * @return string contenido PDF en binario
 */
function generar_pdf_reclamo_completo($datos) {
    $dompdf = new Dompdf();

    // Sanitizar y preparar datos para el PDF
    $id = esc_html($datos['id']);
    $nombre = esc_html($datos['nombres'] . ' ' . $datos['apellidos']);
    $tipo_doc = esc_html($datos['tipo_doc']);
    $num_doc = esc_html($datos['numero_doc']);
    $email = esc_html($datos['correo']);
    $direccion = esc_html($datos['direccion']);
    $telefono = esc_html($datos['telefono']);
    $menor_edad = !empty($datos['menor_edad']);
    $producto = esc_html($datos['tipo_bien'] ?? '');
    $moneda = $datos['tipo_moneda'] ?? 'SOLES';
    $monto = esc_html(number_format($datos['monto'], 2));
    $descripcion_bien = esc_html($datos['detalle'] ?? '');
    $detalle_reclamo = esc_html($datos['detalle'] ?? '');
    $tipo = strtolower($datos['tipo_reclamo'] ?? 'reclamo');
    $acciones_proveedor = esc_html($datos['pedido'] ?? '');
    
    $fecha_resp = !empty($datos['fecha_respuesta']) ? strtotime($datos['fecha_respuesta']) : false;
    $fecha_reg = !empty($datos['created_at']) ? strtotime($datos['created_at']) : false;
    $reg_dia = $fecha_reg ? date('d', $fecha_reg) : '';
    $reg_mes = $fecha_reg ? date('m', $fecha_reg) : '';
    $reg_anio = $fecha_reg ? date('Y', $fecha_reg) : '';

    $html = '
    <html>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <style>
            body, * {
                font-family: "Roboto", sans-serif;
                font-optical-sizing: auto;
                font-style: normal;
                font-variation-settings:
                    "wdth" 100;
                font-size: 9px !important;
                box-sizing: border-box;
            }
            table { width: 100%; border-collapse: collapse; table-layout: fixed; }
            th, td { border: 1px solid #000; padding: 4px; }
            .no-border { border: none; }
            .title { text-align: center; font-weight: bold; font-size: 14px; }
            .section-title { font-weight: bold; background: #eee; }
            .firma { height: 60px; vertical-align: bottom; text-align: center; }
            .observacion { font-size: 10px; margin-top: 10px; }
            .t-center{text-align:center;}
            .semibold{
                font-weight: 500;
            }
            table {
                border-collapse: collapse;
            }
            
            table + table {
                margin-top: -1px; /* ajusta según el grosor del borde */
            }
        </style>
    </head>
    <body>
        <table>
            <tr>
                <td colspan="4" class="title">LIBRO DE RECLAMACIONES</td>
                <td colspan="3" class="title">HOJA DE RECLAMACIÓN <br>Nº V' . str_pad($id, 3 , '0', STR_PAD_LEFT) . '-' . $reg_anio . '</td>
            </tr>
            <tr>
                <td><strong>FECHA:</strong></td>
                <td class="t-center">' . date('d') . '</td>
                <td class="t-center">' . date('m') . '</td>
                <td class="t-center">' . date('Y') . '</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="7">AMBACAR S.A.C</td>
            </tr>
            <tr>
                <td colspan="7">RUC: 20601055415</td>
            </tr>
        </table>
        <table>
            <tr><td colspan="4" class="section-title">1. IDENTIFICACIÓN DEL CONSUMIDOR RECLAMANTE</td></tr>
            <tr>
                <td><strong>NOMBRE:</strong></td>
                <td colspan="3">' . $nombre . '</td>
            </tr>
            <tr>
                <td><strong>DOMICILIO</strong>:</td>
                <td colspan="3">' . $direccion . '</td>
            </tr>
            <tr>
                <td><strong>TIPO DOC.:</strong></td>
                <td>' . $tipo_doc . '</td>
                <td><strong>N° DOC.:</strong></td>
                <td>' . $num_doc . '</td>
            </tr>
            <tr>
                <td><strong>TELEFONO:</strong></td>
                <td>' . $telefono . '</td>
                <td><strong>EMAIL:</strong></td>
                <td>' . $email . '</td>
            </tr>
            <tr>
                <td><strong>MENOR DE EDAD:</strong></td>
                <td colspan="3">' . ($menor_edad ? 'SI' : 'NO') . '</td>
            </tr>
        </table>
        <table>
            <tr>
                <td rowspan="3"><strong>DATOS DEL APODERADO EN CASO SEA MENOR DE EDAD:</strong></td>
                <td colspan="2"><strong>DNI:</strong></td>
            </tr>
            <tr>
                <td><strong>NOMBRE:</strong></td>
                <td><strong>APELLIDOS:</strong></td>
            </tr>
            <tr>
                <td><strong>TELEFONO:</strong></td>
                <td><strong>EMAIL:</strong></td>
            </tr>
            <tr>
                <td class="section-title t-center">Marca</td>
                <td class="section-title t-center">Local</td>
                <td class="section-title t-center">Area</td>
            </tr>
            <tr>
                <td class="t-center">Ford</td>
                <td class="t-center">RM San Borja</td>
                <td class="t-center">PostVenta</td>
            </tr>
        </table>
        <table>
            <tr><td colspan="5" class="section-title">2. IDENTIFICACIÓN DEL BIEN CONTRATADO</td></tr>
            <tr>
                <td><strong>PRODUCTO:</strong></td>
                <td></td>
                <td><strong>MONEDA:</strong></td>
                <td colspan="2">SOLES</td>
            </tr>
            <tr>
                <td><strong>SERVICIO:</strong></td>
                <td></td>
                <td><strong>MONTO RECLAMADO:</strong></td>
                <td colspan="2">456</td>
            </tr>
            <tr>
                <td colspan="2"><strong>DESCRIPCIÓN:</strong></td>
                <td colspan="3">' . $descripcion_bien . '</td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="8" class="section-title">3. DETALLE DE LA RECLAMACIÓN Y PEDIDO DEL CONSUMIDOR</td>
                <td class="section-title t-center"><strong>RECLAMO</strong></td>
                <td class="section-title t-center">' . ($tipo === 'reclamo' ? 'X' : '') . '</td>
                <td class="section-title t-center"><strong>QUEJA</strong></td>
                <td class="section-title t-center">' . ($tipo === 'queja' ? 'X' : '') . '</td>
            </tr>
            <tr>
                <td colspan="8" rowspan="3" valign="top"><strong>DETALLE:</strong> ' . $detalle_reclamo . '</td>
                <td colspan="4" rowspan="3" class="firma">FIRMA DEL CONSUMIDOR</td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="12" class="section-title">4. OBSERVACIONES Y ACCIONES ADOPTADAS POR EL PROVEEDOR</td>
            </tr>
            <tr>
                <td colspan="6" style="height:50px" valign="top">Reclamo: </td>
                <td colspan="6" style="height:50px" valign="top">Queja: </td>
            </tr>
            <tr>
                <td colspan="5" ><strong>FECHA DE COMUNICACIÓN DE LA RESPUESTA:</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="4" rowspan="3" class="firma">FIRMA DEL PROVEEDOR</td>
            </tr>
            <tr>
                <td colspan="8" rowspan="2"></td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <p>* La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.</p>
                    <p>* El proveedor debe dar respuesta al reclamo o queja en un plazo no mayor a quince (15) días hábiles, el cual es improrrogable.</p>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ';


    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $dompdf->output();
}

// Acción para descarga PDF (en admin_post)
add_action('admin_post_caldera_form_download_pdf', function() {
    if (!current_user_can('manage_options') || !isset($_GET['id'])) {
        wp_die('No autorizado');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ambacar_libro_reclamaciones';
    $id = intval($_GET['id']);
    $datos = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);

    if (!$datos) {
        wp_die('Reclamo no encontrado');
    }

    require_once CALDERA_FORM_PATH . 'lib/dompdf/autoload.inc.php';
    $pdf_content = generar_pdf_reclamo_completo($datos);

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename=reclamo-' . $id . '.pdf');
    echo $pdf_content;
    exit;
});
