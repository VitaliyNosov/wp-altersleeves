<?php

namespace Mythic_Core\Objects\Store;

use Exception;
use MC_Vars;
use Mythic_Core\Users\MC_Affiliates;
use WC_Coupon;

/**
 * Class MC_Affiliate_Coupon_De_Hardcode
 *
 * @package Mythic_Core\Objects\Store
 */
class MC_Affiliate_Coupon_De_Hardcode {
    
    // TODO: check coupons without discounts: cardboardcommand, casuallycompetitive, playingwithpower
    
    private static $affiliates_data = [
        5184 => [
            'coupon' => 'affinityartifacts',
            'url'    => '/content-creator/affinityartifacts',
            'fee'    => 250,
        ],
        7207 => [
            'coupon' => 'affinityforcommander',
            'url'    => '/content-creator/affinityforcommander',
            'fee'    => 0,
        ],
        7172 => [
            'coupon' => 'commandersquarters',
            'url'    => '/content-creator/commandersquarters',
            'fee'    => 0,
        ],
        7090 => [
            'coupon' => 'cardboardcommand',
            'url'    => '/content-creator/cardboardcommand',
            'fee'    => 0,
        ],
        3117 => [
            'coupon' => 'casuallycompetitive',
            'url'    => '/content-creator/casuallycompetitive',
            'fee'    => 250,
        ],
        6975 => [
            'coupon' => 'edhrecast',
            'url'    => '/content-creator/edhrecast',
            'fee'    => 0,
        ],
        6608 => [
            'coupon' => 'garbageandy',
            'url'    => '/content-creator/garbageandy',
            'fee'    => 0,
        ],
        6603 => [
            'coupon' => 'ihateyourdeck',
            'url'    => '/content-creator/ihateyourdeck',
            'fee'    => 0,
        ],
        6541 => [
            'coupon' => 'magicmics',
            'url'    => '/content-creator/magicmics',
            'fee'    => 250,
        ],
        3137 => [
            'coupon' => 'manacurves',
            'url'    => '/content-creator/manacurves',
            'fee'    => 0,
        ],
        3552 => [
            'coupon' => 'mentalmisplay',
            'url'    => '/content-creator/mentalmisplay',
            'fee'    => 0,
        ],
        3128 => [
            'coupon' => 'mtgmuddstah',
            'url'    => '/content-creator/mtg-muddstah',
            'fee'    => 250,
        ],
        2947 => [
            'coupon' => 'pleasantkenobi',
            'url'    => '/content-creator/pleasantkenobi',
            'fee'    => 280,
        ],
        6447 => [
            'coupon' => 'playtowin',
            'url'    => '/content-creator/playtowin',
            'fee'    => 0,
        ],
        3071 => [
            'coupon' => 'playingwithpower',
            'url'    => '/content-creator/PlayingWithPower',
            'fee'    => 0,
        ],
        6604 => [
            'coupon' => 'taaliavess',
            'url'    => '/content-creator/taaliavess',
            'fee'    => 0,
        ],
        7434 => [
            'coupon' => 'drinksofalara',
            'url'    => '/content-creator/drinksofalara',
            'fee'    => 0,
        ],
        3130 => [
            'coupon' => 'mtglexicon',
            'url'    => '/content-creator/mtglexicon',
            'fee'    => 0,
        ],
        99   => [
            'coupon' => '',
            'url'    => '',
            'fee'    => 250,
        ],
    ];
    
    /**
     * Run all de-hardcode functions
     */
    public static function runGlobalDeHardcode() {
        self::addAffiliateUserMetaCoupon();
        self::addAffiliateUserMetaCurrency();
        self::changeOldCouponsToPromotion();
    }
    
