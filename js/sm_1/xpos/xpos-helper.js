/**
* huypq@smartosc.com
* 24 03 2015
*
* Helpful functions (optional functions) stand here!
*/
;(function(window, undefined) {
    /**
    * huypq@smartosc.com
    * 24/03/2015
    * Console log only if verbose log flag is set true (DEVELOPER MODE)
    */
    function prelog(thing, decoration) {
        if (window.VERBOSE_LOG) {
            if (decoration) {
                thing = '%c' + thing;
                console.log(thing, decoration);
            } else {
                console.log(thing);
            }

        }
        return window.VERBOSE_LOG;
    }

    /**
    * huypq@smartosc.com
    * 03/04/2015
    * Keep selected option of select HtmlElement in html aspect
    */
    function preserveSelectedOption(event, elem) {
        [].forEach.call(elem.options, function(_value, _index) {
            if (_index == elem.selectedIndex) {
                _value.setAttribute('selected', true);
            } else {
                _value.removeAttribute('selected');
            }
        });
    }

    //add to global scope
    window.VERBOSE_LOG = false;
    window.prelog = prelog;
    window.preserveSelectedOption = preserveSelectedOption;
})(window, undefined);
