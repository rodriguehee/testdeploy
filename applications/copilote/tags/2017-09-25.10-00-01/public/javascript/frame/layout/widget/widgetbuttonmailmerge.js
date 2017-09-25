/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * This is VOOZANOO, a program. Contact voozanoo@epiconcept.fr, or   *
 * see http://www.voozanoo.net/ for more information.                *
 *                                                                   *
 * Copyright 2001-2010 Epiconcept                                    *
 *                                                                   *
 * This program is free software; you can redistribute it and/or     *
 * modify it under the terms of the GNU General Public License as    *
 * published by the Free Software Foundation - version 2             *
 *                                                                   *
 * This program is distributed in the hope that it will be useful,   *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of    *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the     *
 * GNU General Public License in file COPYING for more details.      *
 *                                                                   *
 * You should have received a copy of the GNU General Public         *
 * License along with this program; if not, write to the Free        *
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor,       *
 * Boston, MA 02111, USA.                                            *
 *                                                                   *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
YUI.add("widgetbuttonmailmerge", function(Y)
{
	/**
	 * Layout
	 *
	 * @module layout
	 * @submodule widget
	 */

	/**
	 * WidgetButtonMailmerge
	 *
	 * @class WidgetButtonMailmerge
	 * @extends WidgetButton
	 * @constructor
	 */
	function WidgetButtonMailmerge(oCfg)
	{
		WidgetButtonMailmerge.superclass.constructor.apply(this, arguments);
	}

	WidgetButtonMailmerge.NAME = 'WidgetButtonMailmerge';
	WidgetButtonMailmerge.ATTRS =
	{
		sDatasetId : {
			setter: function( sValue )
			{
				if ( Y.Lang.isUndefined( this.Layout().Frame().DataSetManager().DataSet(sValue) ) )
				{
					throw new Y.VznException(null, "DataSet " + sValue + " was not found in DataSetManager", 'setter of sDatasetFollowed attribute', 'Y.WidgetButtonMailmerge');
				}

				return sValue;
			}
		}
	}

	Y.WidgetButtonMailmerge = Y.extend(WidgetButtonMailmerge, Y.WidgetButton,
	{
		/**
		 * Surcharge la méthode Save du WidgetButton pour placer un listener "once" (une seule fois)
		 * puis appelle la méthode Save parente
		 */
		Save : function( oEvent )
		{
			Y.WidgetButtonMailmerge.superclass.Save.apply(this, arguments);

			var aRedirectionSettings = ( this._getResoreRedirectSettings() || this.get('aRedirectionSettings') );

			if ( aRedirectionSettings !== null ) {
				var aParams = aRedirectionSettings.params;
				var aFormatParams = {};
				aParams.forEach(function (aParam, iIndex) {
					if (aParam.type === 'value') {
						aFormatParams[aParam.alias] = aParam.value;
					} else if ( aParam.type === 'dataset' ) {
						var oDataSets = this.Layout().Frame().DataSetManager().get( 'aDataSet' );
						for ( var sKey in oDataSets ) {
							var oCurrentDataset = oDataSets[ sKey ];
							if ( oCurrentDataset.get( 'sId' ) === aParam.name ) {
								var iPKeyValue = oCurrentDataset.PrimaryKey().GetValue();
								oCurrentDataset.RowData().BackupCursor();
								oCurrentDataset.SelectRowFromPrimaryKey( iPKeyValue );
								aFormatParams[ aParam.alias ] = oCurrentDataset.Field( aParam.field ).GetValue();
								oCurrentDataset.RowData().RestoreCursor();
							}
						}
					}
				}, this);
			}
			var sUrl = aRedirectionSettings.module + '/' +  aRedirectionSettings.ctrl + '/' +  aRedirectionSettings.action;
			window.open(this.Layout().Frame().GetURI( sUrl, aFormatParams ), 'Document Mailmerge','menubar=no, status=no' );
		},


		/**
		 * Surcharge de la fonction pour supprimer la redirection de la frame :
		 * Les paramètres de redirection ne sont utilisés que pour appeler le controller mailmerge
		 * @private
		 */
		_save: function()
		{
			//Redirection settings taken at first from a possibly frame "on stand by"
			//then from redirection set in this.get('aRedirectionSettings')
			var aRedirectionSettings = ( this._getResoreRedirectSettings() || this.get('aRedirectionSettings') );

			var aDataSetsIds = null;
			var oDataSet = this.get( 'oDataSet' );

			if ( Y.Lang.isUndefined( oDataSet ) === false ) {
				aDataSetsIds = {};
				aDataSetsIds[ oDataSet.Id() ] = {};
			}

			this.Layout().Frame().Save( aDataSetsIds, {
				'REDIRECT_SETTINGS' : {},
				'DIALOG_OPTIONS' : this.get('oDialogOptions'),
				'ADDITIONAL_PARAMS' : this.get('sAdditionalParams')
			});
		}
        
	});
}, '0.0.1', {
	requires: ['widgetbutton']
});