const TITLE_LIMIT = 70;
const DESCRIPTION_LIMIT = 160;
const seoAdvancedPanel = document.getElementById('seo-advanced-panel');
const seoGooglePreview = document.getElementById('google-preview');
const seoTwitterPreview = document.getElementById('twitter-preview');
const seoFacebookPreview = document.getElementById('facebook-preview');

/**
 * Load a CSS script dynamically
 * @param {string} url
 */
const loadCss = function (url) {
    const link = document.createElement('link');
    link.href = url;
    link.type = 'text/css';
    link.rel = 'stylesheet';

    document.getElementsByTagName('head')[0].appendChild(link);
};

/**
 * Update data for Google preview widget
 * @param {Object} form
 */
const updateGooglePreview = function (form) {
    if (!seoGooglePreview) {
        return;
    }

    seoGooglePreview.querySelector('.preview-title').innerHTML = form.title;
    seoGooglePreview.querySelector('.preview-description').innerHTML = form.description;
};

/**
 * Update data for Twitter preview widget
 * @param {Object} form
 */
const updateTwitterPreview = function (form) {
    if (!seoTwitterPreview) {
        return;
    }

    seoTwitterPreview.querySelector('.preview-title').innerHTML = form.ogTitle;
    seoTwitterPreview.querySelector('.preview-description').innerHTML = form.ogDescription;
    seoTwitterPreview.querySelector('.preview-url').innerHTML = '<i class="linkify icon"></i>' + form.ogUrl;

    if (form.ogImage) {
        seoTwitterPreview.querySelector('.preview-image').src = form.ogImage;
    } else {
        seoTwitterPreview.querySelector('.preview-image').src = '/bundles/dedisyliusseoplugin/images/twitter-placeholder.png';
    }
};

/**
 * Update data for Facebook preview widget
 * @param {Object} form
 */
const updateFacebookPreview = function (form) {
    if (!seoFacebookPreview) {
        return;
    }

    seoFacebookPreview.querySelector('.preview-title').innerHTML = form.ogTitle;
    seoFacebookPreview.querySelector('.preview-description').innerHTML = form.ogDescription;
    seoFacebookPreview.querySelector('.preview-url').innerHTML = form.ogUrl;

    if (form.ogImage) {
        seoFacebookPreview.querySelector('.preview-image').src = form.ogImage;
    } else {
        seoFacebookPreview.querySelector('.preview-image').src = '/bundles/dedisyliusseoplugin/images/facebook-placeholder.png';
    }
};

/**
 * @param {Object} form
 */
const updatePreviewWidgets = function (form) {
    updateGooglePreview(form);
    updateTwitterPreview(form);
    updateFacebookPreview(form);
};

/**
 * Add or update counter element below HTML element
 * @param {HTMLElement} el
 * @param {number} count
 * @param {number} max
 */
const setCounter = function (el, count, max) {
    let counterContainer = el.querySelector('.counter');
    if (!counterContainer) {
        counterContainer = document.createElement('div');
        counterContainer.className = 'counter';
        el.appendChild(counterContainer);
    }

    counterContainer.classList.remove('danger');
    if (count > max) {
        counterContainer.classList.add('danger');
    }

    counterContainer.innerHTML = count + ' / ' + max;
};