    /**
     * @return array
     */
    public static function addAffiliateUserMetaCoupon() {
        $result = [ 'status' => 0, 'message' => '' ];
        
        foreach( static::$affiliates_data as $single_key => $single ) {
            if( !empty( $single['coupon'] ) ) {
                if( empty( wc_get_coupon_id_by_code( $single['coupon'] ) ) ) {
                    $wc_register_result = MC_Coupon::createCoupon( $single['coupon'] );
                    
                    if( empty( $wc_register_result['status'] ) ) {
                        $result['message'] = 'An error has occurred while creating new wc coupon';
                        
                        return $result;
                    }
                }
                
                update_user_meta( $single_key, '_mc_affiliate_coupon', $single['coupon'] );
            }
            
            if( !empty( $single['fee'] ) ) {
                update_user_meta( $single_key, '_mc_affiliate_monthly_fee', $single['fee'] );
            }
            
            if( !empty( $single['url'] ) ) {
                update_user_meta( $single_key, '_mc_affiliate_url', $single['url'] );
            }
        }
        
        return [ 'status' => 1 ];
    }
    
    /**
     * @return bool|int
     */
    public static function addAffiliateUserMetaCurrency() {
        return update_user_meta( 2947, '_mc_affiliate_monthly_fee_currency', 'GBP' );
    }
    
    /**
     * Save old coupons as promotion
     *
     * @throws Exception
     */
    public static function changeOldCouponsToPromotion() {
        static::saveOldCouponsAsPromotion( static::micCoupons(), 'magicmics' );
        static::saveOldCouponsAsPromotion( static::taaliaCoupons(), 'taaliavess' );
        static::saveOldCouponsAsPromotion( static::marchesaBottom(), 'marchesa' );
        static::saveOldCouponsAsPromotion( static::marchesaTop(), 'marchesa' );
        static::saveOldCouponsAsPromotion( static::playtowinCoupons(), 'playtowin' );
    }
    
    /**
     * @param $coupons
     * @param $promotion_title
     *
     * @throws Exception
     */
    public static function saveOldCouponsAsPromotion( $coupons, $promotion_title ) {
        $new_promotions = [];
        foreach( $coupons as $single ) {
            $wc_coupon = new WC_Coupon( $single );
            // skip if we don't have existing wc coupon
            if( empty( $wc_coupon->get_date_created() ) ) continue;
            
            // skip if coupon has limit and it's already used
            if( !empty( $limit = $wc_coupon->get_usage_limit() ) ) {
                if( $limit <= $wc_coupon->get_usage_count() ) continue;
            }
            $coupon_amount       = (string) number_format( $wc_coupon->get_amount(), 2 );
            $coupon_free_product = intval( get_post_meta( $wc_coupon->get_id(), 'affiliate_product', true ) );
            
            $new_promotions[ $wc_coupon->get_discount_type() ][ $coupon_amount ][ $coupon_free_product ][] = $single;
        }
        
        if( empty( $new_promotions ) ) return;
        
        $userId = MC_Affiliates::userCouponToId( $promotion_title );
        
        foreach( $new_promotions as $new_promotion_type => $new_promotion_type_data ) {
            if( empty( $new_promotion_type_data ) ) continue;
            
            foreach( $new_promotion_type_data as $new_promotion_amount => $new_promotion_amount_data ) {
                if( empty( $new_promotion_amount_data ) ) continue;
                
                foreach( $new_promotion_amount_data as $new_promotion_free_product => $new_promotion_free_product_data ) {
                    $new_promotion_title = static::generateNewPromotionTitle( $promotion_title );
                    static::saveOldCouponsAsPromotionSingle(
                        $userId,
                        $new_promotion_title,
                        $new_promotion_free_product_data,
                        $new_promotion_type,
                        $new_promotion_amount,
                        $new_promotion_free_product
                    );
                }
            }
        }
    }
    
