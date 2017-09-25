YUI.add("widgetdocuments", function(Y)
{
	/**
	 * Layout
	 *
	 * @module layout
	 * @submodule widget
	 */

	/**
	 * Widget Tabs
	 * @class WidgetDocuments
	 * @extends WidgetTable
	 * @constructor
	 */
	function	WidgetDocuments()
	{
		WidgetDocuments.superclass.constructor.apply( this, arguments ) ;
	}
	
	WidgetDocuments.NAME = 'WidgetDocuments';
	WidgetDocuments.ATTRS =
	{
		//
	};
	
	Y.WidgetDocuments = Y.extend(WidgetDocuments, Y.WidgetTable,
	{
		/**
		 * Create menu entry for edition
		 *
		 * @protected
		 * @method _createEditAction
		 * @param sMenuId {String} id of html element for the menu
		 * @param oDataSet {Y.DataSet} Dataset where cursor in on current table row
		 */
		_createEditAction : function( sMenuId, oDataSet )
		{
			Y.one( '#' + sMenuId ).append( 
				'<li><a href="#" id="' + sMenuId + '-edit' + '">' + Y.Translate._( 'Download' ) + '</a></li>' 
			);

			// Add event
			Y.on(
				'click',
				this._clickOnEditEvent,
				'#' + sMenuId + '-edit',
				this,
				oDataSet.PrimaryKey().GetValue()
			);
		},
		
		/**
		 * Method called after click on delete button
		 *
		 * @method _clickOnDeleteEvent
		 * @protected
		 * @param oEvt {Event}
		 * @param iPKeyValue {Integer}
		 */
		_clickOnEditEvent : function( oEvt, iPKeyValue )
		{
			oEvt.preventDefault();
			
			this.DataSet().RowData().BackupCursor();
			
			this.DataSet().SelectRowFromPrimaryKey( iPKeyValue );
			
			var oRegExp = new RegExp( ".+?/{(.+?)}", 'gi' ),
				aMatch = null,
				sEditUrl = this.get( 'sEditUrl' ),
				sNewEditUrl = this.get( 'sEditUrl' ),
				sUrl;

			while ( ( aMatch = oRegExp.exec( sEditUrl ) ) !== null) {

				var	mValue = this.DataSet().Field( aMatch[ 1 ] ).GetValue();

				if ( Y.Lang.isUndefined( mValue ) ) {
					throw new Y.VznException(
						null, 
						'Field ' + aMatch[ 1 ] + ' not found in dataset', 
						'ClickOnEditEvent', 
						'Y.WidgetTable'
					);
				}
				sNewEditUrl = sNewEditUrl.replace( '{'  + aMatch[ 1 ] + '}', mValue );
			}
            sEditUrl = sNewEditUrl;
			sEditUrl = sNewEditUrl;
			
			this.DataSet().RowData().RestoreCursor();
			
			sUrl = Y.Voozanoo.properties.project_url + sEditUrl;
			
			if ( this.get( 'bSaveBeforeEdit' ) ) {
				this.Layout().Frame().Save( null, {
					'REDIRECT_SETTINGS' : {
						type: 'frame',
						url: sUrl
					}
				});
			} else {
				// !!! on surcharge ici le comportement standard !!!
				window.open( sUrl ) ;
			}	
		}
	});
}, '0.0.1', {
	requires: ['widgetbase']
});