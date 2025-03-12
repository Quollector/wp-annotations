(function($) {
    $(document).ready(function() {
        $margin = 350;
        $laptop = 1280;
        $tablet = 1024;
        $mobile = 768; 
        $modal = $('#wp-annotations--modal');

        // *** Switch comment / browse
        $('body').on('click', '#wp-annotations--switch-bubble', function() {
            
            $('html').removeClass('dash-open');
            $winWidth = $(window).width();

            if( $('html').hasClass('review-mode') ) {
                $('html').removeClass('review-mode laptop mobile tablet');
                $modal.hide();                

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

                $('#wp-annotations--notices').addClass('success').show().find('p').text('Mode ajout de commentaires désactivé');

                setTimeout(function() {
                    $('#wp-annotations--notices').fadeOut(function(){
                        $(this).removeClass('error success');
                    });
                }, 2000);
            }
            else{
                if( $winWidth <= $tablet && $winWidth > $mobile ){
                    $('html').addClass('review-mode tablet');
                }
                else if( $winWidth <= $mobile ){
                    $('html').addClass('review-mode mobile');
                }
                else{
                    $('html').addClass('review-mode laptop');
                }

                $('#wp-annotations--notices').addClass('success').show().find('p').text('Mode ajout de commentaires activé');

                setTimeout(function() {
                    $('#wp-annotations--notices').fadeOut(function(){
                        $(this).removeClass('error success');
                    });
                }, 2000);
            }
        });

        $(window).resize(function() {
            $winWidth = $(window).width();

            if( $('html').hasClass('review-mode') ){
                if( $winWidth <= $tablet && $winWidth > $mobile ){
                    $('html').removeClass('laptop mobile').addClass('tablet');
                }
                else if( $winWidth <= $mobile ){
                    $('html').removeClass('laptop tablet').addClass('mobile');
                }
                else{
                    $('html').removeClass('tablet mobile').addClass('laptop');
                }
            }

        });

        // *** Open/Close dashboard 
        $('body').on('click', '#wp-annotations--dash-bubble', function() {
            $('html').removeClass('review-mode');

            if( $('html').hasClass('dash-open') ) {
                $('html').removeClass('dash-open laptop mobile tablet');
                $modal.hide();

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
            else{
                $('html').addClass('dash-open');
            }
        });

        // *** Add comment
        $('body').on('click', '#wp-annotations--comments-layout', function(event) {            
            var offset = $(this).offset();
            var x = event.pageX - offset.left;
            var y = event.pageY - offset.top;
        
            var pageWidth = $(this).width();
            var pageHeight = $(this).height();
        
            var xPercent = (x / pageWidth) * 100;
            var yPercent = (y / pageHeight) * 100;
        
            $modal
                .css({ top: yPercent + '%', left: xPercent + '%' })
                .show()
                .attr('data-position-x', xPercent.toFixed(2) + '%')
                .attr('data-position-y', yPercent.toFixed(2) + '%')
                .find('textarea').focus();
        
            if ($(window).width() - event.pageX < 300) {
                $modal.find('form').css('transform', 'translateX(-270px)');
            } else {
                $modal.find('form').css('transform', 'none');
            }
        });        

        $('body').on('click', '#wp-annotations--modal', function(event) {
            event.stopPropagation();
        });

        // *** LIGHTBOX
        $('body').on('click', '.wp-annotations--dashboard .comment-item__screenshot .expend', function() {
            var src = $(this).next().attr('src');

            $('#wp-annotations--lightbox').fadeIn().find('img.lightbox-img').attr('src', src);
        });

        $('body').on('click', '#wp-annotations--lightbox .close-light-button', function() {
            $('#wp-annotations--lightbox').fadeOut();
        });


        // *** DASHBOARD

        // DEVICES
        // $('body').on('click', '.wp-annotations--dashboard__devices button', function() { 
        //     $par = $(this).closest('.wp-annotations--dashboard__devices');
        //     $winWidth = $(window).width();

        //     if( $(this).hasClass('laptop') ){
        //         $par.removeClass('tablet mobile').addClass('laptop');
        //         $('html').removeClass('tablet mobile').addClass('laptop');
        //         $('body').css('transform', 'scale(' + $laptop / $winWidth + ')');
        //     }
        //     else if( $(this).hasClass('tablet') ){
        //         $par.removeClass('laptop mobile').addClass('tablet');
        //         $('html').removeClass('laptop mobile').addClass('tablet');
        //     }
        //     else if( $(this).hasClass('mobile') ){
        //         $par.removeClass('laptop tablet').addClass('mobile');
        //         $('html').removeClass('laptop tablet').addClass('mobile');
        //     }
        // });
        
        // Switch active / resolved display
        $('body').on('click', '.wp-annotations--dashboard__comments button', function() { 
            if( $(this).hasClass('comments-actives') ){
                if( !$(this).closest('.wp-annotations--dashboard__comments').hasClass('active') ){
                    $(this).closest('.wp-annotations--dashboard__comments').addClass('active');
                    $('.wp-annotations--dashboard__comments-list.resolved-comments').fadeOut({
                        duration: 250,
                        complete: function() {
                            $('.wp-annotations--dashboard__comments-list.active-comments').fadeIn(250);
                        }
                    });
                }
            }
            else if( $(this).hasClass('comments-resolved') ){
                if( $(this).closest('.wp-annotations--dashboard__comments').hasClass('active') ){
                    $(this).closest('.wp-annotations--dashboard__comments').removeClass('active');
                    $('.wp-annotations--dashboard__comments-list.active-comments').fadeOut({
                        duration: 250,
                        complete: function() {
                            $('.wp-annotations--dashboard__comments-list.resolved-comments').fadeIn(250);
                        }
                    });
                }
            }
        });

        // Comments accordeon
        $('body').on('click', '.wp-annotations--dashboard .accordeon-header button', function() {
            $(this).closest('.accordeon-header').toggleClass('closed').next().slideToggle();
        });

        // Switch active / resolved status
        $('body').on('click', '.wp-annotations--dashboard button.resolve', function() {
            $commentID = $(this).closest('.comment-item').data('comment-id');
            
            $(this).toggleClass('false true');

            $('#wp-annotations--dashboard').addClass('ajax');

            var datas = {
                action: 'update_wp_annotation',
                type: 'status',
                id: $commentID,
                status: $(this).hasClass('true') ? 'Résolu' : 'Non résolu',
                view: $('.wp-annotations--dashboard__comments').hasClass('active') ? 'active' : 'resolved'
            };

            $.ajax({
                url: ajaxurl.url,
                type: 'POST',
                data: datas,
                success: function(response) {
                    if (response.success) {
                        $('#wp-annotations--notices').addClass('success').show().find('p').text(response.data.message);
                        $('#wp-annotations--dashboard').removeClass('ajax');
                        $('#wp-annotations--refresh-box').html(response.data.comments_content);

                        setTimeout(function() {
                            $('#wp-annotations--notices').fadeOut(function(){
                                $(this).removeClass('error success');
                            });
                        }, 2000);                        
                    } else {
                        $('#wp-annotations--notices').addClass('error').show().find('p').text(response.data.message);
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

        // *** COMMENTS FORM (ADD)
        $('#wp-annotation-form').on('submit', function(event) {
            event.preventDefault();
            $('#wp-annotations--dashboard').addClass('ajax');
            $modal.find('#wp-annotation-form').hide();
            
            html2canvas(document.body, {
                scale: 0.7,
                scrollX: 0,
                scrollY: 0,
                width: window.innerWidth,
                height: window.innerHeight, 
                x: window.scrollX, // Décale de 350px vers la droite
                y: window.scrollY
            }).then(function(canvas) {
                var screenshot = canvas.toDataURL('image/png', 0.5); // Convertir en base64

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
                    $modal.attr('data-position-x'),
                    $modal.attr('data-position-y'),
                    $device,
                    $modal.attr('data-page-id'),
                    $modal.attr('data-user-id')
                ];
        
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: {
                        action: 'submit_wp_annotation',
                        datas: datas,
                        screenshot: screenshot // Ajouter l'image en base64
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#wp-annotations--modal').hide().find('textarea').val('');
                            $modal.find('#wp-annotation-form').show();
                            $('#wp-annotations--notices').addClass('success').show().find('p').text(response.data.message);
                            $('#wp-annotations--dashboard').removeClass('ajax');
                            $('#wp-annotations--refresh-box').html(response.data.comments_content);
        
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);
                        } else {
                            $('#wp-annotations--notices').addClass('error').show().find('p').text(response.data.message);
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

        $('#wp-annotation-form textarea').on('keydown', function(event) {
            if (event.key === "Enter") { 
                event.preventDefault();
                $('#wp-annotation-form').submit();
            }
        });

        // *** DELETE COMMENT
        $('body').on('click', '.wp-annotations--dashboard button.delete', function() {
            var confirmation = confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');

            if (confirmation) {
                $commentID = $(this).closest('.comment-item').data('comment-id');
                $screenUrl = $(this).closest('.comment-item').data('screen-url');
    
                $('#wp-annotations--dashboard').addClass('ajax');
    
                var datas = {
                    action: 'update_wp_annotation',
                    type: 'delete',
                    id: $commentID,
                    screenUrl: $screenUrl,
                    view: $('.wp-annotations--dashboard__comments').hasClass('active') ? 'active' : 'resolved'
                };                       
    
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: datas,
                    success: function(response) {
                        if (response.success) {
                            $('#wp-annotations--notices').addClass('success').show().find('p').text(response.data.message);
                            $('#wp-annotations--dashboard').removeClass('ajax');
                            $('#wp-annotations--refresh-box').html(response.data.comments_content);
    
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);                        
                        } else {
                            $('#wp-annotations--notices').addClass('error').show().find('p').text(response.data.message);
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
            }

        });

        // *** EDIT COMMENT

        // Switch edit mode
        $('body').on('click', '.wp-annotations--dashboard button.edit', function() {
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

        // Cancel edit mode
        $('body').on('click', '.wp-annotations--dashboard button.cancel', function(event) {
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

        // Update comment
        $('body').on('submit', '.wp-annotations--dashboard .comment-item__content-form', function(event) {
            event.preventDefault();
            $par = $(this).closest('.comment-item');
            $default = $par.find('.comment-item__content p').text();
            $textarea = $par.find('textarea').val();
            $commentID = $(this).closest('.comment-item').data('comment-id');

            if( $default != $textarea ){
                $('#wp-annotations--dashboard').addClass('ajax');
    
                var datas = {
                    action: 'update_wp_annotation',
                    type: 'update',
                    id: $commentID,
                    comment: $textarea,
                    view: $('.wp-annotations--dashboard__comments').hasClass('active') ? 'active' : 'resolved'
                };                       
    
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: datas,
                    success: function(response) {
                        if (response.success) {
                            $('#wp-annotations--notices').addClass('success').show().find('p').text(response.data.message);
                            $('#wp-annotations--dashboard').removeClass('ajax');
                            $('#wp-annotations--refresh-box').html(response.data.comments_content);
    
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);                        
                        } else {
                            $('#wp-annotations--notices').addClass('error').show().find('p').text(response.data.message);
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
            }
            else{
                $par.removeClass('edit');
                $par.find('.comment-item__content').show().next().hide().find('textarea').val($default);
            }

        });


        // *** FUNCTIONS
        function switchCommentsBrowse(){
        }
    });
})(jQuery);