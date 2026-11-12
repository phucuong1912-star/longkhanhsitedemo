<?php
/* ============== ListingPro Currency sign ============ */
class ListingproPlugin {}

//New update 2.6.10
include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/paypal/form-handler.php');
include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/paypal/form-handler2.php');
//End New update 2.6.10

if (!function_exists('listingpro_subscriber_capabilities')) {

    if (current_user_can('subscriber')) {
        add_action('init', 'listingpro_subscriber_capabilities');
    }

    function listingpro_subscriber_capabilities()
    {
        //if (!is_admin()) {
        $contributor = get_role('subscriber');
        $contributor->add_cap('upload_files');
        $contributor->add_cap('edit_posts');
        $contributor->add_cap('assign_location');
        $contributor->add_cap('assign_list-tags');
        $contributor->add_cap('assign_listing-category');
        $contributor->add_cap('assign_features');
        show_admin_bar(false);

        //}
    }
}
if (!function_exists('listingpro_admin_capabilities')) {

    add_action('init', 'listingpro_admin_capabilities');

    function listingpro_admin_capabilities()
    {
        $contributor = get_role('administrator');
        $contributor->add_cap('assign_location');
        $contributor->add_cap('assign_list-tags');
        $contributor->add_cap('assign_listing-category');
        $contributor->add_cap('assign_features');
    }
}

add_action('admin_print_scripts', function () {
    $assetsurl        = plugins_url('/assets/js/metaboxes.js', __FILE__);
    $objectname = 'script_data';
    global $post, $pagenow;

    if (current_user_can('edit_posts') && ($pagenow == 'post-new.php' || $pagenow == 'post.php')) {
        if (isset($post)) {
            wp_register_script('post-metaboxes', $assetsurl);
            wp_localize_script('post-metaboxes', $objectname, [
                'post_id' => $post->ID,
                'nonce' => wp_create_nonce('lp-ajax'),
                'image_ids' => get_post_meta($post->ID, 'gallery_image_ids', true),
                'label_create' => __("Create Featured Gallery", "listingpro-plugin"),
                'label_edit' => __("Edit Featured Gallery", "listingpro-plugin"),
                'label_save' => __("Save Featured Gallery", "listingpro-plugin"),
                'label_saving' => __("Saving...", "listingpro-plugin")
            ]);
            wp_enqueue_script('post-metaboxes');
        }
    }
});
if (function_exists('listingpro_load_media')) {
    add_action('admin_enqueue_scripts', 'listingpro_load_media');
}
if (!function_exists('listingpro_currency_sign')) {

    function listingpro_currency_sign()
    {
        $currency_code = '';
        $currencycode = '';
        global $listingpro_options;
        if (isset($listingpro_options)) {
            $currency_code = $listingpro_options['currency_paid_submission'];
            if ($currency_code == "USD") {
                $currencycode = "$";
            } elseif ($currency_code == "BDT") {
                $currencycode = "৳";
            } elseif ($currency_code == "TTD") {
                $currencycode = "TT$";
            } elseif ($currency_code == "AUD") {
                $currencycode = "$";
            } elseif ($currency_code == "AED") {
                $currencycode = "د.إ";
            } elseif ($currency_code == "CAD") {
                $currencycode = "$";
            } elseif ($currency_code == "CZK") {
                $currencycode = "K�?";
            } elseif ($currency_code == "DKK") {
                $currencycode = "kr";
            } elseif ($currency_code == "EUR") {
                $currencycode = "€";
            } elseif ($currency_code == "EGP") {
                $currencycode = "E£";
            } elseif ($currency_code == "HKD") {
                $currencycode = "$";
            } elseif ($currency_code == "HUF") {
                $currencycode = "Ft";
            } elseif ($currency_code == "JPY") {
                $currencycode = "¥";
            } elseif ($currency_code == "NOK") {
                $currencycode = "kr";
            } elseif ($currency_code == "NZD") {
                $currencycode = "$";
            } elseif ($currency_code == "PLN") {
                $currencycode = "zł";
            } elseif ($currency_code == "GBP") {
                $currencycode = "£";
            } elseif ($currency_code == "SEK") {
                $currencycode = "kr";
            } elseif ($currency_code == "SGD") {
                $currencycode = "$";
            } elseif ($currency_code == "CHF") {
                $currencycode = "CHF";
            } elseif ($currency_code == "BRL") {
                $currencycode = "R$";
            } elseif ($currency_code == "IDR") {
                $currencycode = "Rp";
            } elseif ($currency_code == "ILS") {
                $currencycode = "₪";
            } elseif ($currency_code == "INR") {
                $currencycode = "INR";
            } elseif ($currency_code == "KOR") {
                $currencycode = "₩";
            } elseif ($currency_code == "KSH") {
                $currencycode = "KSh";
            } elseif ($currency_code == "MYR") {
                $currencycode = "RM";
            } elseif ($currency_code == "MXN") {
                $currencycode = "$";
            } elseif ($currency_code == "PHP") {
                $currencycode = "₱";
            } elseif ($currency_code == "TWD") {
                $currencycode = "NT$";
            } elseif ($currency_code == "THB") {
                $currencycode = "฿";
            } elseif ($currency_code == "VND") {
                $currencycode = "₫";
            } elseif ($currency_code == "ALL") {
                $currencycode = "Lek";
            } elseif ($currency_code == "AFN") {
                $currencycode = "؋";
            } elseif ($currency_code == "ARS") {
                $currencycode = "$";
            } elseif ($currency_code == "AWG") {
                $currencycode = "ƒ";
            } elseif ($currency_code == "AZN") {
                $currencycode = "ман";
            } elseif ($currency_code == "BYN") {
                $currencycode = "Br";
            } elseif ($currency_code == "BZD") {
                $currencycode = "BZ$";
            } elseif ($currency_code == "BMD") {
                $currencycode = "$";
            } elseif ($currency_code == "BOB") {
                $currencycode = "$b";
            } elseif ($currency_code == "BAM") {
                $currencycode = "KM";
            } elseif ($currency_code == "BWP") {
                $currencycode = "P";
            } elseif ($currency_code == "BGN") {
                $currencycode = "лв";
            } elseif ($currency_code == "BRL") {
                $currencycode = "R$";
            } elseif ($currency_code == "BND") {
                $currencycode = "BND";
            } elseif ($currency_code == "KHR") {
                $currencycode = "KHR";
            } elseif ($currency_code == "KYD") {
                $currencycode = "$";
            } elseif ($currency_code == "CLP") {
                $currencycode = "$";
            } elseif ($currency_code == "CNY") {
                $currencycode = "¥";
            } elseif ($currency_code == "COP") {
                $currencycode = "$";
            } elseif ($currency_code == "CRC") {
                $currencycode = "₡";
            } elseif ($currency_code == "HRK") {
                $currencycode = "kn";
            } elseif ($currency_code == "CUP") {
                $currencycode = "₱";
            } elseif ($currency_code == "DOP") {
                $currencycode = "RD$";
            } elseif ($currency_code == "XCD") {
                $currencycode = "$";
            } elseif ($currency_code == "EGP") {
                $currencycode = "£";
            } elseif ($currency_code == "SVC") {
                $currencycode = "$";
            } elseif ($currency_code == "FKP") {
                $currencycode = "£";
            } elseif ($currency_code == "FJD") {
                $currencycode = "$";
            } elseif ($currency_code == "GHS") {
                $currencycode = "GH₵";
            } elseif ($currency_code == "GIP") {
                $currencycode = "£";
            } elseif ($currency_code == "GTQ") {
                $currencycode = "Q";
            } elseif ($currency_code == "GGP") {
                $currencycode = "£";
            } elseif ($currency_code == "GYD") {
                $currencycode = "$";
            } elseif ($currency_code == "HNL") {
                $currencycode = "L";
            } elseif ($currency_code == "IMP") {
                $currencycode = "£";
            } elseif ($currency_code == "JEP") {
                $currencycode = "£";
            } elseif ($currency_code == "KZT") {
                $currencycode = "лв";
            } elseif ($currency_code == "KPW") {
                $currencycode = "₩";
            } elseif ($currency_code == "KRW") {
                $currencycode = "₩";
            } elseif ($currency_code == "KGS") {
                $currencycode = "лв";
            } elseif ($currency_code == "LAK") {
                $currencycode = "₭";
            } elseif ($currency_code == "LBP") {
                $currencycode = "£";
            } elseif ($currency_code == "LRD") {
                $currencycode = "$";
            } elseif ($currency_code == "MKD") {
                $currencycode = "ден";
            } elseif ($currency_code == "MUR") {
                $currencycode = "₨";
            } elseif ($currency_code == "MXN") {
                $currencycode = "$";
            } elseif ($currency_code == "MNT") {
                $currencycode = "₮";
            } elseif ($currency_code == "MZN") {
                $currencycode = "MT";
            } elseif ($currency_code == "NAD") {
                $currencycode = "$";
            } elseif ($currency_code == "NPR") {
                $currencycode = "₨";
            } elseif ($currency_code == "ANG") {
                $currencycode = "ƒ";
            } elseif ($currency_code == "NIO") {
                $currencycode = "C$";
            } elseif ($currency_code == "NGN") {
                $currencycode = "₦";
            } elseif ($currency_code == "NOK") {
                $currencycode = "kr";
            } elseif ($currency_code == "OMR") {
                $currencycode = "﷼";
            } elseif ($currency_code == "PKR") {
                $currencycode = "₨";
            } elseif ($currency_code == "PAB") {
                $currencycode = "B/.";
            } elseif ($currency_code == "PYG") {
                $currencycode = "Gs";
            } elseif ($currency_code == "PEN") {
                $currencycode = "S/.";
            } elseif ($currency_code == "QAR") {
                $currencycode = "﷼";
            } elseif ($currency_code == "RON") {
                $currencycode = "lei";
            } elseif ($currency_code == "RUB") {
                $currencycode = "₽";
            } elseif ($currency_code == "SHP") {
                $currencycode = "£";
            } elseif ($currency_code == "SAR") {
                $currencycode = "﷼";
            } elseif ($currency_code == "RSD") {
                $currencycode = "Дин.";
            } elseif ($currency_code == "SCR") {
                $currencycode = "₨";
            } elseif ($currency_code == "SGD") {
                $currencycode = "$";
            } elseif ($currency_code == "SBD") {
                $currencycode = "$";
            } elseif ($currency_code == "SOS") {
                $currencycode = "S";
            } elseif ($currency_code == "ZAR") {
                $currencycode = "R";
            } elseif ($currency_code == "LKR") {
                $currencycode = "₨";
            } elseif ($currency_code == "SRD") {
                $currencycode = "$";
            } elseif ($currency_code == "SYP") {
                $currencycode = "£";
            } elseif ($currency_code == "TTD") {
                $currencycode = "TT$";
            } elseif ($currency_code == "TVD") {
                $currencycode = "$";
            } elseif ($currency_code == "UAH") {
                $currencycode = "₴";
            } elseif ($currency_code == "UYU") {
                $currencycode = "$U";
            } elseif ($currency_code == "UZS") {
                $currencycode = "лв";
            } elseif ($currency_code == "VEF") {
                $currencycode = "Bs";
            } elseif ($currency_code == "VND") {
                $currencycode = "₫";
            } elseif ($currency_code == "YER") {
                $currencycode = "﷼";
            } elseif ($currency_code == "ZWD") {
                $currencycode = "Z$";
            } elseif ($currency_code == "TRY") {
                $currencycode = "&#8378;";
            }
        }
        return $currencycode;
    }
}


require_once('inc/command-center/commandcenter-function.php');

/* ============== ListingPro Icon8 SVG ============ */

if (!function_exists('listingpro_icon8')) {

    function listingpro_icon8($icon)
    {
        $output = '';
        if ($icon == 'checked') {
            $output = '
												<img class="icon icons8-Checked" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFiUlEQVR4nO1aW2xVRRSdUl8YxQcqVoild8+htRijYjB+GEw0iAaLmpy79+kFia+qmPo2JHxQiT++PiS+vkw0fmmiHwoGEyM+PjSVGCTKw6IfAhrfiSZoAVlmn87cHri97T33nN7bGFbS5PTsM3v2zOyZvfaea8wx/E9xfi/OoAg3WsHTlrGRBDst43cSDBPjgD5bxjdW8K5lPKPfdq3ATDMVYJfgRFtEiQTvWcEhK0CaPxIctIxNVrC8O8QJDR/AnBDTifEQMfYljPrHCjZbxlqd7YBxga7Sgj4cr3/6bEN0q4wE64jxYbxSvj1jn+ps2IAKjOtIsDthwBfE6GtfidPT6lLXsoK7LeOrxITsJsGSybHeGNO+EieR4MVEh1t1UMagJbt2tJCghxg7nP7DxHhJ+zR5okMwyzK2xJ0w/raCBxctwnG5dmKMURckxho9INxkfdoe4lwvr2fVy+hkzLWCITdTQ0EvLjaTDOrFAmLssYLvu0poi99FuMMKfgwiLKxvJUYHMWhDnG0ahK4S2kgwX58LjKvLB0MRt6ZSpP5ZdifBYGcPTjVNwLwIXXEMGnG1J1IrSGzsoUauRBLzIpzlPYIEb5oBTDNpoKeR39iN2BPVgi0JPnJ2bGlbipP1fRBidu3BzsUJDVCmSbCCV50NezoF58XvGLeT4GsTonVCBWq8jxOTccTWAmKscTb8ZQWX+Pc+cJLgzomX09GOkWDXeFhGqAHRMv7VIJmUBYLI0xmNOdWVFFHytCOfiJ0OQYSFVrDfzfrDFR8MYFpiVY4Y5BFwLFYH0mcaDN3EJNjr+n95Ite3jLfG/CBmp4JDymIzUYE6oDHKMr50x/3747mNBsrYTsaBQojTKj5wSZEq+sA0EiFaibHBrcSOWiaRBB+7VVlcIdSszQnXTpbNVYx61g3iF8ugGts85SZ9oFI4OivLTINgGff4pCxgXJnyZNNJ3ziWUPNpaGaXt8FVjFkcp7qaexRxS5q2HYKLfKyrEJLgNxV2hzizJm0hWi3jdSu4zaSETpZl/OE84PHU7UPM9lG/QuiTmVpz5oBxczmbE/TXakRhOc4hwXeu7Rv1xKs5Iaa79vszD8S1uVcjcK0UW3Vr0eFoIlhX9UZG9lZ21/JKBcudr6txz1en2mghxmvOgL01s9gxoGmF0/NzpVCwq97NXhDc5MpBOphXxiKbelS6zv/UzWoygATzfdypEOpRluX4JcZVaqTT8XayAjIeEawHgduf2k+FMC51ZgyIgeBy76JKNbpDnEIRrnCVFwSC+0wOIME6N5AnK4WMZc6AzVk6KRRxITF+KBcsGD+5Tl/IorcKRVlqjoZyHE8alUCaDAh6USDGt+U6L2NTXkla1wrMVMIYV1VKmDHmR66gjCDCXVk71MKaMloSbM+TTdsIq9wKb6j+EaM3z8RKZ29uL9pNboCWVrfGGWwRMlHlIk5uChGuN1MMJOjx1GTCwG0FDzi/3tas4sNY0GQrkeb211Zh9EUxxiNmisAyVrtB7FTPqakRFXGt5zJUxKWmySgILnPM4XBQxDWpGitv8hcvylhNk9AhmOXZMjHW18swB91++bwZRWxbwgxfSCfGZ3Vfy2kROfZJR7sbuTIdicsl9Qr9P5PC+KLHpcHx/V4D9kxhZE/45GtXbrFIV8K7mSsUPDpuyTLbEbvapwTqTrlfabg981yCP20bIW35XYba0dtdvQxdP6lX1Vr98K7mKxla1kmbWZavpyOs8rTDujiR+ojNtDqM+92FpTdg2NHrAU18lM6roTqr+qfP+k5lxHjMMj5J/mBALz5J0F9zsMt7QHGpf+S3Jwfr+QmHslglgE35CUfVMz/CDVrOJME7mk8T41e3UsPuebump5rZ6f5q1gXrMZgG4D9lCrBDc6dxfgAAAABJRU5ErkJggg==" alt="icon-check" />';
        } elseif ($icon == 'unchecked') {
            $output = '
												<img class="icon icons8-Cancel" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAFCUlEQVRoQ9Va7XEaSRB9g36ZperkCCxFcO0IDkVgK4JDERhFcHIExhFYisAiAuEI3BeBRATHVYH8y9qrt/TgYcXuzi4L4qaKki0ts/2mu19/jUNL64fIyU/gDwDCjwNOsPyE6yEFHgAoP0fAt1eq/P/Wy22zA4V/Av4EMNggdOzWDw4YOWC8DahGQAzAXwYgEzgF/nXAhCfdWf58yAvG7xHwE9Cn1lKg74DfAsTXHeBjE0C1gKQix4/ApxAAgBsAt4nqbawKwucWIu8B8EPN+nXdBS6d6ix2z2ggfGEKfHHAsZ3+qEuTqPGyMqFMy4MUGFJLKTBzwEXsAUUBmYt8csDQBBl3gGET9cecrpHGtVsSB0121FO9rPpuKRCa0gL46oA+tQBg2FO9rtq0jb/PRUggJAFqZ5IA52XaLwRi/nBnTkkQ/Z4qaXNvay5CKp8YIWgXOCsCUwhkLnJHTQD4uwO835UpVZ0KwTiAVvC7kcr5pu9sBDIXoUo/0JyOAHkpEF7gUDMp8Lmn6v11hekZEKPDr+YTezenIg3lzOw8z2ZrQMy5741iL/bl2FXmFWhm4IAvpOYEOA39ZQ3IQoS2yMA0TlQZpA5uzUXo/KTmm0SVzJatFRALSPeH4hdFJ2hxRslkHeDU++8KSKCNj4nq1cGpIhBoIUL5mOuttJIB8drgv7vA69i0YyFyD2BWxu9VBxLEKySqb6uez8vrtZIB2YQwZsOFCAMk+b00WBXtFQZdxqtElQEwankLcsBlV5XhIgPCk2WK/YzWynY1QZiy1waTB9FlSl8jAfVhguVConrqQifvqR5HHUfwUBMw24II6JgZcub0jskZuXkbyq0Dpi0QZkmsgd6lwAWBZOkIgK3YKgZMmyBC32baQiBZgOkAZ69Uae+NVxmYtkEYe/WfgLsU+Oa8o4fBpTESFkLLcniNALjfI5CVBGSnuo5dJI9lxt/p8ASS8sFEGSzbWXkwtmurILykXv6dAOFLcmD4q9Y0ER73voB4c+K7GwXNKhvZKZC8Y5swtYNmFQhjrsw1aFpsWb5p2dnXHNucvVEGUAYmyBGnu6DfjewUE2diNBA+80PkF/22HBBLKbZtMD7Z9QGxrRQlKk60CWYh8itFCZLGWU/1dV31NonYbYGZi/zD/kKWNJrnZw7fMI2P0kT+gLYFE6Tx00T1JAPyKDJMl132tYK+SjsLEaYHjSN2HkxshWiHnzVK1gqrsNStQ8NWIbI8rlUUhQcUgGGaFFUhbpL3f9l8CJh2vflgKTEnSWwHzY6Aty/dJi0ya2sHffdO/qwdFNpdWbO4ym92/fegub65Qecz1gVz++VM4uBapp6U2ERMgJPClqlpha1SNrE5vzvb90ykSKPWxOaogw2S8ia23yQYKxyEv4QgoscKARjfLOa4+fylnN/KWXZ5SM2FzfWq0VuWer+UmeXMqbTCjBmG3rLLYmAu9zUzeRT58ARcmU+Mu8Cg0TA0dLogAHFcPDkCLnZlahYnOM/n/JLv2zhqy5NCdOfELgxw/u2H+aMOcNMWoOBeSzbSsEsJg1YvDHj0lheN8tctLICOmwTDhcg7u8Kxmj4xee3yBkSNpna0RnIlJtMZntzq/ohduVheqnFugjSdbrxU49ybpzQNL9WEjfObDnDVRMuNgHhQNIcU4B0VjotZzzRZU7vmdNsEgH/hVkDyWvpp15eCi2d5cNPcxbPJNsKH7/8PkcItXz99rKgAAAAASUVORK5CYII=" alt="icon-cross" />';
        }
        return $output;
    }
}









