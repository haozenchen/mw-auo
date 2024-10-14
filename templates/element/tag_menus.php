<script>
var tagMenus = new Array(<?php echo count($tagMenus)+1; ?>);
<?php foreach ($tagMenus as $b => $tagMenu) : ?>
	tagMenus[<?php echo $b; ?>] = new Array(<?php echo count($tagMenus[$b]); ?>);
	<?php foreach ($tagMenu as $s => $show) : ?>
		tagMenus[<?php echo $b; ?>]['<?php echo $s; ?>'] = '<?php echo $show; ?>';
	<?php endforeach; ?>
<?php endforeach; ?>

window.checkMouseOver = function (tabname, num, hiddenId) {
	var tmpTab = tabname+num;
	if(document.getElementById(hiddenId).value == num){
//		$(tmpTab).className = 'tag2';
	} else {
		$(tmpTab).className = 'tag2';
	}
}

window.checkMouseOut = function (tabname, num, hiddenId) {
	var tmpTab = tabname+num;
	if(document.getElementById(hiddenId).value == num){
		$(tmpTab).className = 'tag3';
	} else {
		$(tmpTab).className = 'tag1';
	}
}
window.changeTag = function (tabname, num, hiddenId) {
	//hidden old div
	var oldNum = document.getElementById(hiddenId).value;
	Element.hide(tagMenus[oldNum]['divname']);
	var oldTab = tabname+oldNum;
	$(oldTab).className = 'tag1';
	//show check div
	var tmpTab = tabname+num;
	Element.show(tagMenus[num]['divname']);
	$(tmpTab).className = 'tag3';
	//set select button
	document.getElementById(hiddenId).value = num;
}
<?php if(!empty($tagSet['tabName']) && !empty($tagSet['startTagCode']) && !empty($tagSet['selectTagId'])) :?>
window.changeTag('<?php echo $tagSet['tabName']; ?>', <?php echo $tagSet['startTagCode']; ?>, '<?php echo $tagSet['selectTagId']; ?>');
<?php endif; ?>
</script>