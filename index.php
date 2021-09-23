<?php
$message = "";
$limit = 1;
$used = 0;

function getHeaders($curl, $header_line ){
	$GLOBALS['message'] = $GLOBALS['message']. $header_line . "<br>";
	if (strpos($header_line, "X-RateLimit-Limit:") !== false){
		$GLOBALS['limit'] = (int) preg_replace('/[^0-9]/', '', $header_line);
	}
	if (strpos($header_line, "X-RateLimit-Used:") !== false){
		$GLOBALS['used'] = (int) preg_replace('/[^0-9]/', '', $header_line);
	}
	return strlen($header_line);
}
function getUsers($username, $type)
{
    $cURLConnection = curl_init();

    curl_setopt($cURLConnection, CURLOPT_URL, 'https://api.github.com/users/' . $username . '/' . $type . '?per_page=100&page=1');
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cURLConnection, CURLOPT_HEADERFUNCTION, "getHeaders");
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.github.v3+json',
        'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Mobile Safari/537.36'
    ));

    $json = curl_exec($cURLConnection);
    curl_close($cURLConnection);
    $obj = json_decode($json);
	$GLOBALS['message'] = $GLOBALS['message'].$obj['message'];
    
    return $json;
}

$user = "github username";

if ($_POST['user'])
{
    $user = $_POST['user'];
    $followers = getUsers($user, "followers");
    $following = getUsers($user, "following");
}
?>

<!DOCTYPE html>
<html>
<title>Github Followers vs Following</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue-grey.css">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html, body, h1, h2, h3, h4, h5 {font-family: "Open Sans", sans-serif}
</style>
<body >

<!-- Navbar -->
<div class="w3-top">
<form method="POST">
 <div class="w3-bar w3-blue w3-left-align w3-large">
 
  <div class="w3-bar-item w3-padding"><input class="w3-input w3-round" name="user" type="text" placeholder="<?php echo $user; ?>" required></div>
<div class="w3-bar-item w3-padding"> <button class="w3-btn w3-blue"><i class="fa fa-search w3-margin-right"></i>Search</button></div>
 </div>
 </form>
</div>

<!-- Page Container -->
<div class="w3-content" style="max-width:1400px;margin-top:50px;margin-bottom:100px">    
  <!-- The Grid -->
  <div class="w3-row">
		<div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
		<p>X-RateLimit Usage: <?php echo $used; ?> Used, <?php echo ($limit-$used); ?> Remaining, <?php echo $limit; ?> Total</p>
		<div class="w3-light-grey w3-round-large">
			<div class="w3-blue w3-round-large w3-center" style="width:<?php echo ($used/$limit)*100; ?>%"><?php echo ($used/$limit)*100; ?>%</div>
		</div>
        <p><?php echo $message; ?></p>
      </div>
    <!-- Left Column -->
    <div class="w3-col m6 w3-round">
      <!-- Profile -->
      <div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
        <p><strong>Following</strong></p>
		<ul class="w3-ul w3-hoverable w3-border w3-round">
		<?php
			$obj = json_decode($following);
			// echo $json;
			foreach ($obj as $d)
			{
			    echo "<li class=\"w3-hover-blue\" onclick=\"location.href='";
				echo $d->html_url;
				echo "';\">";
			    echo $d->login;
			    echo "</li>";
			}
		?>
		</ul>
		<p></p>
      </div>
	  <br>

    <!-- End Left Column -->
    </div>
    <div class="w3-cell w3-round">
      <!-- Profile -->
	  
	  <div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
        <p><strong>Followers</strong></p>
		<ul class="w3-ul w3-hoverable w3-border w3-round">
		    <?php
			$obj = json_decode($followers);
			// echo $json;
			foreach ($obj as $d)
			{
			    echo "<li class=\"w3-hover-blue\" onclick=\"location.href='";
				echo $d->html_url;
				echo "';\">";
			    echo $d->login;
			    echo "</li>";
			}
		?>
		</ul>
		<p></p>
      </div>
	  <br>
      
    <!-- End Left Column -->
    </div>
    
  <!-- End Grid -->
  </div>
  
<!-- End Page Container -->
</div>
<br>



</body>
</html>
