<?php 
/*
Plugin Name: Skrumpt Lead Form
Description: This plugin is exlusive for Skrumpt CRM clients that generates a lead generation form in their website, submitted leads will automatically send to your skrumpt account.
Version: 1.1.6
Author: Skrumpt CRM
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class SkrumptForm {

	private $skrumpt_form_options;

	public function __construct() {
   
        add_shortcode( 'skrumpt_lead_form', array( $this, 'render_form' ) );
		add_action( 'admin_menu', array( $this, 'skrumpt_form_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'skrumpt_form_page_init' ) );

		add_action( 'wp_ajax_postproperty', array( $this, 'postproperty' ) );
		add_action( 'wp_ajax_nopriv_postproperty', array( $this, 'postproperty' ) );

		add_action( 'wp_ajax_postcodelookup', array( $this, 'postcodelookup' ) );
		add_action( 'wp_ajax_nopriv_postcodelookup', array( $this, 'postcodelookup' ) );
    }
    
    public function render_form( $atts ) {

		$skrumpt_form_options = get_option( 'slf_settings' );
		$success_message = (isset($skrumpt_form_options['success_message'])) ? $skrumpt_form_options['success_message'] : "";
		$success_redirect_url = (isset($skrumpt_form_options['success_redirect_url'])) ? $skrumpt_form_options['success_redirect_url'] : "";

		if($success_message == "" || $success_message == null){
			$success_message = "Your details has been submitted";
		}

        wp_register_style( 'register_style', plugins_url('/assets/slf-style.css',__FILE__ ));
        wp_enqueue_style( 'register_style' );

        wp_register_script( 'register_script', plugins_url('/assets/slf-script.js',__FILE__ ), [], '1.0.0', true);
        wp_enqueue_script( 'register_script' );

        wp_localize_script( 'register_script', 'slf_object',
            array( 
				'success_message' => $success_message,
				'success_redirect_url' => $success_redirect_url,
				'campaign_id' => isset($atts['campaign']) ? $atts['campaign'] : '',
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'postproperty' ),
				'postcode_nonce' => wp_create_nonce( 'postcodelookup' )
            )
		);

		$api_key_status = get_option( 'skrumpt_api_key_status');
		return '
		<div id="skrumpt_modal" class="slf_modal">
		
			<div class="slf_modal_content">
		
				<span class="slf_modal_close">&times;</span>
		
				<div class="success_container" style="display:none">
				' . $success_message  . '
				</div>
		
				<form id="slf_form" class="slf_form" method="post">
		
					<div id="slf_step_1" class="slf_class_wrapper">
		
						<div class="slf_wrapper_header">Personal Details</div>
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_title">Title</label>
								<select name="slf_title" class="slf_fields slf_select" id="slf_title">
									<option value="" selected disabled hidden>Select Title</option>
									<option value="Mr">Mr</option>
									<option value="Mrs">Mrs</option>
									<option value="Miss">Miss</option>
									<option value="Ms">Ms</option>
									<option value="Dr">Dr</option>
									<option value="Other">Other</option>
								</select>
							</div>
						</div>
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_email">Email</label>
								<input type="text" name="email" id="slf_email" class="slf_fields" size="50" maxlength="50" />
							</div>
						</div>
						<div class="slf_clear"></div>
		
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_firstname">Firstname</label>
								<input type="text" name="lastname" id="slf_firstname" class="slf_fields" size="50" maxlength="50" />
							</div>
						</div>
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_lastname">Lastname</label>
								<input type="text" name="lastname" id="slf_lastname" class="slf_fields" size="50" maxlength="50" />
							</div>
						</div>
						<div class="slf_clear"></div>
		
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_telephone">Telephone</label>
								<input type="text" name="telephone" id="slf_telephone" class="slf_fields" size="50" maxlength="50" />
							</div>
						</div>
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_mobile">Mobile</label>
								<input type="text" name="mobile" id="slf_mobile" class="slf_fields" size="20" maxlength="20"  />
							</div>
						</div>
						<div class="slf_clear"></div>
		
						<div class="slf_button_wrapper">
							<input type="button" value="Next" id="slf_next_1" class="slf_buttons" />
						</div>
		
					</div>
		
					<div id="slf_step_2" class="slf_class_wrapper">
						<div class="slf_wrapper_header">About your Property</div>
		
						<div class="slf_input_wrapper">
							<label class="slf_label" for="slf_propertytype">Select your address</label>
							<select class="slf_fields slf_select" onChange="selectAddress()" id="slf_address_lists">
								<option value="" selected disabled hidden>Select</option>
							</select>
						</div>
		
		
						<div class="slf_input_wrapper">
							<label class="slf_label" for="slf_address">Address</label>
							<input type="text" name="address" id="slf_address" class="slf_fields" size="50" />
						</div>
		
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_city">City</label>
								<input type="text" name="city" id="slf_city" class="slf_fields" size="30" maxlength="30"  />
							</div>
						</div>
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_county">County</label>
								<input type="text" name="county" id="slf_county" class="slf_fields" size="30" maxlength="30" />
							</div>
						</div>
						<div class="slf_clear"></div>
		
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_country">Country</label>
								<input type="text" name="country" id="slf_country" class="slf_fields" size="50" maxlength="50" />
							</div>
						</div>
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_postcode">Postcode</label>
								<input type="text" name="postcode" id="slf_postcode" class="slf_fields" size="20" maxlength="20" />
							</div>
						</div>
						<div class="slf_clear"></div>
		
						<div class="slf_button_wrapper">
							<input type="button" value="Previous" id="slf_previous_2" class="slf_previous slf_buttons" />
							<input type="button" value="Next" id="slf_next_2" class="slf_buttons" />
						</div>
		
					</div>
		
					<div id="slf_step_3" class="slf_class_wrapper">
						<div class="slf_wrapper_header">About your Property</div>
						<div class="slf_input_wrapper">
							<label class="slf_label" for="slf_propertytype">Property Type</label>
							<select name="propertytype" class="slf_fields slf_select" id="slf_propertytype">
								<option value="" selected disabled hidden>Select Property Type</option>
								<option value="Self-Contained Studio">Self-Contained Studio</option>
								<option value="1-Bed Flat">1-Bed Flat</option>
								<option value="2-Bed Flat">2-Bed Flat</option>
								<option value="3-Bed Flat">3-Bed Flat</option>
								<option value="4-Bed Flat">4-Bed Flat</option>
								<option value="1-Bed Terraced">1-Bed Terraced</option>
								<option value="2-Bed Terraced">2-Bed Terraced</option>
								<option value="3-Bed Terraced">3-Bed Terraced</option>
								<option value="4-Bed Terraced">4-Bed Terraced</option>
								<option value="5-Bed Terraced">5-Bed Terraced</option>
								<option value="6-Bed Terraced">6-Bed Terraced</option>
								<option value="1-Bed Semi-Detached">1-Bed Semi-Detached</option>
								<option value="2-Bed Semi-Detached">2-Bed Semi-Detached</option>
								<option value="3-Bed Semi-Detached">3-Bed Semi-Detached</option>
								<option value="4-Bed Semi-Detached">4-Bed Semi-Detached</option>
								<option value="5-Bed Semi-Detached">5-Bed Semi-Detached</option>
								<option value="6-Bed Semi-Detached">6-Bed Semi-Detached</option>
								<option value="1-Bed Detached">1-Bed Detached</option>
								<option value="2-Bed Detached">2-Bed Detached</option>
								<option value="3-Bed Detached">3-Bed Detached</option>
								<option value="4-Bed Detached">4-Bed Detached</option>
								<option value="5-Bed Detached">5-Bed Detached</option>
								<option value="6-Bed Detached">6-Bed Detached</option>
								<option value="1-Bed Apartment">1-Bed Apartment</option>
								<option value="2-Bed Apartment">2-Bed Apartment</option>
								<option value="3-Bed Apartment">3-Bed Apartment</option>
								<option value="4-Bed Apartment">4-Bed Apartment</option>
								<option value="5-Bed Apartment">5-Bed Apartment</option>
								<option value="Land">Land</option>
								<option value="Guest House">Guest House</option>
								<option value="Hotel">Hotel</option>
							</select>
						</div>
		
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_estimatedvalue">Estimated Value</label>
								<input type="text" name="estimatedvalue" id="slf_estimatedvalue" class="slf_fields" size="50" maxlength="50" />
							</div>
						</div>
						<div class="slf_float_half">
							<div class="slf_input_wrapper">
								<label class="slf_label" for="slf_estimatedsecureddebts">Estimated Secured Debts</label>
								<input type="text" name="estimatedsecureddebts" id="slf_estimatedsecureddebts" class="slf_fields" size="10" maxlength="10" />
							</div>
						</div>
						<div class="slf_clear"></div>
		
						<div class="slf_input_wrapper">
							<label class="slf_label" for="slf_reasonforselling">Reason for Selling</label>
							<select name="slf_reasonforselling" class="slf_fields slf_select" id="slf_reasonforselling">
								<option value="" selected disabled hidden>Select Reason</option>
								<option value="Broken Chain">Broken Chain</option>
								<option value="Reposession">Reposession</option>
								<option value="Debt Problems">Debt Problems</option>
								<option value="Need Cash">Need Cash</option>
								<option value="Deceased Estate">Deceased Estate</option>
								<option value="Divorce">Divorce</option>
								<option value="Family Member Died">Family Member Died</option>
								<option value="Ill-Health">Ill-Health</option>
								<option value="No Buyers">No Buyers</option>
								<option value="Equity Release">Equity Release</option>
								<option value="Relocating">Relocating</option>
								<option value="Retiring">Retiring</option>
								<option value="Money For Children">Money For Children</option>
								<option value="Distressed Property">Distressed Property</option>
								<option value="Unprofitable Investment">Unprofitable Investment</option>
								<option value="Haunted House">Haunted House</option>
								<option value="Down Sizing">Down Sizing</option>
								<option value="Alternative to Refinancing">Alternative to Refinancing</option>
								<option value="Wrong Number / No Email">Wrong Number / No Email</option>
								<option value="Downsizing but not in any hurry">Downsizing but not in any hurry</option>
								<option value="Other">Other</option>
							</select>
						</div>
						<div class="slf_button_wrapper">
						<input type="button" value="Previous" id="slf_previous_3" class="slf_previous slf_buttons" />
							<input type="submit" value="Submit" name="send" id="slf_submit" class="slf_submit slf_buttons`" />
						</div>
		
					</div>
		
		
				</form>
		
			</div>
		
		</div>';
	}
	
	public function postproperty() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/post.php';
	}

	public function postcodelookup() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/postcodelookup.php';
	}

	public function skrumpt_form_add_plugin_page() {
		add_menu_page(
			'Skrumpt Form', // page_title
			'Skrumpt Form', // menu_title
			'manage_options', // capability
			'skrumpt-form', // menu_slug
			array( $this, 'skrumpt_form_create_admin_page' ), // function
			'dashicons-media-document', // icon_url
			100 // position
		);
	}

	public function skrumpt_form_create_admin_page() {

		$this -> skrumpt_form_options = get_option( 'slf_settings' ); ?>

		<div class="wrap">
			<h2>Skrumpt Lead Form</h2>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'skrumpt_form_option_group' );
					do_settings_sections( 'skrumpt-form-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function skrumpt_form_page_init() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/slf-config-settings.php';
	}

	public function skrumpt_form_sanitize($input) {

		$sanitary_values = array();

		if ( isset( $input['api_key'] ) ) {
			$sanitary_values['api_key'] = sanitize_text_field( $input['api_key'] );
		}
		if ( isset( $input['campaign'] ) ) {
			$sanitary_values['campaign'] = $input['campaign'];
		}
		if ( isset( $input['success_message'] ) ) {
			$sanitary_values['success_message'] = esc_textarea( $input['success_message'] );
        }
		if ( isset( $input['success_redirect_url'] ) ) {
			$sanitary_values['success_redirect_url'] = esc_textarea( $input['success_redirect_url'] );
        }
        
		return $sanitary_values;
	}

	public function skrumpt_form_section_info() {
		
	}

	public function api_key_callback($account) {

		$field = '<textarea style="margin-bottom:20px" class="large-text" rows="2" name="slf_settings[api_key]" id="api_key">%s</textarea>';
		$field = $field . $account['details'];

		printf(
			$field ,
			isset( $this->skrumpt_form_options['api_key'] ) ? esc_attr( $this->skrumpt_form_options['api_key']) : ''
		);
	}

	public function campaign_callback($campaigns) {
		?> 
        <select name="slf_settings[campaign]" class="slf_campaign" id="campaign" value="5">
			<option value="" selected disabled hidden>Select a Campaign</option>

			<?php foreach($campaigns as $campaign){ ?>
				<option value="<?= $campaign['id'] ?>" <?= ($this->skrumpt_form_options['campaign'] == $campaign['id']) ? "selected" : "" ?>><?= $campaign['name'] ?></option>
			<?php } ?>

		</select> 
        <?php
	}

	public function success_message_callback() {
		printf(
			'<textarea class="large-text" rows="2" name="slf_settings[success_message]" id="success_message">%s</textarea> If you have Success Redirect URL, this message will be ignored',
			isset( $this->skrumpt_form_options['success_message'] ) ? esc_attr( $this->skrumpt_form_options['success_message']) : ''
		);
	}

	public function success_redirect_url_callback() {
		printf(
			'<textarea class="large-text" rows="2" name="slf_settings[success_redirect_url]" id="success_redirect_url">%s</textarea> Please enter the URL e.g. https://yourwebsite.com/success-page',
			isset( $this->skrumpt_form_options['success_redirect_url'] ) ? esc_attr( $this->skrumpt_form_options['success_redirect_url']) : ''
		);
	}


}

$skrumpt_form = new SkrumptForm();