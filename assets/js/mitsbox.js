var mitsbox = (function($){
    var sel = '.mitsbox',
        css = '<style>.mitsbox{position:fixed;width:100%;height:100%;left:0;top:0;right:0;bottom:0;z-index:100002;display:none;vertical-align:middle;opacity:0;transition:opacity ease-in-out 250ms;}.mitsbox .shade{position:fixed;left:0;top:0;width:100%;height:100%;opacity:.7;filter:alpha(opacity=70);background:#000;z-index:1;}.mitsbox .close-but{position:absolute;display:block;padding:0;width:16px;height:16px;right:22px;top:14px;z-index:2;background:transparent;border:none;cursor:pointer;transition:all ease-in-out 300ms;}.mitsbox .close-but:hover{transform:scale( 1.5 );}.mitsbox .close-but:before,.mitsbox .close-but:after{content:\'\';position:absolute;height:1px;width:100%;top:50%;left:0;margin-top:-1px;background:#000;}.mitsbox .close-but:before{-webkit-transform:rotate(45deg);-ms-transform:rotate(45deg);transform:rotate(45deg);}.mitsbox .close-but:after{-webkit-transform:rotate(-45deg);-ms-transform:rotate(-45deg);transform:rotate(-45deg);}.mitsbox .hover{position:absolute;z-index:2;padding:0;width:800px;height:500px;top:50%;left:50%;margin-left:-400px;margin-top:-250px;max-width:100%;border-radius:10px;background-color:#ffffff;border:1px solid #979797;overflow:hidden;}.mitsbox .hover .content{position:relative;height:100%;width:100%;margin:0;padding:0;overflow-y:auto;}@media screen and ( max-height:505px ){.mitsbox .hover{height:calc( 100% - 10px );top:0;margin-top:5px;position:absolute;}}@media screen and ( max-width:810px ){.mitsbox .hover{width:calc( 100% - 10px );margin-left:5px;left:0;}}@media screen and ( max-width:768px ){.mitsbox .hover{width:440px;margin-left:-220px;left:50%;}}@media screen and ( max-width:450px ){.mitsbox .hover{width:calc( 100% - 10px );margin-left:5px;left:0;}}</style>',
        top = 0,
        scroll = {
            disable: function () {
                top = $( window ).scrollTop();
                $( 'html, body' ).css( 'overflow', 'hidden' );
            },
            enable: function () {
                $( 'html, body' ).css( 'overflow', 'inherit' );
                $( window ).scrollTop( top );
            }
        },
        form = {
            show : function(){
                $( sel ).css( 'display', 'block' );
                scroll.disable();
                setTimeout( function(){
                    $( sel ).css( 'opacity', '1' );
                }, 50 );
            },
            hide : function(){
                $( sel ).css( 'opacity', '0' );
                setTimeout( function(){
                    $( sel )
                        .css( 'display', 'none' );
                    scroll.enable();
                }, 300 );
            }
        },
        assign_events = function(){
            var activator = $( sel ).data( 'activator' );
            var event = $( sel ).data( 'event' );
            if( activator && event )
                $( activator ).off().on( event, form.show );
            $( sel + ' .close-but,' + sel + ' .shade' ).on( 'click', form.hide );
        };

    $( 'head' ).append( css );

    return {
        init : function( selector, maximized ){
            if( selector )
                sel = selector;
            else sel = $(this).id;
            $( sel ).find( '.close, .close-but' ).remove();
            var content = $( sel ).html();
            $( sel )
                .html( '' )
                .append( '<div class="shade"></div>' )
                .append(
                    '<div class="hover">' +
                    '<span class="close-but"></span>' +
                    '<div class="content">' +
                    content +
                    '</div>' +
                    '</div>')
                .addClass( 'mitsbox mitsbox-initiated' );
            assign_events();
            if( maximized )
                $( sel )
                    .find( '.hover' )
                    .css( 'min-width', 'calc( 100% - 20px )' )
                    .css( 'min-height', 'calc( 100% - 20px )' )
                    .css( 'border-radius', '3px' )
                    .css( 'top', '10px' )
                    .css( 'bottom', '10px' )
                    .css( 'left', '10px' )
                    .css( 'right', '10px' )
                    .css( 'margin-left', '0' )
                    .css( 'margin-top', '0' );
        },
        show : function( selector ){
            if( selector )
                sel = selector;
            else
                sel = $(this).id;
            form.show();
        },
        hide : function( selector ){
            if( selector )
                sel = selector;
            else
                sel = $(this).id;
            form.hide();
        }
    }
})(jQuery);