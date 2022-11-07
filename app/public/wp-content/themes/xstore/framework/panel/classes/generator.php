<?php if ( ! defined( 'ABSPATH' ) ) exit( 'No direct script access allowed' );
/**
 * Etheme Admin Panel Generator Class.
 *
 * @since   7.0.0
 * @version 1.0.0
 */
class Etheme_Generator{

    public $api_url = 'https://www.8theme.com/generator-api/';
	// ! Main construct
	function __construct(){}

	/**
	 * Remove generated files
	 *
	 * @version  1.0.0
	 * @since  7.0.0
	 */
	public function generator_remover(){
		$etheme_generated_css_js = get_option('etheme_generated_css_js');
		$path = $etheme_generated_css_js['css']['path'];
		unlink($path);
		if ( ! file_exists($path) ){
			$etheme_generated_css_js['css'] = array();
			update_option('etheme_generated_css_js', $etheme_generated_css_js);
			$this_response['status'] = 'success';
			$this_response['response']['msg'] = esc_html__('File was removed', 'xstore');
			$this_response['response']['html'] = '';//<p class="et-message et-info">' . esc_html__('There is no generated file yet. ', 'xstore') . '</p>';
		} else {
			$this_response['status'] = 'error';
			$this_response['response']['msg'] = esc_html__('Cannot remove file', 'xstore');
		}
		$this->return($this_response);
	}

	/**
	 * Generate size from bytes
	 *
	 * @version  1.0.0
	 * @since   7.0.0
     * @param string $bytes size in bytes
     * @return string size in bytes, KB, MB, GB
	 */
	public function file_size( $bytes ){
		if ( $bytes  >= 1073741824 ) {
			$bytes  = number_format( $bytes  / 1073741824, 2 ) . ' GB';
		} elseif ( $bytes  >= 1048576) {
			$bytes  = number_format( $bytes  / 1048576, 2 ) . ' MB';
		} elseif ( $bytes  >= 1024 ) {
			$bytes  = number_format( $bytes  / 1024, 2 ) . ' KB';
		} elseif ( $bytes  > 1 ) {
			$bytes  = $bytes  . ' bytes';
		} elseif ( $bytes  == 1 ) {
			$bytes  = $bytes  . ' byte';
		} else {
			$bytes  = '0 bytes';
		}
		return $bytes;
	}

