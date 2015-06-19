<?php
/**
 * Created by PhpStorm.
 * User: z97
 * Date: 15-6-3
 * Time: 下午9:14
 */
if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/../../');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC."inc/RemoteAPICore.php");
require_once(DOKU_INC."inc/remote.php");
require_once (DOKU_INC . 'inc/parserutils.php');

class remote_plugin_ajaxtplpage extends DokuWiki_Remote_Plugin {
    public function _getMethods() {
        return array(
            'test' => array(
                'args' => array('string'),
                'return' => 'string'
            ),
            'getUser' => array(
                'args' => array(),
                'return' => 'string'
            ),
        );
    }

    function test($msg){
        return "i am ajaxtplpage!! ($msg) .";
    }

    public function getUser(){
        global $INPUT;
        return $INPUT->server->str('REMOTE_USER') ;
    }

    public function pagef($id){
        $fn=wikiFN($id);
        $ex=@file_exists($fn);
        $rt=array(
            'file'=>$fn,
            'exist'=>$ex
        );
        return $rt;
    }

    protected function restr($str){
        /*
         *     $tpl = str_replace(
        array(
             '@ID@',
             '@NS@',
             '@FILE@',
             '@!FILE@',
             '@!FILE!@',
             '@PAGE@',
             '@!PAGE@',
             '@!!PAGE@',
             '@!PAGE!@',
             '@USER@',
             '@NAME@',
             '@MAIL@',
             '@DATE@',
        ),
        array(
             $id,
             getNS($id),
             $file,
             utf8_ucfirst($file),
             utf8_strtoupper($file),
             $page,
             utf8_ucfirst($page),
             utf8_ucwords($page),
             utf8_strtoupper($page),
             $INPUT->server->str('REMOTE_USER'),
             $USERINFO['name'],
             $USERINFO['mail'],
             $conf['dformat'],
        ), $tpl
            );
         */
        global $INPUT;
        global $conf;
        $str = str_replace(
            array(
              "@USER@",
                "@DATE@"
            ),
            array($INPUT->server->str('REMOTE_USER'),
                $conf['dformat']),
            $str
        );
        return $str;
    }

    protected function cpPage($src,$dst){

        $dst=$this->restr($dst);
        $dfn=wikiFN($dst);
        $dex=@file_exists($dfn);
        if($dex!=false){
            throw new RemoteException("tpllist dst file ($dst) exist");
        }

        $src=$this->restr($src);
        $sfn=wikiFN($src);
        $sex=@file_exists($sfn);
        if($sex==false){
            throw new RemoteException("tpllist src file ($src) do not exist");
        }

        $src_str=file_get_contents($sfn);
        $src_str=$this->restr($src_str);
        file_put_contents($dfn,$src_str);
        return "ok";
    }

    public function genTplPage($tpl_name){
        global $INPUT;
        $user =$INPUT->server->str('REMOTE_USER') ;
        if($user==null||count($user)<1){
            return "no ACL [$user]";
        }
        $tpl_txt=$this->getConf('tpllist');
        $rt=preg_match_all("/([\w:@]+)[ ]+([\w:@]+)[ ]+([\w:@]+)/",$tpl_txt,$matchs);
        if($rt==FALSE){
            throw new RemoteException("tpllist error");
        }
        $tpl_name_list=$matchs[1];
        for($i=0;$i<count($tpl_name_list);$i++){
            if($matchs[1][$i]==$tpl_name){
                $rt=$this->cpPage($matchs[2][$i],$matchs[3][$i]);
            }
        }
        return $rt;
    }

}