    /**
     * @param $userId
     * @param $promotion_title
     * @param $codes
     * @param $promotion_type
     * @param $promotion_amount
     * @param $new_promotion_free_product
     *
     * @return array
     * @throws Exception
     */
    public static function saveOldCouponsAsPromotionSingle( $userId, $promotion_title, $codes, $promotion_type, $promotion_amount,
                                                            $new_promotion_free_product ) {
        $result = [ 'status' => 0, 'message' => '' ];
        if( !empty( wc_get_coupon_id_by_code( $promotion_title ) ) ) {
            $result['message'] = 'This coupon code already used!';
            
            return $result;
        }
        
        $coupon_data['userId']                     = $userId;
        $coupon_data['promotionTitle']             = $promotion_title;
        $coupon_data['couponType']                 = $promotion_type;
        $coupon_data['discountValue']              = $promotion_amount;
        $coupon_data['withDiscount']               = !empty( $promotion_amount ) ? 1 : 0;
        $coupon_data['freeProductsList']           = $new_promotion_free_product;
        $coupon_data['freeProducts']               = !empty( $new_promotion_free_product ) ? 1 : 0;
        $coupon_data['freeProductsQuantity']       = !empty( $new_promotion_free_product ) ? 1 : 0;
        $coupon_data['redirectLink']               = '';
        $coupon_data['alwaysAddCouponAndTracking'] = $userId != 2 ? 1 : 0;
        $coupon_data['highlightedUsing']           = 1;
        $coupon_data['freeUntrackedShipping']      = 1;
        
        $wc_register_result = MC_Coupon::createCoupon( $coupon_data );
        
        if( empty( $wc_register_result['status'] ) ) {
            $result['message'] = $wc_register_result['result'];
            
            return $result;
        }
        
        $coupon_data['couponId'] = $wc_register_result['result'];
        
        $mc_register_result = MC_Affiliate_Coupon::savePromotionData( $coupon_data, true );
        MC_Affiliate_Coupon::changePromotionStatus( $coupon_data['couponId'], true );
        if( empty( $mc_register_result['status'] ) ) {
            $result['message'] = 'Something went wrong while mc saving coupon data';
            
            return $result;
        }
        
        $codes_generating_result = MC_Affiliate_Coupon::generateCouponCodes( $coupon_data['couponId'], $codes );
        if( empty( $codes_generating_result['status'] ) ) {
            $result['message'] = 'Something went wrong while mc generating coupon codes';
            
            return $result;
        }
        
        return [ 'status' => 1, 'new_coupon_id' => $coupon_data['couponId'] ];
    }
    
    /**
     * @param $promotion_title
     *
     * @return string
     */
    public static function generateNewPromotionTitle( $promotion_title ) {
        $promotion_title_new = $promotion_title.'_'.MC_Vars::generate( 3 );
        $wc_coupon           = new WC_Coupon( $promotion_title_new );
        // skip if we don't have existing wc coupon
        if( empty( $wc_coupon->get_date_created() ) ) return $promotion_title_new;
        
        return static::generateNewPromotionTitle( $promotion_title );
    }
    
    /**
     * @return array
     */
    public static function micCoupons() : array {
        return [
            // Month 1
            'bar8ztaq',
            'utu64g7q',
            'hynrzmaf',
            'f5q6577d',
            '7kfk3zm2',
            'nmnuwq5u',
            'anryfwae',
            'vc9xkpsg',
            'xmkgjpm3',
            'd9gtvhet',
            'vt478esy',
            '3uhkyb68',
            'qw4mzg6z',
            'sbfgq347',
            'h92ysyqs',
            'r22k5p6q',
            'euwqczws',
            'vuhvs7gj',
            
            //Month 2
            'wkdqflpb',
            'cfmycafg',
            'dislykwx',
            'zmpgddmi',
            'kwjqgqic',
            'bftpcytu',
            'lcyvmwpy',
            'jaghpuyz',
            'djoaxmnx',
            'dijaraxa',
            'jdktkajt',
            'kbxznszy',
            'kypdnond',
            'yrxxlzpe',
            'ilojgmvz',
            'isxltbqt',
            'wsqsdkal',
            'rhucaqrx',
            'fgswcxvb',
            'lyfxuwbf',
            
            // Month 3
            'egxuhj2r',
            '1gifkn4s',
            'zlanfjkj',
            'bhkmbd9m',
            'ydoahp2s',
            'ghptjcpv',
            'igqekzb5',
            '5yp4cvjw',
            'tymt7yxv',
            'lmgm4ash',
            '3xvg5zbq',
            'u4p0owzr',
            'jts0sduh',
            'nwu1g5uc',
            'p5fmv0rm',
            'ifzeevcw',
            '9ovyxqea',
            'zduq6fkh',
            '0ehbz7et',
            'upo3jrku',
        ];
    }
    
