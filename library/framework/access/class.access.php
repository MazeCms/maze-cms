<?php

defined('_CHECK_') or die("Access denied");

use maze\table\Sessions;
use maze\table\Users;
use maze\table\Privates;
use maze\exception\UserException;
use maze\db\Query;

class Access {

    /**
     * @var экземпляр текущего класса статически 
     */
    private static $_instance;

    /**
     * @var логин пользователя
     */
    private $username;

    /**
     * @var хеш текущей сессии
     */
    private $sid;

    /**
     * @var идентификатор текущего пользователя
     */
    private $uid;
    
    private $id_role = [];
    
    private $id_admin_role;
    
    private $privilege;
    
    private $user_cache = [];
    
    private $acces_role = [];

    /**
     * @var array rule - копилка праивил для расширений 
     */
    private $rule = [];

    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {

        $this->conf = RC::getConfig();
        
        $this->session = RC::app()->session;
        
        $this->cache = RC::getCache("fw_access");
        $this->sid = null;
        $this->uid = null;
    }

    /**
     * Очистка неиспользуемых сессий
     * 
     */
    public function clearSessions() {
         
        $min = date('Y-m-d H:i:s', time() - 60 * $this->conf->get("ses_time"));

        $oldSes = Sessions::find()->where('time_last < :min', [':min' => $min])->asArray()->all();

        if (!empty($oldSes)) {
            foreach ($oldSes as $ses) {
                if ($ses["id_user"] == 0)
                    continue;
                $user = Users::findOne(['id_user' => $ses["id_user"]]);
                $user->lastvisitDate = $ses["time_last"];
                $user->status = 0;
                $user->scenario = 'access';
                $user->update();
            }
        }

        Sessions::deleteAll('time_last < :min', [':min' => $min]);
     
    }

    /**
     * Авторизация пользователей
     * @param string $username
     * @param string $password
     * @param boolean $remember
     * @return boolean
     */
    public function setLogin($username, $password, $remember = true) {
        $this->username = $username;
        $this->password = $password;

        // вытаскиваем пользователя из БД 
        $user = $this->getByLogin($username);

        if ($user == null)
            return false;

        $id_user = $user['id_user'];

        // проверяем пароль
        if ($user['password'] != md5($password))
            return false;

        // запоминаем имя и md5(пароль)
        if ($remember) {
            $expire = time() + 3600 * 24 * 100;
            $cookies = RC::app()->document->getCookies();
            $cookies->add(new \maze\base\Cookie(['name'=>'login', 'value'=>$username, 'expire'=>$expire]));
            $cookies->add(new \maze\base\Cookie(['name'=>'password', 'value'=> md5($password), 'expire'=>$expire]));
        }


        // открываем сессию и запоминаем SID
        $this->sid = $this->openSession($id_user);

        return true;
    }

    /**
     * выход из сесии и чистка cookie
     */
    public function logout() {

        Sessions::deleteAll(['id_user' => $this->uid]);
        Users::updateAll(["status" => 0, "lastvisitDate" => date('Y-m-d H:i:s')], ['id_user' => $this->uid]);
        $cookies = RC::app()->document->getCookies();
        $cookies->remove('login');
        $cookies->remove('password');
        $this->session->destroy();
        $this->sid = null;
        $this->uid = null;
    }

    /**
     * Получение пользователя	
     * @param int $id_user - если не указан, брать текущего
     * @return array - объект пользователя
     */
    public function get($id_user = null) {
        // Если id_user не указан, берем его по текущей сессии.
        if ($id_user == null)
            $id_user = $this->getUid();

        if ($id_user == null)
            return null;

        if (array_key_exists($id_user, $this->user_cache)) {
            return $this->user_cache[$id_user];
        }

        $result = \RC::getDb()->cache(function($db) use($id_user) {
            return Users::find()->where(['id_user' => $id_user, 'bloc' => 0])->asArray()->one();
        }, null, "fw_access");

        return $this->user_cache[$id_user] = $result;
    }

    public function isGuest() {
        return empty($this->get());
    }

    /**
     * Получает пользователя по логину
     * @param string $username
     * @return array
     */
    public function getByLogin($username) {
        
        $result = \RC::getDb()->cache(function($db) use($username) { 
            return Users::find()->where(['username' => $username, 'bloc' => 0])->asArray()->one();
        }, null, "fw_access");
        
        return $result;
    }

