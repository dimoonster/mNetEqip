/**
 * Created by Di_Moon on 19.01.2015.
 */
(function($) {
    var methods;

    methods = {
        // ------------------ Init -----------------
        init: function (options) {
            var options = $.extend({
                callback: null,
                url: '',
                menuName: 'collapsedMenu',
                menuData: null
            }, options);

            return this.each(function () {
                var $this = $(this);
                //$this.empty();

                if (options.url == '') return;

                console.log($this.attr("id"));

                $this.mCollapsedMenu('addGroup', {saveStatus:1, grpName:'InitalTestName1', grpTitle:'Inital Test Title'});
                $this.mCollapsedMenu('addGroup', {saveStatus:1, grpName:'InitalTestName2', grpTitle:'Inital Test Title 2'});
                $this.mCollapsedMenu('addGroup', {saveStatus:1, grpName:'InitalTestName2', grpTitle:'Inital Test Title 2'});

                $.isFunction(options.callback) && options.callback.call($this);
            })
        },
        // ----------------- Добавление группы ---------------
        addGroup: function (grpData) {
            var grpData = $.extend({
                callback: null,
                grpName: '',                // имя группы
                grpTitle: '',               // Текстовый заголовок группы
                collapsed: true,            // Отоброжать развёрнутим или нет (по умолчанию - нет)
                saveStatus: 0               // Сохранена ли группы на сервере 0 - нет, 1 - да
            }, grpData);

            return this.each(function () {
                var $this = $(this);

                if (!grpData.grpName.trim()) return;
                if (!grpData.grpTitle.trim()) grpData.grpTitle = grpData.grpTitle.trim();

                // Сохраним данные о группе
                var savedData = $this.data('grpActualData');
                console.log(typeof savedData);
                if(typeof savedData === 'undefined') {
                    savedData = [];
                }
                if(savedData[grpData.grpName]) {
                    console.log('mCollapsedMenu.addGroup: Группа с именем ['+grpData.grpName+'] уже существует');
                    return;
                }
                savedData[grpData.grpName] = {title: grpData.grpTitle, items: null, saveStatus:grpData.saveStatus};
                $this.data('grpActualData', savedData);

                var grpId = 'mCollapsedMenu-' + $this.attr("id")+'-grp-'+grpData.grpName;

                var output = $('<div class="accordion-group" id="'+grpId+'" />');

                // Сформируем заголовок
                var grpHead = $('<div class="accordion-heading" />');
                grpHead.append($('<a/>', {
                    'html': grpData.grpTitle,
                    'class': 'accordion-toggle',
                    'href': '#'+grpId+'-items',
                    'data-toggle': 'collapse',
                    'data-parent': $this.attr("id")
                }));

                //console.log(grpHead);
                var grpButtons = $('<div/>', { 'class': 'accordion-buttons' });
                $('<a/>', {
                    'href': '#addGroup', 'role': 'button', 'class': 'btn btn-m-small btn-default', 'data-toggle': 'modal',
                    'data-title': 'Добавить тип номенклатуры', 'data-type': 3, 'data-grpid': grpData.grpName
                })
                    .appendTo(grpButtons)
                    .append('<span class="glyphicon glyphicon-plus" aria-hidden="true" />');

                $('<a/>', {
                    'href': '#addGroup','role': 'button', 'class': 'btn btn-m-small btn-default', 'data-toggle': 'modal',
                    'data-title': 'Редактирование группы номенклатуры', 'data-type': 2, 'data-grpid': grpData.grpName
                })
                    .appendTo(grpButtons)
                    .append('<span class="glyphicon glyphicon-edit" aria-hidden="true" />');

                grpHead.append(grpButtons);

                output.append(grpHead);

                // Сформируем тело обёртку
                var grpBody = $('<div/>', {
                    'id': grpId+'-items',
                    'class': 'accordion-body collapse'+(grpData.collapsed?'':' in'),
                    'style': ''
                });

                var grpBodyContainer = $('<div/>', {'class':'accordion-inner'});
                grpBodyContainer.append($('<ul/>'));
                grpBody.append(grpBodyContainer);
                output.append(grpBody);
                output.appendTo($this);

                $.isFunction(grpData.callback) && grpData.callback.call($this);
            })
        },
        // ------------------ Добавление элемента в группу --------------
        addItem: function (itemData) {
            var itemData = $.extend({
                callback: null,
                grpName: '',
                itemTitle: ''
            }, itemData);

            return this.each(function () {
                var $this = $(this);
                $this.empty();

                $.isFunction(itemData.callback) && itemData.callback.call($this);
            })
        }
    };

    $.fn.mCollapsedMenu = function(method) {
        if(methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if(typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            console.log('Метод ['+method+'] не существует');
        }
    }
})(jQuery);