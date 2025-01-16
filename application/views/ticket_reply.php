<?php $this->load->view('includes/header'); ?>
<div class="container-fluid py-4">
    <h2>Ticket Reply For #<?= $data[0]['id'] ?></h2>
    <form id="ticketForm" class="mt-4" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="smallSelect" class="form-label">Status</label>
            <select id="smallSelect" class="form-select form-select-sm" name="status" style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
                <option value="open" <?= $data[0]['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                <option value="reopen" <?= $data[0]['status'] == 'reopen' ? 'selected' : '' ?>>Re Open</option>
                <option value="in_progress" <?= $data[0]['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="resolved" <?= $data[0]['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                <option value="closed" <?= $data[0]['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </div>
        <button type="button" id="update-status" class="btn btn-primary">Update Status</button>
        <!-- Title -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" value="<?= $data[0]['title'] ?>" disabled class="form-control" id="title" name="title" placeholder="Enter ticket title" style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
        </div>

        <div class="card-body pt-4 p-3">
            <ul class="list-group">
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                    <div class="d-flex flex-column">
                        <h6 class="mb-3 text-sm"><?= $data[0]['name']; ?></h6>
                        <span class="mb-2 text-xs"><?= $data[0]['description']; ?></span>
                    </div>
                    <div class="ms-auto text-end shadow-sm p-3 mb-5 bg-white rounded">
                    <span style="float:left;">&#128206;</span>
                        <?php $attachment = json_decode($data[0]['attachments']);
                        foreach ($attachment as $key => $value) { ?>
                            <a class="btn btn-link text-danger text-gradient px-3 mb-0" target="_blank" href="<?= base_url('public/uploads/') . $value ?>"><?= $value ?></a><br>
                        <?php   }
                        ?>
                    </div>
                </li>
                <?php
                if (!empty($replies)) {
                    foreach ($replies as $key => $value) { ?>
                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm"><?= $value['name']; ?></h6>
                                <span class="mb-2 text-xs"><?= $value['message']; ?></span>
                            </div>
                            <div class="ms-auto text-end">
                                <?php $attachment = json_decode($value['attachments']);
                                foreach ($attachment as $key => $value) { ?>
                                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" target="_blank" href="<?= base_url('public/uploads/') . $value ?>">Doc <?= $key + 1 ?></a>
                                <?php   }
                                ?>
                            </div>
                        </li>
                <?php    }
                }
                ?>
            </ul>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description2" class="form-label">Write Reply</label>
            <textarea class="form-control" id="description2" name="description2" rows="4" placeholder="Write Here"></textarea>
            <div class="invalid-feedback">Reply is required.</div>
        </div>

        <!-- File Attachments -->
        <div class="mb-3">
            <label for="fileInput" class="form-label">Attach Files</label>
            <input type="file" id="fileInput" class="form-control" multiple style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
            <ul id="fileList" class="list-unstyled mt-2"></ul>
        </div>
        <input type="hidden" name="t_id" value="<?= $data[0]['id'] ?>">
        <!-- Submit Button -->
        <button type="button" id="ticket-reply" class="btn btn-primary">Reply Ticket</button>
    </form>

</div>
<?php $this->load->view('includes/footer'); ?>