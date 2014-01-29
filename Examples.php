<?php
include_once __DIR__ . '/Runtime.php';

$code = "echo 'Hello World!!!';"; # This PHP source code will be compiled and executed
$ret = PhpTags\Runtime::runSource( $code ); # $ret is array, because it may contain iRawOutput objects
$output = implode( $ret ); # just glue it
echo "$output\n"; # done,  $output variable has 'Hello World!!!'
# I know this example looks a silly :-)

# How about this?
$code = '
$info = array("coffee", "brown", "caffeine");
list($drink, $color, $power) = $info;
echo "$drink is $color and $power makes it special.";';
echo implode( PhpTags\Runtime::runSource($code) ) . "\n";  # coffee is brown and caffeine makes it special.

$code = '
$i=1;
while ( $i<=5 ) {
	echo "|$i|";
	$y=0;
	while ( $y<4 && $y<$i ) {
		$y++;
		if ( $y==3 ) { break 2; echo "hohoho"; }
		echo "($y)";
	}
	$i++;
}';
echo implode( PhpTags\Runtime::runSource($code) ) . "\n"; # |1|(1)|2|(1)(2)|3|(1)(2)

$code = '
$string = "April 15, 2003";
$pattern = "/(\w+) (\d+), (\d+)/i";
$replacement = \'${1}1,$3\';
echo preg_replace($pattern, $replacement, $string);';
echo implode( PhpTags\Runtime::runSource($code) ) . "\n"; # April1,2003

echo implode( PhpTags\Runtime::runSource('echo $bar = $foo = 1, $foo, $bar;') ) . "\n"; # 111
echo implode( PhpTags\Runtime::runSource('echo -(int)-5.5 + (int)(bool)"2";') ) . "\n"; # 6
echo implode( PhpTags\Runtime::runSource('$foo = NULL; echo is_null($foo) ? "true" : "false";') ) . "\n"; # true

# see more in phpunit folder
?>
