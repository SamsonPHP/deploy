<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 23.01.2015
 * Time: 13:13
 */
namespace samsonphp\deploy;

/**
 * Implementation of remote connection logic
 * @package samsonphp\deploy
 */
class Remote
{
    /** @var resource  */
    protected $handle;

    /** @var string Current remote folder path */
    protected $currentFolder;

    /** @var integer Time difference between remote and local */
    protected $timeDiff;

    /**
     * Generic log function for further modification
     * @param string $message
     * @return mixed
     */
    public function log($message)
    {
        // Get passed vars
        $vars = func_get_args();
        // Remove first message var
        array_shift($vars);

        // Render debug message
        trace(debug_parse_markers($message, $vars));

        return false;
    }

    /**
     * Constructor
     * @param string $host
     * @param string $login
     * @param string $pwd
     */
    public function __construct($host, $login, $pwd)
    {
        // Connect to remote
        $this->handle = ftp_connect($host);

        // Login
        if (!ftp_login($this->handle, $login, $pwd) !== false) {
            $this->log('Cannot login to remote server [##@##]', $login, $pwd);
        }

        // Switch to passive mode
        ftp_pasv($this->handle, true);

        // Get time difference
        $this->timeDiff = $this->getTimeDifference();
    }

    /**
     * Compare local file with remote file
     * @param string $fullPath Full local file path
     * @param int $maxAge File maximum possible age
     * @return bool True if file is old and must be updated
     */
    public function isOld($fullPath, $maxAge = 1)
    {
        // Read ftp file modification time and count age of file and check if it is valid
        return (filemtime($fullPath) - (ftp_mdtm($this->handle, basename($fullPath)) + $this->timeDiff)) > $maxAge;
    }

    /** Destructor */
    public function __destruct()
    {
        ftp_close($this->handle);
    }

    /**
     * Get time difference between servers
     * @return integer Time difference between servers
     */
    protected function getTimeDifference()
    {
        $diff = 0;

        // Create temp file
        $localPath = tempnam(sys_get_temp_dir(), 'test');

        $tsFileName = basename($localPath);

        // Copy file to remote
        if ($this->write($localPath)) {
            // Get difference
            $diff = abs(filemtime($localPath) - ftp_mdtm($this->handle, $tsFileName));

            // Convert to hours
            $diff = (integer)($diff > 3600 ? (floor($diff / 3600) * 3600 + $diff % 3600) : 0);

            $this->log('Time difference between servers is [##]', $diff);

            ftp_delete($this->handle, $tsFileName);
        }

        // Remove
        unlink($localPath);

        return $diff;
    }

    /**
     * Go one level up in directory
     */
    public function cdup()
    {
        ftp_cdup($this->handle);
    }

    /**
     * Change current remote folder
     * @param string $path Path to folder
     * @return bool False if we cannot change folder
     */
    public function cd($path)
    {
        // Go to root folder
        if (!ftp_chdir($this->handle, $path)) {
            return $this->log('Remote folder[##] not found', $path);
        }

        // Check if we can write there
        if (!$this->isWritable()) {
            return $this->log('Remote path [##] is not writable', $path);
        }

        $this->log('Switching to [##] folder', $path);

        // Store current folder
        $this->currentFolder = ftp_pwd($this->handle);

        return true;
    }

    /**
     * Try to write to remote
     * @return bool True if we can write to remote
     */
    public function isWritable()
    {
        // Create temp file
        $path = tempnam(sys_get_temp_dir(), 'test');

        // Get temp file name
        $fileName = basename($path);

        // Copy temp file to remote
        if (ftp_put($this->handle, $fileName, $path, FTP_ASCII)) {
            // Remove temp file
            ftp_delete($this->handle, $fileName);
            return true;
        }

        return false;
    }

    /**
     * Create remote directory and get into it
     * @param $path
     */
    public function mkDir($path)
    {
        // Try get into this dir, maybe it already there
        if (!@ftp_chdir($this->handle, $path)) {
            // Create dir
            ftp_mkdir($this->handle, $path);
            // Change rights
            ftp_chmod($this->handle, 0755, $path);
            // Go to it
            ftp_chdir($this->handle, $path);
        }
    }

    /**
     * Write remote file
     * @param string $fullPath Local file path
     * @return bool True if success
     */
    public function write($fullPath)
    {
        $fileName = basename($fullPath);

        $this->log('Uploading file [##]', $fullPath);

        // Copy file to remote
        if (ftp_put($this->handle, $fileName, $fullPath, FTP_BINARY)) {
            // Change rights
            ftp_chmod($this->handle, 0755, $fileName);

            $this->log('-- Success [##]', $fullPath);

            return true;
        } else {
            $this->log('-- Failed [##]', $fullPath);
        }

        return false;
    }
}
