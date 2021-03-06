/*global extendSingleton, getSingleton, isDefined, require */
var ActionHelper;
(function(){
    "use strict";
    /**
    * @name ActionHelper
    * @description To make ajax call
    * @property {String} [basePath] Base path used for ajax call
    * @constructor
    */
    ActionHelper = function(cb){
        var that = this;
        extendSingleton(ActionHelper);
        loadCss((window["baseUrl"] ? window["baseUrl"] : "")+"/bower_components/jquery.percentageloader/index.css");
        require([
            "bower_components/jquery-percentageloader/index"
        ], loaded);
        this.basePath = "/"+$("body").attr("app")+"/";
        var hasOnProgress = ("onprogress" in $.ajaxSettings.xhr());
        if (!hasOnProgress) {
            return;
        }       
        //patch ajax settings to call a progress callback
        var oldXHR = $.ajaxSettings.xhr;
        $.ajaxSettings.xhr = setAjaxSetting;

        function setAjaxSetting(){
            var xhr = oldXHR();
            if(xhr instanceof XMLHttpRequest) {
                xhr.addEventListener("progress", this.progress, false);
            }
            
            if(xhr.upload) {
                xhr.upload.addEventListener("progress", this.progress, false);
            }
            
            return xhr;
        }

        function loaded(){
            if(isDefined(cb)){
                cb(that);
            }
        }
    };

    /**
     * @member ActionHelper#getInstance
     * @description get the single class instance
     * @return {ActionHelper} the single class instance
     */
    ActionHelper.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton(ActionHelper, cb);
        } else {
            return getSingleton(ActionHelper);
        }
    };

    /**
     * @method ActionHelper#execute
     * @description Execute an ajax call
     * @param  {Object} [data]    Data to send
     * @param  {Object} [options] Options of the ajax call
     */
    ActionHelper.prototype.execute = function(data, options){
        if(!isDefined(options.noload)){
            $("body").append("<div class='backdrop'><div id='loader'></div></div>");
            var loader = $("#loader").percentageLoader({
                width : 128, 
                height : 128, 
                progress : 0, 
                value : 'chargement'
            });
            if(isDefined(options.upload)){
                $("body .backdrop").append("<div id='uploader'></div>");
                var uploader = $("#uploader").percentageLoader({
                    width : 64, 
                    height : 64, 
                    progress : 0, 
                    value : 'upload'
                });
            }
        }
        var infos = {
            type: options.type,
            data:data,
            url: this.basePath+options.action,
            dataType:options.dataType,
            success: check,
            error: checkError,
            complete: removeBackdrop,
            progress: updateLoader
        };        
        if(isDefined(options.form)){
            infos.cache = false;
            infos.contentType = false;
            infos.processData = false;
        }

        $.ajax(infos);

        function updateLoader(event){
            if(isDefined(options.noload)){
                return false;
            }
            if(event.target instanceof XMLHttpRequest){
                loader.setProgress(event.loaded / event.total);   
            } 
            if(isDefined(options.upload) && event.target instanceof XMLHttpRequestUpload){
                uploader.setProgress(event.loaded / event.total);    
            }       
        }

        function checkError(event){
            options.cb({success:false, error:event});
        }

        function check(data){
            options.cb(data);
        }

        function removeBackdrop(){
            if(!isDefined(options.noload)){
                $(".backdrop").remove();
            }
        }
    };

    /**
     * @description Redirect user to an internal url
     * @method ActionHelper#redirect
     * @param  {String} path Path to redirect
     */
    ActionHelper.prototype.redirect = function(path, type){
        if(type === undefined){
            window.location.href = this.basePath+path;
        } else {
            window.open(window.location.origin+this.basePath+path, "_"+type);
        }
    };
})();