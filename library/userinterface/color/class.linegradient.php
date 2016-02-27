<?php defined('_CHECK_') or die("Access denied");
class LineGradient extends Field
{

	protected $attr; // обработчик события change
	
	
	public function __construct($name, $value, $attr = array())
	{	
		$attr = (array)$attr;	
		$this->value = $value;
		$this->attr = $attr;
		parent::__construct($name,'text');
						
	}
	
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
                       $( "#' . $id . ' .active-hex")
                           .val(hex)
                           .prev(".add-on").css({backgroundColor: "#"+hex})    
                    },
                    onShow:function(){
                        $(this).parent().find(".active-hex").removeClass("active-hex");
                        $(this).addClass("active-hex")
                    },
                    onBeforeShow: function () {
                        $(this).ColorPickerSetColor(this.value);
                    }
                }).bind("focusout", function(){
                    $(this).prev(".add-on").css({backgroundColor: "#"+$(this).val()}) 
                });
	})
		');
            
		$teg = '<div class="input-prepend input-append" id="'.$id.'">';
                $teg .= '<span style="background-color: #'.(isset($this->value[0]) ? $this->value[0] : '' ).';"  class="add-on"></span>';
                $teg .= '<input style="width:60px;" type="text" name="'.$this->name.'[]" value="'.(isset($this->value[0]) ? $this->value[0] : '' ).'">';
                $teg .= '<span style="background-color: #'.(isset($this->value[1]) ? $this->value[1] : '' ).';" class="add-on"></span>';
                $teg .= '<input style="width:60px;"  type="text" name="'.$this->name.'[]" value="'.(isset($this->value[1]) ? $this->value[1] : '' ).'">';
                $teg .= '<div>';

		return $teg;
		
	}
	
	
	
	public function check()
	{
		
	}
	protected function import()
	{
            $this->doc->addStylesheet("/library/jquery/colorpicker/css/colorpicker.css");
            $this->doc->addScript("/library/jquery/colorpicker/js/colorpicker.js");
	}
	
}

?>