<?php

class PrintErrorListener extends ErrorListener {
	function fireEvent(& $query) {
		print_r($query);
	}
}

?>