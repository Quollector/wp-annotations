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

        // === Mention List Toggle
        $('body').on('keyup', $formModal + ' textarea', function(event) {
            $textarea = $(this);
            $mentionList = $($formModal).find('#mention-list-main');
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
            if (!$(e.target).closest('#mention-list-main, #wp-annotation-form textarea').length) {
                $($formModal).find('#mention-list-main').slideUp(250);
            }
        });

        // === Mention list item click
        $('body').on('click', '.mention-list-main__item', function() {            
            $this = $(this);
            $textarea = $($formModal).find('textarea');
            $mentionList = $($formModal).find('#mention-list-main');
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

            $($formModal).find('form').append($input);
            
            var newText = beforeCursor.replace(/@(\w*)$/, '@' + username + ' ') + afterCursor;
            $textarea.val(newText);
            $mentionList.slideUp(250);
            $textarea.focus();
        });

        // === Reset annotation form
        $($formModal).on('reset', function(event) {
            closeModals();
        })

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
                        action: 'submit_wp_annotation',
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
                            $('#wp-annotations--dashboard').removeClass('ajax');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        displayNotice('Une erreur s\'est produite', 'error');
                        $('#wp-annotations--dashboard').removeClass('ajax');
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

            $('#wp-annotations--dashboard').addClass('ajax');

            var datas = {
                action: 'update_wp_annotation_status',
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
                        $('#wp-annotations--dashboard').removeClass('ajax');
                        displayNotice(response.data.message, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    displayNotice('Une erreur s\'est produite', 'error');
                    $('#wp-annotations--dashboard').removeClass('ajax');
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
                $('#wp-annotations--dashboard').addClass('ajax');
    
                var datas = {
                    action: 'edit_wp_annotation_comment',
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
                            $('#wp-annotations--dashboard').removeClass('ajax');
                            displayNotice(response.data.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#wp-annotations--dashboard').removeClass('ajax');
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
    
                $('#wp-annotations--dashboard').addClass('ajax');
    
                var datas = {
                    action: 'delete_wp_annotation_comment',
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
                            $('#wp-annotations--dashboard').removeClass('ajax');
                            displayNotice(response.data.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#wp-annotations--dashboard').removeClass('ajax');
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

        function resetModalForm() {
            $($formModal).find('textarea').val('').siblings('.mention-list-main').hide();
            $($formModal).find('input[type="checkbox"]').prop('checked', false);
            $($formModal).find('input[type=hidden]').remove();    
            $($formModal).hide();
        }
                 
        function closeModals() {
            $modal.hide();
            resetModalForm();
        }

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

        function initSelect2(){            
            $('#ann-devices-select, #ann-comments-select').select2({
                templateResult: formatDeviceOption,
                templateSelection: formatDeviceOption,
                minimumResultsForSearch: Infinity,
                containerCssClass: 'wp-annotations-container',
                dropdownCssClass: 'wp-annotations-dropdown'
            });
        }

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
                        $('#wp-annotations--dashboard').removeClass('ajax');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    displayNotice('Une erreur s\'est produite', 'error');
                    $('#wp-annotations--dashboard').removeClass('ajax');
                }
            });

        }

        function getDashboardView(){
            return $('#ann-comments-select').val();
        }

        function getDashboardDevice(){
            return $('#ann-devices-select').val();
        }
    });
})(jQuery);