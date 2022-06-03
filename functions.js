// ES5 scope wrapper or IIFE. note the $
;(function (win, doc, $, undefined) {
    'use strict';

})(this, document, jQuery);


function ucwords(obj) {
    obj.value = obj.value.charAt(0).toUpperCase() + obj.value.toLowerCase().slice(1);
}

function strtoupper(obj) {
    obj.value = obj.value.toUpperCase();
}


function toggleCss(file, index) {
    var oldFile = document.getElementsByTagName("link").item(index);
    var newFile = document.createElement("link");
    newFile.setAttribute("rel", "stylesheet");
    newFile.setAttribute("type", "text/css");
    newFile.setAttribute("href", file);

    document.getElementsByTagName("head").item(0).replaceChild(newFile, oldFile);
}


$('.searchTableServices').on('keyup', function() {
    searchTable($(this).val(), $(this).data('table'));
});
function searchTable(inputVal, table) {
    var allCells = $('#'+table+' tr').find('td');
    if(allCells.length > 0) {
        var regExp = new RegExp(inputVal, 'i');
        allCells.each(function(index, li) {
            if(regExp.test($(li).text())) {
                $(li).show();
            } else {
                $(li).hide();
            }
        });
    }
}
// plain AJAX

function flatten (list, result) {
  if(!Array.isArray(list)) return list;

  var i = -1;
  var len = list.length;

  result || (result = []);

  while (++i < len) {
    if (!Array.isArray(list[i])) {
      result.push(list[i]);
      continue;
    }

    flatten(list[i], result);
  }

  return result;
}


function postDataWithAjax(url, params) {
    // IE 5.5+ and every other browser
    var xhr = new(window.XMLHttpRequest || ActiveXObject)('MSXML2.XMLHTTP.3.0');

    xhr.open("POST", url, true);

    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    xhr.setRequestHeader("Accept", "application/json");
    xhr.onreadystatechange = function () {
        if (this.readyState === 4) {
            if (this.status >= 200 && this.status < 400) {
                console.log(JSON.parse(this.responseText));
            }
        }
    };
    xhr.send(params);
    xhr = null;
};

// event handler. works in all browsers
window.fakejQuery = document.querySelectorAll.bind(document)
eventHandler: function (element, eventType, fn) {
    if (document.addEventListener) {
        for (var i = fakejQuery(element).length - 1; i >= 0; i--) {
            fakejQuery(element)[i].addEventListener(eventType, fn, false)
        }
    } else if (document.attachEvent) {
        for (var i = fakejQuery(element).length - 1; i >= 0; i--) {
            fakejQuery(element)[i].attachEvent("on" + eventType, fn)
        }
    } else {
        alert("Ooops, no event listener methods found!");
    }
},

//convert hex to rgb
function hexToRGB(hex) {
    // Expand shorthand form ("#FFF") to full form ("#FFFFF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });
    // return hex values
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

// append
var element = document.createElement('p');
element.appendChild(document.createTextNode('Text'));
document.querySelector('.item').appendChild(element);

function addNote() {
    var newFields = document.getElementById('noteFields');
    var field = '<tr><td width="110" height="20" align="left"><strong>New note: </strong></td><td height="20"><input  style="width: 100%" type="text" size="30" name="notes[]"></td><td  style="padding: 0 0 0 10px;"  width="90" height="20"><button  style="width: 100%" type="button" name="remove" onclick="javascript: removeNoteField(this)">Remove</button></td></tr>';

    newFields.insertAdjacentHTML('beforeend', field);
}

function removeNoteField(r) {
    var i = r.parentNode.parentNode.rowIndex;
    document.getElementById("noteFields").deleteRow(i);
}

function deleteNote(r) {
    var noteId = r.getAttribute('data-id');
    $.post("?ajax=1&what=deleteNote", "&noteId="+noteId, function(data){
        if(data.error == 1) {
            alert(data.error_msg);
        } else {
            console.log(data);
        }
    }, "json");

    removeNoteField(r);
}

/**
 * Create DOM nodes with text, class and appends them to elementAppend
 */
showMessages: function (text, elementCreate, elementAppend, className) {
    var el = doc.createElement(elementCreate);
    el.className += className;
    el.innerHTML = text;

    $(elementAppend).append(el).slideDown(1000, function () {
        setTimeout(function() {
            $(elementCreate).slideUp(1000, function () {
                $(this).fadeOut("slow", function () {
                    $(this).remove();
                 });
            });
        }, 6000);
    });
},

/**
 * Create SEO captions/URLs for menus and content.
 */
var fixSEOCaption = function (caption) {
    return caption.toLowerCase().replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g,"").replace(/\s+/g, "-");
};

