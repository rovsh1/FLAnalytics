function Timeout(callback, timeout) {
	var timer = null;
	
	this.start = function() {
		this.stop();
		timer = setTimeout(callback, timeout);
		return this;
	};
	this.stop = function() {
		if (!timer)
			return this;
		clearTimeout(timer);
		timer = null;
		return this;
	};
	this.isStarted = function() { return timer !== null; };
};