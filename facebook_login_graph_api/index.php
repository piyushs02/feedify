<?php


require 'src/config.php';
require 'src/facebook.php';

function getHTML($url)
{
       $ch = curl_init($url); // initialize curl with given url
       curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); // max. seconds to execute
       curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
       return @curl_exec($ch);
}

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => $config['App_ID'],
  'secret' => $config['App_Secret'],
  'cookie' => true
));

if(isset($_GET['logout']))       
{
    $url = 'https://www.facebook.com/logout.php?next=' . urlencode('http://feedify.co.in/facebook_login_graph_api/') .
      '&access_token='.$_GET['tocken'];
    session_destroy();
    header('Location: '.$url);
}
if(isset($_GET['fbTrue']))
{
    $token_url = "https://graph.facebook.com/oauth/access_token?"
       . "client_id=".$config['App_ID']."&redirect_uri=" . urlencode($config['callback_url'])
       . "&client_secret=".$config['App_Secret']."&code=" . $_GET['code']; 

     $response = getHTML($token_url);
     $params = null;
     parse_str($response, $params);

     $graph_url = "https://graph.facebook.com/me?fields=name,friends&access_token=" 
       . $params['access_token'];

     $user = json_decode(getHTML($graph_url));
     $extra = "<a href='index.php?logout=1&tocken=".$params['access_token']."'><img src='./images/logout-button.png' alt='Sign Out'/></a><br>";     
     $content = $user->name;
}
else
{
    $content = '<a href="https://www.facebook.com/dialog/oauth?client_id='.$config['App_ID'].'&redirect_uri='.$config['callback_url'].'&scope=email,user_likes,user_friends"><img src="./images/login-button.png" alt="Sign in with Facebook"/></a>';
}

//include('html.inc');
print_r($content);
echo $extra;
