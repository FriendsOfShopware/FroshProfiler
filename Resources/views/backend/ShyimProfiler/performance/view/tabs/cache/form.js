//{block name="backend/performance/view/tabs/cache/form" append}
Ext.override(Shopware.apps.Performance.view.tabs.cache.Form, {
    getItems: function () {
        var me = this,
            parent = me.callParent(arguments);

        parent[1].items.push({
            name: 'cache[profiler]',
            boxLabel: 'Profiler-Cache',
            supportText: 'Holds profile caches'
        });

        return parent;
    }
});
//{/block}