
if (!window.EcomDev) {
    window.EcomDev = {};
}

if (!EcomDev.Sphinx) {
    EcomDev.Sphinx = {};
}

EcomDev.Sphinx.SeoUrl = Class.create({
    initialize: function (container, config) {
        if (Object.isArray(config.value) || !config.value) {
            config.value = {};
        }

        if (Object.isArray(config.stores) || !config.stores) {
            config.stores = {};
        }

        if (Object.isArray(config.options) || !config.options) {
            config.options = {};
        }

        this.container = $(container);
        this.config = config;
        this.stores = $H(config.stores);
        this.value = $H(config.value);
        this.options = $H(config.options);

        var containerTemplate = new Template(config.template);
        this.container.update(containerTemplate.evaluate({id: this.container.identify()}));
        this.template = new Template(config.row_template);
        this.select = this.container.down('.footer .option');
        this.updateSelect();
        this.updateRows();
        this.container.down('.footer button.add').observe('click', this.addRow.bind(this));
    },
    updateSelect: function () {
        if (this.options === false) {
            return;
        }

        var keys = this.options.keys();
        this.select.update('');
        for (var i = 0, l = keys.length; i < l; i ++) {
            if (!this.value.get(keys[i])) {
                var option = new Element('option', {value: keys[i]});
                this.select.insert({bottom: option});
                option.update(this.options.get(keys[i]));
            }
        }
    },
    addRow: function () {
        if (this.select.value && !this.value.get(this.select.value)) {
            var map = this.getDefaultOptions();
            var code = this.select.value;
            this.value.set(code, map);
            this.updateSelect();
            this.renderRow(code, map);
        }
    },
    getDefaultOptions: function () {
        return {};
    },
    updateRows: function () {
        var keys = this.value.keys();
        for (var i = 0, l = keys.length; i < l; i ++) {
            var map = this.value.get(keys[i]);
            if (this.options.get(keys[i])) {
                this.renderRow(keys[i], map);
            }
        }
    },
    renderRow: function (code, map) {
        var table = this.container.down('.body');
        var id = this.container.identify() + '-' + code;
        table.insert({
            bottom: this.template.evaluate({
                id: id,
                fieldPrefix: this.config.name,
                code: code,
                label: this.options.get(code)
            })
        });

        this.setRowValues($(id), code, map);
        $(id).down('.delete-row').observe('click', this.removeRow.bind(this, id, code));
    },
    setRowValues: function (row, code, map) {
        if (map.slug) {
            row.down('.default-slug').value = map.slug;
        }

        row.stores = $H({});
        row.storeSelect = row.down('.locale-select');
        row.storeInputId = row.storeSelect.readAttribute('data-input-id');
        row.storeInputName = row.storeSelect.readAttribute('data-input-name');
        row.storeTemplate = row.down('.locale-template');
        row.storeDiv = row.down('.locale-container');
        row.storeTemplateText = row.storeTemplate.innerHTML;
        row.storeAddBtn = row.down('.add');

        if (!Object.isArray(map.store_slug) && map.store_slug) {
            for (var storeId in map.store_slug) {
                if (this.stores.get(storeId)) {
                    this.insertLocale(row, storeId, map.locales[storeId]);
                }
            }
        }

        row.storeAddBtn.observe('click', function () {
            if (row.storeSelect.value) {
                this.insertLocale(row, row.storeSelect.value, '');
                this.renderLocaleSelect(row);
            }
        }.bind(this));

        this.renderLocaleSelect(row);
    },
    insertLocale: function (row, storeId, value) {
        row.stores.set(storeId, true);
        var wrapper = new Element('div', {'class': 'locale'});
        wrapper.update(row.storeTemplateText.replace('__locale__', storeId));
        row.storeDiv.insert({bottom: wrapper});
        wrapper.delete = wrapper.down('.delete');
        wrapper.input = wrapper.down('.input');
        wrapper.label = wrapper.down('.label');
        wrapper.delete.observe('click', this.deleteLocale.bind(this, row, wrapper, storeId));
        wrapper.label.down('.text').update(this.stores.get(storeId).escapeHTML());
        wrapper.input.value = value;
        wrapper.input.writeAttribute('id', row.storeInputId.replace('__locale__', storeId));
        wrapper.label.writeAttribute('for', row.storeInputId.replace('__locale__', storeId));
        wrapper.input.writeAttribute('name', row.storeInputName.replace('__locale__', storeId));
    },
    deleteLocale: function (row, wrapper, storeId) {
        row.stores.unset(storeId);
        row.storeDiv.removeChild(wrapper);
        this.renderLocaleSelect(row);
    },
    renderLocaleSelect: function (row) {
        row.storeSelect.update('');
        this.stores.each(function (pair) {
            if (!row.stores.get(pair.key)) {
                row.storeSelect.insert({bottom: (new Element('option', {value: pair.key})).update(pair.value)});
            }
        });

        if (row.storeSelect.down('option')) {
            row.storeSelect.show();
            row.storeAddBtn.show();
        } else {
            row.storeSelect.hide();
            row.storeAddBtn.hide();
        }
    },
    removeRow: function (id, code) {
        this.value.unset(code);
        this.updateSelect();
        var element = $(id);
        element.up().removeChild(element);
    }
});