    /**
     * @return array
     */
    public static function taaliaCoupons() : array {
        return [
            'iylgkonu',
            'ykwvkekw',
            'cwkjoqca',
            'apvkgcog',
            'mzjwtpiy',
            'ivjlqugc',
            'vodudjch',
            'bvxjwols',
            'kuhufrvo',
            'osqhbfof',
            'psdgetem',
            'oedxuhny',
            'wqkloxhh',
            'nxnyenyk',
            'kxpnanoi',
            'ilmmbwnl',
            'wvychxps',
            'lwajmccu',
            'xttozjul',
            'yktbxdzx',
        ];
    }
    
    /**
     * @return array
     */
    public static function marchesaBottom() : array {
        return [
            'gmcxgafs',
            'sggjvujx',
            'wscontti',
            'kdyeruoq',
            'dbkoobxh',
            'jfnnfslc',
            'lhijzyyi',
            'hcfybbla',
            'bqgimtpt',
            'wsafzlas',
            'dvddtlpj',
            'ddyaylon',
            'ybjxgfnj',
            'vdwbjfxm',
            'zfbwywwp',
            'rlfpyjbl',
            'nwoxprac',
            'ubjxcqro',
            'ygkurqto',
            'kmoqhlqy',
            'sjqbywxa',
            'ocsukvgm',
            'zbyagxvs',
            'lvdlhqdu',
            'tnconriw',
            'tergdrfm',
            'fhyogqll',
            'honzjztf',
            'bmoyvtwb',
            'urhjhdux',
            'bolzglaa',
            'zmyddfwv',
            'xxcvetnp',
            'jyslwibw',
            'lzxgqdya',
            'zuaoperm',
            'avexxsld',
            'cmsssjnt',
            'rstcgmxz',
            'jcvperno',
            'uayglrwl',
            'npfpmyff',
            'cdadxats',
            'agelsaaz',
            'zhmrwsqv',
            'kwyjfnfp',
            'swggldnh',
            'smassmev',
            'hmdqwtqr',
            'xcopagty',
            'aolhhqbi',
            'ougjpsxy',
            'rpgqxfee',
            'hqjwoafi',
            'sflgctil',
            'qqotctef',
            'sinkbbfo',
            'ikbfdxdh',
            'wffnklsx',
            'wutnosff',
            'xazllxnn',
            'dxnrvibz',
            'hiiwjluo',
            'pawlmvun',
            'jndxtyhi',
            'yaqyqxik',
            'grjdqkkq',
            'unxtkaio',
            'rvwiexgo',
            'iodcktug',
            'jziddhaf',
            'bvjmlcln',
            'uokrlcmm',
            'yzeufrgw',
            'ehunrwbz',
            'cpehnarz',
            'zwvlbwrv',
            'hzoqgqbb',
            'nwzckorv',
            'mghbxazz',
            'suxugrgu',
            'ehnpqrlc',
            'tvrxjltv',
            'mbqmecrv',
            'moxcnssd',
            'ylqgnhtp',
            'zbyqxqap',
            'xhzvexay',
            'yjlomeqi',
            'wgjinlhq',
            'iutguqco',
            'fawaaowy',
            'vlnkdepw',
            'ippdefhp',
            'mtofujll',
            'lkzbemuf',
            'gdmzofvs',
            'jotgsxlk',
            'qhbtsnnc',
            'mugxacrs',
            'wkdbgqxj',
            'ooxgzold',
            'mdxlguoi',
            'bojyoerm',
            'nayxuecf',
            'upnznkoa',
            'eqxpgvbb',
            'mgnlsvmu',
            'ezqdfure',
            'nikdymnl',
            'dbbkexqb',
            'nhwmxtlt',
            'wdqllnlb',
            'crivptfy',
            'fbsmmqtg',
            'fbggtftr',
            'abtkppba',
            'kabgglqa',
            'smnushda',
            'svpfajjf',
            'pstiuzju',
            'dlelnuoc',
            'jjyomooo',
            'lqhmsbdb',
            'fndpxzug',
            'svixzskn',
            'tlvcxwyn',
            'srvleesz',
            'dtzgupjv',
            'cfowfnvh',
            'oeclegzz',
            'eibzcxpa',
            'wkrcpidd',
            'hetgddpb',
            'aglcrbdk',
            'janqgjqv',
            'vihisbxz',
            'hkycepiq',
            'pxyqodih',
            'gycbptgx',
            'izayuggb',
            'pdzxxder',
            'muxhqzzc',
            'sosmoojm',
            'vsbjdwjs',
            'ijvluzyf',
            'pcyzhvib',
            'uisrtkwk',
            'hbfwivfw',
            'zjduqczt',
            'uklboljj',
            'frmrwyuc',
            'uzbuuwxf',
            'nusbojms',
            'tpezngly',
            'zpmaxhfd',
            'zgyhajwm',
            'kuwjyvlj',
            'qqnokmux',
            'qnlbrhpp',
            'dsthmuoz',
            'ujpdrhum',
            'cmxmmqgb',
            'wwbmhqut',
            'ivywmbjn',
            'opmprbbx',
            'tigyoahf',
            'kxgfykkn',
            'pgqfhhhi',
            'bpuxyubm',
            'pfkhjpgm',
            'srtafdak',
            'cxqkqbjh',
            'gpdafwoe',
            'rrqlqjzl',
        ];
    }
    
