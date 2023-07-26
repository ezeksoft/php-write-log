<?php 

 /* --------------------------------------------------------------
 |
 |  Sistema de logs 
 |  ---------------
 |  
 |  Para voce nao ficar na escuridao sobre o que
 |  acontece com seu sistema
 |
 |  @author Ezeksoft
 |
 |  @version 1.0
 |  @created_at 2022-07-09
 |  @updated_at 2022-07-09 
 |
 |  --------------------------------------------------------------
*/


namespace Ezeksoft\PHPWriteLog;

use Ezeksoft\PHPWriteLog\
{
	Table,				// entidade para criar a estrutura de uma tabela
	Row					// entidade para criar a estrutura de uma linha de tabela
};

use function Ezeksoft\PHPWriteLog\Helper\
{ 
	array_to_object,	// convert array em objeto
	permission			// pega permissao do diretorio
};

class Log
{
	# valores padrao
	const BEGIN_STRING = "\n"; // string no comeco do texto
	const SKIP_FIRST_LINE = true; // pula string no comeco do texto se for primeira linha

	public $allow_vars = true; // habilita trocar variaveis na string por valores correspondentes
	public $index = 0; // indice de execucao do log


	/**
	 * @access public
	 * @method write
	 * @param string $fullpath				Caminho do arquivo
	 * @param mixed $message				Texto que sera escrito
	 * @param array $config 				Definicoes de como sera escrito
	 * @return string						Mensagem que foi escrita
	 * 
	 * Se a pasta nao existir ou o arquivo, serao criados automaticamente
	 */

