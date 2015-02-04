<?php
/** Create PHP ftp_* global functions mockups */
namespace samsonphp\deploy;

function ftp_connect($host)
{
    return $host = true;
}

function ftp_login($handle = true, $login, $pwd = '')
{
    if($login == 'test') {
        return $handle = true;
    }

    return false;
}

function ftp_pasv($handle = true, $bool = true)
{
    return $handle = true;
}

function ftp_mdtm($handle, $file)
{
    return ftp_login($handle, $file, $file);
}

function ftp_close($handle = true)
{
    return $handle = true;
}

function ftp_delete($handle, $file)
{
    return ftp_login($handle, $file, $file);
}

function ftp_cdup($handle = true)
{
    return $handle = true;
}

function ftp_chdir($handle, $dir)
{
    return ftp_login($handle, $dir, $dir);
}

function ftp_mkdir($handle, $dir)
{
    return ftp_login($handle, $dir, $dir);
}

function ftp_chmod($handle, $rights, $dir = true)
{
    return ftp_login($handle, $rights, $dir = $rights);
}

function ftp_put($handle, $fileName, $source, $mode = true)
{
    static $switch;

    if (!isset($switch) && strpos($source, sys_get_temp_dir()) !== false) {
        return $switch = !$switch;
    }

    return ftp_login($handle, $mode = $fileName, $source);
}

