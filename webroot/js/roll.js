

/***********************************************************************
	SPRY FUNCTIONS
************************************************************************/

if (!document.Spry) document.Spry = {};
if (!document.Spry.Widget) document.Spry.Widget = {};

document.Spry.Widget.picPanels = function(element, opts)
{
	this.element = this.getElement(element);
	this.enableAnimation = true;
	this.currentPanel = null;
	this.enableKeyboardNavigation = true;
	this.hasFocus = false;
	this.previousPanelKeyCode = document.Spry.Widget.picPanels.KEY_LEFT;
	this.nextPanelKeyCode = document.Spry.Widget.picPanels.KEY_RIGHT;

	this.currentPanelClass = "picPanelsCurrentPanel";
	this.focusedClass = "picPanelsFocused";
	this.animatingClass = "picPanelsAnimating";

	document.Spry.Widget.picPanels.setOptions(this, opts);

	if (this.element)
		this.element.style.overflow = "hidden";

	// Developers can specify the default panel as an index,
	// id or an actual element node. Make sure to normalize
	// it into an element node because that is what we expect
	// internally.

	if (this.defaultPanel)
	{
		if (typeof this.defaultPanel == "number")
			this.currentPanel = this.getContentPanels()[this.defaultPanel];
		else
			this.currentPanel = this.getElement(this.defaultPanel);
	}

	// If we still don't have a current panel, use the first one!

	if (!this.currentPanel)
		this.currentPanel = this.getContentPanels()[0];

	// Since we rely on the positioning information of the
	// panels, we need to wait for the onload event to fire before
	// we can attempt to show the initial panel. Once the onload
	// fires, we know that all CSS files have loaded. This is
	// especially important for Safari.

	if (document.Spry.Widget.picPanels.onloadDidFire)
		this.attachBehaviors();
	else
		document.Spry.Widget.picPanels.loadQueue.push(this);
};

document.Spry.Widget.picPanels.prototype.onFocus = function(e)
{
	this.hasFocus = true;
	this.addClassName(this.element, this.focusedClass);
	return false;
};

document.Spry.Widget.picPanels.prototype.onBlur = function(e)
{
	this.hasFocus = false;
	this.removeClassName(this.element, this.focusedClass);
	return false;
};

document.Spry.Widget.picPanels.KEY_LEFT = 37;
document.Spry.Widget.picPanels.KEY_UP = 38;
document.Spry.Widget.picPanels.KEY_RIGHT = 39;
document.Spry.Widget.picPanels.KEY_DOWN = 40;

document.Spry.Widget.picPanels.prototype.onKeyDown = function(e)
{
	var key = e.keyCode;
	if (!this.hasFocus || (key != this.previousPanelKeyCode && key != this.nextPanelKeyCode))
		return true;

	if (key == this.nextPanelKeyCode)
		this.showNextPanel();
	else /* if (key == this.previousPanelKeyCode) */
		this.showPreviousPanel();

	if (e.preventDefault) e.preventDefault();
	else e.returnValue = false;
	if (e.stopPropagation) e.stopPropagation();
	else e.cancelBubble = true;

	return false;
};

document.Spry.Widget.picPanels.prototype.attachBehaviors = function()
{
	var ele = this.element;
	if (!ele)
		return;

	if (this.enableKeyboardNavigation)
	{
		var focusEle = null;
		var tabIndexAttr = ele.attributes.getNamedItem("tabindex");
		if (tabIndexAttr || ele.nodeName.toLowerCase() == "a")
			focusEle = ele;
	
		if (focusEle)
		{
			var self = this;
			document.Spry.Widget.picPanels.addEventListener(focusEle, "focus", function(e) { return self.onFocus(e || window.event); }, false);
			document.Spry.Widget.picPanels.addEventListener(focusEle, "blur", function(e) { return self.onBlur(e || window.event); }, false);
			document.Spry.Widget.picPanels.addEventListener(focusEle, "keydown", function(e) { return self.onKeyDown(e || window.event); }, false);
		}
	}

	if (this.currentPanel)
	{
		// Temporarily turn off animation when showing the
		// initial panel.

		var ea = this.enableAnimation;
		this.enableAnimation = false;
		this.showPanel(this.currentPanel);
		this.enableAnimation = ea;
	}
};

