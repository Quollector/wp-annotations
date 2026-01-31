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
        $('body').on('click', function(e) {
            if (!$(e.target).closest('#mention-list-main, #wp-annotation-form textarea').length) {
                $(this).closest('form').find('#mention-list-main').slideUp(250);
            }
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

    });
})(jQuery);