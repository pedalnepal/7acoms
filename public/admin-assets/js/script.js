function makeUniqueId(length) {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    let counter = 0;
    while (counter < length) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
      counter += 1;
    }
    return result;
}

function initAdminSelect2(scope) {
    if (typeof $.fn.select2 !== 'function') {
        return;
    }

    var $scope = scope ? $(scope) : $(document);
    $scope.find('select').each(function () {
        var $select = $(this);
        if ($select.hasClass('select2-hidden-accessible') || $select.hasClass('no-select2')) {
            return;
        }

        var placeholder = $select.attr('data-placeholder') || '';
        if (!placeholder) {
            var firstOptionValue = $select.find('option:first').val();
            if (firstOptionValue === '') {
                placeholder = $select.find('option:first').text();
            }
        }

        var $modalParent = $select.closest('.modal');
        var options = {
            width: '100%',
            allowClear: $select.find('option[value=""]').length > 0 && !$select.prop('multiple'),
            placeholder: placeholder || undefined,
            dropdownParent: $modalParent.length ? $modalParent : $(document.body)
        };

        if ($select.prop('multiple')) {
            options.closeOnSelect = false;
        }

        $select.select2(options);
    });
}

window.initAdminSelect2 = initAdminSelect2;
$(document).ready(function() {
    $(document).on('click', '#file_attachment_submit', function () {
        var file_data = $("#MediaAttachment").prop("files")[0];
        if (file_data != '' && file_data != undefined) {

            $.each($("#MediaAttachment").prop("files"), function( index, file_data ) {
                var uId = makeUniqueId(15);
                var siz = parseFloat(file_data.size/(1024*1024));

                var tmpHtml = '<div id="'+uId+'" class="media-item">\
                                <div class="media-inside">\
                                    <label for="'+uId+'_input" class="media-content">\
                                        <img id="'+uId+'_media" src="" alt="uploading image">\
                                    </label>\
                                    <div class="progressing-bar"><span class="progressing-text">0</span>\
                                        <span style="width: 0%;" class="progressing-bg"></span><i class="abort-request">x</i>\
                                    </div>\
                                </div>\
                            </div>';
                $("#MediaContent").prepend(tmpHtml);
                if (FileReader) {
                    var fr = new FileReader();
                    fr.onload = function () {
                        document.getElementById(uId+'_media').src = fr.result;
                    }
                    fr.readAsDataURL(file_data);
                }
                var formData = new FormData();
                formData.append('file', file_data);

                var ajaxCallVar = $.ajax({
                    url: $("#file_attachment_submit").data('action'),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = parseFloat((evt.loaded / evt.total) * 100);
                                $('#'+uId).find(".progressing-bg").width(percentComplete.toFixed(2) + '%');
                                $('#'+uId).find(".progressing-text").html(percentComplete.toFixed(2)+'%');
                            }
                        }, false);
                        return xhr;
                    },
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        $('#MediaAttachment').val('');
                        $('.attachment_text').html('Choose a file…');
                        data = JSON.parse(data);

                        $('#'+uId).prepend('<input style="display: none;" type="radio" id="'+uId+'_input" name="smedia" value="'+data.media_id+'" data-url="'+data.media_url+'">');
                        $(uId+'_input').data('url', data.media_url);

                        $('#'+uId).find('.progressing-bar').replaceWith('<div class="media-bottom">\
                        <a href="javascript:void(0);" class="edit-btn" data-media="'+data.media_id+'"></a>\
                        <a href="javascript:void(0);" class="delete-btn media-send-to-trash" data-media="'+data.media_id+'"></a>\
                    </div>');
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        console.log('error', data);
                        if (data.message) {
                            show_toastr('error', data.errors.file[0]);
                            $('#file-error').text(data.errors.file[0]).show();
                        } else {
                            show_toastr('error', 'Some Thing Is Wrong!');
                        }
                    }
                });
                $('#'+uId).find('.abort-request').on('click', function(){
                    $('#'+uId).find(".progressing-text").html('Cancelled');
                    $('#'+uId).addClass('error-occured');
                    $(this).hide();
                    ajaxCallVar.abort();
                });
            });
        } else {
            show_toastr('error', 'Please select file!');
        }
        console.log('not working');
    });

    var counter = 0;
    $("#AllMediaContainer").bind({
        dragenter: function (ev) {
          ev.preventDefault();
          counter++;
          $(this).addClass("active-dropzone");
        },
        dragleave: function () {
          counter--;
          if (counter === 0) {
            $(this).removeClass("active-dropzone");
          }
        },
        drop: function () {
          counter--;
          if (counter === 0) {
            $(this).removeClass("active-dropzone");
          }
        },
    });
    document.getElementById('MediaAttachment').addEventListener('change', function(e) {
      $('#file_attachment_submit').trigger('click');
    });

    var scroll_append = true;
    $('#AllMediaContainer').on('scroll', function () {
        if($('#MediaContent > .media-item').length>0){
         var scrolled_position = $('#MediaContent > .media-item').last().offset().top-500-$(this).outerHeight(true);
         //alert($('#AllMediaContainer').scrollTop());
            if($('#AllMediaContainer').scrollTop() > scrolled_position){
                if(scroll_append==true){
                    scroll_append = false;
                    var total_count = $('#MediaContent > .media-item').length;
                    var keyword = $('#MediaKeyword').val();

                    $.ajax({
                        type:'POST',
                        data:{'action':'load_more', 'count':total_count, 'keyword':keyword},
                        url:site_url+'/admin/dashboard/media/ajax',
                        success:function(response){
                            $('#MediaContent').append(response);
                            scroll_append = true;

                        }
                    });
                }
            }
        }
    });

    var scroll_append = true;
    $('#MediaKeyword').on('keyup', function () {
        if(scroll_append==true){
            scroll_append = false;
            var keyword = $('#MediaKeyword').val();
            // $('#MediaContent').html('');
            $.ajax({
                type:'POST',
                data:{'action':'load_more', 'count':0, 'keyword':keyword},
                url:site_url+'/admin/dashboard/media/ajax',
                success:function(response){
                    $('#MediaContent').html(response);
                    scroll_append = true;

                }
            });
        }
    });
    media_option_init();
    clear_media_init();
    initAdminSelect2(document);
});
$(document).on('click', '.media-send-to-trash', function(event){
    event.preventDefault();
    var media_id = $(this).data('media');
    var deleting_media_id = $('#MediaContent').find('input[value="'+media_id+'"]').parent();
    if(media_id!=''){
        confirmed =  confirm('Are you sure want to delete this file');
        if(confirmed){
            $.ajax({
                type:'POST',
                data:{'action':'trash_media', 'media_id':media_id},
                url:site_url+'/admin/dashboard/media/ajax',
                success:function(response){
                    response = $.parseJSON(response);
                    if(response.success_msg!='' && response.success_msg!=undefined){
                        alert(response.success_msg);
                        deleting_media_id.fadeOut('slow');
                    }else{
                        alert('Error while deleting media.');
                    }
                }
            });
        }else{
            return false;
        }
    }else{
        deleting_media_id.fadeOut('slow');
    }
});
$(document).on('click', '.media-send-to-edit', function(){
    var mediaid = $(this).data('media');
    var mediaInput = $('#MediaContent').find('input[value="'+mediaid+'"]');
    var alt = mediaInput.data('alt');

    $('#MediaEditModal').modal('show');
    $('#MediaEditModal').find('#main_media_title').val(alt);
    $('#MediaEditModal').find('#main_media_title').attr({'value' : alt});
    $('#MediaEditModal').find('#MediaIdForEdit').val(mediaid);
    $('#MediaEditModal').find('#MediaIdForEdit').attr({'value' : mediaid});

    return false;
});
$(document).on('click', '#UpdateAltInMedia', function(e){
    e.preventDefault();
    var data = $("#MediaEditForm").serialize();
    $.ajax({
        type:'POST',
        data:data,
        url:site_url+'/admin/dashboard/media/ajax',
        success:function(response){
            var mediaItem = $('#MediaContent').find('input[value="'+response.id+'"]');

            mediaItem.data('alt', response.alt);
            mediaItem.attr('data-alt', response.alt);

            $('#MediaEditModal').modal('hide');
            $('#MediaEditModal').find('#main_media_file_name').val("");
            $('#MediaEditModal').find('#main_media_file_name').attr({'value' : ""});
        }
    });
});
function media_option_init(){
    $('.media-open').on('click', function(e){
        e.preventDefault();
        $('#MediaModal').modal('show');
        var data_for = $(this).attr("data-for");
        var data_type = $(this).attr("data-type");
        $('#MediaModal').attr({'data-for': data_for});
        $('#MediaModal').attr({'data-type': data_type});
    });
}
function media_editor_setups(ed)
{
    ed.on('keyup', function () {
        ed.save();
    });
    ed.ui.registry.addButton('mybutton', {
        title : 'Add Image',
        icon:'image',
        onAction : function() {
            current_id = ed.id;
            $('#MediaModal').modal('show');
                media_type = $('#MediaModal').attr({'data-type' : 'editor'});
                media_for = $('#MediaModal').attr({'data-for': current_id});
            }
    });
    // Adding a custom button
    ed.ui.registry.addButton('addClassButton', {
        text: 'Edit Class',
        onAction: function () {
            // Get the selected element
            var selectedElement = ed.selection.getNode();
            // Get current class of the selected element
            var currentClass = selectedElement.className || '';
            console.log(currentClass);
            // Open a Dialog to edit class name
            var dialogApi = ed.windowManager.open({
                title: 'Edit Class Name',
                body: {
                    type: 'panel',
                    items: [
                        {
                            type: 'input',
                            name: 'className',
                            label: 'Class Name',
                            value: 'default' // Set the current class as the default value
                        }
                    ]
                },
                buttons: [
                    {type: 'cancel', text: 'Close'},
                    {type: 'submit', text: 'Apply', primary: true}
                ],
                onSubmit: function (api) {
                    var data = api.getData();
                    // Update the class of the selected element
                    selectedElement.className = data.className;
                    api.close();
                }
            });

            var currentClass = selectedElement.className || '';
           dialogApi.setData({ className: currentClass });
        }
    });
    ed.ui.registry.addButton('bootstrapRow', {
        text: 'Insert Row',
        onAction: function () {
            ed.insertContent('<div class="row"></div>');
        }
    });
    ed.ui.registry.addButton('bootstrapColumn', {
        text: 'Insert Column',
        onAction: function () {
            var row = ed.dom.getParent(ed.selection.getStart(), '.row');
            if (row) {
                ed.dom.add(row, 'div', {class: 'col-md-4'}, '<p> </p>');
            } else {
                // If not inside a row, create a new row and add the column
                ed.insertContent('<div class="row"><div class="col-md-4"><p> </p></div></div>');
            }
        }
    });
}
function clear_media_init(){
    $('.clear-media').click(function(){
        $(this).parent().find('.media-image-content').html('');
        $(this).parent().find('input').attr({ 'value' : '' });
        $(this).parent().find('input').val('');
    });
}

