
<?php  if ( ! defined('ABSPATH')) exit('No direct script access allowed');
/**
 * The template for displaying theme copyrights
 *
 * @since   6.2.12
 * @version 1.0.0
 */

$disable_copyrights = etheme_get_query_custom_field('remove_copyrights');
$copyrights_color = etheme_get_option('copyrights_color', 'dark');
$fd = etheme_get_option('footer_demo', 1);
?>
<?php if( ! $disable_copyrights && ( is_active_sidebar('footer-copyrights') || $fd )): ?>
	<div class="footer-bottom text-color-<?php echo esc_attr($copyrights_color); ?>">

		<div class="container">

			<div class="row">

				<div class="col-sm-6 footer-copyrights" style="width: 35%;"><?php if(is_active_sidebar('footer-copyrights')): dynamic_sidebar('footer-copyrights'); else: if($fd) etheme_footer_demo('footer-copyrights'); endif; ?></div>
				<div class="col-sm-6 footer-copyrights-right" style="width: 65%;">
					<?php if(is_active_sidebar('footer-copyrights-right')): dynamic_sidebar('footer-copyrights-right'); else: if($fd) etheme_footer_demo('footer-copyrights-right'); endif; ?>							
				</div>
			</div>
		</div>
	</div>
<?php endif ?>