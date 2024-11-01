<?php

/**
 * This was contained in an addon until version 1.0.0 when it was rolled into
 * core.
 *
 * @package    WBOLT
 * @author     WBOLT
 * @since      1.1.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2019, WBOLT
 */

class Smart_KW_Tool_Base
{
    public static function param($key, $default = '', $type = 'p'){
        if('p' === $type){
            if(isset($_POST[$key])){
                return $_POST[$key];
            }
            return $default;
        } else if ('g' === $type){
            if(isset($_GET[$key])){
                return $_GET[$key];
            }
            return $default;
        }
        if(isset($_POST[$key])){
            return $_POST[$key];
        }
        if(isset($_GET[$key])){
            return $_GET[$key];
        }
        return $default;
    }
    /**
     * @see wpdb
     * @return mixed
     */
    public static function db()
    {
        static $db = null;
        if($db){
            return $db;
        }
        $db = $GLOBALS['wpdb'];
        if($db instanceof wpdb){
            return $db;
        }
        return $db;
    }

    public static function ajax_resp($ret)
    {
        header('content-type:text/json;charset=utf-8');
        echo wp_json_encode($ret);
        exit();
    }
}

class Smart_KW_Tool_Admin extends Smart_KW_Tool_Base
{
    public static $debug = false;
  public static $name = 'skt_pack';
  public static $optionName = 'skt_option';
  public static $cnf_fields = array();

  public static $skt_api = array(
    'taobao' => array(
      'url' => 'https://api1.wbolt.com/taobao-suggestion/sug?code=utf-8&q=',
      'cb' => 'callback'
    ),
    'bing' => array(
      'url' => 'https://api1.wbolt.com/bing-suggestion/qsonhs.aspx?type=cb&q=',
      'cb' => 'cb'
    ),
    'so' => array(
      'url' => 'https://api1.wbolt.com/360-suggestion/suggest?encodein=utf-8&encodeout=utf-8&format=json&word=',
      'cb' => 'callback'
    ),
    'google' => array(
      'url' => 0,
      'cb' => 'jsonp'
    ),
    'baidu' => array(
      'url' => 0,
      'cb' => 'cb'
    ),
    'trade' => array(
      'url' => 'https://api1.wbolt.com/bing-suggestion/osjson.aspx',
      'cb' => 'bingTrade'
    )
  );

  private  static function _cnf()
  {

    $default = array(
      'se_type' => 'so',
      'sst_switch' => 0,
      'gutenberg_switch' => 0,
      'wpkw_switch' => 0,
      'st' => [
        'switch' => 0,
        'appid' => '',
        'key' => '',
        'secret' => '',
      ],
      'qcloud' => [
        'appid' => '',
        'key' => '',
        'secret' => '',
      ],
      'xf' => [
        'appid' => '',
        'key' => '',
      ],
      'opencalais' => [
        'key' => '',
      ],
      'dandelion' => [
        'key' => '',
        'confidence' => '',
      ],
      'active_location' => [
        'title' => 1,
        'tag' => 1
      ],
      'max_word' => 3,
      'def_api' => 'baidu',
      'post_type' => ['post'],
      'auto_post_tag' => 0,
      'active_items' => array()
    );
    $opt = get_option(self::$optionName);
    if(!$opt){
        $opt = $default;
        return $opt;
    }

    foreach ($default as $k => $v) {
      if (!isset($opt[$k])) {
        $opt[$k] = $v;
        continue;
      }
        if (is_array($v)){
            if(!is_array($opt[$k])){
                $opt[$k] = $v;
                continue;
            }
            if(in_array($k,['post_type','active_items'])){
                continue;
            }
            foreach ($v as $sk => $sv) {
                if (!isset($opt[$k][$sk])) {
                    $opt[$k][$sk] = $sv;
                }
            }
        }
    }
    return $opt;
  }

  /**
   * 获取设置
   * @param null $key
   * @param null $default
   *
   * @return array
   */
  public static function cnf($key = null, $default = null)
  {
    static $_option = array();
    if (!$_option) {
      $_option = self::_cnf();
    }

    if (null === $key) {
      return $_option;
    }

    if (isset($_option[$key])) {
      return $_option[$key];
    }

    return $default;
  }

  public static function  array_sanitize_text_field($value)
  {
    if (is_array($value)) {
      foreach ($value as $k => $v) {
        $value[$k] = self::array_sanitize_text_field($v);
      }
      return $value;
    } else {
      return sanitize_text_field($value);
    }
  }


  public function __construct()
  {
    if (is_admin()) {



      //插件设置连接
      add_filter('plugin_action_links', array(__CLASS__, 'actionLinks'), 10, 2);

      add_action('admin_menu', array(__CLASS__, 'admin_menu'));

      //add_action( 'admin_init', array(__CLASS__,'admin_init') );

      add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'), 1);

      add_filter('plugin_row_meta', array(__CLASS__, 'plugin_row_meta'), 10, 2);

      add_filter('post_row_actions', array(__CLASS__, 'post_row_actions'), 99, 2);

      add_filter('bulk_actions-edit-post', array(__CLASS__, 'bulk_actions'), 90);

      add_action('admin_head-post.php', array(__CLASS__, 'admin_head'));
      add_action('admin_head-post-new.php', array(__CLASS__, 'admin_head'));
      add_action('admin_footer-post.php', array(__CLASS__, 'admin_footer_post'));
      add_action('admin_footer-post-new.php', array(__CLASS__, 'admin_footer_post'));

      //	        add_action('admin_head',array(__CLASS__,'admin_head'));
      //	        add_action('admin_footer',array(__CLASS__,'admin_footer_post'));


      add_action('wp_ajax_skt_admin_ajax', array(__CLASS__, 'admin_ajax'));
      add_action('wp_ajax_wb_skt_options', array(__CLASS__, 'wb_skt_options_ajax_save'));
      add_action('wp_ajax_wb_skt_options', array(__CLASS__, 'wb_skt_options_ajax_handler'));

      add_action('admin_notices', array(__CLASS__, 'admin_notices'));

      add_action('edit_post', array(__CLASS__, 'edit_post'), 51, 2);
      //add_filter('use_block_editor_for_post_type',function($is_user,$post_type){return false;},10,2);
    }
  }


  public static function edit_post($post_id, $post)
  {
      if($post->post_status !== 'publish'){
          return;
      }

    if (!self::cnf('auto_post_tag', 0)) {
      return;
    }

    $post_type = self::cnf('post_type',[]);
    if(empty($post_type) || !in_array($post->post_type,$post_type)){
        return;
    }

    self::set_post_smart_tag($post_id);
  }

  public static function wb_remove_block_library_css()
  {
    wp_dequeue_style('wp-block-library');
  }

  public static function smart_tag_default_type()
  {
    $active = self::cnf('active_items', []);
    if (empty($active) || !is_array($active)) {
      return null;
    }
    $def_api = self::cnf('def_api', 'baidu');
    if (!$def_api) {
      $def_api = 'baidu';
    }
    if (!in_array($def_api, $active)) {
      $def_api = $active[0];
    }
    return $def_api;
  }

  public static function can_smart_tag()
  {
    static $can = null;
    if ($can !== null) {
      return $can;
    }
    $can = false;
    do {
      if (!get_option('wb_skt_ver', '0')) {
        break;
      }

      $active = self::cnf('active_items', []);
      if (empty($active) || !is_array($active)) {
        break;
      }
      $def_api = self::cnf('def_api', 'baidu');
      if (!$def_api) {
        $def_api = 'baidu';
      }
      if (!in_array($def_api, $active)) {
        $def_api = $active[0];
      }
      if ($def_api == 'baidu') {
        $cnf = self::cnf('st', array());
        if (empty($cnf)) {
          break;
        }
        if (!isset($cnf['appid']) || !$cnf['appid']) {
          break;
        }
        if (!isset($cnf['secret']) || !$cnf['secret']) {
          break;
        }
      } else {
        $cnf = self::cnf($def_api, array());
        if (empty($cnf)) {
          break;
        }
      }

      if (!isset($cnf['key']) || !$cnf['key']) {
        break;
      }
      $can = true;
    } while (0);
    return $can;
  }

  public static function bulk_actions($actions)
  {
    static $has_bulk_inline_js = false;
      if (!current_user_can('administrator')) {
          return $actions;
      }
      if (!self::can_smart_tag()) {
          return $actions;
      }
      $actions['smart_tag'] = __('Generate tags', 'smart-keywords-tool');
      if (!$has_bulk_inline_js) {
          $has_bulk_inline_js = true;
          $wb_skt_ajax_nonce = wp_create_nonce('wb_skt_admin_ajax');
          $js = array();
          $fun_js = array();
          $fun_js[] = "var ckb = h('.check-column :checkbox:checked');";
          //$fun_js[] = "var _wb_skt_ajax_nonce = '".$wb_skt_ajax_nonce."';";
          $fun_js[] = "if(ckb.length<1){return false;}";
          $fun_js[] = "var n =1;ckb.each(function(idx,el){";
          $fun_js[] = "var tr = h(el).parents('tr');if(tr.find('a.post_smart_tag').length<1)return;";
          $fun_js[] = " n++;setTimeout(function(){h.post(ajaxurl,{_ajax_nonce: '$wb_skt_ajax_nonce', action:'skt_admin_ajax','op':'post_smart_tag',post_id:h(el).val()},function(ret){";
          $fun_js[] = "  if(ret){tr.find('.tags').html(ret);tr.find('a.post_smart_tag').remove(); }";
          $fun_js[] = " });},n * 1000);";
          $fun_js[] = "});";
          $js[] = "(function(h){";
          $js[] = "h('#doaction, #doaction2').on('click',function(){";
          $js[] = "var btn = h(this);var op = btn.prev().val();";
          $js[] = "if(op=='smart_tag'){" . implode('', $fun_js) . "return false;}";

          $js[] = "});";
          $js[] = "})(jQuery);";

          wp_add_inline_script('wp-auth-check', implode('', $js));
      }

    return $actions;
  }

