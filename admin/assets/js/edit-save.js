/*
 * javascript used in the admin layout menu to save and edit the actions
 */
jQuery(document).ready(function($) {

  'use strict';

  // EVENT HANDLER - "#daextsocact-submit-action" CLICK -----------------------------

  /*
   * get the data from the form and create/edit an action with a synchronous
   * ajax request
   */

  $(document.body).on('click', '#daextsocact-submit-action', function() {

    'use strict';

    //init vars
    let daextsocact_edit_id = null;

    //GET DATA -------------------------------------------------------------

    //get description
    const daextsocact_description = $('#daextsocact-description').val();

    //get field data
    const daextsocact_field_data = get_serialized_field_data();

    //get edit id
    if ($('#daextsocact-edit-id').length) {

      //we are editing an action
      daextsocact_edit_id = $('#daextsocact-edit-id').val();

    } else {

      //this is a new action
      daextsocact_edit_id = 0;

    }

    //prepare input for the ajax request
    var data = {
      'action': 'daextsocact_save_action',
      'security': window.DAEXTSOCACT_PARAMETERS.nonce,
      'description': daextsocact_description,
      'field_data': daextsocact_field_data,
      'edit_id': daextsocact_edit_id,
    };

    //set ajax in synchronous mode
    jQuery.ajaxSetup({async: false});

    //perform the ajax
    $.post(window.DAEXTSOCACT_PARAMETERS.ajax_url, data, function(res) {

      'use strict';

      if (res == 'success') {

        //redirect page
        window.location.replace(window.DAEXTSOCACT_PARAMETERS.admin_url +
            '?page=daextsocact-actions');

      }

    });

  });

  // -------------------------------------------------------------------------

  // FUNCTIONS ---------------------------------------------------------------

  function get_serialized_field_data() {

    'use strict';

    /*
     * initialize the array that is going to include all the elements in
     * the field
     */
    let field_data_serialized = [];

    let label_text = '';

    /*
     * save all the elements in the field in the field_data_serialized
     * array
     */
    $('#daextsocact-field > .daextsocact-draggable-element').
        each(function(index) {

          //get element id ---------------------------------------------------
          const element_id = $(this).attr('data-id');

          //get element coordinates
          const pos_x = parseInt($(this).css('left'), 10);
          const pos_y = parseInt($(this).css('top'), 10);

          //get element label text
          if (element_id == 'daextsocact-player-label') {
            label_text = $(this).children().val();
          }

          //push in the array
          field_data_serialized.push({
            element_id: element_id,
            pos_x: pos_x,
            pos_y: pos_y,
            label_text: label_text,
          });

        });

    return field_data_serialized;

  }

  //Dialog Confirm -----------------------------------------------------------
  window.daextsocact = {};
  $(document.body).on('click', '.menu-icon.delete', function(event) {

    'use strict';

    event.preventDefault();
    window.daextsocact.actionToDelete = $(this).prev().val();
    $('#dialog-confirm').dialog('open');

  });

  /**
   * Dialog confirm initialization.
   */
  $(function() {

    'use strict';

    $('#dialog-confirm').dialog({
      autoOpen: false,
      resizable: false,
      height: 'auto',
      width: 340,
      modal: true,
      buttons: {
        [objectL10n.deleteText]: function() {

          'use strict';

          $('#form-delete-' + window.daextsocact.actionToDelete).submit();

        },
        [objectL10n.cancelText]: function() {

          'use strict';

          $(this).dialog('close');

        },
      },
    });

  });

});