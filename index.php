<?php
/**
 * @package         WBOLT
 * Plugin Name:     Smart Keywords Tool
 * Plugin URI:      https://wordpress.org/plugins/smart-keywords-tool/
 * Version:         1.6.8
 * Description:     智能关键词插件（Smart Keywords Tool）是一款集即时关键词推荐、关键词选词工具、文章智能标签及关键词库功能于一体的WordPress网站SEO优化插件。
 * Author:          闪电博
 * Author URI:      https://www.wbolt.com
 * Requires PHP:    5.6.0
 * Domain Path:     /languages
 * Text Domain:     smart-keywords-tool
*/


if(!defined('ABSPATH')){
    return;
}

define('SMART_KW_TOOL_PATH',dirname(__FILE__));
define('SMART_KW_TOOL_BASE_FILE',__FILE__);
define('SMART_KW_TOOL_VERSION','1.6.8');
define('SMART_KW_TOOL_BASE_URL',plugin_dir_url(__FILE__));
define('SMART_KW_TOOL_CODE','skt');
require_once SMART_KW_TOOL_PATH.'/classes/admin.class.php';

new Smart_KW_Tool_Admin();

add_action('plugins_loaded', 'skt_plugin_textdomain' );
function skt_plugin_textdomain() {
	load_plugin_textdomain( 'smart-keywords-tool', false, plugin_basename( SMART_KW_TOOL_PATH ) . '/languages/' );
}


