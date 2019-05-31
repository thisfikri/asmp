<?php $this->theme->get_header('forgotpw');?>

<body class="casual-theme pre-register" id="frontPage">
    <!-- SIMAK Web App Casual Theme -->
    <section class="casual-theme top-action">
        <button class="button help"><i class="fa fa-info-circle"></i></button>
        <button class="button lang"><i class="fa fa-language"></i></button>
    </section>

    <div class="dim-light hide-element"></div>
    <section class="casual-theme help-box hide-element">
        <div class="title">
            <h1>Bantuan</h1>
        </div>
        <button class="button help-box-close-btn"><i class="fa fa-times fa-lg"></i></button>
        <ul>
            <li class="help-title"><i class="fa fa-arrow-circle-right"></i> <a href="#">Lupa Kata Sandi?</a></li>
        </ul>
    </section>
    <div class="casual-theme action-msg-notification front-page hide"></div>
    <section class="casual-theme fpw-rm-box">
        <h2 class="forgotpwrm-dialogue-title"><?php echo $dialogue_title; ?></h2>
        <p class="forgotpwrm-dialogue-txt"><?php echo $result_msg . ' '; ?>Kembali Ke Halaman <a href="<?php echo site_url('login');?>">Login</a></p>
        <?php if ($failed_option === TRUE) : ?>
        <div class="forgotpw-failed-opt">
            <?php if ($method == 'email') :?>
                <button class="button forgotpw-fopt-btn resend" data-previnput="<?php echo implode('-', $this->session->userdata('prev_input'));?>">Kirim Ulang</button>
            <?php endif;?>
			<a href="<?php echo site_url('lupa-kata-sandi/' . $method) ?>" class="button forgotpw-fopt-btn try-again">Kembali Ke Lupa Kata Sandi</a>
        </div>
        <?php endif;?>
    </section>
<?php $this->session->unset_userdata(array('dialogue_title', 'result_msg', 'prev_input', 'failed_option'));?>
<?php $this->theme->get_footer('front');?>