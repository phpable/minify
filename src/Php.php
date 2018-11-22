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

				if ($Info[0] == T_VARIABLE){
					if (!isset($Vars[$Info[1]])){
						$Vars[$Info[1]] = '$' . chr(97 + (count($Vars) % 26));

						if (count($Vars) > 26) {
							$Vars[$Info[1]] .= floor(count($Vars) / 26);
						}
					}

					$Info[1] = $Vars[$Info[1]];
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

}
