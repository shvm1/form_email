function setIm(im) {
        //if(document.getElementById("im_list").options[document.getElementById("im_list").selectedIndex].value!=im) document.getElementById("im_list").options[0].selected = true;
        if(im!="") {
                document.getElementById("src").value = im;
                document.getElementById("im_window").innerHTML = '<img src="'+im.replace('"','\"')+'">';
        }
}
var ImageDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function() {
		var f = document.forms[0], ed = tinyMCEPopup.editor;

		// Setup browse button
		document.getElementById('srcbrowsercontainer').innerHTML = getBrowserHTML('srcbrowser','src','image','theme_advanced_image');
		if (isVisible('srcbrowser'))
			document.getElementById('src').style.width = '180px';

		e = ed.selection.getNode();

		this.fillFileList('image_list', 'tinyMCEImageList');

		if (e.nodeName == 'IMG') {
			f.src.value = ed.dom.getAttrib(e, 'src');
			f.alt.value = ed.dom.getAttrib(e, 'alt');
			f.border.value = this.getAttrib(e, 'border');
			f.vspace.value = this.getAttrib(e, 'vspace');
			f.hspace.value = this.getAttrib(e, 'hspace');
			f.width.value = ed.dom.getAttrib(e, 'width');
			f.height.value = ed.dom.getAttrib(e, 'height');
			f.insert.value = ed.getLang('update');
			this.styleVal = ed.dom.getAttrib(e, 'style');
			selectByValue(f, 'image_list', f.src.value);
			selectByValue(f, 'align', this.getAttrib(e, 'align'));
			this.updateStyle();
		}
	},

	fillFileList : function(id, l) {
		var dom = tinyMCEPopup.dom, lst = dom.get(id), v, cl;

		l = window[l];

		if (l && l.length > 0) {
			lst.options[lst.options.length] = new Option('', '');

			tinymce.each(l, function(o) {
				lst.options[lst.options.length] = new Option(o[0], o[1]);
			});
		} else
			dom.remove(dom.getParent(id, 'tr'));
	},

	update : function() {
		var f = document.forms[0], nl = f.elements, ed = tinyMCEPopup.editor, args = {}, el;

		tinyMCEPopup.restoreSelection();

		if (f.src.value === '') {
			if (ed.selection.getNode().nodeName == 'IMG') {
				ed.dom.remove(ed.selection.getNode());
				ed.execCommand('mceRepaint');
			}

			tinyMCEPopup.close();
			return;
		}

		if (!ed.settings.inline_styles) {
			args = tinymce.extend(args, {
				vspace : nl.vspace.value,
				hspace : nl.hspace.value,
				border : nl.border.value,
				align : getSelectValue(f, 'align')
			});
		} else
			args.style = this.styleVal;

		tinymce.extend(args, {
			src : f.src.value,
			alt : f.alt.value,
			width : f.width.value,
			height : f.height.value
		});

		el = ed.selection.getNode();

		if (el && el.nodeName == 'IMG') {
			ed.dom.setAttribs(el, args);
		} else {
			ed.execCommand('mceInsertContent', false, '<img id="__mce_tmp" />', {skip_undo : 1});
			ed.dom.setAttribs('__mce_tmp', args);
			ed.dom.setAttrib('__mce_tmp', 'id', '');
			ed.undoManager.add();
		}

		tinyMCEPopup.close();
	},

	updateStyle : function() {
		var dom = tinyMCEPopup.dom, st, v, f = document.forms[0];

		if (tinyMCEPopup.editor.settings.inline_styles) {
			st = tinyMCEPopup.dom.parseStyle(this.styleVal);

			// Handle align
			v = getSelectValue(f, 'align');
			if (v) {
				if (v == 'left' || v == 'right') {
					st['float'] = v;
					delete st['vertical-align'];
				} else {
					st['vertical-align'] = v;
					delete st['float'];
				}
			} else {
				delete st['float'];
				delete st['vertical-align'];
			}

			// Handle border
			v = f.border.value;
			if (v || v == '0') {
				if (v == '0')
					st['border'] = '0';
				else
					st['border'] = v + 'px solid black';
			} else
				delete st['border'];

			// Handle hspace
			v = f.hspace.value;
			if (v) {
				delete st['margin'];
				st['margin-left'] = v + 'px';
				st['margin-right'] = v + 'px';
			} else {
				delete st['margin-left'];
				delete st['margin-right'];
			}

			// Handle vspace
			v = f.vspace.value;
			if (v) {
				delete st['margin'];
				st['margin-top'] = v + 'px';
				st['margin-bottom'] = v + 'px';
			} else {
				delete st['margin-top'];
				delete st['margin-bottom'];
			}

			// Merge
			st = tinyMCEPopup.dom.parseStyle(dom.serializeStyle(st));
			this.styleVal = dom.serializeStyle(st);
		}
	},

	getAttrib : function(e, at) {
		var ed = tinyMCEPopup.editor, dom = ed.dom, v, v2;

		if (ed.settings.inline_styles) {
			switch (at) {
				case 'align':
					if (v = dom.getStyle(e, 'float'))
						return v;

					if (v = dom.getStyle(e, 'vertical-align'))
						return v;

					break;

				case 'hspace':
					v = dom.getStyle(e, 'margin-left')
					v2 = dom.getStyle(e, 'margin-right');
					if (v && v == v2)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;

				case 'vspace':
					v = dom.getStyle(e, 'margin-top')
					v2 = dom.getStyle(e, 'margin-bottom');
					if (v && v == v2)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;

				case 'border':
					v = 0;

					tinymce.each(['top', 'right', 'bottom', 'left'], function(sv) {
						sv = dom.getStyle(e, 'border-' + sv + '-width');

						// False or not the same as prev
						if (!sv || (sv != v && v !== 0)) {
							v = 0;
							return false;
						}

						if (sv)
							v = sv;
					});

					if (v)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;
			}
		}

		if (v = dom.getAttrib(e, at))
			return v;

		return '';
	},

	resetImageData : function() {
		var f = document.forms[0];

		f.width.value = f.height.value = "";	
	},

	updateImageData : function() {
		var f = document.forms[0], t = ImageDialog;

		if (f.width.value == "")
			f.width.value = t.preloadImg.width;

		if (f.height.value == "")
			f.height.value = t.preloadImg.height;
	},

	getImageData : function() {
		var f = document.forms[0];

		this.preloadImg = new Image();
		this.preloadImg.onload = this.updateImageData;
		this.preloadImg.onerror = this.resetImageData;
		this.preloadImg.src = tinyMCEPopup.editor.documentBaseURI.toAbsolute(f.src.value);
	}
};
function getXmlHttpObject() {
    var XMLHttp = null;
    if(window.XMLHttpRequest) {
       try {
          XMLHttp = new XMLHttpRequest();
       } catch (e) {}
    } else if(window.ActiveXObject) {
       try {
          XMLHttp = new ActiveXObject("Msxml2.XMLHTTP");
       } catch (e) {
          try {
             XMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
          } catch (e) {}
       }
    }
    return XMLHttp;
}
function trim(str,charlist) {
	charlist = !charlist ? ' \s\xA0' : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
	var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
	return str.replace(re, '');
}
function getImgs(p,i) {
    if(p=='') {
        ed = tinyMCEPopup.editor;
        e = ed.selection.getNode();
        s = ed.dom.getAttrib(e, 'src');
        if(s && s.substring(0,8)=="/common/") {
            while(s.indexOf('/')>=0) {
                s = s.substring(s.indexOf('/')+1);
            }
            s1 = ed.dom.getAttrib(e, 'src');
            p = s1.substring(0,s1.length-s.length);
        }
    }
    for(q=0;q<document.getElementById('im_folder').options.length;q++) {
       if(trim(document.getElementById('im_folder').options[q].value,'/')==trim(p,'/')) {
           if(!document.getElementById('im_folder').options[q].selected) document.getElementById('im_folder').options[q].selected = true;
           break;
       }
    }
    obj = document.getElementById('c_im_list');
    obj.innerHTML = '<div class="ld_im"><img src="/admin/img/loading_black.gif" width="16px"> загрузка ..</div>';
    if(xmlhttpobj) xmlhttpobj.abort();
    var xmlhttpobj = getXmlHttpObject();
    xmlhttpobj.open("GET","imgslist.php?p=" + p + "&i=" + i + "&r=" + Math.random());
    xmlhttpobj.onreadystatechange = function() {
       if(xmlhttpobj.readyState == 4) {
             obj = document.getElementById('c_im_list');
             var rtext = '';
             rtext = xmlhttpobj.responseText;
             obj.innerHTML = rtext;
       }
    }
    xmlhttpobj.send(null);
}
ImageDialog.preInit();
tinyMCEPopup.onInit.add(ImageDialog.init, ImageDialog);
