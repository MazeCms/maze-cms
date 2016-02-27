<div class="alert alert-success">
	<button type="button" class="close" onclick="$(this).closest('.alert').fadeOut(300, function(){$(this).remove()})">&times;</button>
	<strong><?php echo Text::_("LIB_FRAMEWORK_DOCUMENT_MESS_SUCCESS"); ?></strong></br>
	 <?php echo Text::_($text); ?>
</div>