<?php
/**
 * CSV Writer
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/csv
 * @package	CSV
 */
namespace JasBulilit\CSV;

class CSVWriter extends AbstractCSV {

	/**
	 * @var resource
	 */
	private $_fp;

	/**
	 * @param array $row
	 * @return void
	 */
	public function append(array $row) {
		$fp = $this->_getFilePointer();

		if (version_compare(PHP_VERSION, '5.5.4') >= 0) {
			fputcsv($fp, $row, $this->delimiter, $this->enclosure, $this->escape);
		} else {
			fputcsv($fp, $row, $this->delimiter, $this->enclosure);
		}
	}

	/**
	 * close file pointer
	 *
	 * @return void
	 */
	public function close() {
		if (empty($this->_fp)) {
			return;
		}
		fclose($this->_fp);
	}

	/**
	 * @return resource
	 */
	private function _getFilePointer() {
		if (empty($this->_fp)) {
			$this->_fp = $this->_initFilePointer();
		}
		return $this->_fp;
	}

	/**
	 * @throws \RuntimeException
	 * @return resource
	 */
	private function _initFilePointer() {
		$mode = 'w';
		$csv_path = $this->getCSVPath();
		$context = $this->getContext();

		$uri = $this->buildUri($csv_path, parent::FILTER_CHAIN_WRITE);

		$fp = (isset($context))
			? fopen($uri, $mode, false, $context)
			: fopen($uri, $mode);
		if ($fp === false) {
			throw new \RuntimeException('Failed to open file: ' . $uri);
		}

		return $fp;
	}
}
