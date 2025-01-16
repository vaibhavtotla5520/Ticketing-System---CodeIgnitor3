<footer class="footer py-4  ">
</footer>
</div>
</main>
<div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
        <i class="material-icons py-2">settings</i>
    </a>
    <div class="card shadow-lg">
        <div class="card-header pb-0 pt-3">
            <div class="float-start">
                <h5 class="mt-3 mb-0">Profile</h5>
            </div>
            <div class="float-end mt-4">
                <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                    <i class="material-icons">clear</i>
                </button>
            </div>
            <!-- End Toggle Button -->
        </div>
        <hr class="horizontal dark my-1">
        <?php if (!empty($user_roles)) { ?>
            <div class="card-body pt-sm-3 pt-0">
                <select class="form-select" id="change_role_id" aria-label="Default select example" style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;" onchange="change_user_role()">
                    <?php foreach ($user_roles as $roles) { ?>
                        <option value="<?= $roles["role_id"] ?>" <?= $this->session->userdata("role_id") == $roles["role_id"] ? "selected" : "" ?>> <?= $roles["name"] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
            <hr class="horizontal my-1 p-5">
            <table class="table">
                <tbody>
                    <tr>
                        <th scope="row">Name</th>
                        <td><?= $this->session->userdata('name') ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $this->session->userdata('email') ?></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><?= $this->session->userdata('role_name') ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
    </div>
</div>
<!--   Core JS Files   -->
<script src="public/assets/js/core/popper.min.js"></script>
<script src="public/assets/js/core/bootstrap.min.js"></script>
<script src="public/assets/js/core/notify.js"></script>
<script src="public/assets/js/core/notify.min.js"></script>
<script src="public/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="public/assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="public/assets/js/plugins/chartjs.min.js"></script>

<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="public/assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>
<script>
    $(document).ready(function() {
        const fileInput = document.getElementById("fileInput");
        const fileList = document.getElementById("fileList");
        let filesArray = [];

        fileInput.addEventListener("change", () => {
            Array.from(fileInput.files).forEach(file => {
                filesArray.push(file);
                addFileToList(file);
            });
            fileInput.value = "";
        });

        function addFileToList(file) {
            const listItem = document.createElement("li");
            listItem.className = "d-flex justify-content-between align-items-center";
            listItem.innerHTML = `
            <span>${file.name}</span>
            <button type="button" class="btn btn-sm btn-danger remove-btn">Remove</button>
        `;

            listItem.querySelector(".remove-btn").addEventListener("click", () => {
                filesArray = filesArray.filter(f => f !== file);
                fileList.removeChild(listItem);
            });

            fileList.appendChild(listItem);
        }

        $("#submit-ticket").on("click", function(e) {
            e.preventDefault();

            $(".form-control").removeClass("is-invalid");

            let isValid = true;
            const title = $("#title").val().trim();
            const description = tinymce.get("description").getContent().trim();

            // Validate title
            if (!title) {
                $("#title").addClass("is-invalid");
                isValid = false;
            }

            // Validate description
            if (!description) {
                $.notify("Description cannot be empty!", "Error"); // Show an alert for invalid description
                isValid = false;
            }

            if (isValid) {
                const formData = new FormData();
                formData.append("title", title);
                formData.append("description", description);

                // Attach files to FormData
                filesArray.forEach((file, index) => {
                    formData.append(`attachments[]`, file);
                });

                $.ajax({
                    url: "<?= base_url('generate-ticket') ?>",
                    type: "POST",
                    data: formData,
                    processData: false, // Prevent automatic processing of data
                    contentType: false, // Let FormData set the content type
                    success: function(response) {
                        if (!response.error) {
                            $.notify("Ticket submitted successfully!", "success");
                        } else {
                            $.notify(response.error, "error");
                        }
                        $("#ticketForm")[0].reset();
                        tinymce.get("description").setContent(""); // Clear TinyMCE editor
                        fileList.innerHTML = ""; // Clear the file list
                        filesArray = []; // Reset the files array
                    },
                    error: function(xhr) {
                        $.notify("An error occurred while submitting the ticket.", "error");
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        $("#ticket-reply").on("click", function(e) {
            e.preventDefault();

            $(".form-control").removeClass("is-invalid");

            let isValid = true;
            const description = tinymce.get("description2").getContent().trim();

            // Validate description
            if (!description) {
                $.notify("Description cannot be empty!", "success"); // Show an alert for invalid description
                isValid = false;
            }

            if (isValid) {
                const formData = new FormData();
                <?php $t_id = isset($_GET['t_id']) ? $_GET['t_id'] : ''; ?>
                formData.append("description2", description);
                formData.append("t_id", <?= $t_id ?>);

                // Attach files to FormData
                filesArray.forEach((file, index) => {
                    formData.append(`attachments[]`, file);
                });

                $.ajax({
                    url: "<?= base_url('add-ticket-reply') ?>",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    processData: false, // Prevent automatic processing of data
                    contentType: false, // Let FormData set the content type
                    success: function(response) {
                        // console.log(response);
                        if (response.success) {
                            $.notify(response.success, "success");
                        } else {
                            $.notify(response.error, "error");
                        }
                        tinymce.get("description2").setContent(""); // Clear TinyMCE editor
                        fileList.innerHTML = ""; // Clear the file list
                        filesArray = []; // Reset the files array
                        window.location.href = "<?= base_url('ticket-detail') . '?t_id=' . $t_id ?>";
                    },
                    error: function(xhr) {
                        $.notify("An error occurred while Replying the ticket.", "error");
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        $("#update-status").on("click", function() {
            const selectedValue = $("#smallSelect").val();
            const selectedName = $("#smallSelect option:selected").text();
            <?php $t_id = isset($_GET['t_id']) ? $_GET['t_id'] : 0; ?>
            if (window.confirm('Update Status : ' + selectedName)) {
                $.ajax({
                    url: "<?= base_url('update-status') ?>",
                    type: "POST",
                    data: {
                        status: selectedValue,
                        t_id: <?= $t_id ?>
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            $.notify(response.success, "success");
                        } else if (response.error) {
                            $.notify("Error: " + response.error, "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText);
                        $.notify("An error occurred while updating the status.", "error");
                    }
                });
            }
        });
    });
</script>
<script src="https://cdn.tiny.cloud/1/oesle640ne4n0ecv2orxiu7qsyxx9ima1roxy2s913hiurm9/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
    tinymce.init({
        selector: 'textarea',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    });
</script>

</html>
<div id="ticketContainer"></div>

<script>
    function change_user_role() {
        const selectedValue = $('#change_role_id').val();
        const selectedName = $('#change_role_id').find("option:selected").text();
        if (window.confirm('Change Role To : ' + selectedName)) {
            if (selectedValue) {
                $.ajax({
                    url: "<?= base_url('change-role') ?>",
                    method: "POST",
                    data: {
                        role_id: selectedValue,
                        role_name: selectedName
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "Success") {
                            window.location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        $("#responseContainer").html("<p>An error occurred while fetching data.</p>");
                    }
                });
            } else {
                $("#responseContainer").html("");
            }
        }
    }

    function fetchTickets(page = 1) {
        $.ajax({
            url: "<?= base_url('fetch-tickets') ?>",
            method: "GET",
            data: {
                page: page
            },
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    let tableHtml = `
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Ticket ID</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Status</th>
                                <th scope="col">Created At</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                    response.data.forEach(ticket => {
                        var statusBadge = '';
                        if (ticket.status == 'open') {
                            statusBadge = 'warning';
                        } else if (ticket.status == 'reopen') {
                            statusBadge = 'danger';
                        } else if (ticket.status == 'closed') {
                            statusBadge = 'primary';
                        } else if (ticket.status == 'in_progress') {
                            statusBadge = 'success';
                        } else if (ticket.status == 'resolved') {
                            statusBadge = 'info';
                        }
                        tableHtml += `
                        <tr>
                            <th scope="row">${ticket.id}</th>
                            <td>${ticket.title}</td>
                            <td><span class="badge badge-sm bg-gradient-${statusBadge}">${ticket.status}</span></td>
                            <td>${ticket.created_at}</td>
                            <td>${ticket.name}</td>
                            <td>
                                <a class="btn btn-primary" href="<?= base_url('ticket-detail') ?>?t_id=${ticket.id}">Open</a>
                            </td>
                        </tr>
                    `;
                    });

                    tableHtml += `
                        </tbody>
                    </table>
                `;
                    $("#ticketContainer").html(tableHtml);
                    renderPagination(response.current_page, response.total_pages);
                } else {
                    $("#ticketContainer").html("<p>No tickets found.</p>");
                    $("#paginationContainer").html("");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching tickets:", error);
                $("#ticketContainer").html("<p>An error occurred while fetching tickets.</p>");
            }
        });
    }

    function renderPagination(currentPage, totalPages) {
        let paginationHtml = `<nav aria-label="Page navigation"><ul class="pagination">`;

        // First button
        if (currentPage > 1) {
            paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="javascript:;" onclick="fetchTickets(1)" aria-label="First">
                    <span>&laquo;&laquo;</span>
                </a>
            </li>
        `;
        }

        // Previous button
        if (currentPage > 1) {
            paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="javascript:;" onclick="fetchTickets(${currentPage - 1})" aria-label="Previous">
                    <span>&laquo;</span>
                </a>
            </li>
        `;
        }

        // Page number buttons
        let pageRange = 3; // Number of pages to show in the pagination
        let startPage = Math.max(currentPage - Math.floor(pageRange / 2), 1);
        let endPage = Math.min(startPage + pageRange - 1, totalPages);

        for (let i = startPage; i <= endPage; i++) {
            // console.log(currentPage);
            paginationHtml += `
            <li class="page-item ${i == currentPage ? 'active' : ''}">
                <a class="page-link" href="javascript:;" onclick="fetchTickets(${i})">${i}</a>
            </li>
        `;
        }

        // Next button
        if (currentPage < totalPages) {
            paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="javascript:;" onclick="fetchTickets(${currentPage + 1})" aria-label="Next">
                    <span>&raquo;</span>
                </a>
            </li>
        `;
        }

        // Last button
        if (currentPage < totalPages) {
            paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="javascript:;" onclick="fetchTickets(${totalPages})" aria-label="Last">
                    <span>&raquo;&raquo;</span>
                </a>
            </li>
        `;
        }

        paginationHtml += `</ul></nav>`;
        $("#paginationContainer").html(paginationHtml);
    }
    <?php
    if ($this->uri->segment(1) == 'dashboard') {
    ?>
        fetchTickets();
        setInterval(fetchTickets, 15000);
    <?php
    } ?>

    function edit_user(user_id) {
        $.ajax({
            url: "<?= base_url('get-user-details') ?>",
            method: "POST",
            data: {
                user_id: user_id
            },
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $("#editUserId").val(response.data.id);
                    $("#editUserName").val(response.data.name);
                    $("#editUserEmail").val(response.data.email);
                    $("#editUserRole").val(response.data.role_id).prop('selected', true);
                    if (response.data.is_active == 1) {
                        $("#status_active").prop("checked", true);
                    } else {
                        $("#status_inactive").prop("checked", true);
                    }

                    $("#editUserModal").modal("show");
                } else {
                    $.notify(response.message, "error");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching user data:", error);
                $.notify("An error occurred. Please try again.", "error");
            }
        });
    }

    $("#editUserForm").submit(function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    // console.log(formData);
    $.ajax({
        url: "<?= base_url('update-user') ?>",
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                $.notify("User updated successfully.", "success");
                $("#editUserModal").modal("hide");
            } else {
                $.notify("Failed to update user.", "error");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error updating user:", error);
            $.notify("An error occurred. Please try again.", "error");
        }
    });
});
</script>