<?php
/** Create PHP ftp_* global functions mockups */
namespace samsonphp\deploy;

function ftp_connect($host)
{
    return true;
}

function ftp_login($handle, $login, $pwd)
{
    if($login == 'test') {
        return true;
    }

    return false;
}

function ftp_pasv($handle, $bool)
{
    return true;
}

function ftp_mdtm($handle, $file)
{
    return ftp_login($handle, $file, $file);
}

function ftp_close($handle)
{
    return true;
}

function ftp_delete($handle, $file)
{
    return ftp_login($handle, $file, $file);
}

function ftp_cdup($handle)
{
    return true;
}

function ftp_chdir($handle, $dir)
{
    return ftp_login($handle, $dir, $dir);
}

function ftp_mkdir($handle, $dir)
{
    return ftp_login($handle, $dir, $dir);
}

function ftp_chmod($handle, $rights, $dir)
{
    return ftp_login($handle, $rights, $source);
}

function ftp_put($handle, $fileName, $source, $mode)
{
    if (strpos($source, sys_get_temp_dir()) !== false) {
        return true;
    }

    return ftp_login($handle, $fileName, $source);
}