  public static function get_post_tag_tax($post)
  {
      $taxonomies = get_object_taxonomies($post,'object');
      if(empty($taxonomies)){
          return 'post_tag';
      }
      $taxonomy = 'post_tag';
      foreach($taxonomies as $name=>$obj){
          if(preg_match('#post_tags#',$obj->meta_box_cb)){
              $taxonomy = $name;
              break;
          }else if(in_array('manage_post_tags',get_object_vars($obj->cap))){
              $taxonomy = $name;
              break;
          }
      }
      return $taxonomy;
  }

  public static function post_row_actions($actions, $post)
  {
    static $has_inline_js = false;
    if (!current_user_can('edit_post', $post->ID)) {
      return $actions;
    }
      if (empty($post->post_content)) {
          return $actions;
      }
      if (empty($post->post_title)) {
          return $actions;
      }
      if (!self::can_smart_tag()) {
          return $actions;
      }

      $tag_taxonomy = self::get_post_tag_tax($post);
      //print_r([$tag_taxonomy]);
      $tags = wp_get_post_terms($post->ID, $tag_taxonomy);
      if (!empty($tags)) {
          return $actions;
      }
      $actions['post_smart_tag'] = '<a class="post_smart_tag" data-post_id="' . $post->ID . '" href="javascript:;">' . __('Generate tags', 'smart-keywords-tool') . '</a>';
      if (!$has_inline_js) {
          //print_r($post);
          $has_inline_js = true;
          $js = array();
          $wb_skt_ajax_nonce = wp_create_nonce('wb_skt_admin_ajax');
          $js[] = "jQuery('a.post_smart_tag').on('click',function(){var obj = jQuery(this);";
          $js[] = "jQuery.post(ajaxurl,{_ajax_nonce: '$wb_skt_ajax_nonce', taxonomy: '$tag_taxonomy', action:'skt_admin_ajax','op':'post_smart_tag',post_id:obj.data('post_id')},function(ret){";
          $js[] = "if(ret){var tr_row = obj.parents('tr');tr_row.find('.tags').html(ret);tr_row.find('.taxonomy-$tag_taxonomy').html(ret); }obj.remove();";
          $js[] = "});return false;});";

          wp_add_inline_script('wp-auth-check', implode('', $js));
      }

    return $actions;
  }


  public static function smart_tag_tmp()
  {
    do {
      if (!get_option('wb_skt_ver', '0')) {
        $return = 'err:6';
        break;
      }
      $cnf = self::cnf('st', array());
      if (!$cnf) {
        $return = 'err:7';
        break;
      }
      if (!isset($cnf['switch']) || !$cnf['switch']) {
        $return = 'err:8';
        break;
      }
      if (!isset($cnf['appid']) || !$cnf['appid']) {
        $return = 'err:9';
        break;
      }
      if (!isset($cnf['key']) || !$cnf['key']) {
        $return = 'err:10';
        break;
      }
      if (!isset($cnf['secret']) || !$cnf['secret']) {
        $return = 'err:11';
        break;
      }
    } while (0);
  }

  public static function set_post_smart_tag($post_id)
  {
    $return = '';
    do {
      $type = sanitize_text_field(self::param('type'));
      if (!current_user_can('edit_post', $post_id)) {
        $return = 'err:forbid';
        break;
      }
      if (!self::can_smart_tag()) {
        $return = 'err:forbid';
        break;
      }

      $post = get_post($post_id);
      if (empty($post)) {
        $return = 'err:empty post';
        break;
      }
        $tag_taxonomy = sanitize_text_field(self::param('taxonomy'));
      if(!$tag_taxonomy){
          $tag_taxonomy = self::get_post_tag_tax($post);
      }

        $tags = wp_get_post_terms($post_id, $tag_taxonomy);
        if (!empty($tags)) {
            $return = 'err:post has tag';
            break;
        }

      /*$token = self::baidu_app_token($cnf);
            if(!$token){
                $return = 'err:12';
                break;
            }*/

      $title = $post->post_title;
      $content = preg_replace('#</?[a-z0-9]+[^>]*?/?>#i', '', $post->post_content);

      $content = trim(wp_strip_all_tags($content));
      $content = str_replace(array("\n", "\r"), '', $content);

      if (empty($title) || empty($content)) {
        $return = 'err:empty post_content';
        break;
      }

      if (null === $type) {
        $type = self::smart_tag_default_type();
      }

      if ($type == 'opencalais') {
        $ret = self::open_calais($title . $content);
      } else if ($type == 'dandelion') {
        $ret = self::dandelion($title . $content);
      } else if ($type == 'qcloud') {
        $ret = self::qcloud($title, $content);
      } else if ($type == 'xf') {
        $ret = self::xfyun($title, $content);
      } else {
        $ret = self::baidu_tags($title, $content);
      }

      if ($ret['code']) {
        $return = 'err:' . $ret['code'];
        if (isset($ret['error'])) {
          $return .= '-' . $ret['error'];
        }
        if ($ret['desc'] !== 'fail') {
          update_option('wb_skt_baidu_app_error', $ret['desc'], false);
          $return .= '-' . $ret['desc'];
        }
          self::set_api_error($type, $ret);
        break;
      }

      $tags = [];
      foreach ($ret['data'] as $r) {
        $tags[] = $r->name;
      }
      wp_set_post_terms($post_id, $tags, $tag_taxonomy);
      $return = implode('、', $tags);
    } while (0);

    return $return;
  }

  public static function admin_ajax()
  {
    // global $wpdb;

    $op = sanitize_text_field(self::param('op'));
    if(!$op){
        return;
    }
    $allow = [
        'post_smart_tag', 'smart_tag', 'wp_tag', 'get_tags', 'options'
    ];
    if(!in_array($op, $allow)){
        return;
    }
      if (!current_user_can('edit_posts')) {
          return;
      }
      $nonce = self::param('_ajax_nonce');
      if(!$nonce){
          $nonce = self::param('_ajax_nonce','', 'g');
      }
      if (!wp_verify_nonce(sanitize_text_field($nonce), 'wb_skt_admin_ajax')) {
          return;
      }


      switch ($op) {
          case 'post_smart_tag':
              $post_id = absint(self::param('post_id', 0));
              if (!$post_id) {
                  $return = 'err:1';
              } else {
                  /*if (!wp_verify_nonce($_POST['_ajax_nonce'] ?? '', 'wb_skt_admin_ajax')) {
                      $return = 'err:security';
                  }*/
                  $return = self::set_post_smart_tag($post_id);
              }
              echo $return;
              exit();
              break;

          case 'smart_tag':
              // $return = '0';
              $active_sk_item = self::cnf('active_items', []);


              $type = sanitize_text_field(self::param('type'));
              if (!$type) {
                  $type = $active_sk_item ? $active_sk_item[0] : 'baidu';
              }
              $ret = array('tags_html' => '');

              do {
                  if (!wp_verify_nonce(sanitize_text_field(self::param('_ajax_nonce')), 'wb_skt_admin_ajax')) {
                      // $return = '3';
                      $ret['error'] = 'security';
                      break;
                  }
                  $content = trim(self::param('content'));
                  if (!$content) {
                      // $return = '1';
                      $ret['error'] = 'empty post content';
                      break;
                  }
                  $title = sanitize_text_field(self::param('title'));
                  if (!$title) {
                      // $return = '2';
                      $ret['error'] = 'empty post title';
                      break;
                  }

                  if (!self::can_smart_tag()) {
                      // $return = '6';
                      $ret['error'] = 'forbid';
                      break;
                  }

                  //$title = trim(sanitize_text_field($_POST['title']));

                  $content = preg_replace('#</?[a-z0-9]+[^>]*?/?>#i', '', $content);

                  $content = trim(wp_strip_all_tags($content));
                  $content = str_replace(array("\n", "\r"), '', $content);
                  if ($type == 'opencalais') {
                      $ret = self::open_calais($title . $content);
                  } else if ($type == 'dandelion') {
                      $ret = self::dandelion($title . $content);
                  } else if ($type == 'qcloud') {
                      $ret = self::qcloud($title, $content);
                  } else if ($type == 'xf') {
                      $ret = self::xfyun($title, $content);
                  } else {
                      $ret = self::baidu_tags($title, $content);
                  }

                  if ($ret['code']) {
                      $ret['tags_html'] = '';
                      //$return = $ret['code'];
                      if ($ret['desc'] !== 'fail') {
                          update_option('wb_skt_baidu_app_error', $ret['desc'], false);
                      }
                      self::set_api_error($type, $ret);
                      break;
                  }
                  $tags = $ret['data'];

                  $ret['tags_html'] = wp_generate_tag_cloud(
                      $tags,
                      array(
                          'filter' => 0,
                          'format' => 'list',
                      )
                  );
              } while (0);

              self::ajax_resp($ret);

              break;

          case 'wp_tag':
              $taxonomy = sanitize_text_field(self::param('taxonomy', '', 'g'));
              $taxonomy = $taxonomy ? $taxonomy : 'post_tag';

              $db = self::db();
              //and a.name like %s //,'%'.$q.'%'
              $sql = $db->prepare("select a.name,b.count from $db->terms a,$db->term_taxonomy b where a.term_id=b.term_id and b.taxonomy=%s  order by b.count desc ", $taxonomy);
              $list =  $db->get_results($sql);

              self::ajax_resp($list);
              break;

          case 'get_tags':
              $ret = array('code' => 0, 'desc' => 'success');
              $post_id = absint(self::param('pid', 0));
              $taxonomy = sanitize_text_field(self::param('', 'post_tag'));

              $ret['data'] = Smart_KW_Tool_Admin::get_terms($taxonomy, $post_id);
              self::ajax_resp($ret);

              break;
      }
  }

