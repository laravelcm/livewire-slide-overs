/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/slide-over.js":
/*!************************************!*\
  !*** ./resources/js/slide-over.js ***!
  \************************************/
/***/ (() => {

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
window.SlideOver = function () {
  return {
    open: false,
    showActiveComponent: true,
    activeComponent: false,
    componentHistory: [],
    panelWidth: null,
    panelPosition: null,
    listeners: [],
    getActiveComponentPanelAttribute: function getActiveComponentPanelAttribute(key) {
      if (this.$wire.get('components')[this.activeComponent] !== undefined) {
        return this.$wire.get('components')[this.activeComponent]['panelAttributes'][key];
      }
    },
    closePanelOnEscape: function closePanelOnEscape(trigger) {
      if (this.getActiveComponentPanelAttribute('closeOnEscape') === false) {
        return;
      }
      var force = this.getActiveComponentPanelAttribute('closeOnEscapeIsForceful') === true;
      this.closePanel(force);
    },
    closePanelOnClickAway: function closePanelOnClickAway(trigger) {
      if (this.getActiveComponentPanelAttribute('closeOnClickAway') === false) {
        return;
      }
      this.closePanel(true);
    },
    closePanel: function closePanel() {
      var force = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      var skipPreviousPanels = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
      var destroySkipped = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
      if (this.show === false) {
        return;
      }
      if (this.getActiveComponentPanelAttribute('dispatchCloseEvent') === true) {
        var componentName = this.$wire.get('components')[this.activeComponent].name;
        Livewire.dispatch('panelClosed', {
          name: componentName
        });
      }
      if (this.getActiveComponentPanelAttribute('destroyOnClose') === true) {
        Livewire.dispatch('destroyComponent', {
          id: this.activeComponent
        });
      }
      if (skipPreviousPanels > 0) {
        for (var i = 0; i < skipPreviousPanels; i++) {
          if (destroySkipped) {
            var _id = this.componentHistory[this.componentHistory.length - 1];
            Livewire.dispatch('destroyComponent', {
              id: _id
            });
          }
          this.componentHistory.pop();
        }
      }
      var id = this.componentHistory.pop();
      if (id && !force) {
        if (id) {
          this.setActivePanelComponent(id, true);
        } else {
          this.setShowPropertyTo(false);
        }
      } else {
        this.setShowPropertyTo(false);
      }
    },
    setActivePanelComponent: function setActivePanelComponent(id) {
      var _this = this;
      var skip = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      this.setShowPropertyTo(true);
      if (this.activeComponent === id) {
        return;
      }
      if (this.activeComponent !== false && skip === false) {
        this.componentHistory.push(this.activeComponent);
      }
      var focusableTimeout = 50;
      if (this.activeComponent === false) {
        this.activeComponent = id;
        this.showActiveComponent = true;
        this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass');
        this.panelPosition = this.getActiveComponentPanelAttribute('position');
      } else {
        this.showActiveComponent = false;
        focusableTimeout = 400;
        setTimeout(function () {
          _this.activeComponent = id;
          _this.showActiveComponent = true;
          _this.panelWidth = _this.getActiveComponentPanelAttribute('maxWidthClass');
          _this.panelPosition = _this.getActiveComponentPanelAttribute('position');
        }, 300);
      }
      this.$nextTick(function () {
        var _this$$refs$id;
        var focusable = (_this$$refs$id = _this.$refs[id]) === null || _this$$refs$id === void 0 ? void 0 : _this$$refs$id.querySelector('[autofocus]');
        if (focusable) {
          setTimeout(function () {
            focusable.focus();
          }, focusableTimeout);
        }
      });
    },
    focusables: function focusables() {
      var selector = "a, button, input:not([type='hidden']), textarea, select, details, [tabindex]:not([tabindex='-1'])";
      return _toConsumableArray(this.$el.querySelectorAll(selector)).filter(function (el) {
        return !el.hasAttribute('disabled');
      });
    },
    firstFocusable: function firstFocusable() {
      return this.focusables()[0];
    },
    lastFocusable: function lastFocusable() {
      return this.focusables().slice(-1)[0];
    },
    nextFocusable: function nextFocusable() {
      return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable();
    },
    prevFocusable: function prevFocusable() {
      return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable();
    },
    nextFocusableIndex: function nextFocusableIndex() {
      return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1);
    },
    prevFocusableIndex: function prevFocusableIndex() {
      return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1;
    },
    setShowPropertyTo: function setShowPropertyTo(open) {
      var _this2 = this;
      this.open = open;
      if (open) {
        document.body.classList.add('overflow-y-hidden');
      } else {
        document.body.classList.remove('overflow-y-hidden');
        setTimeout(function () {
          _this2.activeComponent = false;
          _this2.$wire.resetState();
        }, 300);
      }
    },
    init: function init() {
      var _this3 = this;
      this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass');
      this.panelPosition = this.getActiveComponentPanelAttribute('position');
      console.log(this.getActiveComponentPanelAttribute('maxWidthClass'));
      this.listeners.push(Livewire.on('closePanel', function (data) {
        var _data$force, _data$skipPreviousPan, _data$destroySkipped;
        _this3.closePanel((_data$force = data === null || data === void 0 ? void 0 : data.force) !== null && _data$force !== void 0 ? _data$force : false, (_data$skipPreviousPan = data === null || data === void 0 ? void 0 : data.skipPreviousPanels) !== null && _data$skipPreviousPan !== void 0 ? _data$skipPreviousPan : 0, (_data$destroySkipped = data === null || data === void 0 ? void 0 : data.destroySkipped) !== null && _data$destroySkipped !== void 0 ? _data$destroySkipped : false);
      }));
      this.listeners.push(Livewire.on('activePanelComponentChanged', function (_ref) {
        var id = _ref.id;
        _this3.setActivePanelComponent(id);
      }));
    },
    destroy: function destroy() {
      this.listeners.forEach(function (listener) {
        listener();
      });
    }
  };
};

/***/ }),

/***/ "./resources/css/slide-over.css":
/*!**************************************!*\
  !*** ./resources/css/slide-over.css ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/public/slide-over": 0,
/******/ 			"public/slide-over": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["public/slide-over"], () => (__webpack_require__("./resources/js/slide-over.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["public/slide-over"], () => (__webpack_require__("./resources/css/slide-over.css")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;