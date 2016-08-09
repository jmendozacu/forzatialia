<?php
/**
 * Magento Booster 1.4+
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpagecache
 * @version      4.0.5
 * @license:     AACcewAJ3nZYMUsItZcwugZ3g4HsbQPMHWb0Pv6oyc
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
require_once(dirname(__FILE__).'/Abstract.php');
class Aitoc_Aitpagecache_Session_Mysql_Pdo_mysql extends Aitoc_Aitpagecache_Session_Mysql_Abstract
{
    /**
     * Opens connection to db
     */
    protected function openConnection()
    {
        $connection = $this->_config->global->resources->default_setup->connection;
        $dbName = (string)$connection->dbname;
        $host = (string)$connection->host;
        $user = (string)$connection->username;
        $password = (string)$connection->password;
        $this->_db = new PDO('mysql:dbname='.$dbName.';host='.$host, $user, $password);
    }

    /**
     * Reads session data from db
     *
     * @return string
     */
    protected function readSession()
    {
        $sql = 'SELECT `session_data` FROM `'.$this->_tableName.'` WHERE `session_id`=:session_id';
        $query = $this->_db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute(array(':session_id'=>$this->_sessionId));
        return $query->fetchColumn();
    }

    /**
     * Closes connection to db
     */
    protected function closeConnection()
    {
        $this->_db = null;
    }
}