<div class="input-group pgui-date-time-edit js-datetime-editor-wrap">
    <input <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "editors/editor_options.tpl", 'smarty_include_vars' => array('Editor' => $this->_tpl_vars['Editor'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            class="form-control"
            type="text"
            value="<?php echo $this->_tpl_vars['Editor']->GetValue(); ?>
"
            data-picker-format="HH:mm:ss"
            >
    <span class="input-group-addon" style="cursor: pointer">
        <span class="icon-time"></span>
    </span>
</div>