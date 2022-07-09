<?php 

use Ezeksoft\PHPWriteLog\{Log, Table, Row};

require '../src/helper.php';
require '../src/Table.php';
require '../src/Row.php';
require '../src/Log.php';


$log = new Log;
$log->permission = 0777;

$table = new Table;
$table->columns = ['INDEX', 'DATE', 'IP', 'USER-AGENT', 'HOST'];

$row = new Row;
$row->content = ['%index', '2022-07-09 00:00:00', '192.168.0.1', 'Firefox/100.0.1.1', 'https://stackoverflow.com'];
$table->rows[] = $row;

$row = new Row;
$row->content = ['%index', '2022-07-09 00:00:00', '192.168.0.112', 'Chrome/100.0.1.155', 'https://google.com'];
$table->rows[] = $row;

$row = new Row;
$row->content = ['%index', '2022-07-09', '192.168.0.112', 'Opera/1', 'https://reddit.com'];
$table->rows[] = $row;

$row = new Row;
$row->content = ['%index', '%date', '%ip', '%user_agent', '%host'];
$table->rows[] = $row;

$log->write('tabelas/b/acessos.txt', $table);