$('#SelectAndInsertMedia').click(function(){
    var media_for   =   $('#MediaModal').attr('data-for');
    var media_id;
    var media_type  =   $('#MediaModal').attr('data-type');
    var media_src;
    var media_alt;

    if (mediaList.smedia.value) {
        var mediaItem = $('#MediaContent').find('input[value="'+mediaList.smedia.value+'"]');

        media_id = mediaList.smedia.value;
        media_src = mediaItem.data('url');
        media_alt = mediaItem.data('alt');
        mediaItem.prop('checked', false);
    }
    if (media_type == 'editor') {
        tinymce.get(media_for).execCommand('mceInsertContent', false, '<img src="'+ media_src + '" alt="'+media_alt+'">');
    }else{
        $('#' + media_for ).attr({"value": media_id});
        $('#' + media_for + '_preview').html('<img src="' + media_src + '">');
    }
    $('#MediaModal').modal('hide');

    mediaList.smedia.value = '';
});
function clone(clone_id,  clone_to)
{
    $( clone_id ).clone().appendTo("#" + clone_to).css({
        'left':'50px',
        'opacity':0
    }).animate(
        {
            'left':'0',
            'opacity' : 1
        },400,
        function(){
            change_id(clone_to);
            init_shortable();
            media_option_init();
            initAdminSelect2("#" + clone_to);
        }
        );
    return false;
}
function clone_before(clone_id, clone_to, el)
{
    before_content = $(el).parent();
    $("#"+clone_id + " > div").clone().insertBefore(before_content);

    change_id(clone_to);

    media_option_init();
    initAdminSelect2("#" + clone_to);
    return false;
}
function delete_row(el, clone_to, parent_class)
{
    $(el).closest('.'+parent_class).animate(
        {
            'left':'50px',
            'opacity' : 0
        },400,
        function(){
            $(this).remove();
            change_id(clone_to);
            change_id(clone_to);
            media_option_init();
        }
        );
    return false;

}
function change_id(clone_to)
{
    var count = 1;
    var main_name = $("#"+clone_to).attr('data-name');
    var all_item  = $("#"+clone_to).attr('data-item');
    var first_item  = $("#"+clone_to).attr('data-first');
    var second_item  = $("#"+clone_to).attr('data-second');
    var third_item  = $("#"+clone_to).attr('data-third');
    var fourth_item  = $("#"+clone_to).attr('data-fourth');
    var fifth_item  = $("#"+clone_to).attr('data-fifth');
    var inside_this = $('#'+clone_to).attr('data-inside');
    if (inside_this==null) {
        inside_this = 'div';
    }

    $("#"+clone_to+" > " + inside_this).each(function(){

        $(this).attr({'data-position': count});
        $(this).find('.'+first_item).attr({'name':main_name+'['+count+']['+first_item+']'}).removeAttr('disabled');

        $(this).find('.'+second_item).attr({
            'name':main_name+'['+count+']['+second_item+']',
            'id'  : main_name+'_'+count+'_'+second_item
        }).removeAttr('disabled');


        $(this).find('.'+third_item).attr({'name':main_name+'['+count+']['+third_item+']'}).removeAttr('disabled');
        $(this).find('.'+fourth_item).attr({'name':main_name+'['+count+']['+fourth_item+']'}).removeAttr('disabled');
        $(this).find('.'+fifth_item).attr({'name':main_name+'['+count+']['+fifth_item+']'}).removeAttr('disabled');

        $(this).find('.media-open').attr({'data-for':main_name+'_'+count});
        $(this).find('.image-preview').attr({'id':main_name+'_'+count+'_preview'});
        $(this).find('.image_id_content').attr({'id':main_name+'_'+count});

        $(this).find('.position').attr({'data-position':count});
        $(this).find('.position').html( count );

        count=count+1;
    });
    clear_media_init();
    initAdminSelect2("#" + clone_to);
}
function init_shortable() {
    $('.sortable').sortable({
        refreshPositions: true,
        opacity: 0.6,
        cursor: 'move',
        containment: "parent",
        items: ".row-sh",
        // placeholder: 'ros-shh-contents',
        helper: 'clone',
        appendTo: 'body',
        forcePlaceholderSize: true,
        handle:'.handle',
        // axis:'y',
        distance: 15,
        update: function( event, ui ) {
            if (ui.item) {
                changed_id = $(ui.item).parent().attr("id");
                change_id(changed_id);
            }
        }
    });
}
$(document).ready(function(){
    init_shortable();
    tiny_editor_init('.E');
    var ctnt = 0;
    $('.menu_list tbody tr').each(function(){
        ctnt++;
        $(this).find('.count').html(ctnt);
    });

    initAdminSelect2(document);
});
$('#List_Sortable').sortable({
    cursor: "move",
    items: "tr",
    helper: 'clone',
    appendTo: 'body',
    handle:'.handle',
    containment: "parent",
    forcePlaceholderSize: true,
    //axis:'y',
    update: function (event, ui) {
        var Alldata;
        var MenuData = new Array();

        var order_change_variable = "change_order";

        var this_id = $(ui.item).parent().attr("id");
        var this_to = $(ui.item).parent().attr("data-for");
        var this_url = $(ui.item).parent().attr("data-url");

        $('#'+this_id+' tr').each(function(row, tr){
            MenuData[row]={
                "order" : $(tr).find('.main_id').text()
            }
        });

        Alldata = JSON.stringify(MenuData);
        var msg = '<section class="content-header"><div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sucessfully Updated Menu Order</div></section>';
        $.ajax({
            type: "POST",
            url: this_url,
            data: { "action" : order_change_variable,  "AMenuData" : Alldata },
            success:function(data){
              $("#notification-container").html(msg);
            },
            error:function(){
                alert('error');
            }
        });
    }
});
/**
 *textarea_to_init: Textarea class to init editoe
 **/
