<?php
namespace W3speedster;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class w3speedster_js extends w3speedster_css{

    
	function w3_modify_file_cache_js($html, $path){
		$src_array = explode('/',$path);
		$count = count($src_array);
		unset($src_array[$count-1]);
		if(!empty($this->settings[base64_decode('aXNfYWN0aXZhdGVk')]) && !empty($this->settings['load_combined_js'])){
			if((strpos($html,'holdready:') !== false || strpos($html,'S.holdReady') !== false) && empty($this->add_settings['holdready'])){
				$html .= ';if(typeof($) == "undefined"){$ = jQuery;}else{var $ = jQuery;}';
				//$html .= 'jQuery.holdReady( true );';
				$this->add_settings['holdready'] = 1;
			}
			$exclude_from_w3_changes = 0;
			if(function_exists('w3speedup_exclude_internal_js_w3_changes')){
				$exclude_from_w3_changes = w3speedup_exclude_internal_js_w3_changes($path,$html);
			}
			if(strpos($html,'holdready:') === false && !$exclude_from_w3_changes){
				
				/*$html = preg_replace('/([\s\;\}\,\)\(\{])([a-zA-Z]+[.]addEventListener\s*\(\s*[\'|"]\s*load\s*[\'|"]\s*,)(\s*function\s*\(\s*(\w+)\s*\)\s*\{)/', "$1setTimeout($3$4=w3loadevent;", $html);*/
				$html = preg_replace('/([\s\;\:\}\,\)\(\{])([a-zA-Z]+[.]addEventListener\s*\(\s*[\'|"]\s*readystatechange\s*[\'|"]\s*,)/', "$1setTimeout(", $html);
				$html = preg_replace('/([\s\;\:\}\,\)\(\{])([a-zA-Z]+[.]addEventListener\s*\(\s*[\'|"]\s*DOMContentLoaded\s*[\'|"]\s*,)/', "$1setTimeout(", $html);
				$html = preg_replace('/([\s\;\:\}\,\)\(\{])([a-zA-Z]+[.]addEventListener\s*\(\s*[\'|"]\s*load\s*[\'|"]\s*,)/', "$1setTimeout(", $html);
				
				$html = preg_replace('/([\,|\;|\}]\s*)(jQuery\(\s*window\s*\)[.]on\(\s*[\"|\']\s*load\s*[\"|\']\s*\,)/', "$1setTimeout(", $html);
			}
			if( !empty($this->add_settings['jquery_excluded']) && empty($this->add_settings['holdready'])){
				$html = (function_exists('w3speedup_load_2_jquery') ? file_get_contents($this->add_settings['jquery_excluded']) : '').';;if(typeof($) != "undefined"){$ = jQuery;}else{var $ = jQuery;}jQuery.holdReady( true );'.$html;
				$this->add_settings['holdready'] = 1;
			}
			if(strpos(trim($html),'"use strict";') === 0){
				$html = preg_replace('/"use strict";/', '', $html, 1);
			}
			
			if(strpos($path,'custom_js_after_load.js') && !empty($this->add_settings['holdready'])){
				$html = ';jQuery.holdReady( false );'.$html;
			}
			if(strpos($path,'elementor/assets/js/frontend.min.js') !== false){
				$html = str_replace('window.elementorFrontend=new b,elementorFrontend.isEditMode()||jQuery((function(){return elementorFrontend.init()}))','window.elementorFrontend=new b,elementorFrontend.isEditMode()||jQuery(function(){var elementor_calling = setInterval(function(){if(jQuery("html").hasClass("w3_js")){clearInterval(elementor_calling);return elementorFrontend.init();}});
		});',$html);	
			}
		}
		//$html = str_replace('$(window).load(et_all_elements_loaded)','$(window).load(et_all_elements_loaded);et_all_elements_loaded()',$html);
		//$html = str_replace(array('document.addEventListener("DOMContentLoaded",function(){'),'jQuery(function(){',$html);
		if(function_exists('w3speedup_internal_js_customize')){
			$html = w3speedup_internal_js_customize($html,$path);
		}
		if(function_exists('w3speedup_internal_js_minify')){
			if(w3speedup_internal_js_minify($path,$html)){
				$html = $this->w3_compress_js($html);
				$html = str_replace('sourceMappingURL=/','sourceMappingURL='.implode('/',$src_array),$html.";\n");
			}
		}else{
			$html = $this->w3_compress_js($html);
			$html = str_replace('sourceMappingURL=/','sourceMappingURL='.implode('/',$src_array),$html.";\n");
		}
		return $html;
	}
	function w3_create_file_cache_js_url($path){
	    $cache_file_path = $this->w3_get_cache_path('js').'/'.md5($this->add_settings['w3_rand_key'].$path).'.js';
        if( !file_exists($cache_file_path) ){
            //$this->w3_check_if_folder_exists($this->w3_get_cache_path('js'));
			$html = file_get_contents($path);
            $html = $this->w3_modify_file_cache_js($html, $path);
            $this->w3_create_file($cache_file_path, $html );
        }
	    return str_replace($this->add_settings['document_root'],'',$cache_file_path);
    }
    function w3_create_file_cache_js($path){
	    $cache_file_path = $this->w3_get_cache_path('js').'/'.md5($this->add_settings['w3_rand_key'].$path).'.js';
        if( !file_exists($cache_file_path) ){
            //$this->w3_check_if_folder_exists($this->w3_get_cache_path('js'));
			$html = file_get_contents($this->add_settings['document_root'].$path);
            $html = $this->w3_modify_file_cache_js($html, $path);
            $this->w3_create_file($cache_file_path, $html );
        }
	    return str_replace($this->add_settings['document_root'],'',$cache_file_path);
    }
    
    function w3_compress_js($html){
        include_once W3SPEEDSTER_PLUGIN_DIR.'includes/jsmin.php';
        $html = \W3jsMin::minify($html);
        return $html;
    }
    function minify_css($html,$all_links){
       return parent::minify_css($html,$all_links);
    }
	function w3_get_next_script_obj($script_links, $si){
		if(empty($script_links[$si+1])){
			return '';
		}
		$script_arr = !empty($script_links[$si+1]) ? $this->w3_parse_link('script',str_replace($this->add_settings['image_home_url'],$this->add_settings['wp_site_url'],$script_links[$si+1])) : array();
		/*if(empty($script_arr['src'])){
			return $this->w3_get_next_script_obj($script_links, ($si+1));
		}*/
		$this->add_settings['script_obj'][$si+1] = $script_arr;
		return $script_arr;
	}
    function minify($html, $script_links){
        if(!empty($this->settings['exclude_page_from_load_combined_js']) && $this->w3_check_if_page_excluded($this->settings['exclude_page_from_load_combined_js'])){
			return $html;
        }
		if(!empty($script_links) && !empty($this->settings['js'])){
			$lazy_load_js = !empty($this->settings['load_combined_js']) && $this->settings['load_combined_js'] == 'after_page_load' ? 1 : 0;
			$force_innerjs_to_lazy_load  = !empty($this->settings['force_lazy_load_inner_javascript']) ? explode("\r\n", $this->settings['force_lazy_load_inner_javascript']) : array();
            $exclude_js_arr_split  = !empty($this->settings['exclude_javascript']) ? explode("\r\n", $this->settings['exclude_javascript']) : array();
			foreach($exclude_js_arr_split as $key => $value){
				if(strpos($value,' defer') !== false){
					$exclude_js_arr[$key]['string'] = str_replace(' defer','',$value);
					$exclude_js_arr[$key]['defer'] = 1;
				}elseif(strpos($value,' full') !== false){
					$exclude_js_arr[$key]['string'] = str_replace(' full','',$value);
					$exclude_js_arr[$key]['full'] = 1;
				}else{	
					$exclude_js_arr[$key]['string'] = $value;
					$exclude_js_arr[$key]['defer'] = 0;
				}
			}
            $exclude_inner_js= !empty($this->settings['exclude_inner_javascript']) ? explode("\r\n", stripslashes($this->settings['exclude_inner_javascript'])) : array('google-analytics', 'hbspt',base64_decode("LyogPCFbQ0RBVEFbICov"));
            $load_ext_js_before_internal_js = !empty($this->settings['load_external_before_internal']) ? explode("\r\n", $this->settings['load_external_before_internal']) : array();
            $all_js='';
            $included_js = array();
            $final_merge_js = array();
            $js_file_name = '';
            $enable_cdn = 0;
            if($this->add_settings['image_home_url'] != $this->add_settings['wp_site_url']){
				$ext = '.js';
				if(empty($this->add_settings['exclude_cdn']) || !in_array($ext,$this->add_settings['exclude_cdn'])){
					$enable_cdn = 1;
				}
			}
			
			$next_script_obj = $this->w3_parse_link('script',str_replace($this->add_settings['image_home_url'],$this->add_settings['wp_site_url'],$script_links[0]));
			$final_merge_has_js = array();
			$last_js_url = '';
			for($si=0; $si < count($script_links); $si++){
                $script = $script_links[$si];
				$script_obj = !empty($this->add_settings['script_obj'][$si]) ? $this->add_settings['script_obj'][$si] : $this->w3_parse_link('script',str_replace($this->add_settings['image_home_url'],$this->add_settings['wp_site_url'],$script_links[$si]));
				$script_text = '';
				$next_script_obj = $this->w3_get_next_script_obj($script_links, $si);
				if(!array_key_exists('src',$script_obj)){
                    $script_text = $this->w3_parse_script('<script',$script);
                }
				$next_script_text = '';
				if(is_array($next_script_obj) && !array_key_exists('src',$next_script_obj)){
					$next_script_text = $this->w3_parse_script('<script',$script_links[$si+1]);
                }
                if(!empty($script_obj['type']) && strtolower($script_obj['type']) != 'application/javascript' && strtolower($script_obj['type']) != 'text/javascript' && strtolower($script_obj['type']) != 'text/jsx;harmony=true'){
                    continue;
                }
				if(function_exists('w3speedup_customize_script_object')){
					$script_obj = w3speedup_customize_script_object($script_obj, $script);
				}
                if(!empty($script_obj['src'])){
					
					//echo $script_obj['src'];
                    $url_array = $this->w3_parse_url($script_obj['src']);
                    $exclude_js = 0;
                    if(!empty($exclude_js_arr) && is_array($exclude_js_arr)){
						foreach($exclude_js_arr as $ex_js){
							if(strpos($script,$ex_js['string']) !== false){
								if($ex_js['defer']){
									$exclude_js = 2;
								}elseif($ex_js['full']){
									$exclude_js = 3;
								}else{
									$exclude_js = 1;
								}
							}
						}
					}
					if(function_exists('w3speedup_exclude_javascript_filter')){
						$exclude_js = w3speedup_exclude_javascript_filter($exclude_js,$script_obj,$script,$html);
					}
					if(!$this->w3_is_external($script_obj['src']) && $this->w3_endswith($url_array['path'], '.js')){
                        $old_path = $url_array['path'];
                        if(file_exists($this->add_settings['document_root'].$url_array['path'])){
							$url_array['path'] = $this->w3_create_file_cache_js($url_array['path']);
						}else{
							$url_array['path'] = $this->w3_create_file_cache_js_url($script_obj['src']);
						}
                        $script_obj['src'] = $this->add_settings['wp_site_url'].$url_array['path'];
                    }
                    if($exclude_js){
                        if( $exclude_js == 3){
                           continue;
						}
						if( $exclude_js == 2){
                            $script_obj['defer'] = 'defer';
						}
						if(file_exists($this->add_settings['document_root'].$url_array['path']) && strpos(file_get_contents($this->add_settings['document_root'].$url_array['path']),'jQuery requires a window with a document') !== false){
							$this->add_settings['jquery_excluded'] = $this->add_settings['document_root'].$url_array['path'];
						}
						$script_obj['src'] = $enable_cdn ? str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'] ,$script_obj['src']) : $script_obj['src'];
						$this->w3_str_replace_set($script,$this->w3_implode_link_array('script',$script_obj));
                        continue;
                    }
                    $exclude_js_bool=0;
					if(!empty($force_innerjs_to_lazy_load)){
                        foreach($force_innerjs_to_lazy_load as $js){
                            if( !empty($js) && strpos($script,$js) !== false){
                                $exclude_js_bool=1;
                                break;
                            }
                        }
                    }
					
                    $val = $script_obj['src'];
                    if(!empty($val) && !$this->w3_is_external($val) && strpos($script, '.js') && empty($exclude_js_bool)){
                        $final_merge_js[] = $url_array['path'];
						$final_merge_has_js[] = $script;
						if((!empty($next_script_text) && !$this->w3_check_js_if_excluded($next_script_text, $exclude_inner_js)) || (!empty($next_script_obj['src']) && $this->w3_is_external($next_script_obj['src']))){
							if(!empty($final_merge_js) && count($final_merge_js) > 0){
								$cache_js_url = $this->w3_create_js_combined_cache_file($final_merge_js, $enable_cdn);
								$this->w3_replace_js_files_with_combined_files($final_merge_has_js,$cache_js_url);
								$last_js_url = $cache_js_url;
								$final_merge_js = array();
								$final_merge_has_js = array();
							}
						}
					}elseif($this->w3_is_external($val) && empty($exclude_js_bool) ){
						$script_obj['data-src'] = $script_obj['src'];
						$script_obj['type'] = 'lazyload_int';
						unset($script_obj['src']);
						$this->w3_str_replace_set($script,$this->w3_implode_link_array('script',$script_obj));
					}elseif($exclude_js_bool){
						$script_obj['src'] = $enable_cdn ? str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'] ,$script_obj['src']) : $script_obj['src'];
						$script_obj['data-src'] = $script_obj['src'];
						$script_obj['type'] = 'lazyload_ext';
						unset($script_obj['src']);
						if(function_exists('w3_external_javascript_customize')){
							$script_obj = w3_external_javascript_customize($script_obj, $script);
						}
						$this->w3_str_replace_set($script,$this->w3_implode_link_array('script',$script_obj));
                    }
                }else{
                    
                    $inner_js = $script_text;
                    $lazy_loadjs = 0;
                    $exclude_js_bool = 0;
					$force_js_bool = 0;
                    $exclude_js_bool = $this->w3_check_js_if_excluded($inner_js, $exclude_inner_js);
					if(function_exists('w3speedup_inner_js_customize')){
						$script_text = w3speedup_inner_js_customize($script_text);
					}
					if(!empty($force_innerjs_to_lazy_load)){
                        foreach($force_innerjs_to_lazy_load as $js){
                            if(strpos($script_text,$js) !== false){
                                $exclude_js_bool=0;
								$force_js_bool = 1;
                                break;
                            }
                        }
                    }
                    if(!empty($exclude_js_bool) && $exclude_js_bool != 2){
						if(function_exists('w3speedup_inner_js_customize')){
							$this->w3_str_replace_set($script,'<script>'.$script_text.'</script>');
						}
					}else{
						if($exclude_js_bool == 2){
							$script_modified = '<script type="lazyload_int2" ';
						}elseif($force_js_bool){
    						$script_modified = '<script type="lazyload_ext" ';
    					}else{
    						$script_modified = '<script type="lazyload_int" ';
    					}
    					foreach($script_obj as $key => $value){
                            if($key != 'type' && $key != 'html'){
                                $script_modified .= $key.'="'.$value.'" ';
                            }
                        }
						if(!empty($this->settings[base64_decode('aXNfYWN0aXZhdGVk')]) && !empty($this->settings['load_combined_js']) && $this->settings['load_combined_js'] == 'after_page_load'){
							$script_text = preg_replace('/([\s\;\}\,\)\(\{\>])([a-zA-Z]+[.]addEventListener\s*\(\s*[\'|"]\s*DOMContentLoaded\s*[\'|"]\s*,)/', "$1setTimeout(", ' '.$script_text);
							$script_text = preg_replace('/([\s\;\}\,\)\(\{\>])([a-zA-Z]+[.]addEventListener\s*\(\s*[\'|"]\s*load\s*[\'|"]\s*,)/', "$1setTimeout(", $script_text);
						}
                        $script_modified = $script_modified.'>'.$script_text.'</script>';
                        $this->w3_str_replace_set($script,$script_modified);
						if((!empty($next_script_text) && !$this->w3_check_js_if_excluded($next_script_text, $exclude_inner_js)) || (!empty($next_script_obj['src']) && $this->w3_is_external($next_script_obj['src']))){
							if(!empty($final_merge_js) && count($final_merge_js) > 0){
								$cache_js_url = $this->w3_create_js_combined_cache_file($final_merge_js, $enable_cdn);
								$this->w3_replace_js_files_with_combined_files($final_merge_has_js,$cache_js_url);
								$last_js_url = $cache_js_url;
								$final_merge_js = array();
								$final_merge_has_js = array();
							}
						}
					}
                }
				if($si == count($script_links)-1 && !empty($final_merge_has_js)){
					if(!empty($final_merge_js) && count($final_merge_js) > 0){
						$cache_js_url = $this->w3_create_js_combined_cache_file($final_merge_js, $enable_cdn);
						$this->w3_replace_js_files_with_combined_files($final_merge_has_js, $cache_js_url);
						$last_js_url = $cache_js_url;
						$final_merge_js = array();
					}
				}
            }
			if(!empty($last_js_url) && empty($lazy_load_js)){
				$this->w3_str_replace_set($last_js_url.'"',$last_js_url.'" onload="load_intJS_main()"');
			}
            /*if(!empty($this->settings['custom_js_after_load'])){
                $final_merge_js['custom_js'] = stripslashes($this->settings['custom_js_after_load']);
            }*/
            
			if(!empty($this->settings['custom_javascript'])){
			   if(!empty($this->settings['custom_javascript_file'])){    
					$custom_js_path = $this->w3_get_cache_path('all-js').'/wnw-custom-js.js';
					if(!is_file($custom_js_path)){
						$this->w3_create_file($custom_js_path, stripslashes($this->settings['custom_javascript']));
					}
					$custom_js_url = $this->add_settings['cache_url'].'/all-js/wnw-custom-js.js';
					$custom_js_url = $enable_cdn ? str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'] ,$custom_js_url) : $custom_js_url;
					$position = strrpos($html,'</body>');
					$html = substr_replace( $html, '<script '.(!empty($this->settings['custom_javascript_defer']) ? 'defer="defer"' : '').' id="wnw-custom-js" src="'.$custom_js_url.'?ver='.rand(10,1000).'"></script>', $position, 0 );
				}else{
					$position = strrpos($html,'</body>');
					$html = substr_replace( $html, '<script>'.stripslashes($this->settings['custom_javascript']).'</script>', $position, 0 ); 
				}
			}
		}
        
        
        return $html;
    }
	function w3_check_js_if_excluded($inner_js, $exclude_inner_js){
		$exclude_js_bool=0;
		if(strpos($inner_js,'moment.') === false && strpos($inner_js,'wp.') === false && strpos($inner_js,'.noConflict') === false && strpos($inner_js,'wp.i18n') === false){
			$exclude_js_bool=1;
		}
		if(strpos($inner_js,'DOMContentLoaded') !== false || strpos($inner_js,'jQuery(') !== false || strpos($inner_js,'$(') !== false || strpos($inner_js,'jQuery.') !== false || strpos($inner_js,'$.') !== false){
			$exclude_js_bool=2;
		}
		
		if(!empty($exclude_inner_js)){
			foreach($exclude_inner_js as $js){
				if(strpos($inner_js,$js) !== false){
					return 1;
					break;
				}
			}
		}
		return $exclude_js_bool;
	}
	function w3_replace_js_files_with_combined_files($final_merge_has_js,$cache_js_url){
		if(!empty($final_merge_has_js)){
			$lazy_load_js = !empty($this->settings['load_combined_js']) && $this->settings['load_combined_js'] == 'after_page_load' ? 1 : 0;
			for($ii = 0; $ii < count($final_merge_has_js); $ii++){
				if($ii == count($final_merge_has_js) -1 ){
					$this->w3_str_replace_set($final_merge_has_js[$ii],'<script type="lazyload_int" data-src="'.$cache_js_url.'"></script>');
				}else{
					$this->w3_str_replace_set($final_merge_has_js[$ii],'');
				}
			}
		}
	}
	
	function w3_create_js_combined_cache_file($final_merge_js, $enable_cdn){
		$file_name = is_array($final_merge_js) ? $this->add_settings['w3_rand_key'].'-'.implode('-', $final_merge_js) : '';
		if(!empty($file_name)){
			$js_file_name = md5($file_name).$this->add_settings['js_ext'];
			if(!is_file($this->w3_get_cache_path('all-js').'/'.$js_file_name)){
				$all_js = '';
				foreach($final_merge_js as $key => $script_path){
					$all_js .= file_get_contents($this->add_settings['document_root'].$script_path).";\n";
				}
				$this->w3_create_file($this->w3_get_cache_path('all-js').'/'.$js_file_name, $all_js);
			}
			$main_js_url = $this->add_settings['cache_url'].'/all-js/'.$js_file_name;
			$main_js_url = $enable_cdn ? str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'] ,$main_js_url) : $main_js_url;
			return $main_js_url;
		}
	}
    function w3_lazy_load_images(){
        global $main_css_url,$internal_js;
        if(empty($main_css_url)){
            $main_css_url = array();
        }
        $lazy_load_by_px = !empty($this->settings['lazy_load_px']) ? (int)$this->settings['lazy_load_px'] : 200;
        $internal_js_delay_load = !empty($this->settings['internal_js_delay_load']) ? $this->settings['internal_js_delay_load']*1000 : 10000;
        $js_delay_load = !empty($this->settings['js_delay_load']) ? $this->settings['js_delay_load']*1000 : 10000;
        $internal_css_delay_load = !empty($this->settings['internal_css_delay_load']) ? $this->settings['internal_css_delay_load']*1000 : 10000;
        $google_fonts_delay_load = !empty($this->settings['google_fonts_delay_load']) ? $this->settings['google_fonts_delay_load']*1000 : 2000;
		$upload_dir   = wp_upload_dir();
        $script = 'var w3_is_mobile='.(!empty($this->add_settings['is_mobile']) ? 1 : 0).';var w3_lazy_load_js='.(!empty($this->settings['load_combined_js']) && $this->settings['load_combined_js'] == 'after_page_load' ? 1 : 0).';var w3_lazy_load_by_px='.$lazy_load_by_px.';var w3_internal_js_delay_load = '.$internal_js_delay_load.';var w3_js_delay_load = '.$js_delay_load.';var w3_internal_css_delay_load = '.$internal_css_delay_load.';var w3_google_fonts_delay_load = '.$google_fonts_delay_load.';var w3_lazy_load_css='.json_encode($main_css_url).';blank_image_webp_url = "'. str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'],$upload_dir['baseurl']).'/blank.pngw3.webp";';
    
           
        $inner_script_optimizer ='const w3_event = new Event("w3_build");var w3_upload_path="'.$this->add_settings['upload_path'].'"; var w3_webp_path="'.$this->add_settings['webp_path'].'";var w3_first_js = false;
            
			var w3_int_first_js = false;
			var w3_first_css = false;
			var w3_first_google_css = false;
			var w3_first = false;
			var w3_external_single_loaded = 1;
			var w3_internal_js = document.querySelectorAll("script[type=lazyload_int]");
			var w3_inline_js = document.querySelectorAll("script[type=lazyload_int2]");
			var w3_mousemoveloadimg = false;
			var w3_page_is_scrolled = false;
			var w3_internal_js_loaded = false;
            var w3_internal_js_called = false;
            var w3_inner_js_counter1 = -1;
            var w3_s1={};
			function w3_to_webp(elementImg){
				for (var ig = 0; ig < elementImg.length; ig++) {
					console.log("rocket",elementImg[ig].getAttribute("data-src"));
					if (elementImg[ig].getAttribute("data-src") != null && elementImg[ig].getAttribute("data-src") != "") {
						var datasrc = elementImg[ig].getAttribute("data-src");
						elementImg[ig].setAttribute("data-src", datasrc.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path));
					}
					if (elementImg[ig].getAttribute("data-srcset") != null && elementImg[ig].getAttribute("data-srcset") != "") {
						var datasrcset = elementImg[ig].getAttribute("data-srcset");
						elementImg[ig].setAttribute("data-srcset", datasrcset.replace(/w3.webp/g, "").split(w3_webp_path).join(w3_upload_path));
					}
					if (elementImg[ig].src != null && elementImg[ig].src != "") {
						var src = elementImg[ig].src;
						elementImg[ig].src = src.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path);
					}
					if (elementImg[ig].srcset != null && elementImg[ig].srcset != "") {
						var srcset = elementImg[ig].srcset;
						elementImg[ig].srcset = srcset.replace(/w3.webp/g, "").split(w3_webp_path).join(w3_upload_path);
					}
				}
			}
			function fixwebp() {
				if (!w3_hasWebP) {
					var elementNames = ["*"];
					w3_to_webp(document.querySelectorAll("img[data-src$=\'w3.webp\']"));
					w3_to_webp(document.querySelectorAll("img[src$=\'w3.webp\']"));
					
					elementNames.forEach(function(tagName) {
						var tags = document.getElementsByTagName(tagName);
						var numTags = tags.length;
						for (var i = 0; i < numTags; i++) {
							var tag = tags[i];
							var style = tag.currentStyle || window.getComputedStyle(tag, false);
							var bg = style.backgroundImage;
							if (bg.match("w3.webp")) {
								if (document.all) {
									tag.style.setAttribute("cssText", ";background-image: " + bg.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path) + " !important;");
								} else {
									tag.setAttribute("style", tag.getAttribute("style") + ";background-image: " + bg.replace("w3.webp", "").replace(w3_webp_path, w3_upload_path) + " !important;");
								}
							}
						}
					});
				}
			}
			function w3_change_webp(){
				if (bg.match("w3.webp")) {
					if ( document.all ) {
						tag.style.setAttribute( "cssText", "background-image: "+bg.replace("w3.webp","").replace(w3_webp_path,w3_upload_path)+" !important" );
						var style1 = tag.currentStyle || window.getComputedStyle(tag, false);
					} else {
						tag.setAttribute( "style", "background-image: "+bg.replace("w3.webp","").replace(w3_webp_path,w3_upload_path)+" !important" );
						var style1 = tag.currentStyle || window.getComputedStyle(tag, false);
					}
				}
			}
            var w3_hasWebP = false;
			(function(){
				var img = new Image();
				img.onload = function() {
					w3_hasWebP = !!(img.height > 0 && img.width > 0);
				};
				img.onerror = function() {
					w3_hasWebP = false;
					fixwebp();
				};
				img.src = blank_image_webp_url;
			})();
            setTimeout(function(){load_googlefont();},w3_google_fonts_delay_load);
			if(w3_lazy_load_js){
				window.addEventListener("DOMContentLoaded", function(event){
					setTimeout(function(){load_intJS_main();},w3_internal_js_delay_load);
				});
			}else{
				load_all_js();
			}
			var w3loadevent;
            window.addEventListener("load", function(event){
				w3loadevent = event;
            });
			window.addEventListener("DOMContentLoaded", function(event){
				setTimeout(function(){load_extCss();},w3_internal_css_delay_load);
                lazyloadimages(0);
            });
			
    
            window.addEventListener("scroll", function(event){
				load_googlefont();
				var top = this.scrollY;
				lazyloadimages(top);
				lazyloadiframes(top);
				w3_js_delay_load=500;
				if(w3_lazy_load_js){
					load_all_js();
				}
				load_extCss();
			   
            }, {passive: true});
			if(!w3_is_mobile){
				window.addEventListener("mousemove", function(){
					load_googlefont();				
					w3_js_delay_load=500;
					if(w3_lazy_load_js){
						load_all_js();
					}
					load_extCss();
				}, {passive: true});
			}
			window.addEventListener("touchstart", function(e){
				if(e.target.tagName!="A"){
					load_googlefont();
					w3_js_delay_load=500;
					if(w3_lazy_load_js){
						load_all_js();
					}
					load_extCss();
				}
            }, {passive: true});
    
            function load_all_js(){
				var element = document.getElementsByTagName("html")[0];
				element.classList.add("w3_start");
				load_intJS_main();
				if(w3_mousemoveloadimg == false){
					var top = this.scrollY;
					lazyloadimages(top);
					w3_mousemoveloadimg = true;
				}
            }

            function insertAfter(newNode, referenceNode) {
                if(referenceNode.parentNode != null){
					referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
				}else{
					var html_tag = document.getElementsByTagName("html")[0];
					html_tag.insertBefore(newNode, referenceNode.nextSibling);
				}
            }
			
			var w3_inner_js_counter = -1;
			var w3_s={};
			function load_extJS() {
                if(w3_first_js){
                    return;
                }
                if(!w3_int_first_js){
                    setTimeout(function(){load_extJS();},1000);
                    return;
                }
				w3_first_js = true;
				load_extJS_execute();
				
                
            }
			function load_extJS_execute(){
				var static_script = document.querySelectorAll("script[type=lazyload_ext]");
                if(static_script.length < 1){
					return;
				}
				if(static_script[0].getAttribute("data-src")!==null){
					var js_obj = w3_load_js_uri(static_script[0]);
					js_obj.onload=function(){
						load_extJS_execute();
					}
					js_obj.onerror=function(){
						load_extJS_execute();
					}
				}else{
					w3_load_inline_js_single(static_script[0]);
					load_extJS_execute();
				}
			}
			function w3_load_js_uri(static_script){
				var ext_js_element = document.createElement("script");
				ext_js_element.async=true;
				if(typeof(static_script.attributes) != "undefined"){
					for (var att, i3 = 0, atts = static_script.attributes, n3 = atts.length; i3 < n3; i3++){
						att = atts[i3];
						if(att.nodeName != "data-src" && att.nodeName != "type"){
							ext_js_element.setAttribute(att.nodeName,att.nodeValue);
						}
					}
				}
				if(static_script.innerHTML != ""){
					ext_js_element.innerHTML = static_script.innerHTML;
				}
				ext_js_element.src=static_script.getAttribute("data-src");
				insertAfter(ext_js_element, static_script);
				delete static_script.dataset.src;
				delete static_script.type;
				if (static_script.parentNode !== null) {
					static_script.parentNode.removeChild(static_script);
				}
				return ext_js_element;
			}
			
			function load_intJS_main(){
                if(w3_internal_js_called){
                    return;
                }
				w3_internal_js_called = true;
                load_intJS();
            }
            function load_intJS() {
				if(w3_int_first_js){
                    return;
                }
				if(w3_inner_js_counter1+1 < w3_internal_js.length){				
                    w3_inner_js_counter1++;
					var script = w3_internal_js[w3_inner_js_counter1];
					if(script.getAttribute("type") !== null && script.getAttribute("type") == "lazyload_int"){
						if(script.getAttribute("data-src") != null){
							var s = w3_load_js_uri(script);
								console.log("internal js loaded");
								s.onload=function(){
									w3_external_single_loaded = 1;
									load_intJS();
								};
								s.onerror=function(){
									w3_external_single_loaded = 1;
									load_intJS();
									w3_redirect_resource_404(this.src);
								}
							
						}else{
							w3_load_inline_js_single(script);
							load_intJS();
						}
					}else{
						load_intJS();
					}
				}else{
					w3_load_inline_js();
				}
                
            }
			function w3_load_inline_js_single(script){
				console.log("single",w3_external_single_loaded);
				if(!w3_external_single_loaded){
					setTimeout(function(){w3_load_inline_js_single(script);},200);
					return false;
				}
				var s = document.createElement("script");
				for (var i2 = 0; i2 < script.attributes.length; i2++) {
					var attrib = script.attributes[i2];
					if(attrib.name != "type"){
						s.setAttribute(attrib.name, attrib.value);
					}	
				}
				s.innerHTML = script.innerHTML;
				insertAfter(s,script);
				if(script.parentNode !== null){
					script.parentNode.removeChild(script);
				}
			}
			function w3_load_inline_js(){
				for (var i3 = 0; i3 < w3_inline_js.length; i3++){
					var script = w3_inline_js[i3];
					w3_load_inline_js_single(script);
				}
				w3_int_first_js = true;
				w3_internal_js_loaded =1;
				var element = document.getElementsByTagName("html")[0];
				setTimeout(function(){element.classList.add("w3_js");},1000);
				load_extJS();
			}
			function w3_redirect_resource_404(js_src) {
				if(js_src.indexOf("w3-cache") != -1){
					var new_url = new URL(window.document.location);
					var if_test = new_url.searchParams.get("tester");
					if(!if_test){
						var form = document.createElement("form");
						form.setAttribute("method", "post");
						form.setAttribute("action", new_url.origin+new_url.pathname);
						var s = document.createElement("input");
						s.setAttribute("type", "submit");
						s.setAttribute("value", "Submit");
						form.appendChild(s); 
						document.getElementsByTagName("body")[0].appendChild(form);
						form.submit();
					}
				}
			}
            
			function load_googlefont(){
                if(w3_first_google_css == false && typeof w3_googlefont != undefined && w3_googlefont != null && w3_googlefont.length > 0){
                    w3_googlefont.forEach(function(src) {
                        var load_css = document.createElement("link");
                        load_css.rel = "stylesheet";
                        load_css.href = src;
                        load_css.type = "text/css";
                        var godefer2 = document.getElementsByTagName("link")[0];
                        if(godefer2 == undefined){
                            document.getElementsByTagName("head")[0].appendChild(load_css);
                        }else{
                            godefer2.parentNode.insertBefore(load_css, godefer2);
                        }
                    });
                    w3_first_google_css = true;
                }
            } 
        var w3_exclude_lazyload = null;
    
        var win_width = screen.availWidth;
		function w3_load_css_uri(static_css){
			var css_element = document.createElement("link");
			css_element.href=static_css.getAttribute("data-href");
			css_element.rel = "stylesheet";
			delete static_css.dataset.href;
			static_css.parentNode.insertBefore(css_element, static_css);
			static_css.parentNode.removeChild(static_css);
		}
        function load_extCss(){
			if(w3_first_css == false){
                lazyloadimages(0);
                lazyloadiframes(0);
                var static_css = document.querySelectorAll("link[data-href]");
				for(var i=0;i<static_css.length;i++){
					if(static_css[i].getAttribute("data-href")!==null){
						w3_load_css_uri(static_css[i]);
					}
				}
                w3_first_css = true;
           }
       }
		setInterval(function(){lazyloadiframes(top);},8000);

		setInterval(function(){lazyloadimages(0);fixwebp();},3000);
		document.addEventListener("click",function(){
			lazyloadimages(0);
		});
		function getDataUrl(img1, width, height) {
			var myCanvas = document.createElement("canvas");
			var ctx = myCanvas.getContext("2d");
			var img = new Image();
			myCanvas.width = parseInt(width);
			myCanvas.height = parseInt(height);
			ctx.drawImage(img, 0, 0);
			img1.src = myCanvas.toDataURL("image/png");
		}
       function lazyload_img(imgs,bodyRect,window_height,win_width){
           for (var i = 0; i < imgs.length; i++) {
                if(imgs[i].getAttribute("data-class") == "LazyLoad"){
                    var elemRect = imgs[i].getBoundingClientRect(),
                    offset   = elemRect.top - bodyRect.top;
                    if(elemRect.top != 0 && elemRect.top - window_height < w3_lazy_load_by_px ){
                        compStyles = window.getComputedStyle(imgs[i]);
                        if(compStyles.getPropertyValue("opacity") == 0){
                            continue;
                        }
                        var src = imgs[i].getAttribute("data-src") ? imgs[i].getAttribute("data-src") : imgs[i].src ;
                        var srcset = imgs[i].getAttribute("data-srcset") ? imgs[i].getAttribute("data-srcset") : "";
						if(!srcset){
							imgs[i].onload = function () {
							   this.setAttribute("data-done","Loaded");
							   if(typeof(w3speedup_after_iframe_img_load) == "function"){
									w3speedup_after_iframe_img_load(this);
							   }
							}
						}
						imgs[i].src = src;
                        if(srcset != null & srcset != ""){
                            imgs[i].srcset = srcset;
                        }
                        delete imgs[i].dataset.class;
                        
                    }else{
						if(typeof(load_dynamic_img) != "undefined"){
							var blanksrc = imgs[i].src;
							if(blanksrc.indexOf("data:") == -1){
								if(imgs[i].getAttribute("width") != null && imgs[i].getAttribute("height") != null){
									var width = parseInt(imgs[i].getAttribute("width"));
									var height = parseInt(imgs[i].getAttribute("height"));
									getDataUrl(imgs[i],width, height);
								}
							}
						}
					}
                }
            }
        }
    
        function lazyload_video(imgs,top,window_height,win_width){
            for (var i = 0; i < imgs.length; i++) {
				var elemRect = imgs[i].getBoundingClientRect();
                if(typeof(imgs[i].getElementsByTagName("source")[0]) == "undefined"){
					lazyload_video_source(imgs[i],top,window_height,win_width, elemRect);
				}else{
					var sources = imgs[i].getElementsByTagName("source");
					for (var j = 0; j < sources.length; j++){
						var source = sources[j];
						lazyload_video_source(source,top,window_height,win_width,elemRect);
					}
				}
            }
        }
		function lazyload_video_source(source,top,window_height,win_width,elemRect){
			if (typeof source != "undefined" && source.getAttribute("data-class") == "LazyLoad") {
				if (elemRect.top - window_height < 0 && (top > 0 || w3_internal_js_loaded == 1)) {
					var src = source.getAttribute("data-src") ? source.getAttribute("data-src") : source.src;
					var srcset = source.getAttribute("data-srcset") ? source.getAttribute("data-srcset") : "";
					if (source.srcset != null & source.srcset != "") {
						source.srcset = srcset;
					}
					if (typeof(source.getElementsByTagName("source")[0]) == "undefined") {
						if(source.tagName == "SOURCE"){
							source.parentNode.src = src;
							source.parentNode.load();
							if (source.parentNode.getAttribute("autoplay") !== null) {
								source.parentNode.play();
							}
						}else{
							console.log("rocket",source);
							source.src = src;
							source.load();
							if (source.getAttribute("autoplay") !== null) {
								source.play();
							}
						}
					} else {
						source.parentNode.src = src;
					}
					delete source.dataset.class;
					source.setAttribute("data-done", "Loaded");
				}
			}
		}
		function lazyloadimages(top){
			var imgs = document.querySelectorAll("img[data-class=LazyLoad]");
			var ads = document.getElementsByClassName("lazyload-ads");
			var sources = document.getElementsByTagName("video");
			var sources_audio = document.getElementsByTagName("audio");
			var bodyRect = document.body.getBoundingClientRect();
			var window_height = window.innerHeight;
			var win_width = screen.availWidth;
			lazyload_img(imgs,bodyRect,window_height,win_width);
			lazyload_video(sources,top,window_height,win_width);
			lazyload_video(sources_audio,top,window_height,win_width);
        }
    
        lazyloadimages(0);
    
        function lazyloadiframes(top){
            var bodyRect = document.body.getBoundingClientRect();
            var window_height = window.innerHeight;
            var win_width = screen.availWidth;
            var iframes = document.querySelectorAll("iframe[data-class=LazyLoad]");
            lazyload_img(iframes,bodyRect,window_height,win_width);
        }';
        $custom_js_path = $this->w3_get_cache_path('all-js').'/wnw-custom-inner-js.js';
        if(!is_file($custom_js_path)){
            $this->w3_create_file($custom_js_path,$this->w3_compress_js($inner_script_optimizer));
        }
        return $script.file_get_contents($custom_js_path);
    
    }
}