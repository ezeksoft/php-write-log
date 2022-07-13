<?php

namespace Ezeksoft\PHPWriteLog;

class Table
{
	public $border_top = false; // exibe/oculta borda no topo da tabela
	public $border_bottom = true; // exibe/oculta borda no rodape da tabela

	/**
	 * @var Array $columns	Celulas do header
	 * */

	public Array $columns = [];


	/**
	 * @var Array $rows	Lista de linhas
	 * */

	public Array $rows = [];


	/**
	 * Carregar arquivo com a tabela
	 * @param String $file	Caminho do arquivo
	 */

	public function load(String $file)
	{
		$content = file($file); // carrega arquivo

		$this->file = (Object) [
			'path' => $file, // caminho do arquivo
			'content' => $content // conteudo do arquivo
		];

		# localizar o comeco dos dados descartando o header
		$header_separator_index = 0; // qual linha esta o separador
		$i = 0; // inicializa indice da linha
		foreach ($content as $row) // itera conteudo
		{
			if (substr($row, 0, 2) == '|-') // se encontrou o separador
			{
				$header_separator_index = $i; // salva o indice do separador
				break; // para iteracao
			}
			$i++; // incrementa indice da linha
		}

		$i = 0; // inicializa indice da linha
		$rows = []; // inicializa espaco para linhas
		$columns = []; // inicializa espaco para colunas
		foreach ($content as $row) // itera linhas do arquivo
		{
			$cells = explode('|', $row); // separa as celulas por |
			$new_cells = []; // inicializa espaco para celulas tratadas
			$j = 0; // inicializa indice da celula na linha
			foreach ($cells as $cell) // itera celulas
			{
				// se nao eh a primeira nem a ultima celula
				if ($j > 0 && $j < sizeof($cells) - 1) $new_cells[] = trim($cell); // salva celula
				$j++; // incrementa indice da celula
			}

			# localizou a primeira linha dos dados
			if ($i > $header_separator_index) // se o indice da linha eh maior que o indice do separador de linha
				$rows[] = $new_cells; // incrementa row

			# localizou o header
			else if ($i == 0)
				$columns = $new_cells; // define header

			$i++; // incrementa indice da linha
		}

		# adiciona linhas
		foreach ($rows as $row_cells) // itera linhas
		{
			$row = new Row; // instancia de linha
			$row->content = $row_cells; // seta celulas da linha
			$this->rows[] = $row; // adiciona esta linha na tabela
		}

		$this->columns = $columns; // define celulas do header
		$this->border_top = false; // oculta borda no topo
		$this->border_bottom = false; // oculta borda no rodape

		# limpa o arquivo
		$h = fopen($file, 'w');
		fclose($h);
	}
}