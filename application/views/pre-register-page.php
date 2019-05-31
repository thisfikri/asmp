<?php $this->theme->get_header('pre-register');?>

<body class="casual-theme pre-register" id="frontPage">
    <div class="msg-box hide" title="Click Disini Untuk Menutup">
    </div>
    <!-- ASMP Web App Casual Theme -->
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
            <li class="help-title"><i class="fa fa-arrow-circle-right"></i> <a href="#">Registrasi Ulang?</a></li>
        </ul>
    </section>

    <section class="casual-theme language-dropdown">
        <ul>
            <li><a href="javascript:void(0)" class="language-opt" id="in_ID"><i class="fa fa-flag"></i> Indonesia</a></li>
            <li><a href="javascript:void(0)" class="language-opt" id="en_US"><i class="fa fa-flag"></i> English</a></li>
        </ul>
    </section>

    <!-- Front Header -->
    <header class="casual-theme front-header">
        <!--<h1>_SIMAK_</h1>-->
        <img src="assets/images/logo/icon-asmp-v2-black.png" width="200" height="60">
        <!--<span class="version-text">v1.0</span>-->
    </header>
    <section class="casual-theme pre-register-box">
        <div class="front-title">
            <h2>Registrasi Awal <i class="fa fa-user-plus"></i></h2>
        </div>

        <form action="javascript:void(0)" method="post" class="pre-register-form" id="formToCheck">
            <!-- Username Field -->
            <div class="field-div">
                <label for="username">
                    <i class="fa fa-user"></i>
                </label>
                <input type="text" name="username" id="uName" value="<?php echo set_value('username'); ?>" placeholder="Nama Pengguna"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- TrueName Field -->
            <div class="field-div">
                <label for="true_name">
                    <i class="fa fa-user-circle"></i>
                </label>
                <input type="text" name="true_name" id="trueName" value="<?php echo set_value('true_name'); ?>" placeholder="Nama Asli Pengguna"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- Password Field -->
            <div class="field-div">
                <label for="password">
                    <i class="fa fa-key"></i>
                </label>
                <input type="password" name="password" id="uPass" placeholder="Kata Sandi"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- Password Confirm Field -->
            <div class="field-div">
                <label for="passconfirm">
                    <i class="fa fa-key"></i>
                </label>
                <input type="password" name="passconfirm" id="uPassConfirm" placeholder="Konfirmasi Kata Sandi"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- Email Field -->
            <div class="field-div">
                <label for="email">
                    <i class="fa fa-envelope"></i>
                </label>
                <input type="email" name="email" id="uEmail" value="<?php echo set_value('email'); ?>" placeholder="E-Mail Pengguna"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- Field/Section Name Field -->
            <div class="field-div">
                <label for="field_section">
                    <i class="fa fa-users"></i>
                </label>
                <input type="text" name="field_section" id="fieldSection" value="<?php echo set_value('field_section'); ?>" placeholder="Nama Bidang Atau Bagian"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- Recovery ID -->
            <div class="field-div">
                <label for="recovery_id">
                    <i class="fa fa-barcode"></i>
                </label>
                <input type="password" name="recovery_id" id="recoveryID" value="<?php echo random_string('alnum', 7) . '-' . random_string('alnum', 7) . '-' . random_string('alnum', 7); ?>" disabled><button class="button show-hide-btn"><i class="fa fa-eye"></i></button>
            </div>
            <div class="clearfix"></div>
            <button type="submit" class="button pre-register-btn" name="preg_submit">Buat <i class="fa fa-user-plus"></i></button>
        </form>
    </section>

<?php $this->theme->get_footer('front');?>