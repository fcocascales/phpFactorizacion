<?php

	include 'primes.php';

	//----------------------------------------------
	// CALCULATION

	/*
		Greatest common divisor (Máximo común divisor)
		Factores comunes en su mínimo exponente

		48, 180 --> 12
			Num1: 48 = 2^4*3
			Num2: 180 = 2^2*3^2*5
			GCD:  12 = 2^2*3
				48÷12=4
				180÷12=15
	*/
	function GCD($number1, $number2) {
		$factors1 = factoring($number1);
		$factors2 = factoring($number2);
		$factors = array();
		foreach($factors1 as $factor1=>$exponent1) {
			foreach($factors2 as $factor2=>$exponent2) {
				if ($factor1 == $factor2) {
					$factors[$factor1] = min($exponent1, $exponent2);
				}
			}
		}
		//print_r($factors); // print_r|var_dump
		if (empty($factors)) return 1;
		else return defactoring($factors);
	}

	/*
		Least common multiple (Mínimo común multiplo)
		Factores no comunes y comunes en su máximo exponente

		48, 180 --> 12
			Num1: 48 = 2^4*3
			Num2: 180 = 2^2*3^2*5
			LCM:  720 = 2^4*3^2*5
				720÷48=15
				720÷180=4
	*/
	function LCM($number1, $number2) {
		$factors1 = factoring($number1);
		$factors2 = factoring($number2);
		$factors = array();
		foreach($factors1 as $factor1=>$exponent1) {
			foreach($factors2 as $factor2=>$exponent2) {
				if ($factor1 == $factor2) {
					$factors[$factor1] = max($exponent1, $exponent2);
				}
				elseif (!array_key_exists($factor2, $factors)) {
					$factors[$factor2] = $exponent2;
				}
			}
			if (!array_key_exists($factor1, $factors)) {
				$factors[$factor1] = $exponent1;
			}
		}
		////print_r($factors); // print_r|var_dump
		return defactoring($factors);
	}

	/*
		array(2=>3, 3=>2, 5=>1) --> 360
		array(2=>2, 4=>1) --> 12
		array(2=>1, 5=>1) --> 20
		array(17=>1) --> 17
	*/
	function defactoring($factors) {
		if (empty($factors)) return 0;
		$number = 1;
		foreach ($factors as $factor=>$exponent) {
			$number *= pow($factor, $exponent);
		}
		return $number;
	}

	/*
		360 --> array(2=>3, 3=>2, 5=>1)
		12 --> array(2=>2, 4=>1)
		20 --> array(2=>1, 5=>1)
		17 --> array(17=>1)
	*/
	function factoring($number) {
		global $primes;
		if ($number < 0) return array();
		elseif ($number == 1) return array(1=>1);
		elseif (in_array($number, $primes)) return array($number=>1);
		$factors = array();
		$result = $number;
		foreach($primes as $prime) {
			if ($prime > $number/2) break;
			$exponent = 0;
			for(;;) {
				list($quotient, $remainder) = division($result, $prime);
				if ($remainder != 0) break;
				$exponent++;
				$result = $quotient;
				if ($exponent > 100) break; // Avoid incontrolled infinite iterations
			}
			if ($exponent >= 1) {
				$factors[$prime] = $exponent;
			}
		}
		return $factors;
	}

	function division($dividend, $divisor){
		////return ($dividend - $dividend % $divisor) / $divisor; // like intdiv php7
		////return floor($dividend / $divisor);
		$quotient = (int)($dividend / $divisor);
		$remainder = $dividend % $divisor;
		return array($quotient, $remainder);
	}

	//----------------------------------------------
	// FORMATS

	function format_factoring($number, $text="") {
		$factors = format_factors($number);
		echo "<div>$text<strong>$number</strong><span> = $factors</span></div>";
	}

	/*
		360 --> array(2=>3, 3=>2, 5=>1) --> "2<sup>3</sup>&times;3<sup>2</sup>&times;5"
		12 --> array(2=>2, 4=>1) --> "2<sup>2</sup>&times;4"
		20 --> array(2=>1, 5=>1) --> "2&times;5"
		17 --> array(17=>1) --> "17"
	*/
	function format_factors($number) {
		$factors = factoring($number);
		$html = "";
		foreach($factors as $factor=>$exponent) {
			if (!empty($html)) $html .= '&times;';
			if ($factor == $number) $html .= "<span class=\"prime\">$factor</span>";
			else $html .= $factor;
			if ($exponent>=2) $html .= "<sup>$exponent</sup>";
		}
		return $html;
	}

	//----------------------------------------------
	// PRINT

	function print_factoring($begin, $end) {
		start_chrono();
		for ($number=$begin; $number<=$end; $number++) {
			echo format_factoring($number);
		}
	}
	function print_gcd($num1, $num2) {
		$gcd = GCD($num1, $num2);
		format_factoring($num1, "Num1: ");
		format_factoring($num2, "Num2: ");
		format_factoring($gcd, "GCD: ");
		$calc1 = $num1/$gcd;
		$calc2 = $num2/$gcd;
		$fact1 = format_factors($calc1);
		$fact2 = format_factors($calc2);
		echo "<div>$num1&divide;$gcd = <strong>$calc1</strong> = $fact1</div>";
		echo "<div>$num2&divide;$gcd = <strong>$calc2</strong> = $fact2</div>";
	}
	function print_lcm($num1, $num2) {
		$lcm = LCM($num1, $num2);
		format_factoring($num1, "Num1: ");
		format_factoring($num2, "Num2: ");
		format_factoring($lcm, "LCM: ");
		$calc1 = $lcm/$num1;
		$calc2 = $lcm/$num2;
		$fact1 = format_factors($calc1);
		$fact2 = format_factors($calc2);
		echo "<div>$lcm&divide;$num1 = <strong>$calc1</strong> = $fact1</div>";
		echo "<div>$lcm&divide;$num2 = <strong>$calc2</strong> = $fact2</div>";
	}

	//----------------------------------------------
	// CHRONO

	function start_chrono() {
		global $start;
		$start = microtime(true);
	}

	function end_chrono() {
		global $start;
		$time_elapsed_secs = microtime(true) - $start;
		return $time_elapsed_secs;
	}

	//----------------------------------------------
	// PARAMETERS

	function getParamLimits() {
		$MIN = 1;
		$MAX = getMaxPrime();
		$min = isset($_GET['min'])? intval($_GET['min']) : $MIN;
		$max = isset($_GET['max'])? intval($_GET['max']) : 100;
		if ($min < $MIN) $min = $MIN;
		if ($max > $MAX) $max = $MAX;
		return array($min, $max);
	}

	function getParamOperation() {
		$operations = array("factoring", "gcd", "lcm");
		$oper = isset($_GET['oper'])? $_GET['oper'] : $operations[0];
		if (!in_array($oper, $operations)) $oper = "";
		return $oper;
	}

	list($min, $max) = getParamLimits();
	$oper = getParamOperation();

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Factoring</title>
<link rel="stylesheet" href="styles.css">
<script type="text/javascript">
	function random() {
		document.getElementById('min').value = rand();
		document.getElementById('max').value = rand();
		function rand() {
			var min = 1;
			var max = <?php echo (int)(getMaxPrime()/10); ?>;
			return Math.floor((Math.random() * max) + min);
		}
	}
