<?php
if (in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '192.168.56.101')))
{
  apc_clear_cache();
  apc_clear_cache('user');
  apc_clear_cache('opcode');
  echo json_encode(array('success' => true));
}
else
{
  die('No valid IP');
}
?>
