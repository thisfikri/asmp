<?php $this->theme->get_header('forgotpw');?>
<body class="casual-theme pre-register" id="frontPage">
<div class="msg-box hide" title="Click Disini Untuk Menutup"></div>
    <!-- SIMAK Web App Casual Theme -->
    <section class="casual-theme top-action">
        <button class="button help"><i class="fa fa-info-circle"></i></button>
        <a href="<?php echo site_url('lupa-kata-sandi');?>" class="button back"><i class="fa fa-arrow-circle-left"></i></a>
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
            <h2>Lupa Kata Sandi <span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-key fa-stack-1x" style="color: #47b2ab"></i></span></h2>
        </div>
        <?php if ($respass_status === FALSE) :?>
        <form action="javascript:void(0)" method="post">
            <!-- Username Field -->
            <div class="field-div">
                <label for="username">
                    <i class="fa fa-user"></i>
                </label>
                <input type="text" name="username" id="uName" placeholder="Nama Pengguna"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- Password Field -->
            <div class="field-div">
                <label for="rid">
                    <i class="fa fa-key"></i>
                </label>
                <input type="password" name="rid" id="RID" placeholder="Recovery ID"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <button type="submit" class="button fpw-btn" name="fpwm-rid">Proses <i class="fa fa-sync"></i></button>
        </form>
        <?php elseif ($respass_status === TRUE) : ?>
        <p style="font-size: 12px;padding: 5px;">* Reset Kata Sandi, kata sandi lama akan diganti dengan yang baru</p>
        <form action="javascript:void(0)" method="post">
            <!-- Username Field -->
            <div class="field-div">
                <label for="new_password">
                    <i class="fa fa-user"></i>
                </label>
                <input type="password" name="new_password" id="uPass" placeholder="Kata Sandi Baru"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- Password Field -->
            <div class="field-div">
                <label for="new_password_confirm">
                    <i class="fa fa-key"></i>
                </label>
                <input type="password" name="new_password_confirm" id="uPassConfirm" placeholder="Konfirmasi Kata Sandi Baru"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <button type="submit" class="button fpw-btn" name="respass-rid">Proses <i class="fa fa-sync"></i></button>
        </form>
        <?php endif;?>
    </section>
<?php $this->theme->get_footer('front');?>