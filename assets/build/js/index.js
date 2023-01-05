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

  // Submitting the checkout
  document.getElementById('submit-sponsorship').addEventListener('click', function (button) {

    // validate form
    if (document.getElementById('first-name') !== null && !validateContactForm()) {
      return false;
    }

    if (!validateCommonFields()) {
      return false;
    }

    let giftEmail = document.getElementById('gift-donation').checked ? document.getElementById('email-gift').value : '';
    let postData = {
      security: document.getElementById('turbo-security').value,
      action: 'ars_create_new_donation_subscription',
      giftEmail: giftEmail,
      donationAmount: getDonationAmount(),
      acceptedTerms: document.getElementById('terms').checked,
    };
    // send results
    jQuery.ajax({
      url: '/wp-admin/admin-ajax.php',
      data: postData,
      method: 'POST',
      dataMethod: 'JSON',
      success: (response) => {
        console.log(response);
      },
      error: (error) => {
        console.log(error.code + ' > ' + error.message);
      }
    });
  });

  // Used to validate the contact fields when a non-logged in user is adding a donation
  function validateContactForm() {

  }

  // Validates the selected payment method, amount and if it is for a gift
  function validateCommonFields() {
    let donationValue = document.querySelector("input[name='selected-amount']:checked").value;
    if (donationValue === "custom") {
      donationValue = document.getElementById('selected-custom-amount').value
    }

    if (parseInt(donationValue) < 5) {
      alert('Donation amount can not be less then 5 eur.');
      return false;
    }

    if (document.getElementById('gift-donation').checked && document.getElementById('email-gift').value === '') {
      alert('Missing gift email.');
      return false;
    }

    if (!document.getElementById('terms').checked) {
      alert('You need to accept the terms and conditions.');
      return false;
    }
    return true;
  }

  // Gets the value of the donation amount
  function getDonationAmount() {
    let donationValue = document.querySelector("input[name='selected-amount']:checked").value;
    if (donationValue === "custom") {
      donationValue = document.getElementById('selected-custom-amount').value
    }
    return parseFloat(donationValue);
  }
});