  public static function txt($msg)
  {
      if(!self::$debug)return;
      if(is_array($msg))$msg = wp_json_encode($msg);
      error_log(current_time('[ymdHis]').$msg."\n",3, __DIR__.'/debug.txt');
  }

  public static function baidu_app_token($cnf)
  {
      self::txt('baidu_app_token');
    $token = get_option('wb_skt_baidu_app_token', array());
    if ($token && isset($token['access_token']) &&  isset($token['expires_in']) && isset($token['updated'])) {
      $now = current_time('U', 1);
      $time = intval($token['updated'] + $token['expires_in']);
      if ($time > $now && $token['access_token']) {
        return $token['access_token'];
      }
    }

    $param = array(
      'grant_type' => 'client_credentials',
      'client_id' => $cnf['key'],
      'client_secret' => $cnf['secret'],
      'scope' => 'smartapp_snsapi_base',
    );

    //https://aip.baidubce.com/oauth/2.0/token
    $url = 'https://openapi.baidu.com/oauth/2.0/token?' . http_build_query($param);
      self::txt('baidu_app_token request token');
    $http = wp_remote_get($url);

    $body = wp_remote_retrieve_body($http);
    if (!$body) {
      return false;
    }

    //error_log($body."\n",3,__DIR__.'/baidu.txt');
    $resp = json_decode($body, true);
    if (!$resp) {
      return false;
    }
    if (isset($resp['error']) && $resp['error']) {
      update_option('wb_skt_baidu_app_error', $resp, false);
      return false;
    }
    $resp['updated'] = current_time('U', 1);
    update_option('wb_skt_baidu_app_token', $resp, false);

    return $resp['access_token'];
  }

  public static function admin_notices()
  {
    global $current_screen;

    if (!current_user_can('update_plugins')) {
      return;
    }
    if (!preg_match('#skt_pack#', $current_screen->base)) {
      return;
    }
    $current         = get_site_transient('update_plugins');

    if (!$current) {
      return;
    }
    $plugin_file = plugin_basename(SMART_KW_TOOL_BASE_FILE);
    if (!isset($current->response[$plugin_file])) {
      return;
    }
    $all_plugins     = get_plugins();
    if (!$all_plugins || !isset($all_plugins[$plugin_file])) {
      return;
    }
    $plugin_data = $all_plugins[$plugin_file];
    $update = $current->response[$plugin_file];

    //print_r($update);
    $update_url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $plugin_file, 'upgrade-plugin_' . $plugin_file);

