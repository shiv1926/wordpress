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

    <h1 class="wp-heading-inline">Cities</h1>
    <div class="form-wrap">
        <h2>Add New City</h2>
        <form method="post" action="admin.php?page=cities" onsubmit="return false;">
            <div class="horizontal_form cities">
                <div class="fields">
                    <label for="parent">Country</label>
                    <select name="country_name" id="country_name" onchange="get_states_by_country(this);">
                        <option value="">Select Country</option>
                        <?php echo $option; ?>
                    </select>
                    <div id="country_message">&nbsp;</div>
                </div>
                <div class="fields">
                    <label for="parent">State</label>
                    <select name="state_name" id="state_name">
                        <option value="">Select State</option>
                    </select>
                    <div id="state_message">&nbsp;</div>
                </div>
                <div class="fields">
                    <label for="city_name">City Name</label>
                    <input name="city_name" id="city_name" type="text" value="">
                    <div id="city_message">&nbsp;</div>
                </div>
                <div class="fields">
                    <input type="button" name="submit" id="add_city" class="button button-primary add_location" value="Add New City">
                    <div>&nbsp;</div>
                </div>
            </div>
        </form>
    </div>
    <?php
    global $wpdb;
    if(isset($_GET['remove']) && $_GET['remove']!='')
    {
        $sql = $wpdb->query("delete FROM ".$wpdb->prefix."dealer_cities where city_id='".$_GET['remove']."'");
    }

    $sql = "SELECT * FROM ".$wpdb->prefix."dealer_cities order by city_name asc";
    $results = $wpdb->get_results($sql);
    echo '<table class="wp-list-table widefat fixed striped table-view-list posts">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="manage-column">City Name</th>';
    echo '<th class="manage-column">Action</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach( $results as $result)
    {
        echo '<tr>';
        echo '<td>'.$result->city_name.'</td>';
        echo '<td><button class="remove" onclick="remove_city('.$result->city_id.');">Remove</button></td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</tbody>';
    echo '</table>';
    ?>
</div>