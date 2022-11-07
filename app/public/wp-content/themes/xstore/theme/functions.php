<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Script, styles, fonts
// **********************************************************************//  
if(!function_exists('etheme_theme_styles')) {
    function etheme_theme_styles() {
        if ( !is_admin() ) {
        	$theme = wp_get_theme();

	        $generated_css_js = get_option('etheme_generated_css_js');
	        $generated_css = false;

	        if ( isset($generated_css_js['css']) ){
		        if ( isset($generated_css_js['css']['is_enabled']) && $generated_css_js['css']['is_enabled'] ){
			        if ( file_exists ($generated_css_js['css']['path']) ){
				        $generated_css = true;
			        }
		        }
	        }


	        // Temp disable generated_css remove after fix on server
	        $generated_css = false;

	        switch (etheme_get_option('fa_icons_library', 'disable') ) {
                case '4.7.0':
	                wp_enqueue_style("etheme-fa",get_template_directory_uri().'/css/fontawesome/4.7.0/font-awesome.min.css', array(), $theme->version);
                    break;
		        case '5.15.3':
			        wp_enqueue_style("etheme-fa",get_template_directory_uri().'/css/fontawesome/5.15.3/all.min.css', array(), $theme->version);
                    break;
                default;
            }

        	if ($generated_css){
		        wp_enqueue_style("et-generated-css",$generated_css_js['css']['url'], array(), $theme->version);
            } else {
        	    
        	    $is_rtl = get_query_var('et_is-rtl', false);
		
		        foreach (etheme_config_css_files() as $script){
		            if ( !isset($script['deps'])) $script['deps'] = array("etheme-parent-style");
			
			        if ( $is_rtl ) {
				        $rtl_file = get_template_directory() . esc_attr( $script['file'] ) . '-rtl'.ETHEME_MIN_CSS.'.css';
				        if (file_exists($rtl_file)) {
					        $script['file'] .= '-rtl';
				        }
			        }
			        
			        wp_register_style(  'etheme-'.$script['name'], get_template_directory_uri() . $script['file'] . ETHEME_MIN_CSS .'.css', $script['deps'], $theme->version );
		        }
		        
                etheme_enqueue_style( 'parent-style' );

		        if ( class_exists( 'WPBMap' ) ) {
			        etheme_enqueue_style( "wpb-style" );
			        wp_enqueue_style( 'js_composer_front');
		        }

		        if ( defined( 'ELEMENTOR_VERSION' ) ) {
			        etheme_enqueue_style( "elementor-style" );
		        }
		        
		        if ( class_exists( 'bbPress' ) && is_bbpress() ) {
			        etheme_enqueue_style( "forum-style" );
		        }

		        if ( class_exists( 'WeDevs_Dokan' ) || class_exists( 'Dokan_Pro' ) ) {
			        etheme_enqueue_style( "dokan-style" );
		        }

		        if ( class_exists( 'WCFMmp' ) ) {
			        etheme_enqueue_style( "wcfmmp-style" );
		        }

		        if ( class_exists( 'WCMp' ) ) {
			        etheme_enqueue_style( "wcmp-style" );
		        }
		        
		        if ( class_exists('Cookie_Notice') ) {
			        etheme_enqueue_style( "cookie-notice-style" );
                }
		
		        // only for wpb
		        if ( isset($_GET['preview']) || get_query_var('et_is_customize_preview', false) ) {
			        etheme_enqueue_all_styles();
		        }
		        else {
			
			        $post_id = get_query_var( 'et_page-id', array( 'id' => 0, 'type' => 'page' ) );
			
			        if ( get_query_var( 'is_single_product', false ) ) {
				
				        if ( get_query_var( 'etheme_single_product_vertical_slider', false ) ) {
					        etheme_enqueue_style( 'slick-library' );
				        }
				
			        }
			
			        if ( in_array( get_query_var( 'et_sidebar', 'left' ), array(
					        'left',
					        'right',
					        'off_canvas'
				        ) ) || ( get_query_var( 'is_mobile', false ) && get_query_var( 'et_sidebar-mobile', 'bottom' ) == 'off_canvas' ) ) {
				        etheme_enqueue_style( 'sidebar' );
				        if ( get_query_var( 'et_sidebar', 'left' ) == 'off_canvas' || ( get_query_var( 'is_mobile', false ) && get_query_var( 'et_sidebar-mobile', 'bottom' ) == 'off_canvas' ) ) {
					        etheme_enqueue_style( 'sidebar-off-canvas' );
				        }
				        if ( get_query_var( 'et_sidebar-widgets-scroll', false ) ) {
					        etheme_enqueue_style( 'sidebar-widgets-with-scroll' );
				        }
			        }
			        if ( get_query_var( 'et_widgets-open-close', false ) ) {
				        etheme_enqueue_style( 'widgets-open-close' );
			        }
			
			        if ( get_query_var( 'et_fixed-footer', false ) ) {
				        etheme_enqueue_style( 'fixed-footer' );
			        }
			
			        if ( get_query_var( 'et_breadcrumbs', false ) ) {
				        etheme_enqueue_style( 'breadcrumbs' );
			        }
			
			        if ( get_query_var( 'et_mobile-optimization', false ) ) {
				        // back top
				        if ( get_query_var( 'et_btt', false ) && ! get_query_var( 'is_mobile', false ) || get_query_var( 'et_btt-mobile', false ) && get_query_var( 'is_mobile', false ) ) {
					        etheme_enqueue_style( 'back-top' );
				        }
				        if ( get_theme_mod( 'mobile_panel_et-mobile', '0' ) && get_query_var( 'is_mobile', false ) ) {
					        etheme_enqueue_style( 'mobile-panel' );
				        }
			        } else {
				        if ( get_query_var( 'et_btt', false ) || ( get_query_var( 'et_btt-mobile', false ) && get_query_var( 'is_mobile', false ) ) ) {
					        etheme_enqueue_style( 'back-top' );
				        }
				        if ( get_theme_mod( 'mobile_panel_et-mobile', '0' ) || get_query_var( 'et_is_customize_preview', false ) ) {
					        etheme_enqueue_style( 'mobile-panel' );
				        }
			        }
			
			        // add pagination globally if has pages
			        global $wp_query;
			        if ( $wp_query->max_num_pages > 1 ) {
				        etheme_enqueue_style( 'pagination' );
			        }
			
			        if ( get_query_var( 'et_is-woocommerce', false ) ) {
				
				        etheme_enqueue_style( 'woocommerce' );
				
				        if ( get_query_var( 'et_is-woocommerce-archive', false ) || get_query_var( 'is_single_product', false ) || get_query_var( 'is_single_product_shortcode', false ) ) {
					        etheme_enqueue_style( 'woocommerce-archive' );
					        if ( get_query_var( 'et_is-swatches', false ) ) {
						        etheme_enqueue_style( "swatches-style" );
					        }
					
					        if ( get_query_var( 'et_is-catalog', false ) ) {
						        etheme_enqueue_style( 'catalog-mode' );
					        }
					
				        }
				        if ( get_query_var( 'is_single_product', false ) || get_query_var( 'is_single_product_shortcode', false ) ) {
					        if ( ! get_query_var( 'etheme_single_product_builder', false ) ) {
						        if ( in_array( get_query_var( 'et_product-layout', 'default' ), array(
							        'wide',
							        'right',
							        'booking'
						        ) ) ) {
							        etheme_enqueue_style( 'single-product-' . get_query_var( 'et_product-layout', 'default' ) );
						        }
						        etheme_enqueue_style( 'single-product' );
					        } else {
						        etheme_enqueue_style( 'single-product-builder' );
					        }
					        etheme_enqueue_style( 'single-product-elements' );
					        if ( ! get_query_var( 'is_single_product_shortcode', false ) && get_theme_mod( 'sticky_add_to_cart_et-desktop', 0 ) ) {
						        etheme_enqueue_style( 'single-product-sticky-cart' );
					        }
				        } elseif ( get_query_var( 'et_is-woocommerce-archive', false ) ) {
					        if ( is_active_sidebar( 'shop-filters-sidebar' ) ) {
						        if ( get_query_var( 'et_sidebar-widgets-scroll', false ) ) {
							        etheme_enqueue_style( 'sidebar-widgets-with-scroll' );
						        }
						        if ( get_query_var( 'et_filters-area-widgets-open-close', false ) ) {
							        etheme_enqueue_style( 'widgets-open-close' );
						        }
					        }
					        if ( etheme_get_option( 'shop_full_width', 0 ) ) {
						        etheme_enqueue_style( 'shop-full-width' );
					        }
					        if ( ! in_array( get_query_var( 'et_product-view', 'disable' ), array(
						        'disable',
						        'custom'
					        ) ) ) {
						        etheme_enqueue_style( 'product-view-' . get_query_var( 'et_product-view', 'disable' ) );
					        }
					        if ( get_query_var( 'et_custom_product_template', 0 ) ) {
						        etheme_enqueue_style( 'content-product-custom' );
					        }
					
					        if ( get_query_var('et_is-quick-view', false) ) {
						        etheme_enqueue_style( "quick-view" );
						        if ( get_query_var('et_is-quick-view-type', 'popup') == 'off_canvas' ) {
							        etheme_enqueue_style( "off-canvas" );
						        }
					        }
					        
					        etheme_enqueue_style( 'no-products-found' );
					        if ( class_exists( 'SB_WooCommerce_Infinite_Scroll' ) || class_exists('SBWIS_WooCommerce_Infinite_Scroll') ) {
						        etheme_enqueue_style( 'sb-infinite-scroll-load-more' );
					        }
				        } elseif ( is_account_page() ) {
					        etheme_enqueue_style( 'account-page' );
				        } //		        elseif ( is_cart() || is_checkout() ) {
                        elseif ( get_query_var( 'et_is-cart', false ) || get_query_var( 'et_is-checkout', false ) ) {
					
					        etheme_enqueue_style( 'cart-page' );
					        etheme_enqueue_style( 'no-products-found' );
					
					        etheme_enqueue_style( 'checkout-page' );
					
					        etheme_enqueue_style( 'thank-you-page' );
				        }
				
				        if ( class_exists( 'YITH_Woocompare_Frontend' ) ) {
					        etheme_enqueue_style( 'yith-compare' );
				        }
				
			        }
			
			        // or single post
			        if ( get_query_var( 'et_is-blog-archive', false ) ) {
				        etheme_enqueue_style( 'blog-global' );
				        etheme_enqueue_style( 'post-global' );
				        etheme_enqueue_style( 'post-quote' );
				        $content_layout = etheme_get_option( 'blog_layout', 'default' );
				        switch ( $content_layout ) {
					        case 'grid':
					        case 'grid2':
						        etheme_enqueue_style( 'post-grid-grid2' );
						        if ( !get_query_var('et_is-single', false) ) {
							        if ( etheme_get_option( 'blog_full_width', 0 ) ) {
								        etheme_enqueue_style( 'blog-full-width' );
							        }
							        if ( etheme_get_option( 'blog_masonry', 0 ) ) {
								        etheme_enqueue_style( 'blog-masonry' );
							        }
						        }
						        break;
					        case 'default':
					        case 'center':
						        break;
					        case 'timeline':
					        case 'timeline2':
						        etheme_enqueue_style( 'post-timeline' );
						        break;
					        case 'small':
					        case 'chess':
						        etheme_enqueue_style( 'post-small-chess' );
						        break;
					        default:
						        etheme_enqueue_style( 'post-' . $content_layout );
						        break;
				        }
				        if ( etheme_get_option( 'blog_navigation_type', 'pagination' ) == 'pagination' ) {
					        etheme_enqueue_style( 'pagination' );
				        } else {
					        if ( !get_query_var('et_is-single', false) ) {
						        etheme_enqueue_style( 'blog-ajax' );
					        }
				        }
			        }
			
			        if ( get_query_var( 'is_single_product', false ) ) {
				        etheme_enqueue_style( 'star-rating' );
				        etheme_enqueue_style( 'comments' );
				        etheme_enqueue_style( 'single-post-meta' );
			        } // @todo if single portfolio include some styles too

                    elseif ( get_query_var( 'et_is-single', false ) ) {
				        etheme_enqueue_style( 'blog-global' );
				        etheme_enqueue_style( 'post-global' );
				        etheme_enqueue_style( 'single-post-global' );
				        etheme_enqueue_style( 'single-post-meta' );
				        // if comments allowed
				        etheme_enqueue_style( 'star-rating' );
				        etheme_enqueue_style( 'comments' );

				        $template = get_query_var( 'et_post-template', 'default' );
				        if ( $template != 'default' ) {
					        switch ( $template ) {
						        case 'large2':
							        etheme_enqueue_style( 'single-post-large' );
							        break;
						        case 'full-width':
							        // if testimonials but more conditions for no reason
							        etheme_enqueue_style( 'single-testimonials' );
							        break;
					        }
					
					        etheme_enqueue_style( 'single-post-' . $template );
				        }
			        }
			
			        if ( get_query_var( 'et_portfolio-page', false ) == $post_id['id'] ) {
				        etheme_enqueue_style( 'portfolio' );
				        // mostly filters are not shown on portfolio category
				        if ( ! get_query_var( 'portfolio_category' ) ) {
					        etheme_enqueue_style( 'isotope-filters' );
				        }
			        }
			
			        if ( is_404() ) {
				        etheme_enqueue_style( '404-page' );
			        }
			
			        if ( class_exists( 'WPCF7' ) || defined( 'MC4WP_VERSION' ) ) {
				        etheme_enqueue_style( 'contact-forms' );
			        }
			
			        if ( class_exists( 'YITH_Woocompare' ) ) {
				        etheme_enqueue_style( 'yith-compare' );
			        }
			
			        if ( is_search() ) {
				        etheme_enqueue_style( 'search-page' );
				        etheme_enqueue_style( 'no-products-found' );
			        }
			
			        // 3d-party plugins
			        // yith wishlist
			        if ( function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) {
				        etheme_enqueue_style( 'wishlist-page' );
			        }
			
		        }
		        
	        }
	        
	        wp_register_style( 'xstore-inline-css', false );
	        wp_register_style( 'xstore-inline-desktop-css', false );
	        wp_register_style( 'xstore-inline-tablet-css', false );
	        wp_register_style( 'xstore-inline-mobile-css', false );
	
//	        $post_id = get_query_var('et_page-id');
	
	        if ( get_query_var('et_is-woocommerce-archive', false) ) {
                
                if ( get_query_var('et_widgets-show-more', false) ) {
	                wp_add_inline_style( 'xstore-inline-css',
		                '.sidebar .sidebar-widget:not(.etheme_swatches_filter):not(.null-instagram-feed) ul:not(.children):not(.sub-menu) > li:nth-child('.get_query_var('et_widgets-show-more-after', 3).')
							~ li:not(.et_widget-open):not(.et_widget-show-more):not(.current-cat):not(.current-item):not(.selected),
							 .sidebar-widget ul.menu > li:nth-child('.get_query_var('et_widgets-show-more-after', 3).')
							~ li:not(.et_widget-open):not(.et_widget-show-more){
								   display: none;
							}');
                }
            }

		    if ( ! defined( 'ET_CORE_VERSION' ) ) {
			    etheme_enqueue_style('et-core-plugin-off');
		    }

        	$icons_type = ( etheme_get_option('bold_icons', 0) ) ? 'bold' : 'light';

        	wp_register_style( 'xstore-icons-font', false );
            wp_enqueue_style( 'xstore-icons-font' );
            wp_add_inline_style( 'xstore-icons-font', 
	            "@font-face {
				  font-family: 'xstore-icons';
				  src:
				    url('".get_template_directory_uri()."/fonts/xstore-icons-".$icons_type.".ttf') format('truetype'),
				    url('".get_template_directory_uri()."/fonts/xstore-icons-".$icons_type.".woff2') format('woff2'),
				    url('".get_template_directory_uri()."/fonts/xstore-icons-".$icons_type.".woff') format('woff'),
				    url('".get_template_directory_uri()."/fonts/xstore-icons-".$icons_type.".svg#xstore-icons') format('svg');
				  font-weight: normal;
				  font-style: normal;
				  font-display: swap;
				}"
			);

			if( etheme_get_option('dark_styles', 0) ) {
				etheme_enqueue_style('dark');
        	}

        	do_action('etheme_last_style');
	
	        // tweak for media queries (start)
	        wp_add_inline_style( 'xstore-inline-tablet-css', '@media only screen and (max-width: 992px) {' );
         
	        // tweak for media queries (start)
	        wp_add_inline_style( 'xstore-inline-mobile-css', '@media only screen and (max-width: 767px) {' );
	
        }
    }
}

