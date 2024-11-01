/*
 * javascript used in the admin layout menu
 */
jQuery(document).ready(function($) {

  'use strict';

  //EVENT HANDLER ( DOCUMENT READY ) -----------------------------------------

  //show selected elements
  daextsocact_show_selected_elements();

  //initialize draggable
  daextsocact_init_draggable_field();

  // -------------------------------------------------------------------------

  //EVENT HANDLER ( "#daextsocact-elements > .daextsocact-draggable-element" CLICK ) -------

  //add element to the field on the double-click event
  $(document.body).
      on('click', '#daextsocact-elements > .daextsocact-draggable-element',
          function() {

            'use strict';

            //create a copy of this element
            const daextsocact_element_copy = $(this).clone();

            //add inline style to position the element at the center of the field
            daextsocact_element_copy.css('left', '504px').css('top', '121px');

            //move the element in the field
            $('#daextsocact-field').append($(daextsocact_element_copy));

            //initialize draggable
            daextsocact_init_draggable_field();

            //EVENT HANDLER ( "#daextsocact-field > .daextsocact-draggable-element" DBLCLICK ) ---

            //remove element from the field on the double-click event
            $('#daextsocact-field > .daextsocact-draggable-element').
                dblclick(function() {

                  'use strict';

                  //remove
                  $(this).remove();

                });

          });

  // -------------------------------------------------------------------------

  //EVENT HANDLER ( "#daextsocact-select-assets" CHANGE ) ---------------------------

  //show the selected element when the "Assets" select box changes
  $(document.body).on('change', '#daextsocact-select-assets', function() {

    'use strict';

    daextsocact_show_selected_elements();

  });

  // -------------------------------------------------------------------------

  //EVENT HANDLER ( "#daextsocact-field > .daextsocact-draggable-element" DBLCLICK ) -------

  //remove element from the field on the double-click event
  $(document.body).
      on('dblclick', '#daextsocact-field > .daextsocact-draggable-element',
          function() {
            // $( "#daextsocact-field > .daextsocact-draggable-element" ).dblclick(function() {

            'use strict';

            //remove
            $(this).remove();

          });

  // -------------------------------------------------------------------------

  //FUNCTIONS ----------------------------------------------------------------

  //show the selected elements
  function daextsocact_show_selected_elements() {

    'use strict';

    const selected_assets = ($('#daextsocact-select-assets').val());

    $('#daextsocact-elements > .daextsocact-draggable-element').hide();

    $('#daextsocact-elements > .' + selected_assets).show(0);

  }

  //initilize the draggable field with the draggable jquery plugin
  function daextsocact_init_draggable_field() {

    'use strict';

    $('#daextsocact-field > .daextsocact-draggable-element').draggable({

      //snap to grid
      grid: [1, 1],

      //contain inside parent
      containment: '#daextsocact-field',

      scroll: false,

    });

  }

  // -------------------------------------------------------------------------

});