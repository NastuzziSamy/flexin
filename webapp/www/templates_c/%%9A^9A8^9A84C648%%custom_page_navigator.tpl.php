<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escapeurl', 'list/custom_page_navigator.tpl', 8, false),)), $this); ?>
<div class="pgui-pagination">
    <ul class="pagination"><?php echo '<li><a>'; ?><?php echo $this->_tpl_vars['PageNavigator']->GetCaption(); ?><?php echo ':</a></li>'; ?>
<?php $_from = $this->_tpl_vars['PageNavigatorPages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['PageNavigatorPage']):
?><?php echo '<li '; ?><?php if ($this->_tpl_vars['PageNavigatorPage']->IsCurrent()): ?><?php echo 'class="active"'; ?><?php endif; ?><?php echo '><a href="'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['PageNavigatorPage']->GetPageLink())) ? $this->_run_mod_handler('escapeurl', true, $_tmp) : smarty_modifier_escapeurl($_tmp)); ?><?php echo '">'; ?><?php echo $this->_tpl_vars['PageNavigatorPage']->GetPageCaption(); ?><?php echo '</a></li>'; ?>
<?php endforeach; endif; unset($_from); ?>
    </ul>
</div>
