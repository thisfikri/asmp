<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PDF_CD_Manipulator extends CI_Model
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    private $_corverted_data = array();

    /**
     * Undocumented variable
     * 
     * @var array
     */
    private const LAYOUT_ITEMS_DEFAULT_DATA = array(
        'idAndMailType' => array(
            'xpos' => 5,
            'ypos' => 5,
            'font_family' => 'times',
            'font_size' => 12,
            'font_style' => 'b'
        ),
        'docTitle' => array(
            'xpos' => 77,
            'ypos' => 48,
            'font_family' => 'times',
            'font_size' => 14,
            'font_style' => 'b'
        ),
        'docAddr' => array(
            'xpos' => 74,
            'ypos' => 56,
            'font_family' => 'times',
            'font_size' => 12,
            'font_style' => 'b'
        ),
        'docContact' => array(
            'xpos' => 84,
            'ypos' => 63,
            'font_family' => 'times',
            'font_size' => 12,
            'font_style' => 'b'
        ),
        'line' => array(
            'xpos' => 18,
            'ypos' => 73,
            'x2pos' => 191,
            'y2pos' => 73
        ),
        'docMailNum' => array(
            'xpos' => 18,
            'ypos' => 84,
            'font_family' => 'times',
            'font_size' => 12,
            'font_style' => ''
        ),
        'docDate' => array(
            'xpos' => 170,
            'ypos' => 84,
            'font_family' => 'times',
            'font_size' => 12,
            'font_style' => ''
        ),
        'docFor' => array(
            'xpos' => 18,
            'ypos' => 104,
            'font_family' => 'times',
            'font_size' => 12,
            'font_style' => ''
        ),
        'docSubject' => array(
            'xpos' => 48,
            'ypos' => 140,
            'font_family' => 'times',
            'font_size' => 12,
            'font_style' => ''
        ),
        'docContents' => array(
            'xpos' => 17,
            'ypos' => 155,
            'w' => 0,
            'h' => 10,
            'font_family' => 'times',
            'font_size' => 12
        ),
        'docSignature' => array(
            'fxpos' => 168,
            'fypos' => 195,
            'sxpos' => 178,
            'sypos' => 215,
            'thxpos' => 170,
            'thypos' => 223,
            'font_family' => 'times',
            'font_size' => 12,
            'ftxt_font_style' => '',
            'stxt_font_style' => '',
            'thtxt_font_style' => ''
        ),
    );

    /**
     * Undocumented function
     *
     * @param array $data
     * @param boolean $encode_return
     * @return void
     */
    public function create_default_data(array $data, $encode_return = TRUE)
    {
        $return_value = array();
        for ($i = 0; $i < count($data); $i++)
        {
            if (array_key_exists($data[$i], self::LAYOUT_ITEMS_DEFAULT_DATA))
            {
                $return_value[$data[$i]] = self::LAYOUT_ITEMS_DEFAULT_DATA[$data[$i]];
            }
        }

        if ($encode_return === TRUE)
        {
            return json_encode($return_value);
        }
        else
        {
            return $return_value;
        }
    }

    /**
     * Undocumented function
     *
     * @param array $configs
     * @return void
     */
    public function create_page_setup(array $configs = NULL)
    {
        if ($configs === NULL)
        {
            $configs = array(
                'orientation' => 'P',
                'unit' => 'mm',
                'format' => 'A4'
            );

            return json_encode($configs);
        } else {
            return json_encode($configs);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param string $data
     * @return void
     */
    public function convert_data(string $data)
    {
        $this->_corverted_data =  json_decode($data, TRUE);
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @return void
     */
    public function get_data()
    {
        return $this->_corverted_data;
    }
}