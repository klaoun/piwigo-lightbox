<?php

global $page, $conf, $template, $user;

include_once(LIGHTBOX_PATH.'functions.inc.php');
$params = unserialize($conf['lightbox']);
$conf['lightbox_rel'] = isset($conf['lightbox_rel']) ? ++$conf['lightbox_rel'] : 0;

switch ($user['theme'])
{
  case 'simple':
    $selector = '#thumbnails a';
    break;

  default:
    $selector = '.thumbnails a';
    break;
}

$template->func_known_script(array('id'=>'jquery', 'src'=>get_root_url().'themes/default/js/jquery.packed.js'), $smarty);
$template->func_known_script(array('id'=>'colorbox', 'src'=>get_root_url().'plugins/lightbox/js/jquery.colorbox-min.js'), $smarty);
$template->block_html_head('', '
<link rel="stylesheet" href="'.get_root_url().'plugins/lightbox/theme/'.$params['theme'].'/colorbox.css" type="text/css" media="screen">
<script type="text/javascript">
$(document).ready(function(){
$("'.$selector.'").attr("href", function () {
  return this.name;    
});
$("'.$selector.'").colorbox({
  current: "",
  transition: "'.$params['transition'].'",
  speed: "'.$params['transition_speed'].'",
  initialWidth: "'.(!empty($params['initial_width']) ? $params['initial_width'] : $config_default['initial_width']).'",
  initialHeight: "'.(!empty($params['initial_height']) ? $params['initial_height'] : $config_default['initial_height']).'",
  width: '.(!empty($params['fixed_width']) ? '"'.$params['fixed_width'].'"' : 'false').',
  height: '.(!empty($params['fixed_height']) ? '"'.$params['fixed_height'].'"' : 'false').'
  },
  function() { 
    $.post("'.get_root_url().'plugins/lightbox/save_history.php", {
      imgid:   this.id,
      catid:   "'.@$page['category']['id'].'",
      section: "'.@$page['section'].'",
      tagids:  "'.@implode(',', @$page['tag_ids']).'"
  });
});
});
</script>', $smarty, $repeat);

foreach($tpl_thumbnails_var as $key => $tpl_var)
{
  // Image URL for lightbox
  if ($newurl = get_lightbox_url($pictures[$key]))
  {
    $tpl_thumbnails_var[$key]['URL'] .= '" id="img-'.$pictures[$key]['id'].'" name="'.$newurl;
  }
  else
  {
    continue;
  }

  // Title display
  if ($params['display_name'])
  {
    $tpl_thumbnails_var[$key]['URL'] .= '" title="'.get_lightbox_title($pictures[$key], $params['name_link']);
  }

  // Arrows display
  if ($params['display_arrows'])
  {
    $tpl_thumbnails_var[$key]['URL'] .= '" rel="colorbox'.$conf['lightbox_rel'];
  }
}

// Add all items from category
if ($params['display_arrows'] and $params['all_cat'] and !empty($page['navigation_bar']))
{
  $rank_of = array_flip($page['items']);
  if ($page['start'] > 0)
  {
    $selection = array_slice($page['items'], 0, $page['start']);
    $template->concat('PLUGIN_INDEX_CONTENT_BEGIN', get_lightbox_extra_pictures($selection, $rank_of, $params['name_link']));
  }

  if (count($page['items']) > $page['start'] + $page['nb_image_page'])
  {
    $selection = array_slice($page['items'], $page['start'] + $page['nb_image_page']);
    $template->concat('PLUGIN_INDEX_CONTENT_END', get_lightbox_extra_pictures($selection, $rank_of, $params['name_link']));
  }
}

?>