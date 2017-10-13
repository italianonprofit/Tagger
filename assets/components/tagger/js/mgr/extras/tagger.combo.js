Tagger.combo.UserGroup = function(config, getStore) {
    config = config || {};
    Ext.applyIf(config,{
        displayField: 'name'
        ,valueField: 'id'
        ,fields: ['id','name']
        ,mode: 'remote'
        ,forceFormValue: false
        ,allowAddNewData: false
        ,addNewDataOnBlur : false
        ,itemDelimiterKey: 188
        ,triggerAction: 'all'
        ,typeAheadDelay: 200
        ,valueDelimiter:','
        ,minChars: 1
        ,clearBtnCls: 'x-form-trigger'
        ,expandBtnCls: 'x-form-trigger'
        ,typeAhead: true
        ,editable: true
        ,forceSelection: true
        ,pageSize: 20
        ,url: MODx.config.connector_url
        ,baseParams: {action: 'security/group/getlist'}
    });
    Ext.applyIf(config,{
        store: new Ext.data.JsonStore({
            url: config.url
            ,root: 'results'
            ,totalProperty: 'total'
            ,fields: config.fields
            ,errorReader: MODx.util.JSONReader
            ,baseParams: config.baseParams || {}
            ,remoteSort: config.remoteSort || false
            ,autoDestroy: true
        })
    });
    if (getStore === true) {
        config.store.load();
        return config.store;
    }

    Tagger.combo.UserGroup.superclass.constructor.call(this,config);

    this.on('newitem', function(bs,v,f){

        v = v.split(',');
        Ext.each(v, function(item){
            item = item.replace(/^\s+|\s+$/g, '');
            var newObj = {
                tag: item,
                id: item
            };
            bs.addNewItem(newObj);
        });
    });
    this.on('removeitem', function(combo){
        combo.lastQuery = '';
    });

    this.on('blur', function(combo){
        if(combo.lastQuery){
            var v = combo.lastQuery.split(',');
            Ext.each(v, function(item){
                item = item.replace(/^\s+|\s+$/g, '');
                var newObj = {
                    tag: item
                };
                combo.addNewItem(newObj);
            });
        }
    });
    this.config = config;
    return this;
};
Ext.extend(Tagger.combo.UserGroup,Ext.ux.form.SuperBoxSelect,{
    setValue : function(value){

        if(!this.rendered){
            console.debug("non renderizzato");
            this.value = value;
            return;
        }
        this.removeAllItems().resetStore();

        this.remoteLookup = [];

        if(Ext.isEmpty(value)){
            return;
        }

        if(!Ext.isEmpty(this.value)){
            return value;
        }
        var values = value;
        if(!Ext.isArray(value)){
            value = '' + value;
            values = value.split(this.valueDelimiter);
        }

        Ext.each(values,function(val){
            val = val.replace(/^\s+|\s+$/g, '');
            var record = this.findRecord(this.valueField, val);
            if(record){
                this.addRecord(record);
            }else if(this.mode === 'remote'){
                this.remoteLookup.push(val);
            }
        },this);

        if(this.mode === 'remote'){
            var q = this.remoteLookup.join(this.queryValuesDelimiter);
            this.doQuery(q,false, true); //3rd param to specify a values query
        }

    },
    refresh:function(value){
        this.value = value;
        var values = value.split(this.valueDelimiter);
        this.getStore().load();
        var ids = [];
        Ext.each(this.items.items, function(item){
            //tagField.addNewItem(item);
            ids.push(item.value);
            //this.addValue(item.value);ù

            this.usedRecords.push(new Ext.ux.form.SuperBoxSelectItem({
                caption: item.value,
                value: item.value,
                display: item.value
            }));
        });
        this.value = ids.join(",");
        this.startValue = ids.join(",");
        Ext.each(values,function(val){
            val = val.replace(/^\s+|\s+$/g, '');
            var record = this.findRecord(this.valueField, val);
            if(record){
                this.addRecord(record);
            }

        },this);
    }
});
Ext.reg('Tagger-combo-UserGroup',Tagger.combo.UserGroup);

/*
Tagger.combo.UserGroup = function (config, getStore) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'fake_groups'
        ,hiddenName: 'fake_groups'
        ,valueField: "id"
        ,displayField: "name"
        ,mode: 'remote'
        ,triggerAction: 'all'
        ,typeAhead: true
        ,editable: true
        ,forceSelection: false
        ,extraItemCls: 'x-tag'
        ,clearBtnCls: 'x-form-trigger'
        ,expandBtnCls: 'x-form-trigger'
        ,xtype:'superboxselect'
        ,url: MODx.config.connector_url
        ,baseParams: {
            action: 'security/group/getlist'
        }
        ,fields: ['name','id','description']
    });
    Ext.applyIf(config,{

        store: new Ext.data.JsonStore({
            url: config.url
            ,root: 'results'
            ,fields: config.fields
            ,errorReader: MODx.util.JSONReader
            ,baseParams: config.baseParams || {}
            ,remoteSort: config.remoteSort || false
            ,autoDestroy: true
            ,listeners: {
                'load': {fn:function(store, records, options ) {
                }}
                ,scope : this
            }
        })
        ,listeners: {
            'beforeselect': {fn:function(combo, record, index ) {
                if (record.data.is_parent == '1'){
                    return false;
                }
            }}
            ,scope : this
        }
    });
    if (getStore === true) {
        config.store.load();
        return config.store;
    }
    Tagger.combo.UserGroup.superclass.constructor.call(this, config);
    this.config = config;
    return this;
};
Ext.extend(Tagger.combo.UserGroup, Ext.ux.form.SuperBoxSelect);
Ext.reg('modx-superbox-group', Tagger.combo.UserGroup);
*/
Tagger.combo.TagSuperSelect = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        triggerAction: 'all'
        ,lazyRender: true
        ,mode: 'remote'
        ,displayField: 'tag'
        ,preventRender: true
        ,valueField: 'id'
        ,editable: true
        ,fields: ['tag','id']
        ,url: Tagger.config.connectorUrl
        ,baseParams:{
            action: 'mgr/tag/getlist',
            limit:0
        }
    });
    Tagger.combo.TagSuperSelect.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.TagSuperSelect,MODx.combo.ComboBox);
