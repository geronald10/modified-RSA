<?php
	var_dump($_POST);
	$nama = $_POST["nama_lengkap"];
	$noKTP = $_POST["no_KTP"];
	$pilihan = $_POST["Pilihan"];
	$divide = "~";

	// echo $nama;
	// echo $noKTP;
	echo $pilihan.'<br/>';

	$text = $nama.$noKTP.$pilihan;

	echo $text.'<br/>';
	$data = txt2num($text);
	echo "text2num = ".$data.'<br/>';
	echo "================".'<br/>';
	echo num2txt($data).'<br/>';
	echo "================".'<br/>';
	


	// base64_decode($text);

	// echo (String)$int;



	list($public_key_n, $public_key_d, $private_key_e) = get_rsa_keys(); // we generate the keys needed
	echo gmp_strval($public_key_n) . '<br />'; // this is one element of the public key;
	echo "Public key : ".'<br />';
	echo gmp_strval($public_key_d) . '<br />'; // the other... this one must be bigger than the secret you will encrypt
	echo "Private key : ".'<br />';
	echo gmp_strval($private_key_e) . '<br />'; // your private key
	$secret = gmp_init($data); // set a secret to encrypt/decrypt
	echo "Encrypted : ".'<br />';
	$encrypted = rsa_encrypt($secret, $public_key_d, $public_key_n); // apply the encryption
	echo gmp_strval($encrypted) . '<br />'; // show the encrypted output
	echo "Decrypted Text:".'<br/>';
	$decrypted = rsa_decrypt($encrypted, $private_key_e, $public_key_n); // decrypt knowing the secret key
	echo gmp_strval($decrypted) . '<br />'; // and this should match the secret.. and it does :)
	echo num2txt($decrypted).'<br/>';

	function txt2num($str) {
		//Turns regular text into a number that can be manipulated by the RSA algorithm
		$result = '0';
		$n = strlen($str);
		do {
			$result = bcadd(bcmul($result, '256'), ord($str{--$n}));
		} while ($n > 0);
		return $result;
	}
	function num2txt($num) {
		//Turns the numeric representation of text (as output by txt2num) back into text
		$result = '';
		do {
			$result .= chr(bcmod($num, '256'));
			$num = bcdiv($num, '256');
		} while (bccomp($num, '0'));
		return $result;
	}

	function get_rsa_keys() // I will not bore you with comments on this function as the ones on wikipedia are way more useful
	{
		$unu = gmp_init(1);
		// echo "UNU: ".$unu;
		$p = get_random_prime();
		echo "P: ".$p;
		$q = get_random_prime();
		echo "Q: ".$p;
		$r = get_random_prime();
		$s = get_random_prime();

		$N = gmp_mul($p, $q);
		$M = gmp_mul($r, $s);
		$fi_de_n = gmp_mul( gmp_sub($p, $unu), gmp_sub($q, $unu) );
		$fi_de_n2 = gmp_mul(gmp_sub($r, $unu), gmp_sub($s, $unu));
		$fi_de_n = gmp_mul($fi_de_n, $fi_de_n2);
		// $alpha = gmp_mul( gmp_sub($r, $unu), gmp_sub($s, $unu) );
		$e = gmp_random(4); 
		$e = gmp_nextprime($e);
		// $kp = gmp_random(4);
		// $kp = gmp_nextprime($kp);
		// $n = log((double)$N);
		// $n = (int)$n;
		// $n = gmp_init($n);
		// echo "log n: ".$n.'<br>'."check this out: ".$kp.'<br>'." N: ".$N.'<br>';

		// echo gmp_gcd($kp, $N).'<br>';
		// while ( (gmp_cmp($n, $kp) < 0) && (gmp_cmp($kp, $N) >1 ) && (gmp_cmp(gmp_gcd($kp, $N), $unu) != 0)  ) {
		// 	echo "N: ".$N." kp: ".$kp.'<br>';
		// 	$kp = gmp_add($kp, $unu);
		// 	# code...
		// }
		// $d = gmp_random(4);
		// $d = gmp_nextprime($d);
			

		// echo gmp_gcd($d, $N).'<br>';
		// echo gmp_cmp($p, $q).'<br>';
		// if (gmp_cmp($p, $q) == 1 ) {
		// 	while ( gmp_cmp(gmp_sub($N, $q), $d)<0 && gmp_cmp($d, $N) > 1 && (gmp_cmp(gmp_gcd($d, $N), $unu) != 0)) {
		// 		$d = gmp_add($d, $unu);
		// 		# code...
		// 	}
		// 	# code...
		// }elseif (gmp_cmp($p, $q) == -1) {
		// 	while ( gmp_cmp(gmp_sub($N, $p), $d)<0 && gmp_cmp($d, $N) > 1 && (gmp_cmp(gmp_gcd($d, $N), $unu) != 0)) {
		// 		$d = gmp_add($d, $unu);
		// 		# code...
		// 	}
		// 	# code...
		// }
		// echo "D: ".$d.'<br>';
		// // $ks = gmp_add(gmp_mul($unu, $d), $unu);
		// $ks = gmp_div(gmp_add($d, $unu),$kp);
		// $ks = modinverse($kp,$d);

		// echo "KP: ".$kp." KS: ".$ks.'<br>';
		// echo "MOdul: ".gmp_mod(gmp_mul($kp, $ks), $d).'<br>';
		// echo "KS: ".$ks.'<br>';



		while (gmp_cmp(gmp_gcd($e, $fi_de_n), $unu) != 0)
		{
			$e = gmp_add($e, $unu);
		}

		$d = modinverse($fi_de_n, $e );
		// echo "TEST:".gmp_mod(gmp_mul($e, $d), $fi_de_n).'<br>' ;
		// $g = gmp_add($M, $unu);
		// $mu = modinverse($alpha,$M);

		// echo "COBA".'<br>';
		// $xt = 123;
		// $cekr = gmp_init(3);
		
		// //encryption
		// // C = (g^(M^(e mod n)))*((r^m)*mod (m^2))
		// $tempx = gmp_mod($e, $N);
		// $temp = gmp_pow($xt, $tempx);
		// $temp = gmp_pow($xt, $tempx );
		// gmp_pow($g, $temp );
		// // $C = gmp_mul(gmp_pow($g, gmp_pow($xt, gmp_mod($e, $N))), (gmp_mul(gmp_pow($r, $M),gmp_mod($unu, gmp_pow($M, 2))));
		// echo $c;
		// //decryption (((C^alpha mod (m^2)-1)/m) * mu mod m) ^d mod n
		// $D = gmp_mod(gmp_pow(gmp_mul(gmp_div((gmp_pow($C, $alpha) * gmp_mod(1, gmp_pow($M, 2) - 1)), $M), ($mu * gmp_mod(1, $M))), $d), $N); 
		// echo $D;

		return array($N, $d, $e);
		// return array($d,$kp,$ks);
	}
	
	function modinverse ($A, $Z)// same as the other one
	{
		$N=$A;
		$M=$Z;
		$u1=1;
		$u2=0;
		$u3=$A;
		$v1=0;
		$v2=1;
		$v3=$Z;
		while ( gmp_cmp($v3, 0) != 0) 
		{
			$qq=gmp_div($u3,$v3);
			$t1=gmp_sub($u1, gmp_mul($qq,$v1));
			$t2=gmp_sub($u2, gmp_mul($qq,$v2));
			$t3=gmp_sub($u3, gmp_mul($qq,$v3));
			$u1=$v1;
			$u2=$v2;
			$u3=$v3;
			$v1=$t1;
			$v2=$t2;
			$v3=$t3;
			$z=1;
		}
		$uu=$u1;
		$vv=$u2;
		$zero = gmp_init(0);
		if (gmp_cmp($vv, $zero) < 0)
		{
			$I=gmp_add($vv,$A);
		}
		else
		{
			$I=$vv;
		}
		return $I;
	}

	function get_random_prime( $val = 4 ) // getting a random prime number.. gmp doesn't have a function for this.. so..
	{
		$seed = gmp_random( $val ); // get a random number
		$prime = gmp_nextprime( $seed ); // get the next prima number
		return $prime;
	}
	function rsa_encrypt($message, $public_key_d, $public_key_n) // this is easy
	{
		$resp = gmp_powm($message, $public_key_d, $public_key_n);
		return $resp;
	}

	function rsa_decrypt($value, $private_key_e, $public_key_n) // this one too
	{
		$resp = gmp_powm($value, $private_key_e, $public_key_n);
		// $resp = gmp_pow($resp, 1/2);
		return $resp;
	}
	?>