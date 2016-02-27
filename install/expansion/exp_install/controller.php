<?php

defined('_CHECK_') or die("Access denied");

use exp\exp_install\model\StartInstall;
use exp\exp_install\model\DBInstall;
use exp\exp_install\model\AccountInstall;
use exp\exp_install\model\StepInstall;
use exp\exp_install\model\LangInstall;
use exp\exp_install\model\ProjectInstall;
use maze\base\Model;
use maze\commet\HttpPush;
use maze\db\Connection;
use maze\thread\Thread;
use maze\install\WizardInstall;

class Install_Controller extends maze\expansion\InstallController {

    public function actionDisplay($step = '0', $lang = null) {
        
        $langModel = new LangInstall;
        $startModel = new StartInstall;
        $dbModel = new DBInstall;
        $accountModel = new AccountInstall;
        $projectInstall = new ProjectInstall;
        $curentModel = $startModel;
        $allModel = [$langModel, $dbModel, $accountModel, $projectInstall];
        $profileModel = null;
        $wizardInstall = null;
        $stepModel = new StepInstall([
            'dbModel' => $dbModel,
            'accountModel' => $accountModel,
            'langModel' => $langModel
        ]);
        $langs = $stepModel->getLang();
        $wizardStep = $this->request->get('wizardStep');
        if ($lang && isset($langs[$lang])) {
            $langModel->attributes = $langs[$lang];
            if ($langModel->validate()) {
                $stepModel->copyLang($lang);
                RC::app()->lang = $langModel->lang_code;
            }
        } else {
            $langModel->attributes = $langs['ru'];
        }

        foreach ($allModel as $model) {
            $model->load($this->request->post());
        }
        switch ($step) {
            case "1":
                $curentModel->validate();
                break;

            case "2":
                $curentModel = $dbModel;
                if ($this->request->isPost()) {
                    if (!$startModel->validate()) {
                        $curentModel = $startModel;
                        $step = 1;
                    } else {
                        $curentModel = $dbModel;
                    }

                    if ($step == 2 && $this->request->post('DBInstall')) {
                        $curentModel = $dbModel;
                    }
                }

                break;

            case "3":

                if ($this->request->isPost() && $this->request->post('DBInstall')) {
                    $curentModel = $dbModel;
                    if (!$curentModel->validate()) {
                        $step = 2;
                        $curentModel = $dbModel;
                    } else {
                        $curentModel = $accountModel;
                    }
                } else {
                    $step = 2;
                    $curentModel = $dbModel;
                }
                if (!$startModel->validate()) {
                    $curentModel = $startModel;
                    $step = 1;
                }


                break;
            case "4":
                if (!Model::validateMultiple($allModel)) {
                    if (!$dbModel->validate()) {
                        $step = 2;
                        $curentModel = $dbModel;
                    } elseif (!$accountModel->validate()) {
                        $step = 3;
                        $curentModel = $accountModel;
                    } else {
                        $curentModel = $projectInstall;
                    }
                } else {

                    $curentModel = $projectInstall;
                }
                break;
            case "5":
                if (!Model::validateMultiple($allModel) || !$profileModel) {
                    if (!$accountModel->validate()) {
                        $step = 3;
                        $curentModel = $accountModel;
                    } elseif (!$projectInstall->validate() || !$profileModel) {
                        $step = 4;
                        $curentModel = $projectInstall;
                        $isBreakStep = false;
                        if ($projectInstall->validate()) {
                            $wizardInstall = $stepModel->getProfile($projectInstall->name);
                            if (!$wizardStep) {
                                $wizardStep = $wizardInstall->firstStep();
                                if ($endModel = $wizardInstall->getEndStep()) {
                                    if ($this->request->post($endModel['name'])) {
                                        $profileModel = true;
                                        $wizardStep = $endModel['name'];
                                    }
                                }
                            }
                            foreach ($wizardInstall->getSteps() as $st) {
                                $modelSt = $wizardInstall->getStepModel($st['name']);
                                $modelSt->load($this->request->post());

                                if ($st['name'] == $wizardStep) {
                                    break;
                                } else {
                                    if (!$modelSt->validate()) {
                                        $wizardStep = $st['name'];
                                        $isBreakStep = true;
                                        break;
                                    } else {
                                        $allModel[] = $modelSt;
                                    }
                                }
                            }
                            $step = '4-1';

                            if ($isBreakStep && $profileModel && count($wizardInstall->allModel) == count($wizardInstall->steps)) {
                                $step = 5;
                            } else {
                                if ($this->request->post($wizardInstall->endStep['name'])) {
                                    if (!$modelSt->validate()) {
                                        $profileModel = false;
                                    }
                                } else {
                                    $profileModel = false;
                                }

                                if ($profileModel) {
                                    $step = 5;
                                    $allModel[] = $modelSt;
                                }
                            }
                        }
                    }
                }

                if (Model::validateMultiple($allModel) && $profileModel) {
                    if ($this->request->isAjax() && $this->request->isPost()) {
                        $result = $stepModel->createCommandStep($this->request->post('nextStep'), $wizardInstall);
                        return json_encode(['html' => $result]);
                    }
                }
                break;
            case "6":
                return RC::app()->document->setRedirect('/admin/admin');
                break;
            default:
                $step = "0";
                $curentModel = $langModel;
        }

        return parent::display([
                    'step' => $step,
                    'langs' => $langs,
                    'curentModel' => $curentModel,
                    'allModel' => $allModel,
                    'stepModel' => $stepModel,
                    'profileModel' => $profileModel,
                    'wizardInstall' => $wizardInstall,
                    'wizardStep' => $wizardStep,
                    'totalStep' => count($wizardInstall->allModel) + 3
        ]);
    }
    
  

}

?>