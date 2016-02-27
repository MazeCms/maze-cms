<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;

class User_Controller_Sessions extends Controller {

    public function accessFilter() {
        return [
            'display' => ["user", "VIEW_SESSIONS"],
            'delete' => ["user", "DELETE_SESSIONS"]
        ];
    }

    public function actionDisplay() {
        $modelFilter = $this->form('FilterSessions');

        if ($response = FilterBuilder::action($modelFilter)){
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            $model = maze\table\Sessions::find()->joinWith('user')->from(['s' => maze\table\Sessions::tableName()]);
            $modelFilter->queryBilder($model);
            return (new GridFormat([
                'id' => 'sessions-grid',
                'model' => $model,
                'colonum' => 's.time_last',
                'colonumData' => [
                    'id' => '$data->id_sess',
                    'id_user' => function($data) {
                        if ($user = $data->user) {
                            if (!$user->avatar) {
                                $user->avatar = '/library/image/custom/user.png';
                            }
                            return Html::imgThumb('@root' . $user->avatar, 50, 50) . ' ' . $user->name;
                        }

                        return Text::_('EXP_USER_SESSIONS_TABLE_ANONIM');
                    },
                    'ip',
                    'ossys' => function($data) {
                        return $this->request->getOS($data->agent);
                    },
                    'browser' => function($data) {
                        return $this->request->gerBrowser($data->agent) ? $this->request->gerBrowser($data->agent) : $data->agent;
                    },
                    'time_start' => function($data) {
                        return DataTime::format($data->time_start, false, '-');
                    },
                    'time_last' => function($data) {
                        return DataTime::format($data->time_last, false, '-');
                    },
                    'id_sess'
                ]
            ]))->renderJson();
        }
        
        return parent::display([
            'modelFilter'=>$modelFilter
        ]);
    }


    public function actionDelete(array $id_sess) {              
       maze\table\Sessions::deleteAll(['id_sess'=>$id_sess]);
       $this->setMessage(Text::_("EXP_USER_SESSIONS_CONTROLLER_DELETE_SES"), 'success');
       $this->setRedirect('/admin/user/sessions');
    }

}

?>