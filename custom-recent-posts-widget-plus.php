<?php

/*
Plugin Name: Custom Recent Posts Widget Plus
Description: 
Version: 1.1
Author: Javier Jara
Author URI: http://www.pineapps.it
License: GPL
Copyright: Javier Jara
*/

// Register widget

add_action( 'widgets_init', 'category_register_widget_cat_recent_posts' );

function category_register_widget_cat_recent_posts() {

	register_widget( 'category_widget_cat_recent_posts' );

}

class category_widget_cat_recent_posts extends WP_Widget {

	// Process widget

	function category_widget_cat_recent_posts() {
	
		$widget_ops = array(

			'classname'   => 'category_widget_cat_recent_posts widget_recent_entries',
			'description' => 'Display recent blog posts from a specific category and you can show the thumbnails too'
		
		);
		
		$this->WP_Widget( 'category_widget_cat_recent_posts', __( 'Custom Recent Posts Widget Plus' ), $widget_ops );
	
	}
	
	// Build the widget settings form

	function form( $instance ) {
	
		$defaults  = array( 'title' => '', 'category' => '', 'number' => 5, 'show_date' => '' );
		$instance  = wp_parse_args( ( array ) $instance, $defaults );
		$title     = $instance['title'];
		$category  = $instance['category'];
		$number    = $instance['number'];
		$show_date = $instance['show_date'];
		$show_thumbnails = $instance['show_thumbnails'];
		
		?>
		
		<p>
			<label for="category_widget_cat_recent_posts_title"><?php _e( 'Title' ); ?>:</label>
			<input type="text" class="widefat" id="category_widget_cat_recent_posts_title" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="category_widget_cat_recent_posts_username"><?php _e( 'Category' ); ?>:</label>				
			
			<?php

			wp_dropdown_categories( array(

				'orderby'    => 'title',
				'hide_empty' => false,
				'name'       => $this->get_field_name( 'category' ),
				'id'         => 'category_widget_cat_recent_posts_category',
				'class'      => 'widefat',
				'selected'   => $category

			) );

			?>

		</p>
		
		<p>
			<label for="category_widget_cat_recent_posts_number"><?php _e( 'Number of posts to show' ); ?>: </label>
			<input type="text" id="category_widget_cat_recent_posts_number" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $number ); ?>" size="3" />
		</p>

		<p>
			<input type="checkbox" id="category_widget_cat_recent_posts_show_date" class="checkbox" name="<?php echo $this->get_field_name( 'show_date' ); ?>" <?php checked( $show_date, 1 ); ?> />
			<label for="category_widget_cat_recent_posts_show_date"><?php _e( 'Display post date?' ); ?></label>
		</p>
		
		<p>
			<input type="checkbox" id="category_widget_cat_recent_posts_show_thumbnails" class="checkbox" name="<?php echo $this->get_field_name( 'show_thumbnails' ); ?>" <?php checked( $show_thumbnails, 1 ); ?> />
			<label for="category_widget_cat_recent_posts_show_thumbnails"><?php _e( 'Show thumbnails?' ); ?></label>
		</p>
		
		<?php
	
	}

	// Save widget settings

	function update( $new_instance, $old_instance ) {

		$instance              = $old_instance;
		$instance['title']     = wp_strip_all_tags( $new_instance['title'] );
		$instance['category']  = wp_strip_all_tags( $new_instance['category'] );
		$instance['number']    = is_numeric( $new_instance['number'] ) ? intval( $new_instance['number'] ) : 5;
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? 1 : 0;
		$instance['show_thumbnails'] = isset( $new_instance['show_thumbnails'] ) ? 1 : 0;

		return $instance;

	}

	// Display widget

	function widget( $args, $instance ) {

		extract( $args );

		echo $before_widget;

		$title     = $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$category  = $instance['category'];
		$number    = $instance['number'];
		$show_date = ( $instance['show_date'] === 1 ) ? true : false;
		$show_thumbnails = ( $instance['show_thumbnails'] === 1 ) ? true : false;

		if ( !empty( $title ) ) echo $before_title . $title . $after_title;

		$cat_recent_posts = new WP_Query( array( 

			'post_type'      => 'post',
			'posts_per_page' => $number,
			'cat'            => $category

		) );

		if ( $cat_recent_posts->have_posts() ) {

			echo '<ul>';

			while ( $cat_recent_posts->have_posts() ) {

				$cat_recent_posts->the_post();

				echo '<li>';
			    if ( $show_thumbnails ) echo '<a href="' . get_permalink() . '">' .the_post_thumbnail( array(32,32) ) .'</a>';
				echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
				if ( $show_date ) echo '<span class="post-date">' . get_the_time( get_option( 'date_format' ) ) . '</span>';
				echo '</li>';

			}

			echo '</ul>';

		} else {

			echo 'No posts yet...';

		}

		wp_reset_postdata();

		echo $after_widget;

	}

}

?>