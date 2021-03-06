/**
 * Created with JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 01.07.13
 * Time: 13:31
 * To change this template use File | Settings | File Templates.
 */

$(function(){
    updateCustodianQuestionsBlock();

    $('input:radio[name="ria_custodian[custodian]"]').click(function() {
        updateCustodianQuestionsBlock();
    });

    //Company profile
    var phoneNumber = $('#wealthbot_riabundle_riacompanyinformationtype_phone_number');
    if (phoneNumber.length > 0) {
        $("#wealthbot_riabundle_riacompanyinformationtype_phone_number").inputmask("mask", {"mask": "(999) 999-9999"});
    }

    var groupList = $("#wealthbot_riabundle_createuser_groups");
    if (groupList.length > 0) {
        groupList.pickList();

    }


    $('.website-test.btn').live('click', function(event) {
        var value = $('#wealthbot_riabundle_riacompanyinformationtype_website').val();

        if (!value || value === 'http://') {
            alert('Enter the value.');
            event.preventDefault();
        } else {
            $(this).attr('href', value);
        }
    });

    function updateUsersForm() {
        $.ajax({
            url: $('#user-form').attr('action'),
            cache: false,
            success: function(response) {
                $('#user_management').html(response);
                var groupList = $("#wealthbot_riabundle_createuser_groups");
                if (groupList.length > 0) {
                    groupList.pickList();
                }
            }
        });
    }

    $('#company_profile_form .btn-ajax, #proposal_form .btn-ajax, #billing_n_accounts_form .btn-ajax, #portfolio_management_form .btn-ajax,' +
        '#update_password .btn-ajax, #user_management .btn-ajax, #user_password_management .btn-ajax').live('click', function (event) {
        var button = this;
        var form = $(button).closest('form');

        $(button).button('loading');

        if($(form).attr('id') == 'billing_n_accounts_form'){

            if(!isValidateFees()){
                alert("Fee should be more than 0 and less then 1.");
                $(".btn").button('reset');
                return false;
            }

            if(!isValidateTiers()){
                alert("Please enter the valid tier top value.");
                $(".btn").button('reset');
                return false;
            }

            validateIsOnlyOneTier();
        }

        form.ajaxSubmit({
            target: form.closest('.tab-pane.active'),
            success: function () {
                $(".btn").button('reset');

                if ($(form).attr('id') == 'billing_n_accounts_form') {
                    updateFees();
                    hideAllIsFinalTierCheckbox();
                    showLastIsFinalTierCheckbox();
                    $('#advisor-codes-list').data('index', $('#advisor-codes-list').find(':input').length);
                }

                updateUsersForm();
            }
        });

        event.preventDefault();
    });

    $('').live('click', function (event) {

        var button = $(this);

        button.button('loading');

        $.ajax({
            url: button.attr('href'),
//            method: POST,
            success: function(response) {
                button.button('reset');
                button.closest('form').html(response);
            }
        });

        event.preventDefault();

    });

    $('.edit-ria-user-btn, .delete-ria-user-btn, .cancel-edit-user-btn').live('click', function (event) {
        var button = $(this);

        button.button('loading');

        $.ajax({
            url: button.attr('href'),
            success: function(response) {
                button.button('reset');

                button.closest('.tab-pane').html(response);

                var groupList = $("#wealthbot_riabundle_createuser_groups");
                if (groupList.length > 0) {
                    groupList.pickList();

                }
            }
        });

        event.preventDefault();

    });

    $('.edit_group_btn, .delete_group_btn').live('click', function (event) {
        var button = $(this);
        var isDelete = button.hasClass('delete_group_btn');

        var process = function() {
            button.button('loading');

            $.ajax({
                url: button.attr('href'),
                success: function(response) {
                    button.button('reset');
                    button.closest('.tab-pane').html(response);
                    updateUsersForm();
                }
            });

        };

        if (isDelete) {
            if (confirm("Are you sure?")) {
                process();
            }
        } else {
            process();
        }

        event.preventDefault();

    });

    $('#ria_documents_form').live('submit', function(event) {
        var form = $(this);
        var btn = form.find('input[type="submit"]');

        btn.button('loading');

        form.ajaxSubmit({target: ".ria-documents-form", complete: function(){ btn.button('reset') } });
        event.preventDefault();
    });

    $('#alerts_configuration_form').live('submit', function(event) {
        var form = $(this);
        var btn = form.find('button[type="submit"]');

        btn.button('loading');

        form.ajaxSubmit({
            dataType: 'json',
            success: function(response) {
                if (response.status == 'error') {
                    form.html(response.content);
                }

                btn.button('reset');
            }
        });

        event.preventDefault();
    });

    $('.alertable input, .alertable select').on('keyup change', function() {
        var parentId = $(this).closest('.alertable').attr('id');
        $('#' + parentId + '_alert').show();
    });

    $('#custodian_id')
        .live('change', getAdvisorCodesList)
        .trigger('change');
    $('#new-id').live('click', addAdvisorCode);
    $('.remove-advisor-code').live('click', removeAdvisorCode);

    function getAdvisorCodesList() {
        var custodianId = $(this).val();
        $('#advisor-codes-list').load(Routing.generate('rx_ria_change_profile_advisor_codes', {'custodian_id': custodianId}), function() {
            $('#advisor-codes-list').data('index', $('#advisor-codes-list').find(':input').length);
            recountAdvisorCodes();
        });
        if (custodianId == '') {
            $('#new-id').hide();
        } else {
            $('#new-id').show();
        }
    }

    function addAdvisorCode(e) {
        e.preventDefault();

        var index = $('#advisor-codes-list').data('index');
        $('#advisor-codes-list').data('index', index + 1);

        var prototype =
            '<div>' +
            '<span class="advisor-number"></span> ' +
            '<input type="text" id="ria_advisor_codes_advisorCodes___name___name" name="ria_advisor_codes[advisorCodes][__name__][name]" required="required" class="input-small" /> ' +
            '<span class="icon-remove remove-advisor-code"></span>' +
            '</div>';
        var $newAdvisorCode = $(prototype.replace(/__name__/g, index));

        $(this).before($newAdvisorCode);

        recountAdvisorCodes();
        return false;
    }

    function removeAdvisorCode() {
        $(this).parent().remove();
        recountAdvisorCodes();
    }

    function recountAdvisorCodes() {
        var advisorCodeNumber = 1;
        $('.advisor-number:visible').each(function() {
            $(this).text(advisorCodeNumber);
            advisorCodeNumber++;
        });
    }
});

function updateCustodianQuestionsBlock() {
    var block = $('#custodian_questions');

    if (block.length > 0) {
        var checkedCustodian = $('input:radio[name="ria_custodian[custodian]"]:checked');
        if (checkedCustodian.length > 0) {
            block.show();
            return true;
        }
    }

    block.hide();
    return false;
}