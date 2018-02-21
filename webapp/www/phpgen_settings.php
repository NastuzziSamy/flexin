<?php

//  define('SHOW_VARIABLES', 1);
//  define('DEBUG_LEVEL', 1);

//  error_reporting(E_ALL ^ E_NOTICE);
//  ini_set('display_errors', 'On');

set_include_path('.' . PATH_SEPARATOR . get_include_path());


include_once dirname(__FILE__) . '/' . 'components/utils/system_utils.php';

//  SystemUtils::DisableMagicQuotesRuntime();

SystemUtils::SetTimeZoneIfNeed('Europe/Belgrade');

function GetGlobalConnectionOptions()
{
    return array(
  'server' => 'localhost',
  'port' => '5432',
  'username' => 'postgres',
  'password' => 'LOU15mic',
  'database' => 'postgres',
  'client_encoding' => 'utf8'
);
}

function HasAdminPage()
{
    return false;
}

function HasHomePage()
{
    return true;
}

function GetHomeURL()
{
    return 'index.php';
}

function GetPageGroups()
{
    $result = array();
    $result[] = array('caption' => 'Default', 'description' => '');
    return $result;
}

function GetPageInfos()
{
    $result = array();
    $result[] = array('caption' => 'Reference', 'short_caption' => 'Reference', 'filename' => 'Reference.php', 'name' => 'Inventaire.Reference', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'Borrow', 'short_caption' => 'Borrow', 'filename' => 'Borrow.php', 'name' => 'Inventaire.Borrow', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'Category', 'short_caption' => 'Category', 'filename' => 'Category.php', 'name' => 'Inventaire.Category', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'State', 'short_caption' => 'State', 'filename' => 'State.php', 'name' => 'Inventaire.State', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'Person', 'short_caption' => 'Person', 'filename' => 'Person.php', 'name' => 'Inventaire.Person', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'Sub-Category', 'short_caption' => 'Sub-Category', 'filename' => 'Sub-Category.php', 'name' => 'Inventaire.Sub-Category', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'Bar code', 'short_caption' => 'Code Barre', 'filename' => 'btn_code_barre.php', 'name' => 'btn_code_barre', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'Equipment', 'short_caption' => 'Equipment', 'filename' => 'Equipment.php', 'name' => 'Inventaire.Equipment', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'Infrastructure', 'short_caption' => 'Infrastructure', 'filename' => 'Infrastructure.php', 'name' => 'Inventaire.Infrastructure', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    $result[] = array('caption' => 'Position', 'short_caption' => 'Position', 'filename' => 'Position.php', 'name' => 'Inventaire.Position', 'group_name' => 'Default', 'add_separator' => false, 'description' => '');
    return $result;
}

function GetPagesHeader()
{
    return
        '';
}

function GetPagesFooter()
{
    return
        ''; 
}

function ApplyCommonPageSettings(Page $page, Grid $grid)
{
    $page->SetShowUserAuthBar(false);
    $page->setShowNavigation(true);
    $page->OnCustomHTMLHeader->AddListener('Global_CustomHTMLHeaderHandler');
    $page->OnGetCustomTemplate->AddListener('Global_GetCustomTemplateHandler');
    $page->OnGetCustomExportOptions->AddListener('Global_OnGetCustomExportOptions');
    $page->getDataset()->OnGetFieldValue->AddListener('Global_OnGetFieldValue');
    $page->getDataset()->OnGetFieldValue->AddListener('OnGetFieldValue', $page);
    $grid->BeforeUpdateRecord->AddListener('Global_BeforeUpdateHandler');
    $grid->BeforeDeleteRecord->AddListener('Global_BeforeDeleteHandler');
    $grid->BeforeInsertRecord->AddListener('Global_BeforeInsertHandler');
    $grid->AfterUpdateRecord->AddListener('Global_AfterUpdateHandler');
    $grid->AfterDeleteRecord->AddListener('Global_AfterDeleteHandler');
    $grid->AfterInsertRecord->AddListener('Global_AfterInsertHandler');
}

/*
  Default code page: 1252
*/
function GetAnsiEncoding() { return 'windows-1252'; }

function Global_OnGetCustomPagePermissionsHandler(Page $page, PermissionSet &$permissions, &$handled)
{

}

function Global_CustomHTMLHeaderHandler($page, &$customHtmlHeaderText)
{

}

function Global_GetCustomTemplateHandler($type, $part, $mode, &$result, &$params, CommonPage $page = null)
{

}

function Global_OnGetCustomExportOptions($page, $exportType, $rowData, &$options)
{

}

function Global_OnGetFieldValue($fieldName, &$value, $tableName)
{

}

function Global_GetCustomPageList(CommonPage $page, PageList $pageList)
{

}

function Global_BeforeUpdateHandler($page, &$rowData, &$cancel, &$message, &$messageDisplayTime, $tableName)
{

}

function Global_BeforeDeleteHandler($page, &$rowData, &$cancel, &$message, &$messageDisplayTime, $tableName)
{

}

function Global_BeforeInsertHandler($page, &$rowData, &$cancel, &$message, &$messageDisplayTime, $tableName)
{

}

function Global_AfterUpdateHandler($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
{

}

function Global_AfterDeleteHandler($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
{

}

function Global_AfterInsertHandler($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
{

}

function GetDefaultDateFormat()
{
    return 'Y-m-d';
}

function GetFirstDayOfWeek()
{
    return 0;
}

function GetPageListType()
{
    return PageList::TYPE_MENU;
}

function GetNullLabel()
{
    return null;
}

function UseMinifiedJS()
{
    return true;
}



?>