(function($) {
    $(document).ready(function() {
        $margin = 350;
        $laptop = 1280;
        $tablet = 1024;
        $mobile = 768; 
        $modal = $('#wp-annotations--modal');    
        var quality = parseFloat(datas.quality);
        

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

            if( $winWidth <= $tablet && $winWidth > $mobile ){
                $('html').removeClass('laptop mobile').addClass('tablet');
            }
            else if( $winWidth <= $mobile ){
                $('html').removeClass('laptop tablet').addClass('mobile');
            }
            else{
                $('html').removeClass('tablet mobile').addClass('laptop');
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
        
            if($(window).width() - event.pageX < 300 && $(window).height() - event.pageY < 200){
                $modal.find('form').css('transform', 'translate(-270px, calc(-100% - 55px))');
            }
            else if ($(window).width() - event.pageX < 300) {
                $modal.find('form').css('transform', 'translateX(-270px)');
            } 
            else if($(window).height() - event.pageY < 200){
                console.log('height');
                $modal.find('form').css('transform', 'translateY(calc(-100% - 55px))');
            }
            else {
                $modal.find('form').css('transform', 'none');
            }
        });        

        $('body').on('click', '#wp-annotations--modal', function(event) {
            event.stopPropagation();
        });

        // *** LIGHTBOX
        $('body').on('click', '.wp-annotations .expend', function() {
            var src = $(this).next().attr('src');
            $('body').addClass('no-scroll');

            $('#wp-annotations--lightbox').fadeIn().find('img.lightbox-img').attr('src', src);
        });

        $('body').on('click', '#wp-annotations--lightbox .close-light-button', function() {
            $('#wp-annotations--lightbox').fadeOut();
            $('body').removeClass('no-scroll');
        });


        // *** DASHBOARD ***
        // Switch device
        $('body').on('click', '.wp-annotations--dashboard__devices button', function() { 
            if( !$(this).hasClass('disabled') ){
                $device = $(this).data('device');
                $('#wp-annotations--dashboard').addClass('ajax');
        
                $.ajax({
                    url: ajaxurl.url,
                    type: 'POST',
                    data: {
                        action: 'wp_annotation_device',
                        device: $device,
                        view: $('.wp-annotations--dashboard__comments').hasClass('active') ? 'active' : 'resolved'
                    },
                    success: function(response) {
                        if (response.success) {                        
                            $('#wp-annotations--dashboard').removeClass('ajax');
                            $('#wp-annotations--refresh-box').html(response.data.comments_content);
        
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);
                        } else {
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
                device: $('.wp-annotations--dashboard__devices').data('device'),
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
                scale: quality,
                scrollX: 0,
                scrollY: 0,
                width: window.innerWidth,
                height: window.innerHeight, 
                x: window.scrollX,
                y: window.scrollY
            }).then(function(canvas) {
                var screenshot = canvas.toDataURL('image/png', quality); // Convertir en base64

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

        $('#wp-annotation-form').on('reset', function(event) {
            $('#wp-annotations--modal').hide().find('textarea').val('').siblings('.mention-list-main').hide();
        })

        $('#wp-annotation-form textarea').on('keydown', function(event) {
            // if (event.key === "Enter") { 
            //     event.preventDefault();
            //     $('#wp-annotation-form').submit();
            // }
            // else 
            if( event.key === "Escape" ){
                $('#wp-annotations--modal').hide().find('textarea').val('').siblings('.mention-list-main').hide();
            }
        });

        $('body').on('keyup', '#wp-annotation-form textarea', function(event) {
            $textarea = $(this);
            $mentionList = $textarea.closest('form').find('#mention-list-main');
            var cursorPos = this.selectionStart;
            var text = $textarea.val().substring(0, cursorPos);
            var match = text.match(/@(\w*)$/);
    
            if (match) {
                $mentionList.slideDown(250);
            } else {
                $mentionList.slideUp(250);
            }
        });

        $('body').on('click', function(e) {
            if (!$(e.target).closest('#mention-list-main, #wp-annotation-form textarea').length) {
                $(this).closest('form').find('#mention-list-main').slideUp(250);
            }
        });

        $('body').on('click', '.mention-list-main__item', function() {            
            $this = $(this);
            $textarea = $this.closest('form').find('textarea');
            $mentionList = $this.closest('form').find('#mention-list-main');
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

            $(this).closest('form').append($input);
            
            var newText = beforeCursor.replace(/@(\w*)$/, '@' + username + ' ') + afterCursor;
            $textarea.val(newText);
            $mentionList.slideUp(250);
            $textarea.focus();
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
                    device: $('.wp-annotations--dashboard__devices').data('device'),
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
                    device: $('.wp-annotations--dashboard__devices').data('device'),
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

        // *** REPLIES
        // Open reply
        $('body').on('click', '.open-add-comments', function() {            
            $par = $(this).closest('.comment-item');
            $commentID = $par.data('comment-id');

            $('body').addClass('no-scroll');
            $('#wp-annotations--replies').addClass('ajax').fadeIn(300);    
    
            var datas = {
                action: 'open_reply_wp_annotation',
                id: $commentID,
            };                    
    
            $.ajax({
                url: ajaxurl.url,
                type: 'POST',
                data: datas,
                success: function(response) {
                    if (response.success) {
                        $('#wp-annotations--replies').removeClass('ajax');  
                        $('#wp-annotations-replies-display').html(response.data.reply_content);                     
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#wp-annotations--notices').addClass('error').show().find('p').text('Une erreur s\'est produite');
                    $('#wp-annotations--replies').removeClass('loading');     
                }
            });
        });
        
        // Add reply
        $('body').on('submit', '#reply-box-form', function(event) {
            event.preventDefault();
            $this = $(this);

            if( $(this).find('textarea').val() != '' ){
                $par = $(this).closest('.reply-box');
                $par.addClass('ajax');

                var commentID = $par.data('comment-id');
                var userID = $par.data('user-id');
                var commentText = $this.find('textarea').val();
                var notifyEmail = $this.find('input[name="email"]').is(':checked') ? 1 : 0;

                var targetsEmail = [];

                $(this).find('input[name="targets_email[]"]').each(function() {                    
                    targetsEmail.push($(this).val());
                });

                targetsEmail = [...new Set(targetsEmail)];              

                var formData = new FormData();
                formData.append('action', 'wp_annotation_replies');
                formData.append('status', 'add');
                formData.append('comment_id', commentID);
                formData.append('user_id', userID);
                formData.append('comment_text', commentText);
                formData.append('notify_email', notifyEmail);
        
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
                            $par.find('textarea, input[name="email"]').val('');
                            $par.removeClass('ajax');
                            $('#wp-annotations--notices').addClass('success').show().find('p').text(response.data.message);
                            $('#reply-box-content').html(response.data.reply_content);
                            refreshDashboard();
        
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);
                        } else {
                            $('#wp-annotations--notices').addClass('error').show().find('p').text(response.data.message);
                            $par.removeClass('ajax');
        
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#wp-annotations--notices').addClass('error').show().find('p').text('Une erreur s\'est produite');
                        $par.removeClass('ajax');
    
                        setTimeout(function() {
                            $('#wp-annotations--notices').fadeOut(function(){
                                $(this).removeClass('error success');
                            });
                        }, 2000);
                    }
                });
            }
            else{
                alert('Veuillez saisir un commentaire');
            }

        });

        // Delete reply
        $('body').on('click', '#wp-annotations--replies .delete', function() {            
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
                        action: 'wp_annotation_replies',
                        status: 'delete',
                        datas: datas
                    },
                    success: function(response) {
                        if (response.success) {
                            $par.find('textarea, input[name="email"]').val('');
                            $par.removeClass('ajax');
                            $('#wp-annotations--notices').addClass('success').show().find('p').text(response.data.message);
                            $('#reply-box-content').html(response.data.reply_content);
                            refreshDashboard();
        
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);
                        } else {
                            $('#wp-annotations--notices').addClass('error').show().find('p').text(response.data.message);
                            $par.removeClass('ajax');
        
                            setTimeout(function() {
                                $('#wp-annotations--notices').fadeOut(function(){
                                    $(this).removeClass('error success');
                                });
                            }, 2000);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#wp-annotations--notices').addClass('error').show().find('p').text('Une erreur s\'est produite');
                        $par.removeClass('ajax');
    
                        setTimeout(function() {
                            $('#wp-annotations--notices').fadeOut(function(){
                                $(this).removeClass('error success');
                            });
                        }, 2000);
                    }
                });
            }
        });
        
        // Close reply
        $('body').on('click', '#wp-annotations--replies .close-replies', function() {
            $('body').removeClass('no-scroll');
            $('#wp-annotations--replies').fadeOut(300);
        });

        // Replies mentions
        $('body').on('keyup', '#reply-box-form textarea', function(event) {
            $textarea = $(this);
            $mentionList = $textarea.closest('form').find('#mention-list');
            var cursorPos = this.selectionStart;
            var text = $textarea.val().substring(0, cursorPos);
            var match = text.match(/@(\w*)$/);
    
            if (match) {
                $mentionList.slideDown(250);
            } else {
                $mentionList.slideUp(250);
            }
        });        
        
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#mention-list, #reply-box-form textarea').length) {
                $(this).closest('form').find('#mention-list').slideUp(250);
            }
        });

        $(document).on('click', '.mention-list__item', function() {
            $this = $(this);
            $textarea = $this.closest('form').find('textarea');
            $mentionList = $this.closest('form').find('#mention-list');
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

            $(this).closest('form').append($input);
            
            var newText = beforeCursor.replace(/@(\w*)$/, '@' + username + ' ') + afterCursor;
            $textarea.val(newText);
            $mentionList.slideUp(250);
            $textarea.focus();
        });

        // Replies input file
        $('body').on('click', '#reply-box-form .file-input .unfiled', function() {            
            $par = $(this).closest('.file-input');
            $par.find('input').click();
        });

        $('body').on('change', '#reply-box-form .file-input input', function() {
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

        $('body').on('click', '#reply-box-form .file-input .filed .clear', function() {
            $par = $(this).closest('.file-input');
            $par.find('.filed').fadeOut(150, function(){
                $par.find('.unfiled').fadeIn(150);
            });
            $par.find('input').val('');
        });

        $('body').on('click', '#reply-box-form .file-input .filed .clear', function() {
            var $fileItem = $(this).closest('.file-item');
            var fileName = $fileItem.find('.text').text();
            $fileItem.fadeOut(150, function() {
                $fileItem.remove();
                selectedFiles = selectedFiles.filter(file => file.name !== fileName);
                updateFileInput();
                if ($('.filed-files').children().length === 0) {
                    $('.unfiled').fadeIn(150);
                }
            });
        });

        // *** FUNCTIONS
        function refreshDashboard(){
            $('#wp-annotations--dashboard').addClass('ajax');
    
            var datas = {
                action: 'update_wp_annotation',
                type: 'refresh',
                view: 'active'
            };                       

            $.ajax({
                url: ajaxurl.url,
                type: 'POST',
                data: datas,
                success: function(response) {
                    if (response.success) {
                        $('#wp-annotations--dashboard').removeClass('ajax');
                        $('#wp-annotations--refresh-box').html(response.data.comments_content);                        
                    } else {
                        $('#wp-annotations--notices').addClass('error').show().find('p').text('Une erreur s\'est produite');
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
})(jQuery);