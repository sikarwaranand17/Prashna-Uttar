<script type="text/javascript">
$('.question-like-machine').on('click' , '.like' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count++;
	$(this).attr('value', count);
	$(this).html('<i class="fe fe-thumbs-up"></i> ' + count);
	$(this).removeClass('like');
	$(this).addClass('active');
	$(this).closest('div').find("a.dislike").addClass('disabled');
	$(this).addClass('undo-like');
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=like", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});
$('.question-like-machine').on('click' , '.undo-like' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count--;
	$(this).attr('value', count);
	$(this).html('<i class="fe fe-thumbs-up"></i> ' + count);
	$(this).addClass('like');
	$(this).removeClass('active');
	$(this).closest('div').find("a.dislike").removeClass('disabled');
	$(this).removeClass('undo-like');
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=unlike", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});

$('.question-like-machine').on('click' , '.dislike' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count++;
	$(this).attr('value', count);
	$(this).html('<i class="fe fe-thumbs-down"></i> ' + count);
	$(this).removeClass('dislike');
	$(this).addClass('active');
	$(this).closest('div').find("a.like").addClass('disabled');
	$(this).addClass('undo-dislike');
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=dislike", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});
$('.question-like-machine').on('click' , '.undo-dislike' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count--;
	$(this).attr('value', count);
	$(this).html('<i class="fe fe-thumbs-down"></i> ' + count);
	$(this).addClass('dislike');
	$(this).removeClass('active');
	$(this).closest('div').find("a.like").removeClass('disabled');
	$(this).removeClass('undo-dislike');
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=undislike", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});

$('.question-like-machine').on('click' , '.follow' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count++;
	$(this).attr('value', count);
	$(this).html('<i class="fe fe-user-plus"></i> ' + $(this).data('lbl-active') + ' · ' + count);
	$(this).removeClass('follow');
	$(this).addClass('active');
	$(this).addClass('unfollow');
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=follow", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});

$('.question-like-machine').on('click' , '.unfollow' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count--;
	$(this).attr('value', count);
	$(this).html('<i class="fe fe-user-plus"></i> ' + $(this).data('lbl') + ' · ' +  count);
	$(this).addClass('follow');
	$(this).removeClass('active');
	$(this).removeClass('unfollow');
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=unfollow", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});

$('.question-like-machine').on('click' , '.share' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count++;
	$(".share").attr('value', count);
	$('.share-receptor').html('<i class="fe fe-repeat"></i> ' +  count);
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=share", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});
</script>
