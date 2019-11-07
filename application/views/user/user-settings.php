<?php $this->theme->get_header('settings');?>


<!-- Contents Section -->
<section class="casual-theme activity-page main-contents">
    <!-- Page Title-->
    <div class="activity-page page-title">
        <h1>_<i class="fa fa-wrench"></i> <?php echo $this->theme->get_page_title('settings'); ?>_</h1>
    </div>
    <div class="casual-theme settings-container">
        <form action="javascript:void(0)" method="post" id="settingsForm">
            <div class="form-section page">
            <h2>~ Pengguna > Halaman ~</h2>
            <form action="javascript:void(0)" method="post" id="settingsForm">
                <label for="mulact_rem_all">Aksi Ganda Hapus & Buang Semua:</label>
                <select name="mulact_rem_all" id="mulRemAct">
                    <option <?php if ($settings[0]->multiple_remove_action == 'all') echo 'selected ' ; ?>value="all">Semua</option>
                    <option <?php if ($settings[0]->multiple_remove_action == 'selected') echo 'selected ' ; ?>value="selected">Yang Dipilih</option>
                </select>
                <label for="mulact_rec_all">Aksi Ganda Recovery Semua:</label>
                <select name="mulact_rec_all" id="mulRecAct">
                    <option <?php if ($settings[0]->multiple_recovery_action == 'all') echo 'selected ' ; ?>value="all">Semua</option>
                    <option <?php if ($settings[0]->multiple_recovery_action == 'selected') echo 'selected ' ; ?>value="selected">Yang Dipilih</option>
                </select>
                <label for="paging_item">Paging:</label>
                <span class="settings-checkbox"><input type="checkbox" name="paging_item" id="pagingItem" <?php if ($settings[0]->paging_status == 1) echo 'checked'; ?>><span class="settings-checkmark" <?php if ($settings[0]->paging_status == 1) echo 'style="display:block"'; ?>><i class="fa fa-check"></i></span></span>
                <label for="paging_limit">Batas Item Yang di Paging:</label>
                <input type="number" name="paging_limit" id="pagingLimit" value="<?php echo $settings[0]->row_limit;?>">
                <button type="submit" name="page_settings" class="button save-settings-btn"><i class="fa fa-save"></i> Simpan</button>
            </form>
        </div>
        <div class="form-section page">
            <h2>~ Pengguna > Keamanan ~</h2>
            <p id="flpCode">Force Protection Code: <?php echo ($flp_code_status == TRUE) ? 'sudah di set': '<button id="generateFLPC">Klik Tombol Ini untuk Mengeset FLP Code</button>';?></p>
            <p id="lrCode">Long Recovery Code: <?php echo ($lr_code_status == TRUE) ? 'sudah di set': '<button id="generateFLPC">Klik Tombol Ini untuk Mengeset Recovery Code</button>';?></p>
        </div>
        <button type="submit" class="button save-all-settings-btn"><i class="fa fa-save"></i> Simpan Semua Perubahan</button>
    </div>
    <div class="casual-theme action-msg-notification hide"></div>
</section>

<script src="<?php echo base_url('assets/lib/jquery-3.3.1.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.transit.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.ui.widget.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.cookie.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.fileupload.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.iframe-transport.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/xss.js');?>"></script>
    <script src="<?php echo base_url('assets/js/asmp-actionlib.js');?>"></script>
    <script src="<?php echo base_url('assets/js/user-page.js');?>"></script>
    <script src="<?php echo base_url('assets/js/photos-box.js');?>"></script>
    <script src="<?php echo base_url('assets/js/workers.js');?>"></script>
</body>
</html>