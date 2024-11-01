/*
 * Javascript used by the public side of WordPress on the document ready event.
 */
jQuery(document).ready(function($) {

  'use strict';

  //EVENT HANDLER - WINDOW ONLOAD --------------------------------------------

  /*
   * The load event is sent when the window and all sub-elements have been
   * completely loaded.
   *
  */
  $(window).on('load', function() {

    'use strict';

    daextsocact_adapt_field_elements();

  });

  // -------------------------------------------------------------------------

  // EVENT HANDLER - WINDOW RESIZE -------------------------------------------

  $(window).resize(function() {

    'use strict';

    //Adapt the size of the elements in the field
    daextsocact_adapt_field_elements();

  });

  // -------------------------------------------------------------------------

  //FUNCTIONS ----------------------------------------------------------------

  /*
   * Adapt the size of the elements in the fields.
   */
  function daextsocact_adapt_field_elements() {

    'use strict';

    //Iterate over all the fields
    $('.daextsocact-field').each(function() {

      'use strict';

      //calculate field ratio
      const field_ratio_x = $(this).width() / 1030;
      const field_ratio_y = $(this).height() / 429;

      //get ID of this field
      const field_id = $(this).attr('id');

      //Iterate over all the field elements for this specific field ------------
      $('#' + field_id + ' > .daextsocact-field-element').each(function() {

        'use strict';

        //adapt the width of the elements to the current ratio -----------------
        const adapted_width = $(this).attr('data-default-width') *
            field_ratio_x;
        const adapted_height = $(this).attr('data-default-height') *
            field_ratio_y;

        $(this).css('width', (adapted_width) + 'px');
        $(this).css('height', (adapted_height) + 'px');
        $(this).
            css('background-size',
                (adapted_width) + 'px ' + (adapted_height) + 'px');

        //adapt the label text to the current ratio ----------------------------
        if ($(this).attr('data-id') == 'daextsocact-player-label') {

          $(this).children().css('font-size', 10 * field_ratio_x + 'px');
          $(this).children().css('width', 81 * field_ratio_x + 'px');
          $(this).children().css('height', 22 * field_ratio_y + 'px');
          $(this).children().css('line-height', 22 * field_ratio_y + 'px');
          $(this).children().css('margin-left', 30 * field_ratio_x + 'px');
          $(this).children().css('margin-right', 12 * field_ratio_x + 'px');

        }

        //set the element to visible
        $(this).css('visibility', 'visible');

      });

    });

  }

});