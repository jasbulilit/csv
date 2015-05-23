<?php
/**
 * Test bootstrap
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/csv
 * @package	CSV Test
 */

require_once __DIR__ . '/../vendor/autoload.php';


function getDataURI($rows) {
	return 'data:text/plain,' . implode(PHP_EOL, $rows);
}

function toCSV($row_str, $delimiter = ',', $enclosure = '"') {
	return explode($delimiter, str_replace($enclosure, '', $row_str));
}
