$('documnet').ready(function () {
    $("#appbundle_addressbook_picture").fileinput({
        showUpload: false,
        browseClass: "btn btn-default btn-sm",
        browseLabel: "Pick Image",
        browseIcon: '<i class="fa fa-file-image-o"></i> ',
        removeClass: "btn btn-danger btn-sm",
        removeLabel: "Delete",
        removeIcon: '<i class="fa fa-trash-o"></i> ',
        uploadClass: "btn btn-default  btn-sm",
        preview: true,
        showPreview: true,
        showRemove: false,
        allowedFileTypes:['image'],
        maxFileSize: 1024,
        initialPreview: ($('#oldImage') && $('#oldImage').val()) ? [
            '<img src="' + $('#oldImage').val() + '" alt="" title="aa" />'
        ] : false,
        initialPreviewConfig:[
            {title:''}
        ]
    })
})