</script>
</head>
<body>
	<?php if($oper == 'factoring'): ?>
		<h1>Factoring</h1>
		<div id="calc"><?php print_factoring($min, $max); ?></div>
		<p>Execution elapsed <?php echo end_chrono() ?> seconds</p>
	<?php elseif($oper == 'gcd'): ?>
		<h1>Greatest common divisor</h1>
		<div id="gcd"><?php print_gcd($min, $max); ?></div>
	<?php elseif($oper == 'lcm'): ?>
		<h1>Least common multiple</h1>
		<div id="lcm"><?php print_lcm($min, $max); ?></div>
	<?php else: ?>
		<h1>Not found</h1>
		<p>Operation unknown</p>
	<?php endif; ?>
	<div id="params">
		<h2>Calculate</h2>
		<ul id="links">
			<li><a href="?max=10">10 numbers</a></li>
			<li><a href="?max=100">100 numbers</a></li>
			<li><a href="?max=1000">1.000 numbers</a></li>
			<li><a href="?max=10000">10.000 numbers</a></li>
		<ul>
		<form>
			<input type="number" name="min" id="min" placeholder="min" value="<?php echo $min ?>">
			<input type="number" name="max" id="max" placeholder="max" value="<?php echo $max ?>">
			<button name="oper" value="factoring">Factoring</button>
			<button name="oper" value="gcd" title="Greatest common divisor">GCD</button>
			<button name="oper" value="lcm" title="Least common multiple">LCM</button>
			<a href="#" onclick="random()">Random</a>
		</form>
	</div>
</body>
</html>
