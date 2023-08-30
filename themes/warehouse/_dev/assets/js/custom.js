/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */


var selected_accordian = 0;
var checked_accordian = [];
var checkInterval;

$(document).ready(function () {  
  selected_accordian = 1;
  checked_accordian.push(1);
  ventanastore_product_varition_accordian();
  overview_product_data();
  variation_product_checkbox_clickable();



  // Set an interval with a delay (e.g., 1000 ms) between checks
  checkInterval = setInterval(function () {
    width_and_height_field_num();
    updateHeightAndWidthText();

    // Attach input event handler to the width and height input fields
    $('#ppat-widget input[type="number"]').on('input', updateHeightAndWidthText);
  }, 100);

  // Add tabs
  $("#iqitadditionaltabs-accordion .card").each(function(){
    var tab_title = $(this).find(".title").text();
    var tab_content = $(this).find(".rte-content").html();
  
    $("#product-infos-tabs").append('<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#'+slugify(tab_title)+'">'+tab_title+'</a></li>');
    $("#product-infos-tabs-content").append('<div class="tab-pane" id="'+slugify(tab_title)+'"><section><div class="custom_tab">'+tab_content+'</div></section></div>');
  });

  // Event delegation for dynamically added content
  $(document).on("click", ".custom_tab .pre-cms-accordion h3", function() {
    $(this).next().slideToggle();
  });
});

function slugify(text) {
  return text
      .toLowerCase()            // Convert to lowercase
      .replace(/\s+/g, '_')     // Replace spaces with dashes
      .replace(/[^\w-]+/g, '')  // Remove non-word characters except dashes
      .replace(/--+/g, '_')     // Replace consecutive dashes with a single dash
      .replace(/^-+|-+$/g, ''); // Trim dashes from both ends
}

function ventanastore_product_varition_accordian(speed = 200){
    // Initially, add the "active" class to the first accordion item and show its content
    $(".product-variants .ventanastore_accordion_item").each(function(index) {
      if((index+1) == selected_accordian) {
        $(this).find('.ventanastore_accordion_title').addClass("active");
        $(this).find('.ventanastore_accordion_title').addClass("activecheck");
        $(this).find('.ventanastore_accordion_title').next('.ventanastore_accordion_content').slideToggle(speed);
      }
    });
   
    // Click event handler for the accordion titles
    $(".ventanastore_accordion_title").click(function() {
      selected_accordian = $(this).data('position');
      checked_accordian.push(selected_accordian);
      
      // Toggle the "active" class and slide toggle the content for the clicked title
      $(this).toggleClass("active");
      $(this).addClass("activecheck");
      $(this).next(".ventanastore_accordion_content").slideToggle(200);

      // Select all other accordion contents and slide them up if they were open
      $(".ventanastore_accordion_title").not(this).removeClass("active");
      $(".ventanastore_accordion_content").not($(this).next(".ventanastore_accordion_content")).slideUp();
    });

    $(".ventanastore_accordion_title").each(function(){
      current_accordian = $(this).data('position');
      if (checked_accordian.includes(current_accordian)) {
        $(this).addClass("activecheck");
      }
    });
}

// Change text field to number field for width and height
function width_and_height_field_num() {
  var $labels = $('#ppat-widget label'); // Get all labels within the #ppat-widget element

  $labels.each(function() {
    var labelText = $(this).text().toLowerCase(); // Get the text content of the label and convert to lowercase

    // Check if the label text contains the words "height" or "width"
    if (labelText.includes('hoogte') || labelText.includes('breedte')) {
      var $input = $(this).next('input[type="text"]'); // Get the next input element after the label

      if ($input.length > 0) {
        $input.attr('type', 'number'); // Change the input type to "number"
        $labels.parent().parent().eq(1).addClass("height_width_custom_row_section");
      }
    }
  });
}

