<?php exit;
if ($randoms) {
    in_array($_thread['tid'], $randoms) AND $arrlist['randomlist'][$_thread['tid']] = $_thread;
}
?>