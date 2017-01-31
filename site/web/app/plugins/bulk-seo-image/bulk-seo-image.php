<?php
/*
Plugin Name: BULK SEO Image
Plugin URI: http://www.seoelite.pw
Description: Easily update all the ALT attribute of your images. Can add the Article Name automatically to all your images improving traffic from search engines.
Author: SEOelite
Version: 1.1
Author URI: http://www.seoelite.pw
*/
function BulkSeoImage() {
	if(isset($_POST['bulkseoimage'])){
		launchbulk(); 
	}
	?>
	<div class="wrap">
	<h2>BULK SEO Image (Configuration)</h2>
	<a href="http://www.a2hosting.com/5787-19-1-182.html" target="_blank"><img style="border:0px" src="https://affiliates.a2hosting.com/media/banners/1-CLICK468X60 - wp.png" width="468" height="60" alt=""></a>
	<form method="post" id="BulkSeoImage">
			<h2>General Options</h2>
<p>BULK SEO Image will automatically update ALT attributes to all your images in all your posts/pages specified by parameters below.</p>
<br><b>New option</b> :  you want to update... Post+Page <input type="radio" name="option1" value="3"> 
										- Post <input type="radio" name="option1" value="1" checked> 
										- Page <input type="radio" name="option1" value="2"> 
					<br><br><u>Example </u>: <span style="color:green">%PostName%</span> : My Post, <span style="color:blue">%CurrentSiteName%</span> : <?php echo get_bloginfo('name'); ?>, <span style="color:orange">$i</span> : Image Number
					<br><table border="1">
						<tr><td><b>Choice</b></td><td><b>Example ALT Attribute Value</b></td></tr>
						<tr><td><input type="radio" name="choice" value="1"> <span style="color:green">%PostName%</span></td><td>My Post</td></tr>
						<tr><td><input type="radio" name="choice" value="2"> <span style="color:blue">%CurrentSiteName%</span> <span style="color:green">%PostName%</span></td><td><?php echo get_bloginfo('name'); ?> My Post</td></tr>
						<tr><td><input type="radio" name="choice" value="3"> <span style="color:green">%PostName%</span> <span style="color:blue">%CurrentSiteName%</span> </td><td>My Post <?php echo get_bloginfo('name'); ?></td></tr>
						<tr><td><input type="radio" name="choice" value="4"> <span style="color:blue">%CurrentSiteName%</span> | <span style="color:green">%PostName%</span></td><td><?php echo get_bloginfo('name'); ?> | My Post</td></tr>
						<tr><td><input type="radio" name="choice" value="5"> <span style="color:green">%PostName%</span> | <span style="color:blue">%CurrentSiteName%</span> </td><td>My Post | <?php echo get_bloginfo('name'); ?></td></tr>
						<tr><td colspan="2"><b>If more than 1 image for a same post</b></td></tr>
						<tr><td><input type="radio" name="choice" value="14" checked> <span style="color:green">%PostName% <span style="color:orange">$i</span></span></td><td>My Post 1,My Post 2,...</td></tr>
						<tr><td><input type="radio" name="choice" value="6"> <span style="color:blue">%CurrentSiteName%</span> <span style="color:green">%PostName%</span> <span style="color:orange">$i</span></td><td><?php echo get_bloginfo('name'); ?> My Post 1,<?php echo get_bloginfo('name'); ?> My Post 2,...</td></tr>
						<tr><td><input type="radio" name="choice" value="7"> <span style="color:green">%PostName%</span> <span style="color:blue">%CurrentSiteName%</span> <span style="color:orange">$i</span></td><td>My Post <?php echo get_bloginfo('name'); ?> 1,My Post <?php echo get_bloginfo('name'); ?> 2,...</td></tr>
						<tr><td><input type="radio" name="choice" value="8"> <span style="color:blue">%CurrentSiteName%</span> | <span style="color:green">%PostName%</span> <span style="color:orange">$i</span></td><td><?php echo get_bloginfo('name'); ?> | My Post 1,<?php echo get_bloginfo('name'); ?> | My Post 2,...</td></tr>
						<tr><td><input type="radio" name="choice" value="9"> <span style="color:green">%PostName%</span> | <span style="color:blue">%CurrentSiteName%</span> <span style="color:orange">$i</span></td><td>My Post | <?php echo get_bloginfo('name'); ?> 1,My Post | <?php echo get_bloginfo('name'); ?> 2,...</td></tr>
						<tr><td><input type="radio" name="choice" value="10"> <span style="color:blue">%CurrentSiteName%</span> <span style="color:green">%PostName%</span>  <span style="color:orange">image $i</span></td><td><?php echo get_bloginfo('name'); ?> My Post image 1,<?php echo get_bloginfo('name'); ?> My Post image 2,...</td></tr>
						<tr><td><input type="radio" name="choice" value="11"> <span style="color:green">%PostName%</span> <span style="color:blue">%CurrentSiteName%</span> <span style="color:orange">image $i</span></td><td>My Post <?php echo get_bloginfo('name'); ?> image 1,My Post <?php echo get_bloginfo('name'); ?> image 2,...</td></tr>
						<tr><td><input type="radio" name="choice" value="12"> <span style="color:blue">%CurrentSiteName%</span> | <span style="color:green">%PostName%</span> <span style="color:orange">image $i</span></td><td><?php echo get_bloginfo('name'); ?> | My Post image 1,<?php echo get_bloginfo('name'); ?> | My Post image 2,...</td></tr>
						<tr><td><input type="radio" name="choice" value="13"> <span style="color:green">%PostName%</span> | <span style="color:blue">%CurrentSiteName%</span> <span style="color:orange">image $i</span></td><td>My Post | <?php echo get_bloginfo('name'); ?> image 1,My Post | <?php echo get_bloginfo('name'); ?> image 2,...</td></tr>
						<tr><td colspan="2"></td></tr>
					</table>
					<br><input id="check1" type="checkbox" name="override" checked />Override default Wordpress image alt tag (recommended) 
					
					<br><strong>Details</strong>
					<ol>
						<li>Please, make a backup of your wordpress database before lauching the script</li>
						<li>The script will scan only published post (and not draft,future,page...) </li>
						<li>The script has been tested and works with wordpress sites that have up to 500 items and 500 pictures. But that does not mean that it can not work for more items / images.</li>
						<li>You can run the script as many times as you like. Each time, the previous ALT attribute values will be overwritten</i>
						<li>If 2 posts use the same image, the script will owerwrite the first ALT Attribute Value, and display only the second ALT Attribute Value. For example, the post <i>My Post 1</i> and <i>My Post 2</i> use the same image <i>IMAGE</i>, the ALT attribute will be only for <i>My Post 2</i> (depending of your precedent choice)</li>
						<li>This plugin is not executed at every webpage load of your website (otherwise, it may affect on site load speed).  We believe that the website speed is highly important</li>						
						<li>This plugin doesn't update your post content (so the alt image in the article can be different than the alt image in the media library)</li>
					</ol>
					<br><input type="submit" name="bulkseoimage" value="Launch Bulk SEO Image" />

	</form>
	
<br><hr>
You are a webdesigner ? Help us to improve the design of this plugin (<a href="http://www.seoelite.pw/" target="_blank">contact</a>)
<br>Do you like this plugin ? why not make a (small) donation. Thank you !!
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="TCXXBFZEAZW5C">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG_global.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
</form>
<hr>
<a href="http://www.a2hosting.com/5787-19-1-184.html" target="_blank"><img style="border:0px" src="https://affiliates.a2hosting.com/media/banners/234X60 - wp.png" width="234" height="60" alt=""></a>
	</div>
	<?php

}

