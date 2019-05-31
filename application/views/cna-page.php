<?php $this->theme->get_header('user-register');?>

<body class="casual-theme cna" id="frontPage">
    <div class="msg-box hide" title="Click Disini Untuk Menutup"></div>
    <!-- SIMAK Web App Casual Theme -->
    <section class="casual-theme top-action">
        <button class="button help"><i class="fa fa-question-circle"></i></button>
        <a href="<?php echo site_url('login'); ?>" class="button back"><i class="fa fa-arrow-circle-left"></i></a>
        <button class="button lang"><i class="fa fa-language"></i></button>
    </section>

    <div class="dim-light hide-element"></div>
    <section class="casual-theme help-box hide-element">
        <div class="title">
            <h1>Bantuan</h1>
        </div>
        <button class="button help-box-close-btn"><i class="fa fa-times fa-lg"></i></button>
        <ul>
            <li class="help-title"><i class="fa fa-arrow-circle-right"></i> <a href="#">Buat Akun Baru?</a></li>
        </ul>
    </section>
    
    <!-- Front Header -->
    <header class="casual-theme front-header">
         <!--<h1>_SIMAK_</h1>-->
         <img src="<?php echo site_url('assets/images/logo/icon-asmp-v2-black.png');?>" width="200" height="60">
        <!--<span class="version-text">v1.0</span>-->  
    </header>
    <section class="casual-theme cna-box">
        <div class="front-title">
            <h2>Buat Akun Baru <i class="fa fa-user-plus"></i></h2>
        </div>

        <form action="javascript:void(0)" method="post" id="formToCheck">
            <!-- Username Field -->
            <div class="field-div">
                <label for="username">
                    <i class="fa fa-user"></i>
                </label>
                <input type="text" name="username" id="uName" placeholder="Nama Pengguna" value="<?php echo set_value('username'); ?>"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- TrueName Field -->
            <div class="field-div">
                <label for="true_name">
                    <i class="fa fa-user-circle"></i>
                </label>
                <input type="text" name="true_name" id="trueName" placeholder="Nama Asli Pengguna" value="<?php echo set_value('true_name'); ?>"><span class="form-hint"></span>
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
                <input type="email" name="email" id="uEmail" placeholder="E-Mail Pengguna" value="<?php echo set_value('email'); ?>"><span class="form-hint"></span>
            </div>
            <div class="clearfix"></div>
            <!-- Field/Section Name Field -->
            <div class="field-div">
                <label for="field_section">
                    <i class="fa fa-users"></i>
                </label>
                <select name="field_section" id="fieldSection">
                    <option value="null"></option>
                    <?php foreach ($field_sections as $key) : ?>
                        <option value="<?php echo $key->field_section_name?>"><?php echo $key->field_section_name?></option>
                    <?php endforeach;?>
                </select>
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
            <button type="submit" class="button cna-btn" name="login">Buat <i class="fa fa-user-plus"></i></button>
        </form>
    </section>

<?php $this->theme->get_footer('front');?>