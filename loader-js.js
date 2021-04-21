(function() {
  	function getScript(url,success){
	    var script=document.createElement('script');
	    script.src=url;
	    var head=document.getElementsByTagName('body')[0],
	        done=false;
	    script.onload=script.onreadystatechange = function(){
	      if ( !done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete') ) {
	        done=true;
	        if( success ){
	          success();
	        };
	        script.onload = script.onreadystatechange = null;
	        head.removeChild(script);
	      }
	    };
	    head.appendChild(script);
  	}

  	var makeid = function(length){
		let result           = '';
		let characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		let charactersLength = characters.length;
	   for ( var i = 0; i < length; i++ ) {
	      result += characters.charAt(Math.floor(Math.random() * charactersLength));
	   }
	   return result;
	}

	var version = makeid(5);

  	if( window.rcs_js ){
		window.rcs_js.forEach(function(el){
			if( typeof(el) == 'object' ){
				getScript(el.parent+'?v='+version,function(){
					if( typeof(el.src) == 'object' ){
						el.src.forEach(function(el2){
							if( typeof(el2) == 'object' ){
								getScript(el2.parent+'?v='+version,function(){
									if( typeof(el2.src) == 'object' ){
										getScript(el2.parent+'?v='+version,function(){
											el2.src.forEach(function(el3){
												if( typeof( el3) == 'object' ){
													getScript(el3.parent+'?v='+version,function(){
														el3.src.forEach(function(el4){
															getScript(el4+'?v='+version);
														})
													});
												}else{
													getScript(el3.parent+'?v='+version);
												}
											});
										})
									}else{
										getScript(el2.parent+'?v='+version);
									}
								})
							}else{
								getScript(el2+'?v='+version);
							}
						});
					}else{
						getScript(el.src+'?v='+version);
					}
				});
			}else{
				getScript(el+'?v='+version);
			}
		});
	}

})();
