<?php
/*
Plugin Name: Picplz Widget
Plugin URI: http://harrywolff.com/2011/02/picplz-widget-for-wordpress/
Description: This plugin creates a new widget that will show the latest 10 photos you have posted to your picplz account.  Current options include:  set your picplz username, height and width of each picture, and option to display picplz logo.  More custom configurations coming.
Author: Harry Wolff
Version: 1.1.2
Author URI: http://harrywolff.com/
*/

/**
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'picplz_load_widgets' );

/**
 * Register our widget.
 * 'Picplz_Widget' is the widget class used below.
 */
function picplz_load_widgets() {
	register_widget( 'Picplz_Widget' );
}

/**
 * Picplz Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 */
class Picplz_Widget extends WP_Widget {

	/**
	 * Widget constructor.
	 */
	function Picplz_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'picplz', 'description' => __('A widget that shows your 10 most recent Picplz photos.') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'picplz-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'picplz-widget', __('Picplz Photos'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$picplz_username = $instance['picplz_username'];
		$picplz_numberOfPhotos = $instance['picplz_numberOfPhotos'];
		$picture_width = str_replace("px", "", $instance['picture_width']);
		$picture_height = str_replace("px", "", $instance['picture_height']);
		$show_picplz_logo = $instance['show_picplz_logo'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/** 
		 *	Display name from widget settings if one was input.
		 *  Show picplz user pictures using function
		 *  Crux of logic for display goes here
		 *  Thank you voidfiles (http://picplz.com/user/voidfiles/)
		 *	for your work with the picplz API !
		 */
		if ( $picplz_username ) {
				?>		
				<style type="text/css" media="screen">
		            .pp-user-widget {
		                width:100%;
		                font-family:Helvetica, Arial;
		                font-weight:bold;
		            }
		            .pp-user-widget ul {
		                width:100%;
		                margin:0;
		                padding:0;
		            }
		            .pp-user-widget li, .pp-user-widget li a  {
		                width:<?php echo $picture_width; ?>px;
		                height:<?php echo $picture_height; ?>px;
		                display:block;
		            }
		            .pp-user-widget li {
		                list-style:none;
		                padding:0;
		                float:left;
		                margin:0 5px 5px 0;
		            }
		            .pp-title {
		                padding-bottom:5px;
		            }
					.pp-brand {
						clear:both;
					}
		            .pp-brand em {
		                text-align:right;
		                padding-right:5px;
		                line-height:13px;
		            }
					ul.picplz-user-widget {
						margin:0;padding:0;
					}
		        </style>
		        <div class="my-widget-holder">
		            <div class="pp-user-widget">
		                <div class='pp-w-body'></div> 
				<?php if ($show_picplz_logo) {	?>
		                <div class='pp-brand'>
		                    <a href="http://picplz.com" title="widget by picplz"><img src='http://a3.picplzthumbs.com/i/kpaeOjVAncDfxS26pGsyOp1xXRs.png'></a>
		                </div>
				<?php }	?>
		            </div>
		        </div>
				<script>window.jQuery || document.write("<script src='<?php bloginfo('wpurl'); ?>/wp-content/plugins/picplz-widget/jquery-1.5.1.min.js'>\x3C/script>")</script>
				<script type="text/javascript" charset="utf-8">
				   (function(JQ){
	                show_my_pics = function(username,numberOfPhotos){
						JQ.getJSON("https://api.picplz.com/api/v2/user.json?include_pics=1&username="+username+"&pic_page_size="+numberOfPhotos+"&callback=?", function(data){
	                        if(data.result == "ok"){
	                            var pics = data.value.users[0].pics,
	                                html = "<ul class='picplz-user-widget' style='margin:0;' >",
	                                parent = JQ(".pp-user-widget"),
	                                holder = JQ(".pp-w-body", parent),
	                                val, url, src, width, height;
	                            JQ.each(pics, function(i, val){
	                                url = val.url;
	                                src = val.pic_files["100sh"].img_url;
	                                //width = val.pic_files["100sh"].width;
	                                //height = val.pic_files["100sh"].height;
									width = <?php echo $picture_width; ?>;
	                                height = <?php echo $picture_width; ?>;
	                                html = html + " <li><a href='http://picplz.com"+url+"'><img src='"+src+"' width='"+width+"' height='"+height+"'></a></li>";
	                            });
	                            html = html + "</ul>";
	                            holder.html(html);
	                        }
	                    });
	                };                
			        show_my_pics("<?php echo $picplz_username; ?>", "<?php echo $picplz_numberOfPhotos; ?>");
	            })(jQuery);
				</script>
			<?php

		}

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['picplz_username'] = strip_tags( $new_instance['picplz_username'] );
		$instance['picplz_numberOfPhotos'] = strip_tags( $new_instance['picplz_numberOfPhotos'] );
		$instance['picture_width'] = strip_tags( $new_instance['picture_width'] );
		$instance['picture_height'] = strip_tags( $new_instance['picture_height'] );
		$instance['show_picplz_logo'] = !empty($new_instance['show_picplz_logo']) ? 1 : 0;

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(	'title' => __('My Recent Picplz Photos', 'example'), 
							'picplz_username' => __('hswolff', 'example'),
							'picplz_numberOfPhotos' => __('10', 'example'),
							'picture_width' => __('100px', 'example'),
							'picture_height' => __('100px', 'example'), 
							'show_picplz_logo' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Username: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'picplz_username' ); ?>"><?php _e('Picplz Username:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'picplz_username' ); ?>" name="<?php echo $this->get_field_name( 'picplz_username' ); ?>" value="<?php echo $instance['picplz_username']; ?>" style="width:100%;" />
		</p>

		<!-- NumberOfPhotos: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'picplz_numberOfPhotos' ); ?>"><?php _e('Number Of Photos To Display:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'picplz_numberOfPhotos' ); ?>" name="<?php echo $this->get_field_name( 'picplz_numberOfPhotos' ); ?>" value="<?php echo $instance['picplz_numberOfPhotos']; ?>" style="width:15%;" />
		</p>

		<!-- Picture Width: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'picture_width' ); ?>"><?php _e('Picture Width:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'picture_width' ); ?>" name="<?php echo $this->get_field_name( 'picture_width' ); ?>" value="<?php echo $instance['picture_width']; ?>" style="width:15%;" />
		</p>
		
		<!-- Picture Height: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'picture_height' ); ?>"><?php _e('Picture Height:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'picture_height' ); ?>" name="<?php echo $this->get_field_name( 'picture_height' ); ?>" value="<?php echo $instance['picture_height']; ?>" style="width:15%;" />
		</p>
		
		<!-- Show Logo: Checkbox Input -->		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_picplz_logo'], true ); ?> id="<?php echo $this->get_field_id( 'show_picplz_logo' ); ?>" name="<?php echo $this->get_field_name( 'show_picplz_logo' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_picplz_logo' ); ?>">Display Picplz logo?</label>
		</p>
		

	<?php
	}
	
}

?>