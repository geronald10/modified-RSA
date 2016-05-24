<?php
	$ex = gmp_init(3);
	$x = gmp_init(8);

	$result = gmp_mod($x, $ex);
	echo $result."ini";

	$hasil =  gmp_invert($result, $ex);
	echo $hasil."itu";
?>