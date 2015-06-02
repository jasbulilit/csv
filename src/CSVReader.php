<?php
/**
 * CSV Reader
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/csv
 * @package	CSV
 */
namespace JasBulilit\CSV;

class CSVReader extends AbstractCSV implements \IteratorAggregate {

	const DEFAULT_ITERATOR_CLASS = '\JasBulilit\CSV\CSVIterator';

	private $_is_skip_header = false;

	/**
	 * @var string
	 */
	private $_iterator_class;

	/**
	 * @param string $csv_path	filepath
	 * @param resource|null $context	stream context resource
	 * @throws \InvalidArgumentException
	 */
	public function __construct($csv_path, $context = null) {
		parent::__construct($csv_path, $context);

		$this->setIteratorClass(self::DEFAULT_ITERATOR_CLASS);
	}

	/**
	 * @return string
	 */
	public function getIteratorClass() {
		return $this->_iterator_class;
	}

	/**
	 * @param string $class_name
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function setIteratorClass($class_name) {
		if ($class_name != self::DEFAULT_ITERATOR_CLASS
			&& ! is_subclass_of($class_name, self::DEFAULT_ITERATOR_CLASS)) {
			throw new \InvalidArgumentException('$class_name must extends ' . self::DEFAULT_ITERATOR_CLASS);
		}
		$this->_iterator_class = $class_name;
	}

	/**
	 * ヘッダー行のスキップフラグを取得
	 *
	 * @return boolean
	 */
	public function getSkipHeaderFlag() {
		return $this->_is_skip_header;
	}

	/**
	 * ヘッダー行のスキップフラグを設定
	 *
	 * @param boolean $skip_header_flag
	 */
	public function setSkipHeaderFlag($skip_header_flag) {
		$this->_is_skip_header = (boolean) $skip_header_flag;
	}

	/**
	 * @see \IteratorAggregate::getIterator()
	 * @return CSVIterator
	 */
	public function getIterator() {
		return new $this->_iterator_class(
			$this->buildUri($this->getCSVPath(), parent::FILTER_CHAIN_READ),
			$this
		);
	}
}
