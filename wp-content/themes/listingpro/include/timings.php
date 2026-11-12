<?php
global $listingpro_options;
$listing_style = $listingpro_options['lp_detail_page_styles'];
if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
?>
    <div class="open-hours">
    <?php
}
    ?>
    <!-- <h2><?php echo esc_html__('Opening Hours', 'listingpro'); ?></h2> -->
    <?php
    $listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
    $format = $listingpro_options['timing_option'];
    $buisness_hours = listing_get_metabox('business_hours');
    $buisness_hours_new = array();
    if (!empty($buisness_hours) && is_array($buisness_hours)) {
        foreach ($buisness_hours as $key => $value) {
            $dayStr = strtotime($key);
            if (empty($dayStr)) {
                $buisness_hours_new[$key] = $value;
            } else {
                $langDay = date('l', $dayStr);
                $buisness_hours_new[$langDay] = $value;
            }
        }
    }
    $buisness_hours = $buisness_hours_new;
    if (!empty($buisness_hours) && is_array($buisness_hours)) {
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
        $lang = get_locale();
        setlocale(LC_ALL, $lang . '.utf-8');
        // $day = strftime("%A");
        $day = date_i18n('l', strtotime('11/15-1976'));
        $day = ucfirst($day);
        $time = strtotime($time);
        $twodays = array();
        $todayOFF = true;
        $todayIsOpen = false;
        $todaycompleteopend = false;
        $newTimeOpen;
        $newTimeClose;
        $newTimeOpen1;
        $newTimeClose1;
        $totimesinaday = false;
        $midNight = strtotime('12:00 am');
        /* new code 10 june */
        $timeOfOpenClose = array();
        $statusOfTime = '';
        $todayTimeStatus = array();
        $dayIsOff = true;
        if (isset($buisness_hours[$day])) {
            //single day
            $dayIsOff = false;
            array_push($todayTimeStatus, $buisness_hours[$day]);
        }
        //2 days time
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
                //2 days time
                //2 times in single day
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
                        $closetime = date("H:i", $closetime);
                        $opentime = date("H:i", $opentime);
                    } else {
                        $closetime = date('h:i A', $closetime);
                        $opentime = date('h:i A', $opentime);
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
                        $closetime = date("H:i", $closetime);
                    } else {
                        $closetime = date('h:i A', $closetime);
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
                    //echo esc_attr( $closetime );
                    //echo 'other day time';
                } else {
                    //echo 'single day single time';
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
                $closetime = date("H:i", $closetime);
                $opentime = date("H:i", $opentime);
                $timeINFORM = date("H:i", $time);
            } else {
                $closetime = date('h:i A', $closetime);
                $opentime = date('h:i A', $opentime);
                $timeINFORM = date("h:i A", $time);
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
                    //close time on same day
                    if ($time >= $opentime && $time <= $closetime) {
                        $statusOfTime = 'opened';
                    } else {
                        $statusOfTime = 'closed';
                    }
                } else {
                    //close time on second day
                    if ($time >= $opentime || $time <= $closetime) {
                        $statusOfTime = 'opened';
                    } elseif ($time > $closetime) {
                        $statusOfTime = 'closed';
                    }
                }
            } elseif (strpos($timeOfOpenClose[1], 'am') != false && strpos($timeINFORM, 'AM') != false) {
                //both time and close time are in am
                if ($time >= $opentime && $time <= $closetime) {
                    $statusOfTime = 'opened';
                } else {
                    $statusOfTime = 'closed';
                }
            } elseif (strpos($timeOfOpenClose[1], 'am') != false && strpos($timeINFORM, 'PM') != false) {
                //both time and close time are in am
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
            if (!empty($format) && $format == '24') {
                $closetime = date("H:i", $closetime);
                $opentime = date("H:i", $opentime);
            } else {
                $closetime = date('h:i A', $closetime);
                $opentime = date('h:i A', $opentime);
            }
        } else {
            //day off
            if (!empty($dayIsOff)) {
                $statusOfTime = 'off';
            } else {
                $statusOfTime = 'closed';
                $timeOfOpenClose[] = '';
                $timeOfOpenClose[] = '';
                $opentime = '';
                $closetime = '';
            }
        }
        // if (!isset($buisness_hours[$day])) {
        //     $statusOfTime = 'off';
        // }
        $dayName = esc_html__('Today', 'listingpro');
        if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
            echo '<div class="today-hrs pos-relative"><ul>';
        } else {
            echo '<div class="lp-today-timing"><strong><i class="fa-regular fa-clock"></i> ' . $dayName . '</strong>';
        }
        if ($statusOfTime == '24hours') {
            if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
                echo '<li class="today-timing clearfix"><strong><i class="fa-regular fa-clock"></i></strong>';
                echo '<span><a class="Opened">' . esc_html__('24 hours open', 'listingpro') . '</a></span>';
                echo '</li>';
            } else {
                echo '<span class="lp-timing-status pull-right status-open">' . esc_html__('24 hours open', 'listingpro') . '</span>';
            }
        } elseif ($statusOfTime == 'opened') {
            if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
                echo '<li class="today-timing clearfix"><strong><i class="fa-regular fa-clock"></i></strong>';
                echo '<a class="Opened">' . esc_html__('Open Now', 'listingpro') . '</a>';
                if ($listing_mobile_view == 'responsive_view' || !wp_is_mobile()) {
                    echo '<span>' . $opentime . ' - ' . $closetime . '</span></li>';
                }
            } else {
                echo '<span class="lp-timing-status status-open">' . esc_html__('Open Now', 'listingpro') . '</span>';
            }
        } elseif ($statusOfTime == 'closed') {
            if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
                echo '<li class="today-timing clearfix"><strong><i class="fa-regular fa-clock"></i></strong>';
                echo '<a class="closed">' . esc_html__('Closed Now', 'listingpro') . '</a>';
                echo '<span>' . $opentime . ' - ' . $closetime . '</span></li>';
            } else {
                echo '<span class="lp-timing-status status-close">' . esc_html__('Closed Now', 'listingpro') . '</span>';
                echo '<span class="lp-timings">' . $opentime . ' - ' . $closetime . '</span>';
            }
        } elseif ($statusOfTime == 'off') {
            if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
                echo '<li class="today-timing clearfix"><strong><i class="fa-regular fa-clock"></i> ' . $day . '</strong>';
                echo '<span><a class="closed dayoff">' . esc_html__('Day Off', 'listingpro') . '</a></span></li>';
            } else {
                echo '<span class="lp-timing-status pull-right status-close">' . esc_html__('Day Off', 'listingpro') . '</span>';
            }
        }
        if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
            echo '</ul><div>';
        } else {
            echo '</div>';
        }
        /* end new code 10 june */
        /*===============================open time section ends=================== */
        if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                echo '<a href="#" class="show-all-timings">' . esc_html__('Show More', 'listingpro') . '</a>';
            } else {
                echo '<a href="#" class="show-all-timings">' . esc_html__('Show all timings', 'listingpro') . '</a>';
            }
        }
        if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
            echo '<ul class="hidding-timings">';
        } else {
            echo '<ul class="lp-today-timing all-days-timings">';
        }
        //code 1 july
        $hoursKeyArray = array();
        $displayTimeKeyArray = array();
        foreach ($buisness_hours as $key => $value) {
            array_push($hoursKeyArray, $key);
        }
        foreach ($hoursKeyArray as $key => $value) {
            if (strpos($value, '-') !== false) {
                $exp_key = explode('-', $value);
                $k = $exp_key[0];
                $displayTimeKeyArray[$k][] = $buisness_hours[$value];
            } elseif (strpos($value, '~') !== false) {
                $exp_key = explode('~', $value);
                $k = $exp_key[0];
                $displayTimeKeyArray[$k][] = $buisness_hours[$value];
            } else {
                $displayTimeKeyArray[$value][] = $buisness_hours[$value];
            }
        }
        $doubleTimeClass = 'clearfix';
        $timeClass = 'lp-timings';
        if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
            $doubleTimeClass .= ' lpdoubltimes';
            $timeClass = '';
        }
        if (!empty($displayTimeKeyArray)) {
            foreach ($displayTimeKeyArray as $key => $value) {
                if (count($value) > 1) {
                    //two times and 2nd time ends in next day
                    echo '<li class="' . $doubleTimeClass . '"><strong>' . $key . '</strong>';
                    foreach ($value as $k => $v) {
                        if (isset($v['open'][0]) && isset($v['close'][0])) {
                            $opentime = $v['open'][0];
                            $closetime = $v['close'][0];
                        } elseif (isset($v['open'][1]) && isset($v['close'][1])) {
                            $opentime = $v['open'][1];
                            $closetime = $v['close'][1];
                        }
                        if (!empty($format) && $format == '24') {
                            $closetime = date("H:i", strtotime($closetime));
                            $opentime = date("H:i", strtotime($opentime));
                        } else {
                            $closetime = date('h:i A', strtotime($closetime));
                            $opentime = date('h:i A', strtotime($opentime));
                        }
                        if (empty($k)) {
                            echo '<span class="' . $timeClass . '">' . $opentime . ' - ' . $closetime;
                        } else {
                            if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
                                echo '</span><em>' . $opentime . ' - ' . $closetime . '</em>';
                            } else {
                                echo '<br>' . $opentime . ' - ' . $closetime . '</span>';
                            }
                        }
                    }
                    echo '</li>';
                } else {
                    //one time or two time so 2nd time ends in same day
                    $opentime = $value[0]['open'];
                    $closetime = $value[0]['close'];
                    if (empty($opentime) && empty($closetime)) {
                        //24 hours open
                        echo '<li class="' . $doubleTimeClass . '"><strong>' . $key . '</strong>';
                        echo '<span class="Opened lp-timing-status pull-right status-open">' . esc_html__('24 hours open', 'listingpro') . '</span></li>';
                    } elseif (!is_array($opentime) && !is_array($closetime)) {
                        //single time
                        if (!empty($format) && $format == '24') {
                            $closetime = date("H:i", strtotime($closetime));
                            $opentime = date("H:i", strtotime($opentime));
                        } else {
                            $closetime = date('h:i A', strtotime($closetime));
                            $opentime = date('h:i A', strtotime($opentime));
                        }
                        echo '<li class="' . $doubleTimeClass . '"><strong>' . $key . '</strong>';
                        echo '<span class="' . $timeClass . '">' . $opentime . ' - ' . $closetime . '</span></li>';
                    } else {
                        //array time
                        if (count($opentime) > 1 && count($closetime) > 1) {
                            //two times in one day
                            if (!empty($format) && $format == '24') {
                                $closetime1 = date("H:i", strtotime($closetime[0]));
                                $opentime1 = date("H:i", strtotime($opentime[0]));
                                $closetime2 = date("H:i", strtotime($closetime[1]));
                                $opentime2 = date("H:i", strtotime($opentime[1]));
                            } else {
                                $closetime1 = date('h:i A', strtotime($closetime[0]));
                                $opentime1 = date('h:i A', strtotime($opentime[0]));
                                $closetime2 = date('h:i A', strtotime($closetime[1]));
                                $opentime2 = date('h:i A', strtotime($opentime[1]));
                            }
                            echo '<li class="' . $doubleTimeClass . '"><strong>' . $key . '</strong>';
                            echo '<span class="' . $timeClass . '">' . $opentime1 . ' - ' . $closetime1;
                            if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
                                echo '</span><em>' . $opentime2 . ' - ' . $closetime2 . '</em>';
                            } else {
                                echo '<br>' . $opentime2 . ' - ' . $closetime2 . '</span>';
                            }
                            echo '</li>';
                        }
                    }
                }
            }
        }
        echo '</ul>';
        if ($listing_style == 'lp_detail_page_styles3' || $listing_style == 'lp_detail_page_styles4') {
            echo '<a data-contract="' . esc_html__('Contract', 'listingpro') . '" data-expand="' . esc_html__('Expand', 'listingpro') . '" href="#" class="toggle-all-days"><i class="fa fa-plus" aria-hidden="true"></i> ' . esc_html__('Expand', 'listingpro') . '</a>';
        }
        if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
            echo '</div>';
        }
    }
    if ($listing_style != 'lp_detail_page_styles3' && $listing_style != 'lp_detail_page_styles4') {
    ?>
    </div>
<?php
    }
