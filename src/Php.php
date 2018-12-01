<?php
namespace Able\Minify;

use \Able\Minify\Abstractions\AMinifier;
use \Able\Helpers\Str;

class Php extends AMinifier {

	/**
	 * @param string $source
	 * @return string
	 */
	function minify(string $source): string {
		$Tokens = (token_get_all($source, TOKEN_PARSE));
		$out = '';

		$Vars = [];
		foreach ($Tokens as $i => $Info){
			if (is_array($Info)){
				if (in_array($Info[0], [T_DOC_COMMENT, T_COMMENT])){
					continue;
				}

				if ($Info[0] == T_STRING && $i > 0 && is_array($Tokens[$i - 1])
					&& $Tokens[$i - 1][0] == T_OBJECT_OPERATOR){
						$Info[1] = $this->replace($Info[1], $Vars);
				}

				if ($Info[0] == T_VARIABLE && !in_array($Info[1], ['$this', '$_POST', '$_GET', '$_REQUEST',
					'$_FILES', '$_SERVER', '$_SESSION', '$_COOKIE', '$_ENV'])){

					$Info[1] = '$' . $this->replace(substr($Info[1], 1), $Vars);
				}

				if ($Info[0] == T_WHITESPACE){
					$Info[1] = ' ';

					if ($i > 0 && !is_array($Tokens[$i - 1])){
						$Info[1] = '';
					}

					if ($i < count($Tokens) - 2 && !is_array($Tokens[$i + 1])){
						$Info[1] = '';
					}
				}

				$out .= $Info[1];
			} else {
				$out .= $Info;
			}
		}

		return $out;
	}

	/**
	 * @param string $name
	 * @param array $Vars
	 * @return string
	 */
	private function replace(string $name, array &$Vars = []): string {
		if (!isset($Vars[$name])){
			$Vars[$name] = chr(97 + (count($Vars) % 26));

			if (count($Vars) > 26) {
				$Vars[$name] .= floor(count($Vars) / 26);
			}
		}

		return $Vars[$name];
	}

}
