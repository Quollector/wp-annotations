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

        $('#ann-devices-select, #ann-comments-select').select2({
            templateResult: formatDeviceOption,
            templateSelection: formatDeviceOption,
            minimumResultsForSearch: Infinity,
            containerCssClass: 'wp-annotations-container',
            dropdownCssClass: 'wp-annotations-dropdown'
        });

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
            $formModal.hide();
            $screenQuality = parseFloat(datas.quality);             
            
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
                    $formModal.attr('data-page-id'),
                    $formModal.attr('data-user-id')
                ];
        
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: {
                        action: 'submit_wp_annotation',
                        datas: datas,
                        device: $device,
                        view: $('.wp-annotations--dashboard__comments').hasClass('active') ? 'active' : 'resolved',
                        screenshot: screenshot // Ajouter l'image en base64
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#wp-annotations--modal').hide().find('textarea').val('');
                            $('#wp-annotations--modal').find('input[type="checkbox"]').prop('checked', false);
                            $('#wp-annotations--modal').find('input[type=hidden]').remove();
                            $modal.find('#wp-annotation-form').show();
                            $('#wp-annotations--dashboard').removeClass('ajax');
                            $('#wp-annotations--notices').addClass('success').show().find('p').text(response.data.message);
                            $('#wp-annotations--refresh-box').html(response.data.comments_content);
        
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);
                        } else {
                            $('#wp-annotations--notices').addClass('error').show().find('p').text(response.data.message);
                            $('#wp-annotations--modal').hide().find('textarea').val('');
                            $('#wp-annotations--modal').find('input[type="checkbox"]').prop('checked', false);
                            $('#wp-annotations--modal').find('input[type=hidden]').remove();
                            $modal.find('#wp-annotation-form').show();
                            $('#wp-annotations--dashboard').removeClass('ajax');
        
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#wp-annotations--notices').addClass('error').show().find('p').text('Une erreur s\'est produite');
                        $('#wp-annotations--dashboard').removeClass('ajax');
    
                        setTimeout(function() {
                            $('#wp-annotations--notices').fadeOut(function(){
                                $(this).removeClass('error success');
                            });
                        }, 2000);
                    }
                });
            });
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
            $($noticeBox).addClass(type).show().find('p').text(message);

            setTimeout(function() {
                $($noticeBox).fadeOut(function(){
                    $(this).removeClass(type);
                });
            }, 2000);
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

    });
})(jQuery);