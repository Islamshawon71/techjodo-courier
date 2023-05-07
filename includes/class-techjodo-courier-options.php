<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 **/


?>
<style>
.techjodo-courier .tab{
    background-color:white;
    padding: 10px;

}
.techjodo-courier .nav-tab-wrapper {
    padding-top:0px;
}
.techjodo-courier .nav-tab-wrapper a{
    margin-left: 0px;
    margin-right: 0.5em;
}
.techjodo-courier input,.techjodo-courier select {
    width: 100%;
    max-width:100%;
}

</style>
<div class="wrap techjodo-courier">
		
		<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
			<a href="<?php echo admin_url('admin.php?page=techjodo_courier&tab=steadfast')?>" class="nav-tab nav-tab-active">Steadfast</a>
            <!-- <a href="<?php echo admin_url('admin.php?page=techjodo_courier&tab=pathao')?>" class="nav-tab ">Pathao</a> -->
        </nav>
       <div class="steadfast-tab tab">
       <form method="post" action="#" enctype="multipart/form-data">
            <h2>Steadfast API</h2>
                Get api ke from Steadfast Website <a target="_blank" href="https://steadfast.com.bd/user/api/documentation">Click here</a> </p>
<?php
if(isset($_POST['steadfast_save'])){
    update_option('steadfast_status', $_POST['steadfast_status']);
    update_option('steadfast_api_key', $_POST['steadfast_api_key']);
    update_option('steadfast_secret_key', $_POST['steadfast_secret_key']);
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Steadfast API Settings saved successfully.', 'textdomain' ); ?></p>
    </div>
    <?php
}
?>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="woocommerce_store_address">Steadfast Status<span class="woocommerce-help-tip"></span></label>
                        </th>
                        <td class="forminp forminp-text">
                            <select name="steadfast_status" id="steadfast_status">
                                <option value="Active" <?php if(get_option( 'steadfast_status' ) == 'Active') { echo 'selected'; }?>>Active</option>
                                <option value="Inactive" <?php if(get_option( 'steadfast_status' ) == 'Inactive') { echo 'selected'; }?>>Inactive</option>
                            </select>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="steadfast_api_key">Stadfast Api-Key<span class="steadfast_api_key"></span></label>
                        </th>
                        <td class="forminp forminp-text">
                        <input type="text" class="form" id="" name="steadfast_api_key" placeholder="Enter Stadfast Api Key" value="<?php echo get_option( 'steadfast_api_key' ) ?>">
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="steadfast_secret_key">Stadfast Secret-Key<span class="steadfast_secret_key"></span></label>
                        </th>
                        <td class="forminp forminp-text">
                        <input type="text" class="form" id="" name="steadfast_secret_key" placeholder="Enter Stadfast Secret Key" value="<?php echo get_option( 'steadfast_secret_key', ) ?>">
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <button name="steadfast_save" class="button-primary" type="submit" value="Save changes">Save changes</button>
                <?php wp_nonce_field( '_wpnonce', '_wpnonce' ); ?>
            </p>
        </form>
       </div>
</div>
