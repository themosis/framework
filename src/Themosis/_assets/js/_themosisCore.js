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

	__webpack_require__(12);

	__webpack_require__(16);

	__webpack_require__(17);

	__webpack_require__(18);

	__webpack_require__(20);

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

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	var _ItemModel = __webpack_require__(7);

	var _ItemModel2 = _interopRequireDefault(_ItemModel);

	var _ItemView = __webpack_require__(8);

	var _ItemView2 = _interopRequireDefault(_ItemView);

	var _ItemsCollection = __webpack_require__(9);

	var _ItemsCollection2 = _interopRequireDefault(_ItemsCollection);

	var _ItemsView = __webpack_require__(10);

	var _ItemsView2 = _interopRequireDefault(_ItemsView);

	__webpack_require__(11);

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

	module.exports = _;

/***/ },
/* 6 */
/***/ function(module, exports) {

	module.exports = Backbone;

/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _backbone = __webpack_require__(6);

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

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var ItemView = (function (_Backbone$View) {
	    _inherits(ItemView, _Backbone$View);

	    function ItemView() {
	        _classCallCheck(this, ItemView);

	        return _possibleConstructorReturn(this, Object.getPrototypeOf(ItemView).apply(this, arguments));
	    }

	    _createClass(ItemView, [{
	        key: 'initialize',
	        value: function initialize(options) {
	            this.collectionView = options.collectionView;
	            this.listenTo(this.collection, 'removeSelected', this.removeSelection);
	        }

	        /**
	         * Render the collection item.
	         *
	         * @returns {ItemView}
	         */

	    }, {
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
	    }, {
	        key: 'tagName',
	        get: function get() {
	            return 'li';
	        }
	    }, {
	        key: 'template',
	        get: function get() {
	            return '#themosis-collection-item-template';
	        }
	    }, {
	        key: 'events',
	        get: function get() {
	            return {
	                'click div.themosis-collection__item': 'select',
	                'click a.check': 'removeItem'
	            };
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

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	var _ItemModel = __webpack_require__(7);

	var _ItemModel2 = _interopRequireDefault(_ItemModel);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var ItemsCollection = (function (_Backbone$Collection) {
	    _inherits(ItemsCollection, _Backbone$Collection);

	    function ItemsCollection() {
	        _classCallCheck(this, ItemsCollection);

	        return _possibleConstructorReturn(this, Object.getPrototypeOf(ItemsCollection).apply(this, arguments));
	    }

	    _createClass(ItemsCollection, [{
	        key: 'initialize',
	        value: function initialize() {
	            // Events
	            this.on('change:selected', this.onSelect);
	            this.on('remove', this.onSelect);
	            this.on('add', this.onAdd);
	        }

	        /**
	         * Triggered when a model in the collection changes
	         * its 'selected' value.
	         *
	         * @return void
	         */

	    }, {
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
	    }, {
	        key: 'model',
	        get: function get() {
	            return _ItemModel2.default;
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

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

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

	    function ItemsView() {
	        _classCallCheck(this, ItemsView);

	        return _possibleConstructorReturn(this, Object.getPrototypeOf(ItemsView).apply(this, arguments));
	    }

	    _createClass(ItemsView, [{
	        key: 'initialize',
	        value: function initialize() {
	            // Bind to collection events
	            this.collection.bind('itemsSelected', this.toggleRemoveButton, this);
	            this.collection.bind('collectionToggle', this.toggleCollectionContainer, this);

	            // Init a WordPress media window.
	            this.frame = wp.media({
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
	                    type: this.$el.data('type')
	                }
	            });

	            // Attach an event on select. Runs when "insert" button is clicked.
	            this.frame.on('select', _underscore2.default.bind(this.selectedItems, this));

	            // Grab the limit.
	            this.limit = parseInt(this.$el.data('limit'));

	            // Init the sortable feature.
	            this.sort();
	        }

	        /**
	         * Listen to media frame select event and retrieve the selected files.
	         *
	         * @return void
	         */

	    }, {
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
	    }, {
	        key: 'events',
	        get: function get() {
	            return {
	                'click button#themosis-collection-add': 'add',
	                'click button#themosis-collection-remove': 'removeSelectedItems'
	            };
	        }
	    }]);

	    return ItemsView;
	})(_backbone2.default.View);

	exports.default = ItemsView;

/***/ },
/* 11 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	var _MediaModel = __webpack_require__(13);

	var _MediaModel2 = _interopRequireDefault(_MediaModel);

	var _MediaView = __webpack_require__(14);

	var _MediaView2 = _interopRequireDefault(_MediaView);

	__webpack_require__(15);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	/**
	 * Implementation.
	 */
	var mediaFields = (0, _jquery2.default)('table.themosis-media').closest('tr');

	_underscore2.default.each(mediaFields, function (elem) {
	    var input = (0, _jquery2.default)(elem).find('input.themosis-media-input');

	    var data = new _MediaModel2.default({
	        value: input.val(),
	        type: input.data('type'),
	        size: input.data('size')
	    });

	    new _MediaView2.default({
	        model: data,
	        el: (0, _jquery2.default)(elem)
	    });
	});

/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var MediaModel = (function (_Backbone$Model) {
	    _inherits(MediaModel, _Backbone$Model);

	    function MediaModel(options) {
	        _classCallCheck(this, MediaModel);

	        var _this = _possibleConstructorReturn(this, Object.getPrototypeOf(MediaModel).call(this, options));

	        _this.defaults = {
	            value: '', // Register the attachment ID
	            type: 'image',
	            size: 'full',
	            display: '', // The text to display - Actually the attachment ID
	            thumbUrl: '', // The src url of the icon/image to use for thumbnail
	            title: ''
	        };
	        return _this;
	    }

	    return MediaModel;
	})(_backbone2.default.Model);

	exports.default = MediaModel;

/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var MediaView = (function (_Backbone$View) {
	    _inherits(MediaView, _Backbone$View);

	    function MediaView() {
	        _classCallCheck(this, MediaView);

	        return _possibleConstructorReturn(this, Object.getPrototypeOf(MediaView).apply(this, arguments));
	    }

	    _createClass(MediaView, [{
	        key: 'initialize',
	        value: function initialize() {
	            // Init field properties.
	            // The hidden input DOM element.
	            this.input = this.$el.find('.themosis-media-input');

	            // The <p> DOM element.
	            this.display = this.$el.find('p.themosis-media__path');

	            // The img thumbnail DOM element.
	            this.thumbnail = this.$el.find('img.themosis-media-thumbnail');

	            // Init a WordPress media window.
	            this.frame = wp.media({
	                // Define behaviour of the media window.
	                // 'post' if related to a WordPress post.
	                // 'select' if use outside WordPress post.
	                frame: 'select',
	                // Allow or not multiple selection.
	                multiple: false,
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
	                    type: this.model.get('type')
	                }
	            });

	            // Attach an event on select. Runs when "insert" button is clicked.
	            this.frame.on('select', _underscore2.default.bind(this.select, this));
	        }

	        /**
	         * Handle event when add button is clicked.
	         *
	         * @param {object} event
	         * @return void
	         */

	    }, {
	        key: 'addMedia',
	        value: function addMedia(event) {
	            event.preventDefault();

	            // Open the media window
	            this.open();
	        }

	        /**
	         * Open the media library window and display it.
	         *
	         * @return void
	         */

	    }, {
	        key: 'open',
	        value: function open() {
	            this.frame.open();
	        }

	        /**
	         * Run when an item is selected in the media library.
	         * The event is fired when the "insert" button is clicked.
	         *
	         * @return void
	         */

	    }, {
	        key: 'select',
	        value: function select() {
	            var selection = this.getItem(),
	                type = selection.get('type'),
	                val = selection.get('id'),
	                display = selection.get('id'),
	                thumbUrl = selection.get('icon'),
	                // Default image url to icon.
	            title = selection.get('title');

	            // If image, get a thumbnail.
	            if ('image' === type) {
	                // Check if the defined size is available.
	                var sizes = selection.get('sizes');

	                if (undefined !== sizes._themosis_media) {
	                    thumbUrl = sizes._themosis_media.url;
	                } else {
	                    thumbUrl = sizes.full.url;
	                }
	            }

	            // Update the model.
	            this.model.set({
	                value: val,
	                display: display,
	                thumbUrl: thumbUrl,
	                title: title
	            });

	            // Update the DOM elements.
	            this.input.val(val);
	            this.display.html(display);
	            this.thumbnail.attr('src', thumbUrl);

	            // Update filename
	            // and show it if not an image.
	            var filename = this.$el.find('div.filename');
	            filename.find('div').html(title);

	            if ('image' !== type) {
	                if (!filename.hasClass('show')) {
	                    filename.addClass('show');
	                }
	            }

	            this.toggleButtons();
	        }

	        /**
	         * Get the selected item from the library.
	         *
	         * @returns {object} A backbone model object.
	         */

	    }, {
	        key: 'getItem',
	        value: function getItem() {
	            return this.frame.state().get('selection').first();
	        }

	        /**
	         * Handle event when delete button is clicked.
	         *
	         * @param {object} event
	         */

	    }, {
	        key: 'deleteMedia',
	        value: function deleteMedia(event) {
	            event.preventDefault();

	            // Reset input
	            this.resetInput();

	            // Toggle buttons
	            this.toggleButtons();
	        }

	        /**
	         * Reset the hidden input value and the model.
	         *
	         * @returns void
	         */

	    }, {
	        key: 'resetInput',
	        value: function resetInput() {
	            this.input.val('');
	            this.model.set({ value: '' });
	        }

	        /**
	         * Toggle buttons display.
	         *
	         * @returns void
	         */

	    }, {
	        key: 'toggleButtons',
	        value: function toggleButtons() {
	            var cells = this.$el.find('table.themosis-media .themosis-media-preview, table.themosis-media .themosis-media-infos, table.themosis-media button');

	            _underscore2.default.each(cells, function (elem) {
	                elem = (0, _jquery2.default)(elem);

	                if (elem.hasClass('themosis-media--hidden')) {
	                    elem.removeClass('themosis-media--hidden');
	                } else {
	                    elem.addClass('themosis-media--hidden');
	                }
	            });
	        }
	    }, {
	        key: 'events',

	        /**
	         * View events.
	         *
	         * @returns {{click #themosis-media-add: string, click #themosis-media-delete: string}}
	         */
	        get: function get() {
	            return {
	                'click #themosis-media-add': 'addMedia',
	                'click #themosis-media-delete': 'deleteMedia'
	            };
	        }
	    }]);

	    return MediaView;
	})(_backbone2.default.View);

	exports.default = MediaView;

/***/ },
/* 15 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 16 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 17 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	__webpack_require__(19);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	//------------------------------------------------
	// Custom publish metabox.
	//------------------------------------------------
	// Handle the custom statuses.
	var submitdiv = (0, _jquery2.default)('#themosisSubmitdiv'),
	    editButton = submitdiv.find('.edit-post-status'),
	    selectDiv = submitdiv.find('#post-status-select'),
	    selectTag = submitdiv.find('#post_status'),
	    statusLabel = submitdiv.find('#post-status-display'),
	    statusButtons = submitdiv.find('.save-post-status, .cancel-post-status'),
	    originalPublish = submitdiv.find('input#original_publish'),
	    publishButton = submitdiv.find('input#publish');

	// Edit button
	editButton.on('click', function (e) {
	    e.preventDefault();

	    // Show the select option list.
	    (0, _jquery2.default)(undefined).hide();
	    selectDiv.slideDown(200);
	});

	// Cancel button or OK buttons
	statusButtons.on('click', function (e) {
	    e.preventDefault();

	    var button = (0, _jquery2.default)(this);

	    // If 'ok' button, update label span with status label.
	    if (button.hasClass('save-post-status')) {
	        // Grab selected label.
	        var selected = selectTag.find(':selected'),
	            label = selected.text(),
	            publishText = selected.data('publish');

	        // Update label text.
	        statusLabel.text(label);

	        // Update publish button.
	        // Check if 'draft'
	        if ('draft' === selected.val()) {
	            // Change value of the "original_publish" input.
	            originalPublish.val('auto-draft');
	            // Change publish button name attribute.
	            publishButton.attr('name', 'save');
	        }

	        // Change publish button text.
	        publishButton.val(publishText);
	    }

	    // If 'cancel' button, make sure to reset the select tag value.
	    if (button.hasClass('cancel-post-status')) {
	        var selected = selectTag.find('option[selected="selected"]');
	        selectTag.val(selected.val());
	    }

	    // Show back edit button.
	    editButton.show();

	    // Close select statuses.
	    selectDiv.slideUp(200);
	});

	//------------------------------------------------
	// Quick edit select tag.
	//------------------------------------------------

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	/**
	 * Handle quickedit status select tag.
	 */
	(function ($, _) {
	    if (!thfmk_themosis._themosisPostTypes) return; // Check if global object is defined first.

	    var cpts = thfmk_themosis._themosisPostTypes,
	        cptInput = $('input.post_type_page'),
	        cpt = cptInput.val(),
	        select = $('.inline-edit-status select[name=_status]');

	    // Check if current post type screen use custom statuses.
	    var keys = _.keys(cpts); // Grab object keys first level down.

	    if (!_.contains(keys, cpt)) return; // Return false if cpt is not found in the keys array. If so, stop and return.

	    // Clean select tag
	    // Keep Draft option only.
	    select.find('option[value!="draft"]').remove();

	    // Loop through the statuses
	    _.each(cpts[cpt]['statuses'], function (obj, key) {
	        select.append('<option value="' + key + '">' + obj.label + '</option>');
	    });
	})(_jquery2.default, _underscore2.default);

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _InfiniteView = __webpack_require__(21);

	var _InfiniteView2 = _interopRequireDefault(_InfiniteView);

	__webpack_require__(24);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	// Implementation.
	// List all infinite fields.
	var infinites = (0, _jquery2.default)('div.themosis-infinite-container').closest('tr');

	_underscore2.default.each(infinites, function (elem) {
	    var infinite = (0, _jquery2.default)(elem),
	        rows = infinite.find('tr.themosis-infinite-row');

	    // Create an infiniteView instance for each infinite field.
	    new _InfiniteView2.default({
	        el: infinite.find('table.themosis-infinite>tbody'),
	        rows: rows
	    });
	});

/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	var _AddView = __webpack_require__(22);

	var _AddView2 = _interopRequireDefault(_AddView);

	var _RowView = __webpack_require__(23);

	var _RowView2 = _interopRequireDefault(_RowView);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	/**
	 * Global event object.
	 * Used to make component talk to each other.
	 *
	 * @type {Object}
	 */
	var vent = _underscore2.default.extend({}, _backbone2.default.Events);

	var InfiniteView = (function (_Backbone$View) {
	    _inherits(InfiniteView, _Backbone$View);

	    function InfiniteView() {
	        _classCallCheck(this, InfiniteView);

	        return _possibleConstructorReturn(this, Object.getPrototypeOf(InfiniteView).apply(this, arguments));
	    }

	    _createClass(InfiniteView, [{
	        key: 'initialize',
	        value: function initialize(options) {
	            // Retrieve passed parameters.
	            this.options = options;

	            // Number of rows.
	            this.updateCount();

	            // Set the limit.
	            this.limit();

	            // Attach the main "add" button to the view.
	            new _AddView2.default({
	                el: this.$el.closest('.themosis-infinite-container').find('div.themosis-infinite-add-field-container'),
	                parent: this
	            });

	            // Create inner rows view and pass them their parent infinite view.
	            this.setRows();

	            // Global events.
	            vent.on('row:sort', this.update, this);

	            // Make it sortable.
	            this.sort();
	        }

	        /**
	         * Create inner rows views.
	         */

	    }, {
	        key: 'setRows',
	        value: function setRows() {
	            var _this2 = this;

	            _underscore2.default.each(this.options.rows, function (elem) {
	                // DOM elements.
	                var row = (0, _jquery2.default)(elem);

	                // Backbone elements.
	                // Setup row views.
	                new _RowView2.default({
	                    el: row,
	                    parent: _this2
	                });
	            }, this);
	        }

	        /**
	         * Handle the sortable event/feature.
	         */

	    }, {
	        key: 'sort',
	        value: function sort() {
	            this.$el.sortable({
	                helper: function helper(e, ui) {
	                    ui.children().each(function () {
	                        (0, _jquery2.default)(this).width((0, _jquery2.default)(this).width());
	                    });
	                    return ui;
	                },
	                forcePlaceholderSize: true,
	                placeholder: 'themosis-ui-state-highlight',
	                handle: '.themosis-infinite-order',
	                update: function update() {
	                    vent.trigger('row:sort');
	                }
	            });
	        }

	        /**
	         * Grab the first row, reset its values and returns it.
	         *
	         * @returns {Object} A row view object.
	         */

	    }, {
	        key: 'getFirstRow',
	        value: function getFirstRow() {
	            var row = this.$el.find('tr.themosis-infinite-row').first().clone(),
	                rowView = new _RowView2.default({
	                el: row,
	                parent: this
	            });

	            return rowView.reset();
	        }

	        /**
	         * Add a new row to the collection.
	         */

	    }, {
	        key: 'add',
	        value: function add() {
	            // Check the limit.
	            if (0 < this.limit && this.count + 1 > this.limit) return;

	            var row = this.getFirstRow();

	            // Add the new row to the DOM.
	            this.$el.append(row.el);

	            this.update();
	        }

	        /**
	         * Insert a new row before the current one.
	         *
	         * @param {Object} currentRow The current row view object.
	         */

	    }, {
	        key: 'insert',
	        value: function insert(currentRow) {
	            // Check the limit.
	            if (0 < this.limit && this.count + 1 > this.limit) return;

	            var row = this.getFirstRow();

	            // Add the new row before the current one.
	            currentRow.$el.before(row.el);

	            this.update();
	        }

	        /**
	         * Remove a row of the collection.
	         *
	         * @param {Object} row The row view object.
	         */

	    }, {
	        key: 'remove',
	        value: function remove(row) {
	            // Keep at least one row.
	            if (1 >= this.count) return;

	            row.$el.remove();

	            this.update();
	        }

	        /**
	         * Update the Infinite custom fields values.
	         * Update row count.
	         * Update row order.
	         * Update row inner fields attributes.
	         *
	         * @return void
	         */

	    }, {
	        key: 'update',
	        value: function update() {
	            // Update row count.
	            this.updateCount();

	            // Rename the fields
	            this.rename();
	        }

	        /**
	         * Update the total number of rows.
	         */

	    }, {
	        key: 'updateCount',
	        value: function updateCount() {
	            this.count = this.$el.find('tr.themosis-infinite-row').length;
	        }

	        /**
	         * Rename all 'name', 'id' and 'for' attributes.
	         */

	    }, {
	        key: 'rename',
	        value: function rename() {
	            var _this3 = this;

	            var rows = this.$el.find('tr.themosis-infinite-row');

	            _underscore2.default.each(rows, function (row, index) {
	                // Order is 1 based.
	                index = String(index + 1);
	                row = (0, _jquery2.default)(row);

	                // Get row fields.
	                var fields = row.find('tr.themosis-field-container'),
	                    order = row.children('td.themosis-infinite-order').children('span');

	                // Update the row inner fields.
	                _underscore2.default.each(fields, function (field) {
	                    // "Field" is the <tr> tag containing all the custom field html.
	                    field = (0, _jquery2.default)(field);

	                    var input = field.find('input, textarea, select'),
	                        label = field.find('th.themosis-label>label'),
	                        collectionField = field.find('.themosis-collection-wrapper'); // Check if there is a collection field.

	                    if (!collectionField.length) {
	                        if (1 < input.length) {
	                            // Contains more than one input.
	                            _underscore2.default.each(input, function (io) {
	                                io = (0, _jquery2.default)(io);
	                                _this3.renameField(io, label, index);
	                            }, _this3);
	                        } else {
	                            // Only one input inside the field.
	                            _this3.renameField(input, label, index);
	                        }
	                    } else {
	                        // Collection field - Set its index/order as data-order.
	                        // If there is collectionField - Update its order/index property.
	                        collectionField.attr('data-order', index);
	                        _this3.renameCollectionField(collectionField, index);

	                        // Check if there are items
	                        var items = collectionField.find('ul.themosis-collection-list input');

	                        if (items.length) {
	                            // If items input, rename their 'name' attribute.
	                            _underscore2.default.each(items, function (item) {
	                                var itemInput = (0, _jquery2.default)(item),
	                                    name = _this3.renameName(itemInput.attr('name'), index);
	                                itemInput.attr('name', name);
	                            }, _this3);
	                        }
	                    }
	                }, _this3); // End inner fields.

	                // Update order display.
	                order.html(index);
	            }, this);
	        }

	        /**
	         * Rename field input and label.
	         *
	         * @param {Object} input The field input wrapped in jQuery object.
	         * @param {Object} label The field label wrapped in jQuery object.
	         * @param {String} index The index used to rename the attributes.
	         * @return void
	         */

	    }, {
	        key: 'renameField',
	        value: function renameField(input, label, index) {
	            if ('button' == input.attr('type')) {
	                if (input.hasClass('wp-picker-clear')) return;
	            }

	            var fieldId = input.attr('id'),
	                fieldName = input.attr('name'),
	                id = this.renameId(fieldId, index),
	                name = this.renameName(fieldName, index);

	            // Update the label 'for' attribute.
	            label.attr('for', id);

	            // Update input 'id' attribute.
	            input.attr('id', id);

	            // Update input 'name' attribute.
	            input.attr('name', name);
	        }

	        /**
	         * Returns a new ID attribute value.
	         *
	         * @param {String} currentId
	         * @param {String} index
	         * @return {String}
	         */

	    }, {
	        key: 'renameId',
	        value: function renameId(currentId, index) {
	            var regex = new RegExp('-([0-9]+)-');
	            return currentId.replace(regex, '-' + index + '-');
	        }

	        /**
	         * Returns a new name attribute value.
	         *
	         * @param {String} currentName
	         * @param {String} index
	         * @return {String}
	         */

	    }, {
	        key: 'renameName',
	        value: function renameName(currentName, index) {
	            var regex = new RegExp("([0-9]+)\]");
	            return currentName.replace(regex, index + ']');
	        }

	        /**
	         * Rename collection field.
	         *
	         * @param {object} field Collection field wrapped in jQuery
	         * @param {int} index The row order/index
	         * @return void
	         */

	    }, {
	        key: 'renameCollectionField',
	        value: function renameCollectionField(field, index) {
	            var regex = new RegExp("([0-9]+)\]"),
	                name = field.data('name'),
	                template = field.find('script#themosis-collection-item-template'),
	                templateContent = template.html();

	            // Update data-name attribute value.
	            field.attr('data-name', name.replace(regex, index + ']'));

	            // Update backbone template content.
	            template.html(templateContent.replace(regex, index + ']'));
	        }

	        /**
	         * Define the limit of rows a user can add.
	         */

	    }, {
	        key: 'limit',
	        value: function limit() {
	            this.limit = this.$el.data('limit');
	        }
	    }]);

	    return InfiniteView;
	})(_backbone2.default.View);

	exports.default = InfiniteView;

/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var AddView = (function (_Backbone$View) {
	    _inherits(AddView, _Backbone$View);

	    function AddView() {
	        _classCallCheck(this, AddView);

	        return _possibleConstructorReturn(this, Object.getPrototypeOf(AddView).apply(this, arguments));
	    }

	    _createClass(AddView, [{
	        key: 'initialize',
	        value: function initialize(options) {
	            this.options = options;
	        }

	        /**
	         * Send an event to add a new row.
	         */

	    }, {
	        key: 'addRow',
	        value: function addRow() {
	            // Calls the infinite parent view method.
	            this.options.parent.add();
	        }
	    }, {
	        key: 'events',
	        get: function get() {
	            return {
	                'click button#themosis-infinite-main-add': 'addRow'
	            };
	        }
	    }]);

	    return AddView;
	})(_backbone2.default.View);

	exports.default = AddView;

/***/ },
/* 23 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _jquery = __webpack_require__(3);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _underscore = __webpack_require__(5);

	var _underscore2 = _interopRequireDefault(_underscore);

	var _backbone = __webpack_require__(6);

	var _backbone2 = _interopRequireDefault(_backbone);

	var _MediaModel = __webpack_require__(13);

	var _MediaModel2 = _interopRequireDefault(_MediaModel);

	var _MediaView = __webpack_require__(14);

	var _MediaView2 = _interopRequireDefault(_MediaView);

	var _ItemsCollection = __webpack_require__(9);

	var _ItemsCollection2 = _interopRequireDefault(_ItemsCollection);

	var _ItemsView = __webpack_require__(10);

	var _ItemsView2 = _interopRequireDefault(_ItemsView);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

	function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var RowView = (function (_Backbone$View) {
	    _inherits(RowView, _Backbone$View);

	    function RowView() {
	        _classCallCheck(this, RowView);

	        return _possibleConstructorReturn(this, Object.getPrototypeOf(RowView).apply(this, arguments));
	    }

	    _createClass(RowView, [{
	        key: 'initialize',
	        value: function initialize(options) {
	            // Retrieve passed parameters
	            this.options = options;

	            _underscore2.default.bindAll(this, 'placeButton');
	            (0, _jquery2.default)(window).on('resize', this.placeButton);
	        }

	        /**
	         * Triggered when click on the row 'add' button.
	         */

	    }, {
	        key: 'insert',
	        value: function insert() {
	            this.options.parent.insert(this);
	        }

	        /**
	         * Triggered when 'delete' button is clicked.
	         */

	    }, {
	        key: 'remove',
	        value: function remove() {
	            this.options.parent.remove(this);
	        }

	        /**
	         * Place the row 'add' button.
	         */

	    }, {
	        key: 'placeButton',
	        value: function placeButton() {
	            var plusButton = this.$el.find('.themosis-infinite-add'),
	                cellHeight = this.$el.find('td.themosis-infinite-options').height(),
	                cellWidth = this.$el.find('td.themosis-infinite-options').width();

	            plusButton.css('margin-top', (cellHeight / 2 - 13) * -1);
	            plusButton.css('margin-left', cellWidth / 2 - 9);
	        }

	        /**
	         * Reset all fields value.
	         *
	         * @return {Object} The view object.
	         */

	    }, {
	        key: 'reset',
	        value: function reset() {
	            var _this2 = this;

	            var fields = this.$el.find('input, textarea, select, div.themosis-collection-wrapper');

	            _underscore2.default.each(fields, function (field) {
	                var f = (0, _jquery2.default)(field),
	                    type = f.data('field');

	                switch (type) {

	                    case 'textarea':
	                        // Reset <textarea> input
	                        _this2.resetTextarea(f);
	                        break;

	                    case 'checkbox':
	                    case 'radio':
	                        // Reset <input type="checkbox|radio">
	                        _this2.resetCheckable(f);
	                        break;

	                    case 'select':
	                        // Reset <select> tag.
	                        _this2.resetSelect(f);
	                        break;

	                    case 'media':
	                        // Reset <input type="hidden">
	                        _this2.resetInput(f);
	                        // Reset media value display and set a new backbone object media.
	                        _this2.resetMedia(f);
	                        break;

	                    case 'collection':
	                        // Reset collection field backbone objects.
	                        _this2.resetCollection(f);

	                        break;

	                    case 'button':
	                        if (f.hasClass('wp-picker-clear')) return;
	                        break;

	                    default:
	                        // Reset <input> tag.
	                        _this2.resetInput(f);
	                }
	            }, this);

	            return this;
	        }

	        /**
	         * Reset <input> value attribute.
	         *
	         * @param {Object} field The input tag wrapped in jQuery object.
	         * @return void
	         */

	    }, {
	        key: 'resetInput',
	        value: function resetInput(field) {
	            field.attr('value', '');

	            /**
	             * Check if color field input.
	             * If so, tell the script to create it.
	             */
	            if (field.hasClass('themosis-color-field')) {
	                // 0 - Get a reference to parent container.
	                var parent = field.closest('td.themosis-field');

	                // 1 - Remove the old generated color picker from the DOM.
	                parent.find('.wp-picker-container').remove();

	                // 2 - Append the input only on DOM (inside parent).
	                parent.append(field);

	                // 3 - Create the color picker.
	                field.wpColorPicker();
	            }
	        }

	        /**
	         * Reset <input type="checkbox"> and <input type="radio">.
	         *
	         * @param {Object} field The input tag wrapped in jQuery object.
	         * @return void
	         */

	    }, {
	        key: 'resetCheckable',
	        value: function resetCheckable(field) {
	            field.removeAttr('checked');
	        }

	        /**
	         * Reset <select> tag.
	         *
	         * @param {Object} field The <select> tag wrapped in Jquery object.
	         * @return void
	         */

	    }, {
	        key: 'resetSelect',
	        value: function resetSelect(field) {
	            var options = field.find('option');

	            options.each(function (i, option) {
	                (0, _jquery2.default)(option).removeAttr('selected');
	            });
	        }

	        /**
	         * Reset <textarea> tag.
	         *
	         * @param {Object} field The <textarea> tag wrapped in jQuery object.
	         * @return void
	         */

	    }, {
	        key: 'resetTextarea',
	        value: function resetTextarea(field) {
	            field.val('');
	        }

	        /**
	         * Reset the custom media field display.
	         *
	         * @param {Object} field The media hidden input tag wrapped in jQuery object.
	         * @return void
	         */

	    }, {
	        key: 'resetMedia',
	        value: function resetMedia(field) {
	            var cells = field.closest('td').find('table.themosis-media>tbody>tr').find('.themosis-media-preview, .themosis-media-infos, button'),
	                addButton = field.closest('td').find('table.themosis-media>tbody>tr').find('#themosis-media-add'),
	                mediaField = field.closest('tr.themosis-field-container');

	            // Reset path content
	            field.closest('td').find('p.themosis-media__path').html('');

	            // Toggle media cells only if it's on "delete" state.
	            if (addButton.hasClass('themosis-media--hidden')) {
	                _underscore2.default.each(cells, function (elem) {
	                    elem = (0, _jquery2.default)(elem);

	                    if (elem.hasClass('themosis-media--hidden')) {
	                        elem.removeClass('themosis-media--hidden');
	                    } else {
	                        elem.addClass('themosis-media--hidden');
	                    }
	                });
	            }

	            // Set a new backbone object for the media field.
	            var data = new _MediaModel2.default({
	                value: field.val(),
	                type: field.data('type'),
	                size: field.data('size')
	            });

	            new _MediaView2.default({
	                model: data,
	                el: mediaField
	            });
	        }

	        /**
	         * Reset the collection field.
	         *
	         * @param {object} f The collection field wrapped in jQuery.
	         * @return void
	         */

	    }, {
	        key: 'resetCollection',
	        value: function resetCollection(f) {
	            var list = f.find('ul.themosis-collection-list'),
	                container = f.find('div.themosis-collection-container');

	            // Delete all items <li>
	            list.children('li').remove();

	            // Hide the collection container
	            if (container.hasClass('show')) {
	                container.removeClass('show');
	            }

	            // Create new collection field instance - Implementation.
	            // Instantiate a collection.
	            var c = new _ItemsCollection2.default();

	            // Instantiate a collection view.
	            new _ItemsView2.default({
	                collection: c,
	                el: f
	            });
	        }
	    }, {
	        key: 'events',
	        get: function get() {
	            return {
	                'mouseenter .themosis-infinite-options': 'placeButton',
	                'click span.themosis-infinite-add': 'insert',
	                'click span.themosis-infinite-remove': 'remove'
	            };
	        }
	    }]);

	    return RowView;
	})(_backbone2.default.View);

	exports.default = RowView;

/***/ },
/* 24 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ }
/******/ ]);