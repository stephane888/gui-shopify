<?php
namespace GuiShopify\CodePromo;
use DB;
/**
 * 
 * @author stephane
 *
 */
class CodePromo{
    private $_WbuShopify;
    
    function __construct($configs) {
        $this->_WbuShopify = new WbuShopify( $configs );
        
    }
    /**
     *
     * @param number $length
     * @return string
     */
    public function generateRandomString($length = 4) {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    /**
     *
     */
    public function generateRandomNumber($length = 2) {
        $characters = '123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    /**
     * genere les codes qui vont etre 
     */
    public function generateRamdom($nombre_code, $partenaire){
        $result=[];
        for ($i = 0; $i < $nombre_code; $i++) {
            $String = $this->generateRandomString(4);
            $String .= $this->generateRandomNumber(2);
            
            // return MAJ
            $String = strtoupper($String);
            while($this->code_promoexist($String)){
                $String = $this->generateRandomString(4);
                $String .= $this->generateRandomNumber(2);
            }
            $this->saveUniqueRamdom( $String, $partenaire );
            $result[]=$String;
        }
        return $result;
    }
    
    public static function createCodes( array $codes ){
        $id_PriceRule = 603704787010;
        $shopify = new shopify();
        $result = [];
        $code_promo = [];
        $return_code = $shopify->createCodePromo_multi( $codes, $id_PriceRule );
        if(isset($return_code['discount_code_creation']['id'])){
            foreach ($codes as $code){
                self::updateCode($code, ['in_shopify'=>1, 'price_rule_id' => $id_PriceRule, 'created'=>1] );
                $code_promo[] = $code;
            }
        }
        return ['codes' => $code_promo, 'return_code'=>$return_code];
    }
    
    /**
     * 
     * @param  int $promo_code
     * @return mixed
     */
    public function code_promoexist($promo_code){
        $BD = new WbuJsonDB();
        $table='codes_promo';
        return DB::queryFirstRow("select * FROM `$table` WHERE `promo_code` = '$promo_code' ");
    }
}