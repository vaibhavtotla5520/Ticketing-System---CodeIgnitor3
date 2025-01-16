<?php if ($this->session->flashdata('message')): ?>
    <!-- <div class="alert alert-info"> -->
    <?php $this->session->flashdata('message'); ?>
    <!-- </div> -->
<?php endif; ?>
<?php $this->load->view('includes/header'); ?>
<?php //echo "<pre>";print_r($data); 
?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
            <div class="card">
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive text-center" id="ticketContainer">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="paginationContainer" class="mt-3"></div>
    <?php $this->load->view('includes/footer'); ?>