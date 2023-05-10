<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class contentPluginopengraph_article extends Plugin {

	private $modules=array("article");
	protected $_events=array("content.rendered");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("opengraphtitle", "string", "");
		$this->addParam("opengraphusestatictitle", "boolean", 0);
		$this->addParam("opengraphtype", "select", "article", false, SpravStatic::getCKArray("opengraph_types"));
		$this->addParam("opengraphstaticurl", "boolean", 0);
		$this->addParam("opengraphusestaticurl", "string", "");
		$this->addParam("opengraphsitename", "string", "");
		$this->addParam("opengraphdynimage", "boolean", 0);
		$this->addParam("opengraphimage", "string", "");
		$this->addParam("opengraphusestaticdesc", "boolean", 0);
		$this->addParam("opengraphdesc", "string", "");
		$this->addParam("opengraphtitle_2", "title", Text::_("Facebook parameters for opengraph"));
		$this->addParam("opengraphauthor", "string", "");
		$this->addParam("opengraphpublisher", "string", "");
		$this->addParam("opengraphadmin", "string", "");
		$this->addParam("opengraphappid", "string", "");
		$this->addParam("opengraphtitle_3", "title", Text::_("Location for opengraph"));
		$this->addParam("opengraphlatitude", "string", "");
		$this->addParam("opengraphlongitude", "string", "");
		$this->addParam("opengraphaddress", "string", "");
		$this->addParam("opengraphlocality", "string", "");
		$this->addParam("opengraphregion", "string", "");
		$this->addParam("opengraphpostal", "string", "");
		$this->addParam("opengraphcountry", "string", "");
		$this->addParam("opengraphtitle_4", "title", Text::_("Contacts for opengraph"));
		$this->addParam("opengraphemail", "string", "");
		$this->addParam("opengraphphone", "string", "");
		$this->addParam("opengraphfax", "string", "");
		$this->addParam("opengraphtitle_5", "title", Text::_("Audio for opengraph"));
		$this->addParam("opengraphdynaudio", "boolean", 0);
		$this->addParam("opengraphaudio", "string", "");
		$this->addParam("opengraphaudiotitle", "string", "");
		$this->addParam("opengraphaudioartist", "string", "");
		$this->addParam("opengraphaudioalbum", "string", "");
		$this->addParam("opengraphtitle_6", "title", Text::_("Video for opengraph"));
		$this->addParam("opengraphdynvideo", "boolean", 0);
		$this->addParam("opengraphvideoyoutube", "boolean", 0);
		$this->addParam("opengraphvideo", "string", "");
		$this->addParam("opengraphvideowidth", "integer", 640);
		$this->addParam("opengraphvideoheight", "integer", 480);
		$this->addParam("opengraphtitle_7", "title", Text::_("Product params for opengraph"));
		$this->addParam("opengraphupc", "string", "");
		$this->addParam("opengraphisbn", "string", "");
	}
	
	protected function onRaise($event, &$data) {
		$module=Module::getInstance()->getName();
		$view=Request::getSafe("view");
		if(defined('_ADMIN_MODE') || !in_array($module, $this->modules)) return "";
		switch($event){
			case "content.rendered":
				$opengraphURL=Router::_("index.php?module=article&amp;view=read&amp;psid=".$data->a_id."&amp;alias=".$data->a_alias);
				$opengraphTitle				= $this->getParam('opengraphtitle');
				$opengraphUseStaticTitle	= $this->getParam('opengraphusestatictitle');
				$opengraphType				= $this->getParam('opengraphtype');
				$opengraphStaticURL			= $this->getParam('opengraphstaticurl');
				$opengraphUseStaticURL		= $this->getParam('opengraphusestaticurl');
				$opengraphSiteName			= $this->getParam('opengraphsitename');
				$opengraphDynImage			= $this->getParam('opengraphdynimage');
				$opengraphImage				= $this->getParam('opengraphimage');
				$opengraphUseStaticDesc		= $this->getParam('opengraphusestaticdesc');
				$opengraphDesc				= $this->getParam('opengraphdesc');
				// Facebook parameter
				$opengraphAuthor			= $this->getParam('opengraphauthor');
				$opengraphPublisher			= $this->getParam('opengraphpublisher');
				$opengraphAdmin				= $this->getParam('opengraphadmin');
				$opengraphAppid				= $this->getParam('opengraphappid');
				// Location 
				$opengraphLatitude			= $this->getParam('opengraphlatitude');
				$opengraphLongitude			= $this->getParam('opengraphlongitude');
				$opengraphAddress			= $this->getParam('opengraphaddress');
				$opengraphLocality			= $this->getParam('opengraphlocality');
				$opengraphRegion			= $this->getParam('opengraphregion');
				$opengraphPostal			= $this->getParam('opengraphpostal');
				$opengraphCountry			= $this->getParam('opengraphcountry');
				// Contacts
				$opengraphEmail				= $this->getParam('opengraphemail');
				$opengraphPhone				= $this->getParam('opengraphphone');
				$opengraphFax				= $this->getParam('opengraphfax');
 				// Video
				$opengraphDynVideo			= $this->getParam('opengraphdynvideo');
				$opengraphVideoYoutube		= $this->getParam('opengraphvideoyoutube');
				$opengraphVideo				= $this->getParam('opengraphvideo');
				$opengraphVideoWidth		= $this->getParam('opengraphvideowidth');
				$opengraphVideoHeight		= $this->getParam('opengraphvideoheight');
				// Audio
				$opengraphDynAudio			= $this->getParam('opengraphdynaudio');
				$opengraphAudio				= $this->getParam('opengraphaudio');
				$opengraphAudioTitle		= $this->getParam('opengraphaudiotitle');
				$opengraphAudioArtist		= $this->getParam('opengraphaudioartist');
				$opengraphAudioAlbum		= $this->getParam('opengraphaudioalbum');
				// Products
				$opengraphUpc				= $this->getParam('opengraphupc');
				$opengraphIsbn				= $this->getParam('opengraphisbn');
				
				/*************************************************************/
				if ($opengraphUseStaticURL)		$opengraphURL = $opengraphStaticURL;
				if (!$opengraphUseStaticTitle)	$opengraphTitle = $data->a_title;
				if (!$opengraphUseStaticDesc)	$opengraphDesc = $data->a_meta_description;
				
				Portal::getInstance()->addCustomTag('<meta property="og:title" content="'.$opengraphTitle.'"/>');
				Portal::getInstance()->addCustomTag('<meta property="og:url" content="'.$opengraphURL.'"/>');
				if ($opengraphSiteName != '') { Portal::getInstance()->addCustomTag('<meta property="og:site_name" content="'.$opengraphSiteName.'"/>'); }
				if ($opengraphDesc != '') { Portal::getInstance()->addCustomTag('<meta property="og:description" content="'.$opengraphDesc.'"/>'); }
				if ($opengraphAuthor != '') { Portal::getInstance()->addCustomTag('<meta property="article:author" content="'.$opengraphAuthor.'"/>');	}
				if ($opengraphPublisher != '') { Portal::getInstance()->addCustomTag('<meta property="article:publisher" content="'.$opengraphPublisher.'"/>');	}
				if ($opengraphAdmin != '') { Portal::getInstance()->addCustomTag('<meta property="fb:admins" content="'.$opengraphAdmin.'"/>');	}
				if ($opengraphAppid != '') { Portal::getInstance()->addCustomTag('<meta property="fb:app_id" content="'.$opengraphAppid.'"/>'); }
				if ($opengraphLatitude != '') { Portal::getInstance()->addCustomTag('<meta property="og:latitude" content="'.$opengraphLatitude.'"/>'); }
				if ($opengraphLongitude != '') { Portal::getInstance()->addCustomTag('<meta property="og:longitude" content="'.$opengraphLongitude.'"/>'); }
				if ($opengraphAddress != '') { Portal::getInstance()->addCustomTag('<meta property="og:street-address" content="'.$opengraphAddress.'"/>'); }
				if ($opengraphLocality != '') { Portal::getInstance()->addCustomTag('<meta property="og:locality" content="'.$opengraphLocality.'"/>'); }
				if ($opengraphRegion != '') { Portal::getInstance()->addCustomTag('<meta property="og:region" content="'.$opengraphRegion.'"/>'); }
				if ($opengraphPostal != '') { Portal::getInstance()->addCustomTag('<meta property="og:postal-code" content="'.$opengraphPostal.'"/>'); }
				if ($opengraphCountry != '') { Portal::getInstance()->addCustomTag('<meta property="og:country-name" content="'.$opengraphCountry.'"/>'); }
				if ($opengraphEmail != '') { Portal::getInstance()->addCustomTag('<meta property="og:email" content="'.$opengraphEmail.'"/>'); }
				if ($opengraphPhone != '') { Portal::getInstance()->addCustomTag('<meta property="og:phone_number" content="'.$opengraphPhone.'"/>'); }
				if ($opengraphFax != '') { Portal::getInstance()->addCustomTag('<meta property="og:fax_number" content="'.$opengraphFax.'"/>'); }
				
				$youtube=true;
				if ($opengraphVideoYoutube) { $opengraphVideoYoutubeCode = $opengraphVideo; }
				if ($opengraphDynVideo) {
					preg_match('/data=[\\"\']([-0-9A-Za-z\/_]*.(flv|swf))/i', $data->a_text, $video);
					preg_match('/youtube\.com\/(v\/|watch\?v=)([\w\-]+)/', $data->a_text, $videoyoutube);
					if (array_key_exists(1, $video)) {
						if (substr($video[1], 0, 4) != 'http') {
							$video[1] = Portal::getInstance()->getURI().$video[1];
						}
						$opengraphVideo = $video[1];
						$youtube=false;
					} else {
						if (array_key_exists(2, $videoyoutube)) {
							$opengraphVideo = 'http://www.youtube.com/v/'.$videoyoutube[2].'?version=3&autohide=1';
							$opengraphImage = 'http://i2.ytimg.com/vi/'.$videoyoutube[2].'/default.jpg';
							$opengraphType = 'video';
							$youtube=false;
						}
					}
				}
				
				if ($opengraphVideo != '') {
					if ($opengraphVideoYoutube == 1 && $youtube) {
						$opengraphVideo = 'http://www.youtube.com/v/'.$opengraphVideoYoutubeCode.'?version=3&autohide=1';
						$opengraphImage = 'http://i2.ytimg.com/vi/'.$opengraphVideoYoutubeCode.'/default.jpg';
						$opengraphType = 'video';
					}
					Portal::getInstance()->addCustomTag('<meta property="og:video" content="'.$opengraphVideo.'"/>');
					Portal::getInstance()->addCustomTag('<meta property="og:video:width" content="'.$opengraphVideoWidth.'"/>');
					Portal::getInstance()->addCustomTag('<meta property="og:video:height" content="'.$opengraphVideoHeight.'"/>');
					Portal::getInstance()->addCustomTag('<meta property="og:video:type" content="application/x-shockwave-flash"/>');
				}
				
				if ($opengraphDynAudio) {
					preg_match('/src=[\\"\']([-0-9A-Za-z\/_]*.(mp3))/i', $data->a_text, $audio);
					if (array_key_exists(1, $audio)) {
						if (substr($audio[1], 0, 4) != 'http') {
							$audio[1] = Portal::getInstance()->getURI().$audio[1];
						}
						$opengraphAudio = $audio[1];
					}
				}
				if ($opengraphAudio != '') {
					Portal::getInstance()->addCustomTag('<meta property="og:audio" content="'.$opengraphAudio.'"/>');
					if ($opengraphAudioTitle != '') { Portal::getInstance()->addCustomTag('<meta property="og:audio:title" content="'.$opengraphAudioTitle.'"/>'); }
					if ($opengraphAudioArtist != '') { Portal::getInstance()->addCustomTag('<meta property="og:audio:artist" content="'.$opengraphAudioArtist.'"/>'); }
					if ($opengraphAudioAlbum != '') { Portal::getInstance()->addCustomTag('<meta property="og:audio:album" content="'.$opengraphAudioAlbum.'"/>'); }
					Portal::getInstance()->addCustomTag('<meta property="og:audio:type" content="application/mp3"/>');
				}
				
				if ($opengraphDynImage) {
					if ($data->a_thumb) {
						$opengraphImage=BARMAZ_UF."/article/thumbs/".Files::splitAppendix($data->a_thumb);
					} else {
						preg_match('/src=[\\"\']([-0-9A-Za-z\/_]*.(jpg|png|gif|jpeg))/i', $data->a_text, $image);
						if (array_key_exists(1, $image)) {
							if (substr($image[1], 0, 4) != 'http') {
								$image[1] = Portal::getInstance()->getURI().$image[1];
							}
							$opengraphImage = $image[1];
						}
					}
				}
				if ($opengraphImage) { Portal::getInstance()->addCustomTag('<meta property="og:image" content="'.$opengraphImage.'"/>'); }
				
				Portal::getInstance()->addCustomTag('<meta property="og:type" content="'.$opengraphType.'"/>');
				if ($opengraphType == 'album' || $opengraphType == 'book' || $opengraphType == 'drink' || $opengraphType == 'food' || $opengraphType == 'game' || $opengraphType == 'movie' || $opengraphType == 'product' || $opengraphType == 'song' || $opengraphType == 'tv_show') {
					if ($opengraphUpc != '') { Portal::getInstance()->addCustomTag('<meta property="og:upc" content="'.$opengraphUpc.'"/>'); }
					if ($opengraphIsbn != '') { Portal::getInstance()->addCustomTag('<meta property="og:isbn" content="'.$opengraphIsbn.'"/>'); }
				}
			break;
			default:
			break;
		}
	}
}
?>