<?php

class Form {
	protected $formData = array();
	protected $submitted = false;

	public function __construct($formData) {
		if(count($formData)) {
			$this->formData = $formData;
			$this->submitted = true;
		}
	}
	
	public function Submitted($submitFields=array()) {
		if(count($submitFields)) {
			foreach($submitFields as $field) {
				if(isset($this->formData[$field]))
					return true;
			}
			return false;
		}
		else
			return $this->submitted;
	}
	
	public function GetFieldValue($name, $value) {
		return ($this->submitted) ? $this->formData[$name] : $value;
	}
	
	public function SetFieldValue($name, $value) {
		$this->formData[$name] = $value;
	}
	
	protected function Sanitise($value) {
		return htmlspecialchars(stripslashes($value), ENT_QUOTES);
	}
	
	public function TextControl($name, $value, $options=array(), $attributes=array()) {
		$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
		$fieldValue = ($this->submitted) ? $postedValue : $value;
		$attributeStr = '';
		if(count($attributes))
			$attributeStr = ' '. implode(' ', $attributes);
		return "<INPUT TYPE=TEXT NAME=\"$name\" VALUE=\"". $this->Sanitise($fieldValue). "\"$attributeStr>";
	}

	public function PasswordControl($name, $value, $options=array('hideValue'=>true), $attributes=array()) {
		if($options['hideValue'])
			$fieldValue = '';
		else {
			$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
			$fieldValue = ($this->submitted) ? $postedValue : $value;			
		}
		$attributeStr = '';
		if(count($attributes))
			$attributeStr = ' '. implode(' ', $attributes);
		return "<INPUT TYPE=PASSWORD NAME=\"$name\" VALUE=\"". $this->Sanitise($fieldValue). "\"$attributeStr>";
	}
	
	public function HiddenControl($name, $value, $options=array('encrypted'=>false), $attributes = array()) {
		if($this->submitted)
			$fieldValue = $this->formData[$name];
		else
			$fieldValue = ($options['encrypted']) ? Crypt::Encrypt($value, Configuration::Get('EncryptionKey')) : $value;
		$attributeStr = '';
		if(count($attributes))
			$attributeStr = ' '. implode(' ', $attributes);
		return "<INPUT TYPE=HIDDEN NAME=\"$name\" VALUE=\"". $this->Sanitise($fieldValue). "\"$attributeStr>";
	}

	public function ButtonControl($name, $value, $options=array('type'=>'submit', 'content'=>null), $attributes = array()) {
		$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
		$fieldValue = ($this->submitted) ? $postedValue : $value;
		$type = (isset($options['type'])) ? " TYPE=\"". $options['type']. "\"" : '';
		$content = (isset($options['content'])) ? $options['content'] : $value;
		$attributeStr = '';
		if(count($attributes))
			$attributeStr = ' '. implode(' ', $attributes);
		return "<BUTTON NAME=\"$name\" VALUE=\"$fieldValue\"$type>$content</BUTTON>";
	}
	
	public function TextAreaControl($name, $value, $options=array(), $attributes = array()) {
		$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
		$fieldValue = ($this->submitted) ? $postedValue : $value;
		$attributeStr = '';
		if(count($attributes))
			$attributeStr = ' '. implode(' ', $attributes);
		return "<TEXTAREA NAME=\"$name\"$attributeStr>". $this->Sanitise($fieldValue). "</TEXTAREA>";
	}
	
	public function CheckboxControl($name, $value, $options=array(), $attributes=array()) {
		$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
		$checked = '';
		if($this->submitted) {
			if($postedValue == $value)
				$checked = ' CHECKED';
		}
		elseif(isset($options['checked']) && $options['checked'])
			$checked = ' CHECKED';
		$attributeStr = '';
		if(count($attributes))
			$attributeStr = ' '. implode(' ', $attributes);
		return "<INPUT TYPE=CHECKBOX NAME=\"$name\" VALUE=\"$value\"$checked$attributeStr>";
	}

	public function RadiobuttonControl($name, $value, $options=array(), $attributes=array()) {
		$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
		$checked = '';
		if($this->submitted) {
			if($postedValue == $value)
				$checked = ' CHECKED';
		}
		elseif(isset($options['checked']) && $options['checked'])
			$checked = ' CHECKED';
		$attributeStr = '';
		if(count($attributes))
			$attributeStr = ' '. implode(' ', $attributes);
		return "<INPUT TYPE=RADIO NAME=\"$name\" VALUE=\"$value\"$checked$attributeStr>";
	}
	
	public function SelectControl($name, $value, $options=array('options' => array()), $attributes=array()) {
		$attributeStr = '';
		if(count($attributes))
			$attributeStr = ' '. implode(' ', $attributes);
		
    $html = "<SELECT NAME=\"$name\"$attributeStr>\n";
    foreach($options['options'] as $optLabel => $optValue) {
			if(is_array($optValue)) {
				$html .= "<OPTGROUP LABEL=\"$optLabel\">\n";
				foreach($optValue as $subLabel => $subValue) {
					$selected = '';
					if(isset($this->formData[$name])) {
						if("$subValue" == $this->formData[$name])
							$selected = " SELECTED";
					}							
					elseif("$subValue" == $value)
						$selected = " SELECTED";
						
		      $html .= "<OPTION VALUE='$subValue'$selected>$subLabel\n";
				}
				$html .= "</OPTGROUP>\n";				
			}
			else {
				$selected = '';
				if(isset($this->formData[$name])) {
					if("$optValue" == $this->formData[$name])
						$selected = " SELECTED";
				}
				elseif("$optValue" == $value)
					$selected = " SELECTED";
	      $html .= "<OPTION VALUE='$optValue'$selected>$optLabel\n";
			}
    }
    $html .= "</SELECT>";
		return $html;
	}

	# Return select control based on sql query eg. $queryData = array('sql'=>'select * from table', 'labelfield'=>'name', 'valuefield'=>'userid')
	# N.B. pass $options['options'] with any predefined values eg. '-select-' => null
	public function SelectControlFromQuery($db, $queryData, $name, $value='', $options=array('options' => array()), $attributes=array()) {
		$iterator = $db->CreateQueryIterator($queryData['sql']);
		while($row = $iterator->GetNextRow()) {
			$options['options'][$row[$queryData['labelfield']]] = $row[$queryData['valuefield']];
		}
		$iterator->Close();
		return $this->SelectControl($name, $value, $options, $attributes);
	}

	# Set the initial / current value of a text like control
	public function SetValue($name, $value=null, $trim=true, $maxLength=0) {
		$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
		if($trim)
			$postedValue = trim($postedValue);
		if($maxLength)
			$postedValue = substr($postedValue, 0, $maxLength);
		if($this->submitted)
			return $this->Sanitise($postedValue);
		else
			return $value;
	}

	# Set the initial / current value of a select control
	public function SetSelect($name, $value, $select=false) {
		$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
		if($this->submitted) {
			if(isset($this->formData[$name]) && $this->formData[$name] == $value)
				return 'SELECTED';
		}
		elseif($select)
			return 'SELECTED';
		else
			return '';
	}
	
	# Set the initial / current value of a checkbox or radio control
	public function SetChecked($name, $value, $checked=false) {
		$postedValue = (isset($this->formData[$name])) ? $this->formData[$name] : null;
		if($this->submitted) {
			if(isset($this->formData[$name]) && $this->formData[$name] == $value)
				return 'CHECKED';
		}
		elseif($checked)
			return 'CHECKED';
		else
			return '';
	}

}

?>