// to remove default enqueue of rtl style from wp
remove_action( 'wp_head', 'locale_stylesheet' );

add_action( 'wp_enqueue_scripts', 'etheme_theme_styles', 30);

add_action('wp_enqueue_scripts', 'etheme_theme_header_styles', 45);

function etheme_theme_header_styles() {
    // takes all elements from header parts that are used 440ms of load time
    $header        = array();
    $header_parts = array(
        'top',
        'main',
        'bottom'
    );
    if ( !get_query_var('et_mobile-optimization', false) ) {
	    foreach ( $header_parts as $header_part ) {
	     
		    $header[$header_part] = json_decode( get_theme_mod( 'header_' . $header_part . '_elements', '' ), true );
		    if ( ! is_array( $header[$header_part] ) ) {
			    $header[$header_part] = array();
		    }
		    uasort( $header[$header_part], function ( $item1, $item2 ) {
			    return $item1['index'] <=> $item2['index'];
		    } );
		
		    $header['mobile_'.$header_part] = json_decode( get_theme_mod( 'header_mobile_' . $header_part . '_elements', '' ), true );
		    if ( ! is_array( $header['mobile_'.$header_part] ) ) {
			    $header['mobile_'.$header_part] = array();
		    }
		    uasort( $header['mobile_'.$header_part], function ( $item1, $item2 ) {
			    return $item1['index'] <=> $item2['index'];
		    } );
		    
        }
    }
    else {
	    foreach ( $header_parts as $header_part ) {
		    $header[$header_part] = json_decode( get_theme_mod( 'header_' . ( get_query_var( 'is_mobile', false ) ? 'mobile_' : '' ) . $header_part . '_elements', '' ), true );
		    if ( ! is_array( $header[$header_part] ) ) {
			    $header[$header_part] = array();
		    }
		    uasort( $header[$header_part], function ( $item1, $item2 ) {
			    return $item1['index'] <=> $item2['index'];
		    } );
	    }
    }
	
	$header_elements = array();
	foreach ( $header as $key => $value ) {
		foreach ( $value as $header_part_key => $header_part_value ) {
			if ( $header_part_value['element'] == 'connect_block' ) {
				
				$blocks = array();
				if ( !get_query_var('et_mobile-optimization', false) ) {
					$blocks[] = get_theme_mod( 'connect_block_package' );
					$blocks[] = get_theme_mod( 'connect_block_mobile_package' );
				}
				else {
					$blocks[] = get_theme_mod( 'connect_block_' . ( get_query_var( 'is_mobile', false ) ? 'mobile_' : '' ) . 'package' );
				}
				
				foreach ($blocks as $block) {
					if ( $block && count( $block ) ) {
						
						if ( $header_part_key !== false ) {
							$key  = array_search( $header_part_key, array_column( $block, 'id' ) );
							$data = json_decode( $block[ $key ]['data'], true );
							
							if ( ! is_array( $data ) ) {
								$data = array();
							}
							
							uasort( $data, function ( $item1, $item2 ) {
								return $item1['index'] <=> $item2['index'];
							} );
							
							foreach ( $data as $key => $value ) {
								$header_elements[] = $key;
							}
						}
					}
				}
				continue;
			}
			$header_elements[] = $header_part_value['element'];
		}
	}
    
    foreach ( $header_elements as $header_element ) {
        do_action('etheme_load_'.$header_element.'_styles');
        switch ( $header_element ) {
            case 'all_departments':
                etheme_enqueue_style( 'all-departments-menu' );
                break;
	        case 'search':
		        etheme_enqueue_style( 'header-search' );
		        if ( get_theme_mod( 'search_ajax_et-desktop', '1' ) ) {
			        $search_type = get_theme_mod( 'search_type_et-desktop', 'input' ); // icon, full form
			        $search_type = apply_filters('search_type', $search_type);
			
			        $is_popup = $search_type == 'popup';
			        if ( apply_filters('search_mode_is_popup', $is_popup) ) {
//				        if ( $element_options['is_woocommerce'] && in_array( 'products', $element_options['search_content'] ) ) {
					        etheme_enqueue_style( 'woocommerce' );
					        etheme_enqueue_style( 'woocommerce-archive' );
					        etheme_enqueue_style( 'product-view-default' );
//				        }
//				        if ( in_array( 'posts', $element_options['search_content'] ) ) {
					        etheme_enqueue_style( 'blog-global' );
//				        }
//				        if ( in_array( 'portfolio', $element_options['search_content'] ) ) {
					        etheme_enqueue_style( 'portfolio' );
//				        }
                    }
                }
		        break;
	        case 'contacts':
		        etheme_enqueue_style( 'header-contacts' );
		        break;
	        case 'main_menu':
	        case 'secondary_menu':
		        etheme_enqueue_style( 'header-menu' );
		        break;
            default;
        }
    }
    
    if ( get_theme_mod( 'header_vertical_et-desktop', '0' ) && (! get_query_var( 'is_mobile', false ) || apply_filters('etheme_force_enqueue_header_vertical_on_mobile', false)) ) {
	    // for some cases when no menus added in main header but style should be enqueued anyway for vertical header menu
	    etheme_enqueue_style( 'header-menu' );
	    etheme_enqueue_style( 'header-mobile-menu' );
        etheme_enqueue_style( 'header-vertical' );
    }
}

