<?php defined('_CHECK_') or die("Access denied");
class User_View_Sessions extends View
{
	public function  registry()
	{
            RC::app()->breadcrumbs = ['label' => 'EXP_USER_SESSIONS_TITLE'];
            $toolbar = RC::app()->toolbar;
            $toolbar->addGroup('user', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_USER_SESSIONS_BTN_DELSES",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("user", "DELETE_SESSIONS"),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#sessions-grid', this.href)"
            ]);
	}
	
	
}
?>