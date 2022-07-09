<?php 

use Ezeksoft\PHPWriteLog\{Log, Table, Row};

require '../src/helper.php';
require '../src/Table.php';
require '../src/Row.php';
require '../src/Log.php';

$log = new Log;
$log->write('pagamento/gateway1/compra_aprovada.txt', [
	[
		'index' => $log->index,
		'status' => 'success',
		'transaction_id' => '123456780001'
	]
], [
	'begin' => ['string' => "\n", 'skip_first_line' => true],
]);

$log->write('pagamento/gateway1/compra_aprovada.txt', [
	[
		'index' => $log->index,
		'status' => 'success',
		'transaction_id' => '123456780002'
	]
], [
	'begin' => ['string' => "\n", 'skip_first_line' => true],
]);

$log = new Log;
$log->write('pagamento/gateway1/compra_reembolsada.txt', [
	[
		'index' => $log->index,
		'status' => 'refund',
		'transaction_id' => '123456780001'
	],
	[
		'index' => $log->index,
		'status' => 'refund',
		'transaction_id' => '123456780002'
	]
], [
	'end' => ['string' => "\n"]
]);
