YUI.add("widgetnotif", function(Y)
{
	/**
	 * Layout
	 *
	 * @module layout
	 * @submodule widget
	 */

	/**
	 * Widget Tabs
	 * @class WidgetNotif
	 * @extends WidgetBase
	 * @constructor
	 */
	function	WidgetNotif()
	{
		WidgetNotif.superclass.constructor.apply(this, arguments);
	}
	
	//var RADIO_TYPE		= 'radio';
	//var CHECKBOX_TYPE	= 'checkbox';

	WidgetNotif.NAME = 'WidgetNotif';
	WidgetNotif.ATTRS =
	{
		/**
		 * @attribute oDataSet
		 * @description data set to use for filling list
		 * @type {DataSet}
		 */
        oDataSet:
        {
            writeOnce : true,
            getter: function( sValue ) {
                return this.Layout().Frame().DataSetManager().DataSet( sValue );
            }
        },
		
		/**
		 * @attribute sLabelColumn
		 * @description Name of dataset's column to use as label in select
		 * @type {String}
		 */
		sLabelColumn:
		{
			value	:	null
		},

        /**
         * @attribute sTypeColumn
         * @description Name of dataset's column to use as class
         * @type {String}
         */
        sTypeColumn:
        {
            value	:	null
        },

        /**
         * @attribute sDismissibleColumn
         * @description Name of dataset's column to use as toggle dismiss button
         * @type {String}
         */
        sDismissibleColumn:
        {
            value	:	null
        },

		/**
		 * @attribute bAlphaSort
		 * @description Force widget to sort list by alphabetic order
		 * @type {boolean}
		 */
		bAlphaSort:
		{
			value: false
		},
				
		/**
		 * @attribute aItems
		 * @description list of items to be shown in list
		 * @type {Array}
		 */
		aItems:
		{
			value: []
		},
		
		/**
		 * @attribute sRootTemplate
		 * @description root template
		 * @type {string}
		 */
		sRootTemplate:
		{
			value: '<div id={id} class="{eltClass}">{items}</div>'
		},
		
		/**
		 * @attribute sItemTemplate
		 * @description template used for each item
		 * @type {string}
		 */
		sItemTemplate:
		{
			value	: [
                '<div class="alert {infoClass} {infoDismissible}" role="alert">',
                    '{eltDismissible}',
                    '{label}',
                '</div>'
			].join( '' )
		},

        /**
         * @attribute sDismissTemplate
         * @description template used for dismiss button
         * @type {string}
         */
        sDismissTemplate:
        {
            value	: '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
        },
		
		/**
		 * Default class to set regarding grid positioning 
		 *
		 * @attribute sDefaultGridClass
		 * @type {String}
		 * @value 'col-md-3 col-lg-4'
		 */
		sDefaultGridClass:
		{
			value: 'col-xs-12'
		}
		
		/**
		 * Is widget used to represent default field, or multiple values field (Radio or Checkbox)
		 * 
		 * @attribute sType
		 * @type {String}
		 * @value RADIO_TYPE
		 */
//		sType :
//		{
//			value: RADIO_TYPE
//		}
	};
	
	Y.WidgetNotif = Y.extend(WidgetNotif, Y.WidgetField,
	{
		/**
		 * Construction logic executed during object instantiation.
		 *
		 * @method initializer
		 * @protected
		 */
		initializer : function()
		{
            var	oWidgetDef = this.get('oWidgetDef');

            this.set('oDataSet', oWidgetDef.d );
            this.set('sLabelColumn', this.GetWidgetDefValue( 'options.text', 'text' ) );
            this.set('sTypeColumn', this.GetWidgetDefValue( 'options.type', 'type' ) );
            this.set('sDismissibleColumn', this.GetWidgetDefValue( 'options.dismissible', 'dismissible' ) );

		},

		
		/**
		 * Render widget
		 *
		 * @method Render
		 * @public
		 * @param sNodeId {string} Node ID to select for append
		 */
		Render : function( sNodeId )
		{
            var oDataSet = this.get( 'oDataSet' );
            var aItems = [],
                aItem,
                sItems = '',
                sItemTemplate = this.get( 'sItemTemplate' ),
                sRootTemplate = this.get( 'sRootTemplate'),
                sDismissTemplate = this.get( 'sDismissTemplate' );

            oDataSet.RowData().First();

            while( false === oDataSet.RowData().EoF() ) {

                aItem = {
                    id: this.UniqId() + '-' + oDataSet.RowData().GetFieldValue('id_data'),
                    label: oDataSet.RowData().GetFieldValue(this.get('sLabelColumn')),
                    "class": oDataSet.RowData().GetFieldValue(this.get('sTypeColumn')),
                    "dismissible": oDataSet.RowData().GetFieldValue(this.get('sDismissibleColumn'))
                };

                aItems.push( aItem );

                oDataSet.RowData().Next();
            }


			for ( i = 0; i < aItems.length; i++ ) {

				sItems += Y.Lang.sub( sItemTemplate, { 
					id: aItems[ i ].id,
					label: aItems[ i ][ 'label' ],
					infoClass : ( aItems[ i ][ 'class' ] ? this._getClass(aItems[ i ][ 'class' ]) : '' /*'list-group-item-info'*/ ),
                    infoDismissible : ( aItems[ i ][ 'dismissible' ] ? this._getDismiss(aItems[ i ][ 'dismissible' ]) : '' ),
                    eltDismissible : ( this._getDismiss(aItems[ i ][ 'dismissible' ]) != '' ? sDismissTemplate : '' )
				} );				
			}

			Y.one(document.getElementById(sNodeId)).append( Y.Lang.sub(
				sRootTemplate,
				{
					id: this.UniqId(),
					items: sItems,
					eltClass: this.GetClass()
				})
			);
	
			//this._addEvents( aItems );
		},

        /**
         * Update widget
         *
         * @method Update
         * @public
         */
        Update: function()
        {

        },

        /**
         * Get Dico Type Notif
         *
         * @method _getDicoTypeNotif
         * @protected
         */
        _getDicoTypeNotif : function( iIdDataDico )
        {
            var oDataSet = this.get( 'oDataSet' );
            // Recuperation de l'id que l'on souhaite dans le dictionnaire
            var oDataSetDico = oDataSet.get('oDataSetManager').DataSetDico(
                'type_notif' );
            var iCodeTypeNotif = 0;
            oDataSetDico.RowData().BackupCursor();
            oDataSetDico.RowData().First();
            while ( ! oDataSetDico.RowData().EoF( true ) ) {
                if ( oDataSetDico.Field( 'id_data' ).GetValue() === iIdDataDico ) {
                    iCodeTypeNotif = oDataSetDico.Field( 'code'
                    ).GetValue();
                    break;
                }
                oDataSetDico.RowData().Next();
            }
            oDataSetDico.RowData().RestoreCursor();
            return iCodeTypeNotif;
        },

        /**
         * Get Dico Type Notif
         *
         * @method _getDicoTypeNotif
         * @protected
         */
        _getDicoYorN : function( iIdDataDico )
        {
            var oDataSet = this.get( 'oDataSet' );
            // Recuperation de l'id que l'on souhaite dans le dictionnaire
            var oDataSetDico = oDataSet.get('oDataSetManager').DataSetDico(
                'lg01' );
            var iCodeYorN = 0;
            oDataSetDico.RowData().BackupCursor();
            oDataSetDico.RowData().First();
            while ( ! oDataSetDico.RowData().EoF( true ) ) {
                if ( oDataSetDico.Field( 'id_data' ).GetValue() === iIdDataDico ) {
                    iCodeYorN = oDataSetDico.Field( 'code'
                    ).GetValue();
                    break;
                }
                oDataSetDico.RowData().Next();
            }
            oDataSetDico.RowData().RestoreCursor();
            return iCodeYorN;
        },

        /**
         * Update widget
         *
         * @method Update
         * @public
         */
        _getClass: function(iId)
        {
            var sClass = '';

            iCodeTypeNotif = this._getDicoTypeNotif(iId);

            switch ( iCodeTypeNotif ) {
                case '1':
                    sClass = 'success';
                    break;
                case '2':
                    sClass = 'info';
                    break;
                case '3':
                    sClass = 'warning';
                    break;
                case '4':
                    sClass = 'danger';
                    break;
            }

            return 'alert-' + sClass;
        },
        /**
         * Update widget
         *
         * @method Update
         * @public
         */
        _getDismiss: function(iId)
        {
            iCodeYorn = this._getDicoYorN(iId);
            if(iCodeYorn == '1'){
                return 'alert-dismissible';
            }
            return '';
        },

		
		/**
		 * Set css class for field
		 * Extends parent method because CSS class are setted on LI elements, not
		 * parent node (DIV).
		 *
		 * @method SetStyle
		 * @param sClassName {String}
		 */
		SetStyle : function(sClassName)
		{
			// Suppression de toutes les class
			Y.all( '#' + this.UniqId() + ' li').each( function( oNode ) {
				oNode 
					.removeClass( this.get('CLASS_ERROR') )
					.removeClass( this.get('CLASS_WARNING') )
					.removeClass( this.get('CLASS_NOTICE') )
					.removeClass( this.get('CLASS_INFO') );
				
				if ( sClassName ) {
					oNode.addClass( sClassName );
				}
			}, this );
		}
	});
}, '0.0.1', {
	requires: ['widgetbase']
});