<?php if (( $this->_tpl_vars['Editor']->getPrefix() || $this->_tpl_vars['Editor']->getSuffix() )): ?>
    <div class="input-group">
<?php endif; ?>
<?php if ($this->_tpl_vars['Editor']->getPrefix()): ?>
    <span class="input-group-addon"><?php echo $this->_tpl_vars['Editor']->getPrefix(); ?>
</span>
<?php endif; ?>
<input
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "editors/editor_options.tpl", 'smarty_include_vars' => array('Editor' => $this->_tpl_vars['Editor'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    class="form-control"
    value="<?php echo $this->_tpl_vars['Editor']->GetHTMLValue(); ?>
"
    <?php if ($this->_tpl_vars['Editor']->getPlaceholder()): ?>
        placeholder="<?php echo $this->_tpl_vars['Editor']->getPlaceholder(); ?>
"
    <?php endif; ?>
    <?php if ($this->_tpl_vars['Editor']->GetPasswordMode()): ?>
        type="password"
    <?php else: ?>
        type="text"
    <?php endif; ?>
    <?php if ($this->_tpl_vars['Editor']->GetMaxLength()): ?>
        maxlength="<?php echo $this->_tpl_vars['Editor']->GetMaxLength(); ?>
"
    <?php endif; ?>
>
<?php if ($this->_tpl_vars['Editor']->getSuffix()): ?>
    <span class="input-group-addon"><?php echo $this->_tpl_vars['Editor']->getSuffix(); ?>
</span>
<?php endif; ?>
<?php if ($this->_tpl_vars['Editor']->getPrefix() || $this->_tpl_vars['Editor']->getSuffix()): ?>
    </div>
<?php endif; ?>