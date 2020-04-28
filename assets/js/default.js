/**
 * AJAX calls
 */
(function($){

        /**
         * Refresh handler
         *
         * @type {number}
         */
    var refresher = 0,

        /**
         * If new translations are there
         *
         * @type {boolean}
         */
        edited = false,

        /**
         * If user is tracked
         *
         * @type {number}
         */
        tracked = 0,

        /**
         * Current items type
         *
         * @type {string}
         */
        type = 'posts',

        /**
         * Current pagination parameter
         *
         * @type {number}
         */

        page = 1,

        /**
         * Tracking option
         *
         * @returns {boolean}
         */
        tracking = function(){
            return ( ! sinatra.options.track_me || ! ( edited && ! confirm( sinatra.warn ) ) );
        },

        /**
         * Postponed refresh
         */
        schedule_refresh = function(){
            if( refresher )
                clearTimeout( refresher );
            refresher = setTimeout( refresh, 750 );
        },

        /**
         * Assign actions
         */
        assign = function(){
            edited = false;
            tracked = 15;
            $( '#translate_object' ).off().change( function(){
                page = 1;
                refresh();
            } );
            if( $( '#search' ).off().on( 'keyup change', function(){
                if( $(this).val().length > 2 ){
                    page = 1;
                    schedule_refresh();
                    $( '.search-reset' ).show();
                }
            } ).val().length )
                $( '.search-reset' ).show();
            $('.search-reset').off().click( function(){
                $( '#search' ).val( '' );
                $(this).hide();
                refresh();
            } );
            $( '.paging:not(.inactive)' ).off().click( function(){
                page = $( this ).attr( 'value' );
                refresh();
            } );
            $( '.current-page' ).off().on( 'change keyup', function(){
                var val = Number( $(this).val() );
                if( ! val || val < 1 || val > Number( $(this).attr('max') ) ) return;
                page = val;
                schedule_refresh();
            } );
            $( '#cb-select-all-1' ).off().click( function(){
                $( '.item-id' ).prop( 'checked', $(this).prop( 'checked' ) );
            } );
            $( '.sinbutt' ).off().click( function( e ){
                refresh({ action: $( this ).data( 'action' ), ids: [ $( this ).val() ] });
                e.stopPropagation();
                e.preventDefault();
                return false;
            } );
            $( '.sinatra-bulk input[type=submit]' ).off().click( function( e ){
                var ids = [];
                $( 'input.item-id:checked' ).each( function(){
                    ids.push( this.value );
                } );
                if( ids.length )
                    refresh({ action: $( this ).parents('.sinatra-bulk').first().find( 'select' ).val(), ids: ids });
                e.stopPropagation();
                e.preventDefault();
                return false;
            } );
            $( '.sinatra-revise' ).off().click( revise_translation );
        },

        /**
         * Save translation after revision
         */
        revise_assign = function(){
            $( '#revision-set, #revision-save' ).off().click( function( e ){
                var post = {};
                $('.editable').each( function(){
                    post[ $( this ).attr( 'name' ) ] = $( this ).text();
                } );
                mitsbox.hide( '#revise-popup' );
                var action = ( this.id === 'revision-set' ? 'set_revised' : 'save_revised' );
                refresh({ action: action, ids: [ $(this).val() ], post: post });
                e.stopPropagation();
                e.preventDefault();
                return false;
            });
        },

        /**
         * Revise translation
         *
         * @param e
         * @returns {boolean}
         */
        revise_translation = function(e){
            $.ajax({
                url: ajaxurl,
                data: {
                    action  : sinatra.action,
                    do      : 'revise',
                    nonce   : sinatra.nonce,
                    id      : $( this ).val(),
                    type    : $( '#translate_object' ).val()
                },
                dataType: 'json',
                type: 'post',
                success: function ( data ) {
                    if( data.error )
                        alert( data.error );
                    else {
                        $('#revise-popup').html( data.result );
                        mitsbox.init( '#revise-popup', true );
                        mitsbox.show( '#revise-popup' );
                        $( '.revision pre' ).off().on( 'keyup mouseup', match_it );
                        revise_assign();
                    }
                },
                error: function( a,b,e ){
                    if( e ) alert( e );
                }
            });
            e.stopPropagation();
            e.preventDefault();
            return false;
        },

        /**
         * Put the matcher triangle to proper position
         */
        match_it = function(){
            var origin = $( this ).parents( 'tr' ).find( 'td.original pre:eq(' + $( this ).index() + ')' );
            if( 'undefined' === typeof origin || ! origin.length )
                origin = $( this );
            $( '#matcher_origin' ).offset( {
                top: origin.offset().top
            } );
            $( '#matcher_translate' ).offset( {
                top: $( this ).offset().top
            } );
        },

        /**
         * Shade the list
         *
         * @param on
         */
        shady = function( on ){
            if( 1 === on )
                $( '.sinatra-content' ).block({
                    message: null,
                    overlayCSS:{
                        background: '#fff',
                        opacity: 0.4
                    }
                });
            else
                $( '.sinatra-content' ).unblock();
        },

        /**
         * Refresh the list
         */
        refresh = function( pre_action ){
            var tobj = $( '#translate_object' );
            if( ! tracking() ) {
                tobj.val( type );
                return;
            }
            type = tobj.val();
            shady( 1 );
            $.ajax({
                url: ajaxurl,
                data: {
                    action  : sinatra.action,
                    do      : 'fetch',
                    pre     : pre_action,
                    page    : page,
                    nonce   : sinatra.nonce,
                    search  : $( '#search').val(),
                    type    : type,
                    tracked : tracked
                },
                dataType: 'json',
                type: 'post',
                success: function ( data ) {
                    if( data.error )
                        alert( data.error );
                    else {
                        $('.sinatra-content').html(data.result);
                        assign();
                    }
                },
                complete: shady,
                error: function( a,b,e ){
                    if( e && e !== 'Bad Request' ) return alert( e );
                    console.log( e );
                    window.location.reload( true );
                }
            });
        },

        /**
         * Save options
         *
         * @param e
         * @returns {boolean}
         */
        save_options = function( e ){
            var btn = $(this);
            var frm = $(this).parents('form');
            frm.block({
                message: null,
                overlayCSS:{
                    background: '#fff',
                    opacity: 0.4
                }
            });
            frm.find('input, select').each( function(){
                if( 'checkbox' === $(this).attr('type') && ! this.checked ) sinatra.options[ this.name ] = '';
                else sinatra.options[ this.name ] = $(this).val();
            } );
            $.ajax({
                url: ajaxurl,
                data: {
                    action  : sinatra.action,
                    do      : 'save_options',
                    options : sinatra.options,
                    nonce   : sinatra.nonce
                },
                dataType: 'json',
                type: 'post',
                success: function ( data ) {
                    if( data.error ) {
                        btn.removeClass( 'options-saved' ).addClass( 'options-not-saved' );
                        alert(data.error);
                    } else {
                        btn.removeClass( 'options-not-saved' ).addClass( 'options-saved' );
                    }
                },
                complete: function(){
                    frm.unblock();
                },
                error: function( a,b,e ){
                    btn.removeClass( 'options-saved' ).addClass( 'options-not-saved' );
                    alert( e );
                }
            });
            e.stopPropagation();
            e.preventDefault();
            return false;
        };

    return {
        /**
         * Initialize
         */
        init: function(){
            $( document ).ready( function(){
                $( '.wp-sinatra-options input, .wp-sinatra-options select' ).off().on( 'change', save_options );
                refresh();
            } );
        }
    }
})(jQuery).init();