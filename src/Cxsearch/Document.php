<?php

namespace Cxsearch;

use Cxsearch\Index;
use Buzz\Browser;

class Document {
	private $index;

	public static function load(Index $index, $id) {
		$obj = new Document($index);



		return $obj
	}
}