/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	__webpack_require__(1);

	__webpack_require__(2);

	__webpack_require__(4);

/***/ },
/* 1 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	(0, _jquery2.default)('.themosis-color-field').wpColorPicker();

/***/ },
/* 3 */
/***/ function(module, exports) {

	module.exports = jQuery;

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(6);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(5);

	var _backbone2 = _interopRequireDefault(_backbone);

	var _ItemModel = __webpack_require__(7);

	var _ItemModel2 = _interopRequireDefault(_ItemModel);

	var _ItemView = __webpack_require__(8);

	var _ItemView2 = _interopRequireDefault(_ItemView);

	var _ItemsCollection = __webpack_require__(9);

	var _ItemsCollection2 = _interopRequireDefault(_ItemsCollection);

	var _ItemsView = __webpack_require__(10);

	var _ItemsView2 = _interopRequireDefault(_ItemsView);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	// Implementation
	var collections = (0, _jquery2.default)('div.themosis-collection-wrapper');

	_underscore2.default.each(collections, function (elem) {
	    // Check if there are files in the rendered collection field.
	    // If not, create an empty collection to listen to and attach it to
	    // the collection field view. Also create a buttons view and pass it
	    // the collection as a dependency.
	    var collectionField = (0, _jquery2.default)(elem),
	        list = collectionField.find('ul.themosis-collection-list'),
	        items = list.children();

	    // Instantiate a collection.
	    var c = new _ItemsCollection2.default();

	    // Instantiate a collection view.
	    var cView = new _ItemsView2.default({
	        collection: c,
	        el: collectionField
	    });

	    console.log(c, cView);

	    if (items.length) {
	        _underscore2.default.each(items, function (el) {
	            var item = (0, _jquery2.default)(el),
	                input = item.find('input');

	            var m = new _ItemModel2.default({
	                'value': parseInt(input.val()),
	                'src': item.find('img').attr('src'),
	                'type': collectionField.data('type'),
	                'title': item.find('.filename').children().text()
	            });

	            // Add the model to the collection.
	            c.add(m);

	            // Create an item view instance.
	            new _ItemView2.default({
	                model: m,
	                el: item,
	                collection: c,
	                collectionView: cView
	            });
	        });
	    }
	});

/***/ },
/* 5 */
/***/ function(module, exports) {

	module.exports = Backbone;

/***/ },
/* 6 */
/***/ function(module, exports) {

	module.exports = _;

/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _backbone = __webpack_require__(5);

	var _backbone2 = _interopRequireDefault(_backbone);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var ItemModel = (function (_Backbone$Model) {
	    _inherits(ItemModel, _Backbone$Model);

	    function ItemModel(options) {
	        _classCallCheck(this, ItemModel);

	        var _this = _possibleConstructorReturn(this, Object.getPrototypeOf(ItemModel).call(this, options));

	        _this.defaults = {
	            'selected': false,
	            'value': '', // The media file ID
	            'src': '',
	            'type': 'image', // The media file URL
	            'title': ''
	        };
	        return _this;
	    }

	    return ItemModel;
	})(_backbone2.default.Model);

	exports.default = ItemModel;

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(6);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(5);

	var _backbone2 = _interopRequireDefault(_backbone);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var ItemView = (function (_Backbone$View) {
	    _inherits(ItemView, _Backbone$View);

	    function ItemView(options) {
	        _classCallCheck(this, ItemView);

	        var _this = _possibleConstructorReturn(this, Object.getPrototypeOf(ItemView).call(this, options));

	        _this.tagName = 'li';
	        _this.template = '#themosis-collection-item-template';
	        _this.events = {
	            'click div.themosis-collection__item': 'select',
	            'click a.check': 'removeItem'
	        };

	        _this.collectionView = options.collectionView;
	        _this.listenTo(_this.collection, 'removeSelected', _this.removeSelection);
	        return _this;
	    }

	    /**
	     * Render the collection item.
	     *
	     * @returns {ItemView}
	     */

	    _createClass(ItemView, [{
	        key: 'render',
	        value: function render() {
	            var template = _underscore2.default.template(this.collectionView.$el.find(this.template).html());
	            this.$el.html(template(this.model.toJSON()));

	            if ('image' !== this.model.get('type')) {
	                this.$el.find('img').addClass('icon');
	                this.$el.find('.filename').addClass('show');
	            }

	            return this;
	        }

	        /**
	         * Triggered when the item image is clicked. Set the state of
	         * the element as selected so the collection can perform action into it.
	         *
	         * @return void
	         */

	    }, {
	        key: 'select',
	        value: function select() {
	            var item = this.$el.children('div.themosis-collection__item');

	            if (item.hasClass('selected')) {
	                // Deselected
	                item.removeClass('selected');
	                this.model.set('selected', false);
	            } else {
	                // Selected
	                item.addClass('selected');
	                this.model.set('selected', true);
	            }
	        }

	        /**
	         * Remove the selected items/models from the collection.
	         * When an item is removed individually, an event is sent to
	         * the collection which will remove the model from its list.
	         *
	         * @param items The selected items to be removed.
	         * @return void
	         */

	    }, {
	        key: 'removeSelection',
	        value: function removeSelection(items) {
	            var _this2 = this;

	            _underscore2.default.each(items, function (elem) {
	                // If this view model is equal to the passed model
	                // remove it.
	                if (_this2.model.cid === elem.cid) {
	                    _this2.remove();
	                    _this2.collection.remove(_this2.model);
	                }
	            }, this);
	        }

	        /**
	         * Triggered when the '-' button is clicked. Remove the item
	         * from the current collection.
	         *
	         * @param {object} e The event object.
	         * @return void
	         */

	    }, {
	        key: 'removeItem',
	        value: function removeItem(e) {
	            e.preventDefault();

	            // Remove the view item
	            this.remove();

	            // Remove from the collection
	            this.collection.remove(this.model);
	        }
	    }]);

	    return ItemView;
	})(_backbone2.default.View);

	exports.default = ItemView;

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(6);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(5);

	var _backbone2 = _interopRequireDefault(_backbone);

	var _ItemModel = __webpack_require__(7);

	var _ItemModel2 = _interopRequireDefault(_ItemModel);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var ItemsCollection = (function (_Backbone$Collection) {
	    _inherits(ItemsCollection, _Backbone$Collection);

	    function ItemsCollection(options) {
	        _classCallCheck(this, ItemsCollection);

	        var _this = _possibleConstructorReturn(this, Object.getPrototypeOf(ItemsCollection).call(this, options));

	        _this.model = _ItemModel2.default;

	        // Events
	        _this.on('change:selected', _this.onSelect);
	        _this.on('remove', _this.onSelect);
	        _this.on('add', _this.onAdd);
	        return _this;
	    }

	    /**
	     * Triggered when a model in the collection changes
	     * its 'selected' value.
	     *
	     * @return void
	     */

	    _createClass(ItemsCollection, [{
	        key: 'onSelect',
	        value: function onSelect() {
	            // Check if there are selected items.
	            // If one or more items are selected, show the main remove button.
	            var selectedItems = this.where({ 'selected': true });

	            this.trigger('itemsSelected', selectedItems);

	            // Trigger an event where we can check the length of the collection.
	            // Use to hide/show the collection container.
	            this.trigger('collectionToggle', this);
	        }

	        /**
	         * Triggered when a model is added in the collection.
	         *
	         * @return void
	         */

	    }, {
	        key: 'onAdd',
	        value: function onAdd() {
	            // Trigger an event in order to check the display of the collection container.
	            this.trigger('collectionToggle', this);
	        }
	    }]);

	    return ItemsCollection;
	})(_backbone2.default.Collection);

	exports.default = ItemsCollection;

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(6);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(5);

	var _backbone2 = _interopRequireDefault(_backbone);

	var _ItemModel = __webpack_require__(7);

	var _ItemModel2 = _interopRequireDefault(_ItemModel);

	var _ItemView = __webpack_require__(8);

	var _ItemView2 = _interopRequireDefault(_ItemView);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var ItemsView = (function (_Backbone$View) {
	    _inherits(ItemsView, _Backbone$View);

	    _createClass(ItemsView, [{
	        key: 'events',
	        get: function get() {
	            return {
	                'click button#themosis-collection-add': 'add',
	                'click button#themosis-collection-remove': 'removeSelectedItems'
	            };
	        }
	    }]);

	    function ItemsView(options) {
	        _classCallCheck(this, ItemsView);

	        // Bind to collection events

	        var _this = _possibleConstructorReturn(this, Object.getPrototypeOf(ItemsView).call(this, options));

	        _this.collection.bind('itemsSelected', _this.toggleRemoveButton, _this);
	        _this.collection.bind('collectionToggle', _this.toggleCollectionContainer, _this);

	        // Init a WordPress media window.
	        _this.frame = wp.media({
	            // Define behaviour of the media window.
	            // 'post' if related to a WordPress post.
	            // 'select' if use outside WordPress post.
	            frame: 'select',
	            // Allow or not multiple selection.
	            multiple: true,
	            // The displayed title.
	            title: 'Insert media',
	            // The button behaviour
	            button: {
	                text: 'Insert',
	                close: true
	            },
	            // Type of files shown in the library.
	            // 'image', 'application' (pdf, doc,...)
	            library: {
	                type: _this.$el.data('type')
	            }
	        });

	        // Attach an event on select. Runs when "insert" button is clicked.
	        _this.frame.on('select', _underscore2.default.bind(_this.selectedItems, _this));

	        // Grab the limit.
	        _this.limit = parseInt(_this.$el.data('limit'));

	        // Init the sortable feature.
	        _this.sort();
	        return _this;
	    }

	    /**
	     * Listen to media frame select event and retrieve the selected files.
	     *
	     * @return void
	     */

	    _createClass(ItemsView, [{
	        key: 'selectedItems',
	        value: function selectedItems() {
	            var selection = this.frame.state('library').get('selection');

	            // Check if a limit is defined. Only filter the selection if selection is larger than the limit.
	            if (this.limit) {
	                var realLimit = this.limit - this.collection.length < 0 ? 0 : this.limit - this.collection.length;
	                selection = selection.slice(0, realLimit);
	            }

	            selection.map(function (attachment) {
	                this.insertItem(attachment);
	            }, this);
	        }

	        /**
	         * Insert selected items to the collection view and its collection.
	         *
	         * @param attachment The attachment model from the WordPress media API.
	         * @return void
	         */

	    }, {
	        key: 'insertItem',
	        value: function insertItem(attachment) {
	            // Build a specific model for this attachment.
	            var m = new _ItemModel2.default({
	                'value': attachment.get('id'),
	                'src': this.getAttachmentThumbnail(attachment),
	                'type': attachment.get('type'),
	                'title': attachment.get('title')
	            });

	            // Build a view for this attachment and pass it its model and current collection.
	            var itemView = new _ItemView2.default({
	                model: m,
	                collection: this.collection,
	                collectionView: this
	            });

	            // Add the model to the collection.
	            this.collection.add(m);

	            // Add the model to the DOM.
	            this.$el.find('ul.themosis-collection-list').append(itemView.render().el);
	        }

	        /**
	         * Get the attachment thumbnail URL and returns it.
	         *
	         * @param {object} attachment The attachment model.
	         * @return {string} The attachment thumbnail URL.
	         */

	    }, {
	        key: 'getAttachmentThumbnail',
	        value: function getAttachmentThumbnail(attachment) {
	            var type = attachment.get('type'),
	                url = attachment.get('icon');

	            if ('image' === type) {
	                // Check if the _themosis_media size is available.
	                var sizes = attachment.get('sizes');

	                if (undefined !== sizes._themosis_media) {
	                    url = sizes._themosis_media.url;
	                } else {
	                    // Original image is less than 100px.
	                    url = sizes.full.url;
	                }
	            }

	            return url;
	        }

	        /**
	         * Handle the display of the main remove button.
	         *
	         * @return void
	         */

	    }, {
	        key: 'toggleRemoveButton',
	        value: function toggleRemoveButton(items) {
	            var length = items.length ? true : false;

	            if (length) {
	                // Show the main remove button.
	                this.$el.find('button#themosis-collection-remove').addClass('show');
	            } else {
	                // Hide the main remove button.
	                this.$el.find('button#themosis-collection-remove').removeClass('show');
	            }
	        }

	        /**
	         * Handle the display of the collection container.
	         *
	         * @return void
	         */

	    }, {
	        key: 'toggleCollectionContainer',
	        value: function toggleCollectionContainer(collection) {
	            var length = collection.length,
	                addButton = this.$el.find('button#themosis-collection-add'),
	                container = this.$el.find('div.themosis-collection-container');

	            // Check the number of collection items.
	            // If total is larger or equal to length, disable the add button.
	            if (this.limit && this.limit <= length) {
	                addButton.addClass('disabled');
	            } else {
	                // Re-activate the ADD button if there are less items than the limit.
	                addButton.removeClass('disabled');
	            }

	            // Show the collection container if there are items in collection.
	            if (length) {
	                container.addClass('show');
	            } else {
	                // Hide the collection container.
	                container.removeClass('show');
	            }
	        }

	        /**
	         * Triggered when 'add' button is clicked. Open the media library.
	         *
	         * @param e The event object
	         * @return void
	         */

	    }, {
	        key: 'add',
	        value: function add(e) {
	            console.log('Add', e, this);

	            // Check the Add button.
	            var addButton = (0, _jquery2.default)(e.currentTarget);

	            // If button is disabled, return.
	            if (addButton.hasClass('disabled')) return;

	            this.frame.open();
	        }

	        /**
	         * Triggered when 'remove' button is clicked. Tell view/collection
	         * to remove files from the current collection.
	         *
	         * @return void
	         */

	    }, {
	        key: 'removeSelectedItems',
	        value: function removeSelectedItems() {
	            // Call parent view to trigger its method to remove files from its collection.
	            var selectedItems = this.collection.where({ 'selected': true });

	            this.collection.trigger('removeSelected', selectedItems);
	        }

	        /**
	         * Allow collection items to be sortable using drag&drop.
	         *
	         * @return void
	         */

	    }, {
	        key: 'sort',
	        value: function sort() {
	            this.$el.find('ul.themosis-collection-list').sortable({
	                helper: function helper(e, ui) {
	                    ui.children().each(function () {
	                        (0, _jquery2.default)(this).width((0, _jquery2.default)(this).width());
	                    });
	                    return ui;
	                },
	                forcePlaceholderSize: true,
	                placeholder: 'themosis-collection-ui-state-highlight',
	                handle: '.themosis-collection__item'
	            });
	        }
	    }]);

	    return ItemsView;
	})(_backbone2.default.View);

	exports.default = ItemsView;

/***/ }
/******/ ]);