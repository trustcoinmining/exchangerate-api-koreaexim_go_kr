<?php 

/*
	한국수출입은행 환율 OpenAPI , Decimal / Float Type 변경 PHP 모듈.
	
	@2019 Copyleft by trustcoinmining.com
	MIT License.
	아무나 가져다 쓰셔요. 

	-------

	API 출력후, 아래의 Json Parsing 후, regex 스트링은 그대로 카피하여, javascript 코드 및
	regex (regular expression) 지원하는 곳에 사용하여도 됩니다.

	API 출력이, 1,123.00 처럼 string type 처리해야 되므로, 연산을 위해서,
	아래의 getAmount() function 을 이용하여, , 를 제거한 integer / decimal (rdbms) type으로
	변경시 사용하세요.

	아래는 mysql 에 넣는 예제 입니다.

	- mysql table 구조 및 만드는 예제.

	--- sql structure - 
--
-- 테이블 구조 `currency_exchangerate`
--

CREATE TABLE `currency_exchangerate` (
  `no` int(11) NOT NULL,
  `usd` decimal(11,2) NOT NULL,
  `eur` decimal(11,2) NOT NULL,
  `jpy100` decimal(11,2) NOT NULL,
  `cnh` decimal(11,2) NOT NULL,
  `gbp` decimal(11,2) NOT NULL,
  `chf` decimal(11,2) NOT NULL,
  `cad` decimal(11,2) NOT NULL,
  `hkd` decimal(11,2) NOT NULL,
  `aud` decimal(11,2) NOT NULL,
  `wdate` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

*/

$servername = "localhost";
$username = "dbxxx";
$password = "fSHVWxxxxxxxxt";
$db = "dbexchangedb";
$tablename = "currency_exchangerate";

function getAmount($money)
{
    $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
    $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

    $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

    $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
    $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

    return (float) str_replace(',', '.', $removedThousendSeparator);
}

// Create connection
$connect_db = mysqli_connect($servername, $username, $password);
$select_db= mysqli_select_db($connect_db,$db);

	$url_exchangerate = "https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey=mkigDVCGjxxxxxauthkeyxxxxxxxxx&data=AP01";
	$contents = file_get_contents($url_exchangerate);
	$pr = json_decode($contents,true);
	//	echo $pr;
	
	$USD1 = '';
	$JPY1 = '';
	$EUR1 = '';
	$CNH1 = '';
	$CAD1 = '';
	$CHF1 = '';
	$GBP1 = '';
	$HKD1 = '';
	$AUD1 = '';
	foreach($pr as $key => $val) {
		$value = $val['cur_unit'];
		if($value == null) {
			exit;
		}

		$crname = $value;
		$crrate = getAmount($val['deal_bas_r']); 
		if($crname == "USD") {
			$USD1 = $crrate; 
		}
		else if ($crname == "EUR") {
			$EUR1 = $crrate; 
		}
		else if ($crname == "JPY(100)") {
			$JPY1 = $crrate; 
		}
		else if ($crname == "CNH") {
			$CNH1 = $crrate; 
		}
		else if ($crname == "CAD") {
			$CAD1 = $crrate; 
		}
		else if ($crname == "CHF") {
			$CHF1 = $crrate; 
		}
		else if ($crname == "GBP") {
			$GBP1 = $crrate; 
		}
		else if ($crname == "HKD") {
			$HKD1 = $crrate; 
		}
		else if ($crname == "AUD") {
			$AUD1 = $crrate; 
		}
	}

	$sql = "insert into ".$tablename." set
		usd='".$USD1."',
		eur='".$EUR1."',
		jpy100='".$JPY1."',
		cnh='".$CNH1."',
		gbp='".$GBP1."',
		chf='".$CHF1."',
		cad='".$CAD1."',
		hkd='".$HKD1."',
		aud='".$AUD1."'
		";

		mysqli_query($connect_db, $sql);
	 	// echo $sql;
?>
