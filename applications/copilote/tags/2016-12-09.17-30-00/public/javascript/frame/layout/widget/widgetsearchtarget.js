YUI.add("widgetsearchtarget", function(Y)
{
	/**
	 * Layout
	 *
	 * @module layout
	 * @submodule widget
	 */

	/**
	 * WidgetText
	 *
	 * @class WidgetText
	 * @extends WidgetField
	 * @constructor
	 */
	function WidgetSearchTarget()
	{
		WidgetSearchTarget.superclass.constructor.apply( this, arguments ) ;
	}

	WidgetSearchTarget.NAME = 'WidgetSearchTarget';
	WidgetSearchTarget.ATTRS =
	{
		/**
		 * @attribute sDestination
		 * @description Destination Widget Name
		 * @type {Object}
		 * @value ""
		 */
		sDestination :
		{
			value : ''
		},
		sSource :
		{
			value : ''
		}
	}

	Y.WidgetSearchTarget = Y.extend( WidgetSearchTarget, Y.WidgetSearch,
	{
		/**
		 * Construction logic executed during object instantiation.
		 *
		 * @method initializer
		 * @protected
		 */
		initializer : function()
		{
			var	oWidgetDef = this.get( 'oWidgetDef' ) ;
			if ( ! Y.Lang.isUndefined( oWidgetDef.options ) ) {
				if ( ! Y.Lang.isUndefined( oWidgetDef.options.destination ) ) {
					this.set( 'sDestination', oWidgetDef.options.destination ) ;
				}
				if ( ! Y.Lang.isUndefined( oWidgetDef.options.source ) ) {
					this.set( 'sSource', oWidgetDef.options.source ) ;
				}
			}
		},
		
		/**
         * A result is selected
         *
         * @method OnSelect
         * @public
         * @param e {Object} Event
         */
        OnSelect : function(e)
        {
			var result = e.result;
            var	oSearchWidget = arguments[1];
            var oInput = Y.one( document.getElementById( oSearchWidget.UniqId() ) );
			var oSearchDataSet = oSearchWidget.get('oSearchDataSet');
			var	aFields = oSearchDataSet.MetaData().AllFields();
			var	aData = [];
			var sFieldValue = '';
			var sDestinationValue = '' ;

            e.preventDefault();
            oInput.ac.hide();

			// Set aData by removing fields used by this widget 
			// And set field_value if found
			for ( var i = 0; i < aFields.length; i++ ) {				
				if ( Y.Lang.isUndefined( result.raw[ aFields[ i ] ] ) == false ) {
					if ( aFields[ i ] != 'head' && aFields[ i ] != 'body' && aFields[ i ] != 'footer' && aFields[ i ] != 'field_value' ) {					
						aData[ aFields[i] ] = result.raw[ aFields[ i ] ];
					} else if ( aFields[ i ] == 'field_value' ) {
						sFieldValue = result.raw[ aFields[ i ] ];
					} else if ( aFields[ i ] == oSearchWidget.get( 'sSource' ) ) {
						sDestinationValue = result.raw[ aFields[ i ] ];
					}
				}
			}

			// Before updating the value attribute of the search field, set value
			// of ac plugin, to prevent sendRequest event.
			oInput.ac.set( 'value', sFieldValue );
			oInput.set( 'value', sFieldValue, false );
			
			// Target dataset update: replace or insert record
            if ( oSearchWidget.get( 'oTargetDataSet' ) != null ) {
				
            	var	oTargetDataSet = oSearchWidget.get('oTargetDataSet');

            	if ( oSearchWidget.get( 'sActionOnTarget' ) == oSearchWidget.get( 'MODE_REPLACE' ) ) {
                    //oTargetDataSet.RefreshFromRecordId( result.raw[ 'id_data' ] );
					// Update target dataset content
					// if aData is empty, record will be reseted
                    oSearchWidget.Layout().Frame().DataSetManager().UpdateRecord( oTargetDataSet, aData );
					
            	} else if ( oSearchWidget.get( 'sActionOnTarget' ) == oSearchWidget.get( 'MODE_INSERT' ) ) {

					// aData is empty, don't add new record.
					if ( ! aData.length ) {
						return;
					}

					// Process for quick add
					if ( oSearchWidget.get( 'sQuickAddKey' ) != null ) { //Must fill aData ?						
						aData[ oSearchWidget.get('sQuickAddKey') ] = result.raw[ oSearchWidget.get( 'sQuickAddKey' ) ];
					}

					oSearchWidget.Layout().Frame().DataSetManager().AddRecord( oTargetDataSet, aData );
					
            	} else {
            		throw new Y.VznException(null, "WidgetSearch: unknow action after select: " + oSearchWidget.get('sActionOnTarget'), 'OnSelect', 'Y.WidgetSearch');
            	}
            }
			
			// Search dataset update
			var oSearchDataSet = oSearchWidget.get('oSearchDataSet');
			
			var oWidgetDestination = oSearchWidget.Layout().GetWidget( oSearchWidget.get( 'sDestination' ) ) ;
			if ( sDestinationValue == null ) {
				oWidgetDestination.Field().SetValue( null ) ;
			}
			else {
				oWidgetDestination.Field().SetValue( sDestinationValue ) ;
			}
			oWidgetDestination.Update() ;
			
			oSearchWidget.Layout().Frame().DataSetManager().UpdateRowData( oSearchDataSet.Id(), [ result.raw ] );
		}
	} ) ;
}, '0.0.1', {
	requires: ['widgetsearch']
} ) ;