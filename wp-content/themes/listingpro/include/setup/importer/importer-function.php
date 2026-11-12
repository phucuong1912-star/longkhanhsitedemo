<?php
/*====================================================================================*/
/* import demo content */
add_action('wp_ajax_importDemo', 'importDemo');
if (!function_exists('importDemo')) {
  function importDemo()
  {
    check_ajax_referer('lp_ajax_nonce', 'lpNonce');
    // Nonce is checked, get the POST data and sign user on
    if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
      $res = json_encode(array('nonceerror' => 'yes'));
      die($res);
    }
    $layout = null;
    $res = null;

    if (isset($_POST['data'])) {
      $layout = $_POST['data'];

      if ($layout && $layout != '') {

        function set_demo_menus()
        {
          $primary_menu = get_term_by('name', 'Main', 'nav_menu');
          $footer_menu = get_term_by('name', 'Footer', 'nav_menu');
          set_theme_mod(
            'nav_menu_locations',
            array(
              'main' => $primary_menu->term_id,
              'footer' => $footer_menu->term_id,
            )
          );
        }

        function set_demo_data($file)
        {

          if (!defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true);

          require_once ABSPATH . 'wp-admin/includes/import.php';

          $importer_error = false;

          if (!class_exists('WP_Importer')) {

            $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

            if (file_exists($class_wp_importer)) {

              require_once($class_wp_importer);
            } else {

              $importer_error = true;
            }
          }

          if (!class_exists('WP_Import')) {

            $class_wp_import = get_template_directory() . '/framework/importer/importer/wordpress-importer.php';

            if (file_exists($class_wp_import))
              require_once($class_wp_import);
            else
              $importer_error = true;
          }

          if ($importer_error) {

            die("Error on import");
          } else {

            if (!is_file($file)) {

              echo "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please use the Wordpress importer and import the XML file (should be located in your download .zip: Sample Content folder) manually ";
            } else {

              $wp_import = new WP_Import();
              $wp_import->fetch_attachments = true;
              $res = $wp_import->import($file);
              echo esc_attr($res);
            }
          }
        }
        function clean_default_widgets()
        {
          update_option('sidebars_widgets', $null);
          //echo esc_html__('widget has been cleaned.','listingpro');
        }

        function process_widget_import_file($file)
        {
          if (! file_exists($file)) {
            echo esc_html__('Widget Import file could not be found. Please try again.', 'listingpro');
          } else {
            global $wp_filesystem;
            $data   = $wp_filesystem->get_contents($file);
            $data = json_decode($data);
            $this->widget_import_results = import_widgets($data);
          }
        }

        function available_widgets()
        {


          global $wp_registered_widget_controls;

          $widget_controls = $wp_registered_widget_controls;

          $available_widgets = array();

          foreach ($widget_controls as $widget) {

            if (! empty($widget['id_base']) && ! isset($available_widgets[$widget['id_base']])) { // no dupes

              $available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
              $available_widgets[$widget['id_base']]['name'] = $widget['name'];
            }
          }


          return apply_filters('xshop_theme_import_widget_available_widgets', $available_widgets);
        }


        function import_widgets($data)
        {

          global $wp_registered_sidebars;

          if (empty($data) || ! is_object($data)) {
            echo esc_html__('Widget import data could not be read. Please try a different file.', 'listingpro');
          } else {

            $data = apply_filters('radium_theme_import_widget_data', $data);
          }


          //$available_widgets = $this->available_widgets();
          $available_widgets = available_widgets();


          $widget_instances = array();
          foreach ($available_widgets as $widget_data) {
            $widget_instances[$widget_data['id_base']] = get_option('widget_' . $widget_data['id_base']);
          }


          $results = array();

          foreach ($data as $sidebar_id => $widgets) {

            if ('wp_inactive_widgets' == $sidebar_id) {

              continue;
            }



            if (isset($wp_registered_sidebars[$sidebar_id])) {
              $sidebar_available = true;
              $use_sidebar_id = $sidebar_id;
              $sidebar_message_type = 'success';
              $sidebar_message = '';
            } else {
              $sidebar_available = false;
              $use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
              $sidebar_message_type = 'error';
              $sidebar_message = __('Sidebar does not exist in theme (using Inactive)', 'listingpro');
            }

            $results[$sidebar_id]['name'] = ! empty($wp_registered_sidebars[$sidebar_id]['name']) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
            $results[$sidebar_id]['message_type'] = $sidebar_message_type;
            $results[$sidebar_id]['message'] = $sidebar_message;
            $results[$sidebar_id]['widgets'] = array();

            // Loop widgets
            foreach ($widgets as $widget_instance_id => $widget) {

              $fail = false;

              $id_base = preg_replace('/-[0-9]+$/', '', $widget_instance_id);
              $instance_id_number = str_replace($id_base . '-', '', $widget_instance_id);

              if (! $fail && ! isset($available_widgets[$id_base])) {
                $fail = true;
                $widget_message_type = 'error';
                $widget_message = __('Site does not support widget', 'listingpro'); // explain why widget not imported
              }

              $widget = apply_filters('radium_theme_import_widget_settings', $widget);

              if (! $fail && isset($widget_instances[$id_base])) {

                $sidebars_widgets = get_option('sidebars_widgets');
                $sidebar_widgets = isset($sidebars_widgets[$use_sidebar_id]) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

                $single_widget_instances = ! empty($widget_instances[$id_base]) ? $widget_instances[$id_base] : array();
                foreach ($single_widget_instances as $check_id => $check_widget) {

                  if (in_array("$id_base-$check_id", $sidebar_widgets) && (array) $widget == $check_widget) {

                    $fail = true;
                    $widget_message_type = 'warning';
                    $widget_message = __('Widget already exists', 'listingpro'); // explain why widget not imported

                    break;
                  }
                }
              }

              // No failure
              if (! $fail) {


                $single_widget_instances = get_option('widget_' . $id_base); // all instances for that widget ID base, get fresh every time
                $single_widget_instances = ! empty($single_widget_instances) ? $single_widget_instances : array('_multiwidget' => 1); // start fresh if have to
                $single_widget_instances[] = (array) $widget; // add it

                end($single_widget_instances);
                $new_instance_id_number = key($single_widget_instances);

                if ('0' === strval($new_instance_id_number)) {
                  $new_instance_id_number = 1;
                  $single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
                  unset($single_widget_instances[0]);
                }

                if (isset($single_widget_instances['_multiwidget'])) {
                  $multiwidget = $single_widget_instances['_multiwidget'];
                  unset($single_widget_instances['_multiwidget']);
                  $single_widget_instances['_multiwidget'] = $multiwidget;
                }

                update_option('widget_' . $id_base, $single_widget_instances);

                $sidebars_widgets = get_option('sidebars_widgets'); // which sidebars have which widgets, get fresh every time
                $new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
                $sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
                update_option('sidebars_widgets', $sidebars_widgets); // save the amended data

                // Success message

                if ($sidebar_available) {
                  $widget_message_type = 'success';
                  $widget_message = __('Imported', 'listingpro');
                } else {
                  $widget_message_type = 'warning';
                  $widget_message = __('Imported to Inactive', 'listingpro');
                }
              }

              // Result for widget instance
              $results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset($available_widgets[$id_base]['name']) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
              $results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = $widget->title ? $widget->title : __('No Title', 'listingpro'); // show "No Title" if widget instance is untitled
              $results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
              $results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;
            }
          }

          // Hook after import
          do_action('xshop_theme_import_widget_after_import');
          //echo esc_html__('Widgets has been successfully imported','listingpro');
          die();
        }



        function set_demo_theme_options($file)
        {


          // File exists?
          if (!file_exists($file)) {
            echo esc_html__('Theme options Import file could not be found. Please try again.', 'listingpro');
          } else {
            // Get file contents and decode
            global $wp_filesystem;
            $data   = $wp_filesystem->get_contents($file);
            $data = json_decode($data, true);

            $theme_option_name       =  'TakeThemes_options';

            if (is_array($data) && !empty($data)) {
              $data = apply_filters('solitaire_theme_import_theme_options', $data);
              update_option($theme_option_name, $data);
              //echo esc_html__('Theme options have been successfully imported', 'listingpro');
              //die();
            } else {
              echo esc_html__('Theme options import data could not be read. Please try a different file.', 'listingpro');
            }
          }
        }



        if ($layout == "solitaire") {

          $solitaire_options = get_template_directory() . '/framework/importer/demo-files/solitaire/solitaire-options.json';

          $solitaire_widgets = get_template_directory() . '/framework/importer/demo-files/solitaire/solitaire_widgets.json';

          $solitaire_content = get_template_directory() . '/framework/importer/demo-files/solitaire/solitaire.xml';

          set_demo_data($solitaire_content);

          set_demo_menus();

          clean_default_widgets();

          set_demo_theme_options($solitaire_options);

          wp_delete_post(1);
          wp_delete_post(2);
          update_option('show_on_front', 'page');
          update_option('page_on_front', 144);
          update_option('page_for_posts', 223);

          process_widget_import_file($solitaire_widgets);
        } else if ($layout == "jewelia") {

          $jewelia_options = get_template_directory() . '/framework/importer/demo-files/jewelia/jewelia-options.json';

          $jewelia_widgets = get_template_directory() . '/framework/importer/demo-files/jewelia/jewelia_widgets.json';

          $jewelia_content = get_template_directory() . '/framework/importer/demo-files/jewelia/jewelia.xml';

          set_demo_data($jewelia_content);

          set_demo_menus();

          clean_default_widgets();

          set_demo_theme_options($jewelia_options);


          wp_delete_post(1);
          wp_delete_post(2);
          update_option('show_on_front', 'page');
          update_option('page_on_front', 144);
          update_option('page_for_posts', 223);

          $createPage = array(
            'post_title'    => 'Wishlist',
            'post_content'  => '[vc_row][vc_column][solitaire_wishlist title="My Wishlist"][/vc_column][/vc_row]',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'page',
            'post_name'     => 'wishlist'
          );
          wp_insert_post($createPage);


          if (class_exists('Redux')) {
            $opt_name = 'redux_demo';
            if (get_page_by_title('Wishlist') != NULL) {
              $page = get_page_by_title('Wishlist');
              $permalink = get_permalink($page->ID);
              Redux::set_option($opt_name, 'wishlist_page_url', $permalink);
            }
          }

          process_widget_import_file($jewelia_widgets);
        } else if ($layout == "quark1") {

          $quark1_options = get_template_directory() . '/framework/importer/demo-files/quark1/quark1-options.json';

          $quark1_widgets = get_template_directory() . '/framework/importer/demo-files/quark1/quark1_widgets.json';

          $quark1_content = get_template_directory() . '/framework/importer/demo-files/quark1/quark1.xml';

          set_demo_data($quark1_content);

          set_demo_menus();

          clean_default_widgets();

          set_demo_theme_options($quark1_options);

          wp_delete_post(1);
          wp_delete_post(2);
          update_option('show_on_front', 'page');
          update_option('page_on_front', 1756);
          update_option('page_for_posts', 1862);

          process_widget_import_file($quark1_widgets);
        } else if ($layout == "quark2") {

          $quark2_options = get_template_directory() . '/framework/importer/demo-files/quark2/quark2-options.json';

          $quark2_widgets = get_template_directory() . '/framework/importer/demo-files/quark2/quark2_widgets.json';

          $quark2_content = get_template_directory() . '/framework/importer/demo-files/quark2/quark2.xml';

          set_demo_data($quark2_content);

          set_demo_menus();

          clean_default_widgets();

          set_demo_theme_options($quark2_options);


          wp_delete_post(1);
          wp_delete_post(2);

          update_option('show_on_front', 'page');
          update_option('page_on_front', 1756);
          update_option('page_for_posts', 1862);
          process_widget_import_file($quark2_widgets);
        }
      }
    }
    die();
  }
}