	/**
	 * Generate files
	 *
	 * @version  1.0.0
	 * @since  7.0.0
	 */
	public function generator(){
        $activated_data = get_option( 'etheme_activated_data' );
		$token = $activated_data['api_key'];
		$modules = $_POST['modules'];
		$type = $_POST['type'];

		$this_response = array();
		$this_response['response'] = array();
		$this_response['status'] = 'success';


		$url = $this->api_url . '?modules=' . $modules . '&type=' .$type . '&token=' .$token;

		$response = wp_remote_get($url);

		if ( wp_remote_retrieve_response_code($response) != 200 ){
			$this_response['status'] = 'error';
			$this_response['response']['msg'] = esc_html__('Did not return 200', 'xstore');
			$this_response['response']['icon'] = '';
			$this_response['response']['btn'] = '<span class="et-button et-button-green no-loader et_close-popup">' . esc_html__('ok', 'xstore') . '</span>';
			$this->return($this_response);
		}

		$body = wp_remote_retrieve_body($response);

		if ( ! $body ){
			$this_response['status'] = 'error';
			$this_response['response']['msg'] = esc_html__('Empty response data', 'xstore');
			$this_response['response']['icon'] = '';
			$this_response['response']['btn'] = '<span class="et-button et-button-green no-loader et_close-popup">' . esc_html__('ok', 'xstore') . '</span>';
			$this->return($this_response);
		}

		$etheme_generated_css_js = get_option('etheme_generated_css_js');


		if (json_decode($body)){
			$decoded_body = json_decode($body, true);


			if ( $decoded_body ){

				$this_response['status'] = 'error';
				$this_response['response']['msg'] = $decoded_body['response'];
				$this->return($this_response);
			}
		}

		$uploads = wp_upload_dir();
		$tmpxml_dir = $uploads['basedir']. '/xstore';


		$is_dir = is_dir( $tmpxml_dir );

		if ( ! $is_dir ) {
			$resoult = wp_mkdir_p( $tmpxml_dir );
			if ( ! $resoult ){
				$this_response['status'] = 'error';
				$this_response['response']['msg'] = esc_html__('Can not create folder', 'xstore');
				$this->return($this_response);
			}
		}

		if ( $type == 'css' ) {
			$tmpxml = $tmpxml_dir. '/xstore-custom-css.css';
			$generated_data = array(
				'is_enabled' => true,
				'path' => $tmpxml,
				'url' => $uploads['baseurl'] . '/xstore/xstore-custom-css.css',
				'modules' => explode(',', $modules)
			);
			$etheme_generated_css_js['css'] = $generated_data;


		} elseif ($type == 'js') {
			$tmpxml = $tmpxml_dir. '/xstore-custom-js.js';

			$generated_data = array(
				'is_enabled' => true,
				'path' => $tmpxml,
				'url' => $uploads['baseurl'] . '/xstore/xstore-custom-js.js',
				'modules' => explode(',', $modules)
			);

			$etheme_generated_css_js['js'] = $generated_data;

		} else {
			$tmpxml = '';
			$this_response['status'] = 'error';
			$this_response['response']['msg'] = esc_html__('wrong param', 'xstore');
			$this->return($this_response);
		}
		
		$body = str_replace('images/',  get_template_directory_uri() . '/images/', $body);

		if ( ! function_exists('etheme_fpcontent') ){
			$this_response['status'] = 'error';
			$this_response['response']['msg'] = esc_html__('etheme_fpcontent function is not exists', 'xstore');
			$this->return($this_response);
        }

		if ( etheme_fpcontent($tmpxml, $body) == false ){
			$this_response['status'] = 'error';
			$this_response['response']['msg'] = esc_html__('can not save file', 'xstore');
			$this->return($this_response);
		}

		update_option('etheme_generated_css_js', $etheme_generated_css_js);

		$generated_data['size'] = $this->file_size(filesize($tmpxml));
		$this_response['response'] = array(
			'msg' =>  '<h3 style="margin-bottom: 15px;">' . esc_html__('File successfully created!', 'xstore') . '</h3>',
			'icon' => '<img src="' . ETHEME_BASE_URI . ETHEME_CODE .'assets/images/success-icon.png" alt="installed icon" style="margin-top: 15px;"><br/><br/>',
			'btn' => '<span class="et-button et-button-green no-loader et_close-popup">' . esc_html__('ok', 'xstore') . '</span>',
			'data' => $generated_data
		);

        $this->return($this_response);
	}

