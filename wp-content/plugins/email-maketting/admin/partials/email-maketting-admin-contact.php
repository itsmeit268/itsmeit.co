<?php

/**
 * @link       https://itsmeit.co
 * @since      1.0.0
 *
 * @package    Email_Maketting
 * @subpackage Email_Maketting/admin/partials
 */

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Email_Maketting_Admin_Contact extends WP_List_Table {

    public function __construct() {
        parent::__construct(array(
            'singular' => 'id',
            'plural'   => 'emails',
            'ajax'     => false
        ));
    }

    function prepare_items() {
        // Tạo các cột cho bảng
        $columns = $this->get_columns();
        $hidden_columns = $this->get_hidden_columns();
        $sortable_columns = $this->get_sortable_columns();

        // Thiết lập dữ liệu cho bảng
        $data = $this->get_data();

        // Áp dụng search
        $data = $this->search_data($data);

        // Sắp xếp dữ liệu nếu có yêu cầu sắp xếp
        $data = $this->sort_data($data);

        // Lưu các giá trị filter và search vào URL phân trang
        $current_page = $this->get_pagenum();
        $per_page = $this->get_items_per_page('emails_per_page', 20);
        $total_items = count($data);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));

        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        // Thiết lập thông tin cho bảng
        $this->_column_headers = array($columns, $hidden_columns, $sortable_columns);

        $this->process_bulk_action();

        $this->items = $data;
    }


    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

    function get_columns() {
        return array(
            'cb'          => '<input type="checkbox" />',
            'name'   => __('Name'),
            'subject'     => __('Subject'),
            'email'       => __('Email'),
            'content'     => __('Content'),
            'sent_count'  => __('Sent Count'),
            'create_date' => __('Create Date')
        );
    }

    function get_hidden_columns() {
        return array();
    }

    function get_sortable_columns() {
        return array(
            'name'        => array('name', false),
            'email'       => array('email', false),
            'create_date' => array('create_date', false),
        );
    }

    function get_data() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'email_contact';
        $data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        return $data;
    }

    function search_data($data) {
        $search_term = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        if (!empty($search_term)) {
            $searched_data = array_filter($data, function ($item) use ($search_term) {
                $name_matched = stripos($item['name'], $search_term) !== false;
                $email_matched = stripos($item['email'], $search_term) !== false;

                return $name_matched || $email_matched;
            });

            return $searched_data;
        }

        return $data;
    }

    function sort_data($data) {
        if (isset($_REQUEST['orderby']) && !empty($_REQUEST['orderby'])) {
            $orderby = sanitize_key($_REQUEST['orderby']);
            $order = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')) ? $_REQUEST['order'] : 'asc';

            usort($data, function ($a, $b) use ($orderby, $order) {
                if ($order === 'asc') {
                    return $a[$orderby] <=> $b[$orderby];
                } else {
                    return $b[$orderby] <=> $a[$orderby];
                }
            });
        }

        return $data;
    }


    function column_default($item, $column_name) {
        return isset($item[$column_name]) ? $item[$column_name] : '';
    }


    function column_name($item) {
        $actions = array(
            'delete' => sprintf(
                '<a href="%s">Delete</a>',
                esc_url(add_query_arg(array(
                    'page'  => $_REQUEST['page'],
                    'action' => 'delete',
                    'id' => $item['id'],
                    'paged' => isset($_REQUEST['paged']) ? $_REQUEST['paged'] : '',
                ), admin_url('admin.php')))
            ),
        );

        return sprintf(
            '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            $item['name'],
            $item['id'],
            $this->row_actions($actions)
        );
    }

    function display_search() {
        $search_term = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        ?>
        <div class="alignright actions">
            <form id="email-contact-search" method="post">
               <input type="search" id="email-search-input" name="s" value="<?= $search_term ?>" />
                <input type="submit" name="email_search" id="email-search-submit" class="button" value="Search" />
            </form>
            </div>
        <?php

    }

    public function display() {
        ?>
        <div class="tablenav top">
            <?php $this->display_search(); ?>
        </div>
        <?php
        ?>
        <form id="email-contact-bulk-action" method="post">
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php
            $this->process_bulk_action();
            parent::display();
            ?>
        </form>
        <?php
    }


    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );

        return $actions;
    }

    function process_bulk_action() {

        if ('delete' === $this->current_action() ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'email_contact';

            if (isset($_POST['id']) && is_array($_POST['id'])) {
                $ids = $_POST['id'];
                $ids = array_map('intval', $ids);

                if (!empty($ids)) {
                    $ids_placeholder = implode(',', array_fill(0, count($ids), '%d'));
                    $sql = $wpdb->prepare("DELETE FROM $table_name WHERE id IN ($ids_placeholder)", $ids);
                    $wpdb->query($sql);
                }
                ?>
                <script>location.reload();</script>
                <?php
            }

            if (isset($_REQUEST['id'])) {
                $id = intval($_REQUEST['id']);
                if ($id > 0) {
                    $sql = $wpdb->prepare("DELETE FROM $table_name WHERE id = %d", $id);
                    $wpdb->query($sql);
                }
                ?>
                <script>
                    var currentPage = '<?php echo isset($_REQUEST['page']) ? esc_js($_REQUEST['page']) : ''; ?>';
                    var urlParams = new URLSearchParams(window.location.search);
                    var pagedParam = urlParams.get('paged');
                    var pageUrl = '';

                    if (pagedParam) {
                        pageUrl = '<?php echo esc_js(admin_url('admin.php?page=')); ?>' + currentPage + '&paged=' + pagedParam;
                    } else if (currentPage) {
                        pageUrl = '<?php echo esc_js(admin_url('admin.php?page=')); ?>' + currentPage;
                    }

                    if (pageUrl) {
                        location.href = pageUrl;
                    }
                </script>
                <?php
            }

            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }
}
