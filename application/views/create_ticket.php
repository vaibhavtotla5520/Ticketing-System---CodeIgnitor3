<?php $this->load->view('includes/header'); ?>
<div class="container-fluid py-4">
    <h2>Create a Ticket</h2>
    <form id="ticketForm" class="mt-4" enctype="multipart/form-data">
        <!-- Title -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Enter ticket title" style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
            <div class="invalid-feedback">Title is required.</div>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe your issue"></textarea>
            <div class="invalid-feedback">Description is required.</div>
        </div>

        <!-- File Attachments -->
        <div class="mb-3">
            <label for="fileInput" class="form-label">Attach Files</label>
            <input type="file" id="fileInput" class="form-control" multiple style="border: 1px solid #e0e0e0; padding-left: 14px; background-color: #fff;">
            <ul id="fileList" class="list-unstyled mt-2"></ul>
        </div>

        <!-- Submit Button -->
        <button type="button" id="submit-ticket" class="btn btn-primary">Create Ticket</button>
    </form>

</div>
<?php $this->load->view('includes/footer'); ?>