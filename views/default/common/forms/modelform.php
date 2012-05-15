<?php

function ModelFormView($model_form, $fields=array())
{

global $base_url, $model;

$arr_required[0]='';
$arr_required[1]='*';

if(count($fields)==0)
{

	$fields=array_keys($model_form);

}

?>

<div class="form">
		<?php
		
		foreach($fields as $field)
		{
			
			$func_form=$model_form[$field]->form;
			
			switch($func_form)
			{

			default:
				
				?>

				<p>
				<label><?php echo $model_form[$field]->label;?> <?php echo $arr_required[$model_form[$field]->required]; ?> <span class="error"><?php echo $model_form[$field]->std_error; ?></span>: </label>
				<?php
				
				echo call_user_func_array($func_form , $model_form[$field]->parameters);
				
				?>
				</p>
			<?php

			break;

			case "HiddenForm":
				
				echo call_user_func_array($func_form , $model_form[$field]->parameters)."\n";
	
			break;

			}

		}

		?>
</div>

<?php

}

?>
