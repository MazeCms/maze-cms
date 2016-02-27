<?php defined('_CHECK_') or die("Access denied");
class Color extends LineGradient
{
	
	public static function _($name, $value, $attr= array())
	{
		return new self($name, $value, $attr); 
	}
	
	public function get_html()
	{
            $id = isset($this->attr["id"]) ? $this->attr["id"] : str_replace(array("[","]"), "_", $this->name);
            $this->doc->setTextScritp('
	jQuery(document).ready(function(){
            $( "#' . $id . ' input[type=text]").ColorPicker({
                    onChange: function (hsb, hex, rgb) {                    
                       $( "#' . $id . ' input[type=text]")
                           .val(hex)
                           .prev(".add-on").css({backgroundColor: "#"+hex})    
                    },
                    onBeforeShow: function () {
                        $(this).ColorPickerSetColor(this.value);
                    }
                }).bind("focusout", function(){
                    $(this).prev(".add-on").css({backgroundColor: "#"+$(this).val()}) 
                });
	})
		');
            
		$teg = '<div class="input-prepend" id="'.$id.'">';
                $teg .= '<span style="background-color: #'.(isset($this->value) ? $this->value : '' ).';"  class="add-on"></span>';
                $teg .= '<input style="width:60px;" type="text" name="'.$this->name.'" value="'.(isset($this->value) ? $this->value : '' ).'">';
                $teg .= '<div>';

		return $teg;
		
	}
	
	
	
	public function check()
	{
		
	}
	
	
}

?>