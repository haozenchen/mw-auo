<?php
if (!isset($vboxWidth)) {
	$vboxWidth = 800;
}
if (!isset($vboxHeight)) {
	$vboxHeight = 600;
}
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/SVG/DTD/svg10.dtd">
<svg viewBox="0 0 <?php e($vboxWidth)?> <?php e($vboxHeight)?>" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<?php e($content_for_layout) ?>
</svg>
