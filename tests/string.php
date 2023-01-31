<?php 

use Ezeksoft\PHPWriteLog\{Log, Table, Row};

require '../src/Helper.php';
require '../src/Table.php';
require '../src/Row.php';
require '../src/Log.php';

$log = new Log;
$log->permission = 0777; // opcional
$log->write('datas/hoje.txt', "%index - Data de hoje: %date");
$log->write('datas/hoje.txt', "%index - Caminho do arquivo: %filepath");
$log->write('datas/hoje.txt', "%index - Requisição com parametros %request.x_www_form_urlencoded");
$log->write('datas/hoje.txt', "%index - Requisição raw %request.raw");
$log->write('datas/hoje.txt', "%index - User-Agent %user_agent");
$log->write('datas/hoje.txt', "%index - Hash: %uniqid");
