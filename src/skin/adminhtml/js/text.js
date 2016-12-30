
if (!window.EcomDev) {
    window.EcomDev = {};
}

if (!EcomDev.Sphinx) {
    EcomDev.Sphinx = {};
}

EcomDev.Sphinx.SeoFilter = Class.create({
    initialize: function (container, config) {
        if (Object.isArray(config.value) || !config.value) {
            config.value = {};
        }

        if (Object.isArray(config.attributes) || !config.attributes) {
            config.attributes = {};
        }

        if (Object.isArray(config.attributeOptions) || !config.attributeOptions) {
            config.attributeOptions = {};
        }

        this.container = $(container);
        this.config = config;
        this.attributes = $H(config.attributes);
        this.value = $H(config.value);
        this.attributeOptions = $H(config.attributeOptions);

        var containerTemplate = new Template(config.template);
        this.container.update(containerTemplate.evaluate({id: this.container.identify()}));
        this.template = new Template(config.row_template);
        this.select = this.container.down('.footer .attribute');
        this.updateSelect();
        this.updateRows();
        this.container.down('.footer button.add').observe('click', this.addRow.bind(this));
    },
    updateSelect: function () {
        if (this.attributes === false) {
            return;
        }

        var keys = this.attributes.keys();
        this.select.update('');
        for (var i = 0, l = keys.length; i < l; i ++) {
            if (!this.value.get(keys[i])) {
                var option = new Element('option', {value: keys[i]});
                this.select.insert({bottom: option});
                option.update(this.attributes.get(keys[i]));
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
            if (this.attributes.get(keys[i])) {
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
                label: this.attributes.get(code)
            })
        });

        this.setRowValues($(id), code, map);
        $(id).down('.delete-row').observe('click', this.removeRow.bind(this, id, code));
    },

    fillRowFromMap: function (row, code, map) {
        for (var optionId in map) {
            if (typeof(this.attributeOptions.get(code)[optionId]) === 'string') {
                this.insertOption(row, optionId, this.attributeOptions.get(code)[optionId]);
            }
        }
    },

    setRowValues: function (row, code, map) {
        row.attributeCode = code;
        row.options = $H({});
        row.optionSelect = row.down('.option-select');
        row.optionInputId = row.optionSelect.readAttribute('data-input-id');
        row.optionInputName = row.optionSelect.readAttribute('data-input-name');
        row.optionTemplate = row.down('.option-template');
        row.optionDiv = row.down('.option-container');
        row.optionTemplateText = row.optionTemplate.innerHTML;
        row.optionAddBtn = row.down('.add');


        this.fillRowFromMap(row, code, map);

        row.optionAddBtn.observe('click', function () {
            if (row.optionSelect.value) {
                this.insertOption(row, row.optionSelect.value, '');
                this.renderOptionSelect(row);
            }
        }.bind(this));

        this.renderOptionSelect(row);
    },
    insertOption: function (row, optionId, value) {
        row.options.set(optionId, true);
        var wrapper = new Element('div', {'class': 'option'});
        wrapper.update(row.optionTemplateText.replace('__option__', optionId));
        row.optionDiv.insert({bottom: wrapper});
        wrapper.delete = wrapper.down('.delete');
        wrapper.input = wrapper.down('.input');
        wrapper.label = wrapper.down('.label');
        wrapper.delete.observe('click', this.deleteOption.bind(this, row, wrapper, optionId));
        wrapper.label.down('.text').update(this.attributeOptions.get(row.attributeCode)[optionId].escapeHTML());
        wrapper.input.value = value;
        wrapper.input.writeAttribute('id', row.optionInputId.replace('__option__', optionId));
        wrapper.label.writeAttribute('for', row.optionInputId.replace('__option__', optionId));
        wrapper.input.writeAttribute('name', row.optionInputName.replace('__option__', optionId));
    },
    deleteOption: function (row, wrapper, optionId) {
        row.options.unset(optionId);
        row.optionDiv.removeChild(wrapper);
        this.renderOptionSelect(row);
    },
    renderOptionSelect: function (row) {
        row.optionSelect.update('');
        var options = this.attributeOptions.get(row.attributeCode);
        if (options) {
            for (var optionId in options) {
                if (!row.options.get(optionId)) {
                    row.optionSelect.insert({bottom: (new Element('option', {value: optionId})).update(options[optionId])});
                }
            }
        }

        if (row.optionSelect.down('option')) {
            row.optionSelect.show();
            row.optionAddBtn.show();
        } else {
            row.optionSelect.hide();
            row.optionAddBtn.hide();
        }
    },
    removeRow: function (id, code) {
        this.value.unset(code);
        this.updateSelect();
        var element = $(id);
        element.up().removeChild(element);
    }
});
