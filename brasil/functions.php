<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    define('brasil_THEME_VERSION', '3.0');

    if( !OC_ADMIN ) {
        if( !function_exists('add_close_button_action') ) {
            function add_close_button_action(){
                echo '<script type="text/javascript">';
                    echo '$(".flashmessage .ico-close").click(function(){';
                        echo '$(this).parent().hide();';
                    echo '});';
                echo '</script>';
            }
            osc_add_hook('footer', 'add_close_button_action') ;
        }
    }
    function theme_brasil_regions_map_admin() {
        $regions = unserialize(osc_get_preference('region_maps','brasil_theme'));
        switch( Params::getParam('action_specific') ) {
            case('edit_region_map'):
                $regions[Params::getParam('target-id')] = Params::getParam('region');
                osc_set_preference('region_maps', serialize($regions), 'brasil_theme');
                osc_add_flash_ok_message(__('Region saved correctly', 'brasil'), 'admin');
                header('Location: ' . osc_admin_render_theme_url('oc-content/themes/brasil/admin/map_settings.php')); exit;
            break;
        }
    }
    function map_region_url($region_id) {
        $regionData = Region::newInstance()->findByPrimaryKey($region_id);
        if ( osc_rewrite_enabled() ) {
            $url = osc_base_url();
            if( osc_get_preference('seo_url_search_prefix') != '' ) {
                $url .= osc_get_preference('seo_url_search_prefix') . '/';
            }
            $url .= osc_sanitizeString($regionData['s_name']) . '-r' . $regionData['pk_i_id'];
            return $url;
        } else {
            return osc_search_url( array( 'sRegion' => $regionData['s_name']) );
        }
    }
    function theme_brasil_actions_admin() {
        switch( Params::getParam('action_specific') ) {
            case('settings'):
                $footerLink = Params::getParam('footer_link');
                osc_set_preference('keyword_placeholder', Params::getParam('keyword_placeholder'), 'brasil_theme');
                osc_set_preference('footer_link', ($footerLink ? '1' : '0'), 'brasil_theme');

                osc_add_flash_ok_message(__('Theme settings updated correctly', 'brasil'), 'admin');
                header('Location: ' . osc_admin_render_theme_url('oc-content/themes/brasil/admin/settings.php')); exit;
            break;
            case('upload_logo'):
                $package = Params::getFiles('logo');
                if( $package['error'] == UPLOAD_ERR_OK ) {
                    if( move_uploaded_file($package['tmp_name'], WebThemes::newInstance()->getCurrentThemePath() . "images/logo.jpg" ) ) {
                        osc_add_flash_ok_message(__('The logo image has been uploaded correctly', 'brasil'), 'admin');
                    } else {
                        osc_add_flash_error_message(__("An error has occurred, please try again", 'brasil'), 'admin');
                    }
                } else {
                    osc_add_flash_error_message(__("An error has occurred, please try again", 'brasil'), 'admin');
                }
                header('Location: ' . osc_admin_render_theme_url('oc-content/themes/brasil/admin/header.php')); exit;
            break;
            case('remove'):
                if(file_exists( WebThemes::newInstance()->getCurrentThemePath() . "images/logo.jpg" ) ) {
                    @unlink( WebThemes::newInstance()->getCurrentThemePath() . "images/logo.jpg" );
                    osc_add_flash_ok_message(__('The logo image has been removed', 'brasil'), 'admin');
                } else {
                    osc_add_flash_error_message(__("Image not found", 'brasil'), 'admin');
                }
                header('Location: ' . osc_admin_render_theme_url('oc-content/themes/brasil/admin/header.php')); exit;
            break;
        }
    }
    osc_add_hook('init_admin', 'theme_brasil_actions_admin');
    osc_add_hook('init_admin', 'theme_brasil_regions_map_admin');
    osc_admin_menu_appearance(__('Header logo', 'brasil'), osc_admin_render_theme_url('oc-content/themes/brasil/admin/header.php'), 'header_brasil');
    osc_admin_menu_appearance(__('Theme settings', 'brasil'), osc_admin_render_theme_url('oc-content/themes/brasil/admin/settings.php'), 'settings_brasil');
    osc_admin_menu_appearance(__('Map settings', 'brasil'), osc_admin_render_theme_url('oc-content/themes/brasil/admin/map_settings.php'), 'map_settings_brasil');


    if( !function_exists('logo_header') ) {
        function logo_header() {
            $html = '<img border="0" alt="' . osc_page_title() . '" src="' . osc_current_web_theme_url('images/logo.jpg') . '" />';
            if( file_exists( WebThemes::newInstance()->getCurrentThemePath() . "images/logo.jpg" ) ) {
                return $html;
            } else {
                return osc_page_title();
            }
        }
    }

    // install update options
    if( !function_exists('brasil_theme_install') ) {
        function brasil_theme_install() {
            osc_set_preference('keyword_placeholder', __('ie. PHP Programmer', 'brasil'), 'brasil_theme');
            osc_set_preference('version', brasil_THEME_VERSION, 'brasil_theme');
            osc_set_preference('footer_link', true, 'brasil_theme');
        }
    }

    if(!function_exists('check_install_brasil_theme')) {
        function check_install_brasil_theme() {
            $current_version = osc_get_preference('version', 'brasil_theme');
            //check if current version is installed or need an update<
            if( !$current_version ) {
                brasil_theme_install();
            }
        }
    }
    check_install_brasil_theme();

?>