if ( !function_exists('etheme_config_css_files')) {
	function etheme_config_css_files() {
		return include get_template_directory() . '/framework/config-css.php';
	}
}

if ( !function_exists('etheme_enqueue_style')) {
	function etheme_enqueue_style($filename, $force_enqueue = false, $include_globally = true) {
		
		global $etheme_styles;
		$config = etheme_config_css_files();
		$did_wp_head_action = did_action('wp_head'); // check if content is real and not $post->post_content (seo plugins like to parse)
		if ( !isset($config[$filename])) return;
		
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$force_enqueue = true;
		}
		
		if ( in_array($config[$filename]['name'], (array)$etheme_styles)) {
			return;
		}
		
		// always enqueue styles but if include inline then the code below will make the deal
		wp_enqueue_style( 'etheme-'.$filename );
		
		if ( !$force_enqueue ) {
			if ( $did_wp_head_action && $include_globally ) {
				$etheme_styles[] = $config[ $filename ]['name'];
			}
			return;
		}
		
		if ( get_query_var('et_is-rtl', false) ) {
			$rtl_file = get_template_directory() . esc_attr( $config[$filename]['file']) . '-rtl'.ETHEME_MIN_CSS.'.css';
			if (file_exists($rtl_file)) {
				$config[$filename]['file'] .= '-rtl';
			}
		}
		
		$postfix = '';
		if ( !etheme_get_option( 'cssjs_ver', 0 ) ) {
			$theme = wp_get_theme();
			$postfix = '?ver='.$theme->version;
		}
//	    $loadCssfilename = get_template_directory_uri() . esc_attr( $config[$filename]['file'] ).'.css?ver='.esc_attr( $theme->version );
//	    wp_add_inline_script( 'load_css', "loadCSS('".$loadCssfilename."');", 'after' );
		?>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri() . esc_attr( $config[$filename]['file'] ) . ETHEME_MIN_CSS; ?>.css<?php echo esc_attr( $postfix ); ?>" type="text/css" media="all" /> <?php // phpcs:ignore ?>
		<?php
		if ( $did_wp_head_action && $include_globally ) {
			$etheme_styles[] = $config[ $filename ]['name'];
		}
	}
}