    /**
     * Получение id текущего пользователя
     * @return string|null - UID
     */
    public function getUid() {
        if ($this->uid != null)
            return $this->uid;

        $sid = $this->getSid();

        if ($sid === null || $sid === false)
            return null;

        $result = Sessions::find()
                ->joinWith('user')
                ->andWhere([Sessions::tableName() . '.sid' => $sid, 'u.bloc' => 0])
                ->asArray()
                ->one();

        if (count($result) == 0)
            return null;

        $this->uid = $result['id_user'];

        return $this->uid;
    }

    /**
     * Возвращает хеш текущей сессии
     * @return string|null
     */
    private function getSid() {
        if (!($this->sid === null))
            return $this->sid;

        $sid = $this->session->getSessionId();
        $cookies = RC::app()->request->getCookies();
        
        if ($sid !== null) {
            $exists = Sessions::find()->where(['and', 'sid=:sid', 'id_user!=0'], [':sid' => $sid])->exists();
            if (!$exists)
                $sid = null;
        }

        if ($sid == null && $cookies->getValue('login')) {
            $user = $this->getByLogin($cookies->getValue('login'));

            if ($user !== null && $user['password'] == $cookies->getValue('password')) {
                $sid = $this->openSession($user['id_user']);
            }
        }

        if ($sid !== null) {
            $this->sid = $sid;
        } else {
            $this->sid = false;
        }

        return $this->sid;
    }

    /**
     *   Открытие новой сессии
     *   результат	- SID     * 
     */
    public function openSession($id_user = null) {

        if ($id_user !== null) {
            Users::updateAll(["status" => "1"], ['id_user' => $id_user]);
        }

        $sid = $this->session->getSessionId();

        $now = date('Y-m-d H:i:s');

        if ($sid == null) {
            $this->session->sessoinStart();
        }

        if ($id_user !== null) {
            $session['id_user'] = $id_user;
        }
        $session['time_last'] = $now;

        $checkup_sid = Sessions::updateAll($session, ['sid' => $sid]);

        if ((int) $checkup_sid == 0) {
            $check_sid = Sessions::find()->where(['sid' => $sid])->exists();
            if (!$check_sid) {
                $sid = null;
            }
        }

        if ($sid !== null)
            return $sid;

        $sid = $this->session->getSessionId();

        $session = array(
            'id_user' => $id_user == null ? 0 : $id_user,
            'sid' => $sid,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'agent' => $_SERVER['HTTP_USER_AGENT'],
            'time_start' => $now,
            'time_last' => $now
        );
        $ormSessions = new Sessions(['scenario' => 'access']);
        $ormSessions->attributes = $session;
        $ormSessions->save(false);
        return $sid;
    }

    /**
     * Роли - привелегии пользователей
     * 
     * $privilege - название привелегии
     * $exp_name - название приложения
     * $id_user - id пользователя, если не указан, брать текущего
     */
    public function roles($exp_name, $privilege, $id_user = null, $param = []) {

        if ($id_user == null)
            $id_user = $this->getUid();

        if ($id_user == null)
            return false;

        $key = ["roles", $exp_name, $privilege, $id_user, $param];

        $key_priv = md5(serialize($key));

        if (!isset($this->privilege[$key_priv])) {
            
            $result = \RC::getDb()->cache(function($db) use ($privilege, $exp_name, $id_user) {
                return Privates::find()
                                ->innerJoinWith('rolePrivate.roles.userRoles')
                                ->andWhere([
                                    Privates::tableName() . '.name' => $privilege,
                                    Privates::tableName() . '.exp_name' => $exp_name,
                                    maze\table\UserRoles::tableName() . '.id_user' => $id_user])->one();
            }, null, "fw_access");

            if (empty($result)) {
                $this->privilege[$key_priv] = false;
            } else {
                $permission = $this->create('maze\access\Permission', [
                    'expName' => $result->exp_name,
                    'name' => $result->name,
                    'title' => $result->title,
                    'ruleName' => $result->rule_name,
                    'description' => $result->description
                ]);
                $this->privilege[$key_priv] = $permission;
            }
        }

        $permission = $this->privilege[$key_priv];
         
        if ($permission) {
            
            if ($permission->ruleName) {
               
                if(empty($param)){
                    return true;
                }
                elseif ($rule = $this->getRule($permission->ruleName, $permission->expName)) {                   
                    return $rule->execute($id_user, $permission, $param);
                }
            }
            return true;
        }
        return false;
    }

