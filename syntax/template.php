<?php
/**
 * DokuWiki Plugin wst (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Vitalie Ciubotaru <vitalie@ciubotaru.tk>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

class syntax_plugin_wst_template extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';//maybe switch to 'container'
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'normal'; //?
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 320; // should go before Doku_Parser_Mode_media 320
    }
    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{[W|w][S|s][T|t]>(?:(?:[^\}]*?\{.*?\}\})|.*?)+?\}\}', $mode, 'plugin_wst_template');
    }

/**
    public function postConnect() {
        $this->Lexer->addExitPattern('\}\}', 'plugin_wst');
    }
**/
    /**
     * Handle matches of the wst syntax
     *
     * @param string          $match   The match of the syntax
     * @param int             $state   The state of the handler
     * @param int             $pos     The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler){
		if (empty($match)) return false;
        $template_arguments = array();
		$dump = trim(substr($match, 6, -2));     // remove curly brackets and "wst:" keyword
		$dump = preg_replace_callback('/\{\{(((?!(\{\{|\}\})).*?|(?R))*)\}\}/', function($match) {return str_replace('|', '{{!}}', $match[0]);}, $dump);
        $dump = explode('|', $dump);             // split template name and arguments
		$template_name = $dump[0];
		array_splice($dump, 0, 1); // leave only arguments (if any)
        if ($dump) {
			$template_arguments = array();
			foreach ($dump as $key => $value) {
				// cases with {{Template:X|key1=value1|key2=value2}}
				if (strpos($value, '=') !== false) {
					$tmp = explode("=", $value);
					$template_arguments[$tmp[0]] = $tmp[1];
				}
				// cases with {{Template:X|value1|value2}}, same as 1=value1
				// start from 1, not 0
				else $template_arguments[$key+1] = $value;
			}
		}
		$template_arguments = str_replace('{{!}}', '|', $template_arguments);
		$template = $this->get_template($template_name);
		if (!$template) return;
		$template_text = $this->replace_args($template, $template_arguments);
		return $template_text;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml') return false;
        if (!$data) return false;
		$renderer->doc .= $renderer->render_text($data, 'xhtml');
        return true;
    }

	function get_template($name) {
		/**
		 * by default, a page from namespace specified in $conf['namespace'] will be loaded
		 * To override this, prepend a colon to $name
		**/
/**
		if (substr($name, 0, 1) == ":") $template = rawWiki(substr($name, 1));
		else $template = rawWiki($this->getConf('namespace') . ":" . $name);
**/
		$template = rawWiki((substr($name, 0, 1) == ":") || ($this->getConf('namespace') == '') ? substr($name, 1) : $this->getConf('namespace') . ":" . $name);
		if (!$template) return false;
		$template = preg_replace('/<noinclude>.*?<\/noinclude>/s', '', $template);
		$template = preg_replace('/<includeonly>|<\/includeonly>/', '', $template);
		return $template;
	}

	function replace_args($template_text, $args) {
		$keys = array_keys($args);
		foreach ($keys as $key) {
			$template_text = str_replace('{{{' . $key . '}}}', $args[$key], $template_text);
		}
		// replace mising arguments with a placeholder
		$template_text = preg_replace('/\{\{\{.*?\}\}\}/', $this->getLang('missing_argument'), $template_text);
		return $template_text;
	}
}

