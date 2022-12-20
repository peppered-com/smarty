<?php

/**
 * Smarty Method ClearAllAssign
 *
 * Smarty::clearAllAssign() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_ClearAllAssign
{
    /**
     * Valid for all objects
     *
     * @var int
     */
    public $objMap = 7;

    /**
     * clear all the assigned template variables.
     *
     * @api  Smarty::clearAllAssign()
     * @link https://www.smarty.net/docs/en/api.clear.all.assign.tpl
     *
     * @param \Smarty\Data|\Smarty_Internal_Template|\Smarty $data
     *
     * @return \Smarty\Data|\Smarty_Internal_Template|\Smarty
     */
    public function clearAllAssign(\Smarty\Data $data)
    {
        $data->tpl_vars = array();
        return $data;
    }
}