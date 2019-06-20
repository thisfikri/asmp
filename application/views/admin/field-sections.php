<?php $this->theme->get_header('field-sections'); ?>

<!-- Contents Section -->
<section class="casual-theme activity-page main-contents">
        <!-- Page Title-->
        <div class="activity-page page-title">
            <h1>_<i class="fa fa-users"></i> <?php echo $this->theme->get_page_title('field-sections'); ?>_</h1>
        </div>
        <div class="casual-theme action-msg-notification hide"></div>
        <div class="prompt-box hide">
            <button class="close-btn"><i class="fa fa-times fa-fw"></i></button>
            <form action="javascript:void(0)" method="post">
                <input type="password" name="password" id="uPass" placeholder="Masukan Kata Sandi Anda.">
            </form>
        </div>
        <div class="dim-light hide-element"></div>
        <div class="casual-theme table-container" id="fieldSections">
            <div class="table-header">
                <div class="multiple-action hide" data-mail-ids="">
                    <span>Aksi Ganda: </span>
                    <button class="button multiple-action-btn trash"><i class="fa fa-times fa-lg"></i> Buang</button>
                </div>
                <button class="button add-item-btn" title="Tambah Bidang/Bagian"><i class="fa fa-plus-circle fa-lg"></i> Tambah</button>
            </div>
            <div class="add-field-section-box hide">
                <h3 class="title"><i class="fa fa-plus-circle fa-lg"></i> Tambah Bidang/Bagian <button class="close-btn"><i class="fa fa-times fa-fw"></i></button></h2>
                <form action="javascript:void(0)" class="add-field-section-form">
                    <label for="field_section_name">Nama Bidang/Bagian:</label>
                    <input type="text" name="field_section_name" id="fieldSectionName">
                    <label for="field_section_task">Tugas:</label>
                    <select type="text" name="field_section_task" id="fieldSectionTask">
                        <option value="normal_accept_sending">Menerima Surat Masuk Balasan, Mengirim Surat Keluar</option>
					    <option value="accept_lvl1_dpss">Menerima Surat Masuk lvl1, Mengirim Surat Disposisi</option>
					    <option value="accept_lvl2_dpss">Menerima Surat Masuk lvl2, Mengirim Surat Disposisi</option>
                    </select>
                    <button type="submit" name="add_field_section" class="button"><i class="fa fa-plus-circle fa-lg"></i> Tambah</button>
                </form>
            </div>
            <table class="item-list" border="1">
                <tr>
                    <th class="table-title"><input type="checkbox" id="checkAll"><span class="checkmark all"><i class="fa fa-check"></i></span></th>
                    <th class="table-title"><i class="fa fa-sort"></i> Nama Bidang/Bagian</th>
                    <th class="table-title"><i class="fa fa-sort"></i> Tugs</th>
                    <th class="table-title">Aksi</th>
                </tr>
                <!-- <tr class="item id1">
                    <td><input type="checkbox" class="checkbox item1"><span class="checkmark item1"><i class="fa fa-check"></i></span></td>
                    <td>Kepala Bagian</td>
                    <td>Menerima Surat, Accept/Decline Surat, Pimpinan</td>
                    <td><button class="button action-btn remove" id="item1"><i class="fa fa-times"></i></button></td>
                </tr>
                <tr class="item id2">
                    <td><input type="checkbox" class="checkbox item2"><span class="checkmark item2"><i class="fa fa-check"></i></span></td>
                    <td>Kepala Bagian</td>
                    <td>Menerima Surat, Accept/Decline Surat, Pimpinan</td>
                    <td><button class="button action-btn remove" id="item2"><i class="fa fa-times"></i></button></td>
                </tr>
                <tr class="item id3">
                    <td><input type="checkbox" class="checkbox item3"><span class="checkmark item3"><i class="fa fa-check"></i></span></td>
                    <td>Kepala Bagian</td>
                    <td>Menerima Surat, Accept/Decline Surat, Pimpinan</td>
                    <td><button class="button action-btn remove" id="item3"><i class="fa fa-times"></i></button></td>
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
    <script src="<?php echo base_url('assets/lib/jquery.transit.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.ui.widget.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.cookie.min.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.fileupload.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery.iframe-transport.js');?>"></script>
    <script src="<?php echo base_url('assets/lib/xss.js');?>"></script>
    <script src="<?php echo base_url('assets/js/asmp-actionlib.js');?>"></script>
    <script src="<?php echo base_url('assets/js/admin-page.js');?>"></script>
    <script src="<?php echo base_url('assets/js/photos-box.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url() . 'assets/widgEditor_1.0.1/scripts/widgeditor.js'; ?>"></script>
</body>
</html>