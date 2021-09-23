<?php
	$message = "";
	$limit = 60;
	$used = 0;
	$user = "github username";
	
	function getHeaders($curl, $header_line)
	{
	    //$GLOBALS['message'] = $GLOBALS['message']. $header_line . "<br>";
	    if (strpos($header_line, "X-RateLimit-Limit:") !== false)
	    {
	        $GLOBALS['limit'] = (int)preg_replace('/[^0-9]/', '', $header_line);
	    }
	    if (strpos($header_line, "X-RateLimit-Used:") !== false)
	    {
	        $GLOBALS['used'] = (int)preg_replace('/[^0-9]/', '', $header_line);
	    }
	    return strlen($header_line);
	}
	function getUsers($username, $type, $page)
	{
	    $cURLConnection = curl_init();
	
	    curl_setopt($cURLConnection, CURLOPT_URL, 'https://api.github.com/users/' . $username . '/' . $type . '?per_page=100&page=' . $page);
	    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($cURLConnection, CURLOPT_HEADERFUNCTION, "getHeaders");
	    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
	        'Accept: application/vnd.github.v3+json',
	        'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Mobile Safari/537.36'
	    ));
	
	    $json = curl_exec($cURLConnection);
	    curl_close($cURLConnection);
	    $obj = json_decode($json);
	    $GLOBALS['message'] = $GLOBALS['message'] . $obj['message'];
	
	    return $json;
	}
	
	$followers = array();
	$following = array();
	$dif1 = array();
	$dif2 = array();
	
	if ($_POST['user'])
	{
	    $user = $_POST['user'];
	
	    //$followers = json_decode(getUsers($user, "followers", 1), true);
	    //$following = json_decode(getUsers($user, "following", 1), true);
	    //query followers
	    $z = 1;
	    while ($z <= 30)
	    {
	        $list = json_decode(getUsers($user, "followers", $z) , true);
	        if (count($list) == 0)
	        {
	            break;
	        }
	        if ($message != "")
	        {
	            break;
	        }
	        $followers = array_merge($list, $followers);
	        $z++;
	    }
	
	    //query following
	    $z = 1;
	    while ($z <= 30)
	    {
	        $list = json_decode(getUsers($user, "following", $z) , true);
	        if (count($list) == 0)
	        {
	            break;
	        }
	        if ($message != "")
	        {
	            break;
	        }
	        $following = array_merge($list, $following);
	        $z++;
	    }
	
	    $dif1 = array_diff_assoc($followers, $following);
	    $dif2 = array_diff_assoc($following, $followers);
	
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
					<p>X-RateLimit Usage: <?php echo $used; ?> Used, <?php echo ($limit - $used); ?> Remaining, <?php echo $limit; ?> Total</p>
					<div class="w3-light-grey w3-round-large">
						<div class="w3-indigo w3-round-large w3-center" style="width:<?php echo intval(($used / $limit) * 100); ?>%"><?php echo intval(($used / $limit) * 100); ?>%</div>
					</div>
					<p>
						<?php echo $message; ?>
					</p>
					<div class="w3-row-padding w3-margin-bottom">
						<div class="w3-quarter">
							<div class="w3-container w3-round w3-green w3-padding-16">
								<div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
								<div class="w3-right">
									<h3><?php echo count($following); ?></h3>
								</div>
								<div class="w3-clear"></div>
								<h5>Following</h5>
							</div>
						</div>
						<div class="w3-quarter">
							<div class="w3-container w3-round w3-blue w3-padding-16">
								<div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
								<div class="w3-right">
									<h3><?php echo count($followers); ?></h3>
								</div>
								<div class="w3-clear"></div>
								<h5>Followers</h5>
							</div>
						</div>
						<div class="w3-quarter">
							<div class="w3-container w3-round w3-purple w3-padding-16">
								<div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
								<div class="w3-right">
									<h3><?php echo count($dif1); ?></h3>
								</div>
								<div class="w3-clear"></div>
								<h5>Not followed back</h5>
							</div>
						</div>
						<div class="w3-quarter">
							<div class="w3-container w3-round w3-orange w3-text-white w3-padding-16">
								<div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
								<div class="w3-right">
									<h3><?php echo count($dif2); ?></h3>
								</div>
								<div class="w3-clear"></div>
								<h5>Not following you</h5>
							</div>
						</div>
					</div>
					<div class="w3-row w3-round w3-margin w3-border">
						<div class="w3-col w3-container w3-green" style="width:20%">
							<p>w3-threequarter</p>
						</div>
						<div class="w3-col w3-container w3-blue" style="width:70%">
							<p>w3-quarter</p>
						</div>
						<div class="w3-col w3-container w3-purple" style="width:70%">
							<p>w3-quarter</p>
						</div>
						<div class="w3-col w3-container w3-orange" style="width:70%">
							<p>w3-quarter</p>
						</div>
					</div>
				</div>
				<!-- Left Column -->
				<div class="w3-round w3-col" style="width:50%">
					<!-- Profile -->
					<div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
						<p><strong>Users you have not followed back</strong></p>
						<ul class="w3-ul w3-hoverable w3-border w3-round">
							<?php
								foreach ($dif1 as $d)
								{
								    echo "<li class=\"w3-hover-orange\" onclick=\"window.open('";
								    echo $d['html_url'];
								    echo "', '_blank')\">";
								    echo $d['login'];
								    echo "</li>";
								}
								?>
						</ul>
						<p></p>
					</div>
					<br>
					<!-- End Left Column -->
				</div>
				<div class="w3-round w3-col" style="width:50%">
					<!-- Profile -->
					<div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
						<p><strong>Users not following you</strong></p>
						<ul class="w3-ul w3-hoverable w3-border w3-round">
							<?php
								foreach ($dif2 as $d)
								{
								    echo "<li class=\"w3-hover-orange\" onclick=\"window.open('";
								    echo $d['html_url'];
								    echo "', '_blank')\">";
								    echo $d['login'];
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