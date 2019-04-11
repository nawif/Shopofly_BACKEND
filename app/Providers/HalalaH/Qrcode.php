<?php
class Qrcode
{
    const VERSION = '000201';
    const POI = '010212';
    const HALALAH_ID = '33';
    const HALALAH_GUI = 'sa.halalah';
    const COUNTRY = 'SA';
    const LANG = 'AR';
    const CURRENCY = '682';
    private $merchant_id = '';
    private $branch_id = '';
    private $terminal_id = '';
    private $merchant_category_code = '';
    private $merchant_name = '';
    private $merchant_name_ar = '';
    private $merchant_city_ar = '';
    private $merchant_city = '';
    private $postal_code = '';
    private $amount = '';
    private $bill = '';
    private $mobile = '';
    private $store = '';
    private $loyalty = '';
    private $reference = '';
    private $consumer = '';
    private $terminal = '';
    private $purpose = '';
    private $request = '';
    private $tip = '';
    public function __construct($inputs){
        foreach ($inputs as $key=>$val)
        {
            $this->set_vars($key, strip_tags(trim($val)));
        }
    }
    private function set_vars($key, $val){
        if(isset($this->$key))
        {
            $this->$key = $val;
        }
    }
    private function get_length($str){
        $length = mb_strlen($str);
        if($length <=9){
            return '0'.$length;
        }else{
            return ''.$length;
        }
    }
    private function id_len_val($id, $val){
        return $id.$this->get_length($val).$val;
    }
    private function add_error($err) {
        throw new InvalidArgumentException($err);
    }
    private function generate_crc($string)
    {
        static $table = [
            0x0000, 0x1021, 0x2042, 0x3063, 0x4084, 0x50A5, 0x60C6, 0x70E7,
            0x8108, 0x9129, 0xA14A, 0xB16B, 0xC18C, 0xD1AD, 0xE1CE, 0xF1EF,
            0x1231, 0x0210, 0x3273, 0x2252, 0x52B5, 0x4294, 0x72F7, 0x62D6,
            0x9339, 0x8318, 0xB37B, 0xA35A, 0xD3BD, 0xC39C, 0xF3FF, 0xE3DE,
            0x2462, 0x3443, 0x0420, 0x1401, 0x64E6, 0x74C7, 0x44A4, 0x5485,
            0xA56A, 0xB54B, 0x8528, 0x9509, 0xE5EE, 0xF5CF, 0xC5AC, 0xD58D,
            0x3653, 0x2672, 0x1611, 0x0630, 0x76D7, 0x66F6, 0x5695, 0x46B4,
            0xB75B, 0xA77A, 0x9719, 0x8738, 0xF7DF, 0xE7FE, 0xD79D, 0xC7BC,
            0x48C4, 0x58E5, 0x6886, 0x78A7, 0x0840, 0x1861, 0x2802, 0x3823,
            0xC9CC, 0xD9ED, 0xE98E, 0xF9AF, 0x8948, 0x9969, 0xA90A, 0xB92B,
            0x5AF5, 0x4AD4, 0x7AB7, 0x6A96, 0x1A71, 0x0A50, 0x3A33, 0x2A12,
            0xDBFD, 0xCBDC, 0xFBBF, 0xEB9E, 0x9B79, 0x8B58, 0xBB3B, 0xAB1A,
            0x6CA6, 0x7C87, 0x4CE4, 0x5CC5, 0x2C22, 0x3C03, 0x0C60, 0x1C41,
            0xEDAE, 0xFD8F, 0xCDEC, 0xDDCD, 0xAD2A, 0xBD0B, 0x8D68, 0x9D49,
            0x7E97, 0x6EB6, 0x5ED5, 0x4EF4, 0x3E13, 0x2E32, 0x1E51, 0x0E70,
            0xFF9F, 0xEFBE, 0xDFDD, 0xCFFC, 0xBF1B, 0xAF3A, 0x9F59, 0x8F78,
            0x9188, 0x81A9, 0xB1CA, 0xA1EB, 0xD10C, 0xC12D, 0xF14E, 0xE16F,
            0x1080, 0x00A1, 0x30C2, 0x20E3, 0x5004, 0x4025, 0x7046, 0x6067,
            0x83B9, 0x9398, 0xA3FB, 0xB3DA, 0xC33D, 0xD31C, 0xE37F, 0xF35E,
            0x02B1, 0x1290, 0x22F3, 0x32D2, 0x4235, 0x5214, 0x6277, 0x7256,
            0xB5EA, 0xA5CB, 0x95A8, 0x8589, 0xF56E, 0xE54F, 0xD52C, 0xC50D,
            0x34E2, 0x24C3, 0x14A0, 0x0481, 0x7466, 0x6447, 0x5424, 0x4405,
            0xA7DB, 0xB7FA, 0x8799, 0x97B8, 0xE75F, 0xF77E, 0xC71D, 0xD73C,
            0x26D3, 0x36F2, 0x0691, 0x16B0, 0x6657, 0x7676, 0x4615, 0x5634,
            0xD94C, 0xC96D, 0xF90E, 0xE92F, 0x99C8, 0x89E9, 0xB98A, 0xA9AB,
            0x5844, 0x4865, 0x7806, 0x6827, 0x18C0, 0x08E1, 0x3882, 0x28A3,
            0xCB7D, 0xDB5C, 0xEB3F, 0xFB1E, 0x8BF9, 0x9BD8, 0xABBB, 0xBB9A,
            0x4A75, 0x5A54, 0x6A37, 0x7A16, 0x0AF1, 0x1AD0, 0x2AB3, 0x3A92,
            0xFD2E, 0xED0F, 0xDD6C, 0xCD4D, 0xBDAA, 0xAD8B, 0x9DE8, 0x8DC9,
            0x7C26, 0x6C07, 0x5C64, 0x4C45, 0x3CA2, 0x2C83, 0x1CE0, 0x0CC1,
            0xEF1F, 0xFF3E, 0xCF5D, 0xDF7C, 0xAF9B, 0xBFBA, 0x8FD9, 0x9FF8,
            0x6E17, 0x7E36, 0x4E55, 0x5E74, 0x2E93, 0x3EB2, 0x0ED1, 0x1EF0,
        ];
        $crc16 = 0xFFFF;
        $string = (string) $string;
        $len = strlen($string);
        for ($i = 0; $i < $len; $i++ ) {
            $index = ($crc16 >> 8) ^ ord($string[$i]);
            $crc16 = (($crc16 << 8) & 0xFFFF) ^ $table[$index];
        }
        $crc16 = sprintf('%04x', $crc16);
        return $crc16;
    }
    private function merchant_account_info(){
        $gui_line = '00'.$this->get_length(self::HALALAH_GUI).self::HALALAH_GUI;
        $merchant_line = '';
        if(!empty($this->merchant_id)){
            $merchant_id = $this->merchant_id;
            $merchant_line = '04'.$this->get_length($merchant_id).$merchant_id;
        }
        $branch_line = '';
        if(!empty($this->branch_id)){
            $branch_id = $this->branch_id;
            $branch_line = '05'.$this->get_length($branch_id).$branch_id;
        }
        $terminal_line = '';
        if(!empty($this->terminal_id)){
            $terminal_id = $this->terminal_id;
            $terminal_line = '06'.$this->get_length($terminal_id).$terminal_id;
        }
        $z_line = $gui_line.$merchant_line.$branch_line.$terminal_line;
        $mai_len = $this->get_length($z_line);
        $mai_o = self::HALALAH_ID.$mai_len.$z_line;
        return $mai_o;
    }
    private function mmc(){
        if(empty($this->merchant_category_code)){
            $this->add_error('MUST ADD MERCHANT CATEGORY CODE');
        }
        return $this->id_len_val('52', $this->merchant_category_code);
    }
    private function country(){
        return $this->id_len_val('58', self::COUNTRY);
    }
    private function merchant_name(){
        if(empty($this->merchant_name)){
            $this->add_error('MUST ADD MERCHANT NAME');
        }
        return $this->id_len_val('59', $this->merchant_name);
    }
    private function merchant_city(){
        if(empty($this->merchant_city)){
            $this->add_error('MUST ADD MERCHANT CITY');
        }
        return $this->id_len_val('60', $this->merchant_city);
    }
    private function postal_code(){
        if(!empty($this->postal_code)){
            return $this->id_len_val('61', $this->postal_code);
        }
    }
    private function local_lang_city(){
        $local_line = $this->id_len_val('00', self::LANG);
        $name_line = '';
        $city_line = '';
        if(!empty($this->merchant_name_ar)){
            $name_line = $this->id_len_val('01', $this->merchant_name_ar);
        }else{
            $this->add_error("MUST ADD MERCHANT NAME In Arabic Language");
        }
        if(!empty($this->merchant_city_ar)){
            $city_line = $this->id_len_val('02', $this->merchant_city_ar);
        }else{
            $this->add_error("MUST ADD MERCHANT CITY In Arabic Language");
        }
        $z_line = $local_line.$name_line.$city_line;
        $al_len = $this->get_length($z_line);
        return '64'.$al_len.$z_line;
    }
    private function amount(){
        if(empty($this->amount)){
            $this->add_error('MUST ADD AMOUNT');
        }
        return $this->id_len_val('54', $this->amount);
    }
    private function currency(){
        return $this->id_len_val('53', SELF::CURRENCY);
    }
    private function additional_data(){
        $bill_line = '';
        $mobile_line = '';
        $store_line = '';
        $loyalty_line = '';
        $reference_line = '';
        $consumer_line = '';
        $terminal_line = '';
        $purpose_line = '';
        $request = '';
        if(!empty($this->bill)){
            $bill_line = $this->id_len_val('01', $this->bill);
        }else{
            $this->add_error('MUST ADD Bill');
        }
        if(!empty($this->mobile)){ $mobile_line = $this->id_len_val('02', $this->mobile); }
        if(!empty($this->store)){ $store_line = $this->id_len_val('03', $this->store); }
        if(!empty($this->loyalty)){ $loyalty_line = $this->id_len_val('04', $this->loyalty); }
        if(!empty($this->reference)){
            $reference_line = $this->id_len_val('05', $this->reference);
        }else{
            $this->add_error('MUST ADD Reference');
        }
        if(!empty($this->consumer)){ $consumer_line = $this->id_len_val('06', $this->consumer); }
        if(!empty($this->terminal)){
            $terminal_line = $this->id_len_val('07', $this->terminal);
        }else{
            $this->add_error('MUST ADD Terminal');
        }
        if(!empty($this->purpose)){ $purpose_line = $this->id_len_val('08', $this->purpose); }
        if(!empty($this->request)){ $request = $this->id_len_val('09', $this->request); }
        $z_line = $bill_line.$mobile_line.$store_line.$loyalty_line.$reference_line.$consumer_line.$terminal_line.$purpose_line.$request;
        $al_len = $this->get_length($z_line);
        if(!empty($al_len) && !empty($z_line)){
            return '62'.$al_len.$z_line;
        }
        return "";
    }
    private function crc($o){
        $crc_id_len = '6304';
        $crc = $this->generate_crc($o.$crc_id_len);
        return $crc_id_len.$crc;
    }
    public function output(){
        $o = '';
        $o .= self::VERSION;
        $o .= self::POI;
        $o .= $this->merchant_account_info();
        $o .= $this->mmc();
        $o .= $this->country();
        $o .= $this->merchant_name();
        $o .= $this->merchant_city();
        $o .= $this->postal_code();
        $o .= $this->local_lang_city();
        $o .= $this->amount();
        $o .= $this->currency();
        $o .= $this->additional_data();
        $o .= $this->crc($o);
        return $o;
    }
}