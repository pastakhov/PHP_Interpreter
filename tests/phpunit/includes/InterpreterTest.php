<?php

namespace Foxway;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-28 at 05:28:38.
 */
class InterpreterTest extends \PHPUnit_Framework_TestCase {

	public function testRun_echo_apostrophe() {
		$this->assertEquals(
				Interpreter::run('echo "Hello!";'),
				'Hello!'
				);
	}

	public function testRun_echo_quotes() {
		$this->assertEquals(
				Interpreter::run("echo 'Hello!';"),
				'Hello!'
				);
	}

	public function testRun_echo_union_1() {
		$this->assertEquals(
				Interpreter::run('echo "String" . "Union";'),
				'StringUnion'
				);
	}
	public function testRun_echo_union_2() {
		$this->assertEquals(
				Interpreter::run('echo \'This \' . \'string \' . \'was \' . \'made \' . \'with concatenation.\' . "\n";'),
				"This string was made with concatenation.\n"
				);
	}

	public function testRun_echo_parameters_1() {
		$this->assertEquals(
				Interpreter::run('echo "Parameter1","Parameter2" , "Parameter3";'),
				'Parameter1Parameter2Parameter3'
				);
	}
	public function testRun_echo_parameters_2() {
		$this->assertEquals(
				Interpreter::run('echo \'This \', \'string \', \'was \', \'made \', \'with multiple parameters.\';'),
				'This string was made with multiple parameters.'
				);
	}

