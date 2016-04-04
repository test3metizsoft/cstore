<?php

define('PATH', getcwd() . '/session/');

//echo PATH;exit;
function destroy($dir) {
    $mydir = opendir($dir);
    while (false !== ($file = readdir($mydir))) {
        if ($file != "." && $file != "..") {
            chmod($dir . $file, 0777);
            if (is_dir($dir . $file)) {
                chdir('.');
                destroy($dir . $file . '/');
                if ($file != 'removesessionfiles.php') {
                    rmdir($dir . $file) or DIE("couldn't delete $dir$file<br />");
                }
            } else {
                if ($file != 'removesessionfiles.php') {
                    unlink($dir . $file) or DIE("couldn't delete $dir$file<br />");
                }
            }
        }
    }
    closedir($mydir);
}
destroy(PATH);
echo 'all done.';
?>