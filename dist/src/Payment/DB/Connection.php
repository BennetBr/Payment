<?php

namespace Payment\DB;

/**
 * The Connection class provides static methods for working with the
 * payment database.
 */
abstract class Connection {

    private const CONF_PATH = ".conf/settings.ini";
    private static ?\PDO $instance = NULL;

    /**
     * Instantiates a PDO connection.
     * 
     * @throws \RuntimeException
     */
    public static function getInstance (): \PDO {
        if (static::$instance instanceof \PDO){
            return static::$instance;
        }
        
        $path = $_SERVER['DOCUMENT_ROOT'].static::CONF_PATH;
        if (file_exists($path) && $conf = parse_ini_file($path, true)){

            if (array_key_exists("DB Credentials", $conf)){
                $conf = $conf["DB Credentials"];
            }

            return static::$instance = new \PDO (
                $conf["dsn"],
                $conf["user"],
                $conf["password"]
            );
        }else{
            throw new \RuntimeException ("Could not find configuration file at \"$path\"!");
        }
    }
}