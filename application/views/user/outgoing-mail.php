<?php $this->theme->get_header('outgoing-mail');?>

<!-- Contents Section -->
<section class="casual-theme activity-page main-contents">
        <!-- Page Title-->
        <div class="activity-page page-title">
            <h1>_<i class="fa fa-paper-plane"></i> <?php echo $this->theme->get_page_title('outgoing-mail'); ?>_</h1>
        </div>
        <div class="casual-theme action-msg-notification hide"></div>
        <div class="casual-theme table-container" id="outgoingMail">
            <div class="table-header">
                <div class="multiple-action hide" data-mail-ids="">
                    <span>Aksi Ganda: </span>
                    <button class="button multiple-action-btn trash"><i class="fa fa-trash-alt fa-lg"></i> Buang</button>
                </div>
                <button class="button create-om" title="Buat Surat Keluar" id="addOm"><i class="fa fa-plus-circle fa-lg"></i> Buat</button>
                <button class="button trash-can-btn" title="Tong Sampah"><i class="fa fa-trash-alt fa-lg"></i> Tong Sampah</button>
            </div>
            
            <div class="casual-theme add-om-modal" id="1" data-pdflayouts="<?php echo $pdf_layouts;?>">
                <div class="modal2ndlayer">
                <div class="modal-title"><h3>Buat Surat Keluar</h3></div>
                <button class="close-btn"><i class="fa fa-times"></i></button>
                <form action="javascript:void(0)" method="post" class="mail-modal-form">
                <div class="form-input"></div>
                <textarea name="mail_contents" class="widgEditor" id="mailContentsEditor" style="display: none;"></textarea>
			<div class="clearfix"></div>
			<?php echo form_hidden('editor_data', 'no_data'); ?>
                </form>
                </div>
            </div>
            <div class="casual-theme edit-om-modal" id="1" data-pdflayouts="<?php echo $pdf_layouts;?>">
                <div class="modal2ndlayer">
                <div class="modal-title"><h3>Edit Surat Keluar</h3></div>
                <button class="close-btn"><i class="fa fa-times"></i></button>
                <form action="javascript:void(0)" method="post" class="mail-modal-form">
                <div class="form-input"></div>
                <textarea name="mail_contents" class="widgEditor" id="mailContentsEditorEditing" style="display: none;"></textarea>
			<div class="clearfix"></div>
			<?php echo form_hidden('editor_data', 'no_data'); ?>
                </form>
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
                <!-- <tr class="item id1">
                    <td><input type="checkbox" class="checkbox item1"><span class="checkmark item1"><i class="fa fa-check"></i></span></td>
                    <td>XII/M1/19</td>
                    <td>Rapat Divisi 11</td>
                    <td>Ketua Divisi 11 - Zankpakuto</td>
                    <td><p class="im-status"><i class="fa fa-paper-plane"></i> Terkirim</p></td>
                    <td>18/12/2018</td>
                    <td>
                        <button class="button action-btn view" id="item1"><i class="fa fa-eye"></i></button>
                        <button class="button action-btn send" id="item4"><i class="fa fa-edit"></i></button>
                        <button class="button action-btn trash" id="item1"><i class="fa fa-trash-alt"></i></button>
                    </td>
                </tr>
                <tr class="item id2">
                    <td><input type="checkbox" class="checkbox item2"><span class="checkmark item2"><i class="fa fa-check"></i></span></td>
                    <td>XII/M1/20</td>
                    <td>Rapat Divisi 11</td>
                    <td>Ketua Divisi 12 - Zankpakuto</td>
                    <td><p class="im-status"><i class="fa fa-times-circle"></i> Belum Terkirim</p></td>
                    <td>18/12/2018</td>
                    <td>
                        <button class="button action-btn view" id="item2"><i class="fa fa-eye"></i></button>
                        <button class="button action-btn send" id="item4"><i class="fa fa-edit"></i></button>
                        <button class="button action-btn trash" id="item2"><i class="fa fa-trash-alt"></i></button>
                    </td>
                </tr>
                <tr class="item id3">
                    <td><input type="checkbox" class="checkbox item3"><span class="checkmark item3"><i class="fa fa-check"></i></span></td>
                    <td>XII/M1/20</td>
                    <td>Rapat Divisi 11</td>
                    <td>Ketua Divisi 13 - Zankpakuto</td>
                    <td><p class="im-status"><i class="fa fa-save"></i> Belum Dikirim</p></td>
                    <td>18/12/2018</td>
                    <td>
                        <button class="button action-btn view" id="item3"><i class="fa fa-eye"></i></button>
                        <button class="button action-btn send" id="item4"><i class="fa fa-edit"></i></button>
                        <button class="button action-btn trash" id="item3"><i class="fa fa-trash-alt"></i></button>
                    </td>
                </tr>
                <tr class="item id4">
                    <td><input type="checkbox" class="checkbox item4"><span class="checkmark item4"><i class="fa fa-check"></i></span></td>
                    <td>XII/M1/20</td>
                    <td>Rapat Divisi 11</td>
                    <td>Ketua Divisi 14 - Zankpakuto</td>
                    <td><p class="im-status"><i class="fa fa-paper-plane"></i> Terkirim</p></td>
                    <td>18/12/2018</td>
                    <td>
                        <button class="button action-btn view" id="item4"><i class="fa fa-eye"></i></button>
                        <button class="button action-btn send" id="item4"><i class="fa fa-edit"></i></button>
                        <button class="button action-btn trash" id="item4"><i class="fa fa-trash-alt"></i></button>
                    </td>
                </tr> -->
            </table>
            <div class="pagination" id="pageNavContainer">
                <!-- <a href="#" class="page-link current">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <a href="#" class="page-link arrow-next"><i class="fa fa-angle-right"></i></a> -->
            </div>
            <div class="mail-container">
            </div>
        </div> <!-- Table Contaienr -->
        <div class="casual-theme mail-views hide">
            <div class="casual-theme mail-view">
                <div class="modal2ndlayer">
                    <button class="close-btn"><i class="fa fa-times"></i></button>
                    <div class="mail-top-number"></div>
                    <div class="mail-header">
                        <h1><?php echo $app_settings->mail_document_heading;?></h1>
                        <h5><?php echo $app_settings->mail_document_address;?></h2>
                        <h5><?php echo $app_settings->mail_document_contact;?></h3>
                    </div>
                    <div class="mail-information"></div>
                    <div class="mail-contents"></div>
                </div>
            </div>
        </div>
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
    <script src="<?php echo base_url('assets/js/workers.js');?>"></script>
</body>
</html>