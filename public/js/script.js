
Dropzone.autoDiscover = false;

jQuery.validator.setDefaults({
    errorPlacement: function (error, element) {
        $(element).closest('div.form-group').find('.form-text').html(error.html());
    },
    highlight: function (element) {
        $(element).closest('div.form-group').removeClass('has-success').addClass('has-error');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).closest('div.form-group').removeClass('has-error').addClass('has-success');
        $(element).closest('div.form-group').find('.form-text').html('');
    }
});

var Uploader = (function (window, document, Uploader) {

    var $form, obj, MSG, $btn, $modal, myDropzone;
    $form = $("#imageUploadForm");
    $btn = $("#uploaderBtn");
    $modal = $("#successModal");
    obj = {};

    MSG = {
        name: "Please enter name",
        email: "Please enter email",
        mobile: "Please enter mobile number"
    };


    function validate() {
        $form.validate({
            submitHandler: function (form) {
                return false;
            },
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true,
                    minlength: 7
                },
            },
            messages: {
                name: {
                    required: MSG.name
                },
                email: {
                    required: MSG.email
                },
                mobile: {
                    required: MSG.mobile
                },
            }
        });
    }

    function initializeDropZone() {
        console.log("initializeDropZone");
    
        myDropzone = new Dropzone('div#imageUpload', {
            addRemoveLinks: true,
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 100,
            maxFiles: 3,
            paramName: 'file',
            clickable: true,
            url: 'products/gallery-image',
            init: function () {

                var myDropzone = this;
                // Update selector to match your button
                $btn.on("click", function (e) {
                    alert('imageUpload');
                    e.preventDefault();
                    if ( $form.valid() ) {
                        myDropzone.processQueue();
                    }
                    return false;
                });

                this.on('sending', function (file, xhr, formData) {
                    // Append all form inputs to the formData Dropzone will POST
                    var data = $form.serializeArray();
                    $.each(data, function (key, el) {
                        formData.append(el.name, el.value);
                    });
                    console.log(formData);

                });
            },
            error: function (file, response){
                try {
                    var res = JSON.parse(response);
                    if (typeof res.message !== 'undefined' && !$modal.hasClass('in')) {
                        $("#success-icon").attr("class", "fas fa-thumbs-down");
                        $("#success-text").html(res.message);
                        $modal.modal("show");
                    } else {
                        if ($.type(response) === "string")
                            var message = response; //dropzone sends it's own error messages in string
                        else
                            var message = response.message;
                        file.previewElement.classList.add("dz-error");
                        _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                        _results = [];
                        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            node = _ref[_i];
                            _results.push(node.textContent = message);
                        }
                        return _results;
                    }
                } catch (error) {
                    console.log(error);
                }
                
            },
            successmultiple: function (file, response) {
                console.log(file, response);
                response = JSON.parse(response);
                $("#success-icon").attr("class", "fas fa-thumbs-up");
                $("#success-text").html(response.message);
                $modal.modal("show");
            },
            completemultiple: function (file, response) {
                console.log(file, response, "completemultiple");
                //$modal.modal("show");
            },
            reset: function () {
                console.log("resetFiles");
                this.removeAllFiles(true);
            }
        });
    }

    function registerEvents(){
        $modal.on('hide.bs.modal', function () {
            $form[0].reset();
            myDropzone.emit("reset");

            $("#imageUpload>.dz-message").show();
        });
    }
    
    obj.init = function() {
        validate();
        initializeDropZone();
        registerEvents();
    };
    Uploader = obj;
    //
    return Uploader;
})(window, document, Uploader);

$(function(){
    Uploader.init();
});

