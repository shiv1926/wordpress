<?php
add_filter('acf/location/rule_types', 'acf_location_rules_types');
function acf_location_rules_types( $choices )
{
    $choices['Other']['taxonomy_depth'] = 'Taxonomy Depth';
    return $choices;
}

add_filter('acf/location/rule_operators', 'acf_location_rules_operators');
function acf_location_rules_operators( $choices )
{
    $choices['<'] = 'is less than';
    $choices['>'] = 'is greater than';
    return $choices;
}

add_filter('acf/location/rule_values/taxonomy_depth', 'acf_location_rules_values_taxonomy_depth');
function acf_location_rules_values_taxonomy_depth( $choices )
{
    $choices[0] = 'Parent';
    $choices[1] = 'Child';
    return $choices;
}

add_filter('acf/location/rule_match/taxonomy_depth', 'acf_location_rules_match_taxonomy_depth', 10, 3);
function acf_location_rules_match_taxonomy_depth( $match, $rule, $options )
{
    $depth = (int) $rule['value'];
    if(isset($_GET['page']) && $_GET['page'] == "shopp-categories" && isset($_GET['id'])) {
        $term_depth = (int) count(get_ancestors($_GET['id'], 'shopp_category'));
    } elseif(isset($_GET['taxonomy']) && isset($_GET['tag_ID'])) {
        $term_depth = (int) count(get_ancestors($_GET['tag_ID'], $_GET['taxonomy']));
    }

    if($rule['operator'] == "==") {
        $match = ($term_depth == $depth);
    } elseif($rule['operator'] == "!=") {
        $match = ($term_depth != $depth);
    } elseif($rule['operator'] == "<") {
        $match = ($term_depth < $depth);
    } elseif($rule['operator'] == ">") {
        $match = ($term_depth > $depth);
    }
    return $match;
}


/* Product Category order for home  */
function wh_taxonomy_add_new_meta_field() {
    ?>
    <div class="form-field">
        <label for="category_roof_description"><?php _e('Category Roof Description', 'wh'); ?></label>
        <textarea name="category_roof_description" rows="5" id="category_roof_description"></textarea>
    </div>
  <?php
}
function wh_taxonomy_edit_meta_field($term) {
   $term_id = $term->term_id;
   
   $args = array(
        'name' => $name,
        'parent' => 16
       );
       $terms = get_terms( 'product_cat', $args );
       $carports_sub_cat=array();
       foreach ($terms as $term) {
          $carports_sub_cat[] .= $term->term_id;
       }
$category_roof_description = get_term_meta($term_id, 'category_roof_description', true);
   
if (in_array($term_id, $carports_sub_cat))
  {
 ?>
<tr class="form-field">
        <th scope="row" valign="top"><label for="category_roof_description"><?php _e('Category Roof Description', 'wh'); ?></label></th>
        <td>
  <textarea name="category_roof_description" rows="5" id="category_roof_description"><?php echo wc_sanitize_textarea($category_roof_description) ? wc_sanitize_textarea($category_roof_description) : ''; ?></textarea>
<p class="description"><?php _e('Carport Category Roof Description', 'wh'); ?></p>
        </td>
    </tr>
  <?php
}

}
add_action('product_cat_add_form_fields', 'wh_taxonomy_add_new_meta_field', 10, 1);
add_action('product_cat_edit_form_fields', 'wh_taxonomy_edit_meta_field', 10, 1);

function wh_save_taxonomy_custom_meta($term_id) {
  if(isset($_POST['category_roof_description'])){
       $category_roof_description = filter_input(INPUT_POST, "category_roof_description", FILTER_DEFAULT);
       update_term_meta($term_id, 'category_roof_description', $category_roof_description);  
     }
}
add_action('edited_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
add_action('create_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);