<?php
/**
 * $Id: hlp_svg.php,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */   
/**
 * helper class for SVG
 * @copyright   Copyright 2007, Fonsen Technology Ltd. Corp.
 */
class HlpSvgHelper extends Helper {

	var $topPos = array('x' => 50, 'y' => 80);	// initial position
	var $bw = 40;	// box width
	var $hbw = 20;	// half, set to avoid run-time calculation
	var $bh = 30;	// box height
	var $hbh = 15;	// half, set to avoid run-time calculation
	var $xGap = 100;
	var $yGap = 80;
	var $cLines = array();	// lines to connect children


	/**
	 * tree graph box helper
	 */
	function treeGraph($items, $options) {
		$left = array();
		$leftMax = 0;
		$hLines = array();
		extract($options);	// beware!
		//$this->topPos['x'] = $width * 45;
		foreach($items as $item) {
			if (!isset($left[$item['level']])) {
				/**
				 * initialize left position of each level
				 */
				$left[$item['level']] = false;
			}
			if ($item['level'] == 1) {
				// set top position, suppose 50,50
				$item['pos'] = $this->topPos;
				if ($left[$item['level']] === false) {
					$left[$item['level']] = $item['pos']['x'];
				} else {
					$item['pos']['x'] = $this->xGap + $leftMax;
					$left[$item['level']] = $item['pos']['x'];
				}
				$cLines[$item['id']] = array(
					'x1' => $item['pos']['x'] + $this->hbw,
					'y1' => $item['pos']['y'] + $this->bh, 
					'x2' => $item['pos']['x'] + $this->hbw,
					'y2' => $item['pos']['y'] + $this->hbh + ($this->yGap / 2), 
				);
			} elseif ($item['children'] > 0) {
				// has children
				if ($left[$item['level']] === false) {
					$item['pos']['x'] = $nodes[$item['parent_id']]['pos']['x'];
					$left[$item['level']] = $item['pos']['x'];
				} else {
					$item['pos']['x'] = $this->xGap + $leftMax;
					$left[$item['level']] = $item['pos']['x'];
				}
				$item['pos']['y'] = $item['level'] * $this->yGap;

				if (! isset($hLines[$item['parent_id']])) {
					$hLines[$item['parent_id']] = array('x1' => $item['pos']['x'] + $this->hbw, 'y1' => ($nodes[$item['parent_id']]['pos']['y']+$item['pos']['y'])/2 + $this->hbh);
				} else {
					$hLines[$item['parent_id']] = am($hLines[$item['parent_id']], array('x2' => $item['pos']['x'] + $this->hbw, 'y2' => ($nodes[$item['parent_id']]['pos']['y']+$item['pos']['y'])/2 + $this->hbh));
				}
				/**
				 * create line
				 */
				$lines[$item['id']] = array(
					'x1' => $item['pos']['x'] + $this->hbw,
					'y1' => ($nodes[$item['parent_id']]['pos']['y']+$item['pos']['y'])/2 + $this->hbh,
					'x2' => $item['pos']['x'] + $this->hbw,
					'y2' => $item['pos']['y']
				);
				/**
				 * connect to children
				 */
				$cLines[$item['id']] = array(
					'x1' => $item['pos']['x'] + $this->hbw,
					'y1' => $item['pos']['y'] + $this->bh, 
					'x2' => $item['pos']['x'] + $this->hbw,
					'y2' => $item['pos']['y'] + $this->hbh + ($this->yGap / 2), 
				);
			} else {
				// child
				if ($left[$item['level']] === false) {
					$item['pos']['x'] = $nodes[$item['parent_id']]['pos']['x'];
					$left[$item['level']] = $item['pos']['x'];
				} else {
					$item['pos']['x'] = $this->xGap + $left[$item['level']];
					if ($nodes[$item['parent_id']]['pos']['x'] > $item['pos']['x']) {
						$item['pos']['x'] = $nodes[$item['parent_id']]['pos']['x'];
					}
					$left[$item['level']] = $item['pos']['x'];
				}
				$item['pos']['y'] = $item['level'] * $this->yGap;

				if (! isset($hLines[$item['parent_id']])) {
					$hLines[$item['parent_id']] = array('x1' => $item['pos']['x'] + $this->hbw, 'y1' => ($nodes[$item['parent_id']]['pos']['y']+$item['pos']['y'])/2 + $this->hbh);
				} else {
					$hLines[$item['parent_id']] = am($hLines[$item['parent_id']], array('x2' => $item['pos']['x'] + $this->hbw, 'y2' => ($nodes[$item['parent_id']]['pos']['y']+$item['pos']['y'])/2 + $this->hbh));
				}
				/**
				 * create line
				 */
				$lines[$item['id']] = array(
					'x1' => $item['pos']['x'] + $this->hbw,
					'y1' => ($nodes[$item['parent_id']]['pos']['y']+$item['pos']['y'])/2 + $this->hbh,
					'x2' => $item['pos']['x'] + $this->hbw,
					'y2' => $item['pos']['y'] + 0
				);
			}
			$nodes[$item['id']] = $item;
			$texts[$item['id']] = $item['name'];
			if ($leftMax < $left[$item['level']]) {
				$leftMax = $left[$item['level']];
			}
		}

		foreach ($nodes as $id => $node) {
			e('<rect fill="none" stroke="blue" x="'.$node['pos']['x'].'" y="'.$node['pos']['y'].'" width="'.($this->bw).'" height="'.($this->bh).'"/>');
			e("\n");
			if (isset($lines[$id])) {
				e('<line stroke="green" stroke-width="2" x1="'.$lines[$id]['x1'].'" y1="'.$lines[$id]['y1'].'" x2="'.$lines[$id]['x2'].'" y2="'.$lines[$id]['y2'].'"/>');
			}
			e("\n");
			if (isset($texts[$id])) {
				e('<text x="'.$node['pos']['x'].'" y="'.($node['pos']['y']+$this->bh*0.75).'" style="font-size:10px;">'.$texts[$id].'</text>');
			}
			e("\n");
		}
		foreach ($cLines as $line) {
			e('<line stroke="green" stroke-width="2" x1="'.$line['x1'].'" y1="'.$line['y1'].'" x2="'.$line['x2'].'" y2="'.$line['y2'].'" />');
			e("\n");
		}
		foreach ($hLines as $line) {
			if (count($line) != 4) {
				continue;	// single element, no horizontal line
			}
			e('<line stroke="green" stroke-width="2" x1="'.$line['x1'].'" y1="'.$line['y1'].'" x2="'.$line['x2'].'" y2="'.$line['y2'].'" />');
			e("\n");
		}
	}
}
?>