function tiny_editor_init(textarea_to_init){
    tinymce.init({
        selector: textarea_to_init,
        height: 350,
        setup : media_editor_setups,
        relative_urls : false,
        browser_spellcheck: true,
        branding: false,
        paste_as_text: true,
        plugins: [
            'advlist pageembed autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'template paste textcolor colorpicker textpattern'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: ' media | forecolor backcolor | mybutton'
    });
}
/**
 *cloned_element_class: Item to check how much clone object in initial
 *element_to_clone: element which have to clone {{#div_id > div}}
 *appendto: id where to clone cloned object
 *textarea_class: class of the textarea to initialized tinymce editor
 *textarea_id: id for the cloned textarea
 *clone_with_editor('#cloned_container > div', '#clone_container > div', '#cloned_container', '.itinerary-detail', 'repeat')
 *Note: name of input and textarea must be like in array eg: {{detail[]}}
 */
 function uniqId() {
  return Math.round(new Date().getTime() + (Math.random() * 100));
}
function clone_with_editor(cloned_element_class, element_to_clone, appendto, textarea_class, textarea_id){
    random_textarea_id = uniqId();
    var cloned_itinerary = $(element_to_clone).clone().appendTo(appendto);
    $(cloned_itinerary).find(textarea_class).attr({'id' :  textarea_id +  random_textarea_id });
     tinymce.init({
        selector: '#'+textarea_id+random_textarea_id,
        setup : media_editor_setups,
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | mybutton"
    });
}

/**
 *clone_object: Item to clone (element to clone to)
 *els: normally {{this}}
 *input_class: Class of the textarea for editor initialize
 *repeat_id: Id to Repeat for the textarea
 *closest_before_which: before which close element to clone
 *clone_before_with_editor('#clone_container > div', this, 'itinerary-detail', 'repeat')
 *Note: name of input and textarea must be like in array eg: {{detail[]}}
 */
function clone_before_with_editor(clone_object, els, input_class,repeat_id,closest_before_which){
    random_textarea_id = uniqId();
    before_contents = $(els).closest(closest_before_which);

    var cloned_itinerary = $(clone_object).clone().insertBefore(before_contents);
    $(cloned_itinerary).find('.'+input_class).attr({'id' :  repeat_id +  random_textarea_id });
    tinymce.init({
        selector: '#'+repeat_id+random_textarea_id,
        setup : media_editor_setups,
        plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | mybutton"
    });
}
/**
 *c:normally {{this}}
 *parent_class: parent class to remove
 **/
function remove_row_with_editor(c, parent_class){
    $(c).closest('.'+parent_class).animate({
            'position':'relative',
            'left':'50px',
            'opacity' : 0
        },400,
        function(){
            $(this).remove();
        }
    );
    return false;
}
/**
 *id: Id from the database
 *module: mod_{{module_name}}
 *el: normally {{this}}
 *example:change_status("2", this, ajax_url)
 */
function change_status(id, el, ajax_url, action_s="change_status"){
    $.ajax({
        type:'POST',
        url: ajax_url,
        data: {"action": action_s, "post_id" : id},
        success:function(data){
            datas = jQuery.parseJSON(data);
            $(el).html(datas.contents);
        }
    });
}

$(function () {
    $('#link_from').change(function(){
        var data_val = $(this).val();
        $.ajax({
            type: 'post',
            data: { 'action'  : 'list_menu', 'menu_from' : data_val  },
            url: site_url+"/admin/dashboard/menu/ajax",
            success: function(data){
                $('#menu_link').html(data);
            }
        });
    });
    $('#menu_link').change(function(){
        if($(this).val() != '') {
            var now_val = $(this).find('option:selected').html();
            now_val = now_val.split("-");
            now_val = now_val.join("");
            now_val = $.trim(now_val);
            $('#menu_title').attr({'placeholder' : now_val});
        }else{
            $('#menu_title').attr({'placeholder' : ''});
        }
    });
   $('#link_type').change(function(){
        if ( $(this).val()  == 0 ) {
            $('#internal_link_group').slideDown("slow");
            $('#external_link_group').slideUp("slow");
        }else{
            $('#external_link_group').slideDown("slow");
            $('#internal_link_group').slideUp("slow");
        };
    });
});
