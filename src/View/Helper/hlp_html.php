<?php
/**
 * $Id: hlp_html.php,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */   
/**
 * help class for html
 * @copyright   Copyright 2007, Fonsen Technology Ltd. Corp.
 */
define('NL', "\n"); 
class HlpHtmlHelper extends Helper {

	var $helpers = array('Html', 'Paginator', 'othAuth', 'Session');

	/**
	 * generate rounded corner tab div
	 * @param string $text text to display in tab
	 * @return array year options
	 */     
	function roundTabDiv($text) {
		$width = strlen($text) * 4 + 50;
		$str = '<div class="heading" align="center" style="width: ' . $width . 'px">'.NL;
		$str .= '<div style="overflow: hidden; width: ' . ($width-10) . 'px; height: 1px; background-color: #666666"></div>'.NL;
		$str .= '<div style="overflow: hidden; width: ' . ($width-6) . 'px; height: 1px; background-color: #666666"></div>'.NL;
		$str .= '<div style="overflow: hidden; width: ' . ($width-2) . 'px; height: 2px; background-color: #666666"></div>'.NL;
		$str .= '<div style="overflow: hidden; width: ' . $width . 'px; background-color: #666666">' . $text . '</div>'.NL;
		$str .= '<div style="overflow: hidden; width: ' . $width . 'px; height: 2px; background-color: #666666"></div>'.NL;
		$str .= '</div>';

		return $str;
	}

	/**
	 * generated clear flash java script
	 * which can clear certain div named flashMessage
	 * @return string output
	 */           
	function clearFlash() {
		$str = '<script language="javascript">'.NL;
		$str .= 'if ($("flashMessage")) {'.NL;
		$str .= 'Element.hide("flashMessage");'.NL;
		$str .= 'Element.update("flashMessage", "");'.NL;
		$str .= '}'.NL;
		$str .= '</script>'.NL;
		return $str;
	}

	/**
	 * generate menu items html
	 * @param string $secId section id
	 * @param string $secName section name
	 * @param array $items item data
	 * @param array $linkOpt url options
	 * @return string
	 * @deprecated   
	 */                    
	function menuItemRemoveSoon($secId, $secName, $items, $linkOpt=array()) {
		$options = am(
			$linkOpt,
			array(
				'onClick' => 'return clickreturnvalue();',
				'onMouseOver' => "dropdownmenu(this, event, '$secId');"
			)
		);
		$str = $this->Html->link($secName, '#', $options) . NL;
		$str .= '<div id="' . $secId . '" class="anylinkcss">' . NL;

		foreach ($items as $item) {
			/**
			 * $action here has "controller/action" form
			 */             
			$str .= $this->Html->link($item['desc'], EMMA_ROOT . $item['act']) . NL;
		}
		$str .= '</div>' . NL;

		return $str;
	}

	/**
	 * generate menu items html
	 * @param string $secId section id
	 * @param string $secName section name
	 * @param array $items item data
	 * @param array $linkOpt url options
	 * @return string
	 */                    
	/*function menuLink($secId, $secName, $linkOpt=array()) {
		if($secId === false) {
			//$options = am($linkOpt, array('onclick' => "alert('您沒有權限進入此選單'); return false"));
			//$url = "#";
			return false;
		} else {
			$options = $linkOpt;
			$url = EMMA_ROOT.'mods/display/'.$secId;
			$str = "<div id=\"menu_$secId\" class=\"topmenu\" onClick=\"Cookie.set('setTopMenu', 'menu_$secId'); Cookie.set('menuBlock', '');\";>";
			$str .= $this->Html->link($secName, $url, $options);
			$str .= "</div>";  	
			return $str;
		}
	}*/
	
	function menuLink($secId, $secName, $link, $linkOpt=array()) {
		if($link === false) {
			return false;
		} else {
			$options = $linkOpt;
			$url = EMMA_ROOT.$link;
			$str = "<div id=\"menu_$secId\" class=\"topmenu\" onClick=\"Cookie.set('setTopMenu', 'menu_$secId'); Cookie.set('menuBlock', '');\";>";
			$str .= $this->Html->link($secName, $url, $options);
			$str .= "</div>";  	
			return $str;
		}
	}


