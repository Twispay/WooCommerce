<?php
/**
 * Twispay Custom Transaction Table Class
 *
 * Custom Transaction Class on the Administrator dashboard
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 * @version  1.0.8
 */

// Add the copy of the WP_List_Table class. We made a copy because the class is private.
require_once TWISPAY_PLUGIN_DIR . 'includes/class-ma-list-table.php';

/**
 * Base custom class for displaying a list of items in an ajaxified HTML table.
 */
class Twispay_TransactionTable extends Twispay_Tw_List_Table {

    protected $tw_lang;

    /**
     * Constructor.
     *
     * The child class should call this constructor from its own constructor to override
     * the default $args.
     *
     * @since  3.1.0
     * @access public
     *
     * @param array|string $args {
     *     Array or string of arguments.
     *
     *     @type string $plural   Plural value used for labels and the objects being listed.
     *                            This affects things such as CSS class-names and nonces used
     *                            in the list table, e.g. 'posts'. Default empty.
     *     @type string $singular Singular label for an object being listed, e.g. 'post'.
     *                            Default empty
     *     @type bool   $ajax     Whether the list table supports Ajax. This includes loading
     *                            and sorting data, for example. If true, the class will call
     *                            the _js_vars() method in the footer to provide variables
     *                            to any scripts handling Ajax events. Default false.
     *     @type string $screen   String containing the hook name used to determine the current
     *                            screen. If left null, the current screen will be automatically set.
     *                            Default null.
     * }
     */
    function __construct( $tw_lang ) {
        global $status, $page;

        $this->tw_lang = $tw_lang;

        parent::__construct( array(
            'singular'  => 'notification',
            'plural'    => 'notifications',
            'ajax'      => false
        ) );
    }

