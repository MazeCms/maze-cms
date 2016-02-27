<?php

defined('_CHECK_') or die("Access denied");

use wid\wid_formsend\model\Formsend;

$params = $this->getParams();
$request = RC::app()->request;
$document = RC::app()->document;

$modelForm = new Formsend();
$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "widget-form-send-" . $this->id);

if ($request->isPost()) {
    $modelForm->load($request->post(null, 'none'));

    if ($modelForm->idform == $id_css) {
        if ($modelForm->validate()) {

            $email = trim($params->getVar('email'));
            $email = preg_split("/,[\s]+|,/s", $email);
            if (!empty($email)) {
                $thema = $params->getVar('thema') ? $params->getVar('thema') : Text::_('WID_FORMSEND_FORM_LABEL_THEME');

                foreach ($email as $m) {

                    $mail[] = RC::getMail()->compose('@wid/wid_formsend/tmp/mail', ['modelForm' => $modelForm, 'params' => $params, 'thema' => $thema])
                            ->setSubject($thema)
                            ->setTo($m, $m)
                            ->send();
                }

                if (in_array(false, $mail)) {
                    $document->setMessage("Ошибка отправки email уведомления", 'error');
                } else {
                    $textsus = $params->getVar('textsuccess') ? $params->getVar('textsuccess') : Text::_("WID_FORMSEND_FORM_MESS");
                    $document->setMessage($textsus, 'success');
                }
                if (!$request->isAjax())
                    $document->setRedirect(\URI::instance()->toString(['path', 'query', 'fragment']));
            }
        }else {
            $document->setMessage("Ошибка отправки email уведомления", 'error');
        }
    }
}

$modelForm->idform = $id_css;
$layout = $params->getVar('layout') ? $params->getVar('layout') : 'default';

echo $this->render('tmp/' . $layout, ['modelForm' => $modelForm, 'params' => $params, 'id_css' => $id_css, 'id' => $this->id, 'widget' => $this]);
?>
