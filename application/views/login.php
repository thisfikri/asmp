<?php $this->theme->get_header('login');?>

<body class="casual-theme" id="frontPage">
    <div class="msg-box hide" title="Click Disini Untuk Menutup"></div>

    <?php if ($msg): ?>
		<div class="msg-box php <?php echo $msg_type; ?>" title="Click Disini Untuk Menutup">
			<p class="msg-txt"><?php echo $msg; ?></p>
		</div>
	<?php endif;?>

    <!-- SIMAK Web App Casual Theme -->
    <section class="casual-theme top-action">
        <button class="button help"><i class="fa fa-question-circle"></i></button>
        <button class="button lang"><i class="fa fa-language"></i></button>
        <div class="clearfix"></div>
    </section>

    <div class="dim-light hide-element"></div>
    <section class="casual-theme help-box hide-element">
        <div class="title">
            <h1>Bantuan</h1>
        </div>
        <button class="button help-box-close-btn"><i class="fa fa-times fa-lg"></i></button>
        <ul>
            <li class="help-title"><i class="fa fa-arrow-circle-right"></i> <a href="#">Login?</a></li>
        </ul>
    </section>

    <!-- Front Header -->
    <header class="casual-theme front-header">
        <!--<h1>_SIMAK_</h1>-->
        <img src="assets/images/logo/icon-asmp-v2-black.png" width="120"> <!-- Original:width="200" height="60" --> 
        <!--<span class="version-text">v1.0</span>-->
    </header>
    <section class="casual-theme login-box">
        <div class="front-title">
            <h2>Login <i class="fa fa-lock"></i></h2>
        </div>

        <form action="javascript:void(0)" method="post" class="login-form" id="formToCheck">
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
                <label for="password">
                    <i class="fa fa-key"></i>
                </label>
                <input type="password" name="password" id="uPass" placeholder="Password"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <button type="submit" class="button login-btn" name="login">Masuk <i class="fa fa-sign-in-alt"></i></button>
        </form>
        <!-- Link Option Box -->
        <div class="link-option-box">
        <?php if ($register_limit != 0): ?>
            <a href="<?php echo site_url('buat-akun-baru');?>">Buat Akun Baru <i class="fa fa-user-plus"></i></a> |
        <?php endif;?>
            <a href="<?php echo site_url('lupa-kata-sandi');?>">Lupa Kata Sandi <i class="fa fa-question"></i></a>
        </div>
    </section>

<?php $this->theme->get_footer('front');?>