if (seoAdvancedPanel) {
    /**
     * It's current form data for SEO content
     */
    const form = {
        /**
         * @returns {HTMLElement|null}
         */
        get defaultData() {
            const parent = seoAdvancedPanel.querySelector('.seo-advanced-panel-content');

            return parent ? parent.querySelector('.preview-default-data') : null;
        },

        /**
         * Return default value for metadataTitle
         * @returns {string}
         * @private
         */
        get _title() {
            return this.defaultData.querySelector('.title').textContent;
        },

        /**
         * Return default value for metadataDescription
         * @returns {string}
         * @private
         */
        get _description() {
            return this.defaultData.querySelector('.description').textContent;
        },

        /**
         * Return default value for openGraphMetadataTitle
         * @returns {string}
         * @private
         */
        get _ogTitle() {
            return this.defaultData.querySelector('.og-title').textContent;
        },

        /**
         * Return default value for openGraphMetadataDescription
         * @returns {string}
         * @private
         */
        get _ogDescription() {
            return this.defaultData.querySelector('.og-description').textContent;
        },

        /**
         * Return default value for openGraphMetadataImage
         * @returns {string}
         * @private
         */
        get _ogImage() {
            return this.defaultData.querySelector('.og-image').textContent;
        },

        /**
         * Return default value for openGraphMetadataUrl
         * @returns {string}
         * @private
         */
        get _ogUrl() {
            return this.defaultData.querySelector('.og-url').textContent;
        },

        /**
         * Return calculated value for metadataTitle
         * @returns {string}
         */
        get title() {
            const input = seoAdvancedPanel.querySelector('.seo-advanced-panel-content.active input[data-form-title]');

            return this.limitString(input ? input.value || this._title : this._title, TITLE_LIMIT);
        },

        /**
         * Return calculated value for metadataDescription
         * @returns {string}
         */
        get description() {
            const input = seoAdvancedPanel.querySelector('.seo-advanced-panel-content.active input[data-form-description]');

            return this.limitString(input ? input.value || this._description : this._description, DESCRIPTION_LIMIT);
        },

        /**
         * Return calculated value for openGraphMetadataTitle
         * @returns {string}
         */
        get ogTitle() {
            const input = seoAdvancedPanel.querySelector('.seo-advanced-panel-content.active input[data-form-og-title]');

            return this.limitString(input ? input.value || this._ogTitle || this.title : this._ogTitle || this.title, TITLE_LIMIT);
        },

        /**
         * Return calculated value for openGraphMetadataDescription
         * @returns {string}
         */
        get ogDescription() {
            const input = seoAdvancedPanel.querySelector('.seo-advanced-panel-content.active input[data-form-og-description]');

            return this.limitString(input ? input.value || this._ogDescription || this.description : this._ogDescription || this.description, DESCRIPTION_LIMIT);
        },

        /**
         * Return calculated value for openGraphMetadataImage
         * @returns {string}
         */
        get ogImage() {
            const input = seoAdvancedPanel.querySelector('.seo-advanced-panel-content.active input[data-form-og-image]');

            return input ? input.value || this._ogImage : this._ogImage;
        },

        /**
         * Return calculated value for openGraphMetadataUrl
         * @returns {string}
         */
        get ogUrl() {
            const input = seoAdvancedPanel.querySelector('.seo-advanced-panel-content.active input[data-form-og-url]');

            return input ? input.value || this._ogUrl : this._ogUrl;
        },

        /**
         * @param {string} string
         * @param {number} limit
         * @returns {string}
         */
        limitString(string, limit) {
            if (String(string).length > limit) {
                return String(string).slice(0, limit) + '...';
            }

            return String(string);
        }
    };

    updatePreviewWidgets(form);
    seoAdvancedPanel.addEventListener('keyup', function (event) {
        updatePreviewWidgets(form);

        // Update char counter for formTitle and formOgTitle inputs into current form
        if (event.target.dataset.hasOwnProperty('formTitle') || event.target.dataset.hasOwnProperty('formOgTitle')) {
            setCounter(event.target.closest('.field'), event.target.value.length, TITLE_LIMIT);
        }

        // Update char counter for formDescription and formOgDescription inputs into current form
        if (event.target.dataset.hasOwnProperty('formDescription') || event.target.dataset.hasOwnProperty('formOgDescription')) {
            setCounter(event.target.closest('.field'), event.target.value.length, DESCRIPTION_LIMIT);
        }
    });

    seoAdvancedPanel.querySelectorAll('*[data-locale]').forEach(function (el) {
        el.addEventListener('click', function () {
            // Need sleep some time to update widget after semantic-ui lib perform click event on accordion element
            setTimeout(function () {
                if (form.defaultData) {
                    updatePreviewWidgets(form);
                }
            }, 500);
        });
    });
}

loadCss('/bundles/dedisyliusseoplugin/css/seo-content-form.css');