	/**
	 * generate menu items html
	 * @param string $secId section id
	 * @param string $secName section name
	 * @param array $items item data
	 * @param array $linkOpt url options
	 * @deprecated   
	 * @return string
	 */                    
	function menuItemOld($secId, $secName, $items, $linkOpt=array()) {
		$options = am($linkOpt, array('class' => 'menuhead'));
		$str = "<div id=\"menu_$secId\" class=\"topmenu\" onMouseOver=\"MenuOn('$secId')\" onMouseOut=\"MenuOff('$secId')\">" . NL;
		$str .= $this->Html->link($secName, '#', $options) . NL;
		if (count($items) > 0) {
			$str .= "<div class=\"submenu\" id=\"submenu_$secId\">" . NL;
			$i = 0;
			foreach ($items as $item) {
				if (EMMA_OP_MODE != 'single') {
					$hasPerm = $this->othAuth->hasPermission(Inflector::underscore($item['act']));
					if (!$hasPerm) {
						continue;
					}
				}

				$i++;
				$str .= $this->Html->link($item['desc'], EMMA_ROOT . $item['act']) . NL;
				if ($i < count($items)) {
					$str .= '<span>｜</span>';
				}
			}
			$str .= "<!--[if lte IE 6.5]><iframe></iframe><![endif]--></div>" . NL;
		}
		return $str;
	}

	/**
	 * substr with encoding
	 * default 20 char
	 * @deprecated Use briefText() instead
	 * @param string $str
	 * @param bool $addDot if true, add dot after substr-ed string
	 * @return string
	 */                 
	function substr($str, $addDot = true) {
		$len = 20;
		$enc = 'utf8';
		$out = mb_substr($str, 0, $len, $enc);
		if ($addDot === true and !empty($out)) {
			$out .= '..';
		}
		return $out;
	}

	/**
	 * get weekday name
	 * @param int $weekday
	 * @return string
	 */         
	function wdname($weekday) {
		$weekday_names = array(
			'日', '一', '二', '三', '四', '五', '六',
		);
		if (($weekday >= 0) and ($weekday <=6)) {
			return $weekday_names[$weekday];
		}
		return false;
	}

	/**
	 * paginator extension
	 * generate html link of specified page
	 * @param string $title
	 * @param array $options
	 * @param int $page
	 * @return string      
	 */
	function page($title = 'Page', $options = array(), $page) {
		$model = $this->Paginator->defaultModel();
		if ($this->params['paging'][$model]['page'] == $page) {
			/**
			 * already at that page
			 */
			return $title;
		}
		$options['url'] = am($options['url'], array('page' => $page));
		return $this->Paginator->link($title, $options['url'], $options);
	}

	function PageStyle() {
		$pagestyle = array('width'=>'16', 'height'=>'16', 'hspace'=>'2', 'border'=>'0', 'align'=>'absmiddle');
		return $pagestyle;
	}
	/**
	 * paginator extension
	 * generate html link of last page
	 * @param string $title
	 * @param array $options
	 * @return string      
	 */
  /*function lastPage($title = 'Last', $options = array()) {
    $model = $this->Paginator->defaultModel();
    return $this->page($title, $options, $this->params['paging'][$model]['pageCount']);
  }*/

	function lastPageUrl($options = array()) {
		$pagestyle = $this->PageStyle();
		$pagestyle['title'] = '最後一頁';
		$pagestyle['alt'] = '最後一頁';
		$model = $this->Paginator->defaultModel();
        return $this->page($this->Html->tag('i', '', array('class' => 'px-3 fas fa-step-forward bl-1')), $options, $this->params['paging'][$model]['pageCount']);
	}
	/**
	 * paginator extension
	 * generate html link of first page
	 * @param string $title
	 * @param array $options
	 * @return string      
	 */
  /*function firstPage($title = 'First', $options = array()) {
    return $this->page($title, $options, 1);
  }*/

