<?php
if (!function_exists('sanitize_path')) {
    /**
     * Similar to realpath but with non existing file.
     * @param string $path
     * @return string
     */
    function sanitize_path($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $isRoot = $path[0] == DIRECTORY_SEPARATOR;
        $path = explode(DIRECTORY_SEPARATOR, $path);
        $parts = array_filter($path, 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return ($isRoot ? DIRECTORY_SEPARATOR : '') . implode(DIRECTORY_SEPARATOR, $absolutes);
    }
}