	/**
	 * Generate section/subsection html
	 *
	 * @version  1.0.0
	 * @since  7.0.0
     * @param array $data section data
     * @param string $opt_class section class
     * @param string $has_subsection if section has subsection
     * @param bool $main is main section
     * @param array $checked_modules checked modules
     * @param array $required_modules required modules
     * @return array $checked_modules response data
	 */
    public function generate_section($data,$opt_class = 'opt-subsection',$has_subsection = '', $main=false, $checked_modules=array(), $required_modules = array()){
		$max_size = 0;
		$generated_css_js = get_option('etheme_generated_css_js');

		foreach ( $data as $key => $item ) {
			if ($main){
				$opt_class = 'opt-section';
				$has_subsection = 'input-section';
			}

			if ( isset($item['subsection']) ) {
				$opt_class .= ' has-subsection';
				$has_subsection .= ' has-subsection';
			}

			$checked = '';

			if( ! isset($generated_css_js['css']) || ! isset($generated_css_js['css']['modules']) ){
				$modules = array();
			} else {
				$modules = $generated_css_js['css']['modules'];
			}
   
			if ( isset($item['required'])) {
			    $opt_class .= ' required';
			    $checked = 'checked';
            }

			if ( strpos($key,',') != false ){
				$e_key = explode(',',$key);

				foreach ( $e_key as $i_key ) {
					if ( $i_key !='' && ! in_array($i_key,$checked_modules) ){
						$checked_modules[]=$i_key;
						if ( in_array($i_key, $modules) ){
							$checked = 'checked';
						}
					}
				}
			} else {
				$checked_modules[]=$key;
				if ( in_array($key, $modules) ){
					$checked = 'checked';
				}
			}

			if (!isset($item['subsection'])){
				$max_size = $max_size + $item['size'];
				if (isset($item['has_required'])){
					foreach ( $item['has_required'] as $r_key => $r_size ) {
						if ( $r_key !='' && ! in_array($r_key,$required_modules) ){
							$required_modules[] = $r_key;
							$max_size = $max_size + $r_size;
						}
					}
                }
			} elseif (isset($item['has_required'])){
				foreach ( $item['has_required'] as $r_key => $r_size ) {
					if ( $r_key !='' && ! in_array($r_key,$required_modules) ){
						$required_modules[] = $r_key;
						$max_size = $max_size + $r_size;
					}
			    }
            }

			?>
			<div class="<?php echo esc_attr($opt_class); ?>">
                <?php
                $required = '';

                if ( isset($item['has_required']) ){
	                $required = json_encode($item['has_required']);
                }

                ?>
				<p class="opt-item">
					<input
						class="<?php echo esc_attr($has_subsection); ?>"
						type="checkbox"
						id="<?php echo esc_attr($key); ?>"
						name="css-part"
						value="<?php echo esc_attr($key); ?>"
						<?php echo esc_attr($checked); ?>
						data-size="<?php echo esc_attr($item['size']); ?>"
                        data-required="<?php echo esc_js($required); ?>"
					>
					<label for="<?php echo esc_attr($key); ?>"><?php echo esc_attr($item['label']); ?></label>
					<span class="size">
                        <?php if ( $has_subsection && strpos($has_subsection,'has-subsection' ) !==false ): ;?>
                            <span class="real-size" data-size="<?php echo esc_js(0);?>">0</span>
                            <span class="delimeter">/</span>
                        <?php endif; ?>
                        <span class="size-value" data-size="<?php echo esc_attr($item['size']); ?>"><?php echo esc_attr($item['size']); ?></span>
                        <spna class="size-format">Kb</spna>
                    </span>
				</p>
				<?php
				if ( isset($item['subsection']) ) {
					$subsections = $this->generate_section($item['subsection'], $opt_class = 'opt-subsection',$has_subsection = '', false, $checked_modules, $required_modules);
					$max_size = $max_size + $subsections['max_size'];
					$checked_modules = $subsections['checked_modules'];
					$required_modules = $subsections['required_modules'];
				}
				?>
			</div>

		<?php }

		return array( 'max_size'=>$max_size, 'checked_modules' => $checked_modules,'required_modules' => $required_modules);
	}

	/**
	 * Return response
	 *
	 * @version  1.0.0
	 * @since  7.0.0
     * @param array $response response data
     * @return string json encoded $response data
	 */
	public function return($response){
	    return wp_send_json($response);
	}

	/**
	 * Get remote or cached generator data
	 *
	 * @version  1.0.0
	 * @since  7.0.0
	 * @return array array with generator data
	 */
    public function get_data(){
	    $modules = get_transient( 'etheme_generator_modules' );
	    if (
            ! $modules
            || empty( $modules )
            || ( isset($_GET['et_clear_transient']) && $_GET['et_clear_transient'] == 'generator_modules' )
        ){
		    $activated_data = get_option( 'etheme_activated_data' );
		    $token = $activated_data['api_key'];
		    $data = wp_remote_get($this->api_url . '?modules=none&type=options&token='.$token);
		    $body = wp_remote_retrieve_body($data);
		    $modules = json_decode($body, true);
		    set_transient( 'etheme_generator_modules', $modules, 24 * HOUR_IN_SECONDS );
	    }
	    return $modules;
    }
}
