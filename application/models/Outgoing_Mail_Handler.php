<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ASMP - Aplikasi Sistem Menejemen Perkantoran
 *
 * @package ASMP
 * @author ThisFikri (Leader)
 * @copyright Copyright (c) 2018, Recodech <ocraineore@gmail.com>
 * @link https://github.com/codecoretech
 * @version BETA BUILD 02
 * @since Aplha 1.0.0
 * @license GNU GPL v3.0
 *
 * Aplikasi ini dibuat dan dikembangkan untuk dipergunakan dalam hal administrasi perkantoran
 */

/**
 * Outgoing_Mail_Handler Class
 *
 * Menghandle Outgoing Mail
 *
 * @package ASMP
 * @category Model
 * @author ThisFikri
 */
class Outgoing_Mail_Handler extends CI_Model
{
    /**
     * Send Outgoing Mail
     *
     * @param array $mail_data outgoing mail data to send
     * @param string $output output type
     * @return void
     */
    public function send_om(array $mail_data, $output = 'echo')
    {
        if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');

            $query = $this->db->where('username', $username)->get('users');
            $sender_data = $query->result();
            $sender_pos = $sender_data[0]->position;

            $query = $this->db->where('field_section_name', $sender_pos)->get('field_sections');
            $filed_section_data = $query->result();
            $pos_task = $filed_section_data[0]->task;
            $receiver_data = null;
            if ($pos_task == 'normal_accept_sending')
            {
                $query = $this->db->where('task', 'accept_lvl1_dpss')->get('field_sections');
                $result = $query->result();
                $receiver_pos = $result[0]->field_section_name;

                $query = $this->db->where('position', $receiver_pos)->get('users');
                $receiver_data = $query->result();
                $receiver_data = $receiver_data[0];
            }
            else
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array('status' => 'error',
                        'message' => 'this function only for `user` authority and task normal',
                    )
                ));
                exit(0);
            }

            $query = $this->db->where('username', $username)->get('outgoing_mail');
            $om_count = $query->num_rows();

            $query = $this->db->get('incoming_mail');
            $im_count = $query->num_rows();

            $data_to_send = array(
                'id' => $om_count + 1,
                'mail_number' => $mail_data['mail_number'],
                'username' => $username,
                'subject' => $mail_data['mail_subject'],
                'sender' => $sender_pos,
                'receiver' => $receiver_pos,
                'contents' => $mail_data['editor_data'],
                'status' => 'terkirim',
                'date' => date('Y-m-d'),
                'pdf_layout' => $mail_data['pdf_layouts'],
            );
            $this->db->insert('outgoing_mail', $data_to_send);
            if ($this->db->affected_rows())
            {
                $data_to_send['id'] = $im_count + 1;
                $data_to_send['username'] = $receiver_data->username;
                $data_to_send['status'] = 'baru';
                $this->db->insert('incoming_mail', $data_to_send);
                if ($this->db->affected_rows())
                {
                    $data_to_send['id'] = $om_count + 1;
                    $data_to_send['username'] = $username;
                    $data_to_send['status'] = 'terkirim';
                    $data_to_send['user_setting'] = $this->app_settings->get_user_settings($username)[0];

                    // Tulis log
                    $this->activity_log->create_activity_log('send', ' Surat Keluar Berhasil Dikirim', $data_to_send, $username);

                    if ($output == 'echo')
                    {
                        echo 'surat keluar berhasil dikirim';
                    }
                    else if ($output == 'json')
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'success',
                                'message' => 'surat keluar berhasil dikirim',
                                'data' => $data_to_send,
                            )
                        ));
                    }
                }
                else
                {
                    $data_to_send['status'] = 'disimpan';
                    if ($output == 'echo')
                    {
                        echo 'surat keluar gagal dikirim';
                    }
                    else if ($output == 'json')
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'success',
                                'message' => 'surat keluar gagal dikirim',
                                'data' => $data_to_send,
                            )
                        ));
                    }
                }
            }
            else
            {
                $data_to_send['status'] = 'disimpan';
                $data_to_send['user_setting'] = $this->app_settings->get_user_settings($username)[0];
                if ($output == 'echo')
                {
                    echo 'surat keluar gagal disimpan dan dikirim';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'failed',
                            'message' => 'surat keluar gagal disimpan dan dikirim',
                            'data' => $data_to_send,
                        )
                    ));
                }
            }
        }
        else
        {
            if ($output == 'echo')
            {
                echo 'this function only for `user` authority';
            }
            else if ($output == 'json')
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array('status' => 'error',
                        'message' => 'this function only for `user` authority',
                    )
                ));

            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Save Outgoing Mail
     *
     * @param array $mail_data outgoing mail data to send
     * @param string $output output type
     * @return void
     */
    public function save_om(array $mail_data, $output = 'echo')
    {
        if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');

            $query = $this->db->where('username', $username)->get('users');
            $sender_data = $query->result();
            $sender_pos = $sender_data[0]->position;

            $query = $this->db->where('field_section_name', $sender_pos)->get('field_sections');
            $filed_section_data = $query->result();
            $pos_task = $filed_section_data[0]->task;
            $receiver_data = null;
            if ($pos_task == 'normal_accept_sending')
            {

                $query = $this->db->where('task', 'accept_lvl1_dpss')->get('field_sections');
                $result = $query->result();
                if ($result)
                {
                    $receiver_pos = $result[0]->field_section_name;

                    $query = $this->db->where('position', $receiver_pos)->get('users');
                    $receiver_data = $query->result();
                    $receiver_data = $receiver_data[0];

                    $query = $this->db->where('username', $username)->get('outgoing_mail');
            $om_count = $query->num_rows();

            $data_to_send = array(
                'id' => $om_count + 1,
                'mail_number' => $mail_data['mail_number'],
                'username' => $username,
                'subject' => $mail_data['mail_subject'],
                'sender' => $sender_pos,
                'receiver' => $receiver_pos,
                'contents' => $mail_data['editor_data'],
                'status' => 'disimpan',
                'date' => date('Y-m-d'),
                'pdf_layout' => $mail_data['pdf_layouts'],
            );
            $this->db->insert('outgoing_mail', $data_to_send);
            if ($this->db->affected_rows())
            {
                // Tulis log
                $this->activity_log->create_activity_log('add_om', ' Surat Keluar berhasil ditambahkan', $data_to_send, $username);

                $data_to_send['user_setting'] = $this->app_settings->get_user_settings($username)[0];
                if ($output == 'echo')
                {
                    echo 'surat keluar berhasil disimpan';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'surat keluar berhasil disimpan',
                            'data' => $data_to_send,
                        )
                    ));
                }
            }
            else
            {
                if ($output == 'echo')
                {
                    echo 'surat keluar gagal disimpan';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'failed',
                            'message' => 'surat keluar gagal disimpan',
                        )
                    ));
                }
            }
                }
                else
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'penerima tidak terdaftar'
                        )
                    ));
                }
            }
            else
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'this function only for `user` authority and task normal',
                    )
                ));
            }
        }
        else
        {
            if ($output == 'echo')
            {
                echo 'this function only for `user` authority';
            }
            else if ($output == 'json')
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'this function only for `user` authority',
                    )
                ));

            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Throw Outgoing Mail to trash
     *
     * @param array $mail_data outgoing mail data to send
     * @param string $output output type
     * @return void
     */
    public function throw_om(array $mail_data, $output = 'echo')
    {
        if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');

            $query = $this->db->where('username', $username)->get('trash_can');
            $trash_count = $query->num_rows();

            $data_to_send = array(
                'id' => $trash_count + 1,
                'mail_type' => 'om',
                'mail_number' => $mail_data['mail_number'],
                'username' => $username,
                'subject' => $mail_data['subject'],
                'sender' => $mail_data['sender'],
                'receiver' => $mail_data['receiver'],
                'contents' => $mail_data['contents'],
                'status' => $mail_data['status'],
                'date' => date('Y-m-d'),
                'pdf_layout' => $mail_data['pdf_layout'],
            );
            $this->db->insert('trash_can', $data_to_send);
            if ($this->db->affected_rows())
            {
                $this->db->where('mail_number', $mail_data['mail_number'])->delete('outgoing_mail');
                if ($this->db->affected_rows())
                {
                    $query = $this->db->get('outgoing_mail');
                    $row_count = $query->num_rows();
                    $i = 0;
                    $first_id = $mail_data['id'] + 1;
                    $target_id = $mail_data['id'];
                    $row_count = $row_count - ($mail_data['id'] - 1);
                    for (; $i < $row_count; $i++)
                    {
                        $this->db->where('id', $first_id)->update('outgoing_mail', array('id' => $target_id));
                        ++$first_id;
                        ++$target_id;
                    }

                    // Tulis log
                    $this->activity_log->create_activity_log('move_to_trash', ' surat keluar berhasil dibuang', $data_to_send, $username);

                    if ($output == 'echo')
                    {
                        echo 'surat keluar berhasil dibuang';
                    }
                    else if ($output == 'json')
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'success',
                                'message' => 'surat keluar berhasil dibuang',
                            )
                        ));
                    }
                }
                else
                {
                    if ($output == 'echo')
                    {
                        echo 'surat keluar gagal dibuang';
                    }
                    else if ($output == 'json')
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'failed',
                                'message' => 'surat keluar gagal dibuang',
                            )
                        ));
                    }
                }
            }
            else
            {
                if ($output == 'echo')
                {
                    echo 'surat keluar gagal dibuang';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array('status' => 'failed',
                            'message' => 'surat keluar gagal dibuang',
                        )
                    ));
                }
            }
        }
        else
        {
            if ($output == 'echo')
            {
                echo 'this function only for `user` authority';
            }
            else if ($output == 'json')
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array('status' => 'error',
                        'message' => 'this function only for `user` authority',
                    )
                ));

            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Load Outgoing Mail Data
     *
     * @param string $output output type
     * @return void
     */
    public function load_om($output = 'echo')
    {
        if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');
            $query = $this->db->where('username', $username)->get('outgoing_mail');
            $result = $query->result_array();
            if ($result)
            {
                if ($output == 'echo')
                {
                    print_r($result);
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'load complete',
                            'om_data' => $result,
                        )
                    ));
                }
            }
        }
    }

    public function update_om(array $mail_data, $output = 'echo')
    {
        if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');
            $prev_mailnum = $mail_data['prev_mailnum'];
            $data_to_update = array(
                'pdf_layout' => $mail_data['pdf_layouts'],
                'mail_number' => $mail_data['mail_number'],
                'subject' => $mail_data['mail_subject'],
                'contents' => $mail_data['editor_data'],
                'date' => date('Y-m-d'),
                'last_modified' => date('Y-m-d h:i:s A'),
            );

            $this->db->where(array(
                'username' => $username,
                'mail_number' => $prev_mailnum,
            ))->update('outgoing_mail', $data_to_update);

            if ($this->db->affected_rows())
            {
                $query = $this->db->where(array(
                    'username' => $username,
                    'mail_number' => $data_to_update['mail_number'],
                ))->get('outgoing_mail');
                $result_data = $query->result();

                // Tulis log
                $this->activity_log->create_activity_log('edit', ' Surat Keluar Berhasil Diubah', $data_to_update, $username);

                if ($output == 'echo')
                {
                    echo 'Surat Keluar Berhasil Diubah';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Surat Keluar Berhasil Diubah',
                            'data' => $result_data[0],
                        )
                    ));
                }
            }
            else
            {
                if ($output == 'echo')
                {
                    echo 'Surat Keluar Gagal Diubah';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Surat Keluar Gagal Diubah',
                        )
                    ));
                }
            }
        }
        else
        {
            if ($output == 'echo')
            {
                echo 'this function only for `user` authority';
            }
            else if ($output == 'json')
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array('status' => 'error',
                        'message' => 'this function only for `user` authority',
                    )
                ));

            }
        }
    }

    public function resend_om(array $mail_data, $output = 'echo')
    {
        $username = $this->session->userdata('user_login');

        $query = $this->db->get('incoming_mail');
        $im_count = $query->num_rows();

        $query = $this->db->where('field_section_name', $mail_data['sender'])->get('field_sections');
        $filed_section_data = $query->result();
        $pos_task = $filed_section_data[0]->task;
        $receiver_data = null;
        $om_id = $mail_data['id'];

        //var_dump($filed_section_data);
        if ($pos_task == 'normal_accept_sending')
        {
            $query = $this->db->where('task', 'accept_lvl1_dpss')->get('field_sections');
            $result = $query->result();
            $receiver_pos = $result[0]->field_section_name;

            $query = $this->db->where('position', $receiver_pos)->get('users');
            $receiver_data = $query->result();
            $receiver_data = $receiver_data[0];

            $mail_data['id'] = $im_count + 1;
            $mail_data['username'] = $receiver_data->username;
            $mail_data['status'] = 'baru';

            unset($mail_data['mail_send']);
            unset($mail_data['last_modified']);
            unset($mail_data['user_setting']);

            $this->db->insert('incoming_mail', $mail_data);
            if ($this->db->affected_rows())
            {
                $mail_data['id'] = $om_id;
                $mail_data['status'] = 'terkirim';

                $this->db->where(array(
                    'username' => $username,
                    'mail_number' => $mail_data['mail_number'],
                ))->update('outgoing_mail', array('status' => 'terikirim'));

                // Tulis log
                $this->activity_log->create_activity_log('send', ' Surat Keluar Berhasil Dikirim', $mail_data, $username);

                if ($output == 'echo')
                {
                    echo 'Surat Keluar Berhasil Dikirim';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Surat Keluar Berhasil Dikirim',
                            'data' => $mail_data,
                        )
                    ));
                }
            }
            else
            {
                $mail_data['id'] = $om_id;
                $mail_data['status'] = 'disimpan';
                if ($output == 'echo')
                {
                    echo 'Surat Keluar Gagal Dikirim';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Surat Keluar Gagal Dikirim',
                            'data' => $mail_data,
                        )
                    ));
                }
            }
        }
        else
        {
            $this->output->set_content_type('application/json')->set_output(json_encode(
                array('status' => 'error',
                    'message' => 'this function only for `user` authority and task normal',
                )
            ));
        }
    }
}

/* End of file filename.php */
