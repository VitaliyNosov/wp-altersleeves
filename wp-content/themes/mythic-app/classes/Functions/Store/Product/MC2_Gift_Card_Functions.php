<?php

namespace Mythic\Functions\Store\Product;

use Dompdf\Dompdf;
use MC2_Order_Functions;
use Mythic\Abstracts\MC2_DB_Table;
use Mythic\Functions\MC2_Giftcard_Functions;
use Mythic\Helpers\MC2_Vars;
use Mythic\System\MC2_Statuses;
use WP_Error;

/**
 * Class MC2_Gift_Card_Functions
 *
 * @package Mythic\Objects
 */
class MC2_Gift_Card_Functions extends MC2_DB_Table {
    
    protected static $table_name = 'gift_card_codes';
    
    public function create_table_query() : string {
        return "CREATE TABLE `wp_mc_gift_card_codes` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
                  `visible_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `code` (`code`),
                  KEY `visible_id` (`visible_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }
    
    const DIR_GIFTCARDS = ABSPATH.'/files/giftcards';
    const URI_GIFTCARDS = SITE_URL.'files/giftcards';
    
    /**
     *
     * Universal Design query - ALWAYS use this to query!
     *
     * @param array $params
     * @param false $all
     *
     * @return array
     */
    public static function query( $params = [], $all = false ) : array {
        $args = [
            'post_type'      => self::$post_type,
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];
        foreach( $params as $key => $param ) $args[ $key ] = $param;
        
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => [ 'giftcard' ],
            'compare'  => 'IN',
        ];
        if( $all ) $args['post_status'] = MC2_Statuses::keys();
        
