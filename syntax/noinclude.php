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

class syntax_plugin_wst_noinclude extends DokuWiki_Syntax_Plugin {
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
//    function getAllowedTypes() { return array('formatting', 'substition', 'disabled'); }  
    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<noinclude>|</noinclude>', $mode, 'plugin_wst_noinclude');
//        $this->Lexer->addEntryPattern('\{\{[W|w][S|s][T|t]:(?=.*\}\})', $mode, 'plugin_wst');
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
		return '';
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
//		$renderer->doc .= $renderer->render_text($data, 'xhtml');
        return true;
    }
}

