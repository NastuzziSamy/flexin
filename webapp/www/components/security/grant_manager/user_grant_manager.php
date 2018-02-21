<?php

include_once dirname(__FILE__) . '/../permission_set.php';

abstract class UserGrantManager
{
    /**
     * @param string $userName
     * @param string $dataSourceName
     * @return IPermissionSet
     */
    public abstract function GetSecurityInfo($userName, $dataSourceName);

    /**
     * @abstract
     * @param string $userName
     * @return boolean
     */
    public abstract function HasAdminGrant($userName);

    /**
     * @abstract
     * @param string $userName
     * @return boolean
     */
    public abstract function HasAdminPanel($userName);

    /**
     * @abstract
     * @param string $userName
     * @return array
     */
    public abstract function getAdminDatasources($userName);
}
