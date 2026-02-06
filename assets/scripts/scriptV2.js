(function($) {
    $(document).ready(function() {        

        // ##     ##    ###    ########  ####    ###    ########  ##       ########  ######  
        // ##     ##   ## ##   ##     ##  ##    ## ##   ##     ## ##       ##       ##    ## 
        // ##     ##  ##   ##  ##     ##  ##   ##   ##  ##     ## ##       ##       ##       
        // ##     ## ##     ## ########   ##  ##     ## ########  ##       ######    ######  
        //  ##   ##  ######### ##   ##    ##  ######### ##     ## ##       ##             ## 
        //   ## ##   ##     ## ##    ##   ##  ##     ## ##     ## ##       ##       ##    ## 
        //    ###    ##     ## ##     ## #### ##     ## ########  ######## ########  ######  

        var margin = 350;
        var laptop = 1280;
        var tablet = 1024;
        var mobile = 768; 
        var quality = parseFloat(datas.quality);

        $modal = $('.wp-annotations__modal');  
        $formModal = '#wp-annotation-form';  
        $switchBubble = '#wp-annotations--switch-bubble';
        $dashBubble = '#wp-annotations--dash-bubble';
        $noticeBox = '#wp-annotations--notices';
        $layout = '#wp-annotations--comments-layout';
        $dashboard = '#wp-annotations--dashboard';
        $replyBox = '#wp-annotations__replies';
        $replyForm = '#reply-box-form';

        //  ######  ##      ## #### ########  ######  ##     ##     ######   #######  ##     ## ##     ## ######## ##    ## ########  ######        ## ########  ########   #######  ##      ##  ######  ######## 
        // ##    ## ##  ##  ##  ##     ##    ##    ## ##     ##    ##    ## ##     ## ###   ### ###   ### ##       ###   ##    ##    ##    ##      ##  ##     ## ##     ## ##     ## ##  ##  ## ##    ## ##       
        // ##       ##  ##  ##  ##     ##    ##       ##     ##    ##       ##     ## #### #### #### #### ##       ####  ##    ##    ##           ##   ##     ## ##     ## ##     ## ##  ##  ## ##       ##       
        //  ######  ##  ##  ##  ##     ##    ##       #########    ##       ##     ## ## ### ## ## ### ## ######   ## ## ##    ##     ######     ##    ########  ########  ##     ## ##  ##  ##  ######  ######   
        //       ## ##  ##  ##  ##     ##    ##       ##     ##    ##       ##     ## ##     ## ##     ## ##       ##  ####    ##          ##   ##     ##     ## ##   ##   ##     ## ##  ##  ##       ## ##       
        // ##    ## ##  ##  ##  ##     ##    ##    ## ##     ##    ##    ## ##     ## ##     ## ##     ## ##       ##   ###    ##    ##    ##  ##      ##     ## ##    ##  ##     ## ##  ##  ## ##    ## ##       
        //  ######   ###  ###  ####    ##     ######  ##     ##     ######   #######  ##     ## ##     ## ######## ##    ##    ##     ######  ##       ########  ##     ##  #######   ###  ###   ######  ######## 

        $('body').on('click', $switchBubble, function() {
            $('html').removeClass('dash-open');
            
            if( $('html').hasClass('review-mode') ) {
                setHTMLClasses(true);
                closeModals();      

                checkCommentsEdits(); 
                
                displayNotice('Mode ajout de commentaires désactivé', 'success');
            }
            else{
                setHTMLClasses(false, 'review');
                displayNotice('Mode ajout de commentaires activé', 'success');
            }
        });

        // ########  ######## ##     ## ####  ######  ########  ######      ######  ##          ###     ######   ######  
        // ##     ## ##       ##     ##  ##  ##    ## ##       ##    ##    ##    ## ##         ## ##   ##    ## ##    ## 
        // ##     ## ##       ##     ##  ##  ##       ##       ##          ##       ##        ##   ##  ##       ##       
        // ##     ## ######   ##     ##  ##  ##       ######    ######     ##       ##       ##     ##  ######   ######  
        // ##     ## ##        ##   ##   ##  ##       ##             ##    ##       ##       #########       ##       ## 
        // ##     ## ##         ## ##    ##  ##    ## ##       ##    ##    ##    ## ##       ##     ## ##    ## ##    ## 
        // ########  ########    ###    ####  ######  ########  ######      ######  ######## ##     ##  ######   ######  

        updateDeviceClass();

        $(window).resize(function() {
            updateDeviceClass();
        });

        // ########     ###     ######  ##     ## ########   #######     ###    ########  ########     ########  #######   ######    ######   ##       ######## 
        // ##     ##   ## ##   ##    ## ##     ## ##     ## ##     ##   ## ##   ##     ## ##     ##       ##    ##     ## ##    ##  ##    ##  ##       ##       
        // ##     ##  ##   ##  ##       ##     ## ##     ## ##     ##  ##   ##  ##     ## ##     ##       ##    ##     ## ##        ##        ##       ##       
        // ##     ## ##     ##  ######  ######### ########  ##     ## ##     ## ########  ##     ##       ##    ##     ## ##   #### ##   #### ##       ######   
        // ##     ## #########       ## ##     ## ##     ## ##     ## ######### ##   ##   ##     ##       ##    ##     ## ##    ##  ##    ##  ##       ##       
        // ##     ## ##     ## ##    ## ##     ## ##     ## ##     ## ##     ## ##    ##  ##     ##       ##    ##     ## ##    ##  ##    ##  ##       ##       
        // ########  ##     ##  ######  ##     ## ########   #######  ##     ## ##     ## ########        ##     #######   ######    ######   ######## ######## 
                                                                                                                                                    
        $('body').on('click', $dashBubble, function() {
            $('html').removeClass('review-mode');

            if( $('html').hasClass('dash-open') ) {
                setHTMLClasses(true);
                closeModals();

                checkCommentsEdits();
            }
            else{
                setHTMLClasses(false, 'dash');
            }
        });

        // ##     ##  #######  ########     ###    ##       
        // ###   ### ##     ## ##     ##   ## ##   ##       
        // #### #### ##     ## ##     ##  ##   ##  ##       
        // ## ### ## ##     ## ##     ## ##     ## ##       
        // ##     ## ##     ## ##     ## ######### ##       
        // ##     ## ##     ## ##     ## ##     ## ##       
        // ##     ##  #######  ########  ##     ## ######## 

        // === Open Modal
        $('body').on('click', $layout, function(event) {            
            var offset = $(this).offset();
            var x = event.pageX - offset.left;
            var y = event.pageY - offset.top;
        
            var pageWidth = $(this).width();
            var pageHeight = $(this).height();
        
            var xPercent = (x / pageWidth) * 100;
            var yPercent = (y / pageHeight) * 100;
        
            $modal
                .css({ top: yPercent + '%', left: xPercent + '%' })
                .show();

            $($formModal).show().find('textarea').focus();
        });  

        // === Stop propagation on modal form
        $('body').on('click', $formModal, function(e) {
            e.stopPropagation();
        });

        // === Close modal on Escape key
        $($layout).on('keydown', function(event) {
            if( event.key === "Escape" ){
                closeModals();
            }
        });

        // === Reset annotation form
        $($formModal).on('reset', function(event) {
            closeModals();
        })

        // ##     ## ######## ##    ## ######## ####  #######  ##    ##    ##       ####  ######  ######## 
        // ###   ### ##       ###   ##    ##     ##  ##     ## ###   ##    ##        ##  ##    ##    ##    
        // #### #### ##       ####  ##    ##     ##  ##     ## ####  ##    ##        ##  ##          ##    
        // ## ### ## ######   ## ## ##    ##     ##  ##     ## ## ## ##    ##        ##   ######     ##    
        // ##     ## ##       ##  ####    ##     ##  ##     ## ##  ####    ##        ##        ##    ##    
        // ##     ## ##       ##   ###    ##     ##  ##     ## ##   ###    ##        ##  ##    ##    ##    
        // ##     ## ######## ##    ##    ##    ####  #######  ##    ##    ######## ####  ######     ##    

        // === Mention List Toggle
        $('body').on('keyup', '.mention-list-parent textarea', function(event) {
            $textarea = $(this);
            $mentionList = $textarea.closest('.mention-list-parent').find('.mention-list-box');
            var cursorPos = this.selectionStart;
            var text = $textarea.val().substring(0, cursorPos);
            var match = text.match(/@(\w*)$/);
    
            if (match) {
                $mentionList.slideDown(250);
            } else {
                $mentionList.slideUp(250);
            }
        });

        // === Close Mention List on outside click
        $($layout).on('click', function(e) {
            if (!$(e.target).closest('.mention-list-box, .mention-list-parent textarea').length) {
                $('.mention-list-parent').find('.mention-list-box').slideUp(250);
            }
        });

        // === Mention list item click
        $('body').on('click', '.mention-list-item', function() {            
            $this = $(this);
            $textarea = $this.closest('.mention-list-parent').find('textarea');
            $mentionList = $this.closest('.mention-list-parent').find('.mention-list-box');
            var username = $this.data('user-name');            
            var text = $textarea.val();
            var cursorPos = $textarea[0].selectionStart;
            var beforeCursor = text.substring(0, cursorPos);
            var afterCursor = text.substring(cursorPos);            
            
            let $input = $('<input>', {
                type: 'hidden',
                name: 'targets_email[]',
                value: $this.data('user-id')
            });

            $this.closest('form').append($input);
            
            var newText = beforeCursor.replace(/@(\w*)$/, '@' + username + ' ') + afterCursor;
            $textarea.val(newText);
            $mentionList.slideUp(250);
            $textarea.focus();
        });

        // ########  ######## ##     ## ####  ######  ########  ######  
        // ##     ## ##       ##     ##  ##  ##    ## ##       ##    ## 
        // ##     ## ##       ##     ##  ##  ##       ##       ##       
        // ##     ## ######   ##     ##  ##  ##       ######    ######  
        // ##     ## ##        ##   ##   ##  ##       ##             ## 
        // ##     ## ##         ## ##    ##  ##    ## ##       ##    ## 
        // ########  ########    ###    ####  ######  ########  ######  

        initSelect2();

        //  ######  ##     ## ########  ##     ## #### ########       ###    ##    ## ##    ##  #######  ########    ###    ######## ####  #######  ##    ##    ########  #######  ########  ##     ## 
        // ##    ## ##     ## ##     ## ###   ###  ##     ##         ## ##   ###   ## ###   ## ##     ##    ##      ## ##      ##     ##  ##     ## ###   ##    ##       ##     ## ##     ## ###   ### 
        // ##       ##     ## ##     ## #### ####  ##     ##        ##   ##  ####  ## ####  ## ##     ##    ##     ##   ##     ##     ##  ##     ## ####  ##    ##       ##     ## ##     ## #### #### 
        //  ######  ##     ## ########  ## ### ##  ##     ##       ##     ## ## ## ## ## ## ## ##     ##    ##    ##     ##    ##     ##  ##     ## ## ## ##    ######   ##     ## ########  ## ### ## 
        //       ## ##     ## ##     ## ##     ##  ##     ##       ######### ##  #### ##  #### ##     ##    ##    #########    ##     ##  ##     ## ##  ####    ##       ##     ## ##   ##   ##     ## 
        // ##    ## ##     ## ##     ## ##     ##  ##     ##       ##     ## ##   ### ##   ### ##     ##    ##    ##     ##    ##     ##  ##     ## ##   ###    ##       ##     ## ##    ##  ##     ## 
        //  ######   #######  ########  ##     ## ####    ##       ##     ## ##    ## ##    ##  #######     ##    ##     ##    ##    ####  #######  ##    ##    ##        #######  ##     ## ##     ## 

        $($formModal).on('submit', function(event) {
            event.preventDefault();
            $($dashboard).addClass('ajax');
            $($formModal).hide();
            $screenQuality = parseFloat(datas.quality);  

            $('html').addClass('screenshot');           
            
            html2canvas(document.body, {
                scale: quality,
                scrollX: 0,
                scrollY: 0,
                width: window.innerWidth,
                height: window.innerHeight, 
                x: window.scrollX,
                y: window.scrollY
            }).then(function(canvas) {
                var screenshot = canvas.toDataURL('image/png', quality);

                $('html').removeClass('screenshot');

                if( $('html').hasClass('tablet') ){
                    $device = 'tablet';
                }
                else if( $('html').hasClass('mobile') ){
                    $device = 'mobile';
                }
                else{
                    $device = 'laptop';
                }
                
                var datas = [
                    $(event.target).serialize(),
                    $device, 
                    $($formModal).data('page-id')
                ];
        
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: {
                        action: 'wp_annotations_submit_comment',
                        datas: datas,
                        device: $device,
                        deviceView: getDashboardDevice(),
                        view: getDashboardView(),
                        screenshot: screenshot // Ajouter l'image en base64
                    },
                    success: function(response) {
                        if (response.success) {                            
                            resetAfterSubmit(response.data.comments_content, response.data.message);
                        } else {
                            displayNotice(response.data.message, 'error');
                            $($dashboard).removeClass('ajax');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        displayNotice('Une erreur s\'est produite', 'error');
                        $($dashboard).removeClass('ajax');
                    }
                });
            });
        });

        // ######## #### ##       ######## ######## ########   ######  
        // ##        ##  ##          ##    ##       ##     ## ##    ## 
        // ##        ##  ##          ##    ##       ##     ## ##       
        // ######    ##  ##          ##    ######   ########   ######  
        // ##        ##  ##          ##    ##       ##   ##         ## 
        // ##        ##  ##          ##    ##       ##    ##  ##    ## 
        // ##       #### ########    ##    ######## ##     ##  ######  

        $('body').on('change', '#ann-devices-select, #ann-comments-select', function() {
            filterComments();
        });

        //  ######   #######  ##     ##       ###     ######   ######   #######  ########  ########  ########  #######  ##    ## 
        // ##    ## ##     ## ###   ###      ## ##   ##    ## ##    ## ##     ## ##     ## ##     ## ##       ##     ## ###   ## 
        // ##       ##     ## #### ####     ##   ##  ##       ##       ##     ## ##     ## ##     ## ##       ##     ## ####  ## 
        // ##       ##     ## ## ### ##    ##     ## ##       ##       ##     ## ########  ##     ## ######   ##     ## ## ## ## 
        // ##       ##     ## ##     ##    ######### ##       ##       ##     ## ##   ##   ##     ## ##       ##     ## ##  #### 
        // ##    ## ##     ## ##     ##    ##     ## ##    ## ##    ## ##     ## ##    ##  ##     ## ##       ##     ## ##   ### 
        //  ######   #######  ##     ##    ##     ##  ######   ######   #######  ##     ## ########  ########  #######  ##    ## 

        $('body').on('click', $dashboard + ' .accordeon-header button', function() {
            $(this).closest('.accordeon-header').toggleClass('closed').next().slideToggle();
        });

        //  ######   #######  ##     ##     ######  ########    ###    ######## ##     ##  ######  
        // ##    ## ##     ## ###   ###    ##    ##    ##      ## ##      ##    ##     ## ##    ## 
        // ##       ##     ## #### ####    ##          ##     ##   ##     ##    ##     ## ##       
        // ##       ##     ## ## ### ##     ######     ##    ##     ##    ##    ##     ##  ######  
        // ##       ##     ## ##     ##          ##    ##    #########    ##    ##     ##       ## 
        // ##    ## ##     ## ##     ##    ##    ##    ##    ##     ##    ##    ##     ## ##    ## 
        //  ######   #######  ##     ##     ######     ##    ##     ##    ##     #######   ######  

        $('body').on('click', $dashboard + ' button.resolve', function() {
            $commentID = $(this).closest('.comment-item').data('comment-id');

            $($dashboard).addClass('ajax');

            var datas = {
                action: 'wp_annotations_update_status',
                id: $commentID,
                view: getDashboardView(),
                deviceView: getDashboardDevice()
            };

            $.ajax({
                url: ajaxurl.url,
                type: 'POST',
                data: datas,
                success: function(response) {
                    if (response.success) {
                        resetAfterSubmit(response.data.comments_content, response.data.message);                       
                    } else {                        
                        $($dashboard).removeClass('ajax');
                        displayNotice(response.data.message, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    displayNotice('Une erreur s\'est produite', 'error');
                    $($dashboard).removeClass('ajax');
                }
            });
        });

        //  ######   #######  ##     ##    ######## ########  #### ######## ####  #######  ##    ## 
        // ##    ## ##     ## ###   ###    ##       ##     ##  ##     ##     ##  ##     ## ###   ## 
        // ##       ##     ## #### ####    ##       ##     ##  ##     ##     ##  ##     ## ####  ## 
        // ##       ##     ## ## ### ##    ######   ##     ##  ##     ##     ##  ##     ## ## ## ## 
        // ##       ##     ## ##     ##    ##       ##     ##  ##     ##     ##  ##     ## ##  #### 
        // ##    ## ##     ## ##     ##    ##       ##     ##  ##     ##     ##  ##     ## ##   ### 
        //  ######   #######  ##     ##    ######## ########  ####    ##    ####  #######  ##    ## 

        // === Switch edit mode
        $('body').on('click', $dashboard + ' button.edit', function() {
            $par = $(this).closest('.comment-item');
            $default = $par.find('.comment-item__content p').text();
            $textarea = $par.find('textarea').val();

            if( $par.hasClass('edit') ){
                var confirmation = $default != $textarea ? confirm('Votre commentaire n\'a pas été sauvegardé. Souhaitez-vous l\'annuler ?') : true;

                if( confirmation ){
                    $par.removeClass('edit');
                    $par.find('.comment-item__content').show().next().hide().find('textarea').val($default);
                }
            }
            else{
                $par.addClass('edit');
                $par.find('.comment-item__content').hide().next().show();
            }
        });

        // === Cancel edit mode
        $('body').on('click', $dashboard + ' button.cancel', function(event) {
            event.preventDefault();
            $par = $(this).closest('.comment-item');
            $default = $par.find('.comment-item__content p').text();
            $textarea = $par.find('textarea').val();

            if( $par.hasClass('edit') ){
                var confirmation = $default != $textarea ? confirm('Votre commentaire n\'a pas été sauvegardé. Souhaitez-vous l\'annuler ?') : true;

                if( confirmation ){
                    $par.removeClass('edit');
                    $par.find('.comment-item__content').show().next().hide().find('textarea').val($default);
                }
            }
        });

        // === Update comment
        $('body').on('submit', $dashboard + ' .comment-item__content-form', function(event) {
            event.preventDefault();
            $par = $(this).closest('.comment-item');
            $default = $par.find('.comment-item__content p').text();
            $textarea = $par.find('textarea').val();
            $commentID = $(this).closest('.comment-item').data('comment-id');

            if( $default != $textarea ){
                $($dashboard).addClass('ajax');
    
                var datas = {
                    action: 'wp_annotations_edit_comment',
                    id: $commentID,
                    comment: $textarea,
                    deviceView: getDashboardDevice(),
                    view: getDashboardView()
                };                       
    
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: datas,
                    success: function(response) {
                        if (response.success) {
                            resetAfterSubmit(response.data.comments_content, response.data.message);                       
                        } else {
                            $($dashboard).removeClass('ajax');
                            displayNotice(response.data.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $($dashboard).removeClass('ajax');
                        displayNotice('Une erreur s\'est produite', 'error');
                    }
                });
            }
            else{
                $par.removeClass('edit');
                $par.find('.comment-item__content').show().next().hide().find('textarea').val($default);
            }

        });

        // ########  ######## ##       ######## ######## ########     ######   #######  ##     ## 
        // ##     ## ##       ##       ##          ##    ##          ##    ## ##     ## ###   ### 
        // ##     ## ##       ##       ##          ##    ##          ##       ##     ## #### #### 
        // ##     ## ######   ##       ######      ##    ######      ##       ##     ## ## ### ## 
        // ##     ## ##       ##       ##          ##    ##          ##       ##     ## ##     ## 
        // ##     ## ##       ##       ##          ##    ##          ##    ## ##     ## ##     ## 
        // ########  ######## ######## ########    ##    ########     ######   #######  ##     ## 

        $('body').on('click', $dashboard + ' button.delete', function() {
            var confirmation = confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');

            if (confirmation) {
                $commentID = $(this).closest('.comment-item').data('comment-id');
    
                $($dashboard).addClass('ajax');
    
                var datas = {
                    action: 'wp_annotations_delete_comment',
                    id: $commentID,
                    deviceView: getDashboardDevice(),
                    view: getDashboardView()
                };                       
    
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: datas,
                    success: function(response) {
                        if (response.success) {
                            resetAfterSubmit(response.data.comments_content, response.data.message);                        
                        } else {
                            $($dashboard).removeClass('ajax');
                            displayNotice(response.data.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $($dashboard).removeClass('ajax');
                        displayNotice('Une erreur s\'est produite', 'error');
                    }
                });
            }

        });

        // ########  ######## ########  ##       #### ########  ######  
        // ##     ## ##       ##     ## ##        ##  ##       ##    ## 
        // ##     ## ##       ##     ## ##        ##  ##       ##       
        // ########  ######   ########  ##        ##  ######    ######  
        // ##   ##   ##       ##        ##        ##  ##             ## 
        // ##    ##  ##       ##        ##        ##  ##       ##    ## 
        // ##     ## ######## ##        ######## #### ########  ######  

        // === Open reply
        $('body').on('click', '.open-add-comments', function() {            
            $par = $(this).closest('.comment-item');
            $commentID = $par.data('comment-id');

            $('body').addClass('no-scroll');
            $($replyBox).addClass('ajax').fadeIn(300);    
    
            var datas = {
                action: 'wp_annotations_open_reply',
                id: $commentID,
            };                    
    
            $.ajax({
                url: ajaxurl.url,
                type: 'POST',
                data: datas,
                success: function(response) {
                    if (response.success) {
                        $($replyBox).removeClass('ajax');  
                        $('#wp-annotations-replies-display').html(response.data.reply_content);                     
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $($replyBox).removeClass('loading');  
                    displayNotice('Une erreur s\'est produite', 'error');   
                }
            });
        });
        
        // === Close reply
        $('body').on('click', $replyBox + ' .close-replies', function() {
            $('body').removeClass('no-scroll');
            $($replyBox).fadeOut(300);
        });

        // ########  ######## ########  ##       ##    ##    ######## #### ##       ######## 
        // ##     ## ##       ##     ## ##        ##  ##     ##        ##  ##       ##       
        // ##     ## ##       ##     ## ##         ####      ##        ##  ##       ##       
        // ########  ######   ########  ##          ##       ######    ##  ##       ######   
        // ##   ##   ##       ##        ##          ##       ##        ##  ##       ##       
        // ##    ##  ##       ##        ##          ##       ##        ##  ##       ##       
        // ##     ## ######## ##        ########    ##       ##       #### ######## ######## 

        $('body').on('click', $replyForm + ' .file-input .unfiled', function() {            
            $par = $(this).closest('.file-input');
            $par.find('input').click();
        });

        $('body').on('change', $replyForm + ' .file-input input', function() {
            if($(this).attr('name') == 'mulfiles'){
                $par = $(this).closest('.file-input');
                var files = $(this)[0].files;
                selectedFiles = Array.from(files);
                updateFileDisplay();
            }
            else{
                $par = $(this).closest('.file-input');
                var fileName = $(this).val().split('\\').pop();  
        
                $par.find('.filed .text').text(fileName);
                $par.find('.unfiled').fadeOut(150, function(){
                    $par.find('.filed').fadeIn(150);
                });
            }
        });

        $('body').on('click', $replyForm + ' .file-input .filed .clear', function() {
            $par = $(this).closest('.file-input');
            var $fileItem = $(this).closest('.file-item');
            var fileName = $fileItem.find('.text').text();

            $par.find('.filed').fadeOut(150, function(){
                $par.find('.unfiled').fadeIn(150);
            });
            $par.find('input').val('');

            $fileItem.fadeOut(150, function() {
                $fileItem.remove();
                selectedFiles = selectedFiles.filter(file => file.name !== fileName);
                updateFileInput();
                if ($('.filed-files').children().length === 0) {
                    $('.unfiled').fadeIn(150);
                }
            });
        });

        //  ######  ##     ## ########  ##     ## #### ########    ########  ######## ########  ##       ##    ## 
        // ##    ## ##     ## ##     ## ###   ###  ##     ##       ##     ## ##       ##     ## ##        ##  ##  
        // ##       ##     ## ##     ## #### ####  ##     ##       ##     ## ##       ##     ## ##         ####   
        //  ######  ##     ## ########  ## ### ##  ##     ##       ########  ######   ########  ##          ##    
        //       ## ##     ## ##     ## ##     ##  ##     ##       ##   ##   ##       ##        ##          ##    
        // ##    ## ##     ## ##     ## ##     ##  ##     ##       ##    ##  ##       ##        ##          ##    
        //  ######   #######  ########  ##     ## ####    ##       ##     ## ######## ##        ########    ##    

        $('body').on('submit', $replyForm, function(event) {
            event.preventDefault();
            $this = $(this);

            if( $(this).find('textarea').val() != '' ){
                $par = $(this).closest('.reply-box');
                $par.addClass('ajax');

                $visible = $par.find('input[name="client-visible"]');


                var commentID = $par.data('comment-id');
                var userID = $par.data('user-id');
                var commentText = $this.find('textarea').val();

                if ($visible.attr('type') === 'checkbox') {
                    var clientVisible = $visible.is(':checked') ? 1 : 0;
                } else {
                    var clientVisible = parseInt($visible.val()) || 0;
                }

                var targetsEmail = [];

                $(this).find('input[name="targets_email[]"]').each(function() {                    
                    targetsEmail.push($(this).val());
                });

                targetsEmail = [...new Set(targetsEmail)];              

                var formData = new FormData();
                formData.append('action', 'wp_annotations_submit_reply');
                formData.append('comment_id', commentID);
                formData.append('user_id', userID);
                formData.append('comment_text', commentText);
                formData.append('client_visible', clientVisible);

                if( targetsEmail.length > 0 ){
                    targetsEmail.forEach((email, index) => {
                        formData.append(`targets_email[${index}]`, email);
                    });
                } else {
                    formData.append('targets_email', '');
                }
        
                var fileInput = $this.find('input[type="file"]')[0];
                if (fileInput.files.length > 0) {
                    formData.append('reply_file', fileInput.files[0]);
                }
        
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false,  
                    success: function(response) {
                        if (response.success) {
                            resetAfterReplySubmit(response.data.reply_content, response.data.message, $par);
                            refreshDashboard();
                        } else {
                            $par.removeClass('ajax');
                            displayNotice(response.data.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $par.removeClass('ajax');
                        displayNotice('Une erreur s\'est produite', 'error');
                    }
                });
            }
            else{
                alert('Veuillez saisir un commentaire');
            }

        });

        // ########  ######## ##       ######## ######## ########    ########  ######## ########  ##       ##    ## 
        // ##     ## ##       ##       ##          ##    ##          ##     ## ##       ##     ## ##        ##  ##  
        // ##     ## ##       ##       ##          ##    ##          ##     ## ##       ##     ## ##         ####   
        // ##     ## ######   ##       ######      ##    ######      ########  ######   ########  ##          ##    
        // ##     ## ##       ##       ##          ##    ##          ##   ##   ##       ##        ##          ##    
        // ##     ## ##       ##       ##          ##    ##          ##    ##  ##       ##        ##          ##    
        // ########  ######## ######## ########    ##    ########    ##     ## ######## ##        ########    ##    

        $('body').on('click', $replyBox + ' .delete', function() {            
            var confirmation = confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');

            if( confirmation ){
                $par = $(this).closest('.reply-box');
                $par.addClass('ajax');
                $commentID = $par.data('comment-id');
                $parRep = $(this).closest('.reply-item');
                $replyID = $parRep.data('id');
                    
                var datas = [
                    $replyID,
                    $commentID
                ];
        
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: {
                        action: 'wp_annotation_delete_reply',
                        datas: datas
                    },
                    success: function(response) {
                        if (response.success) {
                            resetAfterReplySubmit(response.data.reply_content, response.data.message, $par);
                            refreshDashboard();
                        } else {
                            $par.removeClass('ajax');
                            displayNotice(response.data.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $par.removeClass('ajax');
                        displayNotice('Une erreur s\'est produite', 'error');
                    }
                });
            }
        });

        // ##       ####  ######   ##     ## ######## ########   #######  ##     ## 
        // ##        ##  ##    ##  ##     ##    ##    ##     ## ##     ##  ##   ##  
        // ##        ##  ##        ##     ##    ##    ##     ## ##     ##   ## ##   
        // ##        ##  ##   #### #########    ##    ########  ##     ##    ###    
        // ##        ##  ##    ##  ##     ##    ##    ##     ## ##     ##   ## ##   
        // ##        ##  ##    ##  ##     ##    ##    ##     ## ##     ##  ##   ##  
        // ######## ####  ######   ##     ##    ##    ########   #######  ##     ## 

        $('body').on('click', '.wp-annotations .expend', function() {
            var src = $(this).next().attr('src');
            $('body').addClass('no-scroll');

            $('#wp-annotations--lightbox').fadeIn().find('img.lightbox-img').attr('src', src);
        });

        $('body').on('click', '#wp-annotations--lightbox .close-light-button', function() {
            $('#wp-annotations--lightbox').fadeOut();
            $('body').removeClass('no-scroll');
        });

        // ######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######  
        // ##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ## 
        // ##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##       
        // ######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######  
        // ##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ## 
        // ##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ## 
        // ##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######  

        // ### Reset modal form
        function resetModalForm() {
            $($formModal).find('textarea').val('').siblings('.mention-list-main').hide();
            $($formModal).find('input[type="checkbox"]').prop('checked', false);
            $($formModal).find('input[type=hidden]').remove();    
            $($formModal).hide();
        }
             
        // ### Close modals
        function closeModals() {
            $modal.hide();
            resetModalForm();
        }

        // ### Display notice messages
        function displayNotice(message, type='success') {
            var uniqueID = 'notice-' + Date.now();
            $newNotice = '<div class="wp-ann-notice-item ' + uniqueID + '">';
            if( type === 'success' ){
                $newNotice += '<span><iconify-icon icon="material-symbols:check-circle"></iconify-icon></span>';
            }
            else{
                $newNotice += '<span><iconify-icon icon="material-symbols:warning-rounded"></iconify-icon></span>';
            }
            $newNotice += '<p class="wp-ann-notice-item__message">' + message + '</p>';
            $newNotice += '</div>';


            $($noticeBox).append($newNotice);

            setTimeout(function() {
                $($noticeBox).find('.' + uniqueID).fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        // ### Set HTML classes for review or dashboard mode
        function setHTMLClasses( active = false, type = 'review' ){
            if( active ){
                $('html').removeClass('review-mode dash-open laptop mobile tablet');
            }
            else if( type === 'dash' ){
                $('html').addClass('dash-open');
            }
            else{
                $winWidth = $(window).width();

                if( $winWidth <= tablet && $winWidth > mobile ){
                    $('html').addClass('review-mode tablet');
                }
                else if( $winWidth <= mobile ){
                    $('html').addClass('review-mode mobile');
                }
                else{
                    $('html').addClass('review-mode laptop');
                }
            }
        }

        // ### Update device class on HTML tag
        function updateDeviceClass() {            
            $winWidth = $(window).width();    

            if( $winWidth <= tablet && $winWidth > mobile ){
                $('html').removeClass('laptop mobile').addClass('tablet');
            }
            else if( $winWidth <= mobile ){
                $('html').removeClass('laptop tablet').addClass('mobile');
            }
            else{
                $('html').removeClass('tablet mobile').addClass('laptop');
            }
        }

        // ### Check for unsaved comment edits
        function checkCommentsEdits(){            
            if( $('.wp-annotations--dashboard .comment-item.edit').length ){

                var confirmation = confirm('Vous avez des commentaires en cours de modification. Souhaitez-vous les annuler ?');

                if( confirmation ){
                    $('.wp-annotations--dashboard .comment-item').each(function() {
                        if( $(this).hasClass('edit') ){
                            $default = $(this).find('.comment-item__content p').text();
    
                            $(this).removeClass('edit');
                            $(this).find('.comment-item__content').show().next().hide().find('textarea').val($default);
                        }
                    });
                }
            }
        }

        // ### Refresh after reply submission
        function resetAfterReplySubmit(content, message, parent){
            parent.find('textarea, input[name="email"]').val('');
            parent.removeClass('ajax');
            $('#reply-box-content').html(content);
            refreshDashboard();

            if( message ){
                displayNotice(message, 'success');
            }
        }

        // ### Reset dashboard after AJAX actions
        function resetAfterSubmit(content, message){
            closeModals();
            $($dashboard).removeClass('ajax');
            $('#wp-annotations--refresh-box').html(content);

            if( message ){
                displayNotice(message, 'success');
            }

            $('#ann-devices-select, #ann-comments-select').select2('destroy');
            initSelect2();
        }

        // ### Refresh dashboard comments
        function refreshDashboard(){
            $($dashboard).addClass('ajax');
    
            var datas = {
                action: 'wp_annotations_refresh_dashboard',
                type: 'refresh',
                deviceView: getDashboardDevice(),
                view: getDashboardView()
            };                       

            $.ajax({
                url: ajaxurl.url,
                type: 'POST',
                data: datas,
                success: function(response) {
                    if (response.success) {
                        $($dashboard).removeClass('ajax');
                        resetAfterSubmit(response.data.comments_content, false);                       
                    } else {
                        $($dashboard).removeClass('ajax');
                        displayNotice(response.data.message, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $($dashboard).removeClass('ajax');
                    displayNotice('Une erreur s\'est produite', 'error');
                }
            });

        }

        // ### Initialize Select2 for dashboard filters
        function initSelect2(){            
            $('#ann-devices-select, #ann-comments-select').select2({
                templateResult: formatDeviceOption,
                templateSelection: formatDeviceOption,
                minimumResultsForSearch: Infinity,
                containerCssClass: 'wp-annotations-container',
                dropdownCssClass: 'wp-annotations-dropdown'
            });
        }

        // ### Format Select2 options with icons
        function formatDeviceOption(option) {
            if (!option.id) {
                return option.text;
            }
            var $option = $(option.element);
            var icon = $option.data('icon');
            if (!icon) {
                return option.text;
            }
            var $result = $(
                '<span><iconify-icon icon="' + icon + '"></iconify-icon> ' + option.text + '</span>'
            );
            return $result;
        }

        // ### Filter comments
        function filterComments(){
            $($dashboard).addClass('ajax');
            
            $.ajax({
                url: ajaxurl.url,
                type: 'POST',
                data: {
                    action: 'filter_wp_annotations_comments',
                    deviceView: getDashboardDevice(),
                    view: getDashboardView()
                },
                success: function(response) {
                    if (response.success) {
                        resetAfterSubmit(response.data.comments_content, false);
                    } else {
                        displayNotice(response.data.message, 'error');
                        $($dashboard).removeClass('ajax');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    displayNotice('Une erreur s\'est produite', 'error');
                    $($dashboard).removeClass('ajax');
                }
            });

        }

        // ### Getters for dashboard filters ---
        function getDashboardView(){
            return $('#ann-comments-select').val();
        }

        function getDashboardDevice(){
            return $('#ann-devices-select').val();
        }
        // --- ###
    });
})(jQuery);