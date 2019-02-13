<?php
error_reporting(-1);
ini_set('implicit_flush', 0);
require(__DIR__.'/../vendor/autoload.php');
use wrossmann\json_stream\JsonStream;

function test($obj) {
	$handle = fopen('php://memory', 'rwb');
	$js = new JsonStream($handle);

	$js->encode($obj);
	rewind($handle);
	$js_out = stream_get_contents($handle);
	
	$stock_out = json_encode($obj);
	
	if( $js_out !== $stock_out ) {
		printf("FAIL:\n\t%s\n\t%s\n", $js_out, $stock_out);
	}

	return $js_out === $stock_out;
}

$tests = [
	0, 1, 2, true, false, null,
	"", "\0", "a", "abc", "klâwen",
	[], [0], [1], [0,1,2,3],
	[0,1,2,3,4=>4], [0,1,2,3,5=>5]
];
$testobject = new stdClass();
$testobject->foo = $tests;

$tests[] = $testobject;


$res = true;
foreach($tests as $test) {
	$res = test($test) && $res;
}

echo ($res ? "PASS" : "FAIL") . PHP_EOL;

exit( $res ? 0 : 1 );