	public function write(string $fullpath, mixed $message=null, array $config=[]) : ?string
	{
		if (gettype($message) == 'object' || gettype($message) == 'array')
		{
			# nome da classe que veio no argumento
			$message_class = get_class((object) $message);

			$aux = new \ReflectionClass(__CLASS__); // pega informacoes sobre a classe Log
			$namespace = $aux->getNamespaceName(); // pega o namespace

			# se for uma tabela 
			if ($message_class == "$namespace\Table")
			{
				$table = $message; // clona objeto
				$columns = $table->columns ?? []; // colunas
				$rows = array_to_object($table->rows ?? []); // linhas da coluna
				$message = ""; // inicaliza espaco para a mensagem

				$max_chars = []; // inicializa espaco para as colunas
				$aux = []; // espaco para uma lista de colunas com as linhas
				$aux[] = (object) ["content" => $columns];  // cria um array com as colunas
				foreach ($rows as $row) $aux[] = $row; // injeta linhas no array que contem as colunas
				
				$new_rows = []; // inicializa espaco para na lista de linhas formatada

				# pega as celulas que tem maior quantidade de caracteres por coluna
				$n = 0;
				foreach ($aux as $row)
				{
					$row_ = $row->content ?? []; // conteudo de texto da linha

					$i = 0;
					$cells = []; // inicializa espaco para as celulas da linha
					foreach ($row_ as $row_column) // itera as celulas da linha
					{
						if ($this->allow_vars) $row_column = $this->replaces_vars((String) $row_column, compact('n'));
						$cells[] = $row_column;

						// se a celular que tem mais caracteres dessa coluna for menor que 
						// a quantidade de caracteres dessa coluna da linha atual
						if (strlen($max_chars[$i] ?? '') < strlen($row_column))
							$max_chars[$i] = $row_column; // atualizar
						$i++;
					}

					$row->content = $cells; // atualizar conteudo da linha
					if ($n > 0) $new_rows[] = $row; // pula a primeira linha pois eh o header
					$n++;
				}

				$rows = $new_rows; // atualiza linhas

				$msg = "";
				$i = 0; 
				foreach ($columns as $column) // itera colunas
				{
					// quantos espacos precisam ser colocados na celula da linha atual
					// para que ela fique com a mesma largura da celula do header
					$spaces_qty = strlen($max_chars[$i] ?? '') - strlen($column);

					$rest = $spaces_qty % 2; // verifica se a quantidade de espacos eh impar, se for, sobra
											 // e essa quantidade de espacos deve ser adicionada 
											 // na esquerda ou direita
					
					$space_left = 0; // quantidade de espacos na esquerda
					$space_right = 0; // quantidade de espacos na direita
					
					$spaces_left_str = ''; // espacos da esquerda
					$spaces_right_str = ''; // espacos da direita

					$floor = floor($spaces_qty / 2); // espacos pela metade arredodandos para baixo
					
					// se nao sobrou espacos
					if ($rest == 0) $space_left = $space_right = $floor; // espacos da esquerda e direita

					// se sobrou espacos
					else 
					{
						$space_left = $floor; // espacos do lado esquerdo
						$space_right = $floor + $rest; // espacos do lado direito + alocar espacos extra a direita
					}

					// produz espacos
					for ($j = 0; $j < $space_left; $j++) $spaces_left_str .= " ";
					for ($j = 0; $j < $space_right; $j++) $spaces_right_str .= " ";

					// monta celula
					$msg .= "|$spaces_left_str$column$spaces_right_str";
					$i++;
				}

				if (!empty($columns)) $msg .= "|";

				if ($table->border_top)
				{
					// linha no header
					$dashes = ""; // inicializa espaco para os caracteres
					foreach ($max_chars as $char) // itera o texto das maiores celulas
						for ($i = 0; $i < strlen($char) - 1; $i++) // itera os caracteres de cada celula
							$dashes .= "-"; // produz tracos
					for ($i = 0; $i < sizeof($max_chars) * 2; $i++) // itera a quantidade de celulas
							$dashes .= "-"; // produz tracos
					$message .= "$dashes-\n"; // concatena tracos com mensagem
				}

				$message .= $msg;

				$r = 0;
				foreach ($rows as $row) // itera linhas
				{ 
					$message .= "\n"; // quebra a linha em cada loop
					$row = $row->content ?? []; // pega conteudo de texto da linha
					$msg = ""; // inicializa espaco para uma mensagem auxiliar

					$i = 0; 
					foreach ($row as $row_column) // itera as celulas da linha
					{
						// quantos espacos precisam ser colocados na celula da linha atual
						// para que ela fique com a mesma largura da celula do header
						$spaces_qty = strlen($max_chars[$i] ?? '') - strlen($row_column);

						$rest = $spaces_qty % 2; // verifica se a quantidade de espacos eh impar, se for, sobra
												 // e essa quantidade de espacos deve ser adicionada 
												 // na esquerda ou direita
						
						$space_left = 0; // quantidade de espacos na esquerda
						$space_right = 0; // quantidade de espacos na direita
						
						$spaces_left_str = ''; // espacos da esquerda
						$spaces_right_str = ''; // espacos da direita

						$floor = floor($spaces_qty / 2); // espacos pela metade arredodandos para baixo
						
						// se nao sobrou espacos
						if ($rest == 0) $space_left = $space_right = $floor; // espacos da esquerda e direita

						// se sobrou espacos
						else 
						{
							$space_left = $floor; // espacos do lado esquerdo
							$space_right = $floor + $rest; // espacos do lado direito + alocar espacos extra a direita
						}

						// produz espacos
						for ($j = 0; $j < $space_left; $j++) $spaces_left_str .= " ";
						for ($j = 0; $j < $space_right; $j++) $spaces_right_str .= " ";

						// monta celula
						$msg .= "|$spaces_left_str$row_column$spaces_right_str";
						$i++;
					}

					// se for a primeira linha
					// e se o separador entre o header e as linhas for do modelo 2
					if ($r == 0) 
					{
						$str = "";
						// corta a mensagem auxiliar em um array de caracteres
						// e substitui tudo que nao for "|" por "-"
						foreach (str_split($msg) as $char) $str .= $char == '|' ? "|" : "-";
						$msg = $str . "|\n" . $msg; // contactena separador com a primeira linha
					}

					$message .= "$msg|"; // adiciona nova linha na tabela com a mensagem auxiliar formatada

					$r++; // contador de linhas
				}


				if ($table->border_bottom)
				{
					// linha do footer
					$dashes = ""; // inicializa espaco para os caracteres
					foreach ($max_chars as $char) // itera o texto das maiores celulas
						for ($i = 0; $i < strlen($char) - 1; $i++) // itera os caracteres de cada celula
							$dashes .= "-"; // produz tracos
					for ($i = 0; $i < sizeof($max_chars) * 2; $i++) // itera a quantidade de celulas
							$dashes .= "-"; // produz tracos
					$message .= "\n$dashes-"; // concatena tracos com mensagem
				}
			}
		}

		# Configuracoes
		$config = array_to_object($config);

		# Converte a mensagem para sempre ser uma string
		if (gettype($message) == 'array' || gettype($message) == 'object')
			$message = json_encode($message);


		# Se a mensagem nao existe, usar dados da requisicao por padrao
		if ($message === true)
		{
			if (!empty($_REQUEST)) $message = json_encode($_REQUEST); // caso seja x-www-form-urlencoded converte o array em json
			if (!empty($input = file_get_contents('php://input'))) $message = $input; // caso seja raw, use a string
		}

		# Criar a pasta do arquivo se nao existir
		# ---------------------------------------
		$aux = explode(DIRECTORY_SEPARATOR, $fullpath); // corta caminho por barra
		$path = ""; // espaco para o diretorio
		$i = 0; // posicao do diretorio na string

		// separa diretorio e arquivo na string
		// intera caminho por DIRECTORY_SEPARATOR
		foreach ($aux as $dir) 
		{
			// se o item iterado nao for o ultimo, entao eh um diretorio
			if ($i < sizeof($aux) - 1) $path .= "$dir/";

			$i++; // contabiliza itens
		}

		// cria pasta recursivamente
		if (!is_dir($path)) @mkdir($path, $this->permission ?? permission(__DIR__), true);
		# ---------------------------------------

		# Configuracoes de escrita
		$c_begin = $config->begin->string ?? self::BEGIN_STRING; // string no inicio da linha
		$c_end = $config->end->string ?? ''; // string no fim da linha 
		$skip_first_line = $config->begin->skip_first_line ?? self::SKIP_FIRST_LINE;	// pular insercao de string no inico da 
																						// linha se for primeira linha do arquivo

		# Inicializa o incio e fim da linha vazios
		$begin = '';
		$end = '';
		
		# Inicializa o espaco onde diz se estamos na primeira ou ultima linha
		$is_first_line = null;
		$is_last_line = null;

		# Ler arquivo caso exista
		$file = @file($fullpath);
		if (!file_exists($fullpath))
		{
			$h = fopen($fullpath, 'w');
			fclose($h);
		}

		# Se nao existe nada no arquivo, estamos primeira linha
		if (empty($file)) $is_first_line = true;

		# Seta os espacos inicio e fim da linha
		$begin = $c_begin;
		$end = $c_end;

		# Se estamos na primeira linha e a configuracao diz para omitir a string de inicio de linha
		if ($is_first_line === true && $skip_first_line === true) $begin = '';

		if ($this->allow_vars) $message = $this->replaces_vars((String) $message, [
			"fullpath" => $fullpath, 
			"n" => $this->index
		]);

		# Monta mensagem que sera escrita
		$text = "$begin$message$end";

		# Salva o log
	    $h = fopen($fullpath, 'a+');
	    fwrite($h, $text);
	    fclose($h);

	    # Contabiliza quantidade de execucoes de escrita de log
		$this->index++;

		# Retorna a mensagem que foi escrita
		return $text;
	}


