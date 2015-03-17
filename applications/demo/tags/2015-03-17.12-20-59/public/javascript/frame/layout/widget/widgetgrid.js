/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * This is VOOZANOO, a program. Contact voozanoo@epiconcept.fr, or   *
 * see http://www.voozanoo.net/ for more information.                *
 *                                                                   *
 * Copyright 2001-2012 Epiconcept                                    *
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
YUI.add( "widgetgrid", function(Y)
{
	/**
	 * Layout
	 *
	 * @module layout
	 * @submodule widget
	 */

	/**
	 * Widget Grid
	 *
	 * @class WidgetGrid
	 * @extends WidgetBase
	 * @constructor
	 */
	function	WidgetGrid( oCfg, oLayout, aMetaData ) {
		WidgetGrid.superclass.constructor.apply( this, arguments ) ;
	}

	WidgetGrid.NAME = 'WidgetGrid';
	
	WidgetGrid.ATTRS = {
		oDataSet: {
			value: null
		},
		iRowIndex: {
			value: -1
		},
		sPrintLabel: {
			value: "Print"
		},
		sExportLabel: {
			value: "Excel Export"
		},
		sAddRowLabel: {
			value: "Add Row"
		},
		sDeleteRowLabel: {
			value: "Delete Row"
		},
		sSuffixAddButton: {
			value: "-addbutton"
		},
		sSuffixDeleteButton: {
			value: "-deletebutton"
		},
		sSuffixExportButton: {
			value: "-excelbutton"
		},
		sSuffixPrintButton: {
			value: "-printbutton"
		},
		aIds: {
			value: []
		},
		bNewId: {
			value: false
		},
		sTheme: {
			value: ""
		},
		iRowsHeight: {
			value: 25
		},
		iWidth: {
			value: 600
		},
		iHeight: {
			value: 400
		},
		bSortable: {
			value: false
		},
		bFilterRow: {
			value: false
		},
		bColumnResize: {
			value: false
		},
		bFilterable: {
			value: false
		},
		iColumnsHeight: {
			value: 25
		},
		bShowStatusBar: {
			value: false
		},
		iStatusBarHeight: {
			value: 25
		},
		bShowAggregates: {
			value: false
		},
		sDeleteMsg: {
			value: "Confirm the deletion ?"
		},
		oComputedColumns : {
			value: {}
		},
		iComputedIndex: {
			value: 0
		},
		sDecimalSeparator: {
			value: ","
		},
		sThousandSeparator: {
			value: " "
		},
		sCountLabel: {
			value: "Nb de lignes"
		},
		sSumLabel: {
			value: "Total"
		},
		sAvgLabel: {
			value: "Moyenne"
		}
	} ;
	
	Y.WidgetGrid = Y.extend( WidgetGrid, Y.WidgetBase, { 
		
		initializer : function() {
			var oWidgetDef = this.get( 'oWidgetDef' ) ;
			
			this.set( 'oDataSet', this.Layout().Frame().DataSetManager().DataSet( oWidgetDef.d ) ) ;
			
			if ( oWidgetDef.options.aggregates_count_label != undefined ) {
				this.set( 'sCountLabel', oWidgetDef.options.aggregates_count_label ) ;
			}
			
			if ( oWidgetDef.options.aggregates_avg_label != undefined ) {
				this.set( 'sAvgLabel', oWidgetDef.options.aggregates_avg_label ) ;
			}
			
			if ( oWidgetDef.options.aggregates_sum_label != undefined ) {
				this.set( 'sSumLabel', oWidgetDef.options.aggregates_sum_label ) ;
			}
			
			if ( oWidgetDef.options.export_btn_label != undefined ) {
				this.set( 'sExportLabel', oWidgetDef.options.export_btn_label ) ;
			}
			
			if ( oWidgetDef.options.deleterow_btn_label != undefined ) {
				this.set( 'sDeleteRowLabel', oWidgetDef.options.deleterow_btn_label ) ;
			}
			
			if ( oWidgetDef.options.addrow_btn_label != undefined ) {
				this.set( 'sAddRowLabel', oWidgetDef.options.addrow_btn_label ) ;
			}
			
			if ( oWidgetDef.options.print_btn_label != undefined ) {
				this.set( 'sPrintLabel', oWidgetDef.options.print_btn_label ) ;
			}
			
			if ( oWidgetDef.options.deleterow_warning != undefined ) {
				this.set( 'sDeleteMsg', oWidgetDef.options.deleterow_warning ) ;
			}
			
			if ( oWidgetDef.options.theme != undefined ) {
				this.set( 'sTheme', oWidgetDef.options.theme ) ;
			}
			
			if ( oWidgetDef.options.rowsheight != undefined ) {
				this.set( 'iRowsHeight', oWidgetDef.options.rowsheight ) ;
			}
			
			if ( oWidgetDef.options.columnsheight != undefined ) {
				this.set( 'iColumnsHeight', oWidgetDef.options.columnsheight ) ;
			}
			
			if ( oWidgetDef.options.width != undefined ) {
				this.set( 'iWidth', oWidgetDef.options.width ) ;
			}
			
			if ( oWidgetDef.options.height != undefined ) {
				this.set( 'iHeight', oWidgetDef.options.height ) ;
			}
			
			if ( oWidgetDef.options.sortable != undefined ) {
				this.set( 'bSortable', Boolean( oWidgetDef.options.sortable ) ) ;
			}
			
			if ( oWidgetDef.options.showfilterrow != undefined ) {
				this.set( 'bFilterRow', Boolean( oWidgetDef.options.showfilterrow ) ) ;
			}
			
			if ( oWidgetDef.options.columnresize != undefined ) {
				this.set( 'bColumnResize', Boolean( oWidgetDef.options.columnresize ) ) ;
			}
			
			// l'affichage de la ligne de filtrage implique le filtrage global
			var filterable = false ;
			if ( oWidgetDef.options.filterable != undefined ) {
				filterable = Boolean( oWidgetDef.options.filterable ) ;
			}
			if ( this.get( 'bFilterRow') ) {
				filterable = true ;
			}
			this.set( 'bFilterable', filterable ) ;
			
			if ( oWidgetDef.options.statusbarheight != undefined ) {
				this.set( 'iStatusBarHeight', Boolean( oWidgetDef.options.statusbarheight ) ) ;
			}
			
			if ( oWidgetDef.options.showaggregates != undefined ) {
				this.set( 'bShowAggregates', Boolean( oWidgetDef.options.showaggregates ) ) ;
			}
			
			// l'affichage des aggrégats implique l'affichage de la barre de statut
			var showstatusbar = false ;
			if ( oWidgetDef.options.showstatusbar != undefined ) {
				showstatusbar = Boolean( oWidgetDef.options.showstatusbar ) ;
			}
			if ( this.get( 'bShowAggregates' ) ) {
				showstatusbar = true ;
			}
			this.set( 'bShowStatusBar', showstatusbar ) ;
		},
		
		Render: function( sNodeId ) {
			var suffix = "-gridbtns" ;
			var theme = this.get( "sTheme" ) ;
			var listClassName = " jqx-reset jqx-reset-" + theme + 
				" jqx-rc-all jqx-rc-all-" + theme + 
				" jqx-widget jqx-widget-" + theme + 
				" jqx-widget-content jqx-widget-content-" + theme ;
			var listStyle = "margin-top: 20px" ;
		    var btnClassName = " jqx-rc-all jqx-rc-all-" + theme + 
			" jqx-widget jqx-widget-" + theme + 
			" jqx-button jqx-button-" + theme + 
		    " jqx-fill-state-pressed + jqx-fill-state-pressed-" + theme ;
			var btnStyle = "margin-left: 5px" ;
			Y.one( '#' + sNodeId ).append( '<div id="' + this.UniqId() + '"></div>' ) ;
			Y.one( '#' + sNodeId ).append( '<div id="' + this.UniqId() + suffix + '" class="' + listClassName + '" style="' + listStyle + '"></div>' ) ;
			Y.one( '#' + this.UniqId() + suffix ).append( '<input id="' + this.UniqId() + this.get( 'sSuffixAddButton' ) + '" class="' + btnClassName + '" style="' + btnStyle + '" type="button" value="' + this.get( 'sAddRowLabel' )  + '">' ) ;
			Y.one( '#' + this.UniqId() + suffix ).append( '<input id="' + this.UniqId() + this.get( 'sSuffixDeleteButton' ) + '" class="' + btnClassName + '" style="' + btnStyle + '" type="button" value="' + this.get( 'sDeleteRowLabel' )  + '">' ) ;
			Y.one( '#' + this.UniqId() + suffix ).append( '<input id="' + this.UniqId() + this.get( 'sSuffixExportButton' ) + '" class="' + btnClassName + '" style="' + btnStyle + '" type="button" value="' + this.get( 'sExportLabel' )  + '">' ) ;
			Y.one( '#' + this.UniqId() + suffix ).append( '<input id="' + this.UniqId() + this.get( 'sSuffixPrintButton' ) + '" class="' + btnClassName + '" style="' + btnStyle + '" type="button" value="' + this.get( 'sPrintLabel' )  + '">' ) ;
		},
		
		PostRender : function() {
			WidgetGrid.superclass.PostRender.apply( this, arguments ) ;
			this.Initialize() ;
			$( "#" + this.UniqId() ).jqxGrid( 'focus' ) ;
		},
		
		DataSet : function() {
			return this.get( 'oDataSet' ) ;
		},
		
		/**
		 * Gestion de l'ajout pour obtension de la clé autoincrémentée
		 */
		DataSetChangeEvent : function( oDataSet, bCursorMoved ) {
			
			if ( this.get( 'bNewId' ) ) {
                var rowdatas = oDataSet.RowData().get( 'aRowData' ) ;
                var lastIndex = rowdatas.length - 1 ;
                this.get( 'aIds' ).push( rowdatas[lastIndex].id_data ) ;
				$( "#" + this.UniqId() ).jqxGrid( 'addrow', null, rowdatas[lastIndex] ) ;
				// sans cette méthode, le rendu est étrange lors de la modif des cellules de la ligne
				$( "#" + this.UniqId() ).jqxGrid( 'updatebounddata' ) ;
				this.set( 'bNewId', false ) ;
				
				// on redonne la main sur le bouton "Ajouter une ligne"
				// les sélecteurs YUI et JQuery posent problème
				document.getElementById( this.UniqId() + this.get( 'sSuffixAddButton' ) ).disabled = false ;
				
				// on remet le curseur sur la ligne créée
				var columnName = $( "#" + this.UniqId() ).jqxGrid( "columns" ).records[0].datafield ;
				$( "#" + this.UniqId() ).jqxGrid( 'selectcell', lastIndex, columnName ) ;
				var position = $( "#" + this.UniqId() ).jqxGrid( 'scrollposition' ) ;
				var height = $( "#" + this.UniqId() ).jqxGrid( 'height' ) ;
				$( "#" + this.UniqId() ).jqxGrid( 'scrolloffset', height, position.left ) ;
				$( "#" + this.UniqId() ).jqxGrid( 'focus' ) ;
			}
		},
		
		Initialize : function( oConf ) {
			// pour passer l'objet aux méthodes
	        var that = this ;
	        
			var columns = new Array() ;
			var datafields = new Array() ;
			var gridWidth = 0 ;
			var aColumnDefs = this.get( 'oWidgetDef' ).columns.column ;
			
			for ( var i = 0; i < aColumnDefs.length; i++ ) {
				var column = {} ;
				var field = {} ;
				column.datafield = aColumnDefs[i].f ;
				column.text = aColumnDefs[i].title ;
				column.width = aColumnDefs[i].width ;
				gridWidth = gridWidth + parseInt( column.width ) ; // Attention à la concaténation
				field.name = aColumnDefs[i].f ;
				
				column.editable = true ;
				if ( aColumnDefs[i].readonly != undefined && Boolean( aColumnDefs[i].readonly ) ) {
					column.editable = false ;
				}
				if ( aColumnDefs[i].formula != undefined ) {
					column.editable = false ;
				}
				
				column.pinned = false ;
				if ( aColumnDefs[i].pinned != undefined && Boolean( aColumnDefs[i].pinned ) ) {
					column.pinned = true ;
				}
				
				// la redefinition des fonctions d'aggregation n'est pas aisée
				//  on passe par le renderer si on veut redéfinir le libellé [non !]
				column.aggregates = [] ;
				if ( aColumnDefs[i].aggregate != undefined ) {
					column.aggregates.push( aColumnDefs[i].aggregate ) ;
					/*
					 * la surcharge ne supporte pas le zéro ... 
					 * c'est d'autant plus génant que ça implique un bug sur les mises à jour
					 * ou en cas d'initialisation à partir de zéro 
					 * 
					column.aggregatesrenderer = function( aggregates ) {
						var renderstring = "" ;
						console.log( aggregates ) ;
						$.each( aggregates, function( key, value ) {
							value = parseFloat( value ) ;
							if ( isNaN( value ) ) {
								value = "" ;
							}
							switch( key ) {
								case "count":
									renderstring += that.get( 'sCountLabel' ) + ": " + value ;
									break ;
								case "sum":
									renderstring += that.get( 'sSumLabel' ) + ": " + value ;
									break ;
								case "avg":
									renderstring += that.get( 'sAvgLabel' ) + ": " + value ;
									break ;
								default:
									renderstring += key + ": " + value ;
							}
						} ) ;
						return renderstring ;
					}
					*/
				}

                column.cellsalign = "left" ;
                if ( aColumnDefs[i].cellsalign != undefined ) {
                    if ( aColumnDefs[i].cellsalign == "right" || aColumnDefs[i].cellsalign == "center" ) {
                        column.cellsalign = aColumnDefs[i].cellsalign ;
                    }
                }
                
                if ( aColumnDefs[i].cellsformat != undefined ) {
                	column.cellsformat = aColumnDefs[i].cellsformat ;
                }
                
                // Utilisation des bornes définies dans le varset
                column.validation = function ( cell, value ) {
                    var minValue = that.DataSet().MetaData().GetFieldMin( cell.datafield ) ;
                    var maxValue = that.DataSet().MetaData().GetFieldMax( cell.datafield ) ;
                    
                    if( "date" == that.DataSet().MetaData().GetFieldType( cell.datafield ) ) {
                    	value = value.getFullYear() + "-" + 
                    		( 1 + value.getMonth() ) + "-" + value.getDate() ;
                    }
                    
                    if ( minValue && value < minValue ) {
                        return { 
                        	result: false, 
                        	message: cell.datafield + ": '" + value + 
                        		"' doit être supérieur ou égal à '" + minValue + "'"
                        } ;
                    }
                    if ( maxValue && value > maxValue ) {
                        return { 
                        	result: false, 
                        	message: cell.datafield + ": '" + value + 
                        		"' doit être inférieur ou égal à '" + maxValue + "'" 
                        } ;
                    }
                    return true ;
                } ;
                
                // Gestion des types de colonnes
				switch ( this.DataSet().MetaData().GetFieldType( aColumnDefs[i].f ) ) {
				
					// gestion des "id_data"
					case this.DataSet().MetaData().get( 'TYPE_PRIMARY_KEY' ) :
						field.type = "number" ;
						column.editable = false ;
						break ;
						
					// gestion des "integer"
					case this.DataSet().MetaData().get( 'TYPE_INTEGER' ) :
						field.type = "number" ;
						column.columntype = "numberinput" ; 
						break ;
						
					// gestion des "float"
					case this.DataSet().MetaData().get( 'TYPE_FLOAT' ) :
						field.type = "number" ;
						// le numberinput serait une bonne idée mais il ne tient pas 
						//  compte du cellsformat du coup son comportement peut ne pas
						//   être en phase avec la définition du XML => textbox
						column.columntype = "textbox" ; 
						break ;
						
					// gestion des "date"
					case this.DataSet().MetaData().get( 'TYPE_DATE' ) :
						column.cellsformat = "d" ;
						field.type = "date" ;
						column.columntype = "datetimeinput" ;
						break ;
						
					// gestion des "text" pour l'instant dans un champ "input" text classique
					case this.DataSet().MetaData().get( 'TYPE_TEXT' ) :
						field.type = "string" ;
						column.columntype = "textbox" ;
						break ;
						
					// gestion des "fkey_dico_ext"
                    case this.DataSet().MetaData().get( 'TYPE_DICO_EXT' ) :
                        var dicoName = this.DataSet().MetaData().GetDicoExtName( aColumnDefs[i].f ) ;
	                    var dicoDataSet = this.DataSet().get('oDataSetManager').DataSetDico( dicoName ) ;
	                    var dicoValues = dicoDataSet.get( 'oRowData' ).get( 'aRowData' ) ;
	                    var dicoSource = {
	                        datatype: "array",
	                        datafields: [
	                            { name: "label", type: "string"},
	                            { name: "id_data", type: "integer"}
	                        ],
	                        localdata: dicoValues
	                    } ;
	                    var dicoAdapter = new $.jqx.dataAdapter( dicoSource, { autobind: true } ) ;
	                    
                        field.type = "string" ;
                        column.columntype = "template" ;
                        
                        column.createEditor = function( row, value, editor ) { 
                        	editor.jqxDropDownList( {
                        		checkboxes: true,
                        		source: dicoAdapter,
                        		displayMember: "label",
                        		valueMember: "id_data",
                        		autoDropDownHeight: "true",
                        		selectionRenderer: function() {
                        			return "Veuillez choisir:" ;
                        		}
                        	} ) ;
                        } ;
                        
                        column.cellsrenderer = function ( rowindex, columnfield, cellvalue  ) {
                        	var name = that.DataSet().MetaData().GetDicoExtName( this.datafield ) ;
                            var dataSet = that.DataSet().get('oDataSetManager').DataSetDico( name ) ;
                            var items = dataSet.get( 'oRowData' ).get( 'aRowData' ) ;
            				var labels = [] ;
            				var value = [] ;
            				if( typeof( cellvalue ) == "string" ) {
            					value = cellvalue.split( "," ) ;
            				}
            				
            				for ( var i = 0; i < items.length; i++ ) {
            					var obj = items[i] ;
            					for ( var j = 0; j < value.length; j++ ) {
            						if ( obj.id_data == value[j] ) {
            							labels.push( obj.label ) ;
            						}
            					}
            				}
            				
            				var result = labels.join( ", " ) ;
            				return "<div style='margin:4px'>" + result + "</div>" ;
                        } ; 
                        
                        column.initEditor = function( row, cellvalue, editor, celltext, pressedkey ) {
                        	var items = editor.jqxDropDownList( 'getItems' ) ;
                        	editor.jqxDropDownList( 'uncheckAll' ) ;
                        	var values = cellvalue.split( "," ) ;
                        	
                        	for ( var j = 0; j < values.length; j++ ) {
                        		for( var i = 0; i < items.length; i++ ) {
                        			if ( items[i].value == values[j] ) {
                        				editor.jqxDropDownList( "checkIndex", i ) ;
                        			}
                        		}
                        	}
                        } ;
                        
                        /* à la fin de la saisie on met à jour */
                        column.getEditorValue = function( row, cellvalue, editor ) {
        					that.get( 'oDataSet' ).DataSetManager().SelectRowFromPrimaryKey(
        						that.get( 'oDataSet' ),
        						that.get( 'aIds' )[row]
            				) ;
        					
        					var value = [] ;
        					if ( "" != editor.val() ) {
        						value = editor.val().split( "," ) ;
        					}
        					
        					that.get( 'oDataSet' ).DataSetManager().SetFieldValue( 
        						that.get( 'oDataSet' ).Field( this.datafield ), 
        						value 
        					) ;
        					var params = { 
        						DIALOG_OPTIONS : {
        							bDlgDisabled : true
        						}
        					} ;
            				that.get( 'oDataSet' ).DataSetManager().get( 'oFrame' ).Save( null, params ) ;
                        	return editor.val() ;
                        } ;
                        break ;
                        
    				// gestion des "fkey_varset"
                    case this.DataSet().MetaData().get( 'TYPE_FKEY_VARSET' ) :
                    	var dicoName = aColumnDefs[i].d ;
                    	var dicoDataSet = that.DataSet().get('oDataSetManager').DataSet( dicoName ) ;
	                    var dicoValues = dicoDataSet.get( 'oRowData' ).get( 'aRowData' ) ;
                    	var dicoSource = {
                            datatype: "array",
                            datafields: [
                                { name: "label", type: "string"},
                                { name: "id_data", type: "integer"}
                            ],
                            localdata: dicoValues
                        } ;
                        var dicoAdapter = new $.jqx.dataAdapter( dicoSource, { autoBind: true } ) ;
                        var dico = {} ;
                        // rendre le nom unique pour gérer le cas de plusieurs champs branchés sur un même dico
                        dico.name = "dico_" + i ; 
                        if ( aColumnDefs[i].ghost != undefined ) {
                        	dico.name = aColumnDefs[i].ghost ;
                        }
                        dico.value = column.datafield ;
                        dico.values = {
                            source: dicoAdapter.records,
                            value: 'id_data',
                            name: 'label'
                        } ;
                        datafields.push( dico ) ;
                        
                        field.type = "string" ;
                        column.columntype = "combobox" ;
                        column.displayfield = dico.name ;
                        
                        column.createEditor = function( row, value, editor ) { 
                        	var source ;
                        	for ( var i = 0; i < datafields.length; i++) {
                        		if ( datafields[i].name == this.displayfield ) {
                        			source = datafields[i].values.source ;
                        			source.unshift( { id_data : null, label: "" } ) ;
                        		}
                        	}
                            var adapter = new $.jqx.dataAdapter( source, { autoBind: true } ) ;
                        	editor.jqxComboBox( { 
                        		source: adapter, 
                        		displayMember: "label", 
                        		valueMember: "id_data",
                        		dropDownHeight: 75
                        	} ) ;
                        } ;
                    	break ;
                    	
					// gestion des "fkey_dico"
                    case this.DataSet().MetaData().get( 'TYPE_DICO' ) :
                        var dicoName = this.DataSet().MetaData().GetDicoName( aColumnDefs[i].f ) ;
                        var dicoDataSet = this.DataSet().get('oDataSetManager').DataSetDico( dicoName ) ;
                        var dicoValues = dicoDataSet.get( 'oRowData' ).get( 'aRowData' ) ;
                        var dicoSource = {
                            datatype: "array",
                            datafields: [
                                { name: "label", type: "string"},
                                { name: "id_data", type: "integer"}
                            ],
                            localdata: dicoValues
                        } ;
                        var dicoAdapter = new $.jqx.dataAdapter( dicoSource, { autoBind: true } ) ;
                        var dico = {} ;
                        // rendre le nom unique pour gérer le cas de plusieurs champs branchés sur un même dico
                        dico.name = "dico_" + i ; 
                        dico.value = column.datafield ;
                        dico.values = {
                            source: dicoAdapter.records,
                            value: 'id_data',
                            name: 'label'
                        } ;
                        datafields.push( dico ) ;
                        
                        field.type = "number" ;
                        column.columntype = "combobox" ;
                        column.displayfield = dico.name ;
                        
                        column.createEditor = function( row, value, editor ) { 
                        	var name = that.DataSet().MetaData().GetDicoName( this.datafield ) ;
                            var dataSet = that.DataSet().get('oDataSetManager').DataSetDico( name ) ;
                            var localdata = dataSet.get( 'oRowData' ).get( 'aRowData' ) ;
                            localdata.unshift( { id_data : null, label: "" } ) ; // annuler une saisie
                            var dicoSource = {
                                datatype: "array",
                                datafields: [
                                    { name: "label", type: "string"},
                                    { name: "id_data", type: "integer"}
                                ],
                                localdata: localdata
                            } ;
                            var adapter = new $.jqx.dataAdapter( dicoSource, { autoBind: true } ) ;
                            
                        	editor.jqxComboBox( { 
                        		source: adapter, 
                        		displayMember: "label", 
                        		valueMember: "id_data",
                        		dropDownHeight: 75
                        	} ) ;
                        } ;
                        break ;
					default:
						column.columntype = "textbox" ;
						field.type = "string" ;
						break ;
				}
			
	            // cas des champs calculés
	            if( aColumnDefs[i].formula != undefined ) {
	            	column.cellsrenderer = function( index, datafield, value, defaultvalue, column, rowdata ) {
	            		var update = false ;
	                	var formula = "" ;
	                	var cellsformat = "" ;
	            		for ( var i = 0 ; i < aColumnDefs.length; i++ ) {
	            			if( aColumnDefs[i].f == datafield ) {
	            				formula = aColumnDefs[i].formula ;
	            				if ( aColumnDefs[i].cellsformat != undefined ) {
	            					cellsformat = aColumnDefs[i].cellsformat ;
	            				}
	            			}
	            		}
	
	            		var resultat = eval( formula ) ;
	            		var formattedResultat = resultat ;
	            		var type = that.DataSet().MetaData().GetFieldType( datafield ) ;
	            		
	            		if ( type == that.DataSet().MetaData().get( 'TYPE_STRING' ) ) {
	            			if ( value != resultat ) {
	                			update = true ;
	                		}
	            		}
	            		
	            		if ( type == that.DataSet().MetaData().get( 'TYPE_INTEGER' ) ||
	            			 type == that.DataSet().MetaData().get( 'TYPE_FLOAT' )	) {
	                		if ( isNaN( resultat ) ) {
	                			resultat = "" ;
	                			formattedResultat = "" ;
	                		}
	                		else if ( column.cellsformat ) { 
	                			var da = new $.jqx.dataAdapter() ;
	                			formattedResultat = da.formatNumber( resultat, cellsformat, {
	                				decimalseparator: that.get( 'sDecimalSeparator' ),
	                				thousandsseparator: that.get( 'sThousandSeparator' )
	                			} ) ; 
	                		}
	                		if ( value != resultat ) {
	                			update = true ;
	                		}
	            		}
	            		
	            		if ( type == that.DataSet().MetaData().get( 'TYPE_FKEY_VARSET' ) ) {
	                    	for ( var i = 0; i < datafields.length; i++) {
	                    		if ( datafields[i].name == this.displayfield ) {
	                    			var s = datafields[i].values.source ;
	                    			for ( var j = 0; j < s.length; j++ ) {
	                    				if ( s[j].id_data == resultat ) {
	                    					formattedResultat = s[j].label ;
 	                    				}
	                    			}
	                    		}
	                    	}
	                    	if( resultat === null ) {
	                    		formattedResultat = "" ;
	                    	} 
	                    	if ( formattedResultat != value ) {
	                    		update = true ;
	                    		rowdata[this.displayfield] = formattedResultat ;
	                    	}
	            		}
	            		
	            		if ( that.get( "iComputedIndex" ) == index && update ) {
	            			// faire directement la mise à jour ici ???
	            			var oComputedColumns = that.get( "oComputedColumns") ;
	            			oComputedColumns[datafield] = resultat ;
	            			that.set( "oComputedColumns", oComputedColumns ) ;
	            		}
	            		return "<div style='margin:4px; text-align: " + column.cellsalign + "'>" + formattedResultat + "</div>" ;
	            	} ;
	            }
	            
				columns.push( column ) ;
				datafields.push( field ) ;
			}
			
			var data = this.DataSet().RowData().get('aRowData') ;
			for ( var i = 0; i != data.length; i++ ) {
				this.get( 'aIds' )[i] = data[i].id_data ;
			}
			
			var source = {
			    localdata: data,
			    datatype: "array",
			    datafields: datafields
			} ;
			
			var dataAdapter = new $.jqx.dataAdapter( source, {
			    loadComplete: function (data) { },
			    loadError: function (xhr, status, error) { }    
			} ) ;
			
			$( "#" + this.UniqId() ).jqxGrid( {
				// attributs dont l'option n'est pas configurable, sinon on perd en fonctionnalités
			    editable: true,
			    columns: columns,
			    source: dataAdapter,
			    selectionmode: "singlecell",
			    scrollmode: "logical",
			    // options globales du module configurable via le XML
				theme: this.get( 'sTheme' ),
				rowsheight: this.get( 'iRowsHeight' ),
			    columnsheight: this.get( 'iColumnsHeight' ),
				width: this.get( 'iWidth' ),
				height: this.get( 'iHeight' ), 
			    sortable: this.get( 'bSortable' ),
			    showstatusbar: this.get( 'bShowStatusBar' ), 
			    statusbarheight: this.get( 'iStatusBarHeight' ), 
			    showaggregates: this.get( 'bShowAggregates' ), 
			    columnsresize: this.get( 'bColumnResize' ),
			    filterable: this.get( 'bFilterable' ),
			    showfilterrow: this.get( 'bFilterRow' )
			} ) ;
			
			var localizationobj = {} ;
			var days = {
                // full day names
                names: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
                // abbreviated day names
                namesAbbr: ["Dim", "Lun", "Mar", "Mecr", "Jeu", "Ven", "Sam"],
                // shortest day names
                namesShort: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"]
	        } ;
	        localizationobj.days = days ;
	        var months = {
                // full month names (13 months for lunar calendards -- 13th month should be "" if not lunar)
                names: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre", ""],
                // abbreviated month names
                namesAbbr: ["Jan", "Fev", "Mar", "Avr", "Mai", "Juin", "Juil", "Aou", "Sep", "Oct", "Nov", "Dec", ""]
	        } ;
	        localizationobj.months = months ;
	        localizationobj.patterns = {
	            // short date pattern
	            d: "dd/MM/yyyy",
	            // long date pattern
	            D: "dddd dd MMMM yyyy",
	            // short time pattern
	            t: "h:mm tt",
	            // long time pattern
	            T: "h:mm:ss tt",
	            // long date, short time pattern
	            f: "dddd dd MMMM yyyy h:mm tt",
	            // long date, long time pattern
	            F: "dddd dd MMMM yyyy h:mm:ss tt",
	            // month/day pattern
	            M: "dd MMMM",
	            // month/year pattern
	            Y: "MMMM yyyy",
	            // S is a sortable format that does not vary by culture
	            S: "yyyy\u0027-\u0027MM\u0027-\u0027dd\u0027T\u0027HH\u0027:\u0027mm\u0027:\u0027ss"
	        } ;	
	        localizationobj.decimalseparator = this.get( 'sDecimalSeparator' ) ;
	        localizationobj.thousandsseparator = this.get( 'sThousandSeparator' ) ;
	        $( "#" + this.UniqId() ).jqxGrid('localizestrings', localizationobj ) ;
	        
	        // il faut que le curseur voo3 suive celui de la grille
			$( "#" + this.UniqId() ).bind( 'cellselect', function ( event ) {
				that.get( 'oDataSet' ).DataSetManager().SelectRowFromPrimaryKey(
					that.get( 'oDataSet' ),
					that.get( 'aIds' )[args.rowindex]
				) ;
			} ) ;
			
			// on met en cache la ligne éditée
			$( "#" + this.UniqId() ).bind( 'cellbeginedit', function ( event ) { 
				//console.log( "cellbeginedit:" + event.args.rowindex ) ;
				that.set( "iComputedIndex", event.args.rowindex ) ;
				that.set( 'bCompute', false ) ;
			} ) ;
			
			// tracer la cinétique des évènements de l'API
			$( "#" + this.UniqId() ).bind( 'cellendedit', function ( event ) { 
				//console.log( "cellendedit: " + event.args.rowindex ) ;
			} ) ;
			
	        // mise à jour au changement de valeur dans une cellule
			$( "#" + this.UniqId() ).bind( 'cellvaluechanged', function ( event ) {
				var args = event.args ;
				var column = $( '#' + that.UniqId() ).jqxGrid( 'getcolumn', args.datafield ) ;
			    var fieldValue = args.value ;
			    
			    var oComputedColumns = that.get( "oComputedColumns") ;
			    for( var f in oComputedColumns ) {
					$( "#" + that.UniqId() ).jqxGrid( 
						'setcellvalue', 
						that.get( "iComputedIndex"), 
						f, 
						oComputedColumns[f]
					) ;
			    	oComputedColumns[f]
			    }
			    that.set( "oComputedColumns", {} ) ;
			    
			    // pour les fkey_dico_ext le mapping se fait sur le checkchange Event (cf geteditorvalue)
			    if ( column.columntype == "template" ) {
			    	return ;
			    }
			    
			    // attention à l'annulation de la saisie => test / null
				if ( typeof( args.value ) == "object" && args.value ) {
					fieldValue = args.value.value ;
				}
				else if( column.columntype == "datetimeinput" && args.value != null ) {
					args.value.setHours( 12 ) ;
					fieldValue = args.value.toISOString().substr( 0, 10 ) ;
				}
				
				that.get( 'oDataSet' ).DataSetManager().SelectRowFromPrimaryKey(
					that.get( 'oDataSet' ),
					that.get( 'aIds' )[args.rowindex]
				) ;
				that.get( 'oDataSet' ).DataSetManager().SetFieldValue( 
					that.get( 'oDataSet' ).Field( args.datafield ), 
					fieldValue 
				) ;
				var params = { 
					DIALOG_OPTIONS : {
						bDlgDisabled : true
					}
				} ;
				that.get( 'oDataSet' ).DataSetManager().get( 'oFrame' ).Save( null, params ) ;
			} ) ;
			
			// On mémorise la ligne de la cellule seléctionnée pour gérer les "delete" 
			$( "#" + this.UniqId() ).bind( 'cellselect', function ( event ) {
				that.set( 'iRowIndex', event.args.rowindex ) ;
			} ) ;
			
			// Ajout d'une ligne en fin de grille
			$( "#" + this.UniqId() + this.get( 'sSuffixAddButton' ) ).bind( 'click', function () {
                that.get( 'oDataSet' ).DataSetManager().NewRecord( that.get( 'oDataSet' ) ) ;
				var params = { 
					DIALOG_OPTIONS : {
						bDlgDisabled : true
					}
				} ;
                that.get( 'oDataSet' ).DataSetManager().get( 'oFrame' ).Save( null, params ) ; 
                that.set( 'bNewId', true ) ; 
                // l'ajout de la ligne se fait après le commit pour récupérer l'autoincrément
                //  => DataSetChangeEvent
                
                // On ne permet pas d'ajouter une 2nde ligne  
                this.disabled = true ;
            } ) ;
			
			// Suppression de la ligne sur laquelle se trouve le curseur
			$( "#" + this.UniqId() + this.get( 'sSuffixDeleteButton' ) ).bind( 'click', function () {
				
                var rowscount = $( "#" + that.UniqId() ).jqxGrid( 'getdatainformation' ).rowscount ;
                var selectedrowindex = that.get( 'iRowIndex' ) ;
                
                if ( selectedrowindex >= 0 && selectedrowindex < rowscount ) {
                    var id = $( "#" + that.UniqId() ).jqxGrid( 'getrowid', selectedrowindex ) ;
                    
                    var confirmMsg = 'Ligne "' + that.get( 'aIds' )[selectedrowindex] + '"\n' ; 
                    confirmMsg += that.get( 'sDeleteMsg' ) ;
    				if ( ! window.confirm( confirmMsg ) ) {
    					return ;
    				}
                    
                    var deleteIds = [] ;
                    deleteIds.push( that.get( 'aIds' )[selectedrowindex] ) ;
                    
                    // suppression Voozanoo 4
                    that.get( 'oDataSet' ).DataSetManager().DeleteRecords( 
                    	that.get( 'oDataSet' ), 
                    	deleteIds
                    ) ;
                    
                    // suppression dans l'interface graphique 
                    $( "#" + that.UniqId() ).jqxGrid( 'deleterow', id ) ;
                    
                    // on reconstruit le tableau mappant les index du tableau sur les clés primaires
                    for ( var j = selectedrowindex; j < that.get( 'aIds' ).length; j++ ) {
                    	that.get( 'aIds' )[j] = that.get( 'aIds' )[j+1] ;
                    }
                    that.get( 'aIds' ).pop() ;
                    
                    // on redonne la main pour la saisie au clavier
    				$( "#" + that.UniqId() ).jqxGrid( 'focus' ) ;
                }
                else {
                	window.alert( "Aucune ligne selectionnée" ) ;
                	return ;
                }
            } ) ;
			
			// Export Excel de la grille
			$( "#" + this.UniqId() + this.get( 'sSuffixExportButton' ) ).bind( 'click', function () {
				var records = $( "#" + that.UniqId() ).jqxGrid( "source" ).records ;
				var columns = $( "#" + that.UniqId() ).jqxGrid( "columns" ).records ;
				var rows = [] ;
				for ( var r = 0; r < records.length; r++ ) {
					var row = {} ;
					for( var c = 0; c < columns.length; c++ ) {
						var f = columns[c].displayfield ;
						var df = columns[c].datafield ;
						row[df] = records[r][f] ;
					}
					rows.push( row ) ;
				}
				
				$( "#" + that.UniqId() ).jqxGrid( 
					'exportdata', 
					'xls', 
					that.UniqId(), 
					true, // exporte les entetes de colonne
					rows, // limite l'export à certaines lignes
					false, // exporte les colonnes masquées
					Y.Url.build( { uri : "jqwidget/export/savefile" } ), // controlleur qui va gérer l'export
					"utf8"
				) ; 
            } ) ;
			
			// Impression de la grille
			$( "#" + this.UniqId() + this.get( 'sSuffixPrintButton' ) ).bind( 'click', function () {
				var gridContent = $( "#" + that.UniqId() ).jqxGrid( 'exportdata',  'html' ) ;
				var popup = window.open( "", "", "width=800, height=600" ) ;
				var d = popup.document.open() ;
				var htmlContent = "<html><head><meta charset='utf-8' /></head><body>" + gridContent + "</body></html>" ;
				d.write( htmlContent ) ;
				d.close() ;
				popup.print() ;
            } ) ;
			
			// Pour que les formats soient bien définis au chargement :
			$( "#" + this.UniqId() ).jqxGrid( "render" ) ;
		}
	} ) ;
} ) ;
