document.addEventListener('DOMContentLoaded', function () {
  // Change on the gift-donation option. Shows/hides the gift's email field.
  document.getElementById('gift-donation').addEventListener('change', (e) => {
    if (e.target.checked) {
      document.querySelector('.email-gift').style.display = 'block';
    } else {
      document.querySelector('.email-gift').style.display = 'none';
    }
  });

  // Change of the amount for donation. Watches when to show the custom payment amount
  document.getElementsByName('selected-amount').forEach((e) => {
    e.addEventListener('change', function (element) {
      if (element.target.value === 'custom') {
        document.querySelector('.part-element').style.display = 'inline';
      } else {
        document.querySelector('.part-element').style.display = 'none';
      }
      document.querySelector('.selected-donation-amount').classList.remove('selected-donation-amount');
      element.target.parentNode.classList.add('selected-donation-amount');
    })
  });

  // Change the payment method and show the related description box
  document.getElementsByName('payment-method').forEach((e) => {
    e.addEventListener('change', function (element) {
      document.querySelector('.payment-method-selected').classList.remove('payment-method-selected');
      element.target.parentNode.classList.add('payment-method-selected');
    });
  });
});