	function firstPageUrl($options = array()) {
		$pagestyle = $this->PageStyle();
		$pagestyle['title'] = '第一頁';
		$pagestyle['alt'] = '第一頁';
        return $this->page($this->Html->tag('i', '', array('class' => 'px-3 fas fa-step-backward br-1 col-auto')), $options, 1);
	}
	/**
	 * paginator extension
	 * generate html link of previous page
	 * @param array $options
	 * @return string      
	 */
	function prevPageUrl($options = array()) {
		$pagestyle = $this->PageStyle();
		$pagestyle['title'] = '上一頁';
		$pagestyle['alt'] = '上一頁';
		return $this->Paginator->prev($this->Html->tag('i', '', array('class' => 'px-3 fa  fa-caret-left fa-lg br-1 col-auto')), $options, $this->Html->tag('i', '', array('class' => 'px-3 fa  fa-caret-left fa-lg br-1 col-auto')), array('class'=>'pageinline', 'escape' => false));
	}
	/**
	 * paginator extension
	 * generate html link of next page
	 * @param array $options
	 * @return string      
	 */
	function nextPageUrl($options = array()) {
		$pagestyle = $this->PageStyle();
		$pagestyle['title'] = '下一頁';
		$pagestyle['alt'] = '下一頁';
        return $this->Paginator->next($this->Html->tag('i', '', array('class' => 'px-3 fa  fa-caret-right fa-lg bl-1 col-auto')), $options, $this->Html->tag('i', '', array('class' => 'px-3 fa fa-caret-right fa-lg bl-1  col-auto')), array('class'=>'pageinline', 'escape' => false));
	}
	/**
	 * paginator extension
	 * generate html link of help
	 * @param string $link
	 * @return string
	 */
	function helpUrl($link = null) {
		App::import('Model', 'Setting');
		$settingModel = new Setting;
		if ($settingModel->getSys('OnlineHelp') == 1) {
			$helpstyle = array('width'=>'20', 'height'=>'20', 'hspace'=>'0', 'border'=>'0', 'align'=>'absmiddle', 'title'=>'協助', 'alt'=>'協助');
			if ($link == null) {
				return $this->Html->image('help.gif', $helpstyle);
			}else{ 
				return $this->Html->link($this->Html->image('help.gif', $helpstyle), $link, array('target'=>'blank'), false, false);
			}
		}
	}

	function sidemenuItem($items, $menuBlockKey = null, $menuItemKey = null) {
	//function sidemenuItem($items) {
		if (count($items) > 0) {
			$tmpItems = $items;
			$items = array();
			$itemsCount = 0;
			foreach($tmpItems as $tmpItem) {
				$items[$itemsCount] = $tmpItem;
				$itemsCount++;
			}
			$str = "<ul>";
			$initK = $items[0]['id'];
			foreach ($items as $catagory) {
				$K = $catagory['id'];
				$str .= "<span id='category_$K' tag='category' onClick='sideMenuCategory($K)'><img src='/emma/img/menuClose.gif' border=0>" . " " . $catagory['name'] . "</span>";
				$str .= "<div id='menuBlock_$K' tag='menuBlock' style='display:none'>";
				foreach($catagory['MenuItem'] as $m => $item) {
					$n = $K . '_' . $m;
					$str .= "<li id='menuItem_$n' onClick='sideMenuItem($K, $m)'>" . $this->Html->link($item['name'], EMMA_ROOT . $item['action']) . "</li>";
				}
				$str .= "</div>";
			}
			$str .= "</ul>";
			//$str .= "<script>onloadSideMenu($initK)</script>";
			$str .= "<script>onloadSideMenu($initK, $menuBlockKey, $menuItemKey)</script>";
		}
		return $str;
	}

	/**
	 * display brief text for better user display
	 * ex:
	 * 	if text is longer than 10, we cut it into 10 and added some text
	 * 	also we can add title to it that display full text on mouse over
	 *
	 * @param string $text
	 * @param array $params
	 * @return string
	 */
	function briefText($text, $params = array()) {
		$params = array_merge(array('title' => false, 'after' => '', 'max' => 10, 'cut' => 10, 'enc' => Configure::read('App.encoding')), (array)$params);
		extract($params);
		if (strlen($text) == 0 or mb_strlen($text, $enc) <= $max) {
			return $text;
		}
		if ($title === true) {
			$title = $text;
		}
		if ($title !== false) {
			return '<span title="' . htmlspecialchars($title) . '">' . mb_substr($text, 0, $cut, $enc) . $after . '</span>';
		}
		return mb_substr($text, 0, $cut, $enc);
	}

