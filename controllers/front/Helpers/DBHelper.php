<?php

class DBHelper
{
	private const WEBSTRUM_TABLE_NAME = "webstrum_gallery_photos";
	private static function makeQuery($selectString, $whereFrom, $whereWhat, $asWhat = null, $limit = null) : string 
	{ 
		$asWhat = $asWhat != null ? " as ".$asWhat : "";
		$limit = $limit != null ? " LIMIT ".$limit : "";
        return "SELECT ".$selectString.$asWhat." FROM "._DB_PREFIX_.self::WEBSTRUM_TABLE_NAME." WHERE ".$whereFrom."=".$whereWhat.$limit;
    }
    public static function executeS($selectString, $whereFrom, $whereWhat, $asWhat = null, $limit = null)
    {
    	$query = self::makeQuery($selectString, $whereFrom, $whereWhat, $asWhat, $limit);
    	return Db::getInstance()->executeS($query);
    }
    public static function getValue($selectString, $whereFrom, $whereWhat, $asWhat = null)
    {
    	$query = self::makeQuery($selectString, $whereFrom, $whereWhat, $asWhat);
    	return Db::getInstance()->getValue($query);
    }
    public static function getRow($selectString, $whereFrom, $whereWhat, $asWhat = null)
    {
    	$query = self::makeQuery($selectString, $whereFrom, $whereWhat, $asWhat);
    	return Db::getInstance()->getRow($query);
    }
    public static function insert($data)
    {
    	Db::getInstance()->insert(self::WEBSTRUM_TABLE_NAME, $data);
    }
    public static function delete($from, $what)
    {
        Db::getInstance()->delete(self::WEBSTRUM_TABLE_NAME, $from.' = '.$what);    
    	
    }
    public static function createDBTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'webstrum_gallery_photos` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `product_id` int(10) NOT NULL,
        `photo_url` varchar(128) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $result = Db::getInstance()->execute($sql);
        return $result;
    }
}