<?php
global $vc_teaser_box;
$posts_query = $el_class = $args = $my_query = $speed = $mode = $swiper_options = ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited valid use case
$content = $link = $layout = $thumb_size = $link_target = $slides_per_view = $wrap = ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited valid use case
$autoplay = $hide_pagination_control = $hide_prev_next_buttons = $title = $show_meta = $design = ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited valid use case
$posts = array(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited valid use case
extract( shortcode_atts( array(
	'el_class' => '',
	'posts_query' => '',
	'mode' => 'horizontal',
	'speed' => '5000',
	'slides_per_view' => '1',
	'swiper_options' => '',
	'wrap' => '',
	'autoplay' => 'no',
	'hide_pagination_control' => '',
	'hide_prev_next_buttons' => '',
	'layout' => 'title,thumbnail,excerpt',
	'link_target' => '',
	'thumb_size' => 'thumbnail',
	'partial_view' => '',
	'title' => '',
    'show_meta' => 'no',
    'design' => '1'
), $atts ) );
list( $args, $my_query ) = vc_build_loop_query( $posts_query ); //
$teaser_blocks = vc_sorted_list_parse_value( $layout );
while ( $my_query->have_posts() ) {
	$my_query->the_post(); // Get post from query
	$post = new stdClass(); // Creating post object. // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited valid use case
	$post->id = get_the_ID();
	$post->link = get_permalink( $post->id );
	$post->post_type = get_post_type();
	if ( $vc_teaser_box->getTeaserData( 'enable', $post->id ) === '1' ) {
		$post->custom_user_teaser = true;
		$data = $vc_teaser_box->getTeaserData( 'data', $post->id );
		if ( ! empty( $data ) ) $data = json_decode( $data );
		$post->bgcolor = $vc_teaser_box->getTeaserData( 'bgcolor', $post->id );
		$post->custom_teaser_blocks = array();
		$post->title_attribute = the_title_attribute( 'echo=0' );
		if ( ! empty( $data ) )
			foreach ( $data as $block ) {
				$settings = array();
				if ( $block->name === 'title' ) {
					$post->title = the_title( "", "", false );
				} elseif ( $block->name === 'image' ) {
					if ( $block->image === 'featured' ) {
						$post->thumbnail_data = $this->getPostThumbnail( $post->id, $thumb_size );
					} elseif ( ! empty( $block->image ) ) {
						$post->thumbnail_data = wpb_getImageBySize( array( 'attach_id' => (int)$block->image, 'thumb_size' => $thumb_size ) );
					} else {
						$post->thumbnail_data = false;
					}
					$post->thumbnail = $post->thumbnail_data && isset( $post->thumbnail_data['thumbnail'] ) ? $post->thumbnail_data['thumbnail'] : '';
					$post->image_link = empty( $video ) && $post->thumbnail && isset( $post->thumbnail_data['p_img_large'][0] ) ? $post->thumbnail_data['p_img_large'][0] : $video;
				} elseif ( $block->name === 'text' ) {
					if ( $block->mode === 'custom' ) {
						$settings[] = 'text';
						$post->content = $block->text;
					} elseif ( $block->mode === 'excerpt' ) {
						$settings[] = $block->mode;
						$post->excerpt = $this->getPostExcerpt();
					} else {
						$settings[] = $block->mode;
						$post->content = $this->getPostContent();
					}
				}
				if ( isset( $block->link ) ) {
					if ( $block->link === 'post' ) {
						$settings[] = 'link_post';
					} elseif ( $block->link === 'big_image' ) {
						$settings[] = 'link_image';
					} else {
						$settings[] = 'no_link';
					}
					$settings[] = '';
				}
				$post->custom_teaser_blocks[] = array( $block->name, $settings );
			}
	} else {
		$post->custom_user_teaser = false;
		$post->title = the_title( "", "", false );
		$post->title_attribute = the_title_attribute( 'echo=0' );
		$post->post_type = get_post_type();
		$post->content = $this->getPostContent();
		$post->excerpt = $this->getPostExcerpt();
		$post->thumbnail_data = $this->getPostThumbnail( $post->id, $thumb_size );
		$post->thumbnail = $post->thumbnail_data && isset( $post->thumbnail_data['thumbnail'] ) ? $post->thumbnail_data['thumbnail'] : '';
		$video = get_post_meta( $post->id, "_p_video", true );
		$post->image_link = empty( $video ) && $post->thumbnail && isset( $post->thumbnail_data['p_img_large'][0] ) ? $post->thumbnail_data['p_img_large'][0] : $video;
	}

	$post->categories_css = $this->getCategoriesCss( $post->id );

	$posts[] = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited valid use case
}
wp_reset_query();
// $options = vc_parse_options_string($bxslider_options, $this->shortcode, 'bxslider_options');
$tmp_options = vc_parse_options_string( $swiper_options, $this->shortcode, 'swiper_options' );
// }}
$this->setLinktarget( $link_target );

$options = array();
// Convert keys to Camel case.
if ( (int)$slides_per_view > 0 ) $options['slidesPerView'] = (int)$slides_per_view;
if ( (int)$autoplay > 0 ) $options['autoplay'] = (int)$autoplay;
$options['mode'] = $mode;
// $options['calculateHeight'] = true;
$css_class = $this->settings['base'] . ' wpb_content_element vc_carousel_slider_' . $slides_per_view . ' vc_carousel_' . $mode . ( empty( $el_class ) ? '' : ' ' . $el_class );

$box_id = rand(1000,9999);
?>

	<div class="swiper-container posts-carousel items-carousel carousel-design-<?php echo esc_attr($design); ?> slider-<?php echo esc_attr($box_id); ?>" data-breakpoints="1" data-xs-slides="3" data-sm-slides="3" data-md-slides="3" data-lt-slides="3" data-slides-per-view="3" data-autoplay="<?php echo esc_attr( ($autoplay == 'yes') ? $speed : 'false' ); ?>">
	    <div class="swiper-wrapper">
		<?php foreach ( $posts as $post ): // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited valid use case ?>
			<?php
				$blocks_to_build = $post->custom_user_teaser === true ? $post->custom_teaser_blocks : $teaser_blocks;
				$block_style = isset( $post->bgcolor ) ? ' style="background-color: ' . $post->bgcolor . '"' : '';
			?>
                <div class="swiper-slide carousel-item"<?php echo wp_specialchars_decode($block_style); ?>>
                    <div class="post-item post-format-<?php echo esc_attr($post->post_format); ?>">
						<?php foreach ( $blocks_to_build as $block_data ): ?>
							<?php include $this->getBlockTemplate(); ?>
						<?php endforeach; ?>
					</div>
				</div>
		<?php endforeach; ?>
	    </div>
	</div>
<?php return; ?>