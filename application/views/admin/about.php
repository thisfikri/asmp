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
				<li><button class="check-for-update" data-token="<?php echo $this->session->userdata('CSRF');?>">Cek Versi Terbaru <i class="fa fa-arrow-circle-down fa-lg fa-fw"></i></button></li>
			</ul>
            <br>
			<p class="about-contents-note"><strong>Catatan:</strong> Jika ada error yang anda tidak mengerti, anda bisa menghubungi pengembang untuk penyelesaian masalah</p>
            <div class="update-dialogue-box">
				<h2 class="update-dialogue-title">Pembaharuan <i class="fa fa-arrow-circle-down fa-lg fa-fw"></i></h2>
				<div class="process-log">
					<a href="<?php echo site_url() . 'admin/tentang-aplikasi';?>" class="refresh">Refresh <i class="fa fa-spinner"></i></a>
				</div>
			</div>
			<br>
		</div>
		<?php if ($this->session->userdata('changelog-on')) : ?>
		<div class="changelogs-container">
			<button class="changelogs-container-close-btn"><i class="fa fa-times-circle fa-lg"></i></button>
			<h2 class="changelogs-container-title">Log Perubahan</h2>
			<ul>
				<?php echo $this->session->userdata('changelogs'); ?>
			</ul>
		</div>
		<?php endif;?>
</section>
    <script src="<?php echo base_url('assets/lib/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.ui.widget.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.cookie.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.fileupload.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.iframe-transport.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/asmp-actionlib.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/admin-page.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/photos-box.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/mail-function.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/workers.js');?>"></script>
</body>
</html>