document.Spry.Widget.picPanels.prototype.getElement = function(ele)
{
	if (ele && typeof ele == "string")
		return document.getElementById(ele);
	return ele;
};

document.Spry.Widget.picPanels.prototype.addClassName = function(ele, className)
{
	if (!ele || !className || (ele.className && ele.className.search(new RegExp("\\b" + className + "\\b")) != -1))
		return;
	ele.className += (ele.className ? " " : "") + className;
};

document.Spry.Widget.picPanels.prototype.removeClassName = function(ele, className)
{
	if (!ele || !className || (ele.className && ele.className.search(new RegExp("\\b" + className + "\\b")) == -1))
		return;
	ele.className = ele.className.replace(new RegExp("\\s*\\b" + className + "\\b", "g"), "");
};

document.Spry.Widget.picPanels.setOptions = function(obj, optionsObj, ignoreUndefinedProps)
{
	if (!optionsObj)
		return;
	for (var optionName in optionsObj)
	{
		if (ignoreUndefinedProps && optionsObj[optionName] == undefined)
			continue;
		obj[optionName] = optionsObj[optionName];
	}
};

document.Spry.Widget.picPanels.prototype.getElementChildren = function(element)
{
	var children = [];
	var child = element.firstChild;
	while (child)
	{
		if (child.nodeType == 1 /* Node.ELEMENT_NODE */)
			children.push(child);
		child = child.nextSibling;
	}
	return children;
};

document.Spry.Widget.picPanels.prototype.getCurrentPanel = function()
{
	return this.currentPanel;
};

document.Spry.Widget.picPanels.prototype.getContentGroup = function()
{
	return this.getElementChildren(this.element)[0];
};

document.Spry.Widget.picPanels.prototype.getContentPanels = function()
{
	return this.getElementChildren(this.getContentGroup());
};

document.Spry.Widget.picPanels.prototype.getContentPanelsCount = function()
{
	return this.getContentPanels().length;
};

document.Spry.Widget.picPanels.onloadDidFire = false;
document.Spry.Widget.picPanels.loadQueue = [];

document.Spry.Widget.picPanels.addLoadListener = function(handler)
{
	if (typeof window.addEventListener != 'undefined')
		window.addEventListener('load', handler, false);
	else if (typeof document.addEventListener != 'undefined')
		document.addEventListener('load', handler, false);
	else if (typeof window.attachEvent != 'undefined')
		window.attachEvent('onload', handler);
};

document.Spry.Widget.picPanels.processLoadQueue = function(handler)
{
	document.Spry.Widget.picPanels.onloadDidFire = true;
	var q = document.Spry.Widget.picPanels.loadQueue;
	var qlen = q.length;
	for (var i = 0; i < qlen; i++)
		q[i].attachBehaviors();
};

document.Spry.Widget.picPanels.addLoadListener(document.Spry.Widget.picPanels.processLoadQueue);

document.Spry.Widget.picPanels.addEventListener = function(element, eventType, handler, capture)
{
	try
	{
		if (element.addEventListener)
			element.addEventListener(eventType, handler, capture);
		else if (element.attachEvent)
			element.attachEvent("on" + eventType, handler);
	}
	catch (e) {}
};

document.Spry.Widget.picPanels.prototype.getContentPanelIndex = function(ele)
{
	if (ele)
	{
		ele = this.getElement(ele);
		var panels = this.getContentPanels();
		var numPanels = panels.length;
		for (var i = 0; i < numPanels; i++)
		{
			if (panels[i] == ele)
				return i;
		}
	}
	return -1;
};

