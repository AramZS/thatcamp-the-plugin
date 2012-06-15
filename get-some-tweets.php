<?php

//Based on previous work by myself and others, best seen at https://github.com/AramZS/twitter-search-shortcode

//Establish date variable.
$now=getdate();
//$date="2010-02-11T05:59:21Z";
$date=$now["year"];
//$date="2010";


function get_some_link_relevent_tweets($postID, $link) {

	//Add our needed post metas if they are not there. 
	//The parameters are outlined in - http://codex.wordpress.org/Function_Reference/add_post_meta
	//We associate the meta with a post ID (required), give it a name (aka key), a value (empty for now), and say if it is unique. 
	add_post_meta($postID, 'related_tweet_one', '', true);
	add_post_meta($postID, 'related_tweet_two', '', true);
	
	//Now we don't want to waste the server's time with retrieving new tweets every time the page loads, so lets check the first field
	//We'll make sure that we haven't done this bit already. 
	$check = get_post_meta($postID, 'related_tweet_one', true);
	
	if (empty($check)){
		
		//The URL of the RSS of a search query
		$twitterSearchRSS = 'http://search.twitter.com/search.atom?q=';
		
		//Convert the link into something that can be fed into a query (turn '/' into '%2F' and all that.
		$searchReadyLink = urlencode($link);
		
		//Limit the number of items in the RSS feed to 2, since that's all we will be using. 
		$itemsPerPage = '&rpp=2';
		//Note, you could turn that 2 into a variable set by the user and allow them to pull and display as many as 100 tweets. 
		//Though you'd have to change more than just this. 
		
		//Combine all the elements. 
		$twitterSearch = $twitterSearchRSS . $searchReadyLink . $itemsPerPage;			
		
		$file = $twitterSearch;
		if (fopen ($file, "r")) {
			$xml = simplexml_load_file($file);
			if (!(empty($xml->entry))){
						
				//Oddly, that XML we took in is not yet an array. So let's turn it into one.
				//This will go through each entry in the RSS file and turn it into an array within a greater array.
				foreach($xml->entry as $entry)
				{
					$xmlarray[]=$entry;
				}
				
				//We are going to count how many times we go through the next loop.
				$c = 1;

				//Now we want to take each tweet, itself an array, and pull data out of it. 
				foreach($xmlarray as $tweet) {
					
						//The link is stored in the array as the first array item named 'link'
						//We want, the 'href' data, where the actual link is stored.
						//And we need to cast it as a string so it will display.
						//For more on casting as a string see http://stackoverflow.com/questions/2867575/get-value-from-simplexmlelement-object
						$link = (string) $tweet->link[0]['href'];

						//Ok, so here's where it gets complex... If you are running WP3.4, there is now an awesome oEmbed function that rendders tweets for us. 
						//See http://codex.wordpress.org/Embeds and http://core.trac.wordpress.org/browser/tags/3.4/wp-includes/media.php#L0 for more info.
						//But if you are running a WP version before that, no-go. 
						//So let's use this new function, but create a fallback. 
						//Remember we got the WordPress version before?
						//Well, to provide forward compatability, we need to convert it to a number.
						//Using this PHP function allows us to turn the string into a decimal number PHP understands. 
						//Let's get the current WordPress version. 
						$wpver = get_bloginfo('version');
						//Should get a string like '3.4' - for more info see http://core.trac.wordpress.org/browser/tags/3.4/wp-includes/version.php#L0						
						$floatWPVer = floatval($wpver);

						//Now, we check if the version of WordPress we are running is equal to or greater than 3.4.
						if ($floatWPVer >= 3.4){
							
							$output = $link;
							if ($c == 1) {

								update_post_meta($postID, 'related_tweet_one', $output);
							
							}
							if ($c == 2) {
							
								update_post_meta($postID, 'related_tweet_two', $output);
							
							}
							
						
						} else {
						
							//Ok, now if it isn't WP3.4, we're going to have to render the tweets on our own. 
							//Let's grab all the info we need. 
							$uri=$tweet->author->uri;
							$name=$tweet->author->name;
							$image=$tweet->link[1]['href'];
							$timestamp = $tweet->published;
							$link=$tweet->link[0]['href'];
							$unixtime = strtotime($timestamp);
							$datetime = date('h:i:s A, n-j-y', $unixtime);
							
							$output = "<div class=\"ta-twitter_user ta\">
								<ul class=\"ta-ul\">
								<li class=\"ta-image ta\"><img class=\"ta-avatar ta\" src=\"$image\"></li>
								<li class=\"ta-published ta\"><a href=\"$link\">$datetime</a></li>
								<li class=\"ta-user ta\"><a href=\"$uri\" target=\"_blank\">$name</a></li>
								<li class=\"ta-description ta\">$tweet->title</li>
								
								</ul>
								</div>";
								
							if ($c == 1) {
							
								update_post_meta($postID, 'related_tweet_one', $output);
							
							}
							if ($c == 2) {
							
								update_post_meta($postID, 'related_tweet_two', $output);
							
							}
							
						}
					$c++; //Increment $c by 1. This lets us count how many times we've been through the loop.
				}
			}
				
		}
		else 
		{
			$execute_archive .= " Sorry, Twitter doesn't seem to be working right now. Try again later.";
		}
	
	}


}

?>