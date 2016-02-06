(function(birdmin){

    var editors = {};
    var ui = new UserInterface;

    function UserInterface()
    {
        /**
         * Registers the instance and listeners.
         * @returns {birdmin.UI}
         */
        this.register = function()
        {
            ui.getEditorElements().each(function() {
                ui.createEditor( $(this) );
            });
            if (document.getElementById('MediaDropzone')) {
                Dropzone.options.MediaDropzone = {
                    createImageThumbnails: false,
                    previewTemplate: document.getElementById('MediaDropzoneTemplate').innerHTML
                };
            }

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
         * Create an HTML editor out of the given element.
         * @param $element
         * @returns {object}
         */
        this.createEditor = function($element)
        {
            editors[$element.attr('id')] = $element;
            return this;
        };
    }

    ui.register();

    birdmin.ui = ui;

})(birdmin);