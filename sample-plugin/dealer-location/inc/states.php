<div class="dealer_box">
    <?php
    global $wpdb;
    $sql = "SELECT * FROM ".$wpdb->prefix."dealer_countries order by country_name asc ";
    $results = $wpdb->get_results($sql);
    $option = '';
    foreach( $results as $result)
    {
        $option.='<option value="'.$result->country_id.'">'.$result->country_name.'</option>';
    }
    ?>

    <h1 class="wp-heading-inline">Countries</h1>
    <div class="form-wrap">
        <h2>Add New State</h2>
        <form method="post" action="admin.php?page=states" onsubmit="return false;">
            <div class="horizontal_form">
                <div class="fields">
                    <label for="parent">Country</label>
                    <select name="country_name" id="country_name">
                        <option value="">Select Country</option>
                        <?php echo $option; ?>
                    </select>
                    <div id="country_message">&nbsp;</div>
                </div>
                <div class="fields">
                    <label for="state_name">State Name</label>
                    <input name="state_name" id="state_name" type="text" value="">
                    <div id="state_message">&nbsp;</div>
                </div>
                <div class="fields">
                    <input type="button" name="submit" id="add_state" class="button button-primary add_location" value="Add New State">
                    <div>&nbsp;</div>
                </div>
            </div>
        </form>
    </div>
    <?php
    global $wpdb;
    if(isset($_GET['remove']) && $_GET['remove']!='')
    {
        $sql = $wpdb->query("delete FROM ".$wpdb->prefix."dealer_cities where state_id='".$_GET['remove']."'");
        $sql = $wpdb->query("delete FROM ".$wpdb->prefix."dealer_states where state_id='".$_GET['remove']."'");
    }

    $sql = "SELECT * FROM ".$wpdb->prefix."dealer_states order by state_name asc";
    $results = $wpdb->get_results($sql);
    echo '<table class="wp-list-table widefat fixed striped table-view-list posts">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="manage-column">State Name</th>';
    echo '<th class="manage-column">Country Name</th>';
    echo '<th class="manage-column">Action</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach( $results as $result)
    {
        echo '<tr>';
        echo '<td>'.$result->state_name.'</td>';
        echo '<td>'.get_country_name($result->country_id).'</td>';
        echo '<td><button class="remove" onclick="remove_state('.$result->state_id.');">Remove</button></td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</tbody>';
    echo '</table>';
    ?>
</div>