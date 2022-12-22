<?php

namespace Smarty\Compiler;

/**
 * Smarty Internal Extension
 * This file contains the Smarty template extension to create a code frame
 * @author     Uwe Tews
 */

/**
 * Create code frame for compiled and cached templates
 */
class CodeFrame
{

	/**
	 * @var \Smarty\Template
	 */
	private $_template;

	public function __construct(\Smarty\Template $_template) {
		$this->_template = $_template;
	}

	/**
     * Create code frame for compiled and cached templates
     *
     * @param string                                $content   optional template content
     * @param string                                $functions compiled template function and block code
     * @param bool                                  $cache     flag for cache file
     * @param \Smarty\Compiler\Template $compiler
     *
     * @return string
     */
    public function create(
        $content = '',
        $functions = '',
        $cache = false,
        \Smarty\Compiler\Template $compiler = null
    ) {
        // build property code
        $properties[ 'version' ] = \Smarty::SMARTY_VERSION;
        $properties[ 'unifunc' ] = 'content_' . str_replace(array('.', ','), '_', uniqid('', true));
        if (!$cache) {
            $properties[ 'has_nocache_code' ] = $this->_template->compiled->has_nocache_code;
            $properties[ 'file_dependency' ] = $this->_template->compiled->file_dependency;
            $properties[ 'includes' ] = $this->_template->compiled->includes;
        } else {
            $properties[ 'has_nocache_code' ] = $this->_template->cached->has_nocache_code;
            $properties[ 'file_dependency' ] = $this->_template->cached->file_dependency;
            $properties[ 'cache_lifetime' ] = $this->_template->cache_lifetime;
        }
        $output = sprintf(
			"<?php\n/* Smarty version %s, created on %s\n  from '%s' */\n\n",
            $properties[ 'version' ],
	        date("Y-m-d H:i:s"),
	        str_replace('*/', '* /', $this->_template->source->filepath)
        );
        $output .= "/* @var \\Smarty\\Template \$_smarty_tpl */\n";
        $dec = "\$_smarty_tpl->_decodeProperties(\$_smarty_tpl, " . var_export($properties, true) . ',' .
               ($cache ? 'true' : 'false') . ')';
        $output .= "if ({$dec}) {\n";
        $output .= "function {$properties['unifunc']} (\\Smarty\\Template \$_smarty_tpl) {\n";
        if (!$cache && !empty($compiler->tpl_function)) {
            $output .= '$_smarty_tpl->smarty->getRuntime(\'TplFunction\')->registerTplFunctions($_smarty_tpl, ';
            $output .= var_export($compiler->tpl_function, true);
            $output .= ");\n";
        }
        if ($cache && $this->_template->smarty->hasRuntime('TplFunction')) {
            $output .= "\$_smarty_tpl->smarty->getRuntime('TplFunction')->registerTplFunctions(\$_smarty_tpl, " .
                       var_export($this->_template->smarty->getRuntime('TplFunction')->getTplFunction($this->_template), true) . ");\n";
        }
        $output .= "?>";
        $output .= $content;
        $output .= "<?php }\n?>";
        $output .= $functions;
        $output .= "<?php }\n";
        // remove unneeded PHP tags
        if (preg_match('/\s*\?>[\n]?<\?php\s*/', $output)) {
            $curr_split = preg_split(
                '/\s*\?>[\n]?<\?php\s*/',
                $output
            );
            preg_match_all(
                '/\s*\?>[\n]?<\?php\s*/',
                $output,
                $curr_parts
            );
            $output = '';
            foreach ($curr_split as $idx => $curr_output) {
                $output .= $curr_output;
                if (isset($curr_parts[ 0 ][ $idx ])) {
                    $output .= "\n";
                }
            }
        }
        if (preg_match('/\?>\s*$/', $output)) {
            $curr_split = preg_split(
                '/\?>\s*$/',
                $output
            );
            $output = '';
            foreach ($curr_split as $idx => $curr_output) {
                $output .= $curr_output;
            }
        }
        return $output;
    }
}