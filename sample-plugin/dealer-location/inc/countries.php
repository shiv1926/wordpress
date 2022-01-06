<div class="dealer_box">
    <h1 class="wp-heading-inline">Countries</h1>
    <div class="form-wrap">
        <h2>Add New Country</h2>
        <form method="post" action="admin.php?page=countries" onsubmit="return false;">
            <div class="horizontal_form">
                <div class="fields">
                    <label for="country_name">Country Name</label>
                    <input name="country_name" id="country_name" type="text" value="">
                    <div id="country_message">&nbsp;</div>
                </div>
                <div class="fields">
                    <input type="button" name="submit" id="add_country" class="button button-primary add_location" value="Add New Country">
                    <div>&nbsp;</div>
                </div>
            </div>
        </form>
    </div>

    <?php
    global $wpdb;
    if(isset($_GET['remove']) && $_GET['remove']!='')
    {
        $sql = "SELECT * FROM ".$wpdb->prefix."dealer_states where country_id='".$_GET['remove']."'";
        $results = $wpdb->get_results($sql);
        if(count($results) > 0) 
        {
            foreach( $results as $result)
            {
                $sql = $wpdb->query("delete FROM ".$wpdb->prefix."dealer_cities where state_id='".$result->state_id."'");
            }
        }
        $wpdb->query("delete FROM ".$wpdb->prefix."dealer_states where country_id='".$_GET['remove']."'");
        $wpdb->query("delete FROM ".$wpdb->prefix."dealer_countries where country_id='".$_GET['remove']."'");
    }

    $sql = "SELECT * FROM ".$wpdb->prefix."dealer_countries order by country_name asc";
    $results = $wpdb->get_results($sql);
    echo '<table class="wp-list-table widefat fixed striped table-view-list posts">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="manage-column">Country Name</th>';
    // echo '<th class="manage-column">Action</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach( $results as $result)
    {
        echo '<tr>';
        echo '<td>'.$result->country_name.'</td>';
        // echo '<td><button class="remove" onclick="remove_country('.$result->country_id.');">Remove</button></td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</tbody>';
    echo '</table>';
    ?>
</div>