if ( !function_exists('etheme_enqueue_all_styles')) {
    function etheme_enqueue_all_styles() {
	    $theme = wp_get_theme();
	    $config = etheme_config_css_files();
	    foreach ($config as $filedata) {
	        if (in_array($filedata['name'], array('fixed-footer', 'header-vertical'))) continue;
		    if ( in_array($filedata['name'], array('single-product', 'single-product-wide', 'single-product-right', 'single-product-booking')) && get_query_var( 'etheme_single_product_builder', false ) ) {
		        continue;
            }
		    elseif ( in_array($filedata['name'], array('single-product-builder') ) && !get_query_var( 'etheme_single_product_builder', false ) ) {
		        continue;
            }
	        
		    wp_enqueue_style( 'etheme-'.$filedata['name'] );?>
<!--            <link rel="stylesheet" id="--><?php //echo 'etheme-'.esc_attr( $filedata['name'] ); ?><!---css" href="--><?php //echo get_template_directory_uri() . esc_attr( $filedata['file'] ); ?><!--?ver=--><?php //echo esc_attr( $theme->version ); ?><!--" type="text/css" media="all" /> --><?php //// phpcs:ignore ?>
            <?php
        }
    }
}

 if ( ! function_exists( 'etheme_theme_styles_after' ) ) {
	function etheme_theme_styles_after() {
		wp_enqueue_style( 'xstore-inline-css' );
		// tweak for media queries (end)
		wp_add_inline_style( 'xstore-inline-tablet-css', '}' );
		wp_enqueue_style( 'xstore-inline-tablet-css' );
		// tweak for media queries (end)
		wp_add_inline_style( 'xstore-inline-mobile-css', '}' );
		wp_enqueue_style( 'xstore-inline-mobile-css' );

		if ( function_exists('vc_build_safe_css_class') ) {

		    $et_fonts = get_option( 'etheme-fonts', false );

		    // remove custom fonts from vc_google_fonts wp_enqueue_style to prevent site speed falling  
			if ( $et_fonts ) {
			    foreach ( $et_fonts as $value ) {
			    	wp_dequeue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $value['name'] ) );
			    }
			}
		}
	}
}

