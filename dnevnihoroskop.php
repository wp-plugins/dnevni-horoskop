<?php
/*
Plugin Name: Dnevni Horoskop
Plugin URI: http://dev.horoskopius.com/wordpress-plugin/
Description: Vrlo lagan i jednostavan plugin za vaš Wordpress blog, gde preko Widgeta ili Short kodom u postovima ili stranicama možete prikazivati Dnevni horoskop sa sajta Horoskopius.com.
Version: 4.0
Author: Horoskopius web team
Author URI: http://www.horoskopius.com
License: GPL3
 */
 
/**
 *     Dnevni Horoskop Wordpress plugin
 *     Copyright (C) 2012  www.horoskopius.com
 *     http://dev.horoskopius.com/
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */	

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

include_once dirname( __FILE__ ) . '/sdksmall.php';

if ( version_compare( $wp_version, '3.0', '<' ) ) {
        
        function hordaily_version_warning() {
            echo "
            <div id='horoskop-warning' class='updated fade'><p><strong>".sprintf(__('Dnevni horoskop %s zahteva WordPress 3.0 ili višu verziju.'),'plugin') ."</strong> ".sprintf(__('Molimo Vas <a href="%s">ažurirajte WordPress</a>.'), 'http://codex.wordpress.org/Upgrading_WordPress'). "</p></div>
            ";
        }
        add_action('admin_notices', 'hordaily_version_warning'); 
        
        return; 
    }

function hordailyshow()
{
	global $wpdb;
	@$hor_private_key =  'jfk++9OJRvQfo5J54HZhEh1kGPs=';
	@$hor_public_key = 'MmQyOTE3Y2RjMjJhZjI3YmIzOTNhNzM0NDE3OWU3NWVjYjMwNTdiYg==';
	@$hor_daily_alphabet = get_option('hordaily_alphabet');
	@$hor_link = get_option('hordaily_link');

	$horoskop = new HoroskopiusSmallSDK();
	$horoskop->setPrivateKey(@$hor_private_key);
	$horoskop->setPublicKey(@$hor_public_key);
	$horoskop->getLink(@$hor_link);
	$horoskop->setAlphabet(@$hor_daily_alphabet);
	$horoskop->getResponse();
	
}

add_filter('the_content','hordaily_show_filter');

function hordaily_show_filter($content)
{
	return 	preg_replace_callback('/\[DNEVNIHOROSKOP\]/sim','hordaily_show_filter_callback',$content);
}


function  hordaily_show_filter_callback($matches) 
{

	global $wpdb;
	@$hor_private_key =  'jfk++9OJRvQfo5J54HZhEh1kGPs=';
	@$hor_public_key = 'MmQyOTE3Y2RjMjJhZjI3YmIzOTNhNzM0NDE3OWU3NWVjYjMwNTdiYg==';
	@$hor_daily_alphabet = get_option('hordaily_alphabet');
	@$hor_link = get_option('hordaily_link');

	$horoskop = new HoroskopiusSmallSDK();
	$horoskop->setPrivateKey(@$hor_private_key);
	$horoskop->setPublicKey(@$hor_public_key);
	$horoskop->getLink(@$hor_link);
	$horoskop->setAlphabet(@$hor_daily_alphabet);
	$horoskop->getResponse();
}


function hordaily_install() 
{
	add_option('hordaily_title', "Dnevni horoskop");
	add_option('hordaily_alphabet', "1");
	add_option('hordaily_link', "1");
}

function hordaily_widget($args) 
{
	extract($args);
	if(get_option('hordaily_title') <> "")
	{
		echo $before_widget;
		echo $before_title;
		echo get_option('hordaily_title');
		echo $after_title;
	}
	hordailyshow();
	if(get_option('hordaily_title') <> "")
	{
		echo $after_widget;
	}
}

function hordaily_control() 
{
	echo "Dnevni horoskop";
}


function hordaily_widget_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('dnevnihoroskop', 'Dnevni horoskop', 'hordaily_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('dnevnihoroskop', array('Dnevni horoskop', 'widgets'), 'hordaily_control');
	} 
}

function hordaily_deactivation() 
{

}


function hordaily_option() 
{
	global $wpdb;
	echo '<h2>Podešavanja za dnevni horoskop</h2>';
	
	$hor_title = get_option('hordaily_title');
	$hor_alphabet = get_option('hordaily_alphabet');
	$hor_link = get_option('hordaily_link');
	
	if (@$_POST['hordaily_submit']) 
	{
		$hor_title = stripslashes($_POST['hordaily_title']);
		$hor_alphabet = stripslashes($_POST['hordaily_alphabet']);
		$hor_link = stripslashes($_POST['hordaily_link']);
		
		update_option('hordaily_title',$hor_title);;
		update_option('hordaily_alphabet', $hor_alphabet);
		update_option('hordaily_link', $hor_link);
	}
	
	echo '<form name="hordaily_form" method="post" action="">';
	
	echo '<p>Naslov horoskopa :<br><input  style="width: 250px;" type="text" value="';
	echo $hor_title . '" name="hordaily_title" id="hordaily_title" /></p>';
	
	$seles1 = null;
	$seles2 = null;
	$seles3 = null;
	
	if ($hor_alphabet == 1) : $seles1 = ' selected'; elseif ($hor_alphabet == 2) : $seles2 = ' selected'; elseif ($hor_alphabet == 3) : $seles3 = ' selected'; endif;
	
	echo '<p>Izaberite pismo na kojem želite ispisan dnevni horoskop:<br><select style="width: 250px" name="hordaily_alphabet" id="hordaily_alphabet">
			<option value="1"' . $seles1 . '>Latinica</option>
			<option value="2"' . $seles2 . '>Ćirilica</option>
			</select></p>';
			
	
	$seles1 = null;
	$seles2 = null;
	$seles3 = null;
	
	if ($hor_link == 1) : $selel1 = ' selected'; elseif ($hor_link == 2) : $selel2 = ' selected'; endif;
	
	echo '<p>Želite da postavite link ka Horoskopius web portalu, koji vam je sve ovo učinio besplatno? Da, molimo :) <br><select style="width: 250px" name="hordaily_link" id="hordaily_link">
			<option value="1"' . $selel1 . '>Da :)</option>
			<option value="2"' . $selel2 . '>Ne :(</option>
			</select></p>';

	echo '<input name="hordaily_submit" id="hordaily_submit" lang="publish" class="button-primary" value="Update" type="Submit" />';
	echo '</form>';
	?>
    <h2>Konfiguracijske opcije</h2>
    <ol>
    	<li>Drag and drop Dnevni horoskop widget</li>
        <li>Imate i short code za stranice i postove. U vaš post ili stranicu dodajte samo [DNEVNIHOROSKOP]</li>
        <li>Dodajte direktno u temu</li>
    </ol>
    Možete posetiti <a href="http://www.horoskopius.com" target="_blank">Horoskopius</a>
    <?php
}

function hordaily_add_to_menu() 
{
	add_options_page('Dnevni Horoskop', 'Dnevni Horoskop', 'manage_options', __FILE__, 'hordaily_option' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'hordaily_add_to_menu');
}

function addstyle_hordaily_enqueue() {  
  $StyleUrl = plugin_dir_url(__FILE__).'horoskop.css';
  wp_enqueue_style( 'HoroskopStyle', $StyleUrl );  
}  

add_action("plugins_loaded", "hordaily_widget_init");
register_activation_hook(__FILE__, 'hordaily_install');
register_deactivation_hook(__FILE__, 'hordaily_deactivation');
add_action('init', 'hordaily_widget_init');
addstyle_hordaily_enqueue();
?>