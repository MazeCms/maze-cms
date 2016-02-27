<div class="alert alert-danger">
	<button type="button" class="close" onclick="$(this).closest('.alert').fadeOut(300, function(){$(this).remove()})" data-dismiss="alert">&times;</button>
	<strong><?php echo Text::_("LIB_FRAMEWORK_DOCUMENT_MESS_ERROR"); ?></strong></br>
	 <?php echo Text::_($text); ?>
</div>