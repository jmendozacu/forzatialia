<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if(!class_exists('FPD_Parameters')) {

	class FPD_Parameters {

		public $product_id;
		public $fancy_product;

		public function __construct( $product_id ) {

			$this->product_id = $product_id;
			$this->fancy_product = new Fancy_Product($product_id);

		}

		public function get_images_parameters() {

			$strip_from_option_key = 'fpd_designs_parameter_';

			$images_parameters = array();
			foreach( FPD_Admin_Settings::get_default_parameters_settings() as $option ) {

				if( isset($option['default']) && strpos($option['id'], $strip_from_option_key) !== false ) {

					$parameter = str_replace($strip_from_option_key, '', $option['id']);
					$pure_key = str_replace('fpd_', '', $option['id']);
					$images_parameters[$parameter] = $this->fancy_product->get_option($pure_key);

				}

			}

			$images_parameters['removable'] = 1;

			return $images_parameters;

		}

		public function get_images_parameters_string() {

			return self::convert_parameters_to_string($this->get_images_parameters());

		}

		public function get_custom_texts_parameters_string() {

			$strip_from_option_key = 'fpd_custom_texts_parameter_';

			$custom_texts_parameters = array();
			foreach( FPD_Admin_Settings::get_default_parameters_settings() as $option ) {

				if( isset($option['default']) && strpos($option['id'], $strip_from_option_key) !== false ) {

					$parameter = str_replace($strip_from_option_key, '', $option['id']);
					$pure_key = str_replace('fpd_', '', $option['id']);
					$custom_texts_parameters[$parameter] = $this->fancy_product->get_option($pure_key);

				}

			}

			$custom_texts_parameters['removable'] = 1;

			return self::convert_parameters_to_string($custom_texts_parameters);

		}

		public function get_custom_images_parameters_string() {

			$strip_from_option_key = 'fpd_uploaded_designs_parameter_';

			$custom_images_parameters = array();
			foreach( FPD_Admin_Settings::get_default_parameters_settings() as $option ) {

				if( isset($option['default']) && strpos($option['id'], $strip_from_option_key) !== false ) {

					$parameter = str_replace($strip_from_option_key, '', $option['id']);
					$pure_key = str_replace('fpd_', '', $option['id']);
					$custom_images_parameters[$parameter] = $this->fancy_product->get_option($pure_key);

				}

			}

			return self::convert_parameters_to_string($custom_images_parameters);

		}

		public static function convert_parameters_to_string( $parameters ) {

			if( empty($parameters) ) { return '{}'; }

			$params_object = '{';
			foreach($parameters as $key => $value) {

				if( fpd_not_empty($value) ) {

					//convert boolean value to integer
					if(is_bool($value)) { $value = (int) $value; }

					switch($key) {
						case 'x':
							$params_object .= '"x":'. $value .',';
						break;
						case 'y':
							$params_object .= '"y":'. $value .',';
						break;
						case 'z':
							$params_object .= '"z":'. $value .',';
						break;
						case 'colors':
							$params_object .= '"colors":"'. (is_array($value) ? implode(", ", $value) : $value) .'",';
						break;
						case 'removable':
							$params_object .= '"removable":'. $value .',';
						break;
						case 'draggable':
							$params_object .= '"draggable":'. $value .',';
						break;
						case 'rotatable':
							$params_object .= '"rotatable":'. $value .',';
						break;
						case 'resizable':
							$params_object .= '"resizable":'. $value .',';
						break;
						case 'removable':
							$params_object .= '"removable":'. $value .',';
						break;
						case 'zChangeable':
							$params_object .= '"zChangeable":'. $value .',';
						break;
						case 'scale':
							$params_object .= '"scale":'. $value .',';
						break;
						case 'angle':
							$params_object .= '"degree":'. $value .',';
						break;
						case 'price':
							$params_object .= '"price":'. $value .',';
						break;
						case 'autoCenter':
							$params_object .= '"autoCenter":'. $value .',';
						break;
						case 'font':
							$params_object .= '"font":"'. $value .'",';
						break;
						case 'patternable':
							$params_object .= '"patternable":'. $value .',';
						break;
						case 'textSize':
							$params_object .= '"textSize":'. $value .',';
						break;
						case 'editable':
							$params_object .= '"editable":'. $value .',';
						break;
						case 'replace':
							$params_object .= '"replace":"'. $value .'",';
						break;
						case 'autoSelect':
							$params_object .= '"autoSelect":'. $value .',';
						break;
						case 'topped':
							$params_object .= '"topped":'. $value .',';
						break;
						case 'boundingBoxClipping':
							$params_object .= '"boundingBoxClipping":'. $value .',';
						break;
						case 'maxLength':
							$params_object .= '"maxLength":'. $value .',';
						break;
						case 'fontWeight':
							$params_object .= '"fontWeight":"'. $value .'",';
						break;
						case 'fontStyle':
							$params_object .= '"fontStyle":"'. $value .'",';
						break;
						case 'textAlign':
							$params_object .= '"textAlign":"'. $value .'",';
						break;
						case 'curvable':
							$params_object .= '"curvable":'. $value .',';
						break;
						case 'curved':
							$params_object .= '"curved":'. $value .',';
						break;
						case 'curveSpacing':
							$params_object .= '"curveSpacing":'. $value .',';
						break;
						case 'curveRadius':
							$params_object .= '"curveRadius":'. $value .',';
						break;
						case 'curveReverse':
							$params_object .= '"curveReverse":'. $value .',';
						break;
						case 'opacity':
							$params_object .= '"opacity":'. $value .',';
						break;
						case 'minW':
							$params_object .= '"minW":'. $value .',';
						break;
						case 'minH':
							$params_object .= '"minH":'. $value .',';
						break;
						case 'maxW':
							$params_object .= '"maxW":'. $value .',';
						break;
						case 'maxH':
							$params_object .= '"maxH":'. $value .',';
						break;
						case 'resizeToW':
							$params_object .= '"resizeToW":'. $value .',';
						break;
						case 'resizeToH':
							$params_object .= '"resizeToH":'. $value .',';
						break;
						case 'currentColor':
							$params_object .= '"currentColor":"'. $value .'",';
						break;
						case 'uploadZone':
							$params_object .= '"uploadZone":'. $value .',';
						break;
					}
				}
			}

			//bounding box
			if( empty($parameters['bounding_box_control']) ) {

				//use custom bounding box
				if(isset($parameters['bounding_box_x']) &&
				   isset($parameters['bounding_box_y']) &&
				   isset($parameters['bounding_box_width']) &&
				   isset($parameters['bounding_box_height'])
				   ) {

					if( fpd_not_empty($parameters['bounding_box_x']) && fpd_not_empty($parameters['bounding_box_y']) && fpd_not_empty($parameters['bounding_box_width']) && fpd_not_empty($parameters['bounding_box_height']) ) {
						$params_object .= '"boundingBox": { "x":'. $parameters['bounding_box_x'] .', "y":'. $parameters['bounding_box_y'] .', "width":'. $parameters['bounding_box_width'] .', "height":'. $parameters['bounding_box_height'] .'}';

					}
				}

			}
			else if ( isset($parameters['bounding_box_by_other']) && fpd_not_empty(trim($parameters['bounding_box_by_other'])) ) {
				$params_object .= '"boundingBox": "'. $parameters['bounding_box_by_other'] .'"';
			}

			$params_object = trim($params_object, ',');
			$params_object .= '}';
			$params_object = str_replace('_', ' ', $params_object);

			return $params_object;

		}

	}

}


?>