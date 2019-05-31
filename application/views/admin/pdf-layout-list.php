<?php $this->theme->get_header('pdf-layout');?>
<!-- Contents Section -->
<section class="casual-theme activity-page main-contents">
    <!-- Page Title-->
    <div class="activity-page page-title">
        <h1>_<i class="fa fa-users"></i> <?php echo $this->theme->get_page_title('pdf-layout'); ?>_</h1>
    </div>
    <div class="casual-theme action-msg-notification hide"></div>
    <div class="casual-theme pdf-layout-create-btn-container">
        <button class="button pdf-layout-create-btn">Buat Layout Baru <i class="fa fa-plus-circle"></i></button>
    </div>
    <div class="casual-theme pdf-components-sb">
    </div>
    <div class="casual-theme pdf-layout-list-container">
    <?php foreach($pdf_layouts as $key ) : ?>
    <div class="pdf-layout-box" id="layout<?php echo $key->id; ?>">
        <i class="fa fa-file-pdf fa-inverse pdf-icon"></i>
        <div class="pdf-layout-action-container">
            <button class="button pdf-layout-action edit"><i class="fa fa-edit fa-fw fa-lg"></i></button>
            <button class="button pdf-layout-action view"><i class="fa fa-eye fa-fw fa-lg"></i></button>
            <button class="button pdf-layout-action active-non-active"><?php echo ($key->layout_status == 'active') ? '<i class="fa fa-toggle-on fa-fw fa-lg"></i>' : '<i class="fa fa-toggle-off fa-fw fa-lg"></i>'; ?></button>
            <button class="button pdf-layout-action remove"><i class="fa fa-trash-alt fa-fw fa-lg"></i></button>
        </div>
        <p class="pdf-layout-name"><?php echo $key->layout_name; ?></p>
    </div>
    <?php endforeach;?>
        <!-- <div class="pdf-layout-box" id="layout1">
            <i class="fa fa-file-pdf fa-inverse pdf-icon"></i>
            <div class="pdf-layout-action-container">
                <button class="button pdf-layout-action edit"><i class="fa fa-edit fa-fw fa-lg"></i></button>
                <button class="button pdf-layout-action view"><i class="fa fa-eye fa-fw fa-lg"></i></button>
                <button class="button pdf-layout-action active-non-active"><i class="fa fa-toggle-off fa-fw fa-lg"></i></button>
                <button class="button pdf-layout-action remove"><i class="fa fa-trash-alt fa-fw fa-lg"></i></button>
            </div>
            <p class="pdf-layout-name">Default</p>
        </div> -->
    </div>
</section>
    <script src="<?php echo base_url('assets/lib/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.ui.widget.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.cookie.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.fileupload.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.iframe-transport.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/xss-filters.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/asmp-actionlib.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/admin-page.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/photos-box.js'); ?>"></script>
</body>
</html>