	public function testRun_echo_multiline_1() {
		$this->assertEquals(
				Interpreter::run('echo "This spans
multiple lines. The newlines will be
output as well";'),
				"This spans\nmultiple lines. The newlines will be\noutput as well"
				);
	}
	public function testRun_echo_multiline_2() {
		$this->assertEquals(
				Interpreter::run('echo "Again: This spans\nmultiple lines. The newlines will be\noutput as well.";'),
				"Again: This spans\nmultiple lines. The newlines will be\noutput as well."
				);
	}

	public function testRun_echo_variables_1() {
		$this->assertEquals(
				Interpreter::run('
$foo = "foobar";
$bar = "barbaz";
echo "foo is $foo"; // foo is foobar'),
				'foo is foobar'
				);
	}
	public function testRun_echo_variables_2() {
		$this->assertEquals(
				Interpreter::run('echo "foo is {$foo}";'),
				'foo is foobar'
				);
	}
	public function testRun_echo_variables_3() {
		$this->assertEquals(
				Interpreter::run('echo "foo is {$foo}.";'),
				'foo is foobar.'
				);
	}
	public function testRun_echo_variables_4() {
		$this->assertEquals(
				Interpreter::run('echo "foo is $foo\n\n";'),
				"foo is foobar\n\n"
				);
	}
	public function testRun_echo_variables_5() {
		$this->assertEquals(
				Interpreter::run('echo \'foo is $foo\';'),
				'foo is $foo'
				);
	}
	public function testRun_echo_variables_6() {
		$this->assertEquals(
				Interpreter::run('echo $foo,$bar;'),
				'foobarbarbaz'
				);
	}
	public function testRun_echo_variables_7() {
		$this->assertEquals(
				Interpreter::run('echo "$foo$bar";'),
				'foobarbarbaz'
				);
	}
	public function testRun_echo_variables_8() {
		$this->assertEquals(
				Interpreter::run('echo "s{$foo}l{$bar}e";'),
				'sfoobarlbarbaze'
				);
	}
	public function testRun_echo_variables_9() {
		$this->assertEquals(
				Interpreter::run('echo "s{$foo}l$bar";'),
				'sfoobarlbarbaz'
				);
	}
	public function testRun_echo_variables_10() {
		$this->assertEquals(
				Interpreter::run('echo "start" . $foo . "end";'),
				'startfoobarend'
				);
	}
	public function testRun_echo_variables_11() {
		$this->assertEquals(
				Interpreter::run('echo "This ", \'string \', "was $foo ", \'with multiple parameters.\';'),
				'This string was foobar with multiple parameters.'
				);
	}

	public function testRun_echo_escaping_1() {
		$this->assertEquals(
				Interpreter::run('echo \'s\\\\\\\'e\';'),	// echo 's\\\'e';
				's\\\'e'									// s\'e
				);
	}
	public function testRun_echo_escaping_2() {
		$this->assertEquals(
				Interpreter::run('echo "s\\\\\\"e";'),	// echo "s\\\"e";
				's\\"e'									// s\"e
				);
	}

	public function testRun_echo_digit() {
		$this->assertEquals(
				Interpreter::run('echo 5;'),
				'5'
				);
	}

	public function testRun_echo_math_1() {
		$this->assertEquals(
				Interpreter::run('echo \'5 + 5 * 10 = \', 5 + 5 * 10;'),
				'5 + 5 * 10 = 55'
				);
	}
	public function testRun_echo_math_2() {
		$this->assertEquals(
				Interpreter::run('echo -5 + 5 + 10 + 20 - 50 - 5;'),
				'-25'
				);
	}
	public function testRun_echo_math_3() {
		$this->assertEquals(
				Interpreter::run('echo 5 + 5 / 10 + 50/100;'),
				'6'
				);
	}
	public function testRun_echo_math_4() {
		$this->assertEquals(
				Interpreter::run('echo 10 * 10 + "20" * \'20\' - 30 * 30 + 40 / 9;'),
				'-395.55555555556'
				);
	}

	public function testRun_echo_math_params() {
		$this->assertEquals(
				Interpreter::run('echo \'10 + 5 * 5 = \', 10 + 5 * 5, "\n\n";'),
				"10 + 5 * 5 = 35\n\n"
				);
	}

	public function testRun_echo_math_variables() {
		$this->assertEquals(
				Interpreter::run('
$foo = 100;
$bar = \'5\';
echo "\$foo * \$bar = $foo * $bar = ", $foo * $bar, "\n\n";'),
				"\$foo * \$bar = 100 * 5 = 500\n\n"
				);
		$this->assertEquals(
				Interpreter::run('echo "\$foo / \$bar = $foo / $bar = ", $foo / $bar, "\n\n";'),
				"\$foo / \$bar = 100 / 5 = 20\n\n"
				);
		$this->assertEquals(
				Interpreter::run('echo "-\$foo / -\$bar = {-$foo} / {-$bar} = ", -$foo / -$bar, "\n\n";'),
				"-\$foo / -\$bar = {-100} / {-5} = 20\n\n"
				);
	}

	public function testRun_echo_math_union_1() {
		$this->assertEquals(
				Interpreter::run('echo 10 + 5 . 5;'),
				'155'
				);
	}
	public function testRun_echo_math_union_2() {
		$this->assertEquals(
				Interpreter::run('echo 10 + 5 . 5  * 9;'),
				'1545'
				);
	}
	public function testRun_echo_math_union_3() {
		$this->assertEquals(
				Interpreter::run('echo 10 + 5 . 5  * 9 . 4 - 5 . 8;'),
				'154498'
				);
	}

	public function testRun_echo_math_Modulus_1() {
		$this->assertEquals(
				Interpreter::run('echo 123 % 21;'),
				'18'
				);
	}
	public function testRun_echo_math_Modulus_2() {
		$this->assertEquals(
				Interpreter::run('echo 123 % 21 + 74 % -5;'),
				'22'
				);
	}
	public function testRun_echo_math_Modulus_3() {
		$this->assertEquals(
				Interpreter::run('echo 123 % 21 + 74.5 % -5 * 4 / 2 . 5 + -1;'),
				'264'
				);
	}

	public function testRun_echo_math_BitwiseAnd_1() {
		$this->assertEquals(
				Interpreter::run('echo 123 & 21;'),
				'17'
				);
	}
	public function testRun_echo_math_BitwiseAnd_2() {
		$this->assertEquals(
				Interpreter::run('echo 123 & 21 + 94 & 54;'),
				'50'
				);
	}
	public function testRun_echo_math_BitwiseAnd_3() {
		$this->assertEquals(
				Interpreter::run('echo 123 & 21 + 94 & -54;'),
				'66'
				);
	}

	public function testRun_echo_math_BitwiseOr_1() {
		$this->assertEquals(
				Interpreter::run('echo 123 | 21;'),
				'127'
				);
	}
	public function testRun_echo_math_BitwiseOr_2() {
		$this->assertEquals(
				Interpreter::run('echo 123 | -21 / 3;'),
				'-5'
				);
	}

	public function testRun_echo_math_BitwiseXor() {
		$this->assertEquals(
				Interpreter::run('echo -123 ^ 21;'),
				'-112'
				);
	}

	public function testRun_echo_math_LeftShift_1() {
		$this->assertEquals(
				Interpreter::run('echo 123 << 2;'),
				'492'
				);
	}
	public function testRun_echo_math_LeftShift_2() {
		$this->assertEquals(
				Interpreter::run('echo 123 << 2 + 4;'),
				'7872'
				);
	}
	public function testRun_echo_math_LeftShift_3() {
		$this->assertEquals(
				Interpreter::run('echo 123 << 2 + 4 << 2;'),
				'31488'
				);
	}
	public function testRun_echo_math_LeftShift_4() {
		$this->assertEquals(
				Interpreter::run('echo 123 << 2 + 4 << 2 * 8;'),
				'515899392'
				);
	}

	public function testRun_echo_math_RightShift_1() {
		$this->assertEquals(
				Interpreter::run('echo 123 >> 2;'),
				'30'
				);
	}
	public function testRun_echo_math_RightShift_2() {
		$this->assertEquals(
				Interpreter::run('echo 123 >> 2 + 3;'),
				'3'
				);
	}

}
