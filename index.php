<!doctype HTML>
<html lang="en">
<head>
</head>
<body>
<style>
	body{
		font-family:tahoma;
		margin:0;
		padding:0;
	}
	a{
		text-decoration:none;
		color:#0099B9;
		cursor:pointer;
	}
	ul{
		margin:0;
		padding:0;
	}
	ul li{
		list-style:none;
		background:#eee;
		padding:10px;
		margin-bottom:20px;
		width:400px;
		font-size:10pt;
	}
</style>
<?php include_once('feeds-api/twitter.php'); ?>
<ul>
<?php
	foreach ($twitter_data as $tweet) {
		//$retweet = $tweet['retweeted_status'];
		$isRetweet                = !empty($retweet);
		# Retweet - get the retweeter's name and screen name
		$retweetingUser           = $isRetweet ? $tweet['user']['name'] : null;
		$retweetingUserScreenName = $isRetweet ? $tweet['user']['screen_name'] : null;
		# Tweet source user (could be a retweeted user and not the owner of the timeline)
		$user           = !$isRetweet ? $tweet['user'] : $retweet['user'];	
		$userName       = $user['name'];
		$userScreenName = $user['screen_name'];
		$userAvatarURL  = stripcslashes($user['profile_image_url']);
		$userAccountURL = 'http://twitter.com/' . $userScreenName;
		# The tweet
		$id              = $tweet['id'];
		$formattedTweet = !$isRetweet ? formatTweet($tweet['text']) : formatTweet($retweet['text']);
		$statusURL   = 'http://twitter.com/' . $userScreenName . '/status/' . $id;
		$date        = timeAgo($tweet['created_at']);
		# Reply
		$replyID     = $tweet['in_reply_to_status_id'];
		$isReply     = !empty($replyID);
		# Tweet actions (uses web intents)
		$replyURL    = 'https://twitter.com/intent/tweet?in_reply_to=' . $id;
		$retweetURL  = 'https://twitter.com/intent/retweet?tweet_id=' . $id;
		$favoriteURL = 'https://twitter.com/intent/favorite?tweet_id=' . $id;	
?>
				<!--HERE'S THE RESULT-->
				<li >
					<a  href="<?php echo $userAccountURL; ?>"><img  src="<?php echo $userAvatarURL; ?>"></a></br>
					UserName    : <a  href="<?php echo $userAccountURL; ?>"><?php echo $userName; ?></a> , 
								  <a  href="<?php echo $userAccountURL; ?>">@<?php echo $userScreenName; ?></a></br>

					Date Posted : <a href="<?php echo $statusURL; ?>" target="_blank"><?php echo $date; ?></a></br>
						
					Text 		:<?php echo $formattedTweet ; ?></br>		
							     <a  href=" <?php echo $statusURL; ?>" target="_blank">Details</a></p>			
				</li>	
				<!-- END RESULT	 -->
    <?php 
	}	
	?>
</ul>
</body>
</html>