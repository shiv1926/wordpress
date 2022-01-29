<?php
namespace W3speedster;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class w3speed_html_optimize extends w3speedster_js{
    function w3_speedster($html){
		if(function_exists('w3speedup_pre_start_optimization')){
            $html = w3speedup_pre_start_optimization($html);
        }
        $upload_dir = wp_upload_dir();
		if(!is_file($upload_dir['basedir'].'/w3test.html') && !empty($html)){
			file_put_contents($upload_dir['basedir'].'/w3test.html',$html);
		}
        if($this->w3_no_optimization($html)){
            return $html;
        }
		if(function_exists('w3speedup_customize_add_settings')){
			$this->add_settings = w3speedup_customize_add_settings($this->add_settings);
		}
		if(function_exists('w3speedup_customize_main_settings')){
			$this->settings = w3speedup_customize_main_settings($this->settings);
		}
		$this->add_settings['disable_htaccess_webp'] = function_exists('w3_disable_htaccess_wepb') ? w3_disable_htaccess_wepb() : 0;
		if(!empty($this->settings['js'])){
			$html = $this->w3_custom_js_enqueue($html);
		}
        $html = str_replace(array('<script type="text/javascript"',"<script type='text/javascript'",'<style type="text/css"',"<style type='text/css'"),array('<script','<script','<style','<style'),$html);
        if(function_exists('w3speedup_before_start_optimization')){
            $html = w3speedup_before_start_optimization($html);
        }
        
        $js_json_exists = 0;
        /*if(is_file($file = $this->w3_get_full_url_cache_path().'/js.json')){
            $rep_js = json_decode(file_get_contents($file));
            if(is_array($rep_js[0]) && is_array($rep_js[1])){
                $js_json_exists = 1;
                if(is_file($file = $this->w3_get_full_url_cache_path().'/main_js.json')){
                    global $internal_js;
                    $internal_js = json_decode(file_get_contents($file));
                }
            }
        }*/
        $img_json_exists = 0;
        if(is_file($file = $this->w3_check_full_url_cache_path().'/img.json')){
            $rep_img = json_decode(file_get_contents($file));
            if(is_array($rep_img[0]) && is_array($rep_img[1])){
                $img_json_exists = 1;
            }
        }
        $rep_main_css = array();
        $css_json_exists = 0;
        if(is_file($file = $this->w3_check_full_url_cache_path().'/main_css.json')){
            $rep_main_css = json_decode(file_get_contents($file));
        }
		if(is_file($file = $this->w3_check_full_url_cache_path().'/css.json')){
            $rep_css = json_decode(file_get_contents($file));
            if(is_array($rep_css[0]) && is_array($rep_css[1])){
                $css_json_exists = 1;
            }
		}
        if(is_file($file = $this->w3_check_full_url_cache_path().'/content_head.json') && $css_json_exists){
            $rep_content_head = json_decode(file_get_contents($file));
            if(is_array($rep_content_head) && count($rep_content_head) > 0){
                $content_head_exists = 1;
            }else{
                $content_head_exists = 0;
            }
        }
		if($img_json_exists && $css_json_exists){
			$html = $this->w3_debug_time($html,'before create all links');
            $all_links = $this->w3_setAllLinks($html,array('script'));
			$html = $this->w3_debug_time($html,'after create all links');
            $html = $this->minify($html, $all_links['script']);
            $html = $this->w3_debug_time($html,'minify script');
            if(is_array($rep_content_head) && count($rep_content_head) > 0){
				for($i = 0; $i < count($rep_content_head); $i++){
					$html = $this->w3_insert_content_head($html,$rep_content_head[$i][0],$rep_content_head[$i][1]);
				}
			}
			$html = $this->w3_debug_time($html,'after replace json data');
            //global $main_css_url;
			//$main_css_url = $rep_main_css;
			//$this->add_settings['critical_css'] = $rep_main_css['critical_css'];
			$html = $this->w3_str_replace_bulk($html);
            $html = $this->w3_str_replace_bulk_json($html,array_merge($rep_css[0],$rep_img[0]),array_merge($rep_css[1],$rep_img[1]));
        }else{
			$html = $this->w3_debug_time($html,'before create all links');
            $lazyload = array('script','link','img','url');
			if(!empty($this->settings['lazy_load_iframe'])){
				$lazyload[] = 'iframe';
			}
			if(!empty($this->settings['lazy_load_video'])){
				$lazyload[] = 'video';
			}
			if(!empty($this->settings['lazy_load_audio'])){
				$lazyload[] = 'audio';
			}
            $all_links = $this->w3_setAllLinks($html,$lazyload);
			$html = $this->w3_debug_time($html,'after create all links');
            if(!empty($all_links['script'])){
				$html = $this->minify($html, $all_links['script']);
			}
			$html = $this->w3_debug_time($html,'minify script');
			$html = $this->lazyload($html, array('iframe'=>$all_links['iframe'],'video'=>$all_links['video'],'audio'=>$all_links['audio'],'img'=>$all_links['img'],'url'=>$all_links['url'] ) );
			if(!empty($all_links['style'])){
				$html = $this->load_style_tag_in_head($html, $all_links['style']);
			}
            $html = $this->w3_debug_time($html,'lazyload images');
            $html = $this->minify_css($html,$all_links['link']);
			$html = $this->w3_debug_time($html,'minify css');
            $html = $this->w3_str_replace_bulk($html);
            $html = $this->w3_str_replace_bulk_img($html);
            $ignore_critical_css = 0;
			if((function_exists('is_user_logged_in') && is_user_logged_in()) || is_search() || is_404()){
				$ignore_critical_css = 1;
			}
			if(function_exists('w3_no_critical_css')){
				$ignore_critical_css = w3_no_critical_css($this->add_settings['full_url']);
			}
			if(!empty($_REQUEST['w3_get_css_post_type'])){
				$html .= 'rocket22'.$this->w3_preload_css_path().'--'.$this->add_settings['critical_css'].'--'.is_file($this->w3_preload_css_path().'/'.$this->add_settings['critical_css']);
			}
			if(!empty($this->settings['load_critical_css']) && !$ignore_critical_css){
				if(!is_file($this->w3_preload_css_path().'/'.$this->add_settings['critical_css'])){
					$this->w3_add_page_critical_css();
				}else{
					$critical_css = file_get_contents($this->w3_preload_css_path().'/'.$this->add_settings['critical_css']);
					if(!empty($critical_css)){
						$html = $this->w3_insert_content_head($html , '{{main_w3_critical_css}}',2);
						if(function_exists('w3speedup_customize_critical_css')){
							$critical_css = w3speedup_customize_critical_css($critical_css);
						}
						if(!empty($this->settings['load_critical_css_style_tag'])){
							$this->w3_str_replace_set_css('{{main_w3_critical_css}}','<style>'.$critical_css.'</style>');
						}else{
							$enable_cdn = 0;
							if($this->add_settings['image_home_url'] != $this->add_settings['home_url']){
								$ext = '.css';
								if(empty($this->add_settings['exclude_cdn']) || !in_array($ext,$this->add_settings['exclude_cdn'])){
									$enable_cdn = 1;
								}
							}
							$this->w3_str_replace_set_css('{{main_w3_critical_css}}','<link rel="stylesheet" href="'.str_replace($this->add_settings['document_root'],($enable_cdn ? $this->add_settings['image_home_url'] : $this->add_settings['wp_site_url']),$this->w3_preload_css_path().'/'.$this->add_settings['critical_css']).'"/>');
						}
					}else{
						$this->w3_add_page_critical_css();
					}
				}
			}
			$html = $this->w3_str_replace_bulk_css($html);
            $html = $this->w3_debug_time($html,'replace json');
			$preload_html = $this->w3_preload_resources();
			$html = $this->w3_insert_content_head($html , $preload_html,3);
			$this->w3_insert_content_head_in_json();
		}
        //$starte = microtime();
        //$html .= 'rocket'.($starte-$startm);
		$position = strrpos($html,'</body>');
		$html =	substr_replace( $html, '<script>'.$this->w3_lazy_load_images().'</script>', $position, 0 );
        $html = $this->w3_debug_time($html,'w3 script');
		
        if(function_exists('w3speedup_after_optimization')){
            $html = w3speedup_after_optimization($html);
        }
		$html = $this->w3_debug_time($html,'before final output');
        return $html;
    } 
    function w3_add_page_critical_css(){
		if(!empty($this->settings['optimization_on'])){
			$preload_css = w3_get_option('w3speedup_preload_css');
			$preload_css = !empty($preload_css) ? $preload_css : array();
			if(is_array($preload_css) && count($preload_css) > 20){
				return;
			}
			if(!array_key_exists($this->add_settings['full_url_without_param'],$preload_css) || (!empty($preload_css[$this->add_settings['full_url_without_param']]) && $preload_css[$this->add_settings['full_url_without_param']][0] != $this->add_settings['critical_css']) ){
				$preload_css[$this->add_settings['full_url_without_param']] = array($this->add_settings['critical_css'],2,$this->w3_preload_css_path());
				w3_update_option('w3speedup_preload_css',$preload_css,'no');
				w3_update_option('w3speedup_preload_css_total',(int)w3_get_option('w3speedup_preload_css_total')+1,'no');
				return 'added';
			}
		}
	}
	public function w3_header_check() {
        return is_admin()
			|| $this->isSpecialContentType()
	    	|| $this->isSpecialRoute()
	    	|| $_SERVER['REQUEST_METHOD'] === 'POST'
	    	|| $_SERVER['REQUEST_METHOD'] === 'PUT'
			|| $_SERVER['REQUEST_METHOD'] === 'DELETE';
	}
   private function isSpecialContentType() {
		if($this->w3_endswith($this->add_settings['full_url'],'.xml') || $this->w3_endswith($this->add_settings['full_url'],'.xsl')){
        	return true;
        }
		return false;
    }
    private function isSpecialRoute() {
		$current_url = $this->add_settings['full_url'];
		if( preg_match('/(.*\/wp\/v2\/.*)/', $current_url) ) {
			return true;
		}
		if( preg_match('/(.*wp-login.*)/', $current_url) ) {
			return true;
		}
		if( preg_match('/(.*wp-admin.*)/', $current_url) ) {
			return true;
		}
		return false;
    }
	
