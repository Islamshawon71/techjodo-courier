jQuery(document).ready(function(){
    var button = '<button type="button" class="page-title-action multiple_steadfast_upload">Update to Steadfast</button>';
    jQuery(button).insertAfter('.page-title-action');
});


jQuery('.update_to_steadfast').click(function(){
    let order_id = jQuery(this).val();

    var $btn = jQuery(this);
    var originalText = $btn.html();
    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
    $btn.prop('disabled', true);

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'update_order_status',
            order_id: order_id
        },
        success: function(response) {
            if (response) {
                $btn.closest( "div" ).empty().append('<a target="_blank" href="https://steadfast.com.bd/consignment/'+response+'">'+response+'</a>');
            } else {
                alert('Error updating order status.');
                $btn.html(originalText);
                \$btn.prop('disabled', false);
            }
            
        },
        error: function(xhr, status, error) {
            $btn.html(originalText);
            $btn.prop('disabled', false);
            alert('Error: ' + error);
        }
    });

});



jQuery(document).on("click",".multiple_steadfast_upload",function() {

    // event.preventDefault();

    var order_ids = [];

    jQuery('input[name="post[]"]:checked').each(function() {
      order_ids.push(jQuery(this).val());
    });

    var $btn = jQuery(this);
    var originalText = $btn.html();
    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
    $btn.prop('disabled', true);

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'update_multiple_order_to_steadfast',
            order_ids: order_ids
        },
        success: function(response) {
            if (response) {
                alert('Updating order status.');
            } else {
                alert('Error updating order status.');
            }
            $btn.html(originalText);
            $btn.prop('disabled', false);
        },
        error: function(xhr, status, error) {
            $btn.html(originalText);
            $btn.prop('disabled', false);
            alert('Error: ' + error);
        }
    });

});



jQuery('.manualitem').hide();

jQuery('.manualAdd').click(function() {
    jQuery('.manualitem').toggle();
    jQuery('.update_to_steadfast').toggle();
    
});