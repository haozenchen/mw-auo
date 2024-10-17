<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $sysName ?></title>
<meta http-equiv="description" content="鋒形科技人力資源管理系統">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">

<?php echo $this->Html->meta('charset', 'UTF-8'); ?>
<?php echo $this->Html->css('jquery-ui-min/jquery-ui.min') ?>
<?php echo $this->Html->css('w2ui-1.5.rc1') ?>
<?php echo $this->Html->css('w3') ?>
<?php echo $this->Html->css('bootstrap') ?>

<?php echo $this->Html->css('toastr.min') ?>
<?php echo $this->Html->css('fontawesome-free-5.13.1-web/css/all.css?123'); ?>
<?php echo $this->Html->script('prototype') ?>
<?php echo $this->Html->script('scriptaculous') ?>
<?php echo $this->Html->script('cookie') ?>
<?php echo $this->Html->script('hiddendiv') ?>
<?php echo $this->Html->script('menu') ?>
<?php echo $this->Html->script('jquery-2.1.0') ?>
<?php echo $this->Html->script('jquery-ui-min/jquery-ui.min') ?>
<?php echo $this->Html->script('jquery.ui.touch-punch.min') ?>
<?php echo $this->Html->script('menu') ?>
<?php echo $this->Html->script('toastr.min') ?>
<?php echo $this->Html->script('w2ui-1.5.rc1') ?>
<script>
	<?php if(!empty($mfaAlert)){
		echo 'alert("'.$mfaAlert.'");';
	} ?>
	var $j = jQuery.noConflict();
	var helperJsonWorkArray = new Array();
	<?php
		if(!empty($jsCode)) {
			echo $jsCode;
		}
	?>
</script>
</head>
<body>
<style type="text/css">
#listing.content,#contentLayout>form{
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: auto;
}
#contentLayout>form,.tb{
    max-height: 100%;
    height: auto;
    overflow: auto;
}
.tb{
    margin-bottom: 10px;
}
.w2ui-tabs .w2ui-tab {
	padding: 6px 20px;
	text-align: center;
	color: #000000;
	background-color: transparent;
	border: 1px solid #c0c0c0;
	border-bottom: 1px solid #eef4f9;
	white-space: nowrap;
	margin: 1px 1px -1px 0px;
	border-top-left-radius: 0px;
	border-top-right-radius: 0px;
	cursor: default;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	-o-user-select: none;
	user-select: none;
}

.w2ui-tabs .w2ui-tab.active {
	color: #000000;
	background-color: #eef4f9;
	border: 1px solid #c0c0c0;
	border-bottom: 1px solid transparent;
	border-top: 3px solid #00BACC;

	font-weight: bold;
}

.w2ui-grid .w2ui-grid-toolbar {
	position: absolute;
	border-bottom: 1px solid #c0c0c0;
	background-color: #CAD7E0;
	height: 38px;
	padding: 6px 1px 4px 1px;
	margin: 0px;
	box-shadow: 0px 1px 2px #dddddd;
}

