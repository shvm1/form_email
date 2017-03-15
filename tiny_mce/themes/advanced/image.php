<?php
session_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
    require_once(DR."/modules/func.php");
    require_once DR.'/classes/autoload.php';
    
    db_connect();
    $user = new User($_SESSION['user_id']);
    if(!$user->u_id) exit('<script>top.window.location.reload(true);</script>');
    //$path = DR."common/c".$user->client->hc_id;
    
    $path = isset($_REQUEST['im_folder']) ? trim(substr($_REQUEST['im_folder'],0,255),'/') : '';
    $path1 = $path;
    $path1 = str_replace('..','.',$path1);
    $path1 = str_replace('//','/',$path1);
    if($path=='' || $path1!=$path || substr($path,0,6)!="common" || !is_dir(DR.'/'.$path) || !file_exists(DR.'/'.$path)) { $path1 = ''; $path = DR."/common/c".$user->client->hc_id; } else $path = DR.'/'.trim($path,'/');
    
    $path_len = strlen($path);
    $ext_arr = Array("png","gif","jpg","jpe","jpeg","bmp","iff","tiff");
    if(!file_exists($path)) @mkdir($path, 0777);
    if(!is_writable($path)) @chmod($path, 0777);

    

    // config
    $res = mysql_query("select * from ".PRX."_config");
    if(issquery($res)) {
       while($row = mfa($res)) {
          $row['c_value'] = str_replace("{DOCUMENT_ROOT}",$_SERVER['DOCUMENT_ROOT'].$conf[1],$row['c_value']);
          define($row['c_name'],$row['c_value']);
       }
    }

    // delete image
    $im = isset($_GET['im']) ? trim(substr($_GET['im'],0,255)) : '';
    $a = isset($_GET['a']) ? trim(substr($_GET['a'],0,255)) : '';
    $im1 = $im;
    $im1 = str_replace('..','.',$im1);
    $im1 = str_replace('//','/',$im1);
    if($im1==$im && substr($im,0,6)=="common" && is_file(DR.'/'.$im) && file_exists(DR.'/'.$im)) {
        $path1 = substr($im,0,strlen($im)-strlen(strrchr($im,'/')));
        unlink(DR.'/'.$im);
    }

    // upload image
    $sz = Array();
    $sz = explode(",",ADVIMAGE_SMALL_SIZE);
    if(isset($sz[0]) && (int)$sz[0]>0) $up_width1 = $sz[0]; else $up_width1 = "";
    if(isset($sz[1]) && (int)$sz[1]>1) $up_height1 = $sz[1]; else $up_height1 = "";
    $sz = Array();
    $sz = explode(",",ADVIMAGE_NORMAL_SIZE);
    if(isset($sz[0]) && (int)$sz[0]>0) $up_width2 = $sz[0]; else $up_width2 = "";
    if(isset($sz[1]) && (int)$sz[1]>1) $up_height2 = $sz[1]; else $up_height2 = "";
    $sz = Array();
    $sz = explode(",",ADVIMAGE_BIG_SIZE);
    if(isset($sz[0]) && (int)$sz[0]>0) $up_width3 = $sz[0]; else $up_width3 = "";
    if(isset($sz[1]) && (int)$sz[1]>1) $up_height3 = $sz[1]; else $up_height3 = "";

    if($_SERVER['REQUEST_METHOD']=="POST") {
        for($i=0;$i<10;$i++) {
            $im_name = 'up_img'.$i;
            if(isset($_FILES[$im_name]) && file_exists($_FILES[$im_name]['tmp_name']) && $_FILES[$im_name]['error']==0) {
                $im1 = 0;
                $fname = $_FILES[$im_name]['name'];
                if(strpos($_FILES[$im_name]['name'],".")) $ext = strtolower(substr(strrchr($fname,"."),1)); else $ext = "";
                if($ext == "jpg") $im1 = imageCreateFromJpeg($_FILES[$im_name]['tmp_name']); else
                if($ext == "gif") $im1 = imageCreateFromGif($_FILES[$im_name]['tmp_name']); else
                if($ext == "png") $im1 = imageCreateFromPng($_FILES[$im_name]['tmp_name']);
                if($im1) {
                                //if(imageSX($im1)>imageSY($im1)) {
                                    $kf1 = imageSX($im1)/imageSY($im1);
                                    $kf2 = imageSY($im1)/imageSX($im1);
                               // } else {
                                   // $kf2 = imageSX($im1)/imageSY($im1);
                                   // $kf1 = imageSY($im1)/imageSX($im1);
                               // }
                                $sz2[0] = isset($_POST['up_width'.$i]) ? (int)$_POST['up_width'.$i] : 0;
                                $sz2[1] = isset($_POST['up_height'.$i]) ? (int)$_POST['up_height'.$i] : 0;
                                if(!$sz2[0] && !$sz2[1]) $sz2[0] = imageSX($im1);
                                
                                //if(isset($sz2[0]) && $sz2[0]>0) $sw2=(int)$sz2[0]; else $sw2=0; if(isset($sz2[1]) && $sz2[1]>0) $sh2=(int)$sz2[1]; else $sh2=round($sw2/$kf1); if($sw2<1) if($sh2>0) $sw2=round($sh2/$kf2); else $sw2=imageSX($im1); if($sh2<1) $sh2=imageSY($im1);
                                if(!empty($sz2[0]))
                                    {
                                    $sw2=(int)$sz2[0];
                                    }
                                else 
                                    {
                                    $sw2=0;
                                    }
                                if(!empty($sz2[1])) 
                                    {
                                    $sh2=(int)$sz2[1];
                                    
                                    }
                                else 
                                    {
                                    $sh2=round($sw2/$kf1);
                                    }
                                    
                                if($sw2 < 1)
                                    {
                                    $sw2=round($sh2/$kf2);
                                    }
                                
                                /* echo '<pre>';
                                var_dump($sz2,$sw2,$sh2,(imageSX($im1)>imageSY($im1)));
                                echo '</pre>';*/
                                
                                $im2 = 0;
                                //if(imageSX($im1)>imageSY($im1)) {
                                    $im2 = imageCreateTrueColor($sw2,$sh2);
                                //} else {
                                    //$im2 = imageCreateTrueColor($sh2,$sw2);
                                //}

                                $bgcolor = "";
                                $col1 = imageColorAllocate($im2, 255,255,255);
                                imageFill($im2,0,0,$col1);
                                if(imageSX($im1)<imageSY($im1)) {
                                    imageCopyResampled($im2, $im1, 0,round((imageSY($im2)-(imageSX($im2)*imageSY($im1)/imageSX($im1)))/2), 0,0, imageSX($im2),(imageSX($im2)*imageSY($im1)/imageSX($im1)), imageSX($im1),imageSY($im1));
                                } else {
                                    imageCopyResampled($im2, $im1, round((imageSX($im2)-(imageSY($im2)*imageSX($im1)/imageSY($im1)))/2),0, 0,0, (imageSY($im2)*imageSX($im1)/imageSY($im1)),imageSY($im2), imageSX($im1),imageSY($im1));
                                }

                                $fname = substr($fname,0,strlen($fname)-strlen($ext)-1);
                                $fname = dconcat2(trlate3($fname),1);
                                if($sz2[0]>0 || $sz2[1]>0) $fname .= "_".imageSX($im2)."x".imageSY($im2);
                                $n1=0;
                                $n2="";
                                while($n1 < 100000) {
                                    if(file_exists($path."/".$fname.$n2.".".$ext)) {
                                        $n1++;
                                        $n2="_".$n1;
                                    } else {
                                        $fname .= $n2;
                                        break;
                                    }
                                }
                                $fname .= ".".$ext;
                                if($ext == "jpg") imageJpeg($im2, $path."/".$fname, 90); else
                                if($ext == "gif") imageGif($im2, $path."/".$fname); else
                                if($ext == "png") imagePng($im2, $path."/".$fname);
                                @chmod($path."/".$fname, 0644);
                } else {
                    if(in_array($ext,$im_ext)) {
                        $fname = substr($fname,0,strlen($fname)-strlen($ext)-1);
                        $fname = dconcat2(trlate3($fname),1);
                        $n1=0;
                        $n2="";
                        while($n1 < 100000) {
                           if(file_exists($path."/".$fname.$n2.".".$ext)) {
                               $n1++;
                               $n2="_".$n1;
                           } else {
                               $fname .= $n2;
                               break;
                           }
                        }
                        $fname .= ".".$ext;
                        move_uploaded_file($_FILES[$im_name]['tmp_name'],$path."/".$fname);
                    }
                }
            }
        }
    }
    
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#advanced_dlg.image_title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="../../utils/mctabs.js"></script>
	<script type="text/javascript" src="../../utils/form_utils.js"></script>
	<script type="text/javascript" src="js/image.js"></script>
