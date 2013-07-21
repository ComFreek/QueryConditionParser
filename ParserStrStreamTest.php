<?php

require('ParserStrStream.php');

class ParserStrStreamTest extends PHPUnit_Framework_TestCase {
	public function testMultiByteLength() {
		$str = new ParserStrStream('â');
		$this->assertEquals(1, $str->len());
	}
	
	public function testNextPrevBoundsHandling() {
		$str = new ParserStrStream('âêîôû');
		$this->assertEquals(false, $str->prev());
		for ($i=0; $i<$str->len() - 1; $i++) {
			$this->assertEquals(true, $str->next() !== false);
		}
		$this->assertEquals(false, $str->next());
	}
}