	/**
	 * @deprecated 
	 * @version 1.0
	 * @access public
	 * @method replaces_vars
	 * @param String $string					Mensagem com variaveis
	 * @param Array $custom 					Informacoes extras
	 * @return String
	 * 
	 * Substitui variaveis pelo valor correspondente
	 */

	/* 
	public function replaces_vars(String $string, Array $custom=[]) : String
	{
		$custom = array_to_object($custom);

		// substituir variaveis por valores
		$vars_keys = [];
		$vars_keys['index'] = '%index';
		$vars_keys['ip'] = '%ip';
		$vars_keys['user_agent'] = '%user_agent';
		$vars_keys['date'] = '%date';
		$vars_keys['host'] = '%host';

		$vars_values = [];
		$vars_values['index'] = $custom->n ?? 0;
		$vars_values['ip'] = $_SERVER['REMOTE_ADDR'] ?? $vars_keys['ip'];
		$vars_values['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? $vars_keys['user_agent'];
		$vars_values['date'] = date('Y-m-d H:i:s');
		$vars_values['host'] = $_SERVER['HTTP_HOST'] ?? $vars_keys['host'];
		
		$string = str_replace($vars_keys['index'], $vars_values['index'], $string);
		$string = str_replace($vars_keys['ip'], $vars_values['ip'], $string);
		$string = str_replace($vars_keys['user_agent'], $vars_values['user_agent'], $string);
		$string = str_replace($vars_keys['date'], $vars_values['date'], $string);
		$string = str_replace($vars_keys['host'], $vars_values['host'], $string);

		return $string;
	}
	*/


	/**
	 * @version 1.1
	 * @access public
	 * @method replaces_vars
	 * @param string $string					Mensagem com variaveis
	 * @param array $custom 					Informacoes extras
	 * @return string
	 * 
	 * Substitui variaveis pelo valor correspondente
	 */

	public function replaces_vars(string $string, array $custom=[]) : string
	{
		$custom = array_to_object($custom);

		$vars = [
			['key' => '%index', 'value' => $custom->n ?? 0],
			['key' => '%ip', 'value' => $_SERVER['REMOTE_ADDR'] ?? 'NULL'],
			['key' => '%user_agent', 'value' => $_SERVER['HTTP_USER_AGENT'] ?? 'NULL'],
			['key' => '%date', 'value' => date('Y-m-d H:i:s')],
			['key' => '%uniqid', 'value' => uniqid()],
			['key' => '%host', 'value' => $_SERVER['HTTP_HOST'] ?? 'NULL'],
			['key' => '%request.x_www_form_urlencoded', 'value' => json_encode($_REQUEST ?? [])],
			['key' => '%request.raw', 'value' => (function($raw) { 
				return $raw ? $raw : 'NULL'; })(file_get_contents('php://input')) ],
			['key' => '%filepath', 'value' => $custom->fullpath ?? 'NULL'],
		];

		foreach ($vars as $var) 
		{
			$var = (object) $var;
			$string = str_replace($var->key, $var->value, $string);
		}

		return $string;
	}
}