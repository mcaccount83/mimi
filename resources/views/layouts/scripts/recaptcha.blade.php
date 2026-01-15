<script>
document.addEventListener('DOMContentLoaded', function() {
    function waitForRecaptcha() {
        if (typeof grecaptcha !== 'undefined' && grecaptcha.enterprise) {
            setupFormSubmit();
        } else {
            setTimeout(waitForRecaptcha, 100);
        }
    }

    function setupFormSubmit() {
        var forms = document.querySelectorAll('form');

        forms.forEach(function(form) {
            var recaptchaInput = form.querySelector('input[name="g-recaptcha-response"]');

            if (recaptchaInput) {
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
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
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
                            alert('reCAPTCHA verification failed. Please try again.');
                        });
                    });
                });
            }
        });
    }

    waitForRecaptcha();
});
</script>
