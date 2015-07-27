YUI.add("frameformcalc", function(Y)
{
    /**
     * Frame
     *
     * @module frame
     * @submodule form
     */

    /**
     * FrameFormCalc
     *
     * @class FrameFormCalc
     * @extends FrameForm
     * @constructor
     */
    function FrameFormCalc()
    {
        FrameFormCalc.superclass.constructor.apply(this, arguments);
    }

    FrameFormCalc.NAME = 'FrameFormCalc';
    FrameFormCalc.ATTRS =
    {
        oIOManager : {
            value: null
        },

        /**
         * @attribute oTabView
         * @description YUI3 tabview widget
         * @type {Y.TabView}
         * @value null
         */
        oTabView :
        {
            value: null
        }
    };

    Y.FrameFormCalc = Y.extend(FrameFormCalc, Y.FrameForm,
        {
            /**
             * Variable calculé
             *
             * @method initializer
             * @public
             */
            initializer : function()
            {
                if( this.DataSetManager().DataSet( 'bdgt' ) ){
                    oDataSet = this.DataSetManager().DataSet( 'bdgt');
                    aRecord = oDataSet.RowData().GetRecord();

                    this.get( 'oLayout' ).on( 'layout:intialized', function() {

                        Y.one('.montant_c6 input').on('valuechange', this.updateCalcMontantC6, this );
                        Y.one('.montant_d6 input').on('valuechange', this.updateCalcMontantD6, this );
                        Y.one('.montant_e6 input').on('valuechange', this.updateCalcMontantE6, this );
                        Y.one('.montant_f6 input').on('valuechange', this.updateCalcMontantF6, this );
                        Y.one('.montant_g6 input').on('valuechange', this.updateCalcMontantG6, this );


                    }, this);
                }

                if( this.DataSetManager().DataSet( 'rh' ) ){
                    oDataSet = this.DataSetManager().DataSet( 'rh');
                    aRecord = oDataSet.RowData().GetRecord();

                    this.get( 'oLayout' ).on( 'layout:intialized', function() {
                        //rh_typerecrut, rh_coutmenscharg, rh_montantprime, rh_quotite => rh_couttotmens
                        nodeListRHCoutTotMens = Y.all('.rh_typerecrut select, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select');

                        nodeListRHCoutTotMens.each(function ( node ) {

                            if(node.get('tagName') == 'SELECT'){
                                node.on('change', this.updateCalcRHCoutTotMens, this );
                            }

                            if(node.get('tagName') == 'INPUT'){
                                node.on('valuechange', this.updateCalcRHCoutTotMens, this );
                            }
                        }, this);

                        nodeListRHImpSCSPTot = Y.all('.rh_typerecrut select, .rh_couttotmens input, .rh_impscspduree input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impscspmontant input');

                        nodeListRHImpSCSPTot.each(function ( node ) {

                            if(node.get('tagName') == 'SELECT'){
                                node.on('change', this.updateCalcRHImpSCSPTot, this );
                            }

                            if(node.get('tagName') == 'INPUT'){
                                node.on('valuechange', this.updateCalcRHImpSCSPTot, this );
                            }
                        }, this);

                        oTabView = Y.all('.yui3-tabview-content ul li a');

                        oTabView.each(function ( node ) {
                            node.on('click', this.addEventsRHImpC1Tot, this );
                            node.on('click', this.addEventsRHImpC2Tot, this );
                            node.on('click', this.addEventsRHCoutTot, this );
                        }, this);
						
						nodeListRHImpC2Tot = Y.all('.rh_typerecrut select, .rh_impc2 select, .rh_typec2 input, .rh_impc2duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc2montant input');

						nodeListRHImpC2Tot.each(function ( node ) {

							if(node.get('tagName') == 'SELECT'){
								node.on('change', this.updateCalcRHImpC2Tot, this );
							}

							if(node.get('tagName') == 'INPUT'){
								node.on('valuechange', this.updateCalcRHImpC2Tot, this );
							}
						}, this);
						
						nodeListRHImpC1Tot = Y.all('.rh_typerecrut select, .rh_impc1 select, .rh_typec1 input, .rh_impc1duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc1montant input');

						nodeListRHImpC1Tot.each(function ( node ) {

							if(node.get('tagName') == 'SELECT'){
								node.on('change', this.updateCalcRHImpC1Tot, this );
							}

							if(node.get('tagName') == 'INPUT'){
								node.on('valuechange', this.updateCalcRHImpC1Tot, this );
							}
						}, this);

                        nodeListRHCoutTot = Y.all('.rh_typerecrut select, .rh_impc1 select, .rh_typec1 input, .rh_impc1duree input, .rh_impc2 select, .rh_typec2 input, .rh_impc2duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc1montant input, .rh_impc2montant input, .rh_impscspduree input, .rh_impscspmontant input');

                        nodeListRHCoutTot.each(function ( node ) {

                            if(node.get('tagName') == 'SELECT'){
                                node.on('change', this.updateCalcRHCoutTot, this );
                            }

                            if(node.get('tagName') == 'INPUT'){
                                node.on('valuechange', this.updateCalcRHCoutTot, this );
                            }
                        }, this);

                    }, this);
                }

                if( this.DataSetManager().DataSet( 'dt' ) ){
                    oDataSet = this.DataSetManager().DataSet( 'dt');
                    aRecord = oDataSet.RowData().GetRecord();

                    this.get( 'oLayout' ).on( 'layout:intialized', function() {

                        nodeListDTCouTot = Y.all('.dt_lieudest input, .dt_montantij input, .dt_nbj input, .dt_couttrans input, .dt_coutdiv input');

                        nodeListDTCouTot.each(function ( node ) {

                            if(node.get('tagName') == 'SELECT'){
                                node.on('change', this.updateCalcDTCouTot, this );
                            }

                            if(node.get('tagName') == 'INPUT'){
                                node.on('valuechange', this.updateCalcDTCouTot, this );
                            }
                        }, this);

                    }, this);
                }

                if( this.DataSetManager().DataSet( 'dmp' ) ){
                    oDataSet = this.DataSetManager().DataSet( 'dmp');
                    aRecord = oDataSet.RowData().GetRecord();

                    this.get( 'oLayout' ).on( 'layout:intialized', function() {

                        nodeListDMPEJN1 = Y.all('.ec_diminution_ae input, .ec_diminution_cp input');

                        nodeListDMPEJN1.each(function ( node ) {

                            if(node.get('tagName') == 'SELECT'){
                                node.on('change', this.updateCalcDMPEJN1, this );
                            }

                            if(node.get('tagName') == 'INPUT'){
                                node.on('valuechange', this.updateCalcDMPEJN1, this );
                            }
                        }, this);
                    }, this);
                }

                if( this.DataSetManager().DataSet( 'sta' ) ){
                    oDataSet = this.DataSetManager().DataSet( 'sta');
                    aRecord = oDataSet.RowData().GetRecord();

                    this.get( 'oLayout' ).on( 'layout:intialized', function() {

                        nodeListSTAImpSCSPTot = Y.all('.sta_impscspduree input, .sta_impscspmontant input');

                        nodeListSTAImpSCSPTot.each(function ( node ) {

                            if(node.get('tagName') == 'SELECT'){
                                node.on('change', this.updateCalcSTAImpSCSPTot, this );
                            }

                            if(node.get('tagName') == 'INPUT'){
                                node.on('valuechange', this.updateCalcSTAImpSCSPTot, this );
                            }
                        }, this);
						
						nodeListSTAImpC1Tot = Y.all('.sta_impc1 select, .sta_typec1 input, .sta_impc1duree input, .sta_impc1montant input');

						nodeListSTAImpC1Tot.each(function ( node ) {

							if(node.get('tagName') == 'SELECT'){
								node.on('change', this.updateCalcSTAImpC1Tot, this );
							}

							if(node.get('tagName') == 'INPUT'){
								node.on('valuechange', this.updateCalcSTAImpC1Tot, this );
							}
						}, this);
						
						nodeListSTAImpC2Tot = Y.all('.sta_impc2 select, .sta_typec2 input, .sta_impc2duree input, .sta_impc2montant input');

						nodeListSTAImpC2Tot.each(function ( node ) {

							if(node.get('tagName') == 'SELECT'){
								node.on('change', this.updateCalcSTAImpC2Tot, this );
							}

							if(node.get('tagName') == 'INPUT'){
								node.on('valuechange', this.updateCalcSTAImpC2Tot, this );
							}
						}, this);
						
						nodeListSTACoutTot = Y.all('.sta_impscspduree input, .sta_impscspmontant input, .sta_impc1 select, .sta_typec1 input, .sta_impc1duree input, .sta_impc1montant input, .sta_impc2 select, .sta_typec2 input, .sta_impc2duree input, .sta_impc2montant input')

						nodeListSTACoutTot.each(function ( node ) {

							if(node.get('tagName') == 'SELECT'){
								node.on('change', this.updateCalcSTACoutTot, this );
							}

							if(node.get('tagName') == 'INPUT'){
								node.on('valuechange', this.updateCalcSTACoutTot, this );
							}
						}, this);

                        oTabView = Y.all('.yui3-tabview-content ul li a');

                        oTabView.each(function ( node ) {
                            node.on('click', this.addEventsSTAImpC1Tot, this );
                            node.on('click', this.addEventsSTAImpC2Tot, this );
                            node.on('click', this.addEventsSTACoutTot, this );
                        }, this);

                    }, this);
                }

            },

            addEventsSTAImpC1Tot: function(){

                setTimeout(function(){
                    nodeListSTAImpC1Tot = Y.all('.sta_impc1 select, .sta_typec1 input, .sta_impc1duree input, .sta_impc1montant input');

                    nodeListSTAImpC1Tot.each(function ( node ) {

                        if(node.get('tagName') == 'SELECT'){
                            node.on('change', this.updateCalcSTAImpC1Tot, this );
                        }

                        if(node.get('tagName') == 'INPUT'){
                            node.on('valuechange', this.updateCalcSTAImpC1Tot, this );
                        }
                    }, this);
                }.bind(this), 500);


            },

            addEventsSTAImpC2Tot: function(){

                setTimeout(function(){
                    nodeListSTAImpC2Tot = Y.all('.sta_impc2 select, .sta_typec2 input, .sta_impc2duree input, .sta_impc2montant input');

                    nodeListSTAImpC2Tot.each(function ( node ) {

                        if(node.get('tagName') == 'SELECT'){
                            node.on('change', this.updateCalcSTAImpC2Tot, this );
                        }

                        if(node.get('tagName') == 'INPUT'){
                            node.on('valuechange', this.updateCalcSTAImpC2Tot, this );
                        }
                    }, this);
                }.bind(this), 500);

            },

            addEventsSTACoutTot: function(){

                setTimeout(function(){
                    nodeListSTACoutTot = Y.all('.sta_impscspduree input, .sta_impscspmontant input, .sta_impc1 select, .sta_typec1 input, .sta_impc1duree input, .sta_impc1montant input, .sta_impc2 select, .sta_typec2 input, .sta_impc2duree input, .sta_impc2montant input')

                    nodeListSTACoutTot.each(function ( node ) {

                        if(node.get('tagName') == 'SELECT'){
                            node.on('change', this.updateCalcSTACoutTot, this );
                        }

                        if(node.get('tagName') == 'INPUT'){
                            node.on('valuechange', this.updateCalcSTACoutTot, this );
                        }
                    }, this);
                }.bind(this), 500);

            },


            addEventsRHImpC1Tot: function(){

                setTimeout(function(){
                    nodeListRHImpC1Tot = Y.all('.rh_typerecrut select, .rh_impc1 select, .rh_typec1 input, .rh_impc1duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc1montant input');

                    nodeListRHImpC1Tot.each(function ( node ) {

                        if(node.get('tagName') == 'SELECT'){
                            node.on('change', this.updateCalcRHImpC1Tot, this );
                        }

                        if(node.get('tagName') == 'INPUT'){
                            node.on('valuechange', this.updateCalcRHImpC1Tot, this );
                        }
                    }, this);
                }.bind(this), 500);


            },

            addEventsRHImpC2Tot: function(){

                setTimeout(function(){
                    nodeListRHImpC2Tot = Y.all('.rh_typerecrut select, .rh_impc2 select, .rh_typec2 input, .rh_impc2duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc2montant input');

                    nodeListRHImpC2Tot.each(function ( node ) {

                        if(node.get('tagName') == 'SELECT'){
                            node.on('change', this.updateCalcRHImpC2Tot, this );
                        }

                        if(node.get('tagName') == 'INPUT'){
                            node.on('valuechange', this.updateCalcRHImpC2Tot, this );
                        }
                    }, this);
                }.bind(this), 500);

            },

            addEventsRHCoutTot: function(){

                setTimeout(function(){
                    nodeListRHCoutTot = Y.all('.rh_typerecrut select, .rh_impc1 select, .rh_typec1 input, .rh_impc1duree input, .rh_impc2 select, .rh_typec2 input, .rh_impc2duree input, .rh_couttotmens input, .rh_catperso select, .rh_coutmenscharg input, .rh_primeinfo select, .rh_montantprime input, .rh_quotite select, .rh_impc1montant input, .rh_impc2montant input, .rh_impscspduree input, .rh_impscspmontant input')

                    nodeListRHCoutTot.each(function ( node ) {

                        if(node.get('tagName') == 'SELECT'){
                            node.on('change', this.updateCalcRHCoutTot, this );
                        }

                        if(node.get('tagName') == 'INPUT'){
                            node.on('valuechange', this.updateCalcRHCoutTot, this );
                        }
                    }, this);
                }.bind(this), 500);

            },

            updateCalcMontantC6: function(event){

                oDataSet = this.DataSetManager().DataSet( 'bdgt');
                aRecord = oDataSet.RowData().GetRecord();

                var iMontantC6 = ( event.target.get('value') == null || event.target.get('value') == '' ? 0 : event.target.get('value') );
                var iMontantC7 = ( aRecord.montant_c7 == null ? 0 : aRecord.montant_c7 );
                var iMontantD6 = ( aRecord.montant_d6 == null ? 0 : aRecord.montant_d6 );
                var iMontantE6 = ( aRecord.montant_e6 == null ? 0 : aRecord.montant_e6 );
                var iMontantF6 = ( aRecord.montant_f6 == null ? 0 : aRecord.montant_f6 );
                var iMontantG6 = ( aRecord.montant_g6 == null ? 0 : aRecord.montant_g6 );
                var iMontantH7 = ( aRecord.montant_h7 == null ? 0 : aRecord.montant_h7 );


                var iMontantC8 = parseFloat( iMontantC6 ) + parseFloat( iMontantC7 );
                var iMontantH6 = parseFloat( iMontantC6 ) + parseFloat( iMontantD6 ) + parseFloat( iMontantF6 );
                var iMontantI6 = parseFloat( iMontantC6 ) + parseFloat( iMontantE6 ) + parseFloat( iMontantG6 );
                var iMontantH8 = parseFloat( iMontantH6 ) + parseFloat( iMontantH7 );

                this._setFieldValue( 'bdgt', 'montant_c8', iMontantC8 );
                this._setFieldValue( 'bdgt', 'montant_h6', iMontantH6 );
                this._setFieldValue( 'bdgt', 'montant_i6', iMontantI6 );
                this._setFieldValue( 'bdgt', 'montant_h8', iMontantH8 );
            },

            updateCalcMontantD6: function(event){

                oDataSet = this.DataSetManager().DataSet( 'bdgt');
                aRecord = oDataSet.RowData().GetRecord();

                var iMontantC6 = ( aRecord.montant_c6 == null ? 0 : aRecord.montant_c6 );
                var iMontantD6 = ( event.target.get('value') == null || event.target.get('value') == '' ? 0 : event.target.get('value') );
                var iMontantD7 = ( aRecord.montant_d7 == null ? 0 : aRecord.montant_d7 );
                var iMontantF6 = ( aRecord.montant_f6 == null ? 0 : aRecord.montant_f6 );
                var iMontantH7 = ( aRecord.montant_h7 == null ? 0 : aRecord.montant_h7 );

                var iMontantD8 = parseFloat( iMontantD6 ) + parseFloat( iMontantD7 );
                var iMontantH6 = parseFloat( iMontantC6 ) + parseFloat( iMontantD6 ) + parseFloat( iMontantF6 );
                var iMontantH8 = parseFloat (iMontantH6 ) + parseFloat( iMontantH7 );

                this._setFieldValue( 'bdgt', 'montant_d8', iMontantD8 );
                this._setFieldValue( 'bdgt', 'montant_h6', iMontantH6 );
                this._setFieldValue( 'bdgt', 'montant_h8', iMontantH8 );
            },

            updateCalcMontantE6: function(event){

                oDataSet = this.DataSetManager().DataSet( 'bdgt');
                aRecord = oDataSet.RowData().GetRecord();

                var iMontantC6 = ( aRecord.montant_c6 == null ? 0 : aRecord.montant_c6 );
                var iMontantE6 = ( event.target.get('value') == null || event.target.get('value') == '' ? 0 : event.target.get('value') );
                var iMontantE7 = ( aRecord.montant_d7 == null ? 0 : aRecord.montant_d7 );
                var iMontantG6 = ( aRecord.montant_g6 == null ? 0 : aRecord.montant_g6 );
                var iMontantI7 = ( aRecord.montant_i7 == null ? 0 : aRecord.montant_i7 );

                var iMontantE8 = parseFloat( iMontantE6 ) + parseFloat( iMontantE7 );
                var iMontantI6 = parseFloat( iMontantC6 ) + parseFloat( iMontantE6 ) + parseFloat( iMontantG6 );
                var iMontantI8 = iMontantI6 + iMontantI7;

                this._setFieldValue( 'bdgt', 'montant_e8', iMontantE8 );
                this._setFieldValue( 'bdgt', 'montant_i6', iMontantI6 );
                this._setFieldValue( 'bdgt', 'montant_i8', iMontantI8 );
            },

            updateCalcMontantF6: function(event){

                oDataSet = this.DataSetManager().DataSet( 'bdgt');
                aRecord = oDataSet.RowData().GetRecord();

                var iMontantC6 = ( aRecord.montant_c6 == null ? 0 : aRecord.montant_c6 );
                var iMontantF6 = ( event.target.get('value') == null || event.target.get('value') == '' ? 0 : event.target.get('value') );
                var iMontantF7 = ( aRecord.montant_f7 == null ? 0 : aRecord.montant_f7 );
                var iMontantD6 = ( aRecord.montant_d6 == null ? 0 : aRecord.montant_d6 );
                var iMontantH7 = ( aRecord.montant_h7 == null ? 0 : aRecord.montant_h7 );

                var iMontantF8 = parseFloat( iMontantF6 ) + parseFloat( iMontantF7 );
                var iMontantH6 = parseFloat( iMontantC6 ) + parseFloat( iMontantD6 ) + parseFloat( iMontantF6 );
                var iMontantH8 = parseFloat (iMontantH6 ) + parseFloat( iMontantH7 );

                this._setFieldValue( 'bdgt', 'montant_f8', iMontantF8 );
                this._setFieldValue( 'bdgt', 'montant_h6', iMontantH6 );
                this._setFieldValue( 'bdgt', 'montant_h8', iMontantH8 );
            },

            updateCalcMontantG6: function(event){

                oDataSet = this.DataSetManager().DataSet( 'bdgt');
                aRecord = oDataSet.RowData().GetRecord();

                var iMontantC6 = ( aRecord.montant_c6 == null ? 0 : aRecord.montant_c6 );
                var iMontantG6 = ( event.target.get('value') == null || event.target.get('value') == '' ? 0 : event.target.get('value') );
                var iMontantG7 = ( aRecord.montant_g7 == null ? 0 : aRecord.montant_g7 );
                var iMontantE6 = ( aRecord.montant_e6 == null ? 0 : aRecord.montant_e6 );
                var iMontantI7 = ( aRecord.montant_i7 == null ? 0 : aRecord.montant_i7 );

                var iMontantG8 = parseFloat( iMontantG6 ) + parseFloat( iMontantG7 );
                var iMontantI6 = parseFloat( iMontantC6 ) + parseFloat( iMontantE6 ) + parseFloat( iMontantG6 );
                var iMontantI8 = iMontantI6 + iMontantI7;

                this._setFieldValue( 'bdgt', 'montant_g8', iMontantG8 );
                this._setFieldValue( 'bdgt', 'montant_i6', iMontantI6 );
                this._setFieldValue( 'bdgt', 'montant_i8', iMontantI8 );
            },

            updateCalcRHCoutTotMens: function(event){
                oDataSet = this.DataSetManager().DataSet( 'rh');
                aRecord = oDataSet.RowData().GetRecord();

                var rh_couttotmens = null;

                var rh_coutmenscharg = parseFloat(0);
                var rh_montantprime = parseFloat(0);
                var rh_quotite = "0";

                var arh_quotite = {
                    "0": 0,
                    "43": 0.5,
                    "44": 0.6,
                    "45": 0.7,
                    "46": 0.8,
                    "47": 0.9,
                    "48": 1
                };

                if(event.target.ancestor(".field").hasClass('rh_coutmenscharg')){
                    rh_coutmenscharg = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_coutmenscharg = parseFloat(aRecord.rh_coutmenscharg);
                }

                if(event.target.ancestor(".field").hasClass('rh_montantprime')){
                    rh_montantprime = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_montantprime = parseFloat(aRecord.rh_montantprime);
                }

                if(aRecord.rh_quotite){
                    rh_quotite = aRecord.rh_quotite
                }

                if(aRecord.rh_typerecrut == 33){
                    rh_couttotmens = (rh_coutmenscharg + 35 + rh_montantprime) * arh_quotite[rh_quotite];
                }

                this._setFieldValue( 'rh', 'rh_couttotmens', rh_couttotmens );

            },

            updateCalcRHImpSCSPTot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'rh');
                aRecord = oDataSet.RowData().GetRecord();

                var rh_impscsptot = null;
                var rh_couttotmens = parseFloat(0);
                var rh_impscspduree = parseFloat(0);
                var rh_impscspmontant = parseFloat(0);

                if(event.target.ancestor(".field").hasClass('rh_couttotmens')){
                    rh_couttotmens = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_couttotmens = parseFloat(aRecord.rh_couttotmens);
                }

                if(event.target.ancestor(".field").hasClass('rh_impscspduree')){
                    rh_impscspduree = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_impscspduree = parseFloat(aRecord.rh_impscspduree);
                }



                if(aRecord.rh_typerecrut == 33){

                    rh_impscsptot = rh_couttotmens * rh_impscspduree;
                }

                if(aRecord.rh_typerecrut == 34){

                    if(event.target.ancestor(".field").hasClass('rh_impscspmontant')){
                        rh_impscspmontant = parseFloat(event.target.get("value").replace(',', '.'));
                    }else{
                        rh_impscspmontant = parseFloat(aRecord.rh_impscspmontant);
                    }

                    rh_impscsptot = rh_impscspmontant * 1.65;
                }


                this._setFieldValue( 'rh', 'rh_impscsptot', rh_impscsptot );
            },

            updateCalcRHImpC1Tot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'rh');
                aRecord = oDataSet.RowData().GetRecord();
                var rh_couttotmens = parseFloat(0);
                var rh_impc1duree = parseFloat(0);
                var rh_impc1montant = parseFloat(0);
                var rh_typec1 = "";

                if(event.target.ancestor(".field").hasClass('rh_couttotmens')){
                    rh_couttotmens = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_couttotmens = parseFloat(aRecord.rh_couttotmens);
                }

                if(event.target.ancestor(".field").hasClass('rh_impc1duree')){
                    rh_impc1duree = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_impc1duree = parseFloat(aRecord.rh_impc1duree);
                }

                if(event.target.ancestor(".field").hasClass('rh_typec1')){
                    rh_typec1 = event.target.get("value");
                }else{
                    rh_typec1 = aRecord.rh_typec1;
                }

                if(event.target.ancestor(".field").hasClass('rh_impc1montant')){
                    rh_impc1montant = event.target.get("value");
                }else{
                    rh_impc1montant = aRecord.rh_impc1montant;
                }

                var rh_impc1tot = null;

                if(aRecord.rh_typerecrut == 33){

                    if( rh_typec1 == "ANR" || rh_typec1 == "EU" ){
                        rh_impc1tot = rh_couttotmens * rh_impc1duree * parseFloat(1.1);
                    }else{
                        rh_impc1tot = rh_couttotmens * rh_impc1duree;
                    }
                }

                if(aRecord.rh_typerecrut == 34){

                    if( rh_typec1 == "ANR" || rh_typec1 == "EU" ){
                        rh_impc1tot = rh_impc1montant * 1.75;
                    }else{
                        rh_impc1tot = rh_impc1montant * 1.65;
                    }
                }

                this._setFieldValue( 'rh', 'rh_impc1tot', Math.round(rh_impc1tot * 100) / 100 );
            },

            updateCalcRHImpC2Tot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'rh');
                aRecord = oDataSet.RowData().GetRecord();
                var rh_couttotmens = parseFloat(0);
                var rh_impc2duree = parseFloat(0);
                var rh_impc2montant = parseFloat(0);
                var rh_typec2 = "";

                if(event.target.ancestor(".field").hasClass('rh_couttotmens')){
                    rh_couttotmens = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_couttotmens = parseFloat(aRecord.rh_couttotmens);
                }

                if(event.target.ancestor(".field").hasClass('rh_impc2duree')){
                    rh_impc2duree = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_impc2duree = parseFloat(aRecord.rh_impc2duree);
                }

                if(event.target.ancestor(".field").hasClass('rh_typec2')){
                    rh_typec2 = event.target.get("value");
                }else{
                    rh_typec2 = aRecord.rh_typec2;
                }

                if(event.target.ancestor(".field").hasClass('rh_impc2montant')){
                    rh_impc2montant = event.target.get("value");
                }else{
                    rh_impc2montant = aRecord.rh_impc2montant;
                }

                var rh_impc2tot = null;

                if(aRecord.rh_typerecrut == 33){

                    if( rh_typec2 == "ANR" || rh_typec2 == "EU" ){
                        rh_impc2tot = rh_couttotmens * rh_impc2duree * parseFloat(1.1);
                    }else{
                        rh_impc2tot = rh_couttotmens * rh_impc2duree;
                    }
                }

                if(aRecord.rh_typerecrut == 34){

                    if( rh_typec2 == "ANR" || rh_typec2 == "EU" ){
                        rh_impc2tot = rh_impc2montant * 1.75;
                    }else{
                        rh_impc2tot = rh_impc2montant * 1.65;
                    }
                }

                this._setFieldValue( 'rh', 'rh_impc2tot', Math.round(rh_impc2tot * 100) / 100 );
            },

            updateCalcRHCoutTot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'rh');
                aRecord = oDataSet.RowData().GetRecord();

                var rh_couttot;
                var rh_impscsptot = parseFloat(0);
                var rh_impc1tot = parseFloat(0);
                var rh_impc2tot = parseFloat(0);

                if(event.target.ancestor(".field").hasClass('rh_impscsptot')){
                    rh_impscsptot = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_impscsptot = parseFloat(aRecord.rh_impscsptot);
                }

                if(event.target.ancestor(".field").hasClass('rh_impc1tot')){
                    rh_impc1tot = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_impc1tot = parseFloat(aRecord.rh_impc1tot);
                }

                if(event.target.ancestor(".field").hasClass('rh_impc2tot')){
                    rh_impc2tot = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    rh_impc2tot = parseFloat(aRecord.rh_impc2tot);
                }
                if(isNaN(rh_impscsptot)) rh_impscsptot = 0;
                if(isNaN(rh_impc1tot)) rh_impc1tot = 0;
                if(isNaN(rh_impc2tot)) rh_impc2tot = 0;

                rh_couttot = rh_impscsptot + rh_impc1tot + rh_impc2tot;

                this._setFieldValue( 'rh', 'rh_couttot', rh_couttot );
            },

            updateCalcDTCouTot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'dt');
                aRecord = oDataSet.RowData().GetRecord();

                var dt_couttot;
                var dt_montantij = parseFloat(0);
                var dt_nbj = parseFloat(0);
                var dt_couttrans = parseFloat(0);
                var dt_coutdiv = parseFloat(0);

                if(event.target.ancestor(".field").hasClass('dt_montantij')){
                    dt_montantij = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    dt_montantij = parseFloat(aRecord.dt_montantij);
                }

                if(event.target.ancestor(".field").hasClass('dt_nbj')){
                    dt_nbj = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    dt_nbj = parseFloat(aRecord.dt_nbj);
                }

                if(event.target.ancestor(".field").hasClass('dt_couttrans')){
                    dt_couttrans = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    dt_couttrans = parseFloat(aRecord.dt_couttrans);
                }

                if(event.target.ancestor(".field").hasClass('dt_coutdiv')){
                    dt_coutdiv = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    dt_coutdiv = parseFloat(aRecord.dt_coutdiv);
                }

                if(isNaN(dt_montantij)) dt_montantij = 0;
                if(isNaN(dt_nbj)) dt_nbj = 0;
                if(isNaN(dt_couttrans)) dt_couttrans = 0;
                if(isNaN(dt_coutdiv)) dt_coutdiv = 0;

                dt_couttot = dt_montantij * dt_nbj + dt_couttrans + dt_coutdiv;

                this._setFieldValue( 'dt', 'dt_couttot', dt_couttot );
            },

            updateCalcDMPEJN1: function(event) {
                oDataSet = this.DataSetManager().DataSet( 'dmp');
                aRecord = oDataSet.RowData().GetRecord();

                var dmp_ejn1;
                var ec_ej_contractuel_n1 = parseFloat(0);
                var ec_ej_prev_n1 = parseFloat(0);
                var ec_diminution_ae = parseFloat(0);
                var ec_diminution_cp = parseFloat(0);

                if(event.target.ancestor(".field").hasClass('ec_ej_contractuel_n1')){
                    ec_ej_contractuel_n1 = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    ec_ej_contractuel_n1 = parseFloat(aRecord.ec_ej_contractuel_n1);
                }

                if(event.target.ancestor(".field").hasClass('ec_ej_prev_n1')){
                    ec_ej_prev_n1 = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    ec_ej_prev_n1 = parseFloat(aRecord.ec_ej_prev_n1);
                }

                if(event.target.ancestor(".field").hasClass('ec_diminution_ae')){
                    ec_diminution_ae = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    ec_diminution_ae = parseFloat(aRecord.ec_diminution_ae);
                }

                if(event.target.ancestor(".field").hasClass('ec_diminution_cp')){
                    ec_diminution_cp = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    ec_diminution_cp = parseFloat(aRecord.ec_diminution_cp);
                }

                if(isNaN(ec_ej_contractuel_n1)) ec_ej_contractuel_n1 = 0;
                if(isNaN(ec_ej_prev_n1)) ec_ej_prev_n1 = 0;
                if(isNaN(ec_diminution_ae)) ec_diminution_ae = 0;
                if(isNaN(ec_diminution_cp)) ec_diminution_cp = 0;

                dmp_ejn1 = ec_ej_contractuel_n1 + ec_ej_prev_n1 + ec_diminution_ae + ec_diminution_cp;

                this._setFieldValue( 'dmp', 'dmp_ejn1', dmp_ejn1 );
            },

            updateCalcSTAImpSCSPTot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'sta');
                aRecord = oDataSet.RowData().GetRecord();

                var sta_impscsptot;
                var sta_impscspduree = parseFloat(0);
                var sta_impscspmontant = parseFloat(0);

                if(event.target.ancestor(".field").hasClass('sta_impscspduree')){
                    sta_impscspduree = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impscspduree = parseFloat(aRecord.sta_impscspduree);
                }

                if(event.target.ancestor(".field").hasClass('sta_impscspmontant')){
                    sta_impscspmontant = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impscspmontant = parseFloat(aRecord.sta_impscspmontant);
                }

                if(isNaN(sta_impscspduree)) sta_impscspduree = 0;
                if(isNaN(sta_impscspmontant)) sta_impscspmontant = 0;

                sta_impscsptot = sta_impscspduree * 540;

                this._setFieldValue( 'sta', 'sta_impscsptot', sta_impscsptot );
            },

            updateCalcSTAImpC1Tot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'sta');
                aRecord = oDataSet.RowData().GetRecord();

                var sta_impc1tot;
                var sta_impc1duree = parseFloat(0);
                var sta_impc1montant = parseFloat(0);

                if(event.target.ancestor(".field").hasClass('sta_impc1duree')){
                    sta_impc1duree = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impc1duree = parseFloat(aRecord.sta_impc1duree);
                }

                if(event.target.ancestor(".field").hasClass('sta_impc1montant')){
                    sta_impc1montant = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impc1montant = parseFloat(aRecord.sta_impc1montant);
                }

                if(isNaN(sta_impc1duree)) sta_impc1duree = 0;
                if(isNaN(sta_impc1montant)) sta_impc1montant = 0;

                sta_impc1tot = sta_impc1duree * 540;

                this._setFieldValue( 'sta', 'sta_impc1tot', sta_impc1tot );
            },

            updateCalcSTAImpC2Tot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'sta');
                aRecord = oDataSet.RowData().GetRecord();

                var sta_impc2tot;
                var sta_impc2duree = parseFloat(0);
                var sta_impc2montant = parseFloat(0);

                if(event.target.ancestor(".field").hasClass('sta_impc2duree')){
                    sta_impc2duree = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impc2duree = parseFloat(aRecord.sta_impc2duree);
                }

                if(event.target.ancestor(".field").hasClass('sta_impc2montant')){
                    sta_impc2montant = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impc2montant = parseFloat(aRecord.sta_impc2montant);
                }

                if(isNaN(sta_impc2duree)) sta_impc2duree = 0;
                if(isNaN(sta_impc2montant)) sta_impc2montant = 0;

                sta_impc2tot = sta_impc2duree * 540;

                this._setFieldValue( 'sta', 'sta_impc2tot', sta_impc2tot );
            },

            updateCalcSTACoutTot: function(event){
                oDataSet = this.DataSetManager().DataSet( 'sta');
                aRecord = oDataSet.RowData().GetRecord();

                var sta_couttot;
                var sta_impscsptot = parseFloat(0);
                var sta_impc1tot = parseFloat(0);
                var sta_impc2tot = parseFloat(0);

                if(event.target.ancestor(".field").hasClass('sta_impscsptot')){
                    sta_impscsptot = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impscsptot = parseFloat(aRecord.sta_impscsptot);
                }

                if(event.target.ancestor(".field").hasClass('sta_impc1tot')){
                    sta_impc1tot = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impc1tot = parseFloat(aRecord.sta_impc1tot);
                }

                if(event.target.ancestor(".field").hasClass('sta_impc2tot')){
                    sta_impc2tot = parseFloat(event.target.get("value").replace(',', '.'));
                }else{
                    sta_impc2tot = parseFloat(aRecord.sta_impc2tot);
                }

                if(isNaN(sta_impscsptot)) sta_impscsptot = 0;
                if(isNaN(sta_impc1tot)) sta_impc1tot = 0;
                if(isNaN(sta_impc2tot)) sta_impc2tot = 0;

                sta_couttot = sta_impscsptot + sta_impc1tot + sta_impc2tot;

                this._setFieldValue( 'sta', 'sta_couttot', sta_couttot );
            },

            _setFieldValue: function( dataset, field, value ){

                oDataSet = this.DataSetManager().DataSet( dataset );
                aRecord = oDataSet.RowData().GetRecord();

                oDataSet.RowData().BackupCursor();
                oDataSet.SelectRowFromPrimaryKey( aRecord.id_data );
                oDataSet.Field( field ).SetValue(  value );
                oDataSet.RowData().RestoreCursor();

                this.DataSetManager().ValueChangeEvent( oDataSet.Field( field ) );
            }
        });
}, '0.0.1', {
    requires: ['frameform']
});