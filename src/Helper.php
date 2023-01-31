<?php 
namespace Ezeksoft\PHPWriteLog\Helper
{


	/**
	 * Transforma um Array em Objeto
	 * @param Array $array	Seu Array
	 * */

	function array_to_object($array)
	{
		return json_decode(json_encode($array));
	}


	/**
	 * Pega permissao da pasta
	 * @param String $path	Caminho de onde se deseja saber a permissao
	 * */

	function permission($path)
	{
		// converte o numero da permissao para um formato legivel
		return substr(sprintf('%o', fileperms($path)), -4);
	}
}