// Payment and checkout page scripts
document.addEventListener('DOMContentLoaded', function () {

  // Change on the gift-donation option. Shows/hides the gift's email field.
  if (document.getElementById('gift-donation') !== null) {
    document.getElementById('gift-donation').addEventListener('change', (e) => {
      if (e.target.checked) {
        document.querySelector('.email-gift').style.display = 'block';
      } else {
        document.querySelector('.email-gift').style.display = 'none';
      }
    });
  }

  // Change of the amount for donation.
  if (document.getElementsByName('selected-amount') !== null) {
    document.getElementsByName('selected-amount').forEach((e) => {
      e.addEventListener('change', function (element) {
        const selectedAmount = document.querySelector('.selected-donation-amount');
        if (selectedAmount !== null) {
          selectedAmount.classList.remove('selected-donation-amount');
        }
        document.getElementById('plan-id').value = element.target.dataset.subscription;
        element.target.parentNode.classList.add('selected-donation-amount');
      })
    });
  }

  // Submitting the checkout
  if (document.getElementById('submit-sponsorship') !== null) {
    document.getElementById('submit-sponsorship').addEventListener('click', function (button) {

      // validate form
      if (document.getElementById('first-name') !== null && !validateContactForm()) {
        return false;
      }

      if (!validateCommonFields()) {
        return false;
      }

      button.target.disabled = true;

      const giftEmail = document.getElementById('gift-donation').checked ? document.getElementById('email-gift').value : '';
      const postData = {
        security: document.getElementById('turbo-security').value,
        action: 'va_create_new_donation_subscription',
        giftEmail: giftEmail,
        animalID: document.getElementById('animal-id').value,
        donationAmount: getDonationAmount(),
        acceptedTerms: document.getElementById('terms').checked,
      };

      // send results
      makeAjaxCall(postData)
        .then((response) => {
          console.log(response)
          alert('Success')
          location.href = response.data.redirect_to;
        })
        .catch((message) => {
          alert(message)
          document.getElementById('submit-sponsorship').target.disabled = false;
        })
    });
  }

  // Render the PayPal Button
  if (document.getElementById('paypal-button-container') !== null) {
    /**
     * @var paypal
     */
    paypal.Buttons({
      onInit: function (data, actions) {
        // Disable the buttons
        actions.disable();
        const planID = document.getElementById('plan-id');
        const terms = document.getElementById('terms');
        const giftCheckbox = document.getElementById('gift-donation');
        const giftEmail = document.getElementById('email-gift');
        // Listen for changes to the checkbox
        terms.addEventListener('change', function () {
          const validGift = (giftCheckbox.checked && giftEmail.value !== '') || !giftCheckbox.checked;
          (terms.checked && planID.value !== '' && validGift) ? actions.enable() : actions.disable();
        });
        // Listen for changes to hidden input for the value of the selected plan
        planID.addEventListener('change', function () {
          const validGift = (giftCheckbox.checked && giftEmail.value !== '') || !giftCheckbox.checked;
          (terms.checked && planID.value !== '' && validGift) ? actions.enable() : actions.disable();
        });
        // Listen to changes in the checkbox of the gift
        giftCheckbox.addEventListener('change', function () {
          const validGift = (giftCheckbox.checked && giftEmail.value !== '') || !giftCheckbox.checked;
          (terms.checked && planID.value !== '' && validGift) ? actions.enable() : actions.disable();
        });
        // Listen to changes in the gift email filed
        giftEmail.addEventListener('change', function () {
          const validGift = (giftCheckbox.checked && giftEmail.value !== '') || !giftCheckbox.checked;
          (terms.checked && planID.value !== '' && validGift) ? actions.enable() : actions.disable();
        });
      },
      onClick: function () {
        validateCommonFields();
      },
      createSubscription: function (data, actions) {
        if (document.getElementById('plan-id').value === '') {
          alert('No donation plan selected');
          return;
        }
        return actions.subscription.create({
          'plan_id': document.getElementById('plan-id').value
        });
      },
      onApprove: function (data, actions) {
        console.log(data)
        console.log(actions)
        document.getElementById('submit-sponsorship').click();
        alert('You have successfully created subscription ' + data.subscriptionID);
      }
    }).render('#paypal-button-container');
  }


  // Cancelling subscription (fired from My-subscriptions page)
  if (document.querySelector('.cancel-button') !== null) {
    document.querySelectorAll('.cancel-button').forEach((element) => {
      element.addEventListener('click', function (e) {

        e.target.disabled = true;
        e.target.children[0].style.display = 'inline-block';

        const postData = {
          post_id: e.target.dataset.postId,
          security: document.getElementById('turbo-security').value,
          action: 'va_cancel_subscription_ajax',
        };

        makeAjaxCall(postData)
          .then(success => {
            alert(success.data.message);
            e.target.disabled = false;
            e.target.children[0].style.display = 'none';
            // replace the subscription status
            document.querySelector('.row-' + postData.post_id + ' .subscription-status').innerText = success.data.status;
            document.querySelector('.row-' + postData.post_id + ' .next-due-date').innerText = 'n/a'; // remove due date
            e.target.remove() // removes the cancellation button
          })
          .catch(error => {
            alert(error);
            e.target.disabled = false;
            e.target.children[0].style.display = 'none';
          });
        return false; // since this is clicked link, always return false
      });
    });
  }

  // Used to validate the contact fields when a non-logged in user is adding a donation
  function validateContactForm() {
    const firstName = document.getElementById('first-name');
    const lastName = document.getElementById('last-name');
    const email = document.getElementById('email');
    const pass = document.getElementById('password');
    const pass2 = document.getElementById('re-password');
    const fields = [firstName, lastName, email, pass, pass2];
    let hasError = false;

    // clear all previous errors
    for (let element of fields) {
      element.nextElementSibling.innerText = '';
      element.style.borderColor = 'inherit';
    }
    if (!firstName.value) {
      firstName.nextElementSibling.innerText = 'Field can not be empty';
      firstName.style.borderColor = 'red';
      hasError = true;
    }

    if (!lastName.value) {
      lastName.nextElementSibling.innerText = 'Field can not be empty';
      lastName.style.borderColor = 'red';
      hasError = true;
    }

    if (!email.value) {
      email.nextElementSibling.innerText = 'Field can not be empty';
      email.style.borderColor = 'red';
      hasError = true;
    }

    if (!pass.value) {
      pass.nextElementSibling.innerText = 'Field can not be empty';
      pass.style.borderColor = 'red';
      hasError = true;
    }
    if (!pass2.value) {
      pass2.nextElementSibling.innerText = 'Field can not be empty';
      pass2.style.borderColor = 'red';
      hasError = true;
    }

    if (pass.value !== pass2.value) {
      pass.nextElementSibling.innerText = 'Passwords did not matched';
      pass.style.borderColor = 'red';
      hasError = true;
    }


    if (hasError) {
      document.querySelector('.contact-details').scrollIntoView({
        behavior: "smooth",
        block: "start",
        inline: "nearest"
      });
      return false;
    }

    return true;
  }

  // Validates the selected amount, the gift fields, animal ID and selected terms
  function validateCommonFields() {
    // Show a validation error if the checkbox for "Terms & Conditions" is not checked
    if (!document.getElementById('terms').checked) {
      document.getElementById('terms-error').classList.remove('hidden');
    } else {
      document.getElementById('terms-error').classList.add('hidden');
    }

    // Show a validation error if there is no selected subscription plan
    if (document.getElementById('plan-id').value === '') {
      document.getElementById('subscription-plan-error').classList.remove('hidden');
    } else {
      document.getElementById('subscription-plan-error').classList.add('hidden');
    }

    // Check if the gift checkbox is checked and if there is a value for it
    if (document.getElementById('gift-donation').checked && document.getElementById('email-gift').value === '') {
      document.getElementById('gift-email-error').classList.remove('hidden');
    } else {
      document.getElementById('gift-email-error').classList.add('hidden');
    }

    // Checks if there is an animal selected for donation
    const animalID = document.getElementById('animal-id');
    if (animalID === null || animalID.value === '') {
      document.getElementById('missing-animal-error').classList.remove('hidden');
    } else {
      document.getElementById('missing-animal-error').classList.add('hidden');
    }
  }

  // Gets the value of the donation amount
  function getDonationAmount() {
    let donationValue = document.querySelector("input[name='selected-amount']:checked").value;
    return parseFloat(donationValue);
  }

  function makeAjaxCall(postData) {
    return new Promise((resolve, reject) => {
      jQuery.ajax({
        url: '/wp-admin/admin-ajax.php',
        data: postData,
        method: 'POST',
        dataType: 'JSON',
        success: (response) => {
          if (response.status === 1) {
            resolve(response);
          } else {
            reject(response.message);
          }
        },
        error: (error) => {
          reject(error.code + ' > ' + error.message);
        }
      });
    });
  }
});

