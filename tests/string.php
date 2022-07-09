<?php 

use Ezeksoft\PHPWriteLog\{Log, Table, Row};

require '../src/helper.php';
require '../src/Table.php';
require '../src/Row.php';
require '../src/Log.php';

$log = new Log;
$log->permission = 0777; // opcional
$log->write('datas/hoje.txt', "Data de hoje: %date");