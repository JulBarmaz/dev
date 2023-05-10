<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetHelperPlayer{
	public function __construct() {
		/* при замене не забыть убрать лицензию и медиафайлы */
		Portal::getInstance()->addScript("/redistribution/video_js/video.min.js");
		Portal::getInstance()->addScript("/redistribution/video_js/lang/".Text::getLanguage().".js");
		$script ='$(document).ready(function(){
					$(".video-js").each(function (videoIndex) {
						var videoId = $(this).attr("id");
						videojs(videoId).ready(function(){
							this.on("play", function(e) {
								//pause other video
								$(".video-js").each(function (index) {
									if (videoIndex !== index) {
										this.player.pause();
									}
								});
							});
						});
					});
				});'; 
		Portal::getInstance()->addScriptDeclaration($script);
		Portal::getInstance()->addStyleSheet("/redistribution/video_js/video-js.min.css");
	}
	public function render($video, $width="100%", $height="auto", $ratio="16-9", $preload="none"){
		$html="";
		if (is_object($video) && $video->v_id){
			$videofiles=array('ogg'=>'', 'mp4'=>'', 'webm'=>'', 'youtube'=>'');
			if($video->v_video_youtube){
				$videofiles['youtube'] = $video->v_video_youtube;
				Portal::getInstance()->addScript("/redistribution/video_js/Youtube.min.js");
			} else {
				if($video->v_video_mp4){
					if (Router::isAbsoluteLink($video->v_video_mp4)) $videofiles['mp4'] = $video->v_video_mp4;
					else $videofiles['mp4'] = Router::_(BARMAZ_UF.$video->v_video_mp4, false, true, 1, 2);
				}
				if($video->v_video_webm){
					if (Router::isAbsoluteLink($video->v_video_webm)) $videofiles['webm'] = $video->v_video_webm;
					else $videofiles['webm'] = Router::_(BARMAZ_UF.$video->v_video_webm, false, true, 1, 2);
				}
				if($video->v_video_ogg){
					if (Router::isAbsoluteLink($video->v_video_ogg)) $videofiles['ogg'] = $video->v_video_ogg;
					else $videofiles['ogg'] = Router::_(BARMAZ_UF.$video->v_video_ogg, false, true, 1, 2);
				}
			}
			if ($videofiles['mp4'] || $videofiles['webm'] || $videofiles['ogg'] || $videofiles['youtube']){
				$html.="<div class=\"videocontainer\">";
				$html.="<video id=\"player_".$video->v_id."\" class=\"video-js vjs-default-skin vjs-fluid vjs-".$ratio."\" controls preload=\"".$preload."\"";
				if($video->v_image) $html.=" poster=\"".$video->v_image."\"";
				$html.=" title=\"".$video->v_title."\" width=\"".$width."\"";
				if($height!="auto") $html.=" height=\"".$height."\"";
				if($videofiles['youtube']) $html.=' data-setup=\'{ "techOrder": ["youtube", "html5"], "sources": [{ "type": "video/youtube", "src": "'.$videofiles['youtube'].'"}] }\'';
				else $html.=' data-setup=\'{"fluid": true}\'';
				$html.=">";
				if ($videofiles['mp4']) $html.="<source src=\"".$videofiles['mp4']."\" type=\"video/mp4\" />";
				if ($videofiles['webm']) $html.="<source src=\"".$videofiles['webm']."\" type=\"video/webm\" />";
				if ($videofiles['ogg']) $html.="<source src=\"".$videofiles['ogg']."\" type=\"video/ogg\" />";
				$html.="</video>";
				$html.="</div>";
			}
		}
		return $html;
	}
}

?>