    /**
     * Displays the search box.
     *
     * @since  3.1.0
     * @access public
     *
     * @param string $text     The 'submit' button label.
     * @param string $input_id ID attribute value for the search input field.
     */
    public function search_box( $text, $input_id ) {
        if ( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] ) {
            echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
        }
        if ( isset( $_REQUEST['order'] ) && $_REQUEST['order'] ) {
            echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
        }
        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] ) {
            echo '<input type="hidden" name="status" value="' . esc_attr( $_REQUEST['status'] ) . '" />';
        }

        ?>
            <p class="search-box">
                <input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php _admin_search_query(); ?>" />
                <?php submit_button( $text, 'button', '', false, array(  'id' => 'search-submit' ) ); ?>
            </p>
        <?php
    }

    /**
     * Custom function that retrive the number of Transactions
     *
     * @param Object $wpdb         Wordpress refference to database.
     */
    private function get_all_count( $wpdb ) {
        $table_name = $wpdb->prefix . 'twispay_tw_transactions  ';

        $wpdb->get_results( "SELECT id_tw_transactions FROM $table_name" );

        return $wpdb->num_rows;
    }

    /**
     * Get an associative array ( id => link ) with the list
     * of views available on this table.
     *
     * @since  3.1.0
     * @access protected
     *
     * @return array
     */
    function get_views() {
        global $wpdb;

        $views = array();
        $current = ( ! empty( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'all' );

        //All link
        $class = ( $current == 'all' ? ' class="current"' :'' );
        $all_url = remove_query_arg( 'status' );
        $views['all'] = "<a href='{$all_url }' {$class} >" . $this->tw_lang['transaction_list_all_views'] . "<span class='view_count'> ( " . $this->get_all_count( $wpdb ) . " )</span></a>";

        return $views;
    }

    /**
     * Custom modification on name column
     */
    function column_id_tw_transactions( $item ) {
        $actions = array(
            'refund'                 => sprintf( '<a href="?page=%s&action=%s&payment_ad=%s">' . $this->tw_lang['transaction_list_refund_title'] . '</a>', $_REQUEST['page'], 'refund_payment', $item['transactionId'] ),
            'cancel_recurring'       => sprintf( '<a href="?page=%s&action=%s&order_ad=%s">' . $this->tw_lang['transaction_list_recurring_title'] . '</a>', $_REQUEST['page'], 'recurring_payment', $item['orderId'] )
        );

        if ( $item['status'] == 'complete-ok' ) {
            return sprintf( '%1$s %2$s', $item['id_tw_transactions'], $this->row_actions( $actions ) );
        }
        else {
            return $item['id_tw_transactions'];
        }
    }

    /**
     *
     * @param object $item
     * @param string $column_name
     */
    function column_default( $item, $column_name ) {
        global $woocommerce;

        switch ( $column_name ) {
            case 'id_tw_transactions':
            case 'customer_name':
            case 'transactionId':
            case 'status':
            case 'checkout_url':
                return $item[$column_name];
            case 'id_cart':
                return '#' . $item[$column_name];
        }
    }

    /**
     *
     * @param object $item
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id_tw_transactions']
        );
    }

    /**
     * Get a list of columns. The format is:
     * 'internal-name' => 'Title'
     *
     * @since 3.1.0
     * @access public
     * @abstract
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'                  => '<input type="checkbox" />',
            'id_tw_transactions'  => $this->tw_lang['transaction_list_id'],
            'id_cart'             => $this->tw_lang['transaction_list_id_cart'],
            'customer_name'       => $this->tw_lang['transaction_list_customer_name'],
            'transactionId'       => $this->tw_lang['transaction_list_transactionId'],
            'status'              => $this->tw_lang['transaction_list_status'],
            'checkout_url'        => $this->tw_lang['transaction_list_checkout_url']
        );
        return $columns;
    }

    /**
     * Get a list of sortable columns. The format is:
     * 'internal-name' => 'orderby'
     * or
     * 'internal-name' => array( 'orderby', true )
     *
     * The second format will make the initial sorting order be descending
     *
     * @since 3.1.0
     * @access protected
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'id_tw_transactions'  => array( 'id_tw_transactions', false ),
            'customer_name'       => array( 'customer_name', false ),
            'transactionId'       => array( 'transactionId', false ),
            'status'              => array( 'status', false )
        );
        return $sortable_columns;
    }

    /**
     * Prepares the list of items for displaying.
     * @uses TW_List_Table::set_pagination_args()
     *
     * @since 3.1.0
     * @access public
     * @abstract
     */
    function prepare_items() {
        global $wpdb;

        $s = ( isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : 'all' );
        $ma_status = ( isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'all' );
        $order_by = ( isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '' );
        $order_how = ( isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'asc' );

        $transaction = $wpdb->prefix . "twispay_tw_transactions";

        $per_page = 10;
        $query =
                "SELECT
                    tr.id_tw_transactions,
                    tr.id_cart,
                    ( SELECT display_name FROM " . $wpdb->prefix . "users WHERE ID = REPLACE( tr.identifier, '_', '' ) ) as customer_name,
                    tr.transactionId,
                    tr.orderId,
                    tr.status,
                    tr.checkout_url
                FROM $transaction tr
                ";

        if ( $s != 'all' ) {
            $query .= " WHERE tr.id_cart LIKE '%$s%'";
        }

        // Order by functionality. Works on all columns.
        if ( $order_by != '' ) {
            switch ( $order_by ) {
                case 'id_tw_transactions':
                case 'customer_name':
                case 'transactionId':
                case 'status':
                    $query .= " ORDER BY $order_by $order_how";
            }
        }
        else {
            $query .= " ORDER BY tr.id_tw_transactions desc";
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->process_bulk_action();
        $data = $wpdb->get_results( $query, ARRAY_A );

        // Set pagination to page.
        $current_page = $this->get_pagenum();
        $total_items = count( $data ) ;
        $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items'  => $total_items,
            'per_page'     => $per_page,
            'total_pages'  => ceil( $total_items / $per_page )
        ) );
    }
}
