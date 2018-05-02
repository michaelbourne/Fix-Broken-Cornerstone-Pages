<?php
/*
Plugin Name: Fix Broken Cornerstone Pages
Plugin URI: https://ursa6.com
Description: This will fix broken Cornerstoen pages, as long as the content (shortcodes) are intact.
Author: Michael Bourne
Version: 0.1
Author URI: https://ursa6.com
*/

add_action( 'admin_menu', 'fix_cs_pages_plugin_add_admin_menu' );
add_action( 'admin_init', 'fix_cs_pages_confirm_init' );


function fix_cs_pages_plugin_add_admin_menu(  ) { 

    add_options_page( 'Fix Cornerstone Pages', 'Fix Cornerstone Pages', 'manage_options', 'fix_cs_pages', 'fix_cs_pages_plugin_options_page' );

}


function fix_cs_pages_confirm_init(  ) { 

    register_setting( 'pluginPage', 'fix_cs_pages_confirm' );
    register_setting( 'pluginPage', 'fix_cs_pages_post_id' );

    add_settings_section(
        'fix_cs_pages_plugin_pluginPage_section', 
        __( '', 'global-blocks-cornerstone' ), 
        'fix_cs_pages_confirm_section_callback', 
        'pluginPage'
    );

    add_settings_field( 
        'fix_cs_pages_plugin_checkbox_field_0', 
        __( 'Check to confirm your choice', 'global-blocks-cornerstone' ), 
        'fix_cs_pages_plugin_checkbox_field_0_render', 
        'pluginPage', 
        'fix_cs_pages_plugin_pluginPage_section' 
    );

    add_settings_field( 
        'fix_cs_pages_plugin_post_id', 
        __( 'Enter the post ID', 'global-blocks-cornerstone' ), 
        'fix_cs_pages_plugin_post_id_render', 
        'pluginPage', 
        'fix_cs_pages_plugin_pluginPage_section' 
    );

}


function fix_cs_pages_plugin_checkbox_field_0_render(  ) { 
?>

        <input type='checkbox' name='fix_cs_pages_confirm[fix_cs_pages_plugin_checkbox_field_0]' value='1'>
        <?php

}

function fix_cs_pages_plugin_post_id_render(  ) { 

    $options = get_option( 'fix_cs_pages_post_id' );

    if(empty($options['fix_cs_pages_plugin_post_id'])):
?>
        <input type='number' name='fix_cs_pages_post_id[fix_cs_pages_plugin_post_id]' value='<?php $options['fix_cs_pages_plugin_post_id']; ?>' />
<?php

    endif;

}


function fix_cs_pages_confirm_section_callback(  ) { 

    $option1 = get_option( 'fix_cs_pages_confirm' );
    $option2 = get_option( 'fix_cs_pages_post_id' );

    if($option1['fix_cs_pages_plugin_checkbox_field_0'] == 1 && get_post_status($option2['fix_cs_pages_plugin_post_id'])) :

        if( class_exists(Cornerstone_Content)) {

            $post = get_post( $option2['fix_cs_pages_plugin_post_id'] );
            $cs = new Cornerstone_Content($post);
            $cs->save();

        }

        delete_option( 'fix_cs_pages_confirm' );
        delete_option( 'fix_cs_pages_post_id' );


        echo '<h4 style="color: blue;">Post ID: ' . $option2['fix_cs_pages_plugin_post_id'] . ' has been repaired (maybe)! Repair another page?</h4>';

    endif;

        echo __( 'This is a one way street, no going back. Make a database backup first, ALWAYS!', 'global-blocks-cornerstone' );

    
}


function fix_cs_pages_plugin_options_page(  ) { 

    ?>
    <form action='options.php' method='post'>

        <h1>Fix Broken Cornerstone Pages</h1>

        <h3>This form might fix broken CS pages, so long as the post meta is intact.</h3>
        <p>Examples: <br>1) You accidently edit the page via the text or visual tab. <br>2) You run a search and replace on the post_content fields and break your shortcodes. <br>3) gremlins.<br></p>
        <p>Caveats:<br>1. <strong>This will only work on pages created after July 2016, when Cornerstone switched to a JSOn storage format. If your page is older, it uses serialized data, and I can't help you.</strong><br>2. I offer no support for this. Use at your own risk.<br>3. There is nothing special here, I completely hijack existing Cornerstone functions to regenerate post content based on the cornerstone data saved in the post meta field. If that JSON content is broken, you're out of luck.</p>

        <?php
        settings_fields( 'pluginPage' );
        do_settings_sections( 'pluginPage' );
        submit_button();
        ?>

    </form>
    <?php

}