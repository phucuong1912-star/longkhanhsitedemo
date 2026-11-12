<?php
/* ============== Check TIme ============ */
if (!function_exists('listingpro_check_time')) {
    function listingpro_check_time($postid, $status = false)
    {
        global $listingpro_options;
        $listing_layout = $listingpro_options['listing_views'];
        $output = '';
        $buisness_hours = listing_get_metabox_by_ID('business_hours', $postid);
        $buisness_hours_new = array();
        if (!empty($buisness_hours) && is_array($buisness_hours)) {
            foreach ($buisness_hours as $key => $value) {
                $dayStr = strtotime($key);
                if (empty($dayStr)){
                    $buisness_hours_new[$key] = $value;
                }else{
                    $langDay = date_i18n("l", $dayStr);
                    $buisness_hours_new[$langDay] = $value;
                }
            }
        }
        $buisness_hours = $buisness_hours_new;
        if (!empty($buisness_hours) && count($buisness_hours) > 0) {
            /**
             * $dynTimeZone
             * Change Into true If You Want To Enable Dynamic TimeZone
             **/
            $dynTimeZone = false;
            $timezone = get_option('gmt_offset');
            if ($dynTimeZone) {
                $lat = listing_get_metabox('latitude');
                $long = listing_get_metabox('longitude');
                $timezone = getClosestTimezone($lat, $long);
            }
            $time = gmdate("H:i", time() + 3600 * ($timezone + date("I")));
            $day = gmdate("l");
            $time = strtotime($time);
            $lang = get_locale();
            setlocale(LC_ALL, $lang . '.utf-8');
            // $day = strftime("%A");
            $day = date_i18n('l', strtotime('11/15-1976'));
            $day = ucfirst($day);
            $newTimeOpen;
            $newTimeClose;
            $newTimeOpen1;
            $newTimeClose1;
            $midNight = strtotime('12:00 am');
            $timeOfOpenClose = array();
            $statusOfTime = '';
            $todayTimeStatus = array();
            $dayIsOff = true;
            if (isset($buisness_hours[$day])) {
                $dayIsOff = false;
                array_push($todayTimeStatus, $buisness_hours[$day]);
            }
            foreach ($buisness_hours as $key => $value) {
                $exp_key = array();
                if ((strpos($key, $day . "-") !== false || strpos($key, "-" . $day) !== false)) {
                    if (strpos($key, $day . "-") !== false) {
                        $dayIsOff = false;
                    }
                    $exp_key = explode('-', $key);
                } elseif ((strpos($key, $day . "~") !== false || strpos($key, "~" . $day) !== false)) {
                    if (strpos($key, $day . "~") !== false) {
                        $dayIsOff = false;
                    }
                    $exp_key = explode('~', $key);
                }
                if (!empty($exp_key)) {
                    array_push($todayTimeStatus, $buisness_hours[$key]);
                }
            }
            if (!empty($todayTimeStatus)) {
                if (count($todayTimeStatus) > 1) {
                    if (is_array($todayTimeStatus[0]['close']) && is_array($todayTimeStatus[0]['open'])) {
                        $openTime1 = null;
                        $closeTime1 = null;
                        $openTime2 = null;
                        $closeTime2 = null;
                        $openTime3 = null;
                        $closeTime3 = null;
                        $thirdTime = false;
                        $openTime1 = $todayTimeStatus[0]['open'][0];
                        $openTime1 = str_replace(' ', '', $openTime1);
                        $closeTime1 = $todayTimeStatus[0]['close'][0];
                        $closeTime1 = str_replace(' ', '', $closeTime1);
                        $openTime1 = strtotime($openTime1);
                        $closeTime1 = strtotime($closeTime1);
                        $openTime2 = $todayTimeStatus[1]['open'][1];
                        $openTime2 = str_replace(' ', '', $openTime2);
                        $closeTime2 = $todayTimeStatus[1]['close'][1];
                        $closeTime2 = str_replace(' ', '', $closeTime2);
                        if ($closeTime2 == '00:00') {
                            $closeTime2 = '2400';
                        }
                        $openTime2 = strtotime($openTime2);
                        $closeTime2 = strtotime($closeTime2);
                        if (isset($todayTimeStatus[2]['close']) && isset($todayTimeStatus[2]['open'])) {
                            //possibly third time in same day
                            $openTime3 = $todayTimeStatus[2]['close'][1];
                            $openTime3 = str_replace(' ', '', $openTime3);
                            $closeTime3 = $todayTimeStatus[2]['close'][1];
                            $closeTime3 = str_replace(' ', '', $closeTime3);
                            if ($closeTime3 == '00:00') {
                                $closeTime3 = '2400';
                            }
                            $thirdTime = true;
                            $openTime3 = strtotime($openTime3);
                            $closeTime3 = strtotime($closeTime3);
                        }
                        if ($time >= $openTime1 && $time <= $closeTime1) {
                            array_push($timeOfOpenClose, $todayTimeStatus[0]['open'][0]);
                            array_push($timeOfOpenClose, $todayTimeStatus[0]['close'][0]);
                        } elseif (empty($thirdTime)) {
                            if ($time > $midNight) {
                                //before midnight
                                if ($time >= $closeTime2) {
                                    array_push($timeOfOpenClose, $todayTimeStatus[1]['open'][1]);
                                    array_push($timeOfOpenClose, $todayTimeStatus[1]['close'][1]);
                                }
                            } else {
                                if ($time <= $closeTime2) {
                                    array_push($timeOfOpenClose, $todayTimeStatus[1]['open'][1]);
                                    array_push($timeOfOpenClose, $todayTimeStatus[1]['close'][1]);
                                }
                            }
                        } elseif (!empty($thirdTime)) {
                            if ($time <= $closeTime3) {
                                array_push($timeOfOpenClose, $todayTimeStatus[2]['open'][1]);
                                array_push($timeOfOpenClose, $todayTimeStatus[2]['close'][1]);
                            }
                        }
                    } else {
                        $closetime = $todayTimeStatus[0]['close'];
                        $closetime = str_replace(' ', '', $closetime);
                        $closetime = strtotime($closetime);
                        $opentime = $todayTimeStatus[0]['open'];
                        $opentime = str_replace(' ', '', $opentime);
                        $opentime = strtotime($opentime);
                        if (!empty($format) && $format == '24') {
                            $closetime = date(get_option('time_format'), $closetime);
                            $opentime = date(get_option('time_format'), $opentime);
                        } else {
                            $closetime = date(get_option('time_format'), $closetime);
                            $opentime = date(get_option('time_format'), $opentime);
                        }
                        if ($closetime == '00:00') {
                            $closetime = '2400';
                        }
                        $closetime = strtotime($closetime);
                        $opentime = strtotime($opentime);
                        if ($time > $opentime) {
                            array_push($timeOfOpenClose, $todayTimeStatus[0]['open']);
                            array_push($timeOfOpenClose, $todayTimeStatus[0]['close']);
                        } else {
                            array_push($timeOfOpenClose, $todayTimeStatus[1]['open']);
                            array_push($timeOfOpenClose, $todayTimeStatus[1]['close']);
                        }
                    }
                } else {
                    //single day
                    if (is_array($todayTimeStatus[0]['close'])) {
                        $closetime = '';
                        $onlySecondDay = false;
                        if (isset($todayTimeStatus[0]['close'][0])) {
                            $closetime = $todayTimeStatus[0]['close'][0];
                        }
                        $closetime = str_replace(' ', '', $closetime);
                        if ($closetime == '00:00') {
                            $closetime = '2400';
                        }
                        $closetime = strtotime($closetime);
                        if (!empty($format) && $format == '24') {
                            $closetime = date(get_option('time_format'), $closetime);
                        } else {
                            $closetime = date(get_option('time_format'), $closetime);
                        }
                        $closetime = strtotime($closetime);
                        if ($time > $closetime) {
                            $onlySecondDay = true;
                        }
                        if (!empty($onlySecondDay)) {
                            if (isset($todayTimeStatus[0]['open'][1]) && isset($todayTimeStatus[0]['close'][1])) {
                                array_push($timeOfOpenClose, $todayTimeStatus[0]['open'][1]);
                                array_push($timeOfOpenClose, $todayTimeStatus[0]['close'][1]);
                            }
                        } else {
                            array_push($timeOfOpenClose, $todayTimeStatus[0]['open'][0]);
                            array_push($timeOfOpenClose, $todayTimeStatus[0]['close'][0]);
                        }
                    } else {
                        array_push($timeOfOpenClose, $todayTimeStatus[0]['open']);
                        array_push($timeOfOpenClose, $todayTimeStatus[0]['close']);
                    }
                }
            }
            if (!empty($timeOfOpenClose)) {
                // time exists for this day, not day off
                $openallTIme = false;
                $dayofComplete = false;
                $opentime = str_replace(' ', '', $timeOfOpenClose[0]);
                $closetime = str_replace(' ', '', $timeOfOpenClose[1]);
                if (empty($opentime) && empty($closetime)) {
                    $openallTIme = true;
                }
                if (strtotime($closetime) < strtotime($opentime)) {
                    $closetime = strtotime($closetime);
                    $opentime = strtotime($opentime);
                } else {
                    $closetime = strtotime($closetime);
                    $opentime = strtotime($opentime);
                }
                $timeINFORM = $time;
                if (!empty($format) && $format == '24') {
                    $closetime = date(get_option('time_format'), $closetime);
                    $opentime = date(get_option('time_format'), $opentime);
                    $timeINFORM = date(get_option('time_format'), $time);
                } else {
                    $closetime = date(get_option('time_format'), $closetime);
                    $opentime = date(get_option('time_format'), $opentime);
                    $timeINFORM = date(get_option('time_format'), $time);
                }
                if ($closetime == '00:00') {
                    $closetime = '2400';
                }
                $closetime = strtotime($closetime);
                $opentime = strtotime($opentime);
                if (!empty($openallTIme)) {
                    //24 hours open
                    $statusOfTime = '24hours';
                } elseif (strpos($timeINFORM, 'AM') == false || strpos($timeINFORM, 'PM') == false) {
                    if ($opentime < $closetime) {
                        if ($time >= $opentime && $time <= $closetime) {
                            $statusOfTime = 'opened';
                        } else {
                            $statusOfTime = 'closed';
                        }
                    } else {
                        if ($time >= $opentime || $time <= $closetime) {
                            $statusOfTime = 'opened';
                        } elseif ($time > $closetime) {
                            $statusOfTime = 'closed';
                        }
                    }
                } elseif (strpos($timeOfOpenClose[1], 'am') != false && strpos($timeINFORM, 'AM') != false) {
                    if ($time >= $opentime && $time <= $closetime) {
                        $statusOfTime = 'opened';
                    } else {
                        $statusOfTime = 'closed';
                    }
                } elseif (strpos($timeOfOpenClose[1], 'am') != false && strpos($timeINFORM, 'PM') != false) {
                    if ($time >= $opentime && $time >= $closetime) {
                        $statusOfTime = 'opened';
                    } else {
                        $statusOfTime = 'closed';
                    }
                } else {
                    if ($time >= $opentime && $time <= $closetime) {
                        $statusOfTime = 'opened';
                    } else {
                        $statusOfTime = 'closed';
                    }
                }
            } else {
                if (!empty($dayIsOff)) {
                    $statusOfTime = 'off';
                } else {
                    $statusOfTime = 'closed';
                }
            }
            // if (!isset($buisness_hours[$day])) {
            //     $statusOfTime = 'off';
            // }
            if ($statusOfTime == '24hours') {
                if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2') {
                    if ($status == false) {
                        $output = '<a title="24 hours open" class="lp-open-timing li-listing-clock-outer li-listing-clock green-tooltip status-green "><i class="fa fa-clock-o" aria-hidden="true"></i> ' . esc_html__('24 Hours Open', 'listingpro') . '</a>';
                    } else {
                        $output = 'open';
                    }
                } else {
                    if ($status == false) {
                        $output = '<span class="grid-opened li-listing-clock-outer status-green">' . esc_html__('24 hours open', 'listingpro') . '</span>';
                    } else {
                        $output = 'open';
                    }
                }
            } elseif ($statusOfTime == 'opened') {
                if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2') {
                    if ($status == false) {
                        $output = '<a title="Open Now" class="lp-open-timing li-listing-clock-outer li-listing-clock green-tooltip status-green "><i class="fa fa-clock-o" aria-hidden="true"></i> ' . esc_html__('Open Now', 'listingpro') . '</a>';
                    } else {
                        $output = 'open';
                    }
                } else {
                    if ($status == false) {
                        $output = '<span class="grid-opened li-listing-clock-outer status-green">' . esc_html__('Open Now', 'listingpro') . '</span>';
                    } else {
                        $output = 'open';
                    }
                }
            } elseif ($statusOfTime == 'closed') {
                if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2') {
                    if ($status == false) {
                        $output = '<a title="Closed Now~" class="lp-open-timing li-listing-clock-outer li-listing-clock red-tooltip status-red "><i class="fa fa-clock-o" aria-hidden="true"></i> ' . esc_html__('Closed Now', 'listingpro') . '</a>';
                    } else {
                        $output = 'close';
                    }
                } else {
                    if ($status == false) {
                        $output = '<span class="grid-closed status-red li-listing-clock-outer">' . esc_html__('Closed Now', 'listingpro') . '</span>';
                    } else {
                        $output = 'close';
                    }
                }
            } elseif ($statusOfTime == 'off') {
                if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2') {
                    if ($status == false) {
                        $output = '<a title="day off" class="lp-open-timing li-listing-clock-outer li-listing-clock red-tooltip status-red "><i class="fa fa-clock-o" aria-hidden="true"></i> ' . esc_html__('Day Off', 'listingpro') . '</a>';
                    } else {
                        $output = 'close';
                    }
                } else {
                    if ($status == false) {
                        $output = '<span class="grid-closed status-red li-listing-clock-outer">' . esc_html__('Day Off', 'listingpro') . '</span>';
                    } else {
                        $output = 'close';
                    }
                }
            }
            return $output;
        }
    }
}