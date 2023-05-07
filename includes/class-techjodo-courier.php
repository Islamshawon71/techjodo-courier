<?php 

// Add a submenu page to the WooCommerce menu
function add_custom_submenu_page() {
    add_submenu_page(
        'woocommerce', // Parent slug
        __( 'Techjod Courier', 'techjodo-courier' ), // Page title
        __( 'Techjod Courier', 'techjodo-courier' ), // Menu title
        'manage_options', // Capability required to access the menu page
        'techjodo_courier', // Menu slug
        'techjodo_courier_callback' // Callback function to render the menu page
    );
}
add_action( 'admin_menu', 'add_custom_submenu_page' );

// Callback function to render the custom submenu page
function techjodo_courier_callback() {
    require plugin_dir_path( __FILE__ ) . 'class-techjodo-courier-options.php';
}

// ADDING 2 NEW COLUMNS WITH THEIR TITLES (keeping "Total" and "Actions" columns at the end)
add_filter( 'manage_edit-shop_order_columns', 'techjodo_courier_shop_order_column', 20 );
function techjodo_courier_shop_order_column($columns)
{
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
			if(get_option( 'steadfast_status' ) == 'Active'){
            	$reordered_columns['steadfast'] = __( 'Steadfast','techjodo-courier');
			}
        }
    }
    return $reordered_columns;
}

// Adding custom fields meta data for each new column (example)
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id )
{
    switch ( $column )
    {
        case 'steadfast' :
            // Get custom post meta data
            $steadfast_consignment_id = get_post_meta( $post_id, 'steadfast_consignment_id', true );
            if(!empty($steadfast_consignment_id))
                echo '<a target="_blank" href="https://steadfast.com.bd/consignment/'.$steadfast_consignment_id.'">'.$steadfast_consignment_id.'</a>';
            else
                echo '<div><button type="button" class="update_to_steadfast" value="'.$post_id.'">Update to Steadfast</button></div>';

            break; 
    }
}

function enqueue_script_for_orders_page( $hook ) {
	wp_enqueue_style( 'techjodo-courier', plugin_dir_url( __FILE__ ) . '../css/techjodo-courier-styles.css' );
	wp_enqueue_script(
		'techjodo-courier-script', // Script handle
		plugin_dir_url( __FILE__ ) . '../js/techjodo-courier-script.js', // Script source
		array( 'jquery' ), // Dependencies
		'1.0', // Script version
		true // Load in footer
	);
}
add_action( 'admin_enqueue_scripts', 'enqueue_script_for_orders_page' );

// Callback function to update order status
function update_order_status() {
    // Get order ID and new status from AJAX data
    $order_id = $_POST['order_id'];

    $order = wc_get_order( $order_id );


	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://portal.steadfast.com.bd/api/v1/create_order',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array(
			'invoice' => $order_id,
			'recipient_name' => $order->get_billing_first_name() . $order->get_billing_last_name(),
			'recipient_phone' => $order->get_billing_phone(),
			'recipient_address' => $order->get_billing_address_1() . $order->get_billing_address_2(),
			'cod_amount' => $order->get_total(),
			'note' => $order->get_customer_note()
		),
		CURLOPT_HTTPHEADER => array(
			'Api-Key: ' .  get_option( 'steadfast_api_key' ),
			'Secret-Key: ' .  get_option( 'steadfast_secret_key' )
		),
	));
	$response = json_decode(curl_exec($curl), true);
	curl_close($curl);
	
	if ($response['status'] == 200) {
		update_post_meta( $order_id, 'steadfast_consignment_id', $response['consignment']['consignment_id'] );
		$order->add_order_note('Steadfast Consinment ID : ' . $response['consignment']['consignment_id'] );
		echo $response['consignment']['consignment_id'];
	} else {
		$order->add_order_note( json_encode($response['errors']) );
	}
    wp_die();
}

// Add AJAX action for logged-in users
add_action( 'wp_ajax_update_order_status', 'update_order_status' );

// Add AJAX action for non-logged-in users
add_action( 'wp_ajax_nopriv_update_order_status', 'update_order_status' );



add_action('add_meta_boxes', 'steadfast_order_details_metabox');
function steadfast_order_details_metabox() {
  add_meta_box(
    'order_details_metabox',
    'SteadFast Courier',
    'steadfast_metabox_callback',
    'shop_order',
    'normal',
    'high'
  );
}

// Callback function to generate metabox contents
function steadfast_metabox_callback($post) {
  $order_id = $post->ID;
  $steadfast_consignment_id = get_post_meta( $order_id, 'steadfast_consignment_id', true );
	if(!$steadfast_consignment_id){ ?>
		<div><button type="button" class="update_to_steadfast" value="<?php echo $order_id; ?>">Update to Steadfast</button>
		<a class="manualAdd" href="#">Add Manually</a>
		<div class="manualitem">
			<input type="text" name="steadfast_consignment_id" value="">
		</div>
		</div>
	<?php }else{ ?>
		<a target="_blank" href="https://steadfast.com.bd/consignment/<?php echo $steadfast_consignment_id;?>"><?php echo $steadfast_consignment_id;?></a>
	<?php }
  ?>
  
  <?php
}

// Save metabox data
add_action('save_post', 'order_details_metabox_save');
function order_details_metabox_save($post_id) {
  if (isset($_POST['_order_total'])) {
    update_post_meta($post_id, '_order_total', sanitize_text_field($_POST['_order_total']));
  }
  if (isset($_POST['_status'])) {
    update_post_meta($post_id, '_status', sanitize_text_field($_POST['_status']));
  }
}




// Callback function to update order status
function update_multiple_order_to_steadfast_callback() {
    // Get order ID and new status from AJAX data

	$order_ids = $_POST['order_ids'];

    // Iterate through each order ID and update the order status
    foreach ($order_ids as $order_id) {

		$order = wc_get_order( $order_id );

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://portal.steadfast.com.bd/api/v1/create_order',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array(
				'invoice' => $order_id,
				'recipient_name' => $order->get_billing_first_name() . $order->get_billing_last_name(),
				'recipient_phone' => $order->get_billing_phone(),
				'recipient_address' => $order->get_billing_address_1() . $order->get_billing_address_2(),
				'cod_amount' => $order->get_total(),
				'note' => $order->get_customer_note()
			),
			CURLOPT_HTTPHEADER => array(
				'Api-Key: ' .  get_option( 'steadfast_api_key' ),
				'Secret-Key: ' .  get_option( 'steadfast_secret_key' )
			),
		));

		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);
		
		if ($response['status'] == 200) {
			update_post_meta( $order_id, 'steadfast_consignment_id', $response['consignment']['consignment_id'] );
			$order->add_order_note('Steadfast Consinment ID : ' . $response['consignment']['consignment_id'] );
			echo $response['consignment']['consignment_id'];
		} else {
			$order->add_order_note( json_encode($response['errors']) );
		}
	}

    wp_die();
}

// Add AJAX action for logged-in users
add_action( 'wp_ajax_update_multiple_order_to_steadfast', 'update_multiple_order_to_steadfast_callback' );

// Add AJAX action for non-logged-in users
add_action( 'wp_ajax_nopriv_update_multiple_order_to_steadfast', 'update_multiple_order_to_steadfast_callback' );

