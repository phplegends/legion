<?php

namespace Light;


/**
 * Load and cast json file
 * 
 * @param string $file
 * @param boolean $castToArray
 * */
function json_load_file($file, $castToArray = false)
{
    if (file_exists($file)) {

        return json_decode(file_get_contents($file), $castToArray);
    }

    throw new \InvalidArgumentException("The file '$file' doesn't not exists");
}


/**
 * 
 * @param & array $array
 * @param string $path
 * @param mixed $values
 * @return mixed
 * */
function array_path_set(array &$array, $path, $value)
{
    foreach (array_reverse(explode('.', $path)) as $key) {
        $value = [$key => $value];
    }
    return $array = array_replace_recursive($array, (array)$value);
}

/**
 * 
 * 
 * @param array $array
 * @param string $path
 * @param mixed $default
 * @return mixed
 * */
function array_path_get(array $array, $path, $default = null)
{
    $ref =& $array;
    foreach (explode('.', $path) as $key) $ref =& $ref[$key];
    return $ref === null ? $default : $ref;
}