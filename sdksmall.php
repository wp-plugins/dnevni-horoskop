<?php
if (!function_exists('curl_init')) {
  throw new HoroskopiusSmallAPIException('Horoskopius SDK zahteva CURL PHP ekstenziju.');
}
if (!function_exists('json_decode')) {
  throw new HoroskopiusSmallAPIException('Horoskopius SDK zahteva JSON PHP ekstenziju.');
}
define('HORS_PATH_BASE', dirname(__FILE__) );
define( 'DRSS', DIRECTORY_SEPARATOR );


class HoroskopiusSmallAPIException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}


class HoroskopiusSmallSDK {
	
	private $public_key;
	private $private_key;
	private $horoscope;
	private $horoscope_type;
	private $category;
	private $cache;
	private $response_type;
	private $signature;
	/* Headlines */
	private $headline_horoscope;
	private $headline_category;
	private $headline_type;
	private $date_horoskop;
	private $speedup;
	private $latin;
	private $links;
	
	public function __construct() {
			 $this->response_type = 'xml';
			 $this->horoscope = 1;
			 $this->category = 1;
			 $this->horoscope_type = 1;
			 $this->cache = 1;
			 $this->speedup = 1;
			 $this->latin = 1;
			 $this->links = 1;
	}
	
	public function setAlphabet($i) {
			$this->latin = ($i > 0 && $i <= 2) ? $i : $this->latin;
	}
	
	private function generateSignature($k) {
		$sig = base64_encode(hash_hmac('sha1', $k, true));	
		return $sig;
	}
	
	public function getLink($l) {
			$this->links = ($l > 0 && $l <= 2) ? $l : $this->links;
	}
	
	public function setPrivateKey($k) {
			$this->private_key = $k;
	}
	
	public function setPublicKey($k) {
			$this->public_key = $k;	
	}
	
	public function getResponse() {
		switch ($this->response_type) :
			case 'xml':
				$this->returnXML();
			break;
		endswitch;
	}
	
	private function setCurlResponse() {	
		$this->signature = $this->generateSignature($this->private_key);
		$url = "http://dev.horoskopius.com/service/";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "type=$this->response_type&horoscope=$this->horoscope&category=$this->category&horoscope_type=$this->horoscope_type&sig=" . urlencode($this->signature) . "&auth=" . urlencode($this->public_key) . "&cache=" . $this->cache .""); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Horoskopius');
		$result = curl_exec($ch);
		return $result;
	}
	
	private function returnXML() {
		$cachefile = HORS_PATH_BASE . DRSS . 'cachebase' . DRSS . $this->response_type . $this->category . $this->horoscope . $this->horoscope_type . '.htm';
		$cachetime = 120*60;
		if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile)) && $this->speedup == 1) :
		require($cachefile);
		echo '<!-- speed up horoskopius -->';
		else:
		$response = $this->setCurlResponse();
		$xml = new SimpleXmlElement($response, LIBXML_NOCDATA);
		$cnt = count($xml->{"horoscope"});
		$content = '<div id="horoskopiusdaily"><ul>';
		for($i=0; $i<$cnt; $i++) :
			$content.= '<li><h3><span class="sign-container hor-' . strtolower(str_replace("Š", "s", $xml->{"horoscope"}[$i]->{"sign"})) . '"></span><strong>' . $this->latin2cyrillic($xml->{"horoscope"}[$i]->{"sign"}) . '</strong></h3> ' . $this->latin2cyrillic($xml->{"horoscope"}[$i]->{"horoscopetxt"}) . '</li>';
		endfor;
			if ($this->links == 1) : 
			$link = '<a href="http://www.horoskopius.com">Horoskopius</a>';
			else : 
			$link = 'Horoskopius';
			endif;
			$content .= '<li class="horoskopius-link">' . $this->latin2cyrillic('Horoskop obezbedio - Astro portal').' -  ' . $link . '</li>';
			
			$content .= '</ul></div>';
			echo $content;
		$fp = fopen($cachefile, 'w');
		fwrite($fp, $content);
		fclose($fp);
		endif;
	}
	
	
	private function latin2cyrillic($text) {
		
		$tr = array(
					"A"=>"А",
					"B"=>"Б",
					"C"=>"Ц",
					"Č"=>"Ч",
					"D"=>"Д",
					"Đ"=>"Ђ",
					"E"=>"Е",
					"F"=>"Ф",
					"G"=>"Г",
					"H"=>"Х",
					"I"=>"И", 
					"J"=>"Ј",
					"K"=>"К",
					"L"=>"Л",
					"M"=>"М",
					"N"=>"Н", 
					"O"=>"О",
					"P"=>"П",
					"R"=>"Р",
					"S"=>"С",
					"Š"=>"Ш", 
					"T"=>"Т",
					"U"=>"У",
					"V"=>"В",
					"Z"=>"З",
					"Ž"=>"Ж", 
					"Ć"=>"Ћ",
					"a"=>"а",
					"b"=>"б",
					"c"=>"ц",
					"č"=>"ч", 
					"ć"=>"ћ",
					"d"=>"д",
					"đ"=>"ђ",
					"e"=>"е",
					"f"=>"ф",
					"g"=>"г", 
					"h"=>"х",
					"i"=>"и",
					"j"=>"ј",
					"k"=>"к",
					"l"=>"л", 
					"m"=>"м",
					"n"=>"н",
					"o"=>"о",
					"p"=>"п",
					"r"=>"р", 
					"s"=>"с",
					"š"=>"ш",
					"t"=>"т",
					"u"=>"у",
					"v"=>"в", 
					"z"=>"з",
					"ž"=>"ж",
					"Lj"=>"Љ",
					"Nj"=>"Њ",
					"Dž"=>"Џ",
					"lj"=>"љ",
					"nj"=>"њ",
					"dž"=>"џ"
					);
	if ($this->latin == 2) : 
	return strtr($text,$tr);	
	else : 
	return $text;
	endif;
	}
	
}
?>