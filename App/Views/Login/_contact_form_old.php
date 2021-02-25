
                            <p class="mt-0 mb-1">
                                Want to get in touch? Fill out the form below to send me a message and I will get back to you as soon as possible!
                            </p>
                                <form id="loginForm" method="POST" action="/contact/message">
<!--                                <div class="row m-0">-->

                                    <input type="hidden" name="init"
                                           value="<?php if (isset($_SESSION['token'])) print $_SESSION['token']; ?>">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control form-control-sm" aria-describedby="basic2"
                                                   id="name" name="name" type="text" placeholder="Full name"
                                                   value="<?php if(isset($_GET['name'])) print filter_var($_GET['name'], FILTER_SANITIZE_STRING); ?>"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control form-control-sm" aria-describedby="basic1" id="email"
                                                   name="email" type="email" placeholder="Email" value="<?php if(isset($_GET['email'])) print filter_var($_GET['email'], FILTER_SANITIZE_EMAIL); ?>" required>
                                        </div>
                                        <span id="info_login"></span>
                                    </div>
<!--                                </div>-->

                                    <textarea name="message" class="form-control form-control-sm" rows="3" placeholder="Type your message here"><?php if(isset($_GET['message'])) print filter_var($_GET['message'], FILTER_SANITIZE_STRING); ?></textarea>


                                    <div class="text-center mt-1">
                                        <button class="btn btn-success" type="submit" id="submit" name="submit">Send</button>
                                    </div>

                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="6Les0WUaAAAAAI5l_xG1X5TDVM6yEB497i8yqQR4" data-callback="verifyRecaptchaCallback" data-expired-callback="expiredRecaptchaCallback"></div>
                                        <input class="form-control d-none" data-recaptcha="true" required data-error="Please complete the Captcha">
                                        <div class="help-block with-errors"></div>
                                    </div>

                            </form>