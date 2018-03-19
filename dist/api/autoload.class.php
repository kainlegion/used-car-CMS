<?php

class Autoload
{
    private static $loader;
    public $path = __DIR__;

    public static function init($path = null)
    {
        //判断是否已经存在autoload类的实例化对象
        if (self::$loader == null) {
            self::$loader = new autoload($path);
        }
        return self::$loader;
    }

    private function __construct($path = null)
    {
        if ($path != null) {
            $this->path = $path;
        }
        spl_autoload_register(array($this, 'load_controller'));
    }
    //类文件加载
    private function load_controller($class_name)
    {
        //将类名全部转换为小写字母
        $class_name = strtolower($class_name);
        //调用统一文件加载函数
        self::load_file($class_name, $this->path."/", '.php');
    }


    /**
     * 加载文件统一函数
     *
     * @param $class_name 文件名
     * @param $file_path 文件路径
     * @param $postfix 后缀名
     *
     * @author xul
     */

    private function load_file($class_name, $file_path, $postfix)
    {
        //判断该文件是否存在，存在则加载文件
        if (file_exists($file_path . $class_name . $postfix)) {
            //动态加载文件路径，使该路径下的文件可以直接被include
            set_include_path($file_path);
            //php默认加载类文件函数。(默认后缀: .inc; .php;该函数会自动将类名转换为小写)
            spl_autoload($class_name, $postfix);
        }
    }
}
