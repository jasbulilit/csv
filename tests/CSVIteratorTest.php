<?php
/**
 * CSVIterator Test
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/csv
 * @package	CSV Test
 */
use JasBulilit\CSV\CSVReader;

/**
 * @coversDefaultClass \JasBulilit\CSV\CSVIterator
 */
class CSVIteratorTest extends \PHPUnit_Framework_TestCase {

	private static $_dummy_csv = array(
		'00001,"あいうえお","abcdefg",1',
		'01101,"かきくけこ","hijklmn",2',
		'01102,"さしすせそ","opqrstu",3'
	);

	private static $_dummy_tsv = array(
		"90001	'あいうえお'	'abcdefg'	1",
		"91101	'かきくけこ'	'hijklmn'	2",
		"91102	'さしすせそ'	'opqrstu'	3"
	);

	private $_dummy_csv_uri;
	private $_reader;

	protected function setUp() {
		$this->_dummy_csv_uri = getDataURI(self::$_dummy_csv);
		$this->_reader = new CSVReader($this->_dummy_csv_uri);
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstructorWithTSV() {
		$delimiter = '	';
		$enclosure = "'";
		$dummy_tsv_uri = getDataURI(self::$_dummy_tsv);

		$reader = new CSVReader($dummy_tsv_uri);
		$reader->setDelimiter($delimiter);
		$reader->setEnclosure($enclosure);

		$iterator = new \JasBulilit\CSV\CSVIterator($dummy_tsv_uri, $reader);
		foreach (self::$_dummy_tsv as $row) {
			$this->assertEquals(toCSV($row, $delimiter, $enclosure), $iterator->current());
			$iterator->next();
		}
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstructorWithContext() {
		$this->_reader->setContext(stream_context_create());
		$iterator = new \JasBulilit\CSV\CSVIterator($this->_dummy_csv_uri, $this->_reader);
		foreach (self::$_dummy_csv as $row) {
			$this->assertEquals(toCSV($row), $iterator->current());
			$iterator->next();
		}
	}

	/**
	 * @covers ::current
	 */
	public function testCurrent() {
		$iterator = new \JasBulilit\CSV\CSVIterator($this->_dummy_csv_uri, $this->_reader);
		$this->assertEquals(toCSV(self::$_dummy_csv[0]), $iterator->current());
	}

	/**
	 * @covers ::current
	 * @covers ::_isEmpty
	 */
	public function testCurrentWithEmptyRow() {
		$empty_uri = getDataURI(array());
		$iterator = new \JasBulilit\CSV\CSVIterator($empty_uri, new CSVReader($empty_uri));
		$this->assertEquals(array(), $iterator->current());
	}

	/**
	 * @covers ::next
	 */
	public function testNext() {
		$iterator = new \JasBulilit\CSV\CSVIterator($this->_dummy_csv_uri, $this->_reader);
		foreach (self::$_dummy_csv as $row) {
			$this->assertEquals(toCSV($row), $iterator->current());
			$iterator->next();
		}
	}

	/**
	 * @covers ::key
	 */
	public function testKey() {
		$iterator = new \JasBulilit\CSV\CSVIterator($this->_dummy_csv_uri, $this->_reader);
		foreach (self::$_dummy_csv as $key => $row) {
			$this->assertEquals($key, $iterator->key());
			$iterator->next();
		}
	}

	/**
	 * @covers ::valid
	 */
	public function testValid() {
		$iterator = new \JasBulilit\CSV\CSVIterator($this->_dummy_csv_uri, $this->_reader);
		foreach (self::$_dummy_csv as $row) {
			$this->assertTrue($iterator->valid());
			$iterator->next();
		}
//		$this->assertFalse($iterator->valid());
	}

	/**
	 * @covers ::rewind
	 */
	public function testRewind() {
		$iterator = new \JasBulilit\CSV\CSVIterator($this->_dummy_csv_uri, $this->_reader);

		$csv = null;
		foreach (self::$_dummy_csv as $row) {
			$csv = $iterator->current();
			$iterator->next();
		}
		$this->assertEquals(toCSV(self::$_dummy_csv[2]), $csv, 'brefore rewind');
		$iterator->rewind();
		$this->assertEquals(toCSV(self::$_dummy_csv[0]), $iterator->current(), 'after rewind');
	}
}
