<div class="alert alert-info">
	<button type="button" class="close" onclick="$(this).closest('.alert').fadeOut(300, function(){$(this).remove()})" data-dismiss="alert">&times;</button>
	<strong><?php echo Text::Lib("LIB_FRAMEWORK_DOCUMENT_MESS_INFO"); ?></strong></br>
	 <?php echo Text::_($text); ?>
</div>