	function w3_custom_js_enqueue($html){
		if(!empty($this->settings['custom_js'])){
			$custom_js = 'setTimeout(function(){document.dispatchEvent(w3_event);},50);'.stripslashes($this->settings['custom_js']);
		}else{
			$custom_js = 'console.log("js loaded");';
		}
		$js_file_name1 = 'custom_js_after_load.js';
		if(!is_file($this->w3_get_cache_path('js').'/'.$js_file_name1)){
			$this->w3_create_file($this->w3_get_cache_path('js').'/'.$js_file_name1, $custom_js);
		}
		$html = $this->w3_str_replace_last('</body>','<script src="'.$this->add_settings['cache_url'].'/js/'.$js_file_name1.'"></script></body>',$html);
		return $html;
		
	}
    function w3_no_optimization($html){
        if(!empty($_REQUEST['orgurl']) || strpos($html,'<body') === false){
            return true;
        }
        if (function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            return true;
        }
		if($this->w3_header_check()){
			return true;
		}
        if(empty($this->settings['optimization_on']) && empty($_REQUEST['tester']) && empty($_REQUEST['testing'])){
             return true;
        }
		if(function_exists('w3speedup_exclude_page_optimization')){
            return w3speedup_exclude_page_optimization($html);
        }
        if($this->w3_check_if_page_excluded($this->settings['exclude_pages_from_optimization'])){
            return true;
        }
        global $current_user;
        if((empty($_REQUEST['testing']) && is_404()) || (!empty($current_user) && current_user_can('edit_others_pages')) ){
            return true;
        }
        return false;
    }
    
