<?php
if (!defined('ABSPATH')) exit;

function lk_price_status_labels() {
 return array('notsay'=>'Không công khai','inexpensive'=>'₫ - Bình dân','moderate'=>'₫₫ - Vừa phải','pricey'=>'₫₫₫ - Khá cao','ultra_high_end'=>'₫₫₫₫ - Cao cấp');
}
function lk_price_status_select($name, $value = 'notsay', $label = 'Thông tin giá') {
 echo '<label class="lk-price-status-select">'.esc_html($label).'<select name="'.esc_attr($name).'">';
 foreach (lk_price_status_labels() as $key=>$label) echo '<option value="'.$key.'" '.selected($value ?: 'notsay',$key,false).'>'.esc_html($label).'</option>';
 echo '</select></label>';
}
function lk_weekdays() {
 return array('Monday'=>'Thứ Hai','Tuesday'=>'Thứ Ba','Wednesday'=>'Thứ Tư','Thursday'=>'Thứ Năm','Friday'=>'Thứ Sáu','Saturday'=>'Thứ Bảy','Sunday'=>'Chủ nhật');
}
function lk_valid_time($value) {
 if (!is_scalar($value) || !preg_match('/^(\d{1,2}):(\d{2})\s*(am|pm)?$/i',trim((string)$value),$matches)) return null;
 $hour=(int)$matches[1];$minute=(int)$matches[2];$period=strtolower($matches[3]??'');
 if ($minute>59 || ($period ? ($hour<1 || $hour>12) : $hour>23)) return null;
 if ($period) $hour=$hour%12+($period==='pm'?12:0);
 return sprintf('%02d:%02d',$hour,$minute);
}
function lk_legacy_schedule($open='07:00',$close='22:00') {
 $open=lk_valid_time($open)??'07:00';$close=lk_valid_time($close)??'22:00';$schedule=array();
 foreach(lk_weekdays() as $day=>$label) $schedule[$day]=array('mode'=>$open===$close?'24h':'open','slots'=>array(array('open'=>$open,'close'=>$close)));
 return $schedule;
}
function lk_read_schedule($options,$id=0) {
 $raw=$options['business_hours']??null;
 if (!is_array($raw) || (!$raw && empty($options['lk_hours_defined']))) return lk_legacy_schedule($id?get_post_meta($id,'_lk_open',true):'07:00',$id?get_post_meta($id,'_lk_close',true):'22:00');
 $schedule=array();
 foreach(lk_weekdays() as $day=>$label) {
  $row=$raw[$day]??$raw[$label]??null;$slots=array();$mode='closed';
  if(is_array($row)) {
   $opens=(array)($row['open']??'');$closes=(array)($row['close']??'');
   foreach($opens as $index=>$open) {
    $close=$closes[$index]??null;
    if ($open==='' && $close==='') {$mode='24h';break;}
    $open=lk_valid_time($open);$close=lk_valid_time($close);
    if($open!==null && $close!==null) {if($open===$close){$mode='24h';break;}$slots[]=array('open'=>$open,'close'=>$close);$mode='open';}
   }
  }
  $schedule[$day]=array('mode'=>$mode,'slots'=>$slots);
 }
 return $schedule;
}

