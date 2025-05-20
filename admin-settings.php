<?php
add_action('admin_menu', function () {
    add_options_page('Caldera Form', 'Caldera Form', 'manage_options', 'caldera-form', 'caldera_form_admin_page');
});

add_action('admin_init', function () {
    register_setting('caldera_form_options', CALDERA_FORM_OPTION_EMAILS);
});

function caldera_form_admin_page()
{
    ?>
    <div class="wrap">
        <h1>Configuración de Caldera Form</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('caldera_form_options');
            do_settings_sections('caldera_form_options');
            $emails = get_option(CALDERA_FORM_OPTION_EMAILS, '');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Correos de notificación (separados por coma)</th>
                    <td><input type="text" name="<?= CALDERA_FORM_OPTION_EMAILS ?>" value="<?= esc_attr($emails) ?>" size="80" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
