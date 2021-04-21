(function(){
	var makeid = function(length){
		let result           = '';
		let characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		let charactersLength = characters.length;
	   	for ( var i = 0; i < length; i++ ) {
	      result += characters.charAt(Math.floor(Math.random() * charactersLength));
	   	}
	   	return result;
	}

	if( window.rcs_css ){
		window.rcs_css.forEach(function(el){
			var link = document.createElement('link');
				link.setAttribute('type','text/css');
				link.setAttribute('rel','stylesheet');
				link.setAttribute('href',el+'?v='+makeid);
				document.querySelector('head').appendChild(link);
		});
	}
})();