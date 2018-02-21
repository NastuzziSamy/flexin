<?php

include_once dirname(__FILE__) . '/' . 'permission_set.php';
include_once dirname(__FILE__) . '/' . '../utils/hash_utils.php';
include_once dirname(__FILE__) . '/' . 'user_identity_storage/user_identity_storage.php';

abstract class AbstractUserAuthorization
{
    /** @var UserIdentityStorage  */
    private $identityStorage;

    public function __construct(UserIdentityStorage $identityStorage = null) {
        $this->identityStorage = $identityStorage;
    }

    public function getIdentityStorage() {
        return $this->identityStorage;
    }

    /**
     * @return int
     */
    public abstract function GetCurrentUserId();

    /**
     * @return string|null
     */
    public function GetCurrentUser()
    {
        $identity = $this->identityStorage->getUserIdentity();
        if (is_null($identity)) {
            return 'guest';
        }

        return $identity->userName;
    }

    /**
     * @return bool
     */
    public abstract function IsCurrentUserLoggedIn();

    /**
     * @param string $userName
     * @param string $dataSourceName
     * @return IPermissionSet
     */
    public abstract function GetUserRoles($userName, $dataSourceName);

    /**
     * @param string $userName
     * @return bool
     */
    public abstract function HasAdminGrant($userName);

    /**
     * @param string $userName
     * @return bool
     */
    public abstract function HasAdminPanel($userName);

    /**
     * @param array $connectionOptions see GetGlobalConnectionOptions
     */
    public function ApplyIdentityToConnectionOptions(&$connectionOptions) { }
}

class NullUserAuthorization extends AbstractUserAuthorization
{
    public function GetCurrentUser()
    {
        return null;
    }

    public function GetUserRoles($userName, $dataSourceName)
    {
        return new AdminPermissionSet();
    }

    public function IsCurrentUserLoggedIn() {
        return false;
    }

    public function GetCurrentUserId()
    {
        return 0;
    }

    public function HasAdminGrant($userName)
    {
        return false;
    }

    public function HasAdminPanel($userName)
    {
        return false;
    }

    public function ApplyIdentityToConnectionOptions(&$connectionOptions) { }
}

abstract class IdentityCheckStrategy
{
    public function ApplyIdentityToConnectionOptions($connectionOptions) { }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public abstract function CheckUsernameAndPassword($username, $password);

    public abstract function CheckUsernameAndEncryptedPassword($username, $password);

    public abstract function GetEncryptedPassword($plainPassword);
}
