<script>
document.addEventListener('DOMContentLoaded', function() {
    var forms = document.querySelectorAll('form');

    // Disable all submit buttons immediately, before recaptcha even starts loading
    forms.forEach(function(form) {
        var recaptchaInput = form.querySelector('input[name="g-recaptcha-response"]');
        if (recaptchaInput) {
            var submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.dataset.originalHtml = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="bi bi-arrow-repeat spin"></i>Loading security check...';
            }
        }
    });

    var recaptchaLoadTimeout = setTimeout(function() {
        // Recaptcha never loaded — fail safe instead of looping forever
        forms.forEach(function(form) {
            var recaptchaInput = form.querySelector('input[name="g-recaptcha-response"]');
            if (!recaptchaInput) return;
            var submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = submitButton.dataset.originalHtml;
                submitButton.disabled = true; // stays disabled — don't allow unverified submit
            }
            customErrorAlert('Unable to load the security check. Please disable any ad blockers and refresh the page.');
        });
    }, 10000); // 10s timeout

    function waitForRecaptcha() {
        if (typeof grecaptcha !== 'undefined' && grecaptcha.enterprise) {
            clearTimeout(recaptchaLoadTimeout);
            setupFormSubmit();
        } else {
            setTimeout(waitForRecaptcha, 100);
        }
    }

    function setupFormSubmit() {
        forms.forEach(function(form) {
            var recaptchaInput = form.querySelector('input[name="g-recaptcha-response"]');
            if (!recaptchaInput) return;

            var submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = submitButton.dataset.originalHtml;
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                var submitButton = this.querySelector('button[type="submit"]');
                var recaptchaInput = this.querySelector('input[name="g-recaptcha-response"]');

                if (recaptchaInput.value) {
                    HTMLFormElement.prototype.submit.call(this);
                    return;
                }

                if (submitButton) {
                    submitButton.disabled = true;
                    var originalHTML = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="bi bi-arrow-repeat spin"></i>Processing...';
                }

                var formToSubmit = this;

                grecaptcha.enterprise.ready(function() {
                    grecaptcha.enterprise.execute('{{ config('services.recaptcha.site_key') }}', {action: 'submit'})
                    .then(function(token) {
                        recaptchaInput.value = token;
                        HTMLFormElement.prototype.submit.call(formToSubmit);
                    })
                    .catch(function(error) {
                        console.error('reCAPTCHA error:', error);
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalHTML;
                        }
                        customErrorAlert('reCAPTCHA verification failed. Please try again.');
                    });
                });
            });
        });
    }

    waitForRecaptcha();
});
</script>

// document.addEventListener('DOMContentLoaded', function() {
//     function waitForRecaptcha() {
//         if (typeof grecaptcha !== 'undefined' && grecaptcha.enterprise) {
//             setupFormSubmit();
//         } else {
//             setTimeout(waitForRecaptcha, 100);
//         }
//     }

//     function setupFormSubmit() {
//         var forms = document.querySelectorAll('form');

//         forms.forEach(function(form) {
//             var recaptchaInput = form.querySelector('input[name="g-recaptcha-response"]');

//             if (recaptchaInput) {
//                 form.addEventListener('submit', function(e) {
//                     e.preventDefault();

//                     var submitButton = this.querySelector('button[type="submit"]');
//                     var recaptchaInput = this.querySelector('input[name="g-recaptcha-response"]');

//                     if (recaptchaInput.value) {
//                         HTMLFormElement.prototype.submit.call(this);
//                         return;
//                     }

//                     if (submitButton) {
//                         submitButton.disabled = true;
//                         var originalHTML = submitButton.innerHTML;
//                         submitButton.innerHTML = '<i class="bi bi-arrow-repeat spin"></i>Processing...';
//                     }

//                     var formToSubmit = this;

//                     grecaptcha.enterprise.ready(function() {
//                         grecaptcha.enterprise.execute('{{ config('services.recaptcha.site_key') }}', {action: 'submit'})
//                         .then(function(token) {
//                             recaptchaInput.value = token;
//                             HTMLFormElement.prototype.submit.call(formToSubmit);
//                         })
//                         .catch(function(error) {
//                             console.error('reCAPTCHA error:', error);
//                             if (submitButton) {
//                                 submitButton.disabled = false;
//                                 submitButton.innerHTML = originalHTML;
//                             }
//                             customErrorAlert('reCAPTCHA verification failed. Please try again.');
//                         });
//                     });
//                 });
//             }
//         });
//     }

//     waitForRecaptcha();
// });