.w2ui-grid .w2ui-grid-body table .w2ui-head {
	margin: 0px;
	padding: 0px;
	border-right: 1px solid #c5c5c5;
	border-bottom: 1px solid #c5c5c5;
	color: #000000;
	background-image: -webkit-linear-gradient(#fff, #fff);
	background-image: -moz-linear-gradient(#fff, #fff);
	background-image: -ms-linear-gradient(#fff, #fff);
	background-image: -o-linear-gradient(#fff, #fff);
	background-image: linear-gradient(#fff, #fff);
	filter: progid:dximagetransform.microsoft.gradient(startColorstr='#fff', endColorstr='#fff', GradientType=0);
}

.w2ui-tabs table {
  border-bottom: 1px solid silver;
  padding: 0px 0px;
}

.w2ui-layout > div .w2ui-panel .w2ui-panel-toolbar {
	position: absolute;
	left: 0px;
	top: 0px;
	right: 0px;
	z-index: 2;
	display: none;
	overflow: hidden;
	background-color: #fafafa;
	border-bottom: 1px solid #000;
	padding: 4px;
}

.w2ui-grid .w2ui-grid-footer {
	position: absolute;
	margin: 0px;
	padding: 0px;
	text-align: center;
	height: 24px;
	overflow: hidden;
	user-select: text;
	-webkit-user-select: text;
	-moz-user-select: text;
	-ms-user-select: text;
	-o-user-select: text;
	box-shadow: 0px -1px 4px #eeeeee;
	color: #444444;
	background-color: #DAE3EB;
	border-top: 0px solid #dddddd;
	border-bottom-left-radius: 2px;
	border-bottom-right-radius: 2px;
}

.dot {
  height: 7px;
  width: 7px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
}

.w2ui-grid .w2ui-grid-body table td.w2ui-grid-data {
  margin: 0px;
  padding: 0px;
  font-family: "微軟正黑體", "Microsoft JhengHei", "Segoe UI Semibold", "Segoe UI", "Lucida Grande", Verdana, Arial, Helvetica, sans-serif;
  font-size: 14px
}

::-webkit-scrollbar {
	width: 8px; 
	height: 10px; 
}

::-webkit-scrollbar-thumb {
	border-radius: 10px; 
	background: lightgray; 
}

.test-1::-webkit-scrollbar-track {
	border-radius: 10px; 
	background: #EDEDED; 
}

#popup {
    z-index: 1000;
    margin: auto;
    padding: 20px;
    position: fixed;
    width: 20%;
    height: 20%;
    text-align: center;
    border-radius: 10px;
    top: 30%;
    bottom: 50%;
    left: 40%;
    right:40%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: white;
    box-shadow: 0 0 5px 5px slategray;
}

.popupContent {
    margin: auto;
    font-size: 1rem;
}
label{
	margin-bottom: 0px;
	margin-right: 10px
}

.rightBtn {
	position:absolute;
	top:7px;
	right:30px;
	}
</style>

<div id="popup" style="display:none;">
	<div class="popupContent">
		<p>操作閒置</p>
		<p id="session_life_time_show">??秒後自動登出</p>
	</div>
</div>

<div class="container-fluid" style="height:100%">
    <div class="row" style="height:100%">
        <div class="col-auto p-0 w3-sidebar w3-bar-block w3-animate-left" id="mySidebar" style="z-index:999">
            <div id="sidebar" style="width: 40px; height: 100%"></div>
        </div>
        <div class="col p-0" style="height:100%">
            <div>
				<div class="w3-container"></div>
            </div>
            <div id="contentLayout" class="container-fluid d-flex flex-column" style="height:100%">
                <?= $this->fetch('content') ?>
            </div>
        </div>
    </div>
</div>
<!-- Sidebar -->

<!-- Page Content -->

<div id="loading" style="display:none;">Loading ..&nbsp;</div>
<div id="transbtm" style="display: none;"></div>
<script>
	

function w3_open() {
    jQuery("#contentLayout").css({'padding-left':'230px','padding-right':'30px'});
	jQuery("#mySidebar").css({'width':'200px', 'display':'block'});
	if(w2ui['layout']){
		w2ui['layout'].resize();
	}

}

function w3_close() {
    jQuery("#contentLayout").css({'padding-left':'65px','padding-right':'30px'});
	jQuery("#mySidebar").css({'width':'40px', 'display':'block'});
	if(w2ui['layout']){
		w2ui['layout'].resize();
	}
}

<?php
	$menuNodes = array();
	$top_menuData[99] = array('name' => __('登出系統', true), 'icon' => 'fas fa-sign-out-alt', 'link' => 'SaasAdmins/logout');
	$currentKey = '';

	foreach ($top_menuData as $key => $value) {
		$menuNodes[$key]['id'] = (isset($value['id']))?$value['id']:'menu_'.$key;
		$menuNodes[$key]['text'] = $value['name'];
		$menuNodes[$key]['icon'] = $value['icon'];
		if(empty($mod_menu[$key])){
			$menuNodes[$key]['link'] = '/'.MWROOT.'/'.$value['link'];
		}else{
			$menuNodes[$key]['expanded'] = false;
		}
		if($this->request->getRequestTarget() == $value['link']){
			$currentKey = $key;
		}
		if(!empty($mod_menu[$key])){
			foreach($mod_menu[$key] as $k2 => $v2){
				$menuNodes[$key]['nodes'][$k2]['id'] = 'menu_'.$key.'_'.$k2;
				$menuNodes[$key]['nodes'][$k2]['text'] = $v2['name'];
				$menuNodes[$key]['nodes'][$k2]['expanded'] = true;
				//$menuNodes[$key]['nodes'][$k2]['group'] = true;
				if(!empty($mod_menu[$key][$k2]['MenuItem'])){
					$menuNodes[$key]['nodes'][$k2]['icon'] = 'fas fa-folder';
					$i = 0;
					foreach($mod_menu[$key][$k2]['MenuItem'] as $k3 => $v3){
						$menuNodes[$key]['nodes'][$k2]['nodes'][$i]['id'] = 'menu_'.$key.'_'.$k2.'_'.$k3;
						$menuNodes[$key]['nodes'][$k2]['nodes'][$i]['text'] = $v3['name'];
						$menuNodes[$key]['nodes'][$k2]['nodes'][$i]['icon'] = 'far fa-dot-circle';
						$i++;
					}
				}else{
					$menuNodes[$key]['nodes'][$k2]['icon'] = $v2['icon'];
					$menuNodes[$key]['nodes'][$k2]['link'] = '/emma/'.$v2['action'];
				}
				if($this->request->getRequestTarget() == $v2['action']){
					$currentKey = $key.'_'.$k2;
				}

			}
		}
	}
?>
	//set current menu
	var setMenu = getSetMenu();
	let currentKey = '<?php echo $currentKey?>';
	if((setMenu == '' || setMenu == 'menu_99') && currentKey != ''){
		setMenu = 'menu_'+currentKey;
	}
	jQuery(function () {
		var expandMenu = ['menu_1']
		jQuery('#sidebar').w2sidebar({
			name : 'sidebar',
			flatButton: true,
			topHTML: '<div style="height:30px">',
			nodes: <?php echo json_encode(array_values($menuNodes)) ?>,
			onFlat: function (event) {
				jQuery('#sidebar').css('width', (event.goFlat ? '40px' : '200px'));
				if(event.goFlat){
					w3_close();
					sessionStorage.removeItem('sideBar');
				}else{
					w3_open();
					sessionStorage.sideBar = 'open';
				}

			},
			onClick: function (event) {
				if(event.node.link){
					//Cookie.set('setTopMenu', event.target);
					document.cookie = 'setMenu='+event.target+'; path=/';
					window.location.href = event.node.link;
				}else if(expandMenu.include(event.target)){
					if(w2ui['sidebar'].flat){
						w2ui['sidebar'].goFlat();
						w3_open();
						w2ui.sidebar.expand(event.target);
					}
				}
			}
		});
		var sideBar = sessionStorage.sideBar;
		if(sideBar){
			$j('#sidebar').css('width','200px');
			$j('#sidebar > div').css('width','200px');
			w3_open();
		}else{
			w2ui['sidebar'].goFlat();
		}
		w2ui['sidebar'].select(setMenu);

		expandMenu.forEach(menu => {
			if(setMenu.match(menu+'_')){
				w2ui.sidebar.expand(menu);
				//w2ui['sidebar'].goFlat();
			}
		});
	});
	
	var notifyConfig = {
		body: '您現在可以新增任務並啟用提醒',
		icon: '/img/app_icon.png'
	};

	if (Notification.permission === 'default' || Notification.permission === 'undefined') {
			Notification.requestPermission(function (permission) {
				if (permission === 'granted') {
					var notification = new Notification('Hi 恭喜!', notifyConfig);
					notification.onclick = function() {
						return false;
					}
				}
		});
	}

	function getSetMenu(){
		let cookies = document.cookie;
		let regex = /setMenu=(.*)/;
		let match = regex.exec(cookies);
		let setMenu = '';
		if(match !== null){
			setMenu = match[1];
		}
		return setMenu;
	}

</script>

<script>
	var session_life_time = <?php echo $sessionMaxLifeTime; ?>;
	var protocol = location.protocol;
	session_life_time = Math.floor(new Date().getTime() / 1000) + parseInt(session_life_time);
	var urlCode = jQuery(location).attr('href').split('/')[3];
	$j(function () {
		document.cookie = 'lifeTimePoint' + urlCode + '=' + session_life_time + ((protocol === 'https:') ? ';secure' : '') + '; Path=/;';
		function session_timer() {
			jQuery.each(document.cookie.split(';'), function (key, val) {
				if (jQuery.trim(val.split('=')[0]) == ('lifeTimePoint' + urlCode)) {
					lifeTimePoint = val.split('=')[1];
				}
			});

			sur_life_time = lifeTimePoint - Math.floor(new Date().getTime() / 1000);
			if (sur_life_time < 120) {

				$j('#popup').show();
				$j('#session_life_time_show').fadeIn('50');
				$j('#session_life_time_show').html('<span style="color:#FF0000; padding:0 5px 0 0;">' + sur_life_time + '</span><?php echo __('秒後自動登出',true)?>！');
				if (sur_life_time < 1) {
					clearInterval(session_life_time_ssid);
					$j('#popup').hide();
					fsLink('/'+urlCode+'/SaasAdmins/logout/1');
				}
			} else {
				$j('#popup').hide();
			}
		}

		function start_session_time_count() {
			session_life_time_ssid = setInterval(session_timer, 1000);
		}
		start_session_time_count();

		jQuery(document).mousedown(function () {
			resetLifeTime();
		});
	});

	function resetLifeTime() {
		var session_life_time = <?php echo $sessionMaxLifeTime; ?>;
		session_life_time = Math.floor(new Date().getTime() / 1000) + parseInt(session_life_time);
		document.cookie = 'lifeTimePoint' + urlCode + '=' + session_life_time + ((protocol === 'https:') ? ';secure' : '') + '; Path=/;';
		$j('#popup').hide();
	}
</script>