function overview_product_data(){
  var data_id = 0;
  var overview_data = ['.Kleurget_data','.Montagewijzeget_data', '.Bedieningszijdeget_data' , '.Ladderbandget_data', '.ladderbandget_data' , '.Zijgeleidingget_data' ]
  overview_data.forEach( function (e) {
      var data = $(e).text();
      var firstWord = data.split(':')[0];
      var styledData = data.replace(firstWord , "<span class='first-word'>" + firstWord + "</span>");

      $('.single_product_overview_secction').append(`<p id="${data_id}_data">${styledData}</p>`);
      data_id++; 
  });
}

$('#add-to-cart-or-refresh input[type="radio"]').on('change', function() {
    $(document).ajaxStop(function() {
        var data_id = 0;
        var overview_data = ['.Kleurget_data', '.Montagewijzeget_data', '.Bedieningszijdeget_data', '.Ladderbandget_data', '.ladderbandget_data', '.Zijgeleidingget_data'];

        overview_data.forEach(function(e) {
            var data = $(e).text();
            
            // Extract the first word up to the colon symbol
            var firstWord = data.split(':')[0];
            
            // Apply the same first word styling to the data variable
            var styledData = data.replace(firstWord, "<span class='first-word'>" + firstWord + "</span>");
            
            $('#' + data_id + '_data').html(styledData); // Use .html() to preserve the HTML
            data_id++;
        });
        variation_product_checkbox_clickable();
    });
});


// new add to cart button

$('#single_product_add_to_cart_btn').on('click', function () {
    $('div.product-add-to-cart button.add-to-cart').trigger('click');
    setTimeout(function() {
        $('#sidebar_add_to_cart_quanity').val('1');
    }, 2000);
    
});

//new quantity input
$('#sidebar_add_to_cart_quanity').on('input', function(){
    var quantity = $('#sidebar_add_to_cart_quanity').val();
    $('input#quantity_wanted').val(quantity);
});

// sidebar height and width text change
function updateHeightAndWidthText() {
    // console.log("test case #1");
    var width = $('#ppat-widget input[type="number"]').val();
    var height = $('#ppat-widget .col_height_field input[type="number"]').val();
    
    $('#ppat-widget input[type="number"]').each(function(){
      if($(this).parent().find("label").text().toString().includes("Breedte") && $(this).val() != "") {
        $('#product_width_val').text($(this).val() +' mm');
      }
      if($(this).parent().find("label").text().toString().includes("Hoogte") && $(this).val() != "") {
        $('#product_height_val').text($(this).val() +' mm');
      }
    });
}

$(document).ready(function() {
  $(document).on('click', '.modal-trigger', function() {
    var title = $(this).data('title');
    var description = $(this).data('description');
    
    if (title && description) {
      $('#modal-hint .modal-title').text(title);
      $('#modal-hint .modal-body').text(description);
    }else{
      $('#modal-hint .modal-title').text('');
      $('#modal-hint .modal-body').text('');
    }
  });
  
  var found = 0;
  for (let i = 0; i < 10; i++) {
    setTimeout(function() {

      if($(".ppat-unit-entry-wrapper").find("input").length) {
        found += 1;
      }
      
      if(found == 1) {
        if(typeof ppat_product != typeof undefined) {
          var min = parseFloat(ppat_product['min_row']);
          var max = parseFloat(ppat_product['max_row']);
          $(".form-control.ppat-unit-entry").attr("placeholder", "eg: "+min);
        }

        $(".form-control.ppat-unit-entry").val("");

        /*
        $(".ppat-unit-entry-wrapper").each(function(){
          if ($(this).text().includes("Breedte")) {
            $(this).find("input").on("change", function(){
              updateHeightAndWidthText();
            });
          }
        });
        */
      }
    }, i * 100);
  }
});

function variation_product_checkbox_clickable() {
    $('.ventanastore_accordion_content ul').off('click', 'li'); // Unbind to avoid duplicate bindings
    $('.ventanastore_accordion_content ul').on('click', 'li', function() {
        //console.log("clicking");
        $(this).siblings().find('input[type="radio"]').prop('checked', false);
        $(this).find('input[type="radio"]').prop('checked', true).change();
    });
}
