<?php
/**
 * XGPCore
 *
 * PHP Version 7.1+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\core;

use application\libraries\TemplateLib;
use application\libraries\Users_library;
use CI_Lang;

/**
 * XGPCore Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
abstract class XGPCore
{

    protected static $lang;
    protected static $users;
    protected static $objects;
    protected static $page;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->setLangClass(); // LANGUAGE
        $this->setUsersClass(); // USERS
        $this->setObjectsClass(); // OBJECTS
        $this->setTemplateClass(); // TEMPLATE
    }

    /**
     * setLangClass
     *
     * @return void
     */
    private function setLangClass()
    {
        $languages = new Language();
        self::$lang = $languages->lang();
    }

    /**
     * setUsersClass
     *
     * @return void
     */
    private function setUsersClass()
    {
        self::$users = new Users_library();
    }

    /**
     * setObjectsClass
     *
     * @return void
     */
    private function setObjectsClass()
    {
        self::$objects = new Objects();
    }

    /**
     * setTemplateClass
     *
     * @return void
     */
    private function setTemplateClass()
    {
        self::$page = new TemplateLib(self::$lang, self::$users);
    }

    /**
     * Load the provided model, support a dir path
     *
     * @param string $class Mandatory field, if not will throw an exception
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function loadModel($class)
    {
        try {
            // some validations
            if ((string) $class && $class != '' && !is_null($class)) {
                $class_route = strtolower(substr($class, 0, strrpos($class, '/')));
                $class_name = ucfirst(strtolower(substr($class, strrpos($class, '/') + 1, strlen($class))));
                $model_file = XGP_ROOT . MODELS_PATH . strtolower($class) . '.php';

                // check if the file exists
                if (file_exists($model_file)) {
                    require_once $model_file;

                    $class_route = strtr(MODELS_PATH . $class_route . DIRECTORY_SEPARATOR . $class_name, ['/' => '\\']);
                    $this->{$class_name . '_Model'} = new $class_route(new Database());
                    return;
                }
            }

            // not found
            throw new \Exception('Model not defined');
        } catch (\Exception $e) {
            die('Fatal error: ' . $e->getMessage());
        }
    }

    /**
     * Load a language file using CI Library
     *
     * @param string|array $language_file
     * @return void
     */
    protected function loadLang($language_file): void
    {
        try {
            // require email library
            $ci_lang_path = XGP_ROOT . SYSTEM_PATH . 'ci3_custom' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Lang.php';

            if (!file_exists($ci_lang_path)) {
                // not found
                throw new \Exception('Language file "' . $language_file . '" not defined');
                return;
            }

            // required by the library
            if (!defined('BASEPATH')) {
                define('BASEPATH', XGP_ROOT . APP_PATH);
            }

            // use CI library
            require_once $ci_lang_path;

            $this->langs = new CI_Lang;
            $this->langs->load($language_file, DEFAULT_LANG);
        } catch (\Exception $e) {
            die('Fatal error: ' . $e->getMessage());
        }
    }
}

/* end of XGPCore.php */
