
    <p class="mt-0 mb-1">
        Want to get in touch? Fill out the form below to send me a message and I will get back to you as soon as possible!
    </p>
        <form id="contact-form" method="POST" action="/contact/message" role="form">
            <div class="messages"></div>

            <div class="controls">
                <input type="hidden" name="init"
                       value="<?php if (isset($_SESSION['token'])) print $_SESSION['token']; ?>">
                <div class="form-group">
                    <label for="form_name">Full name *</label>
                    <input id="form_name" class="form-control form-control-sm"
                           name="name" type="text" placeholder="Please enter your name" data-error="Name is required."
                           required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                    <label for="form_email">Email *</label>
                    <input class="form-control form-control-sm" id="form_email"
                           name="email" type="email" placeholder="Please enter your email"
                           data-error="Valid email is required." required>
                    <div class="help-block with-errors"></div>
                </div>
<!--                                </div>-->
                <div class="form-group">
                    <label for="form_message">Message *</label>
                    <textarea id="form_message" name="message" class="form-control form-control-sm"
                              rows="3" placeholder="Type your message here" required
                              data-error="Please, leave us a message."></textarea>
                    <div class="help-block with-errors"></div>
                </div>

                <div class="float-right mt-1">
                    <button class="btn btn-success" type="submit" id="submit" name="submit">Send message</button>
                </div>

                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6Les0WUaAAAAAI5l_xG1X5TDVM6yEB497i8yqQR4" data-callback="verifyRecaptchaCallback" data-expired-callback="expiredRecaptchaCallback"></div>
                    <input class="form-control d-none" data-recaptcha="true" required data-error="Please complete the Captcha">
                    <div class="help-block with-errors"></div>
                </div>
            </div>
    </form>