window.Parsley.addMessages('en', {
    dimensions: 'The display picture dimensions should be a minimum of 100px by 100px'
});

var app = {};

app.utils = {};

app.utils.formDataSuppoerted = (function () {
    return !!('FormData' in window);
}());

window.Parsley.addValidator('filemimetypes', {
    requirementType: 'string',
    validateString: function (value, requirement, parsleyInstance) {

        if (!app.utils.formDataSuppoerted) {
            return true;
        }

        var file = parsleyInstance.$element[0].files;

        if (file.length == 0) {
            return true;
        }

        var allowedMimeTypes = requirement.replace(/\s/g, "").split(',');
        return allowedMimeTypes.indexOf(file[0].type) !== -1;

    },
    messages: {
        en: 'This file is not allowed'
    }
});