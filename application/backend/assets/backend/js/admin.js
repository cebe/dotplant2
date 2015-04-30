/* global $, bootbox */
/* jshint -W097 */

"use strict";

var Admin = function() {

	};

Admin.prototype = {
	constructor: Admin

};

Admin.makeSlug = function (selectorsFrom, selectorTo){
	if (selectorsFrom instanceof Array === false) {
		selectorsFrom = [selectorsFrom];
	}
	var valueFrom = '';
	for (var i=0,ii=selectorsFrom.length; i<ii; i++){
		var val=$(selectorsFrom[i]).val().trim();
		if (val.length){
			valueFrom = val;
			break;
		}
	}
	if (valueFrom.length) {
		$.ajax({
		
			url: "/backend/dashboard/make-slug",
			type: "GET",
			cache: false,
			dataType: 'json',
			data: {
				'word': valueFrom
			}

		}).done(function(data){
			
			$(selectorTo).val(data);

		}).fail(function(jqXHR, textStatus){
			
			$.pnotify({
			    title: 'Error',
			    text: jqXHR.responseText,
			    type: 'error',
			    history: false
			});

		});
	}
};

Admin.copyFrom = function(selectorsFrom, selectorTo) {
	if (selectorsFrom instanceof Array === false) {
		selectorsFrom = [selectorsFrom];
	}
	var valueFrom = '';
	for (var i=0,ii=selectorsFrom.length; i<ii; i++){
		var val=$(selectorsFrom[i]).val().trim();
		if (val.length){
			valueFrom = val;
			break;
		}
	}
	if (valueFrom.length) {
		$(selectorTo).val(valueFrom);
	}
};

$(function () {
    $('.ajax-notifications').on('click', '[data-type="new-notification"]', function () {
        var $link = $(this);
        $.ajax({
            'data' : {
                'id' : $link.data('id')
            },
            'success' : function (data) {
                $link.parents('.unread').eq(0).removeClass('unread');
                $link.remove();
                var $nc = $('.notifications-count');
                $nc.text($nc.text() - 1);
            },
            'url' : '/backend/dashboard/mark-notification'
        });
        return false;
    }).on('click', '.show-more a', function () {
        var $link = $(this);
        var id = $link.data('id');
        var $list = $link.parents('ul').eq(0);
        $link.parent().remove();
        $.ajax({
            'data' : {
                'id' : id
            },
            'success' : function (data) {
                var $nc = $('.notifications-count');
                $(data).find('li').each(function () {
                    var $this = $(this);
                    if ($list.find('li[data-id=' + $this.data('id') + ']').length === 0) {
                        $this.appendTo($list);
                        if ($this.find('.unread').length === 1) {
                            $nc.text($nc.text() + 1);
                        }
                    }
                });
            },
            'url' : '/backend/dashboard/notifications'
        });
        return false;
    });
});

$(function(){
    $('[data-toggle="popover"]').popover({
        container: 'body'
    });
    $('[data-toggle="tooltip"]').tooltip();

    jQuery('[data-toggle="tooltip"]').tooltip();
    jQuery('[data-action="delete"]').on('click', function(e) {
        jQuery('#delete-confirmation').attr('data-url', jQuery(this).attr('href')).attr('data-items', '').modal('show');
        return false;
    });
    jQuery('#delete-confirmation [data-action="confirm"]').click(function() {
        var $modal = jQuery(this).parents('.modal').eq(0);
        var data =  typeof($modal.attr('data-items')) == "string" && $modal.attr('data-items').length > 0
            ? {'items': $modal.attr('data-items').split(',')}
            :{};
        jQuery.ajax({
            'url': $modal.attr('data-url'),
            'type': 'post',
            'data': data,
            'success': function (data) {
                location.reload();
            }
        });
        return true;
    });
    jQuery('body').on('click', '[data-action="delete-category"]', function() {
        jQuery('#delete-category-confirmation').attr('data-url', jQuery(this).attr('href')).attr('data-items', '').modal('show');
        return false;
    });
    jQuery('#delete-category-confirmation [data-action="confirm"]').click(function() {
        var $modal = jQuery(this).parents('.modal').eq(0);
        var data =  typeof($modal.attr('data-items')) == "string" && $modal.attr('data-items').length > 0
            ? {'items': $modal.attr('data-items').split(',')}
            :{};
        jQuery.ajax({
            'url': $modal.attr('data-url') + '&mode=' + jQuery('#delete-mode').val(),
            'type': 'post',
            'data': data,
            'success': function (data) {
                location.reload();
            }
        });
        return true;
    });
});