<style>
body, p, table {cursor:default;}
a {cursor:hand;}
img {border:0px;}
.im_bl {width:55px;height:70px;padding-top:1px;border:1px solid #eee;overflow:hidden;float:left;margin-right:5px;margin-bottom:5px;}
.im_title {font-family:Arial;font-size:8px;}
.im_size {font-family:Arial;font-size:8px;color:#999;}
.im_size a {color:red;text-decoration:none;}
.ld_im, .ld_im img {vertical-align:middle;}
</style>
</head>
<body id="image" style="display: none; overflow:hidden;">
        <div class="tabs">
		<ul>
			<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;">{#advanced_dlg.image_title}</a></span></li>
			<li id="upload_tab"><span><a href="javascript:mcTabs.displayTab('upload_tab','upload_panel');" onmousedown="return false;">Загрузить</a></span></li>
		</ul>
	</div>

	<div class="panel_wrapper" style="height: 370px;overflow:hidden;">
		<div id="general_panel" class="panel current">
     <form name="im_form" onsubmit="" action="#">
     <table border="0" width="100%" cellpadding="4" cellspacing="0">
          <tr style="display:none">
            <td class="nowrap"><label for="im_list">Папка</label></td>
            <td><select onchange="getImgs(this.options[this.options.selectedIndex].value,'');" id="im_folder" style="width:100%;font-size:8pt;font-family:Courier new;"><?php
                //$q = "";
                //foreach($ext_arr as $i=>$v) {
                  //  $q .= '*.'.$v.',';
                //}
               // $im_list = count(glob(DR."/common/{".trim($q,",")."}",GLOB_BRACE));
               // if($im_list>0) $im_cnt = $im_list.' изображени'.getw($im_list,34); else $im_cnt = 'папка пуста';
                echo '<option value="/common/c'.$user->client->hc_id.'/" selected>- корневая папка </option>';
               // $file_list = glob(DR."/common/*");
                //natsort($file_list);
                //foreach($file_list as $i=>$v) {
                    //if(!is_dir($v)) continue;
                   // $im_list = count(glob($v."/{".trim($q,",")."}",GLOB_BRACE));
                   // if($im_list>0) $im_cnt = $im_list.' изображени'.getw($im_list,34); else $im_cnt = 'папка пуста';
                   // $v = substr(strrchr($v,"/"),1);
                   // echo '<option value="/common/'.$v.'">../'.$v.'/'.str_repeat('&nbsp;',8-strlen($v)).' ['.$im_cnt.']</option>';
               // }
?>
                </select></td>
          </tr>
          <tr style="display:none">
            <td class="nowrap"><label for="im_list">{#advanced_dlg.image_list}</label></td>
            <td>
                <div style="width:100%;height:200px;overflow-x:hidden;overflow-y:scroll;border:1px solid #ddd;"><div id="c_im_list" style="margin:5px;"></div></div>
            </td>
          </tr>
          <tr>
            <td class="nowrap"><label for="src">{#advanced_dlg.image_src}</label></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><input id="src" name="src" type="text" class="mceFocus" value="" style="width: 200px" onchange="setIm(this.value);ImageDialog.getImageData();" /></td>
                  <td id="srcbrowsercontainer">&nbsp;</td>
                </tr>
              </table></td>
          </tr>
		  <tr>
			<td><label for="image_list">{#advanced_dlg.image_list}</label></td>
			<td><select id="image_list" name="image_list" onchange="document.getElementById('src').value=this.options[this.selectedIndex].value;document.getElementById('alt').value=this.options[this.selectedIndex].text;"></select></td>
		  </tr>
          <tr>
            <td class="nowrap"><label for="alt">{#advanced_dlg.image_alt}</label></td>
            <td><input id="alt" name="alt" type="text" value="" style="width: 200px" /></td>
          </tr>
          <tr>
            <td class="nowrap"><label for="align">{#advanced_dlg.image_align}</label></td>
            <td><select id="align" name="align" onchange="ImageDialog.updateStyle();">
                <option value="">{#not_set}</option>
                <option value="baseline">{#advanced_dlg.image_align_baseline}</option>
                <option value="top">{#advanced_dlg.image_align_top}</option>
                <option value="middle">{#advanced_dlg.image_align_middle}</option>
                <option value="bottom">{#advanced_dlg.image_align_bottom}</option>
                <option value="text-top">{#advanced_dlg.image_align_texttop}</option>
                <option value="text-bottom">{#advanced_dlg.image_align_textbottom}</option>
                <option value="left">{#advanced_dlg.image_align_left}</option>
                <option value="right">{#advanced_dlg.image_align_right}</option>
              </select></td>
          </tr>
          <tr>
            <td class="nowrap"><label for="width">{#advanced_dlg.image_dimensions}</label></td>
            <td><input id="width" name="width" type="text" value="" size="3" maxlength="5" />
              x
              <input id="height" name="height" type="text" value="" size="3" maxlength="5" /></td>
          </tr>
          <tr>
            <td class="nowrap"><label for="border">{#advanced_dlg.image_border}</label></td>
            <td><input id="border" name="border" type="text" value="" size="3" maxlength="3" onchange="ImageDialog.updateStyle();" /></td>
          </tr>
          <tr height="150px">
            <td class="nowrap" colspan="2">
            <input id="vspace" name="vspace" type="hidden" value="" size="3" onchange="ImageDialog.updateStyle();" />
            <input id="hspace" name="hspace" type="hidden" value="" size="3" onchange="ImageDialog.updateStyle();" />
            <div id="im_window" style="width: 350px; height: 150px; overflow: scroll; border: 1px solid #919b9c; display: none;"></div></td>
          </tr>
        </table></form>
		</div>

                <div id="upload_panel" class="panel">
                <form name="up_form" action="" method="POST" enctype="multipart/form-data">
     <table border="0" cellpadding="4" cellspacing="0">
          <tr height="30px">
            <td width="260px" class="nowrap"><input type="file" name="up_img1" style="width: 95%;"></td>
            <td><input id="up_width1" name="up_width1" type="text" value="<?php echo $up_width1; ?>" size="3" maxlength="5" />
              x
              <input id="up_height1" name="up_height1" type="text" value="<?php echo $up_height1; ?>" size="3" maxlength="5" /></td>
          </tr>
          
          <tr height="30px" style="display:none">
            <td class="nowrap">Загрузить в папку: <select name="im_folder" style="width:90%;font-size:8pt;font-family:Courier new;"><?php
                //$q = "";
                //foreach($ext_arr as $i=>$v) {
                //    $q .= '*.'.$v.',';
                //}
                //$im_list = count(glob(DR."/common/{".trim($q,",")."}",GLOB_BRACE));
                //if($im_list>0) $im_cnt = $im_list.' изображени'.getw($im_list,34); else $im_cnt = 'папка пуста';
                echo '<option value="/common/c'.$user->client->hc_id.'/" selected>- корневая папка </option>';
                //$file_list = glob(DR."/common/*");
                //natsort($file_list);
                //foreach($file_list as $i=>$v) {
                    //if(!is_dir($v)) continue;
                    //$im_list = count(glob($v."/{".trim($q,",")."}",GLOB_BRACE));
                    //if($im_list>0) $im_cnt = $im_list.' изображени'.getw($im_list,34); else $im_cnt = 'папка пуста';
                    //$v = substr(strrchr($v,"/"),1);
                    //echo '<option value="/common/'.$v.'">../'.$v.'/'.str_repeat('&nbsp;',8-strlen($v)).' ['.$im_cnt.']</option>';
                //}
?>
                </select></td>
            <td></td>
          </tr>
          <tr height="50px">
            <td colspan="2"><input type="button" onClick="document.forms['up_form'].submit();" value="Загрузить" /></td>
          </tr>
        </table>
        </form>
		</div>

	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="submit" id="insert" name="insert" value="{#insert}" onClick="ImageDialog.update();return false;" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
<script language="JavaScript"><!--
<?php
    if($path1!="") echo "getImgs('".$path1."','');\r\n"; else echo "getImgs('','');\r\n";
    
    if($_SERVER['REQUEST_METHOD']=="POST")
        {
        echo 'setIm("/'.$path1.'/'.$fname.'"); ImageDialog.getImageData(); ImageDialog.update();';
        }
    
?>
// --></script>
</body>
</html>
