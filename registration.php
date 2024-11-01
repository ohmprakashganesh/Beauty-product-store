<style>
    #uni_modal .modal-content>.modal-footer,#uni_modal .modal-content>.modal-header{
        display:none;
    }
</style>
<div class="container-fluid">
    <form action="" id="registration">
        <div class="row">
            <h3 class="text-center">Create New Account
                <span class="float-right">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </span>
            </h3>
            <hr>
        </div>
        <div class="row align-items-center h-100">
            <div class="col-lg-5 border-right">
                <div class="form-group">
                    <label for="firstname" class="control-label">Firstname</label>
                    <input type="text" class="form-control form-control-sm form" name="firstname" id="firstname" required>
                </div>
                <div class="form-group">
                    <label for="lastname" class="control-label">Lastname</label>
                    <input type="text" class="form-control form-control-sm form" name="lastname" id="lastname" required>
                </div>
                <div class="form-group">
                    <label for="contact" class="control-label">Contact</label>
                    <input type="text" class="form-control form-control-sm form" name="contact" id="contact" required>
                </div>
                <div class="form-group">
                    <label for="gender" class="control-label">Gender</label>
                    <select name="gender" id="gender" class="custom-select select" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="form-group">
                    <label for="default_delivery_address" class="control-label">Default Delivery Address</label>
                    <textarea class="form-control form" rows='3' name="default_delivery_address" id="default_delivery_address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <input type="email" class="form-control form-control-sm form" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" class="form-control form-control-sm form" name="password" id="password" required>
                </div>
                <div class="form-group d-flex justify-content-between">
                    <a href="javascript:void(0)" id="login-show">Already have an Account?</a>
                    <button class="btn btn-primary btn-flat" type="submit">Register</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        $('#login-show').click(function() {
            uni_modal("", "login.php")
        });

        $('#registration').submit(function(e) {
            e.preventDefault();
            start_loader();

            // Remove previous error messages
            if ($('.err-msg').length > 0) $('.err-msg').remove();

            // Client-side Validation
            var isValid = true;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var contactPattern = /^[0-9]{10}$/;  // Assuming it's a 10-digit number
            var namePattern = /^[a-zA-Z\s]+$/;  // Validates names with letters and spaces
            var password = $('#password').val();
            var email = $('#email').val();
            var contact = $('#contact').val();
            var firstname = $('#firstname').val();
            var lastname = $('#lastname').val();
            var defaultAddress = $('#default_delivery_address').val();

            // Validate Firstname
            if (firstname.trim() === "") {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Firstname is required.</div>').insertAfter('#firstname');
            } else if (!namePattern.test(firstname)) {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Firstname can only contain letters and spaces.</div>').insertAfter('#firstname');
            }

            // Validate Lastname
            if (lastname.trim() === "") {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Lastname is required.</div>').insertAfter('#lastname');
            } else if (!namePattern.test(lastname)) {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Lastname can only contain letters and spaces.</div>').insertAfter('#lastname');
            }

            // Validate Contact
            if (!contactPattern.test(contact)) {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Please enter a valid 10-digit contact number.</div>').insertAfter('#contact');
            }

            // Validate Gender
            if ($('#gender').val() === "") {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Gender is required.</div>').insertAfter('#gender');
            }

            // Validate Default Delivery Address
            if (defaultAddress.trim() === "") {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Default delivery address is required.</div>').insertAfter('#default_delivery_address');
            }

            // Validate Email
            if (!emailPattern.test(email)) {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Please enter a valid email address.</div>').insertAfter('[name="email"]');
            }

            // Validate Password
            if (password.length < 6) {
                isValid = false;
                $('<div class="alert alert-danger err-msg">Password must be at least 6 characters long.</div>').insertAfter('[name="password"]');
            }

            // If not valid, stop submission
            if (!isValid) {
                end_loader();
                return false;
            }

            // Submit the form via AJAX
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=register",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                error: function(err) {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var _err_el = $('<div>');
                        _err_el.addClass("alert alert-danger err-msg").text(resp.msg);
                        $('[name="password"]').after(_err_el);
                        end_loader();
                    } else {
                        console.log(resp);
                        alert_toast("An error occurred", 'error');
                        end_loader();
                    }
                }
            });
        });
    });
</script>
