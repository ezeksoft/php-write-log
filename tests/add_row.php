<?php 

use Ezeksoft\PHPWriteLog\{Log, Table, Row};

require '../src/Helper.php';
require '../src/Table.php';
require '../src/Row.php';
require '../src/Log.php';


$log = new Log;
$log->permission = 0777;

$table = new Table;
$table->load('tabelas/acessos_add_linha.txt');

$row = new Row;
$row->content = ['%index', '%date', '127.0.0.1', 'Curl', 'localhost'];
$table->rows[] = $row;

$log->write($table->file->path, $table);