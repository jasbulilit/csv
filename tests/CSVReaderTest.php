<?php
/**
 * CSVReader Test
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/csv
 * @package	CSV Test
 */

/**
 * @coversDefaultClass \JasBulilit\CSV\CSVReader
 */
class CSVReaderTest extends \PHPUnit_Framework_TestCase {

	private static $_dummy_csv = array(
		'00001,"あいうえお","abcdefg",1',
		'01101,"かきくけこ","hijklmn",2',
		'01102,"さしすせそ","opqrstu",3'
	);

	/**
	 * @covers ::__construct
	 */
	public function testConstructor() {
		$csv_uri = getDataURI(self::$_dummy_csv);
		$reader = new \JasBulilit\CSV\CSVReader($csv_uri);
		$this->assertEquals($csv_uri, $reader->getCSVPath());
		$this->assertNull($reader->getContext());
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstructorWithContext() {
		$csv_uri = getDataURI(self::$_dummy_csv);
		$context = stream_context_create();
		$reader = new \JasBulilit\CSV\CSVReader($csv_uri, $context);
		$this->assertEquals($context, $reader->getContext());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorWithInvalidContext() {
		$csv_uri = getDataURI(self::$_dummy_csv);
		$context = array(
			'Foo'	=> 'bar'
		);
		$reader = new \JasBulilit\CSV\CSVReader($csv_uri, $context);
	}

	/**
	 * @covers ::setIteratorClass
	 */
	public function testSetCSVIterator() {
		$csv_uri = getDataURI(self::$_dummy_csv);
		$iterator_class = 'DummyCSVIterator';
		$reader = new \JasBulilit\CSV\CSVReader($csv_uri);
		$reader->setIteratorClass($iterator_class);

		$this->assertTrue(is_a($reader->getIterator(), $iterator_class));
	}

	/**
	 * @covers ::__construct
	 * @covers ::setIteratorClass
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage $class_name must extends
	 */
	public function testSetInvalidIterator() {
		$csv_uri = getDataURI(self::$_dummy_csv);
		$iterator_class = 'ArrayIterator';
		$reader = new \JasBulilit\CSV\CSVReader($csv_uri);
		$reader->setIteratorClass($iterator_class);
	}

	/**
	 * @covers ::setIteratorClass
	 * @covers ::getIteratorClass
	 */
	public function testSetAndGetIterator() {
		$csv_uri = getDataURI(self::$_dummy_csv);
		$reader = new \JasBulilit\CSV\CSVReader($csv_uri);
		$this->assertEquals(\JasBulilit\CSV\CSVReader::DEFAULT_ITERATOR_CLASS, $reader->getIteratorClass());

		$reader->setIteratorClass('DummyCSVIterator');
		$this->assertEquals('DummyCSVIterator', $reader->getIteratorClass());
	}

	/**
	 * @covers ::getIterator
	 */
	public function testGetIterator() {
		$csv_uri = getDataURI(self::$_dummy_csv);
		$reader = new \JasBulilit\CSV\CSVReader($csv_uri);

		$this->assertTrue(is_a($reader->getIterator(), '\JasBulilit\CSV\CSVIterator'));
	}

	/**
	 * IteratorAggregate
	 */
	public function testIteratorAggregate() {
		$csv_uri = getDataURI(self::$_dummy_csv);
		$reader = new \JasBulilit\CSV\CSVReader($csv_uri);

		foreach ($reader as $k => $row) {
			$this->assertEquals(toCSV(self::$_dummy_csv[$k]), $row);
		}
	}
}

class DummyCSVIterator extends \JasBulilit\CSV\CSVIterator {}
