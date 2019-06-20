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
 *  Incoming_Mail_Handler Class
 *
 * Menghandle Incoming Mail
 *
 * @package ASMP
 * @category Model
 * @author ThisFikri
 */
class Incoming_Mail_Handler extends CI_Model
{
    /**
     * Throw Incoming Mail to trash
     *
     * @param array $mail_data incoming mail data to send
     * @param string $output output type
     * @return void
     */
    public function throw_im(array $mail_data, $output = 'echo')
    {
        if ($this->checker->is_admin())
        {
            $username = $this->session->userdata('admin_login');

            $query = $this->db->where('username', $username)->get('trash_can');
            $trash_count = $query->num_rows();

            $data_to_send = array(
                'id' => $trash_count + 1,
                'mail_type' => 'im',
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
                $this->db->where('mail_number', $mail_data['mail_number'])->delete('incoming_mail');
                if ($this->db->affected_rows())
                {
                    $query = $this->db->get('incoming_mail');
                    $row_count = $query->num_rows();
                    $i = 0;
                    $first_id = $mail_data['id'] + 1;
                    $target_id = $mail_data['id'];
                    $row_count = $row_count - ($mail_data['id'] - 1);
                    for (; $i < $row_count; $i++)
                    {
                        $this->db->where('id', $first_id)->update('incoming_mail', array('id' => $target_id));
                        ++$first_id;
                        ++$target_id;
                    }

                    // Tulis log
                    $this->activity_log->create_activity_log('move_to_trash', ' surat masuk berhasil dibuang', $data_to_send, $username);

                    if ($output == 'echo')
                    {
                        echo 'surat masuk berhasil dibuang';
                    }
                    else if ($output == 'json')
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'success',
                                'message' => 'surat masuk berhasil dibuang',
                            )
                        ));
                    }
                }
                else
                {
                    if ($output == 'echo')
                    {
                        echo 'surat masuk gagal dibuang';
                    }
                    else if ($output == 'json')
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'failed',
                                'message' => 'surat masuk gagal dibuang',
                            )
                        ));
                    }
                }
            }
            else
            {
                if ($output == 'echo')
                {
                    echo 'surat masuk gagal dibuang';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array('status' => 'failed',
                            'message' => 'surat masuk gagal dibuang',
                        )
                    ));
                }
            }
        }
        else
        {
            if ($output == 'echo')
            {
                echo 'this function only for `admin` authority';
            }
            else if ($output == 'json')
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array('status' => 'error',
                        'message' => 'this function only for `admin` authority',
                    )
                ));

            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * disposition
     *
     * @return void
     */
    public function disposition($mail_data, $output = 'echo')
    {
        if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');

            $query = $this->db->get('incoming_mail');
            $im_count = $query->num_rows();

            $query = $this->db->where('field_section_name', $mail_data['receiver'])->get('field_sections');
            $filed_section_data = $query->result();
            $pos_task = $filed_section_data[0]->task;
            $receiver_data = null;
            $im_id = $mail_data['id'];

            //var_dump($filed_section_data);
            if ($pos_task == 'accept_lvl1_dpss' || $pos_task == 'accept_lvl2_dpss')
            {
                if ($pos_task == 'accept_lvl1_dpss')
                {
                    $receiver_task = 'accept_lvl2_dpss';
                }
                else if ($pos_task == 'accept_lvl2_dpss')
                {
                    $receiver_task = 'leader_accept_lvl3_reply';
                }

                $query = $this->db->where('task', $receiver_task)->get('field_sections');
                $result = $query->result();
                $receiver_pos = $result[0]->field_section_name;

                $query = $this->db->where('position', $receiver_pos)->get('users');
                $receiver_data = $query->result();
                $receiver_data = $receiver_data[0];

                $mail_data['id'] = $im_count + 1;
                $mail_data['username'] = $receiver_data->username;
                $mail_data['receiver'] = $receiver_pos;
                $mail_data['status'] = 'baru-dpss';

                unset($mail_data['last_modified']);
                unset($mail_data['user_setting']);

                $query = $this->db->where(array(
                    'username' => $mail_data['username'],
                    'mail_number' => $mail_data['mail_number'],
                ))->get('incoming_mail');

                if (!$query->result())
                {
                    $this->db->insert('incoming_mail', $mail_data);
                    if ($this->db->affected_rows())
                    {
                        $mail_data['id'] = $im_id;
                        $mail_data['status'] = 'lawas-dpssd';

                        $this->db->where(array(
                            'username' => $username,
                            'mail_number' => $mail_data['mail_number'],
                        ))->update('incoming_mail', array('status' => 'lawas-dpssd'));

                        // Tulis log
                        $this->activity_log->create_activity_log('disposition', ' Surat Masuk Berhasil Di disposisikan', $mail_data, $username);
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array(
                                'status' => 'success',
                                'message' => 'Surat Masuk Berhasil Di Disposisikan',
                                'data' => $mail_data,
                            )
                        ));
                    }
                    else
                    {
                        $mail_data['id'] = $im_id;
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Surat Masuk Gagal Di Disposisikan',
                                'data' => $mail_data,
                            )
                        ));
                    }
                }
                else
                {
                    $mail_data['id'] = $im_id;
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'warning',
                            'message' => 'Surat Masuk Sudah Di Disposisikan',
                            'data' => $mail_data,
                        )
                    ));
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
        else
        {
            $this->output->set_content_type('application/json')->set_output(json_encode(
                array('status' => 'error',
                    'message' => 'this function only for `user` authority and task normal',
                )
            ));
        }
    }

    // ------------------------------------------------------------------------

    /**
     * reply - Digunakan untuk mendisposisikan surat masuk
     *
     */
    public function reply($mail_data, $output = 'echo')
    {
        if ($this->checker->is_admin())
        {
            $fdata = $mail_data[0];
            $mail_data = $mail_data[1];

            $username = $this->session->userdata('admin_login');

            $query = $this->db->get('incoming_mail');
            $im_count = $query->num_rows();

            $query = $this->db->where('field_section_name', $mail_data['receiver'])->get('field_sections');
            $filed_section_data = $query->result();
            $pos_task = $filed_section_data[0]->task;
            $receiver_data = null;
            $im_id = $mail_data['id'];

            if ($pos_task == 'leader_accept_lvl3_reply')
            {
                $query = $this->db->where('position', $mail_data['sender'])->get('users');
                $receiver_data = $query->result();
                $receiver_data = $receiver_data[0];

                $mail_data['id'] = $im_count + 1;
                $mail_data['username'] = $receiver_data->username;
                $mail_data['receiver'] = $receiver_data->position;
                $mail_data['sender'] = $filed_section_data[0]->field_section_name;
                $mail_data['contents'] = $fdata['mail_contents'];
                $mail_data['status'] = 'baru-' . $fdata['reply_response'];

                unset($mail_data['last_modified']);
                unset($mail_data['user_setting']);

                $this->db->insert('incoming_mail', $mail_data);
                if ($this->db->affected_rows())
                {
                    $mail_data['id'] = $im_id;
                    $mail_data['status'] = 'lawas-replied';

                    $this->db->where(array(
                        'username' => $username,
                        'mail_number' => $mail_data['mail_number'],
                    ))->update('incoming_mail', array('status' => 'lawas-replied'));

                    // Tulis log
                    $this->activity_log->create_activity_log('reply', ' Surat Masuk Berhasil Di balas', $mail_data, $username);
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Surat Masuk Berhasil Di Balas',
                            'data' => $mail_data,
                        )
                    ));
                }
                else
                {
                    $mail_data['id'] = $im_id;
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Surat Masuk Gagal Di Balas',
                            'data' => $mail_data,
                        )
                    ));
                }
            }
            else
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array('status' => 'error',
                        'message' => 'this function only for `admin`',
                    )
                ));
            }
        }
        else
        {
            if ($output == 'echo')
            {
                echo 'this function only for `admin`';
            }
            else if ($output == 'json')
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array('status' => 'error',
                        'message' => 'this function only for `admin`',
                    )
                ));

            }
        }
    }
}