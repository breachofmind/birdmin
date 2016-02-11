(function(birdmin, vex){

    vex.defaultOptions.className = 'vex-theme-plain vex-wide';

    var editors = {};

    var allowedTags = 	['a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'base',
        'bdi', 'bdo', 'blockquote', 'br', 'button', 'canvas', 'caption', 'cite', 'code', 'col',
        'colgroup', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'div', 'dl', 'dt', 'em',
        'embed', 'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'header', 'hgroup', 'hr', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'keygen', 'label', 'legend',
        'li', 'link', 'main', 'map', 'mark', 'menu', 'menuitem', 'meter', 'nav', 'noscript', 'object', 'ol',
        'optgroup', 'option', 'output', 'p', 'param', 'pre', 'progress', 'queue', 'rp', 'rt', 'ruby', 's', 'samp',
        'script', 'style', 'section', 'select', 'small', 'source', 'span', 'strike', 'strong', 'sub',
        'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title',
        'tr', 'track', 'u', 'ul', 'var', 'video', 'wbr','component'];

    var allowedEmptyTags = ['textarea', 'a', 'iframe', 'object', 'video', 'style', 'script', '.fa','component'];

    var dropzoneHandlers = {
        default: function(dz)
        {
            dz.on('success',function(file) {
                var response = JSON.parse(file.xhr.responseText);
                console.log(response);
            });
        },
        relate: function(dz,element)
        {
            var elementId = element.getAttribute('id');
            var template = Handlebars.compile( $("#"+elementId+"Template").html() );
            var list = $("#"+elementId+"List");

            dz.on('success', function(file) {
                var response = JSON.parse(file.xhr.responseText);

                for (var index in response) {
                    list.append(template(response[index]));
                }
            });
        }
    };



    var ui = new UserInterface;

    function UserInterface()
    {
        /**
         * Registers the instance and listeners.
         * @returns {birdmin.UI}
         */
        this.register = function()
        {
            editors = {};
            ui.getEditorElements().each(function() {
                ui.createEditor( $(this) );
            });

            return ui;
        };

        /**
         * Return the editor elements.
         * @returns {{}}
         */
        this.getEditors = function()
        {
            return editors;
        };

        /**
         * Returns elements on the page that should be HTML editors.
         * @returns {jQuery|HTMLElement}
         */
        this.getEditorElements = function()
        {
            return $('.html-editor');
        };

        /**
         * Register a new dropzone.
         * @param elementId string
         */
        this.createDropzone = function(elementId)
        {
            var element = document.getElementById(elementId);
            if (!element) {
                throw (elementId+" Dropzone does not exist");
            }
            var handler = dropzoneHandlers[element.getAttribute('data-handler')];
            var dz = new Dropzone("#"+elementId, {
                previewTemplate: document.getElementById('DropzonePreviewTemplate').innerHTML,
                createImageThumbnails: false
            });
            handler(dz,element);
        };

        /**
         * Create an HTML editor out of the given element.
         * @param $element
         * @returns {object}
         */
        this.createEditor = function($element)
        {
            editors[$element.attr('id')] = $element.froalaEditor({
                toolbarButtons: ['undo', 'redo' , '|', 'bold', 'italic', 'underline', 'strikethrough', 'clearFormatting','formatUL','formatOL','paragraphFormat','insertImage', 'html'],
                htmlAllowedTags: allowedTags,
                htmlAllowedEmptyTags: allowedEmptyTags
            });
            return this;
        };
    }

    function openAjaxDialog(event)
    {
        var $this = $(this);
        event.preventDefault();

        $.get($this.attr('href'), null, function(response) {
            vex.dialog.open({
                message:"Select",
                input:response
            });
        })
    }

    birdmin.init(function(){
        $('body').on('click','a[data-ajax-dialog]', openAjaxDialog);
    });

    ui.register();

    birdmin.ui = ui;

})(birdmin, vex);