// Parse both frontend and admin fields before writing anything to listing data.
function lk_parse_schedule($raw) {
 if(!is_array($raw)) return new WP_Error('hours','Vui lòng nhập lịch mở cửa theo từng thứ.');
 $schedule=array();
 foreach(lk_weekdays() as $day=>$label) {
  $row=$raw[$day]??null;$mode=is_array($row)&&is_scalar($row['mode']??null)?(string)$row['mode']:'';
  if(!in_array($mode,array('open','closed','24h'),true)) return new WP_Error('hours','Vui lòng chọn trạng thái cho '.$label.'.');
  $slots=array();
  if($mode==='open') {
   $opens=$row['open']??array();$closes=$row['close']??array();
   if(!is_array($opens)||!is_array($closes)||!count($opens)||count($opens)>3||count($opens)!==count($closes)) return new WP_Error('hours',$label.' cần từ 1 đến 3 khung giờ.');
   foreach(array_values($opens) as $index=>$open) {
    $close=array_values($closes)[$index];$open=lk_valid_time($open);$close=lk_valid_time($close);
    if($open===null||$close===null||$open===$close) return new WP_Error('hours','Giờ mở và đóng của '.$label.' chưa hợp lệ. Nếu mở cả ngày, hãy chọn Mở 24 giờ.');
    $slots[]=array('open'=>$open,'close'=>$close);
   }
   usort($slots,function($a,$b){return strcmp($a['open'],$b['open']);});
  }
  $schedule[$day]=array('mode'=>$mode,'slots'=>$slots);
 }
 // Compare intervals on a weekly clock, including overnight and Sunday → Monday.
 $intervals=array();$days=array_keys(lk_weekdays());
 foreach($schedule as $day=>$row) {
  $offset=array_search($day,$days,true)*1440;
  if($row['mode']==='24h')$intervals[]=array($offset,$offset+1440);
  foreach($row['mode']==='open'?$row['slots']:array() as $slot) {
   $start=lk_time_minutes($slot['open']);$end=lk_time_minutes($slot['close']);if($end<$start)$end+=1440;
   $intervals[]=array($offset+$start,$offset+$end);
  }
 }
 $repeated=$intervals;foreach($intervals as $interval)$repeated[]=array($interval[0]+10080,$interval[1]+10080);
 usort($repeated,function($a,$b){return $a[0]<=>$b[0];});
 for($i=1;$i<count($repeated);$i++)if($repeated[$i][0]<$repeated[$i-1][1])return new WP_Error('hours','Các khung giờ đang trùng nhau, kể cả khung giờ qua nửa đêm. Vui lòng kiểm tra lại.');
 return $schedule;
}
function lk_time_minutes($time) {$parts=explode(':',$time);return (int)$parts[0]*60+(int)$parts[1];}
function lk_schedule_options($schedule) {
 $hours=array();
 foreach($schedule as $day=>$row) {
  if($row['mode']==='closed')continue;
  if($row['mode']==='24h'){$hours[$day]=array('open'=>'','close'=>'');continue;}
  if(count($row['slots'])===1)$hours[$day]=$row['slots'][0];
  else $hours[$day]=array('open'=>array_column($row['slots'],'open'),'close'=>array_column($row['slots'],'close'));
 }
 return array('business_hours'=>$hours,'lk_hours_defined'=>1);
}
function lk_save_legacy_hours($id,$schedule) {
 $open='';$close='';
 foreach($schedule as $row) {
  if($row['mode']==='24h'){$open='00:00';$close='00:00';break;}
  if($row['mode']==='open'){$open=$row['slots'][0]['open'];$close=$row['slots'][0]['close'];break;}
 }
 update_post_meta($id,'_lk_open',$open);update_post_meta($id,'_lk_close',$close);
}
function lk_day_hours_label($row) {
 if($row['mode']==='closed')return 'Nghỉ';
 if($row['mode']==='24h')return 'Mở 24 giờ';
 return implode(', ',array_map(function($slot){return $slot['open'].' – '.$slot['close'].($slot['close']<$slot['open']?' (qua ngày)':'');},$row['slots']));
}
function lk_schedule_status($schedule,$now=null) {
 $now=$now?:current_datetime();$day=$now->format('l');$today=$schedule[$day];
 $previous=$schedule[$now->modify('-1 day')->format('l')];$minute=(int)$now->format('H')*60+(int)$now->format('i');
 $first=$today['slots'][0]??array('open'=>'','close'=>'');
 $status=array('open'=>$first['open'],'close'=>$first['close'],'opened'=>false,'hoursLabel'=>$today['mode']==='closed'?'Nghỉ hôm nay':lk_day_hours_label($today));
 if($previous['mode']==='open')foreach($previous['slots'] as $slot)if($slot['close']<$slot['open']&&$minute<lk_time_minutes($slot['close']))return array('open'=>$slot['open'],'close'=>$slot['close'],'opened'=>true,'hoursLabel'=>lk_day_hours_label(array('mode'=>'open','slots'=>array($slot))));
 if($today['mode']==='24h')return array('open'=>'00:00','close'=>'00:00','opened'=>true,'hoursLabel'=>'Mở 24 giờ');
 if($today['mode']==='open')foreach($today['slots'] as $slot) {
  $start=lk_time_minutes($slot['open']);$end=lk_time_minutes($slot['close']);
  if($minute>=$start&&($end<$start||$minute<$end))return array('open'=>$slot['open'],'close'=>$slot['close'],'opened'=>true,'hoursLabel'=>lk_day_hours_label(array('mode'=>'open','slots'=>array($slot))));
 }
 return $status;
}

