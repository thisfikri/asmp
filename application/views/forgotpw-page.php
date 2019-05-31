<?php $this->theme->get_header('forgotpw');?>
<body class="casual-theme pre-register" id="frontPage">
    <div class="msg-box hide" title="Click Disini Untuk Menutup"></div>
    <!-- SIMAK Web App Casual Theme -->
    <section class="casual-theme top-action">
        <button class="button help"><i class="fa fa-info-circle"></i></button>
        <a href="<?php echo site_url('login');?>" class="button back"><i class="fa fa-arrow-circle-left"></i></a>
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

    <!-- Front Header -->
    <header class="casual-theme front-header">
        <!--<h1>_SIMAK_</h1>-->
        <img src="<?php echo site_url('assets/images/logo/icon-asmp-v2-black.png');?>" width="200" height="60">
        <!--<span class="version-text">v1.0</span>-->
    </header>
    <section class="casual-theme fpw-box">
        <div class="front-title">
            <h2>Pilih Metode <i class="fa fa-arrow-circle-down"></i></h2>
        </div>
        <div class="fpw-method-options">
            <a href="<?php echo site_url('lupa-kata-sandi/recovery_id');?>" class="button method-rid"><i class="fa fa-key"></i> Recovery ID (offline)</a>
            <a href="<?php echo site_url('lupa-kata-sandi/email');?>" class="button method-email"><i class="fa fa-envelope"></i> E-Mail (online)</a>
        </div>
    </section>
<?php $this->theme->get_footer('front');?>