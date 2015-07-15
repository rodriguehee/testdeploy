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
        }
    };

    Y.FrameFormCalc = Y.extend(FrameFormCalc, Y.FrameForm,
        {
            /**
             * Surcharge méthode parente pour controler la sélection de la session de L2
             *
             * @method Save
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
            },

            /*DataSetChangeEvent : function( oDataSet ) {

            },

            ValueChangeEvent : function( oField ) {

            },*/

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

                this._setFieldValue( 'montant_c8', iMontantC8 );
                this._setFieldValue( 'montant_h6', iMontantH6 );
                this._setFieldValue( 'montant_i6', iMontantI6 );
                this._setFieldValue( 'montant_h8', iMontantH8 );
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

                this._setFieldValue( 'montant_d8', iMontantD8 );
                this._setFieldValue( 'montant_h6', iMontantH6 );
                this._setFieldValue( 'montant_h8', iMontantH8 );
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

                this._setFieldValue( 'montant_e8', iMontantE8 );
                this._setFieldValue( 'montant_i6', iMontantI6 );
                this._setFieldValue( 'montant_i8', iMontantI8 );
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

                this._setFieldValue( 'montant_f8', iMontantF8 );
                this._setFieldValue( 'montant_h6', iMontantH6 );
                this._setFieldValue( 'montant_h8', iMontantH8 );
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

                this._setFieldValue( 'montant_g8', iMontantG8 );
                this._setFieldValue( 'montant_i6', iMontantI6 );
                this._setFieldValue( 'montant_i8', iMontantI8 );
            },

            _setFieldValue: function( field, value ){

                oDataSet = this.DataSetManager().DataSet( 'bdgt');
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