/**
 * AJAX search form.
 * TODO: make the for loop more flexible so it can work with all kind of data
 * TODO: performance improve
 */
var $urlSplit = win.location.href.toString().split(win.location.host)[1].split("/");
$(".ajax-search").on("keyup", function () {
    var $search = $(".ajax-search").val();
    if ($.trim($search).length > 2) {
        $.ajax({
            type: "GET",
            url: "/admin/" + $urlSplit[2] + "/search",
            data: {"ajaxsearch": $search},
            dataType: "json",
            contentType: "application/json; charset=utf-8;",
            cache: !1,
        }).done(function (result, request, headers) {
            if (request === "success") {
                $("#results").empty();
                $.each(result.ajaxsearch, function (key, value) {
                    var $ul = $("<ul class='table-row'>");
                    var $val = $.parseJSON(value);
                    for (var property in $val) {
                        if ($val.hasOwnProperty(property)) {
                            if ($val[property]) {
                                $ul.append("<li data-id ='"+$val["_id"]+"' class='table-cell'>"+$val[property]+"</li>");
                            }
                        }
                    }
                    $("#results").append($ul);
                });
            }
        }).fail(function (error) {
            console.log("Error:", error.responseText); //TODO must create a dialog popup
        });
        $("#results").show();
        $("#linked").hide();
    } else {
        $("#results").hide();
        $("#linked").show();
    }
});

function hasClass(element, className) {
    if (element.classList) {
        return element.classList.contains(className);
    } else {
        return new RegExp('(^| )' + className + '( |$)', 'gi').test(element.className);
    }
}

function addClass(element, className) {
    if (!hasClass(element, className)) {
        if (element.classList) {
          element.classList.add(className);
        }
        else {
          element.className += ' ' + className;
        }
    }
}

function removeClass(element, className) {
    if (element.classList) {
        return element.classList.remove(className);
    } else {
        return element.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    }
}


function toggleClass(element, className) {
    if (hasClass(element, className)) {
        removeClass(element, className);
    } else {
        addClass(element, className);
    }
}

// ready()
if (document.readyState === 'complete') {
    setTimeout(function(){});
} else if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", function(){}, false);
    document.addEventListener("load", function(){}, false);
} else {
    document.attachEvent('onreadystatechange', function(){});
    window.attachEvent("onload", function(){});
}

//Get a random item from an array
var items = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", 'foo', 'bar', 'baz'];
var  randomItem = items[Math.floor(Math.random() * items.length)];


// rand number
function getRandomNumber(min, max){
    return Math.floor(Math.random() * (max - min)) + min;
}

getRandomNumber(15, 20);


