<?php
/**
 * CSV Iterator
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/csv
 * @package	CSV
 */
namespace JasBulilit\CSV;

class CSVIterator implements \Iterator {

	/**
	 * @var \SplFileObject
	 */
	private $_csv;

	/**
	 * @var CSVReader
	 */
	private $_reader;

	/**
	 * @param string $csv_path	filepath/url(when allow_url_fopen enabled)
	 * @param CSVReader $reader
	 */
	public function __construct($csv_path, CSVReader $reader) {
		$open_mode = 'r';
		$use_include_path = false;
		$context = $reader->getContext();
		if ($context !== null) {
			$this->_csv = new \SplFileObject($csv_path, $open_mode, $use_include_path, $context);
		} else {
			$this->_csv = new \SplFileObject($csv_path, $open_mode, $use_include_path);
		}

		$this->_csv->setFlags(\SplFileObject::DROP_NEW_LINE|\SplFileObject::READ_CSV);
		$this->_csv->setCsvControl(
			$reader->getDelimiter(), $reader->getEnclosure(), $reader->getEscape());

		$this->_reader = $reader;
	}

	/**
	 * @return string[]
	 */
	public function current() {
		$row = $this->_csv->current();
		if ($this->_isEmpty($row)) {
			return array();
		}
		return $row;
	}

	/**
	 * @see \SplFileObject::next()
	 * @return void
	 */
	public function next() {
		$this->_csv->next();
	}

	/**
	 * @see \SplFileObject::key()
	 * @return integer	row index
	 */
	public function key() {
		return $this->_csv->key();
	}

	/**
	 * @see \SplFileObject::valid()
	 * @return boolean
	 */
	public function valid() {
		return $this->_csv->valid();
	}

	/**
	 * @see \SplFileObject::rewind()
	 * @return void
	 * @throws \RuntimeException
	 */
	public function rewind() {
		$this->_csv->rewind();
		if ($this->_reader->getSkipHeaderFlag()) {
			$this->current();
			$this->next();
		}
	}

	/**
	 * 空行判定
	 *
	 * @param String[] $row
	 * @return boolean
	 */
	private function _isEmpty($row) {
		foreach ($row as $col) {
			if ($col != '') {
				return false;
			}
		}
		return true;
	}
}
