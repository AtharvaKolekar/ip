<?php
// Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }

function getUserIP(){
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}


$user_agent = $_SERVER['HTTP_USER_AGENT'];
$origin=null;/////////////////
if(isset($_SERVER['HTTP_REFERER'])) {
    $origin = $_SERVER['HTTP_REFERER'];     
}

$pos1 = strpos($user_agent, '(')+1;
$pos2 = strpos($user_agent, ')')-$pos1;
$part = substr($user_agent, $pos1, $pos2);
$parts = explode(" ", $part);
$p1=null; $p2=null; $p3=null;$p4=null;

if(isset($parts[2])){
    $p1 = $parts[2];
}
if(isset($parts[3])){
    $p2 = $parts[3];
}
if(isset($parts[4])){
    $p3 = $parts[4];
}
if(isset($parts[6])){
    $p4 = $parts[6];
} 

$device_name = $p2.' '.$p3;

function getOS() { 

    global $user_agent;
    $os_platform  = "Unknown OS Platform";
    $os_array     = array(
                          '/windows nt 10/i'      =>  'Windows 10',
                          '/windows nt 6.3/i'     =>  'Windows 8.1',
                          '/windows nt 6.2/i'     =>  'Windows 8',
                          '/windows nt 6.1/i'     =>  'Windows 7',
                          '/windows nt 6.0/i'     =>  'Windows Vista',
                          '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                          '/windows nt 5.1/i'     =>  'Windows XP',
                          '/windows xp/i'         =>  'Windows XP',
                          '/windows nt 5.0/i'     =>  'Windows 2000',
                          '/windows me/i'         =>  'Windows ME',
                          '/win98/i'              =>  'Windows 98',
                          '/win95/i'              =>  'Windows 95',
                          '/win16/i'              =>  'Windows 3.11',
                          '/macintosh|mac os x/i' =>  'Mac OS X',
                          '/mac_powerpc/i'        =>  'Mac OS 9',
                          '/linux/i'              =>  'Linux',
                          '/ubuntu/i'             =>  'Ubuntu',
                          '/iphone/i'             =>  'iPhone',
                          '/ipod/i'               =>  'iPod',
                          '/ipad/i'               =>  'iPad',
                          '/android 12/i'         =>  'Android 12',
                          '/android 11/i'         =>  'Android 11',
                          '/android 10/i'         =>  'Android 10',
                          '/android 9/i'          =>  'Android 9',
                          '/android 8/i'          =>  'Android 8',
                          '/android 7/i'          =>  'Android 7',
                          '/blackberry/i'         =>  'BlackBerry',
                          '/webos/i'              =>  'Mobile'
                    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}

function getBrowser() {
    global $user_agent;
    $browser        = "Unknown Browser";
    $browser_array = array(
                            '/msie/i'      => 'Internet Explorer',
                            '/firefox/i'   => 'Firefox',
                            '/safari/i'    => 'Safari',
                            '/chrome/i'    => 'Chrome',
                            '/edge/i'      => 'Edge',
                            '/opera/i'     => 'Opera',
                            '/netscape/i'  => 'Netscape',
                            '/maxthon/i'   => 'Maxthon',
                            '/konqueror/i' => 'Konqueror',
                           
                     );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $browser = $value;
    return $browser;
}

function getBB()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
   
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

$user_ip        = getUserIP();
$user_os        = getOS();
$user_browser   = getBrowser();

$ua=getBB();
$vv = $ua['version'];
$vv = explode(".",$vv);

$jsondata1 = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$user_ip)));
$date = new DateTime("now", new DateTimeZone($jsondata1['geoplugin_timezone']) );
$ccode = strtolower( $jsondata1['geoplugin_countryCode']).'.png';
$isMobile="false";
function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
if(isMobile()){
    $isMobile="true";
}

$device_details = array(
    "ip"=> $user_ip , 
    "os"=> $user_os , 
    "browser"=> $user_browser, 
    "version"=> $vv[0], 
    "origin"=> $origin, 
    "device"=> $device_name,
    "unix"=> ''.time().'', 
    "date"=> $date->format('d/m/Y'),
    "time"=> $date->format('H:i:s'),
    "H"=> $date->format('H'),
    "h"=> $date->format('h'),
    "i"=> $date->format('i'),
    "s"=> $date->format('s'),
    "a"=> $date->format('a'),
    "Y"=> $date->format('Y'),
    "m"=> $date->format('m'),
    "d"=> $date->format('d'),
    "day"=> $date->format('l'),
    "city"=> $jsondata1['geoplugin_city'], 
    "region"=> $jsondata1['geoplugin_region'], 
    "regionCode"=> $jsondata1['geoplugin_regionCode'], 
    "country"=> $jsondata1['geoplugin_countryName'], 
    "countryCode"=> $jsondata1['geoplugin_countryCode'], 
    "lFlag"=> "https://flagcdn.com/128x96/".$ccode,
    "mFlag"=> "https://flagcdn.com/64x48/".$ccode,
    "sFlag"=> "https://flagcdn.com/32x24/".$ccode,
    "continent"=> $jsondata1['geoplugin_continentName'], 
    "timezone"=> $jsondata1['geoplugin_timezone'], 
    "latitude"=> $jsondata1['geoplugin_latitude'], 
    "longitude"=> $jsondata1['geoplugin_longitude'], 
    "currencySymbol"=> $jsondata1['geoplugin_currencySymbol'],
    "isMobile" =>     $isMobile,

);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($device_details, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

?>
