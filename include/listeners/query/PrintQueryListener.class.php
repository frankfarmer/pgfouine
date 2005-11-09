<?php

class PrintQueryListener extends QueryListener {
	function fireEvent(& $query) {
		print_r($query);
	}
}

?>