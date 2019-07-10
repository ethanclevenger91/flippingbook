<?php
/*
Plugin Name: FlippingBook
Plugin URI: https://flippingbook.com/__UnlockFBOnline?target=/
Description: Embed FlippingBook interactive publications into your blog post or WordPress webpage quickly and easily.
Version: 1.2.5
Author: FlippingBook Team
Author URI: http://flippingbook.com/__UnlockFBOnline?target=/about
*/
?>
<?php
/*  Copyright 2016  FlippingBook Team (email: chernov.sergey@flippingbook.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
class Flippingbook {
	public $shortcode_tag = 'flippingbook';
	function __construct() {
		$cdl_mask = '#https?://(.*\.)?(cld\.mobi)|(cld\.bz)/.*#i';
		$cld_provider = plugins_url().'/flippingbook/oembed.php?format={format}';
		wp_oembed_add_provider( $cdl_mask, $cld_provider, true );
		$flippingbook_mask = '#https?://(www\.)?(online\.)?flippingbook\.com/view/.*#i';
		$flippingbook_provider = 'https://flippingbook.com/____fbonline/oembed/';
		wp_oembed_add_provider( $flippingbook_mask, $flippingbook_provider, true );
		
		add_shortcode( $this->shortcode_tag, array( $this, 'shortcode_handler' ) );
		
		if ( is_admin() ){
			add_action('admin_head', array( $this, 'admin_head') );
		}
	}
	
	function admin_head() {
		// check user permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}
		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this ,'mce_external_plugins' ) );
		}
	}
	function mce_external_plugins( $plugin_array ) {
		//$plugin_array[$this->shortcode_tag] = plugins_url( 'js/flippingbook.js' , __FILE__ );
		return $plugin_array;
	}
	function shortcode_handler( $atts , $content ) {
		$url = parse_url($content);
		if($url){
			$domain = $url['host'];
			$a = shortcode_atts(
				array(
					'width' => '600',
					'height' => '480',
					'lightbox' => 'false',
					'title' => 'Generated by FlippingBook Publisher'
				), $atts );
			
			if ( strpos($domain,'flippingbook.com')  !== false )
			{
				$data_lightbox = ('true' ===($a['lightbox']))?'':'data-fb-lightbox="no"';
				$html = '<a href="'.$content.'" class="fb-embed" '.$data_lightbox.' data-fb-version="1" data-fb-method="wp" data-fb-width="'.$a['width'].'px" data-fb-height="'.$a['height'].'px" style="max-width: 100%; display: none;">'.$a['title'].'</a>';
				if( strpos($domain,'online.flippingbook.com')  !== false )
				{
					$html .= '<script async defer src="https://'.$domain.'/content/embed/boot.js"></script>';
				}
				else
				{
					$html .= '<script async defer src="https://'.$domain.'/____fbonline/content/embed/boot.js"></script>';
				}
			}
			else
			{
				$data_lightbox = ('true' ===($a['lightbox']))?'':'data-cld-lightbox="no"';
				$html = '<a href="'.$content.'" class="cld-embed" '.$data_lightbox.' data-cld-width="'.$a['width'].'px" data-cld-height="'.$a['height'].'px">'.$a['title'].'</a>';
				$html .= '<script async defer src="https://'.$domain.'/content/embed-boot/boot.js"></script>';
			}
			return $html;
		}
		return "FlippingBook shortcode is not correct";
	}
}
	$flippingbook = new Flippingbook();

?>
