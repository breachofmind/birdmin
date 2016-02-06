$.fn.extend({
    /**
     * Get/set the given class name.
     * @param className string
     * @param bool on/off
     * @returns {jquery}
     */
    switchClass: function(className, bool) {
        if (typeof bool == 'undefined') {
            return this.hasClass(className);
        }
        this[bool ? 'addClass' : 'removeClass'] (className);
        return this;
    },

    /**
     * Gets/sets the 'processing' class.
     * @param bool on/off
     * @returns {*|jquery}
     */
    processing: function(bool) {
        return this.switchClass('processing',bool);
    },

    /**
     * Gets/sets the 'loading' class.
     * @param bool on/off
     * @returns {*|jquery}
     */
    loading: function(bool) {
        return this.switchClass('loading', bool);
    },

    /**
     * Serializes a form into key value pairs.
     * @returns {{}}
     */
    serializeObject: function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }
});

/**
 * Enhance the trim() method to include a character mask.
 * @param chr string
 */
String.prototype.trimMask = function (chr) {
    if (typeof chr=="undefined") {
        return this.trim();
    }

    return this.replace(new RegExp("^["+chr+"]+|["+chr+"]+$","g"), "");
};

/**
 * Convert a string to a url slug, replacing spaces with dashes and lowercase.
 * @returns {string}
 */
String.prototype.toSlug = function() {
    return this
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-');
};

function str_slug (string) {
    if (!string || string=="") {
        return string;
    }
    return string.toSlug();
}

/**
 * Keycode constants.
 * @type {{37: string, 39: string, 38: string, 40: string, 13: string}}
 */
var keycodes = {
    37:"LEFT", 39:"RIGHT", 38:"UP", 40:"DOWN", 13:"ENTER"
};