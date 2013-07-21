<?php

require('Parser.php');

class ParserTest extends PHPUnit_Framework_TestCase {
	public function testParser() {
		$str = <<< EOF
attr1=val1+attr2='val2\''
EOF;
		
		$expected = array(
			array(
				'attr' => 'attr1',
				'op' => '=',
				'val' => 'val1'
			),
			array(
				'attr' => 'attr2',
				'op' => '=',
				'val' => 'val2\''
			)
		);

		$res = Parser::parse($str);
		$this->assertEquals(count($expected), count($res));
		for ($i=0; $i < count($expected); $i++) {
			$this->assertEquals(true, empty(array_diff($res[$i], $expected[$i])));
		}
	}
}