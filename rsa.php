<?php

	$nama = $_POST["nama_lengkap"];
	$noKTP = $_POST["no_KTP"];
	$pilihan = $_POST["Pilihan"];
	$divide = "~";

	// Get all of the input and combine it separated by ~ sign
	$text = $nama.$divide.$noKTP.$divide.$pilihan;
	// change text format to Number format so it can be encrypted by RSA
	$data = txt2num($text);


	// generate keys 
	list($public_key_n, $public_key_d,$public_key_z1, $private_key_e,$public_key_z2) = get_rsa_keys(); needed
	// echo gmp_strval($public_key_n) . '<br />'; // this is one element of the public key;
	// echo "Public key : ".'<br />';
	// echo gmp_strval($public_key_d) . '<br />'; // the other... this one must be bigger than the secret you will encrypt
	// echo "Private key : ".'<br />';
	// echo gmp_strval($private_key_e) . '<br />'; // your private key
	// set a secret to encrypt/decrypt
	$secret = gmp_init($data); 

	$encrypted = rsa_encrypt($secret, $public_key_d, $public_key_z1); // apply the encryption
	
	$decrypted = rsa_decrypt($encrypted, $private_key_e, $public_key_z2); // decrypt knowing the secret key
	
	// Change nummber format to text format so it can be read by man 
	$decryptedText = num2txt($decrypted);
	

	//Turns regular text into a number that can be manipulated by the RSA algorithm	
	function txt2num($str) {
		$result = '0';
		$n = strlen($str);
		do {
			$result = bcadd(bcmul($result, '256'), ord($str{--$n}));
		} while ($n > 0);
		return $result;
	}

	//Turns the numeric representation of text (as output by txt2num) back into text
	function num2txt($num) {
		$result = '';
		do {
			$result .= chr(bcmod($num, '256'));
			$num = bcdiv($num, '256');
		} while (bccomp($num, '0'));
		return $result;
	}

	// Modified RSA Algorithm
	function get_rsa_keys() 
	{
		// get random prime number
		$unu = gmp_init(1);
		$p = get_random_prime();
		$q = get_random_prime();
		$r = get_random_prime();
		$s = get_random_prime();

		$N = gmp_mul($p, $q); // n = p * q
		$M = gmp_mul($r, $s);
		$M = gmp_mul($N, $M); // m = p * q * r * s

		$fi_de_n = gmp_mul( gmp_sub($p, $unu), gmp_sub($q, $unu) ); //phi(n) = (p-1)*(q-1)
		$fi_de_n2 = gmp_mul(gmp_sub($r, $unu), gmp_sub($s, $unu)); //phi(m) = (r-1)*(s-1)
		$fi_de_n = gmp_mul($fi_de_n, $fi_de_n2); //phi(n) = (p-1)*(q-1)*(r-1)*(s-1)

		// search random number that is co prime to N 
		$e = gmp_random(4); 
		$e = gmp_nextprime($e);
		
		while (gmp_cmp(gmp_gcd($e, $fi_de_n), $unu) != 0)
		{
			$e = gmp_add($e, $unu);
		}
		// search d which is modInverse of e (which is the complement of e)
		$d = modinverse($fi_de_n, $e );

		return array($N, $d,$M, $e,$N);
	}
	
	// function to find mod Inverse
	function modinverse ($A, $Z)
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

	function rsa_encrypt($message, $public_key_d, $public_key_z1) // function to encrypt
	{
		$resp = gmp_powm($message, $public_key_d, $public_key_z1);
		return $resp;
	}

	function rsa_decrypt($value, $private_key_e, $public_key_z2) // function to decrypt
	{
		$resp = gmp_powm($value, $private_key_e, $public_key_z2);
		return $resp;
	}
	?>
	
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bootstrap Long Multi-Step Form</title>

        <!-- CSS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,700">
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="assets/css/form-elements.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/media-queries.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Favicon and touch icons -->
        <link rel="shortcut icon" href="assets/ico/favicon.png">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

    </head>

    <body>
		
		<!-- Top menu -->
		<nav class="navbar navbar-inverse" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#top-navbar-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.html">EVOTE Modified RSA Form </a>
				</div>
			</div>
		</nav>
        
        <!-- Description -->
		<div class="description-container">
	        <div class="container">
	        	<div class="row">
	                <div class="col-sm-12 description-title">
	                    <h2>Electronic Vote (E-VOTE)</h2>
	                </div>
	            </div>
			</div>
		</div>
		
		<!-- Multi Step Form -->
		<div class="msf-container">
	        <div class="container">
	            <div class="row">
	                <div class="col-sm-12 msf-form">
	                    
	                    <form role="form" action="index.php" method="post" class="form-inline">
	                    	<fieldset>
	            				<h4>Data ENKRIPSI<span class="step"> (Step 1 / 2)</span></h4>
	            				<div class="form-group">
				                    <label for="first-name">INPUT</label><br>
				                    <input type="text" name="nama_lengkap" class="first-name form-control" id="first-name" value=<?php echo "$text"; ?>>
				                </div>
				                <div class="form-group">
				                    <label for="last-name">Encrypted</label><br>
				                    <input type="text" name="no_KTP" class="last-name form-control" id="last-name" value=<?php echo "$encrypted";?>>
				                </div>
	            				<br>
	            				<button type="button" class="btn btn-next">Next <i class="fa fa-angle-right"></i></button>
	            			</fieldset>
	            			
	            			<fieldset>
	            				<h4>Tentukan Pilihan Anda<span class="step"> (Step 2 / 2)</span></h4>
				                <div class="form-group">
				                    <label for="first-name">DECRYPTED in Number</label><br>
				                    <input type="text" name="nama_lengkap" class="first-name form-control" id="first-name" value=<?php echo "$decrypted"; ?>>
				                </div>
				                <div class="form-group">
				                    <label for="last-name">Decrypted Text</label><br>
				                    <input type="text" name="no_KTP" class="last-name form-control" id="last-name" value=<?php echo "$decryptedText";?>>
				                </div>
	            				<br>
	            				<br>
	            				<button type="button" class="btn btn-previous"><i class="fa fa-angle-left"></i> Previous</button>
	            				<button type="submit" class="btn">DONE</button>
	            			</fieldset>
	                    	
	                    </form>
	                    
	                </div>
	            </div>
			</div>
		</div>
		
		

        <!-- Javascript -->
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.backstretch.min.js"></script>
        <script src="assets/js/scripts.js"></script>
        
        <!--[if lt IE 10]>
            <script src="assets/js/placeholder.js"></script>
        <![endif]-->

    </body>

</html>