    public function create($name, $param = []) {
        $object = null;
        if (is_array($name)) {
            $object = RC::createObject($name);
        } else {
            $conf = ['class' => $name];
            $object = RC::createObject(array_merge($conf, $param));
        }
        return $object;
    }

    public function getRule($name, $exp_name) {
        if (!isset($this->rule[$name][$exp_name])) {

            $row = (new Query)->select(['data'])
                    ->from('{{%private_rule}}')
                    ->where(['name' => $name, 'exp_name' => $exp_name])
                    ->one();

            $this->rule[$name][$exp_name] = $row === false ? null : unserialize($row['data']);
        }

        return $this->rule[$name][$exp_name];
    }

    public function getPrivate($name, $exp_name) {
        
        $row = (new Query)->from('{{%private}}')->where(['exp_name' => $exp_name, 'name' => $name])->one();
        
        $result = null;
        
        if ($row) {
            $result = $this->create('\maze\access\Permission', [
                'expName' => $row['exp_name'],
                'name' => $row['name'],
                'title' => $row['title'],
                'ruleName' => $row['rule_name'],
                'description' => $row['description']
            ]);
        }
        
        return $result;
    }

    public function add($object) {
        if (is_array($object) || is_string($object)) {
            $object = $this->create($object);
        }
        if ($object instanceof \maze\access\Permission) {
            return $this->addPrivate($object);
        } elseif ($object instanceof \maze\access\Rule) {
            return $this->addRule($object);
        } else {
            throw new UserException("Неизвестный тип данных");
        }
    }

    public function update($object) {
        if (is_array($object) || is_string($object)) {
            $object = $this->create($object);
        }
        if ($object instanceof \maze\access\Permission) {
            return $this->updatePrivate($object);
        } elseif ($object instanceof \maze\access\Rule) {
            return $this->updateRule($object);
        } else {
            throw new UserException("Неизвестный тип данных");
        }
    }

    protected function addPrivate($permission) {


        if (!(new Query)->from('{{%private_rule}}')->where(['exp_name' => $permission->expName, 'rule_name' => $permission->ruleName])->exists()) {
            throw new UserException(Text::_("Такого правила ({name}) не существует в БД ", ['name' => $permission->ruleName]));
        }

        if ((new Query)->from('{{%private}}')->where(['exp_name' => $permission->expName, 'name' => $permission->name])->exists()) {
            throw new UserException(Text::_("Такое разрешение ({name}) уже существует в БД ", ['name' => $permission->name]));
        }

        RC::getDb()->createCommand()
                ->insert('{{%private}}', [
                    'name' => $permission->name,
                    'exp_name' => $permission->expName,
                    'rule_name' => $permission->ruleName,
                    'title' => $permission->title,
                    'description' => $permission->description
                ])->execute();

        return true;
    }

    protected function updatePrivate($permission) {


        if (!(new Query)->from('{{%private_rule}}')->where(['exp_name' => $permission->expName, 'rule_name' => $permission->ruleName])->exists()) {
            throw new UserException(Text::_("Такого правила ({name}) не существует в БД ", ['name' => $permission->ruleName]));
        }

        if (!(new Query)->from('{{%private}}')->where(['exp_name' => $permission->expName, 'name' => $permission->name])->exists()) {
            throw new UserException(Text::_("Такое разрешение ({name}) уже существует в БД ", ['name' => $permission->name]));
        }

        RC::getDb()->createCommand()
                ->update('{{%private}}', [
                    'rule_name' => $permission->ruleName,
                    'title' => $permission->title,
                    'description' => $permission->description
                        ], [
                    'name' => $permission->name,
                    'exp_name' => $permission->expName,
                ])->execute();

        return true;
    }

    protected function addRule($rule) {
        if ((new Query)->from('{{%private_rule}}')->where(['exp_name' => $rule->expName, 'name' => $rule->name])->exists()) {
            throw new UserException(Text::_("Такое правила ({name}) уже существует ", ['name' => $rule->name]));
        }

        $time = time();
        if ($rule->createdAt === null) {
            $rule->createdAt = $time;
        }
        if ($rule->updatedAt === null) {
            $rule->updatedAt = $time;
        }
        RC::getDb()->createCommand()
                ->insert('{{%private_rule}}', [
                    'name' => $rule->name,
                    'exp_name' => $rule->expName,
                    'data' => serialize($rule),
                    'created_at' => $rule->createdAt,
                    'updated_at' => $rule->updatedAt,
                ])->execute();

        return true;
    }