	/**
	 * link html return to main div
	 * @param array $params
	 */
	function linkDivReturn($params = array()) {
		$params = array_merge(
			array(
				'maindiv' => 'listing',
				'currdiv' => 'add',
				'text' => 'Return',
				'url' => 'javascript:void(0)',
			),
			$params
		);
		extract($params);
		return $this->Html->link($text, $url, array('onclick' => "Element.hide('$currdiv'); Element.show('$maindiv'); return false;"));
	}

	/**
	 * transfer west year to roc year
	 * @param int $year West year, ex: 2009, if null, use this year
	 * @param string $fmt Format for sprinft output, default: %03d
	 * @return string
	 */
	function rocYear($year = null, $fmt = '%03d') {
		if (is_null($year)) {
			$year = date('Y');
		}
		return sprintf($fmt, $year - 1911);
	}

	/**
	 * return an array options for FormHelper::input()
	 * like php's range(), but created key, value pair
	 * ex: rangeOptions(1, 3) returns array(1 => 1, 2 => 2, 3 => 3)
	 * @param int $start
	 * @param int $end
	 * @param int $step Default 1
	 * @return array
	 */
	function rangeOptions($start, $end, $step = 1) {
		$results = array();
		for ($i = $start; $i <= $end; $i += $step) {
			$results[$i] = $i;
		}
		return $results;
	}

	/**
	 * print seal area text
	 * @param array $data Text to print, in array
	 * @param int $width Width percentage of table inside the seal area
	 * @return string
	 */
	function sealText($data = null, $width = '80', $paddingTop = '0') {
		if (empty($data)) {
			$data = array(
				__('經辦', true),
				__('覆核', true),
				__('主管', true),
			);
		} elseif (!is_array($data)) {
			return $data;
		}
		$tdWidth = $width / (count($data) * 2);
		$out = '<table width="'.$width.'%" align="center" style="padding-top:'.$paddingTop.'pt;"><tr>';
		foreach ($data as $word) {
			$out .= "<td width=\"$tdWidth%\" align=\"center\">$word</td><td width=\"$tdWidth%\">&nbsp;</td>";
		}
		$out .= '<tr></table>';
		return $out;
	}

	/**
	 * print btn start
	 */
	function btn1() {
		return '<div class="divBtn" onmouseover="this.className=\'divBtn_hover\'" onmouseout="this.className=\'divBtn\'">';
	}

	/**
	 * print btn end
	 */
	function btn0() {
		return '</div>';
	}

	/**
	 * print btn, surround given output string
	 */
	function btn($string) {
		return $this->btn1() . $string . $this->btn0();
	}

	/**
	 * just retun value for checkbox of new core
	 * @note This is temp solution
	 * @todo FIXME.Remove this in future
	 */
	function cbxValue($value) {
		return ife($value, $value, 0);
	}
	
