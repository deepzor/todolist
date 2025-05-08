;(function ($) {

    $(window).on("load", function(){

       $('.todolist__toggle').on('click', function() {
           $(this).hide();
           $('.todolist__overlay').toggleClass('show');
           showNotesList();
       });

       $(document).on('input','.todolist__item-title input', checkFields);
       $(document).on('input','.todolist__item-content textarea', checkFields);

       function checkFields(){
           if($(this).val()){
               $(this).removeClass('todolist__error');
           }
       }

       $(document).on('click','.todolist__controls-save', function(){
           const noteTitle = $('.todolist__item-title input').val().trim();
           const noteContent = $('.todolist__item-content textarea').val().trim();
           const index = $('.todolist__item').data('index');

           if(!noteTitle){
               $('.todolist__item-title input').toggleClass('todolist__error');
           }

           if(!noteContent){
               $('.todolist__item-content textarea').toggleClass('todolist__error');
           }

           if(noteTitle && noteContent){
               updateNote(noteTitle, noteContent, index);
           }
       });

       $(document).on('click','.todolist__controls-remove', function(){
           const index = $('.todolist__item').data('index');
           deleteNote(index);
       });

       $(document).on('click','.todolist__note',function() {
           const index = $(this).data('index');
           showNotesEditor(index);
       });

        $(document).on('click','.todolist__controls-add',function() {
            showNotesEditor();
        });

       $(document).on('click','.todolist__close-image',function() {
           $('.todolist__overlay').toggleClass('show');
           $('.todolist__wrapper').html('');
           $('.todolist__toggle').show();
       });

       function showNotesEditor(index) {
           $.ajax({
               url: window.wp_js.ajax_url,
               type: 'post',
               data: {action: 'edit_note', note_index: index},
               success: (response) => {
                   $('.todolist__wrapper').html(response);
               }
           });
       }

       function showNotesList() {
            $.ajax({
                url: window.wp_js.ajax_url,
                type: 'post',
                data: {action: 'list_notes'},
                success: (response) => {
                    $('.todolist__wrapper').html(response);
                }
            });
       }

       function updateNote(title, content, index) {
           $.ajax({
               url: window.wp_js.ajax_url,
               type: 'post',
               data: {action: 'save_note', note_index: index ,note_title: title, note_content: content},
               success: (response) => {
                   showNotesList();
                   console.log(response);
               }
           });
       }

       function deleteNote(noteIndex) {
           $.ajax({
               url: window.wp_js.ajax_url,
               type: 'post',
               data: {action: 'delete_note', note_index: noteIndex},
               success: () => {
                   showNotesList();
               }
           });
       }

    });

})(jQuery);

