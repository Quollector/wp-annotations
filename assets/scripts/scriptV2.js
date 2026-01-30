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

        $modal = $('#wp-annotations--modal');  
        $formModal = $('#wp-annotation-form');  
        $switchBubble = '#wp-annotations--switch-bubble';
        $dashBubble = '#wp-annotations--dash-bubble';
        $noticeBox = '#wp-annotations--notices';

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

        // ######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######  
        // ##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ## 
        // ##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##       
        // ######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######  
        // ##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ## 
        // ##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ## 
        // ##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######  

        function resetModalForm() {
            $formModal.find('textarea').val('').siblings('.mention-list-main').hide();
            $formModal.find('input[type="checkbox"]').prop('checked', false);
            $formModal.find('input[type=hidden]').remove();    
            $formModal.hide();
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