document.Spry.Widget.picPanels.prototype.showPanel = function(elementOrIndex)
{
	var pIndex = -1;
	
	if (typeof elementOrIndex == "number")
		pIndex = elementOrIndex;
	else // Must be the element for the content panel.
		pIndex = this.getContentPanelIndex(elementOrIndex);

	var numPanels = this.getContentPanelsCount();
	if (numPanels > 0)
		pIndex = (pIndex >= numPanels) ? numPanels - 1 : pIndex;
	else
		pIndex = 0;

	var panel = this.getContentPanels()[pIndex];
	var contentGroup = this.getContentGroup();

	if (panel && contentGroup)
	{
		if (this.currentPanel)
			this.removeClassName(this.currentPanel, this.currentPanelClass);
		this.currentPanel = panel;

		var nx = -panel.offsetLeft;
		var ny = -panel.offsetTop;

		if (this.enableAnimation)
		{
			if (this.animator)
				this.animator.stop();
			var cx = contentGroup.offsetLeft;
			var cy = contentGroup.offsetTop;
			if (cx != nx || cy != ny)
			{
				var self = this;
				this.addClassName(this.element, this.animatingClass);
				this.animator = new document.Spry.Widget.picPanels.PanelAnimator(contentGroup, cx, cy, nx, ny, { duration: this.duration, fps: this.fps, transition: this.transition, finish: function()
				{
					self.removeClassName(self.element, self.animatingClass);
					self.addClassName(panel, self.currentPanelClass);
				} });
				this.animator.start();
			}
		}
		else
		{
			contentGroup.style.left = nx + "px";
			contentGroup.style.top = ny + "px";
			this.addClassName(panel, this.currentPanelClass);
		}
	}

	return panel;
};

document.Spry.Widget.picPanels.prototype.showFirstPanel = function()
{
	return this.showPanel(0);
};

document.Spry.Widget.picPanels.prototype.showLastPanel = function()
{
	return this.showPanel(this.getContentPanels().length - 1);
};

document.Spry.Widget.picPanels.prototype.showPreviousPanel = function()
{
	return this.showPanel(this.getContentPanelIndex(this.currentPanel) - 1);
};

document.Spry.Widget.picPanels.prototype.showNextPanel = function()
{
	return this.showPanel(this.getContentPanelIndex(this.currentPanel) + 1);
};

document.Spry.Widget.picPanels.PanelAnimator = function(ele, curX, curY, dstX, dstY, opts)
{
	this.element = ele;

	this.curX = curX;
	this.curY = curY;
	this.dstX = dstX;
	this.dstY = dstY;
	this.fps = 60;
	this.duration = 500;
	this.transition = document.Spry.Widget.picPanels.PanelAnimator.defaultTransition;
	this.startTime = 0;
	this.timerID = 0;
	this.finish = null;

	var self = this;
	this.intervalFunc = function() { self.step(); };
	
	document.Spry.Widget.picPanels.setOptions(this, opts, true);

	this.interval = 1000/this.fps;
};

document.Spry.Widget.picPanels.PanelAnimator.defaultTransition = function(time, begin, finish, duration) { time /= duration; return begin + ((2 - time) * time * finish); };

document.Spry.Widget.picPanels.PanelAnimator.prototype.start = function()
{
	this.stop();
	this.startTime = (new Date()).getTime();
	this.timerID = setTimeout(this.intervalFunc, this.interval);
};

document.Spry.Widget.picPanels.PanelAnimator.prototype.stop = function()
{
	if (this.timerID)
		clearTimeout(this.timerID);
	this.timerID = 0;
};

document.Spry.Widget.picPanels.PanelAnimator.prototype.step = function()
{
	var elapsedTime = (new Date()).getTime() - this.startTime;
	var done = elapsedTime >= this.duration;
	var x, y;

	if (done)
	{
		x = this.curX = this.dstX;
		y = this.curY = this.dstY;
	}
	else
	{
		x = this.transition(elapsedTime, this.curX, this.dstX - this.curX, this.duration);
		y = this.transition(elapsedTime, this.curY, this.dstY - this.curY, this.duration);
	}

	this.element.style.left = x + "px";
	this.element.style.top = y + "px";

	if (!done)
		this.timerID = setTimeout(this.intervalFunc, this.interval);
	else if (this.finish)
		this.finish();
};