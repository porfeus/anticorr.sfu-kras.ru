/**
 * Created by User on 02.08.2017.
 * Handlers for tinymce editor upload images
 */

window.tinymce_file_picker_callback = function(cb, value, meta) {
  var input = document.createElement('input');
  input.setAttribute('type', 'file');
  input.setAttribute('accept', 'image/*');

  // Note: In modern browsers input[type="file"] is functional without
  // even adding it to the DOM, but that might not be the case in some older
  // or quirky browsers like IE, so you might want to add it to the DOM
  // just in case, and visually hide it. And do not forget do remove it
  // once you do not need it anymore.

  input.onchange = function() {
    var file = this.files[0];

    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function () {
      // Note: Now we need to register the blob in TinyMCEs image blob
      // registry. In the next release this part hopefully won't be
      // necessary, as we are looking to handle it internally.
      var id = 'blobid' + (new Date()).getTime();
      var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
      var base64 = reader.result.split(',')[1];
      var blobInfo = blobCache.create(id, file, base64);
      blobCache.add(blobInfo);

      // call the callback and populate the Title field with the file name
      cb(blobInfo.blobUri(), { title: file.name });
    };
  };

  input.click();
};

window.tinymce_images_upload_handler = function (blobInfo, success, failure) {
  var xhr, formData;

  xhr = new XMLHttpRequest();
  xhr.withCredentials = false;
  xhr.open('POST', '/api/files');
  xhr.onload = function() {
    var json;

    if (xhr.status != 201) {
      failure('HTTP Error: ' + xhr.status);
      return;
    }

    json = JSON.parse(xhr.responseText);

    if (!json || typeof json.url != 'string') {
      failure('Invalid JSON: ' + xhr.responseText);
      return;
    }

    success(json.url);
  };

  formData = new FormData();
  formData.append('file', blobInfo.blob(), blobInfo.filename());
  formData.append('name', blobInfo.filename());

  xhr.send(formData);
};