var index;
if ($('.edit-btn').attr('data-clicked') == 'true') {
  index = 1;
} 
else {
  index = 0;
}

$(".edit-btn").attr('data-clicked', false); 

// Create a Stripe client.
var stripe = Stripe(public_key);

// Create an instance of Elements.
var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var style = {
  base: {
    color: '#32325d',
    lineHeight: '1.5',
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '16px',
    '::placeholder': {
      color: '#aab7c4'
    }
  },
  invalid: {
    color: '#fa755a',
    iconColor: '#fa755a'
  }
};

// Create an instance of the card Element.
var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
var form = document.getElementsByClassName('card-element');
card.mount(form[index]);

// Handle real-time validation errors from the card Element.
card.addEventListener('change', function(event) {
  var displayError = document.getElementsByClassName('card-errors');
  if (event.error) {
    displayError[index].textContent = event.error.message;
  } else {
    displayError[index].textContent = '';
  }
});

// Handle form submission.
$('.payment-form').on("submit", function(event) {
  event.preventDefault();
  stripe.createToken(card).then(function(result) {
    var rownumber1 = $('table.order-list tbody tr:last').index();
    var rownumber2 = $('table.sale-list tbody tr:last').index();
    var rownumber3 = $('table.purchase-list tbody tr:last').index();
    if (result.error) {
      if($('select[name="paid_by_id"]').val() == 3){
          // Inform the user if there was an error.
          var errorElement = document.getElementsByClassName('card-errors');
          errorElement[index].textContent = result.error.message;
      }
      else if (rownumber1 < 0){
        event.preventDefault();
      }
      else{
        var form = document.getElementsByClassName('payment-form');
        // Submit the form
        form[index].submit();
      }
    }
    else if (rownumber1 < 0 && rownumber2 < 0 && rownumber3 < 0){
      event.preventDefault();
    }
    else {
      // Send the token to your server.
      stripeTokenHandler(result.token, index);
    }
  });
});

function stripeTokenHandler(token, index) {
  // Insert the token ID into the form so it gets submitted to the server
  var form = document.getElementsByClassName('payment-form');
  var hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form[index].appendChild(hiddenInput);

  // Submit the form
  form[index].submit();
}
