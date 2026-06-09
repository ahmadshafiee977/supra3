/*!
 * Flash export buttons for Buttons and DataTables.
 * 2015 SpryMedia Ltd - datatables.net/license
 *
 * ZeroClipbaord - MIT license
 * Copyright (c) 2012 Joseph Huckaby
 */

(function (factory) {
  if (typeof define === "function" && define.amd) {
    // AMD
    define(["jquery", "datatables.net", "datatables.net-buttons"], function (
      $
    ) {
      return factory($, window, document);
    });
  } else if (typeof exports === "object") {
    // CommonJS
    module.exports = function (root, $) {
      if (!root) {
        root = window;
      }

      if (!$ || !$.fn.dataTable) {
        $ = require("datatables.net")(root, $).$;
      }

      if (!$.fn.dataTable.Buttons) {
        require("datatables.net-buttons")(root, $);
      }

      return factory($, root, root.document);
    };
  } else {
    // Browser
    factory(jQuery, window, document);
  }
})(function ($, window, document, undefined) {
  "use strict";
  var DataTable = $.fn.dataTable;

  /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
   * ZeroClipboard dependency
   */

  /*
   * ZeroClipboard 1.0.4 with modifications
   * Author: Joseph Huckaby
   * License: MIT
   *
   * Copyright (c) 2012 Joseph Huckaby
   */

  /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
   * Local (private) functions
   */

  /**
   * If a Buttons instance is initlaised before it is placed into the DOM, Flash
   * won't be able to bind to it, so we need to wait until it is available, this
   * method abstracts that out.
   *
   * @param {ZeroClipboard} flash ZeroClipboard instance
   * @param {jQuery} node  Button
   */
  var _glue = function (flash, node) {
    var id = node.attr("id");

    if (node.parents("html").length) {
      flash.glue(node[0], "");
    } else {
      setTimeout(function () {
        _glue(flash, node);
      }, 500);
    }
  };

  /**
   * Get the file name for an exported file.
   *
   * @param {object}  config       Button configuration
   * @param {boolean} incExtension Include the file name extension
   */
  var _filename = function (config, incExtension) {
    // Backwards compatibility
    var filename =
      config.filename === "*" &&
      config.title !== "*" &&
      config.title !== undefined
        ? config.title
        : config.filename;

    if (typeof filename === "function") {
      filename = filename();
    }

    if (filename.indexOf("*") !== -1) {
      filename = $.trim(filename.replace("*", $("title").text()));
    }

    // Strip characters which the OS will object to
    filename = filename.replace(/[^a-zA-Z0-9_\u00A1-\uFFFF\.,\-_ !\(\)]/g, "");

    return incExtension === undefined || incExtension === true
      ? filename + config.extension
      : filename;
  };

  /**
   * Get the sheet name for Excel exports.
   *
   * @param {object}  config       Button configuration
   */
  var _sheetname = function (config) {
    var sheetName = "Sheet1";

    if (config.sheetName) {
      sheetName = config.sheetName.replace(/[\[\]\*\/\\\?\:]/g, "");
    }

    return sheetName;
  };

  /**
   * Get the title for an exported file.
   *
   * @param {object}  config  Button configuration
   */
  var _title = function (config) {
    var title = config.title;

    if (typeof title === "function") {
      title = title();
    }

    return title.indexOf("*") !== -1
      ? title.replace("*", $("title").text() || "Exported data")
      : title;
  };

  /**
   * Set the flash text. This has to be broken up into chunks as the Javascript /
   * Flash bridge has a size limit. There is no indication in the Flash
   * documentation what this is, and it probably depends upon the browser.
   * Experimentation shows that the point is around 50k when data starts to get
   * lost, so an 8K limit used here is safe.
   *
   * @param {ZeroClipboard} flash ZeroClipboard instance
   * @param {string}        data  Data to send to Flash
   */
  var _setText = function (flash, data) {
    var parts = data.match(/[\s\S]{1,8192}/g) || [];

    flash.clearText();
    for (var i = 0, len = parts.length; i < len; i++) {
      flash.appendText(parts[i]);
    }
  };

  /**
   * Get the newline character(s)
   *
   * @param {object}  config Button configuration
   * @return {string}        Newline character
   */
  var _newLine = function (config) {
    return config.newline
      ? config.newline
      : navigator.userAgent.match(/Windows/)
      ? "\r\n"
      : "\n";
  };

  /**
   * Combine the data from the `buttons.exportData` method into a string that
   * will be used in the export file.
   *
   * @param  {DataTable.Api} dt     DataTables API instance
   * @param  {object}        config Button configuration
   * @return {object}               The data to export
   */
  var _exportData = function (dt, config) {
    var newLine = _newLine(config);
    var data = dt.buttons.exportData(config.exportOptions);
    var boundary = config.fieldBoundary;
    var separator = config.fieldSeparator;
    var reBoundary = new RegExp(boundary, "g");
    var escapeChar = config.escapeChar !== undefined ? config.escapeChar : "\\";
    var join = function (a) {
      var s = "";

      // If there is a field boundary, then we might need to escape it in
      // the source data
      for (var i = 0, ien = a.length; i < ien; i++) {
        if (i > 0) {
          s += separator;
        }

        s += boundary
          ? boundary +
            ("" + a[i]).replace(reBoundary, escapeChar + boundary) +
            boundary
          : a[i];
      }

      return s;
    };

    var header = config.header ? join(data.header) + newLine : "";
    var footer =
      config.footer && data.footer ? newLine + join(data.footer) : "";
    var body = [];

    for (var i = 0, ien = data.body.length; i < ien; i++) {
      body.push(join(data.body[i]));
    }

    return {
      str: header + body.join(newLine) + footer,
      rows: body.length,
    };
  };

  // Basic initialisation for the buttons is common between them
  var flashButton = {
    available: function () {
      return ZeroClipboard_TableTools.hasFlash();
    },

    init: function (dt, button, config) {
      // Insert the Flash movie
      ZeroClipboard_TableTools.moviePath = DataTable.Buttons.swfPath;
      var flash = new ZeroClipboard_TableTools.Client();

      flash.setHandCursor(true);
      flash.addEventListener("mouseDown", function (client) {
        config._fromFlash = true;
        dt.button(button[0]).trigger();
        config._fromFlash = false;
      });

      _glue(flash, button);

      config._flash = flash;
    },

    destroy: function (dt, button, config) {
      config._flash.destroy();
    },

    fieldSeparator: ",",

    fieldBoundary: '"',

    exportOptions: {},

    title: "*",

    filename: "*",

    extension: ".csv",

    header: true,

    footer: false,
  };

  /**
   * Convert from numeric position to letter for column names in Excel
   * @param  {int} n Column number
   * @return {string} Column letter(s) name
   */
  function createCellPos(n) {
    var ordA = "A".charCodeAt(0);
    var ordZ = "Z".charCodeAt(0);
    var len = ordZ - ordA + 1;
    var s = "";

    while (n >= 0) {
      s = String.fromCharCode((n % len) + ordA) + s;
      n = Math.floor(n / len) - 1;
    }

    return s;
  }

  /**
   * Create an XML node and add any children, attributes, etc without needing to
   * be verbose in the DOM.
   *
   * @param  {object} doc      XML document
   * @param  {string} nodeName Node name
   * @param  {object} opts     Options - can be `attr` (attributes), `children`
   *   (child nodes) and `text` (text content)
   * @return {node}            Created node
   */
  function _createNode(doc, nodeName, opts) {
    var tempNode = doc.createElement(nodeName);

    if (opts) {
      if (opts.attr) {
        $(tempNode).attr(opts.attr);
      }

      if (opts.children) {
        $.each(opts.children, function (key, value) {
          tempNode.appendChild(value);
        });
      }

      if (opts.text) {
        tempNode.appendChild(doc.createTextNode(opts.text));
      }
    }

    return tempNode;
  }

  /**
   * Get the width for an Excel column based on the contents of that column
   * @param  {object} data Data for export
   * @param  {int}    col  Column index
   * @return {int}         Column width
   */
  function _excelColWidth(data, col) {
    var max = data.header[col].length;
    var len;

    if (data.footer && data.footer[col].length > max) {
      max = data.footer[col].length;
    }

    for (var i = 0, ien = data.body.length; i < ien; i++) {
      len = data.body[i][col].toString().length;

      if (len > max) {
        max = len;
      }

      // Max width rather than having potentially massive column widths
      if (max > 40) {
        break;
      }
    }

    // And a min width
    return max > 5 ? max : 5;
  }

  try {
    var _serialiser = new XMLSerializer();
    var _ieExcel;
  } catch (t) {}

  /**
   * Convert XML documents in an object to strings
   * @param  {object} obj XLSX document object
   */
  function _xlsxToStrings(obj) {
    if (_ieExcel === undefined) {
      // Detect if we are dealing with IE's _awful_ serialiser by seeing if it
      // drop attributes
      _ieExcel =
        _serialiser
          .serializeToString(
            $.parseXML(excelStrings["xl/worksheets/sheet1.xml"])
          )
          .indexOf("xmlns:r") === -1;
    }

    $.each(obj, function (name, val) {
      if ($.isPlainObject(val)) {
        _xlsxToStrings(val);
      } else {
        if (_ieExcel) {
          // IE's XML serialiser will drop some name space attributes from
          // from the root node, so we need to save them. Do this by
          // replacing the namespace nodes with a regular attribute that
          // we convert back when serialised. Edge does not have this
          // issue
          var worksheet = val.childNodes[0];
          var i, ien;
          var attrs = [];

          for (i = worksheet.attributes.length - 1; i >= 0; i--) {
            var attrName = worksheet.attributes[i].nodeName;
            var attrValue = worksheet.attributes[i].nodeValue;

            if (attrName.indexOf(":") !== -1) {
              attrs.push({ name: attrName, value: attrValue });

              worksheet.removeAttribute(attrName);
            }
          }

          for (i = 0, ien = attrs.length; i < ien; i++) {
            var attr = val.createAttribute(
              attrs[i].name.replace(":", "_dt_b_namespace_token_")
            );
            attr.value = attrs[i].value;
            worksheet.setAttributeNode(attr);
          }
        }

        var str = _serialiser.serializeToString(val);

        // Fix IE's XML
        if (_ieExcel) {
          // IE doesn't include the XML declaration
          if (str.indexOf("<?xml") === -1) {
            str =
              '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' + str;
          }

          // Return namespace attributes to being as such
          str = str.replace(/_dt_b_namespace_token_/g, ":");
        }

        // Both IE and Edge will put empty name space attributes onto the
        // rows and columns making them useless
        str = str
          .replace(/<row xmlns="" /g, "<row ")
          .replace(/<cols xmlns="">/g, "<cols>");

        obj[name] = str;
      }
    });
  }

  // Excel - Pre-defined strings to build a basic XLSX file
  var excelStrings = {
    "_rels/.rels":
      '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' +
      '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' +
      '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' +
      "</Relationships>",

    "xl/_rels/workbook.xml.rels":
      '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' +
      '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' +
      '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' +
      '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' +
      "</Relationships>",

    "[Content_Types].xml":
      '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' +
      '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' +
      '<Default Extension="xml" ContentType="application/xml" />' +
      '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml" />' +
      '<Default Extension="jpeg" ContentType="image/jpeg" />' +
      '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" />' +
      '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" />' +
      '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" />' +
      "</Types>",

    "xl/workbook.xml":
      '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' +
      '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' +
      '<fileVersion appName="xl" lastEdited="5" lowestEdited="5" rupBuild="24816"/>' +
      '<workbookPr showInkAnnotation="0" autoCompressPictures="0"/>' +
      "<bookViews>" +
      '<workbookView xWindow="0" yWindow="0" windowWidth="25600" windowHeight="19020" tabRatio="500"/>' +
      "</bookViews>" +
      "<sheets>" +
      '<sheet name="" sheetId="1" r:id="rId1"/>' +
      "</sheets>" +
      "</workbook>",

    "xl/worksheets/sheet1.xml":
      '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' +
      '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" mc:Ignorable="x14ac" xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac">' +
      "<sheetData/>" +
      "</worksheet>",

    "xl/styles.xml":
      '<?xml version="1.0" encoding="UTF-8"?>' +
      '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" mc:Ignorable="x14ac" xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac">' +
      '<fonts count="5" x14ac:knownFonts="1">' +
      "<font>" +
      '<sz val="11" />' +
      '<name val="Calibri" />' +
      "</font>" +
      "<font>" +
      '<sz val="11" />' +
      '<name val="Calibri" />' +
      '<color rgb="FFFFFFFF" />' +
      "</font>" +
      "<font>" +
      '<sz val="11" />' +
      '<name val="Calibri" />' +
      "<b />" +
      "</font>" +
      "<font>" +
      '<sz val="11" />' +
      '<name val="Calibri" />' +
      "<i />" +
      "</font>" +
      "<font>" +
      '<sz val="11" />' +
      '<name val="Calibri" />' +
      "<u />" +
      "</font>" +
      "</fonts>" +
      '<fills count="6">' +
      "<fill>" +
      '<patternFill patternType="none" />' +
      "</fill>" +
      "<fill/>" + // Excel appears to use this as a dotted background regardless of values
      "<fill>" +
      '<patternFill patternType="solid">' +
      '<fgColor rgb="FFD9D9D9" />' +
      '<bgColor indexed="64" />' +
      "</patternFill>" +
      "</fill>" +
      "<fill>" +
      '<patternFill patternType="solid">' +
      '<fgColor rgb="FFD99795" />' +
      '<bgColor indexed="64" />' +
      "</patternFill>" +
      "</fill>" +
      "<fill>" +
      '<patternFill patternType="solid">' +
      '<fgColor rgb="ffc6efce" />' +
      '<bgColor indexed="64" />' +
      "</patternFill>" +
      "</fill>" +
      "<fill>" +
      '<patternFill patternType="solid">' +
      '<fgColor rgb="ffc6cfef" />' +
      '<bgColor indexed="64" />' +
      "</patternFill>" +
      "</fill>" +
      "</fills>" +
      '<borders count="2">' +
      "<border>" +
      "<left />" +
      "<right />" +
      "<top />" +
      "<bottom />" +
      "<diagonal />" +
      "</border>" +
      '<border diagonalUp="false" diagonalDown="false">' +
      '<left style="thin">' +
      '<color auto="1" />' +
      "</left>" +
      '<right style="thin">' +
      '<color auto="1" />' +
      "</right>" +
      '<top style="thin">' +
      '<color auto="1" />' +
      "</top>" +
      '<bottom style="thin">' +
      '<color auto="1" />' +
      "</bottom>" +
      "<diagonal />" +
      "</border>" +
      "</borders>" +
      '<cellStyleXfs count="1">' +
      '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" />' +
      "</cellStyleXfs>" +
      '<cellXfs count="2">' +
      '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="0" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="2" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="2" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="2" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="2" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="4" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="5" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="5" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="5" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="5" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="5" borderId="0" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="0" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="0" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="0" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="0" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="3" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="3" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="3" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="3" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="4" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="4" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="4" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="4" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="4" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="0" fillId="5" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="1" fillId="5" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="2" fillId="5" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="3" fillId="5" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      '<xf numFmtId="0" fontId="4" fillId="5" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' +
      "</cellXfs>" +
      '<cellStyles count="1">' +
      '<cellStyle name="Normal" xfId="0" builtinId="0" />' +
      "</cellStyles>" +
      '<dxfs count="0" />' +
      '<tableStyles count="0" defaultTableStyle="TableStyleMedium9" defaultPivotStyle="PivotStyleMedium4" />' +
      "</styleSheet>",
  };
  // Note we could use 3 `for` loops for the styles, but when gzipped there is
  // virtually no difference in size, since the above can be easily compressed

  /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
   * DataTables options and methods
   */

  // Set the default SWF path
  DataTable.Buttons.swfPath =
    "//cdn.datatables.net/buttons/1.2.0/swf/flashExport.swf";

  // Method to allow Flash buttons to be resized when made visible - as they are
  // of zero height and width if initialised hidden
  DataTable.Api.register("buttons.resize()", function () {
    $.each(ZeroClipboard_TableTools.clients, function (i, client) {
      if (client.domElement !== undefined && client.domElement.parentNode) {
        client.positionElement();
      }
    });
  });

  /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
   * Button definitions
   */

  // Excel save file - this is really a CSV file using UTF-8 that Excel can read
  DataTable.ext.buttons.excelFlash = $.extend({}, flashButton, {
    className: "buttons-excel buttons-flash",

    text: function (dt) {
      return dt.i18n("buttons.excel", "Excel");
    },

    action: function (e, dt, button, config) {
      var flash = config._flash;
      var rowPos = 0;
      var rels = $.parseXML(excelStrings["xl/worksheets/sheet1.xml"]); //Parses xml
      var relsGet = rels.getElementsByTagName("sheetData")[0];

      var xlsx = {
        _rels: {
          ".rels": $.parseXML(excelStrings["_rels/.rels"]),
        },
        xl: {
          _rels: {
            "workbook.xml.rels": $.parseXML(
              excelStrings["xl/_rels/workbook.xml.rels"]
            ),
          },
          "workbook.xml": $.parseXML(excelStrings["xl/workbook.xml"]),
          "styles.xml": $.parseXML(excelStrings["xl/styles.xml"]),
          worksheets: {
            "sheet1.xml": rels,
          },
        },
        "[Content_Types].xml": $.parseXML(excelStrings["[Content_Types].xml"]),
      };

      var data = dt.buttons.exportData(config.exportOptions);
      var currentRow, rowNode;
      var addRow = function (row) {
        currentRow = rowPos + 1;
        rowNode = _createNode(rels, "row", { attr: { r: currentRow } });

        for (var i = 0, ien = row.length; i < ien; i++) {
          // Concat both the Cell Columns as a letter and the Row of the cell.
          var cellId = createCellPos(i) + "" + currentRow;
          var cell;

          if (row[i] === null || row[i] === undefined) {
            row[i] = "";
          }

          // Detect numbers - don't match numbers with leading zeros or a negative
          // anywhere but the start
          if (
            typeof row[i] === "number" ||
            (row[i].match &&
              $.trim(row[i]).match(/^-?\d+(\.\d+)?$/) &&
              !$.trim(row[i]).match(/^0\d+/))
          ) {
            cell = _createNode(rels, "c", {
              attr: {
                t: "n",
                r: cellId,
              },
              children: [_createNode(rels, "v", { text: row[i] })],
            });
          } else {
            // Replace non standard characters for text output
            var text = !row[i].replace
              ? row[i]
              : row[i]
                  .replace(/&(?!amp;)/g, "&amp;")
                  .replace(/</g, "&lt;")
                  .replace(/>/g, "&gt;")
                  .replace(/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F-\x9F]/g, "");

            cell = _createNode(rels, "c", {
              attr: {
                t: "inlineStr",
                r: cellId,
              },
              children: {
                row: _createNode(rels, "is", {
                  children: {
                    row: _createNode(rels, "t", {
                      text: text,
                    }),
                  },
                }),
              },
            });
          }

          rowNode.appendChild(cell);
        }
        relsGet.appendChild(rowNode);
        rowPos++;
      };

      $("sheets sheet", xlsx.xl["workbook.xml"]).attr(
        "name",
        _sheetname(config)
      );

      if (config.customizeData) {
        config.customizeData(data);
      }

      if (config.header) {
        addRow(data.header, rowPos);
        $("row c", rels).attr("s", "2"); // bold
      }

      for (var n = 0, ie = data.body.length; n < ie; n++) {
        addRow(data.body[n], rowPos);
      }

      if (config.footer && data.footer) {
        addRow(data.footer, rowPos);
        $("row:last c", rels).attr("s", "2"); // bold
      }

      // Set column widths
      var cols = _createNode(rels, "cols");
      $("worksheet", rels).prepend(cols);

      for (var i = 0, ien = data.header.length; i < ien; i++) {
        cols.appendChild(
          _createNode(rels, "col", {
            attr: {
              min: i + 1,
              max: i + 1,
              width: _excelColWidth(data, i),
              customWidth: 1,
            },
          })
        );
      }

      // Let the developer customise the document if they want to
      if (config.customize) {
        config.customize(xlsx);
      }

      _xlsxToStrings(xlsx);

      flash.setAction("excel");
      flash.setFileName(_filename(config));
      flash.setSheetData(xlsx);
      _setText(flash, "");
    },

    extension: ".xlsx",
  });

  // PDF export
  DataTable.ext.buttons.pdfFlash = $.extend({}, flashButton, {
    className: "buttons-pdf buttons-flash",

    text: function (dt) {
      return dt.i18n("buttons.pdf", "PDF");
    },

    action: function (e, dt, button, config) {
      // Set the text
      var flash = config._flash;
      var data = dt.buttons.exportData(config.exportOptions);
      var totalWidth = dt.table().node().offsetWidth;

      // Calculate the column width ratios for layout of the table in the PDF
      var ratios = dt
        .columns(config.columns)
        .indexes()
        .map(function (idx) {
          return dt.column(idx).header().offsetWidth / totalWidth;
        });

      flash.setAction("pdf");
      flash.setFileName(_filename(config));

      _setText(
        flash,
        JSON.stringify({
          title: _filename(config, false),
          message: config.message,
          colWidth: ratios.toArray(),
          orientation: config.orientation,
          size: config.pageSize,
          header: config.header ? data.header : null,
          footer: config.footer ? data.footer : null,
          body: data.body,
        })
      );
    },

    extension: ".pdf",

    orientation: "portrait",

    pageSize: "A4",

    message: "",

    newline: "\n",
  });

  return DataTable.Buttons;
});