    protected function updateRule($name, $rule) {
        if ($rule->name !== $name) {
            RC::getDb()->createCommand()
                    ->update('{{%private}}', ['rule_name' => $rule->name], ['rule_name' => $name, 'exp_name' => $rule->expName])
                    ->execute();
        }

        $rule->updatedAt = time();

        RC::getDb()->createCommand()
                ->update('{{%private_rule}}', [
                    'name' => $rule->name,
                    'exp_name' => $rule->exp_name,
                    'data' => serialize($rule),
                    'updated_at' => $rule->updatedAt,
                        ], [
                    'name' => $name,
                ])->execute();

        return true;
    }

    public function isAdmin() {
        return $this->roles("system", "LOGIN_ADMIN");
    }

    /**
     * РОЛИ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ     * 
     * return (array) возвращает массив вида [индекс] = id роль ...
     */
    public function getIdRole($id_user = null) {
        $user = $this->get($id_user);

        if (empty($user))
            return false;

        $id_user = $user["id_user"];

        if (isset($this->id_role[$id_user])) {
            return $this->id_role[$id_user];
        }

        $result = \RC::getDb()->cache(function($db) use ($id_user) {
                return maze\table\UserRoles::find()->where(['id_user' => $id_user])->asArray()->all();
            }, null, "fw_access");

        $arr = array_map(function($array) {
            return $array['id_role'];
        }, $result);

        $this->id_role[$id_user] = $arr;

        return $arr;
    }

    /**
     * Получить ID роли ROOT пользователя
     * @return int
     */
    public function getIdAdminRole() {
        if ($this->id_admin_role !== null)
            return $this->id_admin_role;

        $result = \RC::getDb()->cache(function($db) {
            return maze\table\Roles::find()->innerJoinWith('rolePrivate.privates', false)
                            ->andWhere([
                                Privates::tableName() . '.name' => 'ROOT',
                                Privates::tableName() . '.exp_name' => 'system'
                            ])->one();
        }, null, "fw_access");
        $this->id_admin_role = $result->id_role;
        return $result->id_role;
    }

    /**
     * Проверка на разрешение для ролей пользователя с id_user  
     * @param sting $key_role - хеш правила
     * @param string $exp_name - системное название приложения
     * @param int $id_user - ID пользователя если пусто то текущего
     * @return boolean
     */
    public function getAccessRole($key_role, $exp_name, $id_user = null) {

        if (!isset($this->acces_role[$exp_name])) {

            $result = \RC::getDb()->cache(function($db) use ($exp_name) {
                return maze\table\AccessRole::find()->where(['exp_name' => $exp_name])->asArray()->all();
            }, null, "fw_access");
            

            $this->acces_role[$exp_name] = array();

            foreach ($result as $access) {
                $this->acces_role[$exp_name][$access["key_role"]][] = $access["id_role"];
            }
        }

        if (!isset($this->acces_role[$exp_name][$key_role]))
            return true;

        $user_role = $this->getIdRole($id_user);
        $acc_role = $this->acces_role[$exp_name][$key_role];

        foreach ($user_role as $id_role) {
            if (in_array($id_role, $acc_role)) {
                return true;
                break;
            }
        }
        return false;
    }

    /**
     * Проверка на принадлежности ролям $arrIDrole
     * @param array|string $arrIDrole - массив или строка через(,) ID проверямых пролей
     * @param int $id_user - ID пользователь или текущий
     * @return boolean
     */
    public function getAccessIDRole($arrIDrole, $id_user = null) {

        if ($arrIDrole == null)
            return true;

        if (is_string($arrIDrole)) {
            $arrIDrole = $arrIDrole == null ? array() : explode(",", $arrIDrole);
        }

        if (empty($arrIDrole))
            return true;

        $user_role = $this->getIdRole($id_user);

        if (empty($user_role))
            return false;

        foreach ($user_role as $id_role) {
            if (in_array($id_role, $arrIDrole)) {
                return true;
                break;
            }
        }
        return false;
    }

}

?>