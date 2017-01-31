jQuery(document).ready(function ($) {

    $(".ops-table").tablesorter();

    $('body').on('click', 'a.ops-show-keyword-list', function (e) {
        e.preventDefault();
        if ($('.ops-keyword-list').is(':visible') === false) {
            $('.ops-keyword-list').slideDown();
        } else {
            $('.ops-keyword-list').slideUp();
        }
    });

    setTimeout(function () {
        $('.ops-updated').slideUp();
    }, 2000);

    $('.datepicker').datepicker();

    $('body').on('click', '.ops-edit-single-backlink', function (e) {
        e.preventDefault();
        var wrapper = $(this).closest('.ops-single-backlink-wrapper');
        if ($(wrapper).hasClass('active') === false) {
            $(wrapper).addClass('active').find('.ops-edit-single-backlink-box').slideDown();
        } else {
            $(wrapper).removeClass('active').find('.ops-edit-single-backlink-box').slideUp();
        }
    });

    //SETTINGS
    $('#ops-modules .ops-module a').on('click', function (e) {
        e.preventDefault();
        if ($(this).next('input').val() == '1') {
            // deactivate
            $(this).next('input').val('0');
        } else {
            // activete
            $(this).next('input').val('1');
        }
        $('input[type=submit]').trigger('click');
        return false;

    });


    // SETTINGS - post types on share counter
    $('body, html').on('click', '.ops-all-post-types .ops-post-type', function () {
        var type = $(this).data('pt');
        $(this).remove();
        $('form input[name=ops_all_shares_checked]').val('0');
        $('form input[name=ops_share_timer]').val('0');
        $('.ops-selected-post-types').append('<div class="ops-post-type" data-pt="' + type + '"><input type="hidden" value="' + type + '" name="core[post_types][]" />' + type + '</div>');
    });

    // settings select post type
    $('body, html').on('click', '.ops-selected-post-types .ops-post-type', function () {
        var type = $(this).data('pt');
        $(this).remove();
        $('.ops-all-post-types').append('<div class="ops-post-type" data-pt="' + type + '">' + type + '</div>');
    });


    // meta box
    $('body').on('click', '.ops-keyword-wrapper a.ops-edit-kw', function (e) {
        e.preventDefault();
        var settings = $(this).closest('.ops-keyword-wrapper').find('.ops-keyword-setting');
        if (settings.is(':visible') == false) {
            settings.slideDown();
        } else {
            settings.slideUp();
        }
    });

    // remove keyword in metabox only
    $('body').on('click', '#ops-main-meta-box .ops-keyword-wrapper a.ops-delete-kw', function (e) {
        e.preventDefault();
        $(this).closest('.ops-keyword-wrapper').slideUp('slow', function () {
            $(this).remove();
        });
    });

    // remove keyword from dashboard
    $('body').on('click', '#ops-dashboard .ops-keyword-wrapper a.ops-delete-kw', function (e) {
        e.preventDefault();
        var button = $(this);

        var r = confirm("Really delete keyword?");
        if (r == true) {
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'ops_dashboard_delete_kw',
                    kwid: $(this).closest('.ops-keyword-wrapper').data('kwid')
                },
                success: function (data) {
                    $(button).closest('.ops-keyword-wrapper').slideUp('slow', function () {
                        $(this).remove();
                    });
                },
                error: function () {
                    $(this).closest('.ops-keyword-wrapper').html('error occured');

                }
            });
        }

    });

    // settings controls
    $('body').on('click', 'a#ops-delete-inactive-keywords, a#ops-forget-authorization, a#ops-run-reciprocal-check, a#ops-run-rank-report', function (e) {
        var r = confirm("Really do this action?");
        if (r == true) {
            // go ahead
        } else {
            e.preventDefault();
        }
    });

    // save keyword from dashboard
    $('body').on('submit', '#ops-dashboard form.ops-dashboard-update-kw', function (e) {
        e.preventDefault();
        var button = $(this);
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_dashboard_update_kw',
                kwid: $(this).closest('.ops-keyword-wrapper').data('kwid'),
                searches: $(this).closest('.ops-keyword-wrapper').find('input[name=searches]').val(),
                pid: $(this).closest('.ops-keyword-wrapper').find('input[name=post_id]').val()
            },
            success: function (data) {
                $(button).append('<span class="ops-saved">Saved</span>');
                setTimeout(function () {
                    $(button).closest('.ops-keyword-setting').slideUp();
                    $('body').find('.ops-saved').fadeOut();
                }, 200);
            },
            error: function () {
                $(this).closest('.ops-keyword-wrapper').html('error occured');

            }
        });

    });
    // ops sortable
    $('.ops-sortable-metabox').sortable({
        handle: '.ops-move-kw'
    });

    $('.ops-sortable-dashboard').sortable({
        handle: '.ops-move-kw',
        update: function (event, ui) {
            var positions = [];
            $('#ops-dashboard .ops-keyword-wrapper').each(function () {
                positions.push($(this).data('kwid'));
            });

            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'ops_update_dashboard_master_sort',
                    positions: JSON.stringify(positions)
                },
                success: function (data) {
                    $('.ops-dashboard-right').before(data);
                },
                error: function () {
                }
            });
        }
    });

    $('body').on('keyup', '.ops-keyword-wrapper .ops-change-text', function () {
        var content = $(this).val();
        var field = $(this).data('field');
        var keyword = $(this).closest('.ops-keyword-wrapper');
        keyword.find('.' + field).html(content);
    });


    // add new form ajax
    $('body').on('click', '#ops-tab-rank-report .ops-add-new-keyword', function (e) {
        e.preventDefault();
        var clickButton = $(this);
        var total_kws = $(this).closest('#ops-tab-rank-report').data('total');
        var pid = $(this).closest('#ops-tab-rank-report').data('pid');
        var preloaderUrl = $(this).closest('#ops-tab-rank-report').data('preloader');

        var permalink = '';
        if ($('body').find('#sample-permalink').length) {
            var samplePermalink = $('body').find('#sample-permalink');
            if (typeof samplePermalink[0].innerText !== 'undefined') {
                permalink = samplePermalink[0].innerText;
            } else {
                permalink = '';
            }
        }

        var keyword = '';
        if ($('body').find('#sample-permalink').length) {
            var pageTitle = $('body').find('input#title').val();
            if (typeof pageTitle !== 'undefined') {
                keyword = pageTitle.toLowerCase();
            } else {
                keyword = '';
            }
        }

        // udpate total keywords
        $(this).closest('#ops-tab-rank-report').data('total', total_kws + 1);

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_insert_new_kw_to_metabox',
                total_kws: total_kws,
                pid: pid,
                permalink: permalink,
                keyword: keyword
            },
            beforeSend: function () {
                $('body').find('#ops-tab-rank-report').append('<img src="' + preloaderUrl + '" id="ops-preloader">')
            },
            success: function (data) {
                $(clickButton).before(data);
                $('body').find('#ops-preloader').fadeOut(function(){
                    $(this).remove();
                });

                if(clickButton.hasClass('ops-hide-when-used')){
                    $(clickButton).after('<div class="ops-sad-premium-message">You can only track one keyword in free version.</div>');
                    $(clickButton).hide();
                }
            },
            error: function () {
                alert('Something went wrong, please contact plugin author.');
            }
        });
    });

    // save keyword from dashboard
    $('body').on('submit', '#ops-dashboard .ops-edit-single-backlink-box form', function (e) {
        e.preventDefault();
        var button = $(this);
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_update_backlink_keyword',
                blid: $(this).find('input[name=blid]').val(),
                url: $(this).find('input[name=url]').val(),
                type: $(this).find('select[name=type] option:selected').val(),
                price: $(this).find('input[name=price]').val(),
                price_monthly: $(this).find('input[name=price_monthly]').val(),
                keyword_id: $(this).find('select[name=keyword_id] option:selected').val(),
                reciprocal_check: $(this).find('input[name=reciprocal_check]').attr('checked'),
                comment: $(this).find('input[name=comment]').val(),
                start_date: $(this).find('input[name=start_date]').val(),
                contact: $(this).find('input[name=contact]').val()
            },
            success: function (data) {
                $(button).append(data);
                $(button).append('<span class="ops-saved">Saved</span>');
                setTimeout(function () {
                    $(button).closest('.ops-edit-single-backlink-box').slideUp();
                    $('body').find('.ops-saved').remove();
                    $(button).closest('.ops-single-backlink-wrapper').removeClass('active');
                }, 500);
            },
            error: function () {
                $(this).closest('.ops-keyword-wrapper').html('error occured');

            }
        });

    });

    $('#ops-dashboard .ops-add-new-backlink').on('click', function (e) {
        e.preventDefault();
        if ($('.ops-add-new-backlink-form').is(':visible') === false) {
            $('.ops-add-new-backlink-form').slideDown();
        } else {
            $('.ops-add-new-backlink-form').slideUp();
        }

    });

    // delete backlink
    $('body').on('click', '.ops-edit-single-delete', function (e) {
        e.preventDefault();
        var r = confirm("Really delete backlink?");
        if (r == true) {
            var blwrapper = $(this).closest('.ops-single-backlink-wrapper');
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'ops_delete_bl',
                    blid: $(this).data('blid')
                },
                success: function (data) {
                    $(blwrapper).slideUp();
                },
                error: function () {
                }
            });
        }
    });

    $('input.ops-reciprocal-trigger').on('click', function () {
        var reciprocalSettings = $(this).closest('.postbox').find('.ops-reciprocal-settings');
        if (reciprocalSettings.hasClass('active') === false) {
            reciprocalSettings.slideDown();
            reciprocalSettings.addClass('active');
        } else {
            reciprocalSettings.slideUp(function () {
                reciprocalSettings.removeClass('active');
            });

        }
    });

    $('.ops-show-keyword-backlinks').on('click', function (e) {
        e.preventDefault();
        var backlinks = $(this).closest('.ops-keyword-wrapper').find('.ops-keyword-backlinks');
        if ($(backlinks).is(':visible') === false) {
            $(backlinks).slideDown();
        } else {
            $(backlinks).slideUp();
        }
    });

    // opportunities - comment ajax
    $("#ops-opportunity-comment form").on('submit', function(e){
        e.preventDefault();
        var keyword = $(this).find('input#keyword').val();
        var language = $(this).find('input#language').val();
        var preloaderUrl = $(this).data('preloader');
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_comment_ideas',
                keyword: keyword,
                language: language,
            },
            beforeSend: function () {
                $('body').find('#ops-opportunity-comment #ops-comment-output').prepend('<img src="' + preloaderUrl + '" id="ops-preloader">')
            },
            success: function (data) {
                $('body').find('#ops-comment-output').html(data);
                $('body').find('#ops-preloader').fadeOut(function(){
                    $(this).remove();
                });
            },
            error: function () {
                alert('Something went wrong, please contact plugin author.');
            }
        });
    });
});
