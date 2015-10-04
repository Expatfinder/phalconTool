/*global [className]Manager */
var [name]Manager;
(function(){
    "use strict";
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#init[className]
     * @event
     * @description initialize [name]
     */
    function init(){
        new JsHelper();
        [name]Manager = [className]Manager.getInstance();
    }
    
})();