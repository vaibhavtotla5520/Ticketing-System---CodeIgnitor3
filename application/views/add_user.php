<?php $this->load->view('includes/header'); ?>
<div class="container-fluid py-4">
    <h2>Add User</h2>
    <form id="userForm" class="mt-4" enctype="multipart/form-data">
        <!-- Name -->
        <div class="mb-3 col-sm-4">
            <label for="title" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter User Name" style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
            <div class="invalid-feedback">Name is required.</div>
        </div>
        <div class="mb-3 col-sm-4">
            <label for="title" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter User Email" style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
            <div class="invalid-feedback">Email is required.</div>
        </div>
        <div class="mb-3 col-sm-4">
            <label for="title" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter User Password" style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
            <div class="invalid-feedback">Password is required.</div>
        </div>
        <div class="mb-3 col-sm-4">
            <label for="title" class="form-label">Select Roles</label>
            <select class="form-control" name="roles" id="roles" aria-label="Default select example" style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
                <option value="0" disabled selected> Select Roles</option>
                <?php if (!empty($master_roles)) {
                    foreach ($master_roles as $roles) { ?>
                        <option value="<?= $roles["id"] ?>"> <?= $roles["name"] ?></option>
                <?php    }
                } ?>
            </select>
        </div>
        <div class="mb-3">
            <button type="button" id="add-user" class="btn btn-primary">Add User</button>
        </div>
    </form>
</div>

</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    $("#add-user").click(function() {
        // Clear previous validation errors
        $(".invalid-feedback").hide();
        $(".form-control").removeClass("is-invalid");

        // Gather form data
        let formData = {
            name: $("#name").val().trim(),
            email: $("#email").val().trim(),
            password: $("#password").val(),
            roles: $("#roles").val(),
        };

        // Validate form inputs
        let hasError = false;

        if (!formData.name) {
            $("#name").addClass("is-invalid");
            $("#name").next(".invalid-feedback").show();
            hasError = true;
        }

        if (!formData.email || !validateEmail(formData.email)) {
            $("#email").addClass("is-invalid");
            $("#email").next(".invalid-feedback").text("Valid email is required.").show();
            hasError = true;
        }

        if (!formData.password || formData.password.length < 6) {
            $("#password").addClass("is-invalid");
            $("#password").next(".invalid-feedback").text("Password must be at least 6 characters.").show();
            hasError = true;
        }

        if (!formData.roles) {
            $("#roles").addClass("is-invalid");
            $("#roles").next(".invalid-feedback").text("Please select a role.").show();
            hasError = true;
        }

        if (hasError) return;

        // Submit data via AJAX
        $.ajax({
            url: "<?= base_url('add_user_action') ?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status == 1) {
                    $.notify(response.message, "success");
                    $("#userForm")[0].reset();
                } else if (response.error) {
                    $.notify(response.error, "error");
                }
            },
            error: function() {
                $.notify("An error occurred while processing your request.", "error");
            },
        });
    });

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
</script>