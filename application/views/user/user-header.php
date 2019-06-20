<!DOCTYPE html>
<html lang="id-ID">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASMP - <?php echo ucwords($page_title); ?></title>
    <link rel="icon" href="<?php echo base_url('assets/images/logo/asmp-browser-icon.png');?>" type="image/png">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/main-style.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/framework/font-awesome/css/fontawesome-all.min.css');?>">
    <style type="text/css" media="all">
		@import "<?php echo site_url().'assets/widgEditor_1.0.1/css/widgEditor.css';?>";
	</style>
	
    
</head>

<body class="casual-theme" id="userDashboard">
<iframe src="<?php echo site_url('assets/audio/2-seconds-of-silence.mp3');?>" allow="autoplay" style="display:none"></iframe>
<audio src="<?php echo site_url('assets/audio/system-fault.mp3');?>" type="audio/mp3"></audio>
    <!-- Main Header -->
    <header class="casual-theme activity-page header">
        <!-- <button class="navbar-icon arrow">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
        </button> -->

        <div class="app-title"><!--<h1>_SIMAK_</h1><span class="version-text">v1.0</span>-->
            <img src="<?php echo site_url('assets/images/logo/icon-asmp-v2-white.png')?>" width="100" height="40">
        </div>

        <button class="button options"><i class="fa fa-ellipsis-v"></i></button>
        <div class="notification-area">
                <span class="fa-stack mail-count-num" style="<?php echo ($new_im['display']) ? 'display: inline-block' : 'display: none';?>">
                <i class="fa fa-circle fa-stack-2x"></i><b class="fa-stack-1x" style="color: #48c8d1"><?php echo $new_im['count']; ?></b></span>
            <!-- <button class="button notification"><i class="fa fa-bell fa-fw"></i></button> -->
        </div>
    </header>
    <!-- This Notification feature is avaible soon as possible -->
    <!-- <div class="casual-theme activity-page notification-box outerdiv">
        <div class="casual-theme activity-page notification-box innerdiv">
            <div class="notification-item incoming-mail">
                <ul>
                    <li class="item-notification-type"><i class="fa fa-envelope"></i> Surat Masuk</li>
                    <li class="subject">Subject: CEO Important Meeting</li>
                    <li class="sender">Sender: Secretary</li>
                </ul>
            </div>

            <div class="notification-item incoming-mail">
                <ul>
                    <li class="item-notification-type"><i class="fa fa-envelope"></i> Incoming Mail</li>
                    <li class="subject">Subject: CEO Important Meeting</li>
                    <li class="sender">Sender: Secretary</li>
                </ul>
            </div>

            <div class="notification-item incoming-mail">
                <ul>
                    <li class="item-notification-type"><i class="fa fa-envelope"></i> Incoming Mail</li>
                    <li class="subject">Subject: CEO Important Meeting</li>
                    <li class="sender">Sender: Secretary</li>
                </ul>
            </div>
        </div>
    </div> -->

    <div class="casual-theme activity-page options-dropdown">
        <ul>
            <li><a href="<?php echo site_url('user/settings');?>" class="preference-btn"><i class="fa fa-wrench"></i> Preference</a></li>
            <li><a href="javascript:void(0)" class="logout-btn" data-token="<?php echo $this->session->userdata('CSRF');?>"><i class="fa fa-sign-out-alt"></i> Keluar</a></li>
        </ul>
    </div>
    
    <!-- Main Navigation -->
    <nav class="casual-theme activity-page nav sidebar">
        <div class="side-profile">
            <p class="button user-role"><i class="fa fa-user"></i> <?php echo ucfirst($uprof_data->role)?></p>
            <button class="button edit-profile"><i class="fa fa-user-edit"></i> Edit</button>
            <div class="profile-img">
                <img src="<?php echo site_url('gallery/' . $uprof_data->gallery_dir . '/' . $uprof_data->profile_picture);?>" alt="profile-image" width="130" height="130" style="border-radius: 50%;">
            </div>
            <button class="button choose-btn hide"><i class="fa fa-image"></i> Choose</button>
            <div class="casual-theme photos-box-container">
                <div class="photos-box">
                    <div class="white-container">
                        <button class="option-tab current" id="upload"><i class="fa fa-upload"></i> Upload</button>
                        <button class="option-tab" id="gallery"><i class="fa fa-images"></i> Gallery</button>
                        <div class="upload-container">
                            <label for="files[]" class="file-upload-input">
                                <input type="file" name="files[]" id="fileUpload" title="" multiple>
                                <button class="chs-file-btn"><i class="fa fa-image"></i> Pilih File</button>
                                <p class="file-name-txt">Tidak ada file yang dipilih.</p>
                                <div class="clearfix"></div>
                            </label>
                            <div class="upload-buttons">
                                <button class="upload-btn"><i class="fa fa-upload"></i> Upload</button>
                            </div>
                                
                            <div class="uploaded-images">
                                <!-- <div class="image-container unchecked" id="upld0">
                                    <img src="../assets/images/profile/default.png" alt="profile-photo" class="image">
                                </div> -->
                            </div>
                        </div>
                        <div class="gallery-container hide">
                            <div class="image-list">
                            </div>
                        </div>
                        <button class="photos-box-close-btn"><i class="fa fa-times fa-lg"></i></button>
                        <button class="delete-image-btn"><i class="fa fa-trash-alt fa-lg"></i></button>
                        <button class="choose-image-btn" data-checkedimg="0" disabled><i class="fa fa-save fa-lg"></i></button>
                        <button class="select-all-btn" data-tab="upld"><i class="fa fa-check-circle fa-lg"></i></button>
                    </div>
                </div>
            </div>
            <div class="profile-info">
                <ul>
                    <li>Nama: <?php echo $uprof_data->true_name?></li>
                    <li>Posisi: <?php echo $uprof_data->position;?></li>
                </ul>

                <form class="update-type 01 hide" method="POST">
                    <h3><i class="fa fa-user-circle"></i> Name Changes</h3>
                    <label for="true_name">True Name</label>
                    <input type="text" name="true_name" value="<?php echo $uprof_data->true_name?>"><span class="hint"></span>
                    <div class="clearfix"></div>
                    <label for="username">Username</label>
                    <input type="text" name="username" value="<?php echo $uprof_data->username?>"><span class="hint"></span>
                    <div class="clearfix"></div>
                    <label for="password">Password</label>
                    <input type="password" name="password" value=""><span class="hint"></span>
                    <div class="clearfix"></div>
                    <button type="submit" name="update-type-01-submit" class="button"><i class="fa fa-save"></i> Simpan</button>
                </form>

                <form class="update-type 02 hide" method="POST">
                        <h3><i class="fa fa-key"></i> Password Changes</h3>
                    <label for="old_password">Old Password</label>
                    <input type="password" name="old_password" value=""><span class="hint"></span>
                    <div class="clearfix"></div>
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" value=""><span class="hint"></span>
                    <div class="clearfix"></div>
                    <label for="new_password_confirm">New Password Confirm</label>
                    <input type="password" name="new_password_confirm" value=""><span class="hint"></span>
                    <div class="clearfix"></div>
                    <button type="submit" name="update-type-01-submit" class="button"><i class="fa fa-save"></i> Simpan</button>
                    </form>
            </div>
        </div>
        <ul>
            <li><a href="<?php echo site_url('user/dashboard');?>"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="<?php echo site_url('user/surat-masuk');?>"><i class="fa fa-inbox"></i> Incoming Mail</a></li>
            <?php if ($om_auth) : ?>
            <li><a href="<?php echo site_url('user/surat-keluar');?>"><i class="fa fa-paper-plane"></i> Outgoing Mail</a></li>
            <?php endif;?>
            <li><a href="<?php echo site_url('user/tentang-aplikasi');?>"><i class="fa fa-info-circle"></i> About</a></li>
        </ul>
    </nav>