Ext.reg('tagger-combo-TagSuperSelect',Tagger.combo.TagSuperSelect);

Tagger.combo.Group = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'group'
        ,hiddenName: 'group'
        ,displayField: 'name'
        ,valueField: 'id'
        ,fields: ['name','id']
        ,pageSize: 20
        ,url: Tagger.config.connectorUrl
        ,baseParams:{
            action: 'mgr/group/getlist'
        }
    });
    Tagger.combo.Group.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.Group,MODx.combo.ComboBox);
Ext.reg('tagger-combo-group',Tagger.combo.Group);

Tagger.combo.Tag = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'tag'
        ,hiddenName: 'tag'
        ,displayField: 'tag'
        ,valueField: 'id'
        ,fields: ['tag','id']
        ,pageSize: 20
        ,editable: config.allowAdd
        ,forceSelection: !config.allowAdd
        ,url: Tagger.config.connectorUrl
        ,baseParams:{
            action: 'mgr/tag/getlist'
        }
    });
    Tagger.combo.Tag.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.Tag,MODx.combo.ComboBox);
Ext.reg('tagger-combo-tag',Tagger.combo.Tag);

Tagger.combo.FieldType = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('tagger.field.tagfield') ,'tagger-field-tags'],
                [_('tagger.field.combobox') ,'tagger-combo-tag']
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,value: 'tagger-field-tags'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Tagger.combo.FieldType.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.FieldType,MODx.combo.ComboBox);
Ext.reg('tagger-combo-field-type',Tagger.combo.FieldType);

Tagger.combo.FilterType = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                ['MULTICHECK' ,'MULTICHECK'],
                ['RADIO' ,'RADIO'],
                ['TAGS' ,'TAGS'],
                ['BOOL' ,'BOOL'],
                ['RANGE' ,'RANGE'],
                ['TEXT' ,'TEXT']
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Tagger.combo.FilterType.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.FilterType,MODx.combo.ComboBox);
Ext.reg('tagger-combo-filter-type',Tagger.combo.FilterType);

Tagger.combo.Templates = function(config, getStore) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'templates'
        ,hiddenName: 'templates'
        ,displayField: 'templatename'
        ,valueField: 'id'
        ,fields: ['templatename','id']
        ,mode: 'remote'
        ,triggerAction: 'all'
        ,typeAhead: true
        ,editable: true
        ,forceSelection: true
        ,pageSize: 20
        ,url: MODx.config.connectors_url + 'element/template.php'
        ,baseParams: {
            action: 'getlist'
        }
    });
    Ext.applyIf(config,{
        store: new Ext.data.JsonStore({
            url: config.url
            ,root: 'results'
            ,totalProperty: 'total'
            ,fields: config.fields
            ,errorReader: MODx.util.JSONReader
            ,baseParams: config.baseParams || {}
            ,remoteSort: config.remoteSort || false
            ,autoDestroy: true
        })
    });
    if (getStore === true) {
        config.store.load();
        return config.store;
    }
    Tagger.combo.Templates.superclass.constructor.call(this,config);
    this.config = config;
    return this;
};
Ext.extend(Tagger.combo.Templates,Ext.ux.form.SuperBoxSelect);
Ext.reg('tagger-combo-templates',Tagger.combo.Templates);

Tagger.combo.TV = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'tv'
        ,hiddenName: 'tv'
        ,displayField: 'name'
        ,valueField: 'id'
        ,fields: ['name','id']
        ,pageSize: 20
        ,url: Tagger.config.connectorUrl
        ,baseParams:{
            action: 'mgr/extra/gettvs'
        }
    });
    Tagger.combo.TV.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.TV,MODx.combo.ComboBox);
Ext.reg('tagger-combo-tv',Tagger.combo.TV);

Tagger.combo.GroupPlace = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('tagger.group.place_in_tab') ,'in-tab'],
                [_('tagger.group.place_tvs_tab') ,'in-tvs'],
                [_('tagger.group.place_above_content') ,'above-content'],
                [_('tagger.group.place_below_content') ,'below-content'],
                [_('tagger.group.place_bottom_page') ,'bottom-page']
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,value: 'in-tab'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Tagger.combo.GroupPlace.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.GroupPlace,MODx.combo.ComboBox);
Ext.reg('tagger-combo-group-place',Tagger.combo.GroupPlace);

Tagger.combo.SortDir = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('tagger.group.sort_asc') ,'asc'],
                [_('tagger.group.sort_desc') ,'desc']
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,value: 'asc'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Tagger.combo.SortDir.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.SortDir,MODx.combo.ComboBox);
Ext.reg('tagger-combo-sort-dir',Tagger.combo.SortDir);

Tagger.combo.SortField = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('tagger.group.sort_field_alias') ,'alias'],
                [_('tagger.group.sort_field_rank') ,'rank']
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,value: 'alias'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Tagger.combo.SortField.superclass.constructor.call(this,config);
};
Ext.extend(Tagger.combo.SortField,MODx.combo.ComboBox);
Ext.reg('tagger-combo-sort-field',Tagger.combo.SortField);
