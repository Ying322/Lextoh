<?php
  /**
    Fonctions partagées par plusieurs services.
  */

  /**
    Conversion d'une chaîne en hexadécimal (2 caractères représentent 1 octet) vers une chaîne d'octets.
  */
  /*
  function hex2bin($hex_str) {
    $bin_str = pack("H*", $hex_str);   
    return $bin_str;
  }
*/
  /**
    Décodage d'une étiquette de graphe-Q, si elle est encodée en pseudoUrlEncode (sinon retourne la chaîne inchangée)
  */
  function gQDecode($str) {
    if(!preg_match('/^#U/', $str))  // Si pas d'identifiant de codage: la chaîne n'est pas encodée en pseudo-urlencode
      return $str;
    $str = substr($str, 2, strlen($str));  // Enlever l'identifiant de codage
    $matches = array();
    while(preg_match('/#(.+?)#/', $str, $matches)) {  // Pour chaque caractère "spécial" (on a son code hexa au lieu du caractère lui-même)
      $code = $matches[1];
      $decoded = mb_convert_encoding(hex2bin(sprintf('%08s', $code)), 'UTF-8', 'UCS-4BE');
      $str = preg_replace('/#(.+?)#/', $decoded, $str);
    }
    return $str;
  }
  
   function gQEncode($str) {  // Fonction pour échapper les caractères unicode multibytes
      $unicodeStr = bin2hex(mb_convert_encoding($str, 'UCS-4BE', 'UTF-8'));
      $newStr = '';
      foreach(str_split($unicodeStr, 8) as $char) {  // Récupérer le code Unicode de chaque caractère
        $decChar = hexdec($char);
        if(($decChar >= 0x30 && $decChar <= 0x39) || ($decChar >= 0x41 && $decChar <= 0x5A) | ($decChar >= 0x61 && $decChar <= 0x7A) ) // Si le caractère est un nombre ou une majuscule latine ou une minuscule latine
          $newStr .= chr($decChar);  // On l'écrit tel quel
        else {  // Sinon (= c'est un caractère "spécial")
          // On écrit son code hexa entre doubles-croisillons
          $hexChar = dechex($decChar);  
          $newStr .= "#$hexChar#";
        }
      }
      return $str==$newStr?$str:"#U$newStr";   
   }


/**
  Convertit une chaîne contenant un nombre en notaation exponentielle (p. ex. '-4.566e-12') en nombre flotant.
*/   
function exp_to_dec($float_str)
// formats a floating point number string in decimal notation, supports signed floats, also supports non-standard formatting e.g. 0.2e+2 for 20
// e.g. '1.6E+6' to '1600000', '-4.566e-12' to '-0.000000000004566', '+34e+10' to '340000000000'
// Author: Bob
// http://php.net/manual/en/language.types.float.php
{
    // make sure its a standard php float string (i.e. change 0.2e+2 to 20)
    // php will automatically format floats decimally if they are within a certain range
    $float_str = (string)((float)($float_str));

    // if there is an E in the float string
    if(($pos = strpos(strtolower($float_str), 'e')) !== false)
    {
        // get either side of the E, e.g. 1.6E+6 => exp E+6, num 1.6
        $exp = substr($float_str, $pos+1);
        $num = substr($float_str, 0, $pos);
       
        // strip off num sign, if there is one, and leave it off if its + (not required)
        if((($num_sign = $num[0]) === '+') || ($num_sign === '-')) $num = substr($num, 1);
        else $num_sign = '';
        if($num_sign === '+') $num_sign = '';
       
        // strip off exponential sign ('+' or '-' as in 'E+6') if there is one, otherwise throw error, e.g. E+6 => '+'
        if((($exp_sign = $exp[0]) === '+') || ($exp_sign === '-')) $exp = substr($exp, 1);
        else trigger_error("Could not convert exponential notation to decimal notation: invalid float string '$float_str'", E_USER_ERROR);
       
        // get the number of decimal places to the right of the decimal point (or 0 if there is no dec point), e.g., 1.6 => 1
        $right_dec_places = (($dec_pos = strpos($num, '.')) === false) ? 0 : strlen(substr($num, $dec_pos+1));
        // get the number of decimal places to the left of the decimal point (or the length of the entire num if there is no dec point), e.g. 1.6 => 1
        $left_dec_places = ($dec_pos === false) ? strlen($num) : strlen(substr($num, 0, $dec_pos));
       
        // work out number of zeros from exp, exp sign and dec places, e.g. exp 6, exp sign +, dec places 1 => num zeros 5
        if($exp_sign === '+') $num_zeros = $exp - $right_dec_places;
        else $num_zeros = $exp - $left_dec_places;
       
        // build a string with $num_zeros zeros, e.g. '0' 5 times => '00000'
        $zeros = str_pad('', $num_zeros, '0');
       
        // strip decimal from num, e.g. 1.6 => 16
        if($dec_pos !== false) $num = str_replace('.', '', $num);
       
        // if positive exponent, return like 1600000
        if($exp_sign === '+') return $num_sign.$num.$zeros;
        // if negative exponent, return like 0.0000016
        else return $num_sign.'0.'.$zeros.$num;
    }
    // otherwise, assume already in decimal notation and return
    else return $float_str;
}  


  function verbatim($xml) {
		return nl2br(preg_replace('/  /', '&nbsp;&nbsp;', htmlentities($xml)));
  }
  
  function minuscula($str){
	$convert_to = array(
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
    "v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï",
    "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж",
    "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы",
    "ь", "э", "ю", "я"
  );
  $convert_from = array(
    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U",
    "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
    "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж",
    "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ъ",
    "Ь", "Э", "Ю", "Я"
  );
  return str_replace($convert_from, $convert_to, $str); 
}


?>
