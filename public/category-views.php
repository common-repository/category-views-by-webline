<?php
class Category_Views extends WP_Widget {
	
	/**
	 *
	 * Unique identifier for your widget.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * widget file.
	 *
	 * @since    1.0.1
	 *
	 * @var      string
	 */
	protected $widget_slug = 'wli_category_views';

	/**
	 *   Category_Views constructor
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                No arguments passed
	 *  @author             Weblineindia
	 *
	 */
	public function __construct() {
		// widget settings
		$widget_ops = array(
			'classname'     => $this->get_widget_slug().'-class',
			'description'   => __('A Simple plugin allow you to upload category image with different widget options.',$this->get_widget_slug())
		);
		// create the widget
		parent::__construct(
				 'categoryviews', 
				 __('Category Views',$this->get_widget_slug()),
				 $widget_ops
				 );
		
		if ( strpos( $_SERVER['SCRIPT_NAME'], 'edit-tags.php' ) > 0 ) {
			add_action('admin_head',array($this,'add_style'));
			add_action('quick_edit_custom_box',array($this,'quick_edit'),10, 3);
		}
		
		add_action('category_add_form_fields', array($this,'add_image_field'));
		add_action('category_edit_form_fields', array($this,'edit_image_field'));
		add_action('edit_term', array($this,'save_category_image'));
		add_action('create_term', array($this,'save_category_image'));
		add_filter('manage_edit-category_columns', array($this, 'category_columns' ));
		add_filter('manage_category_custom_column',array($this, 'category_column'),10, 3);
		add_action('admin_enqueue_scripts', array($this,'cvbw_enqueue_scripts'));
	}

	// Callback function to enqueue admin CSS and JS
	function cvbw_enqueue_scripts()
    {
		// Enqueue Admin Notices CSS
		wp_enqueue_style('cvbw-custom', plugin_dir_url(__FILE__) . '/assets/css/cvbw-admin-notices.css', array(), '1.0', 'all');
    }

