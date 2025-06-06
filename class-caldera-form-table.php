<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Caldera_Form_Table extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            'singular' => 'reclamo',
            'plural'   => 'reclamos',
            'ajax'     => false
        ]);
    }

    public function get_columns()
    {
        return [
            'cb'            => '<input type="checkbox" />',
            'id'            => 'ID',
            'nombres'       => 'Nombres',
            'apellidos'     => 'Apellidos',
            'tipo_doc'      => 'Tipo Doc',
            'numero_doc'    => 'N° Doc',
            'correo'        => 'Correo',
            'telefono'      => 'Teléfono',
            'created_at'    => 'Fecha Registro',
            'acciones'      => 'Acciones',
        ];
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="reclamo[]" value="%s" />',
            $item['id']
        );
    }

    public function column_acciones($item)
    {
        $url_pdf = admin_url('admin-post.php?action=caldera_form_download_pdf&id=' . $item['id']);
        $btn_pdf = '<a href="' . esc_url($url_pdf) . '" class="button" target="_blank">PDF</a>';

        return $btn_pdf;
    }

    public function prepare_items()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ambacar_libro_reclamaciones';

        $per_page = 10;
        $current_page = $this->get_pagenum();

        $search = '';
        if (!empty($_REQUEST['s'])) {
            $search = sanitize_text_field($_REQUEST['s']);
        }

        $where = '';
        if ($search) {
            $like = '%' . $wpdb->esc_like($search) . '%';
            $where = $wpdb->prepare(" WHERE nombres LIKE %s OR apellidos LIKE %s OR numero_doc LIKE %s", $like, $like, $like);
        }

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");

        $offset = ($current_page - 1) * $per_page;
        $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name $where ORDER BY created_at ASC LIMIT %d OFFSET %d", $per_page, $offset), ARRAY_A);

        $this->_column_headers = [$this->get_columns(), [], []];

        $this->items = $items;

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'nombres':
            case 'apellidos':
            case 'tipo_doc':
            case 'numero_doc':
            case 'correo':
            case 'telefono':
            case 'created_at':
                return esc_html($item[$column_name]);
            default:
                return print_r($item, true);
        }
    }
}
