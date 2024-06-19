<script>
<?php if($navsec != 'spaces') { ?> $(document).on('click' , 'a.add-q' , function(){
	var preloader = '<div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-body box-body"><br><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" alt="" style="height:6px"></center><br></div></div></div>';
	$('.modal-receptor').html(preloader); 
	$('.modal-receptor').modal();
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=add_question", {id: '<?php echo $current_user->id; ?>' , data: 'new_question' , hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		parseThisTo(data,'.modal-receptor');
		$('.modal-receptor').modal(); 
	});
}); <?php } ?>

$("a.view-search").click(function() {
	$(".mob-search-brand").css('display', 'none');
	$(".mob-search-box").css('display', 'flex');
});

$("a.back-from-search").click(function() {
	$(".mob-search-brand").css('display', 'block');
	$(".mob-search-box").css('display', 'none');
});

<?php if(isset($analytics_info) && is_array($analytics_info) ) { ?>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $analytics_info['UA']; ?>', 'auto');
  ga('send', 'pageview');
<?php } ?>
</script>

<div class="modal in modal-receptor" id="myModal" role="dialog" aria-labelledby="myModalLabel"></div>