        return get_posts( $args );
    }
    
    /**
     * @return int
     */
    public static function productId() {
        $exists = get_page_by_title( 'Gift Card', OBJECT, 'product' );
        if( empty( $exists ) ) return 0;
        
        return $exists->ID;
    }
    
    /**
     * @param array $args
     */
    public static function fieldSleevesByType( $args = [] ) {
        $digital = $args['digital'] ?? 0;
        $sleeves = $args['sleeves'] ?? 0;
        include DIR_THEME_TEMPLATE_PARTS.'/store/giftcard/field-type.php';
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public static function sleeves( $id ) {
        return get_post_meta( $id, 'mc_sleeves', true );
    }
    
    public static function importGift_Card() {
        $gift_cards = self::getCouponCodesAndVisibleIds();
        if( empty( $gift_cards ) ) return;
        
        $gift_card_tax    = get_term_by( 'name', 'Gift Card', 'shop_coupon_cat' );
        $gift_card_tax_id = !empty( $gift_card_tax ) ? $gift_card_tax->term_id : 0;
        
        foreach( $gift_cards as $gift_card ) {
            $coupon_code = $gift_card['code'];
            $visible_id  = $gift_card['visible_id'];
            $exists      = get_page_by_title( $coupon_code, OBJECT, 'shop_coupon' );
            if( empty( $exists ) ) {
                $coupon_id = self::create( $coupon_code, $visible_id );
            } else {
                $coupon_id = $exists->ID;
            }
            update_post_meta( $coupon_id, 'mc_gift_card', 1 );
            update_post_meta( $coupon_id, 'mc_visible_id', $visible_id );
            if( !empty( $gift_card_tax_id ) ) wp_set_object_terms( $coupon_id, [ $gift_card_tax_id ], 'shop_coupon_cat', false );
        }
    }
    
    public static function getCouponCodesAndVisibleIds() {
        return [
            [ 'code' => 'KDYVGH', 'visible_id' => 100001 ],
            [ 'code' => 'PWGJWQ', 'visible_id' => 100002 ],
            [ 'code' => 'JMHZZK', 'visible_id' => 100003 ],
            [ 'code' => 'YNVLDJ', 'visible_id' => 100004 ],
            [ 'code' => 'VMVDDS', 'visible_id' => 100005 ],
            [ 'code' => 'FCAJKS', 'visible_id' => 100006 ],
            [ 'code' => 'JSTYCQ', 'visible_id' => 100007 ],
            [ 'code' => 'NMFCBT', 'visible_id' => 100008 ],
            [ 'code' => 'HAZRUF', 'visible_id' => 100009 ],
            [ 'code' => 'RGWCMC', 'visible_id' => 100010 ],
            [ 'code' => 'PFFMGI', 'visible_id' => 100011 ],
            [ 'code' => 'IFNQSB', 'visible_id' => 100012 ],
            [ 'code' => 'WMWSBR', 'visible_id' => 100013 ],
            [ 'code' => 'FLPKAD', 'visible_id' => 100014 ],
            [ 'code' => 'TPXRHQ', 'visible_id' => 100015 ],
            [ 'code' => 'GSLRRS', 'visible_id' => 100016 ],
            [ 'code' => 'ZLGGDB', 'visible_id' => 100017 ],
            [ 'code' => 'SKJOLS', 'visible_id' => 100018 ],
            [ 'code' => 'CCBZLZ', 'visible_id' => 100019 ],
            [ 'code' => 'WLZPEM', 'visible_id' => 100020 ],
            [ 'code' => 'PRUDNU', 'visible_id' => 100021 ],
            [ 'code' => 'RNOERJ', 'visible_id' => 100022 ],
            [ 'code' => 'LUBXNJ', 'visible_id' => 100023 ],
            [ 'code' => 'YFXQVB', 'visible_id' => 100024 ],
            [ 'code' => 'KGBPPM', 'visible_id' => 100025 ],
            [ 'code' => 'GEGPWO', 'visible_id' => 100026 ],
            [ 'code' => 'HEHEMF', 'visible_id' => 100027 ],
            [ 'code' => 'TULXLF', 'visible_id' => 100028 ],
            [ 'code' => 'CCHNWE', 'visible_id' => 100029 ],
            [ 'code' => 'KJDBOE', 'visible_id' => 100030 ],
            [ 'code' => 'SDZBJF', 'visible_id' => 100031 ],
            [ 'code' => 'KKGSYR', 'visible_id' => 100032 ],
            [ 'code' => 'ZSACOA', 'visible_id' => 100033 ],
            [ 'code' => 'SKGREV', 'visible_id' => 100034 ],
            [ 'code' => 'WPUMLR', 'visible_id' => 100035 ],
            [ 'code' => 'ABQGQL', 'visible_id' => 100036 ],
            [ 'code' => 'RKBSBV', 'visible_id' => 100037 ],
            [ 'code' => 'PLDXXD', 'visible_id' => 100038 ],
            [ 'code' => 'XSWWTR', 'visible_id' => 100039 ],
            [ 'code' => 'CXIJSA', 'visible_id' => 100040 ],
            [ 'code' => 'SGOLRI', 'visible_id' => 100041 ],
            [ 'code' => 'OQGLJM', 'visible_id' => 100042 ],
            [ 'code' => 'QIFEXD', 'visible_id' => 100043 ],
            [ 'code' => 'MIFVNX', 'visible_id' => 100044 ],
            [ 'code' => 'RZLMCZ', 'visible_id' => 100045 ],
            [ 'code' => 'WZTQDM', 'visible_id' => 100046 ],
            [ 'code' => 'REKLIL', 'visible_id' => 100047 ],
            [ 'code' => 'OBWORA', 'visible_id' => 100048 ],
            [ 'code' => 'KLVVKH', 'visible_id' => 100049 ],
            [ 'code' => 'FQWNLE', 'visible_id' => 100050 ],
            [ 'code' => 'ZLCPPZ', 'visible_id' => 100051 ],
            [ 'code' => 'BZVIVT', 'visible_id' => 100052 ],
            [ 'code' => 'VAPVNN', 'visible_id' => 100053 ],
            [ 'code' => 'SZPBOG', 'visible_id' => 100054 ],
            [ 'code' => 'IWMVGF', 'visible_id' => 100055 ],
            [ 'code' => 'LYSSXF', 'visible_id' => 100056 ],
            [ 'code' => 'XPPLQB', 'visible_id' => 100057 ],
            [ 'code' => 'VGOMHI', 'visible_id' => 100058 ],
            [ 'code' => 'BMVZZV', 'visible_id' => 100059 ],
            [ 'code' => 'GNGBHV', 'visible_id' => 100060 ],
            [ 'code' => 'WHLSKA', 'visible_id' => 100061 ],
            [ 'code' => 'YZSFHP', 'visible_id' => 100062 ],
            [ 'code' => 'KMQTYK', 'visible_id' => 100063 ],
            [ 'code' => 'KHXATY', 'visible_id' => 100064 ],
            [ 'code' => 'WEWLTT', 'visible_id' => 100065 ],
            [ 'code' => 'PREJOX', 'visible_id' => 100066 ],
            [ 'code' => 'RFCYMS', 'visible_id' => 100067 ],
            [ 'code' => 'URREQJ', 'visible_id' => 100068 ],
            [ 'code' => 'CNFYHM', 'visible_id' => 100069 ],
            [ 'code' => 'NOGQLH', 'visible_id' => 100070 ],
            [ 'code' => 'NVJKGI', 'visible_id' => 100071 ],
            [ 'code' => 'YWHXHJ', 'visible_id' => 100072 ],
            [ 'code' => 'SUAUWT', 'visible_id' => 100073 ],
            [ 'code' => 'BQIYCU', 'visible_id' => 100074 ],
            [ 'code' => 'GLNXOD', 'visible_id' => 100075 ],
            [ 'code' => 'UEOMUN', 'visible_id' => 100076 ],
            [ 'code' => 'SQCDPY', 'visible_id' => 100077 ],
            [ 'code' => 'RQBDIR', 'visible_id' => 100078 ],
            [ 'code' => 'KVBWZO', 'visible_id' => 100079 ],
            [ 'code' => 'JAHRPZ', 'visible_id' => 100080 ],
            [ 'code' => 'QGCNFZ', 'visible_id' => 100081 ],
            [ 'code' => 'YHKSDZ', 'visible_id' => 100082 ],
            [ 'code' => 'ZIODMQ', 'visible_id' => 100083 ],
            [ 'code' => 'ZAJESO', 'visible_id' => 100084 ],
            [ 'code' => 'WXGLHT', 'visible_id' => 100085 ],
            [ 'code' => 'ELURFP', 'visible_id' => 100086 ],
            [ 'code' => 'IWAKMO', 'visible_id' => 100087 ],
            [ 'code' => 'HQBROB', 'visible_id' => 100088 ],
            [ 'code' => 'DXLKWT', 'visible_id' => 100089 ],
            [ 'code' => 'YWNZJP', 'visible_id' => 100090 ],
            [ 'code' => 'KRNPXN', 'visible_id' => 100091 ],
            [ 'code' => 'DXWXWY', 'visible_id' => 100092 ],
            [ 'code' => 'IMZWQP', 'visible_id' => 100093 ],
            [ 'code' => 'ZLYPDY', 'visible_id' => 100094 ],
            [ 'code' => 'TJLGON', 'visible_id' => 100095 ],
            [ 'code' => 'JAOHRV', 'visible_id' => 100096 ],
            [ 'code' => 'RIINUQ', 'visible_id' => 100097 ],
            [ 'code' => 'DBZYUO', 'visible_id' => 100098 ],
            [ 'code' => 'ARAOAV', 'visible_id' => 100099 ],
            [ 'code' => 'VZLHOD', 'visible_id' => 100100 ],
            [ 'code' => 'EADKIL', 'visible_id' => 100101 ],
            [ 'code' => 'VNMZCA', 'visible_id' => 100102 ],
            [ 'code' => 'ANHNQL', 'visible_id' => 100103 ],
            [ 'code' => 'YMEQHG', 'visible_id' => 100104 ],
            [ 'code' => 'MDILAM', 'visible_id' => 100105 ],
            [ 'code' => 'DGVMZC', 'visible_id' => 100106 ],
            [ 'code' => 'IVQTVS', 'visible_id' => 100107 ],
            [ 'code' => 'NQTKFC', 'visible_id' => 100108 ],
            [ 'code' => 'XWSZWT', 'visible_id' => 100109 ],
            [ 'code' => 'QQLHJL', 'visible_id' => 100110 ],
            [ 'code' => 'NMSLCN', 'visible_id' => 100111 ],
            [ 'code' => 'IRRIFU', 'visible_id' => 100112 ],
            [ 'code' => 'VBHLDD', 'visible_id' => 100113 ],
            [ 'code' => 'CEQSQV', 'visible_id' => 100114 ],
            [ 'code' => 'ZDTUQQ', 'visible_id' => 100115 ],
            [ 'code' => 'TCSRSL', 'visible_id' => 100116 ],
            [ 'code' => 'QBCBBG', 'visible_id' => 100117 ],
            [ 'code' => 'NPCNFI', 'visible_id' => 100118 ],
            [ 'code' => 'CKIUCW', 'visible_id' => 100119 ],
            [ 'code' => 'DNXEBU', 'visible_id' => 100120 ],
            [ 'code' => 'KJEZYV', 'visible_id' => 100121 ],
            [ 'code' => 'GUVIFU', 'visible_id' => 100122 ],
            [ 'code' => 'JLROWJ', 'visible_id' => 100123 ],
            [ 'code' => 'PIQBOQ', 'visible_id' => 100124 ],
            [ 'code' => 'RAVCZN', 'visible_id' => 100125 ],
            [ 'code' => 'NZXARM', 'visible_id' => 100126 ],
            [ 'code' => 'PFGQJE', 'visible_id' => 100127 ],
            [ 'code' => 'YIKTNN', 'visible_id' => 100128 ],
            [ 'code' => 'XNRNNH', 'visible_id' => 100129 ],
            [ 'code' => 'RQQSCF', 'visible_id' => 100130 ],
            [ 'code' => 'FFAGBW', 'visible_id' => 100131 ],
            [ 'code' => 'NHOIRO', 'visible_id' => 100132 ],
            [ 'code' => 'LDMNAE', 'visible_id' => 100133 ],
            [ 'code' => 'JJGFRB', 'visible_id' => 100134 ],
            [ 'code' => 'RLTHJY', 'visible_id' => 100135 ],
            [ 'code' => 'DCRSKE', 'visible_id' => 100136 ],
            [ 'code' => 'AYNMKV', 'visible_id' => 100137 ],
            [ 'code' => 'AWDJRY', 'visible_id' => 100138 ],
            [ 'code' => 'RJJHFO', 'visible_id' => 100139 ],
            [ 'code' => 'KJZRUI', 'visible_id' => 100140 ],
            [ 'code' => 'HVYXFC', 'visible_id' => 100141 ],
            [ 'code' => 'NBXENH', 'visible_id' => 100142 ],
            [ 'code' => 'XZYKSV', 'visible_id' => 100143 ],
            [ 'code' => 'ZCFOBN', 'visible_id' => 100144 ],
            [ 'code' => 'QCLQBU', 'visible_id' => 100145 ],
            [ 'code' => 'YGSCBL', 'visible_id' => 100146 ],
            [ 'code' => 'LOAWHU', 'visible_id' => 100147 ],
            [ 'code' => 'PDTQBE', 'visible_id' => 100148 ],
            [ 'code' => 'VUTGWB', 'visible_id' => 100149 ],
            [ 'code' => 'PQGMNU', 'visible_id' => 100150 ],
            [ 'code' => 'HTSRXG', 'visible_id' => 100151 ],
            [ 'code' => 'DRHOCC', 'visible_id' => 100152 ],
            [ 'code' => 'CZCLOG', 'visible_id' => 100153 ],
            [ 'code' => 'ITKSQE', 'visible_id' => 100154 ],
            [ 'code' => 'GMKEYN', 'visible_id' => 100155 ],
            [ 'code' => 'QHHEVW', 'visible_id' => 100156 ],
            [ 'code' => 'ONXTJJ', 'visible_id' => 100157 ],
            [ 'code' => 'TDIMQQ', 'visible_id' => 100158 ],
            [ 'code' => 'OADFCI', 'visible_id' => 100159 ],
            [ 'code' => 'EJLEKS', 'visible_id' => 100160 ],
            [ 'code' => 'ASPBRW', 'visible_id' => 100161 ],
            [ 'code' => 'CZEJDU', 'visible_id' => 100162 ],
            [ 'code' => 'RRDORJ', 'visible_id' => 100163 ],
            [ 'code' => 'XUWYSC', 'visible_id' => 100164 ],
            [ 'code' => 'RWQRJD', 'visible_id' => 100165 ],
            [ 'code' => 'SEJTXC', 'visible_id' => 100166 ],
            [ 'code' => 'GEWVJU', 'visible_id' => 100167 ],
            [ 'code' => 'OFXKEY', 'visible_id' => 100168 ],
            [ 'code' => 'AGWDYE', 'visible_id' => 100169 ],
            [ 'code' => 'GHMJXB', 'visible_id' => 100170 ],
            [ 'code' => 'ZHUNAY', 'visible_id' => 100171 ],
            [ 'code' => 'PKIEZI', 'visible_id' => 100172 ],
            [ 'code' => 'XGVKYJ', 'visible_id' => 100173 ],
            [ 'code' => 'RPOOSK', 'visible_id' => 100174 ],
            [ 'code' => 'SAZZCO', 'visible_id' => 100175 ],
            [ 'code' => 'PILKMC', 'visible_id' => 100176 ],
            [ 'code' => 'CSOCGX', 'visible_id' => 100177 ],
            [ 'code' => 'UPDQMT', 'visible_id' => 100178 ],
            [ 'code' => 'LLVYCD', 'visible_id' => 100179 ],
            [ 'code' => 'CIBFXV', 'visible_id' => 100180 ],
            [ 'code' => 'ATEWMR', 'visible_id' => 100181 ],
            [ 'code' => 'QEWCIV', 'visible_id' => 100182 ],
            [ 'code' => 'NVTKPH', 'visible_id' => 100183 ],
            [ 'code' => 'ZVHVYL', 'visible_id' => 100184 ],
            [ 'code' => 'XOCWOS', 'visible_id' => 100185 ],
            [ 'code' => 'IILADT', 'visible_id' => 100186 ],
            [ 'code' => 'UMQULY', 'visible_id' => 100187 ],
            [ 'code' => 'IHAVWU', 'visible_id' => 100188 ],
            [ 'code' => 'FQFIGZ', 'visible_id' => 100189 ],
            [ 'code' => 'ARMJVB', 'visible_id' => 100190 ],
            [ 'code' => 'ZXHRDD', 'visible_id' => 100191 ],
            [ 'code' => 'LTORPJ', 'visible_id' => 100192 ],
            [ 'code' => 'JICKUA', 'visible_id' => 100193 ],
            [ 'code' => 'WJNKCS', 'visible_id' => 100194 ],
            [ 'code' => 'WSYVYO', 'visible_id' => 100195 ],
            [ 'code' => 'QNJNFC', 'visible_id' => 100196 ],
            [ 'code' => 'XFVBQP', 'visible_id' => 100197 ],
            [ 'code' => 'NZBWKV', 'visible_id' => 100198 ],
            [ 'code' => 'ZQQJXZ', 'visible_id' => 100199 ],
            [ 'code' => 'KMRCFI', 'visible_id' => 100200 ],
            [ 'code' => 'TKQXCI', 'visible_id' => 100201 ],
            [ 'code' => 'OPUKRO', 'visible_id' => 100202 ],
            [ 'code' => 'TLLVHK', 'visible_id' => 100203 ],
            [ 'code' => 'SLUDQW', 'visible_id' => 100204 ],
            [ 'code' => 'YNZQKM', 'visible_id' => 100205 ],
            [ 'code' => 'CWQZKO', 'visible_id' => 100206 ],
            [ 'code' => 'GEUBOU', 'visible_id' => 100207 ],
            [ 'code' => 'IIGWPK', 'visible_id' => 100208 ],
            [ 'code' => 'QGMYJN', 'visible_id' => 100209 ],
            [ 'code' => 'XMCDVO', 'visible_id' => 100210 ],
            [ 'code' => 'TYRVIU', 'visible_id' => 100211 ],
            [ 'code' => 'XGUTTQ', 'visible_id' => 100212 ],
            [ 'code' => 'PYOGMX', 'visible_id' => 100213 ],
            [ 'code' => 'HMMFGP', 'visible_id' => 100214 ],
            [ 'code' => 'LBRUXD', 'visible_id' => 100215 ],
            [ 'code' => 'CKBPJV', 'visible_id' => 100216 ],
            [ 'code' => 'NRUZWR', 'visible_id' => 100217 ],
            [ 'code' => 'KZLCXM', 'visible_id' => 100218 ],
            [ 'code' => 'CADSYQ', 'visible_id' => 100219 ],
            [ 'code' => 'QXMTYL', 'visible_id' => 100220 ],
            [ 'code' => 'JIRKOE', 'visible_id' => 100221 ],
            [ 'code' => 'TRHDEO', 'visible_id' => 100222 ],
            [ 'code' => 'YOEAQD', 'visible_id' => 100223 ],
            [ 'code' => 'KOIUVZ', 'visible_id' => 100224 ],
            [ 'code' => 'HJQGIU', 'visible_id' => 100225 ],
            [ 'code' => 'YTQUOC', 'visible_id' => 100226 ],
            [ 'code' => 'ORRABG', 'visible_id' => 100227 ],
            [ 'code' => 'GHGEHO', 'visible_id' => 100228 ],
            [ 'code' => 'OBZTIP', 'visible_id' => 100229 ],
            [ 'code' => 'GXVVLV', 'visible_id' => 100230 ],
            [ 'code' => 'RQAFTP', 'visible_id' => 100231 ],
            [ 'code' => 'XVFLGI', 'visible_id' => 100232 ],
            [ 'code' => 'FZLRAC', 'visible_id' => 100233 ],
            [ 'code' => 'ZJUXEW', 'visible_id' => 100234 ],
            [ 'code' => 'KZCDZY', 'visible_id' => 100235 ],
            [ 'code' => 'YUKKBD', 'visible_id' => 100236 ],
            [ 'code' => 'NVMKWY', 'visible_id' => 100237 ],
            [ 'code' => 'MCNSPJ', 'visible_id' => 100238 ],
            [ 'code' => 'CUDKTG', 'visible_id' => 100239 ],
            [ 'code' => 'GAUUHE', 'visible_id' => 100240 ],
            [ 'code' => 'FDAFJO', 'visible_id' => 100241 ],
            [ 'code' => 'SFCMEW', 'visible_id' => 100242 ],
            [ 'code' => 'RIEMVD', 'visible_id' => 100243 ],
            [ 'code' => 'BTWZEE', 'visible_id' => 100244 ],
            [ 'code' => 'RWKZBX', 'visible_id' => 100245 ],
            [ 'code' => 'PGJIFW', 'visible_id' => 100246 ],
            [ 'code' => 'RNZJIM', 'visible_id' => 100247 ],
            [ 'code' => 'IMUEQX', 'visible_id' => 100248 ],
            [ 'code' => 'ZQIIPH', 'visible_id' => 100249 ],
            [ 'code' => 'GCZQXA', 'visible_id' => 100250 ],
            [ 'code' => 'QSMEKJ', 'visible_id' => 100251 ],
            [ 'code' => 'OVQRBX', 'visible_id' => 100252 ],
            [ 'code' => 'JPJRRX', 'visible_id' => 100253 ],
            [ 'code' => 'PKINQE', 'visible_id' => 100254 ],
            [ 'code' => 'JXNNKD', 'visible_id' => 100255 ],
            [ 'code' => 'DGELXG', 'visible_id' => 100256 ],
            [ 'code' => 'AJBTQS', 'visible_id' => 100257 ],
            [ 'code' => 'TQNCRO', 'visible_id' => 100258 ],
            [ 'code' => 'LPCVFB', 'visible_id' => 100259 ],
            [ 'code' => 'WUXXQI', 'visible_id' => 100260 ],
            [ 'code' => 'HGWUGE', 'visible_id' => 100261 ],
            [ 'code' => 'GZQZIQ', 'visible_id' => 100262 ],
            [ 'code' => 'OFZSDR', 'visible_id' => 100263 ],
            [ 'code' => 'LIKGYA', 'visible_id' => 100264 ],
            [ 'code' => 'EWYLNS', 'visible_id' => 100265 ],
            [ 'code' => 'XOWPUT', 'visible_id' => 100266 ],
            [ 'code' => 'FSZTSA', 'visible_id' => 100267 ],
            [ 'code' => 'TEYJVU', 'visible_id' => 100268 ],
            [ 'code' => 'WVBCOY', 'visible_id' => 100269 ],
            [ 'code' => 'EKRSAA', 'visible_id' => 100270 ],
            [ 'code' => 'ECJQKM', 'visible_id' => 100271 ],
            [ 'code' => 'CFMYGZ', 'visible_id' => 100272 ],
            [ 'code' => 'CSWBRX', 'visible_id' => 100273 ],
            [ 'code' => 'MGGUJT', 'visible_id' => 100274 ],
            [ 'code' => 'EFBAXQ', 'visible_id' => 100275 ],
            [ 'code' => 'PBXWVF', 'visible_id' => 100276 ],
            [ 'code' => 'HLKEGE', 'visible_id' => 100277 ],
            [ 'code' => 'BQVGMU', 'visible_id' => 100278 ],
            [ 'code' => 'VXTCNO', 'visible_id' => 100279 ],
            [ 'code' => 'FYTPPA', 'visible_id' => 100280 ],
            [ 'code' => 'LRRHWL', 'visible_id' => 100281 ],
            [ 'code' => 'OWBFUY', 'visible_id' => 100282 ],
            [ 'code' => 'EEEIAX', 'visible_id' => 100283 ],
            [ 'code' => 'ZUZJEH', 'visible_id' => 100284 ],
            [ 'code' => 'DRHANH', 'visible_id' => 100285 ],
            [ 'code' => 'HKSNHD', 'visible_id' => 100286 ],
            [ 'code' => 'UEEUXF', 'visible_id' => 100287 ],
            [ 'code' => 'ZWJZAK', 'visible_id' => 100288 ],
            [ 'code' => 'SJPQVY', 'visible_id' => 100289 ],
            [ 'code' => 'HFCPXJ', 'visible_id' => 100290 ],
            [ 'code' => 'KIOQRD', 'visible_id' => 100291 ],
            [ 'code' => 'KLWMTX', 'visible_id' => 100292 ],
            [ 'code' => 'DMZZJB', 'visible_id' => 100293 ],
            [ 'code' => 'VJSMCS', 'visible_id' => 100294 ],
            [ 'code' => 'JLKHQJ', 'visible_id' => 100295 ],
            [ 'code' => 'LGRJZR', 'visible_id' => 100296 ],
            [ 'code' => 'YFXNWO', 'visible_id' => 100297 ],
            [ 'code' => 'WKEOFG', 'visible_id' => 100298 ],
            [ 'code' => 'HUNKCL', 'visible_id' => 100299 ],
            [ 'code' => 'FCHZAC', 'visible_id' => 100300 ],
            [ 'code' => 'JAUVWE', 'visible_id' => 100301 ],
            [ 'code' => 'COSSXN', 'visible_id' => 100302 ],
            [ 'code' => 'LGQENX', 'visible_id' => 100303 ],
            [ 'code' => 'PTDBWI', 'visible_id' => 100304 ],
            [ 'code' => 'BNNOAN', 'visible_id' => 100305 ],
            [ 'code' => 'QHEDII', 'visible_id' => 100306 ],
            [ 'code' => 'QMZCGNVK', 'visible_id' => 100307 ],
            [ 'code' => 'ZPYGEZJN', 'visible_id' => 100308 ],
            [ 'code' => 'EYQGVP', 'visible_id' => 100309 ],
            [ 'code' => 'MLXJRX', 'visible_id' => 100310 ],
            [ 'code' => 'ONNOVC', 'visible_id' => 100311 ],
            [ 'code' => 'WFGLQB', 'visible_id' => 100312 ],
            [ 'code' => 'GRGWAU', 'visible_id' => 100313 ],
            [ 'code' => 'PLVCWP', 'visible_id' => 100314 ],
            [ 'code' => 'AZLHJY', 'visible_id' => 100315 ],
            [ 'code' => 'VPMRGR', 'visible_id' => 100316 ],
            [ 'code' => 'CYMMNL', 'visible_id' => 100317 ],
            [ 'code' => 'UOGIHV', 'visible_id' => 100318 ],
            [ 'code' => 'NCFVIR', 'visible_id' => 100319 ],
            [ 'code' => 'STYGQA', 'visible_id' => 100320 ],
            [ 'code' => 'MAZEAW', 'visible_id' => 100321 ],
            [ 'code' => 'CIHCHF', 'visible_id' => 100322 ],
            [ 'code' => 'NJCNVJ', 'visible_id' => 100323 ],
            [ 'code' => 'LOWKRR', 'visible_id' => 100324 ],
            [ 'code' => 'KLPCYZ', 'visible_id' => 100325 ],
            [ 'code' => 'DCTQQO', 'visible_id' => 100326 ],
            [ 'code' => 'JOEKIV', 'visible_id' => 100327 ],
            [ 'code' => 'KDYZDS', 'visible_id' => 100328 ],
            [ 'code' => 'OOINCE', 'visible_id' => 100329 ],
            [ 'code' => 'TQREFU', 'visible_id' => 100330 ],
            [ 'code' => 'SWOKIL', 'visible_id' => 100331 ],
            [ 'code' => 'VLFQBT', 'visible_id' => 100332 ],
            [ 'code' => 'GMLGZB', 'visible_id' => 100333 ],
            [ 'code' => 'XEIEAN', 'visible_id' => 100334 ],
            [ 'code' => 'JDTFDO', 'visible_id' => 100335 ],
            [ 'code' => 'GKUTKQ', 'visible_id' => 100336 ],
            [ 'code' => 'IANTNG', 'visible_id' => 100337 ],
            [ 'code' => 'PZWUYT', 'visible_id' => 100338 ],
            [ 'code' => 'WUEKFY', 'visible_id' => 100339 ],
            [ 'code' => 'RHOBOL', 'visible_id' => 100340 ],
            [ 'code' => 'XDJAFK', 'visible_id' => 100341 ],
            [ 'code' => 'DWPORG', 'visible_id' => 100342 ],
            [ 'code' => 'KEFYRL', 'visible_id' => 100343 ],
            [ 'code' => 'ZORCOP', 'visible_id' => 100344 ],
            [ 'code' => 'WKGXLQ', 'visible_id' => 100345 ],
            [ 'code' => 'OUQQGD', 'visible_id' => 100346 ],
            [ 'code' => 'QQMDHB', 'visible_id' => 100347 ],
            [ 'code' => 'XCKKZD', 'visible_id' => 100348 ],
            [ 'code' => 'GZBDOR', 'visible_id' => 100349 ],
            [ 'code' => 'DXIJVW', 'visible_id' => 100350 ],
            [ 'code' => 'QQZWLG', 'visible_id' => 100351 ],
            [ 'code' => 'ITTKMZ', 'visible_id' => 100352 ],
            [ 'code' => 'IMEBDO', 'visible_id' => 100353 ],
            [ 'code' => 'GJBYGL', 'visible_id' => 100354 ],
            [ 'code' => 'VYFFZH', 'visible_id' => 100355 ],
            [ 'code' => 'HGUBDR', 'visible_id' => 100356 ],
            [ 'code' => 'RQUTLM', 'visible_id' => 100357 ],
            [ 'code' => 'GHUPRN', 'visible_id' => 100358 ],
            [ 'code' => 'FWDRRG', 'visible_id' => 100359 ],
            [ 'code' => 'XQUIDO', 'visible_id' => 100360 ],
            [ 'code' => 'VOJFVL', 'visible_id' => 100361 ],
            [ 'code' => 'SIZHVB', 'visible_id' => 100362 ],
            [ 'code' => 'GEFIQT', 'visible_id' => 100363 ],
            [ 'code' => 'NVHKWB', 'visible_id' => 100364 ],
            [ 'code' => 'AYERGL', 'visible_id' => 100365 ],
            [ 'code' => 'UOJEVH', 'visible_id' => 100366 ],
            [ 'code' => 'KPHWPM', 'visible_id' => 100367 ],
            [ 'code' => 'JVWMUC', 'visible_id' => 100368 ],
            [ 'code' => 'NUFVPZ', 'visible_id' => 100369 ],
            [ 'code' => 'TIXCZH', 'visible_id' => 100370 ],
            [ 'code' => 'ATHNYP', 'visible_id' => 100371 ],
            [ 'code' => 'PFTTUN', 'visible_id' => 100372 ],
            [ 'code' => 'WEERSC', 'visible_id' => 100373 ],
            [ 'code' => 'QPTBVI', 'visible_id' => 100374 ],
            [ 'code' => 'AKIZKL', 'visible_id' => 100375 ],
            [ 'code' => 'UWKLUW', 'visible_id' => 100376 ],
            [ 'code' => 'BLNASB', 'visible_id' => 100377 ],
            [ 'code' => 'JWFVRP', 'visible_id' => 100378 ],
            [ 'code' => 'AREWWU', 'visible_id' => 100379 ],
            [ 'code' => 'AHNNCW', 'visible_id' => 100380 ],
            [ 'code' => 'ELDHAJ', 'visible_id' => 100381 ],
            [ 'code' => 'OUAHIC', 'visible_id' => 100382 ],
            [ 'code' => 'ADZNWW', 'visible_id' => 100383 ],
            [ 'code' => 'FFLBOS', 'visible_id' => 100384 ],
            [ 'code' => 'HKQNXN', 'visible_id' => 100385 ],
            [ 'code' => 'HLIGUB', 'visible_id' => 100386 ],
            [ 'code' => 'RCAFQC', 'visible_id' => 100387 ],
            [ 'code' => 'XMTOMT', 'visible_id' => 100388 ],
            [ 'code' => 'WNJNQV', 'visible_id' => 100389 ],
            [ 'code' => 'DDDOUX', 'visible_id' => 100390 ],
            [ 'code' => 'ETCTOW', 'visible_id' => 100391 ],
            [ 'code' => 'RPFWUW', 'visible_id' => 100392 ],
            [ 'code' => 'CIFTHL', 'visible_id' => 100393 ],
            [ 'code' => 'EXMAKC', 'visible_id' => 100394 ],
            [ 'code' => 'THCIWK', 'visible_id' => 100395 ],
            [ 'code' => 'SHFTPR', 'visible_id' => 100396 ],
            [ 'code' => 'BVUBVZ', 'visible_id' => 100397 ],
            [ 'code' => 'TUTRGX', 'visible_id' => 100398 ],
            [ 'code' => 'KHLYKI', 'visible_id' => 100399 ],
            [ 'code' => 'ERZUUX', 'visible_id' => 100400 ],
            [ 'code' => 'QKXWEO', 'visible_id' => 100401 ],
            [ 'code' => 'QHJMHH', 'visible_id' => 100402 ],
            [ 'code' => 'USLEIZ', 'visible_id' => 100403 ],
            [ 'code' => 'QWBYRY', 'visible_id' => 100404 ],
            [ 'code' => 'HNXVHQ', 'visible_id' => 100405 ],
            [ 'code' => 'CZZSSB', 'visible_id' => 100406 ],
            [ 'code' => 'ZHTYGL', 'visible_id' => 100407 ],
            [ 'code' => 'ETXFGR', 'visible_id' => 100408 ],
            [ 'code' => 'QANMMW', 'visible_id' => 100409 ],
            [ 'code' => 'QKKQRT', 'visible_id' => 100410 ],
            [ 'code' => 'MPIMQM', 'visible_id' => 100411 ],
            [ 'code' => 'ZEBWCY', 'visible_id' => 100412 ],
            [ 'code' => 'YFVHUZ', 'visible_id' => 100413 ],
            [ 'code' => 'KUWZCM', 'visible_id' => 100414 ],
            [ 'code' => 'DIZRRT', 'visible_id' => 100415 ],
            [ 'code' => 'DVLGCV', 'visible_id' => 100416 ],
            [ 'code' => 'ECGJZP', 'visible_id' => 100417 ],
            [ 'code' => 'HGBOGN', 'visible_id' => 100418 ],
            [ 'code' => 'INHSGR', 'visible_id' => 100419 ],
            [ 'code' => 'CABUSM', 'visible_id' => 100420 ],
            [ 'code' => 'EOYSLI', 'visible_id' => 100421 ],
            [ 'code' => 'RRQRKA', 'visible_id' => 100422 ],
            [ 'code' => 'ZTTHIZ', 'visible_id' => 100423 ],
            [ 'code' => 'MDYDWM', 'visible_id' => 100424 ],
            [ 'code' => 'MFHLHK', 'visible_id' => 100425 ],
            [ 'code' => 'VGIWIG', 'visible_id' => 100426 ],
            [ 'code' => 'TIYYTX', 'visible_id' => 100427 ],
            [ 'code' => 'HOMVZX', 'visible_id' => 100428 ],
            [ 'code' => 'YFOBBT', 'visible_id' => 100429 ],
            [ 'code' => 'ITPHZC', 'visible_id' => 100430 ],
            [ 'code' => 'LRFFFP', 'visible_id' => 100431 ],
            [ 'code' => 'ROEYYB', 'visible_id' => 100432 ],
            [ 'code' => 'FNSAKT', 'visible_id' => 100433 ],
            [ 'code' => 'QATNOD', 'visible_id' => 100434 ],
            [ 'code' => 'EJVKNN', 'visible_id' => 100435 ],
            [ 'code' => 'ABKBQV', 'visible_id' => 100436 ],
            [ 'code' => 'DGQKQI', 'visible_id' => 100437 ],
            [ 'code' => 'WRYUHV', 'visible_id' => 100438 ],
            [ 'code' => 'WNVTKL', 'visible_id' => 100439 ],
            [ 'code' => 'PLURJM', 'visible_id' => 100440 ],
            [ 'code' => 'SHIMNP', 'visible_id' => 100441 ],
            [ 'code' => 'WBJXLG', 'visible_id' => 100442 ],
            [ 'code' => 'QWPVHM', 'visible_id' => 100443 ],
            [ 'code' => 'MVQMNM', 'visible_id' => 100444 ],
            [ 'code' => 'GKHTGC', 'visible_id' => 100445 ],
            [ 'code' => 'FUEOTH', 'visible_id' => 100446 ],
            [ 'code' => 'JHXQWL', 'visible_id' => 100447 ],
            [ 'code' => 'DUCLRL', 'visible_id' => 100448 ],
            [ 'code' => 'WGFVYI', 'visible_id' => 100449 ],
            [ 'code' => 'XDCDLW', 'visible_id' => 100450 ],
            [ 'code' => 'IQHNJM', 'visible_id' => 100451 ],
            [ 'code' => 'HJCLNG', 'visible_id' => 100452 ],
            [ 'code' => 'EZYSDB', 'visible_id' => 100453 ],
            [ 'code' => 'QCKCSC', 'visible_id' => 100454 ],
            [ 'code' => 'UMXDUW', 'visible_id' => 100455 ],
            [ 'code' => 'FNBYGG', 'visible_id' => 100456 ],
            [ 'code' => 'UUGWHY', 'visible_id' => 100457 ],
            [ 'code' => 'ROLQIE', 'visible_id' => 100458 ],
            [ 'code' => 'TXYFWF', 'visible_id' => 100459 ],
            [ 'code' => 'PWOFAN', 'visible_id' => 100460 ],
            [ 'code' => 'FXGVEO', 'visible_id' => 100461 ],
            [ 'code' => 'TZBJCH', 'visible_id' => 100462 ],
            [ 'code' => 'VEGEWQ', 'visible_id' => 100463 ],
            [ 'code' => 'SATHPN', 'visible_id' => 100464 ],
            [ 'code' => 'DUSKXK', 'visible_id' => 100465 ],
            [ 'code' => 'JUDUNI', 'visible_id' => 100466 ],
            [ 'code' => 'XRQQFQ', 'visible_id' => 100467 ],
            [ 'code' => 'FXNWIM', 'visible_id' => 100468 ],
            [ 'code' => 'FRVUGT', 'visible_id' => 100469 ],
            [ 'code' => 'WABAVL', 'visible_id' => 100470 ],
            [ 'code' => 'HJJUYV', 'visible_id' => 100471 ],
            [ 'code' => 'BAOUDL', 'visible_id' => 100472 ],
            [ 'code' => 'NCTUSN', 'visible_id' => 100473 ],
            [ 'code' => 'HJMFWP', 'visible_id' => 100474 ],
            [ 'code' => 'OZLVXB', 'visible_id' => 100475 ],
            [ 'code' => 'FLHKSR', 'visible_id' => 100476 ],
            [ 'code' => 'IDSGGJ', 'visible_id' => 100477 ],
            [ 'code' => 'QWVJUV', 'visible_id' => 100478 ],
            [ 'code' => 'SIXWYU', 'visible_id' => 100479 ],
            [ 'code' => 'PULLCC', 'visible_id' => 100480 ],
            [ 'code' => 'CFGREO', 'visible_id' => 100481 ],
            [ 'code' => 'HDKVTT', 'visible_id' => 100482 ],
            [ 'code' => 'GPXFQU', 'visible_id' => 100483 ],
            [ 'code' => 'PAOBFE', 'visible_id' => 100484 ],
            [ 'code' => 'SBMQEX', 'visible_id' => 100485 ],
            [ 'code' => 'RNWXVG', 'visible_id' => 100486 ],
            [ 'code' => 'JQDCDZ', 'visible_id' => 100487 ],
            [ 'code' => 'UEJGTG', 'visible_id' => 100488 ],
            [ 'code' => 'KASYJR', 'visible_id' => 100489 ],
            [ 'code' => 'WLKOHX', 'visible_id' => 100490 ],
            [ 'code' => 'DCQOOP', 'visible_id' => 100491 ],
            [ 'code' => 'JBWCDU', 'visible_id' => 100492 ],
            [ 'code' => 'MGEFJT', 'visible_id' => 100493 ],
            [ 'code' => 'VYLYLP', 'visible_id' => 100494 ],
            [ 'code' => 'BZZCKN', 'visible_id' => 100495 ],
            [ 'code' => 'AMGZUA', 'visible_id' => 100496 ],
            [ 'code' => 'UXYFPH', 'visible_id' => 100497 ],
            [ 'code' => 'GBBDUK', 'visible_id' => 100498 ],
            [ 'code' => 'ETEIRA', 'visible_id' => 100499 ],
            [ 'code' => 'UVSIMP', 'visible_id' => 100500 ],
            [ 'code' => 'DQDSHX', 'visible_id' => 100501 ],
            [ 'code' => 'IUPQZD', 'visible_id' => 100502 ],
            [ 'code' => 'TTSCXR', 'visible_id' => 100503 ],
            [ 'code' => 'EDXFBF', 'visible_id' => 100504 ],
            [ 'code' => 'MFECCP', 'visible_id' => 100505 ],
            [ 'code' => 'WUIBOA', 'visible_id' => 100506 ],
            [ 'code' => 'QIEFWJ', 'visible_id' => 100507 ],
            [ 'code' => 'VGOPYQ', 'visible_id' => 100508 ],
            [ 'code' => 'YVHQEQ', 'visible_id' => 100509 ],
            [ 'code' => 'MKHXXU', 'visible_id' => 100510 ],
            [ 'code' => 'ZIIHEW', 'visible_id' => 100511 ],
            [ 'code' => 'HVIOMP', 'visible_id' => 100512 ],
            [ 'code' => 'NWIEXS', 'visible_id' => 100513 ],
            [ 'code' => 'CUWZXJ', 'visible_id' => 100514 ],
            [ 'code' => 'VJRBSK', 'visible_id' => 100515 ],
            [ 'code' => 'XCFYUD', 'visible_id' => 100516 ],
            [ 'code' => 'VXMNFR', 'visible_id' => 100517 ],
            [ 'code' => 'KCZERQ', 'visible_id' => 100518 ],
            [ 'code' => 'VBECZQ', 'visible_id' => 100519 ],
            [ 'code' => 'GUEBHQ', 'visible_id' => 100520 ],
            [ 'code' => 'NCOUGS', 'visible_id' => 100521 ],
            [ 'code' => 'AXPEIT', 'visible_id' => 100522 ],
            [ 'code' => 'CKWXXU', 'visible_id' => 100523 ],
            [ 'code' => 'YNTDFL', 'visible_id' => 100524 ],
            [ 'code' => 'LJEUYH', 'visible_id' => 100525 ],
            [ 'code' => 'DRFWGV', 'visible_id' => 100526 ],
            [ 'code' => 'YDMGZC', 'visible_id' => 100527 ],
            [ 'code' => 'BLQCJH', 'visible_id' => 100528 ],
            [ 'code' => 'XQFOWC', 'visible_id' => 100529 ],
            [ 'code' => 'YJMJBZ', 'visible_id' => 100530 ],
            [ 'code' => 'YAWGTM', 'visible_id' => 100531 ],
            [ 'code' => 'RPBUBZ', 'visible_id' => 100532 ],
            [ 'code' => 'HHMGTD', 'visible_id' => 100533 ],
            [ 'code' => 'XFTIRR', 'visible_id' => 100534 ],
            [ 'code' => 'WDNGIM', 'visible_id' => 100535 ],
            [ 'code' => 'FEOFDG', 'visible_id' => 100536 ],
            [ 'code' => 'ECRUTN', 'visible_id' => 100537 ],
            [ 'code' => 'MPVWYQ', 'visible_id' => 100538 ],
            [ 'code' => 'QHXTVM', 'visible_id' => 100539 ],
            [ 'code' => 'YOHGKN', 'visible_id' => 100540 ],
            [ 'code' => 'KZHWWU', 'visible_id' => 100541 ],
            [ 'code' => 'SARAWX', 'visible_id' => 100542 ],
            [ 'code' => 'SJVCLC', 'visible_id' => 100543 ],
            [ 'code' => 'GMLLEG', 'visible_id' => 100544 ],
            [ 'code' => 'UOXCHP', 'visible_id' => 100545 ],
            [ 'code' => 'MKKSDM', 'visible_id' => 100546 ],
            [ 'code' => 'WMQZIX', 'visible_id' => 100547 ],
            [ 'code' => 'TTCBUS', 'visible_id' => 100548 ],
            [ 'code' => 'BORIFZ', 'visible_id' => 100549 ],
            [ 'code' => 'GAXBZH', 'visible_id' => 100550 ],
            [ 'code' => 'YQPEJI', 'visible_id' => 100551 ],
            [ 'code' => 'SFHSST', 'visible_id' => 100552 ],
            [ 'code' => 'YMVTFD', 'visible_id' => 100553 ],
            [ 'code' => 'XIDJSI', 'visible_id' => 100554 ],
            [ 'code' => 'RWVOYA', 'visible_id' => 100555 ],
            [ 'code' => 'ZFKPMV', 'visible_id' => 100556 ],
            [ 'code' => 'WZQPBS', 'visible_id' => 100557 ],
            [ 'code' => 'UDWRON', 'visible_id' => 100558 ],
            [ 'code' => 'NPPOBC', 'visible_id' => 100559 ],
            [ 'code' => 'FFCSAW', 'visible_id' => 100560 ],
            [ 'code' => 'RTHQIQ', 'visible_id' => 100561 ],
            [ 'code' => 'YLALXS', 'visible_id' => 100562 ],
            [ 'code' => 'SBFITZ', 'visible_id' => 100563 ],
            [ 'code' => 'VHFUBV', 'visible_id' => 100564 ],
            [ 'code' => 'ZJCZIC', 'visible_id' => 100565 ],
            [ 'code' => 'PDOHFE', 'visible_id' => 100566 ],
            [ 'code' => 'ERCLBJ', 'visible_id' => 100567 ],
            [ 'code' => 'XEFUWM', 'visible_id' => 100568 ],
            [ 'code' => 'KYZYZU', 'visible_id' => 100569 ],
            [ 'code' => 'PZCTZJ', 'visible_id' => 100570 ],
            [ 'code' => 'TIQDUK', 'visible_id' => 100571 ],
            [ 'code' => 'ETWLCO', 'visible_id' => 100572 ],
            [ 'code' => 'RJYYRU', 'visible_id' => 100573 ],
            [ 'code' => 'MQSTOE', 'visible_id' => 100574 ],
            [ 'code' => 'DWCVKE', 'visible_id' => 100575 ],
            [ 'code' => 'XRIFUP', 'visible_id' => 100576 ],
            [ 'code' => 'MGWUHP', 'visible_id' => 100577 ],
            [ 'code' => 'FUWPEN', 'visible_id' => 100578 ],
            [ 'code' => 'JIKRFK', 'visible_id' => 100579 ],
            [ 'code' => 'NUJKNI', 'visible_id' => 100580 ],
            [ 'code' => 'DZNXKH', 'visible_id' => 100581 ],
            [ 'code' => 'QLHNJG', 'visible_id' => 100582 ],
            [ 'code' => 'VLXJWN', 'visible_id' => 100583 ],
            [ 'code' => 'KMDPAA', 'visible_id' => 100584 ],
            [ 'code' => 'EBRNFL', 'visible_id' => 100585 ],
            [ 'code' => 'NLWKAQ', 'visible_id' => 100586 ],
            [ 'code' => 'WZZHJU', 'visible_id' => 100587 ],
            [ 'code' => 'TRBHPS', 'visible_id' => 100588 ],
            [ 'code' => 'NNMMZG', 'visible_id' => 100589 ],
            [ 'code' => 'QXCOVW', 'visible_id' => 100590 ],
            [ 'code' => 'ACUWEX', 'visible_id' => 100591 ],
            [ 'code' => 'TEETCG', 'visible_id' => 100592 ],
            [ 'code' => 'UCWPBN', 'visible_id' => 100593 ],
            [ 'code' => 'XAPKWK', 'visible_id' => 100594 ],
            [ 'code' => 'QSTQRR', 'visible_id' => 100595 ],
            [ 'code' => 'PHPHSM', 'visible_id' => 100596 ],
            [ 'code' => 'DVMTLV', 'visible_id' => 100597 ],
            [ 'code' => 'WVYFFH', 'visible_id' => 100598 ],
            [ 'code' => 'GABKYY', 'visible_id' => 100599 ],
            [ 'code' => 'QZSPKP', 'visible_id' => 100600 ],
            [ 'code' => 'CXKMCN', 'visible_id' => 100601 ],
            [ 'code' => 'OBKULZ', 'visible_id' => 100602 ],
            [ 'code' => 'MTEABA', 'visible_id' => 100603 ],
            [ 'code' => 'BFBJCW', 'visible_id' => 100604 ],
            [ 'code' => 'DUMCLS', 'visible_id' => 100605 ],
            [ 'code' => 'ZKTHXU', 'visible_id' => 100606 ],
            [ 'code' => 'ISGHIB', 'visible_id' => 100607 ],
            [ 'code' => 'ZXFKHS', 'visible_id' => 100608 ],
            [ 'code' => 'ZLKBVN', 'visible_id' => 100609 ],
            [ 'code' => 'MODXDH', 'visible_id' => 100610 ],
            [ 'code' => 'JZIKFU', 'visible_id' => 100611 ],
            [ 'code' => 'CXINXD', 'visible_id' => 100612 ],
            [ 'code' => 'CBHXFA', 'visible_id' => 100613 ],
            [ 'code' => 'TOBPGL', 'visible_id' => 100614 ],
            [ 'code' => 'EPSVHS', 'visible_id' => 100615 ],
            [ 'code' => 'QWRIDA', 'visible_id' => 100616 ],
            [ 'code' => 'JULYVH', 'visible_id' => 100617 ],
            [ 'code' => 'HKKXAX', 'visible_id' => 100618 ],
            [ 'code' => 'NTGKUE', 'visible_id' => 100619 ],
            [ 'code' => 'UAVMPL', 'visible_id' => 100620 ],
            [ 'code' => 'GABTXP', 'visible_id' => 100621 ],
            [ 'code' => 'DLQOEU', 'visible_id' => 100622 ],
            [ 'code' => 'ZGJTBW', 'visible_id' => 100623 ],
            [ 'code' => 'QOQATR', 'visible_id' => 100624 ],
            [ 'code' => 'HNMRMM', 'visible_id' => 100625 ],
            [ 'code' => 'KETLLW', 'visible_id' => 100626 ],
            [ 'code' => 'LADNYW', 'visible_id' => 100627 ],
            [ 'code' => 'KIHKKQ', 'visible_id' => 100628 ],
            [ 'code' => 'QDJWHT', 'visible_id' => 100629 ],
            [ 'code' => 'PCQESZ', 'visible_id' => 100630 ],
            [ 'code' => 'PMNKTJ', 'visible_id' => 100631 ],
            [ 'code' => 'KBXWHW', 'visible_id' => 100632 ],
            [ 'code' => 'LTABQP', 'visible_id' => 100633 ],
            [ 'code' => 'MTLXWA', 'visible_id' => 100634 ],
            [ 'code' => 'QXEZEE', 'visible_id' => 100635 ],
            [ 'code' => 'AODGEQ', 'visible_id' => 100636 ],
            [ 'code' => 'UHHSNU', 'visible_id' => 100637 ],
            [ 'code' => 'RRTDWD', 'visible_id' => 100638 ],
            [ 'code' => 'ZALQDA', 'visible_id' => 100639 ],
            [ 'code' => 'PZGYWZ', 'visible_id' => 100640 ],
            [ 'code' => 'UBEDOR', 'visible_id' => 100641 ],
            [ 'code' => 'CTCLRJ', 'visible_id' => 100642 ],
            [ 'code' => 'BGCHFQ', 'visible_id' => 100643 ],
            [ 'code' => 'GPQYMB', 'visible_id' => 100644 ],
            [ 'code' => 'HOAYHH', 'visible_id' => 100645 ],
            [ 'code' => 'UOOHWU', 'visible_id' => 100646 ],
            [ 'code' => 'DIMUBN', 'visible_id' => 100647 ],
            [ 'code' => 'UVMPAL', 'visible_id' => 100648 ],
            [ 'code' => 'KDNFDN', 'visible_id' => 100649 ],
            [ 'code' => 'VTCUPT', 'visible_id' => 100650 ],
            [ 'code' => 'GPVNNM', 'visible_id' => 100651 ],
            [ 'code' => 'HYTBXU', 'visible_id' => 100652 ],
            [ 'code' => 'YHJMBF', 'visible_id' => 100653 ],
            [ 'code' => 'JVKJHX', 'visible_id' => 100654 ],
            [ 'code' => 'FOFRRY', 'visible_id' => 100655 ],
            [ 'code' => 'TPQIAL', 'visible_id' => 100656 ],
            [ 'code' => 'RYQZKF', 'visible_id' => 100657 ],
            [ 'code' => 'EXMVEA', 'visible_id' => 100658 ],
            [ 'code' => 'OIDMQB', 'visible_id' => 100659 ],
            [ 'code' => 'ZMAPPE', 'visible_id' => 100660 ],
            [ 'code' => 'DGUXUP', 'visible_id' => 100661 ],
            [ 'code' => 'ZHIVYW', 'visible_id' => 100662 ],
            [ 'code' => 'VZDIDJ', 'visible_id' => 100663 ],
            [ 'code' => 'TNTPQE', 'visible_id' => 100664 ],
            [ 'code' => 'TELBES', 'visible_id' => 100665 ],
            [ 'code' => 'GXAWJX', 'visible_id' => 100666 ],
            [ 'code' => 'ODDNBO', 'visible_id' => 100667 ],
            [ 'code' => 'PIQIDC', 'visible_id' => 100668 ],
            [ 'code' => 'FCQESI', 'visible_id' => 100669 ],
            [ 'code' => 'CEGZQT', 'visible_id' => 100670 ],
            [ 'code' => 'RJCKNQ', 'visible_id' => 100671 ],
            [ 'code' => 'DFHDYF', 'visible_id' => 100672 ],
            [ 'code' => 'ETGYDU', 'visible_id' => 100673 ],
            [ 'code' => 'JGRSWT', 'visible_id' => 100674 ],
            [ 'code' => 'TQGAEA', 'visible_id' => 100675 ],
            [ 'code' => 'LFXWLS', 'visible_id' => 100676 ],
            [ 'code' => 'JFMMOD', 'visible_id' => 100677 ],
            [ 'code' => 'WPNQIC', 'visible_id' => 100678 ],
            [ 'code' => 'KYRMZM', 'visible_id' => 100679 ],
            [ 'code' => 'IZOKRF', 'visible_id' => 100680 ],
            [ 'code' => 'USOTAG', 'visible_id' => 100681 ],
            [ 'code' => 'ZJAKZD', 'visible_id' => 100682 ],
            [ 'code' => 'RLFUUO', 'visible_id' => 100683 ],
            [ 'code' => 'TRVKKC', 'visible_id' => 100684 ],
            [ 'code' => 'PRVHPZ', 'visible_id' => 100685 ],
            [ 'code' => 'CBLMLM', 'visible_id' => 100686 ],
            [ 'code' => 'QJAYDA', 'visible_id' => 100687 ],
            [ 'code' => 'WLQJPP', 'visible_id' => 100688 ],
            [ 'code' => 'WISTZH', 'visible_id' => 100689 ],
            [ 'code' => 'VLGXKU', 'visible_id' => 100690 ],
            [ 'code' => 'XCQGIW', 'visible_id' => 100691 ],
            [ 'code' => 'FBGRAG', 'visible_id' => 100692 ],
            [ 'code' => 'BIDZOL', 'visible_id' => 100693 ],
            [ 'code' => 'RFNGPU', 'visible_id' => 100694 ],
            [ 'code' => 'JAYPUF', 'visible_id' => 100695 ],
            [ 'code' => 'UGFLCB', 'visible_id' => 100696 ],
            [ 'code' => 'NPZSLD', 'visible_id' => 100697 ],
            [ 'code' => 'NHFGXU', 'visible_id' => 100698 ],
            [ 'code' => 'URJCXW', 'visible_id' => 100699 ],
            [ 'code' => 'FFSXQK', 'visible_id' => 100700 ],
            [ 'code' => 'ONYEJS', 'visible_id' => 100701 ],
            [ 'code' => 'ZUNUJM', 'visible_id' => 100702 ],
            [ 'code' => 'QPWKUT', 'visible_id' => 100703 ],
            [ 'code' => 'JJCUHO', 'visible_id' => 100704 ],
            [ 'code' => 'MOBTAE', 'visible_id' => 100705 ],
            [ 'code' => 'BLQRGD', 'visible_id' => 100706 ],
            [ 'code' => 'WLTACW', 'visible_id' => 100707 ],
            [ 'code' => 'SWBHSO', 'visible_id' => 100708 ],
            [ 'code' => 'JRHWEN', 'visible_id' => 100709 ],
            [ 'code' => 'XYZMPE', 'visible_id' => 100710 ],
            [ 'code' => 'CWQHFH', 'visible_id' => 100711 ],
            [ 'code' => 'UEPUEF', 'visible_id' => 100712 ],
            [ 'code' => 'ULIFVW', 'visible_id' => 100713 ],
            [ 'code' => 'XOZKMH', 'visible_id' => 100714 ],
            [ 'code' => 'ETGUOR', 'visible_id' => 100715 ],
            [ 'code' => 'HBZMHF', 'visible_id' => 100716 ],
            [ 'code' => 'FGLOTN', 'visible_id' => 100717 ],
            [ 'code' => 'UUVZNN', 'visible_id' => 100718 ],
            [ 'code' => 'NUEPSR', 'visible_id' => 100719 ],
            [ 'code' => 'MEIQYE', 'visible_id' => 100720 ],
            [ 'code' => 'FRXGSI', 'visible_id' => 100721 ],
            [ 'code' => 'CFCEPG', 'visible_id' => 100722 ],
            [ 'code' => 'EDMTEB', 'visible_id' => 100723 ],
            [ 'code' => 'FTCQLJ', 'visible_id' => 100724 ],
            [ 'code' => 'QFYLCT', 'visible_id' => 100725 ],
            [ 'code' => 'SFPZQE', 'visible_id' => 100726 ],
            [ 'code' => 'VSKGGQ', 'visible_id' => 100727 ],
            [ 'code' => 'IWHQVR', 'visible_id' => 100728 ],
            [ 'code' => 'LEQYGZ', 'visible_id' => 100729 ],
            [ 'code' => 'SLIOAU', 'visible_id' => 100730 ],
            [ 'code' => 'IWAFFQ', 'visible_id' => 100731 ],
            [ 'code' => 'FXWIGV', 'visible_id' => 100732 ],
            [ 'code' => 'LEMJHL', 'visible_id' => 100733 ],
            [ 'code' => 'ERIRJF', 'visible_id' => 100734 ],
            [ 'code' => 'UYPRZN', 'visible_id' => 100735 ],
            [ 'code' => 'KREBYN', 'visible_id' => 100736 ],
            [ 'code' => 'VDLKON', 'visible_id' => 100737 ],
            [ 'code' => 'NIXBUR', 'visible_id' => 100738 ],
            [ 'code' => 'ZQDKAR', 'visible_id' => 100739 ],
            [ 'code' => 'ZPEBTU', 'visible_id' => 100740 ],
            [ 'code' => 'MKTJOD', 'visible_id' => 100741 ],
            [ 'code' => 'UTKVLT', 'visible_id' => 100742 ],
            [ 'code' => 'XSVRLP', 'visible_id' => 100743 ],
            [ 'code' => 'WLTZNQ', 'visible_id' => 100744 ],
            [ 'code' => 'HQSOPW', 'visible_id' => 100745 ],
            [ 'code' => 'CAPTUT', 'visible_id' => 100746 ],
            [ 'code' => 'CIGNPL', 'visible_id' => 100747 ],
            [ 'code' => 'IMBUFL', 'visible_id' => 100748 ],
            [ 'code' => 'WUTDRA', 'visible_id' => 100749 ],
            [ 'code' => 'LMWUZS', 'visible_id' => 100750 ],
            [ 'code' => 'KYTPWJ', 'visible_id' => 100751 ],
            [ 'code' => 'ZXUTKA', 'visible_id' => 100752 ],
            [ 'code' => 'OZLQNY', 'visible_id' => 100753 ],
            [ 'code' => 'RQLEDO', 'visible_id' => 100754 ],
            [ 'code' => 'NJSZXS', 'visible_id' => 100755 ],
            [ 'code' => 'IQHVVD', 'visible_id' => 100756 ],
            [ 'code' => 'YXBEMH', 'visible_id' => 100757 ],
            [ 'code' => 'ODUDJV', 'visible_id' => 100758 ],
            [ 'code' => 'STPWZZ', 'visible_id' => 100759 ],
            [ 'code' => 'PODENX', 'visible_id' => 100760 ],
            [ 'code' => 'JAAKPC', 'visible_id' => 100761 ],
            [ 'code' => 'ZYYBCR', 'visible_id' => 100762 ],
            [ 'code' => 'PYDEVJ', 'visible_id' => 100763 ],
            [ 'code' => 'ZGZYMO', 'visible_id' => 100764 ],
            [ 'code' => 'CRTQTC', 'visible_id' => 100765 ],
            [ 'code' => 'RJDBZE', 'visible_id' => 100766 ],
            [ 'code' => 'ZAKTTJ', 'visible_id' => 100767 ],
            [ 'code' => 'RUVODC', 'visible_id' => 100768 ],
            [ 'code' => 'JQPGYA', 'visible_id' => 100769 ],
            [ 'code' => 'QQHABF', 'visible_id' => 100770 ],
            [ 'code' => 'TYRBBP', 'visible_id' => 100771 ],
            [ 'code' => 'UDOUSI', 'visible_id' => 100772 ],
            [ 'code' => 'JYABJQ', 'visible_id' => 100773 ],
            [ 'code' => 'GIOPLO', 'visible_id' => 100774 ],
            [ 'code' => 'XAOBSE', 'visible_id' => 100775 ],
            [ 'code' => 'GATLVR', 'visible_id' => 100776 ],
            [ 'code' => 'WTUZYC', 'visible_id' => 100777 ],
            [ 'code' => 'FVDXPL', 'visible_id' => 100778 ],
            [ 'code' => 'NQQMNY', 'visible_id' => 100779 ],
            [ 'code' => 'VXTHGR', 'visible_id' => 100780 ],
            [ 'code' => 'RRUPPG', 'visible_id' => 100781 ],
            [ 'code' => 'UGXOVM', 'visible_id' => 100782 ],
            [ 'code' => 'YQNPHG', 'visible_id' => 100783 ],
            [ 'code' => 'QPJCYW', 'visible_id' => 100784 ],
            [ 'code' => 'BIJDRS', 'visible_id' => 100785 ],
            [ 'code' => 'JJHOHV', 'visible_id' => 100786 ],
            [ 'code' => 'UAKJTW', 'visible_id' => 100787 ],
            [ 'code' => 'CYKWHO', 'visible_id' => 100788 ],
            [ 'code' => 'VARIQG', 'visible_id' => 100789 ],
            [ 'code' => 'AIFEMC', 'visible_id' => 100790 ],
            [ 'code' => 'MNWQKD', 'visible_id' => 100791 ],
            [ 'code' => 'NIWQKC', 'visible_id' => 100792 ],
            [ 'code' => 'JVIONX', 'visible_id' => 100793 ],
            [ 'code' => 'KCWZDD', 'visible_id' => 100794 ],
            [ 'code' => 'FDNHVL', 'visible_id' => 100795 ],
            [ 'code' => 'VCLFQV', 'visible_id' => 100796 ],
            [ 'code' => 'HWDBPL', 'visible_id' => 100797 ],
            [ 'code' => 'QMZMSS', 'visible_id' => 100798 ],
            [ 'code' => 'PTMIMZ', 'visible_id' => 100799 ],
            [ 'code' => 'XTFHMO', 'visible_id' => 100800 ],
            [ 'code' => 'FUFEFJ', 'visible_id' => 100801 ],
            [ 'code' => 'USRNWA', 'visible_id' => 100802 ],
            [ 'code' => 'XTBLJN', 'visible_id' => 100803 ],
            [ 'code' => 'JCPVLW', 'visible_id' => 100804 ],
            [ 'code' => 'XPZANK', 'visible_id' => 100805 ],
            [ 'code' => 'ZVSQCE', 'visible_id' => 100806 ],
            [ 'code' => 'UQCAOS', 'visible_id' => 100807 ],
            [ 'code' => 'UIXTET', 'visible_id' => 100808 ],
            [ 'code' => 'UQUXEO', 'visible_id' => 100809 ],
            [ 'code' => 'HJSOZO', 'visible_id' => 100810 ],
            [ 'code' => 'OLJOSQ', 'visible_id' => 100811 ],
            [ 'code' => 'GXWPUG', 'visible_id' => 100812 ],
            [ 'code' => 'VYCEQQ', 'visible_id' => 100813 ],
            [ 'code' => 'EDKQSW', 'visible_id' => 100814 ],
            [ 'code' => 'DQAGUI', 'visible_id' => 100815 ],
            [ 'code' => 'LFTAMI', 'visible_id' => 100816 ],
            [ 'code' => 'KVRAUE', 'visible_id' => 100817 ],
            [ 'code' => 'XECEIJ', 'visible_id' => 100818 ],
            [ 'code' => 'NNUHNV', 'visible_id' => 100819 ],
            [ 'code' => 'FKMRCU', 'visible_id' => 100820 ],
            [ 'code' => 'YMDMFD', 'visible_id' => 100821 ],
            [ 'code' => 'JQWDXR', 'visible_id' => 100822 ],
            [ 'code' => 'RWFFWM', 'visible_id' => 100823 ],
            [ 'code' => 'PVQJFJ', 'visible_id' => 100824 ],
            [ 'code' => 'QGNQFU', 'visible_id' => 100825 ],
            [ 'code' => 'QEFHXS', 'visible_id' => 100826 ],
            [ 'code' => 'IPVWSF', 'visible_id' => 100827 ],
            [ 'code' => 'ZLBOZX', 'visible_id' => 100828 ],
            [ 'code' => 'KWBSCH', 'visible_id' => 100829 ],
            [ 'code' => 'KZNWAJ', 'visible_id' => 100830 ],
            [ 'code' => 'XKKOFK', 'visible_id' => 100831 ],
            [ 'code' => 'GDFQUR', 'visible_id' => 100832 ],
            [ 'code' => 'HWINJT', 'visible_id' => 100833 ],
            [ 'code' => 'XLWXSB', 'visible_id' => 100834 ],
            [ 'code' => 'KHRVEX', 'visible_id' => 100835 ],
            [ 'code' => 'TZYKJX', 'visible_id' => 100836 ],
            [ 'code' => 'FQJBCU', 'visible_id' => 100837 ],
            [ 'code' => 'ONHVQE', 'visible_id' => 100838 ],
            [ 'code' => 'QITHYB', 'visible_id' => 100839 ],
            [ 'code' => 'KACPRJ', 'visible_id' => 100840 ],
            [ 'code' => 'ZJABEN', 'visible_id' => 100841 ],
            [ 'code' => 'NNBSEP', 'visible_id' => 100842 ],
            [ 'code' => 'CCQDJL', 'visible_id' => 100843 ],
            [ 'code' => 'LNAUSB', 'visible_id' => 100844 ],
            [ 'code' => 'OJTRAC', 'visible_id' => 100845 ],
            [ 'code' => 'QVABWR', 'visible_id' => 100846 ],
            [ 'code' => 'GOMWJK', 'visible_id' => 100847 ],
            [ 'code' => 'JPVTOA', 'visible_id' => 100848 ],
            [ 'code' => 'QPEMWV', 'visible_id' => 100849 ],
            [ 'code' => 'DYVVPM', 'visible_id' => 100850 ],
            [ 'code' => 'TNBNQD', 'visible_id' => 100851 ],
            [ 'code' => 'TYLMQJ', 'visible_id' => 100852 ],
            [ 'code' => 'VQFPPT', 'visible_id' => 100853 ],
            [ 'code' => 'GMPMBS', 'visible_id' => 100854 ],
            [ 'code' => 'UIXZQZ', 'visible_id' => 100855 ],
            [ 'code' => 'XIUHEL', 'visible_id' => 100856 ],
            [ 'code' => 'QQDOAQ', 'visible_id' => 100857 ],
            [ 'code' => 'GJFXVR', 'visible_id' => 100858 ],
            [ 'code' => 'KOQCDV', 'visible_id' => 100859 ],
            [ 'code' => 'TGAMTZ', 'visible_id' => 100860 ],
            [ 'code' => 'HTBWBM', 'visible_id' => 100861 ],
            [ 'code' => 'LDCGQX', 'visible_id' => 100862 ],
            [ 'code' => 'VQUVQY', 'visible_id' => 100863 ],
            [ 'code' => 'CZQMFL', 'visible_id' => 100864 ],
            [ 'code' => 'NPJEFU', 'visible_id' => 100865 ],
            [ 'code' => 'JMRIJU', 'visible_id' => 100866 ],
            [ 'code' => 'GLMBTY', 'visible_id' => 100867 ],
            [ 'code' => 'QZJMPP', 'visible_id' => 100868 ],
            [ 'code' => 'WIOAMY', 'visible_id' => 100869 ],
            [ 'code' => 'LRIAGD', 'visible_id' => 100870 ],
            [ 'code' => 'CBRLEB', 'visible_id' => 100871 ],
            [ 'code' => 'BWTYNE', 'visible_id' => 100872 ],
            [ 'code' => 'CKAKJK', 'visible_id' => 100873 ],
            [ 'code' => 'OLUXOM', 'visible_id' => 100874 ],
            [ 'code' => 'VKZZNR', 'visible_id' => 100875 ],
            [ 'code' => 'TMSNGL', 'visible_id' => 100876 ],
            [ 'code' => 'EIKNVG', 'visible_id' => 100877 ],
            [ 'code' => 'VTLRBJ', 'visible_id' => 100878 ],
            [ 'code' => 'RVURGA', 'visible_id' => 100879 ],
            [ 'code' => 'CQXLOF', 'visible_id' => 100880 ],
            [ 'code' => 'IWQVIF', 'visible_id' => 100881 ],
            [ 'code' => 'VSXOFO', 'visible_id' => 100882 ],
            [ 'code' => 'EXRXZJ', 'visible_id' => 100883 ],
            [ 'code' => 'UWUTMY', 'visible_id' => 100884 ],
            [ 'code' => 'RIUAWK', 'visible_id' => 100885 ],
            [ 'code' => 'RSDEOW', 'visible_id' => 100886 ],
            [ 'code' => 'IBWTZD', 'visible_id' => 100887 ],
            [ 'code' => 'CZPACQ', 'visible_id' => 100888 ],
            [ 'code' => 'XEJCJF', 'visible_id' => 100889 ],
            [ 'code' => 'ORKNBE', 'visible_id' => 100890 ],
            [ 'code' => 'FFFCEN', 'visible_id' => 100891 ],
            [ 'code' => 'GQBMXI', 'visible_id' => 100892 ],
            [ 'code' => 'KYPYTC', 'visible_id' => 100893 ],
            [ 'code' => 'HKOYFG', 'visible_id' => 100894 ],
            [ 'code' => 'DLQXGF', 'visible_id' => 100895 ],
            [ 'code' => 'HQABBK', 'visible_id' => 100896 ],
            [ 'code' => 'VKEMJP', 'visible_id' => 100897 ],
            [ 'code' => 'EZYJEH', 'visible_id' => 100898 ],
            [ 'code' => 'OVYVLN', 'visible_id' => 100899 ],
            [ 'code' => 'KEWVXA', 'visible_id' => 100900 ],
            [ 'code' => 'RSUNIW', 'visible_id' => 100901 ],
            [ 'code' => 'ZNZAVL', 'visible_id' => 100902 ],
            [ 'code' => 'GXDJBO', 'visible_id' => 100903 ],
            [ 'code' => 'VXTVVZ', 'visible_id' => 100904 ],
            [ 'code' => 'KKLEXZ', 'visible_id' => 100905 ],
            [ 'code' => 'CVDVWI', 'visible_id' => 100906 ],
            [ 'code' => 'MPIJYZ', 'visible_id' => 100907 ],
            [ 'code' => 'ZBMYAH', 'visible_id' => 100908 ],
            [ 'code' => 'AWDXHH', 'visible_id' => 100909 ],
            [ 'code' => 'AFOZRD', 'visible_id' => 100910 ],
            [ 'code' => 'DSPSUO', 'visible_id' => 100911 ],
            [ 'code' => 'FCOGRI', 'visible_id' => 100912 ],
            [ 'code' => 'JWUIVU', 'visible_id' => 100913 ],
            [ 'code' => 'VQHLJC', 'visible_id' => 100914 ],
            [ 'code' => 'DUZNBI', 'visible_id' => 100915 ],
            [ 'code' => 'TUSGIN', 'visible_id' => 100916 ],
            [ 'code' => 'PQDLYO', 'visible_id' => 100917 ],
            [ 'code' => 'BSBQKE', 'visible_id' => 100918 ],
            [ 'code' => 'TUHZZY', 'visible_id' => 100919 ],
            [ 'code' => 'LLKWIY', 'visible_id' => 100920 ],
            [ 'code' => 'FVCSUY', 'visible_id' => 100921 ],
            [ 'code' => 'PAPSOF', 'visible_id' => 100922 ],
            [ 'code' => 'ZQUADG', 'visible_id' => 100923 ],
            [ 'code' => 'STEFMG', 'visible_id' => 100924 ],
            [ 'code' => 'UZFMYG', 'visible_id' => 100925 ],
            [ 'code' => 'TMLLPQ', 'visible_id' => 100926 ],
            [ 'code' => 'FEZTCA', 'visible_id' => 100927 ],
            [ 'code' => 'XAQABJ', 'visible_id' => 100928 ],
            [ 'code' => 'YDZKLT', 'visible_id' => 100929 ],
            [ 'code' => 'VVMIYF', 'visible_id' => 100930 ],
            [ 'code' => 'PUFJQB', 'visible_id' => 100931 ],
            [ 'code' => 'EMQZSU', 'visible_id' => 100932 ],
            [ 'code' => 'RWCDML', 'visible_id' => 100933 ],
            [ 'code' => 'KIZQFP', 'visible_id' => 100934 ],
            [ 'code' => 'TAGWWN', 'visible_id' => 100935 ],
            [ 'code' => 'XJRNWQ', 'visible_id' => 100936 ],
            [ 'code' => 'YIBWOD', 'visible_id' => 100937 ],
            [ 'code' => 'HODQXQ', 'visible_id' => 100938 ],
            [ 'code' => 'MMWAIP', 'visible_id' => 100939 ],
            [ 'code' => 'KAUVBB', 'visible_id' => 100940 ],
            [ 'code' => 'MSRMMB', 'visible_id' => 100941 ],
            [ 'code' => 'EDGHSW', 'visible_id' => 100942 ],
            [ 'code' => 'PKHTOD', 'visible_id' => 100943 ],
            [ 'code' => 'DSRDTK', 'visible_id' => 100944 ],
            [ 'code' => 'EWCFMA', 'visible_id' => 100945 ],
            [ 'code' => 'BAKQFR', 'visible_id' => 100946 ],
            [ 'code' => 'OOORXY', 'visible_id' => 100947 ],
            [ 'code' => 'FQCCAJ', 'visible_id' => 100948 ],
            [ 'code' => 'NHUAKE', 'visible_id' => 100949 ],
            [ 'code' => 'HKWUXE', 'visible_id' => 100950 ],
            [ 'code' => 'NUASSZ', 'visible_id' => 100951 ],
            [ 'code' => 'UERSQY', 'visible_id' => 100952 ],
            [ 'code' => 'NIWQXA', 'visible_id' => 100953 ],
            [ 'code' => 'FFVYWM', 'visible_id' => 100954 ],
            [ 'code' => 'HYKWSB', 'visible_id' => 100955 ],
            [ 'code' => 'FWXGIZ', 'visible_id' => 100956 ],
            [ 'code' => 'YLKDXD', 'visible_id' => 100957 ],
            [ 'code' => 'TKXMQH', 'visible_id' => 100958 ],
            [ 'code' => 'HNFVYP', 'visible_id' => 100959 ],
            [ 'code' => 'JMTMDO', 'visible_id' => 100960 ],
            [ 'code' => 'ODKHUJ', 'visible_id' => 100961 ],
            [ 'code' => 'INCAWT', 'visible_id' => 100962 ],
            [ 'code' => 'SKYRTU', 'visible_id' => 100963 ],
            [ 'code' => 'TQZHAN', 'visible_id' => 100964 ],
            [ 'code' => 'JSFFBK', 'visible_id' => 100965 ],
            [ 'code' => 'YLJQAW', 'visible_id' => 100966 ],
            [ 'code' => 'QQCXGX', 'visible_id' => 100967 ],
            [ 'code' => 'NQPKUG', 'visible_id' => 100968 ],
            [ 'code' => 'ILXGQB', 'visible_id' => 100969 ],
            [ 'code' => 'LBPVTG', 'visible_id' => 100970 ],
            [ 'code' => 'GFEAAB', 'visible_id' => 100971 ],
            [ 'code' => 'TUYJVO', 'visible_id' => 100972 ],
            [ 'code' => 'BGEQPW', 'visible_id' => 100973 ],
            [ 'code' => 'YIQNTC', 'visible_id' => 100974 ],
            [ 'code' => 'GWXMIT', 'visible_id' => 100975 ],
            [ 'code' => 'CELZOP', 'visible_id' => 100976 ],
            [ 'code' => 'NDDUVI', 'visible_id' => 100977 ],
            [ 'code' => 'KGEMWV', 'visible_id' => 100978 ],
            [ 'code' => 'ZSBVCK', 'visible_id' => 100979 ],
            [ 'code' => 'ARMQOY', 'visible_id' => 100980 ],
            [ 'code' => 'CGMFQN', 'visible_id' => 100981 ],
            [ 'code' => 'PEVBVU', 'visible_id' => 100982 ],
            [ 'code' => 'DBFMFK', 'visible_id' => 100983 ],
            [ 'code' => 'NIWGLY', 'visible_id' => 100984 ],
            [ 'code' => 'ZETWPI', 'visible_id' => 100985 ],
            [ 'code' => 'BXJNGQ', 'visible_id' => 100986 ],
            [ 'code' => 'AHWQUV', 'visible_id' => 100987 ],
            [ 'code' => 'WGGGEW', 'visible_id' => 100988 ],
            [ 'code' => 'ZHUTKC', 'visible_id' => 100989 ],
            [ 'code' => 'HKTBJV', 'visible_id' => 100990 ],
            [ 'code' => 'HOXVGJ', 'visible_id' => 100991 ],
            [ 'code' => 'OUFYLR', 'visible_id' => 100992 ],
            [ 'code' => 'JLNBTY', 'visible_id' => 100993 ],
            [ 'code' => 'EEPARR', 'visible_id' => 100994 ],
            [ 'code' => 'LMNNHN', 'visible_id' => 100995 ],
            [ 'code' => 'TEKXOJ', 'visible_id' => 100996 ],
            [ 'code' => 'KCSEKG', 'visible_id' => 100997 ],
            [ 'code' => 'PBLUSR', 'visible_id' => 100998 ],
            [ 'code' => 'ODTLHS', 'visible_id' => 100999 ],
        ];
    }
    
    /**
     * @param string $coupon_code
     * @param int    $visible_id
     *
     * @return int|WP_Error
     */
    public static function create( $coupon_code = '', $visible_id = 0 ) {
        if( empty( $coupon_code ) ) $coupon_code = MC2_Vars::generate( 8, true );
        for( $x = 0; $x <= 100; $x++ ) {
            $exists = get_page_by_title( $coupon_code, OBJECT, 'shop_coupon' );
            if( !empty( $exists ) ) break;
            $coupon_code = MC2_Vars::generate( 8, true );
        }
        $args      = [
            'post_title'   => $coupon_code,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_author'  => 1,
            'post_type'    => 'shop_coupon',
        ];
        $coupon_id = wp_insert_post( $args );
        if( !is_numeric( $coupon_id ) ) return 0;
        wp_set_object_terms( $coupon_id, 'Gift Card', 'shop_coupon_cat' );
        update_post_meta( $coupon_id, 'discount_type', 'fixed_cart' );
        update_post_meta( $coupon_id, 'coupon_amount', 0 );
        update_post_meta( $coupon_id, 'individual_use', 'no' );
        update_post_meta( $coupon_id, 'product_ids', '' );
        update_post_meta( $coupon_id, 'exclude_product_ids', '' );
        update_post_meta( $coupon_id, 'usage_limit', '0' );
        update_post_meta( $coupon_id, 'usage_limit_per_user', '0' );
        update_post_meta( $coupon_id, 'expiry_date', '' );
        update_post_meta( $coupon_id, 'apply_before_tax', 'yes' );
        update_post_meta( $coupon_id, 'free_shipping', 'no' );
        update_post_meta( $coupon_id, 'mc_gift_card', 1 );
        if( !empty( $visible_id ) ) update_post_meta( $coupon_id, 'mc_visible_id', $visible_id );
        
        return $coupon_id;
    }
    
    public static function addToCart( $product_id, $quantity = 1 ) {
        if( empty( $product_id ) ) return '';
        $itemKey = WC()->cart->add_to_cart( $product_id, $quantity );
        
        return $itemKey;
    }
    
    /**
     * @param       $order
     * @param false $force_physical
     */
    public static function assign_gift_cards_to_order( $order, $force_physical = false ) {
        if( is_numeric( $order ) ) $order = wc_get_order( $order );
        $order_id         = $order->get_id();
        $order_items      = $order->get_items();
        $assigned_coupons = self::getGift_CardFromOrder( $order_id );
        if( !empty( $assigned_coupons ) ) return;
        $assigned_giftcards = [];
        $giftcard_quantity  = 0;
        $items_in_order     = 0;
        $digital_quantity   = 0;
        foreach( $order_items as $order_item ) {
            $product_id     = $order_item->get_product_id();
            $quantity       = $order_item->get_quantity();
            $items_in_order = $items_in_order + $quantity;
            if( !MC2_Product_Functions::isGiftCard( $product_id ) ) continue;
            $giftcard_quantity = $giftcard_quantity + $quantity;
            $digital           = self::digital( $product_id );
            if( empty( $digital ) && !$force_physical ) continue;
            $digital_quantity = $digital_quantity + $quantity;
            $product          = wc_get_product( $product_id );
            $sleeves          = get_post_meta( $product_id, 'mc_sleeves', true );
            for( $x = 1; $x <= $quantity; $x++ ) {
                $giftcard_id   = self::create();
                $product_price = $product->get_price();
                update_post_meta( $giftcard_id, 'coupon_amount', $product_price );
                update_post_meta( $giftcard_id, 'mc_linked_order', $order_id );
                update_post_meta( $giftcard_id, 'mc_sleeves', $sleeves );
                self::createCertificate( $order_id, $giftcard_id );
            }
        }
        if( !empty( $assigned_giftcards ) ) update_post_meta( $order_id, 'MC2_Gift_Cards', $assigned_giftcards );
        if( $items_in_order == $giftcard_quantity && $giftcard_quantity == $digital_quantity ) MC2_Order_Functions::complete( $order_id );
    }
    
    public static function getGift_CardFromOrder( $order_id ) {
        $giftcards = get_post_meta( $order_id, 'MC2_Gift_Cards', true );
        if( !empty( $giftcards ) && is_array( $giftcards ) ) return $giftcards;
        
        return [];
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public static function digital( $id ) {
        return !empty( get_post_meta( $id, 'mc_digital', true ) );
    }
    
    /**
     * @param $order_id
     * @param $giftcard_id
     *
     * @return string
     */
    public static function createCertificate( $order_id, $giftcard_id ) {
        $dompdf = new Dompdf();
        
        ob_start();
        include DIR_THEME_TEMPLATE_PARTS.'/store/giftcard/certificate.php';
        $output = ob_get_clean();
        
        $dompdf->loadHtml( $output );
        $dompdf->setPaper( 'A4', 'portrait' );
        $dompdf->render();
        $output_pdf = $dompdf->output();
        
        $path = ABSPATH.'/files/giftcards/'.$order_id;
        if( !is_dir( $path ) ) mkdir( $path, 0755, true );
        $path = $path.'/'.$giftcard_id.'.pdf';
        file_put_contents( $path, $output_pdf );
        
        return $path;
    }
    
    /**
     * @param $order
     */
    public static function update_gift_card_balance( $order ) {
        $coupons = $order->get_coupon_codes();
        if( empty( $coupons ) ) return;
        $order_items = $order->get_items();
        $alters      = 0;
        foreach( $order_items as $order_item ) {
            $product_id = $order_item['product_id'];
            if( !MC2_Product_Functions::isAlter( $product_id ) ) continue;
            $quantity = $order_item['quantity'] ?? 0;
            $alters   = $alters + $quantity;
        }
        $alters_paid = 0;
        foreach( $coupons as $coupon_name ) {
            $coupon = get_page_by_title( $coupon_name, OBJECT, 'shop_coupon' );
            if( empty( $coupon ) ) continue;
            $coupon_id = $coupon->ID;
            $giftcard  = self::isGiftCard( $coupon_id );
            if( empty( $giftcard ) ) continue;
            for( $x = 1; $x <= $alters; $x++ ) {
                $current_value = get_post_meta( $coupon_id, 'coupon_amount', true );
                if( empty( $current_value ) ) break;
                $current_value = $current_value - 6;
                if( $current_value < 0 ) $current_value = 0;
                update_post_meta( $coupon_id, 'coupon_amount', $current_value );
                $alters_paid++;
                if( $alters_paid == $alters ) break 2;
            }
        }
    }
    
    /**
     * @param int $coupon_id
     *
     * @return bool
     */
    public static function isGiftCard( $coupon_id = 0 ) {
        return has_term( 'Gift Card', 'shop_coupon_cat', $coupon_id );
    }
    
    /**
     * @param $order_id
     * @param $coupon_id
     *
     * @return string
     */
    public static function getCertificatePath( $order_id, $coupon_id ) {
        $path = ABSPATH.'/files/giftcards/'.$order_id.'/'.$coupon_id.'.pdf';
        if( !file_exists( $path ) ) self::createCertificate( $order_id, $coupon_id );
        
        return $path;
    }
    
    public static function attachGift_CardToEmail( $attachments, $email_id, $order, $email ) {
        $email_ids = [ 'customer_processing_order', 'customer_completed_order' ];
        $order_id  = $order->get_id();
        if( !file_exists( ABSPATH.'/files/giftcards/'.$order_id ) ) return $attachments;
        if( in_array( $email_id, $email_ids ) ) {
            $certificate_path      = ABSPATH.'/files/giftcards/'.$order_id;
            $giftcard_certificates = scandir( $certificate_path );
            foreach( $giftcard_certificates as $giftcard_certificate ) {
                if( $giftcard_certificate == '.' || $giftcard_certificate == '..' ) continue;
                $attachments[] = $certificate_path.'/'.$giftcard_certificate;
            }
        }
        
        return $attachments;
    }
    
}