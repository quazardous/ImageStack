<?php
/**
 * Merge an array recursively. Values are overwritten.
 * @param array $array1
 * @param array $array2
 * @return array merged array
 */
function array_merge_recursive_distinct ( array $array1, array $array2 )
{
	$merged = $array1;

	foreach ( $array2 as $key => &$value )
	{
		if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
		{
			$merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
		}
		else
		{
			$merged [$key] = $value;
		}
	}

	return $merged;
}