function lk_schedule_editor($schedule=null,$root='hours',$settings=array()) {
 $schedule=$schedule?:lk_legacy_schedule();
 echo '<fieldset class="lk-hours-editor"><legend>'.esc_html($settings['hours_title']??'Giờ mở cửa theo thứ').'</legend><p class="lk-hours-help">Giờ đóng sớm hơn giờ mở được tính sang ngày kế tiếp. Có thể thêm tối đa 3 khung giờ mỗi ngày.</p>';
 foreach(lk_weekdays() as $day=>$label) {
  $row=$schedule[$day];$slots=$row['slots']?:array(array('open'=>'07:00','close'=>'22:00'));$prefix=$root.'['.$day.']';
  echo '<div class="lk-hours-day" data-hours-root="'.esc_attr($prefix).'" data-day-label="'.esc_attr($label).'"><div class="lk-hours-day-heading"><strong>'.esc_html($label).'</strong><label><span class="lk-hours-sr">Trạng thái '.esc_html($label).'</span><select name="'.esc_attr($prefix).'[mode]" data-hours-mode>';
  foreach(array('open'=>'Theo khung giờ','closed'=>'Nghỉ','24h'=>'Mở 24 giờ') as $mode=>$text)echo '<option value="'.$mode.'" '.selected($row['mode'],$mode,false).'>'.$text.'</option>';
  echo '</select></label></div><div class="lk-hours-slots"'.($row['mode']!=='open'?' hidden':'').'>';
  foreach($slots as $index=>$slot) {
   echo '<div class="lk-hours-slot">';
   foreach(array('open'=>'Mở cửa','close'=>'Đóng cửa') as $field=>$text)echo '<label>'.esc_html($text).'<input type="time" data-hours-time="'.$field.'" name="'.esc_attr($prefix).'['.$field.']['.$index.']" value="'.esc_attr($slot[$field]).'" aria-label="'.esc_attr($text.' '.$label.' khung '.($index+1)).'" '.($row['mode']==='open'?'required':'disabled').'></label>';
   echo '<button type="button" class="lk-hours-remove" aria-label="'.esc_attr('Xóa khung giờ '.$label).'"'.(count($slots)===1?' disabled':'').'>×</button></div>';
  }
  echo '<button type="button" class="lk-hours-add">+ Thêm khung giờ</button></div></div>';
 }
 echo '<button type="button" class="lk-hours-copy">Áp dụng giờ Thứ Hai cho cả tuần</button><p class="lk-hours-error" role="alert" hidden></p></fieldset>';
}
function lk_schedule_details($data) {
 $html='<details class="lk-weekly-hours"><summary>'.lk_icon('clock').'<span><strong class="'.($data['opened']?'is-open':'is-closed').'">'.($data['opened']?'Đang mở cửa':'Đang đóng cửa').'</strong> · '.esc_html($data['hoursLabel']).'</span>'.lk_icon('chevron-down').'</summary><dl>';
 $today=current_datetime()->format('l');
 foreach(lk_weekdays() as $day=>$label)$html.='<div'.($day===$today?' class="is-today"':'').'><dt>'.esc_html($label).'</dt><dd>'.esc_html(lk_day_hours_label($data['schedule'][$day])).'</dd></div>';
 return $html.'</dl></details>';
}
