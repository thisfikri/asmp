<?php $this->theme->get_header('user-management'); ?>

<!-- Contents Section -->
<section class="casual-theme activity-page main-contents">
        <!-- Page Title-->
        <div class="activity-page page-title">
            <h1>_<i class="fa fa-users"></i> <?php echo $this->theme->get_page_title('user-management'); ?>_</h1>
        </div>
        <div class="casual-theme action-msg-notification hide"></div>
        <div class="prompt-box hide">
            <button class="close-btn"><i class="fa fa-times fa-fw"></i></button>
            <form action="javascript:void(0)" method="post">
                <input type="password" name="password" id="uPass" placeholder="Masukan Kata Sandi Anda.">
            </form>
        </div>
        <div class="dim-light hide-element"></div>
        <div class="casual-theme table-container" id="userManagement">
            <div class="table-header">
                <div class="multiple-action hide" data-mail-ids="">
                    <span>Aksi Ganda: </span>
                    <button class="button multiple-action-btn trash"><i class="fa fa-times fa-lg"></i> Buang</button>
                </div>
            </div>
            <table class="item-list" border="1">
                <tr>
                    <th class="table-title"><input type="checkbox" id="checkAll"><span class="checkmark all"><i class="fa fa-check"></i></span></th>
                    <th class="table-title"><i class="fa fa-sort"></i> Nama</th>
                    <th class="table-title"><i class="fa fa-sort"></i> Bidang/Bagian</th>
                    <th class="table-title">Aksi</th>
                </tr>
                <!-- <tr class="item id1">
                    <td><input type="checkbox" class="checkbox item1"><span class="checkmark item1"><i class="fa fa-check"></i></span></td>
                    <td>Zankpakuto</td>
                    <td>Ketua Divisi 11</td>
                    <td><button class="button action-btn remove" id="item1"><i class="fa fa-times"></i></button></td>
                </tr>
                <tr class="item id2">
                    <td><input type="checkbox" class="checkbox item2"><span class="checkmark item2"><i class="fa fa-check"></i></span></td>
                    <td>Zankpakuto</td>
                    <td>Ketua Divisi 11</td>
                    <td><button class="button action-btn remove" id="item2"><i class="fa fa-times"></i></button></td>
                </tr> -->
            </table>
            <div class="pagination">
                <!-- <a href="#" class="page-link current">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <a href="#" class="page-link arrow-next"><i class="fa fa-angle-right"></i></a> -->
            </div>
        </div>
    </section>
    <script src="<?php echo base_url('assets/lib/jquery-3.3.1.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.ui.widget.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.cookie.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.fileupload.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.iframe-transport.js');?>"></script>
    <script src="<?php echo base_url('assets/js/asmp-actionlib.js');?>"></script>
    <script src="<?php echo base_url('assets/js/admin-page.js');?>"></script>
    <script src="<?php echo base_url('assets/js/photos-box.js');?>"></script>
</body>
</html>