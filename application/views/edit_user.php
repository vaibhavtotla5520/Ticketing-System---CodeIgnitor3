<?php $this->load->view('includes/header'); ?>
<div class="container-fluid py-4">
    <h2>Users</h2>
    <?php //echo "<pre>";
    //print_r($users); 
    ?>
    <div class="card-body px-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">USER</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Function</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">CREATED AT</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">CREATED BY</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ACTION </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm"><?= $user['name'] ?></h6>
                                        <p class="text-xs text-secondary mb-0"><?= $user['email'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0"><?= $user['role_name'] ?></p>
                                <p class="text-xs text-secondary mb-0">USER ID : <?= $user['id'] ?></p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <span class="badge badge-sm bg-gradient-<?= $user['is_active'] == 1 ? "success" : "secondary" ?>"><?= $user['is_active'] == 1 ? "Active" : "In-Active" ?></span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold"><?= $user['created_at'] ?></span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold"><?= $user['created_by_name'] ?></span>
                            </td>
                            <td class="align-middle text-center">
                                <a href="#" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user" onclick="edit_user(<?= $user['id'] ?>)">
                                    <i class="material-icons">&#xe3c9;</i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="form-group">
                        <label for="editUserName">Name</label>
                        <input type="text" class="form-control" id="editUserName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editUserEmail">Email</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editUserRole">Role</label>
                        <select class="form-control" id="editUserRole" name="role" required>
                            <option value="">Select Role</option>
                            <option value="1">USER</option>
                            <option value="6">TEAM LEADER</option>
                            <option value="7">ADMIN</option>
                            <option value="9">SUPER ADMIN</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editUserStatus">Status</label>
                        <div>
                            <label class="mr-3">
                                <input type="radio" id="status_active" name="status" value="1"> Active
                            </label>
                            <label>
                                <input type="radio" id="status_inactive" name="status" value="0"> Inactive
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick='$("#editUserModal").modal("hide");'>Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>