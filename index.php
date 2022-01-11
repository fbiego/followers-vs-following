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
	$Followers = [];
	$Following = [];
	$following = array();
	$dif1 = [];
	$dif2 = [];
	$a = 25;
	$b = 25;
	$c = 25;
	$d = 25;
	
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
			
			//foreach ($list as $lg){
			//	array_push($followers, $lg['login'];
			//}
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
		
		array_multisort(array_column($followers, 'login'), SORT_ASC, $followers);
		array_multisort(array_column($following, 'login'), SORT_ASC, $following);
	    
	    foreach($followers as $fl){
	        $Followers[$fl['login']] = $fl['html_url'];
	    }
		
		foreach($following as $fl){
	        $Following[$fl['login']] = $fl['html_url'];
	        if(!array_key_exists($fl['login'], $Followers)){
	            $dif2[$fl['login']] = $fl['html_url'];
	            echo $fl['login'];
	        }
	    }
	    foreach($followers as $fl){
	        if(!array_key_exists($fl['login'], $Following)){
	            $dif1[$fl['login']] = $fl['html_url'];
	        }
	    }
	    
		
		$k = count($following);
		$l = count($followers);
		$m = count($dif1);
		$n = count($dif2);
		
		$total = $k + $l + $m + $n;
		
		if ($total > 0){
			$a = intval(($k / $total) * 100);
			$b = intval(($l / $total) * 100);
			$c = intval(($m / $total) * 100);
			$d = intval(($n / $total) * 100);
		}
	
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
							<div class="w3-container w3-round w3-teal w3-text-white w3-padding-16">
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
						<?php if ($a > 0) { echo'<div class="w3-col w3-container w3-green" style="width:' . $a . '%"> <p> ' . intval($a) . '%</p></div>' ;}?>
						<?php if ($b > 0) { echo'<div class="w3-col w3-container w3-blue" style="width:' . $b . '%"> <p> ' . intval($b) . '%</p></div>' ;}?>
						<?php if ($c > 0) { echo'<div class="w3-col w3-container w3-purple" style="width:' . $c . '%"> <p> ' . intval($c) . '%</p></div>' ;}?>
						<?php if ($d > 0) { echo'<div class="w3-col w3-container w3-teal" style="width:' . $d . '%"> <p> ' . intval($d) . '%</p></div>' ;}?>
					</div>
					
					<div class="w3-row w3-round w3-margin w3-border">
						<button class="w3-button w3-green w3-round-large" onclick="toggle('list');">Show full list</button>
						<button class="w3-button w3-purple w3-round-large" onclick="toggle('dif');">Show Difference</button>
					</div>
				</div>
				<!-- Left Column -->
				<div class="w3-round w3-col" id="dif1" style="width:50%">
					<!-- Profile -->
					<div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
						<p><strong>Users you have not followed back</strong></p>
						<ul class="w3-ul w3-hoverable w3-border w3-round">
							<?php
								foreach ($dif1 as $d => $l)
								{
								    echo "<li class=\"w3-hover-purple\" onclick=\"window.open('";
								    echo $l;
								    echo "', '_blank')\">";
								    echo $d;
								    echo "</li>";
								}
								?>
						</ul>
						<p></p>
					</div>
					<br>
					<!-- End Left Column -->
				</div>
				<div class="w3-round w3-col" id="dif2" style="width:50%">
					<!-- Profile -->
					<div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
						<p><strong>Users not following you</strong></p>
						<ul class="w3-ul w3-hoverable w3-border w3-round">
							<?php
								foreach ($dif2 as $d => $l)	
								{
								    echo "<li class=\"w3-hover-teal\" onclick=\"window.open('";
								    echo $l;
								    echo "', '_blank')\">";
								    echo $d;
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
				
				<!-- Left Column -->
				<div class="w3-round w3-col w3-hide" id="followers" style="width:50%">
					<!-- Profile -->
					<div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
						<p><strong>Followers</strong></p>
						<ul class="w3-ul w3-hoverable w3-border w3-round">
							<?php
								foreach ($followers as $d)
								{
								    echo "<li class=\"w3-hover-purple\" onclick=\"window.open('";
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
				<div class="w3-round w3-col w3-hide" id="following" style="width:50%">
					<!-- Profile -->
					<div class="w3-container w3-margin w3-display-container w3-round w3-border w3-theme-border wl">
						<p><strong>Following</strong></p>
						<ul class="w3-ul w3-hoverable w3-border w3-round">
							<?php
								foreach ($following as $d)	
								{
								    echo "<li class=\"w3-hover-teal\" onclick=\"window.open('";
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
		
		<script>
		function toggle(mode){
			
			let f1= document.querySelector('#followers');
			let f2 = document.querySelector('#following');
			let d1 = document.querySelector('#dif1');
			let d2 = document.querySelector('#dif2');
			if (mode == "list"){
				f1.className = "w3-round w3-col";
				f2.className = "w3-round w3-col";
				d1.className = "w3-round w3-col w3-hide";
				d2.className = "w3-round w3-col w3-hide";
			} else {
				f1.className = "w3-round w3-col w3-hide";
				f2.className = "w3-round w3-col w3-hide";
				d1.className = "w3-round w3-col";
				d2.className = "w3-round w3-col";
			}
		}
	
		</script>
	</body>

</html>
