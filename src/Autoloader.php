<?php
namespace App\Autoloader;

/**
 *  # Autoloader-Class
 *  An Autoloder-Class intended to be used as a default implementation for __autoload().
 * 
 *  Highly inspired from the PSR-4 autoloader: https://www.php-fig.org/psr/psr-4 and the sample code: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md.
 *  The Class is searching inside given Strukture and give back a `require_once('base/directory/of/that/File.php')`.
 * 
 *  ## Integrate this autoloader with: 
 * |    $Loading = new App\Autoloader\Autoloader;
 * 
 *  ## Add the Strukture of that App:
 * |    $Loading->setStruktur(relative/path/to/folder);
 * |    $Loading->setStruktur(relative/path/to/another/folder);
 * 
 *  ## Add the Root Directory of that App
 * |    $Loading->setRoot(dirname(__FILE__));
 * 
 * ## Set the Classes
 * |    $Loading->setClass('Namespace\YourBootstrap');
 * |    $Loading->setClass('Sublevel\Namespace\YourController');
 * |    $Loading->setClass('Another\Sublevel\Namespace\YourModel');
 * 
 * ## Register 
 * |    $Loading->register()
 * 
 * ## Call the Classes in your Awasome Code
 * |    $YourBootstrap = new Namespace\YourBootstrap;
 * |    $YourController = new Sublevel\Namespace\YourController;
 * |    $YourModel = new Another\Sublevel\Namespace\YourModel;
 * 
 * :pray:
 */

class Autoloader {
    
    // Hold the Filenames as array
    protected $baseDir = array();
    // Collect the Struktur of that app in an array , 
    protected $strukture = array();
    // Hold the Root-Dir
    protected $root = NULL;

    /** 
     *  Set the Strukture
     * @Param:
     *  | $dir_strukture: (string) Set the Struktur of the Folders in that App, for example: 'app/lib' 
     * _@Return: (String) push in array $strukture
     * */
    public function setStrukture($dir_strukture) {

        // normalize that String
        $dir_strukture = trim($dir_strukture, DIRECTORY_SEPARATOR);

        // add this an $struktur, if value not exits
        if(in_array($dir_strukture, $this->strukture) == false) {
            array_push($this->strukture, $dir_strukture . DIRECTORY_SEPARATOR);
        } 
    }

    /** 
     *  Set the Root
     * @Param:
     *  | $root: (string) Set the Root-Dir from that App
     * _@Return: (String) push in Var $root
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
     *  | $class: (string) The Class with Namespace, for example: 'Sublevel\Namespace\Class'
     * _ @Return: (Bool) true, if that file added, false if the file in the given Struktur not exist
     * */
    public function setClass ($class) {

        $class = trim($class, '\\');

        // find path in stack ($strukture)
        foreach ($this->strukture as $value) {
            
            // try to add the class as key and the filename to $baseDir
            if(file_exists($this->root . $value . $this->splitClass($class) .'.php')) {
                // create the key and the value
                $this->baseDir[$class . '\\' . $this->splitClass($class)] = rtrim($this->root . $value);
                
                return true;
            }  else {
                // give up
                return false;
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
     * | $class: (string) The Class with Namespace, for example: 'Sublevel\Namespace\Class'
     * _ @Return: (string) The class, for eexample: 'Class'
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
     * get the class from the namespace\Class
     * | $class: (string) The Class with Namespace, for example: 'Sublevel\Namespace\Class'
     * _ @Return: (mixed) The class, for eexample: 'Class'
     */
    protected function loadClass($class) {

        // try to load a mapped file for the class
        if ($this->loadMappedFile($class)) {
                
            return $this->loadMappedFile($class);
        }
        // never found a mapped file
        return false;
    }

    /**
     * Load the mapped file
     * | $class: (string) The Class with Namespace, for example: 'Sublevel\Namespace\Class'
     * _ @Return: (mixed) The complete file, if not exist, false.
     */
    protected function loadMappedFile($class){
        // check for existing base directory in the $baseDir
        if (isset($this->baseDir[$class]) === false) {
            return false;
        } else {
            // look through base directories for this namespace prefix
            foreach ($this->baseDir as $key => $value) {
                if($key === $class) {
                    // create the file from base directory and the class with extension
                    $file = $value
                          . $this->splitClass($class)
                          . '.php';
                    
                    // get the File
                    if ($this->requireFile($file)) {
                        // yeah, we're done
                        return $file;
                    } else {
                        // Houston, we have problem. it didn't work
                        return false;
                    }
                }
            }
        }
    }

    /**
     * Include mapped file
     * | $class: (string) The complete File
     * _ @Return: (bool) true, when the file could be included, false, when taht was not possible.
     */
    protected function requireFile($file) {
        // try to include that file
        if (file_exists($file)) {
            require_once $file;
            
            return true;
        }

        return false;
    }
}
