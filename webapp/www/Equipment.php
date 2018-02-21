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
    
    
    
    class Inventaire_Equipment_Inventaire_BorrowPage extends DetailPage
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
                new FilterColumn($this->dataset, 'ID_Person', 'ID_Person_Name', $this->RenderText('ID Person')),
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
            $main_editor = new ComboBox('id_person_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $main_editor->SetAllowNullValue(false);
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
                'ID Person', 
                'ID_Person', 
                $main_editor, 
                $this->dataset, 'ID_Person', 'Name', $lookupDataset);
            
            $editColumn->PrepareEditorControl();
            
            $multi_value_select_editor = new MultiValueSelect('ID_Person');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
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
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_list');
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
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_list');
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
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_view');
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
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_view');
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
                'ID Person', 
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
                'ID Person', 
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
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_print');
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
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_print');
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
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_export');
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
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_export');
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
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_compare');
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
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for Name field
            //
            $column = new TextViewColumn('ID_Person', 'ID_Person_Name', 'ID Person', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Person_Name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('ID_Equipment', 'ID_Equipment_Equipement_name', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Borrow_ID_Equipment_Equipement_name_handler_view', $column);
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
    
    
    
    
    // OnBeforePageExecute event handler
    
    
    
    class Inventaire_Equipment_Inventaire_ContainsPage extends DetailPage
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Contains"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new IntegerField('id_Equipment_Contains');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('id_Equipment_Contents');
            $this->dataset->AddField($field, false);
            $this->dataset->AddLookupField('id_Equipment_Contains', '"Inventaire"."Equipment"', new IntegerField('ID_Equipment', null, null, true), new StringField('Equipement_name', 'id_Equipment_Contains_Equipement_name', 'id_Equipment_Contains_Equipement_name_Inventaire_Equipment'), 'id_Equipment_Contains_Equipement_name_Inventaire_Equipment');
            $this->dataset->AddLookupField('id_Equipment_Contents', '"Inventaire"."Equipment"', new IntegerField('ID_Equipment', null, null, true), new StringField('Equipement_name', 'id_Equipment_Contents_Equipement_name', 'id_Equipment_Contents_Equipement_name_Inventaire_Equipment'), 'id_Equipment_Contents_Equipement_name_Inventaire_Equipment');
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
                new FilterColumn($this->dataset, 'id', 'id', $this->RenderText('Id')),
                new FilterColumn($this->dataset, 'id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', $this->RenderText('Id Equipment Contains')),
                new FilterColumn($this->dataset, 'id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', $this->RenderText('Id Equipment Contents'))
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['id'])
                ->addColumn($columns['id_Equipment_Contains'])
                ->addColumn($columns['id_Equipment_Contents']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('id_Equipment_Contains')
                ->setOptionsFor('id_Equipment_Contents');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new TextEdit('id_edit');
            
            $filterBuilder->addColumn(
                $columns['id'],
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
            
            $main_editor = new ComboBox('id_equipment_contains_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contains', 
                'id_Equipment_Contains', 
                $main_editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            
            $editColumn->PrepareEditorControl();
            
            $multi_value_select_editor = new MultiValueSelect('id_Equipment_Contains');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('id_Equipment_Contains');
            
            $filterBuilder->addColumn(
                $columns['id_Equipment_Contains'],
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
            
            $main_editor = new ComboBox('id_equipment_contents_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contents', 
                'id_Equipment_Contents', 
                $main_editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            
            $editColumn->PrepareEditorControl();
            
            $multi_value_select_editor = new MultiValueSelect('id_Equipment_Contents');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('id_Equipment_Contents');
            
            $filterBuilder->addColumn(
                $columns['id_Equipment_Contents'],
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
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
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
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for id field
            //
            $editor = new TextEdit('id_edit');
            $editColumn = new CustomEditColumn('Id', 'id', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for id_Equipment_Contains field
            //
            $editor = new ComboBox('id_equipment_contains_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contains', 
                'id_Equipment_Contains', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for id_Equipment_Contents field
            //
            $editor = new ComboBox('id_equipment_contents_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contents', 
                'id_Equipment_Contents', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for id field
            //
            $editor = new TextEdit('id_edit');
            $editColumn = new CustomEditColumn('Id', 'id', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for id_Equipment_Contains field
            //
            $editor = new ComboBox('id_equipment_contains_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contains', 
                'id_Equipment_Contains', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for id_Equipment_Contents field
            //
            $editor = new ComboBox('id_equipment_contents_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contents', 
                'id_Equipment_Contents', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_print');
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_export');
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_compare');
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
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contains_Equipement_name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains_id_Equipment_Contents_Equipement_name_handler_view', $column);
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
    
    
    
    
    // OnBeforePageExecute event handler
    
    
    
    class Inventaire_Equipment_Inventaire_Contains01Page extends DetailPage
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Contains"');
            $field = new IntegerField('id');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new IntegerField('id_Equipment_Contains');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('id_Equipment_Contents');
            $this->dataset->AddField($field, false);
            $this->dataset->AddLookupField('id_Equipment_Contains', '"Inventaire"."Equipment"', new IntegerField('ID_Equipment', null, null, true), new StringField('Equipement_name', 'id_Equipment_Contains_Equipement_name', 'id_Equipment_Contains_Equipement_name_Inventaire_Equipment'), 'id_Equipment_Contains_Equipement_name_Inventaire_Equipment');
            $this->dataset->AddLookupField('id_Equipment_Contents', '"Inventaire"."Equipment"', new IntegerField('ID_Equipment', null, null, true), new StringField('Equipement_name', 'id_Equipment_Contents_Equipement_name', 'id_Equipment_Contents_Equipement_name_Inventaire_Equipment'), 'id_Equipment_Contents_Equipement_name_Inventaire_Equipment');
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
                new FilterColumn($this->dataset, 'id', 'id', $this->RenderText('Id')),
                new FilterColumn($this->dataset, 'id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', $this->RenderText('Id Equipment Contains')),
                new FilterColumn($this->dataset, 'id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', $this->RenderText('Id Equipment Contents'))
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['id'])
                ->addColumn($columns['id_Equipment_Contains'])
                ->addColumn($columns['id_Equipment_Contents']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('id_Equipment_Contains')
                ->setOptionsFor('id_Equipment_Contents');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new TextEdit('id_edit');
            
            $filterBuilder->addColumn(
                $columns['id'],
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
            
            $main_editor = new ComboBox('id_equipment_contains_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contains', 
                'id_Equipment_Contains', 
                $main_editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            
            $editColumn->PrepareEditorControl();
            
            $multi_value_select_editor = new MultiValueSelect('id_Equipment_Contains');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('id_Equipment_Contains');
            
            $filterBuilder->addColumn(
                $columns['id_Equipment_Contains'],
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
            
            $main_editor = new ComboBox('id_equipment_contents_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contents', 
                'id_Equipment_Contents', 
                $main_editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            
            $editColumn->PrepareEditorControl();
            
            $multi_value_select_editor = new MultiValueSelect('id_Equipment_Contents');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('id_Equipment_Contents');
            
            $filterBuilder->addColumn(
                $columns['id_Equipment_Contents'],
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
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
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
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for id field
            //
            $editor = new TextEdit('id_edit');
            $editColumn = new CustomEditColumn('Id', 'id', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for id_Equipment_Contains field
            //
            $editor = new ComboBox('id_equipment_contains_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contains', 
                'id_Equipment_Contains', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for id_Equipment_Contents field
            //
            $editor = new ComboBox('id_equipment_contents_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contents', 
                'id_Equipment_Contents', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for id field
            //
            $editor = new TextEdit('id_edit');
            $editColumn = new CustomEditColumn('Id', 'id', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for id_Equipment_Contains field
            //
            $editor = new ComboBox('id_equipment_contains_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contains', 
                'id_Equipment_Contains', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for id_Equipment_Contents field
            //
            $editor = new ComboBox('id_equipment_contents_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
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
                'Id Equipment Contents', 
                'id_Equipment_Contents', 
                $editor, 
                $this->dataset, 'ID_Equipment', 'Equipement_name', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_print');
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_export');
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new NumberViewColumn('id', 'id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_compare');
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
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contains', 'id_Equipment_Contains_Equipement_name', 'Id Equipment Contains', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contains_Equipement_name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('id_Equipment_Contents', 'id_Equipment_Contents_Equipement_name', 'Id Equipment Contents', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'DetailGridInventaire.Equipment.Inventaire.Contains01_id_Equipment_Contents_Equipement_name_handler_view', $column);
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
    
    // OnBeforePageExecute event handler
    
    
    
    class Inventaire_EquipmentPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Equipment"');
            $field = new IntegerField('ID_Equipment', null, null, true);
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new StringField('Equipement_name');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new StringField('Description');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ID_Containing');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ID_Position');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ID_Sub-category');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ID_State');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ID_Reference');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ID_Infratructure');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new StringField('CodeBarre');
            $this->dataset->AddField($field, false);
            $this->dataset->AddLookupField('ID_Position', '"Inventaire"."Position"', new IntegerField('ID_Position', null, null, true), new IntegerField('ID_Position', 'ID_Position_ID_Position', 'ID_Position_ID_Position_Inventaire_Position', true), 'ID_Position_ID_Position_Inventaire_Position');
            $this->dataset->AddLookupField('ID_Sub-category', '"Inventaire"."Sub-Category"', new IntegerField('ID_Sub-category', null, null, true), new StringField('Sub-category_Name', 'ID_Sub-category_Sub-category_Name', 'ID_Sub-category_Sub-category_Name_Inventaire_Sub-Category'), 'ID_Sub-category_Sub-category_Name_Inventaire_Sub-Category');
            $this->dataset->AddLookupField('ID_State', '"Inventaire"."State"', new IntegerField('ID_State', null, null, true), new StringField('State_name', 'ID_State_State_name', 'ID_State_State_name_Inventaire_State'), 'ID_State_State_name_Inventaire_State');
            $this->dataset->AddLookupField('ID_Reference', '"Inventaire"."Reference"', new IntegerField('ID_Reference', null, null, true), new StringField('entitled', 'ID_Reference_entitled', 'ID_Reference_entitled_Inventaire_Reference'), 'ID_Reference_entitled_Inventaire_Reference');
            $this->dataset->AddLookupField('ID_Infratructure', '"Inventaire"."Infrastructure"', new IntegerField('ID_Infrastructure', null, null, true), new StringField('Infrastructure_name', 'ID_Infratructure_Infrastructure_name', 'ID_Infratructure_Infrastructure_name_Inventaire_Infrastructure'), 'ID_Infratructure_Infrastructure_name_Inventaire_Infrastructure');
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
                new FilterColumn($this->dataset, 'ID_Equipment', 'ID_Equipment', $this->RenderText('ID Equipment')),
                new FilterColumn($this->dataset, 'Equipement_name', 'Equipement_name', $this->RenderText('Equipement Name')),
                new FilterColumn($this->dataset, 'Description', 'Description', $this->RenderText('Description')),
                new FilterColumn($this->dataset, 'ID_Containing', 'ID_Containing', $this->RenderText('ID Containing')),
                new FilterColumn($this->dataset, 'ID_Position', 'ID_Position_ID_Position', $this->RenderText('ID Position')),
                new FilterColumn($this->dataset, 'ID_Sub-category', 'ID_Sub-category_Sub-category_Name', $this->RenderText('ID Sub-category')),
                new FilterColumn($this->dataset, 'ID_State', 'ID_State_State_name', $this->RenderText('ID State')),
                new FilterColumn($this->dataset, 'ID_Reference', 'ID_Reference_entitled', $this->RenderText('ID Reference')),
                new FilterColumn($this->dataset, 'ID_Infratructure', 'ID_Infratructure_Infrastructure_name', $this->RenderText('ID Infratructure')),
                new FilterColumn($this->dataset, 'CodeBarre', 'CodeBarre', $this->RenderText('Code Barre'))
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['ID_Equipment'])
                ->addColumn($columns['Equipement_name'])
                ->addColumn($columns['Description'])
                ->addColumn($columns['ID_Containing'])
                ->addColumn($columns['ID_Position'])
                ->addColumn($columns['ID_Sub-category'])
                ->addColumn($columns['ID_State'])
                ->addColumn($columns['ID_Reference'])
                ->addColumn($columns['ID_Infratructure'])
                ->addColumn($columns['CodeBarre']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('ID_Position')
                ->setOptionsFor('ID_Sub-category')
                ->setOptionsFor('ID_State')
                ->setOptionsFor('ID_Reference')
                ->setOptionsFor('ID_Infratructure')
                ->setOptionsFor('CodeBarre');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new TextEdit('id_equipment_edit');
            
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
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('equipement_name_edit');
            
            $filterBuilder->addColumn(
                $columns['Equipement_name'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('Description');
            
            $filterBuilder->addColumn(
                $columns['Description'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new ComboBox('id_containing_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $main_editor->SetAllowNullValue(false);
            
            $multi_value_select_editor = new MultiValueSelect('ID_Containing');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $filterBuilder->addColumn(
                $columns['ID_Containing'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new AutocompleteComboBox('id_position_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_ID_Position_ID_Position_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('ID_Position', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_ID_Position_ID_Position_search');
            
            $filterBuilder->addColumn(
                $columns['ID_Position'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new AutocompleteComboBox('id_sub-category_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_ID_Sub-category_Sub-category_Name_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('ID_Sub-category', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_ID_Sub-category_Sub-category_Name_search');
            
            $text_editor = new TextEdit('ID_Sub-category');
            
            $filterBuilder->addColumn(
                $columns['ID_Sub-category'],
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
            
            $main_editor = new AutocompleteComboBox('id_state_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_ID_State_State_name_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('ID_State', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_ID_State_State_name_search');
            
            $text_editor = new TextEdit('ID_State');
            
            $filterBuilder->addColumn(
                $columns['ID_State'],
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
            
            $main_editor = new ComboBox('id_reference_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $main_editor->SetAllowNullValue(false);
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Reference"');
            $field = new StringField('entitled');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Reference', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $lookupDataset->setOrderByField('entitled', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Reference', 
                'ID_Reference', 
                $main_editor, 
                $this->dataset, 'ID_Reference', 'entitled', $lookupDataset);
            
            $editColumn->PrepareEditorControl();
            
            $multi_value_select_editor = new MultiValueSelect('ID_Reference');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('ID_Reference');
            
            $filterBuilder->addColumn(
                $columns['ID_Reference'],
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
            
            $main_editor = new AutocompleteComboBox('id_infratructure_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_ID_Infratructure_Infrastructure_name_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('ID_Infratructure', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_ID_Infratructure_Infrastructure_name_search');
            
            $text_editor = new TextEdit('ID_Infratructure');
            
            $filterBuilder->addColumn(
                $columns['ID_Infratructure'],
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
            
            $main_editor = new TextEdit('codebarre_edit');
            
            $filterBuilder->addColumn(
                $columns['CodeBarre'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
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
            if (GetCurrentUserGrantForDataSource('Inventaire.Equipment.Inventaire.Borrow')->HasViewGrant() && $withDetails)
            {
            //
            // View column for Inventaire_Equipment_Inventaire_Borrow detail
            //
            $column = new DetailColumn(array('ID_Equipment'), 'Inventaire.Equipment.Inventaire.Borrow', 'Inventaire_Equipment_Inventaire_Borrow_handler', $this->dataset, 'Borrow');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $grid->AddViewColumn($column);
            }
            
            if (GetCurrentUserGrantForDataSource('Inventaire.Equipment.Inventaire.Contains')->HasViewGrant() && $withDetails)
            {
            //
            // View column for Inventaire_Equipment_Inventaire_Contains detail
            //
            $column = new DetailColumn(array('ID_Equipment'), 'Inventaire.Equipment.Inventaire.Contains', 'Inventaire_Equipment_Inventaire_Contains_handler', $this->dataset, 'Contains');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $grid->AddViewColumn($column);
            }
            
            if (GetCurrentUserGrantForDataSource('Inventaire.Equipment.Inventaire.Contains01')->HasViewGrant() && $withDetails)
            {
            //
            // View column for Inventaire_Equipment_Inventaire_Contains01 detail
            //
            $column = new DetailColumn(array('ID_Equipment'), 'Inventaire.Equipment.Inventaire.Contains01', 'Inventaire_Equipment_Inventaire_Contains01_handler', $this->dataset, 'Contains');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $grid->AddViewColumn($column);
            }
            
            //
            // View column for ID_Equipment field
            //
            $column = new NumberViewColumn('ID_Equipment', 'ID_Equipment', 'ID Equipment', $this->dataset);
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
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Equipement_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Description_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for ID_Containing field
            //
            $column = new NumberViewColumn('ID_Containing', 'ID_Containing', 'ID Containing', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for ID_Position field
            //
            $column = new NumberViewColumn('ID_Position', 'ID_Position_ID_Position', 'ID Position', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('.');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_State_State_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Reference_entitled_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_CodeBarre_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for ID_Equipment field
            //
            $column = new NumberViewColumn('ID_Equipment', 'ID_Equipment', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Equipement_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Description_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for ID_Containing field
            //
            $column = new NumberViewColumn('ID_Containing', 'ID_Containing', 'ID Containing', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for ID_Position field
            //
            $column = new NumberViewColumn('ID_Position', 'ID_Position_ID_Position', 'ID Position', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('.');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_State_State_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Reference_entitled_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_CodeBarre_handler_view');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for Equipement_name field
            //
            $editor = new TextEdit('equipement_name_edit');
            $editColumn = new CustomEditColumn('Equipement Name', 'Equipement_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Description field
            //
            $editor = new TextAreaEdit('description_edit', 50, 8);
            $editColumn = new CustomEditColumn('Description', 'Description', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ID_Containing field
            //
            $editor = new ComboBox('id_containing_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editColumn = new CustomEditColumn('ID Containing', 'ID_Containing', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ID_Position field
            //
            $editor = new ComboBox('id_position_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Position"');
            $field = new IntegerField('ID_Position', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new DateField('date');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('longitude');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('latitude');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Place');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('ID_Position', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Position', 
                'ID_Position', 
                $editor, 
                $this->dataset, 'ID_Position', 'ID_Position', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ID_Sub-category field
            //
            $editor = new ComboBox('id_sub-category_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Sub-Category"');
            $field = new StringField('Sub-category_Name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Sub-category', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new IntegerField('ID_Category');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Sub-category_Name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Sub-category', 
                'ID_Sub-category', 
                $editor, 
                $this->dataset, 'ID_Sub-category', 'Sub-category_Name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ID_State field
            //
            $editor = new ComboBox('id_state_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."State"');
            $field = new IntegerField('ID_State', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('State_name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('State_name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID State', 
                'ID_State', 
                $editor, 
                $this->dataset, 'ID_State', 'State_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ID_Reference field
            //
            $editor = new ComboBox('id_reference_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Reference"');
            $field = new StringField('entitled');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Reference', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $lookupDataset->setOrderByField('entitled', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Reference', 
                'ID_Reference', 
                $editor, 
                $this->dataset, 'ID_Reference', 'entitled', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ID_Infratructure field
            //
            $editor = new ComboBox('id_infratructure_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Infrastructure"');
            $field = new IntegerField('ID_Infrastructure', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('Infrastructure_name');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Position');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Infrastructure_name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Infratructure', 
                'ID_Infratructure', 
                $editor, 
                $this->dataset, 'ID_Infrastructure', 'Infrastructure_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for CodeBarre field
            //
            $editor = new TextEdit('codebarre_edit');
            $editColumn = new CustomEditColumn('Code Barre', 'CodeBarre', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for Equipement_name field
            //
            $editor = new TextEdit('equipement_name_edit');
            $editColumn = new CustomEditColumn('Equipement Name', 'Equipement_name', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Description field
            //
            $editor = new TextAreaEdit('description_edit', 50, 8);
            $editColumn = new CustomEditColumn('Description', 'Description', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ID_Containing field
            //
            $editor = new ComboBox('id_containing_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editColumn = new CustomEditColumn('ID Containing', 'ID_Containing', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ID_Position field
            //
            $editor = new ComboBox('id_position_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Position"');
            $field = new IntegerField('ID_Position', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new DateField('date');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('longitude');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('latitude');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Place');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('ID_Position', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Position', 
                'ID_Position', 
                $editor, 
                $this->dataset, 'ID_Position', 'ID_Position', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ID_Sub-category field
            //
            $editor = new ComboBox('id_sub-category_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Sub-Category"');
            $field = new StringField('Sub-category_Name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Sub-category', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new IntegerField('ID_Category');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Sub-category_Name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Sub-category', 
                'ID_Sub-category', 
                $editor, 
                $this->dataset, 'ID_Sub-category', 'Sub-category_Name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ID_State field
            //
            $editor = new ComboBox('id_state_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."State"');
            $field = new IntegerField('ID_State', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('State_name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('State_name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID State', 
                'ID_State', 
                $editor, 
                $this->dataset, 'ID_State', 'State_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ID_Reference field
            //
            $editor = new ComboBox('id_reference_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Reference"');
            $field = new StringField('entitled');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Reference', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $lookupDataset->setOrderByField('entitled', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Reference', 
                'ID_Reference', 
                $editor, 
                $this->dataset, 'ID_Reference', 'entitled', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ID_Infratructure field
            //
            $editor = new ComboBox('id_infratructure_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Infrastructure"');
            $field = new IntegerField('ID_Infrastructure', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('Infrastructure_name');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Position');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Infrastructure_name', GetOrderTypeAsSQL(otAscending));
            $editColumn = new LookUpEditColumn(
                'ID Infratructure', 
                'ID_Infratructure', 
                $editor, 
                $this->dataset, 'ID_Infrastructure', 'Infrastructure_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for CodeBarre field
            //
            $editor = new TextEdit('codebarre_edit');
            $editColumn = new CustomEditColumn('Code Barre', 'CodeBarre', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for ID_Equipment field
            //
            $column = new NumberViewColumn('ID_Equipment', 'ID_Equipment', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Equipement_name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Description_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for ID_Containing field
            //
            $column = new NumberViewColumn('ID_Containing', 'ID_Containing', 'ID Containing', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for ID_Position field
            //
            $column = new NumberViewColumn('ID_Position', 'ID_Position_ID_Position', 'ID Position', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('.');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_State_State_name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Reference_entitled_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_CodeBarre_handler_print');
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for ID_Equipment field
            //
            $column = new NumberViewColumn('ID_Equipment', 'ID_Equipment', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Equipement_name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Description_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for ID_Containing field
            //
            $column = new NumberViewColumn('ID_Containing', 'ID_Containing', 'ID Containing', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for ID_Position field
            //
            $column = new NumberViewColumn('ID_Position', 'ID_Position_ID_Position', 'ID Position', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('.');
            $grid->AddExportColumn($column);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_State_State_name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Reference_entitled_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_CodeBarre_handler_export');
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for ID_Equipment field
            //
            $column = new NumberViewColumn('ID_Equipment', 'ID_Equipment', 'ID Equipment', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Equipement_name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_Description_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for ID_Containing field
            //
            $column = new NumberViewColumn('ID_Containing', 'ID_Containing', 'ID Containing', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for ID_Position field
            //
            $column = new NumberViewColumn('ID_Position', 'ID_Position_ID_Position', 'ID Position', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('.');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_State_State_name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Reference_entitled_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('Inventaire_EquipmentGrid_CodeBarre_handler_compare');
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
    
        function CreateMasterDetailRecordGrid()
        {
            $result = new Grid($this, $this->dataset);
            
            $this->AddFieldColumns($result, false);
            $this->AddPrintColumns($result);
            
            $result->SetAllowDeleteSelected(false);
            $result->SetShowUpdateLink(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(false);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            
            $this->setupGridColumnGroup($result);
            $this->attachGridEventHandlers($result);
            
            return $result;
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
            $detailPage = new Inventaire_Equipment_Inventaire_BorrowPage('Inventaire_Equipment_Inventaire_Borrow', $this, array('ID_Equipment'), array('ID_Equipment'), $this->GetForeignKeyFields(), $this->CreateMasterDetailRecordGrid(), $this->dataset, GetCurrentUserGrantForDataSource('Inventaire.Equipment.Inventaire.Borrow'), 'UTF-8');
            $detailPage->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource('Inventaire.Equipment.Inventaire.Borrow'));
            $detailPage->SetTitle('Borrow');
            $detailPage->SetMenuLabel('Borrow');
            $detailPage->SetHeader(GetPagesHeader());
            $detailPage->SetFooter(GetPagesFooter());
            $detailPage->SetHttpHandlerName('Inventaire_Equipment_Inventaire_Borrow_handler');
            $handler = new PageHTTPHandler('Inventaire_Equipment_Inventaire_Borrow_handler', $detailPage);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $detailPage = new Inventaire_Equipment_Inventaire_ContainsPage('Inventaire_Equipment_Inventaire_Contains', $this, array('id_Equipment_Contains'), array('ID_Equipment'), $this->GetForeignKeyFields(), $this->CreateMasterDetailRecordGrid(), $this->dataset, GetCurrentUserGrantForDataSource('Inventaire.Equipment.Inventaire.Contains'), 'UTF-8');
            $detailPage->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource('Inventaire.Equipment.Inventaire.Contains'));
            $detailPage->SetTitle('Contains');
            $detailPage->SetMenuLabel('Contains');
            $detailPage->SetHeader(GetPagesHeader());
            $detailPage->SetFooter(GetPagesFooter());
            $detailPage->SetHttpHandlerName('Inventaire_Equipment_Inventaire_Contains_handler');
            $handler = new PageHTTPHandler('Inventaire_Equipment_Inventaire_Contains_handler', $detailPage);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $detailPage = new Inventaire_Equipment_Inventaire_Contains01Page('Inventaire_Equipment_Inventaire_Contains01', $this, array('id_Equipment_Contents'), array('ID_Equipment'), $this->GetForeignKeyFields(), $this->CreateMasterDetailRecordGrid(), $this->dataset, GetCurrentUserGrantForDataSource('Inventaire.Equipment.Inventaire.Contains01'), 'UTF-8');
            $detailPage->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource('Inventaire.Equipment.Inventaire.Contains01'));
            $detailPage->SetTitle('Contains');
            $detailPage->SetMenuLabel('Contains');
            $detailPage->SetHeader(GetPagesHeader());
            $detailPage->SetFooter(GetPagesFooter());
            $detailPage->SetHttpHandlerName('Inventaire_Equipment_Inventaire_Contains01_handler');
            $handler = new PageHTTPHandler('Inventaire_Equipment_Inventaire_Contains01_handler', $detailPage);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_Equipement_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_Description_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_State_State_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Reference_entitled_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_CodeBarre_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_Equipement_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_Description_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_State_State_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Reference_entitled_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_CodeBarre_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_Equipement_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_Description_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_State_State_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Reference_entitled_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_CodeBarre_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Position"');
            $field = new IntegerField('ID_Position', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new DateField('date');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('longitude');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('latitude');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new StringField('Place');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('ID_Position', GetOrderTypeAsSQL(otAscending));
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), ''));
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_ID_Position_ID_Position_search', 'ID_Position', 'ID_Position', null);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Sub-Category"');
            $field = new StringField('Sub-category_Name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Sub-category', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new IntegerField('ID_Category');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Sub-category_Name', GetOrderTypeAsSQL(otAscending));
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), ''));
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_ID_Sub-category_Sub-category_Name_search', 'ID_Sub-category', 'Sub-category_Name', null);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."State"');
            $field = new IntegerField('ID_State', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('State_name');
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('State_name', GetOrderTypeAsSQL(otAscending));
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), ''));
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_ID_State_State_name_search', 'ID_State', 'State_name', null);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                PgConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"Inventaire"."Infrastructure"');
            $field = new IntegerField('ID_Infrastructure', null, null, true);
            $field->SetIsNotNull(true);
            $lookupDataset->AddField($field, true);
            $field = new StringField('Infrastructure_name');
            $lookupDataset->AddField($field, false);
            $field = new IntegerField('ID_Position');
            $lookupDataset->AddField($field, false);
            $lookupDataset->setOrderByField('Infrastructure_name', GetOrderTypeAsSQL(otAscending));
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), ''));
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_ID_Infratructure_Infrastructure_name_search', 'ID_Infrastructure', 'Infrastructure_name', null);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Equipement_name field
            //
            $column = new TextViewColumn('Equipement_name', 'Equipement_name', 'Equipement Name', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_Equipement_name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Description field
            //
            $column = new TextViewColumn('Description', 'Description', 'Description', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_Description_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Sub-category_Name field
            //
            $column = new TextViewColumn('ID_Sub-category', 'ID_Sub-category_Sub-category_Name', 'ID Sub-category', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Sub-category_Sub-category_Name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for State_name field
            //
            $column = new TextViewColumn('ID_State', 'ID_State_State_name', 'ID State', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_State_State_name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for entitled field
            //
            $column = new TextViewColumn('ID_Reference', 'ID_Reference_entitled', 'ID Reference', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Reference_entitled_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for Infrastructure_name field
            //
            $column = new TextViewColumn('ID_Infratructure', 'ID_Infratructure_Infrastructure_name', 'ID Infratructure', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_ID_Infratructure_Infrastructure_name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for CodeBarre field
            //
            $column = new TextViewColumn('CodeBarre', 'CodeBarre', 'Code Barre', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'Inventaire_EquipmentGrid_CodeBarre_handler_view', $column);
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
        $Page = new Inventaire_EquipmentPage("Inventaire_Equipment", "Equipment.php", GetCurrentUserGrantForDataSource("Inventaire.Equipment"), 'UTF-8');
        $Page->SetTitle('Equipment');
        $Page->SetMenuLabel('Equipment');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("Inventaire.Equipment"));
        GetApplication()->SetCanUserChangeOwnPassword(
            !function_exists('CanUserChangeOwnPassword') || CanUserChangeOwnPassword());
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
