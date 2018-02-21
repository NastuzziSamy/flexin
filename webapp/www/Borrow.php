<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

    include_once dirname(__FILE__) . '/components/startup.php';
    include_once dirname(__FILE__) . '/components/application.php';


    include_once dirname(__FILE__) . '/' . 'database_engine/pgsql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page/page.php';
    include_once dirname(__FILE__) . '/' . 'components/page/detail_page.php';
    include_once dirname(__FILE__) . '/' . 'components/page/nested_form_page.php';


    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthorizationStrategy()->ApplyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    
    // OnBeforePageExecute event handler
    
    
    
    class Inventaire_BorrowPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Borrow"');
            $field = new IntegerField('ID_Person');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ID_Borrow', null, null, true);
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new IntegerField('ID_Equipment');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new TimeField('Borrowing_hour');
            $this->dataset->AddField($field, false);
            $field = new DateTimeField('Borrowing_date');
            $this->dataset->AddField($field, false);
            $field = new DateTimeField('return_date');
            $this->dataset->AddField($field, false);
            $field = new TimeField('Return_hour');
            $this->dataset->AddField($field, false);
            $this->dataset->AddLookupField('ID_Person', '"Inventaire"."Person"', new IntegerField('ID_Person', null, null, true), new StringField('Name', 'ID_Person_Name', 'ID_Person_Name_Inventaire_Person'), 'ID_Person_Name_Inventaire_Person');
            $this->dataset->AddLookupField('ID_Equipment', '"Inventaire"."Equipment"', new IntegerField('ID_Equipment', null, null, true), new StringField('Equipement_name', 'ID_Equipment_Equipement_name', 'ID_Equipment_Equipement_name_Inventaire_Equipment'), 'ID_Equipment_Equipement_name_Inventaire_Equipment');
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function setupCharts()
        {
    
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'ID_Person', 'ID_Person_Name', $this->RenderText('Person')),
                new FilterColumn($this->dataset, 'ID_Borrow', 'ID_Borrow', $this->RenderText('ID Borrow')),
                new FilterColumn($this->dataset, 'ID_Equipment', 'ID_Equipment_Equipement_name', $this->RenderText('ID Equipment')),
                new FilterColumn($this->dataset, 'Borrowing_hour', 'Borrowing_hour', $this->RenderText('Borrowing Hour')),
                new FilterColumn($this->dataset, 'Borrowing_date', 'Borrowing_date', $this->RenderText('Borrowing Date')),
                new FilterColumn($this->dataset, 'return_date', 'return_date', $this->RenderText('Return Date')),
                new FilterColumn($this->dataset, 'Return_hour', 'Return_hour', $this->RenderText('Return Hour'))
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['ID_Person'])
                ->addColumn($columns['ID_Borrow'])
                ->addColumn($columns['ID_Equipment'])
                ->addColumn($columns['Borrowing_hour'])
                ->addColumn($columns['Borrowing_date'])
                ->addColumn($columns['return_date'])
                ->addColumn($columns['Return_hour']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('ID_Person')
                ->setOptionsFor('ID_Equipment')
                ->setOptionsFor('Borrowing_date')
                ->setOptionsFor('return_date');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new AutocompleteComboBox('id_person_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_ID_Person_Name_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('ID_Person', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_ID_Person_Name_search');
            
            $text_editor = new TextEdit('ID_Person');
            
            $filterBuilder->addColumn(
                $columns['ID_Person'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $text_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $text_editor,
                    FilterConditionOperator::BEGINS_WITH => $text_editor,
                    FilterConditionOperator::ENDS_WITH => $text_editor,
                    FilterConditionOperator::IS_LIKE => $text_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $text_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('id_borrow_edit');
            
            $filterBuilder->addColumn(
                $columns['ID_Borrow'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new ComboBox('id_equipment_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $main_editor->SetAllowNullValue(false);
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Equipment"');
            $field = new IntegerField('ID_Equipment', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('Equipement_name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Description');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Containing');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Position');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Sub-category');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_State');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Reference');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Infratructure');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('CodeBarre');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Equipement_name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Equipment', 
                'ID_Equipment', 
                $main_editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            
            $editColumn->PrepareEditorControl();
            
            $multi_value_select_editor = new MultiValueSelect('ID_Equipment');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('ID_Equipment');
            
            $filterBuilder->addColumn(
                $columns['ID_Equipment'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $text_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $text_editor,
                    FilterConditionOperator::BEGINS_WITH => $text_editor,
                    FilterConditionOperator::ENDS_WITH => $text_editor,
                    FilterConditionOperator::IS_LIKE => $text_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $text_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TimeEdit('borrowing_hour_edit');
            
            $filterBuilder->addColumn(
                $columns['Borrowing_hour'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DateTimeEdit('borrowing_date_edit', false, 'Y-m-d H:i:s');
            
            $filterBuilder->addColumn(
                $columns['Borrowing_date'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::DATE_EQUALS => $main_editor,
                    FilterConditionOperator::DATE_DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::TODAY => null,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DateTimeEdit('return_date_edit', false, 'Y-m-d H:i:s');
            
            $filterBuilder->addColumn(
                $columns['return_date'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::DATE_EQUALS => $main_editor,
                    FilterConditionOperator::DATE_DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::TODAY => null,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TimeEdit('return_hour_edit');
            
            $filterBuilder->addColumn(
                $columns['Return_hour'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actions = $grid->getActions();
            $actions->setCaption($this->GetLocalizerCaptions()->GetMessageString('Actions'));
            $actions->setPosition(ActionList::POSITION_LEFT);
            
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
            
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $operation->SetAdditionalAttribute('data-modal-operation', 'delete');
                $operation->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
            
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Copy'), OPERATION_COPY, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Person_Name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for ID_Borrow field
            //
            $column = new NumberViewColumn('ID_Borrow', 'ID_Borrow', 'ID Borrow', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Borrowing_date field
            //
            $column = new DateTimeViewColumn('Borrowing_date', 'Borrowing_date', 'Borrowing Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Borrowing_hour field
            //
            $column = new DateTimeViewColumn('Borrowing_hour', 'Borrowing_hour', 'Borrowing Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for return_date field
            //
            $column = new DateTimeViewColumn('return_date', 'return_date', 'Return Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Return_hour field
            //
            $column = new DateTimeViewColumn('Return_hour', 'Return_hour', 'Return Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Person_Name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for ID_Borrow field
            //
            $column = new NumberViewColumn('ID_Borrow', 'ID_Borrow', 'ID Borrow', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Borrowing_hour field
            //
            $column = new DateTimeViewColumn('Borrowing_hour', 'Borrowing_hour', 'Borrowing Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Borrowing_date field
            //
            $column = new DateTimeViewColumn('Borrowing_date', 'Borrowing_date', 'Borrowing Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for return_date field
            //
            $column = new DateTimeViewColumn('return_date', 'return_date', 'Return Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Return_hour field
            //
            $column = new DateTimeViewColumn('Return_hour', 'Return_hour', 'Return Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for ID_Person field
            //
            $editor = new ComboBox('id_person_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Person"');
            $field = new StringField('Name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Email');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Person', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('First name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Phone');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Person', 
                'ID_Person', 
                $editor, 
                $this->dataset, 'ID_Person', 'Name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ID_Equipment field
            //
            $editor = new ComboBox('id_equipment_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Equipment"');
            $field = new IntegerField('ID_Equipment', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('Equipement_name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Description');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Containing');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Position');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Sub-category');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_State');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Reference');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Infratructure');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('CodeBarre');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Equipement_name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Equipment', 
                'ID_Equipment', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Borrowing_hour field
            //
            $editor = new TimeEdit('borrowing_hour_edit');
            $editColumn = new CustomEditColumn('Borrowing Hour', 'Borrowing_hour', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Borrowing_date field
            //
            $editor = new DateTimeEdit('borrowing_date_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Borrowing Date', 'Borrowing_date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for return_date field
            //
            $editor = new DateTimeEdit('return_date_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Return Date', 'return_date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Return_hour field
            //
            $editor = new TimeEdit('return_hour_edit');
            $editColumn = new CustomEditColumn('Return Hour', 'Return_hour', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for ID_Person field
            //
            $editor = new ComboBox('id_person_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Person"');
            $field = new StringField('Name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Email');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Person', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('First name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Phone');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'Person', 
                'ID_Person', 
                $editor, 
                $this->dataset, 'ID_Person', 'Name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ID_Equipment field
            //
            $editor = new ComboBox('id_equipment_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Equipment"');
            $field = new IntegerField('ID_Equipment', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('Equipement_name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Description');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Containing');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Position');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Sub-category');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_State');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Reference');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Infratructure');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('CodeBarre');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Equipement_name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Equipment', 
                'ID_Equipment', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Borrowing_hour field
            //
            $editor = new TimeEdit('borrowing_hour_edit');
            $editColumn = new CustomEditColumn('Borrowing Hour', 'Borrowing_hour', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Borrowing_date field
            //
            $editor = new DateTimeEdit('borrowing_date_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Borrowing Date', 'Borrowing_date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for return_date field
            //
            $editor = new DateTimeEdit('return_date_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Return Date', 'return_date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Return_hour field
            //
            $editor = new TimeEdit('return_hour_edit');
            $editColumn = new CustomEditColumn('Return Hour', 'Return_hour', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Person_Name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for ID_Borrow field
            //
            $column = new NumberViewColumn('ID_Borrow', 'ID_Borrow', 'ID Borrow', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Borrowing_hour field
            //
            $column = new DateTimeViewColumn('Borrowing_hour', 'Borrowing_hour', 'Borrowing Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Borrowing_date field
            //
            $column = new DateTimeViewColumn('Borrowing_date', 'Borrowing_date', 'Borrowing Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for return_date field
            //
            $column = new DateTimeViewColumn('return_date', 'return_date', 'Return Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Return_hour field
            //
            $column = new DateTimeViewColumn('Return_hour', 'Return_hour', 'Return Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Person_Name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for ID_Borrow field
            //
            $column = new NumberViewColumn('ID_Borrow', 'ID_Borrow', 'ID Borrow', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Borrowing_hour field
            //
            $column = new DateTimeViewColumn('Borrowing_hour', 'Borrowing_hour', 'Borrowing Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Borrowing_date field
            //
            $column = new DateTimeViewColumn('Borrowing_date', 'Borrowing_date', 'Borrowing Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for return_date field
            //
            $column = new DateTimeViewColumn('return_date', 'return_date', 'Return Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Return_hour field
            //
            $column = new DateTimeViewColumn('Return_hour', 'Return_hour', 'Return Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Person_Name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Borrowing_hour field
            //
            $column = new DateTimeViewColumn('Borrowing_hour', 'Borrowing_hour', 'Borrowing Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Borrowing_date field
            //
            $column = new DateTimeViewColumn('Borrowing_date', 'Borrowing_date', 'Borrowing Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for return_date field
            //
            $column = new DateTimeViewColumn('return_date', 'return_date', 'Return Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Return_hour field
            //
            $column = new DateTimeViewColumn('Return_hour', 'Return_hour', 'Return Hour', $this->dataset);
            $column->SetDateTimeFormat('H:i:s');
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
        }
    
        private function AddCompareHeaderColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        public function isFilterConditionRequired()
        {
            return false;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset);
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(true);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(true);
            $result->setAllowCompare(true);
            $this->AddCompareHeaderColumns($result);
            $this->AddCompareColumns($result);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
    
    
            $this->SetShowPageList(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
            $this->setPrintListAvailable(true);
            $this->setPrintListRecordAvailable(false);
            $this->setPrintOneRecordAvailable(true);
            $this->setExportListAvailable(array('excel','word','xml','csv','pdf'));
            $this->setExportListRecordAvailable(array());
            $this->setExportOneRecordAvailable(array('excel','word','xml','csv','pdf'));
    
            return $result;
        }
     
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_BorrowGrid_ID_Person_Name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_BorrowGrid_ID_Person_Name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_BorrowGrid_ID_Person_Name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Person"');
            $field = new StringField('Name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Email');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Person', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('First name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Phone');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Name', GetOrderTypeAsSQL(otAscending));
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), ''));
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_ID_Person_Name_search', 'ID_Person', 'Name', null);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'Person', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_BorrowGrid_ID_Person_Name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_BorrowGrid_ID_Equipment_Equipement_name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
        }
       
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderPrintColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderExportColumn($exportType, $fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomDrawRow($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr)
        {
    
        }
    
        protected function doExtendedCustomDrawRow($rowData, &$rowCellStyles, &$rowStyles, &$rowClasses, &$cellClasses)
        {
    
        }
    
        protected function doCustomRenderTotal($totalValue, $aggregate, $columnName, &$customText, &$handled)
        {
    
        }
    
        protected function doCustomCompareColumn($columnName, $valueA, $valueB, &$result)
        {
    
        }
    
        protected function doBeforeInsertRecord($page, &$rowData, &$cancel, &$message, &$messageDisplayTime, $tableName)
        {
    
        }
    
        protected function doBeforeUpdateRecord($page, &$rowData, &$cancel, &$message, &$messageDisplayTime, $tableName)
        {
    
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, &$cancel, &$message, &$messageDisplayTime, $tableName)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterUpdateRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterDeleteRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doCustomHTMLHeader($page, &$customHtmlHeaderText)
        { 
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doGetCustomExportOptions(Page $page, $exportType, $rowData, &$options)
        {
    
        }
    
        protected function doGetCustomUploadFileName($fieldName, $rowData, &$result, &$handled, $originalFileName, $originalFileExtension, $fileSize)
        {
    
        }
    
        protected function doPrepareChart(Chart $chart)
        {
    
        }
    
        protected function doPageLoaded()
        {
    
        }
    
        protected function doGetCustomPagePermissions(Page $page, PermissionSet &$permissions, &$handled)
        {
    
        }
    
        protected function doGetCustomRecordPermissions(Page $page, &$usingCondition, $rowData, &$allowEdit, &$allowDelete, &$mergeWithDefault, &$handled)
        {
    
        }
    
    }



    try
    {
        $Page = new Inventaire_BorrowPage("Inventaire_Borrow", "Borrow.php", GetCurrentUserGrantForDataSource("Inventaire.Borrow"), 'UTF-8');
        $Page->SetTitle('Borrow');
        $Page->SetMenuLabel('Borrow');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("Inventaire.Borrow"));
        GetApplication()->SetCanUserChangeOwnPassword(
            !function_exists('CanUserChangeOwnPassword') || CanUserChangeOwnPassword());
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
