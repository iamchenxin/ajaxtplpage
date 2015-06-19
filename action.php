<?php
/**
 * Date: 2015/3/11
 * Time: 4:39
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'action.php');

class  action_plugin_ajaxtplpage extends DokuWiki_Action_Plugin
{
    function register(&$controller)
    {
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'set_data', array());
        $controller->register_hook('RPC_CALL_ADD', 'AFTER',  $this, 'add_rpc_all');
    }

    function set_data(){

    }

    function add_rpc_all(&$event, $param){
        $my_rpc_call=array(
            'tp.getUser' => array('ajaxtplpage', 'getUser'),
            'tp.pagef' => array('ajaxtplpage', 'pagef'),
            'tp.tpage' =>array('ajaxtplpage', 'genTplPage')
        );
        $event->data=array_merge($event->data,$my_rpc_call);
    }

}