            <p class="mt-0 mb-1">
                Register new account. It is FREE!
            </p>
            <form class="m-0" id="newUserForm" method="POST" action="/register/store">
                <input type="hidden" name="init" value="<?php print $_SESSION['token'];  ?>">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic1">E-mail</span>
                        </div>
                        <input placeholder="some@email.com" class="form-control p-2" aria-describedby="basic1"
                               onblur ="checkIfUserExist()" id="email" name="email" type="email"
                               value="<?php isset($_GET['email']) ? print $_GET['email'] : null; ?>" required>
                    </div>
                    <span id="user_check"></span>
                    <small class="errorFeedback errorSpan" id="emailError">E-mail is required</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic2">Password</span>
                        </div>
                        <input class="form-control p-2" aria-describedby="basic2" id="password1" name="password1" type="password"
                        pattern=".{1,20}" placeholder="Use strong password" title="1-20 characters" required>
                    </div>
                    <small class="errorFeedback errorSpan" id="passwordError">Password required</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic3">Repeat Password</span>
                        </div>
                        <input placeholder="Repeat the password" class="form-control p-2" aria-describedby="basic3" id="password2" name="password2" type="password"
                               required>
                    </div>
                    <small class="errorFeedback errorSpan" id="password2Error">Passwords don’t match</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic4">Name and Surname</span>
                        </div>
                        <input placeholder="John Doe" class="form-control p-2" aria-describedby="basic4" id="name"
                               name="name" type="text" value="<?php isset($_GET['name']) ? print $_GET['name'] : null ?>"
                               required>
                    </div>
                </div>

                <div class="form-group text-center">
                    <button class="btn btn-success" type="submit" id="submit" name="submit">Submit</button>
                </div>
            </form>


            <script type="text/javascript">
                function checkIfUserExist() {
                    let email = $("#email").val();
                    if (email!=="") {
                        $.post("/register/checkEmailExist",{"email":email},check_info);
                    }
                }
                function check_info(data, textStatus){
                    $("#user_check").html(data);
                }
            </script>
