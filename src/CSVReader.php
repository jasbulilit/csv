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

	/**
	 * @var string
	 */
	private $_iterator_class;

	/**
	 * @param string $csv_path	filepath
	 * @param resource|null $context	stream context resource
	 * @param string $class_name	CSVIterator class name
	 * @throws \InvalidArgumentException
	 */
	public function __construct($csv_path, $context = null, $class_name = self::DEFAULT_ITERATOR_CLASS) {
		parent::__construct($csv_path, $context);

		$this->setIteratorClass($class_name);
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
	 * @see \IteratorAggregate::getIterator()
	 * @return CSVIterator
	 */
	public function getIterator() {
		$options = array(
			'context' => $this->getContext()
		);

		return new $this->_iterator_class(
			$this->buildUri($this->getCSVPath(), parent::FILTER_CHAIN_READ),
			$this->delimiter,
			$this->enclosure,
			$this->escape,
			$options
		);
	}
}
