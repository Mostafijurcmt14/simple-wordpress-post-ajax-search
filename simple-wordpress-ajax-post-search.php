

function my_theme_scripts() {
    wp_enqueue_script('jquery'); // Ensure jQuery is loaded
    wp_localize_script('jquery', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'my_theme_scripts');



function search_shortcode(){
	?>
<input type="text" id="search-input" name="s" placeholder="Search...">
<div id="search-results"></div>
	<?php
}
add_shortcode('test_shortcode','search_shortcode');

	
	
function my_ajax_search() {
    $keyword = wp_kses_post($_POST['keyword']);
    $search_query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        's' => $keyword,
    ));

    if ($search_query->have_posts()) :
        while ($search_query->have_posts()) : $search_query->the_post();
            echo '<div class="search-result">';
            echo '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
            echo '<p>' . get_the_excerpt() . '</p>';
            echo '</div>';
        endwhile;
    else :
        echo 'No posts found.';
    endif;

    wp_reset_postdata();
    die();
}
add_action('wp_ajax_my_search', 'my_ajax_search');
add_action('wp_ajax_nopriv_my_search', 'my_ajax_search');


function custom_ajax_f(){
	?>
<script>
jQuery(document).ready(function($) {
    $('#search-input').keyup(function() {
        var searchValue = $(this).val();
        if(searchValue) {
            $.ajax({
                type: 'POST',
                url: MyAjax.ajaxurl,
                data: { 
                    'action': 'my_search', 
                    'keyword': searchValue 
                },
                success: function(response) {
                    $('#search-results').html(response);
                    return false;
                }
            });
        } else {
            $('#search-results').html('');
        }
    });
});
	
</script>
	<?php
}
add_action('wp_footer','custom_ajax_f', 999);

