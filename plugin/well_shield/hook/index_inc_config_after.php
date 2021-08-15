<?php exit;
$well_filter = array_value($config, 'filter');
if (1 != $gid && array_value($well_filter, 'ip_enable')) {
    if(in_array($longip, array_value($well_filter, 'ips', array()))) exit('Access Denied');
}
?>