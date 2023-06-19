<?php

add_action('admin_init', 'sdev_register_settings');
add_action('admin_menu', 'sdev_add_settings_page');

function sdev_add_settings_page() {
    add_options_page(
        'SDEV Plugin Settings',
        'SDEV Settings',
        'manage_options',
        'sdev-settings',
        'sdev_render_settings_page'
    );
}

function sdev_render_settings_page() {
    $universal_content = get_option('amf_universal_content');
    ?>
    <div class="wrap">
        <h1>AMFLP Plugin Settings</h1>

        <form method="post" action="options.php">
            
            <label for="sdev-universal-content">Universal Content</label>
            <?php wp_editor($universal_content, 'amf_universal_content', array('textarea_rows' => 5)); ?>

            
            <?php submit_button('Save Content'); ?>
        </form>

        <h2>CSV Import</h2>
        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="sdev_import_csv">
            <input type="file" name="sdev_csv_file" accept=".csv">
            <?php wp_nonce_field('sdev_import_csv', 'sdev_import_csv_nonce'); ?>
            <?php submit_button('Import CSV'); ?>
        </form>
    </div>
    <?php
}

function sdev_register_settings() {
    register_setting('amfence-options', 'amf_universal_content');
}