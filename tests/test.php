<?php 

require '../vendor/autoload.php';

use Ezeksoft\PHPWriteLog\{Log, Table, Row};

$log = new Log;
$log->write('datas/autoload_hoje.txt', "%index - Data de hoje: %date");