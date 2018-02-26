<?php
/*
Plugin Name: Custom Post Popup
Plugin URI: https://gnanavel.wordpress.com/
Description: Display custom post with popup. Use this shortcode to display testimonial <strong>[CUSTOM_POST_POPUP type="post" orderby="none" posts_per_page="12" customclass="custom class" orderby="none"]</strong>
Version: 1.0
Author: Gnanavel
License: GPLv2 or later
*/


Class CustomPostPopup {

	public $plugin_dir;
	public $plugin_url;

	function  __construct(){
		global $wpdb;
		$prefix = $wpdb->prefix;
		$this->plugin_dir = plugin_dir_path(__FILE__);
		$this->plugin_url = plugin_dir_url(__FILE__);
		add_shortcode( 'CUSTOM_POST_POPUP', array($this, 'custom_post_popup_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array($this,'wpsp_enqueue_scripts_styles' ));
	}
	
	function wpsp_enqueue_scripts_styles(){
		wp_enqueue_script('wpct_fancybox_js', $this->plugin_url.'js/fancybox/source/jquery.fancybox.pack.js', array('jquery'), '1.0.0', true);
		wp_enqueue_style('wpct_fancybox_css', $this->plugin_url.'js/fancybox/source/jquery.fancybox.css');
		wp_enqueue_script('wpct_frontend_js', $this->plugin_url.'js/wpspfrontend.js', array('jquery'), '1.0.0', true);
		wp_enqueue_style('wpsp_frontend_css', $this->plugin_url.'css/frontend.css');
	}
	
	public function custom_post_popup_shortcode($atts) {
		
		extract( shortcode_atts( array(
			'posts_per_page' => '12',
			'orderby' => 'none',
			'type'=>'type',		
			'customclass'=>'',
		), $atts ) );
		
		$args = array(
			'posts_per_page' => (int) $posts_per_page,
			'post_type' => $posttype,
			'orderby' => $orderby,
			'no_found_rows' => true,
		);
		
		$dispCount  = (int) $posts_per_page;
		if($dispCount==12){
			$colmd = 3;
		}else if($dispCount=="4"){
			$colmd = 4; 
		}else{
			$colmd = 4;
		}
		$query = new WP_Query( $args  );

		$testimonials = '<div class="col-md-12">';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$post_id = get_the_ID();
				
				$featimageURL = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
								
				
				$feat_image       = ( !empty($featimageURL) ) ?  '<img src="'.$featimageURL.'" class="img-responsive testimonialimg">':'';	
				$featpopupimage   = ( !empty($featimageURL) ) ? '<img src="'.$featimageURL.'" class="img-responsive testimonialpopupimg">':'';	

				$testimonials .= '<div class="col-md-'.$colmd.' '.$customclass.' col-sm-4 col-xs-12 singletestimonialcont">';				
				$testimonials .= $feat_image;			
				$testimonials .= '<p class="testimonial-client-name">' . ucwords(get_the_title()) . '</p>';					
					
				$testimonials .= '<p class="testimonial-text">'.$this->wpse69204_excerpt().'</p>'; 
				$testimonials .= '<div class="fancyboxcont" id="post_'.$post_id.'"><div class="col-md-12 popuptitlemaincont"><div class="popuptitlecont"><h2 class="popuptitle">'.get_the_title().'</h2><p class="popuprolecity"> '.$role.$source.'</p></div></div><div class="col-md-12 popupmailtxtcont">'.$featpopupimage.''.get_the_content().'</div></div>';
			
				$testimonials .= '</div>';
			endwhile;
			wp_reset_postdata();
		} 
		$testimonials .= '</div>';
		return $testimonials;
	}

	public function wpse69204_excerpt( $num_words = 20, $ending = '...', $post_id = null )
	{
		global $post;
		$current_post = $post_id ? get_post( $post_id ) : $post;
		$excerpt = strip_shortcodes( $current_post->post_content );
		$excerpt = wp_trim_words( $excerpt, $num_words, $ending );
		$excerpt .= '<p class="cs_readmore" ><a class="various" href="#post_'.$post->ID.'" title="">More &raquo;</a></p>';
		return $excerpt;
	}
}

$CustomPostPopup = new CustomPostPopup();