    echo '<div class="update-message notice inline notice-warning notice-alt"><p>' . esc_html($plugin_data['Name']) . '有新版本可用。';
      echo '<a href="' . esc_url($update->url) . '" target="_blank" aria-label="查看' . esc_attr($plugin_data['Name']) . '版本' . esc_attr($update->new_version) . '详情">查看版本' . esc_html($update->new_version) . '详情</a>';
      echo '或<a href="' . esc_url($update_url) . '" class="update-link" aria-label="现在更新 ' . esc_attr($plugin_data['Name']) . '">现在更新</a>。</p></div>';
  }

  public static function admin_head()
  {

    $active_block_model = false;
    $post_id = get_the_ID();
    $setting_field = self::$optionName;
    $option_name = self::$optionName;
    $opt = self::_cnf();

    $wb_skt_ajax_nonce = wp_create_nonce('wb_skt_admin_ajax');

    /* $js_arr = array();
        if( !wp_script_is('wbsst-inline-js') ){
	        $js_arr=array('wbsst-inline-js');
        }*/

    // 判断有适用tag的模块
    $taxonomies = get_object_taxonomies(get_post($post_id), 'object');
    foreach($taxonomies as $tax){
      if(!$tax->hierarchical && $tax->show_ui) {
        $active_block_model = true;
        break;
      }
    }


    if ( self::is_active_gutenberg_editor()) {
      if($active_block_model){
        wp_enqueue_style(
          'wb_block_editor_skt',
          SMART_KW_TOOL_BASE_URL . 'assets/wb_block_editor.css',
          array('wp-edit-blocks'),
          SMART_KW_TOOL_VERSION
        );
  
        wp_enqueue_script(
          'skt-admin-js',
          SMART_KW_TOOL_BASE_URL . 'assets/block/wb_block.js',
          array('wp-plugins', 'wp-element', 'wp-editor', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-core-data', 'wp-block-editor'),
          SMART_KW_TOOL_VERSION
        );
      }
    } else {
      wp_enqueue_style('wb-skt-admin-style', plugin_dir_url(SMART_KW_TOOL_BASE_FILE) . 'assets/wbp_admin.css', array(), SMART_KW_TOOL_VERSION);
      wp_enqueue_script('skt-admin-js', plugin_dir_url(SMART_KW_TOOL_BASE_FILE) . 'assets/wbp_admin.js', array(), SMART_KW_TOOL_VERSION, true);
    }


    $se_type = isset($opt['se_type']) ? $opt['se_type'] : 'so';
    $ipt = array('j-skt-input');
    if (isset($opt['active_location'])) {
      if (isset($opt['active_location']['title']) && $opt['active_location']['title']) {
        $ipt[] = '#title';
      }
      if (isset($opt['active_location']['tag']) && $opt['active_location']['tag']) {
        $ipt[] = '.ajaxtag .newtag';
      }
    }

    //sst开关
    if ($opt['sst_switch']) {
      $ipt[] = '.sst-skt-input';
    }
    $st = $opt['st'];

    //智能分词配置
    $active_sk_item = self::cnf('active_items');
    $st_active_status = !empty($active_sk_item);

    $skt_cnf = array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'dir_url' => SMART_KW_TOOL_BASE_URL,
      'type' => $se_type,
      'api' => self::$skt_api[$se_type]['url'],
      'callback' => self::$skt_api[$se_type]['cb'],
      'input' => implode(',', $ipt),
      'wp' => $opt['wpkw_switch'] ? 1 : 0,
      'st_active' => $st_active_status,
      'is_pro' => get_option('wb_skt_ver', 0),
      'pid' => get_the_ID(),
      'sk_items' => $active_sk_item,
      'sk_cnf' => array(
        'baidu' => '百度,百度关键词提取',
        'xf' => '讯飞,讯飞关键词提取',
        'opencalais' => 'Opencalais,Opencalais',
        'dandelion' => 'Dandelion,Dandelion'
      ),
    );

    $in_line_js =  'var skt_cnf = ' . wp_json_encode($skt_cnf) . ';';
    $in_line_js .= 'var _wb_skt_ajax_nonce = \'' . $wb_skt_ajax_nonce . '\';';
    wp_add_inline_script('skt-admin-js', $in_line_js, 'before');

    wp_localize_script('skt-admin-js', 'wbl', array(
      'title_required' => __('Post title cannot be empty', 'smart-keywords-tool'),
      'content_required' => __('Post content cannot be empty', 'smart-keywords-tool'),
      'fail_required' => __('Request failed', 'smart-keywords-tool'),
      'got_empty_tags' => __('No related tags', 'smart-keywords-tool'),
      'loading_tags' => __('Waiting to be generated...', 'smart-keywords-tool'),
      'get_smt_btn_name' => __('Choose from AI tags', 'smart-keywords-tool'),
      'add_tags_oneclick' => __('Add all', 'smart-keywords-tool'),
    ));
  }

  public static function admin_footer_post()
  {

    include SMART_KW_TOOL_PATH . '/inc/_admin_footer.php';
  }

  public static function plugin_row_meta($links, $file)
  {

    $base = plugin_basename(SMART_KW_TOOL_BASE_FILE);
    if ($file == $base) {
      $links[] = '<a href="https://www.wbolt.com/plugins/skt">' . __('Plugin Site', 'smart-keywords-tool') . '</a>';
      $links[] = '<a href="https://www.wbolt.com/skt-plugin-documentation.html">' . __('说明文档', 'smart-keywords-tool') . '</a>';
      $links[] = '<a href="https://www.wbolt.com/plugins/skt#J_commentsSection">' . __('反馈', 'smart-keywords-tool') . '</a>';
    }
    return $links;
  }

  public static function actionLinks($links, $file)
  {

    if ($file != plugin_basename(SMART_KW_TOOL_BASE_FILE))
      return $links;

    if (!get_option('wb_skt_ver', 0)) {
      $a_link = '<a href="https://www.wbolt.com/plugins/skt" target="_blank"><span style="color: #FCB214;">升至Pro版</span></a>';
      array_unshift($links, $a_link);
    }

    $settings_link = '<a href="' . menu_page_url(self::$name, false) . '">' . __('Setting', 'smart-keywords-tool') . '</a>';

    array_unshift($links, $settings_link);

    return $links;
  }

  /**
   * 插件设置
   */
  public static function admin_menu()
  {
    //global $wb_settings_page_hook_skt;,$wb_settings_page_hook_skt =
    add_options_page(
      __('Smart Keywords Tool Setting', 'smart-keywords-tool'),
      __('Smart Keywords Tool', 'smart-keywords-tool'),
      'manage_options',
      self::$name,
      array(__CLASS__, 'render_views')
    );
  }


  public static function vue_assets()
  {
    $assets = include __DIR__ . '/plugins_assets.php';

    if (!$assets || !is_array($assets)) {
      return;
    }

    $wp_styles = wp_styles();
    if (isset($assets['css']) && is_array($assets['css'])) foreach ($assets['css'] as $r) {
      $wp_styles->add($r['handle'], SMART_KW_TOOL_BASE_URL . $r['src'], $r['dep'], null, $r['args']);
      $wp_styles->enqueue($r['handle']); //.'?v=1'
    }
    if (isset($assets['js']) && is_array($assets['js'])) foreach ($assets['js'] as $r) {
      if (!$r['src'] && $r['in_line']) {
        wp_register_script($r['handle'], false, $r['dep'], false, true);
        wp_enqueue_script($r['handle']);
        wp_add_inline_script($r['handle'], $r['in_line'], 'after');
      } else if ($r['src']) {
        wp_enqueue_script($r['handle'], SMART_KW_TOOL_BASE_URL . $r['src'], $r['dep'], null, true);
      }
    }
  }


  public static function render_views()
  {
    echo '<div id="app"></div>';
    //include_once( SMART_KW_TOOL_PATH . '/tpl/index.html');
  }

  public static function admin_enqueue_scripts($hook)
  {

    if (!preg_match('#settings_page_skt_pack#', $hook)) return;

    $prompt_items = array();
    if (file_exists(__DIR__ . '/_prompt.php')) {
      include __DIR__ . '/_prompt.php';
    }
      global $wp_post_types;
    $post_types = get_post_types(['public' => true,'can_export'=>1]);
    foreach($post_types as $k=>$v){
        $post_types[$k] = $wp_post_types[$k]->label;
    }

    $wb_skt_ajax_nonce = wp_create_nonce('wb_skt_admin_ajax');
    $wb_cnf = array(
      '_wb_skt_ajax_nonce' => $wb_skt_ajax_nonce,
      'base_url' => admin_url(),
      'home_url' => home_url(),
      'ajax_url' => admin_url('admin-ajax.php'),
      'dir_url' => SMART_KW_TOOL_BASE_URL,
      'pd_code' => SMART_KW_TOOL_CODE,
      'doc_url' => "https://www.wbolt.com/skt-plugin-documentation.html",
      'pd_title' => '智能关键词插件',
      'pd_version' => SMART_KW_TOOL_VERSION,
      'is_pro' => intval(get_option('wb_skt_ver', 0)),
      'action' => array(
        'act' => 'wb_skt_options',
        'fetch' => 'get_setting',
        'push' => 'set_setting'
      ),
      'prompt' => $prompt_items,
        'post_types'=>$post_types,
    );

    add_filter('style_loader_tag', function ($tag, $handle, $href, $media) {
      if (!preg_match('#^vue-#', $media)) {
        return $tag;
      }

      $media = htmlspecialchars_decode($media);
      $r = [];
      parse_str(str_replace('vue-', '', $media), $r);
      $rel = '';
      $attr = [];
      if ($r && is_array($r)) {
        if (isset($r['rel'])) {
          $rel = $r['rel'];
          unset($r['rel']);
        }
        foreach ($r as $attr_k => $attr_v) {
          $attr[] = sprintf('%s="%s"', $attr_k, esc_attr($attr_v));
        }
      }

      $tag = sprintf(
        '<link href="%s" rel="%s" %s/>' . "\n",
        $href,
        $rel,
        implode(" ", $attr)
      );
      return $tag;
    }, 10, 4);

    add_filter('script_loader_tag', function ($tag, $handle, $src) {
      if (!preg_match('#-vue-js-#', $handle)) {
        return $tag;
      }
      $parts = explode('?', $src, 2);
      $src = $parts[0];
      $type = '';
      $attr = '';
      if (isset($parts[1])) {
        $r = [];
        parse_str(htmlspecialchars_decode($parts[1]), $r);
        //print_r($r);
        if ($r) {
          if (isset($r['type'])) {
            $type = sprintf(' type="%s"', esc_attr($r['type']));
            unset($r['type']);
          }
          $attr_txt = '';
          if (isset($r['attr'])) {
            $attr_txt = $r['attr'];
            unset($r['attr']);
          }
          foreach ($r as $k => $v) {
            $attr .= sprintf(' %s="%s"', $k, esc_attr($v));
          }
          if ($attr_txt) {
            $attr .= sprintf(' %s', esc_attr($attr_txt));
          }
        }
      }
      //print_r([$handle,$src]);

      $tag = sprintf('<script%s src="%s"%s id="%s-js"></script>' . "\n", $type, $src, $attr, $handle);
      return $tag;
    }, 10, 3);


    wp_register_script('wbs-inline-js', false, null, false);
    wp_enqueue_script('wbs-inline-js');

    wp_add_inline_script('wbs-inline-js', ' var wb_cnf=' . wp_json_encode($wb_cnf, JSON_UNESCAPED_UNICODE) . '; var wb_vue_path ="' . SMART_KW_TOOL_BASE_URL . 'tpl/";', 'before');

    wp_localize_script('wbs-inline-js', 'wbl', array(
      'active_input_label' => __('KEY', 'smart-keywords-tool'),
      'active_input_placeholder' => __('Please enter a valid key', 'smart-keywords-tool'),
      'active_get' => __('PURCHASE', 'smart-keywords-tool'),
      'active_win_title' => __('Activate PRO', 'smart-keywords-tool'),
      'active_btn_yes' => __('SUBMIT', 'smart-keywords-tool'),
      'active_btn_no' => __('CANCEL', 'smart-keywords-tool'),
      'active_toast_need_input' => __('Please enter a valid key', 'smart-keywords-tool'),
      'active_confirm_domain' => __('After binding a domain, it cannot be changed. Confirm binding to the domain:', 'smart-keywords-tool'),
      'active_confirm_title' => __('Binding info confirmation', 'smart-keywords-tool'),
      'active_confirm_btn' => __('CONFIRM', 'smart-keywords-tool'),
      'active_btn_close' => __('CLOSE', 'smart-keywords-tool'),
      'active_verify_success' => __('Verification successful!', 'smart-keywords-tool'),
      'beforeunload_tips' => __('Modification not submitted. Leave this page?', 'smart-keywords-tool'),
      'save_success' => __('Saved', 'smart-keywords-tool'),
      'tips_no_ret' => __('No results, try another keyword or switch to other search engines', 'smart-keywords-tool'),
      'pls_input_kw' => __('Please enter a keyword', 'smart-keywords-tool'),
      'tips_had_copy' => __('Copied', 'smart-keywords-tool'),
    ));

    self::vue_assets();
  }

  public static function get_api_error()
  {
      $api_error = get_option('wb_skt_api_error');
      if(!$api_error){
          $api_error = [];
      }
      return $api_error;
  }

  public static function set_api_error($type, $ret)
  {
      $types = ['opencalais'=>'OpenCalais','dandelion'=>'Dandelion','qcloud'=>'腾讯云','xf'=>'讯飞','baidu'=>'百度'];
      $row = [
          'date' => current_time('mysql'),
          'api' => $types[$type] ?? $type,
          'detail'=> $ret['desc']
      ];
      if(!empty($ret['api_code'])){
          $row['detail'] = '错误码:'.$ret['api_code'].(!empty($ret['api_msg'])?','.$ret['api_msg']:'').','.$ret['desc'];
      }
      $api_error = get_option('wb_skt_api_error');
      if(!$api_error){
          $api_error = [];
      }
      $api_error[] = $row;
      $num = count($api_error);
      if($num > 20){
          array_shift($api_error);
      }
      update_option('wb_skt_api_error',$api_error);

      //$errors =
      //error_log(is_array($ret) ? print_r($ret,true) : $ret, 3, __DIR__.'/api.log');
  }

  public static function wb_skt_options_ajax_save()
  {
      $op = self::param('op');
      if(!$op){
          return;
      }
      // $spec_op = ['chk_ver', 'promote'];
      $allow = [
          'lexicon',
          'baidu_reset_token', 'get_setting', 'set_setting', 'verify'
      ];
      if(!in_array($op, $allow, true)){
          return ;
      }
      if(!current_user_can('manage_options')){
          self::ajax_resp(['code' => 1, 'desc' => __('No permission.', 'smart-keywords-tool')]);
          return;
      }
      $nonce = self::param('_ajax_nonce');
      if(!$nonce){
          $nonce = self::param('_ajax_nonce','', 'g');
      }
      if (!wp_verify_nonce(sanitize_text_field($nonce), 'wb_skt_admin_ajax')) {
          self::ajax_resp(['code'=>1,'desc'=> __('Illegal operation.', 'smart-keywords-tool')]);
          return;
      }

      switch ($op)
      {
          case 'lexicon':
              $opt = array();
              //数据
              $data = get_option('wb_skt_kws', array());
              if (!is_array($data)) {
                  $data = [];
              }
              foreach ($data as $k => $v) {
                  if (!isset($v['items'])) {
                      $v['items'] = [];
                  }
                  if (!is_array($v['items'])) {
                      $v['items'] = [];
                  }
                  $v['items'] = array_values($v['items']);
                  $data[$k] = $v;
              }
              //添加
              $add_param = self::param('add');
              if ($add_param && is_array($add_param)) {
                  $item = self::array_sanitize_text_field($add_param);
                  $ret = ['code' => 0, 'desc' => 'success', 'data' => []];
                  do {
                      if (!isset($item['subject']) || strlen($item['subject']) == 0) {
                          $ret['code'] = 1;
                          $ret['desc'] = '主题不能为空';
                          break;
                      }
                      if (!isset($item['items'])) { // || empty($item['items'])
                          $item['items'] = '';
                      }
                      $is_append = sanitize_text_field(self::param('append'));
                      if ($is_append) {

                          if (preg_match('#^\d$#', $item['subject']) && $item['items'] && is_array($item['items'])) {
                              $idx = intval($item['subject']);
                              if (isset($data[$idx])) {
                                  $row = $data[$idx];
                                  $row['items'] = array_merge($row['items'], $item['items']);
                                  $row['items'] = array_unique($row['items']);
                                  $data[$idx] = $row;
                                  update_option('wb_skt_kws', array_values($data), false);
                              }
                          } else {
                              array_push($data, $item);
                              update_option('wb_skt_kws', array_values($data), false);
                          }
                          break;
                      }

                      $item['items'] = trim($item['items']);
                      if ($item['items']) {
                          $item['items'] = str_replace(["\r\n", "\n"], ',', $item['items']);
                          $item['items'] = explode(',', $item['items']);
                          $item['items'] = array_map('trim', $item['items']);
                          $item['items'] = array_filter($item['items'], 'strlen');
                          $item['items'] = array_unique($item['items']);
                      } else {
                          $item['items'] = [];
                      }
                      array_push($data, $item);
                      update_option('wb_skt_kws', array_values($data), false);
                  } while (0);

                  self::ajax_resp($ret);
                  return;
              }
              //删除
              $rm_param = self::param('rm');
              if ($rm_param && is_array($rm_param)) {
                  $item = self::array_sanitize_text_field($rm_param);
                  do {
                      if (!isset($item[0])) {
                          break;
                      }
                      $is_batch = sanitize_text_field(self::param('batch'));
                      if ($is_batch) {
                          foreach ($item as $idx) {
                              if (isset($data[$idx])) {
                                  unset($data[$idx]);
                              }
                          }
                          update_option('wb_skt_kws', array_values($data), false);
                          break;
                      }
                      $idx = intval($item[0]);
                      if (isset($data[$idx])) {
                          unset($data[$idx]);
                          update_option('wb_skt_kws', array_values($data), false);
                      }
                  } while (0);
                  $ret = ['code' => 0, 'desc' => 'success', 'data' => []];
                  self::ajax_resp($ret);
                  return;
              }
              //新增tag
              $tag_param = self::param('tag');
              if ($tag_param && is_array($tag_param)) {

                  $ret = ['code' => 0, 'desc' => 'success', 'data' => []];

                  do{
                      $tags = self::array_sanitize_text_field($tag_param);
                      foreach ($tags as $tag) {
                          $tag = trim($tag);
                          if (!term_exists($tag, 'post_tag')) {
                              wp_insert_term($tag, 'post_tag');
                          }
                      }
                  }while(0);
                  self::ajax_resp($ret);
                  return;
              }


              $ret = array('code' => 0, 'desc' => 'success');
              $ret['data']['items'] = $data;
              $ret['data']['cnf'] = array(
                  'new' => array(
                      'id' => '',
                      'subject' => '',
                      'items' => array()
                  )
              );
              self::ajax_resp($ret);
              break;
          case 'baidu_reset_token':
              delete_option('wb_skt_baidu_app_token');
              $ret = array('code' => 0, 'desc' => 'success');
              self::ajax_resp($ret);
              break;
          case 'get_setting':
              $opt =  self::cnf();
              $ret = array('code' => 0, 'desc' => 'success');
              $ret['data']['opt'] = $opt;
              $ret['data']['cnf'] = array(
                  'api' => self::$skt_api,
              );
              $ret['data']['group'] = [];
              $ret['data']['log'] = [];
              $is_group = sanitize_text_field(self::param('group'));
              if ($is_group) {
                  $data = get_option('wb_skt_kws', array());
                  $group = [];
                  if (is_array($data)) {
                      foreach ($data as $k => $r) {
                          if (empty($r['subject'])) {
                              continue;
                          }
                          $group[] = ['id' => $k, 'name' => $r['subject']];
                      }
                  }
                  $ret['data']['group'] = $group;
              }
              $key_tag = sanitize_text_field(self::param('key'));
              if($key_tag === 'tags'){
                  $ret['data']['log'] = self::get_api_error();
              }

              self::ajax_resp($ret);
              break;
          case 'set_setting':
              $opt_data = self::array_sanitize_text_field(self::param('opt', []));
              $ret = array('code' => 1, 'desc' => 'fail');
              do{
                  $key = sanitize_text_field(self::param('key'));
                  $key2 = implode('', ['re', 'set']);
                  if ($key2 === $key) {
                      $w_key = implode('_', ['wb', 'sk' . 't', '']);
                      $u_uid = get_option($w_key . 'ver', 0);
                      if ($u_uid) {
                          update_option($w_key . 'ver', 0);
                          update_option($w_key . 'cnf_' . $u_uid, '');
                      }
                      break;
                  }
                  if(!empty($opt_data) && is_array($opt_data)){
                      self::wb_set_setting($opt_data);
                  }
                  $ret = array('code' => 0, 'desc' => 'success');
              }while(0);

              self::ajax_resp($ret);
              break;
          case 'verify':
              $ret = ['code' => 1, 'desc' => 'fail'];
              $param = array(
                  'code' => sanitize_text_field(self::param('key')),
                  'host' => sanitize_text_field(self::param('host')),
                  'ver' => 'skt',
              );
              $err = '';
              do {
                  if(empty($param['code']) || empty($param['host'])){
                      $err = __('Illegal operation.', 'smart-keywords-tool');
                      break;
                  }
                  $http = wp_remote_post('https://www.wbolt.com/wb-api/v1/verify', array('sslverify' => false, 'body' => $param, 'headers' => array('referer' => home_url()),));
                  if (is_wp_error($http)) {
                      $err = __('Verification failed, please try again later', 'smart-keywords-tool')
                          . '[error code001 ' . $http->get_error_message() . '])';
                      break;
                  }

                  if ($http['response']['code'] != 200) {
                      $err = __('Verification failed, please try again later', 'smart-keywords-tool')
                          . '[error code001 ' . $http['response']['code'] . '])';
                      break;
                  }

                  $body = $http['body'];


                  $data = json_decode($body, true);
                  if (!$data || $data['code']) {
                      $err_code = $data['data'] ? $data['data'] : '';
                      switch ($err_code) {
                          case 100:
                          case 101:
                          case 102:
                          case 103:
                              $err = __('Configuration error, contact for', 'smart-keywords-tool')
                                  . '<a href="https://www.wbolt.com/?wb=member#/contact" target="_blank">'
                                  . __('technical support', 'smart-keywords-tool') . '</a>('
                                  . __('error code', 'smart-keywords-tool') . $err_code . ')';
                              break;
                          case 200:
                              $err = __('Invalid key, please enter a correct key. (error code 200)', 'smart-keywords-tool');
                              break;
                          case 201:
                              $err = __('Key usage out of limit. (error code 201)', 'smart-keywords-tool');
                              break;
                          case 202:
                          case 203:
                          case 204:
                              $err = __('Verification server exception, contact for', 'smart-keywords-tool')
                                  . '<a href="https://www.wbolt.com/?wb=member#/contact" target="_blank">'
                                  . __('technical support', 'smart-keywords-tool') . '</a>('
                                  . __('error code', 'smart-keywords-tool') . $err_code . ')';
                              break;
                          default:
                              $err = __('Unexpected error, contact for', 'smart-keywords-tool')
                                  . '<a href="https://www.wbolt.com/?wb=member#/contact" target="_blank">'
                                  . __('technical support', 'smart-keywords-tool') . '</a>('
                                  . __('error code', 'smart-keywords-tool') . $err_code . ')';
                      }
                      break;
                  }
                  if (!$data['data']) {
                      $err = __('Verification failed, please try again later. (error code 004)', 'smart-keywords-tool');
                      break;
                  }
                  update_option('wb_skt_ver', $data['v'], false);
                  update_option('wb_skt_cnf_' . $data['v'], $data['data'], false);

                  $ret = ['code' => 0, 'desc' => 'success'];

              } while (false);
              if($err){
                  $ret['desc'] = $err;
              }
              self::ajax_resp($ret);
              break;
      }

  }

  public static function wb_skt_options_ajax_handler()
  {
      $op = self::param('op');
      if(!$op){
          return;
      }
      $allow = [
          'chk_ver', 'promote', 'options'
      ];
      if(!in_array($op, $allow, true)){
          return ;
      }
      $nonce = self::param('_ajax_nonce');
      if(!$nonce){
          $nonce = self::param('_ajax_nonce','', 'g');
      }
      if (!wp_verify_nonce(sanitize_text_field($nonce), 'wb_skt_admin_ajax')) {
          self::ajax_resp(['code'=>1,'desc'=> __('Illegal operation.', 'smart-keywords-tool')]);
          return;
      }
      if(!current_user_can('edit_posts')){
          self::ajax_resp(['code' => 1, 'desc' => __('No permission.', 'smart-keywords-tool')]);
          return;
      }

    switch ($op) {
      case 'chk_ver':
        $http = wp_remote_get('https://www.wbolt.com/wb-api/v1/themes/checkver?code=skt&ver=' . SMART_KW_TOOL_VERSION . '&chk=1', array('sslverify' => false, 'headers' => array('referer' => home_url()),));
        if (wp_remote_retrieve_response_code($http) == 200) {
          echo esc_html(wp_remote_retrieve_body($http));
        }
        exit();
        break;

      case 'promote':
        $ret = ['code' => 0, 'desc' => 'success', 'data' => ''];
        $data = [];
        $expired = 0;
        $update_cache = false;
        do {
          $option = get_option('wb_skt_promote', null);
          do {
            if (!$option || !is_array($option)) {
              break;
            }

            if (!isset($option['expired']) || empty($option['expired'])) {
              break;
            }

            $expired = intval($option['expired']);
            if ($expired < current_time('U')) {
              $expired = 0;
              break;
            }

            if (!isset($option['data']) || empty($option['data'])) {
              break;
            }

            $data = $option['data'];
          } while (0);

          if ($data) {
            $ret['data'] = $data;
            break;
          }
          if ($expired) {
            break;
          }

          $update_cache = true;
          $param = ['c' => 'skt', 'h' => $_SERVER['HTTP_HOST']];
          $http = wp_remote_post('https://www.wbolt.com/wb-api/v1/promote', array('sslverify' => false, 'body' => $param, 'headers' => array('referer' => home_url()),));

          if (is_wp_error($http)) {
            $ret['error'] = $http->get_error_message();
            break;
          }
          if (wp_remote_retrieve_response_code($http) !== 200) {
            $ret['error-code'] = '201';
            break;
          }
          $body = trim(wp_remote_retrieve_body($http));
          if (!$body) {
            $ret['empty'] = 1;
            break;
          }
          $data = json_decode($body, true);
          if (!$data) {
            $ret['json-error'] = 1;
            $ret['body'] = $body;
            break;
          }
          //data = [title=>'',image=>'','expired'=>'2021-05-12','url=>'']
          $ret['data'] = $data;
          if (isset($data['expired']) && $data['expired'] && preg_match('#^\d{4}-\d{2}-\d{2}$#', $data['expired'])) {
            $expired = strtotime($data['expired'] . ' 23:50:00');
          }
        } while (0);
        if ($update_cache) {
          if (!$expired) {
            $expired = current_time('U') + 21600;
          }
          update_option('wb_skt_promote', ['data' => $ret['data'], 'expired' => $expired], false);
        }

        self::ajax_resp($ret);


        break;



      case 'options':
        $ver = get_option('wb_skt_ver', 0);
        $cnf = '';
        if ($ver) {
          $cnf = get_option('wb_skt_cnf_' . $ver, '');
        }
        self::ajax_resp(['o' => $cnf]);
        break;
    }
  }

  public static function wb_set_setting($data)
  {
    //$opt = self::opt();
    /*$opt = array();
    foreach ($data as $key => $value) {
      $opt[$key] = self::stripslashes_deep($value);
    }*/
      $opt = self::_cnf();
      foreach($opt as $k=>$v){
          if(!isset($data[$k])){
              continue;
          }
          if(is_array($v) && !is_array($data[$k])){
              continue;
          }
          $opt[$k] = $data[$k];
      }

    return update_option(self::$optionName, $opt);
  }

  public static function stripslashes_deep($value)
  {
    if (is_array($value)) {
      foreach ($value as $k => $v) {
        $value[$k] = self::stripslashes_deep($v);
      }
    } else {
      $value = stripslashes($value);
    }
    return $value;
  }

  public static function baidu_tags($title, $content)
  {
      self::txt('baidu_tags');

    $cnf = self::cnf('st');

    $ret = ['code' => 1, 'desc' => 'fail', 'data' => []];
    do {
      if (!isset($cnf['appid']) || !$cnf['appid']) {
        $ret['code'] = 7;
        $ret['error'] = 'empty conf';
        break;
      }
      if (!isset($cnf['key']) || !$cnf['key']) {
        $ret['code'] = 8;
        $ret['error'] = 'empty conf';
        break;
      }
      if (!isset($cnf['secret']) || !$cnf['secret']) {
        $ret['code'] = 9;
        $ret['error'] = 'empty conf';
        break;
      }

      $token = self::baidu_app_token($cnf);
      if (!$token) {
        $ret['code'] = 10;
        $ret['error'] = 'token error';
        $error = get_option('wb_skt_baidu_app_error');
        $ret['desc'] = '获取token失败' . ($error ? ',' . $error['error_description'] : '');
        break;
      }
      $post_data = [
        'title' => mb_substr($title, 0, 80),
        'content' => mb_substr($content, 0, 65530)
      ];
      //https://aip.baidubce.com/rpc/2.0/nlp/v1/keyword
      $api = 'https://aip.baidubce.com/rpc/2.0/nlp/v1/keyword?charset=UTF-8&access_token=' . $token;
      $param = array(
        'sslverify' => false,
        'timeout' => 10,
        'body' => wp_json_encode($post_data, JSON_UNESCAPED_UNICODE),
        'headers' => array('Content-Type' => 'application/json')
      );
      $http = wp_remote_post($api, $param);

      $body = wp_remote_retrieve_body($http);
      if (empty($body)) {
        $ret['desc'] = $http->get_error_message();
        $ret['code'] = 11;
        break;
      }
        self::txt($body);
      $resp = json_decode($body, true);
      //error_log($body."\n",3,__DIR__.'/baidu.txt');

      if (!$resp) {
        $ret['error'] = 'api-error';
        $ret['desc'] = 'api response decode error';
        $ret['code'] = 12;
        break;
      }
      if (isset($resp['error_code']) && $resp['error_code']) {
        $ret['code'] = 13;
        $ret['error'] = $resp['error_code'];
        $ret['api_code'] = $resp['error_code'];
        $ret['api_msg'] = $resp['error_msg'];
        $desc = self::error_desc('baidu', $ret['error']);
        if (!$desc) {
          $desc = $resp['error_msg'];
        }
        $ret['desc'] = $desc;
        //$return = '[error:'.$resp['error_code'].',msg:'.$resp['error_msg'].']';
        break;
      }
      if (isset($resp['error']) && $resp['error']) {
        $ret['code'] = 13;
        $ret['error'] = 'api-error';
        $ret['desc'] = $resp;
        break;
      }

      if (!isset($resp['items']) || empty($resp['items'])) {
        $ret['code'] = 14;
        break;
      }
      $max_word = intval(self::cnf('max_word'));
      $num = 0;
      $tags = array();
      foreach ($resp['items'] as $key => $r) {
        $tag = new stdClass();
        $tag->link = '###';
        $tag->name = $r['tag'];
        $tag->count = round($r['score'] * 10000);
        $tags[] = $tag;
        $num++;
        if ($num >= $max_word) {
          break;
        }
      }
      $ret['code'] = 0;
      $ret['desc'] = 'success';
      $ret['data'] = $tags;
    } while (0);


    return $ret;
  }

  public static function qcloud($title, $content)
  {
    $cnf = self::cnf('qcloud');

    $ret = ['code' => 1, 'desc' => 'fail', 'data' => []];
    do {
      if (!isset($cnf['appid']) || !$cnf['appid']) {
        $ret['code'] = 7;
        $ret['error'] = 'empty config';
        break;
      }
      if (!isset($cnf['key']) || !$cnf['key']) {
        $ret['code'] = 8;
        $ret['error'] = 'empty config';
        break;
      }
      if (!isset($cnf['secret']) || !$cnf['secret']) {
        $ret['code'] = 9;
        $ret['error'] = 'empty config';
        break;
      }


      // '10000'
      $text = mb_substr(implode(' ', [$title, $content]), 0, 5000);
      $max_word = intval(self::cnf('max_word'));
      $req  = [
        'Text' => $text,
        'Num' => $max_word,
      ];
      $body = wp_json_encode($req, JSON_UNESCAPED_UNICODE);
      $sign = [
        'time' => current_time('U', 1),
        'date' => current_time('Y-m-d'),
        'key' => $cnf['key'],
        'secret' => $cnf['secret'],
        'payload' => $body,
      ];

      $api = 'https://nlp.tencentcloudapi.com/';
      $param = array(
        'sslverify' => false,
        'timeout' => 10,
        'body' => $body,
        'headers' => array(
          'X-TC-Action' => 'KeywordsExtraction',
          'X-TC-RequestClient' => 'wbolt-quaniz/wordpress',
          'X-TC-Timestamp' => $sign['time'],
          'X-TC-Version' => '2019-04-08',
          'X-TC-Region' => 'ap-guangzhou',
          'Content-Type' => 'application/json',
          'Authorization' => self::authTC3($sign),
        )
      );
      $http = wp_remote_post($api, $param);

      $body = wp_remote_retrieve_body($http);
      if (empty($body)) {
        $ret['error'] = 'empty body';
        $ret['desc'] = $http->get_error_message();
        $ret['code'] = 11;
        break;
      }
      $resp = json_decode($body, true);
      //error_log($body."\n",3,__DIR__.'/qcloud.txt');

      if (!$resp) {
        $ret['code'] = 12;
        $ret['error'] = 'api-error';
        $ret['desc'] = '接口返回错误';
        break;
      }
      if (!isset($resp['Response']) || empty($resp['Response'])) {
        $ret['code'] = 13;
        $ret['error'] = 'api-resp-error';
        $ret['desc'] = '接口返回错误';
        break;
      }
      $resp = $resp['Response'];
      if (isset($resp['Error']) && $resp['Error']) {
        $ret['code'] = 13;
        $ret['error'] = $resp['Error']['Code'];
        $ret['api_code'] = $resp['Error']['Code'];
        $ret['api_msg'] = $resp['Error']['Message'];
        $desc = self::error_desc('qcloud', $ret['error']);
        if (!$desc) {
          $desc = $resp['Error']['Message'];
        }
        $ret['desc'] = $desc;

        break;
      }

      if (!isset($resp['Keywords']) || empty($resp['Keywords'])) {
        $ret['code'] = 14;
        break;
      }
      $tags = array();

      $num = 0;
      foreach ($resp['Keywords'] as $key => $r) {
        $tag = new stdClass();

        if (strlen($r['Word']) > 80) {
          continue;
        }
        $tag->link = '###';
        $tag->name = $r['Word'];
        $tag->count = round($r['Score'] * 10000);
        $tags[] = $tag;
        $num++;
        if ($num >= $max_word) {
          break;
        }
      }
      $ret['code'] = 0;
      $ret['desc'] = 'success';
      $ret['data'] = $tags;
    } while (0);


    return $ret;
  }

  public static function authTC3($param)
  {
    $algo = "TC3-HMAC-SHA256";
    $service = 'nlp';
    $credentialScope = $param['date'] . "/" . $service . "/tc3_request";

    $payloadHash = hash("SHA256", $param['payload']);
    $h = [
      'content-type:application/json',
      'host:nlp.tencentcloudapi.com' . "\n"
    ];
    $canonicalRequest = implode("\n", [
      'POST', //$reqmethod
      '/', //$canonicalUri
      '', //$canonicalQueryString
      implode("\n", $h), //$canonicalHeaders
      'content-type;host', //$signedHeaders
      $payloadHash, //$payloadHash
    ]);
    //error_log($canonicalRequest."\n",3,__DIR__.'/qcloud.txt');

    $hashedCanonicalRequest = hash("SHA256", $canonicalRequest);

    $str2sign = implode("\n", [
      $algo,
      $param['time'],
      $credentialScope,
      $hashedCanonicalRequest
    ]);
    //error_log($str2sign."\n",3,__DIR__.'/qcloud.txt');
    $signature = self::signTC3($param['secret'], $param['date'], $service, $str2sign);
    $auth = $algo .
      " Credential=" . $param['key'] . "/" . $credentialScope .
      ", SignedHeaders=content-type;host, Signature=" . $signature;
    //error_log($auth."\n",3,__DIR__.'/qcloud.txt');
    return $auth;
  }
  public static function signTC3($skey, $date, $service, $str2sign)
  {
    $dateKey = hash_hmac("SHA256", $date, "TC3" . $skey, true);
    $serviceKey = hash_hmac("SHA256", $service, $dateKey, true);
    $reqKey = hash_hmac("SHA256", "tc3_request", $serviceKey, true);
    return hash_hmac("SHA256", $str2sign, $reqKey);
  }

  public static function open_calais($txt)
  {
    $ret = ['code' => 1, 'desc' => 'fail', 'data' => []];

    $cnf = self::cnf('opencalais');
    $param = [
      'timeout' => 30,
      'sslverify' => false,
      'headers' => [
        'X-AG-Access-Token' => $cnf['key'],
        'Content-Type'      => 'text/html',
        'outputFormat'      => 'application/json'
      ],
      'body' => $txt
    ];

    $tags = [];

    do {

      if (!$cnf['key']) {
        $ret['code'] = 22;
        break;
      }

      $http = wp_remote_post('https://api-eit.refinitiv.com/permid/calais', $param);

      if (is_wp_error($http)) {
        $ret['code'] = 23;
        $ret['desc'] = $http->get_error_message();
        break;
      }
      $body = wp_remote_retrieve_body($http);
      if (!$body) {
        $ret['code'] = 24;
        break;
      }
      //            error_log($body."\n",3,__DIR__.'/calais.txt');
      $data = json_decode($body, true);
      if (!$data || !is_array($data)) {
        $ret['code'] = 25;
        break;
      }
      if(!empty($data['message'])){
          $ret['code'] = 26;
          $ret['desc'] = $data['message'];
          break;
      }
      $max_word = intval(self::cnf('max_word'));
      $num = 0;
      foreach ($data as $r) {
        if (isset($r['_typeGroup']) && $r['_typeGroup'] == 'socialTag') {
          $tag = new stdClass();
          $tag->name = $r['name'];
          $tag->link = '###';
          $tag->count = 10;
          $tags[] = $tag;
          $num++;
          if ($num >= $max_word) {
            break;
          }
        }
      }

      $ret['data'] = $tags;
      $ret['code'] = 0;
      $ret['desc'] = 'success';
    } while (0);

    return $ret;
  }

  public static function dandelion($txt = null)
  {
    $ret = ['code' => 1, 'desc' => 'fail', 'data' => []];
    $cnf = self::cnf('dandelion');
    $min_confidence = $cnf['confidence'] ? $cnf['confidence'] : 0.6;

    $param = [
      'timeout' => 30,
      'sslverify' => false,
      'body' => [
        'text' => $txt,
        'min_confidence' => $min_confidence,
        'token' => $cnf['key'],

      ]
    ];
    $tags = [];

    do {

      if (!$cnf['key']) {
        $ret['code'] = 22;
        break;
      }

      $http = wp_remote_post('https://api.dandelion.eu/datatxt/nex/v1', $param);

      if (is_wp_error($http)) {
        $ret['code'] = 23;
        $ret['desc'] = $http->get_error_message();
        break;
      }
      $body = wp_remote_retrieve_body($http);
      if (!$body) {
        $ret['code'] = 24;
        break;
      }

      //            error_log($body."\n",3,__DIR__.'/dandelion.txt');
      $data = json_decode($body, true);
      if (!$data || !is_array($data)) {
        $ret['code'] = 25;
        break;
      }
      if(!empty($data['error'])){
          $ret['code'] = 26;
          $ret['desc'] = self::error_desc('dandelion',$data['code']);
          $ret['api_code'] = $data['code'];
          $ret['api_msg'] = $data['message'];
          break;
      }
      if (!isset($data['annotations'])) {
        $ret['code'] = 26;
        break;
      }

      $exists = [];
      $max_word = intval(self::cnf('max_word'));
      $num = 0;
      foreach ($data['annotations'] as $r) {
        if ($r['title']) {
          if (in_array($r['title'], $exists)) {
            continue;
          }
          $exists[] = $r['title'];
          $tag = new stdClass();
          $tag->name = $r['title'];
          $tag->link = '###';
          $tag->count = 10;
          if (isset($r['confidence'])) {
            $tag->count = ceil(10000 * $r['confidence']);
          }
          $tags[] = $tag;
          $num++;
          if ($num >= $max_word) {
            break;
          }
        }
      }
      $ret['data'] = $tags;
      $ret['code'] = 0;
      $ret['desc'] = 'success';
    } while (0);

    return $ret;
  }

  public static function get_terms($taxonomy = 'post_tag', $post_id = 0)
  {
    $post_id = (int) $post_id;
    if (!$post_id) {
      return '';
    }

    $terms = wp_get_post_terms($post_id, $taxonomy, array('fields' => 'names'));
    if (empty($terms) || is_wp_error($terms)) {
      return '';
    }

    $terms = array_unique($terms); // Remove duplicate
    //		$terms = join( ', ', $terms );
    //		$terms = esc_attr( $terms );
    //		$terms = apply_filters( 'tags_to_edit', $terms );

    return $terms;
  }

  public static function xfyun($title, $content)
  {
    $cnf = self::cnf('xf');

    $ret = ['code' => 1, 'desc' => 'fail', 'data' => []];
    do {
      if (!isset($cnf['appid']) || !$cnf['appid']) {
        $ret['code'] = 7;
        $ret['error'] = 'empty config';
        break;
      }
      if (!isset($cnf['key']) || !$cnf['key']) {
        $ret['code'] = 8;
        $ret['error'] = 'empty config';
        break;
      }

      // '10000'
      $text = mb_substr(implode(' ', [$title, $content]), 0, 5000);

      $timestamp = current_time('U', 1);
      $body = ['text' => $text];
      $xParam = base64_encode(wp_json_encode(['type' => 'dependent']));
      $xCheckSum = md5($cnf['key'] . $timestamp . $xParam);
      $api = 'https: //ltpapi.xfyun.cn/v1/ke';
      $param = array(
        'sslverify' => false,
        'timeout' => 10,
        'body' => $body,
        'headers' => array(
          'X-Appid' => $cnf['appid'],
          'X-CurTime' => $timestamp,
          'X-Param' => $xParam,
          'X-CheckSum' => $xCheckSum,
          'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
        )
      );
      $http = wp_remote_post($api, $param);

      $body = wp_remote_retrieve_body($http);
      if (empty($body)) {
        $ret['error'] = 'empty body';
        $ret['desc'] = $http->get_error_message();
        $ret['code'] = 11;
        break;
      }
      $resp = json_decode($body, true);
      //error_log($body."\n",3,__DIR__.'/xf.txt');

      if (empty($resp) || !is_array($resp) || !isset($resp['code'])) {
        $ret['code'] = 12;
        $ret['error'] = 'api-error';
        $ret['desc'] = '接口返回错误';
        break;
      }

      if ($resp['code']) {
        $ret['code'] = 13;
        $ret['error'] = $resp['code'];
        $ret['desc'] = $resp['desc'];
        $ret['api_code'] = $resp['code'];
        //$ret['api_msg'] = $resp['desc'];
        break;
      }

      if (empty($resp['data'])) {
        $ret['code'] = 0;
        $ret['desc'] = 'success';
        $ret['data'] = [];
        break;
      }

      $data = $resp['data']['ke'];
      $max_word = intval(self::cnf('max_word'));
      $num = 0;
      $tags = array();
      foreach ($data as $key => $r) {
        $tag = new stdClass();

        if (strlen($r['word']) > 80) {
          continue;
        }
        $tag->link = '###';
        $tag->name = $r['word'];
        $tag->count = round($r['score'] * 10000);
        $tags[] = $tag;
        $num++;
        if ($num >= $max_word) {
          break;
        }
      }
      $ret['code'] = 0;
      $ret['desc'] = 'success';
      $ret['data'] = $tags;
    } while (0);


    return $ret;
  }

  /**
   * 是否启用古腾堡
   * @return bool
   */
  public static function is_active_gutenberg_editor()
  {
    if (function_exists('is_gutenberg_page') && is_gutenberg_page()) {
      return true;
    }

    global $current_screen;
    $current_screen = get_current_screen();
    if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) {
      return true;
    }
    return false;
  }



  public static function error_desc($type, $code)
  {
    $error_list = [
      'qcloud' => [
        "InternalError" => "内部错误。",
        "InvalidParameterValue.EmptyValueError" => "参数空值错误",
        "InvalidParameterValue.TextEncodeError" => "文本编码错误，不符合utf-8",
        "InvalidParameterValue.TextFormatError" => "文本输入格式错误",
        "InvalidParameterValue.TextTooLong" => "输入文本超出长度限制",
        "InvalidParameterValue.ValueRangeError" => "参数取值范围错误",
        "ResourceInsufficient.QuotaRunOut" => "额度用尽，请充值后重试",
        "ResourceUnavailable.ServiceNotOpenedError" => "您的账号尚未开通NLP服务，请登录腾讯云NLP控制台进行服务开通后再使用",
        "UnauthorizedOperation.AuthenticateFailed" => "实名认证失败",
        "FailedOperation.IllegalTextError" => "非法文本输入导致返回异常",
        "FailedOperation.RequestTimeout" => "后端服务超时。",
        "FailedOperation.RpcFail" => "RPC请求失败，一般为算法微服务故障。",
        "FailedOperation.TextEmbeddingFailed" => "文本向量化失败",
        "FailedOperation.WordNotFound" => "查找不到词语",
        "InternalError.ResourceRequestError" => "资源请求错误",
        "InternalError.ServiceCallError" => "服务调用错误",
        "InvalidParameterValue.TextNumTooMuch" => "输入文本超出数量限制",
        "LimitExceeded.ResourceReachedLimit" => "资源用量达到上限",
        "ResourceInUse.NameExists" => "名称已存在",
        "ResourceInUse.ResourceOperating" => "资源正在操作中",
        "ResourceNotFound.DataNotFound" => "数据资源不存在",
        "ResourceNotFound.FileNotFound" => "文件资源不存在",
        "ResourceUnavailable.FileUnavailable" => "文件资源不可用",
        "ActionOffline" => "接口已下线。",
        "AuthFailure.InvalidAuthorization" => "请求头部的 Authorization 不符合腾讯云标准。",
        "AuthFailure.InvalidSecretId" => "密钥非法（不是云 API 密钥类型）。",
        "AuthFailure.MFAFailure" => "MFA 错误。",
        "AuthFailure.SecretIdNotFound" => "密钥不存在。请在 控制台 检查密钥是否已被删除或者禁用，如状态正常，请检查密钥是否填写正确，注意前后不得有空格。",
        "AuthFailure.SignatureExpire" => "签名过期。Timestamp 和服务器时间相差不得超过五分钟，请检查本地时间是否和标准时间同步。",
        "AuthFailure.SignatureFailure" => "签名错误。签名计算错误，请对照调用方式中的签名方法文档检查签名计算过程。",
        "AuthFailure.TokenFailure" => "token 错误。",
        "AuthFailure.UnauthorizedOperation" => "请求未授权。请参考 CAM 文档对鉴权的说明。",
        "DryRunOperation" => "DryRun 操作，代表请求将会是成功的，只是多传了 DryRun 参数。",
        "FailedOperation" => "操作失败。",
        "InvalidAction" => "接口不存在。",
        "InvalidParameter" => "参数错误（包括参数格式、类型等错误）。",
        "InvalidParameterValue" => "参数取值错误。",
        "InvalidRequest" => "请求 body 的 multipart 格式错误。",
        "IpInBlacklist" => "IP地址在黑名单中。",
        "IpNotInWhitelist" => "IP地址不在白名单中。",
        "LimitExceeded" => "超过配额限制。",
        "MissingParameter" => "缺少参数。",
        "NoSuchProduct" => "产品不存在",
        "NoSuchVersion" => "接口版本不存在。",
        "RequestLimitExceeded" => "请求的次数超过了频率限制。",
        "RequestLimitExceeded.GlobalRegionUinLimitExceeded" => "主账号超过频率限制。",
        "RequestLimitExceeded.IPLimitExceeded" => "IP限频。",
        "RequestLimitExceeded.UinLimitExceeded" => "主账号限频。",
        "RequestSizeLimitExceeded" => "请求包超过限制大小。",
        "ResourceInUse" => "资源被占用。",
        "ResourceInsufficient" => "资源不足。",
        "ResourceNotFound" => "资源不存在。",
        "ResourceUnavailable" => "资源不可用。",
        "ResponseSizeLimitExceeded" => "返回包超过限制大小。",
        "ServiceUnavailable" => "当前服务暂时不可用。",
        "UnauthorizedOperation" => "未授权操作。",
        "UnknownParameter" => "未知参数错误，用户多传未定义的参数会导致错误。",
        "UnsupportedOperation" => "操作不支持。",
        "UnsupportedProtocol" => "http(s) 请求协议错误，只支持 GET 和 POST 请求。",
        "UnsupportedRegion" => "接口不支持所传地域。",
      ],
      'baidu' => [
        "1" => "服务器内部错误，请再次请求，如果持续出现此类错误，请通过QQ群（458967388）或工单联系技术支持团队解决",
        "2" => "服务暂不可用，请再次请求，如果持续出现此类错误，请通过QQ群（458967388）或工单联系技术支持团队解决",
        "3" => "调用的API不存在，请检查接口地址是否输入正确",
        "4" => "集群超限额，请通过QQ群（458967388）或工单联系技术支持团队",
        "6" => "无权限访问，请检查创建的APP应用是否有该接口的使用权限（1、API列表页是否可见、2、应用详情的接口是否勾选）",
        "17" => "每天请求量超限额，请您严格控制日调用量，调用量需<=该接口的日调用量限制，若有更高调用量需求，请前往产品页面进行购买或通过QQ群（458967388）进行咨询",
        "18" => "QPS超限额，请您严格控制处理并发量，调用并发需<=该接口的QPS限制，若有更高QPS需求，请前往产品页面进行购买或通过QQ群（458967388）进行咨询",
        "19" => "请求总量超限额",
        "100" => "输入中包含了无效或错误参数，请检查代码",
        "110" => "Token失效，请进行更换",
        "111" => "token过期，Access Token的有效期为30天，请在到期前进行更换",
        "216100" => "参数错误",
        "216202" => "传入文本涉及敏感内容，请检查后重试",
        "216203" => "传入参数非法，请检查后重试",
        "216401" => "请求频率超限",
        "282000" => "服务器内部错误，请再次请求，如果持续出现此类错误，请通过QQ群（458967388）或工单联系技术支持团队",
        "282002" => "编码错误，接口默认支持的是GBK编码，若需要输入的文本为UTF-8编码，请在url上添加参数charset=UTF-8",
        "282003" => "缺少接口必要参数",
        "282004" => "parameter(s)  请求中包含非法参数，请检查后重新尝试",
        "282008" => "charset:{字符编码名称}  仅支持GBK和UTF-8，其余为不支持的字符编码，请检查后重新尝试",
        "282011" => "定制化接口未使用数据进行训练，或者训练后未生效该接口，建议检查定制化是否完成数据训练以及是否已生效该接口",
        "282130" => "当前查询无结果返回，请检查原始评论中是否含有核心观点，也可能是机器召回不足导致观点无法被识别到导致，如需优化，请工单反馈",
        "282131" => "输入长度超限，请参照接口文档说明，检查输入文本的长度，并进行严格控制",
        "282133" => "{参数名} not exist 接口参数缺失，请检查输入参数是否优遗漏，补充参数并再次尝试",
        "282134" => "输入为空，请检查输入内容，输入的内容避免为空",
        "282300" => "算法词典暂未收录该word，可联系客服做算法版本的升级以进行支持，相关badcase和需求可以提交工单进行咨询",
        "282301" => "word_1提交的词汇暂未收录，无法比对相似度，可联系客服做算法版本升级进行支持，相关badcase和需求可以提交工单进行咨询",
        "282302" => "word_2提交的词汇暂未收录，无法比对相似度，可联系客服做算法版本升级进行支持，相关badcase和需求可以提交工单进行咨询",
        "282303" => "word_1和word_2暂未收录，无法比对相似度 ，可联系客服做算法版本升级进行支持，相关badcase和需求可以提交工单进行咨询",
        "282355" => "达到存储数量上限，请检查已经创建的实体库或实体名单数量",
        "282501" => "单个实体的字符串长度超限，请控制长度限制为64个字符内",
      ],
        'dandelion' => [
            'error.notFound' => '找不到请求的资源。',
            'error.missingParameter' => '缺少必需的参数。',
            'error.unknownParameter' => '请求发送了一个未知参数。',
            'error.invalidParameter' => '一个或多个参数的值无效。',
            'error.requestURITooLong' => '提交的请求的URI太长。这通常发生在使用HTTP-GET调用API时；对于大型请求，请使用HTTP-POST。',
            'error.methodNotAllowed' => '您正在使用HTTP方法（GET/POST）发送不允许的请求。 ',
            'error.requestTooLarge' => '提交的请求的正文太大（如本文所述，当前限制设置为1MiB）。',
            'error.authenticationError' => '您的请求是不允许的：当没有更多的单元可用时，或者当身份验证令牌不正确时（或者旧版$app_id和$app_key无效），就会发生这种情况。',
            'error.internalServerError' => '我们这边发生了一些非常不对劲的事情。如果发生这种情况，我们可能已经在做了。',
            'error.badGateway' => '  回答您的查询所需的某些外部资源未成功响应。',
            'error.RateLimitExceeded' => '您超出了费率限制，请求尚未处理，并且没有消耗任何单位。请考虑在调用Dandelion API以满足限制时添加延迟。',
        ],
    ];
    if (!isset($error_list[$type])) {
      return false;
    }
    $list = $error_list[$type];
    if (!isset($list[$code])) {
      return false;
    }
    return $list[$code];
  }
}
