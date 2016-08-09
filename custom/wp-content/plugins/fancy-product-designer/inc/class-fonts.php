<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Fonts') ) {

	class FPD_Fonts {

		/**
		 * Get google webfonts fonts
		 *
		 * @return array
		 */
		public static function get_google_webfonts() {

			$optimised_google_webfonts = array();

			//load fonts from google webfonts
			//delete_transient('fpd_google_webfonts');
			$optimised_google_webfonts = get_transient( 'fpd_google_webfonts' );
			if ( empty( $optimised_google_webfonts ) )	{

				$google_webfonts = false;

				$url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCPtPFVhMCcfbQm_cKVyY92FP8MFpi9NBM';

				if( function_exists('curl_init') ) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_URL, $url);
					$google_webfonts = curl_exec($ch);
					curl_close($ch);
				}

				if( $google_webfonts === false && function_exists('file_get_contents') ) {
					$google_webfonts = @file_get_contents($url);
				}

				if($google_webfonts !== false) {

					$google_webfonts = json_decode($google_webfonts);
					$optimised_google_webfonts = array();

					if( isset($google_webfonts->items) ) {
						foreach($google_webfonts->items as $item) {
							foreach($item->variants as $variant) {
								$key = str_replace(' ', '+', $item->family).':'.$variant;
								$optimised_google_webfonts[$key] = $item->family. ' '. $variant;
							}
						}
					}

				}

				//no webfonts could be loaded, try again in one min otherwise store them for one week
				set_transient('fpd_google_webfonts', $optimised_google_webfonts, sizeof($optimised_google_webfonts) == 0 ? 60 : 604800 );

			}

			return $optimised_google_webfonts;

		}

		/**
		 * Get woff fonts
		 *
		 * @return array
		 */
		public static function get_woff_fonts() {

			//load woff fonts from fonts directory
			$files = scandir(FPD_PLUGIN_DIR.'/fonts');
			$woff_files = array();
			foreach($files as $file) {
				if(preg_match("/.woff/", $file)) {
					$woff_files[$file] = str_replace('_', ' ', preg_replace("/\\.[^.\\s]{3,4}$/", "", $file) );
				}
			}

			return $woff_files;

		}

		//returns an array with all active fonts
		public static function get_enabled_fonts() {

			$all_fonts = array();

			$common_fonts = get_option( 'fpd_common_fonts' );
			if( !empty($common_fonts) ) {
				$all_fonts = explode(",", $common_fonts);
			}

			$google_webfonts = get_option( 'fpd_google_webfonts' );
			if( !empty($google_webfonts) ) {
				foreach($google_webfonts as $google_webfont) {
					$google_webfont = strpos($google_webfont, ':') === false ? $google_webfont : substr($google_webfont, 0, strpos($google_webfont, ':'));
					$google_webfont = str_replace('+', ' ', $google_webfont);

					if(!in_array($google_webfont, $all_fonts))
						$all_fonts[] = $google_webfont;
				}
			}

			$directory_fonts = get_option( 'fpd_fonts_directory' );
			if( !empty($directory_fonts) ) {
				foreach($directory_fonts as $directory_font) {
					$all_fonts[] = str_replace('_', ' ', preg_replace("/\\.[^.\\s]{3,4}$/", "", $directory_font) );
				}
			}

			asort($all_fonts);

			return $all_fonts;

		}

		public static function save_woff_fonts_css() {

			$fonts_css = FPD_PLUGIN_DIR.'/css/jquery.fancyProductDesigner-fonts.css';
			chmod($fonts_css, 0775);
			$handle = @fopen($fonts_css, 'w') or print('Cannot open file:  '.$fonts_css);
			$files = scandir(FPD_PLUGIN_DIR.'/fonts');
			$data = '';
			if(is_array($files)) {
				foreach($files as $file) {
					if(preg_match("/.woff/", $file)) {
						$new_file = str_replace(' ', '_', $file);
						rename(FPD_PLUGIN_DIR.'/fonts/'.$file, FPD_PLUGIN_DIR.'/fonts/'.$new_file);
						$data .= '@font-face {'."\n";
						$data .= '  font-family: "'.preg_replace("/\\.[^.\\s]{3,4}$/", "", str_replace('_', ' ', $file)).'";'."\n";
						$data .= '  src: local("#"), url(../fonts/'.$new_file.') format("woff");'."\n";
						$data .= '  font-weight: normal;'."\n";
						$data .= '  font-style: normal;'."\n";
						$data .= '}'."\n\n\n";
					}
				}
			}

			fwrite($handle, $data);
			fclose($handle);

		}

		public static function output_webfont_links() {

			$google_webfonts = get_option( 'fpd_google_webfonts' );
			$max_fonts_per_href = 10;
			$href_wf = array();
			if( !empty($google_webfonts) ) {

				for($i=0; $i < sizeof($google_webfonts); $i++) {

					array_push($href_wf, $google_webfonts[$i]);

					if( ($i % $max_fonts_per_href) == $max_fonts_per_href-1 || $i == sizeof($google_webfonts)-1 ) {
						echo '<link href="http://fonts.googleapis.com/css?family='.implode ("|", $href_wf).'" rel="stylesheet" type="text/css">';
						$href_wf = array();
					}
				}

			}

		}

	}

}

?>