function launchbulk() {
global $wpdb;

set_time_limit(0);
ini_set('output_buffering', 0);
ini_set('implicit_flush', 1);
try { while( @ob_end_flush() ); } catch( Exception $e ) {} 
ob_start();
	
$choice=(int)$_POST['choice'];
$option1=(int)$_POST['option1'];

switch($option1){
	case 1:  	BulkSeoImageGo('post',$choice);   break; 
	case 2:  	BulkSeoImageGo('page',$choice);   break; 
	case 3:  	BulkSeoImageGo('post',$choice);  BulkSeoImageGo('page',$choice); break; 
}



}

function dashboardBulkSeoImage() {
	if (function_exists('add_options_page')) {
	add_options_page('Bulk SEO Image', 'Bulk SEO Image', 'manage_options', basename(__FILE__), 'BulkSeoImage');
	}
}

	function BulkSeoImageGo($param,$choice){
		global $wpdb;
		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}posts` WHERE post_status = 'publish' AND post_type='".$param."'");
		if (empty ($results)) {
			return false;
		} else {	
				echo '<h1>Result ('.$param.')</h1><table border="1">
					<tr><td><b>(id) Article</b></td><td><b>OLD ALT</b></td><td><b>NEW ALT</b></td></tr>';	ob_flush(); flush();
				
				foreach ($results as $result) { 		
						$attachments = get_attached_media('image', $result->ID );
						$countarray=count($attachments);
						$i=1;
						foreach ($attachments as $attachment) {
							echo '<tr><td><b>('.$result->ID.') '.$result->post_title.'</b></td><td>';  ob_flush(); flush();
							$alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
							echo $alt;  ob_flush(); flush();
							if($countarray>1){
								if($choice>9 AND $choice != 14) { $ii='image '.$i; }else{ $ii=$i;} 
							}else{
								$ii='';
							}				
							switch($choice){
								case 1:  	$text_alt_display =  $result->post_title;   break; 
								case 14:  	$text_alt_display =  $result->post_title.' '.$ii;   break; 
								case 2:  	$text_alt_display =  get_bloginfo('name').' '.$result->post_title;   break;
								case 3:  	$text_alt_display =  $result->post_title.' '.get_bloginfo('name');   break;
								case 4:  	$text_alt_display =  get_bloginfo('name').' | '.$result->post_title;   break;
								case 5:  	$text_alt_display =  $result->post_title.' | '.get_bloginfo('name');   break;						
								case 6:		$text_alt_display =  get_bloginfo('name').' '.$result->post_title.' '.$ii;   break;
								case 10:  	$text_alt_display =  get_bloginfo('name').' '.$result->post_title.' '.$ii;   break;
								case 7:		$text_alt_display =  $result->post_title.' '.get_bloginfo('name').' '.$ii;   break;
								case 11:  	$text_alt_display =  $result->post_title.' '.get_bloginfo('name').' '.$ii;   break;
								case 8:		$text_alt_display =  get_bloginfo('name').' | '.$result->post_title.' '.$ii;   break;
								case 12:	$text_alt_display =  get_bloginfo('name').' | '.$result->post_title.' '.$ii;   break;
								case 9:		$text_alt_display =  $result->post_title.' | '.get_bloginfo('name').' '.$ii;   break;			
								case 13:	$text_alt_display =  $result->post_title.' | '.get_bloginfo('name').' '.$ii;   break;	
								default :   $text_alt_display =  $result->post_title;   break; 
							}

							if(!empty($alt) AND $_POST['override']!=TRUE){ 
								$text_alt_display = '-';
							}else{
								update_post_meta($attachment->ID, '_wp_attachment_image_alt',$text_alt_display);
							}
							echo '</td><td>'.$text_alt_display.'</td></tr>';  ob_flush(); flush();
							++$i;
						}	
				}
				echo '</table>'; ob_flush(); flush();
			}
		}
		
add_action('admin_menu','dashboardBulkSeoImage');

?>
