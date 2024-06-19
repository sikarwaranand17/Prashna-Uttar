<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo WEB_LINK; ?>public/js/vendor/jquery.min.js"><\/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="<?php echo WEB_LINK; ?>public/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo WEB_LINK; ?>public/plugins/typeahead/typeahead.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?php echo WEB_LINK; ?>public/js/ie10-viewport-bug-workaround.js"></script>
<script type="text/javascript">var PATH = '<?php echo WEB_LINK; ?>';</script>
<script src="<?php echo WEB_LINK; ?>public/plugins/quickfit/jquery.quickfit.js"></script>
<script src="<?php echo WEB_LINK; ?>public/plugins/cropper/cropper.min.js"></script>
<script src="<?php echo WEB_LINK; ?>public/js/jquery.slugit.js"></script>
<script type="text/javascript">var HASH = '<?php echo $random_hash; ?>';</script>

<script src="<?php echo WEB_LINK; ?>public/plugins/niceselect/js/jquery.nice-select.js"></script>
<script src="<?php echo WEB_LINK; ?>public/plugins/sweetalert/sweetalert.min.js"></script>
<script type="text/javascript">
		
		$(document).ready(function() {
			$('.nice-select').niceSelect();
			$('[data-toggle="tooltip"]').tooltip();
		})
		
		$(".searchbox-field").focusout(function() {
			$(".overlay").fadeOut(100);
		});

var fittedwidth = $('.title').width();
$('.quickfit').quickfit({ max: 22, min: 15, width: fittedwidth, truncate: false});

// Instantiate the Bloodhound suggestion engine
var accountBloodhound  = new Bloodhound({
  datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
   remote: {
        url: '<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=q_suggestions#%QUERY',
        wildcard: '%QUERY',
        transport: function (opts, onSuccess, onError) {
            var url = opts.url.split("#")[0];
            var query = opts.url.split("#")[1];
            $.ajax({
                url: url,
                type: 'POST',
				dataType: 'JSON',
				data: 'id=<?php echo $current_user->id; ?>&data=' + query + '&hash="<?php echo $random_hash; ?>"',
				success: onSuccess,
				error: function(data) {
					console.log(data);
				}
            });
        }
    }
});
// Instantiate the Typeahead UI
$('.typeahead').typeahead(null, {
	hint: true,
	highlight: true,
	name: 'search',
    display: 'full',
    source: accountBloodhound 
}).bind('typeahead:select', function (obj, datum) {
	if(datum.length !== 0 && datum.slug !== '' ) {
		window.location.href = datum.slug;
	}
});

$('.typeahead').focus();

function scrollToAnchor(aid){
    var aTag = $("a[name='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
}
function scrollToId(aid){
	var aid = aid.split('#')[1];
    var aTag = $("[id='"+ aid +"']");
    $('html,body').animate({scrollTop: eval(aTag.offset().top - 50)},'slow');
}

function generateSwal(title, type, message) {
    swal(title, message, type);
}
function parseThisTo(data,receptor,type) {
	 var error_found = true;
	try { var obj = $.parseJSON(data); } catch(err) { error_found = false; }
	if ( error_found == true ) {
		if(receptor == '.modal-receptor') {
			swal({title: obj.title,text: obj.msg,type: obj.type,}, function () {$('.modal-receptor').modal('hide'); });
		} else {
			generateSwal(obj.title,obj.type, obj.msg); 
		}
	} else { if(type == 'append') { $(receptor).append(data); } else { $(receptor).html(data); } }
}

<?php
	if (isset($_GET['edit']) && isset($_GET['msg']) && $_GET['edit'] == "success") {
	$status_msg = $db->escape_value($_GET['msg']);				
?>
		generateSwal("Success!","success","<?php echo $status_msg ?>");
<?php
	}
	if (isset($_GET['edit']) && isset($_GET['msg']) && $_GET['edit'] == "fail"  ) {
		$status_msg = $db->escape_value($_GET['msg']);
?>
		generateSwal("Error!","error","<?php echo $status_msg ?>");
<?php 
	}
?>
$('<li id="tags_loading_wrap"><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" style="height:6px"/></center></li>').appendTo('.feed-ul');
$('#tags_loading_wrap').hide();

$('.load-tag').click(function(){
	$('#tags_loading_wrap').show();
	var page= parseInt($('.tag-page').val()) + 1;
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=tags", {id: '<?php echo $current_user->id; ?>' , data: 'tags' , page: page , hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		$('#tags_loading_wrap').hide();
		parseThisTo(data,'.tag-holder','append');
		 $('.tag-page').val(page);
	});
});
</script>
<?php $current_user->set_online(); ?>