// clear html
function escapeHTML(text) {
    var replacements= {"<": "&lt;", ">": "&gt;","&": "&amp;", """: "&quot;"};
    return text.replace(/[<>&"]/g, function(character) {
        return replacements[character];
    });
}

//Returning a new object with selected properties
Object.prototype.pick = function(arr) {
    var obj = {};
    arr.forEach(function(key){
        obj[key] = this[key];
    });

    return obj;
};

var objA = {"name": "colin", "car": "suzuki", "age": 17};

var objB = objA.pick(['car', 'age']);
// {"car": "suzuki", "age": 17}


function getObjectIndexFromId(obj, id) {
    if (typeof obj == "object") {
        for (var i = 0; i < obj.length; i++) {
            if (typeof obj[i] != "undefined" && typeof obj[i].id != "undefined" && obj[i].id == id) {
                return i;
            }
        }
    }
    return false;
}

for (var i = Things.length - 1; i >= 0; i--) {
    Things[i]
};

function fadeIn(el) {
  var opacity = 0;

  el.style.opacity = 0;
  el.style.filter = '';

  var last = +new Date();
  var tick = function() {
    opacity += (new Date() - last) / 400;
    el.style.opacity = opacity;
    el.style.filter = 'alpha(opacity=' + (100 * opacity)|0 + ')';

    last = +new Date();

    if (opacity < 1) {
      (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
    }
  };

  tick();
}

//Pretty-print JSON

JSON.stringify({a: "lorem", b: "ipsum"}, null, 4);
JSON.stringify({a: "lorem", b: "ipsum"}, null, '\t');

var quicksort = function(arr) {
    if(arr.length <= 1) {
        return arr;
    }
    var swapPos = Math.floor((arr.length - 1) / 2);
    var swapValue = arr[swapPos], less = [], more = [];
    arr = arr.slice(0, swapPos).concat(arr.slice(swapPos + 1));
    for (var i = arr.length - 1; i >= 0; i--) {
        if (arr[i] < swapValue) {
            less.push(arr[i]);
        } else {
            more.push(arr[i]);
        }
    };
    return (quicksort(less)).concat([swapValue], quicksort(more));
};

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes === 0) {
            return 'n/a';
        }
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)),10);
        if (i === 0) {
            return bytes + ' ' + sizes[i];
        }
    return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}
// from fb
function m() {
    var res = {};
    for (var i = 0; i < arguments.length; i++) {
        if (arguments[i]) {
            Object.assign(res, arguments[i]);
        }
    }
    return res;
}

// http://rosskendall.com/files/rfc822validemail.js.txt
function isRFC822ValidEmail(sEmail) {

  var sQtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
  var sDtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
  var sAtom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
  var sQuotedPair = '\\x5c[\\x00-\\x7f]';
  var sDomainLiteral = '\\x5b(' + sDtext + '|' + sQuotedPair + ')*\\x5d';
  var sQuotedString = '\\x22(' + sQtext + '|' + sQuotedPair + ')*\\x22';
  var sDomain_ref = sAtom;
  var sSubDomain = '(' + sDomain_ref + '|' + sDomainLiteral + ')';
  var sWord = '(' + sAtom + '|' + sQuotedString + ')';
  var sDomain = sSubDomain + '(\\x2e' + sSubDomain + ')*';
  var sLocalPart = sWord + '(\\x2e' + sWord + ')*';
  var sAddrSpec = sLocalPart + '\\x40' + sDomain; // complete RFC822 email address spec
  var sValidEmail = '^' + sAddrSpec + '$'; // as whole string

  var reValidEmail = new RegExp(sValidEmail);

  if (reValidEmail.test(sEmail)) {
    return true;
  }

  return false;
}
// The codes below are from Web Tools Weekly

// When an event is triggered on an element, two properties help to identify the element: the target property and the currentTarget property. Assuming we have a .parent element with a .child element inside it, look at the following code:

var parent = document.querySelector('.parent');

parent.addEventListener('click',function(e) {
  console.log(e.target.className);
  console.log(e.currentTarget.className);
}, false);


var listArray = Array.prototype.slice.call(list);
console.log(Array.isArray(listArray)); // true

function toArray(obj) {
  var array = ;
  for (var i = obj.length >>> 0; i--;) {
    array[i] = obj[i];
  }
  return array;
}


var listArray2 = toArray(list);
console.log(Array.isArray(listArray2)); // true
