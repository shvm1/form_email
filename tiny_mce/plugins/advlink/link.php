<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
    require_once(DR."/mod/func.php");

    $path = DR."/common";
    $path_len = strlen($path);
    $ext_arr = Array("png","gif","jpg","jpe","jpeg","bmp","iff","tiff");
    if(!file_exists($path)) @mkdir($path, 0777);
    if(!is_writable($path)) @chmod($path, 0777);

    db_connect();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#advlink_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="../../utils/mctabs.js"></script>
	<script type="text/javascript" src="../../utils/form_utils.js"></script>
	<script type="text/javascript" src="../../utils/validate.js"></script>
	<script type="text/javascript" src="js/advlink.js"></script>
	<link href="css/advlink.css" rel="stylesheet" type="text/css" />
</head>
<body id="advlink" style="display: none">
    <form onsubmit="insertAction();return false;" action="#">
		<div class="tabs">
			<ul>
				<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;">{#advlink_dlg.general_tab}</a></span></li>
				<li id="popup_tab"><span><a href="javascript:mcTabs.displayTab('popup_tab','popup_panel');" onmousedown="return false;">{#advlink_dlg.popup_tab}</a></span></li>
				<li id="events_tab"><span><a href="javascript:mcTabs.displayTab('events_tab','events_panel');" onmousedown="return false;">{#advlink_dlg.events_tab}</a></span></li>
				<li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{#advlink_dlg.advanced_tab}</a></span></li>
			</ul>
		</div>

		<div class="panel_wrapper">
			<div id="general_panel" class="panel current">
				<fieldset>
					<legend>{#advlink_dlg.general_props}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
                                                <tr>
                                                <td class="nowrap"><label for="files_list">Страницы</label></td>
                                                <td><select onchange="setLink(this.value);" id="im_list" style="width: 100%;"><option value=""></option>
<?php
                                                   $res1 = "";
                                                   $res1 = mq("select * from ".PRX."_pages where p_type<>1 order by p_sortindex");
                                                   if(issquery($res1)) {
                                                       while($row1 = mfa($res1)) {
                                                           $s = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$row1['p_level']-1);
                                                           echo '<option value="/'.trim($row1['p_url2'].'/'.$row1['p_id2'],'/').'/">'.$s.$row1['p_title'].'</option>';
                                                           if($row1['p_type']==3 && $row1['p_opt_categories']=='Y' && $row1['cnt_child1']>0) {
                                                               $res2 = "";
                                                               $res2 = mq("select * from ".PRX."_article_categories where p_id='".$row1['p_id']."' order by ac_sortindex");
                                                               if(issquery($res2)) {
                                                                   while($row2 = mfa($res2)) {
                                                                       $s1 = $s.str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$row2['ac_level']);
                                                                       echo '<option value="/'.trim($row1['p_url2'].'/'.$row1['p_id2'],'/').'/'.trim($row2['ac_url2'].'/'.$row2['ac_id2'],'/').'/">'.$s1.'К: '.$row2['ac_title'].'</option>';
                                                                   }
                                                               }
                                                           }
                                                       }
                                                   }
?>
                                                </select></td>
                                                </tr>
                                                <tr>
                                                <td class="nowrap"><label for="files_list">Файлы</label></td>
                                                <td><select onchange="setLink(this.value);" id="im_list" style="width: 100%;"><option value=""></option>
<?php
                                                   $res1 = "";
                                                   $res1 = mq("select * from ".PRX."_files order by f_type,f_title");
                                                   if(issquery($res1)) {
                                                       while($row1 = mfa($res1)) {
                                                           echo '<option value="/files/'.$row1['f_filename'].'">'.$row1['f_title'].' ('.$row1['f_filename'].')</option>';
                                                       }
                                                   }

                                                   $q = "";
                                                   foreach($ext_arr as $i=>$v) {
                                                       $q .= '*.'.$v.',';
                                                   }
                                                   $im_list = glob($path."/{".trim($q,",")."}",GLOB_BRACE);
                                                   natsort($im_list);
                                                   foreach($im_list as $i=>$v) {
                                                       $v = substr(strrchr($v,"/"),1);
                                                       echo '<option value="/common/'.$v.'">'.$v.'</option>';
                                                   }
?>
                                                  </select></td>
                                                  </tr>
                                                  <tr>
						  <td class="nowrap"><label id="hreflabel" for="href">{#advlink_dlg.url}</label></td>
						  <td><table border="0" cellspacing="0" cellpadding="0">
								<tr>
								  <td><input id="href" name="href" type="text" class="mceFocus" value="" onchange="selectByValue(this.form,'linklisthref',this.value);" /></td>
								  <td id="hrefbrowsercontainer">&nbsp;</td>
								</tr>
							  </table></td>
						</tr>
						<tr id="linklisthrefrow">
							<td class="column1"><label for="linklisthref">{#advlink_dlg.list}</label></td>
							<td colspan="2" id="linklisthrefcontainer"><select id="linklisthref"><option value=""></option></select></td>
						</tr>
						<tr>
							<td class="column1"><label for="anchorlist">{#advlink_dlg.anchor_names}</label></td>
							<td colspan="2" id="anchorlistcontainer"><select id="anchorlist"><option value=""></option></select></td>
						</tr>
						<tr>
							<td><label id="targetlistlabel" for="targetlist">{#advlink_dlg.target}</label></td>
							<td id="targetlistcontainer"><select id="targetlist"><option value=""></option></select></td>
						</tr>
						<tr>
							<td class="nowrap"><label id="titlelabel" for="title">{#advlink_dlg.titlefield}</label></td>
							<td><input id="title" name="title" type="text" value="" /></td>
						</tr>
						<tr>
							<td><label id="classlabel" for="classlist">{#class_name}</label></td>
							<td>
								 <select id="classlist" name="classlist" onchange="changeClass();">
									<option value="" selected="selected">{#not_set}</option>
								 </select>
							</td>
						</tr>
						<tr>
							<td class="nowrap"><label id="sclasslabel" for="sclass">Скрипт</label></td>
                                                        <td id="sclasscontainer">&nbsp;</td>
                                                </tr>
					</table>
				</fieldset>
			</div>

			<div id="popup_panel" class="panel">
				<fieldset>
					<legend>{#advlink_dlg.popup_props}</legend>

					<input type="checkbox" id="ispopup" name="ispopup" class="radio" onclick="setPopupControlsDisabled(!this.checked);buildOnClick();" />
					<label id="ispopuplabel" for="ispopup">{#advlink_dlg.popup}</label>

					<table border="0" cellpadding="0" cellspacing="4">
						<tr>
							<td class="nowrap"><label for="popupurl">{#advlink_dlg.popup_url}</label>&nbsp;</td>
							<td>
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td><input type="text" name="popupurl" id="popupurl" value="" onchange="buildOnClick();" /></td>
										<td id="popupurlbrowsercontainer">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="nowrap"><label for="popupname">{#advlink_dlg.popup_name}</label>&nbsp;</td>
							<td><input type="text" name="popupname" id="popupname" value="" onchange="buildOnClick();" /></td>
						</tr>
						<tr>
							<td class="nowrap"><label>{#advlink_dlg.popup_size}</label>&nbsp;</td>
							<td class="nowrap">
								<input type="text" id="popupwidth" name="popupwidth" value="" onchange="buildOnClick();" /> x
								<input type="text" id="popupheight" name="popupheight" value="" onchange="buildOnClick();" /> px
							</td>
						</tr>
						<tr>
							<td class="nowrap" id="labelleft"><label>{#advlink_dlg.popup_position}</label>&nbsp;</td>
							<td class="nowrap">
								<input type="text" id="popupleft" name="popupleft" value="" onchange="buildOnClick();" /> /                                
								<input type="text" id="popuptop" name="popuptop" value="" onchange="buildOnClick();" /> (c /c = center)
							</td>
						</tr>
					</table>

					<fieldset>
						<legend>{#advlink_dlg.popup_opts}</legend>

						<table border="0" cellpadding="0" cellspacing="4">
							<tr>
								<td><input type="checkbox" id="popuplocation" name="popuplocation" class="checkbox" onchange="buildOnClick();" /></td>
								<td class="nowrap"><label id="popuplocationlabel" for="popuplocation">{#advlink_dlg.popup_location}</label></td>
								<td><input type="checkbox" id="popupscrollbars" name="popupscrollbars" class="checkbox" onchange="buildOnClick();" /></td>
								<td class="nowrap"><label id="popupscrollbarslabel" for="popupscrollbars">{#advlink_dlg.popup_scrollbars}</label></td>
							</tr>
							<tr>
								<td><input type="checkbox" id="popupmenubar" name="popupmenubar" class="checkbox" onchange="buildOnClick();" /></td>
								<td class="nowrap"><label id="popupmenubarlabel" for="popupmenubar">{#advlink_dlg.popup_menubar}</label></td>
								<td><input type="checkbox" id="popupresizable" name="popupresizable" class="checkbox" onchange="buildOnClick();" /></td>
								<td class="nowrap"><label id="popupresizablelabel" for="popupresizable">{#advlink_dlg.popup_resizable}</label></td>
							</tr>
							<tr>
								<td><input type="checkbox" id="popuptoolbar" name="popuptoolbar" class="checkbox" onchange="buildOnClick();" /></td>
								<td class="nowrap"><label id="popuptoolbarlabel" for="popuptoolbar">{#advlink_dlg.popup_toolbar}</label></td>
								<td><input type="checkbox" id="popupdependent" name="popupdependent" class="checkbox" onchange="buildOnClick();" /></td>
								<td class="nowrap"><label id="popupdependentlabel" for="popupdependent">{#advlink_dlg.popup_dependent}</label></td>
							</tr>
							<tr>
								<td><input type="checkbox" id="popupstatus" name="popupstatus" class="checkbox" onchange="buildOnClick();" /></td>
								<td class="nowrap"><label id="popupstatuslabel" for="popupstatus">{#advlink_dlg.popup_statusbar}</label></td>
								<td><input type="checkbox" id="popupreturn" name="popupreturn" class="checkbox" onchange="buildOnClick();" checked="checked" /></td>
								<td class="nowrap"><label id="popupreturnlabel" for="popupreturn">{#advlink_dlg.popup_return}</label></td>
							</tr>
						</table>
					</fieldset>
				</fieldset>
			</div>

			<div id="advanced_panel" class="panel">
			<fieldset>
					<legend>{#advlink_dlg.advanced_props}</legend>

					<table border="0" cellpadding="0" cellspacing="4">
						<tr>
							<td class="column1"><label id="idlabel" for="id">{#advlink_dlg.id}</label></td> 
							<td><input id="id" name="id" type="text" value="" /></td> 
						</tr>

						<tr>
							<td><label id="stylelabel" for="style">{#advlink_dlg.style}</label></td>
							<td><input type="text" id="style" name="style" value="" /></td>
						</tr>

						<tr>
							<td><label id="classeslabel" for="classes">{#advlink_dlg.classes}</label></td>
							<td><input type="text" id="classes" name="classes" value="" onchange="selectByValue(this.form,'classlist',this.value,true);" /></td>
						</tr>

						<tr>
							<td><label id="targetlabel" for="target">{#advlink_dlg.target_name}</label></td>
							<td><input type="text" id="target" name="target" value="" onchange="selectByValue(this.form,'targetlist',this.value,true);" /></td>
						</tr>

						<tr>
							<td class="column1"><label id="dirlabel" for="dir">{#advlink_dlg.langdir}</label></td> 
							<td>
								<select id="dir" name="dir"> 
										<option value="">{#not_set}</option> 
										<option value="ltr">{#advlink_dlg.ltr}</option> 
										<option value="rtl">{#advlink_dlg.rtl}</option> 
								</select>
							</td> 
						</tr>

						<tr>
							<td><label id="hreflanglabel" for="hreflang">{#advlink_dlg.target_langcode}</label></td>
							<td><input type="text" id="hreflang" name="hreflang" value="" /></td>
						</tr>

						<tr>
							<td class="column1"><label id="langlabel" for="lang">{#advlink_dlg.langcode}</label></td> 
							<td>
								<input id="lang" name="lang" type="text" value="" />
							</td> 
						</tr>

						<tr>
							<td><label id="charsetlabel" for="charset">{#advlink_dlg.encoding}</label></td>
							<td><input type="text" id="charset" name="charset" value="" /></td>
						</tr>

						<tr>
							<td><label id="typelabel" for="type">{#advlink_dlg.mime}</label></td>
							<td><input type="text" id="type" name="type" value="" /></td>
						</tr>

						<tr>
							<td><label id="rellabel" for="rel">{#advlink_dlg.rel}</label></td>
							<td><select id="rel" name="rel"> 
									<option value="">{#not_set}</option> 
									<option value="lightbox">Lightbox</option> 
									<option value="alternate">Alternate</option> 
									<option value="designates">Designates</option> 
									<option value="stylesheet">Stylesheet</option> 
									<option value="start">Start</option> 
									<option value="next">Next</option> 
									<option value="prev">Prev</option> 
									<option value="contents">Contents</option> 
									<option value="index">Index</option> 
									<option value="glossary">Glossary</option> 
									<option value="copyright">Copyright</option> 
									<option value="chapter">Chapter</option> 
									<option value="subsection">Subsection</option> 
									<option value="appendix">Appendix</option> 
									<option value="help">Help</option> 
									<option value="bookmark">Bookmark</option>
									<option value="nofollow">No Follow</option>
									<option value="tag">Tag</option>
								</select> 
							</td>
						</tr>

						<tr>
							<td><label id="revlabel" for="rev">{#advlink_dlg.rev}</label></td>
							<td><select id="rev" name="rev"> 
									<option value="">{#not_set}</option> 
									<option value="alternate">Alternate</option> 
									<option value="designates">Designates</option> 
									<option value="stylesheet">Stylesheet</option> 
									<option value="start">Start</option> 
									<option value="next">Next</option> 
									<option value="prev">Prev</option> 
									<option value="contents">Contents</option> 
									<option value="index">Index</option> 
									<option value="glossary">Glossary</option> 
									<option value="copyright">Copyright</option> 
									<option value="chapter">Chapter</option> 
									<option value="subsection">Subsection</option> 
									<option value="appendix">Appendix</option> 
									<option value="help">Help</option> 
									<option value="bookmark">Bookmark</option> 
								</select> 
							</td>
						</tr>

						<tr>
							<td><label id="tabindexlabel" for="tabindex">{#advlink_dlg.tabindex}</label></td>
							<td><input type="text" id="tabindex" name="tabindex" value="" /></td>
						</tr>

						<tr>
							<td><label id="accesskeylabel" for="accesskey">{#advlink_dlg.accesskey}</label></td>
							<td><input type="text" id="accesskey" name="accesskey" value="" /></td>
						</tr>
					</table>
				</fieldset>
			</div>

			<div id="events_panel" class="panel">
			<fieldset>
					<legend>{#advlink_dlg.event_props}</legend>

					<table border="0" cellpadding="0" cellspacing="4">
						<tr>
							<td class="column1"><label for="onfocus">onfocus</label></td> 
							<td><input id="onfocus" name="onfocus" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onblur">onblur</label></td> 
							<td><input id="onblur" name="onblur" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onclick">onclick</label></td> 
							<td><input id="onclick" name="onclick" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="ondblclick">ondblclick</label></td> 
							<td><input id="ondblclick" name="ondblclick" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmousedown">onmousedown</label></td> 
							<td><input id="onmousedown" name="onmousedown" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmouseup">onmouseup</label></td> 
							<td><input id="onmouseup" name="onmouseup" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmouseover">onmouseover</label></td> 
							<td><input id="onmouseover" name="onmouseover" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmousemove">onmousemove</label></td> 
							<td><input id="onmousemove" name="onmousemove" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmouseout">onmouseout</label></td> 
							<td><input id="onmouseout" name="onmouseout" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onkeypress">onkeypress</label></td> 
							<td><input id="onkeypress" name="onkeypress" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onkeydown">onkeydown</label></td> 
							<td><input id="onkeydown" name="onkeydown" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onkeyup">onkeyup</label></td> 
							<td><input id="onkeyup" name="onkeyup" type="text" value="" /></td> 
						</tr>
					</table>
				</fieldset>
			</div>
		</div>

		<div class="mceActionPanel">
			<div style="float: left">
				<input type="submit" id="insert" name="insert" value="{#insert}" />
			</div>

			<div style="float: right">
				<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
			</div>
		</div>
    </form>
</body>
</html>
