<?php $this->theme->get_header('trash-can');?>

<!-- Contents Section -->
<section class="casual-theme activity-page main-contents">
        <!-- Page Title-->
        <div class="activity-page page-title">
            <h1>_<i class="fa fa-paper-plane"></i> <?php echo $this->theme->get_page_title('trash-can'); ?>_</h1>
        </div>
        <div class="casual-theme action-msg-notification hide"></div>
        <div class="prompt-box hide">
            <button class="close-btn"><i class="fa fa-times fa-fw"></i></button>
            <form action="javascript:void(0)" method="post">
                <input type="password" name="password" id="uPass" placeholder="Masukan Kata Sandi Anda.">
            </form>
        </div>
        <div class="casual-theme table-container" id="trashCan">
            <div class="table-header">
                <div class="multiple-action hide" data-mail-ids="">
                    <span>Aksi Ganda: </span>
                    <button class="button multiple-action-btn remove"><i class="fa fa-times fa-lg"></i> Hapus</button>
                    <button class="button multiple-action-btn recovery"><i class="fa fa-recycle fa-lg"></i> Pulihkan</button>
                </div>
            </div>
            
            <table class="item-list" border="1" id="mailAction">
                <tr>
                    <th class="table-title"><input type="checkbox" id="checkAll"><span class="checkmark all"><i class="fa fa-check"></i></span></th>
                    <th class="table-title"><i class="fa fa-sort-numeric-down"></i> No Surat</th>
                    <th class="table-title"><i class="fa fa-sort"></i> Perihal</th>
                    <th class="table-title"><i class="fa fa-sort"></i> Pengirim</th>
                    <th class="table-title"><i class="fa fa-clock"></i> Status</th>
                    <th class="table-title"><i class="fa fa-calendar-alt"></i> Tanggal</th>
                    <th class="table-title">Aksi</th>
                </tr>
            </table>
            <div class="pagination" id="pageNavContainer">
            </div>
        </div> <!-- Table Contaienr -->
            </div>
        </div>
    </section>
    <script src="<?php echo base_url('assets/lib/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.ui.widget.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.cookie.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.fileupload.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.iframe-transport.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/asmp-actionlib.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/user-page.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/photos-box.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/write-editor.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo site_url() . 'assets/widgEditor_1.0.1/scripts/widgeditor.js'; ?>"></script>
    <script src="<?php echo base_url('assets/js/mail-function.js'); ?>"></script>
</body>
</html>