  /**
	* print menu button head 
	*/	
	function menuBtnHead($linkType, $setId , $string) {
		$baseBtnHead = '<div class="menu-btn-div" onmouseover="JavaScript:menuOver(\'baseMenuAnd'.$setId.'\')" onmouseout="JavaScript:menuOut(\'baseMenuAnd'.$setId.'\')" onclick="blockPopMenuEvent(event); showSelectMenu(\'document'.$linkType.$setId.'\', \''.$setId.'\', \''.$linkType.'\');"><span id="baseMenu'.$setId.'" symbol="baseBtnTouch" class="menu-btn-link" onclick="blockPopMenuEvent(event); showSelectMenu(\'document'.$linkType.$setId.'\', \''.$setId.'\', \''.$linkType.'\');"><a href=# symbol="baseBtnA" id="baseBtnA'.$setId.'" style="padding-right:0px">';
		$baseBtnEnd = '</a></span><span id="baseSelect'.$setId.'" symbol="baseBtnTouch" class="menu-btn-touch" onclick="blockPopMenuEvent(event); showSelectMenu(\'document'.$linkType.$setId.'\', \''.$setId.'\', \''.$linkType.'\');"><img onclick="blockPopMenuEvent(event); showSelectMenu(\'document'.$linkType.$setId.'\', \''.$setId.'\', \''.$linkType.'\');" symbol="baseImg" id="touchImg1'.$setId.'" src="/img/divPopArrow1.gif" border="0"><img onclick="blockPopMenuEvent(event); showSelectMenu(\'document'.$linkType.$setId.'\', \''.$setId.'\', \''.$linkType.'\');" symbol="baseImg" id="touchImg2'.$setId.'" src="/img/divPopArrow2.gif" border="0" style="display:none"></span>';
		
		$linkBtnHead = '<div class="menu-btn-div"><span class="menu-btn-link" onmouseover="this.className=\'menu-btn-link_over\';" onmouseout="this.className=\'menu-btn-link\';">';
		$linkBtnEnd = '</span><span id="linkSelect'.$setId.'" symbol="linkBtnTouch" class="menu-btn-select" onmouseover="JavaScript:menuOver(\'linkSelectAnd'.$setId.'\')" onmouseout="JavaScript:menuOut(\'linkSelectAnd'.$setId.'\')" onclick="blockPopMenuEvent(event); showSelectMenu(\'document'.$linkType.$setId.'\', \''.$setId.'\', \''.$linkType.'\');"><img src="/img/divPopArrow1.gif" border="0" symbol="linkImg" id="selectImg1'.$setId.'"><img src="/img/divPopArrow2.gif" border="0" symbol="linkImg" id="selectImg2'.$setId.'" style="display:none"></span>';
		
		$divHead = '<div id="document'.$linkType.$setId.'" class="divBtnPop" symbol="pop_menu" style="display:none; position:absolute;"><ul>';
		
		if($linkType == 'base'){return $baseBtnHead.$string.$baseBtnEnd.$divHead;}
		if($linkType == 'link'){return $linkBtnHead.$string.$linkBtnEnd.$divHead;}
	}
	
  /**
	* print select list of menu inside menu button
	*/	
	function menuBtnText($string) {
		return '<li>'.$string.'</li>';
	}
	
  /**
	* print menu button end
	*/	
	function menuBtnEnd() {
		return '</ul></div></div>';
	}
	
	function excelIntToStr($number) {
		$words = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if($number > 26) {
			$word1 = $words[floor($number/26)-1];
			$word2 = $words[fmod($number, 26)-1];
			$excelWord = $word1 . $word2;
		} else {
			$excelWord = $words[$number-1];
		}
		return $excelWord;
	}
	
	
  function sefDate($date = null, $day_diff = null, $set = 10) {
    if(substr($date, 0, 10) == date('Y-m-d')){
      return __('今天', true);
    }else if($day_diff <= $set && $day_diff > 0){
      return abs($day_diff).__('天後', true);
    }else if(abs($day_diff) <= $set && $day_diff < 0){
      return abs($day_diff).__('天前', true);
    }else{
      return substr($date, 0, 10);
    }
	}
	
	function numberFormat($value, $decimal = null){
		$value = preg_replace('/(\.[0-9]+?)0*$/', '$1', $value);
		if($decimal === null){
			$pointArr = explode('.', $value);
			$decimal = ife(empty($pointArr[1]), 0, @strlen($pointArr[1]));	
		}
		return number_format($value, $decimal);
	}

	function multiCheck($model, $fieldName, $opts, $params){
		$cols = !empty($params['cols'])?$params['cols']:4;
		echo '<table><tr>';
		$i = 0;
		foreach ($opts as $key => $value) {
			echo '<td style="padding: 1px 3px">';
			echo '<input type="checkbox" name="data['.$model.']['.$fieldName.'][]" value="'.$key.'" id="mck_'.$fieldName.'_'.$key.'"'.(in_array($key, (array)$this->data[$model][$fieldName])?' checked="checked"': '').'>';
			echo '<label for="mck_'.$fieldName.'_'.$key.'">'.$value.'</label>';
			echo '</td>';
			$i++;
			if($i%$cols == 0){
				echo '</tr><tr>';
			}
		}
		echo '</tr></table>';
	}

}
?>
