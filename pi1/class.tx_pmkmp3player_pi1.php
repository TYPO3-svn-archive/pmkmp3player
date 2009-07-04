<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2009 Peter Klein <peter@umloud.dk>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Flash MP3 Player' for the 'pmkmp3player' extension.
 *
 * @author	Peter Klein <peter@umloud.dk>
 * @package	TYPO3
 * @subpackage	tx_pmkmp3player
 */
class tx_pmkmp3player_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_pmkmp3player_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_pmkmp3player_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'pmkmp3player';	// The extension key.
	var $pi_checkCHash = true;
	var $uploadDir = 'uploads/tx_pmkmp3player/';	// Upload directory
	var $genretypes = array("Blues","ClassicRock","Country","Dance","Disco","Funk","Grunge","HipHop","Jazz",
				"Metal","NewAge","Oldies","Other","Pop","R&B","Rap","Reggae","Rock","Techno",
				"Industrial","Alternative","Ska","DeathMetal","Pranks","Soundtrack","Euro-Techno",
				"Ambient","TripHop","Vocal","Jazz-Funk","Fusion","Trance","Classical","Instrumental",
				"Acid", "House", "Game", "SoundClip", "Gospel", "Noise", "Alt.Rock", "Bass", "Soul", "Punk",
				"Space", "Meditative", "InstrumentalPop", "InstrumentalRock", "Ethnic", "Gothic", "Darkwave",
				"Techno-Industrial", "Electronic", "Pop/Folk", "Eurodance", "Dream", "SouthernRock", "Comedy",
				"Cult", "GangstaRap", "Top40", "ChristianRap", "Pop/Funk", "Jungle", "NativeAmerican", "Cabaret",
				"NewWave", "Psychedelic", "Rave", "Showtunes", "Trailer", "Lo-fi", "Tribal", "AcidPunk",
				"AcidJazz", "Polka", "Retro", "Musical", "Rock'n'Roll", "HardRock", "Folk", "Folk/Rock",
				"NationalFolk","Swing", "FastFusion", "Bebob", "Latin", "Revival", "Celtic", "BlueGrass",
				"AvantGarde", "GothicRock", "ProgressiveRock", "PsychedelicRock", "SymphonicRock",
				"SlowRock", "BigBand", "Chorus", "EasyListening", "Acoustic", "Humour", "Speech", "Chanson",
				"Opera", "ChamberMusic", "Sonata", "Symphony", "BootyBass", "Primus", "PornGroove", "Satire",
				"SlowJam", "Club", "Tango", "Samba", "Folklore", "Ballad", "PowerBallad", "RhythmicSoul",
				"Freestyle", "Duet", "PunkRock", "DrumSolo", "Euro-House", "DanceHall", "Goa", "Drum&Bass",
				"Club-House", "Hardcore", "Terror", "Indie", "BritPop", "Negerpunk", "PolskPunk", "Beat",
				"ChristianGangstaRap", "HeavyMetal", "BlackMetal", "Crossover", "ContemporaryChristian",
				"ChristianRock", "Merengue", "Salsa", "ThrashMetal","Anime","JPop","SynthPop");
				
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();

		$TSFields = array("file","autostart","loop","bg","leftbg","rightbg","rightbghover","lefticon","righticon","righticonhover","text","slider","loader","track","border");
		$this->config = array_merge($this->getTSConfig($TSFields),$this->getFFConfig($this->cObj->data['pi_flexform']));
		
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<script type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/swfobject.js"></script>
<script type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/mp3player.js"></script>';
		
		// Creating valid pathes for the MP3 player
		$swfFile = t3lib_extMgm::siteRelPath($this->extKey).'res/player.swf';

		$options = 'playerID:"'.$this->cObj->data['uid'].'"';
		while (list($k,$v) = each($this->config)) {
			switch($k) {
				case 'file' :
					$this->cObj->data =array_merge($this->cObj->data,$this->getID3v11($this->uploadDir . $v));
					$this->cObj->data['file'] = $v;
					$this->cObj->data['filesize'] = filesize($this->uploadDir . $v);
					$options.=($v?',soundFile:"'.str_replace(PATH_site,t3lib_div::getIndpEnv('TYPO3_SITE_URL'),t3lib_div::getFileAbsFileName($this->uploadDir . $v)).'"':'');
				break;
				case 'autostart' :
				case 'loop' :
					$options.=($v!==''?','.$k.':"'.($v?'yes':'no').'"':'');
				break;
				case 'bg':
				case 'leftbg':
				case 'rightbg':
				case 'rightbghover':
				case 'lefticon':
				case 'righticon':
				case 'righticonhover':
				case 'text':
				case 'slider':
				case 'loader':
				case 'track':
				case 'border':
					$options.=($v?','.$k.':"'.str_replace(array('aqua','black','blue','fuchsia','gray','green','lime','maroon','navy','olive','purple','red','silver','teal','yellow','white','#'),array('0x00FFFF','0x000000','0x0000FF','0xFF00FF','0x808080','0x008000','0x00FF00','0x800000','0x000080','0x808000','0x800080','0xFF0000','0xC0C0C0','0x008080','0xFFFF00','0xFFFFFF','0x'),$v).'"':'');
				break;
				default :
				//$options.=($v?','.$k.':"'.$v.'"':'');
			
			}
		}
		$content = $this->cObj->cObjGetSingle($conf['beforeObj'], $conf['beforeObj.']);
		$content.= '<p id="'.$this->extKey.$this->cObj->data['uid'].'"><a href="http://www.macromedia.com/go/getflashplayer">'.$this->pi_getLL('getFlash1').'</a> '.$this->pi_getLL('getFlash2').'</p>
