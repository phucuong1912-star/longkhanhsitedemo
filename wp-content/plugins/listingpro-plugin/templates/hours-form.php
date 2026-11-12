<?php						
if( !function_exists('LP_operational_hours_form') ){
	function LP_operational_hours_form($postID,$edit){
		$output = '';
		$MondayOpen = '';
		$MondayClose = '';
		$TusedayOpen = '';
		$TusedayClose = '';
		$WednesdayOpen = '';
		$WednesdayClose = '';
		$ThursdayOpen = '';
		$ThursdayClose = '';
		$FridayOpen = '';
		$FridayClose = '';
		$SaturdayOpen = '';
		$SaturdayClose = '';
		$SundayOpen = '';
		$SundayClose = '';
		
		$MondayEnabled = 'disabled';
		$Mondaychecked = '';
		$TusedayEnabled = 'disabled';
		$Tusedaychecked = '';
		$WednesdayEnabled = 'disabled';
		$Wednesdaychecked = '';
		$ThursdayEnabled = 'disabled';
		$Thursdaychecked = '';
		$FridayEnabled = 'disabled';
		$Fridaychecked = '';
		$SaturdayEnabled = 'disabled';
		$Saturdaychecked = '';
		$SundayEnabled = 'disabled';
		$Sundaychecked = '';
		global $listingpro_options;
		
		// added for style2 button text
        $page_style =   $listingpro_options['listing_submit_page_style'];
        $add_hour_st = '';
        $removeStr      =   esc_html__( 'Remove', 'listingpro-plugin' );
        $removeData     =   esc_html__( 'Remove', 'listingpro-plugin' );
        if($page_style == 'style2'){
            $add_hour_st    =   'lp-add-hours-st';
            $removeStr      =   '<i class="fa fa-times"></i>';
		}
		$listingOphText = null;
		if (isset($listingpro_options['listing_oph_text'])) :
			$listingOphText = $listingpro_options['listing_oph_text'];
		endif;
		$page_style =   'style1';
		if( isset( $listingpro_options['listing_submit_page_style'] ) && !empty($listingpro_options['listing_submit_page_style']) )
		{
			$page_style =   $listingpro_options['listing_submit_page_style'];
		}
		$listing2timeslots = $listingpro_options['lp_hours_slot2'];
			$output .='
				
				<div class="form-group clearfix">';
		if( $page_style == 'style1' )
		{
			$output .='		<label for="operationalHours">'.$listingOphText.'</label>';
		}
			$output .='		
					<div class="day-hours" id="day-hours-BusinessHours" data-lpenabletwotimes="'.$listing2timeslots.'">
						<div class="hours-display">';
		if($edit == true && !empty($postID)){
			$buisness_hours = listing_get_metabox_by_ID('business_hours', $postID);
			
			if(!empty($buisness_hours)){	
				foreach($buisness_hours as $key=>$value){
					$output .='		<div class="hours">';
						if( !empty($value['open'])&& !empty($value['close'])){
							
							if( is_array($value['open']) && is_array($value['close']) ){
								
									$output .='<span class="weekday">'.$key.'</span>';
									if( isset($value['open'][0]) && isset($value['close'][0]) ){
										$output .='<span class="start">'.listing_time_format($value['open'][0],null).'</span>
										<input name="business_hours['.$key.'][open][0]" value="'.$value['open'][0].'" type="hidden">';
										$output .='<span>-</span>';
										$output .='<span class="end">'.listing_time_format($value['close'][0],null).'</span>
										<input name="business_hours['.$key.'][close][0]" value="'.$value['close'][0].'" type="hidden">';
									}
									
									if( isset($value['open'][1]) && isset($value['close'][1]) ){
								
										$output .='<span class="start">'.listing_time_format($value['open'][1],null).'</span>
										<input name="business_hours['.$key.'][open][1]" value="'.$value['open'][1].'" type="hidden">';
										$output .='<span>-</span>';
										$output .='<span class="end">'.listing_time_format($value['close'][1],null).'</span>
										<input name="business_hours['.$key.'][close][1]" value="'.$value['close'][1].'" type="hidden">';
									}
								
								$output .='<a class="remove-hours" href="#">'.$removeStr.'</a>';
								$output .='</div>';
								
							}else{
								$output .='
									<span class="weekday">'.$key.'</span>
									<span class="start">'.listing_time_format($value['open'],null).' </span>
									<span>-</span>
									<span class="end">'.listing_time_format($value['close'],null).'</span>
									<input name="business_hours['.$key.'][open]" value="'.$value['open'].'" type="hidden">
									<input name="business_hours['.$key.'][close]" value="'.$value['close'].'" type="hidden">
									<a class="remove-hours" href="#">'.$removeStr.'</a>
									</div>
								';
							}
						}
						else{
							$output .='
								<span class="weekday">'.$key.'</span>
								<span class="start-end fullday">
								'.esc_html__('24 hours open', 'listingpro-plugin').'
								<input name="business_hours['.$key.'][open]" value="" type="hidden">
								<input name="business_hours['.$key.'][close]" value="" type="hidden">
								</span>
							';
							$output .='<a class="remove-hours" href="#">'.$removeStr.'</i></a>';
							$output .='</div>';
						}
			                
							
							
					
				}
			}
		}else{
		$output .='
				       
						<div class="hours">
							<span class="weekday">'.esc_html__( 'Monday', 'listingpro-plugin' ).'</span>
							<span class="start">'.listing_time_format('09:00',null).'</span>
							<span>-</span>
							<span class="end">'.listing_time_format('17:00',null).'</span>
							<a class="remove-hours" href="#">'.$removeStr.'</i></a>
							<input name="business_hours['.esc_html__( 'Monday', 'listingpro-plugin' ).'][open]" value="'.listing_time_format(null,'09:00').'" type="hidden">
							<input name="business_hours['.esc_html__( 'Monday', 'listingpro-plugin' ).'][close]" value="'.listing_time_format(null,'17:00').'" type="hidden">
						</div>
						<div class="hours">
							<span class="weekday">'.esc_html__( 'Tuesday', 'listingpro-plugin' ).'</span>
							<span class="start">'.listing_time_format('09:00',null).'</span>
							<span>-</span>
							<span class="end">'.listing_time_format('17:00',null).'</span>
							<a class="remove-hours" href="#">'.$removeStr.'</a>
							<input name="business_hours['.esc_html__( 'Tuesday', 'listingpro-plugin' ).'][open]" value="'.listing_time_format(null,'09:00').'" type="hidden">
							<input name="business_hours['.esc_html__( 'Tuesday', 'listingpro-plugin' ).'][close]" value="'.listing_time_format(null,'17:00').'" type="hidden">
						</div>
						<div class="hours">
							<span class="weekday">'.esc_html__( 'Wednesday', 'listingpro-plugin' ).'</span>
							<span class="start">'.listing_time_format('09:00',null).'</span>
							<span>-</span>
							<span class="end">'.listing_time_format('17:00',null).'</span>
							<a class="remove-hours" href="#">'.$removeStr.'</a>
							<input name="business_hours['.esc_html__( 'Wednesday', 'listingpro-plugin' ).'][open]" value="'.listing_time_format(null,'09:00').'" type="hidden">
							<input name="business_hours['.esc_html__( 'Wednesday', 'listingpro-plugin' ).'][close]" value="'.listing_time_format(null,'17:00').'" type="hidden">
						</div>
						<div class="hours">
							<span class="weekday">'.esc_html__( 'Thursday', 'listingpro-plugin' ).'</span>
							<span class="start">'.listing_time_format('09:00',null).'</span>
							<span>-</span>
							<span class="end">'.listing_time_format('17:00',null).'</span>
							<a class="remove-hours" href="#">'.$removeStr.'</a>
							<input name="business_hours['.esc_html__( 'Thursday', 'listingpro-plugin' ).'][open]" value="'.listing_time_format(null,'09:00').'" type="hidden">
							<input name="business_hours['.esc_html__( 'Thursday', 'listingpro-plugin' ).'][close]" value="'.listing_time_format(null,'17:00').'" type="hidden">
						</div>
						<div class="hours">
							<span class="weekday">'.esc_html__( 'Friday', 'listingpro-plugin' ).'</span>
							<span class="start">'.listing_time_format('09:00',null).'</span>
							<span>-</span>
							<span class="end">'.listing_time_format('17:00',null).'</span>
							<a class="remove-hours" href="#">'.$removeStr.'</a>
							<input name="business_hours['.esc_html__( 'Friday', 'listingpro-plugin' ).'][open]" value="'.listing_time_format(null,'09:00').'" type="hidden">
							<input name="business_hours['.esc_html__( 'Friday', 'listingpro-plugin' ).'][close]" value="'.listing_time_format(null,'17:00').'" type="hidden">
						</div>
					';
	}
	$output .= '</div>
					<ul class="hours-select clearfix inline-layout up-4">
						<li>
							<select class="weekday select2">
								<option value="'.esc_html__( 'Monday', 'listingpro-plugin' ).'">'.esc_html__( 'Monday', 'listingpro-plugin' ).'</option>
								<option value="'.esc_html__( 'Tuesday', 'listingpro-plugin' ).'">'.esc_html__( 'Tuesday', 'listingpro-plugin' ).'</option>
								<option value="'.esc_html__( 'Wednesday', 'listingpro-plugin' ).'">'.esc_html__( 'Wednesday', 'listingpro-plugin' ).'</option>
								<option value="'.esc_html__( 'Thursday', 'listingpro-plugin' ).'">'.esc_html__( 'Thursday', 'listingpro-plugin' ).'</option>
								<option value="'.esc_html__( 'Friday', 'listingpro-plugin' ).'">'.esc_html__( 'Friday', 'listingpro-plugin' ).'</option>
								<option value="'.esc_html__( 'Saturday', 'listingpro-plugin' ).'" selected="">'.esc_html__( 'Saturday', 'listingpro-plugin' ).'</option>
								<option value="'.esc_html__( 'Sunday', 'listingpro-plugin' ).'">'.esc_html__( 'Sunday', 'listingpro-plugin' ).'</option>
							</select>
						</li>
						<li>
							<select class="hours-start select2">
								<option value="'.listing_time_format(null,'24:00').'">'.listing_time_format('24:00',null).' </option>
								<option value="'.listing_time_format(null,'24:30').'">'.listing_time_format('24:30',null).'</option>
								<option value="'.listing_time_format(null,'01:00').'">'.listing_time_format('01:00',null).'</option>
								<option value="'.listing_time_format(null,'01:30').'">'.listing_time_format('01:30',null).'</option>
								<option value="'.listing_time_format(null,'02:00').'">'.listing_time_format('02:00',null).'</option>
								<option value="'.listing_time_format(null,'02:30').'">'.listing_time_format('02:30',null).'</option>
								<option value="'.listing_time_format(null,'03:00').'">'.listing_time_format('03:00',null).'</option>
								<option value="'.listing_time_format(null,'03:30').'">'.listing_time_format('03:30',null).'</option>
								<option value="'.listing_time_format(null,'04:00').'">'.listing_time_format('04:00',null).'</option>
								<option value="'.listing_time_format(null,'04:30').'">'.listing_time_format('04:30',null).'</option>
								<option value="'.listing_time_format(null,'05:00').'">'.listing_time_format('05:00',null).'</option>
								<option value="'.listing_time_format(null,'05:30').'">'.listing_time_format('05:30',null).'</option>
								<option value="'.listing_time_format(null,'06:00').'">'.listing_time_format('06:00',null).'</option>
								<option value="'.listing_time_format(null,'06:30').'">'.listing_time_format('06:30',null).'</option>
								<option value="'.listing_time_format(null,'07:00').'">'.listing_time_format('07:00',null).'</option>
								<option value="'.listing_time_format(null,'07:30').'">'.listing_time_format('07:30',null).'</option>
								<option value="'.listing_time_format(null,'08:00').'">'.listing_time_format('08:00',null).'</option>
								<option value="'.listing_time_format(null,'08:30').'">'.listing_time_format('08:30',null).'</option>
								<option value="'.listing_time_format(null,'09:00').'" selected="">'.listing_time_format('09:00',null).'</option>
								<option value="'.listing_time_format(null,'09:30').'">'.listing_time_format('09:30',null).'</option>
								<option value="'.listing_time_format(null,'10:00').'">'.listing_time_format('10:00',null).'</option>
								<option value="'.listing_time_format(null,'10:30').'">'.listing_time_format('10:30',null).'</option>
								<option value="'.listing_time_format(null,'11:00').'">'.listing_time_format('11:00',null).'</option>
								<option value="'.listing_time_format(null,'11:30').'">'.listing_time_format('11:30',null).'</option>
								<option value="'.listing_time_format(null,'12:00').'">'.listing_time_format('12:00',null).'</option>
								<option value="'.listing_time_format(null,'12:30').'">'.listing_time_format('12:30',null).'</option>
								<option value="'.listing_time_format(null,'13:00').'">'.listing_time_format('13:00',null).'</option>
								<option value="'.listing_time_format(null,'13:30').'">'.listing_time_format('13:30',null).'</option>
								<option value="'.listing_time_format(null,'14:00').'">'.listing_time_format('14:00',null).'</option>
								<option value="'.listing_time_format(null,'14:30').'">'.listing_time_format('14:30',null).'</option>
								<option value="'.listing_time_format(null,'15:00').'">'.listing_time_format('15:00',null).'</option>
								<option value="'.listing_time_format(null,'15:30').'">'.listing_time_format('15:30',null).'</option>
								<option value="'.listing_time_format(null,'16:00').'">'.listing_time_format('16:00',null).'</option>
								<option value="'.listing_time_format(null,'16:30').'">'.listing_time_format('16:30',null).'</option>
								<option value="'.listing_time_format(null,'17:00').'">'.listing_time_format('17:00',null).'</option>
								<option value="'.listing_time_format(null,'17:30').'">'.listing_time_format('17:30',null).'</option>
								<option value="'.listing_time_format(null,'18:00').'">'.listing_time_format('18:00',null).'</option>
								<option value="'.listing_time_format(null,'18:30').'">'.listing_time_format('18:30',null).'</option>
								<option value="'.listing_time_format(null,'19:00').'">'.listing_time_format('19:00',null).'</option>
								<option value="'.listing_time_format(null,'19:30').'">'.listing_time_format('19:30',null).'</option>
								<option value="'.listing_time_format(null,'20:00').'">'.listing_time_format('20:00',null).'</option>
								<option value="'.listing_time_format(null,'20:30').'">'.listing_time_format('20:30',null).'</option>
								<option value="'.listing_time_format(null,'21:00').'">'.listing_time_format('21:00',null).'</option>
								<option value="'.listing_time_format(null,'21:30').'">'.listing_time_format('21:30',null).'</option>
								<option value="'.listing_time_format(null,'22:00').'">'.listing_time_format('22:00',null).'</option>
								<option value="'.listing_time_format(null,'22:30').'">'.listing_time_format('22:30',null).'</option>
								<option value="'.listing_time_format(null,'23:00').'">'.listing_time_format('23:00',null).'</option>
								<option value="'.listing_time_format(null,'23:30').'">'.listing_time_format('23:30',null).'</option>
							</select>
						</li>
						<li>
							<select class="hours-end select2">
								<option value="'.listing_time_format(null,'24:00').'">'.listing_time_format('24:00',null).' </option>
								<option value="'.listing_time_format(null,'24:30').'">'.listing_time_format('24:30',null).'</option>
								<option value="'.listing_time_format(null,'01:00').'">'.listing_time_format('01:00',null).'</option>
								<option value="'.listing_time_format(null,'01:30').'">'.listing_time_format('01:30',null).'</option>
								<option value="'.listing_time_format(null,'02:00').'">'.listing_time_format('02:00',null).'</option>
								<option value="'.listing_time_format(null,'02:30').'">'.listing_time_format('02:30',null).'</option>
								<option value="'.listing_time_format(null,'03:00').'">'.listing_time_format('03:00',null).'</option>
								<option value="'.listing_time_format(null,'03:30').'">'.listing_time_format('03:30',null).'</option>
								<option value="'.listing_time_format(null,'04:00').'">'.listing_time_format('04:00',null).'</option>
								<option value="'.listing_time_format(null,'04:30').'">'.listing_time_format('04:30',null).'</option>
								<option value="'.listing_time_format(null,'05:00').'">'.listing_time_format('05:00',null).'</option>
								<option value="'.listing_time_format(null,'05:30').'">'.listing_time_format('05:30',null).'</option>
								<option value="'.listing_time_format(null,'06:00').'">'.listing_time_format('06:00',null).'</option>
								<option value="'.listing_time_format(null,'06:30').'">'.listing_time_format('06:30',null).'</option>
								<option value="'.listing_time_format(null,'07:00').'">'.listing_time_format('07:00',null).'</option>
								<option value="'.listing_time_format(null,'07:30').'">'.listing_time_format('07:30',null).'</option>
								<option value="'.listing_time_format(null,'08:00').'">'.listing_time_format('08:00',null).'</option>
								<option value="'.listing_time_format(null,'08:30').'">'.listing_time_format('08:30',null).'</option>
								<option value="'.listing_time_format(null,'09:00').'">'.listing_time_format('09:00',null).'</option>
								<option value="'.listing_time_format(null,'09:30').'">'.listing_time_format('09:30',null).'</option>
								<option value="'.listing_time_format(null,'10:00').'">'.listing_time_format('10:00',null).'</option>
								<option value="'.listing_time_format(null,'10:30').'">'.listing_time_format('10:30',null).'</option>
								<option value="'.listing_time_format(null,'11:00').'">'.listing_time_format('11:00',null).'</option>
								<option value="'.listing_time_format(null,'11:30').'">'.listing_time_format('11:30',null).'</option>
								<option value="'.listing_time_format(null,'12:00').'">'.listing_time_format('12:00',null).'</option>
								<option value="'.listing_time_format(null,'12:30').'">'.listing_time_format('12:30',null).'</option>
								<option value="'.listing_time_format(null,'13:00').'">'.listing_time_format('13:00',null).'</option>
								<option value="'.listing_time_format(null,'13:30').'">'.listing_time_format('13:30',null).'</option>
								<option value="'.listing_time_format(null,'14:00').'">'.listing_time_format('14:00',null).'</option>
								<option value="'.listing_time_format(null,'14:30').'">'.listing_time_format('14:30',null).'</option>
								<option value="'.listing_time_format(null,'15:00').'">'.listing_time_format('15:00',null).'</option>
								<option value="'.listing_time_format(null,'15:30').'">'.listing_time_format('15:30',null).'</option>
								<option value="'.listing_time_format(null,'16:00').'">'.listing_time_format('16:00',null).'</option>
								<option value="'.listing_time_format(null,'16:30').'">'.listing_time_format('16:30',null).'</option>
								<option value="'.listing_time_format(null,'17:00').'" selected="">'.listing_time_format('17:00',null).'</option>
								<option value="'.listing_time_format(null,'17:30').'">'.listing_time_format('17:30',null).'</option>
								<option value="'.listing_time_format(null,'18:00').'">'.listing_time_format('18:00',null).'</option>
								<option value="'.listing_time_format(null,'18:30').'">'.listing_time_format('18:30',null).'</option>
								<option value="'.listing_time_format(null,'19:00').'">'.listing_time_format('19:00',null).'</option>
								<option value="'.listing_time_format(null,'19:30').'">'.listing_time_format('19:30',null).'</option>
								<option value="'.listing_time_format(null,'20:00').'">'.listing_time_format('20:00',null).'</option>
								<option value="'.listing_time_format(null,'20:30').'">'.listing_time_format('20:30',null).'</option>
								<option value="'.listing_time_format(null,'21:00').'">'.listing_time_format('21:00',null).'</option>
								<option value="'.listing_time_format(null,'21:30').'">'.listing_time_format('21:30',null).'</option>
								<option value="'.listing_time_format(null,'22:00').'">'.listing_time_format('22:00',null).'</option>
								<option value="'.listing_time_format(null,'22:30').'">'.listing_time_format('22:30',null).'</option>
								<option value="'.listing_time_format(null,'23:00').'">'.listing_time_format('23:00',null).'</option>
								<option value="'.listing_time_format(null,'23:30').'">'.listing_time_format('23:30',null).'</option>
							</select>
							
						</li>
							
						<li>
							<div class="checkbox form-group fulldayopen-wrap">
								<input type="checkbox" name="fulldayopen" id="fulldayopen" class="fulldayopen">
								<label for="fulldayopen">'.esc_html__('24 Hours' ,'listingpro-plugin').'</label>
							</div>
							<button data-fullday = "'.esc_html__('24 hours open', 'listingpro-plugin').'" data-remove="'.$removeData.'" data-sorrymsg="'.esc_html__('Sorry','listingpro-plugin').'" data-alreadyadded="'.esc_html__('Already Added', 'listingpro-plugin').'" type="button" value="submit" class="ybtn ybtn--small add-hours '.$add_hour_st.'">';
								$output .='<span><i class="fa fa-plus-square"></i> </span>';
							$output .='</button>
						</li>
						';
						
						if($listing2timeslots=="enable"){
							$output .='
							<div class="lp-check-doubletime checkbox form-group ">
								<input type="checkbox" name="enable2ndday" id="enable2ndday" class="enable2ndday">
								<label for="enable2ndday">'.esc_html__('Add 2nd time slot?' ,'listingpro-plugin').'</label>
							</div>
							<ul class="hours-select clearfix inline-layout up-4 lp-slot2-time">
								
								<li>
									<select class="hours-start2 select2">
										<option value="'.listing_time_format(null,'24:00').'">'.listing_time_format('24:00',null).' </option>
										<option value="'.listing_time_format(null,'24:30').'">'.listing_time_format('24:30',null).'</option>
										<option value="'.listing_time_format(null,'01:00').'">'.listing_time_format('01:00',null).'</option>
										<option value="'.listing_time_format(null,'01:30').'">'.listing_time_format('01:30',null).'</option>
										<option value="'.listing_time_format(null,'02:00').'">'.listing_time_format('02:00',null).'</option>
										<option value="'.listing_time_format(null,'02:30').'">'.listing_time_format('02:30',null).'</option>
										<option value="'.listing_time_format(null,'03:00').'">'.listing_time_format('03:00',null).'</option>
										<option value="'.listing_time_format(null,'03:30').'">'.listing_time_format('03:30',null).'</option>
										<option value="'.listing_time_format(null,'04:00').'">'.listing_time_format('04:00',null).'</option>
										<option value="'.listing_time_format(null,'04:30').'">'.listing_time_format('04:30',null).'</option>
										<option value="'.listing_time_format(null,'05:00').'">'.listing_time_format('05:00',null).'</option>
										<option value="'.listing_time_format(null,'05:30').'">'.listing_time_format('05:30',null).'</option>
										<option value="'.listing_time_format(null,'06:00').'">'.listing_time_format('06:00',null).'</option>
										<option value="'.listing_time_format(null,'06:30').'">'.listing_time_format('06:30',null).'</option>
										<option value="'.listing_time_format(null,'07:00').'">'.listing_time_format('07:00',null).'</option>
										<option value="'.listing_time_format(null,'07:30').'">'.listing_time_format('07:30',null).'</option>
										<option value="'.listing_time_format(null,'08:00').'">'.listing_time_format('08:00',null).'</option>
										<option value="'.listing_time_format(null,'08:30').'">'.listing_time_format('08:30',null).'</option>
										<option value="'.listing_time_format(null,'09:00').'" selected="">'.listing_time_format('09:00',null).'</option>
										<option value="'.listing_time_format(null,'09:30').'">'.listing_time_format('09:30',null).'</option>
										<option value="'.listing_time_format(null,'10:00').'">'.listing_time_format('10:00',null).'</option>
										<option value="'.listing_time_format(null,'10:30').'">'.listing_time_format('10:30',null).'</option>
										<option value="'.listing_time_format(null,'11:00').'">'.listing_time_format('11:00',null).'</option>
										<option value="'.listing_time_format(null,'11:30').'">'.listing_time_format('11:30',null).'</option>
										<option value="'.listing_time_format(null,'12:00').'">'.listing_time_format('12:00',null).'</option>
										<option value="'.listing_time_format(null,'12:30').'">'.listing_time_format('12:30',null).'</option>
										<option value="'.listing_time_format(null,'13:00').'">'.listing_time_format('13:00',null).'</option>
										<option value="'.listing_time_format(null,'13:30').'">'.listing_time_format('13:30',null).'</option>
										<option value="'.listing_time_format(null,'14:00').'">'.listing_time_format('14:00',null).'</option>
										<option value="'.listing_time_format(null,'14:30').'">'.listing_time_format('14:30',null).'</option>
										<option value="'.listing_time_format(null,'15:00').'">'.listing_time_format('15:00',null).'</option>
										<option value="'.listing_time_format(null,'15:30').'">'.listing_time_format('15:30',null).'</option>
										<option value="'.listing_time_format(null,'16:00').'">'.listing_time_format('16:00',null).'</option>
										<option value="'.listing_time_format(null,'16:30').'">'.listing_time_format('16:30',null).'</option>
										<option value="'.listing_time_format(null,'17:00').'">'.listing_time_format('17:00',null).'</option>
										<option value="'.listing_time_format(null,'17:30').'">'.listing_time_format('17:30',null).'</option>
										<option value="'.listing_time_format(null,'18:00').'">'.listing_time_format('18:00',null).'</option>
										<option value="'.listing_time_format(null,'18:30').'">'.listing_time_format('18:30',null).'</option>
										<option value="'.listing_time_format(null,'19:00').'">'.listing_time_format('19:00',null).'</option>
										<option value="'.listing_time_format(null,'19:30').'">'.listing_time_format('19:30',null).'</option>
										<option value="'.listing_time_format(null,'20:00').'">'.listing_time_format('20:00',null).'</option>
										<option value="'.listing_time_format(null,'20:30').'">'.listing_time_format('20:30',null).'</option>
										<option value="'.listing_time_format(null,'21:00').'">'.listing_time_format('21:00',null).'</option>
										<option value="'.listing_time_format(null,'21:30').'">'.listing_time_format('21:30',null).'</option>
										<option value="'.listing_time_format(null,'22:00').'">'.listing_time_format('22:00',null).'</option>
										<option value="'.listing_time_format(null,'22:30').'">'.listing_time_format('22:30',null).'</option>
										<option value="'.listing_time_format(null,'23:00').'">'.listing_time_format('23:00',null).'</option>
										<option value="'.listing_time_format(null,'23:30').'">'.listing_time_format('23:30',null).'</option>
									</select>
								</li>
								<li>
									<select class="hours-end2 select2">
										<option value="'.listing_time_format(null,'24:00').'">'.listing_time_format('24:00',null).' </option>
										<option value="'.listing_time_format(null,'24:30').'">'.listing_time_format('24:30',null).'</option>
										<option value="'.listing_time_format(null,'01:00').'">'.listing_time_format('01:00',null).'</option>
										<option value="'.listing_time_format(null,'01:30').'">'.listing_time_format('01:30',null).'</option>
										<option value="'.listing_time_format(null,'02:00').'">'.listing_time_format('02:00',null).'</option>
										<option value="'.listing_time_format(null,'02:30').'">'.listing_time_format('02:30',null).'</option>
										<option value="'.listing_time_format(null,'03:00').'">'.listing_time_format('03:00',null).'</option>
										<option value="'.listing_time_format(null,'03:30').'">'.listing_time_format('03:30',null).'</option>
										<option value="'.listing_time_format(null,'04:00').'">'.listing_time_format('04:00',null).'</option>
										<option value="'.listing_time_format(null,'04:30').'">'.listing_time_format('04:30',null).'</option>
										<option value="'.listing_time_format(null,'05:00').'">'.listing_time_format('05:00',null).'</option>
										<option value="'.listing_time_format(null,'05:30').'">'.listing_time_format('05:30',null).'</option>
										<option value="'.listing_time_format(null,'06:00').'">'.listing_time_format('06:00',null).'</option>
										<option value="'.listing_time_format(null,'06:30').'">'.listing_time_format('06:30',null).'</option>
										<option value="'.listing_time_format(null,'07:00').'">'.listing_time_format('07:00',null).'</option>
										<option value="'.listing_time_format(null,'07:30').'">'.listing_time_format('07:30',null).'</option>
										<option value="'.listing_time_format(null,'08:00').'">'.listing_time_format('08:00',null).'</option>
										<option value="'.listing_time_format(null,'08:30').'">'.listing_time_format('08:30',null).'</option>
										<option value="'.listing_time_format(null,'09:00').'">'.listing_time_format('09:00',null).'</option>
										<option value="'.listing_time_format(null,'09:30').'">'.listing_time_format('09:30',null).'</option>
										<option value="'.listing_time_format(null,'10:00').'">'.listing_time_format('10:00',null).'</option>
										<option value="'.listing_time_format(null,'10:30').'">'.listing_time_format('10:30',null).'</option>
										<option value="'.listing_time_format(null,'11:00').'">'.listing_time_format('11:00',null).'</option>
										<option value="'.listing_time_format(null,'11:30').'">'.listing_time_format('11:30',null).'</option>
										<option value="'.listing_time_format(null,'12:00').'">'.listing_time_format('12:00',null).'</option>
										<option value="'.listing_time_format(null,'12:30').'">'.listing_time_format('12:30',null).'</option>
										<option value="'.listing_time_format(null,'13:00').'">'.listing_time_format('13:00',null).'</option>
										<option value="'.listing_time_format(null,'13:30').'">'.listing_time_format('13:30',null).'</option>
										<option value="'.listing_time_format(null,'14:00').'">'.listing_time_format('14:00',null).'</option>
										<option value="'.listing_time_format(null,'14:30').'">'.listing_time_format('14:30',null).'</option>
										<option value="'.listing_time_format(null,'15:00').'">'.listing_time_format('15:00',null).'</option>
										<option value="'.listing_time_format(null,'15:30').'">'.listing_time_format('15:30',null).'</option>
										<option value="'.listing_time_format(null,'16:00').'">'.listing_time_format('16:00',null).'</option>
										<option value="'.listing_time_format(null,'16:30').'">'.listing_time_format('16:30',null).'</option>
										<option value="'.listing_time_format(null,'17:00').'" selected="">'.listing_time_format('17:00',null).'</option>
										<option value="'.listing_time_format(null,'17:30').'">'.listing_time_format('17:30',null).'</option>
										<option value="'.listing_time_format(null,'18:00').'">'.listing_time_format('18:00',null).'</option>
										<option value="'.listing_time_format(null,'18:30').'">'.listing_time_format('18:30',null).'</option>
										<option value="'.listing_time_format(null,'19:00').'">'.listing_time_format('19:00',null).'</option>
										<option value="'.listing_time_format(null,'19:30').'">'.listing_time_format('19:30',null).'</option>
										<option value="'.listing_time_format(null,'20:00').'">'.listing_time_format('20:00',null).'</option>
										<option value="'.listing_time_format(null,'20:30').'">'.listing_time_format('20:30',null).'</option>
										<option value="'.listing_time_format(null,'21:00').'">'.listing_time_format('21:00',null).'</option>
										<option value="'.listing_time_format(null,'21:30').'">'.listing_time_format('21:30',null).'</option>
										<option value="'.listing_time_format(null,'22:00').'">'.listing_time_format('22:00',null).'</option>
										<option value="'.listing_time_format(null,'22:30').'">'.listing_time_format('22:30',null).'</option>
										<option value="'.listing_time_format(null,'23:00').'">'.listing_time_format('23:00',null).'</option>
										<option value="'.listing_time_format(null,'23:30').'">'.listing_time_format('23:30',null).'</option>
									</select>
									
								</li>
								<li></li>
							</ul>';
						}
						
				$output .='
					</ul>
				</div>
				

			</div>';
	
		return $output;
	}
}