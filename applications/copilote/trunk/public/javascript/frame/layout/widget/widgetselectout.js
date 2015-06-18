YUI.add("widgetselectout", function(Y)
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
	function WidgetSelectOut()
	{
		WidgetSelectOut.superclass.constructor.apply( this, arguments ) ;
	}

	WidgetSelectOut.NAME = 'WidgetSelectOut';
	WidgetSelectOut.ATTRS =
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
		}
	}

	Y.WidgetSelectOut = Y.extend( WidgetSelectOut, Y.WidgetSelect,
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
					var oPrimaryKey = this.Field().GetDico().PrimaryKey() ;
					oPrimaryKey.DataSet().RowData().First() ;
					while( oPrimaryKey.DataSet().RowData().EoF( true ) == false ) {
						if ( oPrimaryKey.GetValue() == sValue ) {
							var oLabel = oPrimaryKey.DataSet().Field( 'short_label' ) ;
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