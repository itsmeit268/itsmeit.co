<?php

/**
 *  Add hreflang language
 */
add_action('add_meta_boxes', 'add_hreflang_metabox');
add_action('edited_category', 'save_category_fields');
add_action('create_category', 'save_category_fields');
add_action('delete_category', 'delete_category_metabox');
add_action('category_add_form', 'category_meta_fields', 1, 1);
add_action('category_edit_form', 'category_meta_fields', 1, 1);
add_action('save_post', 'save_hreflang_metabox');

function add_hreflang_metabox() {
    add_meta_box(
        'hreflang_metabox',
        'Hreflang Settings',
        'render_hreflang_metabox',
        ['post', 'page'],
        'side',
        'high'
    );
}

function render_hreflang_metabox($post) {
    $hreflang = get_post_meta($post->ID, '_hreflang', true);
    ?>
    <label for="hreflang_value">Hreflang:</label>
    <input type="text" id="hreflang_value" name="hreflang_value" value="<?= esc_attr($hreflang); ?>" style="width: 100%;" />
    <?php
}

function save_hreflang_metabox($post_id) {
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['hreflang_value'])) {
        update_post_meta($post_id, '_hreflang', sanitize_text_field($_POST['hreflang_value']));
    }
}

function category_meta_fields($term) {
    $hreflang = get_term_meta($term->term_id, '_c_hreflang', true);
    ?>
    <div class="cat-hreflang" style="display: flex; flex-direction: row;">
        <label for="_c_hreflang" style="margin-bottom: 10px; margin-right: 10px; width: 270px;">Hreflang:</label>
        <input type="text" id="_c_hreflang" name="_c_hreflang" value="<?= esc_attr($hreflang); ?>" style="width: 100%;margin-bottom: 15px;margin-right: 10px;" />
    </div>
    <?php
}

function save_category_fields($term_id) {
    if ( isset( $_REQUEST['_c_hreflang'] ) ) {
        $term_hreflang = $_REQUEST['_c_hreflang'];
        if( $term_hreflang ) {
            update_term_meta( $term_id, '_c_hreflang', $term_hreflang );
        }
    }
}

function delete_category_metabox($term_id) {
    delete_term_meta($term_id, '_c_hreflang');
}