	/**
	 * get_widget_slug() is use to get the widget slug.
	 *
	 * @since    1.0.1
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_widget_slug() {
		return $this->widget_slug;
	}
	
	/**
	 *   add_style() is used to style category image.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                No arguments passed
	 *  @author             Weblineindia
	 *
	 */
	public function add_style()	{
		echo '<style type="text/css" media="screen">
		th.column-thumb {width:60px;}
		.form-field img.category-image {border:1px solid #eee;max-width:200px;max-height:200px;}
		.inline-edit-row fieldset .thumb label span.title {width:48px;height:48px;border:1px solid #eee;display:inline-block;}
		.column-thumb span {width:48px;height:48px;border:1px solid #eee;display:inline-block;}
		.inline-edit-row fieldset .thumb img,.column-thumb img {width:48px;height:48px;}
		</style>';
	}
	
	/**
	 *   add_image_field() is used to add image field to Add New Category page.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                No arguments passed
	 *  @author             Weblineindia
	 *
	 */
	public function add_image_field() {
		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		
		echo '<div class="form-field">
		<label for="category_image">' . __('Image', $this->get_widget_slug()) . '</label>
		<input type="text" name="category_image" id="category_image" value="" />
		<button class="upload_img_btn button">' . __('Upload Image', $this->get_widget_slug()) . '</button>
		<p class="description">Select category image.</p>
		</div>'.categoryviews_script();
	}
	
	/**
	 *   edit_image_field() is used to add image field to Edit Category page.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                No arguments passed
	 *  @author             Weblineindia
	 *
	 */
	public function edit_image_field($category) {
		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		
		if (category_image_url( $category->term_id, NULL, TRUE ) == CV_DEFAULT_IMAGE)
			$image_url = "";
		else
			$image_url = category_image_url( $category->term_id, NULL, TRUE );
	
		echo '<tr class="form-field">
		<th scope="row" valign="top"><label for="category_image">' . __('Image', $this->get_widget_slug()) . '</label></th>
		<td>
			<img class="category-image" src="'.category_image_url( $category->term_id, NULL, TRUE ).'" />
			<input type="text" name="category_image" id="category_image" value="'.$image_url.'" />
			<button class="upload_img_btn button">' . __('Upload Image', $this->get_widget_slug()) . '</button>
			<button class="remove_img_btn button">' . __('Remove Image', $this->get_widget_slug()) . '</button>
			<br/>
			<span class="description">Select category image.</span>
		</td>
		</tr>'.categoryviews_script();
	}
	
	/**
	 *   save_category_image() is used to save category image while edit or save term.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                $term_id
	 *  @author             Weblineindia
	 *
	 */
	public function save_category_image($term_id) {
		if(isset($_POST['category_image']))
			update_option('cv_category_image'.$term_id, $_POST['category_image']);
	}
	
	/**
	 *   category_columns() is used to add Image column to Categories list.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             $columns
	 *  @var                $columns
	 *  @author             Weblineindia
	 *
	 */
	public function category_columns( $columns ) {
		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns['thumb'] = __('Image', $this->get_widget_slug());
	
		unset( $columns['cb'] );
	
		return array_merge( $new_columns, $columns );
	}
	
	/**
	 *   category_column() is used to add Image column value to Categories list.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                $columns
	 *  @var                $column
	 *  @var                $id
	 *  @author             Weblineindia
	 *
	 */
	public function category_column( $columns, $column, $id ) {
		if ( $column == 'thumb' )
			$columns = '<span><img src="' . category_image_url($id, NULL, TRUE) . '" alt="' . __('Thumbnail', $this->get_widget_slug()) . '" class="wp-post-image" /></span>';
	
		echo $columns;
	}
	
	/**
	 *   quick_edit() is used to add Image column to Quick Edit Categories list.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                $term_id
	 *  @author             Weblineindia
	 *
	 */
	public function quick_edit($column_name, $screen, $name) {
		if ($column_name == 'thumb')
			echo '<fieldset>
			<div class="thumb inline-edit-col">
				<label>
					<span class="title"><img src="" alt="Thumbnail"/></span>
					<span class="input-text-wrap"><input type="text" name="category_image" id="category_image" value="" class="cat_list" /></span>
					<span class="input-text-wrap">
						<button class="upload_img_btn button">' . __('Upload Image', $this->get_widget_slug()) . '</button>
						<button class="remove_img_btn button">' . __('Remove Image', $this->get_widget_slug()) . '</button>
					</span>
				</label>
			</div>
			</fieldset>';
	}
	
	/**
	 *  form() is used to generates the administration form for the widget.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                $instance
	 *  @author             Weblineindia
	 *
	 */
	public function form($instance) {
		/* Set up some default widget settings. */
		$defaults = array(
				'title'=>'Categories',
				'no_categories'=>'5',
				'orderby'=>'name',
				'order'=>'ASC',
				'select_category_views'=>'Plain View',
				'show_empty_categories'=>'off',
				'show_posts_count'=>'off'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', $this->get_widget_slug()); ?></label>
          	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        
        <p>
          	<label for="<?php echo $this->get_field_id( 'no_categories' ); ?>"><?php _e('Number of categories to display', $this->get_widget_slug()); ?></label>
          	<input class="widefat" id="<?php echo $this->get_field_id( 'no_categories' ); ?>" name="<?php echo $this->get_field_name( 'no_categories' ); ?>" type="number" value="<?php echo esc_attr( $instance['no_categories'] ); ?>" />
        </p>
        
        <p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e('Ordering', $this->get_widget_slug()); ?></label><br>
			<select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
				<option <?php selected( $instance['orderby'], 'name'); ?> value="name">name</option>
				<option <?php selected( $instance['orderby'], 'id'); ?> value="id">id</option>
				<option <?php selected( $instance['orderby'], 'slug'); ?> value="slug">slug</option>
				<option <?php selected( $instance['orderby'], 'count'); ?> value="count">count</option>
				<option <?php selected( $instance['orderby'], 'term_group'); ?> value="term_group">term_group</option>
			</select>
			
			<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<option <?php selected( $instance['order'], 'ASC'); ?> value="ASC">ASC</option>
				<option <?php selected( $instance['order'], 'DESC'); ?> value="DESC">DESC</option>
			</select>
		</p>
		
        <p>
			<label for="<?php echo $this->get_field_id( 'select_category_views' ); ?>"><?php _e('How to show categories', $this->get_widget_slug()); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('select_category_views'); ?>" name="<?php echo $this->get_field_name('select_category_views'); ?>">
				<option <?php selected( $instance['select_category_views'], 'Plain View'); ?> value="Plain View">Plain View</option>
				<option <?php selected( $instance['select_category_views'], 'Slide View'); ?> value="Slide View">Slide View</option>
				<option <?php selected( $instance['select_category_views'], 'List View'); ?> value="List View">List View</option>
				<option <?php selected( $instance['select_category_views'], 'Cloud View'); ?> value="Cloud View">Cloud View</option>
			</select>
		</p>
		   
		<p>
          	<input class="checkbox" id="<?php echo $this->get_field_id( 'show_empty_categories' ); ?>" name="<?php echo $this->get_field_name( 'show_empty_categories' ); ?>" type="checkbox" <?php checked($instance['show_empty_categories'], 'on'); ?>/>
          	<label for="<?php echo $this->get_field_id( 'show_empty_categories' ); ?>"><?php _e('Show empty categories', $this->get_widget_slug()); ?></label>
        </p>
        
        <p>
          	<input class="checkbox" id="<?php echo $this->get_field_id( 'show_posts_count' ); ?>" name="<?php echo $this->get_field_name( 'show_posts_count' ); ?>" type="checkbox" <?php checked($instance['show_posts_count'], 'on'); ?>/>
          	<label for="<?php echo $this->get_field_id( 'show_posts_count' ); ?>"><?php _e('Show posts count', $this->get_widget_slug()); ?></label>
        </p>
       
    <?php
	}

	/**
	 *  update() is used to replace the new value when the Saved button is clicked.
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             $instance
	 *  @var                $new_instance,$old_instance
	 *  @author             Weblineindia
	 *
	 */
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['no_categories'] = $new_instance['no_categories'];
		$instance['orderby'] = $new_instance['orderby'];
		$instance['order'] = $new_instance['order'];
		$instance['select_category_views'] = $new_instance['select_category_views'];
		$instance['show_empty_categories'] = isset($new_instance['show_empty_categories'])?$new_instance['show_empty_categories']:'off';
		$instance['show_posts_count'] =isset($new_instance['show_posts_count'])?$new_instance['show_posts_count']:'off';
		return $instance;
	}

	/**
	 * widget() is used to show the frontend part .
	 *
	 *  @since    			1.0.1
	 *
	 *  @return             void
	 *  @var                $args,$instance
	 *  @author             Weblineindia
	 *
	 */
	public function widget($args, $instance) {
		extract( $args );
		
		wp_enqueue_style ( 'categoryviews-style',CV_URL.'/public/assets/css/category-views-style.css');
		wp_enqueue_script( 'categoryviews-script',CV_URL.'/public/assets/js/category-views-script.js', array('jquery'));
		wp_enqueue_script( 'tagcanvas-min-js', CV_URL.'/public/assets/js/tagcanvas-min.js', array('categoryviews-script'));
		wp_enqueue_script( 'tagcanvas-js', CV_URL.'/public/assets/js/tagcanvas.js', array('tagcanvas-min-js'));
		
		$title = $instance['title'];
		$no_categories=$instance['no_categories'];
		$orderby=$instance['orderby'];
		$order=$instance['order'];
		$select_category_views=$instance['select_category_views'];
		$show_empty_categories=$instance['show_empty_categories'];
		$show_posts_count=$instance['show_posts_count'];
		
		if($show_empty_categories == "on")
			$hide_empty='0';
		else
			$hide_empty='1';
		
		if($show_posts_count == "on")
			$show_count='1';
		else
			$show_count='0';
		
		echo $before_widget;
		?>
		<div class="categories_box">
			<div class="categories_header">
				<?php
				if(!empty($title))
					echo apply_filters( 'widget_title', $title );
				else 
					echo "Categories";
				?>
			</div>
			<?php
			if($select_category_views == "Plain View") {
				$category_args = array(
						'show_option_all'    => '',
						'orderby'            => $orderby,
						'order'              => $order,
						'style'              => 'list',
						'show_count'         => $show_count,
						'hide_empty'         => $hide_empty,
						'use_desc_for_title' => 1,
						'child_of'           => 0,
						'feed'               => '',
						'feed_type'          => '',
						'feed_image'         => '',
						'exclude'            => '',
						'exclude_tree'       => '',
						'include'            => '',
						'hierarchical'       => 1,
						'title_li'           => '',
						'show_option_none'   => __( 'No categories' ),
						'number'             => $no_categories,
						'echo'               => 1,
						'depth'              => 0,
						'current_category'   => 0,
						'pad_counts'         => 0,
						'taxonomy'           => 'category',
						'walker'             => null
				);
				echo '<div class="categories_content"><ul>';
					wp_list_categories( $category_args );
				echo '</ul></div>';
			}
					
			if($select_category_views == "List View") {
				$category_args = array(
						'type'				=> 'post',
						'child_of'          =>  0,
						'parent'            =>  0,
						'orderby'           => $orderby,
						'order'             => $order,
						'hide_empty'        => $hide_empty,
						'hierarchical'      => 1,
						'exclude'           => '',
						'include'           => '',
						'number'            => $no_categories,
						'taxonomy'          => 'category',
						'pad_counts'        => 1
				);
				$categories = get_categories($category_args);
				
				echo '<div class="categories_content">';	
				foreach($categories as $category) {
					$cat_img=get_option("cv_category_image$category->term_id");
					echo '<div class="categories_box01">
 							<div class="category_pic">
			 					<a href="'.get_category_link( $category->term_id ).'" title="'.sprintf( __( 'View all posts in %s' ), $category->name ).'">
									<img height="70px" width="70px" alt="No Image" src="'.(!empty($cat_img)?$cat_img:CV_DEFAULT_IMAGE).'">
								</a>
							</div>';
						
						echo '<div class="categories_details">
								<a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) .'">'
									.(($show_posts_count == "on")?"$category->name($category->count)":"$category->name")
							  .'</a>
								<div>'.$category->description.'</div>
		 				 	 </div>
					 </div>';
				}
					echo '</div>';
			}
			
			if($select_category_views == "Slide View") {
				$category_args = array(
						'type'				=> 'post',
						'child_of'          =>  0,
						'parent'            =>  0,
						'orderby'           => $orderby,
						'order'             => $order,
						'hide_empty'        => $hide_empty,
						'hierarchical'      => 1,
						'exclude'           => '',
						'include'           => '',
						'number'            => $no_categories,
						'taxonomy'          => 'category',
						'pad_counts'        => 1
				);
				$categories = get_categories($category_args);
				
				global $slider_count;
				
	   			echo '<div id="slider'.$slider_count.'" class="slider">
						<a href="#" id="control_next'.$slider_count.'" class="control_next">&raquo;</a>
						<a href="#" id="control_prev'.$slider_count.'" class="control_prev">&laquo;</a>
						<ul>';
						foreach($categories as $category) {
							$cat_img=get_option("cv_category_image$category->term_id");
					   		echo '<li>
					   				<a href="'.get_category_link( $category->term_id ).'" title="'.sprintf( __( "View all posts in %s" ), $category->name ).'">
					      				<img alt="No Image" src="'.(!empty($cat_img)?$cat_img:CV_DEFAULT_IMAGE).'">
					      			</a>
					      			<p>
										<a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '">' 
											.(($show_posts_count == "on")?"$category->name($category->count)":"$category->name")
									  .'</a> 
									</p>
					    		 </li>';
					    }
			  	  echo '</ul>
					  </div>';
			  	  
			  	  $slider_count ++;
	   	}
	   	
	   	if($select_category_views == "Cloud View") {
	   		$args = array(
   				'taxonomy'	=> 'category',
   				'hide_empty'=> $hide_empty,
   				'show_count'=> $show_count,
	   		);

	   		global $cloud_count;
	   		
	   		echo '<div class="categories_content">
	   				<div id="myCanvasContainer'.$cloud_count.'">
	   					<canvas width="240" height="290" data-id="'.$cloud_count.'" id="myCanvas'.$cloud_count.'"></canvas>
	   						<div id="tags'.$cloud_count.'">';
	   							wp_tag_cloud($args);
	   			 	  echo '</div>
	   				</div>';
	   		echo '</div>';
	   		$cloud_count ++;
	   	}
	   	?>
	</div>
	<?php
		echo $after_widget;
	}
	
}// class Category_Views
/**
 *   categoryviews_script() is used to upload using wordpress upload.
 *
 *  @since    			1.0.1
 *
 *  @return             script
 *  @var                No arguments passed
 *  @author             Weblineindia
 *
 */
function categoryviews_script() {
	return '<script type="text/javascript">
	    jQuery(document).ready(function() {
			var wordpress_ver = "'.get_bloginfo("version").'", upload_button;
			jQuery(".upload_img_btn").click(function(event) {
				upload_button = jQuery(this);
				var frame;
				if (wordpress_ver >= "3.5") {
					event.preventDefault();
					if (frame) {
						frame.open();
						return;
					}
					frame = wp.media();
					frame.on( "select", function() {
						// Grab the selected attachment.
						var attachment = frame.state().get("selection").first();
						frame.close();
						if (upload_button.parent().prev().children().hasClass("cat_list")) {
							upload_button.parent().prev().children().val(attachment.attributes.url);
							upload_button.parent().prev().prev().children().attr("src", attachment.attributes.url);
						}
						else
						{
							jQuery("#category_image").val(attachment.attributes.url);
							jQuery(".category-image").attr("src",attachment.attributes.url);
						}
					});
					frame.open();
				}
				else {
					tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
					return false;
				}
			});

			jQuery(".remove_img_btn").click(function() {
				jQuery("#category_image").val("");
				jQuery(this).parent().children("img").attr("src","' . CV_DEFAULT_IMAGE . '");
				jQuery(this).parent().siblings(".title").children("img").attr("src","' . CV_DEFAULT_IMAGE . '");
				jQuery(".inline-edit-col :input[name=\'category_image\']").val("");
				return false;
			});

			if (wordpress_ver < "3.5") {
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					if (upload_button.parent().prev().children().hasClass("cat_list")) {
						upload_button.parent().prev().children().val(imgurl);
						upload_button.parent().prev().prev().children().attr("src", imgurl);
					}
					else
					{
						jQuery("#category_image").val(imgurl);
						jQuery(".category-image").attr("src",imgurl);
					}
					tb_remove();
				}
			}

			jQuery("table.wp-list-table tbody").on("click",".editinline",function(){
			    var tax_id = jQuery(this).parents("tr").attr("id").substr(4);
			    var thumb = jQuery("#tag-"+tax_id+" .thumb img").attr("src");
				if (thumb != "' . CV_DEFAULT_IMAGE . '") {
					jQuery(".inline-edit-col :input[name=\'category_image\']").val(thumb);
				} else {
					jQuery(".inline-edit-col :input[name=\'category_image\']").val("");
				}
				jQuery(".inline-edit-col .title img").attr("src",thumb);
			    return true;
			});
	    });
	</script>';
}

/**
 *   category_image_url() is used to get category image url for the given term_id (Default image by default).
 *
 *  @since    			1.0.1
 *
 *  @return             $url
 *  @var                $term_id,$size,$return_placeholder
 *  @author             Weblineindia
 *
 */
function category_image_url($term_id = NULL, $size = NULL, $return_placeholder = FALSE) {

	$url = get_option('cv_category_image'.$term_id);
	if(!empty($url)) {
		$attachment_id = get_attachment_id_by_url($url);
		if(!empty($attachment_id)) {
			if (empty($size))
				$size = 'full';
			$url = wp_get_attachment_image_src($attachment_id, $size);
			$url = $url[0];
		}
	}

	if ($return_placeholder)
		return ($url != '') ? $url : CV_DEFAULT_IMAGE;
	else
		return $url;
}

/**
 *   get_attachment_id_by_url() is used to get attachment ID by image url.
 *
 *  @since    			1.0.1
 *
 *  @return             $id
 *  @var                $image_src
 *  @author             Weblineindia
 *
 */
function get_attachment_id_by_url($image_src) {
	global $wpdb;
	$query = "SELECT ID FROM {$wpdb->posts} WHERE guid = '$image_src'";
	$id = $wpdb->get_var($query);
	return (!empty($id)) ? $id : NULL;
}

// register widget
function category_custom_widget(){
	register_widget( 'Category_Views' );
}

add_action('widgets_init' , 'category_custom_widget');
//add_action('widgets_init', create_function('', 'return register_widget("Category_Views");'));
?>