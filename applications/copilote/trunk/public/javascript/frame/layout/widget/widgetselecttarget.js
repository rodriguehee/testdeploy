YUI.add("widgetselecttarget", function(Y)
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
	function WidgetSelectTarget()
	{
		WidgetSelectTarget.superclass.constructor.apply( this, arguments ) ;
	}

	WidgetSelectTarget.NAME = 'WidgetSelectTarget';
	WidgetSelectTarget.ATTRS =
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
		},
		sSourceDataSet :
		{
			value: ''
		}
	}

	Y.WidgetSelectTarget = Y.extend( WidgetSelectTarget, Y.WidgetSelect,
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
				if ( ! Y.Lang.isUndefined( oWidgetDef.options.sourcedataset ) ) {
					this.set( 'sSourceDataSet', oWidgetDef.options.sourcedataset ) ;
				}
			}
		},
		
		ValueChangeEvent : function( oField )
		{
			if ( this.Field() == null ) {
				return ;
			}

			if (this.Field().DataSet().UniqId() == oField.DataSet().UniqId() &&
				this.Field().GetName() == oField.GetName())
			{
				var sValue = this.Field().GetValue() ;
				var oWidgetDestination = this.Layout().GetWidget( this.get( 'sDestination' ) ) ;
				
				if ( sValue == null ) {
					oWidgetDestination.Field().SetValue( null ) ;
				}
				else {
					var oDataSet = this.Layout().Frame().DataSetManager().DataSet( this.get( 'sSourceDataSet' ) ) ;
					var oPrimaryKey = oDataSet.PrimaryKey() ;
					oPrimaryKey.DataSet().RowData().BackupCursor();
					oPrimaryKey.DataSet().RowData().First() ;
					while( oPrimaryKey.DataSet().RowData().EoF( true ) == false ) {
						if ( oPrimaryKey.GetValue() == sValue ) {
							var oLabel = oPrimaryKey.DataSet().Field( this.get( 'sSource' ) ) ;
							oWidgetDestination.Field().SetValue( oLabel.GetValue() ) ;
							break ;
						}
						oPrimaryKey.DataSet().RowData().Next() ;
					}
				}
				this.Update() ;
				oWidgetDestination.Update() ;
			}
		}
	} ) ;
}, '0.0.1', {
	requires: ['widgetfield']
} ) ;