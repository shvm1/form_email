<?php
session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
    require_once(DR."/modules/func.php");
    require_once DR.'/classes/autoload.php';
    
    db_connect();
    $user = new User($_SESSION['user_id']);
    if(!$user->u_id) exit('endsession');
    

    $path = isset($_GET['p']) ? trim(substr($_GET['p'],0,255)) : '';
    $img = isset($_GET['i']) ? trim(substr($_GET['i'],0,255)) : '';

    $path = dconcat2($path,2);
    $path = trim($path,'/');

    if($path=='' || !file_exists(DR.'/'.$path)) $path = 'common/c'.$user->client->hc_id;

    Header("Content-type: text/plain; charset=utf-8");
    
    $ext_arr = Array("png","gif","jpg","jpe","jpeg","bmp","iff","tiff");
    $q = "";
    foreach($ext_arr as $i=>$v) {
        $q .= '*.'.$v.',';
    }

    $im_list = glob(DR."/".$path."/{".trim($q,",")."}",GLOB_BRACE);
    if(count($im_list)==0) {
        echo iconv('windows-1251','utf-8','[папка пуста]');
        exit();
    } else {
    natsort($im_list);
    foreach($im_list as $i=>$v) {
        $v = substr(strrchr($v,"/"),1);
        list($width,$height) = getimagesize(DR.'/'.$path.'/'.$v);
        $w = ''; $h = '';
        if($width>$height) $w = ' width="50px"'; else $h = ' height="45px"';
        echo iconv('windows-1251','utf-8','<div class="im_bl"><center><div><a href="/'.$path.'/'.$v.'" onClick="setIm(\'/'.$path.'/'.$v.'\'); ImageDialog.getImageData(); return false;" title="Создано: '.date("d.m.y H:i",filemtime(DR.'/'.$path.'/'.$v)).'"><img src="/'.$path.'/'.$v.'"'.$w.$h.'"></a></div><div class="im_title">'.$v.'</div><div class="im_size">'.$width.'x'.$height.' <a href="image.php?im='.$path.'/'.$v.'&a=del" title="Удалить">x</a></div></center></div> ');
    }
    }

    

?>