### Exemplo: texto simples

```php
<?php 

use Ezeksoft\PHPWriteLog\{Log, Table, Row};

require '../src/helper.php';
require '../src/Table.php';
require '../src/Row.php';
require '../src/Log.php';

$log = new Log;
$log->permission = 0777; // opcional
$log->write('datas/hoje.txt', "Data de hoje: %date");
```
Resultado em .txt
```
Data de hoje: 2022-07-09 06:08:24
```

### Exemplo: array/objeto

```php
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
```
Resultado em pagamento/gateway1/compra_aprovada.txt
```
[{"index":0,"status":"success","transaction_id":"123456780001"}]
[{"index":1,"status":"success","transaction_id":"123456780002"}]
```

Resultado em pagamento/gateway1/compra_reembolsada.txt
```
[{"index":0,"status":"refund","transaction_id":"123456780001"},{"index":0,"status":"refund","transaction_id":"123456780002"}]
```

### Exemplo: texto tabela

```php
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

$log->write('tabelas/acessos.txt', $table);
```


Resultado em tabelas/acessos.txt
```
|INDEX|       DATE        |     IP      |    USER-AGENT    |          HOST           
|-----|-------------------|-------------|------------------|-------------------------
|  1  |2022-07-09 00:00:00| 192.168.0.1 |Firefox/100.0.1.1 |https://stackoverflow.com
|  2  |2022-07-09 00:00:00|192.168.0.112|Chrome/100.0.1.155|   https://google.com    
|  3  |    2022-07-09     |192.168.0.112|     Opera/1      |   https://reddit.com    
|  4  |2022-07-09 05:44:38|  127.0.0.1  |    Chrome/...    |       localhost          

```


### Exemplo: adicionar linha de tabela em arquivo existente

```php
<?php 

use Ezeksoft\PHPWriteLog\{Log, Table, Row};

require '../src/helper.php';
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
```

Resultado em tabelas/acessos_add_linha.txt
```
|INDEX|       DATE        |     IP      |    USER-AGENT    |          HOST           |
|-----|-------------------|-------------|------------------|-------------------------|
|  1  |2022-07-09 00:00:00| 192.168.0.1 |Firefox/100.0.1.1 |https://stackoverflow.com|
|  2  |2022-07-09 00:00:00|192.168.0.112|Chrome/100.0.1.155|   https://google.com    |
|  3  |    2022-07-09     |192.168.0.112|     Opera/1      |   https://reddit.com    |
|  4  |2022-07-09 05:44:38|    NULL     |       NULL       |          NULL           |
|  5  |2022-07-13 05:49:58|  127.0.0.1  |       Curl       |        localhost        |
|  6  |2022-07-13 05:50:02|  127.0.0.1  |       Curl       |        localhost        |
|  7  |2022-07-13 05:50:09|  127.0.0.1  |       Curl       |        localhost        |
|  8  |2022-07-13 05:50:15|  127.0.0.1  |       Curl       |        localhost        |

```