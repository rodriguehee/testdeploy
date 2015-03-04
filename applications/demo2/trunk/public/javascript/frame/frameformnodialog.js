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
YUI.add( "frameformnodialog", function( Y )
{
	/**
	 * Frame
	 *
	 * @module frame
	 * @submodule form
	 */

	/**
	 * FrameFormNoDialog
	 *
	 * -----------------
	 * Note : This class is instanciated by Y.MainFrame._loadFrame() and config
	 * is gave as First Args (hash).
	 * Yui3 Natively try to affect ATTRS with KeyName of hash
	 * @see Y.Base http://yuilibrary.com/yui/docs/base/
	 * -----------------
	 *
	 * @class FrameFormNoDialog
	 * @extends Frame
	 * @constructor
	 */
	function FrameFormNoDialog()
	{
		FrameFormNoDialog.superclass.constructor.apply( this, arguments ) ;
	}

	FrameFormNoDialog.NAME = 'FrameFormNoDialog' ;
	FrameFormNoDialog.ATTRS = {} ;
	
	Y.FrameFormNoDialog = Y.extend( FrameFormNoDialog, Y.FrameForm, {
		/**
		 * Save form content
		 *
		 * @method Save
		 * @public
		 * @param aDataSetsIds {Array} of datasets ids to save (if null, save all datasets)
		 * @param oArgs {Object} hash of extra params to give to this.IOManager().Execute()
		 */
		Save : function( aDataSetsIds, oArgs )
		{
			if (this.AllAxisAreSet() == true)
			{
				var bIsValid = this.Layout().Check();

				if ( bIsValid )
				{
					oArgs = oArgs || null;

					var oDataToSave = this._getDataToSave( aDataSetsIds );

					if ( false === this._dataSetsAreEmpty( oDataToSave.data ) ) {
						// this.Layout().ShowLoading(); // vs "core"
						this.IOManager().Execute(	this.SaveFileURL(),
													this,
													oDataToSave,
													'IOSaveSuccess',
													oArgs,
													'IOSaveFailed');
					} else {
						this.Layout().ShowDialog(
							Y.Translate._( 'Advertisement' ),
							Y.Translate._( 'There is no data to save' ),
							1000
						);
					}
				}
				else
				{
					this.Layout().DisplayErrors();
					this.Layout().RefreshFieldsWidgetsDisplay();
				}

			}
		}
	} )
}, '0.0.1', { requires: ['frameform'] } ) ; 
