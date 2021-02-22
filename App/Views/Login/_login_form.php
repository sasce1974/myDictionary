
                            <form class="row m-0" id="loginForm" method="POST" action="/login/new">
                                <input type="hidden" name="init"
                                       value="<?php if (isset($_SESSION['token'])) print $_SESSION['token']; ?>">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm" aria-describedby="basic1" id="email"
                                               name="email" type="email" placeholder="Username/Email" required>
                                    </div>
                                    <span id="info_login"></span>
                                    <small class="errorFeedback errorSpan" id="emailError">E-mail is required</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm" aria-describedby="basic2"
                                               id="password" name="password" type="password" placeholder="Password"
                                               required>
                                    </div>
                                    <small class="errorFeedback errorSpan" id="passwordError">Password required</small>
                                </div>
                                <div class="mx-auto">
                                    <button class="btn btn-success" type="submit" id="submit" name="submit">Login
                                    </button>
                                </div>
                            </form>