/*!
 * REVOLUTION 6.1.6 
 * @version: 1.0 (29.11.2019)
 * @author ThemePunch
*/

/**********************************
	-	GLOBAL VARIABLES	-
**********************************/
;window.RVS = window.RVS === undefined ? {} : window.RVS;
RVS.F = RVS.F === undefined ? {} : RVS.F;
RVS.ENV = RVS.ENV === undefined ? {} : RVS.ENV;
RVS.LIB = RVS.LIB === undefined ? {} : RVS.LIB;
RVS.V = RVS.V === undefined ? {} : RVS.V;
RVS.S = RVS.S === undefined ? {} : RVS.S;
RVS.C = RVS.C === undefined ? {} : RVS.C;
RVS.WIN = RVS.WIN === undefined ? jQuery(window) : RVS.WIN;
RVS.DOC = RVS.DOC === undefined ? jQuery(document) : RVS.DOC;
RVS.OZ = RVS.OZ === undefined ? {} : RVS.OZ;
RVS.SC = RVS.SC === undefined ? {} : RVS.SC;

(function() {
	RVS.V.sizes = RVS.V.sizes==undefined ? ["d","n","t","m"] : RVS.V.sizes;
	RVS.V.dirs = RVS.V.dirs==undefined ? ["top","bottom","left","right"] : RVS.V.dirs;
	RVS.V.dirN = RVS.V.dirN==undefined ? {t:"top",b:"bottom",l:"left",r:"right"} : RVS.V.dirN;
	/*
	DEFINE SHORTCODE FUNCTIONS
	*/
	RVS.SC = RS_SC_WIZARD = {	

		/*
		INITIALISE SHORTCODE MANAGER
		*/
		init : function() {						
			if(typeof QTags !== 'undefined') {			
				var add_rs_button = true;
				if(typeof edButtons !== 'undefined') 
					for(var key in edButtons) {
						if(!edButtons.hasOwnProperty(key) || add_rs_button===false) continue;
						if(edButtons[key].id == 'slider-revolution') add_rs_button = false;							
					}
				
				if(add_rs_button) QTags.addButton('slider-revolution', 'Slider Revolution', function() {RVS.SC.openTemplateLibrary('qtags');});								
			}
			
			if(typeof RVS.LIB.OBJ !== 'undefined' && RVS.LIB.OBJ && RVS.LIB.OBJ.items && RVS.LIB.OBJ.items.length) RVS.SC.defaultAlias = RVS.LIB.OBJ.items[0].alias;						

			// INIT HOOKS AND GET EDITOR TYPE
			elementorHooks();
			vcHooks();
			shortCodeListener();			
		},

		/*
		PARSE SHORTCODE
		*/
		parseShortCode : function(e){
			if (e===undefined) return;
			var t,T,E,a=/(\s+|\W)|(\w+)/g,s="",n="NOT STARTED",r={name:"",attributes:{},content:""},i=(e.match(/\]/g)||[]).length;if(2<i)throw'invalid shortCode: match more then 2 tokens "]". Use only shortcode with this function. Example "[name]teste[/name]" or "[name prop=value]"';for(i=1!==i;null!=(t=a.exec(e))&&(T=t[0],"COMPLETE"!==n);)switch(n){case"NOT STARTED":"["==T&&(n="GETNAME");break;case"GETNAME":/\s/.test(T)?r.name&&(n="PARSING"):/\]/.test(T)?n="GETCONTENT":r.name+=T;break;case"GETCONTENT":/\[/.test(T)?r.name&&(n="COMPLETE"):r.content+=T;break;case"PARSING":if("]"==T)n=1===i?"COMPLETE":"GETCONTENT";else if("="==T){if(!s)throw'invalid token: "'+T+'" encountered at '+t.index;n="GET ATTRIBUTE VALUE"}else/\s/.test(T)?s&&(n="SET ATTRIBUTE"):s+=T;break;case"SET ATTRIBUTE":if(/\s/.test(T))r.attributes[s]=null;else{if("="!=T)throw'invalid token: "'+T+'" encountered at '+t.index;n="GET ATTRIBUTE VALUE"}break;case"GET ATTRIBUTE VALUE":/\s/.test(T)||(n=/["']/.test(T)?(E=T,r.attributes[s]="","GET QUOTED ATTRIBUTE VALUE"):(r.attributes[s]=T,s="","PARSING"));break;case"GET QUOTED ATTRIBUTE VALUE":/\\/.test(T)?n="ESCAPE VALUE":T==E?(n="PARSING",s=""):r.attributes[s]+=T;break;case"ESCAPE VALUE":/\\'"/.test(T)?r.attributes[s]+=T:r.attributes[s]+="\\"+T,n="GET QUOTED ATTRIBUTE VALUE"}return s&&!r.attributes[s]&&(r.attributes[s]=""),r
		},

		/*
		RETURN SHORTCODE TO BLOCK OBJECT
		*/
		scToBlock : function(sc) {			
			var parsed = RVS.SC.parseShortCode(sc);
			var atts = parsed===undefined ? {} : parsed.attributes,
				block = newBlock(atts.alias),
				a,i,j,v,m;			
			// MANAGE OFFSETS
			if (atts.offset!==undefined) {				
				a = atts.offset.split(';');					
				for (i in a) {
					v = a[i].split(":");
					if (v[0]!=="" && v[1]!==undefined) {
						m = v[1].split(",");												
						for (j in m) {
							block.offset[RVS.V.sizes[j]][RVS.V.dirN[v[0]]] = m[j];
							block.offset[RVS.V.sizes[j]]["use"] = true;							
						}
					}						
				}
			}
			// POP UP CHECK
			if (atts.usage && atts.usage==="modal") {
				block.usage = "modal";
				block.modal = true;
				if (atts.modal!==undefined) {
					a = atts.modal.split(";");
					for (i in a) {
						v = a[i].split(":");
						switch(v[0]) {
							case "t":block.popup.time.use = true;block.popup.time.v = v[1];break;
							case "s":block.popup.scroll.use = true;  block.popup.scroll.type="container"; block.popup.scroll.container = v[1]; break;
							case "so":block.popup.scroll.use = true;  block.popup.scroll.type="offset"; block.popup.scroll.v = v[1]; break;
							case "e":block.popup.event.use = true; block.popup.event.v = v[1]; break;
						}
					} 
				}
			}
			// MANAGE ZINDEX && LAYOUT
			if (atts.zindex!==undefined) block.zindex = atts.zindex;
			if (atts.layout!==undefined) block.layout = atts.layout;
			if (atts.slidertitle!==undefined) block.slidertitle = atts.slidertitle;
			else if (atts.sliderTitle!==undefined) block.slidertitle = atts.sliderTitle;
			else if (atts.title!==undefined) block.slidertitle = atts.title;
			return block;
		},
		
		

		updateBlockViews : function(show) {			
		
			if (show===true) jQuery('.rs_optimizer_button_wrapper').closest('.components-panel').addClass("rs_component_panel"); else jQuery('.rs_component_panel').removeClass("rs_component_panel");
		},

		buildShortCode : function() {
			RVS.SC.BLOCK.content = '[rev_slider alias="' + RVS.SC.BLOCK.alias + '"';
			RVS.SC.BLOCK.content += ' slidertitle="'+RVS.SC.BLOCK.slidertitle+'"';
			if (RVS.ENV.activated!==false) updateInherits(true);
			var popup = '', usage ='';
			if (RVS.SC.BLOCK.modal===true) {
				usage = 'modal';
				RVS.SC.BLOCK.content += ' usage="'+usage+'"';				
				if (RVS.SC.BLOCK.popup!==undefined && RVS.ENV.activated!==false) {						
					if (RVS.SC.BLOCK.popup.time!==undefined && RVS.SC.BLOCK.popup.time.use) popup += 't:'+RVS.SC.BLOCK.popup.time.v+";";
					if (RVS.SC.BLOCK.popup.scroll!==undefined && RVS.SC.BLOCK.popup.scroll.use) if(RVS.SC.BLOCK.popup.scroll.type==="offset")  popup += 'so:'+RVS.SC.BLOCK.popup.scroll.v+";"; else popup += 's:'+RVS.SC.BLOCK.popup.scroll.container+";";					
					if (RVS.SC.BLOCK.popup.event!==undefined && RVS.SC.BLOCK.popup.event.use) popup += 'e:'+RVS.SC.BLOCK.popup.event.v+";";
					if (popup!=='') RVS.SC.BLOCK.content +=' modal="'+popup+'"';
				}
			} else {
				if (RVS.ENV.activated!==false) {
					if (RVS.SC.BLOCK.offsettext!==undefined && RVS.SC.BLOCK.offsettext.length>0) RVS.SC.BLOCK.content +=' offset="'+RVS.SC.BLOCK.offsettext+'"'; else RVS.SC.BLOCK.offsettext="";
					if (RVS.SC.BLOCK.zindex!==undefined && RVS.SC.BLOCK.zindex!=="" && RVS.SC.BLOCK.zindex!==0) RVS.SC.BLOCK.content += ' zindex="'+RVS.SC.BLOCK.zindex+'"';
				}
			}
			if (RVS.ENV.activated!==false && RVS.SC.BLOCK.layout !== RVS.SC.BLOCK.origlayout) RVS.SC.BLOCK.content += ' layout="'+RVS.SC.BLOCK.layout+'"';
						
			RVS.SC.BLOCK.content += ']';
			delete RVS.SC.BLOCK.text;			
			return {popup:popup, usage:usage};
		},

		updateShortCode : function() {
			if (RVS!==undefined && RVS.SC!==undefined && RVS.SC.suppress) return;					
			var SC = RVS.SC.buildShortCode();			
			switch(RVS.SC.type) {					
				case 'wpbackery':														
					var temp = jQuery.extend(true,{},RVS.SC.BLOCK);					
					if (SC.usage==="modal") { 
						temp.usage = SC.usage;  temp.modal = SC.popup;delete temp.offset;delete temp.zimdex;
					} else {
						if (temp.offsettext!=="") temp.offset = RVS.SC.BLOCK.offsettext; else delete temp.offset;
						delete temp.usage; delete temp.modal;
					}

					if (temp.layout===temp.origlayout) delete temp.layout;					
					delete temp.offsettext; delete temp.origlayout;	delete temp.content; delete temp.popup;					
					RVS.SC.VC.model.save('params', temp);				
				break;
				
				case 'tinymce':				
					tinyMCE.activeEditor.selection.setContent(RVS.SC.BLOCK.content);				
				break;
				
				case 'elementor':				
					RVS.SC.suppress = true;				
					RVS.SC.EL.model.setSetting('revslidertitle',  RVS.SC.BLOCK.slidertitle);
					RVS.SC.EL.model.setSetting('shortcode', RVS.SC.BLOCK.content);					
					RVS.SC.EL.control.find('input[data-setting="shortcode"]').trigger('input');
					setTimeout(function() {RVS.SC.suppress = false;}, 500);									
				break;				
				case 'qtags':				
					QTags.insertContent(RVS.SC.BLOCK.content);				
				break;
				case 'gutenberg':					
					var obj = {slidertitle: RVS.SC.BLOCK.slidertitle, alias: RVS.SC.BLOCK.alias, modal: RVS.SC.BLOCK.modal ,  content: RVS.SC.BLOCK.content , zindex: RVS.SC.BLOCK.zindex , wrapperid: RVS.SC.BLOCK.wrapperid};					
					revslider_react.setState(obj);						
					revslider_react.props.setAttributes(obj);
					revslider_react.forceUpdate();
				break;
				default:break;
			}
		},
				
		openTemplateLibrary: function(type) {		
			// 5.0 to 6.0 update patch
			if(typeof RVS.LIB.OBJ === 'undefined') return;
			if (type==="tinymce") {
				RVS.SC.BLOCK = {};
			}						
			RVS.SC.type = type;
			if(!RVS.SC.libraryInited) {				
				RVS.SC.libraryInited = true;
				RVS.F.initObjectLibrary(true); 
				RVS.F.initOnOff(jQuery('#obj_addsliderasmodal').css('display', 'inline-block'));				
				
				
				jQuery('body').on('change', '#sel_olibrary_sorting', function() {										
					jQuery('#reset_objsorting').css((this.value === 'datedesc' ? {display: 'none'} : {display: 'inline-block', opacity: '1', visibility: 'visible'} ));
					if(this.dataset.evt!==undefined) RVS.DOC.trigger(this.dataset.evt, this.dataset.evtparam);					
				}).on('change', '#ol_pagination', function(e) {					
					if(this.dataset.evt!==undefined) RVS.DOC.trigger(this.dataset.evt,[e, this.value, this.dataset.evtparam]);				
				});			
			}
			
			var successObj = {modules: 'addRevSliderShortcode', event: 'selectRevSliderItem'};						
			jQuery('#obj_addsliderasmodal .tponoffwrap').addClass('off').find('input').removeAttr('checked').prop('checked', false);
			RVS.F.openObjectLibrary({types: ['modules'], filter: 'all', selected: ['modules'], success: successObj});
			
			var folder = RVS.F.getCookie('rs6_wizard_folder');
			if(folder && folder !== -1 && folder !== '-1' && ((RVS.LIB.OBJ !==undefined && RVS.LIB.OBJ.items!==undefined && RVS.LIB.OBJ.items.modules!==undefined))) RVS.F.changeOLIBToFolder(folder);		
			
		},
		
		openBlockSettings : function(type,sc){
			
			if (RVS.ENV.activated!==true) RVS.F.showRegisterSliderInfo();
			if (sc===undefined && RVS.SC.BLOCK===undefined) return;			
			RVS.SC.BLOCK = sc!==undefined ?  RVS.SC.scToBlock(sc) : RVS.SC.BLOCK===undefined || RVS.SC.BLOCK.text===undefined ? RVS.SC.scToBlock(RVS.SC.BLOCK.content) : RVS.SC.scToBlock(RVS.SC.BLOCK.text);

			if (RVS!==undefined && RVS.SC!==undefined && RVS.SC.BLOCK!==undefined && RVS.SC.BLOCK.alias.length>0) {	
				RVS.SC.type = type;
				//Ajax Call to get the original Layout
			      RVS.F.ajaxRequest('getSliderSizeLayout', { alias : RVS.SC.BLOCK.alias }, function(response) {    
			      	
			          if(response.success) {  
			          	if (response!==undefined && response.layout!==undefined) {
			          		response.layout = response.layout===undefined || response.layout==="" ? "auto" : response.layout;
			          		 RVS.SC.BLOCK.origlayout = response.layout;
			          		 RVS.SC.BLOCK.slidertitle = response.slidertitle!==undefined ? response.slidertitle : response.sliderTitle!==undefined ? response.sliderTitle : response.title!==undefined ? response.title : RVS.SC.BLOCK.slidertitle;			  
			                if(typeof RVS.SC.BLOCK.layout === "undefined" || RVS.SC.BLOCK.layout==="") RVS.SC.BLOCK.layout = RVS.SC.BLOCK.origlayout;
			          	}          			             
			            RVS.F.showWaitAMinute({fadeIn:0,text:RVS_LANG.loadingcontent});				
						RVS.F.initOnOff(jQuery('#rbm_blocksettings'));
						RVS.F.RSDialog.create({modalid:'#rbm_blocksettings', bgopacity:0.5});
						jQuery('#rbm_blocksettings').RSScroll({wheelPropagation:false, suppressScrollX:true});
						RVS.F.RSDialog.center();			
						jQuery('#rbm_blocksettings .origlayout').hide();
						jQuery('#rbm_blocksettings .origlayout.origlayout_'+RVS.SC.BLOCK.origlayout).show();		
						
						if (RVS.SC.type==="wpbackery") {			
							setTimeout(RVS.F.RSDialog.center,19);
							setTimeout(RVS.F.RSDialog.center,50);
						}
						blockSettingsUpdate();
			          }
			      });										
		    } 		
		},

		openSliderEditor : function(alias) { if (alias!==undefined && alias.length>0) window.open(RVS.ENV.admin_url+"&view=slide&alias="+alias);},
		openOptimizer : function(alias){ if (alias!==undefined && alias.length>0) RVS.F.openOptimizer({alias:alias});}						
	};

	// INITIALISE PROCESSES
	if (document.readyState === "loading") 
		window.addEventListener('DOMContentLoaded',function() {	RVS.SC.init();	});
	else
		RVS.SC.init();
		
	function updateInherits(novisual) {
		if (RVS==undefined || RVS.SC.BLOCK==undefined || RVS.SC.BLOCK.offset===undefined) return;
		var inh = {top:0, bottom:0, left:0, right:0},val,s,d,txt='',com,nxt;
		for (var j in RVS.V.dirs) {
			d = RVS.V.dirs[j];			
			com = false;					
			txt += RVS.V.dirs[j][0]+":";
			for (var i in RVS.V.sizes) {				
				s = RVS.V.sizes[i];	
				nxt = (s=="d" && (RVS.SC.BLOCK.offset.d.use || RVS.SC.BLOCK.offset.n.use || RVS.SC.BLOCK.offset.t.use || RVS.SC.BLOCK.offset.n.use)) || (s=="n" && (RVS.SC.BLOCK.offset.n.use || RVS.SC.BLOCK.offset.t.use || RVS.SC.BLOCK.offset.n.use)) || (s=="t" && (RVS.SC.BLOCK.offset.t.use || RVS.SC.BLOCK.offset.m.use)) || (s=="m" &&  RVS.SC.BLOCK.offset.m.use);
				if (com && nxt) txt +=',';
				com = true;
				if (novisual!==true) {
					var inp = jQuery("#rbm_blocksettings .scblockinput[data-r='offset."+s+"."+d+"']");
					if (inp[0]===undefined) continue;				
					inp[0].dataset.s = s;
				}
				if (RVS.SC.BLOCK.offset[s].use) {
					inh[d] = val = RVS.SC.BLOCK.offset[s][d];
					if (novisual!==true) inp[0].style.opacity = 1;
				} else {
					val = inh[d];
					if (novisual!==true) inp[0].style.opacity = 0.5;
				}								
				if (novisual!==true) inp[0].value = val;
				if (nxt) txt +=val; else com = false;
			}
			txt +=';'
		}		
		if (txt==="t:;b:;l:;r:;") txt="";
		RVS.SC.BLOCK.offsettext = txt;		
	}

	function newBlock(alias) {		
		alias = alias===undefined ? "" : alias;
		return new Object({
			alias:alias,
			zindex:0,
			popup: { time : {use:false, v:2000}, 
					 scroll : {use:false, type:"offset", v:2000,container:""},
					 event : {use:false, v:"popup_"+alias}},
			offset: { d : {top:"0px", bottom:"0px", left:"0px", right:"0px" ,use:false}, 
					  n : {top:"0px", bottom:"0px", left:"0px", right:"0px",use:false}, 
					  t : {top:"0px", bottom:"0px", left:"0px", right:"0px",use:false}, 
					  m : {top:"0px", bottom:"0px", left:"0px", right:"0px",use:false}},
			modal: false
		})
	}

	function blockSettingsUpdate() {			
		RVS.F.updateEasyInputs({path:'SC.BLOCK.', container:'#rbm_blocksettings', root:RVS});
		RVS.F.updateAllOnOff();
		updateInherits();
		jQuery('.scblockinput').trigger('init');
		if (RVS.SC.BLOCK.popup!==undefined) {
			document.getElementById('srbs_scr_evt').innerHTML = RVS.SC.BLOCK.popup.event.v;
			if (RVS.ENV.activated!==false) jQuery('.rb_not_on_notactive').removeClass("disabled"); else jQuery('.rb_not_on_notactive').addClass("disabled");
		}
	}

	function blockSettingsReset() {
		if (RVS.SC.BLOCK!==undefined) {
			RVS.SC.BLOCK.zindex = 0;
			RVS.SC.BLOCK.popup = { time : {use:false, v:2000}, scroll : {use:false, type:"offset", v:2000,container:""},event : {use:false, v:"popup_"+RVS.SC.BLOCK.alias}};
			RVS.SC.BLOCK.offset = { d : {top:"0px", bottom:"0px", left:"0px", right:"0px" ,use:false}, n : {top:"0px", bottom:"0px", left:"0px", right:"0px",use:false}, t : {top:"0px", bottom:"0px", left:"0px", right:"0px",use:false}, m : {top:"0px", bottom:"0px", left:"0px", right:"0px",use:false}};
			RVS.SC.BLOCK.modal = false;
		}
	}

/*
ELEMENTOR HOOKS
 */
	function elementorHooks() {

		if (typeof elementor!=="undefined"  && elementor.hooks!==undefined) {			
		
			elementor.hooks.addAction( 'panel/open_editor/widget/slider_revolution', function( panel, model, view ) {
				RVS.SC.type = "elementor";
				RVS.SC.EL = RVS.SC.EL===undefined ? {} : RVS.SC.EL;
				RVS.SC.EL.control = panel.$el.find('#elementor-controls');
				RVS.SC.EL.view = view;
				RVS.SC.EL.model = model;

				// CHECK ALIAS FOR FROM VERSION 6.1.6 +
				if (view!==undefined && view.container!==undefined && view.container.settings!==undefined && view.container.settings.attributes!==undefined) {
					
					if (view.container.settings.attributes.shortcode!==undefined) {
						RVS.SC.BLOCK = RVS.SC.scToBlock(view.container.settings.attributes.shortcode);
						// FALLBACK
						if (view.container.settings.attributes.revslidertitle!==undefined) RVS.SC.BLOCK.slidertitle = view.container.settings.attributes.revslidertitle;
						if (view.container.settings.attributes.modal!==undefined) RVS.SC.BLOCK.modal = view.container.settings.attributes.modal;

					} 
				}
				//STYLING ELEMNTOR TO LOOK MORE SLIDER REVOLUTION LIKE
				jQuery('.elementor-component-tab.elementor-panel-navigation-tab.elementor-tab-control-advanced').hide();
				RVS.SC.EL.control.addClass("rs-elementor-component-tab");				
			});

			// BASIC LISTENER
			window.elementorSelectRevSlider = function(e) {	if (e) RVS.SC.openTemplateLibrary('elementor'); else jQuery('button[data-event="themepunch.selectslider"]').click();}
			/*
			 FURTHER LISTNERS
			*/
			RVS.DOC.on('click', 'button[data-event="themepunch.selectslider"]', function() {RVS.SC.openTemplateLibrary('elementor');});
			RVS.DOC.on('click', 'button[data-event="themepunch.settingsslider"]',  function() {RVS.SC.openBlockSettings('elementor',(RVS.SC.EL.view!==undefined && RVS.SC.EL.view.container!==undefined && RVS.SC.EL.view.container.settings!==undefined && RVS.SC.EL.view.container.settings.attributes!==undefined && RVS.SC.EL.view.container.settings.attributes.shortcode!==undefined ? RVS.SC.EL.view.container.settings.attributes.shortcode : {}));});
			RVS.DOC.on('click', 'button[data-event="themepunch.editslider"]', function() {RVS.SC.openSliderEditor(RVS.SC.BLOCK.alias)});
			RVS.DOC.on('click', 'button[data-event="themepunch.optimizeslider"]', function() {RVS.SC.openOptimizer(RVS.SC.BLOCK.alias)});
		}		
	}

/*
VISUAL COMPOSER HOOKS
*/ 	 	
 	function vcHooks() {
 		function convertVCParamsToSC(params) { 		
	 		var temp = jQuery.extend(true,{},params),
	 			sc = '[rev_slider alias="' + temp.alias + '"';
	 		
	 		if (temp.slidertitle!==undefined) sc+= ' slidertitle="'+temp.slidertitle+'"'; else
	 		if (temp.sliderTitle!==undefined) sc+= ' slidertitle="'+temp.sliderTitle+'"'; else
	 		if (temp.title!==undefined) sc+= ' slidertitle="'+temp.title+'"'; 
			
			if (temp.modal!==undefined) sc+= ' modal="'+temp.modal+'"';
			if (temp.usage!==undefined) sc+= ' usage="'+temp.usage+'"';
			if (temp.offset!==undefined) sc+= ' offset="'+temp.offset+'"';
			if (temp.zindex!==undefined) sc+= ' zindex="'+temp.zindex+'"';
			if (temp.layout!==undefined) sc+= ' layout="'+temp.layout+'"';						
			sc += ']';
			return sc;
	 	}

	 	/*
		OPEN TEMPLATE LIBRARY FOR VC
		 */
		function VCopenTemplateLibrary(params) {		
			jQuery('.wpb-element-edit-modal').hide(); //hide the normal VC window and use own (old vc version)
			jQuery('#vc_properties-panel').hide(); //hide the normal VC window and use own (new vc version)		
			RVS.SC.BLOCK = RVS.SC.scToBlock(convertVCParamsToSC(params));
			RVS.SC.openTemplateLibrary('wpbackery');	
		}

		if (typeof vc==="undefined" || vc==undefined) return;
		window.VcSliderRevolution = vc.shortcode_view.extend({
			events: {
				'click > .vc_controls .vc_control_rev_optimizer': 'rs_optim',
				'click > .vc_controls .vc_control_rev_selector': 'rs_select',
				'click > .vc_controls .vc_control_rev_settings': 'rs_settings',
				'click .column_delete,.vc_control-btn-delete': 'deleteShortcode',
				'click .vc_control-btn-edit': 'editElement',
				'click .column_clone,.vc_control-btn-clone': 'clone',
				mousemove: "checkControlsPosition"
			},
			initialize: function() {return window.VcSliderRevolution.__super__.initialize.call(this);},
			render: function () { RVS.SC.VC = this;	if(vc.add_element_block_view.$el.is(':visible')) VCopenTemplateLibrary(this.model.get('params'));return window.VcSliderRevolution.__super__.render.call(this);},
			editElement: function() { RVS.SC.openSliderEditor(this.model.get('params').alias);},
			rs_select : function() { RVS.SC.VC = this;VCopenTemplateLibrary(this.model.get('params'));},
			rs_optim : function() { RVS.SC.openOptimizer(this.model.get('params').alias);},
			rs_settings : function() { RVS.SC.VC = this; RVS.SC.openBlockSettings('wpbackery',convertVCParamsToSC(this.model.get('params')));}
		});

		if(typeof(window.InlineShortcodeView) !== 'undefined') {			
			var rs_show_frontend_overlay = false;
			jQuery(window).on('vc_build', function() {				
				vc.add_element_block_view.$el.find('[data-element="rev_slider"]').click(function() {
					rs_show_frontend_overlay = true;
				});				
			});		
			window.InlineShortcodeView_rev_slider = window.InlineShortcodeView.extend({	
				events: {
					'click > .vc_controls .vc_control_rev_optimizer': 'rs_optim',
					'click > .vc_controls .vc_control_rev_selector': 'rs_select',
					'click > .vc_controls .vc_control_rev_settings': 'rs_settings',
					'click .column_delete,.vc_control-btn-delete': 'destroy',
					'click .vc_control-btn-edit': 'edit',					
					mousemove: "checkControlsPosition"
				},					
				render: function() {																
					RVS.SC.VC = this;					
					if(rs_show_frontend_overlay) VCopenTemplateLibrary(this.model.get('params'))
					window.InlineShortcodeView_rev_slider.__super__.render.call(this);					
					var mv = this.$controls.find('.vc_element-move');					
					if (this.$controls[0].getElementsByClassName('vc_control_rev_optimizer').length===0) jQuery('<a class="vc_control-btn vc_control_rev_optimizer" href="#" title="File Size Optimizer"><span class="vc_btn-content"><i class="revslider_vc_material-icons material-icons">flash_on</i></span></a>').insertAfter(mv);
					if (this.$controls[0].getElementsByClassName('vc_control_rev_settings').length===0) jQuery('<a class="vc_control-btn vc_control_rev_settings" href="#" title="Module Settings"><span class="vc_btn-content"><i class="revslider_vc_material-icons material-icons">tune</i></span></a>').insertAfter(mv);
					if (this.$controls[0].getElementsByClassName('vc_control_rev_selector').length===0) jQuery('<a class="vc_control-btn vc_control_rev_selector" href="#" title="Select New Slider Revolution 6 Module"><span class="vc_btn-content"><i class="revslider_vc_material-icons material-icons">cached</i></span></a>').insertAfter(mv);				
					if (this.$controls[0].getElementsByClassName('vc_control_rev_edit').length===0) mv.find('.vc_control-btn.vc_control-btn-edit').addClass('vc_control_rev_edit');
					this.$controls.find('.vc_control-btn-clone').hide();
					return this;					
				},
				rs_settings : function() { RVS.SC.VC = this; RVS.SC.openBlockSettings('wpbackery',convertVCParamsToSC(this.model.get('params'))); return false;},
				rs_optim : function() {	RVS.SC.openOptimizer(this.model.get('params').alias);return false;},				
				update: function(model) {	rs_show_frontend_overlay = false;window.InlineShortcodeView_rev_slider.__super__.update.call(this, model);return this;},
				edit: function() {	RVS.SC.openSliderEditor(this.model.get('params').alias);return false;},
				rs_select : function() { RVS.SC.VC = this;	VCopenTemplateLibrary(this.model.get('params'));return false; },			
			});		
		};

		/*
		LISTENERS
		*/
		RVS.DOC.on('mouseenter','.wpb_rev_slider.wpb_content_element.wpb_sortable',function() {
			//CHECK TOOLBAR OF VC
			var controls = jQuery(this.getElementsByClassName('vc_controls-cc')[0]);
			if (controls!==undefined) {
				var mv = controls.find('.vc_element-move');
				if (this.getElementsByClassName('vc_control_rev_optimizer').length===0) jQuery('<a class="vc_control-btn vc_control_rev_optimizer" href="#" title="File Size Optimizer"><span class="vc_btn-content"><i class="revslider_vc_material-icons material-icons">flash_on</i></span></a>').insertAfter(mv);
				if (this.getElementsByClassName('vc_control_rev_settings').length===0) jQuery('<a class="vc_control-btn vc_control_rev_settings" href="#" title="Module Settings"><span class="vc_btn-content"><i class="revslider_vc_material-icons material-icons">tune</i></span></a>').insertAfter(mv);
				if (this.getElementsByClassName('vc_control_rev_selector').length===0) jQuery('<a class="vc_control-btn vc_control_rev_selector" href="#" title="Select New Slider Revolution 6 Module"><span class="vc_btn-content"><i class="revslider_vc_material-icons material-icons">cached</i></span></a>').insertAfter(mv);				
				if (this.getElementsByClassName('vc_control_rev_edit').length===0) mv.find('.vc_control-btn.vc_control-btn-edit').addClass('vc_control_rev_edit');				
			}
		});				
	}
	
	function shortCodeListener() {		
		if (RVS.S.shortCodeListener!==undefined) return;		
		RVS.S.shortCodeListener = true;

		// COOKIE HANDLING
		jQuery('body').on('click', '#objectlibrary *[data-folderid]', function() {RVS.F.setCookie("rs6_wizard_folder",this.dataset.folderid,360);});

		// 
		RVS.F.initOnOff(jQuery('#slide_template_row').css('display', 'inline-block'));	
		
		RVS.DOC.on('registrationdone',function() {
			if (RVS.ENV.activated===false) {
				jQuery('.rs_wp_plg_act_wrapper').show(); 
				jQuery('.rb_not_on_notactive').addClass("disabled");
			} else {
				jQuery('.rs_wp_plg_act_wrapper').hide();
				jQuery('.rb_not_on_notactive').removeClass("disabled");
			}			
		});
		
		if (RVS.ENV.activated===false) {
			jQuery('.rs_wp_plg_act_wrapper').show();
			RVS.DOC.on('click','.rs_wp_plg_act_wrapper',RVS.F.showRegisterSliderInfo);			
		} else {
			jQuery('.rs_wp_plg_act_wrapper').hide();
		}

		/**********************************
			-	PAGE BACKGROUND	COLOR   -
		**********************************/
		// Color Picker
		jQuery('#rs_page_bg_color').rsColorPicker({
			init: function(inputWrap, inputElement, cssColor, widgetSettings) {								
				var ghost = jQuery('<input type="text" class="layerinput" value="' + inputElement.val() + '">').appendTo(inputWrap);								
				inputElement.data('ghost', ghost).hide();				
			},
			change:function(currentInput, cssColor, gradient, globalColors, globalColorActive) {				
				currentInput.data('ghost').val(cssColor);
				currentInput.val(cssColor);
			}
		});			

		// Page Template , Color Picker, checkbox check only when RevSlider Blank Template
		jQuery('body').on('change', '.editor-page-attributes__template select', function() {
			if(jQuery(this).val() === "../public/views/revslider-page-template.php"){
				jQuery('#rs_page_bg_color_column').show(); 
				jQuery('#rs_blank_template').prop('checked', true);
			}
			else {
				jQuery('#rs_page_bg_color_column').hide();									
				jQuery('#rs_blank_template').prop('checked', false);
			}
		});
		
		// Page Template , checkbox check sync Page Template Selectbox
		jQuery('body').on('change', '#rs_blank_template', function() {
			if(jQuery(this).prop('checked')){
				jQuery('.editor-page-attributes__template select').val("../public/views/revslider-page-template.php").change(); 
				jQuery('#rs_page_bg_color_column').show(); 
			}
			else {
				jQuery('.editor-page-attributes__template select').val("").change();
				jQuery('#rs_page_bg_color_column').hide();
			}
		});

							
		/*
		DEFAULT LISTENERS
		 */
		RVS.DOC.on('click','.block-editor-editor-skeleton__content, .interface-interface-skeleton__content', function() {RVS.SC.updateBlockViews(true);});		
		RVS.DOC.on('addRevSliderShortcode', function(e, data) {				
			if(data!==undefined && data.alias !== '-1'){
				data.size = data.size==="" || data.size===undefined ? "auto" : data.size;				
				var block = newBlock(data.alias);				
				RVS.SC.BLOCK = jQuery.extend(true,block,RVS.SC.BLOCK);
				RVS.SC.BLOCK.alias = data.alias;				
				RVS.SC.BLOCK.slidertitle = data.slidertitle!==undefined ? data.slidertitle : data.title!==undefined ? data.title : data.alias;				
				RVS.SC.BLOCK.layout = RVS.SC.BLOCK.origlayout = data.size;				 
				RVS.SC.updateShortCode();
			}		
		});

		RVS.DOC.on('selectRevSliderItem', function() {		
			var folder = RVS.F.getCookie('rs6_wizard_folder');		
			if(folder && folder !== -1 && folder !== '-1') RVS.F.changeOLIBToFolder(folder);			
		});

		
		// CLOSE EDITOR
		RVS.DOC.on('click','#rbm_blocksettings .rbm_close' , function() {
			RVS.SC.updateShortCode();											
			RVS.F.RSDialog.close();
		});
				
		RVS.DOC.on('focus','.scblockinput',function() {						
			this.dataset.focusvalue = this.value;
			this.style.opacity = 1;
		});

		RVS.DOC.on('change blur','.scblockinput',function() {			
			if (this.dataset.s!==undefined && this.dataset.focusvalue!==this.value)	RVS.SC.BLOCK.offset[this.dataset.s].use = true;
			blockSettingsUpdate();
		});

		RVS.DOC.on('updateSRBSSVREVT',function(e,v){
			if(v!==undefined) {
				if (v.val==="") RVS.SC.BLOCK.popup.event.v = "popup_"+RVS.SC.BLOCK.alias;
				document.getElementById('srbs_scr_evt').innerHTML = v.val;
			}
		});
	}

	
})();