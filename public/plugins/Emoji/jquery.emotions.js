(function($){	
	$.fn.emotions = function(options){
		$this = $(this);
		var opts = $.extend({}, $.fn.emotions.defaults, options);
		return $this.each(function(i,obj){
			var o = $.meta ? $.extend({}, opts, $this.data()) : opts;					   	
			var x = $(obj);
			// Entites Encode 
			var encoded = [];
			for(i=0; i<o.s.length; i++){
				encoded[i] = String(o.s[i]).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
			}
			for(j=0; j<o.s.length; j++){
				var repls = x.html();
				if(repls.indexOf(o.s[j]) || repls.indexOf(encoded[j])){
					var imgr = o.b[j];
					var rstr = "<i class='twa twa-lg twa-"+imgr+"'></i>";	
					//Escape the ')' and '(' brackets
var tempStrSmiley1 = o.s[j].replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
var tempStrSmiley2 = encoded[j].replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");

x.html(repls.replace(new RegExp(tempStrSmiley1, 'g'), rstr));
x.html(repls.replace(new RegExp(tempStrSmiley2, 'g'), rstr));

				}
			}
		});
	}	
	// Defaults
	$.fn.emotions.defaults = {
		b : new Array("smirk","innocent","unamused","dizzy-face","sob","smiling-imp","anguished","flushed","smiley","confused","heart","relaxed","kissing-heart","thumbsup","smile","neutral-face","sunglasses","stuck-out-tongue-winking-eye","wink"),			// Emotions Type
		s : new Array(":S","o:)",":3","o.O",":'(","3:)",":(",":O",":D",":|","<3","^_^",":*","(y)",":)","-_-","8)",":P",";)"),
	};
})(jQuery);