    function w3_start_optimization_callback(){
        ob_start(array($this,"w3_speedster") );
		//add_action( 'shutdown', array($this,'w3_ob_end_flush'));
        //register_shutdown_function(array($this,'w3_ob_end_flush') );
    }
    
    function w3_ob_end_flush() {
    
        if (ob_get_level() != 0) {
    
            ob_end_flush();
    
         }
    
    }
	function load_style_tag_in_head($html, $style_tags){
		$load_style_tag_in_head	= !empty($this->settings['load_style_tag_in_head']) ? explode("\r\n", $this->settings['load_style_tag_in_head']) : array();
		foreach($style_tags as $style_tag){
			$load_in_head = 0;
			foreach($load_style_tag_in_head as $ex_css){
				if(!empty($ex_css) && strpos($style_tag, $ex_css) !== false){
					$load_in_head = 1;
				}
			}
			if($load_in_head){
				$html = $this->w3_insert_content_head($html,'/<style(.*)'.$ex_css.'(.*)<\/style>/',5);
				$html = $this->w3_insert_content_head($html,$style_tag,4);
			}
		}
		return $html;
	}
    function lazyload($html, $all_links){
		$upload_dir   = wp_upload_dir();
        $excluded_img = !empty($this->settings['exclude_lazy_load']) ? explode("\r\n",stripslashes($this->settings['exclude_lazy_load'])) : array();
	    if(!empty($this->settings['lazy_load_iframe'])){
            $iframe_links = $all_links['iframe'];
            foreach($iframe_links as $img){
				if(strpos($img,'\\') !== false){
					continue;
				}
                $exclude_image = 0;
                foreach( $excluded_img as $ex_img ){
                    if(!empty($ex_img) && strpos($img,$ex_img)!==false){
                        $exclude_image = 1;
                    }
                }
                if($exclude_image){
                    continue;
                }
                $img_obj = $this->w3_parse_link('iframe',$img);
				$iframe_html = '';
                if(strpos($img_obj['src'],'youtu') !== false){
                    preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)([a-zA-Z0-9_-]+)#", $img_obj['src'], $matches);
                    if(empty($img_obj['style'])){
                        $img_obj['style'] = '';
                    }
                    $img_obj['style'] .= 'background-image:url(https://i.ytimg.com/vi/'.trim(end($matches)).'/sddefault.jpg)';
					//$iframe_html = '<img width="68" height="48" class="iframe-img" src="/wp-content/uploads/yt-png2.png"/>';
                }
                $img_obj['data-src'] = $img_obj['src'];
                $img_obj['src'] = 'about:blank';
                $img_obj['data-class'] = 'LazyLoad';
				
                $this->w3_str_replace_set_img($img,$this->w3_implode_link_array('iframe',$img_obj).$iframe_html);
            }
	    }
        if(!empty($this->settings['lazy_load_video'])){
            $iframe_links = $all_links['video'];
			if(strpos($this->add_settings['upload_base_url'],$this->add_settings['wp_site_url']) !== false){
				$v_src = $this->add_settings['image_home_url'].str_replace($this->add_settings['wp_site_url'],'',$this->add_settings['upload_base_url']).'/blank.mp4';
			}else{
				$v_src = $this->add_settings['upload_base_url'].'/blank.mp4';
			}
            foreach($iframe_links as $img){
				if(strpos($img,'\\') !== false){
					continue;
				}
                $exclude_image = 0;
                foreach( $excluded_img as $ex_img ){
                    if(!empty($ex_img) && strpos($img,$ex_img)!==false){
                        $exclude_image = 1;
                    }
                }
                if($exclude_image){
                    continue;
                }
				
                $img_new = str_replace('src=','data-class="LazyLoad" src="'.$v_src.'" data-src=',$img);
                $this->w3_str_replace_set_img($img,$img_new);
            }
        }
		if(!empty($this->settings['lazy_load_audio'])){
            $iframe_links = $all_links['audio'];
			if(strpos($this->add_settings['upload_base_url'],$this->add_settings['wp_site_url']) !== false){
				$v_src = $this->add_settings['image_home_url'].str_replace($this->add_settings['wp_site_url'],'',$this->add_settings['upload_base_url']).'/blank.mp3';
			}else{
				$v_src = $this->add_settings['upload_base_url'].'/blank.mp3';
			}
            foreach($iframe_links as $img){
				if(strpos($img,'\\') !== false){
					continue;
				}
                $exclude_image = 0;
                foreach( $excluded_img as $ex_img ){
                    if(!empty($ex_img) && strpos($img,$ex_img)!==false){
                        $exclude_image = 1;
                    }
                }
                if($exclude_image){
                    continue;
                }
				
                $img_new = str_replace('src=','data-class="LazyLoad" src="'.$v_src.'" data-src=',$img);
                $this->w3_str_replace_set_img($img,$img_new);
            }
        }
        $img_links = $all_links['img'];
        if(!empty($all_links['img'])){
			$lazy_load_img = !empty($this->settings['lazy_load']) ? 1 : 0;
            $enable_cdn = 0;
            if($this->add_settings['image_home_url'] != $this->add_settings['wp_site_url'] ){
                $enable_cdn = 1;
            }
            $exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
			
            $webp_enable = $this->add_settings['webp_enable'];
			$webp_enable_instance = $this->add_settings['webp_enable_instance'];
			$webp_enable_instance_replace = $this->add_settings['webp_enable_instance_replace'];
			$theme_root_array = explode('/',$this->add_settings['theme_base_url']);
			$theme_root = array_pop($theme_root_array);
			include_once(W3SPEEDSTER_PLUGIN_DIR . 'includes/class_image.php');
			$w3_speedster_opt_img = new w3speedster_optimize_image();
			foreach($img_links as $img){
				$blank_image_url = $enable_cdn ? str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'],$this->add_settings['upload_base_url']) : $this->add_settings['upload_base_url'];
                $exclude_image = 0;
                $imgnn = $img;
				$imgnn_arr = $this->w3_parse_link('img',str_replace($this->add_settings['image_home_url'],$this->add_settings['wp_site_url'],$imgnn));
				if(empty($imgnn_arr['src'])){
					continue;
				}
				if(strpos($imgnn_arr['src'],'\\') !== false){
					continue;
				}
				if(!$this->w3_is_external($imgnn_arr['src'])){
					if(strpos($imgnn_arr['src'],$theme_root) !== false){
						$img_root_path = rtrim($this->add_settings['theme_base_dir'],'/');
						$img_root_url = rtrim($this->add_settings['theme_base_url'],'/');
					}else{
						$img_root_path = $this->add_settings['upload_base_dir'];
						$img_root_url = $this->add_settings['upload_base_url'];
					}
					if(strpos($imgnn_arr['src'],'?') !== false){
						$temp_src = explode('?',$imgnn_arr['src']);
						$imgnn_arr['src'] = $temp_src[0];
					}
					$img_url_arr = parse_url($imgnn_arr['src']);
					$w3_img_ext = '.'.pathinfo($imgnn_arr['src'], PATHINFO_EXTENSION);
					$imgsrc_filepath = str_replace($img_root_url,'',$this->add_settings['home_url'].$img_url_arr['path']);
					$imgsrc_webpfilepath = str_replace($this->add_settings['upload_path'],$this->add_settings['webp_path'],$img_root_path).$imgsrc_filepath.'w3.webp';
					if($enable_cdn){
						$image_home_url = $this->add_settings['image_home_url'];
						foreach($exclude_cdn_arr as $cdn){
							if(strpos($img,$cdn) !== false){
								$image_home_url = $this->add_settings['wp_site_url'];
								break;
							}
						}
						$imgnn = str_replace($this->add_settings['wp_site_url'],$image_home_url,$imgnn);
					}else{
						$image_home_url = $this->add_settings['wp_site_url'];
					}
					
					$img_size = is_file($img_root_path.$imgsrc_filepath) ? @getimagesize($img_root_path.$imgsrc_filepath) : array();
					if(!empty($img_size[0]) && !empty($img_size[1])){
						if(empty($imgnn_arr['width']) || $imgnn_arr['width'] == 'auto'){
							$imgnn = str_replace(array(' width="auto"',' src='),array('',' width="'.$img_size[0].'" src='),$imgnn);
						}
						if(empty($imgnn_arr['height']) || $imgnn_arr['width'] == 'height'){
							$imgnn = str_replace(array(' width="auto"',' src='),array('',' height="'.$img_size[1].'" src='),$imgnn);
						}
						if((int)$img_size[0]/(int)$img_size[1] > 1.9){
							$blank_image_url .= '/blank-h.png';
						}elseif((int)$img_size[0]/(int)$img_size[1] > 1.1){
							$blank_image_url .= '/blank.png';
						}elseif((int)$img_size[0]/(int)$img_size[1] < .9){
							$blank_image_url .= '/blank-p.png';
						}else{
							$blank_image_url .= '/blank-square.png';
						}
					}else{
						$blank_image_url .= '/blank.png';
					}
					if(strpos($img, ' srcset=') === false && !function_exists('w3_disable_srcset')){
						if(!empty($img_size[0]) && $img_size[0] > 600){
							$w3_thumbnail = rtrim(str_replace($w3_img_ext.'$','-595xh'.$w3_img_ext.'$',$imgsrc_filepath.'$'),'$');
							if(in_array($w3_img_ext, $webp_enable) && !is_file($this->add_settings['document_root'].$w3_thumbnail) && !empty($this->settings['opt_img_on_the_go'])){
								$response = $w3_speedster_opt_img->w3_optimize_attachment_url($img_root_path.$imgsrc_filepath);
							}
							if(is_file($img_root_path.$w3_thumbnail)){
								$w3_thumbnail = str_replace(' ','%20',$w3_thumbnail);
								$imgnn_arr['src'] = str_replace(' ','%20',$imgnn_arr['src']);
								$imgnn = str_replace(' src=',' srcset="'.$img_root_url.$w3_thumbnail.' 600w, '.$imgnn_arr['src'].' 1920w" src=',$imgnn);
							}
						}
					}
					if(count($webp_enable) > 0 && in_array($w3_img_ext, $webp_enable)){
						if(!empty($this->settings['opt_img_on_the_go']) && !is_file($imgsrc_webpfilepath) && is_file($img_root_path.$imgsrc_filepath)){
							$w3_speedster_opt_img->w3_optimize_attachment_url($img_root_path.$imgsrc_filepath);
						}
						if(is_file($imgsrc_webpfilepath) && (!empty($this->add_settings['disable_htaccess_webp']) || !is_file($this->add_settings['wp_document_root']."/.htaccess") || $this->add_settings['image_home_url'] != $this->add_settings['wp_site_url'] ) ){
							$imgnn = str_replace($webp_enable_instance,$webp_enable_instance_replace,$imgnn);
						}
					}
				}
				if($lazy_load_img){
					foreach( $excluded_img as $ex_img ){
						if(!empty($ex_img) && strpos($img,$ex_img)!==false){
							$exclude_image = 1;
						}
					}
					if(!empty($imgnn_arr['data-class']) && strpos($imgnn_arr['data-class'],'LazyLoad') !== false){
						$exclude_image = 1;
					}
				}else{
					$exclude_image = 1;
				}
				if(function_exists('w3speedup_image_exclude_lazyload')){
					$exclude_image = w3speedup_image_exclude_lazyload($exclude_image,$img, $imgnn_arr);
				}
				if($exclude_image){
					if($img != $imgnn){
						$this->w3_str_replace_set_img($img,$imgnn);
					}
					continue;
				}
				if(strpos($blank_image_url,'/blank') === false){
					$blank_image_url .= '/blank.png';
				}
                $imgnn = str_replace(' src=',' data-class="LazyLoad" src="'. $blank_image_url .'" data-src=',$imgnn);
				if(strpos($imgnn, ' srcset=') !== false){
					$imgnn = str_replace(' srcset=',' data-srcset=',$imgnn);
				}
				if(function_exists('w3speedup_customize_image')){
					$imgnn = w3speedup_customize_image($imgnn,$img,$imgnn_arr);
				}
                $this->w3_str_replace_set_img($img,$imgnn);
            }
		}
       
        $html = $this->w3_convert_arr_relative_to_absolute($html, $this->add_settings['wp_home_url'].'/index.php',$all_links['url']);
        return $html;
    }
    
}