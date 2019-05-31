<?php $this->theme->get_header('user-register');?>

<body id="frontPage">
    <!-- ASMP Web App Casual Theme -->
    <section class="casual-theme top-action">
        <button class="button lang"><i class="fa fa-language"></i></button>
    </section>
    
    <div class="casual-theme preg-stat-container">
    <?php if ($status == 'success') :?>
        <h2 class="preg-stat title"><i class="fa fa-check-circle"></i> <?php echo $title_status;?></h2>
        <p><a href="<?php echo site_url('login');?>" class="button"><i class="fa fa-arrow-left"></i> Lanjut Ke Halaman Login.</a></p>
    <?php elseif ($status == 'failed') :?>
        <h2 class="preg-stat title"><i class="fa fa-times-circle"></i> <?php echo $title_status;?></h2>
        <p><a href="<?php echo site_url('buat-akun-baru');?>" class="button"><i class="fa fa-arrow-left"></i> Kembali Ke Halaman Registrasi.</a></p>
    <?php endif;?>
    </div>
    <?php $this->session->unset_userdata('title_status', 'status');?>

<?php $this->theme->get_footer('front');?>