add_action( 'wp_footer', 'etheme_theme_styles_after', 10 );

// **********************************************************************// 
// ! Plugins activation
// **********************************************************************// 
if(!function_exists('etheme_register_required_plugins')) {
    global $pagenow;

    if ($pagenow!='plugins.php'){
	    add_action('tgmpa_register', 'etheme_register_required_plugins');
    }

	function etheme_register_required_plugins() {
		if( ! etheme_is_activated() ) return;

		$activated_data = get_option( 'etheme_activated_data' );

		$key = $activated_data['api_key'];

		if( ! $key || empty( $key ) ) return;

		$plugins = get_transient( 'etheme_plugins_info' );
		if (! $plugins || empty( $plugins ) || isset($_GET['et_clear_plugins_transient'])){
			$plugins_dir = ETHEME_API . 'files/get/';
			$token = '?token=' . $key;
			$url = 'https://8theme.com/import/xstore-demos/1/plugins/?plugins_dir=' . $plugins_dir . '&token=' .$token;
			$response = wp_remote_get( $url );
			$response = json_decode(wp_remote_retrieve_body( $response ), true);
			$plugins = $response;
			set_transient( 'etheme_plugins_info', $plugins, 24 * HOUR_IN_SECONDS );
		}

        if ( ! $plugins || ! is_array($plugins) || ! count($plugins) ){
	        $plugins = array();
        }
		// Change this to your theme text domain, used for internationalising strings

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'domain'       		=> 'xstore',         	// Text domain - likely want to be the same as your theme.
			'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
			'menu'         		=> 'install-required-plugins', 	// Menu slug
			'has_notices'      	=> false,                       	// Show admin notices or not
			'is_automatic'    	=> true,					   	// Automatically activate plugins after installation or not
			'message' 			=> '',							// Message to output right before the plugins table
			'strings'      		=> array(
				'page_title'                       			=> esc_html__( 'Install Required Plugins', 'xstore'),
				'menu_title'                       			=> esc_html__( 'Install Plugins', 'xstore' ),
				'installing'                       			=> esc_html__( 'Installing Plugin: %s', 'xstore' ), // %1$s = plugin name
				'downloading_package'                       => esc_html__( 'Downloading the install package&#8230;', 'xstore' ), // %1$s = plugin name
				'oops'                             			=> esc_html__( 'Something went wrong with the plugin API.', 'xstore' ),
				'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'xstore' ), // %1$s = plugin name(s)
				'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'xstore' ), // %1$s = plugin name(s)
				'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'xstore' ), // %1$s = plugin name(s)
				'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'xstore' ), // %1$s = plugin name(s)
				'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'xstore' ), // %1$s = plugin name(s)
				'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'xstore' ), // %1$s = plugin name(s)
				'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'xstore' ), // %1$s = plugin name(s)
				'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'xstore' ), // %1$s = plugin name(s)
				'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'xstore' ),
				'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'xstore' ),
				'return'                           			=> esc_html__( 'Return to Required Plugins Installer', 'xstore' ),
				'plugin_activated'                 			=> esc_html__( 'Plugin activated successfully.', 'xstore' ),
				'complete' 									=> esc_html__( 'All plugins installed and activated successfully. %s', 'xstore' ), // %1$s = dashboard link
				'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
			)
		);

		tgmpa($plugins, $config);
	}
}