    /**
     * @return array
     */
    public static function marchesaTop() : array {
        return [
            'qlomikto',
            'mnksmkrn',
            'gezuqunw',
            'epxoewhf',
            'kswydmdg',
            'nbolknqs',
            'ilxzdcff',
            'ktxngzvg',
            'gnkxzuex',
            'ehxrnpbe',
        ];
    }
    
    /**
     * @return array
     */
    public static function playtowinCoupons() {
        return [
            'tnfvxyjo',
            'czyezahc',
            'ynwyiuor',
            'lbgnjhjy',
            'rxzqhkxk',
            'cozbzics',
            'fqsezagf',
            'fyfdusgw',
            'igbarwkd',
            'jyiheklc',
            'orszemyw',
            'bqgbrgxh',
            'zkmonlsc',
            'jblagxmh',
            'zjwrbgge',
            'wjlmnbgq',
            'jalpecag',
            'remtgvxj',
            'ebuzudwq',
            'vjnyqota',
            'bynrbwwp',
            'ruknzroe',
            'xpozwfer',
            'tzzqqhgw',
            'ffykrypy',
            'ughrsuis',
            'hvafyenv',
            'yzdqlktm',
            'phboeyyb',
            'mqnszjqx',
            'wagiscfd',
            'ftpvzqbn',
            'gbktnqnq',
            'nhfdiuqc',
            'cbytobhv',
            'bhfoxwds',
            'mypizaqb',
            'jeldikyb',
            'rkqmuqnz',
            'ygoeptsf',
            'oilthsup',
            'tqaezknw',
            'ulbcxoad',
            'vzmxqmmr',
            'eltmthqy',
            'vzxljyqq',
            'dsxgwmgf',
            'oapufqgl',
            'rwdjtvke',
            'zvqmrcpt',
            'ugwchbeu',
            'tuktrcjo',
            'mzodrhiv',
            'ryunaovg',
            'oavwfotf',
            'usvzluiz',
            'dnbetggk',
            'nzlfxanm',
            'jpbivumg',
            'wczbkbym',
            'gsshjszf',
            'wjeeuirn',
            'velhucdg',
            'cwrwcpec',
            'otwxoruh',
            'jmpeeeai',
            'nmldcioa',
            'orlojbma',
            'qztdetlj',
            'ofdcmhkb',
            'jzowjoxp',
            'nkhvbsvi',
            'braitwtx',
            'ebkzmpon',
            'phhclyic',
            'mnxvxnta',
            'ajdujibt',
            'irxqkqsd',
            'qchfopha',
            'josdfqfu',
            'vnmthgcs',
            'ccefcrtv',
            'ednfouze',
            'gnqsublw',
            'pwtgarta',
            'khrhtpag',
            'blehrxpj',
            'bognjdqq',
            'ddrvsfjv',
            'dwphyhfz',
            'zamdwjoe',
            'nutyiarm',
            'ltqlvnhr',
            'vwimnqjq',
            'iqgahwds',
            'jecwkrdn',
            'spxjxzsu',
            'ysaawhii',
            'zpycxxhl',
            'etxstdvh',
            'osyrbkja',
            'lpbnvrly',
            'vbuhygog',
            'cabbzjvz',
            'stqjkcpm',
            'qgqtmjas',
            'kciugtqp',
            'zbngisfe',
            'jgjbaene',
            'lpecxnzu',
            'cqhvxerq',
            'uejedejh',
            'sokbfncz',
            'cokvaytq',
            'zbcyfvmt',
            'xtjzufhm',
            'hlitnxkp',
            'izndkurt',
            'vtdxwxmb',
            'kyhzjcrs',
            'azjpuksl',
            'wzcwkvhj',
            'yqoxlcyy',
            'ufdfjvwa',
            'rjbpppxh',
            'csrbeojq',
            'vgjwymue',
            'ncuzmcpw',
            'rrpeblzw',
            'rjtoazzy',
            'enjxxrld',
            'pmqabdrd',
            'rygjtkso',
            'hskrnxws',
            'swiouyal',
            'xnvhuimz',
            'egpnhqwz',
            'igiefmhk',
            'czdklgzf',
            'jqohcqof',
            'wwlgkvmr',
            'edmdmajz',
            'ousyejho',
            'ncpahxea',
            'znqhtlbx',
            'uaamsphl',
            'sxcynwho',
            'cjbcitwa',
            'wlvxojql',
            'cbxxipmv',
            'rdlmhgkk',
            'domzhnci',
            'ztlecmfm',
            'nwgcdhbs',
            'fihkumzl',
            'deeovtvv',
            'doarnyky',
            'sdquunkb',
            'xdpkzjyr',
            'ifueaxhl',
            'bytydmxi',
            'kmmixhzc',
            'bpijfoph',
            'xpkhggkb',
            'suuwaufe',
            'qbalpdco',
            'zawfcuxu',
            'sztezsqu',
            'yesmzvax',
            'wkmodsxo',
            'ozxppdbn',
            'nhmghqfp',
            'lvrzbmib',
            'sokxcbyn',
            'rnpkjufs',
            'aqwtuqqu',
            'bufwelrw',
            'keeblnhc',
            'kehfywch',
            'fstgilau',
            'tdydlgoh',
            'qvkygijn',
            'ehlfyexv',
            'jrfekrow',
            'eyomsaxo',
            'rdpamrbi',
            'izlgzaqi',
            'lswmevev',
            'aitalosc',
            'odxhxslv',
            'sfbxxekm',
            'arrkchtq',
            'fquvddgn',
            'adufbjlm',
            'ijaplxux',
            'lyccttpz',
            'vyvqsita',
            'ogcsfamg',
            'cudbculb',
            'rijioeju',
            'oqgfhgke',
            'rfyuuvup',
            'sxlvmubh',
            'bxreoizn',
            'oeakafrc',
            'ddztpsqe',
            'fnddzoai',
            'jyihxdsl',
            'xxqwwqhu',
            'wzyfqsho',
            'tmyuwqet',
            'jhullche',
            'nqcdykvt',
            'evvwsrdk',
            'jpjjujgc',
            'wrhhjiuy',
            'tiwcvnlz',
            'vsskxyqy',
            'krylerqs',
            'pmxjnuqp',
            'fiajfepo',
            'ynsvwqar',
            'polkyvtt',
            'cdeyzlvc',
            'jmuewgur',
            'birjjiwk',
            'fxntzqpz',
            'aqabzrwg',
            'baxvtgry',
            'zjxvqqyi',
            'aioazfrh',
            'fdbfhtdw',
            'jlglmwsf',
            'avkkzvgo',
            'ozjcfgnc',
            'ovbdgwdx',
            'jngoirqg',
            'kabbiccd',
            'nornlvut',
            'dsrhpvqd',
            'fnychbye',
            'wienssos',
            'ddzayysz',
            'liohlida',
            'fpajtxyy',
            'uhdedvad',
            'wgzejlwa',
            'jtdghkll',
            'vdmxaqrh',
            'dudmwrfr',
        ];
    }
    
}