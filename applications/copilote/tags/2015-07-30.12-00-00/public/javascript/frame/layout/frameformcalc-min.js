YUI.add("frameformcalc",function(b){function a(){a.superclass.constructor.apply(this,arguments)}a.NAME="FrameFormCalc";a.ATTRS={oIOManager:{value:null},oTabView:{value:null}};b.FrameFormCalc=b.extend(a,b.FrameForm,{initializer:function(){if(this.DataSetManager().DataSet("bdgt")){oDataSet=this.DataSetManager().DataSet("bdgt");aRecord=oDataSet.RowData().GetRecord();this.get("oLayout").on("layout:intialized",function(){b.one(".montant_c6 input").on("valuechange",this.updateCalcMontantC6,this);b.one(".montant_d6 input").on("valuechange",this.updateCalcMontantD6,this);b.one(".montant_e6 input").on("valuechange",this.updateCalcMontantE6,this);b.one(".montant_f6 input").on("valuechange",this.updateCalcMontantF6,this);b.one(".montant_g6 input").on("valuechange",this.updateCalcMontantG6,this)},this)}if(this.DataSetManager().DataSet("rh")){oDataSet=this.DataSetManager().DataSet("rh");aRecord=oDataSet.RowData().GetRecord();this.get("oLayout").on("layout:intialized",function(){nodeListRHCoutTotMens=b.all(".rh_typerecrut select, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select");nodeListRHCoutTotMens.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcRHCoutTotMens,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcRHCoutTotMens,this)}},this);nodeListRHImpSCSPTot=b.all(".rh_typerecrut select, .rh_couttotmens input, .rh_impscspduree input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impscspmontant input");nodeListRHImpSCSPTot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcRHImpSCSPTot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcRHImpSCSPTot,this)}},this);oTabView=b.all(".yui3-tabview-content ul li a");oTabView.each(function(c){c.on("click",this.addEventsRHImpC1Tot,this);c.on("click",this.addEventsRHImpC2Tot,this);c.on("click",this.addEventsRHCoutTot,this)},this);nodeListRHImpC2Tot=b.all(".rh_typerecrut select, .rh_impc2 select, .rh_typec2 input, .rh_impc2duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc2montant input");nodeListRHImpC2Tot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcRHImpC2Tot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcRHImpC2Tot,this)}},this);nodeListRHImpC1Tot=b.all(".rh_typerecrut select, .rh_impc1 select, .rh_typec1 input, .rh_impc1duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc1montant input");nodeListRHImpC1Tot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcRHImpC1Tot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcRHImpC1Tot,this)}},this);nodeListRHCoutTot=b.all(".rh_typerecrut select, .rh_impc1 select, .rh_typec1 input, .rh_impc1duree input, .rh_impc2 select, .rh_typec2 input, .rh_impc2duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc1montant input, .rh_impc2montant input, .rh_impscspduree input, .rh_impscspmontant input");nodeListRHCoutTot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcRHCoutTot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcRHCoutTot,this)}},this)},this)}if(this.DataSetManager().DataSet("dt")){oDataSet=this.DataSetManager().DataSet("dt");aRecord=oDataSet.RowData().GetRecord();this.get("oLayout").on("layout:intialized",function(){nodeListDTCouTot=b.all(".dt_lieudest input, .dt_montantij input, .dt_nbj input, .dt_couttrans input, .dt_coutdiv input");nodeListDTCouTot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcDTCouTot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcDTCouTot,this)}},this)},this)}if(this.DataSetManager().DataSet("dmp")){oDataSet=this.DataSetManager().DataSet("dmp");aRecord=oDataSet.RowData().GetRecord();this.get("oLayout").on("layout:intialized",function(){nodeListDMPEJN1=b.all(".ec_diminution_ae input, .ec_diminution_cp input");nodeListDMPEJN1.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcDMPEJN1,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcDMPEJN1,this)}},this)},this)}if(this.DataSetManager().DataSet("sta")){oDataSet=this.DataSetManager().DataSet("sta");aRecord=oDataSet.RowData().GetRecord();this.get("oLayout").on("layout:intialized",function(){nodeListSTAImpSCSPTot=b.all(".sta_impscspduree input, .sta_impscspmontant input");nodeListSTAImpSCSPTot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcSTAImpSCSPTot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcSTAImpSCSPTot,this)}},this);nodeListSTAImpC1Tot=b.all(".sta_impc1 select, .sta_typec1 input, .sta_impc1duree input, .sta_impc1montant input");nodeListSTAImpC1Tot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcSTAImpC1Tot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcSTAImpC1Tot,this)}},this);nodeListSTAImpC2Tot=b.all(".sta_impc2 select, .sta_typec2 input, .sta_impc2duree input, .sta_impc2montant input");nodeListSTAImpC2Tot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcSTAImpC2Tot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcSTAImpC2Tot,this)}},this);nodeListSTACoutTot=b.all(".sta_impscspduree input, .sta_impscspmontant input, .sta_impc1 select, .sta_typec1 input, .sta_impc1duree input, .sta_impc1montant input, .sta_impc2 select, .sta_typec2 input, .sta_impc2duree input, .sta_impc2montant input");nodeListSTACoutTot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcSTACoutTot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcSTACoutTot,this)}},this);oTabView=b.all(".yui3-tabview-content ul li a");oTabView.each(function(c){c.on("click",this.addEventsSTAImpC1Tot,this);c.on("click",this.addEventsSTAImpC2Tot,this);c.on("click",this.addEventsSTACoutTot,this)},this)},this)}},addEventsSTAImpC1Tot:function(){setTimeout(function(){nodeListSTAImpC1Tot=b.all(".sta_impc1 select, .sta_typec1 input, .sta_impc1duree input, .sta_impc1montant input");nodeListSTAImpC1Tot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcSTAImpC1Tot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcSTAImpC1Tot,this)}},this)}.bind(this),500)},addEventsSTAImpC2Tot:function(){setTimeout(function(){nodeListSTAImpC2Tot=b.all(".sta_impc2 select, .sta_typec2 input, .sta_impc2duree input, .sta_impc2montant input");nodeListSTAImpC2Tot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcSTAImpC2Tot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcSTAImpC2Tot,this)}},this)}.bind(this),500)},addEventsSTACoutTot:function(){setTimeout(function(){nodeListSTACoutTot=b.all(".sta_impscspduree input, .sta_impscspmontant input, .sta_impc1 select, .sta_typec1 input, .sta_impc1duree input, .sta_impc1montant input, .sta_impc2 select, .sta_typec2 input, .sta_impc2duree input, .sta_impc2montant input");nodeListSTACoutTot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcSTACoutTot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcSTACoutTot,this)}},this)}.bind(this),500)},addEventsRHImpC1Tot:function(){setTimeout(function(){nodeListRHImpC1Tot=b.all(".rh_typerecrut select, .rh_impc1 select, .rh_typec1 input, .rh_impc1duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc1montant input");nodeListRHImpC1Tot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcRHImpC1Tot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcRHImpC1Tot,this)}},this)}.bind(this),500)},addEventsRHImpC2Tot:function(){setTimeout(function(){nodeListRHImpC2Tot=b.all(".rh_typerecrut select, .rh_impc2 select, .rh_typec2 input, .rh_impc2duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc2montant input");nodeListRHImpC2Tot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcRHImpC2Tot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcRHImpC2Tot,this)}},this)}.bind(this),500)},addEventsRHCoutTot:function(){setTimeout(function(){nodeListRHCoutTot=b.all(".rh_typerecrut select, .rh_impc1 select, .rh_typec1 input, .rh_impc1duree input, .rh_impc2 select, .rh_typec2 input, .rh_impc2duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc1montant input, .rh_impc2montant input, .rh_impscspduree input, .rh_impscspmontant input");nodeListRHCoutTot.each(function(c){if(c.get("tagName")=="SELECT"){c.on("change",this.updateCalcRHCoutTot,this)}if(c.get("tagName")=="INPUT"){c.on("valuechange",this.updateCalcRHCoutTot,this)}},this)}.bind(this),500)},updateCalcMontantC6:function(e){oDataSet=this.DataSetManager().DataSet("bdgt");aRecord=oDataSet.RowData().GetRecord();var h=(e.target.get("value")==null||e.target.get("value")==""?0:e.target.get("value"));var f=(aRecord.montant_c7==null?0:aRecord.montant_c7);var n=(aRecord.montant_d6==null?0:aRecord.montant_d6);var l=(aRecord.montant_e6==null?0:aRecord.montant_e6);var g=(aRecord.montant_f6==null?0:aRecord.montant_f6);var m=(aRecord.montant_g6==null?0:aRecord.montant_g6);var j=(aRecord.montant_h7==null?0:aRecord.montant_h7);var d=parseFloat(h)+parseFloat(f);var k=parseFloat(h)+parseFloat(n)+parseFloat(g);var c=parseFloat(h)+parseFloat(l)+parseFloat(m);var i=parseFloat(k)+parseFloat(j);this._setFieldValue("bdgt","montant_c8",Math.round(d));this._setFieldValue("bdgt","montant_h6",Math.round(k));this._setFieldValue("bdgt","montant_i6",Math.round(c));this._setFieldValue("bdgt","montant_h8",Math.round(i))},updateCalcMontantD6:function(c){oDataSet=this.DataSetManager().DataSet("bdgt");aRecord=oDataSet.RowData().GetRecord();var e=(aRecord.montant_c6==null?0:aRecord.montant_c6);var k=(c.target.get("value")==null||c.target.get("value")==""?0:c.target.get("value"));var j=(aRecord.montant_d7==null?0:aRecord.montant_d7);var d=(aRecord.montant_f6==null?0:aRecord.montant_f6);var g=(aRecord.montant_h7==null?0:aRecord.montant_h7);var i=parseFloat(k)+parseFloat(j);var h=parseFloat(e)+parseFloat(k)+parseFloat(d);var f=parseFloat(h)+parseFloat(g);this._setFieldValue("bdgt","montant_d8",Math.round(i));this._setFieldValue("bdgt","montant_h6",Math.round(h));this._setFieldValue("bdgt","montant_h8",Math.round(f))},updateCalcMontantE6:function(d){oDataSet=this.DataSetManager().DataSet("bdgt");aRecord=oDataSet.RowData().GetRecord();var f=(aRecord.montant_c6==null?0:aRecord.montant_c6);var i=(d.target.get("value")==null||d.target.get("value")==""?0:d.target.get("value"));var h=(aRecord.montant_d7==null?0:aRecord.montant_d7);var j=(aRecord.montant_g6==null?0:aRecord.montant_g6);var c=(aRecord.montant_i7==null?0:aRecord.montant_i7);var g=parseFloat(i)+parseFloat(h);var e=parseFloat(f)+parseFloat(i)+parseFloat(j);var k=e+c;this._setFieldValue("bdgt","montant_e8",Math.round(g));this._setFieldValue("bdgt","montant_i6",Math.round(e));this._setFieldValue("bdgt","montant_i8",Math.round(k))},updateCalcMontantF6:function(d){oDataSet=this.DataSetManager().DataSet("bdgt");aRecord=oDataSet.RowData().GetRecord();var g=(aRecord.montant_c6==null?0:aRecord.montant_c6);var f=(d.target.get("value")==null||d.target.get("value")==""?0:d.target.get("value"));var e=(aRecord.montant_f7==null?0:aRecord.montant_f7);var k=(aRecord.montant_d6==null?0:aRecord.montant_d6);var i=(aRecord.montant_h7==null?0:aRecord.montant_h7);var c=parseFloat(f)+parseFloat(e);var j=parseFloat(g)+parseFloat(k)+parseFloat(f);var h=parseFloat(j)+parseFloat(i);this._setFieldValue("bdgt","montant_f8",Math.round(c));this._setFieldValue("bdgt","montant_h6",Math.round(j));this._setFieldValue("bdgt","montant_h8",Math.round(h))},updateCalcMontantG6:function(d){oDataSet=this.DataSetManager().DataSet("bdgt");aRecord=oDataSet.RowData().GetRecord();var f=(aRecord.montant_c6==null?0:aRecord.montant_c6);var j=(d.target.get("value")==null||d.target.get("value")==""?0:d.target.get("value"));var i=(aRecord.montant_g7==null?0:aRecord.montant_g7);var g=(aRecord.montant_e6==null?0:aRecord.montant_e6);var c=(aRecord.montant_i7==null?0:aRecord.montant_i7);var h=parseFloat(j)+parseFloat(i);var e=parseFloat(f)+parseFloat(g)+parseFloat(j);var k=e+c;this._setFieldValue("bdgt","montant_g8",Math.round(h));this._setFieldValue("bdgt","montant_i6",Math.round(e));this._setFieldValue("bdgt","montant_i8",Math.round(k))},updateCalcRHCoutTotMens:function(f){oDataSet=this.DataSetManager().DataSet("rh");aRecord=oDataSet.RowData().GetRecord();var e=null;var c=parseFloat(0);var g=parseFloat(0);var h="0";var d={"0":0,"43":0.5,"44":0.6,"45":0.7,"46":0.8,"47":0.9,"48":1};if(f.target.ancestor(".field").hasClass("rh_coutmenscharg")){c=parseFloat(f.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.rh_coutmenscharg)}if(f.target.ancestor(".field").hasClass("rh_montantprime")){g=parseFloat(f.target.get("value").replace(",","."))}else{g=parseFloat(aRecord.rh_montantprime)}if(aRecord.rh_quotite){h=aRecord.rh_quotite}if(aRecord.rh_typerecrut==33){e=(c+35+g)*d[h]}this._setFieldValue("rh","rh_couttotmens",Math.round(e))},updateCalcRHImpSCSPTot:function(e){oDataSet=this.DataSetManager().DataSet("rh");aRecord=oDataSet.RowData().GetRecord();var g=null;var d=parseFloat(0);var c=parseFloat(0);var f=parseFloat(0);if(e.target.ancestor(".field").hasClass("rh_couttotmens")){d=parseFloat(e.target.get("value").replace(",","."))}else{d=parseFloat(aRecord.rh_couttotmens)}if(e.target.ancestor(".field").hasClass("rh_impscspduree")){c=parseFloat(e.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.rh_impscspduree)}if(aRecord.rh_typerecrut==33){g=d*c}if(aRecord.rh_typerecrut==34){if(e.target.ancestor(".field").hasClass("rh_impscspmontant")){f=parseFloat(e.target.get("value").replace(",","."))}else{f=parseFloat(aRecord.rh_impscspmontant)}g=f*1.65}this._setFieldValue("rh","rh_impscsptot",Math.round(g))},updateCalcRHImpC1Tot:function(d){oDataSet=this.DataSetManager().DataSet("rh");aRecord=oDataSet.RowData().GetRecord();var c=parseFloat(0);var h=parseFloat(0);var g=parseFloat(0);var f="";if(d.target.ancestor(".field").hasClass("rh_couttotmens")){c=parseFloat(d.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.rh_couttotmens)}if(d.target.ancestor(".field").hasClass("rh_impc1duree")){h=parseFloat(d.target.get("value").replace(",","."))}else{h=parseFloat(aRecord.rh_impc1duree)}if(d.target.ancestor(".field").hasClass("rh_typec1")){f=d.target.get("value")}else{f=aRecord.rh_typec1}if(d.target.ancestor(".field").hasClass("rh_impc1montant")){g=d.target.get("value")}else{g=aRecord.rh_impc1montant}var e=null;if(aRecord.rh_typerecrut==33){if(f=="ANR"||f=="UE"){e=c*h*parseFloat(1.1)}else{e=c*h}}if(aRecord.rh_typerecrut==34){if(f=="ANR"||f=="UE"){e=g*1.75}else{e=g*1.65}}this._setFieldValue("rh","rh_impc1tot",Math.round(e))},updateCalcRHImpC2Tot:function(f){oDataSet=this.DataSetManager().DataSet("rh");aRecord=oDataSet.RowData().GetRecord();var d=parseFloat(0);var h=parseFloat(0);var g=parseFloat(0);var e="";if(f.target.ancestor(".field").hasClass("rh_couttotmens")){d=parseFloat(f.target.get("value").replace(",","."))}else{d=parseFloat(aRecord.rh_couttotmens)}if(f.target.ancestor(".field").hasClass("rh_impc2duree")){h=parseFloat(f.target.get("value").replace(",","."))}else{h=parseFloat(aRecord.rh_impc2duree)}if(f.target.ancestor(".field").hasClass("rh_typec2")){e=f.target.get("value")}else{e=aRecord.rh_typec2}if(f.target.ancestor(".field").hasClass("rh_impc2montant")){g=f.target.get("value")}else{g=aRecord.rh_impc2montant}var c=null;if(aRecord.rh_typerecrut==33){if(e=="ANR"||e=="UE"){c=d*h*parseFloat(1.1)}else{c=d*h}}if(aRecord.rh_typerecrut==34){if(e=="ANR"||e=="UE"){c=g*1.75}else{c=g*1.65}}this._setFieldValue("rh","rh_impc2tot",Math.round(c))},updateCalcRHCoutTot:function(d){oDataSet=this.DataSetManager().DataSet("rh");aRecord=oDataSet.RowData().GetRecord();var g;var f=parseFloat(0);var e=parseFloat(0);var c=parseFloat(0);if(d.target.ancestor(".field").hasClass("rh_impscsptot")){f=parseFloat(d.target.get("value").replace(",","."))}else{f=parseFloat(aRecord.rh_impscsptot)}if(d.target.ancestor(".field").hasClass("rh_impc1tot")){e=parseFloat(d.target.get("value").replace(",","."))}else{e=parseFloat(aRecord.rh_impc1tot)}if(d.target.ancestor(".field").hasClass("rh_impc2tot")){c=parseFloat(d.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.rh_impc2tot)}if(isNaN(f)){f=0}if(isNaN(e)){e=0}if(isNaN(c)){c=0}g=f+e+c;this._setFieldValue("rh","rh_couttot",Math.round(g))},updateCalcDTCouTot:function(g){oDataSet=this.DataSetManager().DataSet("dt");aRecord=oDataSet.RowData().GetRecord();var d;var c=parseFloat(0);var f=parseFloat(0);var h=parseFloat(0);var e=parseFloat(0);if(g.target.ancestor(".field").hasClass("dt_montantij")){c=parseFloat(g.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.dt_montantij)}if(g.target.ancestor(".field").hasClass("dt_nbj")){f=parseFloat(g.target.get("value").replace(",","."))}else{f=parseFloat(aRecord.dt_nbj)}if(g.target.ancestor(".field").hasClass("dt_couttrans")){h=parseFloat(g.target.get("value").replace(",","."))}else{h=parseFloat(aRecord.dt_couttrans)}if(g.target.ancestor(".field").hasClass("dt_coutdiv")){e=parseFloat(g.target.get("value").replace(",","."))}else{e=parseFloat(aRecord.dt_coutdiv)}if(isNaN(c)){c=0}if(isNaN(f)){f=0}if(isNaN(h)){h=0}if(isNaN(e)){e=0}d=c*f+h+e;this._setFieldValue("dt","dt_couttot",Math.round(d))},updateCalcDMPEJN1:function(f){oDataSet=this.DataSetManager().DataSet("dmp");aRecord=oDataSet.RowData().GetRecord();var h;var e=parseFloat(0);var g=parseFloat(0);var c=parseFloat(0);var d=parseFloat(0);if(f.target.ancestor(".field").hasClass("ec_ej_contractuel_n1")){e=parseFloat(f.target.get("value").replace(",","."))}else{e=parseFloat(aRecord.ec_ej_contractuel_n1)}if(f.target.ancestor(".field").hasClass("ec_ej_prev_n1")){g=parseFloat(f.target.get("value").replace(",","."))}else{g=parseFloat(aRecord.ec_ej_prev_n1)}if(f.target.ancestor(".field").hasClass("ec_diminution_ae")){c=parseFloat(f.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.ec_diminution_ae)}if(f.target.ancestor(".field").hasClass("ec_diminution_cp")){d=parseFloat(f.target.get("value").replace(",","."))}else{d=parseFloat(aRecord.ec_diminution_cp)}if(isNaN(e)){e=0}if(isNaN(g)){g=0}if(isNaN(c)){c=0}if(isNaN(d)){d=0}h=e+g+c+d;this._setFieldValue("dmp","dmp_ejn1",Math.round(h))},updateCalcSTAImpSCSPTot:function(f){oDataSet=this.DataSetManager().DataSet("sta");aRecord=oDataSet.RowData().GetRecord();var d;var c=parseFloat(0);var e=parseFloat(0);if(f.target.ancestor(".field").hasClass("sta_impscspduree")){c=parseFloat(f.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.sta_impscspduree)}if(f.target.ancestor(".field").hasClass("sta_impscspmontant")){e=parseFloat(f.target.get("value").replace(",","."))}else{e=parseFloat(aRecord.sta_impscspmontant)}if(isNaN(c)){c=0}if(isNaN(e)){e=0}d=c*540;this._setFieldValue("sta","sta_impscsptot",Math.round(d))},updateCalcSTAImpC1Tot:function(e){oDataSet=this.DataSetManager().DataSet("sta");aRecord=oDataSet.RowData().GetRecord();var f;var d=parseFloat(0);var c=parseFloat(0);if(e.target.ancestor(".field").hasClass("sta_impc1duree")){d=parseFloat(e.target.get("value").replace(",","."))}else{d=parseFloat(aRecord.sta_impc1duree)}if(e.target.ancestor(".field").hasClass("sta_impc1montant")){c=parseFloat(e.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.sta_impc1montant)}if(isNaN(d)){d=0}if(isNaN(c)){c=0}f=d*540;this._setFieldValue("sta","sta_impc1tot",Math.round(f))},updateCalcSTAImpC2Tot:function(e){oDataSet=this.DataSetManager().DataSet("sta");aRecord=oDataSet.RowData().GetRecord();var d;var c=parseFloat(0);var f=parseFloat(0);if(e.target.ancestor(".field").hasClass("sta_impc2duree")){c=parseFloat(e.target.get("value").replace(",","."))}else{c=parseFloat(aRecord.sta_impc2duree)}if(e.target.ancestor(".field").hasClass("sta_impc2montant")){f=parseFloat(e.target.get("value").replace(",","."))}else{f=parseFloat(aRecord.sta_impc2montant)}if(isNaN(c)){c=0}if(isNaN(f)){f=0}d=c*540;this._setFieldValue("sta","sta_impc2tot",Math.round(d))},updateCalcSTACoutTot:function(f){oDataSet=this.DataSetManager().DataSet("sta");aRecord=oDataSet.RowData().GetRecord();var c;var d=parseFloat(0);var g=parseFloat(0);var e=parseFloat(0);if(f.target.ancestor(".field").hasClass("sta_impscsptot")){d=parseFloat(f.target.get("value").replace(",","."))}else{d=parseFloat(aRecord.sta_impscsptot)}if(f.target.ancestor(".field").hasClass("sta_impc1tot")){g=parseFloat(f.target.get("value").replace(",","."))}else{g=parseFloat(aRecord.sta_impc1tot)}if(f.target.ancestor(".field").hasClass("sta_impc2tot")){e=parseFloat(f.target.get("value").replace(",","."))}else{e=parseFloat(aRecord.sta_impc2tot)}if(isNaN(d)){d=0}if(isNaN(g)){g=0}if(isNaN(e)){e=0}c=d+g+e;this._setFieldValue("sta","sta_couttot",Math.round(c))},_setFieldValue:function(e,d,c){oDataSet=this.DataSetManager().DataSet(e);aRecord=oDataSet.RowData().GetRecord();oDataSet.RowData().BackupCursor();oDataSet.SelectRowFromPrimaryKey(aRecord.id_data);oDataSet.Field(d).SetValue(c);oDataSet.RowData().RestoreCursor();this.DataSetManager().ValueChangeEvent(oDataSet.Field(d))}})},"0.0.1",{requires:["frameform"]});