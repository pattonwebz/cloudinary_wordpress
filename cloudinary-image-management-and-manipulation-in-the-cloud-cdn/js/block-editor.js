/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/src/blocks.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/src/blocks.js":
/*!**************************!*\
  !*** ./js/src/blocks.js ***!
  \**************************/
/*! exports provided: cloudinaryBlocks */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "cloudinaryBlocks", function() { return cloudinaryBlocks; });
/* harmony import */ var _components_video__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/video */ "./js/src/components/video.js");
/* harmony import */ var _components_featured_image__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/featured-image */ "./js/src/components/featured-image.js");
/* global window */

/**
 * Main JS.
 */
// Components

 // jQuery, because reasons.

var $ = window.$ = window.jQuery; // Global Constants

var cloudinaryBlocks = {
  Video: _components_video__WEBPACK_IMPORTED_MODULE_0__["default"],
  Featured: _components_featured_image__WEBPACK_IMPORTED_MODULE_1__["default"]
};

/***/ }),

/***/ "./js/src/components/featured-image.js":
/*!*********************************************!*\
  !*** ./js/src/components/featured-image.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);


/* global window wp */


 // Set our component.

var FeaturedTransformationsToggle = function FeaturedTransformationsToggle(props) {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Overwrite Transformations', 'cloudinary'),
    checked: props.overwrite_featured_transformations,
    onChange: function onChange(value) {
      return props.setOverwrite(value);
    }
  }));
}; // Setup our properties.


FeaturedTransformationsToggle = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["withSelect"])(function (select, ownProps) {
  var _select$getEditedPost;

  return {
    overwrite_featured_transformations: (_select$getEditedPost = select('core/editor').getEditedPostAttribute('meta')['_cloudinary_featured_overwrite']) !== null && _select$getEditedPost !== void 0 ? _select$getEditedPost : false
  };
})(FeaturedTransformationsToggle); // Setup our update method.

FeaturedTransformationsToggle = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["withDispatch"])(function (dispatch) {
  return {
    setOverwrite: function setOverwrite(value) {
      dispatch('core/editor').editPost({
        meta: {
          _cloudinary_featured_overwrite: value
        }
      });
    }
  };
})(FeaturedTransformationsToggle); // Hook in and add our component.

var cldFilterFeatured = function cldFilterFeatured(BlockEdit) {
  return function (props) {
    // We only need this on a MediaUpload component that has a value.
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(BlockEdit, props), !!props.value && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(FeaturedTransformationsToggle, props));
  };
}; // Setup an init wrapper.


var Featured = {
  _init: function _init() {
    // Add it to Media Upload to allow for deeper connection with getting
    // the media object, to determine if an asset has transformations.
    // Also adds deeper support for other image types within Guttenberg.
    // @todo: find other locations (i.e Video poster).
    wp.hooks.addFilter('editor.MediaUpload', 'cloudinary/filter-featured-image', cldFilterFeatured);
  }
}; // Push Init.

Featured._init(); // Export to keep it in scope.


/* harmony default export */ __webpack_exports__["default"] = (Featured);

/***/ }),

/***/ "./js/src/components/video.js":
/*!************************************!*\
  !*** ./js/src/components/video.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/objectSpread */ "./node_modules/@babel/runtime/helpers/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);



/* global window wp */




var Video = {
  _init: function _init() {
    if (typeof CLD_VIDEO_PLAYER === 'undefined') {
      return;
    } // Gutenberg Video Settings


    wp.hooks.addFilter('blocks.registerBlockType', 'Cloudinary/Media/Video', function (settings, name) {
      if (name === 'core/video') {
        if ('off' !== CLD_VIDEO_PLAYER.video_autoplay_mode) {
          settings.attributes.autoplay.default = true;
        }

        if ('on' === CLD_VIDEO_PLAYER.video_loop) {
          settings.attributes.loop.default = true;
        }

        if ('off' === CLD_VIDEO_PLAYER.video_controls) {
          settings.attributes.controls.default = false;
        }
      }

      return settings;
    });
  }
};
/* harmony default export */ __webpack_exports__["default"] = (Video); // Init.

Video._init();

var cldAddToggle = function cldAddToggle(settings, name) {
  if ('core/image' === name || 'core/video' === name) {
    if (!settings.attributes) {
      settings.attributes = {};
    }

    settings.attributes.overwrite_transformations = {
      type: 'boolean'
    };
    settings.attributes.transformations = {
      type: 'boolean'
    };
  }

  return settings;
};

wp.hooks.addFilter('blocks.registerBlockType', 'cloudinary/addAttributes', cldAddToggle);
/**
 * Get AMP Lightbox toggle control.
 *
 * @param {Object} props Props.
 *
 * @return {Component} Element.
 */

var TransformationsToggle = function TransformationsToggle(props) {
  var _props$attributes = props.attributes,
      overwrite_transformations = _props$attributes.overwrite_transformations,
      transformations = _props$attributes.transformations,
      setAttributes = props.setAttributes;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Transformations', 'cloudinary')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Overwrite Transformations', 'cloudinary'),
    checked: overwrite_transformations,
    onChange: function onChange(value) {
      setAttributes({
        overwrite_transformations: value
      });
    }
  }));
};

var ImageInspectorControls = function ImageInspectorControls(props) {
  var setAttributes = props.setAttributes,
      media = props.media;
  var InspectorControls = wp.editor.InspectorControls;

  if (media && media.transformations) {
    setAttributes({
      transformations: true
    });
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(TransformationsToggle, props));
};

ImageInspectorControls = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__["withSelect"])(function (select, ownProps) {
  return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0___default()({}, ownProps, {
    media: ownProps.attributes.id ? select('core').getMedia(ownProps.attributes.id) : null
  });
})(ImageInspectorControls);

var cldFilterBlocksEdit = function cldFilterBlocksEdit(BlockEdit) {
  return function (props) {
    var name = props.name;
    var shouldDisplayInspector = 'core/image' === name || 'core/video' === name;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, shouldDisplayInspector ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(ImageInspectorControls, props) : null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(BlockEdit, props));
  };
};

wp.hooks.addFilter('editor.BlockEdit', 'cloudinary/filterEdit', cldFilterBlocksEdit, 20);

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

module.exports = _defineProperty;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/objectSpread.js":
/*!*************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/objectSpread.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var defineProperty = __webpack_require__(/*! ./defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? Object(arguments[i]) : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      defineProperty(target, key, source[key]);
    });
  }

  return target;
}

module.exports = _objectSpread;

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/data":
/*!***************************************!*\
  !*** external {"this":["wp","data"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ })

/******/ });
//# sourceMappingURL=block-editor.js.map