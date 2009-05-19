<?php

define('PHPWG_ROOT_PATH','../../');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');

check_status(ACCESS_GUEST);

if (!isset($_POST['imgid'])
  or !($imgid = explode('img-', $_POST['imgid']))
  or !is_numeric(@$imgid[1]))
{
  die;
}

$do_log = $conf['log'];
if (is_admin())
{
  $do_log = $conf['history_admin'];
}
if (is_a_guest())
{
  $do_log = $conf['history_guest'];
}

if (!$do_log)
{
  exit();
}

$image_id = mysql_real_escape_string($imgid[1]);

if (!empty($_POST['section']))
{
  $page['section'] = mysql_real_escape_string($_POST['section']);
}
if (!empty($_POST['catid']))
{
  $page['category']['id'] = mysql_real_escape_string($_POST['catid']);
}
if ('tags'==@$page['section'] and !empty($_POST['tagids']))
{
  $tags_string = mysql_real_escape_string($_POST['tagids']);
}

  $query = '
INSERT INTO '.HISTORY_TABLE.'
  (
    date,
    time,
    user_id,
    IP,
    section,
    category_id,
    image_id,
    image_type,
    tag_ids,
    lightbox
  )
  VALUES
  (
    CURDATE(),
    CURTIME(),
    '.$user['id'].',
    \''.$_SERVER['REMOTE_ADDR'].'\',
    '.(isset($page['section']) ? "'".$page['section']."'" : 'NULL').',
    '.(isset($page['category']['id']) ? $page['category']['id'] : 'NULL').',
    '.$image_id.',
    "picture",
    '.(isset($tags_string) ? "'".$tags_string."'" : 'NULL').',
    "true"
  )
;';

pwg_query($query);

?>