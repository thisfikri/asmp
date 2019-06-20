<?php $this->theme->get_header('about-app');?>

<!-- Contents Section -->
<section class="casual-theme activity-page main-contents">
        <!-- Page Title-->
        <div class="activity-page page-title">
            <h1>_<i class="fa fa-paper-plane"></i> <?php echo $this->theme->get_page_title('about-app'); ?>_</h1>
        </div>
        <div class="casual-theme action-msg-notification hide"></div>
        <div class="casual-theme about-app-container">
            <ul>
				<li><strong>Nama Aplikasi:</strong> <?php echo $this->aboutapp->get_other_info('app_name');;?></li>
				<li><strong>Versi Aplikasi:</strong> <?php echo $this->aboutapp->get_version();?></li>
				<li><strong>Nama Pengembang Aplikasi:</strong> <?php echo $this->aboutapp->get_other_info('app_dev_name');?></li>
				<li><strong>Email Pengembang Aplikasi:</strong> <?php echo $this->aboutapp->get_other_info('app_dev_email');?></li>
			</ul>
            <br>
			<p class="about-contents-note"><strong>Catatan:</strong> Jika ada error yang anda tidak mengerti, anda bisa menghubungi pengembang untuk penyelesaian masalah</p>
        </div>
</section>
    <script src="<?php echo base_url('assets/lib/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.ui.widget.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.cookie.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.fileupload.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.iframe-transport.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/asmp-actionlib.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/user-page.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/photos-box.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/mail-function.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/workers.js');?>"></script>
</body>
</html>