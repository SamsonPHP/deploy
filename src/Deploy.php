<?php
namespace samsonphp\deploy;

use samson\core\Service;
use samsonphp\event\Event;

/**
 * SamsonPHP deployment service
 *
 * @package samsonphp\deploy
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class Deploy extends Service
{
    /** Идентификатор модуля */
    protected $id = 'deploy';

    /** @var Remote */
    public $remote;

    /** @var array Collection of path names to be ignored */
    public $ignorePath = array('cms');

    /** Path to site document root on local server */
    public $sourceroot = '';

    /** FTP host */
    public $host 	= '';

    /** Path to site document root on remote server */
    public $wwwroot	= '';

    /** FTP username */
    public $username= '';

    /** FTP password */
    public $password= '';

    /**
     * Get all entries in $path
     * @param string $path Folder path for listing
     * @return array Collection of entries int folder
     */
    protected function directoryFiles($path)
    {
        $result = array();
        // Get all entries in path
        foreach (array_diff(scandir($path), array_merge($this->ignorePath, array('..', '.'))) as $entry) {
            // Build full REAL path to entry
            $result[$entry] = realpath($path . '/' . $entry);
        }
        return $result;
    }

    /**
     * Perform synchronizing folder via FTP connection
     * @param string 	$path       Local path for synchronizing
     */
    protected function synchronize($path)
    {
        $this->remote->log('Synchronizing remote folder [##]', $path);

        // Check if we can read this path
        foreach ($this->directoryFiles($path) as $fileName => $fullPath) {
            // If this is a folder
            if (is_dir($fullPath)) {
                // Try to create it
                $this->remote->mkDir($fileName);
                // Go deeper in recursion
                $this->synchronize($fullPath);
            } elseif ($this->remote->isOld($fullPath)) { // Check if file has to be updated
                // Copy file to remote
                $this->remote->write($fullPath);
            }
        }

        // Go one level up
        $this->remote->cdup();
    }

    /**
     * Initialize module
     * @param array $params
     * @return bool
     */
    public function init(array $params = array())
    {
        // Check configuration
        if (!isset($this->sourceroot{0})) {
            return $this->error('Local project folder['.$this->sourceroot.'] is not specified');
        }

        // Check configuration
        if (!isset($this->wwwroot{0})) {
            return $this->error('Remote project folder['.$this->wwwroot.'] is not specified');
        }

        return parent::init($params);
    }

    /**
     * Signal module error
     * @param string $message error message
     * @return bool false
     */
    public function error($message)
    {
        // Signal error
        Event::fire('error', array($this, $message));

        return false;
    }

    /**
     * Perform project deployment
     */
    protected function deploy()
    {
        // If this is remote app - chdir to it
        if (__SAMSON_REMOTE_APP) {

            // Change source root to internal web-application
            $this->sourceroot = $this->sourceroot.str_replace('/', '', __SAMSON_BASE__);

            // Change source root to internal web-application
            $this->wwwroot = $this->wwwroot.str_replace('/', '', __SAMSON_BASE__);

            // Create folder
            $this->remote->mkDir(str_replace('/', '', __SAMSON_BASE__));
        }
        
        // Выполним синхронизацию папок
        $this->synchronize($this->sourceroot);
    }

    /** Controller to perform deploy routine */
    public function __BASE()
    {
        $this->title('Deploying project to '.$this->host);

        // If no remote class name is set
        if (!isset($this->remote)) {
            // Create remote connection instance
            $this->remote = new Remote($this->host, $this->username, $this->password);
        }

        // Go to project remote folder
        if ($this->remote->cd($this->wwwroot)) {

            $this->deploy();

            $this->remote->log('Project[##] has been successfully deployed to [##]', $this->sourceroot, $this->host);
        }
    }
}