<script type="text/javascript">
  swfobject.embedSWF("'.$swfFile.'","'.$this->extKey.$this->cObj->data['uid'].'", "290", "24", "7.0.0", "expressInstall.swf",{'.$options.'},{wmode:"transparent",bgcolor:"#FFFFFF"},{id:"audioplayer'.$this->cObj->data['uid'].'"});
</script>';
		$content.= $this->cObj->cObjGetSingle($conf['afterObj'], $conf['afterObj.']);
		return $this->pi_wrapInBaseClass($content);
	}
	
	/**
	 * Get config data from Flexform
	 *
	 * @param	string		$ffObj: Flexform object
	 * @return	array		config array
	 */
	function getFFConfig($ffObj) {
		$config = array();
		foreach($ffObj['data'] as $sheet => $arr) {
			foreach($arr['lDEF'] as $field => $v) {
				$val = $this->pi_getFFvalue($ffObj,$field,$sheet);
				if ($val!=='') $config[$field] = $val;
			}
		}
		return $config;
	}

	function getTSConfig($confFields) {
		while (list(,$field) = each($confFields)) {
			$val = $this->cObj->stdWrap($this->conf[$field], $this->conf[$field.'.']);
			if ($val!=='') $config[$field] = $val;
		}
		return $config;
	}

	function getID3v11($file) {
		if (!file_exists($file)) return array();
		if (!$file=fopen($file, "rb")) return array();
		fseek($file, -128, SEEK_END);
		$tagheader = fgets($file, 129);
		if (substr($tagheader,0,3)=='TAG') {
			$ID3v11['title'] = trim(substr($tagheader,3,30));
			$ID3v11['artist'] = trim(substr($tagheader,33,30));
			$ID3v11['album'] = trim(substr($tagheader,63,30));
			$ID3v11['year'] = substr($tagheader,93,4);
			$ID3v11['comment'] = trim(substr($tagheader,97,28));
			$ID3v11['track'] = hexdec(bin2hex(substr($tagheader,126,1)));
			$ID3v11['genrenum'] =hexdec(bin2hex(substr($tagheader,127,1)));
			$ID3v11['genre'] = @$this->genretypes[$ID3v11['genrenum']];
			return $ID3v11;
		}
		else return array();
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkmp3player/pi1/class.tx_pmkmp3player_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkmp3player/pi1/class.tx_pmkmp3player_pi1.php']);
}

?>