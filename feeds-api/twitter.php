<?php

/**
 * twitter-timeline-php : Twitter API 1.1 user timeline implemented with PHP, a little JavaScript, and web intents
 * 
 * @package		twitter-timeline-php
 * @author		Kim Maida <contact@kim-maida.com>
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link		http://github.com/kmaida/twitter-timeline-php
 * @credits		Thank you to <http://viralpatel.net/blogs/twitter-like-n-min-sec-ago-timestamp-in-php-mysql/> for base for "time ago" calculations 
 *
**/
############################################################### 
	## SETTINGS
	// Set access tokens <https://dev.twitter.com/apps/>
	$settings = array(
		'consumer_key'              => "y8plJnFDiWzieFL2UXeKumbf3",
		'consumer_secret'           => "1yVyzGUxIbAMcRpU4iglgHJ5Vz4fGRyL8JSscuUlBwuvQWdeud",
		'oauth_access_token'        => "202601214-uTtgCVxSlhWw2nIiIUafFyXb4OoX1Q678qvGimmE",
		'oauth_access_token_secret' => "Hg7vcxak9xcPDSzXPytXHO1vkhpbjOinDWUoUDIqz9VjJ"
	);
	
	// Set API request URL and timeline variables if needed <https://dev.twitter.com/docs/api/1.1>
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	//$twitterUsername = "";
	$tweetCount = 3;
	
	// Use private tokens for development if they exist; delete when no longer necessary
	$tokens = '_utils/tokens.php';
	is_file($tokens) AND include $tokens;
	
	// Require the OAuth class
	require_once('twitter-api-oauth.php');
	
###############################################################
	## MAKE GET REQUEST
	
	$getfield = '?screen_name='  . '&count=' . $tweetCount;
	$twitter = new TwitterAPITimeline($settings);
	
	$json = $twitter->setGetfield($getfield)	// Note: Set the GET field BEFORE calling buildOauth()
				  	->buildOauth($url)
				 	->performRequest();
				 			
	$twitter_data = json_decode($json, true);	// Create an array with the fetched JSON data
	
############################################################### 	
	## DO SOMETHING WITH THE DATA
//-------------------------------------------------------------- Format the time(ago) and date of each tweet

	function timeAgo($dateStr) {
		$timestamp = strtotime($dateStr);	 
		$day = 60 * 60 * 24;
		$today = time(); // current unix time
		$since = $today - $timestamp;
		 # If it's been less than 1 day since the tweet was posted, figure out how long ago in seconds/minutes/hours
		 if (($since / $day) < 1) {
		 
		 	$timeUnits = array(
				   array(60 * 60, 'h'),
				   array(60, 'm'),
				   array(1, 's')
			  );
			  
			  for ($i = 0, $n = count($timeUnits); $i < $n; $i++) { 
				   $seconds = $timeUnits[$i][0];
				   $unit = $timeUnits[$i][1];
			 
				   if (($count = floor($since / $seconds)) != 0) {
					   break;
				   }
			  }
			  return "$count{$unit}";			  
		# If it's been a day or more, return the date: day (without leading 0) and 3-letter month
		 } else {
			  return date('j M', strtotime($dateStr));
		 }	 
	}

//-------------------------------------------------------------- Format the tweet text (links, hashtags, mentions)
	function formatTweet($tweet) {
		$linkified = '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@';
		$hashified = '/(^|[\n\s])#([^\s"\t\n\r<:]*)/is';
		$mentionified = '/(^|[\n\s])@([^\s"\t\n\r<:]*)/is';
		
		$prettyTweet = preg_replace(
			array(
				$linkified,
				$hashified,
				$mentionified
			), 
			array(
				'<a href="$1" class="link-tweet" target="_blank">$1</a>',
				'$1<a class="link-hashtag" href="https://twitter.com/search?q=%23$2&src=hash" target="_blank">#$2</a>',
				'$1<a class="link-mention" href="http://twitter.com/$2" target="_blank">@$2</a>'
			), 
			$tweet
		);
		
		return $prettyTweet;
	}
	
//-------------------------------------------------------------- Timeline HTML output
	# This output markup adheres to the Twitter developer display requirements (https://dev.twitter.com/terms/display-requirements)
	
	# Open the timeline list
?>
	