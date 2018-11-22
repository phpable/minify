<?php
namespace Able\Minify\Abstractions;

abstract class AMinifier {

	/**
	 * @param string $source
	 * @return string
	 */
	abstract public function minify(string $source): string;
}

