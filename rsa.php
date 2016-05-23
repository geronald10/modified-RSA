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


	$data = base64_encode($text);
	echo "Base 64 encode".$data.'<br/>';
	echo "================".'<br/>';
	echo base64_decode($data);
	echo "================".'<br/>';
	
	$int = (int)$text;
	echo $int.'<br/>';

	base64_decode($text);

	echo (String)$int;



	list($public_key_n, $public_key_d, $private_key_e) = get_rsa_keys(); // we generate the keys needed
	echo gmp_strval($public_key_n) . '<br />'; // this is one element of the public key;
	echo "Public key : ".'<br />';
	echo gmp_strval($public_key_d) . '<br />'; // the other... this one must be bigger than the secret you will encrypt
	echo "Private key : ".'<br />';
	echo gmp_strval($private_key_e) . '<br />'; // your private key
	$secret = gmp_init($text); // set a secret to encrypt/decrypt
	echo "Encrypted : ".'<br />';
	$encrypted = rsa_encrypt($secret, $public_key_d, $public_key_n); // apply the encryption
	echo gmp_strval($encrypted) . '<br />'; // show the encrypted output
	echo "Decrypted Text:".'<br/>';
	$decrypted = rsa_decrypt($encrypted, $private_key_e, $public_key_n); // decrypt knowing the secret key
	echo gmp_strval($decrypted) . '<br />'; // and this should match the secret.. and it does :)
	
	function get_rsa_keys() // I will not bore you with comments on this function as the ones on wikipedia are way more useful
	{
		$unu = gmp_init(1);
		$p = get_random_prime();
		$q = get_random_prime();
		$N = gmp_mul($p, $q);
		$fi_de_n = gmp_mul( gmp_sub($p, $unu), gmp_sub($q, $unu) );
		$e = gmp_random(4); 
		$e = gmp_nextprime($e);
		while (gmp_cmp(gmp_gcd($e, $fi_de_n), $unu) != 0)
		{
			$e = gmp_add($e, $unu);
		}
		$d = modinverse($fi_de_n, $e );
		return array($N, $d, $e);
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
		return $resp;
	}
	?>