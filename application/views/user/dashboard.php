<?php $this->theme->get_header('dashboard'); ?>

<!-- Contents Section -->
<section class="casual-theme activity-page main-contents">
        <!-- Page Title-->
        <div class="activity-page page-title">
            <h1>_<i class="fa fa-tachometer-alt"></i> <?php echo $this->theme->get_page_title('dashboard'); ?>_</h1>
        </div>
        <div class="casual-theme di-box-container">
            <div class="di-box incoming-mail">
                <div class="title">
                    <h2><i class="fa fa-inbox"></i> Incoming Mail</h2>
                </div>

                <div class="info-text">
                    <ul>
                        <li><i class="fa fa-envelope fa-lg"></i> New: <?php echo $new_im['count'];?></li>
                        <li><i class="fa fa-envelope-open fa-lg"></i> Old: <?php echo $old_im;?></li>
                        <li><i class="fa fa-equals fa-lg"></i> Total: <?php echo $im_count;?></li>
                    </ul>
                </div>
            </div>
            <?php if ($om_auth) :?>
            <div class="di-box outgoing-mail">
                <div class="title">
                    <h2><i class="fa fa-paper-plane"></i> Outgoing Mail</h2>
                </div>

                <div class="info-text">
                    <ul>
                        <li><i class="fa fa-paper-plane fa-lg"></i> Mail Sent: <?php echo $om_count;?></li>
                        <li><i class="fa fa-exclamation-circle"></i> Mail Not Sent: 0</li>
                        <li><i class="fa fa-equals fa-lg"></i> Total: <?php echo $om_count;?></li>
                    </ul>
                </div>
            </div>
            <?php endif;?>
            <div class="di-box trash-can">
                <div class="title">
                    <h2><i class="fa fa-trash-alt"></i> Trash Can</h2>
                </div>

                <div class="info-text">
                    <ul>
                        <li><i class="fa fa-envelope fa-lg"></i> Incoming Mail: <?php echo $imtr_count;?></li>
                        <li><i class="fa fa-paper-plane fa-lg"></i> Outgoing Mail: <?php echo $omtr_count;?></li>
                        <li><i class="fa fa-equals fa-lg"></i> Total: <?php echo $imtr_count + $omtr_count;?></li>
                    </ul>
                </div>
            </div>
            <div class="di-box activiy-logs">
                <div class="title">
                    <h2><i class="fa fa-history"></i> User Activity Logs</h2>
                    <div class="logs">
                        
                        <ul>
                            <?php foreach ($user_logs as $key) :?>
                            <li><?php echo $key->log;?></li>
                            <?php endforeach;?>
                            <!-- <li><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-sign-in-alt fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span> Fikri Haikal:  Telah Log In ~ 2018-10-15 10:05:41 AM ~</li>
                            <li><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-sync-alt fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span> Fikri Haikal: Mengecek Pembaharuan Aplikasi ~ 2018-11-18 10:26:31 AM ~ </li>
                            <li><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-recycle fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span> Erzard Ovelheim: Item Berhasil Di Kembalikan (Perihal Surat: Rapat Umum Tentang Pendidikan, No Surat: 01/XII/M13/18) ~ 2018-09-09 08:18:59 PM ~</li>
                            <li><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-sign-out-alt fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span> Fikri Haikal:  Telah Log Out ~ 2018-10-15 11:05:41 AM ~</li>
                            <li><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>  Erzard Ovelheim: Surat Masuk Berhasil Di Buang (Perihal Surat: Rapat Umum Tentang Pendidikan, No Surat: 01/XII/M13/18) ~ 2018-09-09 08:22:16 PM ~</li>
                            <li><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-times fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span> Erzard Ovelheim: Item Berhasil Di Hapus Secara Permanen (Perihal Surat: Rapat Umum Tentang Pendidikan, No Surat: 01/XII/M13/18) ~ 2018-09-09 08:22:25 PM ~</li>
                            <li><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-eye fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span> Erzard Ovelheim: Melihat Surat Masuk Baru (Perihal Surat: , No Surat: 01/XII/M13/50) ~ 2018-09-09 10:39:07 PM ~</li>
                            <li><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-share-square fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span> Erzard Ovelheim: Surat Berhasil Di Disposisikan (Perihal Surat: Rapat Umum Tentang Pendidikan, No Surat: 01/XII/M13/50) ~ 2018-09-09 10:39:14 PM ~</li> -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- <footer class="activity-page footer">
        <p class="logo-text">_SIMAK_</p>
        <p class="version-text">v1.0</p>
    </footer> -->
    <script src="<?php echo base_url('assets/lib/jquery-3.3.1.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.ui.widget.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.cookie.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.fileupload.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.iframe-transport.js');?>"></script>
    <script src="<?php echo base_url('assets/js/asmp-actionlib.js');?>"></script>
    <script src="<?php echo base_url('assets/js/user-page.js');?>"></script>
    <script src="<?php echo base_url('assets/js/photos-box.js');?>"></script>
    <script src="<?php echo base_url('assets/js/workers.js');?>"></script>
</body>
</html>