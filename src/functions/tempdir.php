<?php
if (!function_exists('tempdir')) {
    function tempdir($prefix, $dir = null, $mode = 0755) {
        if (empty($dir)) {
            $dir = sys_get_temp_dir();
        }
        $tempfile = tempnam(sys_get_temp_dir(), $prefix);
        if (file_exists($tempfile)) {
            unlink($tempfile);
        }
        mkdir($tempfile, $mode);
        return $tempfile;
    }
}