/* ============== ListingPro Author Name ============ */

if (!function_exists('listingpro_author_name')) {

    function listingpro_author_name()
    {
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $output = $current_user->user_login;
        } else {
            $output = '';
        }
        return $output;
    }
}




/* ============== ListingPro Get Metabox ============ */

if (!function_exists('listing_get_metabox')) {
    function listing_get_metabox($name)
    {
        global $post;
        if ($post) {
            $metabox = get_post_meta($post->ID, 'lp_' . strtolower(THEMENAME) . '_options', true);
            return isset($metabox[$name]) ? $metabox[$name] : "";
        }
        return false;
    }
}

/* ============== ListingPro Get form fields ============ */

if (!function_exists('listing_get_fields')) {
    function listing_get_fields($name, $post_id)
    {
        if ($post_id) {
            $metabox = get_post_meta($post_id, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
            return isset($metabox[$name]) ? $metabox[$name] : "";
        }
        return false;
    }
}


/* ============== ListingPro Get Metabox ============ */

if (!function_exists('listing_get_metabox_by_ID')) {
    function listing_get_metabox_by_ID($name, $postid)
    {
        if ($postid) {
            $metabox = get_post_meta($postid, 'lp_' . strtolower(THEMENAME) . '_options', true);
            return isset($metabox[$name]) ? $metabox[$name] : "";
        } else {
            return false;
        }
    }
}


/* ============== ListingPro Set Metabox ============ */

if (!function_exists('listing_set_metabox')) {
    function listing_set_metabox($name, $val, $postID)
    {
        if ($postID) {
            $metabox = get_post_meta($postID, 'lp_' . strtolower(THEMENAME) . '_options', true);
            if (!empty($metabox) && is_array($metabox)) {
                $metabox[$name] = $val;
            } else {
                $metabox = array();
                $metabox[$name] = $val;
            }
            return update_post_meta($postID, 'lp_' . strtolower(THEMENAME) . '_options', $metabox);
        } else {
            return false;
        }
    }
}


/* ============== ListingPro Set Metabox ============ */

if (!function_exists('listing_set_metabox_of_extraFields')) {
    function listing_set_metabox_of_extraFields($name, $val, $postID)
    {
        if ($postID) {
            $metabox = get_post_meta($postID, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
            $metabox[$name] = $val;
            return update_post_meta($postID, 'lp_' . strtolower(THEMENAME) . '_options_fields', $metabox);
        } else {
            return false;
        }
    }
}

/* ============== ListingPro deleted Metabox ============ */

if (!function_exists('listing_delete_metabox')) {
    function listing_delete_metabox($key, $postid)
    {
        global $post;
        if ($postid) {
            $metabox = get_post_meta($postid, 'lp_' . strtolower(THEMENAME) . '_options', true);
            if (array_key_exists($key, $metabox)) {
                unset($metabox[$key]);
                if (!empty($metabox)) {
                    return update_post_meta($postid, 'lp_' . strtolower(THEMENAME) . '_options', $metabox);
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

/* ============== ListingPro get term meta ============ */

if (!function_exists('listingpro_get_term_meta')) {
    function listingpro_get_term_meta($term_id, $meta_name)
    {
        $value = get_term_meta($term_id, $meta_name, true);
        return $value;
    }
}



/* ============== ListingPro Get taxonomy Meta ============ */

if (!function_exists('listing_get_tax_meta')) {
    function listing_get_tax_meta($termID, $taxonomy, $meta)
    {
        if ($termID) {
            $metae = 'lp_' . $taxonomy . '_' . $meta;
            $metad = listingpro_get_term_meta($termID, $metae);
            return $metad;
        } else {
            return false;
        }
    }
}

/* ============== ListingPro Features array ============ */

if (!function_exists('listing_get_feature_array')) {
    function listing_get_feature_array()
    {
        $cat = array();
        $ucat = array(
            'post_type' => 'listing',
            'hide_empty' => false,
            'orderby' => 'count',
            'order' => 'ASC',
        );
        $features = get_terms('features', $ucat);

        foreach ($features as $feature) {

            $cat[$feature->term_id] = $feature->name;
        }
        return $cat;
    }
}



/* ============== ListingPro update form field id in listing category ============ */



if (!function_exists('listingpro_update_form_fields_meta_in_listing_categories')) {
    function listingpro_update_form_fields_meta_in_listing_categories($post_id)
    {
        if (is_admin()) {
            $screen = get_current_screen();
            if (!empty($screen)) {
                if ($screen->post_type == 'form-fields') {
                    $cats = '';
                    $currentPostID = '';
                    if (!empty($_POST['post_ID'])) {
                        $currentPostID = $_POST['post_ID'];
                    }

                    if (isset($_POST['field-cat']) && !empty($_POST['field-cat'])) {
                        if (isset($_POST['post_ID'])) {
                            $currentPostID = $_POST['post_ID'];
                        }
                        $cats = $_POST['field-cat'];
                        foreach ($cats as $cat) {
                            $fieldIDs = listingpro_get_term_meta($cat, 'fileds_ids');

                            if (empty($fieldIDs)) {
                                $fieldIDs = array();
                            }

                            if (!in_array($currentPostID, $fieldIDs)) {
                                array_push($fieldIDs, $currentPostID);
                                update_term_meta($cat, 'fileds_ids', $fieldIDs);
                            }
                        }
                        implode(',', $cats);
                    }
                    //if(!empty($cats)){
                    $terms = get_terms('listing-category', array(
                        'hide_empty' => false,
                        'exclude' => $cats
                    ));

                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            $fieldIDs = listingpro_get_term_meta($term->term_id, 'fileds_ids');

                            if (!empty($fieldIDs)) {
                                foreach ($fieldIDs as $index => $value) {
                                    if ($currentPostID == $value) {
                                        unset($fieldIDs[$index]);
                                        /* echo $index;
												echo $value; */
                                    }
                                }
                                update_term_meta($term->term_id, 'fileds_ids', $fieldIDs);
                            }
                        }
                    }

                    //}
                }
            }
        }
    }
    add_action('save_post', 'listingpro_update_form_fields_meta_in_listing_categories');
}

/* ============== ListingPro update features in listing post ============ */

if (!function_exists('listingpro_update_features_in_list')) {
    function listingpro_update_features_in_list($post_id)
    {
        if (is_admin()) {
            require_once(ABSPATH . 'wp-admin/includes/screen.php');
            $screen = get_current_screen();
            if (!empty($screen)) {
                if ($screen->post_type == 'listing') {
                    if (isset($_POST['lp_form_fields_inn']) && !empty($_POST['lp_form_fields_inn']['lp_feature']) && isset($_POST['post_ID'])) {
                        $featuresArr = array();
                        if (!empty($_POST['lp_form_fields_inn']['lp_feature']) && is_array($_POST['lp_form_fields_inn']['lp_feature'])) {
                            foreach ($_POST['lp_form_fields_inn']['lp_feature'] as $k => $termID) {
                                $termID = (int) $termID;
                                if (term_exists($termID, 'features')) {
                                    $featuresArr[] = $termID;
                                }
                            }
                        }
                        wp_set_post_terms($_POST['post_ID'], $featuresArr, 'features');
                    }
                    if (!isset($_POST['lp_form_fields_inn']['lp_feature']) && empty($_POST['lp_form_fields_inn']['lp_feature']) && isset($_POST['post_ID'])) {
                        wp_delete_object_term_relationships($_POST['post_ID'], 'features');
                    }
                    if (isset($_POST['post_ID'])) {
                        if ($_POST['claimed_section'] == 'claimed') {
                            $isclaimed = 1;
                        } else {
                            $isclaimed = 0;
                        }
                        $planID = $_POST['Plan_id'];
                        $post_ID = $_POST['post_ID'];

                        $rate = get_post_meta($post_ID, 'listing_rate', true);
                        $reviewed = get_post_meta($post_ID, 'listing_reviewed', true);
                        $claimed = get_post_meta($post_ID, 'claimed', true);
                        $view = get_post_meta($post_ID, 'post_views_count', true);

                        if (empty($rate)) {
                            update_post_meta($post_ID, 'listing_rate', '');
                        }
                        if (empty($reviewed)) {
                            update_post_meta($post_ID, 'listing_reviewed', '');
                        }
                        if (empty($claimed)) {
                            update_post_meta($post_ID, 'claimed', $isclaimed);
                        }
                        if (empty($view)) {
                            update_post_meta($post_ID, 'post_views_count', '1');
                        }

                        $plan_time  = get_post_meta($planID, 'plan_time', true);
                        listing_set_metabox('Plan_id', $planID, $post_ID);
                        listing_set_metabox('lp_purchase_days', $plan_time, $post_ID);
                        update_post_meta($post_ID, 'plan_id', $planID);
                    }
                }
            }
        }
    }
    add_action('save_post', 'listingpro_update_features_in_list');
}


/* ============== ListingPro Features array ============ */

if (!function_exists('listing_get_cat_array')) {
    function listing_get_cat_array()
    {
        $cat = array();
        $ucat = array(
            'post_type' => 'listing',
            'hide_empty' => false,
            'orderby' => 'count',
            'order' => 'ASC',
        );
        $features = get_terms('listing-category', $ucat);

        foreach ($features as $feature) {

            $cat[$feature->term_id] = $feature->name;
        }
        return $cat;
    }
}



/* ============== ListingPro Custom post type columns ============ */

if (!function_exists('listing_columns')) {
    function listing_columns($columns)
    {
        return array(
            'cb' => '<input type="checkbox" />',
            'title' => esc_html__('Title', 'listingpro-plugin'),
            'listing-category' => esc_html__('Listing Category', 'listingpro-plugin'),
            'location' => esc_html__('Location', 'listingpro-plugin'),
            'features' => esc_html__('Features', 'listingpro-plugin'),
            'expires' => esc_html__('Expire After', 'listingpro-plugin'),
            'status' => esc_html__('Payment Status', 'listingpro-plugin'),
            'author' => esc_html__('Author', 'listingpro-plugin'),
            'date' => esc_html__('Date', 'listingpro-plugin'),
            'plan' => esc_html__('Associated Plan', 'listingpro-plugin'),
        );
    }
    add_filter('manage_listing_posts_columns', 'listing_columns');
}




if (!function_exists('listingpro_columns_content')) {
    function listingpro_columns_content($column_name, $post_ID)
    {
        if ($column_name == 'listing-category') {
            $term_list = wp_get_post_terms($post_ID, 'listing-category', array("fields" => "names"));
            foreach ($term_list as $list) {
                echo $list . ',';
            }
        }
        if ($column_name == 'location') {
            $term_list = wp_get_post_terms($post_ID, 'location', array("fields" => "names"));
            foreach ($term_list as $list) {
                echo $list . ',';
            }
        }
        if ($column_name == 'features') {
            $terms = get_the_terms($post_ID, 'features');
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    echo $term->name;
                }
            }
        }
        if ($column_name == 'expires') {
            //$listing_days  = listing_get_metabox_by_ID('listing_duration', $post_ID);
            $listing_status = get_post_status($post_ID);
            $Plan_id = listing_get_metabox_by_ID('Plan_id', $post_ID);
            $plan_time  = listing_get_metabox_by_ID('lp_purchase_days', $post_ID);
            if (!empty($Plan_id) && $listing_status == "publish") {
                if (!empty($plan_time)) {
                    $startdate = get_the_time('d-m-Y');
                    $endDate   = strtotime($startdate . ' + ' . $plan_time . ' days');
                    $diff = ($endDate - time()) / 60 / 60 / 24;
                    if ($diff < 1 && $diff > 0) {
                        $days = 1;
                        echo esc_html($days) . esc_html__(' Days Left', 'listingpro-plugin');
                    } else {
                        $days = floor($diff);
                        echo esc_html($days) . esc_html__(' Days Left', 'listingpro-plugin');
                    }
                } else {
                    $days = esc_html__('Unlimited', 'listingpro-plugin');
                    echo esc_html($days) . esc_html__(' Days Left', 'listingpro-plugin');
                }
            }
        }

        if ($column_name == 'status') {

            echo lp_get_payment_status_column($post_ID);
        }


        if ($column_name == 'plan') {

            $plan_name = esc_html__('N/A', 'listingpro-plugin');
            $plan_id = listing_get_metabox_by_ID('Plan_id', $post_ID);
            if (!empty($plan_id)) {
                $plan_name  = get_the_title($plan_id);
            }
            echo $plan_name;
        }
    }
    add_action('manage_listing_posts_custom_column', 'listingpro_columns_content', 10, 2);
}


/* ============== ListingPro Frontend Uplaod ============ */

if (!function_exists('listingpro_handle_attachment')) {
    function listingpro_handle_attachment($file_handler, $post_id, $set_thu = false)
    {
        // check to make sure its a successful upload
        if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) {
            __return_false();
        }

        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');

        $attach_id = media_handle_upload($file_handler, $post_id);

        // If you want to set a featured image frmo your uploads. 
        if ($set_thu) {
            set_post_thumbnail($post_id, $attach_id);
        }
        return $attach_id;
    }
}



/* ============== ListingPro Frontend Uplaod Featured image ============ */

if (!function_exists('listingpro_handle_attachment_featured')) {
    function listingpro_handle_attachment_featured($file_handler, $post_id)
    {

        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');

        $attach_id = media_handle_upload($file_handler, $post_id);

        set_post_thumbnail($post_id, $attach_id);
        return $attach_id;
    }
}




/* ============== ListingPro get child term (tags) ============ */

if (!function_exists('listingpro_child_term_method')) {


    function listingpro_child_term_method()
    {
        global $listingpro_options;
        wp_register_script('ajax-term-script', plugins_url('/assets/js/child-term.js', __FILE__), array('jquery'));
        wp_enqueue_script('ajax-term-script');

        wp_localize_script('ajax-term-script', 'ajax_term_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));

        // Enable the user with no privileges to run ajax_login() in AJAX

    }
    if (!is_admin()) {

        add_action('wp_enqueue_scripts', 'listingpro_child_term_method');
    }
}

add_action('wp_ajax_ajax_term',        'ajax_term');
add_action('wp_ajax_nopriv_ajax_term', 'ajax_term');
if (!function_exists('ajax_term')) {
    function ajax_term()
    {

        // Nonce is checked, get the POST data and sign user on
        $term_id = $_POST['term_id'];
        $listingid = '';
        $metaFields = array();
        if (isset($_POST['listing_id'])) {
            if (!empty($_POST['listing_id'])) {
                $listingid = $_POST['listing_id'];
                $metaFields = get_post_meta($listingid, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
            }
        }
        $output = null;
        $tid = null;
        $hasFeatures = false;
        $hasFields = false;
        $termNameArray = array();
        $termFieldsArray = array();
        $lpselectedtags = array();
        $fieldIDss = array();
        $allIdsArray = array();
        $featureTitle = '';
        $featureTitle = esc_html__('Select your listing features', 'listingpro-plugin');
        if (!empty($term_id) && is_array($term_id)) {
            foreach ($term_id as $tid) {
                $termdata = get_term_by('id', $tid, 'category');
                $termdataname = $termdata->name;
                $fieldIDs = array();
                $fieldIDs = listingpro_get_term_meta($tid, 'fileds_ids');
                if (!empty($fieldIDs)) {
                    foreach ($fieldIDs as $singlefId) {
                        if (array_search($singlefId, $allIdsArray)) {
                        } else {
                            $lppoststatus = get_post_status($singlefId);
                            if ($lppoststatus == "publish") {
                                array_push($allIdsArray, $singlefId);
                            }
                        }
                    }
                }


                $featureName;
                $features = listingpro_get_term_meta($tid, 'lp_category_tags');

                if (!empty($features)) {
                    foreach ($features as $feature) {
                        $terms = get_term_by('id', $feature, 'features');
                        if (!empty($terms)) {
                            $featureName[" " . $terms->term_id] = $terms->name;

                            /* for pre checked tags on edit listing */
                            if (!empty($metaFields['lp_feature'])) {
                                if (in_array($feature, $metaFields['lp_feature'])) {
                                    $lpselectedtags[$terms->term_id] =  $terms->term_id;
                                }
                            }
                        }
                    }
                    $hasFeatures = true;
                }
            }
            //exit(json_encode($allIdsArray));
            $allIdsArray = array_unique($allIdsArray);
            $fieldIDss[$tid] = $allIdsArray;
            $n = 1;

            if (is_array($fieldIDss) && count($fieldIDss) > 0) {
                $publishedFields = array();
                //$fieldIDss = array_unique($fieldIDss);
                foreach ($fieldIDss as $fidss) {
                    if (!empty($fidss)) {
                        /* $lppoststatus = get_post_status( $lpfid );
                            if($lppoststatus=="publish"){ */
                        $termFieldsArray[$n] = listingpro_field_type($fidss, $listingid);
                        $n++;
                        $hasFields = true;
                        /* } */
                    }
                }
            }


            if (!empty($termFieldsArray)) {
                $cnt = 1;
                foreach ($termFieldsArray as $tf) {
                    if ($cnt == 1) {
                        $output .= '<label for="inputTags" class="featuresBycat">' . esc_html__('Additional Business Info', 'listingpro-plugin') . '</label>';
                    }
                    $output .= $tf;
                    $cnt++;
                }
            }

            /* sorting feature in assending */
            if (!empty($featureName)) {
                asort($featureName);
            }
            $term_group_result = json_encode(array('tags' => $featureName, 'fields' => $output, 'hasfeatues' => $hasFeatures, 'hasfields' => $hasFields, 'fieldsids' => $fieldIDs, 'featuretitle' => $featureTitle, 'lpselectedtags' => $lpselectedtags));
            //$term_group_result = json_encode($listingpro_tag_groups);
            die($term_group_result);
        } else {
            $fieldIDs = listingpro_get_term_meta($term_id, 'fileds_ids');
            $fieldsOutput = null;
            if (is_array($fieldIDs) && count($fieldIDs) > 0) {

                $fieldsOutput .= '<label for="inputTags" class="featuresBycat">' . esc_html__('Additional Business Info', 'listingpro-plugin') . '</label>';
                $fieldsOutput .= listingpro_field_type($fieldIDs, $listingid);
                $hasFields = true;
            } else {
                $hasFields = false;
            }

            $featureName;

            $features = listingpro_get_term_meta($term_id, 'lp_category_tags');

            if (!empty($features)) {
                foreach ($features as $feature) {
                    $terms = get_term_by('id', $feature, 'features');
                    if (!empty($terms)) {
                        $featureName[" " . $terms->term_id] = $terms->name;
                    }
                }
                $hasFeatures = true;
            }

            //$term_fields = get_option(LiSTINGPRO_FORM_FIELDS);
            //$listingpro_term_fields = $term_fields[$term_id]['listingpro_form_fields'];
            if (isset($_POST['listing_id'])) {
                $selected_features = wp_get_post_terms($_POST['listing_id'], 'features');
                if (isset($selected_features) && !empty($selected_features)) {
                    foreach ($selected_features as $selected_feature) {
                        $lpselectedtags[$selected_feature->term_id] =  $selected_feature->term_id;
                    }
                }
            }

            $term_group_result = json_encode(array('tags' => $featureName, 'lpselectedtags' => $lpselectedtags, 'fields' => $fieldsOutput, 'hasfeatues' => $hasFeatures, 'hasfields' => $hasFields, 'fieldsids' => $fieldIDs, 'featuretitle' => $featureTitle));
            //$term_group_result = json_encode($listingpro_tag_groups);
            die($term_group_result);
        }
    }
}

add_action('wp_ajax_lp_get_fields',        'lp_get_fields');
add_action('wp_ajax_nopriv_lp_get_fields', 'lp_get_fields');
if (!function_exists('lp_get_fields')) {
    function lp_get_fields()
    {
        $output = null;
        $featureOutput = null;
        $array = '';
        $value = '';
        $term_id = $_POST['term_id'];
        $list_id = $_POST['list_id'];
        $requestFrom = $_POST['requestFrom'];
        $featureName = array();
        $featurevalue = array();
        $fieldIDs = array();
        $idcounts = 1;
        $featureMID = 'lp_feature';

        /* for listing whose features are there but not assigned to categories */
        $lpFreetags = get_the_terms($list_id, 'features');
        $featurevalued = listing_get_fields('lp_feature', $list_id);
        if (!empty($lpFreetags) || !empty($featurevalued)) {
            $featurevalue = array_merge($featurevalued, $featurevalue);
        }
        if (!empty($lpFreetags)) {
            if (is_array($featurevalued)) {
                foreach ($lpFreetags as $sngTag) {
                    $featureName[$sngTag->term_id] = $sngTag->name;
                }
            }
        }

        if (empty($term_id)) {
            /* for outputing features */
            $settings = array(
                'name'          => 'Select Business Features',
                'id'            => 'lp_form_fields_inn[' . $featureMID . ']',
                'type'          => 'checkboxes',
                'child_of' => '',
                'match' => '',
                'options' => $featureName,
                'value' => $featurevalue,
                'std' => '',
                'desc' => ''
            );
            ob_start();
            call_user_func('settings_checkboxes', $settings);
            $featureOutput[] .= ob_get_contents();
            ob_end_clean();
            ob_flush();
        }

        //die(json_encode($featureName));

        if (!empty($term_id)) {
            foreach ($term_id as $tid) {
                if (isset($requestFrom) && $requestFrom == 'catname') {
                    $category = get_term_by('name', $tid, 'listing-category');
                    $tid = $category->term_id;
                }
                $fieldIDss = listingpro_get_term_meta($tid, 'fileds_ids');
                if (!empty($fieldIDss)) {
                    foreach ($fieldIDss as $singleId) {
                        if (!empty($singleId)) {
                            $fieldIDs[$idcounts] = $singleId;
                            $idcounts++;
                        }
                    }
                }
                $Features = listingpro_get_term_meta($tid, 'lp_category_tags');

                $assigned_features = get_the_terms($list_id, 'features');
                $assigned_features_ids  =   array();
                if ($assigned_features) {
                    foreach ($assigned_features as $assigned_feature) {
                        $assigned_features_ids[]    =   $assigned_feature->term_id;
                    }
                }
                $featurevalue   =   array();
                $featurevalue   =   array_merge($featurevalue, $assigned_features_ids);

                //                    $featurevalued= listing_get_fields('lp_feature',$list_id);
                //                    $featurevalue = array_merge($featurevalued, $featurevalue);

                if (!empty($Features)) {
                    if (!is_array($Features)) {
                        explode(',', $Features);
                        foreach ($Features as $Feature) {
                            $features = get_term_by('id', $Feature, 'features');
                            if (!empty($features->name)) :
                                $featureName[$features->term_id] = $features->name;
                            endif;
                        }
                    } else {
                        foreach ($Features as $Feature) {
                            $features = get_term_by('id', $Feature, 'features');
                            if (!empty($features->name)) :
                                $featureName[$features->term_id] = $features->name;
                            endif;
                        }
                    }
                }
            }
            /*
                print_r($fieldIDs);
                exit; */

            /* for outputing features */
            $settings = array(
                'name'          => 'Select Business Features',
                'id'            => 'lp_form_fields_inn[' . $featureMID . ']',
                'type'          => 'checkboxes',
                'child_of' => '',
                'match' => '',
                'options' => $featureName,
                'value' => $featurevalue,
                'std' => '',
                'desc' => ''
            );
            ob_start();
            call_user_func('settings_checkboxes', $settings);
            $featureOutput[] .= ob_get_contents();
            ob_end_clean();
            ob_flush();
            /* end for outputing features */
            /* for form fields */
            if (!empty($fieldIDs)) {
                $type = 'form-fields';
                $args = array(
                    'post_type' => $type,
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'post__in'         => $fieldIDs,

                );
                $my_query = null;

                $my_query = new WP_Query($args);


                if ($my_query->have_posts()) {
                    while ($my_query->have_posts()) : $my_query->the_post();
                        global $post;
                        $options = '';
                        $array = array();

                        $type = listing_get_metabox('field-type');

                        if (isset($list_id) && !empty($list_id)) {
                            $value = listing_get_fields($post->post_name, $list_id);
                        }


                        if ($type == 'select') {
                            $selectoptn = array("Select Option");
                            $array = array_merge($selectoptn, $array);
                        }
                        if ($type == 'radio') {
                            $options = listing_get_metabox('radio-options');
                        } elseif ($type == 'select') {
                            $options = listing_get_metabox('select-options');
                        } elseif ($type == 'checkboxes') {
                            $options = listing_get_metabox('multicheck-options');
                        }
                        if (!empty($options)) {
                            $myArray = explode(',', $options);
                            foreach ($myArray as $key => $myAr) {
                                $array[$myAr] = $myAr;
                            }
                        }

                        //New by abbas
                        if ($type == 'select') {
                            $selectoption = array("Select Option");
                            $array = array_merge($selectoption, $array);
                        }
                        //End New by abbas

                        $settings = array(
                            'name'          => get_the_title(),
                            'id'            => 'lp_form_fields_inn[' . $post->post_name . ']',
                            'type'          => $type,
                            'child_of' => '',
                            'match' => '',
                            'options' => $array,
                            'value' => $value,
                            'std' => '',
                            'desc' => '',
                            'from' => 'ajax'
                        );
                        ob_start();
                        call_user_func('settings_' . $type, $settings);
                        $output[] .= ob_get_contents();
                        ob_end_clean();
                        ob_flush();
                    endwhile;
                }
            }


            $evalue = '';
            $efieldIDs = array();
            $efieldIDs = listingpro_get_term_openfields(true);
            $lplistingid = $list_id;

            if (!empty($efieldIDs)) {
                $type = 'form-fields';
                $args = array(
                    'post_type' => $type,
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'post__in'         => $efieldIDs,

                );
                $my_query = null;

                $my_query = new WP_Query($args);


                if ($my_query->have_posts()) {
                    while ($my_query->have_posts()) : $my_query->the_post();
                        global $post;
                        $options = '';
                        $array = array();

                        $type = listing_get_metabox('field-type');

                        if (isset($lplistingid) && !empty($lplistingid)) {
                            $evalue = listing_get_fields($post->post_name, $lplistingid);
                        }


                        if ($type == 'select') {
                            $selectoptn = array("Select Option");
                            $array = array_merge($selectoptn, $array);
                        }
                        if ($type == 'radio') {
                            $options = listing_get_metabox('radio-options');
                        } elseif ($type == 'select') {
                            $options = listing_get_metabox('select-options');
                        } elseif ($type == 'checkboxes') {
                            $options = listing_get_metabox('multicheck-options');
                        }
                        if (!empty($options)) {
                            $myArray = explode(',', $options);
                            foreach ($myArray as $key => $myAr) {
                                $array[$myAr] = $myAr;
                            }
                        }

                        $settings = array(
                            'name'          => get_the_title(),
                            'id'            => 'lp_form_fields_inn[' . $post->post_name . ']',
                            'type'          => $type,
                            'child_of' => '',
                            'match' => '',
                            'options' => $array,
                            'value' => $evalue,
                            'std' => '',
                            'desc' => '',
                            'from' => 'ajax'
                        );
                        ob_start();
                        call_user_func('settings_' . $type, $settings);
                        $output[] .= ob_get_contents();
                        ob_end_clean();
                        ob_flush();
                    endwhile;
                }
            }

            /* end for form fields */
        }


        $term_group_result = json_encode(array('fields' => $output, 'features' => $featureOutput));
        die($term_group_result);
    }
}

/* for open fields ajax */
add_action('wp_ajax_lp_get_excluded_fields',        'lp_get_excluded_fields');
add_action('wp_ajax_nopriv_lp_get_excluded_fields', 'lp_get_excluded_fields');
if (!function_exists('lp_get_excluded_fields')) {
    function lp_get_excluded_fields()
    {
        $value = '';
        $fieldIDs = array();
        $fieldIDs = listingpro_get_term_openfields(true);
        $lplistingid = $_POST['lplistingid'];

        if (!empty($fieldIDs)) {
            $type = 'form-fields';
            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'post__in'         => $fieldIDs,

            );
            $my_query = null;
            $output = array();
            $my_query = new WP_Query($args);


            if ($my_query->have_posts()) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    global $post;
                    $options = '';
                    $array = null;

                    $type = listing_get_metabox('field-type');

                    if (isset($lplistingid) && !empty($lplistingid)) {
                        $value = listing_get_fields($post->post_name, $lplistingid);
                    }



                    if ($type == 'radio') {
                        $options = listing_get_metabox('radio-options');
                    } elseif ($type == 'select') {
                        $options = listing_get_metabox('select-options');
                    } elseif ($type == 'checkboxes') {
                        $options = listing_get_metabox('multicheck-options');
                    }
                    if (!empty($options)) {
                        $myArray = explode(',', $options);
                        foreach ($myArray as $key => $myAr) {
                            $array[$myAr] = $myAr;
                        }
                    }

                    $settings = array(
                        'name'          => get_the_title(),
                        'id'            => 'lp_form_fields_inn[' . $post->post_name . ']',
                        'type'          => $type,
                        'child_of' => '',
                        'match' => '',
                        'options' => $array,
                        'value' => $value,
                        'std' => '',
                        'desc' => '',
                        'from' => 'ajax'
                    );
                    ob_start();
                    call_user_func('settings_' . $type, $settings);
                    $output[] .= ob_get_contents();
                    ob_end_clean();
                    ob_flush();
                endwhile;
            }
        }
        $term_group_result = json_encode(array('fields' => $output, 'features' => ''));
        die($term_group_result);
    }
}
/* end for open fields ajax */

if (!function_exists('Listingpro_activation')) {
    function Listingpro_activation()
    {
        $status = get_option('theme_activation');
        if (empty($status) && $status != 'none') {
            update_option('theme_activation', 'none');
        }
?>
        <div class="notice">
            <form action="" method="post">
                <h2 style="margin-top:0;margin-bottom:5px">Activate Listingpro</h2>
                <p><?php esc_html__('Verify your purchase code to unlock all features, see ', 'listingpro-plugin'); ?><a href="https://docs.listingprowp.com/knowledgebase/how-to-activate-listingpro-theme/" target="_blank"><?php echo esc_html__('instructions', 'listingpro-plugin'); ?></a></p>
                <div id="title-wrap" class="input-text-wrap">
                    <label id="title-prompt-text" class="prompt" for="title"> Put here purchase key </label>
                    <input id="title" name="key" autocomplete="off" type="text">
                </div>
                <?php echo wp_nonce_field('api_nonce', 'api_nonce_field', true, false); ?>
                <input type="submit" name="submit" class="button button-primary button-hero" value="Activate" />
            </form>
            <?php

            if (isset($_POST['api_nonce_field']) &&  wp_verify_nonce($_POST['api_nonce_field'], 'api_nonce') && !empty($_POST['key'])) {

                $purchase_key = $_POST['key'];
                $item_id = 19386460;
                //'c8f37d37-52e2-4fed-b0ac-e470ba475772'
                $purchase_data = verify_envato_purchase_code($purchase_key);

                if (isset($purchase_data['verify-purchase']['buyer']) && $purchase_data['verify-purchase']['item_id'] == $item_id) {
                    update_option('theme_activation', 'activated');
                    echo '<p class="successful"> ' . __('Valid License Key!', 'sample-text-domain') . ' </p>';
                } else {
                    echo '<p class="error"> ' . __('Invalid license key', 'sample-text-domain') . ' </p>';
                }
            }
            echo '</div>';
        }
        $status = get_option('theme_activation');
        if (empty($status) || $status != 'activated') {
            //add_action( 'admin_notices', 'Listingpro_activation' );
        }
    }
    function verify_envato_purchase_code($code_to_verify)
    {
        // Your Username
        $username = 'CridioStudio';

        // Set API Key	
        $api_key = 'd22l6udt6rk9s36spidjjlah3nhnxw77';

        // Open cURL channel
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "http://marketplace.envato.com/api/edge/" . $username . "/" . $api_key . "/verify-purchase:" . $code_to_verify . ".json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //Set the user agent
        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        // Decode returned JSON
        $output = json_decode(curl_exec($ch), true);

        // Close Channel
        curl_close($ch);

        // Return output
        return $output;
    }

    /* ========================= check if category has features or not */
    if (!function_exists('lp_category_has_features')) {
        function lp_category_has_features($term_id)
        {

            $featureshas = false;
            if (!empty($term_id)) {
                $termparent = get_term_by('id', $term_id, 'listing-category');
                $parent = $termparent->parent;
            }

            $features = listingpro_get_term_meta($term_id, 'lp_category_tags');
            if (empty($features)) {
                $features = listingpro_get_term_meta($parent, 'lp_category_tags');
            }
            if (!empty($features)) {
                foreach ($features as $feature) {
                    $terms = get_term_by('id', $feature, 'features');
                    if (!empty($terms)) {
                        $featureshas = true;
                    }
                }
            }

            if ($featureshas == false) {
                $fieldIDs = listingpro_get_term_meta($term_id, 'fileds_ids');
                if (!empty($fieldIDs)) {
                    $featureshas = true;
                } else {
                    $featureshas = false;
                }
            }


            return $featureshas;
        }
    }

    /*============================= for ajax call all listings ==============================*/
    function lp_ajax_callback_listings()
    {
        // Implement ajax function here
        $all_listings = '';
        $queryy = new WP_Query(array('post_type' => 'listing', 'posts_per_page'    => -1,  'post_status' => 'publish'));
        if ($queryy->have_posts()) {
            while ($queryy->have_posts()) {
                $queryy->the_post();
                $all_listings .= '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
            }
        }

        $varBack = json_encode($all_listings);
        die($varBack);
    }
    add_action('wp_ajax_lp_get_all_p_listings', 'lp_ajax_callback_listings');    // If called from admin panel
    add_action('wp_ajax_nopriv_lp_get_all_p_listings', 'lp_ajax_callback_listings');    // If called from front end


    /*============================= for dashboard filter options ==============================*/
    if (!function_exists('listingpro_filter_listing_by_taxonomies')) {
        function listingpro_filter_listing_by_taxonomies($post_type, $which)
        {

            // Apply this only on a specific post type
            if ('listing' !== $post_type) {
                return;
            }

            // A list of taxonomy slugs to filter by
            $taxonomies = array('features', 'listing-category');

            foreach ($taxonomies as $taxonomy_slug) {

                // Retrieve taxonomy data
                $taxonomy_obj = get_taxonomy($taxonomy_slug);
                $taxonomy_name = $taxonomy_obj->labels->name;

                // Retrieve taxonomy terms
                $terms = get_terms($taxonomy_slug);

                // Display filter HTML
                echo "<select name='{$taxonomy_slug}' id='{$taxonomy_slug}' class='postform'>";
                echo '<option value="">' . sprintf(esc_html__('Show All %s', 'text_domain'), $taxonomy_name) . '</option>';
                foreach ($terms as $term) {
                    printf(
                        '<option value="%1$s" %2$s>%3$s (%4$s)</option>',
                        $term->slug,
                        ((isset($_GET[$taxonomy_slug]) && ($_GET[$taxonomy_slug] == $term->slug)) ? ' selected="selected"' : ''),
                        $term->name,
                        $term->count
                    );
                }
                echo '</select>';
            }

            //for plans


            global  $wpdb;
            $my_table   =   $wpdb->prefix . 'posts';
            $all_plans  =   $wpdb->get_results("SELECT * FROM $my_table WHERE `post_type`='price_plan' AND `post_status`='publish'");
            ?>

            <select name="price-plan-filter" id="price-plan-filter">
                <option value="all"><?php echo esc_html__('Select Plan', 'listingpro-plugin'); ?></option>
                <?php
                $sPlan = null;
                if (isset($_GET['price-plan-filter'])) {
                    if (!empty($_GET['price-plan-filter'])) {
                        $sPlan = $_GET['price-plan-filter'];
                    }
                }
                foreach ($all_plans as $post) :
                    $selected = '';
                    if ($sPlan == $post->ID) {
                        $selected = 'selected';
                    }
                ?>
                    <option value="<?php echo $post->ID; ?>" <?php echo $selected; ?>><?php echo $post->post_title; ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }
    add_action('restrict_manage_posts', 'listingpro_filter_listing_by_taxonomies', 10, 2);


    /*===================function for insert metabox in listing creation ===================*/
    if (!function_exists('lp_listing_save_additional_metas')) {
        function lp_listing_save_additional_metas($plan_id, $listingid)
        {
            $planmetaArray = array();
            if (!empty($plan_id) && !empty($listingid)) {
                if ($plan_id != "none") {
                    $planmetaArray['price'] = get_post_meta($plan_id, 'plan_price', true);
                    $planmetaArray['menu'] = get_post_meta($plan_id, 'listingproc_plan_menu', true);
                    $planmetaArray['announcment'] = get_post_meta($plan_id, 'listingproc_plan_announcment', true);
                    $planmetaArray['deals'] = get_post_meta($plan_id, 'listingproc_plan_deals', true);
                    $planmetaArray['competitor_campaigns'] = get_post_meta($plan_id, 'listingproc_plan_campaigns', true);
                    $planmetaArray['events'] = get_post_meta($plan_id, 'lp_eventsplan', true);
                    $planmetaArray['bookings'] = get_post_meta($plan_id, 'listingproc_bookings', true);
                    update_post_meta($listingid, 'listing_plan_data', $planmetaArray);
                }
            }
        }
    }

    /*===================function for lisitng to check if actions allowed ===================*/
    if (!function_exists('lp_validate_listing_action')) {
        function lp_validate_listing_action($listingid, $action)
        {
            $pLan_Id = listing_get_metabox_by_ID('Plan_id', $listingid);

            if ($action == 'price') {
                $p_action =   'plan_price';
            } elseif ($action == 'menu') {
                $p_action =   'listingproc_plan_menu';
            } elseif ($action == 'announcment') {
                $p_action =   'listingproc_plan_announcment';
            } elseif ($action == 'deals') {
                $p_action =   'listingproc_plan_deals';
            } elseif ($action   == 'competitor_campaigns') {
                $p_action =   'listingproc_plan_campaigns';
            } elseif ($action == 'events' || $action == 'event_id') {
                $p_action =   'lp_eventsplan';
            } elseif ($action = 'bookings') {
                $p_action =   'listingproc_bookings';
            } elseif ($action == 'leadform') {
                $p_action =   'listingproc_plan_leadform';
            }
            if (isset($pLan_Id)) {

                global $listingpro_options;
                $paid_submission   =    $listingpro_options['enable_paid_submission'];

                if ($pLan_Id != "none") {
                    $plans_meta = get_post_meta($pLan_Id, $p_action, true);
                    if ($plans_meta == 'false') {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    if ($pLan_Id == 'none' || $paid_submission == 'no') {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
            return true;
        }
    }

    /* ================function for filtering backend invoices====================== */
    if (!function_exists('lp_filter_backend_invoice')) {
        function lp_filter_backend_invoice()
        {
            $method = $_POST['method'];
            $status = $_POST['status'];
            $where = 'WHERE ';
            if (!empty($method) && !empty($status)) {
                $where .= "payment_method='$method' AND status='$status'";
            } elseif (!empty($method) && empty($status)) {
                $where .= "payment_method='$method' AND status IN ('pending', 'success', 'failed')";
            } elseif (empty($method) && !empty($status)) {
                $where .= "status='$status'";
            } elseif (empty($method) && empty($status)) {
                $where .= "status IN ('pending', 'success', 'failed')";
            }

            global $wpdb;
            $counter = 1;
            $currency_position = lp_theme_option('pricingplan_currency_position');
            $table = "listing_orders";
            $dbprefix = $wpdb->prefix;
            $table = $dbprefix . $table;
            $results = array();
            $htmlReturn = null;
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
                $query = "";
                $query = "SELECT * from $table  $where ORDER BY main_id DESC";
                $results = $wpdb->get_results($query);
            }
            if (!empty($results)) {
                foreach ($results as $Index => $Value) {
                    $currency_sign = (isset($Value->currency) && $Value->currency != '') ? $Value->currency : listingpro_currency_sign();
                    $main_id = $Value->main_id;
                    $listid = $Value->post_id;
                    $price  = $Value->price;
                    if ($currency_position == 'right') {
                        $price .= $currency_sign;
                    } else {
                        $price = $currency_sign . $price;
                    }

                    $classStatus = '';
                    $textStatus = '';
                    $invoiceStatus = $Value->status;
                    if ($invoiceStatus == "success") {
                        $classStatus = 'success';
                        $textStatus = esc_html__('Active', 'listingpro-plugin');
                    } elseif ($invoiceStatus == "failed") {
                        $classStatus = 'danger';
                        $textStatus = esc_html__('Failed', 'listingpro-plugin');
                    } elseif ($invoiceStatus == "pending" || $invoiceStatus == "in progress") {
                        $classStatus = 'info';
                        $textStatus = esc_html__('Pending', 'listingpro-plugin');
                    }
                    $htmlReturn .= '<tr>';

                    $htmlReturn .= '<td class="manage-column column-categories">' . $Value->order_id . '</td>';
                    $htmlReturn .= '<td class="manage-column column-categories">' . date(get_option('date_format'), strtotime($Value->date)) . '</td>';
                    $htmlReturn .= '<td class="manage-column column-categories">' . $Value->payment_method . '</td>';
                    $htmlReturn .= '<td class="manage-column column-categories">' . $price . '</td>';
                    $buttunType = 'button';
                    if ($Value->payment_method == "wire") {

                        $buttunType = 'submit';

                        $htmlReturn .= '
						
						<td>
							<form class="posts-filter" method="POST">
								<input class="alert alert-' . $classStatus . '" type="' . $buttunType . '" value="' . $textStatus . '" >
								<input type="hidden" name="payment_submitt" value="proceed">
								<input type="hidden" name="order_id" value="' . $Value->order_id . '">
								<input type="hidden" name="post_id" value="' . $Value->post_id . '">
							</form>
						</td>
						
						';
                    } else {
                        $htmlReturn .= '<td><input class="alert alert-' . $classStatus . '" type="' . $buttunType . '" value="' . $textStatus . '" ></td>';
                    }

                    $deletelistidfield = '';
                    $delete_invoicen = 'delete_invoice';
                    if ($invoiceStatus == "pending" || $invoiceStatus == "in progress") {
                        $delete_invoicen = 'delete_invoicee';
                        $deletelistidfield = '<input type="hidden" name="list_id" value="' . $listid . '" />';
                    }

                    $htmlReturn .= '
							<td>
							<form class="wp-core-ui" method="post">
								<input type="submit" name="' . $delete_invoicen . '" class="button action" value="' . esc_html__('Delete', 'listingpro-plugin') . '" onclick="return window.confirm("Are you sure you want to proceed action?")" />
								<input type="hidden" name="main_id" value="' . $main_id . '" />
								' . $deletelistidfield . '
							</form>
																
						</td>';

                    $htmlReturn .= '
				<td>
							<a href="#" class="lp_watchthisinvoice" data-invoiceid="' . $main_id . '" data-type="listing"><span class="dashicons dashicons-visibility"></span></a>
							<div class="lobackspinner"></div>

					</td>';

                    $htmlReturn .= '</tr>';
                }
            } else {
                $htmlReturn = '<p style="width: 98%;position: absolute;padding: 20px;font-size: 16px;text-align: center;">' . esc_html__("Sorry! there is no result", "listingpro-plugin") . '</p>';
            }

            exit(json_encode($htmlReturn));
        }
    }

    add_action('wp_ajax_lp_filter_backend_invoice', 'lp_filter_backend_invoice');    // If called from admin panel


    /* ================function for filtering backend invoices ads====================== */
    if (!function_exists('lp_filter_backend_invoice_ads')) {
        function lp_filter_backend_invoice_ads()
        {
            $method = $_POST['method'];
            $status = $_POST['status'];
            $where = 'WHERE ';
            if (!empty($method) && !empty($status)) {
                $where .= "payment_method='$method' AND status='$status'";
            } elseif (!empty($method) && empty($status)) {
                $where .= "payment_method='$method' AND status IN ('pending', 'success', 'failed')";
            } elseif (empty($method) && !empty($status)) {
                $where .= "status='$status'";
            } elseif (empty($method) && empty($status)) {
                $where .= "status IN ('pending', 'success', 'failed')";
            }

            global $wpdb;
            $counter = 1;
            $currency_position = lp_theme_option('pricingplan_currency_position');
            $table = "listing_campaigns";
            $dbprefix = $wpdb->prefix;
            $table = $dbprefix . $table;
            $results = array();
            $htmlReturn = null;
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
                $query = "";
                $query = "SELECT * from $table  $where ORDER BY main_id DESC";
                $results = $wpdb->get_results($query);
            }
            if (!empty($results)) {
                foreach ($results as $Index => $Value) {

                    $invoiceStatus = $Value->status;
                    $main_id = $Value->main_id;
                    $listid = $Value->post_id;
                    $method = $Value->payment_method;
                    $cdate = '';
                    if ($method == 'wire') {
                        if ($invoiceStatus == 'pending') {
                            $cdate = esc_html__('N/A', 'listingpro-plugin');
                        } else {
                            $adid = get_post_meta($listid, 'campaign_id', true);
                            $cdate = get_the_date(get_option('date_format'), $adid);
                            $cdate = date_i18n(get_option('date_format'), strtotime($cdate));
                        }
                    } else {
                        $adid = $Value->post_id;
                        $cdate = get_the_date(get_option('date_format'), $adid);
                        $cdate = date_i18n(get_option('date_format'), strtotime($cdate));
                    }

                    $currency_sign = (isset($Value->currency) && $Value->currency != '') ? $Value->currency : listingpro_currency_sign();
                    $price         = $Value->price;
                    if ($currency_position == 'right') {
                        $price .= $currency_sign;
                    } else {
                        $price = $currency_sign . $price;
                    }


                    $classStatus = '';
                    $textStatus = '';
                    $invoiceStatus = $Value->status;
                    if ($invoiceStatus == "success") {
                        $classStatus = 'success';
                        $textStatus = esc_html__('Active', 'listingpro-plugin');
                    } elseif ($invoiceStatus == "failed") {
                        $classStatus = 'danger';
                        $textStatus = esc_html__('Failed', 'listingpro-plugin');
                    } elseif ($invoiceStatus == "pending" || $invoiceStatus == "in progress") {
                        $classStatus = 'info';
                        $textStatus = esc_html__('Pending', 'listingpro-plugin');
                    }
                    $htmlReturn .= '<tr>';

                    $htmlReturn .= '<td class="manage-column column-categories">' . $Value->transaction_id . '</td>';
                    $htmlReturn .= '<td class="manage-column column-categories">' . $cdate . '</td>';
                    $htmlReturn .= '<td class="manage-column column-categories">' . $Value->payment_method . '</td>';
                    $htmlReturn .= '<td class="manage-column column-categories">' . $price . '</td>';
                    $buttunType = 'button';
                    if ($Value->payment_method == "wire") {

                        $buttunType = 'submit';

                        $htmlReturn .= '
						
						<td>
							<form class="posts-filter" method="POST">
								<input class="alert alert-' . $classStatus . '" name="name="payment_submit"" type="' . $buttunType . '" value="' . $textStatus . '" >
								<input type="hidden" name="payment_submitt" value="proceed">
								<input type="hidden" name="order_id" value="' . $Value->transaction_id . '">
								<input type="hidden" name="post_id" value="' . $Value->post_id . '">
							</form>
						</td>
						
						';
                    } else {
                        $htmlReturn .= '<td><input class="alert alert-' . $classStatus . '" type="' . $buttunType . '" value="' . $textStatus . '" ></td>';
                    }

                    $deltecompleteinput = null;
                    if ($method == "wire" && ($invoiceStatus == "pending" || $invoiceStatus == "in progress")) {
                        $deltecompleteinput = '<input type="hidden" name="deletecomplete" value="yes" />';
                    }


                    $htmlReturn .= '
							<td>
							<form class="wp-core-ui" method="post">
								<input type="submit" name="delete_invoice_ads" class="button action" value="' . esc_html__('Delete', 'listingpro-plugin') . '" onclick="return window.confirm("Are you sure you want to proceed action?")" />
								<input type="hidden" name="main_id" value="' . $main_id . '" />
								<input type="hidden" name="listId" value="' . $listid . '" />
								' . $deltecompleteinput . '
							</form>
																
						</td>';

                    $htmlReturn .= '
				<td>
							<a href="#" class="lp_watchthisinvoice" data-invoiceid="' . $main_id . '" data-type="ads"><span class="dashicons dashicons-visibility"></span></a>
							<div class="lobackspinner"></div>

					</td>';

                    $htmlReturn .= '</tr>';
                }
            } else {
                $htmlReturn = '<p style="width: 98%;position: absolute;padding: 20px;font-size: 16px;text-align: center;">' . esc_html__("Sorry! there is no result", "listingpro-plugin") . '</p>';
            }

            exit(json_encode($htmlReturn));
        }
    }

    add_action('wp_ajax_lp_filter_backend_invoice_ads', 'lp_filter_backend_invoice_ads');    // If called from admin panel
    add_action('wp_ajax_nopriv_lp_filter_backend_invoice_ads', 'lp_filter_backend_invoice_ads');    // If called from front end

    /* ------------------------------------ */

    if (!function_exists('lp_theme_option')) {
        function lp_theme_option($optionID)
        {
            global $listingpro_options;
            if (isset($listingpro_options["$optionID"])) {
                $optionValue = $listingpro_options["$optionID"];
                return $optionValue;
            } else {
                return false;
            }
        }
    }


    add_action('wp_ajax_lp_get_child_cats', 'lp_get_child_cats');
    add_action('wp_ajax_nopriv_lp_get_child_cats', 'lp_get_child_cats');
    if (!function_exists('lp_get_child_cats')) {
        function lp_get_child_cats()
        {
            $parentID   =   $_POST['parentID'];
            $markup     =   '';
            $child_terms = get_terms('listing-category', array('hide_empty' => false, 'parent' => $parentID));
            if ($child_terms) {
                foreach ($child_terms as $child_term) {
                    $markup .=  '<label class="vc_checkbox-label"><input id="child_category_ids-' . $child_term->term_id . '" value="' . $child_term->term_id . '" class="wpb_vc_param_value child_category_ids checkbox" type="checkbox" name="child_category_ids"> ' . $child_term->name . '</label>';
                }
            }
            $json_attr  =   'json';
            $child_cats_result = json_encode(array('markup' => $markup, 'json_attr' => $json_attr));
            die($child_cats_result);
        }
    }


    /* =============adding more fileds on listing submit and edit=========== */
    if (!function_exists('lp_save_extra_fields_in_listing')) {
        function lp_save_extra_fields_in_listing($filedsArray, $listingID)
        {
            $newarray = array();
            $newarrayF = array();
            if (!empty($filedsArray) && !empty($listingID)) {
                if (isset($filedsArray['lp_feature'])) {
                    unset($filedsArray['lp_feature']);
                }
                foreach ($filedsArray as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $v) {
                            $newarrayF[$key . '-mfilter'][] = $key . '-' . $v;
                        }
                    } else {
                        $newarrayF[$key . '-mfilter'] = $key . '-' . $val;
                    }
                }

                return $newarrayF;
            }
        }
    }

    /* =============output function=========== */

    if (!function_exists('listing_elements_loop_cb')) {
        function listing_elements_loop_cb($el_id, $atts, $via_ajax = false)
        {
            ob_start();
            if ($el_id == 'listing_grids' || $el_id == 'claimed_listings_grids' || $el_id == 'listing_grids_by_id' || $el_id == 'listing_grids_with_coupons' || $el_id == 'listing_options') :
                $lp_class = '';
                if ($el_id == 'listing_grids') {
                    $lp_class = 'lp-row';
                }
                echo '<div class="' . $lp_class . ' padding-top-40 padding-bottom-40 clearfix">';
            endif;
            if (isset($atts['posts_ids'])) {
                $posts_ids = $atts['posts_ids'];
            } else {
                $posts_ids = '';
            }
            if (isset($atts['listing_layout'])) {
                $listing_layout = $atts['listing_layout'];
            } else {
                $listing_layout = '';
            }
            if (isset($atts['listing_grid_style'])) {
                $listing_grid_style = $atts['listing_grid_style'];
            } else {
                $listing_grid_style = 'grid_view1';
            }
            if (isset($atts['listing_list_style'])) {
                $listing_list_style = $atts['listing_list_style'];
            } else {
                $listing_list_style = '';
            }
            if (isset($atts['grid3_button_text'])) {
                $grid3_button_text = $atts['grid3_button_text'];
            } else {
                $grid3_button_text = '';
            }
            if (isset($atts['grid3_button_link'])) {
                $grid3_button_link = $atts['grid3_button_link'];
            } else {
                $grid3_button_link = '';
            }
            if ($el_id == 'listing_options' || $el_id == 'listing_tabs') {
                if (isset($atts['listing_per_page'])) {
                    $number_posts = $atts['listing_per_page'];
                } else {
                    $number_posts = 3;
                }
                if (isset($atts['listing_multi_options'])) {
                    $listing_multi_options = $atts['listing_multi_options'];
                } else {
                    $listing_multi_options = 'cat_view';
                }
                if (isset($atts['listing_loc']) && !empty($atts['listing_loc'])) {
                    $listing_loc = $atts['listing_loc'];
                    $listing_loc = explode(',', $listing_loc);
                } else {
                    $listing_loc = '';
                }
                if (isset($atts['listing_cat']) && !empty($atts['listing_cat'])) {
                    $listing_cat = $atts['listing_cat'];
                    $listing_cat =   explode(',', $listing_cat);
                } else {
                    $listing_cat = '';
                }

                if (isset($atts['listing_include']) && !empty($atts['listing_include'])) {
                    $include_children = $atts['listing_include'];
                } else {
                    $include_children = 0;
                }
                if (isset($atts['listing_opr']) && !empty($atts['listing_opr'])) {
                    $tax_relation = $atts['listing_opr'];
                } else {
                    $tax_relation = 'AND';
                }
            } else {
                if (isset($atts['number_posts'])) {
                    $number_posts = $atts['number_posts'];
                } else {
                    $number_posts = 3;
                }
            }

            $output = null;

            $type = 'listing';
            $posts_ids_arr  =   array();
            if (strpos($posts_ids, ',') !== false) {
                $posts_ids_arr  =   explode(',', $posts_ids);
                $number_posts   = '-1';
            } elseif (!empty($posts_ids)) {
                $posts_ids_arr[]    =   $posts_ids;
                $number_posts   = '-1';
            }

            if ($el_id == 'listing_grids') {
                $args = array(
                    'post_type' => $type,
                    'post_status' => 'publish',
                    'posts_per_page' => $number_posts,
                );
                $argsFOrADS = array(
                    'orderby' => 'rand',
                    'post_type' => $type,
                    'post_status' => 'publish',
                    'posts_per_page' => $number_posts,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'campaign_status',
                            'value'   => array('active'),
                            'compare' => 'IN',
                        ),
                        array(
                            'key'     => 'lp_random_ads',
                            'value'   => array('active'),
                            'compare' => 'IN',
                        ),
                    ),
                );
            } else {
                $args = array(
                    'post_type' => $type,
                    'post_status' => 'publish',
                    'posts_per_page' => $number_posts,
                    'post__in' => $posts_ids_arr
                );
            }


            $listingcurrency = '';
            $listingprice = '';
            $addClassListing = '';

            $listing_query = null;
            if ($el_id == 'listing_grids') {
                $listing_query = new WP_Query($argsFOrADS);
                $found = $listing_query->found_posts;

                if (($found == 0)) {
                    $listing_query = null;
                    $listing_query = new WP_Query($args);
                }
            } elseif ($el_id == 'listing_options') {
                if ($listing_multi_options == 'recent_view') {
                    $args = array(
                        'post_type'       => $type,
                        'post_status'     => 'publish',
                        'posts_per_page'  => $number_posts,
                        'order'           => 'DESC',

                    );
                } elseif ($listing_multi_options == 'location_view') {

                    $args = array(
                        'post_type' => $type,
                        'post_status'     => 'publish',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'location',
                                'field' => 'id',
                                'terms' => $listing_loc,
                                'include_children' => $include_children,
                            )
                        ),
                        'posts_per_page' => $number_posts,
                        'order'               => 'DESC'
                    );
                } elseif ($listing_multi_options == 'cat_view') {

                    $args = array(
                        'post_type' => $type,
                        'post_status'     => 'publish',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'listing-category',
                                'field' => 'id',
                                'terms' => $listing_cat,
                                'include_children' => $include_children,
                            )
                        ),
                        'posts_per_page' => $number_posts,
                        'order'               => 'DESC'
                    );
                } elseif ($listing_multi_options == 'location_cat_view') {
                    if ($listing_cat != '') {
                        $tax_query[] = array(
                            'taxonomy' => 'listing-category',
                            'field' => 'id',
                            'terms' => $listing_cat,
                            'include_children' => $include_children,
                        );
                    }
                    if ($listing_loc != '') {
                        $tax_query[] = array(
                            'taxonomy' => 'location',
                            'field' => 'id',
                            'terms' => $listing_loc,
                            'include_children' => $include_children,
                        );
                    }
                    $tax_query['relation'] = $tax_relation;
                    $args = array(
                        'post_type' => $type,
                        'post_status'     => 'publish',
                        'tax_query' => $tax_query,
                        'posts_per_page' => $number_posts,
                        'order'               => 'DESC'
                    );
                }

                $listing_query = new WP_Query($args);
            } elseif ($el_id == 'listing_tabs') {

                if ($listing_multi_options == 'location_view') {
                    $args = array(
                        'post_type' => $type,
                        'post_status'     => 'publish',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'location',
                                'field' => 'id',
                                'terms' => $listing_loc
                            )
                        ),
                        'posts_per_page' => $number_posts,
                        'order'               => 'DESC'
                    );
                } elseif ($listing_multi_options == 'cat_view') {
                    $args = array(
                        'post_type' => $type,
                        'post_status'     => 'publish',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'listing-category',
                                'field' => 'id',
                                'terms' => $listing_cat,
                                'include_children' => false
                            )
                        ),
                        'posts_per_page' => $number_posts,
                        'order'               => 'DESC'
                    );
                }
                $listing_query = new WP_Query($args);
            } elseif ($el_id == 'listing_grids_with_popular') {
                $args = array(
                    'post_type'       => $type,
                    'post_status'     => 'publish',
                    'posts_per_page' => $number_posts,
                    'meta_key' => 'post_views_count',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC'
                );
                $listing_query = new WP_Query($args);
            } elseif ($el_id == 'claimed_listings_grids') {
                $args = array(
                    'post_type'       => $type,
                    'post_status'     => 'publish',
                    'posts_per_page' => $number_posts,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'lp_listingpro_options',
                            'value'   => 'not_claimed',
                            'compare' => 'NOT LIKE'
                        ),
                        array(
                            'key'     => 'lp_listingpro_options',
                            'value'   => 'Not claimed',
                            'compare' => 'NOT LIKE'
                        ),
                    )
                );
                $listing_query = new WP_Query($args);
            } elseif ($el_id == 'listing_grids_with_coupons') {
                //new code 2.6.15
                $postin = array();
                $Cargs = array(
                    'post_type'       => $type,
                    'post_status'     => 'publish',
                    'posts_per_page' => -1,
                    'meta_key' => 'listing_discount_data',
                    'meta_compare' => 'EXISTS'
                );
                $coupon_query = new WP_Query($Cargs);
                if ($coupon_query->have_posts()) {
                    while ($coupon_query->have_posts()) : $coupon_query->the_post();
                        $dissData = get_post_meta(get_the_ID(), 'listing_discount_data', true);
                        foreach ($dissData as $k => $disData) {
                            if (in_array(get_the_ID(), $postin)) continue;

                            $discount_data = $disData;
                            $cdatatime = date("Y-m-d h:i A");
                            $exETime = '12:00 AM';
                            if (!empty($discount_data['disTimeE'])) {
                                $exETime = $discount_data['disTimeE'];
                            }
                            $exSTime = '12:00 AM';
                            if (!empty($discount_data['disTimeS'])) {
                                $exSTime = $discount_data['disTimeS'];
                            }
                            $expiry_date  = coupon_timestamp($discount_data['disExpE'], $exETime);
                            $date_start   = coupon_timestamp($discount_data['disExpS'], $exSTime);

                            $time_now = strtotime($cdatatime);

                            if (((!empty($expiry_date) && $time_now < $expiry_date) && (!empty($date_start) && $time_now > $date_start)) || $discount_data['disExpE'] == 0) {
                                $postin[] = get_the_ID();
                            } else {
                                if ((!empty($expiry_date) && $time_now < $expiry_date) && (!empty($date_start) && $time_now < $date_start)) {
                                    $postin[] = get_the_ID();
                                } else if (!empty($expiry_date)) {
                                    continue;
                                } else {
                                    $postin[] = get_the_ID();
                                }
                            }
                            continue;
                        }
                    endwhile;
                }

                if (is_array($postin) && empty($postin)) {
                    $postin = array(0);
                }

                $args = array(
                    'post_type'       => $type,
                    'post_status'     => 'publish',
                    'posts_per_page' => $number_posts,
                    'post__in'     => $postin,
                    'meta_key' => 'listing_discount_data',
                    'meta_compare' => 'EXISTS'
                );
                //end new code 2.6.15
                $listing_query = new WP_Query($args);
            } else {
                $listing_query = new WP_Query($args);
            }

            $post_count = 1;

            global $listingpro_options;
            $listing_views = $listingpro_options['listing_views'];

            $GLOBALS['listing_layout_element']  =   $listing_layout;
            if (!empty($GLOBALS['listing_layout_element']) || $GLOBALS['listing_layout_element'] != '') {
                $addClassListing    =   'listing_' . $listing_layout;
            } else {
                if ($listing_views == 'list_view') {
                    $addClassListing = 'listing_list_view';
                } elseif ($listing_views == 'grid_view') {
                    $addClassListing = 'listing_grid_view';
                } else {
                    $addClassListing = '';
                }
            }
            $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];

            if ($listing_mobile_view == 'app_view2' && wp_is_mobile() && $el_id == 'listing_grids') {
                if ($listing_query->have_posts()) {
                    echo '<div class="app-view-new-ads-slider">';
                    while ($listing_query->have_posts()) : $listing_query->the_post();

                        get_template_part('mobile/listing-loop-app-view-adds');

                    endwhile;
                    echo '</div>';
                } else {
                    echo '<p>No Listings found</p>';
                }
            } elseif (($listing_mobile_view == 'app_view2' || $listing_mobile_view == 'app_view') && wp_is_mobile() && $el_id == 'listing_tabs') {

                $terms_Arr  =   $listing_cat;
                $taxonomy   =   'listing-category';
                if ($listing_multi_options == 'location_view') {
                    $terms_Arr  =   $listing_loc;
                    $taxonomy   =   'location';
                }
            ?>
                <?php
                if (!$via_ajax && $el_id == 'listing_tabs') {
                ?>
                    <div class="single-tabber2 listing-tabs-element">
                        <ul class="row list-style-none clearfix" data-tabs="tabs">
                            <?php
                            $terms_counter  =   1;
                            foreach ($terms_Arr as $item) {
                                $active_tab =   '';
                                if ($terms_counter == 1) {
                                    $active_tab =   'active';
                                }
                                $term_Arr   =   get_term_by('id', $item, $taxonomy);
                                if ($term_Arr) {
                                    echo '<li class="' . $active_tab . '"><a href="#' . $term_Arr->slug . '" data-grid="' . $listing_grid_style . '" data-list="' . $listing_list_style . '" data-layout="' . $listing_layout . '" data-num="' . $number_posts . '" data-tax="' . $taxonomy . '" data-term="' . $item . '">' . $term_Arr->name . '</a></li>';
                                }
                                $terms_counter++;
                            }
                            ?>

                        </ul>
                    </div>
                <?php
                }
                ?>
                <div class="detail-page2-tab-content app-view-listing-tabs">
                    <div class="tab-content">
                        <div class="tab-pane active" id="listing-tabs-inner-container">
                            <?php
                            if ($listing_query->have_posts()) {
                                echo '<div class="app-view2-first-recent">';
                                echo '<div class="app-view-new-ads-slider">';
                                while ($listing_query->have_posts()) : $listing_query->the_post();
                                    get_template_part('mobile/listing-loop-app-view-adds');
                                endwhile;
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<p>No Listings found</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                if ($el_id == 'listing_tabs') {
                    $taxonomy   =   'listing-category';
                    if ($listing_multi_options == 'location_view') {
                        $terms_Arr  =  array();
                        if (!empty($listing_cat) && is_array($listing_cat)) {
                            $terms_Arr  =   explode(',', $listing_cat);
                        }
                        $terms_Arr  =   explode(',', $listing_loc);
                        $taxonomy   =   'location';
                    }
                ?>
                    <?php
                    if (!$via_ajax && $el_id == 'listing_tabs') {
                    ?>
                        <div class="single-tabber2 listing-tabs-element">
                            <ul class="row list-style-none clearfix" data-tabs="tabs">
                                <?php
                                $terms_counter  =   1;
                                if (!empty($terms_Arr) && (is_array($terms_Arr) || is_object($terms_Arr))) {
                                    foreach ($terms_Arr as $item) {
                                        $active_tab =   '';
                                        if ($terms_counter == 1) {
                                            $active_tab =   'active';
                                        }
                                        $term_Arr   =   get_term_by('id', $item, $taxonomy);
                                        if ($term_Arr) {
                                            echo '<li class="' . $active_tab . '"><a href="#' . $term_Arr->slug . '" data-grid="' . $listing_grid_style . '" data-list="' . $listing_list_style . '" data-layout="' . $listing_layout . '" data-num="' . $number_posts . '" data-tax="' . $taxonomy . '" data-term="' . $item . '">' . $term_Arr->name . '</a></li>';
                                        }
                                        $terms_counter++;
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    <?php
                    }
                    ?>

                    <div class="detail-page2-tab-content">
                        <div class="tab-content">
                            <div class="tab-pane active" id="listing-tabs-inner-container">


                        <?php
                    }
                    if ($listing_grid_style == 'grid_view5' && $listing_layout == 'grid_view') {
                        $GLOBALS['grid_col_class']  =   4;
                        $GLOBALS['trending_el']  =   true;

                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                            if (($listing_layout == 'grid_view') && ($listing_views == 'lp-list-view-compact' || $listing_views == 'grid_view_classic' || $listing_views == 'grid_view' || $listing_views == 'grid_view_v3' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                echo '<div class="map-view-list-container2">';
                            } else {
                                echo '<div class="map-view-list-container">';
                            }
                            if ($listing_query->have_posts()) {

                                while ($listing_query->have_posts()) : $listing_query->the_post();
                                    get_template_part('mobile/listing-loop-app-view');
                                endwhile;
                            } else {
                                echo '</p>No Listings Found</p>';
                            }
                            if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                echo '</div>';
                            } else {

                                echo '</div>';
                            }
                        } else {
                            if ($listing_query->have_posts()) {
                                echo '<div class="lp-listings">';
                                echo '<div class="row listing-slider">';

                                while ($listing_query->have_posts()) : $listing_query->the_post();
                                    get_template_part('templates/loop-grid-view');

                                endwhile;
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '</p>No Listings Found</p>';
                            }
                        }
                    }
                    if ($listing_grid_style == 'grid_view3' && $listing_layout == 'grid_view') {
                        $GLOBALS['grid_col_class']  =   4;
                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                            if (($listing_layout == 'grid_view') && ($listing_views == 'lp-list-view-compact' || $listing_views == 'grid_view_classic' || $listing_views == 'grid_view' || $listing_views == 'grid_view_v3' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                echo '<div class="map-view-list-container2">';
                            } else {
                                echo '<div class="map-view-list-container">';
                            }
                            if ($listing_query->have_posts()) {
                                while ($listing_query->have_posts()) : $listing_query->the_post();

                                    get_template_part('mobile/listing-loop-app-view');

                                endwhile;
                                wp_reset_postdata();
                            } else {
                                echo '</p>No Listings Found</p>';
                            }
                            if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                echo '</div>';
                            } else {

                                echo '</div>';
                            }
                        } else {

                            $output .=  '<div class="lp-section-content-container listingcampaings">';
                            $output .=  '    <div class="lp-listings grid-style">';
                            $output .=  '        <div class="row">';
                            if ($listing_query->have_posts()) {
                                $home_grid_counter  =   0;
                                ob_start();
                                while ($listing_query->have_posts()) : $listing_query->the_post();

                                    $home_grid_counter++;
                                    get_template_part('templates/loop-grid-view');
                                    if ($home_grid_counter % 3 == 0) {
                                        echo '<div class="clearfix"></div>';
                                    }

                                endwhile;
                                wp_reset_postdata();
                                $loop_temp  =   ob_get_contents();
                                ob_end_clean();
                                $output .=  $loop_temp;
                            } else {
                                echo '</p>No Listings Found</p>';
                            }
                            if (!empty($grid3_button_text) && isset($grid3_button_text)) {
                                $btn_href   =   '';
                                if (!empty($grid3_button_link)) {
                                    $btn_href   =   ' href="' . $grid3_button_link . '"';
                                }
                                $output .=  '    <div class="clearfix"></div><div class="more-listings"><a' . $btn_href . '>' . $grid3_button_text . '</a></div>';
                            }
                            $output .=  '        </div>';
                            $output .=  '    </div>';
                            $output .=  '</div>';
                        }
                    } else {
                        if ($listing_layout == 'list_view' && $listing_list_style == 'list_view_v2') {

                            if ($listing_query->have_posts()) {
                                if ($listing_mobile_view != 'app_view' || !wp_is_mobile()) {
                                    $campaign_layout = 'list';
                                    echo '<div class="lp-section-content-container homepage-listing-view2-element"> <div class="lp-listings list-style active-view">
                                            <div class="search-filter-response">
                                                <div class="lp-listings-inner-wrap">';
                                }

                                while ($listing_query->have_posts()) : $listing_query->the_post();

                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                        get_template_part('mobile/listing-loop-app-view');
                                    } else {
                                        get_template_part('templates/loop-list-view');
                                    }
                                endwhile;
                                if ($listing_mobile_view != 'app_view' || !wp_is_mobile()) {
                                    echo '</div></div></div></div>';
                                }
                            } else {
                                echo '<p style="padding: 40px;">No Listings Found</p>';
                            }
                        } else {


                            $output .= '
        <div class="listing-simple ' . $addClassListing . ' listingcampaings">
            <div class="lp-list-page-grid row" id="content-grids" >';
                            if ($listing_grid_style == 'grid_view1') {
                                if ($listing_query->have_posts()) {
                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                        if (($listing_layout == 'grid_view') && ($listing_views == 'lp-list-view-compact' || $listing_views == 'grid_view' || $listing_views == 'grid_view_v3' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2' || $listing_views == 'grid_view_classic')) {
                                            echo '<div class="map-view-list-container2">';
                                        } else {
                                            echo '<div class="map-view-list-containerlist">';
                                        }
                                    }
                                    while ($listing_query->have_posts()) : $listing_query->the_post();

                                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                            get_template_part('mobile/listing-loop-app-view');
                                        } else {
                                            get_template_part('listing-loop');
                                        }

                                    endwhile;
                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                        if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2' || $listing_views == 'grid_view_classic')) {
                                            echo '</div>';
                                        } else {

                                            echo '</div>';
                                        }
                                    }
                                    $output .= '<div class="md-overlay"></div>';
                                } else {
                                    echo '</p>No Listings Found</p>';
                                }
                            } elseif ($listing_grid_style == 'grid_view_classic') {
                                if ($listing_query->have_posts()) {
                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                        if (($listing_layout == 'grid_view') && ($listing_views == 'lp-list-view-compact' || $listing_views == 'grid_view' || $listing_views == 'grid_view_v3' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2' || $listing_views == 'grid_view_classic')) {
                                            echo '<div class="map-view-list-container2">';
                                        } else {
                                            echo '<div class="map-view-list-containerlist">';
                                        }
                                    }
                                    while ($listing_query->have_posts()) : $listing_query->the_post();

                                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                            get_template_part('mobile/listing-loop-app-view');
                                        } else {
                                            get_template_part('listing-loop-classic');
                                        }

                                    endwhile;
                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                        if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                            echo '</div>';
                                        } else {

                                            echo '</div>';
                                        }
                                    }
                                    $output .= '<div class="md-overlay"></div>';
                                } else {
                                    echo '</p>No Listings Found</p>';
                                }
                            } elseif ($listing_grid_style == 'grid_view2') {
                                if ($listing_query->have_posts()) {
                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                        if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2' || $listing_views == 'grid_view_classic')) {
                                            echo '<div class="map-view-list-container2">';
                                        } else {

                                            echo '<div class="map-view-list-containerlist">';
                                        }
                                    }
                                    while ($listing_query->have_posts()) : $listing_query->the_post();

                                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                            get_template_part('mobile/listing-loop-app-view');
                                        } else {
                                            get_template_part('templates/loop/loop2');
                                        }

                                    //$output .= ob_get_contents();

                                    endwhile;
                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                        if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                            echo '</div>';
                                        } else {

                                            echo '</div>';
                                        }
                                    }
                                    $output .= '<div class="md-overlay"></div>';
                                } else {
                                    echo '</p>No Listings Found</p>';
                                }
                            } elseif ($listing_grid_style == 'grid_view4' || $listing_grid_style == 'grid_view6') {
                                if ($listing_grid_style == 'grid_view4') {
                                    $GLOBALS['grid_view_element'] = 'grid_view4';
                                } elseif ($listing_grid_style == 'grid_view6') {

                                    $GLOBALS['grid_view_element'] = 'grid_view6';
                                }

                                if ($listing_query->have_posts()) {
                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {

                                        if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2' || $listing_views == 'grid_view_classic')) {
                                            echo '<div class="map-view-list-container2">';
                                        } else {

                                            echo '<div class="map-view-list-containerlist">';
                                        }
                                    }
                                    while ($listing_query->have_posts()) : $listing_query->the_post();


                                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {

                                            get_template_part('mobile/listing-loop-app-view');
                                        } else {
                                            if ($listing_grid_style == 'grid_view4') {
                                                get_template_part('listing-loop');
                                            } elseif ($listing_grid_style == 'grid_view6') {

                                                get_template_part('templates/loop/loop3');
                                            }
                                        }
                                    endwhile;
                                    if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                        if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                            echo '</div>';
                                        } else {
                                            echo '</div>';
                                        }
                                    }
                                    $output .= '<div class="md-overlay"></div>';
                                } else {
                                    echo '</p>No Listings Found</p>';
                                }
                            }
                            wp_reset_postdata();
                            global $postGridCount;
                            $postGridCount = '0';
                            $output .= '
            </div>
        </div>';
                        }
                    }
                    if ($el_id == 'listing_tabs') {
                        echo '</div>
                    </div>
                </div>';
                    }
                }
                if ($el_id == 'listing_grids' || $el_id == 'claimed_listings_grids' || $el_id == 'listing_grids_by_id' || $el_id == 'listing_grids_with_coupons' || $el_id == 'listing_options') :
                    echo '</div>';
                endif;
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();

                return $output;
            }
        }

        add_action('wp_ajax_listing_tabs_get_listings', 'listing_tabs_get_listings');
        add_action('wp_ajax_nopriv_listing_tabs_get_listings', 'listing_tabs_get_listings');
        if (!function_exists('listing_tabs_get_listings')) {
            function listing_tabs_get_listings()
            {
                $taxonomy       =   $_POST['taxonomy'];
                $term           =   $_POST['term'];
                $numPost        =   $_POST['numPost'];
                $layout         =   $_POST['layout'];
                $list           =   $_POST['list'];
                $grid           =   $_POST['grid'];

                $atts   =   array(
                    'listing_multi_options'       => 'cat_view',
                    'listing_loc'               => '',
                    'listing_cat'               => '',
                    'listing_per_page'           => '3',
                    'listing_layout'           => $layout,
                    'listing_grid_style'   => $grid,
                    'listing_list_style'   => $list,
                );
                $atts['listing_per_page']   =   $numPost;
                if ($taxonomy == 'listing-category') {
                    $atts['listing_multi_options']  =   'cat_view';
                    $atts['listing_cat']  =   $term;
                }
                if ($taxonomy == 'location') {
                    $atts['listing_multi_options']  =   'location_view';
                    $atts['listing_loc']  =   $term;
                }
                $return =   listing_elements_loop_cb('listing_tabs', $atts, true);
                die(json_encode($return));
            }
        }

        /* ==============users page filter, columns and export=========== */
        add_action('admin_footer', 'lp_export_users_bnt');
        if (!function_exists('lp_export_users_bnt')) {
            function lp_export_users_bnt()
            {
                $screen = get_current_screen();
                $user_type  =   'all';
                if (isset($_GET['users_type_top']) && $_GET['users_type_top'] == 'listing_owners') {
                    $user_type  =   'listing_owners';
                }
                if (isset($_GET['users_type_top']) && $_GET['users_type_top'] == 'general_users') {
                    $user_type  =   'general_users';
                }

                if ($screen->id != "users") {
                    return;
                }
                        ?>
                        <style type="text/css">
                            select[name="users_type_bottom"] {
                                display: none;
                            }

                            input#bottom {
                                display: none;
                            }
                        </style>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                jQuery('.page-title-action').after('<a href="<?php echo admin_url('users.php?download-lp-users=yes&user-type=' . $user_type); ?>" class="lp-export-users page-title-action">Export Users</a>');
                            });
                        </script>
                        <?php
                    }
                }
                if (is_user_logged_in() && current_user_can('list_users') && is_admin() && isset($_GET['download-lp-users']) && $_GET['download-lp-users'] == 'yes') {
                    $users  =   get_users();

                    $user_type  =   'all';
                    if (isset($_GET['user-type'])) {
                        $user_type  =   $_GET['user-type'];
                    }

                    $users_data =   array(
                        array('UserName', 'Full Name', 'Email', 'Phone', 'Listings'),
                    );
                    if ($user_type == 'listing_owners') {

                        foreach ($users as $user) {
                            $posts_count        =   count_user_posts($user->ID, 'listing');

                            if ($posts_count > 0) {
                                $user_email         =   $user->user_email;
                                $username           =   $user->user_login;
                                $full_name          =   $user->first_name . ' ' . $user->last_name;
                                $phone              =   get_user_meta($user->ID, 'phone', true);
                                $user_post_count    =   count_user_posts($user->ID, 'listing');

                                $users_data[]    =   array($username, $full_name, $user_email, $phone, $user_post_count);
                            }
                        }
                    }
                    if ($user_type == 'general_users') {

                        foreach ($users as $user) {
                            $posts_count        =   count_user_posts($user->ID, 'listing');
                            if ($posts_count == 0) {
                                $user_email         =   $user->user_email;
                                $username           =   $user->user_login;
                                $full_name          =   $user->first_name . ' ' . $user->last_name;
                                $phone              =   get_user_meta($user->ID, 'phone', true);
                                $user_post_count    =   count_user_posts($user->ID, 'listing');

                                $users_data[]    =   array($username, $full_name, $user_email, $phone, $user_post_count);
                            }
                        }
                    }
                    if ($user_type == 'all') {
                        foreach ($users as $user) {
                            $user_email         =   $user->user_email;
                            $username           =   $user->user_login;
                            $full_name          =   $user->first_name . ' ' . $user->last_name;
                            $phone              =   get_user_meta($user->ID, 'phone', true);
                            $user_post_count    =   count_user_posts($user->ID, 'listing');

                            $users_data[]    =   array($username, $full_name, $user_email, $phone, $user_post_count);
                        }
                    }




                    users_to_csv_download($users_data, "listingpro-users.csv");
                }
                function users_to_csv_download($array, $filename = "listingpro-users.csv", $delimiter = ",")
                {
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="' . $filename . '";');

                    $f = fopen('php://output', 'w');

                    foreach ($array as $line) {
                        fputcsv($f, $line, $delimiter);
                    }
                    fclose($f);
                    exit();
                }

                add_filter('manage_users_columns', 'lp_users_dashboard_table_columns');
                add_filter('manage_users_custom_column', 'lp_users_dashboard_table_columns_content', 10, 3);

                function lp_users_dashboard_table_columns($column)
                {
                    $column['listings'] = 'Listings';
                    return $column;
                }
                function lp_users_dashboard_table_columns_content($val, $column_name, $user_id)
                {
                    if ($column_name == 'listings') {
                        return count_user_posts($user_id, 'listing');
                    }
                    return $val;
                }



                function add_user_type_filter_options($which)
                {
                    $st = '<select name="users_type_%s" style="float:none;margin-left:10px;">
<option value="">%s</option>%s</select>';


                    $options = '<option value="listing_owners">Listing Owners</option>
	<option value="general_users">General Users</option>';


                    $select = sprintf($st, $which, __('Users Type...'), $options);

                    echo $select;
                    submit_button(__('Filter'), array(), $which, false);
                }
                add_action('restrict_manage_users', 'add_user_type_filter_options');

                function filter_users_by_type($query)
                {

                    global $pagenow;
                    if (is_admin() && 'users.php' == $pagenow) {
                        $top    =   '';
                        $bottom =   '';

                        if (isset($_GET['users_type_top'])) {
                            $top    =   $_GET['users_type_top'];
                        }
                        if ((!empty($top) || !empty($bottom)) && ($top == 'listing_owners' || $bottom == 'listing_owners')) {
                            $query->set('orderby', 'post_count');
                            $query->set('order', 'DESC');
                            $query->set('has_published_posts', array('listing'));
                        } elseif ((!empty($top) || !empty($bottom)) && ($top == 'general_users' || $bottom == 'general_users')) {
                            $general_users_array    =   array();


                            global $wpdb;
                            $table_users    =   $wpdb->prefix . 'users';

                            $results = $wpdb->get_results("SELECT * FROM $table_users", ARRAY_A);
                            if (!empty($results)) {
                                foreach ($results as $result) {
                                    $posts_count        =   count_user_posts($result['ID'], 'listing');
                                    if ($posts_count == 0) {
                                        $general_users_array[]    =   $result['ID'];
                                    }
                                }
                            }
                            $query->set('include', $general_users_array);
                        }
                    }
                }
                add_filter('pre_get_users', 'filter_users_by_type');

                /* ==============Per month price show in yearly price plan=========== */
                if (!function_exists('lp_show_monthly_plan_price')) {
                    function lp_show_monthly_plan_price($plan_id)
                    {
                        $returnPrice = '';
                        $withtaxprice = '';
                        if (lp_theme_option('listingpro_permonth_price_in_plan') == 'yes') {
                            $plan_duration_type = get_post_meta($plan_id, 'plan_duration_type', true);
                            if ($plan_duration_type == "yearly") {
                                $taxOn = lp_theme_option('lp_tax_swtich');
                                if ($taxOn == "1") {
                                    $showtaxwithprice = lp_theme_option('lp_tax_with_plan_swtich');
                                    if ($showtaxwithprice == "1") {
                                        $withtaxprice = true;
                                    }
                                }
                                $plan_price = get_post_meta($plan_id, 'plan_price', true);
                                if ($withtaxprice == "1") {
                                    $taxrate = lp_theme_option('lp_tax_amount');
                                    $taxprice = (float)(($taxrate / 100) * $plan_price);
                                    $plan_price = (float)$plan_price + (float)$taxprice;
                                    $plan_price = round($plan_price, 2);
                                }
                                if (!empty($plan_price)) {
                                    $returnPrice = $plan_price / 12;
                                    $returnPrice = round($returnPrice, 2);
                                    if (lp_theme_option('pricingplan_currency_position') == "left") {
                                        $returnPrice = listingpro_currency_sign() . $returnPrice;
                                    } else {
                                        $returnPrice .= listingpro_currency_sign();
                                    }
                                }
                            }
                        }
                        return $returnPrice;
                    }
                }

                /* ==================filter listing based on price plan at backend listing============== */

                if (!function_exists('lp_sort_listings_by_priceplan')) {
                    function lp_sort_listings_by_priceplan($query)
                    {
                        global $pagenow;
                        // Get the post type
                        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
                        if (is_admin() && $pagenow == 'edit.php' && $post_type == 'listing' && isset($_GET['price-plan-filter']) && $_GET['price-plan-filter'] != 'all') {
                            $query->query_vars['meta_key'] = 'plan_id';
                            $query->query_vars['meta_value'] = $_GET['price-plan-filter'];
                            $query->query_vars['meta_compare'] = '=';
                        }
                    }
                }
                add_filter('parse_query', 'lp_sort_listings_by_priceplan');

                /* =================check if any of plan is published or exits======= */
                if (!function_exists('lp_plan_is_published')) {
                    function lp_plan_is_published($termid)
                    {
                        $check = false;
                        $args = null;
                        $args = array(
                            'post_type' => 'price_plan',
                            'posts_per_page' => -1,
                            'post_status' => 'publish',
                            'meta_query' => array(
                                array(
                                    'key' => 'lp_selected_cats',
                                    'value' => $termid,
                                    'compare' => 'LIKE',
                                ),
                            ),
                        );
                        $cat_Plan_Query = null;
                        $cat_Plan_Query = new WP_Query($args);
                        if ($cat_Plan_Query->have_posts()) {
                            $check = true;
                        }

                        return $check;
                    }
                }



                /* =================making new function for wp_mail to be used in theme only======= */
                if (!function_exists('LP_mail')) {
                    function LP_mail($to, $subject, $message, $headers)
                    {
                        $return =   false;
                        if (wp_mail($to, $subject, $message, $headers)) {
                            $return =   true;
                        }
                        return $return;
                    }
                }
                if (!function_exists('LP_mail_header_headers_append_filter')) {
                    function LP_mail_header_headers_append_filter()
                    {
                        add_filter('wp_mail_from', 'listingpro_mail_from');
                        add_filter('wp_mail_from_name', 'listingpro_mail_from_name');
                    }
                }
                if (!function_exists('LP_mail_header_headers_rf')) {
                    function LP_mail_header_headers_rf()
                    {
                        remove_filter('wp_mail_from', 'listingpro_mail_from');
                        remove_filter('wp_mail_from_name', 'listingpro_mail_from_name');
                    }
                }
                /* =================How to get current URL======= */
                if (!function_exists('LP_current_location')) {
                    function LP_current_location()
                    {
                        if (
                            isset($_SERVER['HTTPS']) &&
                            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
                            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
                        ) {
                            $protocol = 'https://';
                        } else {
                            $protocol = 'http://';
                        }
                        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    }
                }

                /* ======================listingpro addons udpates========================= */
                add_action('admin_notices', 'lp_notice_addons_updates');
                if (!function_exists('lp_notice_addons_updates')) {
                    function lp_notice_addons_updates()
                    {
                        $check_addons_updates  =    get_option('lp_addons_updates');
                        if ($check_addons_updates && is_array($check_addons_updates) && count($check_addons_updates['available_updates']) > 0) {
                        ?>
                            <div class="notice notice-warning is-dismissible">
                                <h3>Listing Addon Updates</h3>
                                <p>Updates are aviallable for following listingpro addons.</p>
                                <ul>
                                    <?php
                                    foreach ($check_addons_updates['available_updates'] as $check_addons_update) {

                                        if ($check_addons_update == 'listingpro-lead-form/plugin.php') {
                                            echo '<li><strong>Lead Form</strong></li>';
                                        }
                                        if ($check_addons_update == 'listingpro-bookings/listingpro-bookings.php') {
                                            echo '<li><strong>Appointments</strong></li>';
                                        }
                                    }
                                    ?>
                                </ul>
                                <a href="<?php echo admin_url('admin.php?page=lp-cc-addons'); ?>">Click Here</a> to go to addons page for update.
                            </div>
                            <?php
                        }
                    }
                }

                if (!wp_next_scheduled('lp_addons_check_updates_cron')) {
                    wp_schedule_event(time(), 'daily', 'lp_addons_check_updates_cron');
                }
                add_action('lp_addons_check_updates_cron', 'lp_addons_check_updates');
                if (!function_exists('lp_addons_check_updates')) {
                    function lp_addons_check_updates()
                    {
                        $check_addons_updates  =    get_option('lp_addons_updates');
                        $license_key    =   get_option('active_license');
                        if ($license_key) {
                            update_option('lp_addons_updates', 'here');
                            $installed_plugins  =   get_plugins();
                            $addons_arr =   array(
                                'listingpro-lead-form/plugin.php',
                                'listingpro-bookings/listingpro-bookings.php'
                            );
                            $api_url    =   CRIDIO_API_URL . 'addonupdate/' . $license_key;

                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_URL, $api_url);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                            $response = curl_exec($ch);
                            curl_close($ch);

                            $response   =   json_decode($response);
                            $response   =   (array) $response;

                            $available_updates  =   (array)$response['updates'];
                            $lead_form_files    =   (array)$response['lead_form_files'];
                            $appointment_files  =   (array)$response['appointment_files'];

                            $update_required    =   array();
                            foreach ($addons_arr as $item) {
                                if (array_key_exists($item, $installed_plugins)) {
                                    $current_addon_v    =   $installed_plugins[$item]['Version'];
                                    if ($available_updates[$item] > $current_addon_v) {
                                        $update_required[]  =   $item;
                                    }
                                }
                            }
                            $lp_addon_update_details    =   array(
                                'available_updates' =>  $update_required,
                                'lead_form_files'   =>  $lead_form_files,
                                'appointment_files' =>  $appointment_files
                            );
                            update_option('lp_addons_updates', $lp_addon_update_details);
                        }
                    }
                }



                //functions below are moved

                if (!function_exists('attendees_to_csv_download')) {
                    function attendees_to_csv_download($array, $filename = "attendees.csv", $delimiter = ",")
                    {
                        header('Content-Type: application/csv');
                        header('Content-Disposition: attachment; filename="' . $filename . '";');

                        ob_end_clean();
                        $f = fopen('php://output', 'w');

                        foreach ($array as $line) {
                            fputcsv($f, $line, $delimiter);
                        }
                        fclose($f);
                        exit();
                    }
                }


                if (!function_exists('download_files_from_server')) {
                    function download_files_from_server($files_arr, $key)
                    {
                        foreach ($files_arr as $filename) {
                            $ch = curl_init();
                            $source = CRIDIO_FILES_URL . '/' . $filename;
                            curl_setopt($ch, CURLOPT_URL, $source);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $data = curl_exec($ch);
                            curl_close($ch);

                            $destination    =   WP_PLUGIN_DIR . '/listingpro-plugin/assets/js/' . $filename;

                            $file = fopen($destination, "w+");
                            fputs($file, $data);
                            fclose($file);
                        }
                    }
                }

                /* ===========================listingpro remove version from css and js======================== */
                if (!function_exists('listingpro_remove_scripts_styles_version')) {
                    function listingpro_remove_scripts_styles_version($src)
                    {
                        if (strpos($src, 'ver=') &&  strpos($src, 'themes/listingpro') &&  strpos($src, 'plugins/listingpro')) {
                            $src = remove_query_arg('ver', $src);
                        }
                        return $src;
                    }
                }
                add_filter('style_loader_src', 'listingpro_remove_scripts_styles_version', 9999);
                add_filter('script_loader_src', 'listingpro_remove_scripts_styles_version', 9999);

                add_action('wp_ajax_lp_purchase_days', 'lp_purchase_days');    // If called from admin panel
                add_action('wp_ajax_nopriv_lp_purchase_days', 'lp_purchase_days');
                if (!function_exists('lp_purchase_days')) {
                    function lp_purchase_days()
                    {
                        $Plan_id = sanitize_text_field($_POST['Plan_id']);
                        if (isset($Plan_id) && $Plan_id != '' && $Plan_id != 0) {
                            $d = get_post_meta($Plan_id, 'plan_time', true);
                            $days = array('lp_days' => $d);
                        } elseif ($Plan_id == 0) {
                            $days = array('lp_days' => '');
                        }
                        die(json_encode($days));
                    }
                }

                if (!function_exists('lp_tuts_and_new_admin_notice')) {
                    function lp_tuts_and_new_admin_notice()
                    {
                        if (isset($_GET['taxonomy'])) {
                            if ($_GET['taxonomy'] == 'listing-category') {
                                if (!isset($_COOKIE['dismiss_admin_notice_tutorial_lpcat'])) {
                                    wp_enqueue_script('lp-fleeq-popup', 'https://sdk.fleeq.io/fleeq-sdk-light.js', null, '5.6.6', false);
                            ?>
                                    <div class="notice notice-success is-dismissible dismiss_admin_notice" data-notice-cookie="dismiss_admin_notice_tutorial_lpcat" style="display: flex;justify-content: flex-start;align-items: center;">
                                        <p style="display: inline-flex;justify-content: flex-start;align-items: center;"><i class="fa fa-video-camera" style="font-size: 24px;margin-right: 15px;"></i>&nbsp;<?php echo ' Watch How&nbsp;<strong>Listing Categories Works</strong>&nbsp;</p>'; ?>
                                        <div style="margin: 2px 0 0 0;color: #135e96;" class="guidez3rdpjs-modal" data-key="3cachydpwb-5j7p7szk8a" data-mtype="g">Click me</div>
                                    </div>
                                <?php
                                }
                            }
                            if ($_GET['taxonomy'] == 'features') {
                                if (!isset($_COOKIE['dismiss_admin_notice_tutorial_feature'])) {
                                    wp_enqueue_script('lp-fleeq-popup', 'https://sdk.fleeq.io/fleeq-sdk-light.js', null, '5.6.6', false);
                                ?>
                                    <div class="notice notice-success is-dismissible dismiss_admin_notice" data-notice-cookie="dismiss_admin_notice_tutorial_feature" style="display: flex;justify-content: flex-start;align-items: center;">
                                        <p style="display: inline-flex;justify-content: flex-start;align-items: center;"><i class="fa fa-video-camera" style="font-size: 24px;margin-right: 15px;"></i>&nbsp;<?php echo ' Watch How&nbsp;<strong>Listing Features Works</strong>&nbsp;</p>'; ?>
                                        <div style="margin: 2px 0 0 0;color: #135e96;" class="guidez3rdpjs-modal" data-key="pimwugvt3x-au4nvbwu4n" data-mtype="g">Click me</div>
                                    </div>
                                <?php
                                }
                            }
                        }
                    }
                }
                add_action('admin_notices', 'lp_tuts_and_new_admin_notice');

                add_action('admin_notices', 'lp_notice_customization');
                if (!function_exists('lp_notice_customization')) {
                    function lp_notice_customization()
                    {
                        $content = get_option('lp_overview_api_content');
                        $theme_activation = get_option('listingpro_activation_date');
                        $diff_in_seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($theme_activation);
                        $diff_in_days = floor($diff_in_seconds / (60 * 60 * 24));

                        $current_screen = get_current_screen();
                        if ($current_screen->id === 'dashboard') {
                            if (isset($content['listingpro_options']['zone0_content']) && !empty($content['listingpro_options']['zone0_content'])) { ?>
                                <div class="lp-listingpro-zone zone-0">
                                    <?php echo wp_kses_post($content['listingpro_options']['zone0_content']); ?>
                                </div>
                            <?php }
                        }
                        $bases = array('lp-flags', 'lp-listings-invoices', 'lp-listings-subscription', 'listingpro');
                        $post_types = array('listing', 'form-fields', 'price_plan', 'lp-ads', 'events', 'lp-reviews', 'lp-claims');
                        if (in_array($current_screen->parent_base, $bases)  || in_array($current_screen->post_type, $post_types)) {
                            if (isset($content['listingpro_options']['zone2_content']) && !empty($content['listingpro_options']['zone2_content'])) { ?>
                                <div class="lp-listingpro-zone zone-2">
                                    <?php echo wp_kses_post($content['listingpro_options']['zone2_content']); ?>
                                </div>
                        <?php }
                        }

                        ?>
                        <?php
                        if (isset($content['listingpro_options']['lp-choose-listingpro-notice']) && !empty($content['listingpro_options']['lp-choose-listingpro-notice'])) {
                            if (isset($_COOKIE['listingpro_choosen'])) {
                            } else {
                        ?>
                                <div class="notice notice-success lp-free-qoute listingpro-choosen" data-notice-cookie="listingpro_choosen" data-cookie-days="<?php echo $content['listingpro_options']['lp-choose-listingpro-dismiss-days']; ?>">
                                    <div class="lp-free-qoute-left">
                                        <div class="lp-free-qoute-content">
                                            <h3>Thank You for Choosing ListingPro WordPress Directory Theme!</h3>
                                            <p>Since 2017, ListingPro helped over 30,000 customers worldwide, from entrepreneurs to agencies. Our 20+ extensions, includes solutions like MedicalProWP and EventProWP.</p>
                                            <a style="cursor:pointer;margin-left:0px; padding-left:0px" class="lp_get_quote" target="_blank" href="<?php echo  'https://listingprowp.com/update-logs'; ?>">Update Log</a>
                                            | <a style="cursor:pointer;margin-right:5px;" class="lp_get_quote" target="_blank" href="<?php echo isset($content['listingpro_options']['docs_link']) ? esc_url($content['listingpro_options']['docs_link']) : 'https://docs.listingprowp.com/'; ?>">Docs</a>
                                            | <a style="cursor:pointer;margin-right:5px;" class="lp_get_quote" target="_blank" href="<?php echo isset($content['listingpro_options']['help_link']) ? esc_url($content['listingpro_options']['help_link']) : 'https://help.listingprowp.com/'; ?>">Helpdesk</a>
                                            | <a style="cursor:pointer;margin-right:5px;" class="lp_get_quote" target="_blank" href="<?php echo isset($content['listingpro_options']['addon_link']) ? esc_url($content['listingpro_options']['addon_link']) : 'https://listingprowp.com/plugins/'; ?>">Add-ons</a>
                                            | <a style="cursor:pointer;margin-right:5px;" class="lp_get_quote" target="_blank" href="<?php echo  'https://listingprowp.com/plugins/?lp-tax-filter%5Bdemos%5D=on'; ?>">Demos</a>
                                            | <a style="cursor:pointer;margin-right:5px;" class="lp_get_quote" target="_blank" href="<?php echo  'https://listingprowp.com/plugins/?lp-tax-filter%5Bsolutions%5D=on'; ?>">Solutions</a>
                                            | <span class="lp-need-quote">Need Customization?<a style="cursor:pointer;padding-left:3px" class="lp_get_quote" target="_blank" href="<?php echo  'https://listingprowp.com/services/custom/'; ?>"><strong class="free-quote">Request Free Quote</strong></a></span>
                                        </div>
                                    </div>
                                    <div class="lp-free-qoute-right">
                                        <a style="cursor:pointer;" href="#"><button style="cursor: pointer;    padding-left: 30px;    padding-top: 5px;    background: none;    font-size: 12px;    border: none" type="button" class="lp_remind_melater btn btn-primary">Dismiss for <?php echo $content['listingpro_options']['lp-choose-listingpro-dismiss-days']; ?> days</button></a>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                        <?php
                        if (isset($content['listingpro_options']['unlock-bundles']) && !empty($content['listingpro_options']['unlock-bundles'])) {
                            if (isset($_COOKIE['unlock_bundle']) || $diff_in_days < 7) {
                            } else {
                        ?>
                                <div class="notice notice-success lp-free-qoute unlock-bundle" data-notice-cookie="unlock_bundle" data-cookie-days="<?php echo $content['listingpro_options']['unlock-bundles-dismiss-days']; ?>">
                                    <div class="lp-free-qoute-left">
                                        <div class="lp-free-qoute-bullborn">
                                            <img src="<?php echo LISTINGPRO_PLUGIN_URI . '/images/unlock-bundle.png'; ?>">
                                        </div>
                                        <div class="lp-free-qoute-content">
                                            <h3>Unlock ListingPro Lifetime Bundle Deals – Save Up to 95% Off!</h3>
                                            <p>Get lifetime access to premium extensions with bundle deals including all-access bundle.</p>
                                            <a style="cursor:pointer;margin-right:5px;color:#0040FF;font-weight:bold;" class="lp_get_quote" target="_blank" href="<?php echo  'https://listingprowp.com/deals/'; ?>">Download Now</a>
                                        </div>
                                    </div>
                                    <div class="lp-free-qoute-right">
                                        <a style="cursor:pointer;" class="lp_get_quote" href="#"><button style="cursor: pointer;    padding-left: 30px;    padding-top: 5px;    background: none;    font-size: 12px;    border: none;    color: black;" type="button" class="lp_remind_melater btn btn-primary">Dismiss for <?php echo $content['listingpro_options']['unlock-bundles-dismiss-days']; ?> days</button></a>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                        <?php if (!isset($_COOKIE['lp_lifetime_50_offer'])) { ?>
                            <div class="notice notice-success lp-free-qoute unlock-bundle lp-lifetime-50-offer" data-notice-cookie="lp_lifetime_50_offer" data-cookie-days="7">
                                <div class="lp-free-qoute-left">
                                    <div class="lp-free-qoute-bullborn">
                                        <img src="<?php echo LISTINGPRO_PLUGIN_URI . '/images/seo-repair-kit.png'; ?>">
                                    </div>
                                    <div class="lp-free-qoute-content">
                                        <h3>🎉 Exclusive Offer for ListingPro Users – Unlock 50% OFF – Lifetime Deal!</h3>
                                        <p>As a valued ListingPro user, you get exclusive access to our premium plan at half the price — forever. No recurring price hikes. No hidden conditions. Upgrade today and lock in your lifetime savings.</p>
                                        <p></p>
                                        <p>👉 <a style="cursor:pointer;color:#0040FF;font-weight:bold;" class="lp_get_quote" target="_blank" href="<?php echo esc_url('https://seorepairkit.com/pricing/'); ?>">Unlock Your Exclusive 50% Lifetime Deal</a> — Apply Code: <strong>LISTINGPRO-LIFETIME-50</strong></p>
                                    </div>
                                </div>
                                <div class="lp-free-qoute-right">
                                    <a style="cursor:pointer;" href="#"><button style="cursor: pointer; padding-left: 30px; padding-top: 5px; background: none; font-size: 12px; border: none; color: black;" type="button" class="lp_remind_melater btn btn-primary">Dismiss for 7 days</button></a>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                        if (isset($content['listingpro_options']['wp-vender']) && !empty($content['listingpro_options']['wp-vender'])) {
                            if (isset($_COOKIE['wp_vender'])) {
                            } else {
                        ?>
                                <div class="notice notice-success lp-free-qoute wp-vender" data-notice-cookie="wp_vender" data-cookie-days="<?php echo $content['listingpro_options']['wp-vender-dismiss-days']; ?>">
                                    <div class="lp-free-qoute-left">
                                        <div class="lp-free-qoute-bullborn">
                                            <img src="<?php echo LISTINGPRO_PLUGIN_URI . '/images/wpvender.png'; ?>">
                                        </div>
                                        <div class="lp-free-qoute-content">
                                            <h3>Need an Expert for ListingPro? Try WPVender!</h3>
                                            <p>Fast-track your ListingPro project with WPVender, the official service provider – <strong>Request quote within the first 15 days to get 15% off!</strong></p>
                                            <a style="cursor:pointer;margin-right:5px;" class="lp_get_quote free-quote" target="_blank" href="<?php echo  'https://wpvender.com/service/listingpro-customization-service'; ?>">Get Free Project Quote</a>
                                            | <a style="cursor:pointer;margin-right:5px;" class="lp_get_quote" target="_blank" href="<?php echo  'https://wpvender.com/book-meeting/'; ?>">Book a Meeting</a>
                                            | <a style="cursor:pointer;margin-right:5px;" class="lp_get_quote" target="_blank" href="<?php echo  'https://api.whatsapp.com/send/?phone=18445274346&text&type=phone_number&app_absent=0'; ?>">Chat via WhatsApp</a>
                                        </div>
                                    </div>
                                    <div class="lp-free-qoute-right">
                                        <a style="cursor:pointer;" href="#"><button style="cursor: pointer;    padding-left: 30px;    padding-top: 5px;    background: none;    font-size: 12px;    border: none" type="button" class="lp_remind_melater btn btn-primary">Dismiss for <?php echo $content['listingpro_options']['wp-vender-dismiss-days']; ?> days</button></a>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>

                        <?php
                        if (isset($content['listingpro_options']['rate-listingpro']) && !empty($content['listingpro_options']['rate-listingpro'])) {
                            if (isset($_COOKIE['rate_listingpro'])  || $diff_in_days < 45) {
                            } else {
                        ?>
                                <div class="notice notice-success lp-free-qoute rate-listingpro" data-notice-cookie="rate_listingpro" data-cookie-days="<?php echo $content['listingpro_options']['rate-listingpro-dismiss-days']; ?>">
                                    <div class="lp-free-qoute-left">
                                        <div class="lp-free-qoute-bullborn">
                                            <img src="<?php echo LISTINGPRO_PLUGIN_URI . '/images/logo-admin.png'; ?>">
                                        </div>
                                        <div class="lp-free-qoute-content">
                                            <h3>Please let us know how was your experience with ListingProWP <a href="https://themeforest.net/item/listingpro-multipurpose-directory-theme/reviews/19386460" style="text-decoration:none" target="_blank">😍 😀 🙂</a></h3>
                                        </div>
                                    </div>
                                    <div class="lp-free-qoute-right">
                                        <a style="cursor:pointer;" class="lp_get_quote" href="#"><button style="cursor: pointer;    padding-left: 30px;    padding-top: 5px;    background: none;    font-size: 12px;    border: none;    color: black;" type="button" class="lp_remind_melater btn btn-primary">Dismiss for <?php echo $content['listingpro_options']['rate-listingpro-dismiss-days']; ?> days</button></a>
                                    </div>
                                </div>
                    <?php
                            }
                        }
                    }
                }

                if (!function_exists('lp_open_chat_box')) {
                    function lp_open_chat_box()
                    {
                        if (!wp_is_mobile()) {
                            echo '
				<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>
				<script type="text/javascript">window.Beacon(\'init\', \'ecc007cc-da1d-4641-ab99-d3185702aee9\')</script>
				<script type="text/javascript">window.Beacon(\'open\');
					Beacon(\'config\', {
					enableSounds: true,
					docsEnabled: false,
					messagingEnabled:true,
					messaging: {
						chatEnabled: true,
						contactForm: {
						showName: true,
						showGetInTouch:true,
						},
					},
					labels: {
						
						wereHereToHelp: "Exclusive portal for Customization Services.",
						whatMethodWorks: "Get solutions for your customized requirements.",

						messageButtonLabel: "Email",
						noTimeToWaitAround:"Send us an email to get a free quote for  personalized  customization solutions.",
						emailValidationLabel: \'Please use a valid email address\',

						chatButtonLabel:"Chat",
						chatHeadingTitle: "Chat with our team",
						chatHeadingSublabel: "We will be with you soon",
						chatbotName:"Listingpro Team",
						chatButtonDescription: "You are one step away to instantly contact the customization team.",
						chatbotGreet:"Hi there! You have reached the customization team. Our service manager will respond shortly.",
					},
				})
				</script>
				';
                        }
                    }
                }

                if (!function_exists('is_admin_dashboard')) {
                    function is_admin_dashboard()
                    {
                        global $pagenow;
                        return $pagenow === 'index.php' && is_admin();
                    }
                }

                function admin_inline_js()
                {
                    if (is_admin_dashboard()) {
                        lp_open_chat_box();
                    }
                }
                add_action('admin_print_scripts', 'admin_inline_js');


                add_action('wp_dashboard_setup', 'lp_dashboard_widget', 99);
                function lp_dashboard_widget()
                {
                    // Add a custom dashboard widget
                    wp_add_dashboard_widget(
                        'lp-dashboard-overview', // Widget ID
                        'Listingpro Overview',       // Widget Title
                        'lp_dashboard_overview_widget' // Callback function to display the widget content
                    );
                }

                function lp_dashboard_overview_widget()
                {
                    $content = get_option('lp_overview_api_content');
                    if (empty($content)) {
                        get_option_data();
                        $content = get_option('lp_overview_api_content');
                    }

                    $parent_theme = wp_get_theme('listingpro'); // Replace 'parent-theme-folder-name' with the actual folder name of your parent theme
                    // Get parent theme version
                    $parent_theme_version = '2.9';
                    if (isset($parent_theme)) {
                        $parent_theme_version = $parent_theme->get('Version');
                    }
                    ?>
                    <div class="lp-overview__header">
                        <div class="lp-overview-parent-logo">
                            <div class="lp-overview__logo">
                                <div class="lp-logo-wrapper"><img src="<?php echo get_template_directory_uri() . '/assets/images/vcicon.png'; ?>"></div>
                            </div>
                            <div class="lp-overview__versions">
                                <span class="lp-overview__version"><?php echo esc_html__('ListingproWP', 'listingpro-plugin'); ?> v<?php echo $parent_theme_version; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                                                                                                                    ?></span>
                            </div>
                        </div>
                        <div class="lp-overview__create">
                            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=listing')); ?>" class="button"><span aria-hidden="true" class="dashicons dashicons-plus"></span> <?php echo esc_attr($content['listingpro_options']['create_listing']); ?></a>
                        </div>
                    </div>
                    <?php if (!empty($content)) {
                    ?>
                        <?php
                        $args = array(
                            'post_type'      => 'listing',
                            'post_status'    => 'publish',
                            'orderby'        => 'modified',
                            'order'          => 'DESC',
                            'posts_per_page' => 3, // Change this number as needed
                        );

                        $recent_listings = new WP_Query($args);

                        if ($recent_listings->have_posts()) {
                            echo '<h3 class="lp-titles">' . esc_html__('Recently Edited Listings', 'listingpro-plugin') . '</h3>';
                            while ($recent_listings->have_posts()) {
                                $recent_listings->the_post();
                                // Display title with edit link and last modified date
                                echo '<p class="list-modified-listing"> <a href="' . esc_url(get_post_permalink(get_the_ID())) . '" target="_blank">' . get_the_title() . '</a> <a href="' . esc_url(get_edit_post_link(get_the_ID())) . '" class="lp-edit-icon"> <i class="fa fa-edit"></i></a> ' . get_the_modified_date() . '</p>';
                            }
                            wp_reset_postdata(); // Reset the post data
                        }
                        ?>
                        <?php echo '<h3 class="lp-titles">' . esc_html__('News & Updates', 'listingpro-plugin') . '</h3>'; ?>
                        <div class="lp-overview__content">
                            <?php echo wp_kses_post($content['listingpro_options']['news']); ?>
                        </div>
                    <?php } ?>
                    <div class="lp-overview__footer">
                        <a href="<?php echo isset($content['listingpro_options']['help_link']) ? esc_url($content['listingpro_options']['help_link']) : 'https://help.listingprowp.com/'; ?>" target="_blank">Help <span class="screen-reader-text"> (opens in a new tab)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
                        |

                        <a href="<?php echo isset($content['listingpro_options']['docs_link']) ? esc_url($content['listingpro_options']['docs_link']) : 'https://docs.listingprowp.com/'; ?>" target="_blank">Docs <span class="screen-reader-text"> (opens in a new tab)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
                        |

                        <a class="lp-highlighted" href="<?php echo isset($content['listingpro_options']['services_link']) ? esc_url($content['listingpro_options']['services_link']) : 'https://listingprowp.com/services/'; ?>" target="_blank">Customization Services <span class="screen-reader-text"> (opens in a new tab)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
                        |
                        <a class="lp-highlighted" href="<?php echo isset($content['listingpro_options']['addon_link']) ? esc_url($content['listingpro_options']['addon_link']) : 'https://listingprowp.com/plugins/'; ?>" target="_blank">Premium Add-ons <span class="screen-reader-text"> (opens in a new tab)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
                    </div>
                    <?php
                }

                function get_option_data()
                {
                    // Endpoint URL
                    $endpoint_url = 'https://promowp.app/wp-json/listingpro-content/v1/options';
                    // Remote POST request
                    $response = wp_remote_get(
                        $endpoint_url,
                        array(
                            'headers' => array(
                                'Content-Type' => 'application/json', // Set content type to JSON
                            ),
                        )
                    );
                    // Check for error
                    if (is_wp_error($response)) {
                        $error_message = $response->get_error_message();
                        // Handle error
                    } else {
                        $body = wp_remote_retrieve_body($response);
                        $data = json_decode($body, true);

                        // Check if data is received successfully
                        if ($data) {
                            update_option('lp_overview_api_content', $data);
                        } else {
                            // Handle empty response
                        }
                    }
                }

                // Schedule the cron job to run every 12 hours
                if (! wp_next_scheduled('lp_twice_daily_api_content')) {
                    wp_schedule_event(time(), 'twicedaily', 'lp_twice_daily_api_content');
                }
                // Function to run for the cron job
                function lp_twice_daily_cb()
                {
                    get_option_data();
                }
                add_action('lp_twice_daily_api_content', 'lp_twice_daily_cb');

                add_action("redux/page/listingpro_options/menu/after", "lp_menu_after_cb");
                if (!function_exists('lp_menu_after_cb')) {

                    function lp_menu_after_cb($redux_object)
                    {
                        $content = get_option('lp_overview_api_content');
                        echo '<div class="customization-menu"><p>Need Customization?<br><a href="https://listingprowp.com/services/custom/" target="_blank"><strong>Request Free Quote</strong></a></p></div>';
                        if (isset($content['listingpro_options']['zone4_content']) && !empty($content['listingpro_options']['zone4_content'])) { ?>
                            <div class="lp-listingpro-zone zone-4">
                                <?php echo wp_kses_post($content['listingpro_options']['zone4_content']); ?>
                            </div>
                        <?php }
                    }
                }
                add_action("redux/page/listingpro_options/form/before", "lp_panel_before_cb");
                if (!function_exists('lp_panel_before_cb')) {

                    function lp_panel_before_cb($redux_object)
                    {
                        $content = get_option('lp_overview_api_content');
                        if (isset($content['listingpro_options']['zone3_content']) && !empty($content['listingpro_options']['zone3_content'])) { ?>
                            <div class="lp-listingpro-zone zone-3">
                                <?php echo wp_kses_post($content['listingpro_options']['zone3_content']); ?>
                            </div>
                    <?php }
                    }
                }

                function add_listingpro_submenu_customization()
                {
                    add_submenu_page(
                        'listingpro', // Parent page slug (replace with the slug of the base page)
                        'Need Customization?', // Page title
                        '<a href="https://listingprowp.com/services/" target="_blank" class="lp-need-customization">Need Customization?</a>', // Menu title
                        'manage_options', // Capability required to access the page
                        'need-customizatio', // Menu slug
                        'need_customization_redirect' // Callback function (redirect function)
                    );
                    if (!class_exists('EventPro')) {
                        add_submenu_page(
                            'edit.php?post_type=events', // Parent page slug (replace with the slug of the base page)
                            'EventproWP', // Page title
                            '<a href="https://listingprowp.com/downloads/eventprowp/" target="_blank">EventPro <span class="premium-addons">Premium</span></a>', // Menu title
                            'manage_options', // Capability required to access the page
                            'eventpro-premium', // Menu slug
                            'eventpro_premium_redirect' // Callback function (redirect function)
                        );
                    }
                    if (!class_exists('MedicalPro')) {
                        add_submenu_page(
                            'edit.php?post_type=listingpro-bookings', // Parent page slug (replace with the slug of the base page)
                            'MedicalProWP', // Page title
                            '<a href="https://listingprowp.com/downloads/medicalprowp" target="_blank">MedicalPro <span class="premium-addons">Premium</span></a>', // Menu title
                            'manage_options', // Capability required to access the page
                            'medicalpro-premium', // Menu slug
                            'medicalpro_premium_redirect' // Callback function (redirect function)
                        );
                    }
                }
                add_action('admin_menu', 'add_listingpro_submenu_customization');

                // Redirect function for the custom submenu
                function need_customization_redirect()
                {
                    // Redirect to a specific URL
                    header('Location: https://listingprowp.com/services/');
                    exit;
                }

                function eventpro_premium_redirect()
                {
                    // Redirect to a specific URL
                    header('Location: https://listingprowp.com/downloads/eventprowp/');
                    exit;
                }

                function medicalpro_premium_redirect()
                {
                    // Redirect to a specific URL
                    header('Location: https://listingprowp.com/downloads/eventprowp/');
                    exit;
                }
                function lp_admin_quick_help_widget()
                {
                    if (defined('DOING_AJAX') && DOING_AJAX) {
                        return;
                    }
                    ?>
                    <style>
                        .lp-quick-help-widget-main a:focus {
                            box-shadow: 0px 1px 2px 0px #0000001a, 0px 4px 15px 0px #0000001a;
                            outline: none;
                            color: transparent;
                        }

                        .lp-quick-help-widget-logo a:focus,
                        .lp-quick-help-widget-contect-footer a:focus {
                            box-shadow: none !important;
                        }

                        .lp-quick-help-widget-main {
                            position: fixed;
                            z-index: 999;
                            bottom: 20px;
                            right: 15px;
                        }

                        .lp-quick-help-widget-main .lp-quick-help-widget-container {
                            display: none;
                        }

                        .lp-quick-help-widget-main.show .lp-quick-help-widget-container {
                            display: block;
                        }

                        .lp-quick-help-widget-container {
                            border-radius: 10px;
                            background: #FFF;
                            width: 430px;
                            box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.25);
                            overflow: hidden;
                            text-align: center;
                            visibility: hidden;
                            opacity: 0;
                            transition: 0.3s ease;
                        }

                        .lp-quick-help-widget-container.show {
                            visibility: visible;
                            opacity: 1;
                        }

                        .lp-quick-help-widget-launcher {
                            text-align: end;
                            text-align: end;
                            margin-top: 15px;
                        }

                        .lp-quick-help-widget-launcher img {
                            cursor: pointer;
                        }

                        .lp-quick-help-widget-header {
                            padding: 40px 29px;
                            background: #2458FF;
                            box-shadow: 0px -10px 20px 0px rgba(0, 0, 0, 0.25) inset;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                        }

                        .lp-quick-help-text-header p {
                            color: #ffffff80;
                            font-size: 16px;
                            font-weight: 600;
                            line-height: 24px;
                            margin: 0;
                        }

                        .lp-quick-help-widget-content {
                            padding: 0px 25px 25px 25px;
                            margin-top: -20px;
                        }

                        .lp-quick-help-widget-links {
                            display: flex;
                            flex-direction: column;
                            gap: 8px;
                            margin-bottom: 26px;
                        }

                        .lp-quick-help-widget-links-column {
                            border-radius: 5px;
                            background: #FFF;
                            box-shadow: 0px 1px 2px 0px #0000001a, 0px 4px 15px 0px #0000001a;
                            display: flex;
                            gap: 15px;
                            align-items: center;
                            padding: 0 20px;
                            height: 90px;
                            text-decoration: none;
                            transition: .2s;
                        }

                        .lp-quick-help-widget-links-column:hover {
                            box-shadow: 0px 1px 2px 0px #0000001a;
                        }

                        .lp-quick-help-widget-links-text h2 {
                            color: #1A1F36;
                            font-size: 16px;
                            font-weight: 600;
                            line-height: 24px;
                            margin: 0;
                            text-align: start;
                        }

                        .lp-quick-help-widget-links-text p {
                            color: #425466;
                            font-size: 14px;
                            line-height: 21px;
                            margin: 0;
                            text-align: start;
                        }

                        .lp-quick-help-widget-contect {
                            display: flex;
                            flex-direction: column;
                            gap: 16px;
                        }

                        .lp-quick-help-widget-contect-heading h1 {
                            color: #1A1F36;
                            text-align: center;
                            font-size: 16px;
                            font-weight: 600;
                            line-height: 24px;
                            margin: 0;
                        }

                        .lp-quick-help-widget-contect-row {
                            display: flex;
                            gap: 9px;
                            flex-wrap: wrap;
                        }

                        .lp-quick-help-widget-contect-column {
                            border-radius: 5px;
                            background: #FFF;
                            box-shadow: 0px 1px 2px 0px #0000001a, 0px 4px 15px 0px #0000001a;
                            padding: 10px 0;
                            text-decoration: none;
                            width: calc(95% / 3);
                            transition: .2s;
                        }

                        .lp-quick-help-widget-contect-column:hover {
                            box-shadow: 0px 1px 2px 0px #0000001a;
                        }

                        .lp-quick-help-widget-contect-image {
                            margin-bottom: 6px;
                        }

                        .lp-quick-help-widget-contect-text h2 {
                            color: #1A1F36;
                            font-size: 12px;
                            font-weight: 600;
                            line-height: 24px;
                            margin: 0;
                        }

                        .lp-quick-help-widget-contect-text p {
                            color: #425466;
                            text-align: center;
                            font-size: 10px;
                            line-height: 21px;
                            margin: 0;
                        }

                        .lp-quick-help-widget-contect-footer p {
                            color: #1A1F36;
                            font-size: 12px;
                            font-weight: 500;
                            line-height: 24px;
                            margin: 0px 0px 8px 0px;
                        }

                        @media only screen and (max-width: 1450px) and (min-width: 1000px) {
                            .lp-quick-help-widget-main.show {
                                zoom: 75%;
                            }
                        }
                    </style>
                    <div class="lp-quick-help-widget-main" style="display:none">
                        <div class="lp-quick-help-widget-container">
                            <div class="lp-quick-help-widget-header">
                                <div class="lp-quick-help-widget-logo">
                                    <a href="https://listingprowp.com/" target="_blank"><img alt="listingpro" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/listingpro-logo-1.svg" /></a>
                                </div>
                                <div class="lp-quick-help-text-header">
                                    <p>Quick Help</p>
                                </div>
                            </div>
                            <div class="lp-quick-help-widget-content">
                                <div class="lp-quick-help-widget-links">
                                    <a href="https://docs.listingprowp.com/knowledge-base/" class="lp-quick-help-widget-links-column" target="_blank">
                                        <div class="lp-quick-help-widget-links-image">
                                            <img alt="document" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/lp-document.svg" />
                                        </div>
                                        <div class="lp-quick-help-widget-links-text">
                                            <h2>Documentation</h2>
                                            <p>All documentation with step-by-step articles</p>
                                        </div>
                                    </a>
                                    <a href="https://help.listingprowp.com/" class="lp-quick-help-widget-links-column" target="_blank">
                                        <div class="lp-quick-help-widget-links-image">
                                            <img alt="helpdesk" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/lp-helpdesk.svg" />
                                        </div>
                                        <div class="lp-quick-help-widget-links-text">
                                            <h2>Helpdesk</h2>
                                            <p>Open support ticket</p>
                                        </div>
                                    </a>
                                    <a href="https://listingprowp.com/plugins/" class="lp-quick-help-widget-links-column" target="_blank">
                                        <div class="lp-quick-help-widget-links-image">
                                            <img alt="addon" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/lp-addon.svg" />
                                        </div>
                                        <div class="lp-quick-help-widget-links-text">
                                            <h2>Add-ons Shop</h2>
                                            <p>Exclusively built plugins for ListingPro</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="lp-quick-help-widget-contect">
                                    <div class="lp-quick-help-widget-contect-heading">
                                        <h1>Need Help with Customization?</h1>
                                    </div>
                                    <div class="lp-quick-help-widget-contect-row">
                                        <a href="https://wpvender.com/service/listingpro-customization-service" target="_blank" class="lp-quick-help-widget-contect-column">
                                            <div class="lp-quick-help-widget-contect-image">
                                                <img alt="" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/lp-mail.svg" />
                                            </div>
                                            <div class="lp-quick-help-widget-contect-text">
                                                <h2>Request Quote</h2>
                                                <p>Submit a form</p>
                                            </div>
                                        </a>
                                        <a href="https://api.whatsapp.com/send/?phone=18445274346&text&type=phone_number&app_absent=0" target="_blank" class="lp-quick-help-widget-contect-column">
                                            <div class="lp-quick-help-widget-contect-image">
                                                <img alt="" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/lp-whatsap.svg" />
                                            </div>
                                            <div class="lp-quick-help-widget-contect-text">
                                                <h2>WhatsApp Chat</h2>
                                                <p>Chat with an Agent</p>
                                            </div>
                                        </a>
                                        <a href="https://wpvender.com/book-meeting/" target="_blank" class="lp-quick-help-widget-contect-column">
                                            <div class="lp-quick-help-widget-contect-image">
                                                <img alt="" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/lp-calendar.svg" />
                                            </div>
                                            <div class="lp-quick-help-widget-contect-text">
                                                <h2>Booking Meeting</h2>
                                                <p>Schedule a 1-on-1</p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="lp-quick-help-widget-contect-footer">
                                        <p>All ListingPro Customization Services are Powered by</p>
                                        <a href="https://wpvender.com/" target="_blank"><img alt="wpvender" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/wp-vender-logo.svg" /></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lp-quick-help-widget-launcher">
                            <img alt="launcher" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/quick-help/lp-launcher.png" />
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.querySelector('.lp-quick-help-widget-main').style.display = 'none';
                            document.addEventListener('click', function(event) {
                                if (event.target.closest('.lp-quick-help-widget-launcher img')) {
                                    document.querySelector('.lp-quick-help-widget-container').classList.toggle('show');
                                    document.querySelector('.lp-quick-help-widget-main').classList.toggle('show');
                                }
                            });
                        });
                    </script>
                <?php
                }
                add_action('admin_notices', 'lp_admin_quick_help_widget');
                ?>