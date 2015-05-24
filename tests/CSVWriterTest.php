<?php
/**
 * CSVWriter Test
 *
 * @author	Jasmine
 * @link	https://github.com/jasbulilit/csv
 * @package	CSV Test
 */

use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \JasBulilit\CSV\CSVWriter
 */
class CSVWriterTest extends \PHPUnit_Framework_TestCase {

	static private $_filters	= array(
		'convert.iconv.utf-8/cp932'	=>	null,
		'convert.eol.lf/crlf'		=>	'EOL_LFToCRLFFilter'
	);

	public function setUp() {
		vfsStream::setup('dummy_dir');
	}

	/**
	 * @covers ::append
	 * @covers ::_getFilePointer
	 * @covers ::_initFilePointer
	 * @covers ::close
	 */
	public function testAppend() {
		$file_path = vfsStream::url('dummy_dir/dummy.csv');

		$writer = new \JasBulilit\CSV\CSVWriter($file_path);
		foreach (self::$_filters as $filter_name => $filter_class) {
			$writer->addFilter($filter_name, $filter_class);
		}

		$dummy_data = $this->_getDummyRows();
		foreach ($dummy_data as $row) {
			$writer->append($row);
		}
		$writer->close();

		$this->assertEquals(
			file_get_contents(dirname(__FILE__) . '/dat/dummy_csv.csv'),
			file_get_contents($file_path)
		);
	}

	/**
	 * @covers ::close
	 */
	public function testClose() {
		$file_path = vfsStream::url('dummy_dir/dummy.csv');

		$writer = new \JasBulilit\CSV\CSVWriter($file_path);
		$writer->close();
	}

	/**
	 * @covers ::_initFilePointer
	 */
	public function testFailureOnOpenFile() {
		// fopen時の警告発生を抑制
		$expected_warning = 'failed to open stream: "NeverOpenStream::stream_open" call failed';
		$this->_disabledPHPWarning($expected_warning);

		stream_wrapper_register('nos', 'NeverOpenStream');
		$file_path = 'nos://dummy';
		$writer = new \JasBulilit\CSV\CSVWriter($file_path);

		$is_catch_exception = false;
		$dummy_data = $this->_getDummyRows();
		try {
			$writer->append($dummy_data[0]);
		} catch(\RuntimeException $e) {
			$is_catch_exception = true;
			$this->assertEquals("Failed to open file: {$file_path}", $e->getMessage());
		}

		$this->_enabledPHPWarning();
		$this->assertTrue($is_catch_exception);
	}

	private function _getDummyRows() {
		return array(
			array('あいうえお', 'かきく"けこ', 'さしす,せそ', "たちつてと\r\nなにぬねの"),
			array('はひふへほ', 'まみむ\めも', 'や""ゆ""よ', 'ら,",り","るれろ', "わ\r\nお\r\nん")
		);
	}

	/**
	 * 予想されるE_WARNINGを無効化
	 *
	 * @param string $expected_warning
	 * @return void
	 */
	private function _disabledPHPWarning($expected_warning) {
		$error_handler = function($errno, $errstr, $errfile, $errline) use ($expected_warning) {
			if ($errno == E_WARNING
				&& strpos($errstr, $expected_warning) !== false) {
				return true;
			}
		};
		set_error_handler($error_handler, E_WARNING);
	}

	/**
	 * E_WARNING有効化(エラーハンドラーを復元)
	 */
	private function _enabledPHPWarning() {
		restore_error_handler();
	}
}

/**
 * 変換フィルタ ベースクラス
 */
abstract class ConvertFilter extends \php_user_filter {
	public function filter($in, $out, &$consumed, $closing) {
		while ($bucket = stream_bucket_make_writeable($in)) {
			$bucket->data	= $this->_convert($bucket->data);
			$consumed		+= $bucket->datalen;
			stream_bucket_append($out, $bucket);
		}
		return PSFS_PASS_ON;
	}

	abstract protected function _convert($data);

}

/**
 * 改行コードフィルタ（LF->CRLF）
 */
class EOL_LFToCRLFFilter extends ConvertFilter {
	protected function _convert($data) {
		return rtrim($data, "\n") . "\r\n";
	}
}

/**
 * 改行コードフィルタ（CRLF->LF）
 */
class EOL_CRLFToLFFilter extends ConvertFilter {
	protected function _convert($data) {
		return rtrim($data, "\r\n") . "\n";
	}
}

/**
 * 常にfopenに失敗するstream
 */
class NeverOpenStream {
	public function stream_open($path , $mode , $options , $opened_path) {
		return false;
	}
}
