<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

            $query = $this->db->where('position', $mail_data[3])->get('users');
            $receiver_data = $query->result();

            $query = $this->db->where('username', $username)->get('incoming_mail');
            $im_count = $query->num_rows();

            $query = $this->db->where('username', $username)->get('outgoing_mail');
            $om_count = $query->num_rows();

            $data_to_send = array(
                'id' => $om_count + 1,
                'mail_number' => $mail_data[0],
                'username' => $username,
                'subject' => $mail_data[1],
                'sender' => $mail_data[2],
                'receiver' => $mail_data[3],
                'contents' => $mail_data[4],
                'status' => 'baru',
                'date' => date('Y-m-d h:i:s A'),
                'pdf_layout' => $mail_data[5]
            );

            $this->db->insert('outgoing_mail', $data_to_send);
            if ($this->db->affected_rows())
            {
                $data_to_send['id'] = $im_count + 1;
                $data_to_send['username'] = $receiver_data[0]->username;
                $this->db->insert('incoming_mail', $data_to_send); 
                if ($this->db->affected_rocws())
                {
                    if ($output == 'echo')
                    {
                        echo 'surat keluar berhasil dikirim';
                    }
                    else if ($output == 'json')
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'success',
                            'message' => 'surat keluar berhasil dikirim'
                            )
                        ));
                    }
                }
            }
            else
            {
                if ($output == 'echo')
                {
                    echo 'surat keluar gagal disimpan dan dikirim';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array('status' => 'failed',
                        'message' => 'surat keluar gagal disimpan dan dikirim'
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
                    'message' => 'this function only for `user` authority'
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

            $query = $this->db->where('username', $username)->get('outgoing_mail');
            $om_count = $query->num_rows();

            $data_to_send = array(
                'id' => $om_count + 1,
                'mail_number' => $mail_data[0],
                'username' => $username,
                'subject' => $mail_data[1],
                'sender' => $mail_data[2],
                'receiver' => $mail_data[3],
                'contents' => $mail_data[4],
                'status' => 'baru',
                'date' => date('Y-m-d h:i:s A'),
                'pdf_layout' => $mail_data[5]
            );
            $this->db->insert('outgoing_mail', $data_to_send);
            if ($this->db->affected_rows())
            {
                if ($output == 'echo')
                {
                    echo 'surat keluar berhasil disimpan';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array('status' => 'success',
                        'message' => 'surat keluar berhasil disimpan'
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
                        array('status' => 'failed',
                        'message' => 'surat keluar gagal disimpan'
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
                    'message' => 'this function only for `user` authority'
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
                'mail_number' => $mail_data[0],
                'username' => $username,
                'subject' => $mail_data[1],
                'sender' => $mail_data[2],
                'receiver' => $mail_data[3],
                'contents' => $mail_data[4],
                'status' => 'baru',
                'date' => date('Y-m-d h:i:s A'),
                'pdf_layout' => $mail_data[5]
            );
            $this->db->insert('trash_can', $data_to_send);
            if ($this->db->affected_rows())
            {
                if ($output == 'echo')
                {
                    echo 'surat keluar berhasil dibuang';
                }
                else if ($output == 'json')
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array('status' => 'success',
                        'message' => 'surat keluar berhasil dibuang'
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
                        'message' => 'surat keluar gagal dibuang'
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
                    'message' => 'this function only for `user` authority'
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
						'om_data' => $result
	                    )
	                ));
                }
            }
        }
    }
}


/* End of file filename.php */