// **********************************************************************// 
// ! Footer Demo Widgets
// **********************************************************************// 

if(!function_exists('etheme_footer_demo')) {
    function etheme_footer_demo($position){
        switch ($position) {
        	
            case 'footer-copyrights':
                ?>
					<div class="etheme_footer_box">

						<!-- etheme_footer_box_left ********************************************************************** -->
						<div class="etheme_footer_box_left">
							<div class="footer_box_image">
								<img src="https://www.domain.com/static/cs/img/inverted-logo.svg">
							</div>
						</div>

					</div>
                <?php
            break;

            case 'footer-copyrights-right':
                ?>
					<!-- etheme_footer_box_right ********************************************************************** -->
					<div class="etheme_footer_box_right">

						<!-- ********************************************************************** -->
						<div class="etheme_footer_box_right_01">
							<h3 style="color: #fff;">Subscribe To Our Weekly Newsletter</h3>
							<form class="etheme-search-form" role="search" action="http://localhost:10003" method="get" type="inline">
	
							    <div class="etheme-search-input-form-wrapper">
									
						                                
					                <div class="etheme-search-input-wrapper">
						                
					                    <input placeholder="Search for products..." class="etheme-search-form-input" type="search" name="s" title="Search" value="" autocomplete="off">
					                    
					                    <input type="hidden" name="et_search" value="true">
					                    
						                                        
						                                        
					                    <span class="etheme-search-form-clear">
					                        <svg xmlns="http://www.w3.org/2000/svg" width=".7em" height=".7em" viewBox="0 0 24 24" fill="currentColor"><path d="M13.056 12l10.728-10.704c0.144-0.144 0.216-0.336 0.216-0.552 0-0.192-0.072-0.384-0.216-0.528-0.144-0.12-0.336-0.216-0.528-0.216 0 0 0 0 0 0-0.192 0-0.408 0.072-0.528 0.216l-10.728 10.728-10.704-10.728c-0.288-0.288-0.768-0.288-1.056 0-0.168 0.144-0.24 0.336-0.24 0.528 0 0.216 0.072 0.408 0.216 0.552l10.728 10.704-10.728 10.704c-0.144 0.144-0.216 0.336-0.216 0.552s0.072 0.384 0.216 0.528c0.288 0.288 0.768 0.288 1.056 0l10.728-10.728 10.704 10.704c0.144 0.144 0.336 0.216 0.528 0.216s0.384-0.072 0.528-0.216c0.144-0.144 0.216-0.336 0.216-0.528s-0.072-0.384-0.216-0.528l-10.704-10.704z"></path></svg>
					                    </span>
						                
					                </div>
					                
					                <div class="etheme-search-form-button-wrapper">
						                    
					                    <button class="etheme-search-form-submit" type="submit" title="Search" aria-label="Search">
					                        
					                        			<i aria-hidden="true" class="fas fa-search"></i><span class="elementor-screen-only">Search</span>                        
					                    </button>
					                </div>
						            
					            </div>

					            <div class="etheme-search-ajax-results"><div class="autocomplete-suggestions" style="position: absolute; display: none; max-height: 300px; z-index: 9999;"></div></div>
									
							</form>
						</div>

						<!-- ********************************************************************** -->
						<div class="etheme_footer_box_right_02">
							<div class="elementor-container elementor-column-gap-default">
								<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-ecab579" data-id="ecab579" data-element_type="column">
						<div class="elementor-widget-wrap elementor-element-populated">
											<div class="elementor-element elementor-element-28f5ca1 elementor-widget elementor-widget-heading" data-id="28f5ca1" data-element_type="widget" data-widget_type="heading.default">
							<div class="elementor-widget-container">
						<h4 class="elementor-heading-title elementor-size-default">Add Your</h4>		</div>
							</div>
							<div class="elementor-element elementor-element-3d51d3f elementor-widget elementor-widget-text-editor" data-id="3d51d3f" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>Lorem ipsum dolor</p>						</div>
							</div>
							<div class="elementor-element elementor-element-0c02e83 elementor-widget elementor-widget-text-editor" data-id="0c02e83" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>pulvinar dapibus leo.</p>						</div>
							</div>
							<div class="elementor-element elementor-element-d40c73c elementor-widget elementor-widget-text-editor" data-id="d40c73c" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>adipiscing elit. Ut elit tellus</p>						</div>
							</div>
							<div class="elementor-element elementor-element-a79520f elementor-widget elementor-widget-text-editor" data-id="a79520f" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>luctus nec ullamcorper mattis</p>						</div>
							</div>
								</div>
						</div>
							<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-c5c37d6" data-id="c5c37d6" data-element_type="column">
						<div class="elementor-widget-wrap elementor-element-populated">
											<div class="elementor-element elementor-element-8cb461d elementor-widget elementor-widget-heading" data-id="8cb461d" data-element_type="widget" data-widget_type="heading.default">
							<div class="elementor-widget-container">
						<h4 class="elementor-heading-title elementor-size-default">Your Heading </h4>		</div>
							</div>
							<div class="elementor-element elementor-element-0c5ae48 elementor-widget elementor-widget-text-editor" data-id="0c5ae48" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>Lorem ipsum dolor</p>						</div>
							</div>
							<div class="elementor-element elementor-element-df0200b elementor-widget elementor-widget-text-editor" data-id="df0200b" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>pulvinar dapibus leo.</p>						</div>
							</div>
							<div class="elementor-element elementor-element-50ffff0 elementor-widget elementor-widget-text-editor" data-id="50ffff0" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>adipiscing elit. Ut elit tellus</p>						</div>
							</div>
							<div class="elementor-element elementor-element-8298641 elementor-widget elementor-widget-text-editor" data-id="8298641" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>luctus nec ullamcorper mattis</p>						</div>
							</div>
								</div>
						</div>
							<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-3f14d3d" data-id="3f14d3d" data-element_type="column">
						<div class="elementor-widget-wrap elementor-element-populated">
											<div class="elementor-element elementor-element-f9244c8 elementor-widget elementor-widget-heading" data-id="f9244c8" data-element_type="widget" data-widget_type="heading.default">
							<div class="elementor-widget-container">
						<h4 class="elementor-heading-title elementor-size-default">Text Here</h4>		</div>
							</div>
							<div class="elementor-element elementor-element-ae196cd elementor-widget elementor-widget-text-editor" data-id="ae196cd" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>Lorem ipsum dolor</p>						</div>
							</div>
							<div class="elementor-element elementor-element-7e8f285 elementor-widget elementor-widget-text-editor" data-id="7e8f285" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>pulvinar dapibus leo.</p>						</div>
							</div>
							<div class="elementor-element elementor-element-43628f2 elementor-widget elementor-widget-text-editor" data-id="43628f2" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>adipiscing elit. Ut elit tellus</p>						</div>
							</div>
							<div class="elementor-element elementor-element-707dcc9 elementor-widget elementor-widget-text-editor" data-id="707dcc9" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>luctus nec ullamcorper mattis</p>						</div>
							</div>
								</div>
						</div>
							<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-7e88be9" data-id="7e88be9" data-element_type="column">
						<div class="elementor-widget-wrap elementor-element-populated">
											<div class="elementor-element elementor-element-29c56a4 elementor-widget elementor-widget-heading" data-id="29c56a4" data-element_type="widget" data-widget_type="heading.default">
							<div class="elementor-widget-container">
						<h4 class="elementor-heading-title elementor-size-default">Add Here</h4>		</div>
							</div>
							<div class="elementor-element elementor-element-b8f8b7d elementor-widget elementor-widget-text-editor" data-id="b8f8b7d" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>Lorem ipsum dolor</p>						</div>
							</div>
							<div class="elementor-element elementor-element-85185c2 elementor-widget elementor-widget-text-editor" data-id="85185c2" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>pulvinar dapibus leo.</p>						</div>
							</div>
							<div class="elementor-element elementor-element-7855768 elementor-widget elementor-widget-text-editor" data-id="7855768" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>adipiscing elit. Ut elit tellus</p>						</div>
							</div>
							<div class="elementor-element elementor-element-b9ad368 elementor-widget elementor-widget-text-editor" data-id="b9ad368" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
										<p>luctus nec ullamcorper mattis</p>						</div>
							</div>
								</div>
						</div>
							</div>
						</div>

						<!-- ********************************************************************** -->
						<div class="etheme_footer_box_right_03">
							<div class="elementor-container elementor-column-gap-default">
										<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-25e202b" data-id="25e202b" data-element_type="column">
								<div class="elementor-widget-wrap elementor-element-populated">
													<div class="elementor-element elementor-element-37632c3 elementor-widget elementor-widget-heading" data-id="37632c3" data-element_type="widget" data-widget_type="heading.default">
									<div class="elementor-widget-container">
								<h2 class="elementor-heading-title elementor-size-default">Find Us Here</h2>		</div>
									</div>
										</div>
							</div>
									<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-fbd11a2" data-id="fbd11a2" data-element_type="column">
								<div class="elementor-widget-wrap elementor-element-populated">
													<div class="elementor-element elementor-element-b7f706f elementor-shape-circle e-grid-align-right e-grid-align-mobile-center elementor-grid-0 elementor-widget elementor-widget-social-icons" data-id="b7f706f" data-element_type="widget" data-widget_type="social-icons.default">
									<div class="elementor-widget-container">
										<div class="elementor-social-icons-wrapper elementor-grid">
												<span class="elementor-grid-item">
										<a class="elementor-icon elementor-social-icon elementor-social-icon-facebook-f elementor-animation-grow elementor-repeater-item-4ab3639" target="_blank">
											<span class="elementor-screen-only">Facebook-f</span>
											<i class="fab fa-facebook-f"></i>					</a>
									</span>
												<span class="elementor-grid-item">
										<a class="elementor-icon elementor-social-icon elementor-social-icon-twitter elementor-animation-grow elementor-repeater-item-c6cf71e" target="_blank">
											<span class="elementor-screen-only">Twitter</span>
											<i class="fab fa-twitter"></i>					</a>
									</span>
												<span class="elementor-grid-item">
										<a class="elementor-icon elementor-social-icon elementor-social-icon-linkedin-in elementor-animation-grow elementor-repeater-item-19d88b2" target="_blank">
											<span class="elementor-screen-only">Linkedin-in</span>
											<i class="fab fa-linkedin-in"></i>					</a>
									</span>
												<span class="elementor-grid-item">
										<a class="elementor-icon elementor-social-icon elementor-social-icon-youtube elementor-animation-grow elementor-repeater-item-baa991c" target="_blank">
											<span class="elementor-screen-only">Youtube</span>
											<i class="fab fa-youtube"></i>					</a>
									</span>
										</div>
									</div>
									</div>
										</div>
							</div>
							</div>
						</div>
					</div>
                <?php
            break;
        }
    }
}