<?php

	function debug( $mixExpression , $boolExit = TRUE , $boolFinish = NULL )
	{
		static $arrMessages;
		if( ! $arrMessages )
		{
			$arrMessages = array();
		}
	
		if( $boolFinish )
		{
			return( implode( " <br/> " , $arrMessages ) );
		}
	
		$arrBacktrace = debug_backtrace();
		$strMessage = "";
		$strMessage .= "<fieldset><legend><font color=\"#007000\">debug</font></legend><pre>" ;
		foreach( $arrBacktrace[0] as $strAttribute => $mixValue )
		{
			$strMessage .= "<b>" . $strAttribute . "</b> ". $mixValue ."\n";
		}
		$strMessage .= "<hr />";
	
		# Abre o buffer, impedindo que seja impresso na tela alguma coisa
		ob_start();
		var_dump( $mixExpression );
		# Pega todo o buffer
		$strMessage .= ob_get_clean();
	
		$strMessage .= "</pre></fieldset>";
	
		
		foreach( $arrMessages as $messages )
		{
			print $messages;
			ob_flush();
			flush();
		}
		print $strMessage;
		print "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font>";
		
		if( $boolExit )
		{
			exit();
		}
	}


?>