<?php
/**
 * Lookup.php
 *
 * Provides reference data tables
 *
 * @version 1.0
 * @package ecart
 * @subpackage references
 **/

/**
 * Lookup
 *
 * @since 1.1
 * @package ecart
 **/
class Lookup {

	/**
	 * Provides a lookup table worldwide regions
	 *	 
	 * @since 1.1
	 *
	 * @return array List of regions
	 **/
	static function regions () {
		$_ = array();
		$_[0] = __("North America","Ecart");
		$_[1] = __("Central America","Ecart");
		$_[2] = __("South America","Ecart");
		$_[3] = __("Europe","Ecart");
		$_[4] = __("Middle East","Ecart");
		$_[5] = __("Africa","Ecart");
		$_[6] = __("Asia","Ecart");
		$_[7] = __("Oceania","Ecart");
		return apply_filters('ecart_regions',$_);
	}

	/**
	 * Finds the translated region name for a specific region index
	 *	 
	 * @since 1.1
	 *
	 * @return string The translated region name
	 **/
	static function region ($id) {
		$r = Lookup::regions();
		return $r[$id];
	}

	/**
	 * Returns a lookup table of supported country defaults
	 *
	 * The information in the following table has been derived from
	 * the ISO standard documents including ISO-3166 for 2-letter country
	 * codes and ISO-4217 for currency codes
	 *	 
	 * @since 1.1
	 *
	 * @return array
	 **/
	static function countries () {
		$_ = array();
		$_['CA'] = array('name'=>__('Canada','Ecart'),'currency'=>array('code'=>'CAD','format'=>'$#,###.##'),'units'=>'metric','region'=>0);
		$_['US'] = array('name'=>__('USA','Ecart'),'currency'=>array('code'=>'USD','format'=>'$#,###.##'),'units'=>'imperial','region'=>0);
		// Specialized "country" for US Armed Forces
	  $_['USAF'] = array('name'=>__('US Armed Forces','Ecart'),'currency'=>array('code'=>'USD','format'=>'$#,###.##'),'units'=>'imperial','region'=>0);
		$_['GB'] = array('name'=>__('United Kingdom','Ecart'),'currency'=>array('code'=>'GBP','format'=>'£#,###.##'),'units'=>'metric','region'=>3);
		$_['DZ'] = array('name'=>__('Algeria','Ecart'),'currency'=>array('code'=>'DZD','format'=>'#,###.## د.ج'),'units'=>'metric','region'=>5);
		$_['AR'] = array('name'=>__('Argentina','Ecart'),'currency'=>array('code'=>'ARS','format'=>'$#.###,##'),'units'=>'metric','region'=>2);
		$_['AW'] = array('name'=>__('Aruba','Ecart'),'currency'=>array('code'=>'AWG','format'=>'ƒ#,###.##'),'units'=>'metric','region'=>2);
		$_['AU'] = array('name'=>__('Australia','Ecart'),'currency'=>array('code'=>'AUD','format'=>'$# ###.##'),'units'=>'metric','region'=>7);
		$_['AT'] = array('name'=>__('Austria','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['BB'] = array('name'=>__('Barbados','Ecart'),'currency'=>array('code'=>'BBD','format'=>'$#,###.##'),'units'=>'metric','region'=>0);
		$_['BS'] = array('name'=>__('Bahamas','Ecart'),'currency'=>array('code'=>'BSD','format'=>'$#,###.##'),'units'=>'metric','region'=>0);
		$_['BH'] = array('name'=>__('Bahrain','Ecart'),'currency'=>array('code'=>'BHD','format'=>'ب.د #,###.##'),'units'=>'metric','region'=>0);
		$_['BE'] = array('name'=>__('Belgium','Ecart'),'currency'=>array('code'=>'EUR','format'=>'#.###,## €'),'units'=>'metric','region'=>3);
		$_['BR'] = array('name'=>__('Brazil','Ecart'),'currency'=>array('code'=>'BRL','format'=>'R$#.###,##'),'units'=>'metric','region'=>2);
		$_['BG'] = array('name'=>__('Bulgaria','Ecart'),'currency'=>array('code'=>'BGN','format'=>'# ###,## лв.'),'units'=>'metric','region'=>3);
		$_['CL'] = array('name'=>__('Chile','Ecart'),'currency'=>array('code'=>'CLP','format'=>'$#.###,##'),'units'=>'metric','region'=>2);
		$_['CN'] = array('name'=>__('China','Ecart'),'currency'=>array('code'=>'CNY','format'=>'¥#,###.##'),'units'=>'metric','region'=>6);
		$_['CO'] = array('name'=>__('Colombia','Ecart'),'currency'=>array('code'=>'COP','format'=>'$#.###,##'),'units'=>'metric','region'=>2);
		$_['CR'] = array('name'=>__('Costa Rica','Ecart'),'currency'=>array('code'=>'CRC','format'=>'₡#.###,##'),'units'=>'metric','region'=>1);
		$_['HR'] = array('name'=>__('Croatia','Ecart'),'currency'=>array('code'=>'HRK','format'=>'#.###,## kn'),'units'=>'metric','region'=>3);
		$_['CY'] = array('name'=>__('Cyprus','Ecart'),'currency'=>array('code'=>'CYP','format'=>'£#.###,##'),'units'=>'metric','region'=>3);
		$_['CZ'] = array('name'=>__('Czech Republic','Ecart'),'currency'=>array('code'=>'CZK','format'=>'# ###,## Kč'),'units'=>'metric','region'=>3);
		$_['DK'] = array('name'=>__('Denmark','Ecart'),'currency'=>array('code'=>'DKK','format'=>'#.###,## kr'),'units'=>'metric','region'=>3);
		$_['DO'] = array('name'=>__('Dominican Republic','Ecart'),'currency'=>array('code'=>'DOP','format'=>'$#,###.##'),'units'=>'metric','region'=>1);
		$_['EC'] = array('name'=>__('Ecuador','Ecart'),'currency'=>array('code'=>'ESC','format'=>'$#,###.##'),'units'=>'metric','region'=>2);
		$_['EG'] = array('name'=>__('Egypt','Ecart'),'currency'=>array('code'=>'EGP','format'=>'£#,###.##'),'units'=>'metric','region'=>5);
		$_['EE'] = array('name'=>__('Estonia','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['FI'] = array('name'=>__('Finland','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['FR'] = array('name'=>__('France','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['DE'] = array('name'=>__('Germany','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['GR'] = array('name'=>__('Greece','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['GP'] = array('name'=>__('Guadeloupe','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['GT'] = array('name'=>__('Guatemala','Ecart'),'currency'=>array('code'=>'GTQ','format'=>'Q#,###.##'),'units'=>'metric','region'=>1);
		$_['HK'] = array('name'=>__('Hong Kong','Ecart'),'currency'=>array('code'=>'HKD','format'=>'$#,###.##'),'units'=>'metric','region'=>6);
		$_['HU'] = array('name'=>__('Hungary','Ecart'),'currency'=>array('code'=>'HUF','format'=>'# ### ### Ft'),'units'=>'metric','region'=>3);
		$_['IS'] = array('name'=>__('Iceland','Ecart'),'currency'=>array('code'=>'ISK','format'=>'#.###.### kr.'),'units'=>'metric','region'=>3);
		$_['IN'] = array('name'=>__('India','Ecart'),'currency'=>array('code'=>'INR','format'=>'₨#,##,###.##'),'units'=>'metric','region'=>6);
		$_['ID'] = array('name'=>__('Indonesia','Ecart'),'currency'=>array('code'=>'IDR','format'=>'Rp #.###,##'),'units'=>'metric','region'=>7);
		$_['IE'] = array('name'=>__('Ireland','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['IL'] = array('name'=>__('Israel','Ecart'),'currency'=>array('code'=>'ILS','format'=>'₪ #,###.##'),'units'=>'metric','region'=>4);
		$_['IT'] = array('name'=>__('Italy','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['JM'] = array('name'=>__('Jamaica','Ecart'),'currency'=>array('code'=>'JMD','format'=>'$#,###.##'),'units'=>'metric','region'=>0);
		$_['JP'] = array('name'=>__('Japan','Ecart'),'currency'=>array('code'=>'JPY','format'=>'¥#,###,###'),'units'=>'metric','region'=>6);
		$_['LV'] = array('name'=>__('Latvia','Ecart'),'currency'=>array('code'=>'LVL','format'=>'# ###.## Ls'),'units'=>'metric','region'=>3);
		$_['LT'] = array('name'=>__('Lithuania','Ecart'),'currency'=>array('code'=>'LTL','format'=>'#.###,## Lt'),'units'=>'metric','region'=>3);
		$_['LU'] = array('name'=>__('Luxembourg','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['MY'] = array('name'=>__('Malaysia','Ecart'),'currency'=>array('code'=>'MYR','format'=>'RM#,###.##'),'units'=>'metric','region'=>6);
		$_['MT'] = array('name'=>__('Malta','Ecart'),'currency'=>array('code'=>'MTL','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['MX'] = array('name'=>__('Mexico','Ecart'),'currency'=>array('code'=>'MXN','format'=>'$#,###.##'),'units'=>'metric','region'=>0);
		$_['NL'] = array('name'=>__('Netherlands','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#.###,##'),'units'=>'metric','region'=>3);
		$_['NZ'] = array('name'=>__('New Zealand','Ecart'),'currency'=>array('code'=>'NZD','format'=>'$#,###.##'),'units'=>'metric','region'=>7);
		$_['NG'] = array('name'=>__('Nigeria','Ecart'),'currency'=>array('code'=>'NGN','format'=>'₦#,###.##'),'units'=>'metric','region'=>5);
		$_['NO'] = array('name'=>__('Norway','Ecart'),'currency'=>array('code'=>'NOK','format'=>'kr # ###,##'),'units'=>'metric','region'=>3);
		$_['PK'] = array('name'=>__('Pakistan','Ecart'),'currency'=>array('code'=>'PKR','format'=>'₨#,###.##'),'units'=>'metric','region'=>4);
		$_['PA'] = array('name'=>__('Panama','Ecart'),'currency'=>array('code'=>'PAB','format'=>'B/. #,###.##'),'units'=>'metric','region'=>1);
		$_['PE'] = array('name'=>__('Peru','Ecart'),'currency'=>array('code'=>'PEN','format'=>'S/. #,###.##'),'units'=>'metric','region'=>2);
		$_['PH'] = array('name'=>__('Philippines','Ecart'),'currency'=>array('code'=>'PHP','format'=>'Php #,###.##'),'units'=>'metric','region'=>6);
		$_['PL'] = array('name'=>__('Poland','Ecart'),'currency'=>array('code'=>'PLN','format'=>'#.###,## zł'),'units'=>'metric','region'=>3);
		$_['PT'] = array('name'=>__('Portugal','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['PR'] = array('name'=>__('Puerto Rico','Ecart'),'currency'=>array('code'=>'USD','format'=>'$#,###.##'),'units'=>'imperial','region'=>0);
		$_['RO'] = array('name'=>__('Romania','Ecart'),'currency'=>array('code'=>'ROL','format'=>'#.###,## lei'),'units'=>'metric','region'=>3);
		$_['RU'] = array('name'=>__('Russia','Ecart'),'currency'=>array('code'=>'RUB','format'=>'# ###,## руб'),'units'=>'metric','region'=>6);
		$_['SG'] = array('name'=>__('Singapore','Ecart'),'currency'=>array('code'=>'SGD','format'=>'$#,###.##'),'units'=>'metric','region'=>6);
		$_['SK'] = array('name'=>__('Slovakia','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['SI'] = array('name'=>__('Slovenia','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#,###.##'),'units'=>'metric','region'=>3);
		$_['ZA'] = array('name'=>__('South Africa','Ecart'),'currency'=>array('code'=>'ZAR','format'=>'R# ###,##'),'units'=>'metric','region'=>5);
		$_['KR'] = array('name'=>__('South Korea','Ecart'),'currency'=>array('code'=>'KRW','format'=>'₩#,###.##'),'units'=>'metric','region'=>6);
		$_['ES'] = array('name'=>__('Spain','Ecart'),'currency'=>array('code'=>'EUR','format'=>'€#.###,##'),'units'=>'metric','region'=>3);
		$_['VC'] = array('name'=>__('St. Vincent','Ecart'),'currency'=>array('code'=>'XCD','format'=>'$#,###.##'),'units'=>'metric','region'=>6);
		$_['SE'] = array('name'=>__('Sweden','Ecart'),'currency'=>array('code'=>'SEK','format'=>'#.###,## kr'),'units'=>'metric','region'=>3);
		$_['CH'] = array('name'=>__('Switzerland','Ecart'),'currency'=>array('code'=>'CHF','format'=>"#'###.## CHF"),'units'=>'metric','region'=>3);
		$_['TW'] = array('name'=>__('Taiwan','Ecart'),'currency'=>array('code'=>'TWD','format'=>'NT$#,###.##'),'units'=>'metric','region'=>6);
		$_['TH'] = array('name'=>__('Thailand','Ecart'),'currency'=>array('code'=>'THB','format'=>'#,###.##฿'),'units'=>'metric','region'=>6);
		$_['TT'] = array('name'=>__('Trinidad and Tobago','Ecart'),'currency'=>array('code'=>'TTD','format'=>'TT$#,###.##'),'units'=>'metric','region'=>0);
		$_['TR'] = array('name'=>__('Turkey','Ecart'),'currency'=>array('code'=>'TRL','format'=>'#.###,## TL'),'units'=>'metric','region'=>4);
		$_['UA'] = array('name'=>__('Ukraine','Ecart'),'currency'=>array('code'=>'UAH','format'=>'# ###,## ₴'),'units'=>'metric','region'=>4);
		$_['AE'] = array('name'=>__('United Arab Emirates','Ecart'),'currency'=>array('code'=>'AED','format'=>'Dhs. #,###.##'),'units'=>'metric','region'=>4);
		$_['UY'] = array('name'=>__('Uruguay','Ecart'),'currency'=>array('code'=>'UYP','format'=>'$#,###.##'),'units'=>'metric','region'=>2);
		$_['VE'] = array('name'=>__('Venezuela','Ecart'),'currency'=>array('code'=>'VUB','format'=>'Bs. #,###.##'),'units'=>'metric','region'=>2);
		return apply_filters('ecart_countries',$_);
	}

	/**
	 * Provides a lookup table of country zones (states/provinces)
	 *	 
	 * @since 1.1
	 *
	 * @return array
	 **/
	static function country_zones () {
		$_ = array();
		$_['AU'] = array();
		$_['AU']['ACT'] = 'Australian Capital Territory';
		$_['AU']['NSW'] = 'New South Wales';
		$_['AU']['NT'] = 'Northern Territory';
		$_['AU']['QLD'] = 'Queensland';
		$_['AU']['SA'] = 'South Australia';
		$_['AU']['TAS'] = 'Tasmania';
		$_['AU']['VIC'] = 'Victoria';
		$_['AU']['WA'] = 'Western Australia';

		$_['CA'] = array();
		$_['CA']['AB'] = 'Alberta';
		$_['CA']['BC'] = 'British Columbia';
		$_['CA']['MB'] = 'Manitoba';
		$_['CA']['NB'] = 'New Brunswick';
		$_['CA']['NF'] = 'Newfoundland';
		$_['CA']['NT'] = 'Northwest Territories';
		$_['CA']['NS'] = 'Nova Scotia';
		$_['CA']['NU'] = 'Nunavut';
		$_['CA']['ON'] = 'Ontario';
		$_['CA']['PE'] = 'Prince Edward Island';
		$_['CA']['PQ'] = 'Quebec';
		$_['CA']['SK'] = 'Saskatchewan';
		$_['CA']['YT'] = 'Yukon Territory';

		$_['US'] = array();
		$_['US']['AL'] = 'Alabama';
		$_['US']['AK'] = 'Alaska ';
		$_['US']['AZ'] = 'Arizona';
		$_['US']['AR'] = 'Arkansas';
		$_['US']['CA'] = 'California';
		$_['US']['CO'] = 'Colorado';
		$_['US']['CT'] = 'Connecticut';
		$_['US']['DE'] = 'Delaware';
		$_['US']['DC'] = 'District Of Columbia';
		$_['US']['FL'] = 'Florida';
		$_['US']['GA'] = 'Georgia';
		$_['US']['HI'] = 'Hawaii';
		$_['US']['ID'] = 'Idaho';
		$_['US']['IL'] = 'Illinois';
		$_['US']['IN'] = 'Indiana';
		$_['US']['IA'] = 'Iowa';
		$_['US']['KS'] = 'Kansas';
		$_['US']['KY'] = 'Kentucky';
		$_['US']['LA'] = 'Louisiana';
		$_['US']['ME'] = 'Maine';
		$_['US']['MD'] = 'Maryland';
		$_['US']['MA'] = 'Massachusetts';
		$_['US']['MI'] = 'Michigan';
		$_['US']['MN'] = 'Minnesota';
		$_['US']['MS'] = 'Mississippi';
		$_['US']['MO'] = 'Missouri';
		$_['US']['MT'] = 'Montana';
		$_['US']['NE'] = 'Nebraska';
		$_['US']['NV'] = 'Nevada';
		$_['US']['NH'] = 'New Hampshire';
		$_['US']['NJ'] = 'New Jersey';
		$_['US']['NM'] = 'New Mexico';
		$_['US']['NY'] = 'New York';
		$_['US']['NC'] = 'North Carolina';
		$_['US']['ND'] = 'North Dakota';
		$_['US']['OH'] = 'Ohio';
		$_['US']['OK'] = 'Oklahoma';
		$_['US']['OR'] = 'Oregon';
		$_['US']['PA'] = 'Pennsylvania';
		$_['US']['RI'] = 'Rhode Island';
		$_['US']['SC'] = 'South Carolina';
		$_['US']['SD'] = 'South Dakota';
		$_['US']['TN'] = 'Tennessee';
		$_['US']['TX'] = 'Texas';
		$_['US']['UT'] = 'Utah';
		$_['US']['VT'] = 'Vermont';
		$_['US']['VA'] = 'Virginia';
		$_['US']['WA'] = 'Washington';
		$_['US']['WV'] = 'West Virginia';
		$_['US']['WI'] = 'Wisconsin';
		$_['US']['WY'] = 'Wyoming';

		$_['USAF']['AA'] = 'Americas';
		$_['USAF']['AE'] = 'Europe';
		$_['USAF']['AP'] = 'Pacific';
		return apply_filters('ecart_country_zones',$_);
	}

	/**
	 * Provides a lookup table of colloquial country areas codified by post code regions
	 *	 
	 * @since 1.1
	 *
	 * @return array
	 **/
	static function country_areas () {
		$_ = array();
		$_['CA'] = array();
		$_['CA']['Northern Canada'] = array('YT'=>array('Y'),'NT'=>array('X'),'NU'=>array('X'));
		$_['CA']['Western Canada'] = array('BC'=>array('V'),'AB'=>array('T'),'SK'=>array('S'),'MB'=>array('R'));
		$_['CA']['Eastern Canada'] = array('ON'=>array('K','L','M','N','P'),'PQ'=>array('G','H','J'),'NB'=>array('E'),'PE'=>array('C'),'NS'=>array('B'),'NF'=>array('A'));

		$_['US'] = array();
		$_['US']['Northeast US'] = array('MA'=>array('01000','02799','05500','05599'),'RI'=>array('02800','02999'),'NH'=>array('03000','03897'),'ME'=>array('03901','04999'),'VT'=>array('05000','05999'),'CT'=>array('06000','06999'),'NJ'=>array('07000','08999'),'NY'=>array('09000','14999','00500','00599','06300','06399'),'PA'=>array('15000','19699'));
		$_['US']['Midwest US'] = array('OH'=>array('43000','45999'),'IN'=>array('46000','47999'),'MI'=>array('48000','49999'),'IA'=>array('50000','52899'),'WI'=>array('53000','54999'),'MN'=>array('55000','56799'),'SD'=>array('57000','57799'),'ND'=>array('58000','58899'),'IL'=>array('60000','62999'),'MO'=>array('63000','65899'),'KS'=>array('66000','67999'),'NE'=>array('68000','69399'));
		$_['US']['South US'] =array('DE'=>array('19700','19999'),'DC'=>array('20000','20599'),'MD'=>array('20600','21999'),'VA'=>array('22000','24699','20100','20199'),'WV'=>array('24700','26899'),'NC'=>array('26900','28999'),'SC'=>array('29000','29999'),'GA'=>array('30000','31999','39800','39999'),'FL'=>array('32000','34999'),'AL'=>array('35000','36999'),'TN'=>array('37000','38599'),'MS'=>array('38600','39799'),'KY'=>array('40000','42799'),'LA'=>array('70000','71499'),'AR'=>array('71600','72999','75500','75599'),'OK'=>array('73000','74999'),'TX'=>array('75000','79999','88500','88599'));
		$_['US']['West US'] =array('MT'=>array('59000','59999'),'CO'=>array('80000','81699'),'WY'=>array('82000','83199'),'ID'=>array('83200','83899'),'UT'=>array('84000','84799'),'AZ'=>array('85000','86599'),'NM'=>array('87000','88499'),'NV'=>array('88900','89899'),'CA'=>array('90000','96699'),'HI'=>array('96700','96899'),'OR'=>array('97000','97999'),'WA'=>array('98000','99499'),'AK'=>array('99500','99999'));

		$_['USAF'] = array();
		$_['USAF']['Americas'] = array('AA'=>array('34000','34099'));
		$_['USAF']['Europe'] = array('AE'=>array('09000','09999'));
		$_['USAF']['Pacific'] = array('AP'=>array('96200','96699'));
		return apply_filters('ecart_areas',$_);
	}

	function customer_types () {
		$_ = array(
			__('Retail','Ecart'),
			__('Wholesale','Ecart'),
			__('Referral','Ecart'),
			__('Tax-Exempt','Ecart')
		);
		return apply_filters('ecart_customer_types',$_);
	}


	function localities () {
		$_ = array();
		return apply_filters('ecart_localities',$_);
	}

	/**
	 * Provides a list of country codes for countries that use VAT taxes
	 * 
	 * @since 1.1
	 *
	 * @return array List of country codes
	 **/
	static function vat_countries () {
		return apply_filters('ecart_vat_countries',array(
			'AU','AT','BE','BG','CZ','DK','DE','EE','GB',
			'GR','ES','FR','IE','IT','CY','LV','LT','LU',
			'HU','MT','NL','PL','PT','RO','SI','SK','FI',
			'SE'
		));
	}

	/**
	 * Provides a list of supported payment cards
	 *	 
	 * @since 1.1
	 *
	 * @return array List of payment cards
	 **/
	static function paycards () {
		$_ = array();
		$_['amex'] = new PayCard('American Express','Amex','/^3[4,7]\d{13}$/',4);
		$_['dc'] = new PayCard("Diner's Club",'DC','/^(30|36|38|39|54)\d{12}$/',3);
		$_['disc'] = new PayCard("Discover Card",'Disc','/^6(011|22[0-9]|4[4-9]0|5[0-9][0-9])\d{12}$/',3);
		$_['jcb'] = new PayCard('JCB','JCB','/^35(2[8-9]|[3-8][0-9])\d{12}$/',3);
		$_['lasr'] = new PayCard('Laser','Lasr','/^(6304|6706|6709|6771)\d{12,15}$/');
		$_['maes'] = new PayCard('Maestro','Maes','/^(311|367|[5-6][0-9][0-9][0-9])\d{8,15}$/',3, array('start'=>5,'issue'=>3));
		$_['mc'] = new PayCard('MasterCard','MC','/^5[1-5]\d{14}$/',3);
		$_['solo'] = new PayCard('Solo','Solo','/^(6334|6767)(\d{12}|\d{14,15})$/',3, array('start'=>5,'issue'=>3));
		$_['visa'] = new PayCard('Visa','Visa','/^4\d{15}$/',3);
		return apply_filters('ecart_payment_cards',$_);
	}

	/**
	 * Gets a specified payment card
	 *	 
	 * @since 1.1
	 *
	 * @return object PayCard object
	 **/
	static function paycard ($card) {
		$cards = Lookup::paycards();
		if (isset($cards[strtolower($card)])) return $cards[strtolower($card)];
		return false;
	}

	/**
	 * A list of translatable payment status labels
	 *	 
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	static function payment_status_labels () {
		$_ = array(
			'PENDING' => __('Pending','Ecart'),
			'CHARGED' => __('Charged','Ecart'),
			'REFUNDED' => __('Refunded','Ecart'),
			'VOID' => __('Void','Ecart')
		);
		return apply_filters('ecart_payment_status_labels',$_);
	}

	/**
	 * A list of stop words to be excluded from search indexes
	 *
	 * Stop words are commonly used words that are not particularly
	 * useful for searching because they would provide too much
	 * noise (irrelevant hits) in the results
	 * 
	 * @since 1.1
	 *
	 * @return array List of stop words
	 **/
	static function stopwords () {
		$_ = array(
	  	    "a", "an", "and", "are", "as", "at", "be", "but", "by",
		    "for", "if", "in", "into", "is", "it",
		    "no", "not", "of", "on", "or", "such",
		    "that", "the", "their", "then", "there", "these",
		    "they", "this", "to", "was", "will", "with"
		);
		return apply_filters('ecart_index_stopwords',$_);
	}

	/**
	 * Provides index factor settings to use when building indexes
	 *
	 * Index factoring provides a configurable set of relevancy weights
	 * that are factored into the scoring of search results. Factors are
	 * in percentages, thus a factor of 50 gives the index half the
	 * relevancy of a normal index. Searching on an index with a factor
	 * of 200 doubles the relevancy of hits on matches in that index.
	 *	 
	 * @since 1.1
	 *
	 * @return array List of index factor settings
	 **/
	static function index_factors () {
		$_ = array(
			'name' => 200,
			'prices' => 160,
			'specs' => 75,
			'summary' => 100,
			'description' => 100,
			'categories' => 50,
			'tags' => 50
		);
		return apply_filters('ecart_index_factors',$_);
	}


} // END class Lookup

?>