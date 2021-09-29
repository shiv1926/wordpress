jQuery(document).ready(function(){
    jQuery("#add_country").click(function(){
        var data_country = {
            'action': 'add_country',
            'country_name': jQuery("#country_name").val()
        };

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data_country,
            async: true,
            dataType: "json",
            success: function (response) {
                if (response.status=='error') {
                    jQuery("#country_message").html('<p class="error_message">This field can not be empty.</p>');
                } else if (response.status == 'already') {
                    jQuery("#country_message").html('<p class="error_message">This name already exist.</p>');
                } else {
                    window.location.href = 'admin.php?page=countries&msg=success';
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });

    jQuery("#add_state").click(function () {
        var data_state = {
            'action': 'add_state',
            'country_name': jQuery("#country_name").val(),
            'state_name': jQuery("#state_name").val(),
        };

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data_state,
            async: true,
            dataType: "json",
            success: function (response) {
                if (response.status == 'error') {
                    jQuery(response.err_obj).each(function(index, value) {
                        console.log(response.err_obj);
                        console.log(index +" : "+ value);
                        jQuery("#" + value.field).html('<p class="error_message">' + value.message+'</p>');
                    })
                } else {
                    window.location.href = 'admin.php?page=states&msg=success';
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
        return false;
    });

    jQuery("#add_city").click(function () {
        var data_city = {
            'action': 'add_city',
            'country_name': jQuery("#country_name").val(),
            'state_name': jQuery("#state_name").val(),
            'city_name': jQuery("#city_name").val(),
        };

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data_city,
            async: true,
            dataType: "json",
            success: function (response) {
                if (response.status == 'error') {
                    jQuery(response.err_obj).each(function (index, value) {
                        console.log(response.err_obj);
                        console.log(index + " : " + value);
                        jQuery("#" + value.field).html('<p class="error_message">' + value.message + '</p>');
                    })
                } else {
                    window.location.href = 'admin.php?page=cities&msg=success';
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
});

function remove_country(country_id) {
    if(confirm('Remove country and their states and cities ?')) {
        window.location.href = 'admin.php?page=countries&remove=' + country_id;
    } else {
        return false;
    }
}

function remove_state(state_id) {
    if (confirm('Remove States and their cities ?')) {
        window.location.href = 'admin.php?page=states&remove=' + state_id;
    } else {
        return false;
    }
}

function remove_city(city_id) {
    if (confirm('Remove city ?')) {
        window.location.href = 'admin.php?page=cities&remove=' + city_id;
    } else {
        return false;
    }
}

function get_states_by_country(option)
{
    var get_states_by_country = {
        'action': 'get_states_by_country',
        'country_id': option.value
    }
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: get_states_by_country,
        async: true,
        dataType: "json",
        success: function (response) {
            jQuery("#state_name").find('option').remove().end().append(response.opt_list);
            jQuery("#city_name").find('option').remove().end().append('<option value="">Select City</option');
        },
        error: function (error) {
            console.log(error);
        }
    });
}

function get_cities_by_state(option)
{
    var get_cities_by_state = {
        'action': 'get_cities_by_state',
        'state_id': option.value
    }
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: get_cities_by_state,
        async: true,
        dataType: "json",
        success: function (response) {
            jQuery("#city_name").find('option').remove().end().append(response.opt_list);
        },
        error: function (error) {
            console.log(error);
        }
    });
}