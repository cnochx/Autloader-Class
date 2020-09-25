<?php
namespace App\Autoloader;

/**
 *  # Autoloader-Class
 *  An Autoloder-Class intended to be used as a default implementation for __autoload().
 * 
 *  Highly inspired from the PSR-4 autoloader: https://www.php-fig.org/psr/psr-4 and the sample code: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md.
 *  The Class is searching inside given structure and give back a `require_once('base/directory/of/that/File.php')`.
 * 
 *  ## Integrate this autoloader with: 
 * |    $Loading = new App\Autoloader\Autoloader;
 * 
 *  ## Add the structure of that App:
 * |    $Loading->setStruktur('core');
 * |    $Loading->setStruktur('app/controllers');
 * 
 *  ## Add the Root Directory of that App
 * |    $Loading->setRoot(dirname(__FILE__));
 * 
 * ## Set the Classes
 * |    $Loading->setClass('core\Bootstrap', 'Bootstrap');
 * |    $Loading->setClass('core\Controller', 'Controller');
 * |    $Loading->setClass('app\controllers\User', 'User');
 * 
 * ## Register 
 * |    $Loading->register()
 * 
 * ## Call the Classes in your Awasome Code
 * |    $InstantiatedBootstrap = new core\Bootstrap\Bootstrap;
 * |    $InstantiatedController = new core\Controller\Controller;
 * |    $InstantiatedUser = new app\controllers\User\User;
 * 
 * 
 * :pray:
 */

class Autoloader {
    
    // Hold the Filenames as array
    protected $baseDir = array();
    // Collect the Struktur of that app in an array , 
    protected $structure = array();
    // Hold the Root-Dir
    protected $root = NULL;

    /** 
     *  Set the structure
     * @Param:
     *  | $dir_structure: (string) Set the Struktur of the Folders in that App, for example: 'app/controllers' 
     * _@Return: (void) push in array $structure
     * */
    public function setstructure($dir_structure) {

        // normalize that String
        $dir_structure = trim($dir_structure, DIRECTORY_SEPARATOR);

        // add this an $struktur, if value not exits
        if(in_array($dir_structure, $this->structure) == false) {
            array_push($this->structure, $dir_structure . DIRECTORY_SEPARATOR);
        } 
    }

    /** 
     *  Set the Root
     * @Param:
     *  | $root: (string) Set the Root Directory from that App
     * _@Return: (string) push in Var $root
     * */
    public function setRoot($root) {

        // normalize dir
        trim($root);
        
        // add this an $root
        if($this->root === NULL) {
            unset($this->root);
        }
        $this->root = $root . DIRECTORY_SEPARATOR;
    }

    /** 
     *  Set the Class as array and collect the filename
     * @Param:
     *  | $prefixNamespace: (string) The Namespace Prefix, for example: 'core\Controller'
     *  | $class: (string) The NAme of Class, for example: 'Controller'
     * _ @Return: (bool) true, if that file added, false if the file in the given Struktur not exists
     * */
    public function setClass ($prefixNamespace, $class) {

        // normalize Namespace and Prefix
        $prefixNamespace = trim($prefixNamespace, '\\');
        $class = trim($class);

        foreach ($this->structure as $key =>$value){
            if(file_exists($this->root . $value . $class . '.php')) {
                $this->baseDir[$prefixNamespace . '\\' . $class] = $value;
            } 
        }   
    }
    /**
     * Register the results
     * _ @Results: (void)
     */
    public function register() {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * get the class from the namespace\Class
     * | $class: (string) The Class with Namespace, for example: 'core\Controller\Controller'
     * _ @Return: (mixed) The File, false if that file not exists
     */
    protected function loadClass($class) {

        // try to load a mapped file for the class
        if ($this->loadMappedFileRegister($class)) {
                
            return $this->loadMappedFileRegister($class);
        }
        // never found a mapped file
        return false;
    }

    /**
     * Load the Mapped file for Register
     * | $class: (string) The Class with Namespace, for example: 'core\Controller\Controller'
     * _ @Return: (mixed) The File, false if that file not exists
     */
    protected function loadMappedFileRegister($class) {
            
            $file = $this->requireOnceFile($this->loadMappedFile($class));
            if ($file){
                // Yeah, we're Done so far
                return $file;
            } else {
                // Houston, we have finally problem. it didn't work so far
                return false;
            }
    }

    /**
     * Load the mapped file
     * | $class: (string) The Class with Namespace, for example: 'core\Controller\Controller'
     * _ @Return: (mixed)  The complete direcotry of that File, false if that file not exists
     */
    protected function loadMappedFile($class){

        // looking for Key exist
        if(array_key_exists($class, $this->baseDir)) {
            foreach ($this->baseDir as $key => $value) {
                if($key === $class) {
                    // create the file from root direectory, base directory and the class with extension
                    $file = $this->root
                          .  $value
                          . $this->splitClass($class)
                          . '.php';                    
                    // get the File
                    if (file_exists($file)) {
                        return $file;
                    } else {
                        
                        return false;
                    }
                }
            }
        } else {
            
            return false;
        }
    }

    /**
     * get the class from the namespace\Class
     * | $class: (string) The Class with Namespace, for example: 'core\Controller\Controller'
     * _ @Return: (string) The class, for example: 'Controller'
     */
    protected function splitClass($class) {

        // make an array from $class
        $exlodeClass = explode("\\", $class);

        // Delete the value with nothing, with the length = 0
        $deleteValue = array('');
        $cleanExplode = array_filter($exlodeClass, function($value) use ($deleteValue) {
            return !(in_array($value, $deleteValue, true));
        });
        
        return $cleanExplode[count($cleanExplode)-1];
    }

    /**
     * Include mapped file as require Once
     * | $class: (string) The complete File
     * _ @Return: (bool) true, when the file could be included, false, when that was not possible.
     */
    protected function requireOnceFile($file) {
        // try to include that file
        if (file_exists($file)) {
            require_once $file;
            
            return true;
        }

        return false;
    }
}
