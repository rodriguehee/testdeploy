YUI.add("widgetselectout",function(b){function a(){a.superclass.constructor.apply(this,arguments)}a.NAME="WidgetSelectOut";a.ATTRS={sDestination:{value:""}};b.WidgetSelectOut=b.extend(a,b.WidgetSelect,{initializer:function(){var c=this.get("oWidgetDef");if(!b.Lang.isUndefined(c.options)){if(!b.Lang.isUndefined(c.options.destination)){this.set("sDestination",c.options.destination)}}},ValueChangeEvent:function(e){if(this.Field()==null){return}if(this.Field().DataSet().UniqId()==e.DataSet().UniqId()&&this.Field().GetName()==e.GetName()){var g=this.Field().GetValue();var d=this.Layout().GetWidget(this.get("sDestination"));if(g==null){d.Field().SetValue(null)}else{var c=this.Field().GetDico().PrimaryKey();c.DataSet().RowData().First();while(c.DataSet().RowData().EoF(true)==false){if(c.GetValue()==g){var f=c.DataSet().Field("short_label");d.Field().SetValue(f.GetValue());break}c.DataSet().RowData().Next()}}this.Update();d.Update()}}})},"0.0.1",{requires:["widgetfield"]});