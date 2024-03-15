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

class Email_Maketting_Admin_User_Table extends WP_List_Table {

    private $edit_page_rendered = false;

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

        // Áp dụng filter
        $data = $this->filter_data($data);

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
//            'id'          => __('ID'),
            'name'        => __('Name'),
            'email'       => __('Email'),
            'create_date' => __('Create Date'),
            'link'        => __('Post Link'),
            'browser'     => __('Browser'),
            'send_count'  => __('Number of sends'),
            'allow'       => __('Allow'),
            'post_id'     => __('Post or Product ID'),
            'category_id'     => __('Category ID'),
        );
    }

    function get_hidden_columns() {
        return array();
    }

    function get_sortable_columns() {
        return array(
//            'id'          => array('id', false),
            'name'        => array('name', false),
            'email'       => array('email', false),
            'create_date' => array('create_date', false),
            'browser'     => array('browser', false),
            'send_count'  => array('send_count', false),
            'post_id'     => array('post_id', false),
            'category_id' => array('category_id', false),
        );
    }

    function get_data() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'email_marketing';
        $data = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A);
        return $data;
    }

    function filter_data($data) {
        if (isset($_REQUEST['allow']) && ($_REQUEST['allow'] === '0' || $_REQUEST['allow'] === '1')) {
            $filtered_data = array_filter($data, function ($item) {
                return $item['allow'] === ($_REQUEST['allow'] === '1' ? '1' : '0');
            });

            return $filtered_data;
        }
        return $data;
    }

    function display_filter() {
        echo '<form id="email-marketing-filter" method="post">';
        echo '<div class="alignleft actions">';
        echo '<select name="allow" id="filter-by-allow">';
        echo '<option value="">All Status</option>';
        echo '<option value="1" ' . (isset($_REQUEST['allow']) && $_REQUEST['allow'] === '1' ? 'selected="selected"' : '') . '>Allow Send</option>';
        echo '<option value="0" ' . (isset($_REQUEST['allow']) && $_REQUEST['allow'] === '0' ? 'selected="selected"' : '') . '>Do Not Allow Send</option>';
        echo '</select>';
        echo '<input type="submit" name="email_filter" id="email-filter-submit" class="button" value="Filter" />';
        echo '</div>';
        echo '</form>';
    }

    function search_data($data) {
        $search_term = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        if (!empty($search_term)) {
            $searched_data = array_filter($data, function ($item) use ($search_term) {
                $name_matched = stripos($item['name'], $search_term) !== false;
                $email_matched = stripos($item['email'], $search_term) !== false;
                $link_matched = stripos($item['link'], $search_term) !== false;
                $browser_matched = stripos($item['browser'], $search_term) !== false;
                $send_count = stripos($item['send_count'], $search_term) !== false;
                $category_id = stripos($item['category_id'], $search_term) !== false;

                return $name_matched || $email_matched || $link_matched || $browser_matched || $send_count || $category_id;
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
            'edit' => sprintf(
                '<a href="%s">Edit</a>',
                esc_url(add_query_arg(array(
                    'page'  => $_REQUEST['page'],
                    'action' => 'edit',
                    'id' => $item['id'],
                    'paged' => isset($_REQUEST['paged']) ? $_REQUEST['paged'] : '',
                ), admin_url('admin.php')))
            ),
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
            <form id="email-marketing-search" method="post">
               <input type="search" id="email-search-input" name="s" value="<?= $search_term ?>" />
                <input type="submit" name="email_search" id="email-search-submit" class="button" value="Search" />
            </form>
            </div>
        <?php

    }

    public function display() {
        if ('edit' === $this->current_action()) {
            if (!$this->edit_page_rendered) {
                $this->render_edit_page();
                $this->edit_page_rendered = true;
            }
        } else {
            ?>
            <div class="tablenav top">
                <?php
                $this->display_filter();
                $this->display_search();
                ?>
            </div>
            <?php
            ?>
            <form id="email-marketing-bulk-action" method="post">
                <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
                <?php
                $this->process_bulk_action();
                parent::display();
                ?>
            </form>
            <?php
        }
    }


    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete',
            'export' => 'Export'
        );

        return $actions;
    }

    function process_bulk_action() {

        if ('delete' === $this->current_action() ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'email_marketing';

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
        if ('export' === $this->current_action()) {
            $this->export_data();
        }

        if ('edit' === $this->current_action()) {
            $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            if ($id) {
                if (isset($_POST['save_email'])) {
                    $name = isset($_POST['name']) ? $_POST['name'] : '';
                    $email = isset($_POST['email']) ? $_POST['email'] : '';
                    $link = isset($_POST['link']) ? $_POST['link'] : '';
                    $allow = isset($_POST['allow']) ? $_POST['allow'] : '';
                    $send_count = isset($_POST['send_count']) ? $_POST['send_count'] : '';
                    $browser = isset($_POST['browser']) ? $_POST['browser'] : '';
                    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
                    $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';

                    global $wpdb;
                    $table_name = $wpdb->prefix . 'email_marketing';

                    $wpdb->update(
                        $table_name,
                        array(
                            'name' => $name,
                            'email' => $email,
                            'link' => trim($link),
                            'allow' => $allow,
                            'send_count' => $send_count,
                            'browser' => $browser,
                            'post_id' => $post_id,
                            'category_id' => $category_id
                        ),
                        array('id' => $id)
                    );
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
                } else {
                    if (!$this->edit_page_rendered && 'edit' === $this->current_action()) {
                        $this->render_edit_page();
                        $this->edit_page_rendered = true;
                    }

                    return;
                }
            }
        }
    }


    function render_edit_page() {
        if (isset($_REQUEST['id'])) {
            $id = $_REQUEST['id'];

            // Lấy thông tin email từ cơ sở dữ liệu
            global $wpdb;
            $table_name = $wpdb->prefix . 'email_marketing';
            $email_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);

            if ($email_data) {
                $post_id = $email_data['post_id'];
                $category_id = $email_data['category_id'];
                $name = $email_data['name'];
                $email = $email_data['email'];
                $link = $email_data['link'];
                $send_count = $email_data['send_count'];
                $allow = $email_data['allow'];
                $browser = $email_data['browser'];
                ?>
                <div class="wrap">
                    <h1>Edit Email Data</h1>

                    <!-- Form HTML with CSS classes -->
                    <div class="email-form-container">
                        <form method="post" action="<?php echo admin_url('admin.php?page=email-maketting&action=edit&id=' . $id . '&paged=' . $_REQUEST['paged']); ?>">
                            <label for="post_id">Post ID:</label>
                            <input type="number" name="post_id" id="post_id" value="<?php echo esc_attr($post_id); ?>"><br><br>

                            <label for="category_id">Category ID:</label>
                            <input type="number" name="category_id" id="category_id" value="<?php echo esc_attr($category_id); ?>"><br><br>

                            <label for="name">Name:</label>
                            <input type="text" name="name" id="name" value="<?php echo esc_attr($name); ?>"><br><br>

                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" value="<?php echo esc_attr($email); ?>"><br><br>

                            <label for="link">Post URL:</label>
                            <input type="text" name="link" id="link" value="<?php echo esc_attr($link); ?>"><br><br>

                            <label for="send_count">Number or sent:</label>
                            <input type="number" name="send_count" id="send_count" value="<?php echo esc_attr($send_count); ?>"><br><br>

                            <label for="allow">Allow:</label>
                            <select name="allow">
                                <option value="1" <?php selected($allow, '1'); ?>>Yes</option>
                                <option value="0" <?php selected($allow, '0'); ?>>No</option>
                            </select><br><br>

                            <label for="browser">Browser:</label>
                            <input type="text" name="browser" id="browser" value="<?php echo esc_attr($browser); ?>"><br><br>

                            <input type="submit" name="save_email" value="Save">
                            <?php wp_nonce_field('email_edit_action', 'email_edit_nonce'); ?>
                        </form>
                    </div>
                </div>
                <?php
            }
        }
    }

    function export_data() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'email_marketing';
        $data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

        $csv_data = '';
        $header = array_keys($data[0]);
        $csv_data .= '"' . implode('","', $header) . '"' . "\n";
        foreach ($data as $row) {
            $csv_data .= '"' . implode('","', $row) . '"' . "\n";
        }

        $folder_name = 'email_file';
        $upload_dir = wp_upload_dir();
        $folder_path = $upload_dir['basedir'] . '/' . $folder_name;

        // Tạo thư mục riêng nếu chưa tồn tại
        wp_mkdir_p($folder_path);

        $filename = 'email_data.csv';
        $csv_file_path = $folder_path . '/' . $filename;
        file_put_contents($csv_file_path, $csv_data);

        $download_url = $upload_dir['baseurl'] . '/' . $folder_name . '/' . $filename;

        wp_schedule_single_event(time() + 30, 'delete_csv_file', array($csv_file_path));

        ?>
        <script>
            window.open("<?= $download_url ?>");
            location.reload();
        </script>
        <?php
    }

    function delete_csv_file($csv_file_path) {
        if (file_exists($csv_file_path)) {
            unlink($csv_file_path);
        }
    }
}
