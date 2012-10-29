<?php

//Special field PercentField for discount, taxes_for_group, transport_for_group

class PercentField extends IntegerField{


	function check($value)
	{
		
		global $model, $lang;
		
		settype($value, "integer");

		//Reload related model if not exists, if exists, only check cache models...

		if($value>100 || $value<0)
		{
			
			$this->std_error=$lang['shop']['the_value_can_not_be_greater_than_100'];

			return 0;

		}

		return $value;
		

	}


}

class MoneyField extends DoubleField{


	function show_formatted($value)
	{

		return $this->currency_format($value);

	}

	
	static function currency_format($value)
	{

		global $arr_currency, $arr_change_currency, $config_shop;
		
		$idcurrency=$_SESSION['idcurrency'];
	
		$symbol_currency=$arr_currency[$idcurrency];
		
		if($config_shop['idcurrency']!=$idcurrency)
		{

			//Make conversion

			$change_value=@$arr_change_currency[$config_shop['idcurrency']][$idcurrency];

			if($change_value>0)
			{

				$value=$value*$change_value;

			}
			else
			{
				//Obtain $change_value for inverse arr_change_currency

				if( isset($arr_change_currency[$idcurrency][$config_shop['idcurrency']]) )
				{

					/*$change_value=1/$arr_change_currency[$idcurrency][ $config_shop['idcurrency'] ];
					$value=$value*$change_value;*/
					$value=$value/$arr_change_currency[$idcurrency][ $config_shop['idcurrency'] ];

				}
				else
				{

					$symbol_currency=$arr_currency[$config_shop['idcurrency']];

				}

			}
			

		}

		return number_format($value, 2).' '.$symbol_currency;

	}

}

?>